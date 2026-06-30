<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Notifications\ObserverAssigned;
use App\Notifications\ObserverRemoved;

class ObserverController extends Controller
{
    /**
     * Birini gözlemci modunda izlemeye başla.
     */
    public function startShadowing(Request $request, User $target)
    {
        $user = auth()->user();

        // Yetki kontrolü (Pivot tabloda var mı?)
        if (!$user->observedUsers()->where('target_user_id', $target->id)->exists()) {
            return back()->with('error', 'Bu kullanıcıyı izleme yetkiniz bulunmamaktadır.');
        }

        // Seansa kaydet (Orijinal kullanıcı ID'sini de sakla - User modelindeki proxy logic için kritik)
        session([
            'active_shadow_user_id' => $target->id,
            'shadowing_original_user_id' => $user->id
        ]);

        return redirect()->route('dashboard')->with('success', $target->name . ' hesabını Gözlemci modunda izliyorsunuz.');
    }

    /**
     * Gözlemci modundan çık ve kendi hesabına dön.
     */
    public function stopShadowing()
    {
        session()->forget(['active_shadow_user_id', 'shadowing_original_user_id']);
        return redirect()->route('dashboard')->with('success', 'Kendi hesabınıza geri döndünüz.');
    }

    /**
     * Kendi profili üzerinden gözlemci eklemek için (Yöneticiler/Direktörler kullanır).
     */
    public function addObserver(Request $request)
    {
        $request->validate([
            'observer_id' => 'required|exists:users,id'
        ]);

        $target = auth()->user();
        $observerId = $request->observer_id;

        if ($target->id == $observerId) {
            return back()->with('error', 'Kendinizi gözlemci olarak ekleyemezsiniz.');
        }

        // Zaten ekli mi?
        if ($target->observers()->where('observer_user_id', $observerId)->exists()) {
            return back()->with('error', 'Bu personel zaten gözlemciniz olarak ekli.');
        }

        $target->observers()->attach($observerId);

        // Bildirim Gönder
        $observer = User::find($observerId);
        $observer->notify(new ObserverAssigned($target));

        return back()->with('success', $observer->name . ' gözlemciniz olarak başarıyla eklendi.');
    }

    /**
     * Gözlemciyi silmek için.
     */
    public function removeObserver(User $observer)
    {
        $target = auth()->user();
        
        $target->observers()->detach($observer->id);

        // Bildirim Gönder
        $observer->notify(new ObserverRemoved($target));

        return back()->with('success', 'Gözlemci başarıyla kaldırıldı.');
    }
}
