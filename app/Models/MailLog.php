<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    protected $table = 'mail_logs';

    protected $fillable = [
        'loggable_type',
        'loggable_id',
        'source_page',
        'source_action',
        'recipients',
        'error_message',
        'notification_class',
        'notification_data',
        'retry_count',
        'resolved_at',
        'resolved_by',
        'bolum_id',
    ];

    protected $casts = [
        'recipients' => 'array',
        'notification_data' => 'array',
        'resolved_at' => 'datetime',
    ];

    // === İLİŞKİLER ===

    /**
     * Polymorphic ilişki (MusteriSikayeti, DisciplinaryCase, Iaa vb.)
     */
    public function loggable()
    {
        return $this->morphTo();
    }

    /**
     * Hatayı çözen kullanıcı
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * İlgili bölüm
     */
    public function bolum()
    {
        return $this->belongsTo(Bolum::class, 'bolum_id');
    }

    // === SCOPE'LAR ===

    /**
     * Çözülmemiş loglar
     */
    public function scopeUnresolved($query)
    {
        return $query->whereNull('resolved_at');
    }

    /**
     * Çözülmüş loglar
     */
    public function scopeResolved($query)
    {
        return $query->whereNotNull('resolved_at');
    }

    /**
     * Bölüm bazlı filtreleme (Bölüm Lideri için)
     */
    public function scopeForBolum($query, $bolumId)
    {
        return $query->where('bolum_id', $bolumId);
    }

    /**
     * Direktör bazlı filtreleme (Yönettiği bölümler)
     */
    public function scopeForDirektor($query, $bolumIds)
    {
        return $query->whereIn('bolum_id', $bolumIds);
    }

    // === YARDIMCI METODLAR ===

    /**
     * Log çözüldü mü?
     */
    public function isResolved(): bool
    {
        return !is_null($this->resolved_at);
    }

    /**
     * Alıcı isimlerini okunaklı formatta döndür
     */
    public function getRecipientsTextAttribute(): string
    {
        if (!$this->recipients || !is_array($this->recipients)) {
            return 'Bilinmiyor';
        }
        return implode(', ', $this->recipients);
    }
}
