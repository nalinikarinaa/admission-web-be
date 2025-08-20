<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // <-- Tambahkan ini untuk hapus file nantinya
use Illuminate\Support\Facades\DB;


class ClassRoomController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'description' => 'required|string',
            'location_id' => 'required|exists:locations,id',
            'max_quota' => 'required|integer',
            'start_time' => 'required',
            'end_time' => 'required',
            'date' => 'required|date',
            'price' => 'required|integer',
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'address' => 'required|string|max:255',

        ]);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/class_photos', $filename);
        } else {
            $filename = null;
        }

        $classRoom = ClassRoom::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'location_id' => $validated['location_id'],
            'max_quota' => $validated['max_quota'],
            'current_quota' => 0,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'date' => $validated['date'],
            'price' => $validated['price'],
            'photo' => $filename,
            'address' => $validated['address'],

        ]);


        $classRoom->remaining_quota = $classRoom->max_quota - $classRoom->current_quota;

        if ($classRoom->photo) {
            $classRoom->photo = url('storage/class_photos/' . $classRoom->photo);
        }

        return response()->json([
            'message' => 'Class created successfully',
            'data' => $classRoom
        ]);
    }

    public function index()
{
    // Ambil data kelas terbaru terlebih dahulu
    $classRooms = ClassRoom::with('location')
        ->latest() // urutkan berdasarkan created_at DESC
        ->get();

    // Tambahkan remaining_quota dan URL foto
    $classRooms->transform(function ($classRoom) {
        $classRoom->remaining_quota = $classRoom->max_quota - $classRoom->current_quota;
        if ($classRoom->photo && !str_starts_with($classRoom->photo, 'http')) {
            $classRoom->photo = url('storage/class_photos/' . $classRoom->photo);
        } else {
            $classRoom->photo = null;
        }
        return $classRoom;
    });

    return response()->json([
        'message' => 'List of classes',
        'data' => $classRooms
    ]);
}


    public function update(Request $request, $id)
    {
        // Temukan kelas berdasarkan ID
        $classRoom = ClassRoom::findOrFail($id);
    
        // Validasi input
        $validated = $request->validate([
            'title' => 'sometimes|required|string',
            'description' => 'sometimes|required|string',
            'location_id' => 'sometimes|required|exists:locations,id',
            'max_quota' => 'sometimes|required|integer',
            'start_time' => 'sometimes|required',
            'end_time' => 'sometimes|required',
            'date' => 'sometimes|required|date',
            'price' => 'sometimes|required|integer',
            'photo' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
            'address' => 'sometimes|string|max:255',
        ]);
    
        // Cek jika ada file foto baru
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($classRoom->photo && Storage::exists('public/class_photos/' . basename($classRoom->photo))) {
                Storage::delete('public/class_photos/' . basename($classRoom->photo));
            }
    
           // Upload foto baru
            $file = $request->file('photo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/class_photos', $filename);

            // Simpan nama file foto ke database
            $classRoom->photo = $filename;
            }

            // Hindari menimpa photo dengan data dari form
            unset($validated['photo']);
            $classRoom->fill($validated);
            $classRoom->save();

    
        // Siapkan URL foto untuk response
        $photoUrl = $classRoom->photo ? url('storage/class_photos/' . $classRoom->photo) : null;
    
        // Response JSON
        return response()->json([
            'message' => 'Class updated successfully',
            'data' => [
                'id' => $classRoom->id,
                'title' => $classRoom->title,
                'description' => $classRoom->description,
                'location_id' => $classRoom->location_id,
                'max_quota' => $classRoom->max_quota,
                'start_time' => $classRoom->start_time,
                'end_time' => $classRoom->end_time,
                'date' => $classRoom->date,
                'price' => $classRoom->price,
                'photo' => $photoUrl,
                'address' => $classRoom->address,

            ]
        ]);
    }
    

    
    public function destroy($id)
    {
        $classRoom = ClassRoom::findOrFail($id);

        // Hapus foto dari storage kalau ada
        if ($classRoom->photo && Storage::exists('public/class_photos/' . basename($classRoom->photo))) {
            Storage::delete('public/class_photos/' . basename($classRoom->photo));
        }

        $classRoom->delete();

        return response()->json([
            'message' => 'Class deleted successfully'
        ]);
    }

    // MENAMPILKAN NAMA KELAS SESUAI ID DI CLASS MANAJEMEN 
    public function show($id)
{
    $class = ClassRoom::find($id);

    if (!$class) {
        return response()->json(['message' => 'Kelas tidak ditemukan.'], 404);
    }

     // Tambahkan sisa kuota
     $class->remaining_quota = $class->max_quota - $class->current_quota;

    return response()->json([
        'message' => 'Detail kelas berhasil diambil.',
        'data' => $class
    ]);
}

public function getKelasKosongPerTahun()
{
    $kelasKosongPerTahun = DB::table('class_rooms')
        ->select(DB::raw('YEAR(date) as tahun'), DB::raw('COUNT(*) as jumlah'))
        ->whereColumn('current_quota', '<', 'max_quota') // atau ->where('current_quota', '<', 5)
        ->groupBy(DB::raw('YEAR(date)'))
        ->orderBy('tahun')
        ->get();

    return response()->json([
        'message' => 'Data kelas kosong berhasil diambil.',
        'data' => $kelasKosongPerTahun
    ]);
}
    
}
