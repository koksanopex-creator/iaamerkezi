<?php

namespace App\Exports;

use App\Models\MusteriSikayeti;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AktifSikayetlerExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents, WithCustomStartCell, WithDrawings
{
    protected $request;
    protected $data;
    protected $stats;

    public function __construct($request)
    {
        $this->request = $request;
        $this->prepareData();
    }

    protected function prepareData()
    {
        $query = MusteriSikayeti::query()->with(['sikayetKategori', 'customer', 'iaaProjesi']);

        $user = auth()->user();
        if ($user->hasRole(['Superadmin', 'Müşteri Şikayeti Kurulu', 'Müşteri Şikayeti Kurulu Yöneticisi', 'Yonetim'])) {
            // Hepsi
        } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi']) && $user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
            $query->whereIn('konum_tipi', ['Yurt İçi', 'Yurt Dışı']);
        } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt İçi', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt İçi'])) {
            $query->where('konum_tipi', 'Yurt İçi');
        } elseif ($user->hasRole(['Müşteri Şikayeti Kurulu - Yurt Dışı', 'Müşteri Şikayeti Kurulu Yöneticisi - Yurt Dışı'])) {
            $query->where('konum_tipi', 'Yurt Dışı');
        } else {
            $allowedBolumIds = $user->getAllowedBolumIds();
            $query->whereHas('sikayetKategori', function ($q) use ($allowedBolumIds) {
                if ($allowedBolumIds !== '*') {
                    $q->whereIn('bolum_id', $allowedBolumIds);
                }
            });
        }

        // === TÜM FİLTRELER (Livewire bileşenindeki applyFilters mantığının aynısı) ===
        $r = $this->request;

        // Durum Filtresi
        if ($r->filled('filtreDurum')) {
            $query->whereIn('musteri_durum', (array) $r->filtreDurum);
        } elseif ($r->filled('durum')) {
            // Geriye dönük uyumluluk (eski tek durum parametresi)
            $query->where('musteri_durum', $r->durum);
        }

        // Öncelik
        if ($r->filled('filtreOncelik')) {
            $query->whereIn('musteri_oncelik', (array) $r->filtreOncelik);
        }

        // Takım
        if ($r->filled('filtreTakim')) {
            $query->whereIn('atanan_cozum_takimi_id', (array) $r->filtreTakim);
        }

        // Müşteri Adı
        if ($r->filled('filtreMusteriAdi')) {
            $query->where('musteri_adi', 'like', '%' . $r->filtreMusteriAdi . '%');
        }

        // Konu
        if ($r->filled('filtreKonu')) {
            $query->where('musteri_sikayet_konusu', 'like', '%' . $r->filtreKonu . '%');
        }

        // Ekleyen
        if ($r->filled('filtreEkleyen')) {
            $query->whereIn('olusturan_kurul_uyesi_id', (array) $r->filtreEkleyen);
        }

        // Son Tarih (Hedef Çözüm Tarihi)
        if ($r->filled('filtreSonTarihBaslangic')) {
            $query->whereDate('musteri_cozum_son_tarihi', '>=', $r->filtreSonTarihBaslangic);
        }
        if ($r->filled('filtreSonTarihBitis')) {
            $query->whereDate('musteri_cozum_son_tarihi', '<=', $r->filtreSonTarihBitis);
        }

        // Kayıt Tarihi
        if ($r->filled('filtreKayitTarihBaslangic')) {
            $query->whereDate('created_at', '>=', $r->filtreKayitTarihBaslangic);
        }
        if ($r->filled('filtreKayitTarihBitis')) {
            $query->whereDate('created_at', '<=', $r->filtreKayitTarihBitis);
        }

        // Kategori
        if ($r->filled('filtreKategori')) {
            $query->whereIn('sikayet_kategorisi_id', (array) $r->filtreKategori);
        }

        // Konum Tipi
        if ($r->filled('filtreKonumTipi')) {
            $query->whereIn('konum_tipi', (array) $r->filtreKonumTipi);
        }

        // Puan Aralığı
        if ($r->filled('filtrePuanMin')) {
            $minPuan = filter_var($r->filtrePuanMin, FILTER_VALIDATE_FLOAT);
            if ($minPuan !== false) $query->where('musteri_puan', '>=', $minPuan);
        }
        if ($r->filled('filtrePuanMax')) {
            $maxPuan = filter_var($r->filtrePuanMax, FILTER_VALIDATE_FLOAT);
            if ($maxPuan !== false) $query->where('musteri_puan', '<=', $maxPuan);
        }

        // Proje Durumu
        if ($r->filled('filtreProjeDurumu')) {
            $query->whereHas('iaaProjesi', function ($subQ) use ($r) {
                $subQ->whereIn('durum', (array) $r->filtreProjeDurumu);
            });
        }

        // Bekleme Süresi
        if ($r->filled('filtreBeklemeMin')) {
            $query->where('created_at', '<=', now()->subDays($r->filtreBeklemeMin));
        }
        if ($r->filled('filtreBeklemeMax')) {
            $query->where('created_at', '>=', now()->subDays($r->filtreBeklemeMax));
        }

        // İadeli Filtre
        if ($r->filled('filtreIadeVar') && $r->filtreIadeVar) {
            $query->whereHas('iadeler')
                  ->whereIn('musteri_durum', ['Tamamlandı', 'Çözümlendi', 'Kapatıldı']);
        }

        // Ziyaretli Filtre
        if ($r->filled('filtreZiyaretVar') && $r->filtreZiyaretVar) {
            $query->whereHas('iaaProjesi', function ($subQ) {
                $subQ->where('visit_planned', true);
            });
        }

        // Aktif Sekme Filtresi
        if ($r->filled('activeTab') && $r->activeTab !== 'tumu') {
            $tab = $r->activeTab;
            if ($tab === 'yeni') {
                $query->whereIn('musteri_durum', ['Yeni']);
            } elseif ($tab === 'islemde') {
                $islemdeDurumlar = ['İşlemde', 'İnceleniyor', 'Atandı', 'Devam Ediyor', 'Revize', 'Beklemede',
                    'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor',
                    'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin'];
                $query->where(function ($q) use ($islemdeDurumlar) {
                    $q->whereIn('musteri_durum', $islemdeDurumlar)
                      ->orWhereHas('iaaProjesi', fn($p) => $p->whereIn('durum', $islemdeDurumlar));
                });
            } elseif ($tab === 'cozulmus') {
                $query->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı', 'Tamamlandı'])
                    ->whereDoesntHave('iaaProjesi', fn($q) => $q->whereIn('durum', ['talep_olarak_kapatildi', 'hatali_bildirim_olarak_kapatildi']));
            } elseif ($tab === 'talep_kapali') {
                $query->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'talep_olarak_kapatildi'));
            } elseif ($tab === 'hatali_bildirim') {
                $query->whereHas('iaaProjesi', fn($q) => $q->where('durum', 'hatali_bildirim_olarak_kapatildi'));
            } elseif ($tab === 'onay_bekleyenler') {
                $query->whereHas('iaaProjesi', fn($q) => $q->whereIn('durum', [
                    'Bölüm Onayı Bekliyor', 'Direktör Onayı Bekliyor', 'Yönetici Onayı Bekliyor',
                    'talep_onayi_bekliyor_kalite', 'talep_onayi_bekliyor_direktor', 'talep_onayi_bekliyor_superadmin',
                    'hatali_bildirim_onayi_bekliyor_kalite', 'hatali_bildirim_onayi_bekliyor_direktor', 'hatali_bildirim_onayi_bekliyor_superadmin'
                ]));
            } elseif ($tab === 'iptal') {
                $query->whereIn('musteri_durum', ['İptal Edildi', 'Reddedildi', 'Tamamlanması Reddedildi']);
            }
        }

        $this->data = $query->latest()->get();

        $this->stats = [
            'Tümü' => ['val' => $this->data->count(), 'color' => 'F1F5F9'],
            'Yeni' => ['val' => $this->data->where('musteri_durum', 'Yeni')->count(), 'color' => 'FEF3C7'],
            'İşlemde' => ['val' => $this->data->whereIn('musteri_durum', ['İşlemde', 'Atandı', 'Devam Ediyor'])->count(), 'color' => 'DBEAFE'],
            'Onay Bekleyen' => ['val' => $this->data->filter(fn($s) => str_contains(strtolower($s->iaaProjesi?->durum ?? ''), 'onay'))->count(), 'color' => 'F3E8FF'],
            'Çözülenler' => ['val' => $this->data->whereIn('musteri_durum', ['Çözümlendi', 'Kapatıldı'])->count(), 'color' => 'DCFCE7'],
        ];
    }

    public function collection()
    {
        return $this->data;
    }

    public function startCell(): string
    {
        return 'A6';
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tarih',
            'Bekleme/Süreç Durumu',
            'Müşteri Adı',
            'Kategori',
            'Şikayet Konusu',
            'Şikayet Durumu',
            'Proje Durumu',
            'Hedef Tarih'
        ];
    }

    public function map($sikayet): array
    {
        $durum = $sikayet->musteri_durum;
        $createdAt = Carbon::parse($sikayet->created_at);
        $now = now();
        
        if (in_array($durum, ['Çözümlendi', 'Kapatıldı'])) {
            $finishDate = $sikayet->updated_at ? Carbon::parse($sikayet->updated_at) : $now;
            $diff = ceil(abs($createdAt->diffInDays($finishDate, false)));
            $beklemeMetni = $diff . " Günde Tamamlandı";
        } elseif ($durum == 'Yeni') {
            $diff = ceil(abs($createdAt->diffInDays($now, false)));
            $beklemeMetni = $diff . " Gündür Atama Bekliyor";
        } else {
            $diff = ceil(abs($createdAt->diffInDays($now, false)));
            $beklemeMetni = $diff . " Gündür İşlemde";
        }
        
        $projeDurum = $sikayet->iaaProjesi?->durum ?? '-';
        $mapping = [
            'hatali_bildirim_onayi_bekliyor_direktor' => 'Hatalı Bildirim (Direktör Onayı)',
            'talep_onayi_bekliyor_direktor' => 'Talep Onayı (Direktör)',
            'bolum_onayi_bekliyor' => 'Bölüm Onayı Bekliyor',
            'onay_bekliyor' => 'Onay Bekliyor'
        ];
        $projeDurumLabel = $mapping[strtolower($projeDurum)] ?? $projeDurum;

        return [
            $sikayet->id,
            $createdAt->format('d.m.Y'),
            $beklemeMetni,
            $sikayet->customer ? $sikayet->customer->name : $sikayet->musteri_adi,
            $sikayet->sikayetKategori ? $sikayet->sikayetKategori->ad : 'Genel',
            $sikayet->musteri_sikayet_konusu,
            $sikayet->musteri_durum,
            $projeDurumLabel,
            $sikayet->musteri_cozum_son_tarihi ? Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : '-'
        ];
    }

    public function drawings()
    {
        $logo = Setting::where('key', 'site_logo')->first();
        if ($logo && $logo->value && file_exists(storage_path('app/public/' . $logo->value))) {
            $drawing = new Drawing();
            $drawing->setName('Logo');
            $drawing->setPath(storage_path('app/public/' . $logo->value));
            $drawing->setHeight(55);
            $drawing->setCoordinates('A1');
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(5);
            return $drawing;
        }
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            6 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']]
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                $sheet->mergeCells('C1:I2');
                $sheet->setCellValue('C1', 'AKTİF ŞİKAYET SÜREÇ TAKİP RAPORU');
                $sheet->getStyle('C1')->getFont()->setSize(20)->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('4F46E5'));
                $sheet->getStyle('C1')->getAlignment()->setHorizontal('center')->setVertical('center');

                $sheet->mergeCells('C3:E3');
                $sheet->setCellValue('C3', 'Rapor Tarihi: ' . now()->format('d.m.Y H:i'));
                
                $sheet->mergeCells('F3:I3');
                $sheet->setCellValue('F3', 'Aktif Filtre: ' . ($this->request->durum ?? 'Tümü'));
                
                $sheet->getStyle('C3:I3')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('666666'));
                $sheet->getStyle('F3')->getAlignment()->setHorizontal('right');

                $sheet->setCellValue('A4', 'ÖZET BİLGİ:');
                $sheet->getStyle('A4')->getFont()->setBold(true);
                
                $col = 'B';
                foreach ($this->stats as $label => $data) {
                    $cell = $col . '4';
                    $sheet->setCellValue($cell, $label . ': ' . $data['val']);
                    $sheet->getStyle($cell)->getFont()->setBold(true);
                    $sheet->getStyle($cell)->getAlignment()->setHorizontal('center');
                    $sheet->getStyle($cell)->getFill()->setFillType('solid')->getStartColor()->setRGB($data['color']);
                    $sheet->getStyle($cell)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $col++;
                }

                $highestRow = $sheet->getHighestRow();
                for ($row = 7; $row <= $highestRow; $row++) {
                    $id = $sheet->getCell('A' . $row)->getValue();
                    $sikayet = $this->data->firstWhere('id', $id);
                    
                    if ($sikayet) {
                        // Müşteri Profili Linki (Admin kaldırıldı)
                        if ($sikayet->customer_id) {
                            $sheet->getCell('D' . $row)->getHyperlink()->setUrl(url('/musteri-profil/' . $sikayet->customer_id));
                            $sheet->getStyle('D' . $row)->getFont()->setUnderline(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('2563EB'));
                        }
                        
                        // Proje Çalışma Alanı Linki (Admin kaldırıldı)
                        $link = $sikayet->iaa_id 
                            ? url('/proje-calisma-alani/' . $sikayet->iaa_id)
                            : url('/sikayetler/' . $sikayet->id);
                        
                        $sheet->getCell('F' . $row)->getHyperlink()->setUrl($link);
                        $sheet->getStyle('F' . $row)->getFont()->setUnderline(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('2563EB'));

                        $durum = $sikayet->musteri_durum;
                        $color = 'FFFFFF';
                        if ($durum == 'Yeni') $color = 'FEF3C7';
                        elseif (in_array($durum, ['İşlemde', 'Atandı', 'Devam Ediyor'])) $color = 'DBEAFE';
                        elseif (in_array($durum, ['Çözümlendi', 'Kapatıldı'])) $color = 'DCFCE7';

                        if ($color != 'FFFFFF') {
                            $sheet->getStyle('A' . $row . ':I' . $row)->getFill()->setFillType('solid')->getStartColor()->setRGB($color);
                        }
                    }
                    $sheet->getRowDimension($row)->setRowHeight(20);
                }

                foreach (range('A', 'I') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            },
        ];
    }
}
