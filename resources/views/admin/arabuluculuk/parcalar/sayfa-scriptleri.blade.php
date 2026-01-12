<script>
    var personelAdi = "{{ $arabuluculuk->relatedUser->name ?? $arabuluculuk->personel->name ?? 'Sinan Poyraz' }}";

    function toggleAliciInput() {
        var select = document.getElementById('odeme_alici_tipi');
        var input = document.getElementById('odenecek_kisi_ad_soyad');
        var aciklama = document.getElementById('input_aciklama');

        if (select.value === 'calisan') {
            input.value = personelAdi;
            input.setAttribute('readonly', true);
            input.classList.add('bg-gray-50');
            input.classList.remove('bg-white');
            aciklama.textContent = 'Ödeme varsayılan olarak ilgili personele (' + personelAdi + ') yapılacaktır.';
            aciklama.parentElement.className = "mt-2 flex items-start gap-2 text-xs text-gray-600 bg-blue-50 p-3 rounded-lg border border-blue-100";
        } else {
            input.value = '';
            input.removeAttribute('readonly');
            input.classList.remove('bg-gray-50');
            input.classList.add('bg-white');
            input.focus();
            aciklama.textContent = 'Lütfen alıcının tam adını ve soyadını giriniz.';
            aciklama.parentElement.className = "mt-2 flex items-start gap-2 text-xs text-amber-700 bg-amber-50 p-3 rounded-lg border border-amber-200";
        }
    }
</script>