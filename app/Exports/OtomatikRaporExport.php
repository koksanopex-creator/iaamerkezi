<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OtomatikRaporExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $data;
    protected $baslik;
    protected $tarih;

    public function __construct($data, $baslik, $tarih)
    {
        $this->data = $data;
        $this->baslik = $baslik;
        $this->tarih = $tarih;
    }

    public function view(): View
    {
        // Aynı blade dosyasını kullanıyoruz ama Excel'de CSS'ler farklı işler.
        // Genelde Excel için ayrı sade bir blade yapmak iyidir ama
        // bu blade de tablo yapısında olduğu için Excel'de çalışacaktır.
        return view('emails.raporlar.otomatik-ozet', [
            'raporData' => $this->data,
            'raporBasligi' => $this->baslik,
            'tarih' => $this->tarih
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // İlk satırı (Başlık) kalın yap
            1 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }
}