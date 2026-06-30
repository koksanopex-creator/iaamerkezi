<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class BolumDetayExport implements FromView, ShouldAutoSize, WithTitle, WithStyles, WithEvents
{
    protected $bolum;
    protected $users;
    protected $totalPuan;
    protected $breakdown;
    protected $startDate;
    protected $endDate;
    protected $grossTotal;
    protected $penaltyTotal;
    protected $netTotal;

    public function __construct(
        $bolum, 
        $users, 
        $totalPuan, 
        $breakdown, 
        $startDate = null, 
        $endDate = null,
        $grossTotal = 0,
        $penaltyTotal = 0,
        $netTotal = 0
    )
    {
        $this->bolum = $bolum;
        $this->users = $users;
        $this->totalPuan = $totalPuan;
        $this->breakdown = $breakdown;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->grossTotal = $grossTotal;
        $this->penaltyTotal = $penaltyTotal;
        $this->netTotal = $netTotal;
    }

    public function view(): View
    {
        return view('dashboard.exports.bolum-detay-excel', [
            'bolum' => $this->bolum,
            'users' => $this->users,
            'totalBolumPuan' => $this->totalPuan,
            'breakdown' => $this->breakdown,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'grossTotal' => $this->grossTotal,
            'penaltyTotal' => $this->penaltyTotal,
            'netTotal' => $this->netTotal
        ]);
    }

    public function title(): string
    {
        return $this->bolum->ad . ' Analizi';
    }

    public function styles(Worksheet $sheet)
    {
        // Sabit sütun genişliklerini ShouldAutoSize ile birleştirelim
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setWidth(35);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);
        $sheet->getColumnDimension('E')->setWidth(18);

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                'alignment' => ['horizontal' => 'center'],
            ],
            4 => ['font' => ['bold' => true]], // Özet başlıkları
            5 => ['font' => ['bold' => true]], // Özet değerleri
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Tüm hücreler için dikey hizalama
                $event->sheet->getDelegate()->getStyle('A1:E200')->getAlignment()->setVertical('center');
                
                // Başlık satırının rengini ayarla (Bölüm Analiz Raporu)
                $event->sheet->getDelegate()->getStyle('A1:E1')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('4F46E5');
            },
        ];
    }
}
