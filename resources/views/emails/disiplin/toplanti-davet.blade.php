@component('mail::message')
# Sayın Katılımcı,

Disiplin Kurulu tarafından düzenlenen **{{ $toplanti->baslik }}** konulu toplantıya davet edildiniz.

**Toplantı Detayları:**
- **Tarih:** {{ $toplanti->baslangic_tarihi->translatedFormat('d F Y H:i') }}
- **Yer:** {{ $toplanti->yer ?: 'Online / Belirtilmemiş' }}
- **Tür:** {{ ucfirst($toplanti->tur) }}

@if($toplanti->icerik)
**Gündem / İçerik:**
{{ $toplanti->icerik }}
@endif

Toplantı odasına aşağıdaki butona tıklayarak erişebilirsiniz:

@component('mail::button', ['url' => route('admin.disiplin.kurul.toplanti.show', $toplanti)])
Toplantı Odasına Git
@endcomponent

Saygılarımızla,
**Köksan Disiplin Kurulu Sistemi**
@endcomponent
