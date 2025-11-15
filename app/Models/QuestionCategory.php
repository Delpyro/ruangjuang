<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder; // ✅ tambahkan ini

class QuestionCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'passing_grade',
        'is_active',
    ];

    protected $casts = [
        'passing_grade' => 'decimal:2',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the sub categories for the category.
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(QuestionSubCategory::class);
    }

    /**
     * Check if the category has any sub categories.
     */
    public function hasSubCategories(): bool
    {
        return $this->subCategories()->exists();
    }

    /**
     * Check if the category has any questions.
     */
    public function hasQuestions(): bool
    {
        return $this->questions()->exists();
    }
}
