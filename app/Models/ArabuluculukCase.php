<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class ArabuluculukCase extends Model
{
    use SoftDeletes;

    protected $table = 'arabuluculuk_cases';

    // Mass Assignment için tüm alanları açıyoruz
    protected $guarded = [];

    protected $casts = [
        'board_required' => 'boolean',
        'talep_tutari' => 'decimal:2',
        'anlasilan_tutar' => 'decimal:2',
        'karsi_taraf_teklif' => 'decimal:2',
    ];

    // --- İLİŞKİLER ---

    public function arabulucu()
    {
        return $this->belongsTo(Arabulucu::class, 'arabulucu_id');
    }

    public function calisan()
    {
        return $this->belongsTo(User::class, 'calisan_user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }



    public function externalLawyer()
    {
        return $this->belongsTo(User::class, 'external_lawyer_id');
    }

    public function files()
    {
        return $this->hasMany(ArabuluculukFile::class, 'case_id');
    }

    public function payments()
    {
        return $this->hasMany(ArabuluculukPayment::class, 'case_id');
    }

    public function logs()
    {
        return $this->hasMany(ArabuluculukLog::class, 'case_id')->orderBy('created_at', 'desc');
    }

    // TEK VE DOĞRU İLİŞKİ METODU BU OLMALI
    // Controller'da ve Blade'de '$case->kurulDegerlendirmesi' diye çağırıyoruz.
    public function kurulDegerlendirmesi()
    {
        return $this->hasMany(ArabuluculukKurulDegerlendirme::class, 'case_id');
    }

    /**
     * Bölüm Lideri ve Çalışan için Maskelenmiş Statü
     */
    protected function publicStatus(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $status = $attributes['status'] ?? '';
                $mutabakat = $attributes['mutabakat'] ?? '';

                if (in_array($status, ['taslak', 'hukuk_incelemesinde', 'yonetim_onayinda', 'imza_asamasinda', 'odeme_bekliyor'])) {
                    return ['text' => 'Süreç Devam Ediyor', 'color' => 'orange', 'icon' => 'clock'];
                }

                if ($status === 'arabulucuda') {
                    return ['text' => 'Arabulucuda', 'color' => 'blue', 'icon' => 'briefcase'];
                }

                if ($status === 'kapatildi' && $mutabakat === 'anlasildi') {
                    return ['text' => 'Süreç Tamamlandı (Anlaşıldı)', 'color' => 'green', 'icon' => 'check-circle'];
                }

                if ($status === 'kapatildi' && $mutabakat === 'anlasilmadi') {
                    return ['text' => 'Süreç Tamamlandı (Anlaşma Yok)', 'color' => 'red', 'icon' => 'x-circle'];
                }

                return ['text' => 'İşlemde', 'color' => 'gray', 'icon' => 'refresh'];
            }
        );
    }
}