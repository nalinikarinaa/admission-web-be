<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
    
        return response()->json([
            'name' => $user->name,
            'username' => $user->username,
            'profile_photo_url' => $user->profile_photo 
                ? asset('storage/profile/' . $user->profile_photo)
                : asset('images/default-profile.png'), // fallback opsional
        ]);
    }
    

public function update(Request $request)
{
    $user = Auth::user();

    $validator = Validator::make($request->all(), [
        'name'           => 'required|string|max:255',
        'username'       => 'required|string|max:255|unique:users,username,' . $user->id,
        'profile_photo'  => 'nullable|image|max:2048', // maksimal 2MB
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Handle upload foto profil jika ada
    if ($request->hasFile('profile_photo')) {
        // Hapus foto lama jika ada
        if ($user->profile_photo && Storage::exists('public/profile/' . $user->profile_photo)) {
            Storage::delete('public/profile/' . $user->profile_photo);
        }

        $file = $request->file('profile_photo');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/profile', $filename); // SIMPAN ke storage/app/public/profile
        
        $user->profile_photo = $filename; // Simpan hanya nama file ke DB
    }

    // Update nama dan username
    $user->name = $request->name;
    $user->username = $request->username;
    $user->save();

    return response()->json([
        'message' => 'Profil berhasil diperbarui',
        'user'    => $user,
        'profile_photo_url' => asset('storage/profile/' . $user->profile_photo), // Menggunakan asset() untuk mendapatkan URL publik
    ]);
}

}
