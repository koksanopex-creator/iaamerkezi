<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class BlockCustomerAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Eğer giriş yapmış kullanıcı bir Müşteri ise (customer_id doluysa)
        if (Auth::check() && Auth::user()->customer_id) {

            // Sadece izin verilen rotalar (Beyaz Liste)
            $allowedRoutes = [
                'dashboard',
                'profile.edit',
                'profile.update',
                'profile.destroy',
                'logout',
                'musteri.profil.show', // Kendi şirket profili
                'admin.sikayetler.create', // Şikayet ekleme
                'admin.sikayetler.store',
                'admin.sikayetler.show', // Şikayet detayı
                'iaa.sikayetler.create', // Yeni Portal Rotaları
                'iaa.sikayetler.show',
                'public.sikayet.create', // Gerekirse public form
                'notifications.index', // Bildirimleri çekme
                'notifications.unreadCount', // Sayaç polling
                'notifications.markAsRead',
                'notifications.toggleStatus',
                'notifications.readAndRedirect'
            ];

            // Şu anki rota ismi izin verilenlerden biri değilse
            if (!in_array($request->route()->getName(), $allowedRoutes)) {
                // Anasayfaya (Dashboard'a) geri at ve hata ver
                return redirect()->route('dashboard')->with('error', 'Bu sayfaya erişim yetkiniz yok.');
            }
        }

        // Eğer giriş yapmış kullanıcı Mavi Yaka ise
        if (Auth::check() && Auth::user()->isMaviYaka()) {

            // Sadece izin verilen rotalar (Beyaz Liste)
            $maviYakaAllowedRoutes = [
                'dashboard',
                'profile.edit',
                'profile.update',
                'profile.destroy',
                'logout',
                'iaa.havuz', // İAA havuzu
                'iaa.havuzTake', // İAA havuzundan alma vs varsa
                'iaa.havuz_take',
                'iaa.havuzaBirak',
                'iaa.gonder_havuz',
                'takimlar.davetlerim', // Takım davetleri
                'takimlar.daveti_cevapla',
                'iaa.takimProjeleri',
                'takimlar.index', // Takım işlemleri için
                'takimlar.isteklerim',
                'iaa.index',
                'iaa.create',
                'iaa.store',
                'iaa.show',
                'notifications.index',
                'notifications.unreadCount',
                'notifications.markAsRead',
                'notifications.toggleStatus',
                'notifications.readAndRedirect'
            ];

            // Rota isimleri wild-card veya birebir eşleşme
            $currentRouteName = $request->route()->getName();
            $isAllowed = false;

            // Birebir eşleşenler
            if (in_array($currentRouteName, $maviYakaAllowedRoutes)) {
                $isAllowed = true;
            }

            // Disiplin detayına izin ver (Görüntüleme)
            if ($currentRouteName === 'admin.disiplin.show') {
                $isAllowed = true; // Sadece kendi dosyasını Controller içinde kısıtlıyor olacağız
            }
            // Disiplin savunma formuna izin ver
            if (str_starts_with($currentRouteName, 'admin.disiplin.savunma')) {
                $isAllowed = true;
            }

            // Arabuluculuk tarafında da sadece public veya kendine açık yerler

            if (!$isAllowed && $currentRouteName !== null) {
                // Anasayfaya (Dashboard'a) geri at ve hata ver
                return redirect()->route('dashboard')->with('error', 'Mavi Yaka statüsündeki personellerin bu sayfaya erişim yetkisi yoktur.');
            }
        }

        return $next($request);
    }
}