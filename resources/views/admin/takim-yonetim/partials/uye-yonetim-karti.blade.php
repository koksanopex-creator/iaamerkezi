<div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
    {{-- Yeni Üye Ekleme Formu --}}
    <div class="p-6 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Takıma Yeni Üye Ekle</h3>
        <form action="{{ route('admin.takim-yonetim.uyeEkle', $takim) }}" method="POST" class="flex flex-col sm:flex-row items-center gap-3">
            @csrf
            <select name="user_id" class="w-full flex-grow border-gray-300 rounded-md shadow-sm" required>
                <option value="">Eklemek için bir kullanıcı seçin...</option>
                @foreach ($potansiyelUyeler as $uye) <option value="{{ $uye->id }}">{{ $uye->name }}</option> @endforeach
            </select>
            <button type="submit" class="w-full sm:w-auto flex-shrink-0 bg-indigo-600 text-white font-semibold py-2 px-4 rounded-md">Ekle</button>
        </form>
    </div>
    {{-- Mevcut Üye Listesi --}}
    <ul class="divide-y divide-gray-200">
        @forelse ($takim->uyeler as $uye)
            <li class="p-4 flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-sm font-bold">{{ substr($uye->name, 0, 1) }}</div>
                    <p class="font-medium text-gray-800">{{ $uye->name }} @if ($uye->id === $takim->lider_user_id)<span class="text-xs text-white bg-indigo-500 px-2 py-0.5 rounded-full ml-1">Lider</span>@endif</p>
                </div>
                @if ($uye->id !== $takim->lider_user_id)
                    <form action="{{ route('admin.takim-yonetim.uyeCikar', ['takim' => $takim, 'user' => $uye]) }}" method="POST" onsubmit="return confirm('Bu üyeyi çıkarmak istediğinizden emin misiniz?');">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold">Çıkar</button>
                    </form>
                @endif
            </li>
        @empty
            <li class="p-4 text-center text-gray-500">Takımda henüz üye yok.</li>
        @endforelse
    </ul>
</div>