<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
                <div class="bg-indigo-100 p-2 rounded-lg text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                {{ __('Yeni Arabulucu Kaydı') }}
            </h2>
            <a href="{{ route('admin.arabulucular.index') }}" class="text-gray-500 hover:text-gray-700 font-medium text-sm flex items-center transition">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Listeye Dön
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                {{-- SOL KOLON: KAYIT FORMU --}}
                <div class="lg:col-span-2">
                    <div class="bg-white p-8 shadow-lg rounded-xl border border-gray-100">
                        
                        <div class="mb-6 border-b border-gray-100 pb-4">
                            <h3 class="text-lg font-bold text-gray-800">Kişisel Bilgiler</h3>
                            <p class="text-sm text-gray-500">Arabulucunun resmi sicil ve iletişim bilgilerini giriniz.</p>
                        </div>

                        {{-- Hata Mesajları --}}
                        @if ($errors->any())
                            <div class="mb-4 bg-red-50 border-l-4 border-red-500 p-4 rounded">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-red-700 font-bold">Lütfen aşağıdaki hataları düzeltin:</p>
                                        <ul class="mt-1 list-disc list-inside text-sm text-red-600">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('admin.arabulucular.store') }}" method="POST">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                {{-- Ad Soyad --}}
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ad Soyad <span class="text-red-500">*</span></label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        </div>
                                        <input type="text" name="name" value="{{ old('name') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" placeholder="Örn: Ahmet Yılmaz" required>
                                    </div>
                                </div>

                                {{-- Sicil No --}}
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Sicil No <span class="text-red-500">*</span></label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/></svg>
                                        </div>
                                        <input type="text" name="sicil_no" value="{{ old('sicil_no') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" placeholder="Örn: 12345" required>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-400">Benzersiz bir numara olmalıdır.</p>
                                </div>

                                {{-- E-Posta --}}
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">E-Posta Adresi</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                                        </div>
                                        <input type="email" name="email" value="{{ old('email') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" placeholder="ornek@email.com">
                                    </div>
                                </div>

                                {{-- Telefon --}}
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Telefon</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        </div>
                                        <input type="text" name="telefon" value="{{ old('telefon') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" placeholder="05XX XXX XX XX">
                                    </div>
                                </div>

                                {{-- Şehir --}}
                                <div class="col-span-2 md:col-span-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Şehir / İl <span class="text-red-500">*</span></label>
                                    <div class="relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </div>
                                        <input type="text" name="sehir" value="{{ old('sehir') }}" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-2" placeholder="Örn: İstanbul" required>
                                    </div>
                                </div>

                                {{-- Adres (Full Width) --}}
                                <div class="col-span-2">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Açık Adres</label>
                                    <textarea name="adres" rows="3" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="Ofis adresi vb.">{{ old('adres') }}</textarea>
                                </div>

                            </div>

                            <div class="mt-8 flex justify-end gap-3">
                                <a href="{{ route('admin.arabulucular.index') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Vazgeç
                                </a>
                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Kaydet ve Listeye Dön
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- SAĞ KOLON: BİLGİLENDİRME PANELİ --}}
                <div class="lg:col-span-1">
                    <div class="space-y-6">
                        
                        {{-- Bilgi Kartı 1 --}}
                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-6">
                            <h4 class="font-bold text-indigo-900 flex items-center gap-2 mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Kayıt Hakkında
                            </h4>
                            <p class="text-sm text-indigo-800 leading-relaxed">
                                Ekleyeceğiniz arabulucu, sisteme kayıt edildikten sonra <strong>Zorunlu</strong> veya <strong>İhtiyari</strong> arabuluculuk dosyalarına atanabilir duruma gelecektir.
                            </p>
                            <div class="mt-4 pt-4 border-t border-indigo-200">
                                <p class="text-xs font-bold text-indigo-500 uppercase">Önemli Hatırlatma</p>
                                <p class="text-xs text-indigo-700 mt-1">
                                    Sicil numarası, UYAP sistemindeki numara ile birebir aynı olmalıdır. Bu numara üzerinden dosya takibi yapılacaktır.
                                </p>
                            </div>
                        </div>

                        {{-- İstatistik Görünümlü Bilgi --}}
                        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                            <h4 class="font-bold text-gray-800 mb-4">Sistem Durumu</h4>
                            
                            <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-100">
                                <span class="text-sm text-gray-500">Sistem Erişimi</span>
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Aktif</span>
                            </div>
                            
                            <div class="flex items-center justify-between mb-3 pb-3 border-b border-gray-100">
                                <span class="text-sm text-gray-500">Kayıt Türü</span>
                                <span class="text-sm font-bold text-gray-700">Dış Paydaş (Arabulucu)</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-500">Yetkilendirme</span>
                                <span class="text-sm font-bold text-gray-700">Otomatik</span>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>