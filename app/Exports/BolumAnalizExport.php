<?php
 
namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BolumAnalizExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('dashboard.exports.bolum-analiz-excel', [
            'bolumPuanListesi' => $this->data['bolumPuanListesi'],
            'grossTotal'       => $this->data['grossTotal'],
            'penaltyTotal'     => $this->data['penaltyTotal'],
            'netTotal'         => $this->data['netTotal'],
            'breakdown'        => $this->data['breakdown']
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Başlık satırları için kalın font
            1 => ['font' => ['bold' => true, 'size' => 14]],
            2 => ['font' => ['bold' => true]],
        ];
    }
}
