<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GarduInduk extends Model
{
    use HasFactory;
    protected $table = 'gardu_induk';
    protected $primaryKey = 'gardu_induk_id';

    protected $fillable = [
        'organization_id',
        'gardu_induk',
        'keterangan',
        'foto',
        'riwayat',
    ];

    protected $casts = [
        'riwayat' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }
}
