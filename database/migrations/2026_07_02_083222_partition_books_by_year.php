<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (config('database.default') === 'mysql') {
            // Disable checks to allow structural changes
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // 1. Drop all INCOMING foreign keys (tables referencing 'books')
            $this->dropIncomingForeignKeys();

            // 2. Drop all OUTGOING foreign keys (defined on 'books' referencing other tables)
            $this->dropOutgoingForeignKeys();

            // 3. Adjust schema for partitioning (PK and Unique Index must include partition key)
            // We partition by 'publication_year' (INT) as it is not timezone-dependent
            $this->prepareTableForPartitioning();

            // DATA MIGRATION SCRIPT:
            // Ensure no NULL values exist in publication_year before partitioning
            DB::table('books')->whereNull('publication_year')->update(['publication_year' => now()->year]);

            // 4. Apply Partitioning
            // MySQL will automatically redistribute existing data into these partitions based on publication_year.
            DB::statement("
                ALTER TABLE books PARTITION BY RANGE (publication_year) (
                    PARTITION p_old VALUES LESS THAN (2000),
                    PARTITION p2000 VALUES LESS THAN (2005),
                    PARTITION p2005 VALUES LESS THAN (2010),
                    PARTITION p2010 VALUES LESS THAN (2015),
                    PARTITION p2015 VALUES LESS THAN (2020),
                    PARTITION p2020 VALUES LESS THAN (2025),
                    PARTITION p_future VALUES LESS THAN MAXVALUE
                )
            ");

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
    }

    /**
     * Drop all foreign keys in the database that point TO the 'books' table.
     */
    private function dropIncomingForeignKeys(): void
    {
        $database = DB::getDatabaseName();
        $incoming = DB::select("
            SELECT TABLE_NAME, CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE REFERENCED_TABLE_NAME = 'books'
            AND TABLE_SCHEMA = ?
            AND CONSTRAINT_NAME <> 'PRIMARY'
        ", [$database]);

        foreach ($incoming as $fk) {
            try {
                DB::statement("ALTER TABLE `{$fk->TABLE_NAME}` DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {}
        }
    }

    /**
     * Drop all foreign keys defined ON the 'books' table referencing other tables.
     */
    private function dropOutgoingForeignKeys(): void
    {
        $database = DB::getDatabaseName();
        $outgoing = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_NAME = 'books'
            AND TABLE_SCHEMA = ?
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ", [$database]);

        foreach ($outgoing as $fk) {
            try {
                DB::statement("ALTER TABLE books DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
            } catch (\Exception $e) {}
        }
    }

    /**
     * Prepares the 'books' table for partitioning.
     * Required: PK and Unique Indexes must contain the partition column ('publication_year').
     */
    private function prepareTableForPartitioning(): void
    {
        // Drop any indexes that might conflict with partitioning requirements
        $conflictingIndexes = [
            'idx_books_isbn_lookup',
            'books_isbn_unique',
            'books_isbn_index',
            'books_isbn_published_unique'
        ];
        foreach ($conflictingIndexes as $idx) {
            try {
                DB::statement("ALTER TABLE books DROP INDEX `{$idx}`");
            } catch (\Exception $e) {}
        }

        // Primary Key must be (id, publication_year)
        DB::statement("ALTER TABLE books MODIFY id BIGINT UNSIGNED NOT NULL");
        DB::statement("ALTER TABLE books DROP PRIMARY KEY");
        DB::statement("ALTER TABLE books ADD PRIMARY KEY (id, publication_year)");
        DB::statement("ALTER TABLE books MODIFY id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT");

        // ISBN unique index must also include publication_year
        DB::statement("ALTER TABLE books ADD UNIQUE INDEX books_isbn_year_unique (isbn, publication_year)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (config('database.default') === 'mysql') {
            DB::statement("ALTER TABLE books REMOVE PARTITIONING");
        }
    }
};
