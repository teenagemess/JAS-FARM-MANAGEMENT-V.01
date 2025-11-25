<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FeedType extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit', 'description', 'price_per_unit'];

    /**
     * Relasi ke FeedingRecord (Many-to-Many)
     */
    public function feedingRecords(): BelongsToMany
    {
        return $this->belongsToMany(FeedingRecord::class, 'feeding_record_feed_type')
                    ->withPivot(['quantity_morning', 'quantity_evening'])
                    ->withTimestamps();
    }
}
