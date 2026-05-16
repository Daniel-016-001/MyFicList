<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Services\S3ImageService;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, S3ImageService $s3Service): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($request->filled('avatar_cropped')) {
            $imageData = $request->input('avatar_cropped');
            if (preg_match('/^data:image\/(\w+);base64,/', $imageData, $type)) {
                $imageData = substr($imageData, strpos($imageData, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif

                if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
                    throw new \Exception('invalid image type');
                }
                $imageData = base64_decode($imageData);

                // Crear un archivo temporal para que S3ImageService pueda procesarlo como UploadedFile
                $tmpFilePath = sys_get_temp_dir() . '/' . uniqid() . '.' . $type;
                file_put_contents($tmpFilePath, $imageData);
                
                $uploadedFile = new \Illuminate\Http\UploadedFile(
                    $tmpFilePath,
                    'avatar.' . $type,
                    'image/' . $type,
                    null,
                    true
                );

                $user->avatar_url = $s3Service->uploadAvatar($uploadedFile);
                unlink($tmpFilePath);
            }
        } elseif ($request->hasFile('avatar')) {
            $user->avatar_url = $s3Service->uploadAvatar($request->file('avatar'));
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
