<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\V1\ClassRoomController;
use App\Http\Controllers\API\V1\AuthController;
use App\Http\Controllers\API\V1\ProfileController;
use App\Http\Controllers\API\V1\LocationController;
use App\Http\Controllers\API\V1\RegisterClassController;
use App\Http\Controllers\API\V1\AdminClassParticipantController;
use App\Http\Controllers\API\V1\AbsensiParticipantController;
use App\Http\Controllers\API\V1\FeedbackParticipantController;
use App\Http\Controllers\API\V1\TransactionController;
use App\Http\Controllers\API\V1\AbsensiController;
use App\Http\Controllers\API\V1\FeedbackController;
use App\Http\Controllers\API\V1\DashboardController;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegisterExport;
use App\Exports\ClassExport;
use App\Exports\PesertaByClassExport;

use App\Models\User;
use Illuminate\Auth\Events\Verified;


// Cek user yang login
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Grup route v1
Route::prefix('v1')->group(function () {

    // Auth API
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    // Class Room API
    Route::prefix('classes')->group(function () {
        Route::get('/getclass', [ClassRoomController::class, 'index']);
        Route::post('/postclass', [ClassRoomController::class, 'store']);
        Route::get('/{id}detailclass', [ClassRoomController::class, 'show']);
        Route::put('/{id}updateclass', [ClassRoomController::class, 'update']);
        // Route::put('/classes/{id}/updateclass', [ClassController::class, 'update']);
        Route::delete('/{id}deleteclass', [ClassRoomController::class, 'destroy']);
    });

    // Profile API
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
    });

    // Dashboard API (role based)
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::get('/dashboard/admin', fn() => response()->json(['message' => 'Halaman Admin']));
    });

    Route::middleware(['auth:sanctum', 'role:user'])->group(function () {
        Route::get('/dashboard/user', fn() => response()->json(['message' => 'Halaman User']));
    });

    Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
        $user = User::find($id);
    
        if (!$user) {
            return response()->json(['message' => 'User not found.'], 404);
        }
    
        if (! hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'Invalid or expired verification link.'], 403);
        }
    
        if ($user->hasVerifiedEmail()) {
            return redirect('http://localhost:5173/login?verified=already');
        }
    
        $user->markEmailAsVerified();
        event(new Verified($user));
    
        return redirect('http://localhost:5173/login?verified=success');
    })->middleware(['signed'])->name('api.verification.verify'); // Ganti nama route di sini
    

    // LOCATION
    Route::prefix('location')->group(function () {
        Route::get('/locationlist', [LocationController::class, 'location']);
    });
    

    // REGISTER KELAS
    // Route::prefix('registerclass')->group(function () {
    //     Route::post('registerclass', [RegisterClassController::class, 'store']);
    // });
    Route::middleware('auth:sanctum')->post('/registerclass/registerclass', [RegisterClassController::class, 'store']);
    Route::put('/admin/registerclass/{id}/verifikasi', [RegisterClassController::class, 'updateVerifikasi']);
    Route::middleware('auth:sanctum')->get('/registerclass/check', [RegisterClassController::class, 'checkIfRegistered']);
    // GET route untuk mengambil data register class berdasarkan ID

    // TAMPIL KELAS BERDASAR ID DI CLASS MANAJEMEN
    Route::get('/admin/classes/{id}/participants', [AdminClassParticipantController::class, 'getParticipantsByClassId']);
    Route::put('/admin/registerclass/{id}/verifikasi', [RegisterClassController::class, 'updateVerifikasi']);

    // TAMPIL DATA DI TRANSAKSI USER
    Route::middleware('auth:sanctum')->get('/user/transactions', [TransactionController::class, 'index']);
    Route::middleware('auth:sanctum')->get('/user/active-classes', [TransactionController::class, 'activeClasses']); 
    
// Route::middleware('auth:api')->get('/user/transactions', [TransactionController::class, 'index']);

    // ABSENSI  
    Route::middleware('auth:sanctum')->post('/absensi', [AbsensiController::class, 'store']);
    // TAMPIL DATA DI DETAIL CLASS HISTORY
    Route::get('/admin/classabsensi/{id}/participants', [AbsensiParticipantController::class, 'getParticipantsByClassId']);
    Route::put('/admin/absensiclass/{id}/verifikasi', [AbsensiController::class, 'updateVerifikasi']);
    Route::delete('/absensi/{id}', [AbsensiController::class, 'destroy']);


    // FEEDBACK 
    Route::middleware('auth:sanctum')->post('/feedback', [FeedbackController::class, 'store']);
        Route::put('/admin/feedback/{id}/verifikasi', [FeedbackController::class, 'updateFeedback']);
        Route::get('/admin/feedback/{id}/participants', [FeedbackParticipantController::class, 'getParticipantsByClassId']);
        Route::get('/feedback/verified', [FeedbackController::class, 'getVerifiedFeedback']);

        // Route::get('/admin/feedback/{classId}/participants', [FeedbackParticipantController::class, 'getParticipantsByClassId']);

        // CHART
        Route::get('/classroom/kelaskosong', [ClassRoomController::class, 'getKelasKosongPerTahun']);
        Route::get('/dashboard/statistik', [DashboardController::class, 'getStatistik']);
        Route::get('/dashboard/statistik-tahunan', [DashboardController::class, 'getStatistikTahunan']);
        Route::get('/dashboard/kelas-kosong', [DashboardController::class, 'kelasKosong']);
        Route::get('/dashboard/kelas-aktif', [DashboardController::class, 'kelasAktif']);
        Route::get('/dashboard/pendaftar-aktif', [DashboardController::class, 'pendaftarAktifList']);



        // EXPORT 
        Route::get('/export/class', function () {
            return Excel::download(new ClassExport, 'kelas.xlsx');
        });
        Route::get('/export/peserta', function () {
            return Excel::download(new RegisterExport, 'data_peserta.xlsx');
        });
        Route::get('/export/peserta/perkelas/{id}', function ($id) {
            return Excel::download(new PesertaByClassExport($id), 'peserta_kelas_' . $id . '.xlsx');
        });


});
