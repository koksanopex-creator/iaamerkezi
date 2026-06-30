@push('pageTitle')
    Toplu Mail Gönderimi | 
@endpush

<div>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white/80 backdrop-blur-md overflow-hidden shadow-xl sm:rounded-2xl border border-white/20">
                <div class="p-8">
                    <div class="mb-8 border-b border-gray-200 pb-4 flex justify-between items-center">
                        <div>
                            <h2 class="text-2xl font-black text-slate-800 tracking-tight">Müşterilere Toplu Mail Gönder</h2>
                            <p class="text-sm text-slate-500 font-medium mt-1">Sistem güncellemeleri veya bilgilendirmeler için seçili firmalara e-posta gönderin.</p>
                        </div>
                        <div class="flex gap-3">
                            <a href="{{ route('admin.musteriler.toplu-mail-loglari') }}" class="bg-white text-indigo-600 hover:bg-indigo-50 font-bold py-2.5 px-4 rounded-xl border border-indigo-200 shadow-sm transition-all active:scale-95 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Geçmiş Loglar
                            </a>
                            @if(auth()->user()->hasRole('Superadmin'))
                            <a href="{{ route('admin.ayarlar.toplu-mail-yetkileri') }}" class="bg-white text-slate-600 hover:bg-slate-50 font-bold py-2.5 px-4 rounded-xl border border-slate-200 shadow-sm transition-all active:scale-95 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Yetki Ayarları
                            </a>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Sol Taraf: Mail Formu -->
                        <div class="lg:col-span-2 space-y-6">
                            <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-100">
                                <div class="mb-4">
                                    <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Konu (Başlık)</label>
                                    <input type="text" wire:model.defer="subject" class="w-full border-slate-200 rounded-xl py-3 px-4 focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 shadow-sm font-bold text-slate-700" placeholder="E-posta konusu yazın...">
                                    @error('subject') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>

                                <div class="mb-4" wire:ignore>
                                    <label class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider mb-2">Mesaj İçeriği</label>
                                    <!-- Quill Editor Container -->
                                    <div id="quill-editor" class="bg-white rounded-b-xl border-slate-200" style="min-height: 250px;"></div>
                                </div>
                                @error('messageContent') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror

                                <div class="mt-6 flex justify-end">
                                    <button wire:click="sendMail" wire:loading.attr="disabled" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg shadow-indigo-200 transition-all active:scale-95 flex items-center gap-2">
                                        <span wire:loading.remove wire:target="sendMail">Gönderimi Başlat</span>
                                        <span wire:loading wire:target="sendMail">Kuyruğa Ekleniyor...</span>
                                        <svg wire:loading.remove wire:target="sendMail" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Sağ Taraf: Hedef Kitle (Müşteriler) -->
                        <div class="bg-slate-50/50 p-6 rounded-2xl border border-slate-100" wire:poll.2s="pollStatuses">
                            <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-200">
                                <h3 class="text-sm font-extrabold text-slate-700 uppercase tracking-wider">Hedef Firmalar</h3>
                                <button wire:click="toggleSelectAll" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">
                                    {{ $selectAll ? 'Tümünü Kaldır' : 'Tümünü Seç' }}
                                </button>
                            </div>
                            
                            <div class="space-y-3 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($customers as $customer)
                                    <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm flex items-start gap-3">
                                        <div class="pt-1">
                                            <input type="checkbox" wire:model.defer="selectedCustomers.{{ $customer->id }}" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-slate-800 text-sm truncate" title="{{ $customer->name }}">{{ $customer->name }}</div>
                                            @foreach($customer->users as $user)
                                                <div class="flex items-center justify-between mt-1.5 p-1.5 rounded-lg bg-slate-50 border border-slate-100">
                                                    <div class="flex flex-col min-w-0">
                                                        <span class="text-[11px] font-bold text-slate-600 truncate">{{ $user->name }}</span>
                                                        <span class="text-[10px] text-slate-400 truncate">{{ $user->email }}</span>
                                                    </div>
                                                    <div class="ml-2 flex-shrink-0">
                                                        @if(isset($recipientStatuses[$user->id]))
                                                            @if($recipientStatuses[$user->id] === 'queued')
                                                                <svg class="w-4 h-4 text-amber-500 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                            @elseif($recipientStatuses[$user->id] === 'sent')
                                                                <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                                            @elseif($recipientStatuses[$user->id] === 'failed')
                                                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Quill Styles -->
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <!-- Quill Script -->
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
        <script>
            document.addEventListener('livewire:initialized', function () {
                var quill = new Quill('#quill-editor', {
                    theme: 'snow',
                    placeholder: 'Mesajınızı buraya yazın...',
                    modules: {
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                            ['link', 'clean']
                        ]
                    }
                });

                // Set initial content if any
                quill.root.innerHTML = @this.get('messageContent');

                // Update Livewire property on text change
                quill.on('text-change', function() {
                    @this.set('messageContent', quill.root.innerHTML, false); // defer
                });

                // Listen for reset or submission to clear editor if needed
                Livewire.on('mailSent', () => {
                    // quill.root.innerHTML = '';
                });
            });
        </script>
    </div>
</div>
