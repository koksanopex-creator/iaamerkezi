<?php

namespace App\Livewire\Project;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Iaa;
use App\Models\IaaLog;
use App\Models\User;
use App\Notifications\ZiyaretciAtandiBildirimi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use App\Notifications\ZiyaretOnayBekliyorBildirimi;
use App\Notifications\ZiyaretOnayDurumuBildirimi;
use App\Notifications\ZiyaretPlanlandiBilgilendirme;
use App\Traits\VisitNotificationTrait;

class PlanVisit extends Component
{
    use WithFileUploads;
    use VisitNotificationTrait;

    public $iaa;
    public $embedded = false;
    public $stepId = null;
    public $customerData = [];
    public $businessUnits = [];
    public $savedVisit = null;
    public $iaaPersonnel = [];
    public $direktorOnayiAktif = false;
    public $uploadedTempFiles = [];
    public $visitLogs = [];
    public $visitFiles = []; // Yeni çoklu dosya yükleme özelliği için
    public $existingFiles = [];
    public $plannerRevisionNote = '';
    public $isRevisionSubmitModalOpen = false;

    public $formData = [
    'visit_date' => '',
    'visit_reason' => 'Diğer',
    'visit_notes' => '',
    'contact_persons' => [],
    'other_contact_persons' => '',
    'customer_product_id' => '',
    'barcode' => '',
    'lot_no' => '',
    'findings' => '',
    'result' => '',
    'complaint_id' => '',
    'user_id' => '',
    'business_unit_id' => '',
    'visitor_ids' => [],
    'visitor_name' => '',
    'other_visitor_name' => '',
    'visit_file' => []
    ];

    public $isOpen = false;
    public $successMessage = '';
    public $errorMessage = '';
    public $isReadOnly = false;
    public $canSeeFindings = false;
    public $isVisitor = false;
    public $canEdit = false;

    // Approval Properties
    public $showApproveModal = false;
    public $showRejectModal = false;
    public $showRevisionModal = false;
    public $estimatedReturnDate;
    public $rejectionReason;
    public $revisionReason;
    public $isEnteringResults = false;
    public $completerName = null;
    public $completedAt = null;
    public $rejectionReasonSuperadmin = null;

    // --- YENİ EKLENEN İPTAL/GERİ AL PROPERTY'LERİ ---
    public $showCancelModal = false;
    public $cancelReason = null;

    public function mount(Iaa $iaa, $embedded = false, $stepId = null)
    {
        $this->iaa = $iaa;
        $this->embedded = $embedded;
        $this->stepId = $stepId;
        $this->formData['visit_date'] = now()->format('Y-m-d\TH:i');


        if ($iaa->bolum && $iaa->bolum->takvim_business_unit_id)
        {
            $this->formData['business_unit_id'] = $iaa->bolum->takvim_business_unit_id;
        }

        if ($this->embedded)
        {
            $this->loadCustomerData();
        }

        // Düzenleme kısıtlaması (Onay sürecindeki projeler salt okunurdur)
        $this->isReadOnly = in_array($iaa->durum, [
            'Bölüm Onayı Bekliyor',
            'Yönetici Onayı Bekliyor',
            'Direktör Onayı Bekliyor',
            'Tamamlandı',
            'Talep Olarak Kapatıldı'
        ]);

        $currentVisit = $this->getVisit();
        if ($currentVisit) {
            $vpStatus = $currentVisit->status;
            if (in_array($vpStatus, ['Beklemede', 'Revize İsteniyor'])) {
                $this->isReadOnly = false;
            } elseif ($vpStatus === 'Tamamlandı') {
                $this->isReadOnly = true;
            }
        }

        $this->syncAuthorization();

        // Sistem Ayarlarından Ziyaret Direktör Onayı Kontrolü
        $this->direktorOnayiAktif = \App\Models\Setting::get('ziyaret_direktor_onayi_aktif') == '1';
        $this->loadVisitHistory();
    }

    public function loadVisitHistory()
    {
        $this->visitLogs = \App\Models\IaaLog::where('iaa_id', $this->iaa->id)
            ->where(function ($q) {
            $q->where('eylem', 'like', '%Ziyaret%')
              ->orWhere('eylem', 'like', '%Ziyaret Planı%');
        })
            ->with('user')
            ->latest()
            ->get();
    }

    public function toggleForm()
    {
        $this->isOpen = !$this->isOpen;
        if ($this->isOpen && empty($this->customerData))
        {
            $this->loadCustomerData();
        }
    }

    public function getVisit()
    {
        if ($this->stepId) {
            return \App\Models\IaaZiyaretPlani::where('iaa_id', $this->iaa->id)
                ->where('iaa_workflow_step_id', $this->stepId)
                ->first();
        } else {
            return \App\Models\IaaZiyaretPlani::where('iaa_id', $this->iaa->id)
                ->whereNull('iaa_workflow_step_id')
                ->first();
        }
    }

    public function editVisit()
    {
        $this->syncAuthorization();
        if (!$this->canEdit)
        {
            $this->errorMessage = 'Bu işlemi gerçekleştirmek için yetkiniz bulunmamaktadır.';
            return;
        }

        $this->isOpen = true;
        $this->savedVisit = null;
        $this->isEnteringResults = ($this->getVisit() !== null && $this->getVisit()->status === 'Onaylandı');
    }

    public function cancelEdit()
    {
        $this->isEnteringResults = false;
        $this->isOpen = false; // Formu tamamen kapat

        // Re-fetch existing visit from saved customer data if available
        if (isset($this->customerData['existing_visit']))
        {
            $this->savedVisit = $this->customerData['existing_visit'];
        }
        else
        {
            // Default to form if somehow data is lost, but usually this won't happen
            $this->savedVisit = null;
        }
    }

    public function loadCustomerData()
    {
        if (!$this->iaa->musteriSikayeti || !$this->iaa->musteriSikayeti->customer)
        {
            $this->errorMessage = 'Müşteri bilgisi bulunamadı.';
            return;
        }

        // === BÖLÜM EŞLEŞMESİ KONTROLÜ ===
        if (empty($this->formData['business_unit_id']) && $this->iaa->bolum && $this->iaa->bolum->takvim_business_unit_id)
        {
            $this->formData['business_unit_id'] = $this->iaa->bolum->takvim_business_unit_id;
        }

        $customerName = $this->iaa->musteriSikayeti->customer->name;
        $buId = $this->formData['business_unit_id'];

        // Optimizasyon: Eğer veriler zaten varsa ve BU değişmediyse tekrar çekme
        if (!empty($this->customerData) && ($this->customerData['business_unit_id'] ?? null) == $buId)
        {
            return;
        }

        try
        {
            $takvimUrl = config('services.takvim.url');

            // Fetch Business Units if not loaded
            if (empty($this->businessUnits))
            {
                $buResponse = Http::timeout(10)->get($takvimUrl . '/api/business-units');
                if ($buResponse->successful())
                {
                    $this->businessUnits = $buResponse->json();
                }
            }

            $response = Http::timeout(15)->get($takvimUrl . '/api/customers/visit-data', [
                'customer_name' => trim($customerName),
                'remote_id' => $this->iaa->id,
                'business_unit_id' => $buId
            ]);

            if ($response->successful())
            {
                $this->customerData = $response->json();
                $this->errorMessage = null;

                // === PERSONEL LİSTESİNİ HARMANLA (Takvim + Yerel İAA) ===
                $localUsers = \App\Models\User::where('is_personnel', 1)->get(['id', 'name', 'email']);
                $takvimUsers = \Illuminate\Support\Collection::make($this->customerData['users'] ?? []);

                foreach ($localUsers as $lUser)
                {
                    $existsInTakvim = $takvimUsers->first(function ($tU) use ($lUser) {
                        $tEmail = is_array($tU) ? ($tU['email'] ?? null) : ($tU->email ?? null);
                        return $tEmail && strtolower($tEmail) == strtolower($lUser->email);
                    });

                    if (!$existsInTakvim)
                    {
                        $this->customerData['users'][] = [
                            'id' => $lUser->id, // Dikkat: Bu yerel ID, save kısmında kontrol edilecek
                            'name' => $lUser->name . ' (İAA Personeli)',
                            'email' => $lUser->email,
                            'is_local' => true
                        ];
                    }
                }

                // Kullanıcıları isme göre alfabetik sırala
                if (isset($this->customerData['users'])) {
                    $this->customerData['users'] = collect($this->customerData['users'])
                        ->sortBy('name')
                        ->values()
                        ->all();
                }

                // === YENİ MANTIK: LOKAL VERİTABANINDAN ÇEK ===
                $localVisit = $this->getVisit();

                if ($localVisit)
                {
                    $localVisit->load('planner');
                    $this->savedVisit = $localVisit->toArray();
                    $this->savedVisit['planner_name'] = $localVisit->planner->name ?? 'Planlayan Kişi';
                    $this->customerData['existing_visit'] = $this->savedVisit;

                    // Business unit name for array
                    foreach ($this->businessUnits as $bu)
                    {
                        $buId = is_array($bu) ? ($bu['id'] ?? null) : ($bu->id ?? null);
                        if ($buId == $localVisit->business_unit_id)
                        {
                            $this->savedVisit['business_unit'] = $bu;
                            break;
                        }
                    }

                    // Fallback for Business Unit name from local DB if API didn't provide it
                    if (!isset($this->savedVisit['business_unit']))
                    {
                        $bolum = \App\Models\Bolum::where('takvim_business_unit_id', $localVisit->business_unit_id)->first();
                        $this->savedVisit['business_unit'] = [
                            'id' => $localVisit->business_unit_id,
                            'name' => $bolum ? $bolum->ad : 'Bilinmeyen Birim (ID:' . $localVisit->business_unit_id . ')'
                        ];
                    }

                    // User name for array
                    $visitorsArray = [];
                    $visitorIds = $localVisit->visitors ? (is_string($localVisit->visitors) ? json_decode($localVisit->visitors, true) : (is_array($localVisit->visitors) ? $localVisit->visitors : [])) : [];
                    
                    if (empty($visitorIds) && $localVisit->visitor_id) {
                        $visitorIds = [(string) $localVisit->visitor_id];
                    }

                    if (!empty($visitorIds))
                    {
                        foreach ($visitorIds as $vId) {
                            $visitorNameStr = null;
                            
                            // 1. Try Takvim API users
                            foreach ($this->customerData['users'] ?? [] as $tUser)
                            {
                                if ($tUser['id'] == $vId)
                                {
                                    $visitorNameStr = $tUser['name'];
                                    break;
                                }
                            }

                            // 2. Try Local DB users
                            $visitorUser = null;
                            if (!$visitorNameStr)
                            {
                                $visitorUser = \App\Models\User::find($vId);
                                if ($visitorUser)
                                {
                                    $visitorNameStr = $visitorUser->name;
                                }
                            }
                            else
                            {
                                $visitorUser = \App\Models\User::find($vId);
                            }

                            $userArray = ['name' => $visitorNameStr ?? 'Bilinmeyen'];
                            if ($visitorUser)
                            {
                                $userArray['email'] = $visitorUser->email;
                                $userArray['phone'] = $visitorUser->telefon;
                                $userArray['title'] = $visitorUser->unvan;
                                $userArray['photo'] = $visitorUser->profile_photo_path ? asset('storage/' . $visitorUser->profile_photo_path) : null;
                            }
                            $visitorsArray[] = $userArray;
                        }
                        
                        $this->savedVisit['users'] = $visitorsArray;
                        $this->savedVisit['user'] = $visitorsArray[0] ?? null; // Geriye dönük uyumluluk için ilki
                    }
                    elseif ($localVisit->visitor_name)
                    {
                        $this->savedVisit['user'] = ['name' => $localVisit->visitor_name];
                        $this->savedVisit['users'] = [['name' => $localVisit->visitor_name]];
                    }

                    // Product name for array
                    if ($localVisit->customer_product_id)
                    {
                        foreach ($this->customerData['products'] ?? [] as $product)
                        {
                            if ($product['id'] == $localVisit->customer_product_id)
                            {
                                $this->savedVisit['product'] = $product;
                                break;
                            }
                        }
                    }

                    // Populate formData for editing
                    $this->formData['visit_date'] = Carbon::parse($localVisit->visit_date)->format('Y-m-d\TH:i');
                    $this->formData['visit_reason'] = $localVisit->visit_reason;
                    $this->formData['visit_notes'] = $localVisit->visit_notes;
                    $this->formData['contact_persons'] = $localVisit->contact_persons ?? [];
                    $this->formData['customer_product_id'] = $localVisit->customer_product_id;
                    $this->formData['barcode'] = $localVisit->barcode;
                    $this->formData['lot_no'] = $localVisit->lot_no;
                    $this->formData['findings'] = $localVisit->findings;
                    $this->formData['result'] = $localVisit->result;
                    $this->formData['business_unit_id'] = $localVisit->business_unit_id;
                    // Çoklu Ziyaretçi ID'leri formata aktarılıyor
                    // Yerel ID'leri customerData['users'] listesindeki ID'lerle eşleştir
                    // (Takvim'den gelen kullanıcılar farklı ID'ye sahip olabilir)
                    $resolvedVisitorIds = [];
                    if (!empty($visitorIds)) {
                        foreach ($visitorIds as $vId) {
                            // Önce direkt ID eşleşmesi dene
                            $directMatch = collect($this->customerData['users'] ?? [])->first(fn($u) => (string)($u['id'] ?? '') === (string)$vId);
                            if ($directMatch) {
                                $resolvedVisitorIds[] = (string)$directMatch['id'];
                            } else {
                                // Email üzerinden eşleştir (yerel ID → Takvim ID)
                                $localUser = \App\Models\User::find($vId);
                                if ($localUser && $localUser->email) {
                                    $takvimMatch = collect($this->customerData['users'] ?? [])->first(function($u) use ($localUser) {
                                        return isset($u['email']) && strtolower($u['email']) === strtolower($localUser->email);
                                    });
                                    if ($takvimMatch) {
                                        $resolvedVisitorIds[] = (string)$takvimMatch['id'];
                                    } else {
                                        // Kullanıcı Takvim listesinde yok, yerel ID ile devam et
                                        $resolvedVisitorIds[] = (string)$vId;
                                    }
                                } else {
                                    $resolvedVisitorIds[] = (string)$vId;
                                }
                            }
                        }
                    }
                    $this->formData['visitor_ids'] = !empty($resolvedVisitorIds) ? $resolvedVisitorIds : ($localVisit->visitor_name ? ['Diğer'] : []);
                    $this->formData['other_visitor_name'] = $localVisit->visitor_name ?? '';
                    $this->formData['visitor_name'] = $localVisit->visitor_name ?? '';

                    if ($localVisit->completed_by)
                    {
                        $this->completerName = $localVisit->completer->name ?? 'Bilinmeyen';
                        $this->completedAt = $localVisit->completed_at->format('d.m.Y H:i');
                    }

                    $this->syncAuthorization();
                }
            }
            else
            {
                $errorMsg = $response->body();
                Log::error('Takvim API Error Response: ' . $errorMsg . ' Status: ' . $response->status());
                $this->errorMessage = 'Takvim verileri alınamadı. (Hata: ' . ($response->json('error') ?? $response->status()) . ')';
            }
        }
        catch (\Exception $e)
        {
            Log::error('Visit data fetch failed: ' . $e->getMessage());
            $this->errorMessage = 'Bağlantı hatası: Takvim uygulamasına ulaşılamıyor. ' . $e->getMessage();
        }
    }

    public function save()
    {
        $this->validate([
            'formData.visit_date' => 'required',
            'formData.visit_reason' => 'required',
            'formData.visitor_ids' => 'required|array|min:1',
            'formData.business_unit_id' => 'required',
        ], [
            'formData.visitor_ids.required' => 'Ziyaretçiler alanı zorunludur.',
            'formData.visitor_ids.min' => 'En az bir ziyaretçi seçilmelidir.'
        ]);

        // Modal interceptor removed, it saves directly now

        try
        {
            $visitorIdsRaw = $this->formData['visitor_ids'] ?? [];
            $finalVisitorIds = [];
            $visitorNames = [];

            foreach ($visitorIdsRaw as $vId) {
                if ($vId === 'Diğer') {
                    if (!empty($this->formData['other_visitor_name'])) {
                        $visitorNames[] = trim($this->formData['other_visitor_name']);
                    }
                    continue;
                }

                $localVisitorId = null;
                $vName = null;

                // 1. Önce harmanlanmış customerData içinden bulmaya çalışalım
                $foundUser = collect($this->customerData['users'] ?? [])->firstWhere('id', $vId);

                if ($foundUser)
                {
                    $vName = $foundUser['name'];
                    // Temiz isim (İAA Personeli ibaresini kaldır)
                    $vName = str_replace(' (İAA Personeli)', '', $vName);

                    // 2. Email üzerinden yerel kullanıcıyı bul
                    if (isset($foundUser['email']))
                    {
                        $localUser = \App\Models\User::where('email', $foundUser['email'])->first();
                        if ($localUser)
                        {
                            $localVisitorId = $localUser->id;
                        }
                    }

                    // Eğer yerel kullanıcı hala bulunamadıysa ve is_local işaretliyse, visitorId zaten yerel ID'dir
                    if (!$localVisitorId && isset($foundUser['is_local']))
                    {
                        $localVisitorId = $vId;
                    }
                }

                // 3. Fallback: Hala isim yoksa yerel DB'den direkt çek
                if (!$vName)
                {
                    $u = \App\Models\User::find($vId);
                    if ($u)
                    {
                        $vName = $u->name;
                        $localVisitorId = $u->id;
                    }
                }

                if (!$localVisitorId && $vId) {
                    $localVisitorId = $vId;
                }

                if ($localVisitorId) {
                    $finalVisitorIds[] = (string) $localVisitorId;
                }
                if ($vName) {
                    $visitorNames[] = $vName;
                }
            }

            $primaryVisitorId = $finalVisitorIds[0] ?? null;
            $visitorNameString = implode(', ', array_filter($visitorNames));

            // Combine contact persons
            $contactPersons = $this->formData['contact_persons'] ?? [];
            if (!empty($this->formData['other_contact_persons']))
            {
                $others = array_map('trim', explode(',', $this->formData['other_contact_persons']));
                $contactPersons = array_merge($contactPersons, $others);
            }

            // [ÇOKLU DOSYA YÜKLEME MANTIĞI - GELENEKSEL TAŞIMA]
            $currentFiles = $this->getVisit()->visit_file ?? [];
            if (!is_array($currentFiles))
                $currentFiles = [$currentFiles];

            if (!empty($this->uploadedTempFiles))
            {
                foreach ($this->uploadedTempFiles as $tempFile)
                {
                    $tempPath = $tempFile['path'];
                    $originalName = $tempFile['name'];

                    if (Storage::disk('public')->exists($tempPath))
                    {
                        $extension = pathinfo($tempPath, PATHINFO_EXTENSION);
                        $cleanName = Str::slug(Str::before($originalName, '.'));
                        $filename = now()->format('Ymd_Hi') . '_' . Str::random(5) . '_' . $cleanName . '.' . $extension;
                        $folder = "proje/{$this->iaa->id}/ziyaret";
                        $finalPath = $folder . '/' . $filename;

                        // DosyayÄ± geÃ§iciden kalÄ±cÄ±ya taÅŸÄ±
                        Storage::disk('public')->move($tempPath, $finalPath);
                        $currentFiles[] = $finalPath;
                    }
                }
                // GeÃ§ici listeyi temizle
                $this->uploadedTempFiles = [];
            }

            // [YENİ] Önceki ziyaretçileri kaydet (Bildirim optimizasyonu için)
            $oldVisitorIds = [];
            if ($this->getVisit() && $this->getVisit()->exists) {
                $oldVisitorIds = $this->getVisit()->visitors ?? [];
                // Geriye dönük uyumluluk (eski visitor_id varsa)
                if ($this->getVisit()->visitor_id && !in_array((string)$this->getVisit()->visitor_id, $oldVisitorIds)) {
                    $oldVisitorIds[] = (string)$this->getVisit()->visitor_id;
                }
            }

            $ziyaretPlani = \App\Models\IaaZiyaretPlani::updateOrCreate(
                [
                    'iaa_id' => $this->iaa->id,
                    'iaa_workflow_step_id' => $this->stepId
                ],
                [
                    'visitor_id' => $primaryVisitorId, // Geriye dönük uyumluluk ve primary için
                    'visitors' => !empty($finalVisitorIds) ? $finalVisitorIds : null,
                    'visitor_name' => $visitorNameString,
                    'planner_id' => Auth::id(),
                    'business_unit_id' => $this->formData['business_unit_id'],
                    'customer_product_id' => $this->formData['customer_product_id'] ?: null,
                    'barcode' => $this->formData['barcode'] ?: null,
                    'lot_no' => $this->formData['lot_no'] ?: null,
                    'contact_persons' => $contactPersons,
                    'visit_date' => clone Carbon::parse($this->formData['visit_date']),
                    'visit_reason' => $this->formData['visit_reason'],
                    'visit_notes' => $this->formData['visit_notes'],
                    'visit_file' => $currentFiles,
                    'status' => 'Beklemede',
                    'rejection_reason_superadmin' => null,
                    'planner_revision_note' => (($this->getVisit()->status ?? '') === 'Revize İsteniyor') ? $this->plannerRevisionNote : null,
                ]
            );

            $this->visitFiles = []; // Inputu sıfırla

        // Fetch to ensure we have the array formatting correct for the view
        $this->savedVisit = $ziyaretPlani->fresh()->toArray();

        foreach ($this->businessUnits as $bu)
        {
            if ($bu['id'] == $this->formData['business_unit_id'])
            {
                $this->savedVisit['business_unit'] = $bu;
                break;
            }
        }

        // Fallback for Business Unit
        if (!isset($this->savedVisit['business_unit']))
        {
            $bolum = \App\Models\Bolum::where('takvim_business_unit_id', $this->formData['business_unit_id'])->first();
            $this->savedVisit['business_unit'] = [
                'id' => $this->formData['business_unit_id'],
                'name' => $bolum ? $bolum->ad : 'Bilinmeyen Birim (ID:' . $this->formData['business_unit_id'] . ')'
            ];
        }

        if (!empty($finalVisitorIds))
            {
                $visitorsArray = [];
                foreach ($finalVisitorIds as $fId) {
                    $visitorUser = \App\Models\User::find($fId);
                    if ($visitorUser) {
                        $visitorsArray[] = [
                            'name' => $visitorUser->name,
                            'email' => $visitorUser->email,
                            'phone' => $visitorUser->telefon,
                            'title' => $visitorUser->unvan,
                            'photo' => $visitorUser->profile_photo_path ? asset('storage/' . $visitorUser->profile_photo_path) : null
                        ];
                    }
                }
                
                $this->savedVisit['users'] = $visitorsArray;
                $this->savedVisit['user'] = $visitorsArray[0] ?? null; // Geriye dönük uyumluluk
            }
            elseif (!empty($visitorNames))
            {
                $this->savedVisit['users'] = array_map(fn($n) => ['name' => $n], $visitorNames);
                $this->savedVisit['user'] = ['name' => $visitorNames[0]];
            }

            if ($this->formData['customer_product_id'])
            {
                foreach ($this->customerData['products'] ?? [] as $product)
                {
                    if ($product['id'] == $this->formData['customer_product_id'])
                    {
                        $this->savedVisit['product'] = $product;
                        
                        // Ziyaret planında seçilen ürünü müşteri şikayetine de yansıt ki listelerde "Ürün belirtilmedi" yazmasın
                        if ($this->iaa->musteriSikayeti) {
                            $this->iaa->musteriSikayeti->update([
                                'musteri_urun_veya_hizmet' => $product['name']
                            ]);
                        }
                        break;
                    }
                }
            }
            $this->successMessage = 'Ziyaret planı başarıyla kaydedildi ve onaya sunuldu.';
            $this->isOpen = false;

            $this->iaa->update(['visit_planned' => true]);

            $visitDate = Carbon::parse($this->formData['visit_date'])->format('d.m.Y H:i');
            $customerNameStr = $this->iaa->musteriSikayeti->customer->name ?? 'Müşteri';

            $eylem = 'Ziyaret Planı Oluşturuldu';
            $aciklama = Auth::user()->name . " tarafından $customerNameStr firmasına $visitDate tarihi için taslak ziyaret planı oluşturuldu.";

            if ($this->isRevisionSubmitModalOpen) {
                $eylem = 'Ziyaret Planı Revize Edildi';
                $aciklama = Auth::user()->name . " tarafından ziyaret planı revize edilerek yeniden onaya sunuldu.";
                if (!empty($this->plannerRevisionNote)) {
                    $aciklama .= " Yapılan değişiklikler: \"" . $this->plannerRevisionNote . "\"";
                }
            } elseif ($this->getVisit() && $this->getVisit()->exists) {
                $eylem = 'Ziyaret Planı Güncellendi';
                $aciklama = Auth::user()->name . " tarafından ziyaret planı güncellendi ve onaya sunuldu.";
            }

            IaaLog::create([
                'iaa_id' => $this->iaa->id,
                'user_id' => Auth::id(),
                'eylem' => $eylem,
                'aciklama' => $aciklama
            ]);

            // [YENİ] Bildirim Mekanizması

            // 1. Ziyaretçilere Bildirim Gönder
            // Bildirim Optimizasyonu: Sadece yeni eklenenlere bildirim gönder, çıkarılanlara iptal bildirimi gönder.
            // Zaten var olanlara tekrar bildirim GÖNDERME.
            $addedVisitors = array_diff($finalVisitorIds, $oldVisitorIds);
            $removedVisitors = array_diff($oldVisitorIds, $finalVisitorIds);

            // Yeni eklenenlere planlandı bildirimi
            foreach ($addedVisitors as $fId)
            {
                $visitor = User::find($fId);
                if ($visitor)
                {
                    $visitor->notify(new \App\Notifications\ZiyaretPlanlandiBilgilendirme($this->iaa, 'visitor'));
                }
            }

            // Mevcut (Değişmeyen) ziyaretçilere "ekibe yeni katılanlar" bildirimi
            $existingVisitors = array_intersect($finalVisitorIds, $oldVisitorIds);
            if (count($addedVisitors) > 0 && count($existingVisitors) > 0) {
                $addedNames = \App\Models\User::whereIn('id', $addedVisitors)->pluck('name')->toArray();
                $addedNamesStr = implode(', ', $addedNames);

                foreach ($existingVisitors as $fId) {
                    $visitor = User::find($fId);
                    if ($visitor) {
                        $visitor->notify(new \App\Notifications\ZiyaretEkipGuncellendiBilgilendirme($this->iaa, $addedNamesStr));
                    }
                }
            }

            // Çıkarılanlara iptal bildirimi
            foreach ($removedVisitors as $fId)
            {
                $visitor = User::find($fId);
                if ($visitor)
                {
                    $visitor->notify(new \App\Notifications\ZiyaretIptalBilgilendirme($this->iaa, 'visitor_cancelled', $this->plannerRevisionNote));
                }
            }

            // 2. Bölüm Liderine Bildirim Gönder
            if ($this->iaa->bolum_id)
            {
                $deptLeaders = User::role('Bölüm Lideri')->where('bolum_id', $this->iaa->bolum_id)->get();
                foreach ($deptLeaders as $leader)
                {
                    $leader->notify(new ZiyaretPlanlandiBilgilendirme($this->iaa, 'leader'));
                }
            }

            // 3. Bölüm Kalite Yöneticisine Bildirim Gönder
            if ($this->iaa->musteriSikayeti && $this->iaa->musteriSikayeti->sikayet_kategorisi_id)
            {
                $catId = $this->iaa->musteriSikayeti->sikayet_kategorisi_id;
                $qualityManagers = User::role('Bölüm Kalite Yöneticisi')
                    ->whereHas('yonettigiSikayetKategorileri', function ($q) use ($catId) {
                    $q->where('sikayet_kategorileri.id', $catId);
                })->get();

                foreach ($qualityManagers as $qm)
                {
                    $qm->notify(new ZiyaretPlanlandiBilgilendirme($this->iaa, 'quality'));
                }
            }

            $catId = $this->iaa->musteriSikayeti->sikayet_kategorisi_id ?? null;
            
            // 4. Onay Makamına Bildirim Gönder
            // Ziyaretler her zaman "Beklemede" olarak başladığından, ilk onay Bölüm Kalite Yöneticisindedir.
            try {
                if ($catId) {
                    $qualityManagers = User::role('Bölüm Kalite Yöneticisi')
                        ->whereHas('yonettigiSikayetKategorileri', function ($q) use ($catId) {
                        $q->where('sikayet_kategorileri.id', $catId);
                    })->get();
                    
                    foreach ($qualityManagers as $qm) {
                        $qm->notify(new ZiyaretOnayBekliyorBildirimi($this->iaa, Auth::user()->name));
                    }
                }
            } catch (\Exception $e) {
                Log::error('Quality Manager notification failed: ' . $e->getMessage());
            }

            $this->iaa->refresh();
            $this->syncAuthorization();
            $this->dispatch('visit-synced');

        }
        catch (\Exception $e)
        {
            Log::error('Visit storage failed: ' . $e->getMessage());
            $this->errorMessage = 'Kaydedilirken bir hata oluştu: ' . $e->getMessage();
        }
    }

    public function updated($name)
    {
        if ($name === 'formData.business_unit_id')
        {
            $this->formData['customer_product_id'] = '';
            $this->loadCustomerData();
        }
    }

    // --- ONAY VE İŞAKIŞI İŞLEMLERİ ---

    public function openApproveModal()
    {
        $this->showApproveModal = true;
    }

    public function closeApproveModal()
    {
        $this->showApproveModal = false;
        $this->estimatedReturnDate = null;
    }

    public function approveVisit()
    {
        $this->validate([
            'estimatedReturnDate' => 'required|date|after_or_equal:today',
        ], [
            'estimatedReturnDate.required' => 'Tahmini dönüş tarihi zorunludur.',
            'estimatedReturnDate.after_or_equal' => 'Dönüş tarihi bugünden önce olamaz.',
        ]);

        $ziyaretPlani = $this->getVisit();

        if ($ziyaretPlani)
        {
            $user = Auth::user();
            $isSuperAdmin = $user->hasRole('Superadmin');
            $isDirector = $user->hasRole('Direktör');
            $isQualityManager = $user->hasRole('Bölüm Kalite Yöneticisi');

            // Güvenlik: Ziyareti gerçekleştirecek kişi (Visitor) kendi ziyaretini onaylayamaz (eğer Admin değilse)
            $isVisitor = false;
            $visitorIdsArray = $ziyaretPlani->visitors ? (is_string($ziyaretPlani->visitors) ? json_decode($ziyaretPlani->visitors, true) : (is_array($ziyaretPlani->visitors) ? $ziyaretPlani->visitors : [])) : [];
            if (empty($visitorIdsArray) && $ziyaretPlani->visitor_id) {
                $visitorIdsArray = [(string) $ziyaretPlani->visitor_id];
            }
            $isVisitor = in_array((string)$user->id, $visitorIdsArray);

            $bolum = $this->iaa->bolum ?? null;
            $catId = $this->iaa->musteriSikayeti->sikayet_kategorisi_id ?? null;

            $canApproveAsQuality = false;
            $canApproveAsDirector = false;

            if ($this->direktorOnayiAktif) {
                $canApproveAsQuality = $isSuperAdmin || ($isQualityManager && $user->yonettigiSikayetKategorileri->contains('id', $catId));
                $canApproveAsDirector = $isSuperAdmin || ($isDirector && $bolum && $bolum->director_id == $user->id);
            } else {
                $canApproveAsQuality = $isSuperAdmin || ($isQualityManager && $user->yonettigiSikayetKategorileri->contains('id', $catId));
            }

            if ($isVisitor && !$isSuperAdmin) {
                $canApproveAsQuality = false;
                $canApproveAsDirector = false;
            }

            if (!$canApproveAsQuality && !$canApproveAsDirector) {
                $this->errorMessage = 'Bu işlemi gerçekleştirmek için yetkiniz bulunmamaktadır.';
                return;
            }

            $yeniDurum = 'Onaylandı';

            if ($this->direktorOnayiAktif) {
                if ($canApproveAsDirector && ($user->hasRole('Direktör') || $isSuperAdmin)) {
                    $yeniDurum = 'Onaylandı';
                } elseif ($canApproveAsQuality && $ziyaretPlani->status === 'Beklemede') {
                    $yeniDurum = 'Direktör Onayı Bekliyor';
                } elseif ($ziyaretPlani->status === 'Direktör Onayı Bekliyor') {
                    $yeniDurum = 'Direktör Onayı Bekliyor'; 
                }
            }

            $ziyaretPlani->status = $yeniDurum;
            $ziyaretPlani->estimated_return_date = $this->estimatedReturnDate;
            if ($yeniDurum === 'Onaylandı') {
                $ziyaretPlani->approved_by = Auth::id();
            }
            $ziyaretPlani->save();

            $this->dispatchVisitWorkflowNotifications($this->iaa, $yeniDurum, Auth::user()->name);

            IaaLog::create([
                'iaa_id' => $this->iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Ziyaret Planı Onaylandı',
                'aciklama' => Auth::user()->name . " tarafından ziyaret planı onaylandı. Tahmini Dönüş Tarihi: " . Carbon::parse($this->estimatedReturnDate)->format('d.m.Y H:i')
            ]);

            $this->successMessage = 'Ziyaret planı başarıyla onaylandı.';

            $this->closeApproveModal();
            $this->loadCustomerData(); // Verileri yenile
        }
    }

    public function revertApproval()
    {
        $ziyaretPlani = $this->getVisit();
        if (!$ziyaretPlani) return;

        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('Superadmin');
        
        // Sadece onaylayan kişi veya Superadmin geri alabilir
        if ($ziyaretPlani->approved_by != $user->id && !$isSuperAdmin) {
            $this->errorMessage = 'Bu onayı geri alma yetkiniz yok.';
            return;
        }

        $eskiDurum = $ziyaretPlani->status;
        $yeniDurum = 'Beklemede'; // Default
        
        if ($eskiDurum === 'Onaylandı' && $this->direktorOnayiAktif) {
            if ($user->hasRole('Direktör') && !$isSuperAdmin) {
                $yeniDurum = 'Direktör Onayı Bekliyor';
            }
        }

        $ziyaretPlani->status = $yeniDurum;
        $ziyaretPlani->approved_by = null;
        $ziyaretPlani->save();

        // Eski bildirimleri sil
        \DB::table('notifications')
            ->where('type', 'like', '%VisitWorkflowNotification%')
            ->whereJsonContains('data->iaa_id', $this->iaa->id)
            ->delete();
        
        // Yeni zil bildirimi gönder (Planlayıcıya)
        $planner = \App\Models\User::find($ziyaretPlani->planner_id);
        if ($planner) {
            $planner->notify(new \App\Notifications\VisitApprovalRevertedNotification($this->iaa, $user->name));
        }
        
        IaaLog::create([
            'iaa_id' => $this->iaa->id,
            'user_id' => $user->id,
            'eylem' => 'Ziyaret Onayı Geri Alındı',
            'aciklama' => "{$user->name} tarafından ziyaret planı onayı geri alındı. Durum '{$eskiDurum}' aşamasından '{$yeniDurum}' aşamasına çekildi."
        ]);

        $this->successMessage = 'Ziyaret onayı başarıyla geri alındı.';
        $this->loadCustomerData(); // Verileri yenile
        $this->syncAuthorization();
    }

    public function openRejectModal()
    {
        $this->showRejectModal = true;
    }

    public $showUpdateReturnDateModal = false;
    public $newReturnDate;

    public function openUpdateReturnDateModal()
    {
        if ($this->getVisit()) {
            $this->newReturnDate = $this->getVisit()->estimated_return_date ? \Carbon\Carbon::parse($this->getVisit()->estimated_return_date)->format('Y-m-d') : null;
            $this->showUpdateReturnDateModal = true;
        }
    }

    public function closeUpdateReturnDateModal()
    {
        $this->showUpdateReturnDateModal = false;
    }

    public function updateReturnDate()
    {
        $this->validate([
            'newReturnDate' => 'required|date|after_or_equal:today',
        ], [
            'newReturnDate.required' => 'Yeni dönüş tarihi zorunludur.',
            'newReturnDate.after_or_equal' => 'Dönüş tarihi bugünden önce olamaz.',
        ]);

        $ziyaretPlani = $this->getVisit();
        if (!$ziyaretPlani) return;

        $eskiTarih = $ziyaretPlani->estimated_return_date ? \Carbon\Carbon::parse($ziyaretPlani->estimated_return_date)->format('d.m.Y') : 'Belirtilmedi';
        $yeniTarihFormat = \Carbon\Carbon::parse($this->newReturnDate)->format('d.m.Y');

        $ziyaretPlani->estimated_return_date = $this->newReturnDate;
        $ziyaretPlani->save();

        IaaLog::create([
            'iaa_id' => $this->iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Dönüş Tarihi Güncellendi',
            'aciklama' => Auth::user()->name . " tarafından tahmini dönüş tarihi {$eskiTarih} tarihinden {$yeniTarihFormat} tarihine güncellendi."
        ]);

        $this->successMessage = 'Dönüş tarihi başarıyla güncellendi.';
        $this->closeUpdateReturnDateModal();
        $this->loadCustomerData(); // Verileri yenile
    }

    public $showReturnDateRevisionModal = false;
    public $returnDateRevisionReason;
    public $requestedReturnDate;

    public function openReturnDateRevisionModal()
    {
        $this->requestedReturnDate = null;
        $this->returnDateRevisionReason = '';
        $this->showReturnDateRevisionModal = true;
    }

    public function requestReturnDateRevision()
    {
        $this->validate([
            'requestedReturnDate' => 'required|date|after_or_equal:today',
            'returnDateRevisionReason' => 'required|min:10'
        ], [
            'requestedReturnDate.required' => 'Talep edilen tarih zorunludur.',
            'requestedReturnDate.after_or_equal' => 'Talep edilen tarih bugünden önce olamaz.',
            'returnDateRevisionReason.required' => 'Revizyon gerekçesi zorunludur.',
            'returnDateRevisionReason.min' => 'Revizyon gerekçesi en az 10 karakter olmalıdır.'
        ]);

        $ziyaretPlani = $this->getVisit();
        if (!$ziyaretPlani) return;

        $ziyaretPlani->return_date_revision_status = 'Bekliyor';
        $ziyaretPlani->return_date_revision_requested_date = $this->requestedReturnDate;
        $ziyaretPlani->return_date_revision_reason = $this->returnDateRevisionReason;
        $ziyaretPlani->return_date_revision_requested_by = \Illuminate\Support\Facades\Auth::id();
        $ziyaretPlani->save();

        IaaLog::create([
            'iaa_id' => $this->iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Dönüş Tarihi Revizyonu Talep Edildi',
            'aciklama' => Auth::user()->name . " tarafından dönüş tarihi için revizyon talep edildi. Talep Edilen Tarih: " . \Carbon\Carbon::parse($this->requestedReturnDate)->format('d.m.Y') . " - Gerekçe: " . $this->returnDateRevisionReason
        ]);
        
        $this->dispatchDonusTarihiRevizyonNotifications($this->iaa, Auth::user()->name, $this->returnDateRevisionReason);

        $this->successMessage = 'Dönüş tarihi revizyon talebiniz başarıyla iletildi.';
        $this->showReturnDateRevisionModal = false;
        $this->loadCustomerData();
    }

    public function cancelReturnDateRevision()
    {
        $ziyaretPlani = $this->getVisit();
        if (!$ziyaretPlani || !in_array($ziyaretPlani->return_date_revision_status, ['Bekliyor', 'Direktör Onayı Bekliyor'])) {
            return;
        }

        // Revert status
        $ziyaretPlani->return_date_revision_status = null;
        $ziyaretPlani->return_date_revision_requested_date = null;
        $ziyaretPlani->return_date_revision_reason = null;
        $ziyaretPlani->return_date_revision_requested_by = null;
        $ziyaretPlani->save();

        IaaLog::create([
            'iaa_id' => $this->iaa->id,
            'user_id' => Auth::id(),
            'eylem' => 'Dönüş Tarihi Revizyonu İptal Edildi',
            'aciklama' => Auth::user()->name . " tarafından yapılan dönüş tarihi revizyon talebi iptal edildi."
        ]);

        // Delete pending notifications for this revision
        \Illuminate\Notifications\DatabaseNotification::where('type', 'App\Notifications\DonusTarihiRevizyonTalebiBildirimi')
            ->where('data->iaa_id', $this->iaa->id)
            ->delete();

        // Send cancellation notification to same people who got the request
        $this->dispatchVisitWorkflowNotifications($this->iaa, 'Dönüş Tarihi Revizyonu İptal Edildi', Auth::user()->name, "Talep iptal edilmiştir.");

        $this->successMessage = "Revizyon talebi iptal edildi.";
        $this->loadCustomerData();
    }

    public $showReturnDateRevisionResponseModal = false;
    public $returnDateRevisionResponseNote;
    public $returnDateRevisionAction; // 'approve' or 'reject'

    public function openReturnDateRevisionResponseModal($action)
    {
        $this->returnDateRevisionAction = $action;
        $this->returnDateRevisionResponseNote = '';
        $this->showReturnDateRevisionResponseModal = true;
    }

    public function respondToReturnDateRevision()
    {
        $ziyaretPlani = $this->getVisit();
        if (!$ziyaretPlani) return;

        if ($this->returnDateRevisionAction === 'reject') {
            $this->validate([
                'returnDateRevisionResponseNote' => 'required|min:5'
            ], [
                'returnDateRevisionResponseNote.required' => 'Reddetme gerekçesi zorunludur.'
            ]);
        }

        $isDirektorOnayi = $ziyaretPlani->return_date_revision_status === 'Direktör Onayı Bekliyor';

        if ($this->returnDateRevisionAction === 'approve') {
            if ($ziyaretPlani->return_date_revision_status === 'Bekliyor' && $this->direktorOnayiAktif) {
                // Bölüm Lideri onayladı, Direktöre gidiyor
                $ziyaretPlani->return_date_revision_status = 'Direktör Onayı Bekliyor';
                $eylem = 'Dönüş Tarihi Revizyonu Bölüm Onayından Geçti';
                $aciklama = Auth::user()->name . " tarafından dönüş tarihi revizyon talebi onaylandı ve direktör onayına sunuldu.";
                $bildirimTipi = 'Dönüş Tarihi Revizyonu Direktör Onayına Gönderildi';
            } else {
                // Direktör onayladı veya direktör onayı kapalı
                $ziyaretPlani->estimated_return_date = $ziyaretPlani->return_date_revision_requested_date;
                $ziyaretPlani->return_date_revision_status = 'Onaylandı';
                $eylem = 'Dönüş Tarihi Revizyonu Onaylandı';
                $aciklama = Auth::user()->name . " tarafından dönüş tarihi revizyon talebi onaylandı ve dönüş tarihi güncellendi.";
                $bildirimTipi = 'Dönüş Tarihi Revizyonu Onaylandı';
            }
        } else {
            $ziyaretPlani->return_date_revision_status = 'Reddedildi';
            $eylem = 'Dönüş Tarihi Revizyonu Reddedildi';
            $aciklama = Auth::user()->name . " tarafından dönüş tarihi revizyon talebi reddedildi. Açıklama: " . $this->returnDateRevisionResponseNote;
            $bildirimTipi = 'Dönüş Tarihi Revizyonu Reddedildi';
        }

        $ziyaretPlani->return_date_revision_response = $this->returnDateRevisionResponseNote;
        $ziyaretPlani->save();

        IaaLog::create([
            'iaa_id' => $this->iaa->id,
            'user_id' => Auth::id(),
            'eylem' => $eylem,
            'aciklama' => $aciklama
        ]);

        $this->dispatchVisitWorkflowNotifications($this->iaa, $bildirimTipi, Auth::user()->name, $this->returnDateRevisionResponseNote);

        $this->successMessage = "Revizyon talebi " . ($this->returnDateRevisionAction === 'approve' ? 'onaylandı.' : 'reddedildi.');
        $this->showReturnDateRevisionResponseModal = false;
        $this->loadCustomerData();
    }

    public function closeRejectModal()
    {
        $this->showRejectModal = false;
        $this->rejectionReason = null;
    }

    public function rejectVisit()
    {
        $this->validate([
            'rejectionReason' => 'required|min:10',
        ], [
            'rejectionReason.required' => 'Ret sebebi girmek zorunludur.',
            'rejectionReason.min' => 'Ret sebebi en az 10 karakter olmalıdır.',
        ]);

        $ziyaretPlani = $this->getVisit();

        if ($ziyaretPlani)
        {
            $ziyaretPlani->status = 'Reddedildi';
            $userRole = Auth::user()->roles->first()->name ?? '';

            if ($userRole === 'Direktör')
            {
                $ziyaretPlani->rejection_reason_director = $this->rejectionReason;
            }
            elseif ($userRole === 'Bölüm Kalite Yöneticisi')
            {
                $ziyaretPlani->rejection_reason_quality = $this->rejectionReason;
            }
            else
            {
                $ziyaretPlani->rejection_reason_superadmin = $this->rejectionReason;
            }

            $ziyaretPlani->save();

            IaaLog::create([
                'iaa_id' => $this->iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Ziyaret Planı Reddedildi',
                'aciklama' => Auth::user()->name . " tarafından ziyaret planı reddedildi. Sebep: " . $this->rejectionReason
            ]);

            $this->successMessage = 'Ziyaret planı reddedildi.';

            $this->dispatchVisitWorkflowNotifications($this->iaa, 'Reddedildi', Auth::user()->name, $this->rejectionReason);

            $this->closeRejectModal();
            $this->loadCustomerData();
        }
    }

    public function openRevisionModal()
    {
        $this->showRevisionModal = true;
    }

    public function closeRevisionModal()
    {
        $this->showRevisionModal = false;
        $this->revisionReason = null;
    }

    public function requestRevision()
    {
        $this->validate([
            'revisionReason' => 'required|min:10',
        ], [
            'revisionReason.required' => 'Revizyon talebi girmek zorunludur.',
            'revisionReason.min' => 'Revizyon talebi en az 10 karakter olmalıdır.',
        ]);

        $ziyaretPlani = $this->getVisit();

        if ($ziyaretPlani)
        {
            $ziyaretPlani->status = 'Revize İsteniyor';
            $userRole = Auth::user()->roles->first()->name ?? '';
            if ($userRole === 'Direktör')
            {
                $ziyaretPlani->rejection_reason_director = $this->revisionReason;
            }
            elseif ($userRole === 'Bölüm Kalite Yöneticisi')
            {
                $ziyaretPlani->rejection_reason_quality = $this->revisionReason;
            }
            else
            {
                $ziyaretPlani->rejection_reason_superadmin = $this->revisionReason;
            }
            $ziyaretPlani->save();

            IaaLog::create([
                'iaa_id' => $this->iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Ziyaret Planı Revize İsteniyor',
                'aciklama' => Auth::user()->name . " tarafından ziyaret planı için revizyon istendi. Sebep: " . $this->revisionReason
            ]);

            $this->successMessage = 'Revizyon talebi başarıyla iletildi.';

            $this->dispatchVisitWorkflowNotifications($this->iaa, 'Revize İsteniyor', Auth::user()->name, $this->revisionReason);

            $this->closeRevisionModal();
            $this->loadVisitHistory();
        }
    }

    public function submitRevisedVisit()
    {
        $this->validate([
            'plannerRevisionNote' => 'nullable|string|max:500'
        ]);
        
        $this->isRevisionSubmitModalOpen = true; // bypass modal check
        $this->save();
    }

    public function closeRevisionSubmitModal()
    {
        $this->isRevisionSubmitModalOpen = false;
        $this->plannerRevisionNote = '';
    }

    public function openCancelModal()
    {
        $this->showCancelModal = true;
    }

    public function closeCancelModal()
    {
        $this->showCancelModal = false;
        $this->cancelReason = null;
    }

    public function cancelVisit()
    {
        $this->validate([
            'cancelReason' => 'required|min:10',
        ], [
            'cancelReason.required' => 'İptal gerekçesi girmek zorunludur.',
            'cancelReason.min' => 'İptal gerekçesi en az 10 karakter olmalıdır.',
        ]);

        $ziyaretPlani = $this->getVisit();

        if ($ziyaretPlani && $ziyaretPlani->status === 'Onaylandı')
        {
            $ziyaretPlani->status = 'İptal Edildi';
            $ziyaretPlani->cancel_reason = $this->cancelReason;
            $ziyaretPlani->cancelled_by = Auth::id();
            $ziyaretPlani->cancelled_at = now();
            $ziyaretPlani->save();

            IaaLog::create([
                'iaa_id' => $this->iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Ziyaret Planı İptal Edildi',
                'aciklama' => Auth::user()->name . " tarafından ziyaret planı iptal edildi. Gerekçe: " . $this->cancelReason
            ]);

            $this->successMessage = 'Ziyaret planı iptal edildi.';

            // Bildirim Gönderimi
            try {
                $notifications = [
                    $this->iaa->atananTakim->lider ?? null,
                    $this->iaa->atananTakim->bolumKaliteYoneticisi ?? null,
                    $ziyaretPlani->visitor,
                    $ziyaretPlani->planner
                ];
                
                // Mükerrer kişilere gitmesini engellemek için unique yapıyoruz
                $uniqueRecipients = collect($notifications)->filter()->unique('id');
                
                $notificationClass = \App\Notifications\ZiyaretIptalEdildiBildirimi::class;
                if (class_exists($notificationClass)) {
                    foreach ($uniqueRecipients as $recipient) {
                        $recipient->notify(new $notificationClass($this->iaa, Auth::user()->name, $this->cancelReason));
                    }
                }
            } catch (\Exception $e) {
                Log::error('Visit Cancel notification failed (PlanVisit): ' . $e->getMessage());
            }

            $this->closeCancelModal();
            $this->loadCustomerData();
        }
    }

    public function revertVisit()
    {
        $ziyaretPlani = $this->getVisit();

        if ($ziyaretPlani && $ziyaretPlani->status === 'Beklemede')
        {
            // İlgili bildirimleri veritabanından sil
            \Illuminate\Support\Facades\DB::table('notifications')
                ->where('data', 'like', '%"iaa_id":' . $this->iaa->id . '%')
                ->whereIn('type', [
                    'App\Notifications\ZiyaretPlanlandiBilgilendirme',
                    'App\Notifications\ZiyaretOnayBekliyorBildirimi'
                ])
                ->delete();

            // Ziyaret planını tamamen sil
            $ziyaretPlani->delete();

            // Iaa durumunu güncelle
            $this->iaa->visit_planned = false;
            $this->iaa->save();

            IaaLog::create([
                'iaa_id' => $this->iaa->id,
                'user_id' => Auth::id(),
                'eylem' => 'Ziyaret Planı Geri Alındı',
                'aciklama' => Auth::user()->name . " tarafından 'Beklemede' durumundaki ziyaret planı iptal edilerek veritabanından geri alındı."
            ]);

            $this->successMessage = 'Ziyaret planı geri alındı ve ilgili bildirimler temizlendi.';
            $this->loadCustomerData();
        }
    }

    public function completeVisit()
    {
        // 1. Yerel Veritabanında Tamamla ve Final Verileri Güncelle
        $ziyaretPlani = $this->getVisit();

        if (!$ziyaretPlani)
            return;

        // Sonuç ve Bulguları alıyoruz (formdan)
        $ziyaretPlani->findings = $this->formData['findings'] ?? $ziyaretPlani->findings;
        $ziyaretPlani->result = $this->formData['result'] ?? $ziyaretPlani->result;
        $ziyaretPlani->status = 'Tamamlandı';
        $ziyaretPlani->completed_by = Auth::id();
        $ziyaretPlani->completed_at = now();

        // [YENİ] Çoklu Dosya Yükleme Mantığı
        if (!empty($this->visitFiles))
        {
            $currentFiles = $ziyaretPlani->visit_file ?? [];
            if (!is_array($currentFiles))
                $currentFiles = [];

            foreach ($this->visitFiles as $file)
            {
                $extension = $file->getClientOriginalExtension();
                $originalName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                $filename = now()->format('Ymd_Hi') . '_' . Str::random(5) . '_' . $originalName . '.' . $extension;

                $path = $file->storeAs("proje/{$this->iaa->id}/ziyaret/sonuclar", $filename, 'public');
                $currentFiles[] = $path;
            }
            $ziyaretPlani->visit_file = $currentFiles;
            $this->visitFiles = []; // Reset uploads
        }

        $ziyaretPlani->save();

        $this->isEnteringResults = false;
        $this->iaa->refresh();
        $this->loadCustomerData(); // Veriyi yenile ki buton kaybolsun

        // 2. Takvim Uygulamasına Gerçek Kayıt İşlemi
        try
        {
            $baseUrl = rtrim(config('services.takvim.url', 'http://localhost:8001'), '/');

            $payload = [
                'customer_id' => $this->savedVisit['customer_id'] ?? null,
                'customer_name' => trim($this->iaa->musteriSikayeti->customer->name), // IAA'daki gerçek müşteri ismi
                'business_unit_id' => $ziyaretPlani->business_unit_id,
                'customer_product_id' => $ziyaretPlani->customer_product_id,
                'visit_date' => Carbon::parse($ziyaretPlani->visit_date)->format('Y-m-d\TH:i:sP'),
                'visit_reason' => $ziyaretPlani->visit_reason,
                'visit_notes' => $ziyaretPlani->visit_notes,
                'visitor_id' => $ziyaretPlani->visitor_id === 'Diğer' ? null : $ziyaretPlani->visitor_id,
                'contact_persons' => $ziyaretPlani->contact_persons,
                'barcode' => $ziyaretPlani->barcode,
                'lot_no' => $ziyaretPlani->lot_no,
                'remote_id' => $this->iaa->id,
                'remote_system' => 'iaa',
                'remote_url' => route('proje.workspace.show', $this->iaa->id),
                'findings' => $ziyaretPlani->findings,
                'result' => $ziyaretPlani->result,
                'visitor_name' => $ziyaretPlani->visitor_name,
                'estimated_return_date' => $ziyaretPlani->estimated_return_date
                    ? Carbon::parse($ziyaretPlani->estimated_return_date)->format('Y-m-d\TH:i:sP')
                    : null,
                'visit_files' => collect($ziyaretPlani->visit_file ?? [])
                    ->filter(fn($path) => Storage::disk('public')->exists($path))
                    ->map(fn($path) => [
                        'name' => basename($path),
                        'url' => asset('storage/' . $path),
                    ])->values()->toArray(),
            ];


            Log::info('Takvim API Payload for Project ' . $this->iaa->id . ': ' . json_encode($payload));
            $response = Http::post($baseUrl . '/api/customers/store-visit', $payload);

            Log::info('Takvim API Response for Project ' . $this->iaa->id . ': [' . $response->status() . '] ' . $response->body());

            if ($response->successful())
            {
                $data = $response->json();
                // Alternatif ID alanlarını kontrol et
                $ziyaretPlani->takvim_remote_id = $data['visit_id'] ?? ($data['visit']['id'] ?? null);
                $ziyaretPlani->save();

                IaaLog::create([
                    'iaa_id' => $this->iaa->id,
                    'user_id' => Auth::id(),
                    'eylem' => 'Ziyaret Takvime Eşitlendi',
                    'aciklama' => 'Ziyaret planı tamamlandı ve dış Takvim uygulamasına başarıyla kaydedildi.'
                ]);

                $this->successMessage = 'Ziyaret başarıyla tamamlandı ve Takvim\'e aktarıldı!';
            }
            else
            {
                throw new \Exception($response->body());
            }

        }
        catch (\Exception $e)
        {
            Log::error('Takvim sync failed on completion: ' . $e->getMessage());
            $this->errorMessage = 'Tamamlandı ancak Takvim uygulamasına aktarılamadı: ' . $e->getMessage();
            $ziyaretPlani->save(); // Save local changes regardless
        }

        $this->loadCustomerData();
    }


    public function syncAuthorization()
    {
        $localVisit = $this->getVisit();
        if ($localVisit)
        {
            $user = Auth::user();
            $visitorIdsArray = $localVisit->visitors ? (is_string($localVisit->visitors) ? json_decode($localVisit->visitors, true) : (is_array($localVisit->visitors) ? $localVisit->visitors : [])) : [];
            if (empty($visitorIdsArray) && $localVisit->visitor_id) {
                $visitorIdsArray = [(string) $localVisit->visitor_id];
            }
            $this->isVisitor = in_array((string)$user->id, $visitorIdsArray);

            // Findings are visible if visit was approved or completed
            $this->canSeeFindings = in_array($localVisit->status, ['Onaylandı', 'Tamamlandı']);

            // Special Visibility for "Complete" button:
            // 1. Visitor
            // 2. Superadmin / Management
            // 3. Director
            // 4. Team Leader (Squad Leader)
            $isTeamLeader = $this->iaa->projeEkibi()
                ->where('user_id', $user->id)
                ->where('iaa_user.rol', 'Lider')
                ->exists();

            if (!$isTeamLeader && $this->iaa->atananTakim) {
                $isTeamLeader = ($this->iaa->atananTakim->lider_user_id == $user->id);
            }

            $isSuperAdmin = $user->hasRole('Superadmin');
            $isDirector = $user->hasRole('Direktör');
            $isCozumLideri = $user->hasRole('Müşteri Şikayeti Çözüm Lideri');
            $isQualityManager = $user->hasRole('Bölüm Kalite Yöneticisi');
            $isPlanner = ($localVisit->planner_id == $user->id);

            $this->canCompleteVisit = $this->isVisitor || $isSuperAdmin || $isDirector || $isTeamLeader || $isQualityManager;

            // --- DÜZENLEME YETKİSİ (canEdit) ---
            // 1. Durum Beklemede/Onay Sürecindeyse: Sadece Admin düzenleyebilir.
            // 2. Durum Onaylandıysa: Visitor, Admin, Direktör, Lider düzenleyebilir (Sonuç girmek için)
            // 3. Durum Tamamlandıysa: Sadece Admin düzenleyebilir.
            // 4. Durum Revize İsteniyorsa: Planlayıcı veya Admin düzenleyebilir.

            $status = $localVisit->status;

            if (in_array($status, ['Beklemede', 'Direktör Onayı Bekliyor', 'Yönetim Onayı Bekliyor']))
            {
                $this->canEdit = $isSuperAdmin || $isTeamLeader || $isCozumLideri || $isPlanner; // Onay sürecinde planner, çözüm lideri, admin veya takım lideri düzenleyebilir
            }
            elseif ($status === 'Onaylandı')
            {
                $this->canEdit = $this->canCompleteVisit; // Sonuç girmek için yetkili herkes
            }
            elseif ($status === 'Tamamlandı')
            {
                $this->canEdit = $isSuperAdmin; // Tamamlanmışı sadece admin açar
            }
            elseif ($status === 'Revize İsteniyor')
            {
                $this->canEdit = $isSuperAdmin || $isTeamLeader || $isCozumLideri || $isPlanner; // Çözüm Lideri ve Takım Lideri de revize edebilir
            }
            else
            {
                $this->canEdit = $isSuperAdmin || $isTeamLeader;
            }
        }
        else
        {
            $this->isVisitor = false;
            $this->canSeeFindings = false;
            $this->canCompleteVisit = false;
            $this->canEdit = true; // Ziyaret yoksa form açılabilir
        }
    }

    public function removeTempUpload($index)
    {
        if (isset($this->uploadedTempFiles[$index]))
        {
            unset($this->uploadedTempFiles[$index]);
            $this->uploadedTempFiles = array_values($this->uploadedTempFiles);
        }
    }

    public function deleteVisitFile($index)
    {
        if ($this->isReadOnly)
            return;

        $ziyaretPlani = $this->getVisit();
        if ($ziyaretPlani && isset($ziyaretPlani->visit_file[$index]))
        {
            $files = $ziyaretPlani->visit_file;
            Storage::disk('public')->delete($files[$index]);
            unset($files[$index]);

            $ziyaretPlani->update(['visit_file' => array_values($files)]);

            $this->loadCustomerData(); // Arayüzü yenile
            $this->successMessage = 'Dosya başarıyla silindi.';
        }
    }

    public $canCompleteVisit = false;

    public function toggleCustomerVisibility($field)
    {
        $validFields = [
            'is_visit_notes_visible_to_customer' => 'Ek Notlar',
            'is_visit_file_visible_to_customer' => 'Ekli Dosyalar',
            'is_findings_visible_to_customer' => 'Tespitler / Yapılan İşlemler',
            'is_result_visible_to_customer' => 'Sonuç / Karar',
            'is_return_date_revision_visible_to_customer' => 'Dönüş Tarihi Revizyon Talebi'
        ];

        if (!array_key_exists($field, $validFields)) {
            return;
        }

        // Yetki Kontrolü
        $user = auth()->user();
        if (!$user || $user->is_personnel != 1) {
            $this->errorMessage = 'Bu işlemi yapmaya yetkiniz yok.';
            return;
        }

        $isSuperadmin = $user->hasRole(['Superadmin', 'Super Admin']);
        $isQM = $user->hasRole('Bölüm Kalite Yöneticisi');
        $isLeader = ($this->iaa->atananTakim && $this->iaa->atananTakim->lider_user_id == $user->id);
        
        $isVisitor = false;
        if (isset($this->savedVisit['visitors']) && is_array($this->savedVisit['visitors'])) {
            $isVisitor = in_array($user->id, $this->savedVisit['visitors']);
        } elseif (isset($this->savedVisit['visitor_id']) && $this->savedVisit['visitor_id'] == $user->id) {
            $isVisitor = true;
        }

        if (!$isSuperadmin && !$isQM && !$isLeader && !$isVisitor) {
            $this->errorMessage = 'Bu ayarı değiştirmek için yetkiniz bulunmuyor.';
            return;
        }

        $ziyaretPlani = $this->getVisit();
        if ($ziyaretPlani) {
            $currentVal = $ziyaretPlani->{$field};
            $newVal = !$currentVal;
            $ziyaretPlani->update([$field => $newVal]);

            $this->savedVisit[$field] = $newVal;

            $durumText = $newVal ? 'Açık' : 'Gizli';
            $alanAdi = $validFields[$field];

            IaaLog::create([
                'iaa_id' => $this->iaa->id,
                'user_id' => $user->id,
                'eylem' => 'Ziyaret Görünürlük Değişimi',
                'aciklama' => "Ziyaret raporundaki '$alanAdi' alanının müşteri görünürlüğü {$user->name} tarafından [$durumText] olarak değiştirildi.",
            ]);

            $this->successMessage = "'$alanAdi' alanı müşteri için $durumText hale getirildi.";
        }
    }

    public function render()
    {
        return view('livewire.project.plan-visit');
    }
}
