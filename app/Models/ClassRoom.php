<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan (opsional jika menggunakan nama tabel default)
    protected $table = 'class_rooms';

    // Kolom yang dapat diisi (mass assignment)
    protected $fillable = [
        'title',
        'description',
        'location_id',
        'max_quota',
        'current_quota',
        'start_time',
        'end_time',
        'date',
        'photo',
        'price',
        'address'
    ];

    /**
     * Relasi ke Location (satu kelas hanya ada satu lokasi)
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Relasi ke Absensi (satu kelas bisa memiliki banyak absensi)
     */
    public function absensis()
    {
        return $this->hasMany(Absensi::class, 'class_id');
    }

    /**
     * Relasi ke ClassKuota (satu kelas memiliki satu kuota)
     */
    public function classKuota()
    {
        return $this->hasOne(ClassKuota::class, 'class_id');
    }
    public function feedbacks()
    {
        return $this->hasMany(Feedback::class, 'class_id');
    }
    public function registerClasses()
    {
        return $this->hasMany(RegisterClass::class, 'class_id');
    }

    
}
