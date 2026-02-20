<?php

namespace App\Observers;

use App\Models\TakimDavetiyesi;
use Illuminate\Support\Facades\Log;

class TakimDavetiyesiObserver
{
    /**
     * Handle the TakimDavetiyesi "created" event.
     */
    public function created(TakimDavetiyesi $davetiye): void
    {
        // BURADAKİ ESKİ BİLDİRİM KODU SİLİNDİ.
        // Artık bildirimler TakimController içinde, yönetici hiyerarşisiyle beraber manuel gönderiliyor.
        // Çift bildirim (Double Notification) sorunu çözüldü.
    }

    /**
     * Handle the TakimDavetiyesi "updated" event.
     */
    public function updated(TakimDavetiyesi $davetiye): void
    {
        // BURADAKİ ESKİ BİLDİRİM KODU SİLİNDİ.
        // Kabul/Red bildirimleri de TakimController içinde yönetiliyor.
    }
}