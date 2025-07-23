<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = ['client_id', 'sale_date', 'total'];

    protected $casts = [
        'sale_date' => 'date',
        'total' => 'float',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
