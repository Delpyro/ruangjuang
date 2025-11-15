<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Question extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'id_tryout',
        'id_question_categories',
        'id_question_sub_category',
        'question',
        'image',
        'explanation',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    /**
     * Scope a query to only include active questions.
     */
    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Get the tryout that owns the question.
     */
    public function tryout(): BelongsTo
    {
        return $this->belongsTo(Tryout::class, 'id_tryout');
    }

    /**
     * Get the category that owns the question.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(QuestionCategory::class, 'id_question_categories');
    }

    /**
     * Get the sub category that owns the question.
     */
    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(QuestionSubCategory::class, 'id_question_sub_category');
    }

    /**
     * Get the answers for the question.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class, 'id_question');
    }

    /**
     * Get the correct answer for the question.
     */
    public function correctAnswer()
    {
        return $this->hasOne(Answer::class, 'id_question')->where('is_correct', true);
    }

    /**
     * Check if the question has a correct answer.
     */
    public function hasCorrectAnswer(): bool
    {
        return $this->answers()->where('is_correct', true)->exists();
    }

    /**
     * Check if the question has an image.
     */
    public function hasImage(): bool
    {
        return !empty($this->image);
    }

    /**
     * Check if the question has explanation.
     */
    public function hasExplanation(): bool
    {
        return !empty($this->explanation);
    }

    /**
     * Get the question with truncated text for display.
     */
    public function getShortQuestionAttribute(): string
    {
        return strlen($this->question) > 100 
            ? substr(strip_tags($this->question), 0, 100) . '...' 
            : strip_tags($this->question);
    }
}