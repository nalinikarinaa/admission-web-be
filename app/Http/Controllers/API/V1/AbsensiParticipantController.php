<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;

class AbsensiParticipantController extends Controller
{
    // Ambil semua peserta berdasarkan class_id
    public function getParticipantsByClassId($classId)
    {
        $participants = Absensi::with('user') // asumsi relasi user() ada di model Absensi
            ->where('class_id', $classId)
            ->get();

        return response()->json([
            'message' => 'Peserta berhasil diambil.',
            'data' => $participants,
        ]);
    }
}
