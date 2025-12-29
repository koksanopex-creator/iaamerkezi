<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Köksan Öneri Sistemi</title>
    
    
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .float-animation { animation: float 6s ease-in-out infinite; }
        .float-animation:nth-child(2) { animation-delay: -2s; }
        .float-animation:nth-child(3) { animation-delay: -4s; }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
        }
        
        /* Griden beyaza doğru yumuşak geçişli arkaplan */
        .gradient-bg {
            background: linear-gradient(135deg, #f1f5f9 0%, #ffffff 100%); /* Tailwind slate-100'den white'a */
        }
        
        /* Butonlar için hover efekti */
        .btn-hover-effect {
            transition: all 0.3s ease;
        }
        .btn-hover-effect:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body class="gradient-bg min-h-screen">

    
    <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
        <div class="float-animation absolute top-20 left-10 w-64 h-64 bg-blue-100/50 rounded-full opacity-50 blur-2xl"></div>
        <div class="float-animation absolute top-40 right-10 w-80 h-80 bg-indigo-100/50 rounded-full opacity-50 blur-2xl"></div>
        <div class="float-animation absolute bottom-20 left-1/3 w-72 h-72 bg-purple-100/50 rounded-full opacity-50 blur-2xl"></div>
    </div>

    
    <div class="relative min-h-screen flex flex-col items-center justify-center p-4 sm:p-6 z-10">
        
        <div class="bg-white/80 backdrop-blur-xl w-full max-w-4xl p-8 sm:p-10 text-center rounded-3xl shadow-2xl border border-gray-200/80">
            
            
            <div class="mb-6">
                <img src="<?php echo e(asset('storage/logos/2mIKZO0DYbIDjSJdjfN1IpO7jkTqEcSOh886xYH5.png')); ?>" 
                     alt="Köksan Logo" 
                     class="h-16 sm:h-20 w-auto mx-auto drop-shadow-sm">
            </div>
            
            
            <div class="mb-8 text-center relative">
                <!-- Arka plan dekoratif elementler -->
                <div class="absolute inset-0 -z-10">
                    <div class="absolute top-0 left-1/4 w-32 h-32 bg-gradient-to-r from-blue-400/20 to-indigo-400/20 rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute -top-4 right-1/3 w-24 h-24 bg-gradient-to-r from-purple-400/20 to-pink-400/20 rounded-full blur-2xl animate-pulse" style="animation-delay: 1s;"></div>
                </div>
                
                <!-- Ana başlık -->
                <div class="relative group mb-4">
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black leading-tight">
                        <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent transition-all duration-500 group-hover:from-purple-600 group-hover:via-blue-600 group-hover:to-indigo-600">
                            Köksan Öneri Sistemi
                        </span>
                    </h1>
                    
                    <!-- Alt çizgi efekti -->
                    <div class="mt-3 mx-auto w-20 h-1 bg-gradient-to-r from-blue-500 via-indigo-500 to-purple-500 rounded-full opacity-60 group-hover:w-28 group-hover:opacity-100 transition-all duration-700"></div>
                </div>
                
                <!-- Açıklama metni -->
                <div class="relative max-w-2xl mx-auto">
                    <p class="text-base sm:text-lg text-gray-600 leading-relaxed font-medium opacity-90 transition-all duration-300 hover:opacity-100 hover:text-gray-700">
                        <span class="inline-block bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent font-semibold">Fikirlerinizi değere dönüştürün.</span>
                        <br class="sm:hidden">
                        <span class="mt-2 sm:mt-0 inline-block">Süreçlerimizi iyileştirmek için bize katılın.</span>
                    </p>
                    
                    <!-- Dekoratif noktalar -->
                    <div class="flex justify-center mt-4 space-x-2">
                        <div class="w-2 h-2 bg-blue-400 rounded-full animate-bounce"></div>
                        <div class="w-2 h-2 bg-indigo-400 rounded-full animate-bounce" style="animation-delay: 0.1s;"></div>
                        <div class="w-2 h-2 bg-purple-400 rounded-full animate-bounce" style="animation-delay: 0.2s;"></div>
                    </div>
                </div>
                
                <!-- Subtle glow effect -->
                <div class="absolute inset-x-0 top-1/2 -translate-y-1/2 h-px bg-gradient-to-r from-transparent via-blue-200 to-transparent opacity-30"></div>
            </div>

            
            <div class="space-y-4 mb-8">
                <div class="flex flex-col sm:flex-row gap-3 justify-center max-w-md mx-auto">
                    <a href="<?php echo e(route('login')); ?>" 
                       class="btn-hover-effect w-full flex-1 inline-flex items-center justify-center bg-gradient-to-r from-indigo-600 to-blue-500 text-white font-semibold py-3 px-6 rounded-xl shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"></path></svg>
                        Giriş Yap
                    </a>
                    <a href="<?php echo e(route('register')); ?>" 
                       class="btn-hover-effect w-full flex-1 inline-flex items-center justify-center bg-white text-gray-700 font-semibold py-3 px-6 rounded-xl shadow-md border border-gray-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3"></path></svg>
                        Kayıt Ol
                    </a>
                </div>
                
                
                <div class="mt-4 text-center">
                    <a href="<?php echo e(route('public.sikayet.create')); ?>" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-red-500 to-orange-500 hover:from-red-600 hover:to-orange-600 rounded-lg font-semibold text-white shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        Müşteri Şikayeti Bildir
                    </a>
                </div>
            

                <div class="pt-4 border-t border-gray-200/80">
                    <a href="<?php echo e(route('guest.iaa.create')); ?>" 
                       class="btn-hover-effect w-full max-w-sm mx-auto inline-flex items-center justify-center bg-gradient-to-r from-green-500 to-emerald-500 text-white font-medium py-3 px-4 rounded-xl shadow-lg text-sm">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        veya Hızlıca Öneri Bırak
                    </a>
                </div>
            </div>

            

            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-2xl mx-auto">
                
                <!-- Toplam Öneri Kartı -->
                <div class="relative group">
                    <div class="absolute -inset-1.5 bg-gradient-to-r from-indigo-400 via-blue-400 to-purple-400 rounded-3xl blur opacity-60 group-hover:opacity-90 transition-all duration-700 group-hover:duration-300 animate-pulse"></div>
                    <div class="relative bg-white/90 backdrop-blur-sm p-6 rounded-3xl shadow-xl border border-white/20 h-full flex flex-col justify-center transition-all duration-500 group-hover:scale-105 group-hover:shadow-2xl">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-widest mb-3 opacity-80">Toplam Öneri</p>
                        <p class="text-4xl font-black bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 bg-clip-text text-transparent leading-tight">
                            <?php echo e($toplamOneri); ?>

                        </p>
                        <div class="mt-3 h-1 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full opacity-30 group-hover:opacity-60 transition-opacity duration-300"></div>
                    </div>
                </div>

                <!-- Son Gelen Öneri Kartı -->
                <div class="relative group">
                    <div class="absolute -inset-1.5 bg-gradient-to-r from-emerald-400 via-green-400 to-teal-400 rounded-3xl blur opacity-60 group-hover:opacity-90 transition-all duration-700 group-hover:duration-300 animate-pulse"></div>
                    <div class="relative bg-white/90 backdrop-blur-sm p-6 rounded-3xl shadow-xl border border-white/20 h-full flex flex-col justify-center transition-all duration-500 group-hover:scale-105 group-hover:shadow-2xl">
                        <div class="flex items-center justify-between mb-3">
                            <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-widest mb-3 opacity-80">Son Gelen Öneri</p>
                        <?php if($sonOneri): ?>
                            <p class="text-2xl font-bold bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 bg-clip-text text-transparent leading-tight">
                                <?php echo e($sonOneri->created_at->diffForHumans()); ?>

                            </p>
                        <?php else: ?>
                            <p class="text-lg font-semibold text-gray-500 opacity-70">Henüz hiç öneri yok</p>
                        <?php endif; ?>
                        <div class="mt-3 h-1 bg-gradient-to-r from-green-500 to-emerald-500 rounded-full opacity-30 group-hover:opacity-60 transition-opacity duration-300"></div>
                    </div>
                </div>

            </div>
        </div>
        
        
        <footer class="w-full text-center py-6 mt-4">
             <p class="text-sm text-gray-500">
                © <?php echo e(date('Y')); ?> Köksan. Her fikir değerlidir.
            </p>
        </footer>
    </div>
</body>
</html><?php /**PATH C:\Users\celal.karaman\Desktop\Projelerim\iaa_projesi\resources\views/welcome.blade.php ENDPATH**/ ?>