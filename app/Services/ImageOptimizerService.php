<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageOptimizerService
{
    /**
     * Konversi gambar yang diunggah ke format WebP terkompresi.
     * Mendukung JPG, PNG, GIF, BMP, WEBP, dan format gambar lainnya.
     *
     * @param UploadedFile|string $file
     * @param string $folder Subdirektori penyimpanan di disk 'public' (contoh: 'produks')
     * @param int $quality Kualitas WebP (0-100, rekomendasi 80-85)
     * @param int $maxDimension Dimensi maksimum sisi terpanjang (lebar/tinggi)
     * @return string Path relatif file yang disimpan (contoh: 'produks/abc123xyz.webp')
     */
    public static function convertToWebp($file, string $folder = 'produks', int $quality = 82, int $maxDimension = 1600): string
    {
        // Jika parameter bukan file valid
        if (!$file) {
            return '';
        }

        // Jika GD atau imagewebp tidak tersedia, fallback ke penyimpanan standar Laravel
        if (!extension_loaded('gd') || !function_exists('imagewebp')) {
            return $file instanceof UploadedFile ? $file->store($folder, 'public') : '';
        }

        $sourcePath = $file instanceof UploadedFile ? $file->getRealPath() : $file;

        if (!file_exists($sourcePath) || !is_readable($sourcePath)) {
            return $file instanceof UploadedFile ? $file->store($folder, 'public') : '';
        }

        // Jangan konversi SVG karena SVG adalah gambar vektor berbasis XML
        $mime = $file instanceof UploadedFile ? $file->getMimeType() : mime_content_type($sourcePath);
        if ($mime === 'image/svg+xml') {
            return $file instanceof UploadedFile ? $file->store($folder, 'public') : '';
        }

        try {
            // Muat gambar ke GD Resource
            $image = @imagecreatefromstring(file_get_contents($sourcePath));

            if (!$image) {
                // Percobaan cadangan berdasarkan ekstensi/mime
                $image = self::createImageFallback($sourcePath, $mime);
            }

            if (!$image) {
                // Jika GD gagal membuka, fallback simpan file asli
                return $file instanceof UploadedFile ? $file->store($folder, 'public') : '';
            }

            // Tangani orientasi EXIF (misalnya dari kamera HP smartphone)
            $image = self::fixExifOrientation($sourcePath, $image, $mime);

            // Pertahankan transparansi (untuk PNG, GIF, WEBP)
            if (function_exists('imagepalettetotruecolor')) {
                imagepalettetotruecolor($image);
            }
            imagealphablending($image, true);
            imagesavealpha($image, true);

            // Resize jika dimensi melebihi batas maksimum agar file tetap ringan
            $origWidth = imagesx($image);
            $origHeight = imagesy($image);

            if ($origWidth > $maxDimension || $origHeight > $maxDimension) {
                if ($origWidth >= $origHeight) {
                    $newWidth = $maxDimension;
                    $newHeight = (int) round(($origHeight / $origWidth) * $maxDimension);
                } else {
                    $newHeight = $maxDimension;
                    $newWidth = (int) round(($origWidth / $origHeight) * $maxDimension);
                }

                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagealphablending($resized, false);
                imagesavealpha($resized, true);

                $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                imagefilledrectangle($resized, 0, 0, $newWidth, $newHeight, $transparent);

                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
                imagedestroy($image);
                $image = $resized;
            }

            // Buat nama file unik .webp
            $filename = Str::random(40) . '.webp';
            $relativePath = trim($folder, '/') . '/' . $filename;

            $disk = Storage::disk('public');
            $disk->makeDirectory($folder);
            $destinationPath = $disk->path($relativePath);

            // Simpan gambar WebP dengan kualitas teroptimasi
            $saved = imagewebp($image, $destinationPath, $quality);
            imagedestroy($image);

            if ($saved && file_exists($destinationPath) && filesize($destinationPath) > 0) {
                return $relativePath;
            }

            // Jika gagal simpan webp, fallback simpan file biasa
            return $file instanceof UploadedFile ? $file->store($folder, 'public') : '';
        } catch (\Throwable $e) {
            Log::warning("Gagal konversi WebP: " . $e->getMessage() . ". Menggunakan penyimpanan standar.");
            return $file instanceof UploadedFile ? $file->store($folder, 'public') : '';
        }
    }

    /**
     * Fallback loader gambar manual jika imagecreatefromstring gagal
     */
    protected static function createImageFallback(string $path, ?string $mime)
    {
        switch ($mime) {
            case 'image/jpeg':
            case 'image/pjpeg':
                return function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($path) : null;
            case 'image/png':
            case 'image/x-png':
                return function_exists('imagecreatefrompng') ? @imagecreatefrompng($path) : null;
            case 'image/webp':
                return function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($path) : null;
            case 'image/gif':
                return function_exists('imagecreatefromgif') ? @imagecreatefromgif($path) : null;
            case 'image/bmp':
            case 'image/x-ms-bmp':
                return function_exists('imagecreatefrombmp') ? @imagecreatefrombmp($path) : null;
            default:
                return null;
        }
    }

    /**
     * Memperbaiki orientasi foto kamera berdasarkan metadata EXIF
     */
    protected static function fixExifOrientation(string $path, $image, ?string $mime)
    {
        if (!function_exists('exif_read_data')) {
            return $image;
        }

        if ($mime !== 'image/jpeg' && $mime !== 'image/pjpeg') {
            return $image;
        }

        try {
            $exif = @exif_read_data($path);
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $rotated = imagerotate($image, 180, 0);
                        imagedestroy($image);
                        return $rotated;
                    case 6:
                        $rotated = imagerotate($image, -90, 0);
                        imagedestroy($image);
                        return $rotated;
                    case 8:
                        $rotated = imagerotate($image, 90, 0);
                        imagedestroy($image);
                        return $rotated;
                }
            }
        } catch (\Throwable $t) {
            // Abaikan kesalahan EXIF
        }

        return $image;
    }
}
