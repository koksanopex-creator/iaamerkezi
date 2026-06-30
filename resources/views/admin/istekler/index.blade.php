<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Kullanıcı İstekleri') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <h3 class="text-lg font-bold mb-4">Bekleyen ve Önceki İstekler</h3>

                    @if (session('success'))
                        <div class="mb-4 bg-green-100 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded">
                            <thead class="bg-gray-50 dark:bg-gray-600">
                                <tr>
                                    <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Kullanıcı</th>
                                    <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Talep Türü</th>
                                    <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Eski Değer</th>
                                    <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Yeni Değer</th>
                                    <th class="px-4 py-2 border-b text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Tarih</th>
                                    <th class="px-4 py-2 border-b text-center text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Durum</th>
                                    <th class="px-4 py-2 border-b text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">İşlem</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                                @forelse ($istekler as $istek)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($istek->user)
                                                <a href="{{ route('profile.show', $istek->user_id) }}" class="text-blue-600 hover:underline">
                                                    {{ $istek->user->name }}
                                                </a>
                                            @else
                                                <span class="text-gray-500">Silinmiş Kullanıcı</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($istek->talep_turu == 'isim_degisikligi')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">İsim Değişikliği</span>
                                            @elseif($istek->talep_turu == 'bolum_degisikligi')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Bölüm Değişikliği</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">{{ $istek->eski_deger }}</td>
                                        <td class="px-4 py-3 font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $istek->talep_turu == 'bolum_degisikligi' && $istek->yeniBolum ? $istek->yeniBolum->ad : $istek->yeni_deger }}
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $istek->created_at->format('d.m.Y H:i') }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($istek->durum == 'bekliyor')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Bekliyor</span>
                                            @elseif($istek->durum == 'onaylandi')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Onaylandı</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Reddedildi</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            @if($istek->durum == 'bekliyor')
                                                <div class="flex justify-end gap-2" x-data="{ open: false, action: '', reason: '' }">
                                                    <button @click="open = true; action = 'approve'" class="text-white bg-green-600 hover:bg-green-700 px-3 py-1 rounded text-xs font-medium">Onayla</button>
                                                    <button @click="open = true; action = 'reject'" class="text-white bg-red-600 hover:bg-red-700 px-3 py-1 rounded text-xs font-medium">Reddet</button>

                                                    <!-- Modal -->
                                                    <div x-show="open" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-transition>
                                                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                                                            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                                                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                                                            </div>
                                                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                                                            <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                                                <form :action="action === 'approve' ? '{{ route('admin.istekler.approve', $istek->id) }}' : '{{ route('admin.istekler.reject', $istek->id) }}'" method="POST">
                                                                    @csrf
                                                                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" x-text="action === 'approve' ? 'İsteği Onayla' : 'İsteği Reddet'"></h3>
                                                                        <div class="mt-2">
                                                                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Kullanıcıya not bırakabilirsiniz (İsteğe bağlı).</p>
                                                                            <textarea name="admin_notu" rows="3" class="w-full border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"></textarea>
                                                                        </div>
                                                                    </div>
                                                                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                                                        <button type="submit" :class="action === 'approve' ? 'bg-green-600 hover:bg-green-700' : 'bg-red-600 hover:bg-red-700'" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
                                                                            Devam Et
                                                                        </button>
                                                                        <button type="button" @click="open = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                                            İptal
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-500" title="{{ $istek->admin_notu }}">Değerlendirildi ({{ $istek->admin->name ?? 'Admin' }})</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-4 text-center text-gray-500 dark:text-gray-400">Hiç istek bulunmuyor.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $istekler->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
