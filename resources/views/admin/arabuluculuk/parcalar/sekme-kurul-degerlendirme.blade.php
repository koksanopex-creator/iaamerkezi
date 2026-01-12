<div class="bg-gray-50 border rounded-lg p-5 mb-6">
    <h4 class="font-bold text-gray-700 mb-3">Değerlendirme Ekle</h4>
    <form action="{{ route('admin.arabuluculuk.addComment', $case->id) }}" method="POST">
        @csrf
        <textarea name="yorum" class="w-full border-gray-300 rounded-md shadow-sm text-sm focus:ring-indigo-500 focus:border-indigo-500" rows="3" placeholder="Görüş ve değerlendirmenizi buraya yazınız..."></textarea>
        <div class="mt-3 flex justify-between items-center">
            <div class="w-1/3">
                <select name="karar" class="w-full border-gray-300 rounded-md text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">Karar (Opsiyonel)</option>
                    <option value="Onay">Onay / Olumlu</option>
                    <option value="Red">Red / Olumsuz</option>
                    <option value="Revize">Revize Gerekli</option>
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded shadow text-sm font-bold transition">Kaydet</button>
        </div>
    </form>
</div>

@foreach($case->kurulDegerlendirmesi as $degerlendirme)
    <div class="bg-white border border-gray-200 rounded-lg p-4 mb-3 shadow-sm">
        <div class="flex justify-between items-start mb-2">
            <div class="flex items-center">
                <div class="font-bold text-gray-900">{{ $degerlendirme->user->name ?? 'Bilinmeyen' }}</div>
                <span class="text-xs text-gray-500 ml-2">{{ $degerlendirme->created_at->format('d.m.Y H:i') }}</span>
            </div>
            @if($degerlendirme->karar)
                <span class="px-2 py-1 text-xs font-bold rounded {{ $degerlendirme->karar == 'Onay' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $degerlendirme->karar }}
                </span>
            @endif
        </div>
        <p class="text-gray-700 text-sm">{{ $degerlendirme->yorum }}</p>
    </div>
@endforeach