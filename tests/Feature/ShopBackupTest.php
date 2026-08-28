<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ShopBackupTest extends TestCase
{
    use RefreshDatabase;

    public function test_backup_command_saves_monthly_shop_data(): void
    {
        Storage::fake('local');

        User::factory()->create(['role' => User::ROLE_ADMIN]);
        Customer::factory()->create(['name' => 'Raj Patel', 'phone' => '9876543210']);

        $month = now()->timezone('Asia/Kolkata')->format('Y-m');

        $this->artisan('shop:backup')->assertSuccessful();

        Storage::disk('local')->assertExists("backups/bahuchar-{$month}.json");

        $payload = json_decode(Storage::disk('local')->get("backups/bahuchar-{$month}.json"), true);

        $this->assertSame($month, $payload['month']);
        $this->assertNotEmpty($payload['tables']['customers']);
        $this->assertSame('9876543210', $payload['tables']['customers'][0]['phone']);
    }

    public function test_backup_command_keeps_only_configured_months(): void
    {
        Storage::fake('local');

        Storage::disk('local')->put('backups/bahuchar-2025-01.json', '{}');
        Storage::disk('local')->put('backups/bahuchar-2025-02.json', '{}');
        Storage::disk('local')->put('backups/bahuchar-2025-03.json', '{}');

        $this->artisan('shop:backup', ['--keep' => 2])->assertSuccessful();

        $files = Storage::disk('local')->files('backups');

        $this->assertCount(2, $files);
        $this->assertFalse(Storage::disk('local')->exists('backups/bahuchar-2025-01.json'));
    }
}
