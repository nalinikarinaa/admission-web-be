<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\ClassRoom;

class ClassExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return ClassRoom::with('location')->get()->map(function ($item) {
            return [
                $item->id,
                $item->title,
                $item->description,
                $item->location->name ?? 'Tidak tersedia', // Ambil nama lokasi
                $item->address,
                $item->max_quota,
                $item->current_quota,
                $item->start_time,
                $item->end_time,
                $item->created_at,
                $item->updated_at,
                $item->date,
                $item->price,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Judul Kelas',
            'Deskripsi',
            'Nama Lokasi',
            'Alamat',
            'Kuota Maks',
            'Kuota Saat Ini',
            'Jam Mulai',
            'Jam Selesai',
            'Dibuat Pada',
            'Diperbarui Pada',
            'Tanggal Kelas',
            'Harga'
        ];
    }
}
