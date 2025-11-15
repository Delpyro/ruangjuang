<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ranking extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (mass assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_user',
        'tryout_id',
        'score',
    ];

    /**
     * Tipe data bawaan (casts) untuk atribut.
     * Ini memastikan 'score' selalu diperlakukan sebagai angka desimal.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'score' => 'decimal:2', // '2' adalah jumlah angka di belakang koma
    ];

    /**
     * Mendapatkan data User yang memiliki ranking ini.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Mendapatkan data Tryout yang terkait dengan ranking ini.
     */
    public function tryout(): BelongsTo
    {
        return $this->belongsTo(Tryout::class, 'tryout_id');
    }
}