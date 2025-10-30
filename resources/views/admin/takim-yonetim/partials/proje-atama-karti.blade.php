<div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl border border-gray-200">
    <div class="p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Takıma Havuzdan Proje Ata</h3>
        @if($havuzdakiOneriler->isNotEmpty())
            <form action="{{ route('admin.takim-yonetim.projeAta', $takim) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <select name="iaa_id" class="block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">Atamak için bir proje seçin...</option>
                        @foreach ($havuzdakiOneriler as $oneri) <option value="{{ $oneri->id }}">{{ Str::limit($oneri->baslik, 50) }} (Puan: {{ number_format($oneri->puan, 2) }})</option> @endforeach
                    </select>
                    <button type="submit" class="w-full bg-green-600 text-white font-semibold py-2 px-4 rounded-md">Projeyi Bu Takıma Ata</button>
                </div>
            </form>
        @else
            <p class="text-sm text-center text-gray-500">Havuzda atanabilecek bir proje bulunmamaktadır.</p>
        @endif
    </div>
</div>