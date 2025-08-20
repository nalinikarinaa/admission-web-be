<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterClass extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak sesuai dengan konvensi Laravel
    protected $table = 'register_class';

    // Tentukan kolom yang dapat diisi secara massal (mass assignable)
    protected $fillable = [
        'nama',
        'phone_number',
        'instagram',
        'email',
        'payment',
        'user_id',
        'class_id',
    ];
    
    // public function kelas()
    // {
    //     return $this->belongsTo(Kelas::class, 'class_id'); // Pastikan 'class_id' sesuai dengan kolom di database
    // }

    // // // Relasi dengan model User
    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'user_id'); // Pastikan 'user_id' sesuai dengan kolom di database
    // }

     // Relasi dengan model ClassRoom (kelas)
     public function kelas()
     {
         return $this->belongsTo(ClassRoom::class, 'class_id'); // Pastikan nama relasi sesuai dengan yang digunakan
     }
 
     // Relasi dengan model User
     public function user()
     {
         return $this->belongsTo(User::class, 'user_id');
     }

//      public function absensi()
// {
//     return $this->hasOne(Absensi::class, 'class_id', 'class_id');
// }


     public function absensi()
{
    return $this->hasOne(Absensi::class, 'class_id', 'class_id')
                ->where('user_id', $this->user_id);
}

public function registerClasses()
{
    return $this->hasMany(RegisterClass::class, 'class_id');
}

public function classRoom()
{
    return $this->belongsTo(ClassRoom::class, 'class_id');
}

    // // Definisikan relasi dengan model Kelas
    // public function kelas()
    // {
    //     return $this->belongsTo(Kelas::class, 'id_kelas');
    // }

    // // Definisikan relasi dengan model User
    // public function user()
    // {
    //     return $this->belongsTo(User::class, 'id_user');
    // }

}
