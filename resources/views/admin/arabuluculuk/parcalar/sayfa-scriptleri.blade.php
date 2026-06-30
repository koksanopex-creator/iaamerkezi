<script>
    // $case değişkeni show.blade.php'den gelir, calisan ilişkisi Model'den gelir.
    var personelAdi = "{{ $case->calisan->name ?? 'Çalışan Bilgisi Yok' }}";

    function toggleAliciInput() {
        var select = document.getElementById('odeme_alici_tipi');
        var input = document.getElementById('odenecek_kisi_ad_soyad');
        var aciklama = document.getElementById('input_aciklama');

        if (select && select.value === 'calisan') {
            input.value = personelAdi;
            input.setAttribute('readonly', true); // Yazmayı engelle
            input.classList.add('bg-gray-50', 'text-gray-500'); // Gri yap
            input.classList.remove('bg-white', 'text-gray-900');
            
            if(aciklama) {
                aciklama.textContent = 'Ödeme varsayılan olarak ilgili personele (' + personelAdi + ') yapılacaktır.';
                aciklama.parentElement.className = "mt-2 flex items-start gap-2 text-xs text-gray-600 bg-blue-50 p-3 rounded-lg border border-blue-100";
            }
        } else {
            // Eğer "Diğer" seçiliyse veya sayfa ilk açıldığında farklı bir durum varsa
            // (Amaç inputu temizlemek değil, kullanıcı girişine açmaktır)
            if(select && select.value !== 'calisan') {
                // Sadece 'Diğer' seçildiğinde içini boşaltmak istersen:
                // input.value = ''; 
                // Ancak kullanıcı bir isim yazıp sayfayı yenilediyse (validation hatası vb.) eski değer kalsın istersen boşaltma.
                // Şimdilik senin "değiştirince düzeliyor" mantığına uygun olarak boşaltalım:
                if (input.value === personelAdi) input.value = ''; 
            }

            input.removeAttribute('readonly');
            input.classList.remove('bg-gray-50', 'text-gray-500');
            input.classList.add('bg-white', 'text-gray-900');
            
            if(aciklama) {
                aciklama.textContent = 'Lütfen alıcının tam adını ve soyadını giriniz.';
                aciklama.parentElement.className = "mt-2 flex items-start gap-2 text-xs text-amber-700 bg-amber-50 p-3 rounded-lg border border-amber-200";
            }
        }
    }

    // SAYFA YÜKLENDİĞİNDE OTOMATİK ÇALIŞTIR (FIX)
    document.addEventListener('DOMContentLoaded', function() {
        toggleAliciInput();
    });
</script>