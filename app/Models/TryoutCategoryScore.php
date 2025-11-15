<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TryoutCategoryScore extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'tryout_category_scores';

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_tryout_id',
        'question_category_id',
        'score',
        'correct_count',
        'wrong_count',
        'unanswered_count',
        'total_questions',
    ];

    /**
     * Tipe data bawaan (casts) untuk atribut.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'decimal:2',
        'correct_count' => 'integer',
        'wrong_count' => 'integer',
        'unanswered_count' => 'integer',
        'total_questions' => 'integer',
    ];

    // --- RELASI ---

    /**
     * Mendapatkan data pengerjaan (UserTryout) yang terkait.
     */
    public function userTryout(): BelongsTo
    {
        // Asumsi model Anda bernama UserTryout
        return $this->belongsTo(UserTryout::class, 'user_tryout_id');
    }

    /**
     * Mendapatkan data kategori soal yang terkait.
     */
    public function questionCategory(): BelongsTo
    {
        // Asumsi model Anda bernama QuestionCategory
        return $this->belongsTo(QuestionCategory::class, 'question_category_id');
    }
}