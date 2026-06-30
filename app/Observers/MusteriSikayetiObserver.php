<?php

namespace App\Observers;

use App\Models\MusteriSikayeti;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MusteriSikayetiObserver
{
    public function created(MusteriSikayeti $sikayet)
    {
        $this->syncWithTakvim($sikayet);
    }

    public function updated(MusteriSikayeti $sikayet)
    {
        $this->syncWithTakvim($sikayet);
    }

    public function deleting(MusteriSikayeti $sikayet)
    {
        // SOFT DELETE - Sadece Takvim senkronizasyonunu tetiklemek yeterli
        Log::info("MusteriSikayetiObserver: deleting (soft) tetiklendi. #{$sikayet->id}");
    }

    public function deleted(MusteriSikayeti $sikayet)
    {
        // Eğer hard delete ise Takvim'e delete action yolla. Soft delete ise de yolla.
        // Aslında 'action' => 'deleted' yollamak mantıklı
        if ($sikayet->isForceDeleting()) {
            $this->syncWithTakvim($sikayet, 'force_deleted');
        } else {
            $this->syncWithTakvim($sikayet, 'deleted');
        }
    }

    public function restored(MusteriSikayeti $sikayet)
    {
        Log::info("MusteriSikayetiObserver: restored tetiklendi. #{$sikayet->id}");
        $this->syncWithTakvim($sikayet, 'restored');
    }

    public function forceDeleting(MusteriSikayeti $sikayet)
    {
        // KALICI SİLME - Puanları geri al
        Log::info("MusteriSikayetiObserver: forceDeleting tetiklendi. #{$sikayet->id}");
        $puan = $sikayet->kazanilan_puan;
        if ($puan > 0) {
            // 1. Oluşturan kişiden geri al
            if ($sikayet->olusturan_kurul_uyesi_id) {
                \App\Models\User::where('id', $sikayet->olusturan_kurul_uyesi_id)->decrement('toplam_puan', $puan);
            }

            // 2. Takım ve takım üyelerinden geri al
            if ($sikayet->atanan_cozum_takimi_id) {
                $takim = \App\Models\Takim::find($sikayet->atanan_cozum_takimi_id);
                if ($takim) {
                    $takim->decrement('toplam_puan', $puan);
                    $uyeler = collect();
                    // Takımın mevcut üyeleri ilişkisi
                    try {
                        $uyeler = $takim->uyeler()->wherePivot('durum', 'onaylandi')->get();
                    } catch (\Exception $e) {}
                    foreach ($uyeler as $uye) {
                        $uye->decrement('toplam_puan', $puan);
                    }
                }
            }
        }

        // Dosyaları fiziksel diskten sil
        foreach ($sikayet->dosyalar as $dosya) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($dosya->dosya_yolu);
        }

        // İlgili IAA projesini sil
        if ($sikayet->iaa_id) {
            $iaa = \App\Models\Iaa::find($sikayet->iaa_id);
            if ($iaa) {
                $iaa->delete();
            }
        }

        // Şikayet loglarını sil
        \App\Models\MusteriSikayetiLog::where('musteri_sikayeti_id', $sikayet->id)->delete();
    }

    /**
     * Sync complaint data with Takvim system.
     */
    protected function syncWithTakvim(MusteriSikayeti $sikayet, $action = 'updated')
    {
        // Avoid infinite loops
        if (request()->has('is_syncing')) {
            return;
        }

        // Dispatch background job for sync to avoid blocking the user
        \App\Jobs\SyncComplaintWithTakvim::dispatch($sikayet->id, $action);
    }
}