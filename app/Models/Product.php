<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'price',
        'description',
        'quantidade',
        'unidade'
    ];

    public function saleItems()
{
    return $this->hasMany(SaleItem::class);
}


}
