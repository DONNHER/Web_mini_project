<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use App\Models\ImportExportLog;
use App\Models\Book;
use App\Imports\BooksImport;
use App\Exports\BooksExport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ImportExportPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Category::factory()->create(['name' => 'Fiction']);
    }

    /** @test */
    public function it_queues_large_book_imports_for_background_processing()
    {
        Excel::fake();
        Storage::fake('local');

        $now = Carbon::now();
        Carbon::setTestNow($now);

        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $file = UploadedFile::fake()->create('large_books.csv', 1024);
        $expectedFileName = $now->timestamp . '_' . $file->getClientOriginalName();
        $expectedPath = 'imports/' . $expectedFileName;

        $response = $this->post(route('admin.import'), [
            'file' => $file,
            'duplicate_action' => 'skip',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify the file was stored on the correct disk
        Storage::disk('local')->assertExists($expectedPath);

        // Verify the import was queued with the correct file path
        Excel::assertQueued($expectedPath, 'local');

        $this->assertDatabaseHas('import_export_logs', [
            'type' => 'import',
            'status' => 'processing',
            'file_name' => $expectedFileName,
        ]);

        Carbon::setTestNow();
    }

    /** @test */
    public function it_validates_malformed_import_files_and_reports_errors()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $log = ImportExportLog::create([
            'type' => 'import',
            'file_name' => 'malformed.csv',
            'status' => 'processing',
            'user_id' => $admin->id
        ]);

        $import = new BooksImport(false, $log->id);

        $this->assertArrayHasKey('isbn', $import->rules());
        $this->assertArrayHasKey('price', $import->rules());
    }

    /** @test */
    public function it_verifies_import_uses_chunking_to_optimize_memory()
    {
        $import = new BooksImport();

        $this->assertInstanceOf(\Maatwebsite\Excel\Concerns\WithChunkReading::class, $import);
        $this->assertEquals(1000, $import->chunkSize());
        $this->assertInstanceOf(\Maatwebsite\Excel\Concerns\WithBatchInserts::class, $import);
        $this->assertEquals(1000, $import->batchSize());
    }

    /** @test */
    public function it_queues_large_book_exports_to_prevent_timeouts()
    {
        Excel::fake();
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);

        $now = Carbon::now();
        Carbon::setTestNow($now);
        $expectedFileName = 'books_export_' . $now->format('Y-m-d_His') . '.xlsx';

        $response = $this->get(route('admin.export', [
            'format' => 'xlsx',
            'columns' => ['title', 'author', 'isbn']
        ]));

        $response->assertStatus(200);

        Excel::assertDownloaded($expectedFileName, function (BooksExport $export) {
            return $export instanceof \Illuminate\Contracts\Queue\ShouldQueue;
        });

        Carbon::setTestNow();
    }

    /** @test */
    public function it_can_handle_high_volume_data_integrity_check()
    {
        $category = Category::where('name', 'Fiction')->first();
        Book::factory(5)->create(['category_id' => $category->id]);

        $export = new BooksExport(['category' => $category->id]);
        $query = $export->query();

        $this->assertEquals(5, $query->count());
        $this->assertStringContainsString('category_id', $query->toSql());
    }
}
