<?php
    $notifications = [];
    if (session('success')) {
        $notifications[] = ['type' => 'success', 'message' => session('success'), 'title' => 'Başarılı!'];
    }
    if (session('error')) {
        $notifications[] = ['type' => 'error', 'message' => session('error'), 'title' => 'Hata!'];
    }
    if (session('warning')) {
        $notifications[] = ['type' => 'warning', 'message' => session('warning'), 'title' => 'Uyarı!'];
    }
    if (session('info')) {
        $notifications[] = ['type' => 'info', 'message' => session('info'), 'title' => 'Bilgi'];
    }
?>

<div 
    x-data="{ 
        notifications: <?php echo e(json_encode($notifications)); ?>,
        add(notification) {
            this.notifications.push({
                id: Date.now(),
                ...notification
            });
        },
        remove(id) {
            this.notifications = this.notifications.filter(n => n.id !== id);
        }
    }"
    x-init="
        notifications.forEach((n, i) => {
            n.id = i;
            setTimeout(() => remove(n.id), 5000);
        });
    "
    class="fixed top-5 right-5 z-[100] flex flex-col gap-3 w-full max-w-sm pointer-events-none"
>
    <template x-for="n in notifications" :key="n.id">
        <div 
            x-show="true"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-x-10"
            x-transition:enter-end="opacity-100 transform translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform translate-x-0"
            x-transition:leave-end="opacity-0 transform translate-x-10"
            class="pointer-events-auto bg-white border-l-4 rounded-lg shadow-2xl p-4 flex items-start gap-3 animate-fade-in-right"
            :class="{
                'border-emerald-500': n.type === 'success',
                'border-red-500': n.type === 'error',
                'border-amber-500': n.type === 'warning',
                'border-blue-500': n.type === 'info'
            }"
        >
            
            <div class="shrink-0 p-1 rounded-full" 
                 :class="{
                    'bg-emerald-100 text-emerald-600': n.type === 'success',
                    'bg-red-100 text-red-600': n.type === 'error',
                    'bg-amber-100 text-amber-600': n.type === 'warning',
                    'bg-blue-100 text-blue-600': n.type === 'info'
                 }">
                <template x-if="n.type === 'success'">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                </template>
                <template x-if="n.type === 'error'">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </template>
                <template x-if="n.type === 'warning'">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </template>
                <template x-if="n.type === 'info'">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </template>
            </div>

            
            <div class="flex-1 min-w-0">
                <p class="font-bold text-gray-900 text-sm" x-text="n.title"></p>
                <p class="text-xs text-gray-600 mt-0.5 leading-relaxed" x-text="n.message"></p>
            </div>

            
            <button @click="remove(n.id)" class="shrink-0 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </template>
</div>
<?php /**PATH /var/www/kys_koksan/iaa/resources/views/components/flash-notifications.blade.php ENDPATH**/ ?>