@push('pageTitle')
    Kullanıcı İçe Aktarma Önizlemesi | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl md:text-2xl font-black text-gray-900 tracking-tight">
                {{ __('Kullanıcı İçe Aktarma Önizlemesi') }}
            </h2>
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-bold rounded-lg transition-colors">
                İptal ve Geri Dön
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Yüklenecek Kayıtlar</h3>
                        <p class="text-sm text-gray-500 mt-1">Lütfen aşağıdaki verileri kontrol edip onaylayın. Sistemde bulunmayan bölüm, direktörlük ve uyruk bilgileri otomatik eklenecektir.</p>
                    </div>
                    
                    <form action="{{ route('admin.users.import_confirm') }}" method="POST">
                        @csrf
                        <input type="hidden" name="cache_key" value="{{ $cacheKey }}">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Verileri Onayla ve Aktar
                        </button>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Adı Soyadı</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Telefon</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Direktörlük</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Bölüm</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Rol(ler)</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Uyruk</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Durum</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            @foreach($data as $index => $row)
                                @php
                                    $email = $row['email'] ?? '-';
                                    $exists = \App\Models\User::where('email', $email)->exists();
                                @endphp
                                <tr class="{{ $exists ? 'bg-red-50' : 'hover:bg-gray-50' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $index + 2 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $row['adi_soyadi'] ?? $row['ad_soyad'] ?? $row['name'] ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $row['telefon'] ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $row['direktorluk'] ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $row['bolum'] ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $row['rol'] ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">{{ $row['uyruk'] ?? '-' }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($exists)
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Email Mevcut (Atlanacak)
                                            </span>
                                        @else
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Yeni Eklenecek
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
