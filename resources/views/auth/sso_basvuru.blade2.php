@extends('layouts.app')

@section('content')
<div class="container mx-auto py-10">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">İAA Sistemine Hoş Geldin</h2>
        <p class="mb-4 text-gray-600">Merhaba <strong>{{ $centralUser['first_name'] }}</strong>, başvurunu tamamlamak için lütfen departmanını seç.</p>
        
        <form action="{{ route('sso.basvuru_kaydet') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700">Departman / Bölüm</label>
                <select name="bolum_id" class="w-full mt-2 p-2 border rounded" required>
                    <option value="">Seçiniz...</option>
                    @foreach($bolumler as $bolum)
                        <option value="{{ $bolum->id }}">{{ $bolum->ad }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">Başvuruyu Tamamla</button>
        </form>
    </div>
</div>
@endsection