@props(['opinion'])

@php
    $classes = match($opinion) {
        'Ceza Verilsin' => 'bg-rose-100 text-rose-700 ring-rose-200',
        'Ceza Verilmesin' => 'bg-emerald-100 text-emerald-700 ring-emerald-200',
        'Ek Soruşturma' => 'bg-amber-100 text-amber-700 ring-amber-200',
        'Çekimser' => 'bg-slate-100 text-slate-600 ring-slate-200',
        default => 'bg-slate-100 text-slate-600 ring-slate-200'
    };
@endphp

<span {{ $attributes->merge(['class' => "text-[10px] font-bold px-2.5 py-1 rounded-full ring-1 ring-inset $classes"]) }}>
    {{ $opinion }}
</span>
