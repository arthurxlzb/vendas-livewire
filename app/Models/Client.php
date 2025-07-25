<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // ou Model, se não for autenticar

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';  // confirma o nome exato da tabela

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}
