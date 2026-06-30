@push('pageTitle')
    Mavi Yaka İçe Aktar | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mavi Yaka Personel İçe Aktar') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Ana Sayfa</a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                <a href="{{ route('admin.mavi-yaka.index') }}" class="ml-1 text-gray-700 hover:text-indigo-600 font-medium md:ml-2">Mavi Yaka Personel</a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                <span class="ml-1 text-gray-500 md:ml-2">Toplu İçe Aktar</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">Toplu Personel İçe Aktar</h1>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8 p-4 bg-indigo-50 rounded-2xl border border-indigo-100">
                    <div>
                        <h3 class="text-indigo-900 font-bold mb-1">İmport Taslak Belgesi</h3>
                        <p class="text-indigo-700 text-sm">Sisteme yüklemeden önce doğru formatı kullandığınızdan emin olun.</p>
                    </div>
                    <a href="{{ route('admin.mavi-yaka.download-template') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-xl hover:bg-indigo-700 shadow-sm transition-all shrink-0">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        Taslak Belgesini İndir
                    </a>
                </div>

                <form action="{{ route('admin.mavi-yaka.import-preview') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="p-6 border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50 hover:bg-white hover:border-indigo-300 transition-all cursor-pointer group relative">
                        <input type="file" name="file" id="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required accept=".csv,.xlsx">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 group-hover:text-indigo-500 transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="mt-4 flex text-sm text-gray-600 justify-center">
                                <span class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    Dosya Seçin
                                </span>
                                <p class="pl-1">veya sürükleyip bırakın</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Sadece CSV veya XLSX (Maks. 5MB)</p>
                            <div id="file-name" class="mt-2 text-sm font-bold text-indigo-600 hidden"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative group cursor-pointer">
                            <input type="radio" name="mode" value="add" class="sr-only peer" checked>
                            <div class="flex p-4 border-2 border-gray-100 rounded-2xl bg-white peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-indigo-600 peer-checked:bg-indigo-600 flex-shrink-0 relative">
                                        <div class="absolute inset-1 bg-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <span class="block font-bold text-gray-900">Sadece Yeni Kayıtlar</span>
                                        <span class="block text-xs text-gray-500">Mevcut personelleri atlar, sadece yenileri ekler.</span>
                                    </div>
                                </div>
                            </div>
                        </label>
                        <label class="relative group cursor-pointer">
                            <input type="radio" name="mode" value="update" class="sr-only peer">
                            <div class="flex p-4 border-2 border-gray-100 rounded-2xl bg-white peer-checked:border-indigo-600 peer-checked:bg-indigo-50 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-indigo-600 peer-checked:bg-indigo-600 flex-shrink-0 relative">
                                        <div class="absolute inset-1 bg-white rounded-full"></div>
                                    </div>
                                    <div>
                                        <span class="block font-bold text-gray-900">Güncelle ve Ekle</span>
                                        <span class="block text-xs text-gray-500">Mevcut personelleri günceller, olmayanları ekler.</span>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t">
                        <a href="{{ route('admin.mavi-yaka.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50">İptal</a>
                        <button type="submit" class="px-6 py-2 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 shadow-sm transition-all">Dosyayı Analiz Et</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('file').addEventListener('change', function(e) {
            const fileName = e.target.files[0] ? e.target.files[0].name : '';
            const display = document.getElementById('file-name');
            if(fileName) {
                display.textContent = 'Seçilen Dosya: ' + fileName;
                display.classList.remove('hidden');
            } else {
                display.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>
