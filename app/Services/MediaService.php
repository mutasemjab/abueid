<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Stores uploaded media under the public web root (assets/uploads/{folder}).
 * Paths are saved relative to the web root so they can be rendered with media_url().
 */
class MediaService
{
    public const ROOT = 'assets/uploads';

    public function store(UploadedFile $file, string $folder): string
    {
        $directory = self::ROOT.'/'.trim($folder, '/');
        $name = Str::uuid().'.'.strtolower($file->getClientOriginalExtension() ?: $file->guessExtension());

        $file->move(base_path($directory), $name);

        return "{$directory}/{$name}";
    }

    /**
     * Store the new file and remove the previous upload (if any).
     */
    public function replace(?string $previous, UploadedFile $file, string $folder): string
    {
        $path = $this->store($file, $folder);
        $this->delete($previous);

        return $path;
    }

    /**
     * Delete an uploaded file. Bundled design assets (outside the upload root) are never deleted.
     */
    public function delete(?string $path): void
    {
        if ($path && str_starts_with($path, self::ROOT.'/') && ! str_contains($path, '..')) {
            File::delete(base_path($path));
        }
    }
}
