document.addEventListener('DOMContentLoaded', function () {

    // 👉 Bu sayfada bildirim config'i yoksa HİÇBİR ŞEY yapma
    if (!window.notificationApiUrls) {
        return;
    }

    // HTML'den gerekli elemanları seç
    const bellIcon = document.getElementById('notification-bell-icon');
    const dropdown = document.getElementById('notification-dropdown');
    const countBadge = document.getElementById('notification-count-badge');
    const notificationList = document.getElementById('notification-list');
    const emptyMessage = document.getElementById('notification-empty');
    const headerContainer = document.getElementById('notification-header-container'); // YENİ
    const footerContainer = document.getElementById('notification-footer-container'); // YENİ

    // 👉 Bu elemanlar yoksa (guest sayfalar vs.) yine çık
    if (!bellIcon || !dropdown || !countBadge || !notificationList || !emptyMessage) {
        return;
    }

    // ... (csrfToken ve window.notificationApiUrls kısımları aynı)
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        .getAttribute('content');

    const {
        index: notificationsUrl,
        unreadCount: unreadCountUrl,
        markAsRead: markAsReadUrl
    } = window.notificationApiUrls;
    let areNotificationsFetched = false;
    let isDropdownOpen = false;

    // --- YARDIMCI: Header ve Footer Render ---
    function renderLayout() {
        // Header
        headerContainer.innerHTML = `
            <div class="notification-header">
                <span class="notification-header-title">Bildirimler</span>
                <a href="#" id="mark-all-read-btn" class="notification-mark-read">T\u00fcm\u00fcn\u00fc Okundu \u0130\u015faretle</a>
            </div>
        `;
        
        // Footer (Ayarlar Kaldırıldı)
        const baseUrl = window.livewire_app_url || '';
        footerContainer.innerHTML = `
            <div class="notification-footer">
                <a href="${baseUrl}/notifications" class="notification-footer-link">T\u00fcm Bildirimleri G\u00f6r</a>
            </div>
        `;

        // Tümünü Okundu İşaretle
        const markBtn = document.getElementById('mark-all-read-btn');
        if (markBtn) {
            markBtn.addEventListener('click', function(e) {
                e.preventDefault();
                markAsRead();
                updateCountBadge(0);
                document.querySelectorAll('.notification-item-unread').forEach(el => {
                    el.classList.remove('notification-item-unread');
                });
            });
        }
    }

    renderLayout();

    // 1. Okunmamış Sayısını Çekme
    function fetchUnreadCount() {
        fetch(unreadCountUrl, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (response.status === 401 || response.status === 419) return null;
            return response.json();
        })
        .then(data => {
            if (data) {
                updateCountBadge(data.count);
            }
        })
        .catch(error => console.error('Bildirim sayısı alınamadı:', error));
    }

    // 2. Sayaç Badge'ini Güncelle
    function updateCountBadge(count) {
        if (count > 0) {
            countBadge.textContent = count;
            countBadge.style.display = 'inline-flex';
        } else {
            countBadge.style.display = 'none';
        }
    }

    // 4. Bireysel Durum Değiştirme (GÜNCELLENDİ: Okundu yapınca SİL)
    function toggleSingleStatus(id, dotElement, linkElement) {
        const baseUrl = window.livewire_app_url || '';
        const toggleUrl = `${baseUrl}/notifications/${id}/toggle`;

        fetch(toggleUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                if (data.new_status === 'unread') {
                    // Tekrar okunmadı yapıldıysa (zaten zilde durmalı)
                    linkElement.classList.add('notification-item-unread');
                    dotElement.style.background = '#3b82f6';
                    dotElement.style.borderColor = '#3b82f6';
                } else {
                    // OKUNDU yapıldıysa - ZİLDEN SİL
                    const wrapper = linkElement.closest('.notification-item-wrapper');
                    if (wrapper) {
                        wrapper.style.transition = 'all 0.3s ease';
                        wrapper.style.opacity = '0';
                        wrapper.style.transform = 'translateX(20px)';
                        setTimeout(() => {
                            wrapper.remove();
                            // Eğer hiç bildirim kalmadıysa boş durum mesajını göster
                            if (notificationList.children.length === 0) {
                                emptyMessage.style.display = 'block';
                            }
                        }, 300); // 300ms animasyon sonrası sil
                    }
                }
                updateCountBadge(data.unread_count);
            }
        })
        .catch(error => console.error('Durum değiştirilemedi:', error));
    }

    // 3. Zile Tıklandığında
    bellIcon.addEventListener('click', function (e) {
        e.preventDefault();
        isDropdownOpen = !isDropdownOpen;
        dropdown.style.display = isDropdownOpen ? 'block' : 'none';

        if (isDropdownOpen) {
            fetchNotifications();
        }
    });

    // 5. Bildirimleri Çek ve Listeyi Doldur (GÜNCELLENDİ)
    function fetchNotifications() {
        notificationList.innerHTML = `
            <div style="padding:20px; text-align:center; color:#94a3b8;">
                <svg class="animate-spin h-5 w-5 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </div>
        `;

        fetch(notificationsUrl, {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            notificationList.innerHTML = '';

            if (data.notifications && data.notifications.length > 0) {
                data.notifications.forEach(notification => {
                    const li = document.createElement('li');
                    
                    const serverLink = notification.data.url || notification.data.link || notification.data.action_url;
                    const href = serverLink ? serverLink : (notification.data.iaa_id ? `/proje-calisma-alani/${notification.data.iaa_id}` : '#');
                    
                    const isUnread = !notification.read_at;
                    const dateStr = notification.created_at || new Date().toISOString();
                    const date = new Date(dateStr);
                    const formattedDate = date.toLocaleString('tr-TR', {
                        day: '2-digit', month: '2-digit', year: 'numeric',
                        hour: '2-digit', minute: '2-digit'
                    });

                    const baseUrl = window.livewire_app_url || '';
                    li.innerHTML = `
                        <div class="notification-item-wrapper" style="display: flex; align-items: center; border-bottom: 1px solid #f1f5f9; background: white; transition: background 0.2s;">
                            <a href="${baseUrl}/notifications/${notification.id}/read" class="notification-item-link ${isUnread ? 'notification-item-unread' : ''}" style="flex: 1; border-bottom: none; display: flex; align-items: center; padding: 12px 16px; text-decoration: none;">
                                <div class="notification-item-content">
                                    <span class="notification-item-title" style="display: block; font-size: 0.875rem; color: #1e293b; ${isUnread ? 'font-weight: 700;' : 'font-weight: 500;'}">${notification.data.message}</span>
                                    <span class="notification-item-time" style="font-size: 0.75rem; color: #64748b;">${formattedDate}</span>
                                </div>
                            </a>
                            <div class="notification-action-zone" style="padding: 0 15px; display: flex; align-items: center;">
                                <button type="button" class="status-toggle-btn" 
                                    data-id="${notification.id}" 
                                    style="width: 20px; height: 20px; border-radius: 50%; border: 2px solid ${isUnread ? '#3b82f6' : '#cbd5e1'}; background: ${isUnread ? '#3b82f6' : 'transparent'}; cursor: pointer; transition: all 0.2s; padding: 0;"
                                    title="${isUnread ? 'Okundu işaretle' : 'Okunmadı işaretle'}">
                                    <div class="inner-dot" style="width: 6px; height: 6px; background: white; border-radius: 50%; margin: 5px auto; display: ${isUnread ? 'block' : 'none'};"></div>
                                </button>
                            </div>
                        </div>
                    `;

                    notificationList.appendChild(li);

                    // Butona tıklama olayı
                    const toggleBtn = li.querySelector('.status-toggle-btn');
                    const link = li.querySelector('.notification-item-link');
                    toggleBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        // Backend toggle 
                        toggleSingleStatus(notification.id, toggleBtn, link);
                    });
                });
            } else {
                emptyMessage.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Bildirimler alınamadı:', error);
            notificationList.innerHTML = '<div style="padding:20px; text-align:center; color:#ef4444; font-size:0.8rem;">Yükleme hatası!</div>';
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
                if (data.status !== 'success') {
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
    document.addEventListener('click', function (e) {
        const container = document.querySelector('.notification-container');
        if (container && !container.contains(e.target)) {
            dropdown.style.display = 'none';
            isDropdownOpen = false;
        }
    });
});