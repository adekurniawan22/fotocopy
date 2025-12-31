<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    use HasFactory;

    protected $table = 'options';

    protected $primaryKey = 'option_id';

    protected $fillable = [
        'option_name',
        'text_value',
    ];

    protected $casts = [
        'text_value' => 'array',
    ];
}