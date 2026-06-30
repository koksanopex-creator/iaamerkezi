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
        
        <tr>
            <td colspan="6" style="font-weight: bold; background-color: #1e3a8a; color: #ffffff; height: 30px; vertical-align: middle; border: 1px solid #ffffff;">   1. PROJE KÜNYESİ</td>
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
        @if($iaa->durum == 'Tamamlandı' && $iaa->real_completion_date)
        <tr>
            <td style="font-weight: bold; background-color: #f0fdf4; color: #166534;">Tamamlanma:</td>
            <td colspan="5" style="background-color: #f0fdf4; color: #166534; font-weight: bold;">{{ \Carbon\Carbon::parse($iaa->real_completion_date)->format('d.m.Y H:i') }}</td>
        </tr>
        @endif
        <tr>
            <td style="font-weight: bold; background-color: #f3f4f6;">İlerleme:</td>
            <td colspan="5">%{{ round($progressPercentage) }} ({{ $completedStepsCount }} / {{ $totalStepsCount }} Adım Tamamlandı)</td>
        </tr>

        <tr><td colspan="6"></td></tr>
        
        <tr>
            <td colspan="6" style="font-weight: bold; background-color: #1e3a8a; color: #ffffff; height: 30px; vertical-align: middle; border: 1px solid #ffffff;">   2. PROJE EKİBİ (SQUAD)</td>
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
                    <td colspan="3">
                        {{ $user->name }}
                        @if($user->trashed())
                            (Pasif / İşten Ayrıldı)
                        @endif
                    </td>
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
        
        <tr>
            <td colspan="6" style="font-weight: bold; background-color: #1e3a8a; color: #ffffff; height: 30px; vertical-align: middle; border: 1px solid #ffffff;">   3. ADIM DETAYLARI VE ANALİZLER</td>
        </tr>
        
        @foreach($steps as $step)
            @php 
                $update = $progressUpdates[$step->id] ?? null;
                $isCompleted = in_array($step->id, $completedStepIds);
            @endphp
            <tr>
                <td style="font-weight: bold; background-color: #bfdbfe; color: #1e3a8a; border: 1px solid #ffffff;">{{ $loop->iteration }}. Adım:</td>
                <td colspan="5" style="font-weight: bold; background-color: #bfdbfe; color: #1e3a8a; border: 1px solid #ffffff;">
                    {{ $step->name }} 
                    @if($isCompleted && $update && $update->completed_at)
                        <span style="font-weight: normal; font-size: 10px;"> (Tamamlanma: {{ \Carbon\Carbon::parse($update->completed_at)->format('d.m.Y H:i') }})</span>
                    @endif
                </td>
            </tr>
            @if($isCompleted)
                @if(isset($isCustomerView) && $isCustomerView && $update && $update->is_hidden_from_customer)
                    <tr>
                        <td colspan="6" style="text-align: center; background-color: #f8fafc; color: #475569; font-weight: bold; height: 40px; vertical-align: middle;">
                            Bu adım projenin {{ $iaa->atananTakim ? $iaa->atananTakim->ad : 'ekip' }} tarafından {{ $update->completed_at ? \Carbon\Carbon::parse($update->completed_at)->format('d.m.Y') : ($update->created_at ? $update->created_at->format('d.m.Y') : '-') }} tarihinde tamamlanmıştır.
                        </td>
                    </tr>
                @else
                    @php 
                        $content = json_decode($update->content, true); 
                        $tools = $content['tools'] ?? [];
                        $fb = $tools['fishbone'] ?? null;
                    @endphp
                    
                    {{-- 5 Neden - Excel --}}
                    @php $fiveWhys = $tools['five_whys'] ?? null; @endphp
                    @if($fiveWhys && count(array_filter($fiveWhys)) > 0)
                        <tr>
                            <td colspan="6" style="font-weight: bold; background-color: #f5f3ff; color: #6d28d9; border-bottom: 1px solid #ddd6fe;">• 5 Neden Analizi</td>
                        </tr>
                        @foreach($fiveWhys as $key => $value)
                            @if(!empty($value) && str_starts_with($key, 'why'))
                                <tr>
                                    <td style="background-color: #f3f4f6; font-weight: bold;">{{ str_replace('why', '', $key) }}. Neden:</td>
                                    <td colspan="5">{{ $value }}</td>
                                </tr>
                            @endif
                        @endforeach
                    @endif
                    
                    {{-- Balık Kılçığı - Excel --}}
                    @if($fb && (!empty($fb['problem']) || !empty($fb['insan']) || !empty($fb['makine'])))
                        <tr>
                            <td colspan="6" style="font-weight: bold; background-color: #fee2e2; color: #991b1b; border-bottom: 1px solid #fecaca;">• Balık Kılçığı Analizi @if(!empty($fb['problem'])) - Problem: {{ $fb['problem'] }} @endif</td>
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
                            $pMax = $pRows->max('frequency') ?: 1;
                            $pCum = 0;
                        @endphp
                        @if($pRows->isNotEmpty())
                            <tr>
                                <td colspan="6" style="font-weight: bold; background-color: #e0f2fe; color: #075985; border-bottom: 1px solid #bae6fd;">• Pareto Analizi: {{ $tools['pareto']['title'] ?? 'Frekans Dağılımı' }}</td>
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
                    
                    {{-- SWOT - Excel --}}
                    @php 
                        $swot = $tools['swot'] ?? null;
                        $hasSwot = $swot && (!empty($swot['strengths']) || !empty($swot['weaknesses']) || !empty($swot['opportunities']) || !empty($swot['threats']));
                    @endphp
                    @if($hasSwot)
                        <tr>
                            <td colspan="6" style="font-weight: bold; background-color: #f0fdf4; color: #166534; border-bottom: 1px solid #bbf7d0;">• SWOT Analizi</td>
                        </tr>
                        <tr>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Güçlü Yönler (S):</td>
                            <td colspan="2">{{ $swot['strengths'] ?? '-' }}</td>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Zayıf Yönler (W):</td>
                            <td colspan="2">{{ $swot['weaknesses'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Fırsatlar (O):</td>
                            <td colspan="2">{{ $swot['opportunities'] ?? '-' }}</td>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Tehditler (T):</td>
                            <td colspan="2">{{ $swot['threats'] ?? '-' }}</td>
                        </tr>
                    @endif

                    {{-- 4M Raporu - Excel --}}
                    @php $fourM = $tools['4m_report'] ?? null; @endphp
                    @if($fourM)
                        <tr>
                            <td colspan="6" style="font-weight: bold; background-color: #f0f7ff; color: #1e40af; border-bottom: 1px solid #dbeafe;">• 4M Analizi</td>
                        </tr>
                        <tr>
                            <td style="background-color: #f3f4f6; font-weight: bold;">İnsan (Man):</td>
                            <td colspan="2">{{ $fourM['man'] ?? '-' }}</td>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Makine (Machine):</td>
                            <td colspan="2">{{ $fourM['machine'] ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Malzeme (Material):</td>
                            <td colspan="2">{{ $fourM['material'] ?? '-' }}</td>
                            <td style="background-color: #f3f4f6; font-weight: bold;">Metot (Method):</td>
                            <td colspan="2">{{ $fourM['method'] ?? '-' }}</td>
                        </tr>
                    @endif

                    {{-- Aksiyon ve Görev Listeleri - Excel --}}
                    @if(isset($tools['action_list']) || isset($tools['task_list']))
                        <tr>
                            <td colspan="6" style="font-weight: bold; background-color: #f8fafc; color: #475569; border-bottom: 1px solid #e2e8f0;">• Aksiyon ve Görev Listeleri</td>
                        </tr>
                        @if(isset($tools['action_list']))
                            @foreach(($tools['action_list']['items'] ?? []) as $item)
                                <tr>
                                    <td style="font-weight: bold;">Aksiyon:</td>
                                    <td colspan="4">{{ $item['text'] }}</td>
                                    <td>{{ $item['is_completed'] ? 'Tamamlandı' : 'Bekliyor' }}</td>
                                </tr>
                            @endforeach
                        @endif
                        @if(isset($tools['task_list']))
                            @foreach(($tools['task_list']['tasks'] ?? []) as $task)
                                <tr>
                                    <td style="font-weight: bold;">Görev:</td>
                                    <td colspan="3">{{ $task['description'] }}</td>
                                    <td style="font-weight: bold;">Sorumlu:</td>
                                    <td>
                                        @php $assignedUser = \App\Models\User::find($task['assigned_user_id']); @endphp
                                        {{ $assignedUser->name ?? '-' }}
                                    </td>
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
                @endif
            @else
                <tr><td colspan="6" style="color: #94a3b8; font-style: italic;">Adım tamamlanmamış.</td></tr>
            @endif
            <tr><td colspan="6" style="border: none; height: 10px;"></td></tr>
        @endforeach

        {{-- === 4. İADE VE HURDA BİLDİRİMİ === --}}
        @if(isset($iade) && $iade)
        <tr><td colspan="6"></td></tr>
        <tr>
            <td colspan="6" style="font-weight: bold; background-color: #1e3a8a; color: #ffffff; height: 25px; vertical-align: middle;">  4. İADE VE HURDA BİLDİRİMİ</td>
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
            <td colspan="5">
                @if(isset($isCustomerView) && $isCustomerView && !$iade->musteri_gorebilir_mi)
                    <span style="color: #94a3b8; font-style: italic;">Bu bölüm sadece şirket içi bilgilendirme amaçlıdır.</span>
                @else
                    {{ $iade->aciklama }}
                @endif
            </td>
        </tr>
        @endif
        @endif

        {{-- === 5. MÜŞTERİ ZİYARET BİLGİLERİ === --}}
        @if(isset($visitData) && $visitData)
        <tr><td colspan="6"></td></tr>
        <tr>
            <td colspan="6" style="font-weight: bold; background-color: #1e3a8a; color: #ffffff; height: 25px; vertical-align: middle;">  {{ isset($iade) && $iade ? '5' : '4' }}. MÜŞTERİ ZİYARET BİLGİLERİ</td>
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
            @if(!(isset($isCustomerView) && $isCustomerView))
            <tr>
                <td style="font-weight: bold; background-color: #f3f4f6;">Notlar:</td>
                <td colspan="5">{{ $visitData['visit_notes'] }}</td>
            </tr>
            @endif
        @endif
        @endif
        
        <tr><td colspan="6"></td></tr>
        <tr>
            <td colspan="6" style="text-align: center; color: #94a3b8; font-style: italic; font-size: 10px;">
                Bu rapor sistemsel olarak {{ auth()->user()->name }} tarafından oluşturulmuştur.
            </td>
        </tr>
    </tbody>
</table>
