<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Certificate extends Model
{
    use HasFactory;

    protected $primaryKey = 'certificate_id';

    protected $fillable = [
        'user_id',
        'file',
        'expired_date',
    ];

    protected $appends = ['file_url', 'file_name'];

    public function getFileUrlAttribute(): ?string
    {
        return ($this->file && Storage::disk('public')->exists($this->file))
            ? Storage::disk('public')->url($this->file)
            : null;
    }

    public function getFileNameAttribute(): ?string
    {
        return $this->file ? basename($this->file) : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }
}
