<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IaaStepAssignment extends Model
{
    use HasFactory;

    // Veritabanındaki tablo adı
    protected $table = 'iaa_step_assignments';

    protected $guarded = []; // Tüm sütunlara izin ver

    // İlişkiler (Gerekirse)
    public function adim()
    {
        return $this->belongsTo(IaaWorkflowStep::class, 'iaa_workflow_step_id');
    }

    public function kullanici()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}