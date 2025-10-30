<div class="p-4 bg-blue-50 border-l-4 border-blue-400 rounded-r-lg">
    <h5 class="block text-lg font-semibold text-blue-800">
        {{ $config['title'] ?? 'Bilgilendirme' }}
    </h5>
    <div class="mt-2 text-sm text-blue-700 prose prose-sm max-w-none">
       {!! nl2br(e($config['content'] ?? 'Bu adım için bilgi metni girilmemiş.')) !!}
    </div>
</div>