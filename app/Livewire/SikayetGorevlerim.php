<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Iaa;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class SikayetGorevlerim extends Component
{
    use WithPagination;

    public function render()
    {
        $user = Auth::user();

        // SORGULAMA: Sadece "Benim Yapmam Gereken" İşler
        $query = Iaa::with([
            'musteriSikayeti.sikayetKategori',
            'atananTakim',
            'aktifAdim.sorumlular',
            'logs.user'
        ])
            ->has('musteriSikayeti')
            ->where(function ($topQ) use ($user) {

                // 1. DURUM: BÖLÜM ONAYI BEKLİYOR
                // A) Ben O Bölümün Lideriyim (Kategori bazlı eşleşme)
                if ($user->hasRole('Bölüm Lideri') && $user->bolum_id) {
                    $topQ->orWhere(function ($q) use ($user) {
                        $q->where('durum', 'Bölüm Onayı Bekliyor')
                            ->whereHas('musteriSikayeti.sikayetKategori', function ($k) use ($user) {
                                $k->where('bolum_id', $user->bolum_id);
                            });
                    });
                }

                // B) Ben O Kategorinin "Bölüm Kalite Yöneticisi"yim
                if ($user->hasRole('Bölüm Kalite Yöneticisi')) {
                    $yonetilenKategoriler = $user->yonettigiSikayetKategorileri->pluck('id');

                    if ($yonetilenKategoriler->isNotEmpty()) {
                        $topQ->orWhere(function ($q) use ($yonetilenKategoriler) {
                            $q->where('durum', 'Bölüm Onayı Bekliyor')
                                ->whereHas('musteriSikayeti', function ($k) use ($yonetilenKategoriler) {
                                    $k->whereIn('sikayet_kategorisi_id', $yonetilenKategoriler);
                                });
                        });
                    }
                }

                // 2. DURUM: YÖNETİCİ ONAYI BEKLİYOR (Ve Ben Yönetim/Superadminim)
                if ($user->hasRole(['Superadmin', 'Yonetim'])) {
                    $topQ->orWhere('durum', 'Yönetici Onayı Bekliyor');
                }

                // 3. DURUM: AKTİF BİR ADIM BANA ATANMIŞSA (Kişisel Görev)
                $topQ->orWhereHas('stepAssignments', function ($assignQ) use ($user) {
                    $assignQ->where('user_id', $user->id)
                        ->whereNotExists(function ($sub) {
                            $sub->select(\Illuminate\Support\Facades\DB::raw(1))
                                ->from('iaa_progress_updates')
                                ->join('iaa_talepleri', 'iaa_progress_updates.iaa_talep_id', '=', 'iaa_talepleri.id')
                                ->whereColumn('iaa_talepleri.iaa_id', 'iaa_step_assignments.iaa_id')
                                ->whereColumn('iaa_progress_updates.iaa_workflow_step_id', 'iaa_step_assignments.iaa_workflow_step_id')
                                ->whereNotNull('completed_at');
                        });
                });

                // 4. DURUM: BEN BU PROJEDE AKTİF GÖREVLİYİM (Takım Üyesi veya Lideri)
                // Proje bitmediği sürece takip edebilmeliyim.
                $topQ->orWhere(function ($activeQ) use ($user) {
                    // Proje Tamamlanmadıysa ve İptal Edilmediyse
                    $activeQ->whereNotIn('durum', ['Tamamlandı', 'İptal Edildi', 'Reddedildi', 'Talep Olarak Kapatıldı', 'hatali_bildirim_olarak_kapatildi', 'talep_olarak_kapatildi'])
                        ->where(function ($teamQ) use ($user) {
                        // A) Proje Ekibindeyim (Pivot Tablo)
                        $teamQ->whereHas('projeEkibi', function ($pe) use ($user) {
                            $pe->where('users.id', $user->id);
                        })
                            // B) Atanan Takımın Lideriyim
                            ->orWhereHas('atananTakim', function ($at) use ($user) {
                            $at->where('lider_user_id', $user->id);
                        });
                    });
                });

                // 4. DURUM: DİSİPLİN / TUTANAK (Eğer Iaa ile ilişkiliyse buraya eklenebilir)
                // Şu an için Iaa modeli üzerinden gidildiği için sadece Proje görevlerini kapsıyor.
            });

        // SIRALAMA: En son işlem görenden eskiye
        $projeler = $query->latest('updated_at')->paginate(10);

        return view('livewire.sikayet-gorevlerim', [
            'projeler' => $projeler
        ]);
    }
}