<?php

namespace Database\Seeders;

use App\Models\Client;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File as HttpFile;

class ClientImageSeeder extends Seeder
{
    /**
     * Import client images from the base folder and create client records.
     */
    public function run(): void
    {
        $sourceDir = base_path('govt png'); // folder with client images

        if (!is_dir($sourceDir)) {
            $this->command?->error("Source directory not found: {$sourceDir}");
            return;
        }

        $pattern = $sourceDir . DIRECTORY_SEPARATOR . '*.{png,jpg,jpeg,webp}';
        $files = glob($pattern, GLOB_BRACE) ?: [];

        if (empty($files)) {
            $this->command?->warn('No image files found to import.');
            return;
        }

        $publicDisk = Storage::disk('public');
        $targetDir = 'clients';
        $nextSortOrder = (int) (Client::max('sort_order') ?? 0) + 1;
        $imported = 0;

        foreach ($files as $filePath) {
            $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
            $rawName = pathinfo($filePath, PATHINFO_FILENAME);

            // Clean up name: trim and collapse spaces; keep original case
            $name = preg_replace('/\s+/', ' ', trim($rawName));

            // Safe file name for storage
            $baseSlug = Str::slug($name, '-');
            $filename = $baseSlug . '.' . $ext;

            // Ensure uniqueness of stored file name
            $counter = 1;
            while ($publicDisk->exists($targetDir . '/' . $filename)) {
                $filename = $baseSlug . '-' . $counter . '.' . $ext;
                $counter++;
            }

            // Move file into storage/public/clients
            try {
                $storedPath = $publicDisk->putFileAs($targetDir, new HttpFile($filePath), $filename);
            } catch (\Throwable $e) {
                $this->command?->error('Failed to store file: ' . $filePath . ' - ' . $e->getMessage());
                continue;
            }

            if (!$storedPath) {
                $this->command?->error('Storage returned false for: ' . $filePath);
                continue;
            }

            // Create or update client by name
            $client = Client::firstOrNew(['name' => $name]);
            if (!$client->exists) {
                $client->featured = false;
                $client->status = true;
                $client->sort_order = $nextSortOrder++;
            }
            $client->logo = $storedPath; // e.g., clients/xyz.png
            $client->save();

            // Remove original file after successful storage
            @unlink($filePath);

            $imported++;
            $this->command?->info("Imported client: {$client->name} ({$client->logo})");
        }

        $this->command?->info("Client import complete. Imported: {$imported}");
        $this->command?->info('Ensure a symlink exists: public/storage -> storage/app/public');
    }
}

