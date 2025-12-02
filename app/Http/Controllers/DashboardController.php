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
use App\Models\ProjeYorumu; // Yorumlar için model
use App\Models\ProfileComment;
use Illuminate\Support\Facades\DB; // <--- BU EKSİK, EKLE!

class DashboardController extends Controller
{
    /**
     * Dashboard ana sayfasını gösterir.
     * Artık role göre farklı istatistikler içerir.
     */
    public function index()
    {
        $user = Auth::user();
        $stats = [];
        $bolumOnayiBekleyenSayisi = 0;

        // --- 1. ONLINE ve SON GÖRÜLEN KULLANICILAR ---
        $onlineKullanicilar = User::where('last_seen_at', '>=', now()->subMinutes(5))
            ->orderBy('last_seen_at', 'desc')
            ->take(10)
            ->get();

        $sonAktifKullanicilar = User::whereNotNull('last_seen_at')
            ->orderBy('last_seen_at', 'desc')
            ->take(10)
            ->get();

        // --- 2. EKSTRA İSTATİSTİKLER (Superadmin için) ---
        $ekstraTablolar = [];
        
        if ($user->hasRole('Superadmin')) {
            $ekstraTablolar['son_takimlar'] = Takim::with('lider')->latest()->take(10)->get();
            $ekstraTablolar['son_cozulen_sikayetler'] = MusteriSikayeti::where('musteri_durum', 'Kapatıldı')->with('cozumTakimi')->latest('updated_at')->take(10)->get();
            $ekstraTablolar['son_tamamlanan_iaa'] = Iaa::where('durum', 'Tamamlandı')->with('atananTakim')->latest('updated_at')->take(10)->get();
            $ekstraTablolar['son_yorumlar'] = ProjeYorumu::with('iaa')->latest()->take(10)->get();
            $ekstraTablolar['son_profil_yorumlari'] = ProfileComment::with(['yazan', 'profileUser'])->latest()->take(10)->get();
            $ekstraTablolar['son_kazanilan_puanlar'] = Iaa::where('puan', '>', 0)->with('atananTakim')->latest('updated_at')->take(10)->get();
        } 

        // --- 3. ROLE GÖRE ANA İSTATİSTİKLER ---

        // A) SUPERADMIN
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
                'toplam_sikayet' => MusteriSikayeti::count(),
                'yeni_sikayet' => MusteriSikayeti::where('musteri_durum', 'Yeni')->count(),
                'islemde_sikayet' => MusteriSikayeti::where('musteri_durum', 'İşlemde')->count(),
                'son_sikayetler' => MusteriSikayeti::latest()->take(3)->get(),
            ];
            $bolumOnayiBekleyenSayisi = Iaa::where('durum', 'Bölüm Onayı Bekliyor')->count();

        // B) MÜŞTERİ ŞİKAYETİ KURULU
        } elseif ($user->hasRole('Müşteri Şikayeti Kurulu')) {
            $kurul_stats = [
                'toplam_sikayet' => MusteriSikayeti::count(),
                'yeni_sikayet' => MusteriSikayeti::where('musteri_durum', 'Yeni')->count(),
                'islemde_sikayet' => MusteriSikayeti::where('musteri_durum', 'İşlemde')->count(),
                'son_sikayetler' => MusteriSikayeti::with('sikayetKategori', 'cozumTakimi')->latest()->take(5)->get(),
            ];
            $user_stats = $this->getStandartKullaniciStats($user);
            $stats = array_merge($kurul_stats, $user_stats);

        // C) ÇÖZÜM LİDERİ
        } elseif ($user->hasRole('Müşteri Şikayeti Çözüm Lideri')) {
            $liderinTakimi = Takim::where('lider_user_id', $user->id)->where('tur', 'sikayet')->withCount('uyeler')->first();
            $stats = [];
            if ($liderinTakimi) {
                $stats['lider_takim'] = $liderinTakimi;
                $stats['cozulen_projeler_count'] = Iaa::where('atanan_takim_id', $liderinTakimi->id)->where('durum', 'Tamamlandı')->count();
                $stats['islemde_projeler_count'] = Iaa::where('atanan_takim_id', $liderinTakimi->id)->where('durum', 'Atandı')->count();
                $stats['son_islemde_projeler'] = Iaa::where('atanan_takim_id', $liderinTakimi->id)->where('durum', 'Atandı')->latest()->take(3)->get();
            }
            $user_stats = $this->getStandartKullaniciStats($user);
            $stats = array_merge($stats, $user_stats);

        // D) BÖLÜM KALİTE YÖNETİCİSİ (Serkan Tölek)
        } elseif ($user->hasRole('Bölüm Kalite Yöneticisi')) {
            
            $sorumluKategoriler = $user->yonettigiSikayetKategorileri->pluck('id')->toArray();
            
            $bolumOnayiBekleyenSayisi = Iaa::where('durum', 'Bölüm Onayı Bekliyor')
                ->whereHas('musteriSikayeti', function ($q) use ($sorumluKategoriler) {
                    $q->whereIn('sikayet_kategorisi_id', $sorumluKategoriler);
                })->count();
            
            // İstatistikler
            $toplamSikayet = MusteriSikayeti::whereIn('sikayet_kategorisi_id', $sorumluKategoriler)->count();
            $cozulenSikayet = MusteriSikayeti::whereIn('sikayet_kategorisi_id', $sorumluKategoriler)->where('musteri_durum', 'Kapatıldı')->count();
            $islemdekiSikayet = MusteriSikayeti::whereIn('sikayet_kategorisi_id', $sorumluKategoriler)->where('musteri_durum', 'İşlemde')->count();

            $onayBekleyenProjelerListe = Iaa::where('durum', 'Bölüm Onayı Bekliyor')
                ->whereHas('musteriSikayeti', function ($q) use ($sorumluKategoriler) {
                    $q->whereIn('sikayet_kategorisi_id', $sorumluKategoriler);
                })
                ->with(['atananTakim', 'musteriSikayeti'])
                ->latest('updated_at')
                ->take(5)
                ->get();

            $sonDepartmanSikayetleri = MusteriSikayeti::whereIn('sikayet_kategorisi_id', $sorumluKategoriler)
                ->with('cozumTakimi')
                ->latest()
                ->take(5)
                ->get();

            $stats = [
                'bolum_onay_sayisi' => $bolumOnayiBekleyenSayisi,
                'toplam_sikayet' => $toplamSikayet,
                'cozulen_sikayet' => $cozulenSikayet,
                'islemdeki_sikayet' => $islemdekiSikayet,
                'onay_bekleyen_liste' => $onayBekleyenProjelerListe,
                'son_departman_sikayetleri' => $sonDepartmanSikayetleri,
                ...$this->getStandartKullaniciStats($user)
            ];

        // E) STANDART KULLANICI
        } else {
            $stats = $this->getStandartKullaniciStats($user);
        }

        // --- 4. BEKLEYEN DAVETLER ---
        $bekleyenProjeDavetleri = $user->gorevliOlduguProjeler()
            ->wherePivot('durum', 'bekliyor')
            ->with('atananTakim.lider')
            ->get();

        // --- 5. BEKLEYEN ADIM GÖREVLERİ (TURUNCU KART İÇİN) ---
        $bekleyenAdimGorevleri = DB::table('iaa_step_assignments')
            ->join('iaas', 'iaa_step_assignments.iaa_id', '=', 'iaas.id')
            ->join('iaa_workflow_steps', 'iaa_step_assignments.iaa_workflow_step_id', '=', 'iaa_workflow_steps.id')
            ->where('iaa_step_assignments.user_id', $user->id)
            ->whereIn('iaas.durum', ['Atandı', 'Revize Ediliyor', 'Bölüm Onayı Bekliyor', 'Yönetici Onayı Bekliyor', 'Tamamlanması Reddedildi'])
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                      ->from('iaa_progress_updates')
                      ->join('iaa_talepleri', 'iaa_progress_updates.iaa_talep_id', '=', 'iaa_talepleri.id')
                      ->whereColumn('iaa_talepleri.iaa_id', 'iaa_step_assignments.iaa_id')
                      ->whereColumn('iaa_progress_updates.iaa_workflow_step_id', 'iaa_step_assignments.iaa_workflow_step_id')
                      ->whereNotNull('iaa_progress_updates.completed_at');
            })
            ->select('iaas.id as iaa_id', 'iaas.baslik as proje_baslik', 'iaa_workflow_steps.name as adim_adi', 'iaa_step_assignments.updated_at as atama_tarihi')
            ->get();

        return view('dashboard', compact(
            'user', 'stats', 'bolumOnayiBekleyenSayisi', 'onlineKullanicilar', 
            'sonAktifKullanicilar', 'ekstraTablolar', 'bekleyenProjeDavetleri', 'bekleyenAdimGorevleri'
        ));
    }
    /**
     * Standart Kullanıcı ve Kurul Üyeleri için ortak dashboard verilerini çeker.
     * GÜNCELLENDİ: Hem Takım Projelerini hem de Squad Projelerini kapsar.
     */
    private function getStandartKullaniciStats(User $user)
    {
        $stats = [];

        // 1. Havuzdaki Öneriler (Değişmedi)
        $stats['havuz_oneri_sayisi'] = Iaa::where('durum', 'Havuzda')->count();
        $stats['son_havuz_onerileri'] = Iaa::where('durum', 'Havuzda')->latest()->take(3)->get();

        // 2. Takımlarım (Değişmedi - Sadece kalıcı üyelikler)
        $takimlarim_ids = $user->takimlar()->pluck('takim_id');
        $stats['takimlarim_sayisi'] = $takimlarim_ids->count();
        $stats['son_takimlarim'] = Takim::whereIn('id', $takimlarim_ids)->latest()->take(3)->get();

        // 3. Katılıma Açık Takımlar (Değişmedi)
        $acik_takimlar_query = Takim::whereDoesntHave('uyeler', fn($q) => $q->where('user_id', $user->id));
        $stats['acik_takim_sayisi'] = $acik_takimlar_query->count();
        $stats['son_acik_takimlar'] = $acik_takimlar_query->withCount('uyeler')->latest()->take(3)->get();

        // === KRİTİK GÜNCELLEME BURADA ===
        
        // A) Kullanıcının erişebileceği TÜM proje ID'lerini topla (Takım + Squad)
        $takimProjeleriIds = Iaa::whereIn('atanan_takim_id', $takimlarim_ids)->pluck('id')->toArray();
        
        $squadProjeleriIds = $user->gorevliOlduguProjeler()
                                  ->wherePivot('durum', 'onaylandi')
                                  ->pluck('iaas.id')
                                  ->toArray();
                                  
        $tumProjeIds = array_unique(array_merge($takimProjeleriIds, $squadProjeleriIds));

        // AKTİF STATÜLER LİSTESİ (Burası Eksikti!)
        // Tamamlanmış hariç, sürecin içindeki her şeyi kapsar.
        $aktifStatuler = [
            'Atandı', 
            'Revize Ediliyor', 
            'Bölüm Onayı Bekliyor', 
            'Yönetici Onayı Bekliyor', 
            'Tamamlanması Reddedildi'
        ];

        // B) İAA Projeleri (Saf İAA)
        $iaaQuery = Iaa::whereIn('id', $tumProjeIds)
                        ->doesntHave('musteriSikayeti')
                        ->whereIn('durum', $aktifStatuler); // <--- DÜZELTİLDİ

        $stats['iaa_projelerim_count'] = $iaaQuery->count();
        $stats['son_iaa_projelerim'] = $iaaQuery->latest()->take(3)->get();

        // C) Şikayet Projeleri (Cihangir'in Göremediği Yer)
        $sikayetQuery = Iaa::whereIn('id', $tumProjeIds)
                           ->has('musteriSikayeti')
                           ->whereIn('durum', $aktifStatuler); // <--- DÜZELTİLDİ: Artık Bölüm Onayındakileri de sayar

        // Eğer sayı 0 ise bu değişkeni hiç gönderme (Böylece Blade'deki @isset çalışmaz ve kart gizlenir)
        $count = $sikayetQuery->count();
        if ($count > 0) {
            $stats['sikayet_projelerim_count'] = $count;
            $stats['son_sikayet_projelerim'] = $sikayetQuery->latest()->take(3)->get();
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
        // 1. Takım Üyeliğinden Gelen Projeler (Mevcut kod)
        $takimIdleri = $user->takimlar()->pluck('takim_id');
        
        // 2. SQUAD Üyeliğinden Gelen Projeler (YENİ)
        $squadProjeIdleri = $user->gorevliOlduguProjeler()->pluck('iaas.id');

        // 3. Birleştirilmiş Sorgu
        $projeler = Iaa::where('iaas.durum', 'Tamamlandı')
            ->where('iaas.puan', '>', 0)
            ->where(function($q) use ($takimIdleri, $squadProjeIdleri) {
                // Takımıma atananlar
                $q->whereIn('iaas.atanan_takim_id', $takimIdleri)
                // VEYA Bizzat squad üyesi olduklarım
                  ->orWhereIn('iaas.id', $squadProjeIdleri);
            })
            ->with('musteriSikayeti') 
            ->get()
            ->map(function ($proje) {
                return [ 
                    'id' => $proje->id,
                    'tip' => $proje->musteriSikayeti ? 'Müşteri Şikayeti' : 'Proje',
                    'baslik' => $proje->baslik,
                    'tarih' => $proje->onaylanma_tarihi ?? $proje->updated_at, // Onay tarihi yoksa güncelleme tarihini al
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