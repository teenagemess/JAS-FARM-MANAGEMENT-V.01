<?php

namespace App\Http\Controllers;

use App\Models\Shelter;
use App\Http\Requests\StoreShelterRequest;
use App\Http\Requests\UpdateShelterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // (1) Tambahkan Import Request

class ShelterController extends Controller
{
    /**
     * Menampilkan daftar Kandang dengan Search, Filter, dan Pagination.
     */
    public function index(Request $request)
    {
        // Gunakan withCount('sheep') agar kita bisa mengurutkan berdasarkan jumlah domba
        $query = Shelter::withCount('sheep');

        // 1. Logika Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // 2. Logika Sorting
        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'fullest': // Terisi Paling Banyak
                    $query->orderByDesc('sheep_count');
                    break;
                case 'emptiest': // Paling Kosong
                    $query->orderBy('sheep_count');
                    break;
                case 'capacity_high': // Kapasitas Besar
                    $query->orderByDesc('capacity');
                    break;
                case 'newest': // Terbaru Dibuat
                    $query->orderByDesc('created_at');
                    break;
                default:
                    $query->orderBy('name');
            }
        } else {
            $query->orderBy('name'); // Default urut nama A-Z
        }

        // 3. Pagination (9 per halaman agar pas di grid 3 kolom)
        $shelters = $query->paginate(9)->withQueryString();

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
