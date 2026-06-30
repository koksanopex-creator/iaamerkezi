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
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    public function query()
    {
        $query = Iaa::query()
            ->whereNotExists(function ($subquery) {
                $subquery->select(DB::raw(1))->from('musteri_sikayetleri')->whereColumn('musteri_sikayetleri.iaa_id', 'iaas.id');
            })
            ->where('durum', '!=', 'talep_olarak_kapatildi')
            ->with(['gonderen', 'bolum', 'atananTakim'])
            ->latest();

        $query->when($this->filters['search'] ?? null, fn($q, $search) => $q->where('baslik', 'like', '%' . $search . '%'));
        
        $query->when($this->filters['durum'] ?? null, function ($q, $durum) {
            if ($durum === 'Talep Alan') return $q->where('durum', 'Havuzda')->has('talepEdenTakimlar');
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

    public function headings(): array
    {
        return ['#', 'Başlık', 'Bölüm / Alan', 'Öneren', 'Öneren Tipi', 'Risk Seviyesi', 'Durum', 'Atanan Takım', 'Puan', 'Oluşturulma Tarihi'];
    }

    public function map($iaa): array
    {
        static $rowNumber = 0;
        $rowNumber++;

        $riskLabel = match($iaa->risk) {
            1 => 'Düşük', 2 => 'Düşük-Orta', 3 => 'Orta', 4 => 'Yüksek', 5 => 'Kritik', default => '-'
        };

        return [
            $rowNumber,
            $iaa->baslik,
            $iaa->bolum ? $iaa->bolum->ad : ($iaa->ilgili_alan ?? 'Genel'),
            $iaa->gonderen->name ?? $iaa->guest_name,
            $iaa->gonderen ? 'Kayıtlı' : 'Misafir',
            $riskLabel,
            $iaa->durum,
            $iaa->atananTakim ? $iaa->atananTakim->ad : '-',
            $iaa->puan ?? '-',
            $iaa->created_at->format('d.m.Y H:i'),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getDelegate()->insertNewRowBefore(1, 4); // 4 Satır boşluk bırakıyoruz

                // TARİH ARALIĞINI HESAPLA
                $tarihBilgisi = "Tüm Zamanlar";
                if (!empty($this->filters['baslangicTarihi']) && !empty($this->filters['bitisTarihi'])) {
                    $bas = Carbon::parse($this->filters['baslangicTarihi'])->format('d.m.Y');
                    $bit = Carbon::parse($this->filters['bitisTarihi'])->format('d.m.Y');
                    $tarihBilgisi = "$bas - $bit Tarihleri Arası";
                } elseif (!empty($this->filters['baslangicTarihi'])) {
                    $bas = Carbon::parse($this->filters['baslangicTarihi'])->format('d.m.Y');
                    $tarihBilgisi = "$bas Tarihinden İtibaren";
                }

                // LOGO
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Köksan Logo');
                if (file_exists(public_path('assets/img/logo.png'))) {
                    $drawing->setPath(public_path('assets/img/logo.png'));
                } elseif (file_exists(public_path('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png'))) {
                    $drawing->setPath(public_path('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png'));
                }
                $drawing->setHeight(55);
                $drawing->setCoordinates('A1');
                $drawing->setWorksheet($event->sheet->getDelegate());

                // BAŞLIKLAR
                $event->sheet->getDelegate()->mergeCells('B2:J2'); 
                $event->sheet->getDelegate()->setCellValue('B2', 'İyileştirmeye Açık Alan (İAA) Raporu');
                $event->sheet->getDelegate()->getStyle('B2')->getFont()->setBold(true)->setSize(16);
                $event->sheet->getDelegate()->getStyle('B2')->getAlignment()->setHorizontal('center');

                // TARİH KAPSAMI (YENİ)
                $event->sheet->getDelegate()->mergeCells('B3:J3');
                $event->sheet->getDelegate()->setCellValue('B3', 'Rapor Kapsamı: ' . $tarihBilgisi);
                $event->sheet->getDelegate()->getStyle('B3')->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF4A5568'));
                $event->sheet->getDelegate()->getStyle('B3')->getAlignment()->setHorizontal('center');

                // OLUŞTURULMA ZAMANI
                $event->sheet->getDelegate()->mergeCells('B4:J4');
                $event->sheet->getDelegate()->setCellValue('B4', 'Oluşturulma: ' . now()->format('d.m.Y H:i') . ' (Sistem tarafından oluşturulmuştur)');
                $event->sheet->getDelegate()->getStyle('B4')->getFont()->setItalic(true)->setSize(9);
                $event->sheet->getDelegate()->getStyle('B4')->getAlignment()->setHorizontal('center');

                // TABLO STİLLERİ
                $headerStyle = ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF4F46E5']]];
                $event->sheet->getDelegate()->getStyle('A5:J5')->applyFromArray($headerStyle); // Başlık artık 5. satırda

                $lastRow = $event->sheet->getDelegate()->getHighestRow();
                $statusColors = ['Reddedildi' => 'FFFFCDD2', 'Tamamlanması Reddedildi' => 'FFFFCDD2', 'Atandı' => 'FFC8E6C9', 'Tamamlandı' => 'FFDCFCE7', 'Onay Bekliyor' => 'FFFFF9C4', 'Havuzda' => 'FFE0E0E0', 'Revize Ediliyor' => 'FFFFE0B2', 'Yönetici Onayı Bekliyor' => 'FFB2EBF2'];

                for ($row = 6; $row <= $lastRow; $row++) {
                    $status = $event->sheet->getDelegate()->getCell('G' . $row)->getValue();
                    if (isset($statusColors[$status])) {
                        $event->sheet->getDelegate()->getStyle('A' . $row . ':J' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($statusColors[$status]);
                    }
                }

                $event->sheet->getDelegate()->getStyle('A5:J' . $lastRow)->applyFromArray(['borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['argb' => 'FFC9C9C9']]]]);
                $event->sheet->getDelegate()->setAutoFilter('A5:J5');
            },
        ];
    }

    public function columnFormats(): array
    {
        return ['I' => NumberFormat::FORMAT_NUMBER_00];
    }
}