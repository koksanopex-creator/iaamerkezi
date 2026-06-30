<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Setting;

class SikayetIadesiExport implements FromView, ShouldAutoSize, WithStyles, WithDrawings, WithEvents
{
    protected $iadeler;
    protected $startDate;
    protected $endDate;
    protected $logoPath;

    public function __construct($iadeler, $startDate, $endDate)
    {
        $this->iadeler = $iadeler;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        
        // Sistem logosu (Ayarlardan çekiyoruz)
        $siteLogo = Setting::get('site_logo');
        if ($siteLogo && file_exists(storage_path('app/public/' . $siteLogo))) {
            $this->logoPath = storage_path('app/public/' . $siteLogo);
        } else {
            // Yedek logo
            $this->logoPath = public_path('logo.svg');
            if (!file_exists($this->logoPath)) {
                $this->logoPath = public_path('favicon.png');
            }
        }
    }

    public function drawings()
    {
        $drawings = [];
        
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Kurumsal Logo');
        $drawing->setPath($this->logoPath);
        $drawing->setHeight(35); // Boyutu küçülttük (Eşşek kadar olmasın diye)
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(5);
        $drawing->setOffsetY(5);
        
        $drawings[] = $drawing;

        return $drawings;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Hücre Birleştirme (A1:B2 Logo, C1:H1 Başlık, C2:H2 Tarih)
                $event->sheet->getDelegate()->mergeCells('A1:B2');
                $event->sheet->getDelegate()->mergeCells('C1:H1');
                $event->sheet->getDelegate()->mergeCells('C2:H2');
                
                // Hizalamalar
                $event->sheet->getDelegate()->getStyle('C1:H2')->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle('C1:H2')->getAlignment()->setVertical('center');
                
                // Sütun Genişlikleri (Kullanıcı talebi: AB: 12, C-H: 10)
                $columns = ['A', 'B'];
                foreach ($columns as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth(12);
                }
                
                $columnsCH = ['C', 'D', 'E', 'F', 'G', 'H'];
                foreach ($columnsCH as $column) {
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth(10);
                }
                
                // Diğer sütunlar (Açıklama vb.) için autofit kalsın
                $event->sheet->getDelegate()->getColumnDimension('I')->setAutoSize(true);
            },
        ];
    }

    public function view(): View
    {
        return view('exports.sikayet-iadesi-excel', [
            'iadeler' => $this->iadeler,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        // Satır Yükseklikleri (Kullanıcı talebi: 18)
        $sheet->getDefaultRowDimension()->setRowHeight(18);

        return [
            1 => ['font' => ['bold' => true, 'size' => 14]], // Başlık (Kullanıcı talebi: 14)
            2 => ['font' => ['italic' => true, 'size' => 10]], // Tarih
            3 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '1a1f2e']]],
        ];
    }
}
