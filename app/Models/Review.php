<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * Menggunakan $guarded agar bisa diisi secara massal
     * (rating, review_text, id_user, tryout_id).
     */
    protected $guarded = [];

    /**
     * Relasi ke User
     * Mendefinisikan foreign key 'id_user' secara eksplisit
     * agar sesuai dengan skema database Anda.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * Relasi ke Tryout
     * (Foreign key 'tryout_id' sudah standar,
     * tapi ditulis agar jelas).
     */
    public function tryout()
    {
        return $this->belongsTo(Tryout::class, 'tryout_id');
    }
}