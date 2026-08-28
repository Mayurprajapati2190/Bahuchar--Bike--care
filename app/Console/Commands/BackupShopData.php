<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class BackupShopData extends Command
{
    protected $signature = 'shop:backup {--keep=12 : Number of monthly backups to retain}';

    protected $description = 'Save a monthly backup of shop customers, services, bills, and related data';

    /**
     * @var list<string>
     */
    private array $tables = [
        'users',
        'customers',
        'bikes',
        'service_records',
        'service_items',
        'bills',
        'sms_messages',
        'customer_otps',
    ];

    public function handle(): int
    {
        $stamp = now()->timezone('Asia/Kolkata')->format('Y-m');
        $disk = Storage::disk('local');
        $payload = $this->collectTables();
        $path = "backups/bahuchar-{$stamp}.json";

        $disk->put($path, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->pruneOldBackups((int) $this->option('keep'));

        $this->info("Monthly backup saved: storage/app/private/{$path}");
        $this->line('Tables: '.implode(', ', array_keys($payload['tables'])));

        return self::SUCCESS;
    }

    /**
     * @return array{generated_at: string, month: string, tables: array<string, list<array<string, mixed>>>}
     */
    private function collectTables(): array
    {
        $tables = [];

        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $tables[$table] = DB::table($table)->get()->map(fn ($row) => (array) $row)->all();
        }

        return [
            'generated_at' => now()->timezone('Asia/Kolkata')->toIso8601String(),
            'month' => now()->timezone('Asia/Kolkata')->format('Y-m'),
            'tables' => $tables,
        ];
    }

    private function pruneOldBackups(int $keep): void
    {
        $files = collect(Storage::disk('local')->files('backups'))
            ->filter(fn (string $file) => str_ends_with($file, '.json') && str_contains($file, 'bahuchar-'))
            ->sort()
            ->values();

        $remove = $files->slice(0, max(0, $files->count() - $keep));

        foreach ($remove as $file) {
            Storage::disk('local')->delete($file);
        }
    }
}
