<?php

namespace App\Livewire\Admin\Disiplin;

use Livewire\Component;
use App\Models\DisciplinaryCase;
use App\Models\DisciplinaryVote;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Notifications\DisiplinKararGeriAlindi;
use App\Models\DisciplinaryPenaltyScale;


class DisiplinOylamaPaneli extends Component
{
    public $caseId;
    public $presentationMode = false;
    public $inModal = false;
    public $tempOyYonu = '';
    public $tempYorum = '';
    public $decisionNote = '';
    public $manualInterventionMode = false;
    public $selectedManualPenalty = '';

    protected function getListeners()
    {
        return [
            'refreshComponent' => '$refresh',
            'toggleGlobalPresentationMode' => 'handleGlobalPresentationMode'
        ];
    }

    public function handleGlobalPresentationMode($status)
    {
        $this->presentationMode = $status;
    }

    public function mount($case, $inModal = false)
    {
        $this->caseId = $case->id;
        $this->inModal = $inModal;
    }

    public function togglePresentationMode()
    {
        $this->presentationMode = !$this->presentationMode;
    }

    public function toggleManualIntervention()
    {
        $this->manualInterventionMode = !$this->manualInterventionMode;
        if ($this->manualInterventionMode && empty($this->selectedManualPenalty)) {
            $case = DisciplinaryCase::findOrFail($this->caseId);
            $this->selectedManualPenalty = $case->sistem_oneri_ceza;
        }
    }

    public function startCaseVoting($note = null)
    {
        if (!Auth::user()->hasAnyRole(['Superadmin', 'Disiplin Kurulu Başkanı', 'Hukuk Admini', 'Hukuk Yöneticisi'])) return;

        $case = DisciplinaryCase::findOrFail($this->caseId);
        $case->update([
            'oylama_aktif' => true,
            'oylama_baslatildi_at' => now(),
            'oylama_baslatan_id' => Auth::id(),
            'oylama_notu' => $note
        ]);

        try {
            $stakeholders = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Hukuk Yöneticisi', 'Hukuk Admini', 'Yönetici', 'İnsan Kaynakları']);
            })->get();

            $kurulUyeleri = User::role(['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı'])->get();
            $allRecipients = $stakeholders->merge($kurulUyeleri)->unique('id');

            foreach ($allRecipients as $recipient) {
                $recipient->notify(new \App\Notifications\DisiplinOylamaBaslatildi($case, Auth::user(), $note));
            }
        } catch (\Exception $e) {
            \Log::error('Oylama başlatma bildirimi hatası (Panel): ' . $e->getMessage());
        }

        session()->flash('success', 'Oylama başarıyla başlatıldı.');
    }

    public function finishCaseVoting()
    {
        if (!Auth::user()->hasAnyRole(['Superadmin', 'Disiplin Kurulu Başkanı', 'Hukuk Admini', 'Hukuk Yöneticisi']) && !Auth::user()->can('disiplin.oylama.bitir')) {
            session()->flash('error', 'Oylamayı sadece yetkili kurul üyeleri bitirebilir.');
            return;
        }

        $case = DisciplinaryCase::findOrFail($this->caseId);
        
        $case->update([
            'oylama_aktif' => false,
            'oylama_bitti_at' => now()
        ]);

        session()->flash('success', 'Oylama sonlandırıldı. Artık tüm üyeler sonuçları görebilir.');
    }

    public function resolveCase($action, $manualPenalty = null)
    {
        if (!Auth::user()->hasAnyRole(['Superadmin', 'Disiplin Kurulu Başkanı', 'Hukuk Admini'])) return;

        $case = DisciplinaryCase::findOrFail($this->caseId);
        $note = $this->decisionNote;
        
        if ($action === 'approve') {
            $penalty = $this->manualInterventionMode ? $this->selectedManualPenalty : ($manualPenalty ?? $case->sistem_oneri_ceza);
            
            // Puan Hesaplama Mantığı
            $deductionPoints = $case->hesaplanan_puan;
            if ($this->manualInterventionMode && $this->selectedManualPenalty) {
                $scale = DisciplinaryPenaltyScale::where('ceza_adi', $this->selectedManualPenalty)->first();
                if ($scale) {
                    // Kural: Min puan 0 ise (Sözlü Uyarı vb.) Max puanı al, değilse Min puanı al.
                    $deductionPoints = ($scale->min_puan == 0) ? $scale->max_puan : $scale->min_puan;
                }
            }

            $case->update([
                'durum' => 'Karar Verildi',
                'final_karar' => 'Ceza Onaylandı',
                'yonetici_notu' => $note,
                'manual_penalty_name' => $this->manualInterventionMode ? $penalty : null,
                'manual_penalty_by' => $this->manualInterventionMode ? Auth::id() : null,
                'hesaplanan_puan' => $deductionPoints,
                'karar_tarihi' => now(),
                'oylama_aktif' => false,
                'oylama_bitti_at' => now(),
            ]);

            // Puanı Kullanıcıdan Düş
            $user = User::find($case->user_id);
            if ($user) {
                $user->decrement('toplam_puan', $deductionPoints);
            }
        } else {
            $case->update([
                'durum' => 'Karar Verildi',
                'final_karar' => 'Savunma Kabul Edildi (Ceza Yok)',
                'yonetici_notu' => $note,
                'karar_tarihi' => now(),
                'oylama_aktif' => false,
                'oylama_bitti_at' => now(),
            ]);
        }

        // Bildirimleri Gönder (Hem Personele Hem Kurul/Yöneticilere)
        try {
            // Personele Bildirim (forPersonel = true)
            $case->user->notify(new \App\Notifications\DisiplinKararVerildi($case, true));

            // Kurul Üyeleri ve İlgili Yöneticilere Bildirim
            $stakeholders = User::whereHas('roles', function($q) {
                $q->whereIn('name', ['Hukuk Yöneticisi', 'Hukuk Admini', 'Yönetici', 'İnsan Kaynakları']);
            })->get();
            $kurulUyeleri = User::role(['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı'])->get();
            
            // Personelin kendi Bölüm Lideri, Yardımcısı ve Direktörünü bul
            $bolumYoneticileri = collect();
            if ($case->user->bolum_id) {
                $bolumYoneticileri = User::where('bolum_id', $case->user->bolum_id)
                    ->whereHas('roles', function($q) {
                        $q->whereIn('name', ['Bölüm Lideri', 'Bölüm Lider Yardımcısı']);
                    })->get();
                    
                // Bölümün bir direktörü varsa onu da ekle
                if ($case->user->bolum && $case->user->bolum->director_id) {
                    $direktor = User::find($case->user->bolum->director_id);
                    if ($direktor) {
                        $bolumYoneticileri->push($direktor);
                    }
                }
            }
            
            $allRecipients = $stakeholders->merge($kurulUyeleri)->merge($bolumYoneticileri)->unique('id');
            
            foreach ($allRecipients as $recipient) {
                // Personele zaten özel bildirim gitti, tekrar genel bildirim atma
                if ($recipient->id !== $case->user_id) {
                    $recipient->notify(new \App\Notifications\DisiplinKararVerildi($case, false));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Karar verildi bildirimi hatası (Livewire): ' . $e->getMessage());
        }

        $this->decisionNote = '';
        session()->flash('success', 'Dosya karara bağlandı ve ilgililere bildirim gönderildi.');

        // Sayfanın en üstündeki durum çubuğunun da güncellenmesi için tam sayfa yenileme tetikliyoruz
        // 'tab' => 'kurul' parametresi ile kullanıcının aynı sekmede kalmasını sağlıyoruz
        return redirect()->route('admin.disiplin.show', ['id' => $this->caseId, 'tab' => 'kurul']);
    }

    public function postponeCase($newDate, $location, $reason)
    {
        if (!Auth::user()->hasAnyRole(['Superadmin', 'Disiplin Kurulu Başkanı', 'Hukuk Admini'])) return;

        $case = DisciplinaryCase::findOrFail($this->caseId);
        $newRound = ($case->rediscussion_count ?? 0) + 1;
        
        $case->update([
            'durum' => 'Kurulda',
            'oylama_aktif' => false,
            'toplanti_tarihi' => $newDate,
            'rediscussion_count' => $newRound,
            'rediscussion_reason' => $reason,
            'yonetici_notu' => ($case->yonetici_notu ? $case->yonetici_notu . "\n\n" : "") . "Tekrar görüşülmesine karar verildi. Sebep: " . $reason
        ]);

        // Mevcut Toplantıyı Yönetme
        // Dosyanın bağlı olduğu "planlandı" veya "devam_ediyor" durumundaki toplantıları bul
        $activeMeetings = $case->toplantilar()->whereIn('durum', ['planlandı', 'devam_ediyor'])->get();
        
        if ($activeMeetings->isEmpty()) {
            // Hiç toplantısı yoksa yeni toplantı oluştur
            $newMeeting = \App\Models\DisiplinKuruluToplanti::create([
                'baslik' => $case->user->name . ' - Disiplin Kurulu (' . $newRound . '. Görüşme)',
                'tur' => 'karar_oturumu',
                'baslangic_tarihi' => $newDate,
                'yer' => $location,
                'durum' => 'planlandı',
                'planlanan_sure_dk' => 30,
                'olusturan_user_id' => Auth::id(),
            ]);
            $newMeeting->disiplinDosyalari()->attach($case->id);
            
            // Kurul üyelerini katılımcı olarak ekle
            $kurulUyeleri = User::role(['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı'])->get();
            foreach ($kurulUyeleri as $uye) {
                \App\Models\DisiplinKuruluToplantiKatilimci::create([
                    'toplanti_id' => $newMeeting->id,
                    'user_id' => $uye->id,
                    'rol' => 'katilimci',
                    'katilim_durumu' => 'bekleniyor',
                ]);
            }
        } else {
            foreach ($activeMeetings as $meeting) {
                // Eğer toplantıda sadece BU dosya varsa, toplantının kendisini ertele
                if ($meeting->disiplinDosyalari()->count() == 1) {
                    $meeting->update([
                        'baslangic_tarihi' => $newDate,
                        'yer' => $location,
                        'baslik' => $meeting->baslik . ' (Ertelendi - ' . $newRound . '. Görüşme)'
                    ]);
                } else {
                    // Eğer toplantıda başka dosyalar da varsa, bu dosyayı mevcut toplantıdan ÇIKAR
                    $meeting->disiplinDosyalari()->detach($case->id);
                    
                    // Ve bu dosya için yeni bir toplantı OLUŞTUR
                    $newMeeting = \App\Models\DisiplinKuruluToplanti::create([
                        'baslik' => $case->user->name . ' - Disiplin Kurulu (' . $newRound . '. Görüşme)',
                        'tur' => 'karar_oturumu',
                        'baslangic_tarihi' => $newDate,
                        'yer' => $location,
                        'durum' => 'planlandı',
                        'planlanan_sure_dk' => 30,
                        'olusturan_user_id' => Auth::id(),
                    ]);
                    
                    $newMeeting->disiplinDosyalari()->attach($case->id);
                    
                    // Eski toplantıdaki katılımcıları yeni toplantıya kopyala
                    foreach ($meeting->katilimcilar as $katilimci) {
                        \App\Models\DisiplinKuruluToplantiKatilimci::create([
                            'toplanti_id' => $newMeeting->id,
                            'user_id' => $katilimci->user_id,
                            'rol' => $katilimci->rol,
                            'katilim_durumu' => 'bekleniyor',
                        ]);
                    }
                }
            }
        }

        // BİLDİRİM GÖNDERİMİ (Kurul Üyelerine ve Başkana)
        $kurulUyeleri = User::role(['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı'])->get();
        foreach ($kurulUyeleri as $uye) {
            try {
                $uye->notify(new \App\Notifications\DisiplinTekrarGorusmePlanlandi($case, Auth::user()));
            } catch (\Exception $e) {
                \Log::error("DisiplinTekrarGorusmePlanlandi hatası: " . $e->getMessage());
            }
        }

        $this->decisionNote = '';
        session()->flash('success', 'Dosya ertelendi ve toplantı takvimi yeni tarihe güncellendi. Üyelere bildirim gönderildi.');

        // Sayfa yenileme ve aynı sekmede kalma
        return redirect()->route('admin.disiplin.show', ['id' => $this->caseId, 'tab' => 'kurul']);
    }

    public function castCaseVote()
    {
        if (empty($this->tempOyYonu)) {
            session()->flash('error', 'Lütfen bir oy yönü seçin.');
            return;
        }

        $case = DisciplinaryCase::findOrFail($this->caseId);
        $currentRound = ($case->rediscussion_count ?? 0) + 1;

        DisciplinaryVote::updateOrCreate(
            ['case_id' => $this->caseId, 'user_id' => Auth::id(), 'round' => $currentRound],
            ['oy_yonu' => $this->tempOyYonu, 'yorum' => $this->tempYorum]
        );

        // [OTOMASYON] Tüm oylar bitti mi kontrol et ve yetkililere haber ver
        $councilRoles = ['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı'];
        $initiatorId = $case->oylama_baslatan_id;

        // Oylamayı başlatan kişi hariç kurul üyeleri listesi
        $requiredVoterIds = User::role($councilRoles)
            ->where('id', '!=', $initiatorId)
            ->pluck('id');

        // Bu turda oy verenlerin sayısı (başlatan kişi hariç)
        $votesCount = DisciplinaryVote::where('case_id', $this->caseId)
            ->where('round', $currentRound)
            ->whereIn('user_id', $requiredVoterIds)
            ->count();

        // Eğer başlatan hariç herkes oy verdiyse bildirimi gönder
        if ($votesCount >= $requiredVoterIds->count()) {
            try {
                // Bildirim oylamayı başlatan kişiye gider
                $initiator = User::find($initiatorId);
                if ($initiator) {
                    $initiator->notify(new \App\Notifications\DisiplinTumOylarTamamlandi($case));
                }
                
                // Ekstra: Eğer başlatan başkan değilse, başkana da bilgi gitsin (isteğe bağlı ama mantıklı)
                if (Auth::user()->hasRole('Disiplin Kurulu Başkanı') && Auth::id() != $initiatorId) {
                    Auth::user()->notify(new \App\Notifications\DisiplinTumOylarTamamlandi($case));
                }

            } catch (\Exception $e) {
                \Log::error('Tüm oylar tamamlandı bildirimi hatası: ' . $e->getMessage());
            }
        }

        $this->tempOyYonu = '';
        $this->tempYorum = '';
        session()->flash('success', 'Oyunuz kaydedildi.');
    }

    public function deleteCaseVote()
    {
        $case = DisciplinaryCase::findOrFail($this->caseId);
        $currentRound = ($case->rediscussion_count ?? 0) + 1;

        DisciplinaryVote::where('case_id', $this->caseId)
            ->where('user_id', Auth::id())
            ->where('round', $currentRound)
            ->delete();

        $this->tempOyYonu = '';
        $this->tempYorum = '';
        session()->flash('success', 'Oyunuz silindi.');
    }

    public function revertDecision()
    {
        if (!Auth::user()->hasAnyRole(['Superadmin', 'Disiplin Kurulu Başkanı', 'Hukuk Admini', 'Hukuk Yöneticisi'])) return;

        $case = DisciplinaryCase::findOrFail($this->caseId);
        
        if (!in_array($case->durum, ['Karar Verildi', 'Kurulda'])) {
            session()->flash('error', 'Bu aşamadaki dosya geri alınamaz.');
            return;
        }

        DB::beginTransaction();
        try {
            $oldStatus = $case->durum;
            $newStatus = 'Kurulda';
            $successMsg = 'Karar başarıyla geri alındı. Dosya tekrar oylamaya açıldı.';
            $shouldSyncPoints = false;
            $targetUserForSync = null;

            if ($oldStatus === 'Karar Verildi') {
                // Puan İadesi
                if ($case->final_karar === 'Ceza Onaylandı') {
                    $user = $case->user;
                    if ($user) {
                        $user->increment('toplam_puan', $case->hesaplanan_puan);
                        $shouldSyncPoints = true;
                        $targetUserForSync = $user;
                    }
                }
                
                // Orijinal Sevk Tarihini Geri Al
                $referralLog = \App\Models\DisciplinaryLog::where('disciplinary_case_id', $case->id)
                    ->where('eylem', 'Kurula Sevk Edildi')
                    ->latest()
                    ->first();
                $case->karar_tarihi = $referralLog ? $referralLog->created_at : now();

            } elseif ($oldStatus === 'Kurulda') {
                // PLANLANAN TOPLANTILARI SİL
                $toplantilar = $case->toplantilar()->whereIn('durum', ['planlandı', 'devam_ediyor'])->get();
                foreach ($toplantilar as $toplanti) {
                    $toplanti->katilimcilar()->delete();
                    $toplanti->disiplinDosyalari()->detach();
                    $toplanti->delete();
                }

                // OYLARI TEMİZLE
                $case->oylar()->delete();

                $newStatus = 'Yönetici Değerlendirmesi';
                $case->karar_tarihi = null;
                $successMsg = 'Kurul sevki geri alındı. Planlanan toplantı ve oylama kayıtları temizlendi.';
            }

            // Orijinal Sistem Puanını Yeniden Hesapla
            $calc = $case->calculateMatrixScore();

            // Dosya Durumunu Güncelle
            $case->update([
                'durum' => $newStatus,
                'oylama_aktif' => ($newStatus === 'Kurulda'),
                'final_karar' => null,
                'karar_tarihi' => $case->karar_tarihi,
                'hesaplanan_puan' => $calc['toplam_puan'],
                'sistem_oneri_ceza' => $calc['oneri_ceza'],
                'manual_penalty_name' => null,
                'manual_penalty_by' => null,
                'oylama_bitti_at' => null,
                'yonetici_notu' => null,
                'toplanti_tarihi' => ($newStatus === 'Kurulda' ? $case->toplanti_tarihi : null),
            ]);

            // Puan Senkronizasyonunu Veritabanı Güncellemesinden Sonra Yapıyoruz (Race Condition'ı önlemek için)
            if ($shouldSyncPoints && $targetUserForSync) {
                try {
                    app(\App\Services\Dashboard\KullaniciPuanService::class)->syncUserCache($targetUserForSync);
                } catch (\Exception $e) { 
                    \Log::error('Puan senkronizasyon hatası (revertDecision): ' . $e->getMessage()); 
                }
            }

            // Bildirimleri Temizle
            if ($oldStatus === 'Karar Verildi') {
                DB::table('notifications')->where('type', 'App\Notifications\DisiplinKararVerildi')->where('data', 'like', '%"case_id":' . $case->id . '%')->delete();
            }

            // Yeni Bildirim Gönder
            try {
                $stakeholders = User::whereHas('roles', function($q) { $q->whereIn('name', ['Hukuk Yöneticisi', 'Hukuk Admini', 'Yönetici', 'İnsan Kaynakları']); })->get();
                $kurulUyeleri = User::role(['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı'])->get();
                $allRecipients = $stakeholders->merge($kurulUyeleri)->push($case->user)->unique('id');
                foreach ($allRecipients as $recipient) { $recipient->notify(new DisiplinKararGeriAlindi($case, Auth::user())); }
            } catch (\Exception $e) { \Log::error('Karar geri alma bildirimi hatası (Livewire): ' . $e->getMessage()); }

            DB::commit();
            session()->flash('success', $successMsg);
            return redirect()->route('admin.disiplin.show', ['id' => $this->caseId, 'tab' => 'kurul']);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Hata oluştu: ' . $e->getMessage());
        }
    }

    public function render()
    {
        $case = DisciplinaryCase::with(['oylar.user', 'behavior.category', 'user.bolum', 'reporter', 'impact', 'scope'])->findOrFail($this->caseId);
        
        $councilRoles = ['Disiplin Kurulu Üyesi', 'Disiplin Kurulu Başkanı'];
        $allCouncilMembers = User::role($councilRoles)->get();
        $totalMembersCount = $allCouncilMembers->count();

        $currentRound = ($case->rediscussion_count ?? 0) + 1;
        $caseVotes = $case->oylar->where('round', $currentRound);
        $myVote = $caseVotes->where('user_id', Auth::id())->first();

        // Oylama durumları
        $votesPenalty = $caseVotes->where('oy_yonu', 'Ceza Verilsin')->count();
        $votesNoPenalty = $caseVotes->where('oy_yonu', 'Ceza Verilmesin')->count();
        $votesInvestigation = $caseVotes->where('oy_yonu', 'Ek Soruşturma')->count();
        $votesAbstain = $caseVotes->where('oy_yonu', 'Çekimser')->count();
        $votesUsed = $caseVotes->count();

        // Yüzdeler
        $percPenalty = $votesUsed > 0 ? ($votesPenalty / $votesUsed) * 100 : 0;
        $percNoPenalty = $votesUsed > 0 ? ($votesNoPenalty / $votesUsed) * 100 : 0;
        $percInvestigation = $votesUsed > 0 ? ($votesInvestigation / $votesUsed) * 100 : 0;
        $percAbstain = $votesUsed > 0 ? ($votesAbstain / $votesUsed) * 100 : 0;

        // [KÖR OYLAMA MANTIĞI]
        // Sadece 'yalnızca kurul üyesi' olanlar oy kullanmadan sonuçları göremez.
        // Superadmin, Hukuk Admini veya Kurul Başkanı rolleri üyelik kısıtlamasını ezer.
        $isPrivileged = Auth::user()->hasAnyRole(['Superadmin', 'Hukuk Admini', 'Disiplin Kurulu Başkanı']);
        $isStandardCouncilMember = Auth::user()->hasRole('Disiplin Kurulu Üyesi') && !$isPrivileged;
        $hasVotedInCurrentRound = (bool)$myVote;
        
        if ($case->durum === 'Karar Verildi' || !$case->oylama_aktif) {
            $canSeeResults = true;
        } else {
            $canSeeResults = ($hasVotedInCurrentRound || !$isStandardCouncilMember) && !$this->presentationMode;
        }

        $penaltyScale = \App\Models\DisciplinaryPenaltyScale::orderBy('min_puan', 'asc')->get();

        $participationRate = $totalMembersCount > 0 ? ($votesUsed / $totalMembersCount) * 100 : 0;

        return view('livewire.admin.disiplin.disiplin-oylama-paneli', [
            'case' => $case,
            'penaltyScale' => $penaltyScale,
            'totalMembersCount' => $totalMembersCount,
            'currentRound' => $currentRound,
            'caseVotes' => $caseVotes,
            'myVote' => $myVote,
            'votesPenalty' => $votesPenalty,
            'votesNoPenalty' => $votesNoPenalty,
            'votesInvestigation' => $votesInvestigation,
            'votesAbstain' => $votesAbstain,
            'votesUsed' => $votesUsed,
            'percPenalty' => $percPenalty,
            'percNoPenalty' => $percNoPenalty,
            'percInvestigation' => $percInvestigation,
            'percAbstain' => $percAbstain,
            'canSeeResults' => $canSeeResults,
            'allCouncilMembers' => $allCouncilMembers,
            'votedUserIds' => $caseVotes->pluck('user_id')->toArray(),
            'waitingVotes' => $totalMembersCount - $votesUsed,
            'participationRate' => ceil($participationRate)
        ]);
    }
}
