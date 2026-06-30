<?php

namespace App\Observers;

use App\Models\SikayetIadesi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SikayetIadesiObserver
{
    public function created(SikayetIadesi $return)
    {
        $this->syncWithTakvim($return);
    }

    public function updated(SikayetIadesi $return)
    {
        $this->syncWithTakvim($return);
    }

    public function deleted(SikayetIadesi $return)
    {
        $this->syncWithTakvim($return, 'deleted');
    }

    protected function syncWithTakvim(SikayetIadesi $return, $action = 'updated')
    {
        try {
            if (request()->has('is_syncing')) {
                return;
            }

            $sikayet = $return->musteriSikayeti;
            if (!$sikayet || !$sikayet->customer) {
                return;
            }

            $bolum = $sikayet->bolum;
            $businessUnitId = $bolum ? $bolum->takvim_business_unit_id : null;

            $payload = [
                'remote_id' => $return->id,
                'remote_complaint_id' => $sikayet->id,
                'customer_name' => $sikayet->customer->name,
                'customer_email' => $sikayet->customer->email,
                'product_name' => $return->urun_turu,
                'quantity' => $return->miktar,
                'unit' => $return->birim,
                'shipped_quantity' => $return->toplam_parti_miktari,
                'shipped_unit' => $return->birim,
                'reason' => $return->iade_sebebi,
                'return_date' => $sikayet->musteri_sikayet_tarihi ? $sikayet->musteri_sikayet_tarihi->format('Y-m-d') : null,
                'business_unit_id' => $businessUnitId,
                'remote_url' => $sikayet->iaa_id 
                                ? route('proje.workspace.show', $sikayet->iaa_id) 
                                : route('admin.sikayetler.show', $sikayet->id),
            ];

            $takvimUrl = config('services.takvim.url');
            if (!$takvimUrl) {
                return;
            }

            $response = Http::timeout(30)->post($takvimUrl . '/api/customers/sync', [
                'type' => 'return',
                'action' => $action,
                'is_syncing' => true,
                'data' => $payload,
            ]);

            if ($response->failed()) {
                Log::error("Error syncing return #{$return->id} to Takvim: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("Critical error syncing return #{$return->id} to Takvim: " . $e->getMessage());
        }
    }
}
