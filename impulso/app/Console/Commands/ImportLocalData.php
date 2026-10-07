<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ImportLocalData extends Command
{
    protected $signature = 'app:import-local-data
        {--confirm : Confirms the one-time transfer into the shared services}
        {--dry-run : Validates the empty PostgreSQL destination without copying data}
        {--files-only : Transfers uploads without copying database rows}
        {--database-only : Copies database rows without transferring uploads}';

    protected $description = 'Imports the local SQLite data and uploads into an empty shared PostgreSQL/S3 setup.';

    private const SKIPPED_TABLES = [
        'migrations', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'sessions',
    ];

    public function handle(): int
    {
        if (! $this->option('confirm')) {
            $this->error('Nothing imported. Rerun with --confirm after configuring and backing up the shared services.');

            return self::FAILURE;
        }

        if ($this->option('files-only') && $this->option('database-only')) {
            $this->error('Choose either --files-only or --database-only, not both.');

            return self::FAILURE;
        }

        try {
            $target = DB::connection();
            if ($target->getDriverName() !== 'pgsql') {
                throw new RuntimeException('The default DB_CONNECTION must be pgsql. Local SQLite is never a valid import destination.');
            }

            $source = DB::connection('sqlite_import');
            $tables = $this->option('files-only') ? [] : $this->prepareTableOrder($source, $target);

            if ($this->option('dry-run')) {
                if (! $this->option('database-only') && config('filesystems.disks.public.driver') !== 's3') {
                    throw new RuntimeException('Set PUBLIC_FILESYSTEM_DRIVER=s3 and configure the shared bucket before transferring uploads.');
                }

                $this->info('Preflight passed. The destination schema is complete and all imported tables are empty. No data was copied.');

                return self::SUCCESS;
            }

            if (! $this->option('database-only')) {
                $this->copyUploads();
            }

            if (! $this->option('files-only')) {
                $this->copyDatabase($source, $target, $tables);
            }
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Import complete. Local SQLite and uploads were kept unchanged.');

        return self::SUCCESS;
    }

    private function prepareTableOrder($source, $target): array
    {
        $sourceTables = collect($source->select("SELECT name FROM sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'"))
            ->pluck('name')
            ->reject(fn (string $table) => in_array($table, self::SKIPPED_TABLES, true))
            ->values()
            ->all();
        $targetTables = collect(Schema::getTableListing())
            ->map(fn (string $table) => Str::afterLast($table, '.'))
            ->all();
        $missingTables = array_values(array_diff($sourceTables, $targetTables));

        if ($missingTables) {
            throw new RuntimeException('Run all migrations on the shared database first. Missing tables: '.implode(', ', $missingTables));
        }

        foreach ($sourceTables as $table) {
            if ($target->table($table)->exists()) {
                throw new RuntimeException("Shared table [{$table}] is not empty. Import stopped before copying any data.");
            }
        }

        $included = array_fill_keys($sourceTables, true);
        $dependencies = array_fill_keys($sourceTables, []);

        foreach ($sourceTables as $table) {
            $quotedTable = str_replace('"', '""', $table);
            foreach ($source->select("PRAGMA foreign_key_list(\"{$quotedTable}\")") as $foreignKey) {
                $parent = $foreignKey->table;
                if ($parent !== $table && isset($included[$parent])) {
                    $dependencies[$table][$parent] = true;
                }
            }
        }

        $ordered = [];
        while ($dependencies) {
            $ready = [];
            foreach ($dependencies as $table => $parents) {
                if (! $parents) {
                    $ready[] = $table;
                }
            }

            if (! $ready) {
                throw new RuntimeException('A circular foreign-key dependency prevents a safe initial import. No data was copied.');
            }

            foreach ($ready as $table) {
                $ordered[] = $table;
                unset($dependencies[$table]);
                foreach ($dependencies as &$parents) {
                    unset($parents[$table]);
                }
                unset($parents);
            }
        }

        return $ordered;
    }

    private function copyDatabase($source, $target, array $tables): void
    {
        $target->transaction(function () use ($source, $target, $tables): void {
            foreach ($tables as $table) {
                $rows = $source->table($table)->get()->map(fn ($row) => (array) $row);

                foreach ($rows->chunk(250) as $batch) {
                    $target->table($table)->insert($batch->all());
                }

                if ($rows->isNotEmpty() && in_array('id', Schema::getColumnListing($table), true)) {
                    $sequenceResult = $target->selectOne("SELECT pg_get_serial_sequence(?, 'id') AS sequence", [$table]);
                    $sequence = $sequenceResult ? $sequenceResult->sequence : null;
                    $maxId = $target->table($table)->max('id');
                    if ($sequence && $maxId !== null) {
                        $target->select('SELECT setval(?, ?, true)', [$sequence, (int) $maxId]);
                    }
                }

                $this->line("Imported {$table}: {$rows->count()} rows");
            }
        });
    }

    private function copyUploads(): void
    {
        if (config('filesystems.disks.public.driver') !== 's3') {
            throw new RuntimeException('Set PUBLIC_FILESYSTEM_DRIVER=s3 and configure the shared bucket before transferring uploads.');
        }

        $sourceRoot = public_path('uploads');
        if (! is_dir($sourceRoot)) {
            throw new RuntimeException("Local uploads directory not found: {$sourceRoot}");
        }

        $disk = Storage::disk('public');
        $copied = 0;
        $skipped = 0;

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($sourceRoot, \FilesystemIterator::SKIP_DOTS)) as $file) {
            if (! $file->isFile() || $file->getFilename() === '.gitkeep') {
                continue;
            }

            $relativePath = str_replace('\\', '/', substr($file->getPathname(), strlen(rtrim($sourceRoot, '\\/')) + 1));
            if ($disk->exists($relativePath)) {
                $skipped++;
                continue;
            }

            $stream = fopen($file->getPathname(), 'rb');
            try {
                if (! $disk->put($relativePath, $stream)) {
                    throw new RuntimeException("Failed to upload [{$relativePath}]. Local copy was kept.");
                }
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }

            $copied++;
        }

        $this->info("Uploads copied: {$copied}; already present: {$skipped}.");
    }
}