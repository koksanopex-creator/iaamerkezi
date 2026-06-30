<section>
    <header>
        <h2 class="text-lg font-bold text-gray-900">
            {{ __('Gözlemci Yönetimi (Bölüm Gözlemcisi)') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Sizi izlemesine izin verdiğiniz personelleri buradan yönetebilirsiniz. Gözlemciler sizin yetkilerinizle sistemi salt okunur olarak görebilir.') }}
        </p>
    </header>

    @if(auth()->user()->observers->isNotEmpty())
        <div class="mt-4 p-4 bg-indigo-50 border border-indigo-100 rounded-xl flex items-start gap-3 animate-fade-in">
            <div class="mt-0.5 text-indigo-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div class="text-sm text-indigo-800">
                <p>Şu an <strong>{{ auth()->user()->observers->count() }} personel</strong> profilinizi izleme yetkisine sahip: 
                   <span class="font-bold text-indigo-900">{{ auth()->user()->observers->pluck('name')->join(', ') }}</span>.
                </p>
            </div>
        </div>
    @endif

    @if(Auth::user()->isShadowing())
        <div class="mt-6 p-6 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800 flex items-center gap-4">
            <div class="p-3 bg-amber-100 rounded-full">
                <span class="text-2xl">⚠️</span>
            </div>
            <div>
                <p class="text-sm font-bold">Kısıtlı Alan</p>
                <p class="text-sm opacity-90">Başka bir kullanıcının hesabını izlerken (Shadowing) gözlemci ayarlarını değiştiremezsiniz. Bu alan sadece kendi hesabınızdayken erişilebilirdir.</p>
            </div>
        </div>
    @else
        <div class="mt-6 space-y-6">
            {{-- Gözlemci Ekleme Formu --}}
            <form action="{{ route('observer.add') }}" method="POST" class="flex flex-col sm:flex-row gap-3 items-end">
                @csrf
                <div class="flex-1 w-full">
                    <x-input-label for="observer_id" :value="__('Yeni Gözlemci Ekle')" />
                    <select id="observer_id" name="observer_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm select2-searchable">
                        <option value="">{{ __('Personel Seçin...') }}</option>
                        @foreach(\App\Models\User::where('id', '!=', auth()->id())->whereNull('customer_id')->get() as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>
                <x-primary-button class="sm:mb-0.5">
                    {{ __('Ekle') }}
                </x-primary-button>
            </form>

            {{-- Mevcut Gözlemciler Listesi --}}
            <div class="bg-gray-50 rounded-xl border border-gray-100 overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Personel') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('Eklenme Tarihi') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">{{ __('İşlem') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse(auth()->user()->observers as $observer)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs">
                                            {{ substr($observer->name, 0, 1) }}
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900">{{ $observer->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $observer->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ ($observer->pivot && $observer->pivot->created_at) ? $observer->pivot->created_at->format('d.m.Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form action="{{ route('observer.remove', $observer->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Bu personelin gözlemci yetkisini kaldırmak istediğinize emin misiniz?')" class="text-red-600 hover:text-red-900">
                                            {{ __('Kaldır') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">
                                    {{ __('Henüz size atanmış bir gözlemci bulunmamaktadır.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</section>

@push('scripts')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-searchable').select2({
            placeholder: "Personel arayın...",
            allowClear: true,
            theme: "classic"
        });
    });
</script>
<style>
    .select2-container--classic .select2-selection--single {
        height: 42px !important;
        border: 1px solid #d1d5db !important;
        border-radius: 0.375rem !important;
        padding-top: 6px !important;
    }
    .select2-container--classic .select2-selection--single .select2-selection__arrow {
        top: 8px !important;
    }
</style>
@endpush
