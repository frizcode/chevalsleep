<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameToraAssetService
{
    /**
     * Download an asset from a URL (e.g., GameTora) and store it in public storage.
     *
     * @param string|null $url
     * @param string $folder Subfolder within public disk ('characters', 'supoka', 'skills', etc.)
     * @return string|null Relative storage path (e.g., 'characters/abc123.webp') or null on failure
     */
    public function importFromUrl(?string $url, string $folder = 'characters'): ?string
    {
        if (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
            // If it's already a relative path, keep it
            return ! empty($url) ? $url : null;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Referer' => 'https://gametora.com/',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                ])
                ->get($url);

            if (! $response->successful()) {
                Log::warning("Failed to download GameTora asset from [{$url}]: HTTP {$response->status()}");
                return null;
            }

            $contentType = $response->header('Content-Type');
            $extension = $this->guessExtension($url, $contentType);

            $filename = Str::random(16) . '.' . $extension;
            $path = trim($folder, '/') . '/' . $filename;

            Storage::disk('public')->put($path, $response->body());

            return $path;
        } catch (\Throwable $e) {
            Log::error("Error importing GameTora asset from [{$url}]: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Determine file extension from URL or Content-Type header.
     */
    protected function guessExtension(string $url, ?string $contentType): string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if ($path) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (in_array($ext, ['webp', 'png', 'jpg', 'jpeg', 'gif', 'svg'], true)) {
                return $ext === 'jpeg' ? 'jpg' : $ext;
            }
        }

        if ($contentType) {
            return match (true) {
                str_contains($contentType, 'webp') => 'webp',
                str_contains($contentType, 'png')  => 'png',
                str_contains($contentType, 'jpeg'), str_contains($contentType, 'jpg') => 'jpg',
                str_contains($contentType, 'svg')  => 'svg',
                default => 'png',
            };
        }

        return 'png';
    }
}
