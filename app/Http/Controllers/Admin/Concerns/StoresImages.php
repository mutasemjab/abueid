<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Services\MediaService;
use Illuminate\Http\Request;

/**
 * Handles a single image input named {field} with an optional "remove_{field}" checkbox.
 */
trait StoresImages
{
    protected function uploadedImage(Request $request, string $field, ?string $current, string $folder): ?string
    {
        $media = app(MediaService::class);

        if ($file = $request->file($field)) {
            return $media->replace($current, $file, $folder);
        }

        if ($request->boolean("remove_{$field}")) {
            $media->delete($current);

            return null;
        }

        return $current;
    }

    protected function deleteImage(?string $path): void
    {
        app(MediaService::class)->delete($path);
    }
}
