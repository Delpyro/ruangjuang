<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class QuestionSubCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'question_category_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Get the category that owns the sub category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionCategory::class, 'question_category_id');
    }


    /**
     * Check if the sub category has any questions.
     */
    public function hasQuestions(): bool
    {
        return $this->questions()->exists();
    }

    /**
     * Get the display name with category.
     */
    public function getFullNameAttribute(): string
    {
        return $this->category ? $this->category->name . ' - ' . $this->name : $this->name;
    }
}