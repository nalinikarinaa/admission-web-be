<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;

class FeedbackParticipantController extends Controller
{
    // Ambil semua peserta berdasarkan class_id
    public function getParticipantsByClassId($classId)
    {
        $participants = Feedback::with('user') // Mengambil data user terkait feedback
            ->where('class_id', $classId)
            ->get();
    
        // dd($participants);  // Debug: Memeriksa apakah data user sudah ada
        
        return response()->json([
            'message' => 'Peserta berhasil diambil.',
            'data' => $participants,
        ]);
    }
    
}
