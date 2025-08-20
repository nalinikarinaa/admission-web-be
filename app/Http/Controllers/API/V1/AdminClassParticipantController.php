<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegisterClass;


// AMBIL DATA DI DETAIL CLASS MANAJEMEN
class AdminClassParticipantController extends Controller
{
    // Ambil semua peserta berdasarkan class_id untuk verifikasi kelas dan pembayaran
    public function getParticipantsByClassId($classId)
    {
        $participants = RegisterClass::where('class_id', $classId)->get();

        return response()->json([
            'message' => 'Peserta berhasil diambil.',
            'data' => $participants,
        ]);
    }
}
