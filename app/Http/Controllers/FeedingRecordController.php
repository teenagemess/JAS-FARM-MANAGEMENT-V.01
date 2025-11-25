<?php

namespace App\Http\Controllers;

use App\Models\Shelter;
use App\Models\FeedType;
use App\Models\FeedingRecord;
use App\Http\Requests\StoreFeedingRecordRequest;
use Illuminate\Support\Facades\Auth;

class FeedingRecordController extends Controller
{
    public function index()
    {
        $feedingRecords = FeedingRecord::with(['shelter', 'user', 'feedTypes'])
                                      ->orderByDesc('date')
                                      ->paginate(10);
        return view('feeding_records.index', compact('feedingRecords'));
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

        // Cek duplikasi
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

    // (BARU) Method Edit
    public function edit(FeedingRecord $feedingRecord)
    {
        $shelters = Shelter::orderBy('name')->get(['id', 'name']);
        $feedTypes = FeedType::orderBy('name')->get(['id', 'name', 'unit', 'price_per_unit']);

        // Load relasi feedTypes agar muncul di form edit
        $feedingRecord->load('feedTypes');

        return view('feeding_records.edit', compact('feedingRecord', 'shelters', 'feedTypes'));
    }

    // (BARU) Method Update
    public function update(StoreFeedingRecordRequest $request, FeedingRecord $feedingRecord)
    {
        $data = $request->validated();

        // Cek duplikasi (abaikan ID saat ini)
        $exists = FeedingRecord::where('shelter_id', $data['shelter_id'])
                               ->where('date', $data['date'])
                               ->where('id', '!=', $feedingRecord->id)
                               ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Catatan pakan untuk kandang ini pada tanggal ini sudah ada!');
        }

        // Update Data Utama
        $feedingRecord->update([
            'shelter_id' => $data['shelter_id'],
            'date' => $data['date'],
            'time_morning' => $data['time_morning'],
            'time_evening' => $data['time_evening'],
        ]);

        // Update Data Pivot (Sync akan menghapus yang lama dan insert yang baru)
        $pivotData = $this->preparePivotData($data['feed_types']);
        $feedingRecord->feedTypes()->sync($pivotData);

        return redirect()->route('feeding-records.index')->with('success', 'Data pakan berhasil diperbarui!');
    }

    // (BARU) Method Destroy
    public function destroy(FeedingRecord $feedingRecord)
    {
        $feedingRecord->feedTypes()->detach(); // Hapus relasi pivot
        $feedingRecord->delete(); // Hapus record utama

        return redirect()->route('feeding-records.index')->with('success', 'Data pakan berhasil dihapus.');
    }

    // Helper untuk menyiapkan data pivot
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
