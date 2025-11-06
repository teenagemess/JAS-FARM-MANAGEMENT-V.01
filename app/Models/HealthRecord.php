<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class HealthRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'sheep_id',
        'handler_id',
        'record_date',
        'diagnosis',
        'treatment_details',
        'medication_used',
        'photo_path'
    ];

    // Relasi One-to-Many
    public function sheep(): BelongsTo
    {
        return $this->belongsTo(Sheep::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handler_id');
    }

    // Relasi Many-to-Many (Pivot)
    public function symptoms(): BelongsToMany
    {
        // Menghubungkan ke tabel 'symptoms' melalui pivot table 'health_record_symptom'
        return $this->belongsToMany(Symptom::class, 'health_record_symptom');
    }
}
