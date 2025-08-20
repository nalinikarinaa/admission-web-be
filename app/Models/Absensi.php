<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan (opsional jika menggunakan nama tabel default)
    protected $table = 'absensis';

    // Kolom yang bisa diisi (mass assignment)
    protected $fillable = [
        'user_id',
        'class_id',
        'foto_kehadiran',
        'status',
        'nama',
    ];

    /**
     * Relasi ke User (satu absensi milik satu user)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    
    /**
     * Relasi ke ClassRoom (satu absensi milik satu kelas)
     */
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}
