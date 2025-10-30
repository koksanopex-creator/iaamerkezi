<?php

namespace App\Exports;

use App\Models\Iaa;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class ProfesyonelIaalarExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize, WithTitle, WithEvents, WithColumnFormatting
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function title(): string
    {
        return 'İAA Raporu';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // 1. Sayfanın en üstüne başlık alanı için 4 yeni satır ekle
                $event->sheet->getDelegate()->insertNewRowBefore(1, 3);

                // 2. Logoyu, satırlar eklendikten SONRA doğru yere yerleştir.
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Köksan Logo');
                $drawing->setPath(public_path('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png'));
                $drawing->setHeight(55);
                $drawing->setCoordinates('A1');
                $drawing->setWorksheet($event->sheet->getDelegate());

                // 3. Rapor Ana Başlığı
                $event->sheet->getDelegate()->mergeCells('B2:H2');
                $event->sheet->getDelegate()->setCellValue('B2', 'İyileştirmeye Açık Alan (İAA) Raporu');
                $event->sheet->getDelegate()->getStyle('B2')->getFont()->setBold(true)->setSize(16);
                $event->sheet->getDelegate()->getStyle('B2')->getAlignment()->setHorizontal('center');

                // 4. Rapor Tarihi
                $event->sheet->getDelegate()->mergeCells('B3:H3');
                $event->sheet->getDelegate()->setCellValue('B3', 'Oluşturulma Tarihi: ' . now()->format('d.m.Y H:i'));
                $event->sheet->getDelegate()->getStyle('B3')->getFont()->setItalic(true);
                $event->sheet->getDelegate()->getStyle('B3')->getAlignment()->setHorizontal('center');

                // 5. Sütun Başlıkları Satırını Stilize Etme
                $headerStyle = [
                    'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                    'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']]
                ];
                $event->sheet->getDelegate()->getStyle('A4:H4')->applyFromArray($headerStyle);

                // 6. Satırları Duruma Göre Renklendirme
                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                $statusColors = [
                    'Reddedildi'  => 'FFFFCDD2', // Açık Kırmızı
                    'Atandı'      => 'FFC8E6C9', // Açık Yeşil
                    'Onay Bekliyor' => 'FFFFF9C4', // Açık Sarı
                    'Havuzda'     => 'FFE0E0E0', // Açık Gri
                ];
                for ($row = 5; $row <= $lastRow; $row++) {
                    $status = $event->sheet->getDelegate()->getCell('C' . $row)->getValue();
                    if (isset($statusColors[$status])) {
                        $event->sheet->getDelegate()->getStyle('A' . $row . ':H' . $row)->getFill()
                            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                            ->getStartColor()->setARGB($statusColors[$status]);
                    }
                }

                // 7. YENİ: Kenarlık Ekleme
                $cellRange = 'A4:H' . $lastRow; // Veri aralığı (başlıklardan son satıra kadar)
                $borderStyle = [
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => 'FFC9C9C9'],
                        ],
                    ],
                ];
                $event->sheet->getDelegate()->getStyle($cellRange)->applyFromArray($borderStyle);

                // 8. YENİ: Otomatik Filtre Ekleme
                $event->sheet->getDelegate()->setAutoFilter('A4:H4');
            },
        ];
    }

    public function headings(): array
    {
        return [
            '#',
            'Başlık',
            'Durum',
            'Öneren',
            'Öneren Tipi',
            'Bölüm / Alan',
            'Oluşturulma Tarihi',
            'Puan',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }
    
    public function map($iaa): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $iaa->baslik,
            $iaa->durum,
            $iaa->gonderen->name ?? $iaa->guest_name,
            $iaa->gonderen ? 'Kayıtlı' : 'Misafir',
            $iaa->bolum->ad ?? $iaa->ilgili_alan,
            $iaa->created_at->format('d.m.Y'),
            $iaa->puan,
        ];
    }

    public function query()
    {
        $query = Iaa::query()->with(['gonderen', 'bolum'])->latest();

        $query->when($this->filters['search'] ?? null, fn($q, $search) => $q->where('baslik', 'like', '%' . $search . '%'));
        
        $query->when($this->filters['durum'] ?? null, function ($q, $durum) {
            if ($durum === 'Talep Alan') {
                return $q->where('durum', 'Havuzda')->whereHas('talepEdenTakimlar');
            }
            return $q->where('durum', $durum);
        });

        $query->when($this->filters['kullaniciTipi'] ?? null, function ($q, $kullaniciTipi) {
            if ($kullaniciTipi === 'kayitli') return $q->whereNotNull('gonderen_user_id');
            if ($kullaniciTipi === 'misafir') return $q->whereNull('gonderen_user_id');
        });

        $query->when($this->filters['baslangicTarihi'] ?? null, fn($q, $tarih) => $q->whereDate('created_at', '>=', $tarih));
        $query->when($this->filters['bitisTarihi'] ?? null, fn($q, $tarih) => $q->whereDate('created_at', '<=', $tarih));
        
        return $query;
    }
}