<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FeedingRecord extends Model
{
    use HasFactory;

    // Nama tabel disesuaikan dengan konvensi jamak Laravel
    protected $table = 'feeding_records';

    protected $fillable = [
        'shelter_id',
        'user_id',
        'date',
        'time_morning',
        'time_evening',
    ];

    /**
     * Relasi: Catatan Pakan ini adalah untuk satu Kandang.
     */
    public function shelter(): BelongsTo
    {
        return $this->belongsTo(Shelter::class);
    }

    public function feedTypes(): BelongsToMany
    {
        // Definisikan pivot table dan kolom tambahan 'quantity'
        return $this->belongsToMany(FeedType::class, 'feeding_record_feed_type')
                    ->withPivot('quantity');
    }

    /**
     * Relasi: Catatan Pakan ini diinput/dilakukan oleh satu User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}
