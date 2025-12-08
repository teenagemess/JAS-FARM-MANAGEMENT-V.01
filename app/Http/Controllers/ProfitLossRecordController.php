<?php

namespace App\Http\Controllers;

use App\Models\ProfitLossRecord;
use App\Models\Sheep;
use App\Models\Shelter;
use App\Http\Requests\StoreProfitLossRecordRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ProfitLossRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = ProfitLossRecord::with(['sheep', 'shelter', 'user']);

        // Filter Tanggal
        if ($request->filled('start_date')) {
            $query->whereDate('date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('date', '<=', $request->end_date);
        }

        // Filter Tipe
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Ambil Data Paginasi
        $records = $query->orderByDesc('date')->orderByDesc('id')->paginate(10)->withQueryString();

        // Hitung Ringkasan (Berdasarkan filter saat ini)
        // Kita clone query agar tidak mengganggu pagination
        $summaryQuery = clone $query;
        // Hapus limit/offset pagination untuk hitung total semua data yang cocok filter
        $summaryQuery->getQuery()->limit = null;
        $summaryQuery->getQuery()->offset = null;

        $allRecords = $summaryQuery->get();

        $totalIncome = $allRecords->where('type', 'income')->sum('amount');
        $totalExpense = $allRecords->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return view('profit_loss.index', compact('records', 'totalIncome', 'totalExpense', 'balance'));
    }

    public function create()
    {
        $sheeps = Sheep::orderBy('tag_number')->get(['id', 'tag_number']);
        $shelters = Shelter::orderBy('name')->get(['id', 'name']);

        return view('profit_loss.create', compact('sheeps', 'shelters'));
    }

    public function store(StoreProfitLossRecordRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();

        ProfitLossRecord::create($data);

        return redirect()->route('profit-loss.index')->with('success', 'Transaksi berhasil dicatat!');
    }

    public function destroy(ProfitLossRecord $profitLoss)
    {
        $profitLoss->delete();
        return redirect()->route('profit-loss.index')->with('success', 'Transaksi berhasil dihapus.');
    }
}
