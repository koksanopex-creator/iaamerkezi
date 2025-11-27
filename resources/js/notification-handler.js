document.addEventListener('DOMContentLoaded', function() {

    // 👉 Bu sayfada bildirim config'i yoksa HİÇBİR ŞEY yapma
    if (!window.notificationApiUrls) {
        return;
    }

    // HTML'den gerekli elemanları seç
    const bellIcon        = document.getElementById('notification-bell-icon');
    const dropdown        = document.getElementById('notification-dropdown');
    const countBadge      = document.getElementById('notification-count-badge');
    const notificationList= document.getElementById('notification-list');
    const emptyMessage    = document.getElementById('notification-empty');

    // 👉 Bu elemanlar yoksa (guest sayfalar vs.) yine çık
    if (!bellIcon || !dropdown || !countBadge || !notificationList || !emptyMessage) {
        return;
    }

    // Ana layout'a eklediğimiz global değişkenlerden verileri al
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content');

    const {
        index: notificationsUrl,
        unreadCount: unreadCountUrl,
        markAsRead: markAsReadUrl
    } = window.notificationApiUrls;
    let areNotificationsFetched = false; // Listeyi tekrar tekrar çekmemek için
    let isDropdownOpen = false;

    // 1. Okunmamış Sayısını Çekme Fonksiyonu
    function fetchUnreadCount() {
        // --- DEĞİŞİKLİK BAŞLANGICI ---
        fetch(unreadCountUrl, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // <-- BU SATIR YÖNLENDİRME HATASINI ÇÖZER
            }
        })
        // --- DEĞİŞİKLİK SONU ---
            .then(response => {
                // Eğer oturum düşmüşse işlemi durdur (Yönlendirme yapmasın)
                if (response.status === 401 || response.status === 419) return null;
                return response.json();
            })
            .then(data => {
                if (data) { // Veri varsa güncelle
                    updateCountBadge(data.count);
                }
            })
            .catch(error => console.error('Bildirim sayısı alınamadı:', error));
    }

    // 2. Sayaç Badge'ini Güncelle
    function updateCountBadge(count) {
        if (count > 0) {
            countBadge.textContent = count;
            countBadge.style.display = 'inline-flex'; // CSS'teki .notification-badge stili
        } else {
            countBadge.style.display = 'none';
        }
    }

    // 3. Zile Tıklandığında (Güncellendi)
    bellIcon.addEventListener('click', function(e) {
        e.preventDefault();
        
        isDropdownOpen = !isDropdownOpen;
        dropdown.style.display = isDropdownOpen ? 'block' : 'none';

        if (isDropdownOpen) {
            // Hem listeyi çek hem de sayacı sıfırla (eğer okunmamış varsa)
            fetchNotifications(); 
            if (parseInt(countBadge.textContent) > 0) {
                markAsRead(); // API'ye "okundu" bilgisini gönder
                updateCountBadge(0); // Sayacı anında sıfırla
            }
        }
    });

    // 4. Bildirimleri Çek ve Listeyi Doldur (Güncellendi)
    function fetchNotifications() {
        notificationList.innerHTML = '<li class="notification-empty-message">Yükleniyor...</li>';
        emptyMessage.style.display = 'none';
        areNotificationsFetched = true; 

        fetch(notificationsUrl, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest' // <-- BURAYA DA EKLİYORUZ
            }
        }) // Bu API artık bize son 5 bildirimi getiriyor
            .then(response => response.json())
            .then(data => {
                notificationList.innerHTML = ''; // Listeyi temizle
                
                if (data.notifications && data.notifications.length > 0) {
                    
                    data.notifications.forEach(notification => {
                        const li = document.createElement('li');
                        const a = document.createElement('a');
                        // Hem action_url'e hem de link'e bak, hangisi varsa onu kullan.
                        a.href = notification.data.action_url || notification.data.link || '#';

                        // --- YENİ EKLENEN KISIM: Tarih/Saat ---
                        const dateStr = notification.created_at || new Date().toISOString();
                        const date = new Date(dateStr);
                        // Türkiye formatına (GG.AA.YYYY SS:DD) çevir
                        const formattedDate = date.toLocaleString('tr-TR', { 
                            day: '2-digit', 
                            month: '2-digit', 
                            year: 'numeric', 
                            hour: '2-digit', 
                            minute: '2-digit' 
                        });
                        
                        // Bildirim metnini ve tarihi HTML olarak ayarla
                        a.innerHTML = `
                            <span style="display: block;">${notification.data.message}</span>
                            <small style="color: #6B7280; font-size: 0.75rem;">${formattedDate}</small>
                        `;
                        
                        // Eğer bildirim OKUNMAMIŞSA (read_at = null), bold yap
                        if (!notification.read_at) {
                            a.style.fontWeight = '700'; // bold
                        }
                        // --- EKLEME SONU ---

                        li.appendChild(a);
                        notificationList.appendChild(li);
                    });
                    
                } else {
                    emptyMessage.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Bildirimler alınamadı:', error);
                notificationList.innerHTML = '<li class="notification-empty-message" style="color: #EF4444;">Hata oluştu.</li>';
            });
    }

    // 5. Okundu Olarak İşaretle (Sunucuda)
    function markAsRead() {
        fetch(markAsReadUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if(data.status !== 'success') {
                console.error('Okundu olarak işaretlenemedi.');
            }
        })
        .catch(error => console.error('Okundu işaretleme hatası:', error));
    }

    // 6. Sayfa Yüklendiğinde sayacı al
    fetchUnreadCount();

    // 7. Her 30 saniyede bir yeni bildirim sayısını kontrol et (Polling)
    setInterval(() => {
        // Dropdown açıksa polling yapma, kullanıcı zaten görüyor
        if (!isDropdownOpen) {
            fetchUnreadCount();
            areNotificationsFetched = false; // Kapalıyken 'çekilmedi' olarak resetle ki tıklayınca güncel listeyi alsın
        }
    }, 30000); // 30 saniye

    // 8. Dışarıya tıklayınca dropdown'u kapat
    document.addEventListener('click', function(e) {
        const container = document.querySelector('.notification-container');
        if (container && !container.contains(e.target)) {
            dropdown.style.display = 'none';
            isDropdownOpen = false;
        }
    });
});