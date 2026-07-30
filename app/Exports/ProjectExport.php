<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Contracts\View\View;

class ProjectExport implements FromView, WithDrawings, WithTitle, ShouldAutoSize, WithStyles, WithEvents
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('proje-calisma-alani.export.excel', $this->data);
    }

    public function drawings()
    {
        $drawings = [];
        
        if (!empty($this->data['logo'])) {
            $logoPath = public_path('storage/' . $this->data['logo']);
            if (file_exists($logoPath)) {
                $drawing = new Drawing();
                $drawing->setName('Logo');
                $drawing->setDescription('Company Logo');
                $drawing->setPath($logoPath);
                $drawing->setResizeProportional(false);
                $drawing->setWidth(170); // 4.51 cm width
                $drawing->setHeight(68); // 1.81 cm height
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(10);
                $drawing->setOffsetY(5);
                $drawings[] = $drawing;
            }
        }

        return $drawings;
    }

    public function styles(Worksheet $sheet)
    {
        // A sütunu genişliği 25.18
        $sheet->getColumnDimension('A')->setWidth(25.18);
        
        // İlk 2 satırı logo için genişletelim
        $sheet->getRowDimension(1)->setRowHeight(26);
        $sheet->getRowDimension(2)->setRowHeight(26);
        
        return [
            // Başlık Stili
            1 => ['font' => ['bold' => true, 'size' => 16]],
            // Künye Başlıkları
            'A' => ['font' => ['bold' => true]],
            // Tüm sayfada metni kaydırma (Wrap Text) aktifleştirme (Özellikle birleştirilmiş uzun metin hücreleri için)
            'A:Z' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'Proje Raporu';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();
                $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                for ($row = 1; $row <= $highestRow; ++$row) {
                    for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                        $cell = $sheet->getCellByColumnAndRow($col, $row);
                        $value = $cell->getValue();

                        if (is_string($value) && strpos($value, '[IMG:') !== false) {
                            preg_match_all('/\[IMG:(.*?)\]/', $value, $matches);
                            
                            if (!empty($matches[1])) {
                                $offsetY = 5;
                                foreach ($matches[1] as $imgRelPath) {
                                    $imgPath = public_path('storage/' . trim($imgRelPath));
                                    if (file_exists($imgPath)) {
                                        $drawing = new Drawing();
                                        $drawing->setName('Görsel');
                                        $drawing->setDescription('Görsel');
                                        $drawing->setPath($imgPath);
                                        $drawing->setHeight(130); // Excel'de standart resim boyutu
                                        $drawing->setCoordinates($cell->getCoordinate());
                                        $drawing->setOffsetX(5);
                                        $drawing->setOffsetY($offsetY);
                                        $drawing->setWorksheet($sheet);
                                        
                                        $offsetY += 135; // Bir sonraki resim için aşağı kaydır
                                    }
                                }
                                
                                // Sadece resim etiketlerini temizle
                                $cleanValue = preg_replace('/\[IMG:.*?\]\s*/', '', $value);
                                $cell->setValue(trim($cleanValue));
                                
                                // Satır yüksekliğini resimlere göre ayarla (Excel row height is in points, 1 pixel = 0.75 points)
                                $requiredHeight = $offsetY * 0.75; 
                                $currentRowHeight = $sheet->getRowDimension($row)->getRowHeight();
                                if ($currentRowHeight == -1 || $currentRowHeight < $requiredHeight) {
                                    $sheet->getRowDimension($row)->setRowHeight($requiredHeight);
                                }
                            }
                        }
                    }
                }
            },
        ];
    }
}
