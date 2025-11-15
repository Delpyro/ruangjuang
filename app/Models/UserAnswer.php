<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAnswer extends Model
{
    /**
     * Nama tabel harus dispesifikan jika berbeda dari 'user_answers'.
     * Berdasarkan migrasi, nama tabel Anda adalah 'users_answers'.
     * @var string
     */
    protected $table = 'users_answers';

    /**
     * Kolom yang dapat diisi secara massal (mass assignable).
     * Telah disesuaikan untuk menyertakan user_tryout_id.
     * @var array<int, string>
     */
    protected $fillable = [
        'id_user', 
        'user_tryout_id', // <-- BARU: Foreign Key ke sesi pengerjaan
        'question_id',
        'answer_id',
        'is_doubtful', 
        'score',
    ];

    /**
     * Casting tipe data untuk kolom.
     * @var array<string, string>
     */
    protected $casts = [
        'is_doubtful' => 'boolean',
    ];

    // --- Relasi ---

    /**
     * Get the user that owns the user answer.
     * Menggunakan id_user secara langsung.
     */
    public function user(): BelongsTo
    {
        // Secara eksplisit mendefinisikan foreign key sebagai 'id_user'
        return $this->belongsTo(User::class, 'id_user'); 
    }
    
    /**
     * Get the tryout session (UserTryout) that owns the user answer.
     * Ini adalah relasi kunci yang mengikat jawaban ke sebuah sesi ujian.
     */
    public function userTryout(): BelongsTo
    {
        return $this->belongsTo(UserTryout::class, 'user_tryout_id');
    }

    /**
     * Get the question associated with the user answer.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    /**
     * Get the answer selected by the user.
     */
    public function answer(): BelongsTo
    {
        return $this->belongsTo(Answer::class);
    }
}