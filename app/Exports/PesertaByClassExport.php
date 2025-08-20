<?php

namespace App\Exports;

use App\Models\RegisterClass;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PesertaByClassExport implements FromCollection, WithHeadings
{
    protected $classId;

    public function __construct($classId)
    {
        $this->classId = $classId;
    }

    public function collection()
    {
        return RegisterClass::where('class_id', $this->classId)
            ->select('nama', 'phone_number', 'email', 'class_verification', 'payment_verification')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Nomor Telepon',
            'Email',
            'Status Kelas',
            'Status Pembayaran',
        ];
    }
}

