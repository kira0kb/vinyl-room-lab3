<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CoverImage
{
    public function store(UploadedFile $file): string
    {
        $source = @imagecreatefromstring(file_get_contents($file->getRealPath()));
        if ($source === false) {
            throw ValidationException::withMessages(['cover' => 'Не удалось открыть изображение. Выбери другой файл.']);
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $side = min($width, $height);
        $cover = imagecreatetruecolor(900, 900);
        imagefill($cover, 0, 0, imagecolorallocate($cover, 255, 255, 255));

        // Берём квадрат из центра и уменьшаем до размера обложки.
        imagecopyresampled($cover, $source, 0, 0,
            (int) (($width - $side) / 2), (int) (($height - $side) / 2),
            900, 900, $side, $side);

        ob_start();
        imagejpeg($cover, null, 90);
        $contents = ob_get_clean();
        imagedestroy($source);
        imagedestroy($cover);

        $path = 'albums/uploads/'.Str::uuid().'.jpg';
        Storage::disk('public')->put($path, $contents);

        return $path;
    }

    public function deleteUploaded(?string $path): void
    {
        // Исходные обложки оставляем: они нужны для повторного заполнения БД.
        if ($path && str_starts_with($path, 'albums/uploads/')) {
            Storage::disk('public')->delete($path);
        }
    }
}
