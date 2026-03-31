<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup the School Attendance database (MySQL)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filename = "backup-" . now()->format('Y-m-d-H-i-s') . ".sql";
        $path = storage_path("app/backups/" . $filename);

        if (!is_dir(storage_path("app/backups"))) {
            mkdir(storage_path("app/backups"), 0755, true);
        }

        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s %s > %s',
            config('database.connections.mysql.username'),
            config('database.connections.mysql.password'),
            config('database.connections.mysql.host'),
            config('database.connections.mysql.database'),
            $path
        );

        $output = NULL;
        $returnVar = NULL;
        exec($command, $output, $returnVar);

        if ($returnVar === 0) {
            $this->info("Backup Successful: {$filename}");
            Log::info("Database backup created successfully: {$filename}");
        } else {
            $this->error("Backup Failed. Ensure mysqldump is in your PATH.");
            Log::error("Database backup failed.");
        }
    }
}
