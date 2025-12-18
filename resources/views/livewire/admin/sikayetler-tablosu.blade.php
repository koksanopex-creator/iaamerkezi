<div> {{-- === KÖK ELEMENT === --}}
    <div class="bg-gradient-to-br from-slate-50 via-blue-50/30 to-indigo-50/40 min-h-screen p-4 md:p-6">
        <div class="max-w-7xl mx-auto">

            {{-- 1. BAŞLIK --}}
            @include('livewire.admin.sikayetler-partials.header')

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 mb-6 overflow-hidden">
                
                {{-- 2. İSTATİSTİKLER --}}
                @include('livewire.admin.sikayetler-partials.stats')

                {{-- 3. FİLTRELER --}}
                @include('livewire.admin.sikayetler-partials.filters')

            </div>

            {{-- 4. KART LİSTESİ --}}
            @include('livewire.admin.sikayetler-partials.cards')

            {{-- SAYFALAMA --}}
            <div class="mt-6">
                {{ $sikayetler->links() }}
            </div>

        </div>
    </div>
    
    @livewire('admin.sikayet-triyaj-modal')

    {{-- CSS Animasyonları --}}
    <style>
        @keyframes fade-in { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fade-in 0.5s ease-out forwards; }
        @keyframes slide-in { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        .animate-slide-in { animation: slide-in 0.4s ease-out forwards; }
        @keyframes slide-up { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
        .animate-slide-up { animation: slide-up 0.3s ease-out forwards; }
        @keyframes pulse { 50% { opacity: .5; } }
        .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>
</div>