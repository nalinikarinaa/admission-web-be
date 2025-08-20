<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ClassRoom;
use App\Models\RegisterClass;

class DashboardController extends Controller
{
    public function getStatistik()
    {
        $kelasAktif = ClassRoom::whereColumn('current_quota', '>=', 'max_quota')->count();
        $kelasKosong = ClassRoom::whereColumn('current_quota', '<', 'max_quota')->count();
        $pendaftarAktif = RegisterClass::where('class_verification', 'aktif')->count();
    
        return response()->json([
            'kelas_aktif' => $kelasAktif,
            'kelas_kosong' => $kelasKosong,
            'pendaftar_aktif' => $pendaftarAktif
        ]);
    }

    public function getStatistikTahunan()
{
    $tahunData = ClassRoom::selectRaw('YEAR(date) as tahun')
        ->groupBy('tahun')
        ->orderBy('tahun')
        ->get()
        ->pluck('tahun');

    $data = [];

    foreach ($tahunData as $tahun) {
        $kelasAktif = ClassRoom::whereYear('date', $tahun)
            ->whereColumn('current_quota', '>=', 'max_quota')
            ->count();

        $kelasKosong = ClassRoom::whereYear('date', $tahun)
            ->whereColumn('current_quota', '<', 'max_quota')
            ->count();

        $pendaftarAktif = RegisterClass::whereYear('created_at', $tahun)
            ->where('class_verification', 'aktif')
            ->count();

        $data[] = [
            'tahun' => $tahun,
            'kelas_aktif' => $kelasAktif,
            'kelas_kosong' => $kelasKosong,
            'pendaftar_aktif' => $pendaftarAktif
        ];
    }

    return response()->json([
        'data' => $data
    ]);
}

public function kelasKosong()
{
    $kelasKosong = ClassRoom::where('max_quota', '>', 0)
        ->whereColumn('current_quota', '<', 'max_quota')
        ->get(['id', 'title as nama', 'max_quota', 'current_quota']);

    return response()->json($kelasKosong);
}

public function kelasAktif()
{
    $kelasAktif = ClassRoom::whereColumn('current_quota', '>=', 'max_quota')
        ->get(['id', 'title as nama', 'max_quota', 'current_quota'])
        ->map(function ($kelas) {
            $kelas->remaining_quota = $kelas->max_quota - $kelas->current_quota;
            return $kelas;
        });

    return response()->json($kelasAktif);
}

public function pendaftarAktifList()
{
    $pendaftarAktif = RegisterClass::with(['user', 'classRoom'])
        ->where('class_verification', 'aktif')
        ->get()
        ->map(function ($item) {
            return [
                'id' => $item->id,
                'nama_user' => $item->user->name ?? 'Tidak diketahui',
                'nama_kelas' => $item->classRoom->title ?? '-',
                'tanggal_daftar' => $item->created_at->format('d-m-Y'),
            ];
        });

    return response()->json($pendaftarAktif);
}

}
