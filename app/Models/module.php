<?php
// app/Models/Module.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Module extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'is_hots',
        'duration',
        'content',
        'quote',
        'price',
        'discount',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_hots' => 'boolean',
        'price' => 'integer',
        'discount' => 'integer',
        'duration' => 'integer',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(QuestionCategory::class, 'module_category')
                    ->withTimestamps();
    }

    // Accessor untuk harga setelah diskon
    public function getFinalPriceAttribute(): float
    {
        if ($this->discount) {
            return $this->price - ($this->price * $this->discount / 100);
        }
        
        return $this->price;
    }
}