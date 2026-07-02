<x-pulse>
    @php
        $lastPurge = \Illuminate\Support\Facades\Cache::get('last_pulse_purge_time');
        $purgeText = $lastPurge ? 'Son Temizleme: ' . \Carbon\Carbon::parse($lastPurge)->isoFormat('D MMM YYYY, HH:mm') : 'Logları Temizle';
    @endphp
    <div style="grid-column: 1 / -1; display: flex; justify-content: flex-end; margin-bottom: 1rem;">
        <form action="{{ route('pulse.purge') }}" method="POST" onsubmit="return confirm('Tüm logları (veritabanı, hata vb.) silmek istediğinize emin misiniz? Bu işlem geri alınamaz.');">
            @csrf
            <button type="submit" style="background-color: #ef4444; color: #ffffff; font-weight: 600; padding: 0.375rem 1rem; border-radius: 0.375rem; font-size: 0.875rem; border: none; cursor: pointer; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#dc2626'" onmouseout="this.style.backgroundColor='#ef4444'">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" style="width: 1rem; height: 1rem;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                </svg>
                {{ $purgeText }}
            </button>
        </form>
    </div>

    <livewire:pulse.servers cols="full" />

    <livewire:pulse.usage cols="4" rows="2" />

    <livewire:pulse.queues cols="4" />

    <livewire:pulse.cache cols="4" />

    <livewire:pulse.slow-queries cols="8" />

    <livewire:pulse.exceptions cols="6" />

    <livewire:pulse.slow-requests cols="6" />

    <livewire:pulse.slow-jobs cols="6" />

    <livewire:pulse.slow-outgoing-requests cols="6" />
</x-pulse>
