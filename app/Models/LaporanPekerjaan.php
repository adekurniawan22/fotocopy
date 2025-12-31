<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanPekerjaan extends Model
{
    use HasFactory;

    protected $table = 'laporan_pekerjaan';
    protected $primaryKey = 'laporan_pekerjaan_id';

    protected $fillable = [
        'organization_id',
        'judul_laporan',
        'dasar_pelaksanaan',
        'mulai_pelaksanaan',
        'selesai_pelaksanaan',
        'lingkup_pekerjaan',
        'hasil_pekerjaan',
        'lampiran',
        'created_by',
        'approved_by',
        'approved_at',
        'status',
        'revision_note',
    ];

    protected $casts = [
        'lampiran' => 'array',
        'mulai_pelaksanaan' => 'date',
        'selesai_pelaksanaan' => 'date',
        'approved_at' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }
}
