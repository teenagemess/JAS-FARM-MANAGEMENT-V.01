<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ReproductionRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'female_sheep_id',
        'male_sheep_id',
        'status',
        'mating_date',
        'actual_delivery_date',
        'weaning_date',
        'offspring_count',
        'assistance_user_id',
    ];

    /**
     * Pastikan kolom tanggal diubah menjadi objek Carbon.
     */
    protected $dates = [
        'mating_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'weaning_date',
    ];

    // --- MUTATOR (Logika Otomatis) ---

    /**
     * Mutator untuk menghitung 'expected_delivery_date' saat 'mating_date' diatur.
     * Rata-rata masa kehamilan domba adalah 147 hari.
     */
public function setMatingDateAttribute($value)
    {
        // 1. Set nilai mating_date ke atribut database
        $this->attributes['mating_date'] = $value;

        // 2. Lakukan kalkulasi hanya jika ada nilai
        if ($value) {
            $matingDate = Carbon::parse($value);

            // 3. Tambahkan 147 hari dan set nilai ke expected_delivery_date
            $this->attributes['expected_delivery_date'] = $matingDate->addDays(147);
        }
    }


    // --- RELASI ---

    public function dam(): BelongsTo
    {
        return $this->belongsTo(Sheep::class, 'female_sheep_id');
    }

    public function sire(): BelongsTo
    {
        return $this->belongsTo(Sheep::class, 'male_sheep_id');
    }

    public function assistant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assistance_user_id');
    }

    // Anda juga perlu menambahkan relasi ke Model Sheep (kebalikan)
}
