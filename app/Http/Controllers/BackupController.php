<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function index(): Response
    {
        $files = collect(Storage::disk('local')->files('backups'))
            ->filter(fn (string $file) => str_ends_with($file, '.json'))
            ->sortDesc()
            ->values()
            ->map(fn (string $file) => [
                'name' => basename($file),
                'size' => Storage::disk('local')->size($file),
                'updated_at' => date('Y-m-d H:i', Storage::disk('local')->lastModified($file)),
            ]);

        return Inertia::render('Backups/Index', [
            'backups' => $files,
        ]);
    }

    public function store(): RedirectResponse
    {
        Artisan::call('shop:backup');

        return redirect()
            ->route('backups.index')
            ->with('status', 'Monthly backup saved.');
    }

    public function download(string $backup): StreamedResponse
    {
        abort_unless(preg_match('/^bahuchar-\d{4}-\d{2}\.json$/', $backup) === 1, 404);

        $path = "backups/{$backup}";
        abort_unless(Storage::disk('local')->exists($path), 404);

        return Storage::disk('local')->download($path);
    }
}
