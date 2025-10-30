<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Iaa;
use App\Models\Bolum;
use App\Models\Takim;
use App\Models\MusteriSikayeti;
use Carbon\Carbon; // Tarih işlemleri için eklendi

class DashboardController extends Controller
{
    /**
     * Dashboard ana sayfasını gösterir.
     * Superadmin için özel istatistikler içerir.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->hasRole('Superadmin')) {
            $stats = [
                'toplam_kullanici' => User::count(),
                'onay_bekleyen_kullanici' => User::where('onaylandi_mi', false)->count(),
                'son_kullanicilar' => User::latest()->take(3)->get(),
                
                'toplam_iaa' => Iaa::count(),
                'onay_bekleyen_iaa' => Iaa::where('durum', 'Onay Bekliyor')->count(),
                'atama_bekleyen_iaa' => Iaa::where('durum', 'Talep Edildi')->count(),
                'son_iaalar' => Iaa::latest()->take(3)->get(),

                'toplam_bolum' => Bolum::count(),
                'son_bolumler' => Bolum::latest()->take(3)->get(),
                
                'toplam_takim' => Takim::count(),
                'son_takimlar' => Takim::with('lider')->withCount('uyeler')->latest()->take(3)->get(),
            ];

            return view('dashboard', ['stats' => $stats]);
        }

        return view('dashboard', compact('user'));
    }

    /**
     * Kullanıcıların ve takımların puan sıralamasını gösterir.
     * GÜNCELLENDİ: Superadmin'i liderlik tablosundan çıkarır.
     */
    public function puanDurumu()
    {
        $kullanicilar = User::where('onaylandi_mi', 1)
                            
                            // === YENİ FİLTREYİ EKLE ===
                            // Rolü 'Superadmin' OLMAYAN kullanıcıları al
                            ->whereDoesntHave('roles', function ($query) {
                                $query->where('name', 'Superadmin');
                            })
                            // === EKLEME SONU ===
                            
                            ->orderByDesc('toplam_puan')
                            ->orderBy('name', 'asc') // Puan eşitliği durumunda isme göre sıralar
                            ->get();

        $takimlar = Takim::where('tur', 'iaa')->orderByDesc('toplam_puan')->get();
        
        return view('puan-durumu', compact('kullanicilar', 'takimlar'));
    }

    public function kullaniciPuanlari(User $user)
    {
        // 1. Projelerden kazanılan puanları al
        $takimIdleri = $user->takimlar()->pluck('takim_id');
        
        $projeler = Iaa::where('iaas.durum', 'Tamamlandı')
            ->whereIn('iaas.atanan_takim_id', $takimIdleri)
            ->where('iaas.puan', '>', 0)
            ->with('musteriSikayeti') // <-- İLİŞKİYİ BURADA YÜKLÜYORUZ
            ->get()
            ->map(function ($proje) {
                return [ 
                    'id' => $proje->id,
                    // === ETİKET DÜZELTMESİ ===
                    'tip' => $proje->musteriSikayeti ? 'Müşteri Şikayeti' : 'Proje',
                    'baslik' => $proje->baslik,
                    'tarih' => $proje->onaylanma_tarihi, // 'tamamlanma_tarihi' yerine 'onaylanma_tarihi'
                    'puan' => $proje->puan,
                    'url' => route('proje.workspace.show', $proje->id) // <-- LİNK DÜZELTMESİ
                ];
            })->all();

        // 2. Müşteri şikayetlerinden kazanılan puanları al
        $sikayetler = MusteriSikayeti::where('olusturan_kurul_uyesi_id', $user->id)
            ->where('kazanilan_puan', '>', 0)
            ->with('iaaProjesi') // iaa_id'yi almak için ilişkiyi yükle
            ->get()
            ->map(function ($sikayet) {
                return [
                    'id' => $sikayet->id,
                    'tip' => 'Müşteri Şikayeti',
                    'baslik' => 'Şikayet Kaydı: ' . $sikayet->musteri_sikayet_konusu,
                    'tarih' => $sikayet->created_at,
                    'puan' => $sikayet->kazanilan_puan,
                    // === LİNK DÜZELTMESİ ===
                    // Eğer şikayet projeye dönüştüyse proje linki ver, dönüşmediyse şikayet detay linki ver
                    'url' => $sikayet->iaaProjesi ? route('proje.workspace.show', $sikayet->iaaProjesi->id) : route('admin.sikayetler.show', $sikayet->id)
                ];
            })->all();

        // 3. İki diziyi birleştir ve sırala
        $kazanilanlarArray = array_merge($projeler, $sikayetler);
        
        usort($kazanilanlarArray, function ($a, $b) {
            $tarihA = Carbon::parse($a['tarih']);
            $tarihB = Carbon::parse($b['tarih']);
            return $tarihB <=> $tarihA; // En yeniden en eskiye
        });
        
        $kazanilanlar = collect($kazanilanlarArray);

        return view('kullanici-puanlari', compact('user', 'kazanilanlar'));
    }
}

