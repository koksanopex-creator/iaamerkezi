<?php

namespace App\Jobs;

use App\Models\MusteriSikayeti;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncComplaintWithTakvim implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sikayetId;
    protected $action;

    /**
     * Create a new job instance.
     */
    public function __construct($sikayetId, $action = 'updated')
    {
        $this->sikayetId = $sikayetId;
        $this->action = $action;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $sikayet = MusteriSikayeti::withTrashed()->find($this->sikayetId);
        
        if (!$sikayet) {
            Log::warning("SyncComplaintWithTakvim: Complaint #{$this->sikayetId} not found.");
            return;
        }

        try {
            $customer = $sikayet->customer;
            if (!$customer) {
                Log::warning("Syncing complaint #{$sikayet->id} skipped: No customer associated.");
                return;
            }

            $bolum = $sikayet->bolum;
            $businessUnitId = $bolum ? $bolum->takvim_business_unit_id : null;

            $token = $sikayet->takip_token;
            if (!$token) {
                $token = $sikayet->id;
            }

            $attachments = [];
            foreach ($sikayet->dosyalar as $dosya) {
                $attachments[] = [
                    'name' => $dosya->orijinal_adi,
                    'url' => asset('storage/' . $dosya->dosya_yolu),
                    'mime_type' => $dosya->mime_tipi
                ];
            }

            $payload = [
                'remote_id' => $sikayet->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'title' => $sikayet->musteri_sikayet_konusu,
                'description' => $sikayet->musteri_sikayet_detayi,
                'status' => $sikayet->musteri_durum,
                'business_unit_id' => $businessUnitId,
                'remote_url' => $sikayet->iaa_id 
                                ? route('proje.workspace.show', $sikayet->iaa_id) 
                                : route('admin.sikayetler.show', $sikayet->id),
                'attachments' => $attachments,
                'remote_creator_name' => $sikayet->olusturanKurulUyesi ? $sikayet->olusturanKurulUyesi->name : 'Sistem',
            ];

            $takvimUrl = config('services.takvim.url');
            if (!$takvimUrl) {
                Log::error("Error syncing complaint to Takvim: Takvim URL is not configured.");
                return;
            }

            if ($this->action === 'deleted' || $this->action === 'force_deleted') {
                $endpoint = $takvimUrl . '/api/complaints/delete';
                $response = Http::timeout(30)->post($endpoint, [
                    'remote_id' => $sikayet->id,
                    'is_syncing' => true,
                    'force' => ($this->action === 'force_deleted')
                ]);
            } elseif ($this->action === 'restored') {
                $endpoint = $takvimUrl . '/api/complaints/restore';
                $response = Http::timeout(30)->post($endpoint, [
                    'remote_id' => $sikayet->id,
                    'is_syncing' => true
                ]);
            } else {
                $response = Http::timeout(30)->post($takvimUrl . '/api/customers/sync', [
                    'type' => 'complaint',
                    'action' => $this->action,
                    'is_syncing' => true,
                    'data' => $payload,
                ]);
            }

            if ($response->failed()) {
                Log::error("Error syncing complaint #{$sikayet->id} to Takvim: " . $response->body());
            } else {
                Log::info("Successfully synced complaint #{$sikayet->id} to Takvim via Job.");
            }
        } catch (\Exception $e) {
            Log::error("Critical error syncing complaint #{$sikayet->id} to Takvim: " . $e->getMessage());
        }
    }
}
