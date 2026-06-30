<div class="fixed bottom-6 right-6 z-50 flex flex-col items-end" x-data="{ open: <?php if ((object) ('isOpen') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isOpen'->value()); ?>')<?php echo e('isOpen'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isOpen'); ?>')<?php endif; ?> }">

    <!-- CHAT WINDOW -->
    <div x-show="open" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 scale-95"
        class="w-96 h-[500px] bg-white rounded-2xl shadow-2xl border border-gray-200 flex flex-col overflow-hidden mb-4">

        <!-- HEADER -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-4 flex justify-between items-center text-white">
            <div class="flex items-center gap-2">
                <div class="p-1.5 bg-white/20 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-sm">Köksan Asistan</h3>
                    <p class="text-[10px] text-white/80">Yapay Zeka Destekli</p>
                </div>
            </div>
            <div class="flex gap-2">
                <button wire:click="clearHistory" class="text-white/70 hover:text-white" title="Geçmişi Temizle">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                    </svg>
                </button>
                <button @click="open = false" class="text-white/70 hover:text-white">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- MESSAGES AREA -->
        <div class="flex-1 overflow-y-auto p-4 bg-slate-50 space-y-4" id="chat-messages">
            <!-- Marked.js for Markdown Rendering -->
            <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $msg): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex <?php echo e($msg['role'] === 'user' ? 'justify-end' : 'justify-start'); ?>">
                        <div
                            class="max-w-[85%] rounded-xl px-4 py-2 text-sm shadow-sm 
                                                <?php echo e($msg['role'] === 'user'
                ? 'bg-indigo-600 text-white rounded-tr-none'
                : 'bg-white text-gray-800 border border-gray-100 rounded-tl-none prose prose-indigo prose-sm max-w-none'); ?>">

                            <!--[if BLOCK]><![endif]--><?php if($msg['role'] === 'ai'): ?>
                                <div x-data x-html="marked.parse(<?php echo \Illuminate\Support\Js::from($msg['content'])->toHtml() ?>)"></div>
                            <?php else: ?>
                                <?php echo e($msg['content']); ?>

                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

                        </div>
                    </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

            <!--[if BLOCK]><![endif]--><?php if($isTyping): ?>
                <div class="flex justify-start">
                    <div
                        class="bg-white border border-gray-100 rounded-xl rounded-tl-none px-4 py-3 shadow-sm flex items-center gap-1">
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-75"></span>
                        <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce delay-150"></span>
                    </div>
                </div>
                <!-- Otomatik Yanıt Tetikleyici -->
                <div wire:init="generateResponse" class="hidden"></div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <!-- INPUT AREA -->
        <div class="p-3 bg-white border-t border-gray-100">
            <form wire:submit.prevent="sendMessage" class="flex gap-2">
                <input type="text" wire:model="userMessage" placeholder="Bir şeyler sorun..."
                    class="flex-1 rounded-lg border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder:text-gray-400"
                    <?php echo e($isTyping ? 'disabled' : ''); ?>>

                <button type="submit"
                    class="p-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
                    <?php echo e($isTyping ? 'disabled' : ''); ?>>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                        <path
                            d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                    </svg>
                </button>
            </form>
        </div>

        <script>
            // Her mesajda en alta kaydır
            const chatContainer = document.getElementById('chat-messages');
            const observer = new MutationObserver(() => {
                chatContainer.scrollTop = chatContainer.scrollHeight;
            });
            observer.observe(chatContainer, { childList: true, subtree: true });

            // Context: Mevcut Sayfayı Livewire'a Bildir (Gözlemci modunda değilse)
            document.addEventListener('livewire:initialized', () => {
                <!--[if BLOCK]><![endif]--><?php if (! (Auth::user()->isShadowing())): ?>
                    Livewire.dispatch('updateCurrentUrl', { url: window.location.href });
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            });

            // SPA geçişlerinde de güncelle (Gözlemci modunda değilse)
            document.addEventListener('livewire:navigated', () => {
                <!--[if BLOCK]><![endif]--><?php if (! (Auth::user()->isShadowing())): ?>
                    Livewire.dispatch('updateCurrentUrl', { url: window.location.href });
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            });
        </script>
    </div>

    <!-- TOGGLE BUTTON -->
    <button wire:click="toggleChat"
        class="group flex items-center justify-center w-14 h-14 bg-indigo-600 text-white rounded-full shadow-lg hover:bg-indigo-700 hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-indigo-300">

        <!-- Open Icon -->
        <svg x-show="!open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-7 h-7">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z" />
        </svg>

        <!-- Close Icon -->
        <svg x-show="open" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
            stroke="currentColor" class="w-7 h-7" style="display: none;">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
        </svg>
    </button>
</div><?php /**PATH /var/www/kys_koksan/iaa/resources/views/livewire/global-chat-bot.blade.php ENDPATH**/ ?>