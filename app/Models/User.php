<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\UserTryout; 
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // Tambahkan ini

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'image',
        'email',
        'phone_number',
        'password',
        'role',
        'status',
        'is_active',
        'last_login_at',
        'last_login_ip'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Relasi many-to-many ke Tryout yang sudah dibeli.
     * Menggunakan 'id_user' sebagai foreign key di tabel pivot.
     */
    public function purchasedTryouts(): BelongsToMany
    {
        return $this->belongsToMany(Tryout::class, 'user_tryouts', 'id_user', 'tryout_id')
            ->using(UserTryout::class)
            ->withPivot('order_id', 'purchased_at', 'started_at', 'ended_at', 'is_completed') // Tambahkan kolom pivot dari UserTryout
            ->withTimestamps();
    }
    
    /**
     * Relasi many-to-many ke Bundle yang sudah dibeli.
     * Menggunakan 'id_user' sebagai foreign key di tabel pivot.
     */
    public function purchasedBundles(): BelongsToMany
    {
        // Asumsi tabel pivot adalah 'bundle_user'
        return $this->belongsToMany(Bundle::class, 'bundle_user', 'id_user', 'bundle_id')
                    ->withPivot('order_id', 'purchased_at', 'transaction_id') // Sesuaikan kolom pivot Anda
                    ->withTimestamps(); // Tambahkan withTimestamps jika tabel pivot memilikinya
    }

    public function completedTryouts()
    {
        return $this->purchasedTryouts()->wherePivot('is_completed', true)->orderByPivot('ended_at', 'asc');
    }

}
