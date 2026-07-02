<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupTempFiles extends Command
{
    protected $signature = 'app:cleanup-temp-files';
    protected $description = 'Remove temporary files older than 24 hours';

    public function handle()
    {
        $this->info('Cleaning up temporary files...');
        
        $disk = Storage::disk('local');
        $directories = ['temp', 'imports', 'exports'];
        $cutoff = now()->subHours(24);
        $count = 0;
        
        foreach ($directories as $dir) {
            if (!$disk->exists($dir)) {
                continue;
            }
            
            $files = $disk->files($dir);
            foreach ($files as $file) {
                $lastModified = Carbon::createFromTimestamp($disk->lastModified($file));
                if ($lastModified->lt($cutoff)) {
                    $disk->delete($file);
                    $count++;
                }
            }
        }
        
        $this->info("Deleted {$count} temporary files.");
        
        return Command::SUCCESS;
    }
}