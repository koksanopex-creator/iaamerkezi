<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\IaaWorkflowStep;

class IaaProgressUpdate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'iaa_talep_id',
        'iaa_workflow_step_id',
        'user_id',
        'content',
        'completed_at',
        'is_hidden_from_customer',
    ];

    /**
     * Bu ilerleme kaydının ait olduğu iş akışı adımını getirir.
     */
    public function step()
    {
        return $this->belongsTo(IaaWorkflowStep::class, 'iaa_workflow_step_id');
    }

    /**
     * Adımı tamamlayan kullanıcıyı getirir.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id')->withTrashed();
    }
}