<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tool extends Model
{
    use HasFactory;

    protected $table = 'tools';

    protected $primaryKey = 'tool_id';

    protected $fillable = [
        'organization_id',
        'warehouse_id',
        'jenis',
        'nama',
        'merk',
        'deskripsi',
        'jumlah',
        'satuan',
        'tanggal_pengadaan',
        'tanggal_kadaluarsa',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'tanggal_pengadaan' => 'date',
        'tanggal_kadaluarsa' => 'date',
    ];

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'warehouse_id');
    }
    
}
