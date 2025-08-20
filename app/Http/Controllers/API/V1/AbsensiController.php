<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use Illuminate\Support\Facades\Storage;

class AbsensiController extends Controller
{
    public function store(Request $request)
    {
        
        $user = auth()->user(); // Ambil user dari token
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }


        $validated = $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'foto_kehadiran' => 'required|image|max:2048',
        ]);

        $user = $request->user();

        // Upload foto kehadiran
        $path = $request->file('foto_kehadiran')->store('absensi_photos', 'public');

        // Cek apakah sudah absen sebelumnya (opsional)
        // $existing = Absensi::where('user_id', $user->id)
        //     ->where('class_id', $request->class_id)
        //     ->first();

        // if ($existing) {
        //     return response()->json(['message' => 'Sudah melakukan absensi.'], 409);
        // }

        $existing = Absensi::where('user_id', $request->user_id)
        ->where('class_id', $request->class_id)
        ->first();
    
    if ($existing) {
        return response()->json([
            'message' => 'Anda sudah absen di kelas ini.'
        ], 409);
    }

        // Simpan absensi
        $absensi = Absensi::create([
            'user_id' => $user->id,
            'class_id' => $validated['class_id'],
            'class_id' => $request->class_id,
            'foto_kehadiran' => $path,
            'status' => 'pending',
            'nama' => $request->nama, 
        ]);

        return response()->json([
            'message' => 'Absensi berhasil',
            'data' => $absensi
        ], 201);
        
    }

    public function updateVerifikasi(Request $request, $id)
    {
        \Log::info("Menerima update untuk absensi ID: " . $id);
    
        $request->validate([
            'absensi_verification' => 'required|in:verifikasi,tidak_hadir',
        ]);
    
        $absensi = Absensi::find($id);
    
        if (!$absensi) {
            return response()->json([
                'message' => 'Absensi tidak ditemukan untuk ID ' . $id
            ], 404);
        }
    
        // Mapping nilai dari form ke status di database
        $absensi->status = $request->absensi_verification === 'verifikasi' ? 'hadir' : 'tidak_hadir';
        // $absensi->status = $request->absensi_verification === 'verifikasi' ? 'hadir' : 'ditolak';

        
        $absensi->save();
    
        return response()->json([
            'message' => 'Status verifikasi berhasil diperbarui.',
            'data' => $absensi,
        ]);
    }
    
    public function getParticipants($classId)
    {
        $participants = DB::table('users')
            ->join('absensis', function ($join) {
                $join->on('users.id', '=', 'absensis.user_id');
            })
            ->where('absensis.class_id', $classId)
            ->select('users.*', 'absensis.id as absensi_id', 'absensis.foto_kehadiran', 'absensis.absensi_verification')
            ->latest()
            ->get();

        return response()->json(['data' => $participants]);
    }

    public function destroy($id)
{
    $absensi = Absensi::find($id);

    if (!$absensi) {
        return response()->json(['message' => 'Absensi tidak ditemukan'], 404);
    }

    // Boleh dihapus hanya jika masih pending
    if ($absensi->status !== 'pending') {
        return response()->json(['message' => 'Absensi sudah diverifikasi dan tidak bisa dihapus'], 403);
    }

    // Hapus file foto
    if ($absensi->foto_kehadiran) {
        Storage::disk('public')->delete($absensi->foto_kehadiran);
    }

    $absensi->delete();

    return response()->json(['message' => 'Absensi berhasil dihapus.']);
}

    

}
