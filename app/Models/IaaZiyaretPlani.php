<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IaaZiyaretPlani extends Model
{
    use HasFactory;

    protected $table = 'iaa_ziyaret_planlari';

    protected $fillable = [
        'iaa_id',
        'iaa_workflow_step_id',
        'visitor_id',
        'visitors',
        'visitor_name',
        'planner_id',
        'approved_by',
        'business_unit_id',
        'customer_product_id',
        'barcode',
        'lot_no',
        'contact_persons',
        'visit_date',
        'visit_reason',
        'visit_notes',
        'status',
        'reject_reason',
        'rejection_reason_director',
        'rejection_reason_quality',
        'rejection_reason_superadmin',
        'planner_revision_note',
        'estimated_return_date',
        'findings',
        'result',
        'takvim_remote_id',
        'completed_by',
        'completed_at',
        'visit_file',
        'is_visit_notes_visible_to_customer',
        'is_findings_visible_to_customer',
        'is_result_visible_to_customer',
        'additional_products',
        'visit_reasons',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
        'estimated_return_date' => 'date',
        'contact_persons' => 'array',
        'visitors' => 'array',
        'completed_at' => 'datetime',
        'visit_file' => 'array',
        'is_visit_notes_visible_to_customer' => 'boolean',
        'is_findings_visible_to_customer' => 'boolean',
        'is_result_visible_to_customer' => 'boolean',
        'additional_products' => 'array',
        'visit_reasons' => 'array',
    ];

    public function iaa()
    {
        return $this->belongsTo(Iaa::class, 'iaa_id');
    }

    public function step()
    {
        return $this->belongsTo(IaaWorkflowStep::class, 'iaa_workflow_step_id');
    }

    public function planner()
    {
        return $this->belongsTo(User::class, 'planner_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function visitor()
    {
        return $this->belongsTo(User::class, 'visitor_id');
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public static function getNotCompletedCountForUser($user)
    {
        $allowedBolumIds = $user->getAllowedBolumIds();
        $query = self::whereNotIn('status', ['Tamamlandı', 'İptal Edildi']);

        if ($allowedBolumIds !== '*') {
            $query->where(function ($q) use ($allowedBolumIds, $user) {
                if (!empty($allowedBolumIds)) {
                    $q->orWhereHas('iaa', function ($sq) use ($allowedBolumIds) {
                        $sq->whereIn('bolum_id', $allowedBolumIds)
                            ->orWhereHas('musteriSikayeti.sikayetKategori', function ($ssq) use ($allowedBolumIds) {
                                $ssq->whereIn('bolum_id', $allowedBolumIds);
                            });
                    });
                }

                $yonetilenZiyaretciBolumIds = [];
                if ($user->hasRole('Direktör')) {
                    $yonetilenZiyaretciBolumIds = $user->yonetilenBolumler()->pluck('bolumler.id')->toArray();
                } elseif ($user->hasRole(['Bölüm Lideri', 'Müşteri Şikayeti Çözüm Lideri', 'Bölüm Kalite Yöneticisi']) && $user->bolum_id) {
                    $yonetilenZiyaretciBolumIds[] = $user->bolum_id;
                } elseif ($user->hasRole('Bölüm Lider Yardımcısı') && $user->bolum_id && $user->hasPermissionTo('bolum.ziyaret.gor')) {
                    $yonetilenZiyaretciBolumIds[] = $user->bolum_id;
                }

                if (!empty($yonetilenZiyaretciBolumIds)) {
                    $personelIds = User::whereIn('bolum_id', $yonetilenZiyaretciBolumIds)->pluck('id')->toArray();
                    if (!empty($personelIds)) {
                        $q->orWhereIn('visitor_id', $personelIds);
                        foreach ($personelIds as $pId) {
                            $q->orWhereJsonContains('visitors', (string)$pId)
                              ->orWhereJsonContains('visitors', $pId);
                        }
                    }
                }

                $q->orWhere('visitor_id', $user->id)
                  ->orWhereJsonContains('visitors', (string)$user->id)
                  ->orWhereJsonContains('visitors', $user->id);
            });
        } else {
            if (!$user->hasRole(['Superadmin', 'Yonetim', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi'])) {
                if ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
                    $query->whereHas('iaa.musteriSikayeti', function($sq) { $sq->where('konum_tipi', 'Yurt İçi'); });
                } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
                    $query->whereHas('iaa.musteriSikayeti', function($sq) { $sq->where('konum_tipi', 'Yurt Dışı'); });
                }
            }
        }

        return $query->count();
    }
}
