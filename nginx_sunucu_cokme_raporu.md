# 🚨 Sorun: Nginx "Empty Reply" (Boş Yanıt) Çöküşü

**Tarih:** 9 Haziran 2026
**Belirti:** Sunucudaki tüm siteler (`kys.koksan.com`, İAA, DMS, Filo, Merkezi Yönetim Sistemi) eşzamanlı olarak ulaşılamaz hale geldi. 
* Tarayıcıda: `ERR_EMPTY_RESPONSE`
* API İsteklerinde: `cURL error 52: Empty reply from server`
**Durum:** Sunucu çalışıyor, veritabanı açık, güvenlik duvarı (Firewall) kapalıydı. Ancak sitelere hiç girilemiyordu.

---

## 🔍 Kök Neden (Root Cause)

1. Arka planda Ubuntu sunucusuna otomatik **Çekirdek (Kernel) ve güvenlik kütüphanesi (OpenSSL vb.) güncellemeleri** uygulandı.
2. Nginx web sunucusuna daha önceden bağımlılık olarak yüklenmiş olan **`nginx-extras`** paketi ve içindeki **`headers-more` eklentisi** (`ngx_http_headers_more_filter_module.so`), güncellenen yeni Linux çekirdeğiyle uyumsuzluğa düştü.
3. Bu uyumsuzluk yüzünden, sitelere dışarıdan veya sunucunun kendi içinden (cURL ile) en ufak bir bağlantı isteği geldiği saniye, o isteği karşılayan Nginx Worker işlemcisi **"Signal 11 (Segmentation Fault / Core Dumped)"** vererek şiddetli bir şekilde parçalanıp çöküyordu.
4. Nginx işlemcisi bağlantıyı yanıtlamadan anında öldüğü için karşı tarafa hiçbir HTTP veya HTML verisi gönderemeden bağlantı sıfırlanıyor (TCP RST/FIN), bu da istemci tarafında "Empty Reply" hatasını doğuruyordu.

---

## 🛠️ Çözüm Adımları (Resolution)

1. **Teşhis:** Nginx hata loglarına (`/var/log/nginx/error.log`) bakıldı ve saniye saniye `worker process exited on signal 11` kayıtları görüldü. Çekirdek logları (`dmesg -T`) incelenerek çökmeye sebep olan sorunlu dosyanın tam adresi (`ngx_http_headers_more_filter_module.so`) nokta atışı tespit edildi.
2. **İlk Müdahale:** Modül `apt-get --reinstall` ile güncellenip Nginx konfigürasyonundaki kalıntıları (`more_clear_headers` kodları) `sed` komutlarıyla temizlendi. Fakat Ubuntu depolarındaki güncel eklenti de mevcut Ubuntu kernel yapısıyla uyumsuz çıktı.
3. **Kökten Temizlik (Purge):** Uyumsuz olan `nginx-extras` paketi ve ona bağlı asılı kalan tüm eski eklentiler (GeoIP, Echo vb.) `apt-get autoremove --purge` komutuyla sistemden tamamen sökülüp atıldı.
4. **Zombi İşlem Temizliği:** Sürekli çöktüğü için hafızada takılı kalan ve Nginx'in temiz bir şekilde restart olmasını engelleyen zombi master/worker işlemleri `killall -9 nginx` komutuyla RAM'den zorla temizlendi.
5. **Mutlu Son:** Sistem tüm eklenti çöplüğünden arındırıldıktan sonra `systemctl start nginx` komutuyla saf/çekirdek Nginx ayağa kaldırıldı ve Merkezi Sistem dahil tüm projeler cURL hataları olmadan saniyeler içinde geri geldi.

---

*Not: Bu rapor, gelecekte yaşanabilecek "Empty Reply" veya "Signal 11" hatalarının tamamen 3. parti Nginx eklentisi (module) kaynaklı olduğunu hatırlatmak için oluşturulmuştur.*
