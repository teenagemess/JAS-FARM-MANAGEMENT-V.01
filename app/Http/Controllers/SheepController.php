<?php

namespace App\Http\Controllers;

use App\Models\Sheep;
use App\Models\Shelter;
use App\Models\ProfitLossRecord; // (1) Impor Model Keuangan
use App\Http\Requests\StoreSheepRequest;
use App\Http\Requests\UpdateSheepRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Services\PriceRecommenderService; // (BARU) Impor Service

class SheepController extends Controller
{
    /**
     * Menampilkan daftar semua data domba dengan fitur Search & Filter.
     */
    public function index(Request $request)
    {
        // ... (Logika Index sama) ...
        $query = Sheep::with('shelter');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('tag_number', 'like', "%{$search}%");
        }

        if ($request->filled('gender')) {
            $query->where('gender', $request->input('gender'));
        }

        if ($request->filled('shelter_id')) {
            $query->where('shelter_id', $request->input('shelter_id'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $sheep = $query->latest()
                       ->paginate(12)
                       ->withQueryString();

        $shelters = Shelter::orderBy('name')->get(['id', 'name']);
        $categories = Sheep::select('category')->distinct()->pluck('category');

        return view('sheep.index', compact('sheep', 'shelters', 'categories'));
    }

    /**
     * Menampilkan form untuk membuat domba baru.
     */
    public function create()
    {
        $shelters = Shelter::orderBy('name')->get();
        $potential_dams = Sheep::where('gender', 'Betina')->get(['id', 'tag_number']);
        $potential_sires = Sheep::where('gender', 'Jantan')->get(['id', 'tag_number']);

        return view('sheep.create', compact('shelters', 'potential_dams', 'potential_sires'));
    }

    /**
     * Menyimpan data domba baru ke database.
     */
    public function store(StoreSheepRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('sheep_photos', 'public');
            $data['photo_path'] = $path;
        }

        $data['user_id'] = Auth::id();

        $sheep = Sheep::create($data);

        // OTOMATIS CATAT PENGELUARAN JIKA ADA HARGA BELI
        if ($sheep->purchase_price > 0) {
            ProfitLossRecord::create([
                'date' => now(),
                'type' => 'expense',
                'category' => 'Pembelian Domba',
                'amount' => $sheep->purchase_price,
                'description' => "Pembelian Domba Baru: {$sheep->tag_number}",
                'sheep_id' => $sheep->id,
                'shelter_id' => $sheep->shelter_id,
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()->route('sheep.index')->with('success', 'Data domba baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman detail satu domba.
     */
    public function show(Sheep $sheep)
    {
        // Muat relasi yang diperlukan untuk tampilan
        $sheep->load(['shelter', 'mother', 'father']);

        // 1. PANGGIL SERVICE REKOMENDASI HARGA
        $recommender = new PriceRecommenderService();
        $priceData = $recommender->calculate($sheep);

        // 2. Ambil Data Timbangan (Paginate: 5 per halaman)
        $weightRecords = $sheep->weightRecords()
                               ->orderByDesc('weighing_date')
                               ->orderByDesc('id')
                               ->paginate(5, ['*'], 'weight_page');

        // 3. Ambil Data Kesehatan (Paginate: 5 per halaman)
        $healthRecords = $sheep->healthRecords()
                               ->with('symptoms')
                               ->orderBy('record_date', 'desc')
                               ->orderBy('id', 'desc')
                               ->paginate(5, ['*'], 'health_page');

        // Kirim data harga ke view
        return view('sheep.show', compact('sheep', 'weightRecords', 'healthRecords', 'priceData'));
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(Sheep $sheep)
    {
        $shelters = Shelter::orderBy('name')->get();
        $potential_dams = Sheep::where('gender', 'Betina')->where('id', '!=', $sheep->id)->get(['id', 'tag_number']);
        $potential_sires = Sheep::where('gender', 'Jantan')->where('id', '!=', $sheep->id)->get(['id', 'tag_number']);

        $tagSuffix = str_replace('JAS-', '', $sheep->tag_number);

        return view('sheep.edit', compact('sheep', 'shelters', 'potential_dams', 'potential_sires', 'tagSuffix'));
    }

    /**
     * Memproses update data.
     */
    public function update(UpdateSheepRequest $request, Sheep $sheep)
    {
        $data = $request->validated();

        // Logika update foto (jika ada foto baru)
        if ($request->hasFile('photo')) {
            if ($sheep->photo_path) {
                Storage::disk('public')->delete($sheep->photo_path);
            }

            $path = $request->file('photo')->store('sheep_photos', 'public');
            $data['photo_path'] = $path;
        }

        $sheep->update($data);

        // Redirect ke halaman show (detail)
        return redirect()->route('sheep.show', $sheep)->with('success', 'Data domba berhasil diperbarui.');
    }
}
