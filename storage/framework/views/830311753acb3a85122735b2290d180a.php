<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Köksan Öneri Sistemi</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 min-h-screen flex flex-col font-sans">

    
    <div
        class="bg-gray-900 border-b border-gray-800 text-white relative overflow-hidden flex-1 flex flex-col items-center justify-center py-20 px-6 sm:px-12 lg:px-24">

        
        <div class="absolute inset-0 bg-cover bg-center opacity-20 mix-blend-overlay"
            style="background-image: url('https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=2301&auto=format&fit=crop');">
        </div>
        <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>

        <div class="relative z-10 w-full max-w-4xl text-center">
            
            <img src="<?php echo e(asset('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png')); ?>" alt="Köksan Logo"
                class="h-24 w-auto mx-auto mb-10 brightness-0 invert drop-shadow-2xl opacity-90 hover:opacity-100 transition-opacity">

            <h1 class="text-5xl md:text-7xl font-black tracking-tight mb-6">
                <span class="block text-white">Kurumsal</span>
                <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">Yönetim
                    Sistemi</span>
            </h1>

            <p class="text-xl md:text-2xl text-gray-300 max-w-2xl mx-auto font-light leading-relaxed mb-12">
                Müşteri şikayetleri yönetimi, disiplin süreçleri ve sürekli iyileştirme önerileri için merkezi yönetim
                sistemi.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="<?php echo e(route('login')); ?>"
                    class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-lg shadow-lg hover:shadow-blue-500/25 transition-all transform hover:-translate-y-1">
                    Sisteme Giriş Yap
                </a>
                <a href="<?php echo e(route('register')); ?>"
                    class="px-8 py-4 bg-gray-800 hover:bg-gray-700 text-white border border-gray-700 rounded-xl font-bold text-lg shadow-lg transition-all transform hover:-translate-y-1">
                    Hesap Oluştur
                </a>
            </div>
        </div>
    </div>

    
    <div class="bg-gray-50 flex-1 py-16 px-6 relative z-20">
        <div class="max-w-7xl mx-auto -mt-32">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

                
                <div
                    class="bg-white p-8 rounded-2xl shadow-xl border border-gray-200 hover:border-orange-200 transition-colors group">
                    <div
                        class="w-14 h-14 bg-orange-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-orange-500 transition-colors duration-300">
                        <svg class="w-8 h-8 text-orange-600 group-hover:text-white transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Şikayet Bildirimi</h3>
                    <p class="text-gray-500 mb-6 leading-relaxed">Ürün veya hizmetlerimizle ilgili yaşadığınız sorunları
                        hızlıca bize iletin.</p>
                    <a href="<?php echo e(route('public.sikayet.create')); ?>"
                        class="inline-flex items-center text-orange-600 font-bold hover:text-orange-700">
                        Bildirim Oluştur <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>

                
                <div
                    class="bg-white p-8 rounded-2xl shadow-xl border border-gray-200 hover:border-emerald-200 transition-colors group">
                    <div
                        class="w-14 h-14 bg-emerald-100 rounded-xl flex items-center justify-center mb-6 group-hover:bg-emerald-500 transition-colors duration-300">
                        <svg class="w-8 h-8 text-emerald-600 group-hover:text-white transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Hızlı Öneri</h3>
                    <p class="text-gray-500 mb-6 leading-relaxed">Şirket süreçlerini iyileştirmek için fikirleriniz
                        bizim için değerli.</p>
                    <a href="<?php echo e(route('guest.iaa.create')); ?>"
                        class="inline-flex items-center text-emerald-600 font-bold hover:text-emerald-700">
                        Fikir Paylaş <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                        </svg>
                    </a>
                </div>

                
                <div
                    class="bg-gray-900 p-8 rounded-2xl shadow-xl border border-gray-700 text-white relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-600 to-purple-600 opacity-20"></div>
                    <div class="relative z-10 h-full flex flex-col justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-blue-200 uppercase tracking-wider mb-2">Toplam Katkı
                            </h3>
                            <p class="text-5xl font-black text-white mb-2"><?php echo e($toplamOneri); ?></p>
                            <p class="text-gray-400">Sisteme girilen toplam öneri ve bildirim sayısı.</p>
                        </div>
                        <div class="pt-8 mt-auto">
                            <p class="text-sm text-gray-400">Son İşlem:</p>
                            <p class="font-mono text-emerald-400">
                                <?php echo e($sonOneri ? $sonOneri->created_at->diffForHumans() : '-'); ?></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <footer class="max-w-7xl mx-auto mt-20 text-center text-gray-400 text-sm">
            <p>&copy; <?php echo e(date('Y')); ?> Köksan A.Ş. Tüm hakları saklıdır. | Köksan Bilgi Teknolojileri</p>
        </footer>
    </div>
</body>

</html><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/welcome.blade.php ENDPATH**/ ?>