<?php

namespace App\Observers;

use App\Models\TakimDavetiyesi;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TakimDavetiAldin;
use App\Notifications\TakimDavetiYanitlandi;
use Illuminate\Support\Facades\Log;

class TakimDavetiyesiObserver
{
    /**
     * Handle the TakimDavetiyesi "created" event.
     * SENARYO: Bir kullanıcı takıma davet edildiğinde
     */
    public function created(TakimDavetiyesi $davetiye): void
    {
        // Sadece 'type' alanı 'davet' ise çalış (diğeri 'istek' olabilir)
        if ($davetiye->type === 'davet') {
            try {
                // Davet edilen kullanıcıyı bul
                $davetEdilenKullanici = $davetiye->davetEdilen;
                // Daveti gönderen lideri bul
                $davetEdenLider = $davetiye->davetEden;

                if ($davetEdilenKullanici && $davetEdenLider) {
                    $davetEdilenKullanici->notify(new TakimDavetiAldin($davetiye->takim, $davetEdenLider));
                }
            } catch (\Exception $e) {
                Log::error('Takım daveti bildirimi gönderilemedi: ' . $e->getMessage());
            }
        }
    }

    /**
     * Handle the TakimDavetiyesi "updated" event.
     * SENARYO: Takım daveti kabul/red edildiğinde
     */
    public function updated(TakimDavetiyesi $davetiye): void
    {
        // Sadece 'durum' alanı DEĞİŞTİYSE ve artık 'bekliyor' DEĞİLSE çalış
        if ($davetiye->isDirty('durum') && $davetiye->durum !== 'bekliyor') {
            try {
                // Takım liderini bul
                $lider = $davetiye->takim->lider;
                
                // Daveti yanıtlayan kullanıcıyı belirle
                $yanitlayanUser = null;
                if ($davetiye->type === 'davet') {
                    $yanitlayanUser = $davetiye->davetEdilen; // Daveti alan kişi
                } else { // type === 'istek' ise
                    $yanitlayanUser = $davetiye->davetEden; // İsteği gönderen kişi
                }

                if ($lider && $yanitlayanUser) {
                    // Takım liderine bildirimi gönder
                    $lider->notify(new TakimDavetiYanitlandi($davetiye, $yanitlayanUser));
                }
            } catch (\Exception $e) {
                Log::error('Takım daveti yanıtlandı bildirimi gönderilemedi: ' . $e->getMessage());
            }
        }
    }
}