<table>
    <thead>
        {{-- Logo Alanı --}}
        <tr><td colspan="6" height="30"></td></tr>
        <tr><td colspan="6" height="30"></td></tr>
        <tr><td colspan="6" height="30"></td></tr>
        
        <tr>
            <th colspan="6" style="font-size: 16px; font-weight: bold; text-align: center; vertical-align: middle; background-color: #E2E8F0;">
                {{ $iaa->baslik }}
            </th>
        </tr>
        <tr>
            <th colspan="6" style="text-align: center; color: #64748B;">
                PROJE DETAY RAPORU - {{ date('d.m.Y H:i') }}
            </th>
        </tr>
    </thead>
    <tbody>
        <tr><td colspan="6"></td></tr>
        
        <tr style="background-color: #1a56db; color: #ffffff;">
            <td colspan="6" style="font-weight: bold;">1. PROJE KÜNYESİ</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Proje No:</td>
            <td>#{{ $iaa->id }}</td>
            <td style="font-weight: bold; background-color: #f3f4f6;">Durum:</td>
            <td colspan="3">{{ $iaa->durum }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Takım:</td>
            <td>{{ $takim->ad ?? '-' }}</td>
            <td style="font-weight: bold; background-color: #f3f4f6;">Takım Lideri:</td>
            <td colspan="3">{{ $takim->lider->name ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Başlangıç:</td>
            <td>{{ \Carbon\Carbon::parse($assignment->start_date)->format('d.m.Y') }}</td>
            <td style="font-weight: bold; background-color: #f3f4f6;">Hedef Bitiş:</td>
            <td colspan="3">{{ \Carbon\Carbon::parse($assignment->due_date)->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">İlerleme:</td>
            <td colspan="5">%{{ round($progressPercentage) }} ({{ $completedStepsCount }} / {{ $totalStepsCount }} Adım Tamamlandı)</td>
        </tr>

        <tr><td colspan="6"></td></tr>
        
        <tr style="background-color: #1a56db; color: #ffffff;">
            <td colspan="6" style="font-weight: bold;">2. PROJE EKİBİ (SQUAD)</td>
        </tr>
        <tr style="font-weight: bold; background-color: #f3f4f6;">
            <td colspan="3">Ad Soyad</td>
            <td colspan="2">Rol</td>
            <td>Durum</td>
        </tr>
        @php $ekip = $iaa->projeEkibi; @endphp
        @if($ekip->isNotEmpty())
            @foreach($ekip as $user)
                <tr>
                    <td colspan="3">{{ $user->name }}</td>
                    <td colspan="2">{{ $user->pivot->rol ?? 'Üye' }}</td>
                    <td>{{ ucfirst($user->pivot->durum ?? '-') }}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6" style="text-align: center;">Kayıtlı ekip üyesi bulunamadı.</td>
            </tr>
        @endif

        <tr><td colspan="6"></td></tr>
        
        <tr style="background-color: #1a56db; color: #ffffff;">
            <td colspan="6" style="font-weight: bold;">3. ADIM DETAYLARI VE ANALİZLER</td>
        </tr>
        
        @foreach($steps as $step)
            @php 
                $update = $progressUpdates[$step->id] ?? null;
                $isCompleted = in_array($step->id, $completedStepIds);
            @endphp
            <tr style="background-color: #E0E7FF;">
                <td style="font-weight: bold;">{{ $loop->iteration }}. Adım:</td>
                <td colspan="5" style="font-weight: bold;">{{ $step->name }}</td>
            </tr>
            @if($isCompleted)
                @php 
                    $content = json_decode($update->content, true); 
                    $tools = $content['tools'] ?? [];
                    $fb = $tools['fishbone'] ?? null;
                @endphp
                
                {{-- Balık Kılçığı - Excel --}}
                @if($fb && (!empty($fb['problem']) || !empty($fb['insan']) || !empty($fb['makine'])))
                    <tr style="background-color: #fee2e2;">
                        <td colspan="6" style="font-weight: bold; color: #991b1b;">• Balık Kılçığı Analizi @if(!empty($fb['problem'])) - Problem: {{ $fb['problem'] }} @endif</td>
                    </tr>
                    <tr>
                        <td style="background-color: #f3f4f6; font-weight: bold;">İnsan:</td>
                        <td>{{ $fb['insan'] ?? '-' }}</td>
                        <td style="background-color: #f3f4f6; font-weight: bold;">Yöntem:</td>
                        <td>{{ $fb['yontem'] ?? '-' }}</td>
                        <td style="background-color: #f3f4f6; font-weight: bold;">Makine:</td>
                        <td>{{ $fb['makine'] ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td style="background-color: #f3f4f6; font-weight: bold;">Malzeme:</td>
                        <td>{{ $fb['malzeme'] ?? '-' }}</td>
                        <td style="background-color: #f3f4f6; font-weight: bold;">Ölçüm:</td>
                        <td>{{ $fb['olcum'] ?? '-' }}</td>
                        <td style="background-color: #f3f4f6; font-weight: bold;">Çevre:</td>
                        <td>{{ $fb['cevre'] ?? '-' }}</td>
                    </tr>
                @endif

                {{-- Pareto - Excel --}}
                @if(isset($tools['pareto']) && !empty($tools['pareto']['rows']))
                    @php 
                        $pRows = collect($tools['pareto']['rows'])->filter(fn($r) => !empty($r['problem']))->sortByDesc('frequency');
                        $pTotal = $pRows->sum('frequency');
                        $pCum = 0;
                    @endphp
                    @if($pRows->isNotEmpty())
                        <tr style="background-color: #e0f2fe;">
                            <td colspan="6" style="font-weight: bold; color: #075985;">• Pareto Analizi: {{ $tools['pareto']['title'] ?? 'Frekans Dağılımı' }}</td>
                        </tr>
                        <tr style="background-color: #f1f5f9; font-weight: bold;">
                            <td colspan="2">Madde</td>
                            <td>Sıklık</td>
                            <td>Toplam</td>
                            <td>Kümülatif %</td>
                            <td>Görsel</td>
                        </tr>
                        @foreach($pRows as $r)
                            @php 
                                $pCum += (float)$r['frequency'];
                                $pPerc = $pTotal > 0 ? ($pCum / $pTotal) * 100 : 0;
                            @endphp
                            <tr>
                                <td colspan="2">{{ $r['problem'] }}</td>
                                <td style="text-align: right;">{{ $r['frequency'] }}</td>
                                <td style="text-align: right;">{{ $pCum }}</td>
                                <td style="text-align: right;">%{{ round($pPerc) }}</td>
                                <td style="color: #3b82f6;">{{ str_repeat('█', min(15, (int)$r['frequency'])) }}</td>
                            </tr>
                        @endforeach
                    @endif
                @endif

                {{-- Detay Notlar --}}
                <tr style="background-color: #fdfdfd;">
                    <td style="font-weight: bold; vertical-align: top;">Notlar:</td>
                    <td colspan="5">
                        @if(isset($content['form_data']) && is_array($content['form_data']))
                            @foreach($content['form_data'] as $field)
                                @if(isset($field['text']) && !empty($field['text']))
                                    • {{ $field['text'] }} {{ !$loop->last ? " | " : "" }}
                                @endif
                            @endforeach
                        @endif
                    </td>
                </tr>
            @else
                <tr><td colspan="6" style="color: #94a3b8; font-style: italic;">Adım tamamlanmamış.</td></tr>
            @endif
            <tr><td colspan="6" style="border: none; height: 10px;"></td></tr>
        @endforeach

        {{-- === 4. İADE VE HURDA BİLDİRİMİ === --}}
        @if(isset($iade) && $iade)
        <tr><td colspan="6"></td></tr>
        <tr style="background-color: #1a56db; color: #ffffff;">
            <td colspan="6" style="font-weight: bold;">4. İADE VE HURDA BİLDİRİMİ</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Ürün Grubu:</td>
            <td>{{ $iade->urun_turu ?? '-' }}</td>
            <td style="font-weight: bold; background-color: #f3f4f6;">İade Sebebi:</td>
            <td colspan="3">{{ $iade->iade_sebebi ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">İade Miktarı:</td>
            <td>{{ floatval($iade->miktar) }} {{ $iade->birim ?? '' }}</td>
            <td style="font-weight: bold; background-color: #f3f4f6;">Toplam Parti:</td>
            <td colspan="3">{{ floatval($iade->toplam_parti_miktari) }} {{ $iade->birim ?? '' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">İade Oranı:</td>
            <td>
                @php
                    $toplam = floatval($iade->toplam_parti_miktari);
                    $miktar = floatval($iade->miktar);
                    $oran = ($toplam > 0) ? ($miktar / $toplam) * 100 : 0;
                @endphp
                %{{ number_format($oran, 1) }}
            </td>
            <td style="font-weight: bold; background-color: #f3f4f6;">İade Tarihi:</td>
            <td colspan="3">{{ $iade->iade_tarihi ? $iade->iade_tarihi->format('d.m.Y') : '-' }}</td>
        </tr>
        @if($iade->aciklama)
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Açıklama:</td>
            <td colspan="5">{{ $iade->aciklama }}</td>
        </tr>
        @endif
        @endif

        {{-- === 5. MÜŞTERİ ZİYARET BİLGİLERİ === --}}
        @if(isset($visitData) && $visitData)
        <tr><td colspan="6"></td></tr>
        <tr style="background-color: #1a56db; color: #ffffff;">
            <td colspan="6" style="font-weight: bold;">{{ isset($iade) && $iade ? '5' : '4' }}. MÜŞTERİ ZİYARET BİLGİLERİ</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Ziyaret Tarihi:</td>
            <td>{{ isset($visitData['visit_date']) ? \Carbon\Carbon::parse($visitData['visit_date'])->format('d.m.Y H:i') : '-' }}</td>
            <td style="font-weight: bold; background-color: #f3f4f6;">Ziyaret Sebebi:</td>
            <td colspan="3">{{ $visitData['visit_reason'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Lot No:</td>
            <td>{{ $visitData['lot_no'] ?? '-' }}</td>
            <td style="font-weight: bold; background-color: #f3f4f6;">Barkod:</td>
            <td colspan="3">{{ $visitData['barcode'] ?? '-' }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Görüşülen Kişiler:</td>
            <td colspan="5">
                @if(!empty($visitData['contact_persons']))
                    @if(is_array($visitData['contact_persons']))
                        {{ implode(', ', $visitData['contact_persons']) }}
                    @else
                        {{ $visitData['contact_persons'] }}
                    @endif
                @else
                    -
                @endif
            </td>
        </tr>
        @if(!empty($visitData['findings']))
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Tespitler:</td>
            <td colspan="5">{{ $visitData['findings'] }}</td>
        </tr>
        @endif
        @if(!empty($visitData['result']))
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Sonuç:</td>
            <td colspan="5">{{ $visitData['result'] }}</td>
        </tr>
        @endif
        @if(!empty($visitData['visit_notes']))
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">Notlar:</td>
            <td colspan="5">{{ $visitData['visit_notes'] }}</td>
        </tr>
        @endif
        @endif

    </tbody>
</table>
