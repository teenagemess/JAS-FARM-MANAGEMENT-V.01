<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FeedType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit', 'notes'];

    /**
     * Relasi: Satu Jenis Pakan diberikan dalam banyak Catatan Pakan.
     */
    public function feedingRecords(): BelongsToMany
    {
        // Pivot table yang akan digunakan: 'feeding_record_feed_type'
        return $this->belongsToMany(FeedingRecord::class, 'feeding_record_feed_type')
                    ->withPivot('quantity'); // WAJIB ada 'withPivot' untuk kolom jumlah
    }
}
