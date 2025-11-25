<?php

namespace App\Http\Controllers;

use App\Models\Sheep;
use App\Models\Shelter;
use App\Http\Requests\StoreSheepRequest;
use App\Http\Requests\UpdateSheepRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SheepController extends Controller
{
    /**
     * Menampilkan daftar semua data domba.
     */
    public function index(Request $request)
    {
        // (2) Mulai Query Builder
        $query = Sheep::with('shelter');

        // (3) Logika Search (Eartag)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('tag_number', 'like', "%{$search}%");
        }

        // (4) Logika Filter Gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        // (5) Logika Filter Kandang
        if ($request->filled('shelter_id')) {
            $query->where('shelter_id', $request->input('shelter_id'));
        }

        // (6) Logika Filter Kategori
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Ambil data (Paginate + Query String agar filter tidak hilang saat ganti halaman)
        $sheep = $query->latest()
                       ->paginate(12)
                       ->withQueryString();

        // (7) Ambil data untuk opsi filter dropdown
        $shelters = Shelter::orderBy('name')->get(['id', 'name']);
        // Ambil kategori unik yang ada di database
        $categories = Sheep::select('category')->distinct()->pluck('category');

        return view('sheep.index', compact('sheep', 'shelters', 'categories'));
    }

    /**
     * Menampilkan form untuk membuat domba baru.
     */
    public function create()
    {
        $shelters = Shelter::orderBy('name')->get();
        // Ambil domba betina sebagai calon induk
        $potential_dams = Sheep::where('gender', 'Betina')->get(['id', 'tag_number']);
        // Ambil domba jantan sebagai calon pejantan
        $potential_sires = Sheep::where('gender', 'Jantan')->get(['id', 'tag_number']);

        return view('sheep.create', compact('shelters', 'potential_dams', 'potential_sires'));
    }

    /**
     * Menyimpan data domba baru ke database.
     */
    public function store(StoreSheepRequest $request)
    {
        // Data sudah divalidasi DAN diformat (misal tag_number sudah "JAS-123")
        $data = $request->validated();

        // Proses upload file jika ada
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('sheep_photos', 'public');
            $data['photo_path'] = $path;
        }

        // Tambahkan ID pengguna yang sedang login ke array data
        $data['user_id'] = Auth::id();

        // Buat domba baru dengan semua data (termasuk user_id)
        Sheep::create($data);

        return redirect()->route('sheep.index')->with('success', 'Data domba baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman detail satu domba.
     */
    public function show(Sheep $sheep)
    {
        // Muat relasi statis (silsilah & kandang)
        $sheep->load(['shelter', 'mother', 'father']);

        // 1. Ambil Data Timbangan (Paginate: 5 per halaman)
        // Gunakan nama parameter 'weight_page' agar tidak bentrok
        $weightRecords = $sheep->weightRecords()
                               ->orderBy('weighing_date', 'desc')
                               ->orderBy('id', 'desc')
                               ->paginate(5, ['*'], 'weight_page');

        // 2. Ambil Data Kesehatan (Paginate: 5 per halaman)
        // Gunakan nama parameter 'health_page'
        $healthRecords = $sheep->healthRecords()
                               ->with('symptoms')
                               ->orderBy('record_date', 'desc')
                               ->orderBy('id', 'desc')
                               ->paginate(5, ['*'], 'health_page');

        return view('sheep.show', compact('sheep', 'weightRecords', 'healthRecords'));
    }

    /**
     * Menampilkan form edit.
     * (PERUBAHAN: Menambahkan $tagSuffix)
     */
    public function edit(Sheep $sheep)
    {
        $shelters = Shelter::orderBy('name')->get();
        $potential_dams = Sheep::where('gender', 'Betina')->where('id', '!=', $sheep->id)->get(['id', 'tag_number']);
        $potential_sires = Sheep::where('gender', 'Jantan')->where('id', '!=', $sheep->id)->get(['id', 'tag_number']);

        // (1) Hapus awalan "JAS-" dari eartag untuk ditampilkan di form
        $tagSuffix = str_replace('JAS-', '', $sheep->tag_number);

        return view('sheep.edit', compact('sheep', 'shelters', 'potential_dams', 'potential_sires', 'tagSuffix'));
    }

    /**
     * Memproses update data.
     */
    public function update(UpdateSheepRequest $request, Sheep $sheep)
    {
        // Data sudah divalidasi DAN diformat (misal tag_number sudah "JAS-123")
        $data = $request->validated();

        // Logika update foto (jika ada foto baru)
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($sheep->photo_path) {
                Storage::disk('public')->delete($sheep->photo_path);
            }

            // Simpan foto baru
            $path = $request->file('photo')->store('sheep_photos', 'public');
            $data['photo_path'] = $path;
        }

        // Update data domba
        $sheep->update($data);

        // Redirect ke halaman show (detail)
        return redirect()->route('sheep.show', $sheep)->with('success', 'Data domba berhasil diperbarui.');
    }
}
