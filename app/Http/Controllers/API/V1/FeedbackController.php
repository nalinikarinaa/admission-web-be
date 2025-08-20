<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Absensi;
use Illuminate\Support\Facades\Storage;
use App\Models\Feedback;

class FeedbackController extends Controller
{
    public function store(Request $request)
    {
        
        $user = auth()->user(); // Ambil user dari token
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }


        $validated = $request->validate([
            'class_id' => 'required|exists:class_rooms,id',
            'comment' => 'required|string|max:255',
        ]);

        $user = $request->user();

        $existing = Feedback::where('user_id', $request->user_id)
        ->where('class_id', $request->class_id)
        ->first();
    
    if ($existing) {
        return response()->json([
            'message' => 'Anda sudah menulis di kelas ini.'
        ], 409);
    }

        // Simpan absensi
        $feedback = Feedback::create([
            'user_id' => $user->id,
            'class_id' => $validated['class_id'],
            'class_id' => $request->class_id,
            'comment' => $validated['comment'],
            'status' => 'pending',
            'nama' => $request->nama, 
        ]);

        return response()->json([
            'message' => 'Feedback berhasil',
            'data' => $feedback
        ], 201);
        
    }

    public function updateFeedback(Request $request, $id)
    {
        \Log::info("Menerima update untuk feedback ID: " . $id);
    
        $request->validate([
            'feedback_verification' => 'required|in:verifikasi,tolak',
        ]);
    
        $feedback = Feedback::find($id);
    
        if (!$feedback) {
            return response()->json([
                'message' => 'Feedback tidak ditemukan untuk ID ' . $id
            ], 404);
        }
    
        // Mapping nilai dari form ke status di database
        // $absensi->status = $request->absensi_verification === 'verifikasi' ? 'hadir' : 'tidak hadir';
        $feedback->status = $request->feedback_verification === 'verifikasi' ? 'terima' : 'tolak';

        
        $feedback->save();
    
        return response()->json([
            'message' => 'Status feedback berhasil diperbarui.',
            'data' => $feedback,
        ]);
    }
    
    public function getFeedbackByClass($classId)
{
    $feedbacks = DB::table('feedback')
        ->join('users', 'feedback.user_id', '=', 'users.id')
        ->where('feedback.class_id', $classId)
        ->select('feedback.*', 'users.nama', 'users.phone_number', 'users.instagram', 'users.email')
        ->get();

    return response()->json([
        'message' => 'Feedback berhasil diambil.',
        'data' => $feedbacks
    ]);
}
    
public function getVerifiedFeedback()
{
    $feedbacks = Feedback::where('status', 'terima')
        ->with(['user:id,name,profile_photo'])
        ->latest()
        ->take(10)
        ->get();

    $feedbacks->transform(function ($item) {
        $item->user->profile_photo_url = $item->user->profile_photo
            ? 'http://127.0.0.1:8000/storage/profile/' . $item->user->profile_photo
            : asset('img/default-avatar.png');
        return $item;
    });

    return response()->json([
        'message' => 'Feedback terverifikasi berhasil diambil.',
        'data' => $feedbacks
    ]);
}




}
