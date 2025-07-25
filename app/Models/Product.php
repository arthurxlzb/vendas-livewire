<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Product extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'price',
    'description',
    'quantity',
    'unit',
];

    protected $casts = [
    'price'          => 'decimal:2',
    'quantity' => 'decimal:2',
];

    public function saleItems(): HasMany
{
    return $this->hasMany(SaleItem::class);
}


}
