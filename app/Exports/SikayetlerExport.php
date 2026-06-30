<?php

namespace App\Exports;

use App\Models\MusteriSikayeti;
use App\Models\SikayetKategori;
use App\Models\Setting;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SikayetlerExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithEvents, WithCustomStartCell, WithDrawings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function startCell(): string
    {
        return 'A4'; // Logo için yer açmak adına 4. satırdan başlasın
    }

    public function query()
    {
        $query = MusteriSikayeti::query()->with(['sikayetKategori', 'customer', 'iaaProjesi']);

        if ($this->request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $this->request->start_date);
        }
        if ($this->request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $this->request->end_date);
        }
        if ($this->request->filled('kategori_id')) {
            $query->where('sikayet_kategorisi_id', $this->request->kategori_id);
        }
        if ($this->request->filled('durum')) {
            if ($this->request->durum === 'Kapatıldı') {
                $query->whereIn('musteri_durum', ['Kapatıldı', 'Çözümlendi']);
            } else {
                $query->where('musteri_durum', $this->request->durum);
            }
        }

        return $query->latest();
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $logo = Setting::where('key', 'site_logo')->first();
        
        if ($logo && $logo->value && file_exists(storage_path('app/public/' . $logo->value))) {
            $drawing->setPath(storage_path('app/public/' . $logo->value));
        } else {
            // Logo yoksa boş bir placeholder veya hata vermemesi için kontrol
            return [];
        }

        $drawing->setName('Sistem Logosu');
        $drawing->setDescription('Sistem Logosu');
        $drawing->setHeight(50);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(10);
        $drawing->setOffsetY(10);

        return $drawing;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tarih',
            'Müşteri Adı (Profile Git)',
            'Kategori',
            'Şikayet Konusu (Proje/Detay Link)',
            'Durum',
            'Öncelik',
            'Hedef Tarih',
            'Kapanış Tarihi',
            'Puan'
        ];
    }

    public function map($sikayet): array
    {
        return [
            $sikayet->id,
            $sikayet->created_at->format('d.m.Y H:i'),
            $sikayet->customer ? $sikayet->customer->name : ($sikayet->musteri_adi ?? '-'),
            $sikayet->sikayetKategori ? $sikayet->sikayetKategori->ad : 'Genel',
            $sikayet->musteri_sikayet_konusu,
            $sikayet->musteri_durum,
            $sikayet->musteri_oncelik,
            $sikayet->musteri_cozum_son_tarihi ? \Carbon\Carbon::parse($sikayet->musteri_cozum_son_tarihi)->format('d.m.Y') : '-',
            ($sikayet->kurul_onay_tarihi ?? $sikayet->musteri_onay_tarihi) ? ($sikayet->kurul_onay_tarihi ?? $sikayet->musteri_onay_tarihi)->format('d.m.Y') : '-',
            $sikayet->musteri_puan ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            4 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']],
                'alignment' => ['horizontal' => 'center']
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // 1. ÜST BİLGİLER
                $sheet->mergeCells('C1:J1'); // Başlığı C sütununa kaydırdık çünkü A1'de logo var
                $sheet->setCellValue('C1', 'MÜŞTERİ ŞİKAYETLERİ RAPORU');
                $sheet->getStyle('C1')->getFont()->setSize(18)->setBold(true);
                $sheet->getStyle('C1')->getAlignment()->setHorizontal('center');

                $sheet->mergeCells('C2:E2');
                $sheet->setCellValue('C2', 'Rapor Tarihi: ' . now()->format('d.m.Y H:i'));
                
                $filtreler = [];
                if($this->request->filled('start_date')) $filtreler[] = "Başlangıç: " . $this->request->start_date;
                if($this->request->filled('end_date')) $filtreler[] = "Bitiş: " . $this->request->end_date;
                if($this->request->filled('durum')) $filtreler[] = "Durum: " . $this->request->durum;
                
                $filtreMetni = count($filtreler) > 0 ? "Filtreler: " . implode(' | ', $filtreler) : "Filtre: Tüm Kayıtlar";
                $sheet->mergeCells('F2:J2');
                $sheet->setCellValue('F2', $filtreMetni);
                
                $sheet->getStyle('C2:J2')->getFont()->setBold(true)->setItalic(true);

                // Satır Yüksekliği Ayarı (Logo alanı için)
                $sheet->getRowDimension(1)->setRowHeight(40);

                // 2. SÜTUN GENİŞLİKLERİ
                foreach (range('A', 'J') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }

                // 3. DİNAMİK RENKLENDİRME VE LİNKLEME
                $highestRow = $sheet->getHighestRow();
                for ($row = 5; $row <= $highestRow; $row++) {
                    $id = $sheet->getCell('A' . $row)->getValue();
                    if ($id) {
                        $sikayet = MusteriSikayeti::find($id);
                        if ($sikayet) {
                            // Linkleme
                            if ($sikayet->customer_id) {
                                $sheet->getCell('C' . $row)->getHyperlink()->setUrl(url('/musteri-profil/' . $sikayet->customer_id));
                                $sheet->getStyle('C' . $row)->getFont()->setUnderline(true)->getColor()->setARGB('FF4F46E5');
                            }
                            $url = $sikayet->iaa_id ? url('/proje-calisma-alani/' . $sikayet->iaa_id) : url('/admin/sikayetler/' . $sikayet->id);
                            $sheet->getCell('E' . $row)->getHyperlink()->setUrl($url);
                            $sheet->getStyle('E' . $row)->getFont()->setUnderline(true)->getColor()->setARGB('FF4F46E5');

                            // Renklendirme
                            $color = 'FFFFFF';
                            switch ($sikayet->musteri_durum) {
                                case 'Yeni': $color = 'FEF3C7'; break;
                                case 'İşlemde': $color = 'DBEAFE'; break;
                                case 'Çözümlendi':
                                case 'Kapatıldı': $color = 'DCFCE7'; break;
                                case 'Talep Olarak Kapatıldı':
                                case 'talep_olarak_kapatildi': $color = 'F3E8FF'; break;
                                case 'Hatalı Bildirim Olarak Kapatıldı':
                                case 'hatali_bildirim_olarak_kapatildi': $color = 'FFEDD5'; break;
                            }
                            $sheet->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($color);
                        }
                    }
                }
            },
        ];
    }
}
