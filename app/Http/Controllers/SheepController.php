<?php

namespace App\Http\Controllers;

use App\Models\Sheep;
use App\Models\Shelter;
use App\Http\Requests\StoreSheepRequest;
use App\Http\Requests\UpdateSheepRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class SheepController extends Controller
{
    /**
     * Menampilkan daftar semua data domba.
     */
    public function index()
    {
        // (Gunakan with() untuk Eager Loading - lebih efisien)
        $sheep = Sheep::with('shelter')
                      ->latest() // Urutkan berdasarkan yang terbaru
                      ->paginate(12); // Ambil 12 data per halaman (Bagus untuk grid 3-4 kolom)

        return view('sheep.index', compact('sheep'));
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
        // Muat relasi silsilah (opsional, tapi bagus)
        $sheep->load(['shelter', 'mother', 'father']);

        return view('sheep.show', compact('sheep'));
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
