<?php
// App/Models/HistoryTool.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryTool extends Model
{
    use HasFactory;

    protected $primaryKey = 'history_tool_id';

    protected $fillable = [
        'organization_id',
        'type',
        'status',
        'created_by',
        'list_tools',
        'list_user',
        'exit_date',
        'keterangan',
        'foto',
        'return_date',
        'is_returned'
    ];

    protected $casts = [
        'list_tools' => 'array',
        'list_user' => 'array',
        'exit_date' => 'date',
        'return_date' => 'date',
        'is_returned' => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }
}
