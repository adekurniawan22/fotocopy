<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partnership extends Model
{
    use HasFactory;

    protected $table = 'partnerships';
    protected $primaryKey = 'partnership_id';

    protected $fillable = [
        'organization_id',
        'partnership_name',
        'alamat',
        'penanggung_jawab',
        'no_hp',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }
}
