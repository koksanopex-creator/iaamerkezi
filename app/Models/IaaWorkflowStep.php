<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IaaWorkflowStep extends Model
{
    use HasFactory;

    protected $fillable = [
        'iaa_workflow_id',
        'name',
        'description',
        'order',
        'default_duration_days',
        'widgets', // <-- YENİ EKLENDİ
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'widgets' => 'array', // <-- YENİ EKLENDİ (JSON'ı otomatik diziye çevirir)
    ];

     // Workflow ile ilişki (varsa kalsın, yoksa eklenebilir)
     public function workflow()
     {
         return $this->belongsTo(IaaWorkflow::class, 'iaa_workflow_id');
     }

     /**
     * === YENİ EKLENEN İLİŞKİ ===
     * Bu adıma atanmış kullanıcıları getirir.
     * Blade tarafında "Benim sorumluluğumda mı?" kontrolü için gereklidir.
     */
    public function sorumlular()
    {
        // iaa_step_assignments tablosu üzerinden User modeline çoka-çok ilişki
        return $this->belongsToMany(User::class, 'iaa_step_assignments', 'iaa_workflow_step_id', 'user_id')
                    ->withPivot('iaa_id') // Hangi projeye ait olduğunu pivot'tan çekiyoruz
                    ->withTimestamps();
    }
}