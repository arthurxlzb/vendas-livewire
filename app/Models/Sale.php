<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
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
