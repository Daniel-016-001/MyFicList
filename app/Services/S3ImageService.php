<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class S3ImageService
{
    /**
     * Sube y redimensiona la foto de perfil del usuario a S3.
     */
    public function uploadAvatar(UploadedFile $file): string
    {
        $fileName = 'perfiles/' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $image->cover(400, 400); 
        
        $encoded = $image->toJpeg(80)->toString();

        Storage::disk('s3')->put($fileName, $encoded, 'public');

        return Storage::disk('s3')->url($fileName);
    }

    /**
     * Sube una imagen para un post del foro a S3, redimensionándola para ahorrar espacio.
     */
    public function uploadForo(UploadedFile $file): string
    {
        $fileName = 'foro/' . time() . '_' . uniqid() . '.jpg'; 

        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        
        // Redimensionar solo si excede los límites (evita ampliar imágenes pequeñas)
        $image->scaleDown(width: 1200, height: 1200); 
        
        $encoded = $image->toJpeg(75)->toString();

        Storage::disk('s3')->put($fileName, $encoded, 'public');

        return Storage::disk('s3')->url($fileName);
    }

    /**
     * Elimina un archivo de S3 a partir de su URL pública.
     */
    public function deleteFile(string $url): void
    {
        // Extraer el path de la URL de S3
        $parsedUrl = parse_url($url);
        $path = ltrim($parsedUrl['path'] ?? '', '/');
        
        if ($path && Storage::disk('s3')->exists($path)) {
            Storage::disk('s3')->delete($path);
        }
    }
}
