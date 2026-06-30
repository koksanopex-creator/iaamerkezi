<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjeYorumu extends Model
{
    use HasFactory;

    // 1. Adım'daki migration'da oluşturduğumuz tablo adını buraya yazıyoruz
    protected $table = 'proje_yorumlari';

    // Veritabanına hangi alanların kaydedilebileceğini belirtiyoruz
    protected $fillable = [
        'iaa_id',
        'iaa_workflow_step_id',
        'user_id',
        'musteri_sikayeti_id',
        'yapan_kisi_adi',
        'yorum_tipi',
        'yorum',
        'dosya_yolu',
        'dosya_adi',
    ];

    /**
     * Yorumu yapan kullanıcı (eğer misafir değilse).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Yorumun ait olduğu proje (Iaa).
     */
    public function iaa()
    {
        return $this->belongsTo(Iaa::class, 'iaa_id');
    }

    /**
     * Yorumun ait olduğu şikayet (Misafir ise).
     */
    public function musteriSikayeti()
    {
        return $this->belongsTo(MusteriSikayeti::class, 'musteri_sikayeti_id');
    }
}