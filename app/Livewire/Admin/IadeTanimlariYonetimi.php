<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Bolum;
use App\Models\IadeTanimi;

class IadeTanimlariYonetimi extends Component
{
    public $secilenBolumId;
    public $yeniTanim = ['urun_grubu' => '', 'iade_sebebi' => '', 'birim' => ''];
    
    // Düzenleme Modu İçin Değişkenler
    public $duzenlenenId = null;
    public $duzenlenenDeger = '';

    public function render()
    {
        $bolumler = Bolum::orderBy('ad')->get();
        
        $tanimlar = [];
        if($this->secilenBolumId) {
            $tanimlar = IadeTanimi::where('bolum_id', $this->secilenBolumId)
                                  ->orderBy('deger')
                                  ->get()
                                  ->groupBy('tip');
        }

        return view('livewire.admin.iade-tanimlari-yonetimi', compact('bolumler', 'tanimlar'))
                ->layout('layouts.app'); 
    }

    public function kaydet($tip)
    {
        $this->validate([
            'secilenBolumId' => 'required',
            "yeniTanim.$tip" => 'required|string|min:2'
        ], [
            "yeniTanim.$tip.required" => 'Bu alan boş bırakılamaz.',
            "yeniTanim.$tip.min" => 'En az 2 karakter girmelisiniz.'
        ]);

        IadeTanimi::create([
            'bolum_id' => $this->secilenBolumId,
            'tip' => $tip,
            'deger' => $this->yeniTanim[$tip]
        ]);

        $this->yeniTanim[$tip] = ''; // Temizle
        
        // Başarı mesajı (Opsiyonel: Toast mesajı varsa onu kullanın)
        session()->flash('success', 'Kayıt başarıyla eklendi.');
    }

    // Düzenleme Modunu Aç
    public function duzenle($id)
    {
        $tanim = IadeTanimi::find($id);
        if ($tanim) {
            $this->duzenlenenId = $id;
            $this->duzenlenenDeger = $tanim->deger;
        }
    }

    // Düzenlemeyi İptal Et
    public function iptalEt()
    {
        $this->duzenlenenId = null;
        $this->duzenlenenDeger = '';
    }

    // Düzenlemeyi Kaydet
    public function guncelle()
    {
        $this->validate([
            'duzenlenenDeger' => 'required|string|min:2'
        ]);

        $tanim = IadeTanimi::find($this->duzenlenenId);
        
        if ($tanim) {
            $tanim->update([
                'deger' => $this->duzenlenenDeger
            ]);
            
            session()->flash('success', 'Kayıt güncellendi.');
        }

        $this->iptalEt(); // Moddan çık
    }

    public function sil($id)
    {
        IadeTanimi::find($id)->delete();
        session()->flash('success', 'Kayıt silindi.');
    }
}