<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan (opsional jika menggunakan nama tabel default)
    protected $table = 'locations';

    // Kolom yang dapat diisi (mass assignment)
    protected $fillable = [
        'name',
        'address',
    ];

    /**
     * Relasi ke ClassRoom (satu lokasi bisa memiliki banyak kelas)
     */
    public function classRooms()
    {
        return $this->hasMany(ClassRoom::class, 'location_id');
    }
}
