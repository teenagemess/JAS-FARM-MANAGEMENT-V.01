<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sheep;
use App\Models\HealthRecord;
use App\Models\ReproductionRecord;
use App\Models\ProfitLossRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // === 1. STATISTIK UTAMA ===
        $totalSheep = Sheep::count();

        // --- PERBAIKAN LOGIKA HITUNG DOMBA SAKIT ---
        // Ambil semua domba yang punya riwayat kesehatan
        $sheepsWithHealth = Sheep::whereHas('healthRecords')->with('healthRecords')->get();

        // Filter: Hanya hitung jika STATUS TERAKHIR-nya sakit
        $activeSicknessCount = $sheepsWithHealth->filter(function ($sheep) {
            // Ambil record terakhir (Tanggal terbaru + ID terbesar)
            $latestRecord = $sheep->healthRecords
                ->sortByDesc(function ($record) {
                    return $record->record_date . str_pad($record->id, 10, '0', STR_PAD_LEFT);
                })
                ->first();

            // Jika record terakhir ada DAN statusnya BUKAN 'Completed', berarti sakit
            return $latestRecord && !in_array($latestRecord->status, ['Completed', 'Sembuh / Selesai']);
        })->count();


        // Hitung domba hamil
        $pregnantCount = ReproductionRecord::where('status', 'Pregnant')->count();

        // Hitung Keuangan Bulan Ini
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $incomeThisMonth = ProfitLossRecord::whereMonth('date', $currentMonth)
                            ->whereYear('date', $currentYear)
                            ->where('type', 'income')
                            ->sum('amount');

        $expenseThisMonth = ProfitLossRecord::whereMonth('date', $currentMonth)
                            ->whereYear('date', $currentYear)
                            ->where('type', 'expense')
                            ->sum('amount');

        $balanceThisMonth = $incomeThisMonth - $expenseThisMonth;

        // === 2. DATA UNTUK GRAFIK ===
        $monthlyIncome = array_fill(1, 12, 0);
        $monthlyExpense = array_fill(1, 12, 0);

        $financials = ProfitLossRecord::select(
                DB::raw('MONTH(date) as month'),
                'type',
                DB::raw('SUM(amount) as total')
            )
            ->whereYear('date', $currentYear)
            ->groupBy('month', 'type')
            ->get();

        foreach ($financials as $record) {
            if ($record->type == 'income') {
                $monthlyIncome[$record->month] = $record->total;
            } else {
                $monthlyExpense[$record->month] = $record->total;
            }
        }

        $monthlySheep = array_fill(1, 12, 0);
        $sheepGrowth = Sheep::select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->get();

        foreach ($sheepGrowth as $record) {
            $monthlySheep[$record->month] = $record->total;
        }

        // === 3. LIST DATA ===

        // --- PERBAIKAN LIST DOMBA SAKIT ---
        // Kita gunakan hasil filter di atas untuk mendapatkan list domba sakit yang valid
        $sickSheepList = $sheepsWithHealth->filter(function ($sheep) {
            $latestRecord = $sheep->healthRecords
                ->sortByDesc(function ($record) {
                    return $record->record_date . str_pad($record->id, 10, '0', STR_PAD_LEFT);
                })
                ->first();
            return $latestRecord && !in_array($latestRecord->status, ['Completed', 'Sembuh / Selesai']);
        })->map(function($sheep) {
            // Map agar yang dikembalikan adalah objek HealthRecord terakhirnya (untuk ditampilkan di view)
            return $sheep->healthRecords
                ->sortByDesc(function ($record) {
                    return $record->record_date . str_pad($record->id, 10, '0', STR_PAD_LEFT);
                })
                ->first();
        })
        ->sortByDesc('record_date') // Urutkan daftar sakit berdasarkan tanggal kejadian
        ->take(5);


        // Daftar Domba Hamil Tua
        $pregnantSheepList = ReproductionRecord::with(['dam', 'dam.shelter'])
                            ->where('status', 'Pregnant')
                            ->orderBy('expected_delivery_date', 'asc')
                            ->limit(5)
                            ->get();

        $recentTransactions = ProfitLossRecord::latest('date')->limit(5)->get();

        return view('dashboard', compact(
            'totalSheep',
            'activeSicknessCount',
            'pregnantCount',
            'balanceThisMonth',
            'sickSheepList',
            'pregnantSheepList',
            'recentTransactions',
            'monthlyIncome',
            'monthlyExpense',
            'monthlySheep'
        ));
    }
}
