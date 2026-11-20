<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GameToraAssetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetImportController extends Controller
{
    public function __construct(
        protected GameToraAssetService $assetService
    ) {}

    /**
     * Quick import of an image URL to local storage.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'url' => ['required', 'url'],
            'folder' => ['nullable', 'string', 'in:characters,supoka,skills,uma'],
        ]);

        $folder = $request->input('folder', 'characters');
        $path = $this->assetService->importFromUrl($request->input('url'), $folder);

        if (! $path) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to download image from the provided URL.',
            ], 422);
        }

        return response()->json([
            'success' => true,
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
        ]);
    }
}
