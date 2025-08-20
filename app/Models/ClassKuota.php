<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassKuota extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan (opsional jika menggunakan nama tabel default)
    protected $table = 'class_kuotas';

    // Kolom yang dapat diisi (mass assignment)
    protected $fillable = [
        'class_id',
        'total_quota',
        'quota_available',
    ];

    /**
     * Relasi ke ClassRoom (setiap kuota kelas terkait dengan satu kelas)
     */
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}


