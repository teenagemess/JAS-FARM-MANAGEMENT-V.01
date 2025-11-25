<?php

namespace App\Http\Controllers;

use App\Models\FeedType;
use App\Http\Requests\StoreFeedTypeRequest;
use App\Http\Requests\UpdateFeedTypeRequest;

class FeedTypeController extends Controller
{
    /**
     * Menampilkan daftar jenis pakan.
     */
    public function index()
    {
        $feedTypes = FeedType::orderBy('name')->paginate(10);
        return view('feed_types.index', compact('feedTypes'));
    }

    /**
     * Form tambah jenis pakan.
     */
    public function create()
    {
        return view('feed_types.create');
    }

    /**
     * Simpan jenis pakan baru.
     */
    public function store(StoreFeedTypeRequest $request)
    {
        FeedType::create($request->validated());
        return redirect()->route('feed-types.index')->with('success', 'Jenis pakan berhasil ditambahkan!');
    }

    /**
     * Form edit jenis pakan.
     */
    public function edit(FeedType $feedType)
    {
        return view('feed_types.edit', compact('feedType'));
    }

    /**
     * Update jenis pakan.
     */
    public function update(UpdateFeedTypeRequest $request, FeedType $feedType)
    {
        $feedType->update($request->validated());
        return redirect()->route('feed-types.index')->with('success', 'Data pakan berhasil diperbarui!');
    }

    /**
     * Hapus jenis pakan.
     */
    public function destroy(FeedType $feedType)
    {
        // Cek apakah pakan ini sudah pernah digunakan di feeding records
        // Jika ya, sebaiknya jangan dihapus sembarangan (opsional validation)

        $feedType->delete();
        return redirect()->route('feed-types.index')->with('success', 'Jenis pakan berhasil dihapus.');
    }
}
