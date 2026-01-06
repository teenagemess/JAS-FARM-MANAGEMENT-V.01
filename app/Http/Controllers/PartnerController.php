<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Sheep;
use Illuminate\Http\Request;

class PartnerController extends Controller
{
    /**
     * Menampilkan daftar semua Mitra.
     */
    public function index()
    {
        // Ambil user dengan role 'mitra', beserta jumlah domba yang mereka kelola
        $partners = User::where('role', 'mitra')
                        ->withCount(['sheeps as total_sheep']) // Asumsi relasi 'sheeps' ada di User
                        ->orderBy('name')
                        ->paginate(10);

        return view('partners.index', compact('partners'));
    }

    /**
     * Menampilkan detail Mitra dan daftar domba mereka.
     */
    public function show(User $partner)
    {
        // Pastikan user ini benar-benar mitra
        if ($partner->role !== 'mitra') {
            abort(404);
        }

        // Ambil domba yang dikelola mitra ini
        $sheep = Sheep::where('partner_id', $partner->id)
                      ->with('shelter') // Load relasi shelter (jika ada)
                      ->latest()
                      ->paginate(12);

        return view('partners.show', compact('partner', 'sheep'));
    }
}
