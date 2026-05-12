<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class S3ImageController extends Controller
{
    /**
     * Sube y redimensiona la foto de perfil del usuario a S3.
     */
    public function uploadAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $file = $request->file('avatar');
        $fileName = 'perfiles/' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Redimensionar con Intervention Image v3
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        $image->cover(400, 400); 
        
        // Codificar la imagen y convertir a string
        $encoded = $image->toJpeg(80)->toString();

        // Subir a S3
        Storage::disk('s3')->put($fileName, $encoded, 'public');

        // Obtener la URL pública
        $url = Storage::disk('s3')->url($fileName);

        // Opcional: Actualizar el usuario autenticado
        if (Auth::check()) {
            Auth::user()->update(['avatar_url' => $url]);
        }

        return response()->json([
            'message' => 'Avatar subido con éxito',
            'url' => $url
        ]);
    }

    /**
     * Sube una imagen para un post del foro a S3, redimensionándola para ahorrar espacio.
     */
    public function uploadForo(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240', // Aumentamos a 10MB ya que la procesaremos
        ]);

        $file = $request->file('image');
        $fileName = 'foro/' . time() . '_' . uniqid() . '.jpg'; // Forzamos .jpg para consistencia y ahorro de espacio

        // Procesar con Intervention Image v3
        $manager = new ImageManager(new Driver());
        $image = $manager->read($file);
        
        // Redimensionar si es más ancha de 1200px
        $image->scale(width: 1200); 
        
        // Codificar a JPG con 75% de calidad para máxima compresión
        $encoded = $image->toJpeg(75)->toString();

        // Subir a S3
        Storage::disk('s3')->put($fileName, $encoded, 'public');

        // Obtener la URL pública
        $url = Storage::disk('s3')->url($fileName);

        return response()->json([
            'message' => 'Imagen del foro procesada y subida con éxito',
            'url' => $url,
            'size_saved' => true
        ]);
    }
}
