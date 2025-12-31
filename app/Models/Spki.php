<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Spki extends Model
{
    use HasFactory;

    protected $table = 'spki';
    protected $primaryKey = 'spki_id';

    protected $fillable = [
        'organization_id',
        'nomor_spki',
        'dari',
        'kepada',
        'macam_pekerjaan',
        'lokasi_pekerjaan',
        'mulai_pelaksanaan',
        'selesai_pelaksanaan',
        'penanggung_jawab_id',
        'penanggung_jawab_nama',
        'pengawas_pekerjaan_id',
        'pengawas_pekerjaan_nama',
        'pengawas_k3_id',
        'pengawas_k3_nama',
        'pelaksana',
        'peralatan',
        'kendaraan',
        'uraian_pekerjaan',
        'catatan',
        'revision_note',
        'status',
        'approved_by',
        'approved_at',
        'created_by',
    ];

    protected $casts = [
        'mulai_pelaksanaan'   => 'date',
        'selesai_pelaksanaan' => 'date',
        'pelaksana'           => 'array',
        'peralatan'           => 'array',
        'approved_at'         => 'datetime',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'kepada', 'user_id');
    }

    public function penanggungJawab()
    {
        return $this->belongsTo(User::class, 'penanggung_jawab_id', 'user_id');
    }

    public function pengawasPekerjaan()
    {
        return $this->belongsTo(User::class, 'pengawas_pekerjaan_id', 'user_id');
    }

    public function pengawasK3()
    {
        return $this->belongsTo(User::class, 'pengawas_k3_id', 'user_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by', 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}