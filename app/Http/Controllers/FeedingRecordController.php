<?php

namespace App\Http\Controllers;

use App\Models\Shelter;
use App\Models\FeedType;
use App\Models\FeedingRecord;
use App\Http\Requests\StoreFeedingRecordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request; // (1) PENTING: Tambahkan ini untuk menangkap filter

class FeedingRecordController extends Controller
{
    // (2) UPDATE METHOD INDEX: Tambahkan Request $request
    public function index(Request $request)
    {
        // Mulai query dasar
        $query = FeedingRecord::with(['shelter', 'user', 'feedTypes']);

        // Logika Filter: Tanggal Mulai
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }

        // Logika Filter: Tanggal Akhir
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Logika Filter: Kandang
        if ($request->filled('shelter_id')) {
            $query->where('shelter_id', $request->shelter_id);
        }

        // Eksekusi query dengan urutan tanggal terbaru
        // withQueryString() penting agar filter tidak hilang saat klik halaman 2, 3, dst.
        $feedingRecords = $query->orderByDesc('date')
                                ->paginate(10)
                                ->withQueryString();

        // Ambil data shelter untuk dropdown filter di View
        $shelters = Shelter::orderBy('name')->get(['id', 'name']);

        return view('feeding_records.index', compact('feedingRecords', 'shelters'));
    }

    public function create()
    {
        $shelters = Shelter::orderBy('name')->get(['id', 'name']);
        $feedTypes = FeedType::orderBy('name')->get(['id', 'name', 'unit', 'price_per_unit']);

        return view('feeding_records.create', compact('shelters', 'feedTypes'));
    }

    public function store(StoreFeedingRecordRequest $request)
    {
        $data = $request->validated();

        $exists = FeedingRecord::where('shelter_id', $data['shelter_id'])
                               ->where('date', $data['date'])
                               ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Catatan pakan untuk kandang ini pada tanggal ini sudah ada!');
        }

        $data['user_id'] = Auth::id();

        $record = FeedingRecord::create($data);

        $pivotData = $this->preparePivotData($data['feed_types']);
        $record->feedTypes()->attach($pivotData);

        return redirect()->route('feeding-records.index')->with('success', 'Pencatatan pakan harian berhasil!');
    }

    public function show(FeedingRecord $feedingRecord)
    {
        $feedingRecord->load(['shelter', 'user', 'feedTypes']);

        return view('feeding_records.show', compact('feedingRecord'));
    }

    public function edit(FeedingRecord $feedingRecord)
    {
        $shelters = Shelter::orderBy('name')->get(['id', 'name']);
        $feedTypes = FeedType::orderBy('name')->get(['id', 'name', 'unit', 'price_per_unit']);

        $feedingRecord->load('feedTypes');

        return view('feeding_records.edit', compact('feedingRecord', 'shelters', 'feedTypes'));
    }

    public function update(StoreFeedingRecordRequest $request, FeedingRecord $feedingRecord)
    {
        $data = $request->validated();

        $exists = FeedingRecord::where('shelter_id', $data['shelter_id'])
                               ->where('date', $data['date'])
                               ->where('id', '!=', $feedingRecord->id)
                               ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Catatan pakan untuk kandang ini pada tanggal ini sudah ada!');
        }

        $feedingRecord->update([
            'shelter_id' => $data['shelter_id'],
            'date' => $data['date'],
            'time_morning' => $data['time_morning'],
            'time_evening' => $data['time_evening'],
        ]);

        $pivotData = $this->preparePivotData($data['feed_types']);
        $feedingRecord->feedTypes()->sync($pivotData);

        return redirect()->route('feeding-records.index')->with('success', 'Data pakan berhasil diperbarui!');
    }

    public function destroy(FeedingRecord $feedingRecord)
    {
        $feedingRecord->feedTypes()->detach();
        $feedingRecord->delete();

        return redirect()->route('feeding-records.index')->with('success', 'Data pakan berhasil dihapus.');
    }

    private function preparePivotData($feedTypesInput)
    {
        return collect($feedTypesInput)->mapWithKeys(function ($feed) {
            $morning = (float) ($feed['morning'] ?? 0);
            $evening = (float) ($feed['evening'] ?? 0);

            if ($morning > 0 || $evening > 0) {
                 return [
                    $feed['id'] => [
                        'quantity_morning' => $morning,
                        'quantity_evening' => $evening
                    ]
                ];
            }
            return [];
        })->toArray();
    }
}
