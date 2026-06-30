<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Contracts\View\View;

class ProjectExport implements FromView, WithDrawings, WithTitle, ShouldAutoSize, WithStyles
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
                $drawing->setHeight(60); // Biraz daha yüksek bir logo
                $drawing->setCoordinates('A1');
                $drawing->setOffsetX(5);
                $drawing->setOffsetY(5);
                $drawings[] = $drawing;
            }
        }

        return $drawings;
    }

    public function styles(Worksheet $sheet)
    {
        // İlk 4 satırı logo için genişletelim
        $sheet->getRowDimension(1)->setRowHeight(30);
        $sheet->getRowDimension(2)->setRowHeight(30);
        
        return [
            // Başlık Stili
            1 => ['font' => ['bold' => true, 'size' => 16]],
            // Künye Başlıkları
            'A' => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Proje Raporu';
    }
}
