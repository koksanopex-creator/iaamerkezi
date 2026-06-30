<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Services\SikayetAnalizService;
use App\Models\ReportRoleAuthorization;
use App\Models\ReportUserAuthorization;
use App\Models\Bolum;
use App\Models\User;

class MusteriSikayetAnalizRaporu extends Component
{
    use WithPagination;

    // Filtre değişkenleri (Multi-select için array olarak tanımlıyoruz)
    #[Url(except: [])]
    public array $bolumId = [];
    #[Url(except: '')]
    public $startDate = '';
    #[Url(except: '')]
    public $endDate = '';
    #[Url(except: 'created_at')]
    public $tarihAlani = 'created_at';
    #[Url(except: [])]
    public array $durum = [];
    #[Url(except: [])]
    public array $oncelik = [];
    #[Url(except: [])]
    public array $customerId = [];
    #[Url(except: [])]
    public array $konumTipi = [];
    #[Url(except: [])]
    public array $altKategoriId = [];
    #[Url(except: [])]
    public array $takimId = [];
    #[Url(except: [])]
    public array $squadUserId = [];

    // Load More (Daha Fazla Göster) için
    public $perPage = 5;

    // === YETKİ MATRİSİ MODAL STATE ===
    public bool $showYetkiModal = false;
    public string $yetkiReportName = 'analiz_raporu'; // Hangi rapor türü için yetki veriliyor
    // Rol seçimi (kullanıcı listeleme filtresi olarak)
    public $yetkiFilterRole = '';
    // Kulınıcı bazlı yetki ekleme/düzenleme
    public $yetkiEditUserId = null;
    public $yetkiDataScope = 'own_department';
    public $yetkiSpecificDeptIds = [];
    // Toplu ekleme için seçili kullanıcı ID'leri
    public array $yetkiSelectedUserIds = [];
    public $yetkiBulkDataScope = 'own_department';
    public $yetkiBulkSpecificDeptIds = [];

    // Sayfalama sıfırlama
    protected $queryString = [
        'bolumId'    => ['except' => []],
        'startDate'  => ['except' => ''],
        'endDate'    => ['except' => ''],
        'tarihAlani' => ['except' => 'created_at'],
        'durum'      => ['except' => []],
        'oncelik'    => ['except' => []],
        'customerId' => ['except' => []],
        'konumTipi'  => ['except' => []],
        'altKategoriId' => ['except' => []],
        'takimId' => ['except' => []],
        'squadUserId' => ['except' => []],
    ];

    public function loadMore()
    {
        $this->perPage += 5;
    }

    public function hideMore()
    {
        $this->perPage = 5;
    }

    private function resetLoadMore()
    {
        $this->perPage = 5;
    }

    public function updatedBolumId()    { $this->resetLoadMore(); }
    public function updatedStartDate()  { $this->resetLoadMore(); }
    public function updatedEndDate()    { $this->resetLoadMore(); }
    public function updatedTarihAlani() { $this->resetLoadMore(); }
    public function updatedDurum()      { $this->resetLoadMore(); }
    public function updatedOncelik()    { $this->resetLoadMore(); }
    public function updatedCustomerId() { $this->resetLoadMore(); }
    public function updatedKonumTipi()  { $this->resetLoadMore(); }
    public function updatedAltKategoriId() { $this->resetLoadMore(); }
    public function updatedTakimId()    { $this->resetLoadMore(); }
    public function updatedSquadUserId() { $this->resetLoadMore(); }

    public function clearFilters()
    {
        $this->bolumId = [];
        $this->startDate = '';
        $this->endDate = '';
        $this->tarihAlani = 'created_at';
        $this->durum = [];
        $this->oncelik = [];
        $this->customerId = [];
        $this->konumTipi = [];
        $this->altKategoriId = [];
        $this->takimId = [];
        $this->squadUserId = [];
        $this->resetLoadMore();
    }

    public function getActiveFilterInfo()
    {
        $aktifFiltreler = [];
        if (!empty($this->bolumId)) $aktifFiltreler[] = "<span class='inline-block bg-yellow-100 text-yellow-800 px-2 py-1 rounded-md text-xs font-bold border border-yellow-200'>" . count($this->bolumId) . " Bölüm</span>";
        if (!empty($this->durum)) $aktifFiltreler[] = "<span class='inline-block bg-blue-100 text-blue-800 px-2 py-1 rounded-md text-xs font-bold border border-blue-200'>" . count($this->durum) . " Durum</span>";
        if (!empty($this->oncelik)) $aktifFiltreler[] = "<span class='inline-block bg-pink-100 text-pink-800 px-2 py-1 rounded-md text-xs font-bold border border-pink-200'>" . count($this->oncelik) . " Öncelik</span>";
        if (!empty($this->altKategoriId)) $aktifFiltreler[] = "<span class='inline-block bg-emerald-100 text-emerald-800 px-2 py-1 rounded-md text-xs font-bold border border-emerald-200'>" . count($this->altKategoriId) . " Alt Kategori</span>";
        if (!empty($this->squadUserId)) $aktifFiltreler[] = "<span class='inline-block bg-purple-100 text-purple-800 px-2 py-1 rounded-md text-xs font-bold border border-purple-200'>" . count($this->squadUserId) . " Personel</span>";
        if (!empty($this->takimId)) $aktifFiltreler[] = "<span class='inline-block bg-cyan-100 text-cyan-800 px-2 py-1 rounded-md text-xs font-bold border border-cyan-200'>" . count($this->takimId) . " Çözüm Takımı</span>";
        if ($this->startDate || $this->endDate) $aktifFiltreler[] = "<span class='inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded-md text-xs font-bold border border-gray-200'>Tarih: " . ($this->startDate ?: 'Başı') . " - " . ($this->endDate ?: 'Sonu') . "</span>";

        if (count($aktifFiltreler) > 0) {
            return "<div class='flex items-center gap-2 flex-wrap'><span class='text-[11px] font-black text-gray-500 uppercase tracking-wider'>Aktif Filtreler:</span> " . implode(' ', $aktifFiltreler) . "</div>";
        } else {
            return "<div class='text-[13px] text-gray-500 font-medium'>Sistemdeki tüm veriler (Tüm Zamanlar, Tüm Durumlar) üzerinden analiz yapılmaktadır.</div>";
        }
    }

    public function getActiveFilterText()
    {
        $aktifFiltreler = [];

        if (!empty($this->bolumId)) {
            $bolumler = \App\Models\Bolum::whereIn('id', $this->bolumId)->pluck('ad')->toArray();
            $aktifFiltreler[] = "Bölüm: " . implode(', ', $bolumler);
        }
        if (!empty($this->durum)) {
            $durumLabels = [
                'yeni' => 'Yeni', 'islemde' => 'İşlemde', 'bolum_onay' => 'Bölüm Onayı',
                'direktor_onay' => 'Direktör Onay', 'final_onay' => 'Final Onay',
                'kapali' => 'Kapatıldı', 'iade' => 'İade'
            ];
            $seciliDurumlar = array_map(fn($d) => $durumLabels[$d] ?? $d, $this->durum);
            $aktifFiltreler[] = "Durum: " . implode(', ', $seciliDurumlar);
        }
        if (!empty($this->oncelik)) {
            $aktifFiltreler[] = "Öncelik: " . implode(', ', $this->oncelik);
        }
        if (!empty($this->altKategoriId)) {
            $altKat = \App\Models\SikayetAltKategori::whereIn('id', $this->altKategoriId)->pluck('ad')->toArray();
            $aktifFiltreler[] = "Alt Kat.: " . implode(', ', $altKat);
        }
        if (!empty($this->squadUserId)) {
            $personel = \App\Models\User::whereIn('id', $this->squadUserId)->pluck('name')->toArray();
            $aktifFiltreler[] = "Personel: " . implode(', ', $personel);
        }
        if (!empty($this->takimId)) {
            $takimlar = \App\Models\Takim::whereIn('id', $this->takimId)->pluck('ad')->toArray();
            $aktifFiltreler[] = "Takım: " . implode(', ', $takimlar);
        }
        if (!empty($this->customerId)) {
            $musteriler = \App\Models\Customer::whereIn('id', $this->customerId)->pluck('name')->toArray();
            $aktifFiltreler[] = "Müşteri: " . implode(', ', $musteriler);
        }
        if (!empty($this->konumTipi)) {
            $aktifFiltreler[] = "Konum: " . implode(', ', $this->konumTipi);
        }
        if ($this->startDate || $this->endDate) {
            $aktifFiltreler[] = "Tarih: " . ($this->startDate ?: 'Başı') . " - " . ($this->endDate ?: 'Sonu');
        }

        if (count($aktifFiltreler) > 0) {
            return implode(' | ', $aktifFiltreler);
        }
        return "Tüm Veriler";
    }

    private function getService(): SikayetAnalizService
    {
        $service = (new SikayetAnalizService())->setFilters([
            'bolumId'    => $this->bolumId ?: null,
            'startDate'  => $this->startDate ?: null,
            'endDate'    => $this->endDate ?: null,
            'tarihAlani' => $this->tarihAlani,
            'durum'      => $this->durum ?: null,
            'oncelik'    => $this->oncelik ?: null,
            'customerId' => $this->customerId ?: null,
            'konumTipi'  => $this->konumTipi ?: null,
            'altKategoriId' => $this->altKategoriId ?: null,
            'takimId'    => $this->takimId ?: null,
            'squadUserId' => $this->squadUserId ?: null,
        ]);

        // Yetki matrisinden gelen bölüm kısıtlamasını uygula
        $allowedBolumIds = ReportRoleAuthorization::getAllowedBolumIdsForUser(auth()->user());
        $service->setHardBolumFilter($allowedBolumIds);

        return $service;
    }

    // === YETKİ MATRİSİ CRUD METODLARI ===

    public function openYetkiModal()
    {
        $this->showYetkiModal = true;
        $this->resetYetkiForm();
    }

    public function closeYetkiModal()
    {
        $this->showYetkiModal = false;
        $this->resetYetkiForm();
    }

    private function resetYetkiForm()
    {
        $this->yetkiFilterRole = '';
        $this->yetkiEditUserId = null;
        $this->yetkiDataScope = 'own_department';
        $this->yetkiSpecificDeptIds = [];
        $this->yetkiSelectedUserIds = [];
        $this->yetkiBulkDataScope = 'own_department';
        $this->yetkiBulkSpecificDeptIds = [];
    }

    /**
     * Tek kullanıcı düzenleme moduna geç
     */
    public function editUserYetki($userId)
    {
        $auth = ReportUserAuthorization::where('user_id', $userId)
            ->where('report_name', $this->yetkiReportName)
            ->first();
        if ($auth) {
            $this->yetkiEditUserId = $userId;
            $this->yetkiDataScope = $auth->data_scope;
            $this->yetkiSpecificDeptIds = $auth->specific_department_ids ?? [];
        }
    }

    /**
     * Tek kullanıcı yetkisini güncelle
     */
    public function updateUserYetki()
    {
        if (!$this->yetkiEditUserId) return;

        ReportUserAuthorization::updateOrCreate(
            [
                'user_id' => $this->yetkiEditUserId,
                'report_name' => $this->yetkiReportName
            ],
            [
                'data_scope' => $this->yetkiDataScope,
                'specific_department_ids' => $this->yetkiDataScope === 'specific_departments'
                    ? array_map('intval', $this->yetkiSpecificDeptIds)
                    : null,
            ]
        );

        // Bildirim gönder
        $user = User::find($this->yetkiEditUserId);
        if ($user) {
            $user->notify(new \App\Notifications\AnalizRaporuYetkiVerildiNotification($this->yetkiDataScope, auth()->user()->name));
        }

        $this->yetkiEditUserId = null;
        session()->flash('yetkiSuccess', 'Kullanıcı yetkisi güncellendi ve bildirim gönderildi.');
    }

    /**
     * Seçili kullanıcıları toplu olarak ekle
     */
    public function saveSelectedUsers()
    {
        if (empty($this->yetkiSelectedUserIds)) {
            session()->flash('yetkiError', 'Lütfen en az bir kullanıcı seçin.');
            return;
        }

        $specificDeptIds = $this->yetkiBulkDataScope === 'specific_departments'
            ? array_map('intval', $this->yetkiBulkSpecificDeptIds)
            : null;

        $usersToNotify = User::whereIn('id', $this->yetkiSelectedUserIds)->get();

        foreach ($this->yetkiSelectedUserIds as $userId) {
            ReportUserAuthorization::updateOrCreate(
                [
                    'user_id' => (int) $userId,
                    'report_name' => $this->yetkiReportName
                ],
                [
                    'data_scope' => $this->yetkiBulkDataScope,
                    'specific_department_ids' => $specificDeptIds,
                ]
            );
        }

        // Bildirim gönder
        foreach ($usersToNotify as $user) {
            $user->notify(new \App\Notifications\AnalizRaporuYetkiVerildiNotification($this->yetkiBulkDataScope, auth()->user()->name));
        }

        $count = count($this->yetkiSelectedUserIds);
        $this->yetkiSelectedUserIds = [];
        session()->flash('yetkiSuccess', "{$count} kullanıcıya yetki verildi ve bildirimler gönderildi.");
    }

    /**
     * Tek kullanıcının yetkisini kaldır
     */
    public function deleteUserYetki($userId)
    {
        ReportUserAuthorization::where('user_id', $userId)
            ->where('report_name', $this->yetkiReportName)
            ->delete();

        // Bildirim gönder
        $user = User::find($userId);
        if ($user) {
            $user->notify(new \App\Notifications\AnalizRaporuYetkiKaldirildiNotification(auth()->user()->name));
        }

        session()->flash('yetkiSuccess', 'Kullanıcı yetkisi kaldırıldı ve bildirim gönderildi.');
    }

    public function render()
    {
        $service = $this->getService();
        $allowedBolumIds = ReportRoleAuthorization::getAllowedBolumIdsForUser(auth()->user(), 'analiz_raporu');
        $filterData = SikayetAnalizService::getFilterData($allowedBolumIds);

        $kpi                = $service->getKpiData();
        $aylikTrend         = $service->getAylikTrend();
        $durumDagilimi      = $service->getDurumDagilimi();
        $bolumDagilimi      = $service->getBolumDagilimi();
        $oncelikDagilimi    = $service->getOncelikDagilimi();
        $kategoriDagilimi   = $service->getKategoriDagilimi();
        $altKategoriDagilimi = $service->getAltKategoriDagilimi();
        $konumTipiDagilimi  = $service->getKonumTipiDagilimi();
        $musteriTop10       = $service->getMusteriTop10();
        $cozumSuresiTrend   = $service->getCozumSuresiTrend();
        $squadPersonelDagilimi = $service->getSquadPersonelDagilimi();
        $paretoAnalizi      = $service->getParetoAnalizi();
        $darBogazAnalizi    = $service->getDarBogazAnalizi();
        $bolumKategoriHeatmap = $service->getBolumKategoriHeatmap();
        $detayTablosu       = $service->getDetayTablosu($this->perPage);

        $this->dispatch('updateAnalizGrafikleri', [
            'aylikTrend'         => $aylikTrend,
            'durumDagilimi'      => $durumDagilimi,
            'bolumDagilimi'      => $bolumDagilimi,
            'oncelikDagilimi'    => $oncelikDagilimi,
            'kategoriDagilimi'   => $kategoriDagilimi,
            'altKategoriDagilimi' => $altKategoriDagilimi,
            'konumTipiDagilimi'  => $konumTipiDagilimi,
            'musteriTop10'       => $musteriTop10,
            'cozumSuresiTrend'   => $cozumSuresiTrend,
            'squadPersonelDagilimi' => $squadPersonelDagilimi,
            'paretoAnalizi'      => $paretoAnalizi,
            'darBogazAnalizi'    => $darBogazAnalizi,
            'bolumKategoriHeatmap' => $bolumKategoriHeatmap,
        ]);

        // Yetki matrisi verileri (sadece Superadmin görür)
        $yetkiliKullanicilar = collect();
        $filtrelenmisKullanicilar = collect();
        $tumRoller = [];
        $tumBolumler = collect();
        $isSuperadmin = auth()->user()->hasRole('Superadmin');

        if ($isSuperadmin) {
            // Mevcut yetkili kullanıcılar
            $yetkiliKullanicilar = ReportUserAuthorization::with('user.bolum', 'user.roles')
                ->where('report_name', $this->yetkiReportName)
                ->orderBy('created_at', 'desc')
                ->get();

            // Rol ile filtrelenmiş kullanıcılar (ekleme paneli için)
            if ($this->yetkiFilterRole) {
                $mevcutUserIds = $yetkiliKullanicilar->pluck('user_id')->toArray();
                $filtrelenmisKullanicilar = User::role($this->yetkiFilterRole)
                    ->where('is_personnel', true)
                    ->whereNotIn('id', $mevcutUserIds)
                    ->with('bolum')
                    ->orderBy('name')
                    ->get();
            }

            $tumRoller = \Spatie\Permission\Models\Role::orderBy('name')->pluck('name')->toArray();
            // Sadece şikayet kategorisi olan (üretim yapan vs.) bölümler gelsin
            $tumBolumler = Bolum::whereHas('sikayetKategorileri')->orderBy('ad')->get(['id', 'ad']);
        }

        return view('livewire.admin.musteri-sikayet-analiz-raporu', compact(
            'kpi',
            'aylikTrend',
            'durumDagilimi',
            'bolumDagilimi',
            'oncelikDagilimi',
            'kategoriDagilimi',
            'altKategoriDagilimi',
            'konumTipiDagilimi',
            'musteriTop10',
            'cozumSuresiTrend',
            'squadPersonelDagilimi',
            'paretoAnalizi',
            'darBogazAnalizi',
            'bolumKategoriHeatmap',
            'detayTablosu',
            'filterData',
            'yetkiliKullanicilar',
            'filtrelenmisKullanicilar',
            'tumRoller',
            'tumBolumler',
            'isSuperadmin'
        ), [
            'activeFilterInfo' => $this->getActiveFilterInfo()
        ]);
    }
}
