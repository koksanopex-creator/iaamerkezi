<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IaaTalep extends Model
{
    use HasFactory;

    // Modelin hangi tabloyu kullanacağını belirtiyoruz.
    protected $table = 'iaa_talepleri';

    // Hangi alanların toplu olarak atanabileceğini belirtiyoruz.
    protected $fillable = [
        'iaa_id',
        'takim_id',
        'talep_eden_user_id',
        'durum',
        'iaa_workflow_id',
        'start_date',
        'due_date',
        'status',
        'workflow_snapshot',
    ];

    protected $casts = [
        'workflow_snapshot' => 'array',
    ];

    /**
     * Bu talep kaydının ait olduğu iş akışını (workflow) döndürür.
     */
    public function workflow()
    {
        return $this->belongsTo(IaaWorkflow::class, 'iaa_workflow_id');
    }
}