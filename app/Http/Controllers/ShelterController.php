<?php

namespace App\Http\Controllers;

use App\Models\Shelter;
use App\Http\Requests\StoreShelterRequest;
use App\Http\Requests\UpdateShelterRequest;
use Illuminate\Support\Facades\Auth;

class ShelterController extends Controller
{
    /**
     * Menampilkan daftar Kandang dalam format tabel.
     */
    public function index()
    {
        // Muat juga relasi sheep untuk menghitung jumlah domba
        $shelters = Shelter::with('sheep')->orderBy('name')->get();

        return view('shelters.index', compact('shelters'));
    }

    /**
     * Menampilkan form untuk membuat Kandang baru.
     */
    public function create()
    {
        return view('shelters.create');
    }

    /**
     * Menyimpan Kandang baru ke database.
     */
    public function store(StoreShelterRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id(); // Tambahkan user ID yang input

        Shelter::create($data);

        return redirect()->route('shelters.index')->with('success', 'Kandang baru berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit Kandang.
     */
    public function edit(Shelter $shelter)
    {
        return view('shelters.edit', compact('shelter'));
    }

    /**
     * Memproses update data Kandang.
     */
    public function update(UpdateShelterRequest $request, Shelter $shelter)
    {
        $shelter->update($request->validated());

        return redirect()->route('shelters.index')->with('success', 'Data Kandang berhasil diperbarui!');
    }

    /**
     * Menghapus Kandang.
     */
    public function destroy(Shelter $shelter)
    {
        // PENTING: Cek apakah kandang memiliki domba aktif sebelum dihapus
        if ($shelter->sheep()->count() > 0) {
            return redirect()->route('shelters.index')->with('error', 'Kandang tidak dapat dihapus karena masih menampung domba!');
        }

        $shelter->delete();

        return redirect()->route('shelters.index')->with('success', 'Kandang berhasil dihapus.');
    }

    /**
     * (BARU) API untuk mendapatkan data kapasitas kandang secara realtime.
     */
    public function getCapacity(Shelter $shelter)
    {
        // Mengembalikan data JSON
        return response()->json([
            'name' => $shelter->name,
            'capacity' => $shelter->capacity,
            'current' => $shelter->current_count, // Menggunakan accessor yang sudah dibuat di Model
            'remaining' => $shelter->capacity - $shelter->current_count
        ]);
    }
}
