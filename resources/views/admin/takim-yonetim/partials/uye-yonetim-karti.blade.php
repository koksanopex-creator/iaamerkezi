<div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
    {{-- Yeni Üye Ekleme Formu --}}
    <div class="p-6 bg-gray-50 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Takıma Yeni Üye Ekle</h3>
        <form action="{{ route('admin.takim-yonetim.uyeEkle', $takim) }}" method="POST" class="flex flex-col sm:flex-row items-center gap-3">
            @csrf
            <select name="user_id" class="w-full flex-grow border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                <option value="">Eklemek için bir kullanıcı seçin...</option>
                @foreach ($potansiyelUyeler as $uye) 
                    <option value="{{ $uye->id }}">{{ $uye->name }} ({{ $uye->email }})</option> 
                @endforeach
            </select>
            <button type="submit" class="w-full sm:w-auto flex-shrink-0 bg-indigo-600 text-white font-semibold py-2 px-4 rounded-md hover:bg-indigo-700 transition-colors">Ekle</button>
        </form>
    </div>

    {{-- Mevcut Üye Listesi --}}
    <ul class="divide-y divide-gray-200">
        @forelse ($takim->uyeler as $uye)
            <li class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                <div class="flex items-center space-x-3">
                    {{-- AVATAR (LİNKLİ) --}}
                    <a href="{{ route('profile.show', $uye->id) }}" target="_blank" class="flex-shrink-0 group">
                        @if($uye->profile_photo_path)
                            <img class="w-10 h-10 rounded-full object-cover border border-gray-200 group-hover:border-indigo-500 transition-colors" src="{{ asset('storage/' . $uye->profile_photo_path) }}" alt="{{ $uye->name }}">
                        @else
                            <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center text-sm font-bold text-gray-600 group-hover:bg-indigo-100 group-hover:text-indigo-700 transition-colors">
                                {{ substr($uye->name, 0, 1) }}
                            </div>
                        @endif
                    </a>

                    {{-- İSİM VE BİLGİLER --}}
                    <div>
                        <a href="{{ route('profile.show', $uye->id) }}" target="_blank" class="font-medium text-gray-900 hover:text-indigo-600 transition-colors flex items-center">
                            {{ $uye->name }}
                            @if ($uye->id === $takim->lider_user_id)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    Lider
                                </span>
                            @endif
                        </a>
                        <p class="text-xs text-gray-500">{{ $uye->bolum->ad ?? 'Bölüm Yok' }}</p>
                    </div>
                </div>

                {{-- SİLME BUTONU (Lider Hariç) --}}
                @if ($uye->id !== $takim->lider_user_id)
                    <form action="{{ route('admin.takim-yonetim.uyeCikar', ['takim' => $takim, 'user' => $uye]) }}" method="POST" onsubmit="return confirm('Bu üyeyi çıkarmak istediğinizden emin misiniz?');">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-semibold bg-red-50 hover:bg-red-100 px-3 py-1 rounded-md transition-colors">Çıkar</button>
                    </form>
                @endif
            </li>
        @empty
            <li class="p-4 text-center text-gray-500 italic">Takımda henüz üye yok.</li>
        @endforelse
    </ul>
</div>