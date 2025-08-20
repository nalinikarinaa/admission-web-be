<?php
// app/Http/Controllers/API/V1/TransactionController.php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RegisterClass;
use Illuminate\Database\QueryException;

class TransactionController extends Controller
{
   public function index(Request $request)
{
    try {
        $user = $request->user(); // Mendapatkan user yang login

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $transactions = RegisterClass::with('kelas.location')
            ->where('user_id', $user->id)
            ->latest()
            ->get()
           ->map(function ($item) use ($user) {
    $feedback = \App\Models\Feedback::where('user_id', $user->id)
        ->where('class_id', $item->class_id)
        ->first();

    $absen = \App\Models\Absensi::where('user_id', $user->id)
        ->where('class_id', $item->class_id)
        ->first();

    $item->feedback = $feedback;
    $item->absensi = $absen;
    return $item;
});


        return response()->json(['data' => $transactions]);

    } catch (QueryException $e) {
        return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
    } catch (\Exception $e) {
        return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
    }
}

    public function activeClasses(Request $request)
    {
        try {
            $user = $request->user();
    
            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }
    
            // $activeClasses = RegisterClass::with('kelas.location')
            //     ->where('user_id', $user->id)
            //     ->where('payment_verification', 'lunas')
            //     ->get();
    
// $activeClasses = RegisterClass::with('kelas.location')
//     ->where('user_id', $user->id)
//     ->where('payment_verification', 'lunas')
//     ->whereDoesntHave('absensi', function ($query) {
//         $query->where('status', 'hadir'); // hanya yang belum hadir
//     })
//     ->latest()
//     ->get();

$activeClasses = RegisterClass::with('kelas.location')
    ->where('user_id', $user->id)
    ->where('payment_verification', 'lunas')
    ->latest()
    ->get()
    ->map(function ($item) use ($user) {
        $absen = \App\Models\Absensi::where('user_id', $user->id)
            ->where('class_id', $item->class_id)
            ->first();

        $item->absensi = $absen; // tambahkan property manual
        return $item;
    });

            return response()->json(['data' => $activeClasses]);
    
        } catch (QueryException $e) {
            return response()->json(['message' => 'Database error: ' . $e->getMessage()], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    



}
