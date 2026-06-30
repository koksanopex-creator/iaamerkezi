<x-mail::message>
# Merhaba {{ $name }},

Disiplin kurulu tarafından düzenlenen **{{ $toplanti->baslik }}** konulu toplantı bilgileri **{{ $guncelleyenAdi }}** tarafından **<u>güncellenmiştir</u>**.

Değişiklik detayları aşağıdadır:

<x-mail::table>
| Özellik | Eski Bilgi | Yeni Bilgi |
| :--- | :--- | :--- |
| **Başlık** | {{ $oldData['baslik'] }} | {{ $newData['baslik'] }} |
| **Tarih** | {{ \Carbon\Carbon::parse($oldData['baslangic_tarihi'])->format('d.m.Y H:i') }} | {{ \Carbon\Carbon::parse($newData['baslangic_tarihi'])->format('d.m.Y H:i') }} |
| **Yer** | {{ $oldData['yer'] ?: 'Belirtilmedi' }} | {{ $newData['yer'] ?: 'Belirtilmedi' }} |
</x-mail::table>

Lütfen yeni programı dikkate alınız.

<x-mail::button :url="route('admin.disiplin.kurul.toplanti.show', $toplanti)">
Toplantı Detayına Git
</x-mail::button>

Saygılarımızla,<br>
**Köksan Disiplin Kurulu Sistemi**
</x-mail::message>
