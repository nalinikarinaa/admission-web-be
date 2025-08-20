<?php

namespace App\Exports;

use App\Models\RegisterClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RegisterExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return RegisterClass::with('kelas')->get()->map(function ($item) {
            return [
                $item->id,
                $item->nama,
                $item->phone_number,
                $item->instagram,
                $item->email,
                $item->payment,
                $item->payment_verification,
                $item->class_verification,
                // $item->user_id,
                $item->kelas->title ?? 'Tidak tersedia', // Ganti class_id → nama kelas
                $item->created_at,
                $item->updated_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama',
            'Nomor Telepon',
            'Instagram',
            'Email',
            'Bukti Pembayaran',
            'Verifikasi Pembayaran',
            'Status Kelas',
            // 'User ID',
            'Nama Kelas', // ubah heading agar lebih jelas
            'Dibuat Pada',
            'Diperbarui Pada'
        ];
    }
}
