<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Bölümü Düzenle: {{ $bolum->ad }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    {{-- ======================== YENİ BÖLÜM EKLEME FORMU ======================== --}}
                    {{-- HATA BURADAYDI: 'bolumler' yerine 'bolum' olmalı --}}
                    @php
                        $isSuperadmin = Auth::user()->hasRole('Superadmin');
                    @endphp

                    <form action="{{ route('admin.bolumler.update', ['bolum' => $bolum]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        {{-- Bölüm Adı --}}
                        <div class="mb-4">
                            <label for="ad" class="block text-gray-700 text-sm font-bold mb-2">Bölüm Adı:</label>
                            <input type="text" name="ad" id="ad" value="{{ old('ad', $bolum->ad) }}" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ !$isSuperadmin ? 'bg-gray-100 cursor-not-allowed' : '' }}" 
                                {{ !$isSuperadmin ? 'readonly' : '' }} required>
                        </div>
                        
                        {{-- Kategori --}}
                        <div class="mb-4">
                            <label for="bolum_kategori_id" class="block text-gray-700 text-sm font-bold mb-2">Kategori:</label>
                            <select name="bolum_kategori_id" id="bolum_kategori_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ !$isSuperadmin ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ !$isSuperadmin ? 'disabled' : '' }}>
                                <option value="">Kategori Seçiniz (Opsiyonel)</option>
                                @foreach($kategoriler as $kategori)
                                    <option value="{{ $kategori->id }}" {{ old('bolum_kategori_id', $bolum->bolum_kategori_id) == $kategori->id ? 'selected' : '' }}>
                                        {{ $kategori->ad }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Direktör --}}
                        <div class="mb-4">
                            <label for="director_id" class="block text-gray-700 text-sm font-bold mb-2">Direktör (İsteğe Bağlı):</label>
                            <select name="director_id" id="director_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ !$isSuperadmin ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ !$isSuperadmin ? 'disabled' : '' }}>
                                <option value="">Direktör Seçiniz (Yok)</option>
                                @foreach($directors as $director)
                                    <option value="{{ $director->id }}" {{ old('director_id', $bolum->director_id) == $director->id ? 'selected' : '' }}>
                                        {{ $director->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Logo --}}
                        <div class="mb-4">
                            <label for="logo_yolu" class="block text-gray-700 text-sm font-bold mb-2">Bölüm Logosu:</label>
                             @if($bolum->logo_yolu)
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $bolum->logo_yolu) }}" alt="Mevcut Logo" class="h-16 w-16 object-cover rounded">
                                </div>
                            @endif
                            <input type="file" name="logo_yolu" id="logo_yolu" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ !$isSuperadmin ? 'cursor-not-allowed' : '' }}" {{ !$isSuperadmin ? 'disabled' : '' }}>
                        </div>

                        {{-- Makine Yönetimi Var mı? --}}
                        <div class="mb-4">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="has_machines" value="1" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" 
                                    {{ old('has_machines', $bolum->has_machines) ? 'checked' : '' }}
                                    {{ !$isSuperadmin ? 'disabled' : '' }}>
                                <span class="ml-2 text-gray-700 font-bold">Bu bölümde makine yönetimi yapılsın mı?</span>
                            </label>
                             @if(!$isSuperadmin && $bolum->has_machines)
                                <input type="hidden" name="has_machines" value="1">
                            @endif
                        </div>

                        {{-- Durum --}}
                        <div class="mb-4">
                            <label for="is_active" class="block text-gray-700 text-sm font-bold mb-2">Durum:</label>
                            <select name="is_active" id="is_active" 
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline {{ !$isSuperadmin ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                {{ !$isSuperadmin ? 'disabled' : '' }}>
                                <option value="1" @selected(old('is_active', $bolum->is_active) == 1)>Aktif</option>
                                <option value="0" @selected(old('is_active', $bolum->is_active) == 0)>Pasif</option>
                            </select>
                        </div>
                        
                        {{-- Butonlar --}}
                        <div class="flex items-center justify-between">
                            @if($isSuperadmin)
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Güncelle
                            </button>
                            @else
                                <span class="text-sm text-gray-500 italic">Bölüm bilgilerini düzenleme yetkiniz yok.</span>
                            @endif
                            <a href="{{ route('admin.bolumler.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                Geri Dön
                            </a>
                        </div>
                    </form>

                    {{-- ======================== MAKİNE YÖNETİMİ ======================== --}}
                    {{-- ======================== MAKİNE YÖNETİMİ KALDIRILDI (Dashboard'a Taşındı) ======================== --}}
                    @if($bolum->has_machines)
                        <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                            <p class="font-bold">Makine Yönetimi</p>
                            <p>Makine ekleme ve düzenleme işlemleri artık 
                                <a href="{{ route('admin.bolumler.dashboard', $bolum) }}" class="underline font-bold">Bölüm Paneli</a> üzerinden yapılmaktadır.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>