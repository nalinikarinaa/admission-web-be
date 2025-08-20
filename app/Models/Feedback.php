<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;


class Feedback extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan (opsional jika menggunakan nama tabel default)
    protected $table = 'feedback';

    // Kolom yang dapat diisi (mass assignment)
    protected $fillable = [
        'user_id',
        'class_id',
        'comment',
        'status',
    ];

    /**
     * Relasi ke User (setiap feedback dimiliki oleh satu user)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke ClassRoom (setiap feedback terkait dengan satu kelas)
     */
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}

