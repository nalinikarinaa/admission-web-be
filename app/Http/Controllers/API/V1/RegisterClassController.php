<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegisterClass;
use App\Models\ClassRoom;
use Illuminate\Support\Facades\DB;


class RegisterClassController extends Controller
{
    public function store(Request $request)
    {
        \Log::info('== Register Class Masuk ==');
        \Log::info('Data:', $request->all());
        \Log::info('File:', [$request->file('payment')]);
    
        $user = auth()->user(); // Ambil user dari token
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
    
        $validated = $request->validate([
            // 'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15',
            // 'email' => 'required|email|max:255',
            'instagram' => 'nullable|string|max:255',
            'payment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'class_id' => 'required|exists:class_rooms,id',
        ]);
    
        $registration = null; // ✅ Definisikan dulu di luar
    
        DB::transaction(function () use ($request, $validated, $user, &$registration) {
            $classRoom = \App\Models\ClassRoom::lockForUpdate()->findOrFail($validated['class_id']);
    
            if ($classRoom->current_quota >= $classRoom->max_quota) {
                abort(400, 'Kuota kelas sudah penuh');
            }
    
            $paymentPath = $request->file('payment')->store('payments', 'public');
    
            // ✅ Simpan hasil ke variabel luar pakai reference (&$registration)
            $registration = RegisterClass::create([
                'nama' => $user->name,
                'phone_number' => $validated['phone_number'],
                'instagram' => $validated['instagram'] ?? null,
                'email' => $user->email,
                'payment' => $paymentPath,
                'user_id' => $user->id,
                'class_id' => $validated['class_id'],
            ]);
    
            $classRoom->increment('current_quota');
        });
    
        return response()->json([
            'message' => 'You have successfully registered for the class!',
            'data' => $registration,
        ], 201);
    }
    

    public function updateVerifikasi(Request $request, $id)
{
    $request->validate([
        'payment_verification' => 'required|in:pending,lunas,gagal',
        'class_verification' => 'required|in:aktif,tidak aktif',
    ]);

    $register = RegisterClass::findOrFail($id);

    $register->payment_verification = $request->payment_verification;
    $register->class_verification = $request->class_verification;
    $register->save();

    return response()->json([
        'message' => 'Status verifikasi berhasil diperbarui.',
        'data' => $register,
    ]);
}

public function show($id)
{
    // Mencari data berdasarkan ID
    $registration = RegisterClass::find($id);

    // Jika data ditemukan, kembalikan response JSON
    if ($registration) {
        return response()->json([
            'message' => 'Pendaftaran kelas ditemukan!',
            'data' => $registration
        ], 200);
    }

    // Jika tidak ditemukan, kembalikan response 404
    return response()->json([
        'message' => 'Pendaftaran kelas tidak ditemukan!'
    ], 404);
}

public function getUserClass(Request $request)
{
    $user = auth()->user(); // Ambil user dari token
    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // Ambil semua pendaftaran kelas berdasarkan user_id
    $registrations = RegisterClass::with('kelas') // Mengambil relasi kelas
                        ->where('user_id', $user->id)
                        ->get();

    // Jika tidak ada pendaftaran kelas
    if ($registrations->isEmpty()) {
        return response()->json(['message' => 'No class registrations found for this user.'], 404);
    }

    // Mengembalikan data pendaftaran kelas
    return response()->json([
        'message' => 'Pendaftaran kelas ditemukan!',
        'data' => $registrations
    ], 200);
}
public function checkIfRegistered(Request $request)
{
    $user = auth()->user(); // Ambil user dari token

    if (!$user) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $classId = $request->query('class_id');

    if (!$classId) {
        return response()->json(['message' => 'Parameter class_id wajib diisi.'], 400);
    }

    $alreadyRegistered = RegisterClass::where('user_id', $user->id)
        ->where('class_id', $classId)
        ->exists();

    return response()->json([
        'exists' => $alreadyRegistered
    ]);
}



}
