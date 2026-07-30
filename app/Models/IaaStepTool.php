<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IaaStepTool extends Model
{
    protected $fillable = [
        'iaa_id',
        'iaa_workflow_step_id',
        'user_id',
        'tool_type',
        'title',
        'data',
        'order'
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function step()
    {
        return $this->belongsTo(IaaWorkflowStep::class, 'iaa_workflow_step_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }}
