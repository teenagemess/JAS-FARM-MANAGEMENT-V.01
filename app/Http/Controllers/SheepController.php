<?php

namespace App\Http\Controllers;

use App\Models\Sheep;
use App\Models\Shelter;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Http\Requests\StoreSheepRequest;
use App\Http\Requests\UpdateSheepRequest;
use App\Models\ProfitLossRecord;
use App\Services\PriceRecommenderService;

class SheepController extends Controller
{
    /**
     * Menampilkan daftar semua data domba dengan fitur Search & Filter.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Sheep::with('shelter', 'partner');

        // FILTER SCOPE MITRA
        if ($user->role === 'mitra') {
            $query->where('partner_id', $user->id);
        }

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
        $partners = User::orderBy('name')->get(['id', 'name']);
        $potential_dams = Sheep::where('gender', 'Betina')->get(['id', 'tag_number']);
        $potential_sires = Sheep::where('gender', 'Jantan')->get(['id', 'tag_number']);

        return view('sheep.create', compact('shelters', 'partners', 'potential_dams', 'potential_sires'));
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

        // Simpan data domba
        $sheep = Sheep::create($data);

        // PERUBAHAN: Logika otomatisasi pencatatan keuangan dihapus.
        // Sekarang harga beli hanya tersimpan di data domba sebagai referensi nilai aset.
        // Jika ingin mencatat pengeluaran kas, silakan lakukan manual di menu Keuangan.

        return redirect()->route('sheep.index')->with('success', 'Data domba baru berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman detail satu domba.
     */
    public function show(Sheep $sheep)
    {
        $sheep->load(['shelter', 'mother', 'father', 'partner']);

        $recommender = new PriceRecommenderService();
        $priceData = $recommender->calculate($sheep);

        $weightRecords = $sheep->weightRecords()
                               ->orderByDesc('weighing_date')
                               ->orderByDesc('id')
                               ->paginate(5, ['*'], 'weight_page');

        $healthRecords = $sheep->healthRecords()
                               ->with('symptoms')
                               ->orderByDesc('record_date')
                               ->orderByDesc('id')
                               ->paginate(5, ['*'], 'health_page');

        return view('sheep.show', compact('sheep', 'weightRecords', 'healthRecords', 'priceData'));
    }

    /**
     * Menampilkan form edit.
     */
    public function edit(Sheep $sheep)
    {
        $shelters = Shelter::orderBy('name')->get();
        $partners = User::orderBy('name')->get(['id', 'name']);
        $potential_dams = Sheep::where('gender', 'Betina')->where('id', '!=', $sheep->id)->get(['id', 'tag_number']);
        $potential_sires = Sheep::where('gender', 'Jantan')->where('id', '!=', $sheep->id)->get(['id', 'tag_number']);
        $tagSuffix = str_replace('JAS-', '', $sheep->tag_number);

        return view('sheep.edit', compact('sheep', 'shelters', 'partners', 'potential_dams', 'potential_sires', 'tagSuffix'));
    }

    /**
     * Memproses update data.
     */
    public function update(UpdateSheepRequest $request, Sheep $sheep)
    {
        $data = $request->validated();

        if ($request->hasFile('photo')) {
            if ($sheep->photo_path) {
                Storage::disk('public')->delete($sheep->photo_path);
            }
            $path = $request->file('photo')->store('sheep_photos', 'public');
            $data['photo_path'] = $path;
        }

        $sheep->update($data);

        return redirect()->route('sheep.show', $sheep)->with('success', 'Data domba berhasil diperbarui.');
    }

    /**
     * Cetak Kartu Ternak (PDF)
     */
    public function printCard(Sheep $sheep)
    {
        $sheep->load(['shelter', 'mother', 'father', 'healthRecords', 'weightRecords']);

        $data = [
            'sheep' => $sheep,
            'farm_name' => 'JAS Farm Sinergi',
            'print_date' => now()->format('d F Y'),
        ];

        $pdf = Pdf::loadView('sheep.print_card', $data);
        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream('Kartu_Ternak_' . $sheep->tag_number . '.pdf');
    }

    /**
     * Menghapus data domba.
     */
    public function destroy(Sheep $sheep)
    {
        if ($sheep->photo_path) {
            Storage::disk('public')->delete($sheep->photo_path);
        }

        $sheep->delete();

        return redirect()->route('sheep.index')->with('success', 'Data domba berhasil dihapus.');
    }
}
