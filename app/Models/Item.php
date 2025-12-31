<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $table = 'items';
    protected $primaryKey = 'item_id';

    protected $fillable = [
        'item_name',
        'foto',
        'buy_price',
        'sell_price',
        'unit',
        'letak',
        'location',
        'description',
    ];

    protected $casts = [
        'foto' => 'array',
        'buy_price' => 'integer',
        'sell_price' => 'integer',
    ];
}
