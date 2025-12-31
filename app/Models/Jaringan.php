<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jaringan extends Model
{
    use HasFactory;
    protected $table = 'jaringan';
    protected $primaryKey = 'jaringan_id';

    protected $fillable = [
        'organization_id',
        'bay_line',
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
