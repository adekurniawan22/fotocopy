<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MethodTool extends Model
{
    use HasFactory;

    protected $table = 'method_tools';
    protected $primaryKey = 'method_id';

    protected $fillable = [
        'organization_id',
        'nama_method',
        'list_tools',
    ];

    protected $casts = [
        'list_tools' => 'array',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id', 'organization_id');
    }

    public function getToolsAttribute()
    {
        if (empty($this->list_tools)) {
            return collect([]);
        }

        $toolIds = array_column($this->list_tools, 'id');
        
        $toolsDB = Tool::whereIn('tool_id', $toolIds)->get()->keyBy('tool_id');

        $result = collect([]);

        foreach ($this->list_tools as $item) {
            if (isset($toolsDB[$item['id']])) {
                $tool = $toolsDB[$item['id']];
                $tool->qty_requirement = $item['qty'];
                $result->push($tool);
            }
        }

        return $result;
    }
}