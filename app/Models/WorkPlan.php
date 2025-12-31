<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkPlan extends Model
{
    use HasFactory;

    protected $primaryKey = 'work_plan_id';

    protected $fillable = [
        'organization_id',
        'work_plan_name',
        'list_items',
        'date_start',
        'date_finish',
        'is_done',
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_finish' => 'date',
        'is_done' => 'boolean',
        'list_items' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }
}
