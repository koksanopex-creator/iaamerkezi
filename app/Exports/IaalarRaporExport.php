<?php

namespace App\Exports;

use App\Models\Iaa;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class IaalarRaporExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    // Filtreleri Controller'dan almak için constructor
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    // Sütun başlıklarını tanımlar
    public function headings(): array
    {
        return [
            'ID',
            'Başlık',
            'Durum',
            'Öneren',
            'Bölüm/Alan',
            'Oluşturulma Tarihi',
            'Puan',
        ];
    }

    // Her bir satırın verisini eşler
    public function map($iaa): array
    {
        return [
            $iaa->id,
            $iaa->baslik,
            $iaa->durum,
            $iaa->gonderen->name ?? $iaa->guest_name,
            $iaa->bolum->ad ?? $iaa->ilgili_alan,
            $iaa->created_at->format('d.m.Y'),
            $iaa->puan ? number_format($iaa->puan, 2) : 'N/A',
        ];
    }

    // Filtrelenmiş veriyi çeken sorgu
    public function query()
    {
        $query = Iaa::query()->with(['gonderen', 'bolum']);

        $query->when($this->filters['search'] ?? null, fn($q, $search) => $q->where('baslik', 'like', '%' . $search . '%'));
        $query->when($this->filters['durum'] ?? null, fn($q, $durum) => $durum === 'Talep Alan' ? $q->where('durum', 'Havuzda')->whereHas('talepEdenTakimlar') : $q->where('durum', 'like', '%' . $durum . '%'));
        $query->when($this->filters['kullaniciTipi'] ?? null, function ($q, $kullaniciTipi) {
            if ($kullaniciTipi === 'kayitli') return $q->whereNotNull('gonderen_user_id');
            if ($kullaniciTipi === 'misafir') return $q->whereNull('gonderen_user_id');
        });
        $query->when($this->filters['baslangicTarihi'] ?? null, fn($q, $tarih) => $q->whereDate('created_at', '>=', $tarih));
        $query->when($this->filters['bitisTarihi'] ?? null, fn($q, $tarih) => $q->whereDate('created_at', '<=', $tarih));

        return $query;
    }
}