<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Iaa;
use App\Models\Bolum;
use App\Models\Takim;
use App\Models\MusteriSikayeti;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard ana sayfasını gösterir.
     * Artık role göre farklı istatistikler içerir.
     */
    public function index()
    {
        $user = Auth::user();
        $stats = []; // İstatistikleri tutacak boş bir dizi
        
        // View'e gönderilecek ekstra değişkenler
        $bolumOnayiBekleyenSayisi = 0; 

        if ($user->hasRole('Superadmin')) {
            // === 1. SUPERADMIN İSTATİSTİKLERİ ===
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
                
                'toplam_sikayet' => MusteriSikayeti::count(),
                'yeni_sikayet' => MusteriSikayeti::where('musteri_durum', 'Yeni')->count(),
                'islemde_sikayet' => MusteriSikayeti::where('musteri_durum', 'İşlemde')->count(),
                'son_sikayetler' => MusteriSikayeti::latest()->take(3)->get(),
            ];
            
            // Superadmin de genel durumu görebilsin
            $bolumOnayiBekleyenSayisi = Iaa::where('durum', 'Bölüm Onayı Bekliyor')->count();

        } elseif ($user->hasRole('Müşteri Şikayeti Kurulu')) {
            // === 2. MÜŞTERİ ŞİKAYETİ KURULU İSTATİSTİKLERİ ===
            
            // a) Kurul İstatistikleri
            $kurul_stats = [
                'toplam_sikayet' => MusteriSikayeti::count(),
                'yeni_sikayet' => MusteriSikayeti::where('musteri_durum', 'Yeni')->count(),
                'islemde_sikayet' => MusteriSikayeti::where('musteri_durum', 'İşlemde')->count(),
                'son_sikayetler' => MusteriSikayeti::with('sikayetKategori', 'cozumTakimi') 
                                    ->latest() 
                                    ->take(5)
                                    ->get(),
            ];

            // b) Standart Kullanıcı İstatistikleri
            $user_stats = $this->getStandartKullaniciStats($user);
            
            // c) İki diziyi birleştir
            $stats = array_merge($kurul_stats, $user_stats);


        } elseif ($user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
            // === 3. MÜŞTERİ ŞİKAYETİ ÇÖZÜM LİDERİ İSTATİSTİKLERİ ===
            $liderinTakimi = Takim::where('lider_user_id', $user->id)
                                ->where('tur', 'sikayet') // Sadece şikayet takımı
                                ->withCount('uyeler')
                                ->first();
            
            $stats = []; // Boş başlat
            if ($liderinTakimi) {
                $stats['lider_takim'] = $liderinTakimi;
                
                $stats['cozulen_projeler_count'] = Iaa::where('atanan_takim_id', $liderinTakimi->id)
                                                    ->where('durum', 'Tamamlandı')
                                                    ->count();
                $stats['islemde_projeler_count'] = Iaa::where('atanan_takim_id', $liderinTakimi->id)
                                                    ->where('durum', 'Atandı')
                                                    ->count();
                $stats['son_islemde_projeler'] = Iaa::where('atanan_takim_id', $liderinTakimi->id)
                                                    ->where('durum', 'Atandı')
                                                    ->latest()
                                                    ->take(3)
                                                    ->get();
            }
            
            // Lider de standart istatistikleri görsün
            $user_stats = $this->getStandartKullaniciStats($user);
            $stats = array_merge($stats, $user_stats);

        } elseif ($user->hasRole('Bölüm Kalite Yöneticisi')) {
            // === 4. BÖLÜM KALİTE YÖNETİCİSİ İSTATİSTİKLERİ (YENİ EKLENDİ) ===
            
            // a) Onay Bekleyen Sayısını Hesapla
            $sorumluKategoriler = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
            
            $bolumOnayiBekleyenSayisi = Iaa::where('durum', 'Bölüm Onayı Bekliyor')
                ->whereHas('musteriSikayeti', function ($q) use ($sorumluKategoriler) {
                    $q->whereIn('sikayet_kategorisi_id', $sorumluKategoriler);
                })->count();
            
            // b) Standart İstatistikleri de görsün
            $stats = $this->getStandartKullaniciStats($user);

        } else {
            // === 5. STANDART KULLANICI İSTATİSTİKLERİ ===
            $stats = $this->getStandartKullaniciStats($user);
        }

        // 'bolumOnayiBekleyenSayisi' değişkenini view'e gönderiyoruz
        return view('dashboard', compact('user', 'stats', 'bolumOnayiBekleyenSayisi'));
    }

    /**
     * Standart Kullanıcı ve Kurul Üyeleri için ortak dashboard verilerini çeker.
     */
    private function getStandartKullaniciStats(User $user)
    {
        $takimlarim_ids = $user->takimlar()->pluck('takim_id');
        $stats = [];

        // Card 1: Havuzdaki Öneriler
        $stats['havuz_oneri_sayisi'] = Iaa::where('durum', 'Havuzda')->count();
        $stats['son_havuz_onerileri'] = Iaa::where('durum', 'Havuzda')->latest()->take(3)->get();

        // Card 2: Takımlarım
        $stats['takimlarim_sayisi'] = $takimlarim_ids->count();
        $stats['son_takimlarim'] = Takim::whereIn('id', $takimlarim_ids)->latest()->take(3)->get();

        // Card 3: Katılıma Açık Takımlar
        $acik_takimlar_query = Takim::whereDoesntHave('uyeler', fn($q) => $q->where('user_id', $user->id));
        $stats['acik_takim_sayisi'] = $acik_takimlar_query->count();
        $stats['son_acik_takimlar'] = $acik_takimlar_query->withCount('uyeler')->latest()->take(3)->get();

        // Card 4: Projelerim (IAA)
        $iaa_takim_ids = Takim::whereIn('id', $takimlarim_ids)->where('tur', 'iaa')->pluck('id');
        if ($iaa_takim_ids->isNotEmpty()) {
            $stats['iaa_projelerim_count'] = Iaa::whereIn('atanan_takim_id', $iaa_takim_ids)
                                                ->where('durum', 'Atandı')
                                                ->count();
            $stats['son_iaa_projelerim'] = Iaa::whereIn('atanan_takim_id', $iaa_takim_ids)
                                                ->where('durum', 'Atandı')
                                                ->latest()
                                                ->take(3)
                                                ->get();
        }

        // Card 5: Projelerim (Müşteri Şikayeti)
        $sikayet_takim_ids = Takim::whereIn('id', $takimlarim_ids)->where('tur', 'sikayet')->pluck('id');
        if ($sikayet_takim_ids->isNotEmpty()) {
            $stats['sikayet_projelerim_count'] = Iaa::whereIn('atanan_takim_id', $sikayet_takim_ids)
                                                    ->where('durum', 'Atandı')
                                                    ->count();
            $stats['son_sikayet_projelerim'] = Iaa::whereIn('atanan_takim_id', $sikayet_takim_ids)
                                                    ->where('durum', 'Atandı')
                                                    ->latest()
                                                    ->take(3)
                                                    ->get();
        }
        
        return $stats;
    }

    // ... (puanDurumu ve kullaniciPuanlari metodları aynı kalabilir) ...
    public function puanDurumu()
    {
        $kullanicilar = User::where('onaylandi_mi', 1)
                            ->whereDoesntHave('roles', function ($query) {
                                $query->where('name', 'Superadmin');
                            })
                            ->orderByDesc('toplam_puan')
                            ->orderBy('name', 'asc')
                            ->get();

        $takimlar = Takim::where('tur', 'iaa')->orderByDesc('toplam_puan')->get();
        
        return view('puan-durumu', compact('kullanicilar', 'takimlar'));
    }

    public function kullaniciPuanlari(User $user)
    {
        $takimIdleri = $user->takimlar()->pluck('takim_id');
        
        $projeler = Iaa::where('iaas.durum', 'Tamamlandı')
            ->whereIn('iaas.atanan_takim_id', $takimIdleri)
            ->where('iaas.puan', '>', 0)
            ->with('musteriSikayeti') 
            ->get()
            ->map(function ($proje) {
                return [ 
                    'id' => $proje->id,
                    'tip' => $proje->musteriSikayeti ? 'Müşteri Şikayeti' : 'Proje',
                    'baslik' => $proje->baslik,
                    'tarih' => $proje->onaylanma_tarihi,
                    'puan' => $proje->puan,
                    'url' => route('proje.workspace.show', $proje->id)
                ];
            })->all();

        $sikayetler = MusteriSikayeti::where('olusturan_kurul_uyesi_id', $user->id)
            ->where('kazanilan_puan', '>', 0)
            ->with('iaaProjesi') 
            ->get()
            ->map(function ($sikayet) {
                return [
                    'id' => $sikayet->id,
                    'tip' => 'Müşteri Şikayeti',
                    'baslik' => 'Şikayet Kaydı: ' . $sikayet->musteri_sikayet_konusu,
                    'tarih' => $sikayet->created_at,
                    'puan' => $sikayet->kazanilan_puan,
                    'url' => $sikayet->iaaProjesi ? route('proje.workspace.show', $sikayet->iaaProjesi->id) : route('admin.sikayetler.show', $sikayet->id)
                ];
            })->all();

        $kazanilanlarArray = array_merge($projeler, $sikayetler);
        
        usort($kazanilanlarArray, function ($a, $b) {
            $tarihA = Carbon::parse($a['tarih']);
            $tarihB = Carbon::parse($b['tarih']);
            return $tarihB <=> $tarihA;
        });
        
        $kazanilanlar = collect($kazanilanlarArray);

        return view('kullanici-puanlari', compact('user', 'kazanilanlar'));
    }
}