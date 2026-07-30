<table>
    <thead>
        <tr>
            <th rowspan="2" style="border: 1px solid #cbd5e1; text-align: center; vertical-align: middle;"></th>
            <th colspan="5" style="font-size: 16px; font-weight: bold; text-align: center; vertical-align: middle; background-color: #E2E8F0; border: 1px solid #cbd5e1;">
                {{ $iaa->baslik }}
            </th>
        </tr>
        <tr>
            <th colspan="5" style="text-align: center; color: #64748B; border: 1px solid #cbd5e1; vertical-align: middle;">
                PROJE DETAY RAPORU - {{ date('d.m.Y H:i') }}
            </th>
        </tr>
        <tr><td colspan="6" style="height: 20px;"></td></tr>
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

                    {{-- === HARİCİ EKLENEN ARAÇLAR (PHASE 2) - EXCEL === --}}
                    @php
                        $stepTools = \App\Models\IaaStepTool::where('iaa_id', $iaa->id)
                            ->where('iaa_workflow_step_id', $step->id)
                            ->orderBy('order')
                            ->get();
                    @endphp
                    @foreach($stepTools as $tool)
                        @php $tData = is_array($tool->data) ? $tool->data : json_decode($tool->data, true); @endphp
                        
                        @if($tool->tool_type === '5why')
                            @if(!empty($tData['whys']) && count(array_filter($tData['whys'])) > 0)
                                <tr>
                                    <td colspan="6" style="font-weight: bold; background-color: #f5f3ff; color: #6d28d9; border-bottom: 1px solid #ddd6fe;">• {{ $tool->title ?? '5 Neden Analizi' }} @if(!empty($tData['problemStatement'])) - Problem: {{ $tData['problemStatement'] }} @endif</td>
                                </tr>
                                @foreach($tData['whys'] as $index => $why)
                                    @if(!empty($why))
                                        <tr>
                                            <td style="background-color: #f3f4f6; font-weight: bold;">{{ $index + 1 }}. Neden:</td>
                                            <td colspan="5">{{ $why }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                                @if(!empty($tData['rootCause']))
                                    <tr>
                                        <td style="background-color: #fee2e2; color: #991b1b; font-weight: bold;">KÖK NEDEN:</td>
                                        <td colspan="5" style="color: #991b1b;">{{ $tData['rootCause'] }}</td>
                                    </tr>
                                @endif
                            @endif
                        @elseif($tool->tool_type === 'chart')
                            @if(!empty($tData['labels']) && (!empty($tData['values']) || !empty($tData['series'])))
                                <tr>
                                    <td colspan="6" style="font-weight: bold; background-color: #ffe4e6; color: #be123c; border-bottom: 1px solid #fecdd3;">• {{ $tool->title ?? 'Grafik Analizi' }} @if(!empty($tData['chartTitle'])) - {{ $tData['chartTitle'] }} @endif</td>
                                </tr>
                                <tr style="background-color: #f1f5f9; font-weight: bold;">
                                    <td colspan="3">Kategori</td>
                                    @if(!empty($tData['series']))
                                        @foreach($tData['series'] as $s)
                                            <td colspan="{{ max(1, floor(3 / count($tData['series']))) }}">{{ $s['name'] ?? 'Değer' }}</td>
                                        @endforeach
                                    @else
                                        <td colspan="3">Değer</td>
                                    @endif
                                </tr>
                                @foreach($tData['labels'] as $index => $label)
                                    <tr>
                                        <td colspan="3">{{ $label }}</td>
                                        @if(!empty($tData['series']))
                                            @foreach($tData['series'] as $s)
                                                <td colspan="{{ max(1, floor(3 / count($tData['series']))) }}">{{ $s['data'][$index] ?? 0 }}</td>
                                            @endforeach
                                        @else
                                            <td colspan="3">{{ $tData['values'][$index] ?? 0 }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        @elseif($tool->tool_type === 'pareto')
                            @php 
                                $pRows = collect($tData['items']??[])->filter(fn($r) => !empty($r['category']) && is_numeric($r['frequency']))->sortByDesc('frequency')->values();
                                $pTotal = $pRows->sum('frequency');
                                $pCum = 0;
                            @endphp
                            @if($pRows->isNotEmpty())
                                <tr>
                                    <td colspan="6" style="font-weight: bold; background-color: #e0f2fe; color: #075985; border-bottom: 1px solid #bae6fd;">• {{ $tool->title ?? 'Pareto Analizi' }}</td>
                                </tr>
                                <tr style="background-color: #f1f5f9; font-weight: bold;">
                                    <td colspan="2">Kategori</td>
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
                                        <td colspan="2">{{ $r['category'] }}</td>
                                        <td style="text-align: right;">{{ $r['frequency'] }}</td>
                                        <td style="text-align: right;">{{ $pCum }}</td>
                                        <td style="text-align: right;">%{{ round($pPerc) }}</td>
                                        <td style="color: #3b82f6;">{{ str_repeat('█', min(15, (int)$r['frequency'])) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @elseif($tool->tool_type === 'fishbone')
                            @php $cats = $tData['categories'] ?? []; @endphp
                            <tr>
                                <td colspan="6" style="font-weight: bold; background-color: #fee2e2; color: #991b1b; border-bottom: 1px solid #fecaca;">• {{ $tool->title ?? 'Balık Kılçığı Analizi' }} @if(!empty($tData['problem_statement'])) - Problem: {{ $tData['problem_statement'] }} @endif</td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">İnsan:</td>
                                <td>{{ implode(', ', $cats['insan'] ?? []) ?: '-' }}</td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Yöntem:</td>
                                <td>{{ implode(', ', $cats['metot'] ?? []) ?: '-' }}</td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Makine:</td>
                                <td>{{ implode(', ', $cats['makine'] ?? []) ?: '-' }}</td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Malzeme:</td>
                                <td>{{ implode(', ', $cats['malzeme'] ?? []) ?: '-' }}</td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Ölçüm:</td>
                                <td>{{ implode(', ', $cats['olcum'] ?? []) ?: '-' }}</td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Çevre:</td>
                                <td>{{ implode(', ', $cats['cevre'] ?? []) ?: '-' }}</td>
                            </tr>
                        @elseif($tool->tool_type === 'swot')
                            <tr>
                                <td colspan="6" style="font-weight: bold; background-color: #f0fdf4; color: #166534; border-bottom: 1px solid #bbf7d0;">• {{ $tool->title ?? 'SWOT Analizi' }}</td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Güçlü Yönler (S):</td>
                                <td colspan="2">{{ $tData['strengths'] ?? '-' }}</td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Zayıf Yönler (W):</td>
                                <td colspan="2">{{ $tData['weaknesses'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Fırsatlar (O):</td>
                                <td colspan="2">{{ $tData['opportunities'] ?? '-' }}</td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Tehditler (T):</td>
                                <td colspan="2">{{ $tData['threats'] ?? '-' }}</td>
                            </tr>
                        @elseif($tool->tool_type === '4m_report')
                            <tr>
                                <td colspan="6" style="font-weight: bold; background-color: #f0f7ff; color: #1e40af; border-bottom: 1px solid #dbeafe;">• {{ $tool->title ?? '4M Gelişim Raporu' }}</td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">İnsan (Man):</td>
                                <td colspan="2">{{ $tData['items']['man'] ?? '-' }}</td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Makine (Machine):</td>
                                <td colspan="2">{{ $tData['items']['machine'] ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Malzeme (Material):</td>
                                <td colspan="2">{{ $tData['items']['material'] ?? '-' }}</td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Metot (Method):</td>
                                <td colspan="2">{{ $tData['items']['method'] ?? '-' }}</td>
                            </tr>
                        @elseif($tool->tool_type === 'checklist')
                            <tr>
                                <td colspan="6" style="font-weight: bold; background-color: #f8fafc; color: #475569; border-bottom: 1px solid #e2e8f0;">• {{ $tool->title ?? 'Kontrol Listesi' }}</td>
                            </tr>
                            @foreach(($tData['items'] ?? []) as $item)
                                <tr>
                                    <td colspan="5">{{ $item['text'] }}</td>
                                    <td>{{ !empty($item['checked']) ? 'Tamamlandı' : 'Bekliyor' }}</td>
                                </tr>
                            @endforeach
                        @elseif($tool->tool_type === 'action_list')
                            <tr>
                                <td colspan="6" style="font-weight: bold; background-color: #f8fafc; color: #475569; border-bottom: 1px solid #e2e8f0;">• {{ $tool->title ?? 'Aksiyon Listesi' }}</td>
                            </tr>
                            @foreach(($tData['items'] ?? []) as $item)
                                <tr>
                                    <td style="font-weight: bold;">Aksiyon:</td>
                                    <td colspan="3">{{ $item['action'] ?? '' }}</td>
                                    <td style="font-weight: bold;">Sorumlu: {{ $item['owner'] ?? '' }}</td>
                                    <td>{{ !empty($item['status']) && $item['status'] == 'completed' ? 'Tamamlandı' : 'Bekliyor' }} ({{ $item['target_date'] ?? '' }})</td>
                                </tr>
                            @endforeach
                        @endif
                    @endforeach

                    {{-- === GRAFİKLER (Bar Chart & Line Chart) - EXCEL === --}}
                    @if(isset($tools['bar_chart_data']) && is_array($tools['bar_chart_data']))
                        @foreach($tools['bar_chart_data'] as $bIndex => $bData)
                            @if(!empty($bData['rows']))
                                <tr>
                                    <td colspan="6" style="font-weight: bold; background-color: #ffe4e6; color: #be123c; border-bottom: 1px solid #fecdd3;">• {{ $bData['title'] ?? 'Sütun Grafiği' }}</td>
                                </tr>
                                <tr style="background-color: #f1f5f9; font-weight: bold;">
                                    <td colspan="3">{{ $bData['axis_x_label'] ?? 'Kategoriler' }}</td>
                                    <td colspan="3">{{ $bData['axis_y_label'] ?? 'Değerler' }}</td>
                                </tr>
                                @foreach($bData['rows'] as $r)
                                    @if(isset($r['label']) && $r['label'] !== '')
                                    <tr>
                                        <td colspan="3">{{ $r['label'] }}</td>
                                        <td colspan="3">{{ $r['value'] ?? 0 }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @endif

                    @if(isset($tools['line_chart_data']) && is_array($tools['line_chart_data']))
                        @foreach($tools['line_chart_data'] as $lIndex => $lData)
                            @if(!empty($lData['rows']))
                                <tr>
                                    <td colspan="6" style="font-weight: bold; background-color: #ffe4e6; color: #be123c; border-bottom: 1px solid #fecdd3;">• {{ $lData['title'] ?? 'Çizgi Grafiği' }}</td>
                                </tr>
                                <tr style="background-color: #f1f5f9; font-weight: bold;">
                                    <td colspan="3">{{ $lData['axis_x_label'] ?? 'Kategoriler' }}</td>
                                    <td colspan="3">{{ $lData['axis_y_label'] ?? 'Değerler' }}</td>
                                </tr>
                                @foreach($lData['rows'] as $r)
                                    @if(isset($r['label']) && $r['label'] !== '')
                                    <tr>
                                        <td colspan="3">{{ $r['label'] }}</td>
                                        <td colspan="3">{{ $r['value'] ?? 0 }}</td>
                                    </tr>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach
                    @endif

                    {{-- === ZİYARET PLANLARI - EXCEL === --}}
                    @php
                        $stepVisits = \App\Models\IaaZiyaretPlani::where('iaa_id', $iaa->id)
                            ->where('iaa_workflow_step_id', $step->id)
                            ->get();
                    @endphp
                    @if($stepVisits->isNotEmpty())
                        <tr>
                            <td colspan="6" style="font-weight: bold; background-color: #fef3c7; color: #d97706; border-bottom: 1px solid #fde68a;">• Ziyaret Planı ve Sonuçları</td>
                        </tr>
                        @foreach($stepVisits as $sv)
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Müşteri:</td>
                                <td colspan="2">{{ $iaa->musteriSikayeti->customer->name ?? $iaa->musteriSikayeti->musteri_adi ?? 'Müşteri' }}</td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Tarih / Durum:</td>
                                <td colspan="2">{{ $sv->visit_date ? \Carbon\Carbon::parse($sv->visit_date)->format('d.m.Y H:i') : '-' }} / {{ $sv->status }}</td>
                            </tr>
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Personel:</td>
                                <td colspan="2">{{ $sv->visitor_name ?? ($sv->visitor->name ?? 'Belirtilmedi') }}</td>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Tahmini Dönüş:</td>
                                <td colspan="2">{{ $sv->estimated_return_date ? \Carbon\Carbon::parse($sv->estimated_return_date)->format('d.m.Y') : '-' }}</td>
                            </tr>
                            @if($sv->result)
                            <tr>
                                <td style="background-color: #f3f4f6; font-weight: bold;">Sonuç:</td>
                                <td colspan="5">{{ $sv->result }}</td>
                            </tr>
                            @endif
                        @endforeach
                    @endif

                    {{-- Detay Notlar ve Diğer Form Verileri --}}
                    @if(isset($content['form_data']) && is_array($content['form_data']))
                        @foreach($content['form_data'] as $field)
                            @if(isset($field['text']) && !empty($field['text']))
                                <tr style="background-color: #fdfdfd;">
                                    <td style="font-weight: bold; vertical-align: top;">Not / Açıklama:</td>
                                    <td colspan="5">{{ $field['text'] }}</td>
                                </tr>
                            @endif
                            
                            @if(isset($field['before_text']) || isset($field['after_text']) || isset($field['before_images']) || isset($field['after_images']))
                                <tr>
                                    <td colspan="6" style="font-weight: bold; background-color: #e0e7ff; color: #3730a3; border-bottom: 1px solid #c7d2fe;">• Önce / Sonra Karşılaştırma</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="font-weight: bold; background-color: #fee2e2; color: #991b1b;">ÖNCE</td>
                                    <td colspan="3" style="font-weight: bold; background-color: #dcfce7; color: #166534;">SONRA</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="vertical-align: top;">{{ $field['before_text'] ?? '-' }}</td>
                                    <td colspan="3" style="vertical-align: top;">{{ $field['after_text'] ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" style="vertical-align: top;">
                                        @if(isset($field['before_images']) && is_array($field['before_images']))
                                            @foreach($field['before_images'] as $img)
                                                @if(is_string($img))
                                                    [IMG:{{ $img }}]<br>
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                    <td colspan="3" style="vertical-align: top;">
                                        @if(isset($field['after_images']) && is_array($field['after_images']))
                                            @foreach($field['after_images'] as $img)
                                                @if(is_string($img))
                                                    [IMG:{{ $img }}]<br>
                                                @endif
                                            @endforeach
                                        @endif
                                    </td>
                                </tr>
                            @endif

                            @if(isset($field['files']) && is_array($field['files']) && count($field['files']) > 0)
                                <tr>
                                    <td style="font-weight: bold; background-color: #f8fafc; vertical-align: top;">Ekli Dosyalar / Resimler:</td>
                                    <td colspan="5">
                                        @foreach($field['files'] as $file)
                                            @if(is_string($file))
                                                @php $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION)); @endphp
                                                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                    [IMG:{{ $file }}]<br>
                                                @else
                                                    <a href="{{ asset('storage/' . $file) }}" style="color: #2563eb; text-decoration: underline;">[Dosyayı Görüntüle]</a><br>
                                                @endif
                                            @endif
                                        @endforeach
                                    </td>
                                </tr>
                            @endif

                            @if(isset($field['user_ids']) || isset($field['info_user_ids']))
                                @php
                                    $uIds = $field['info_user_ids'] ?? $field['user_ids'] ?? [];
                                    $uNames = [];
                                    if (is_array($uIds) && !empty($uIds)) {
                                        $uNames = \App\Models\User::whereIn('id', $uIds)->pluck('name')->toArray();
                                    }
                                @endphp
                                @if(!empty($uNames))
                                    <tr style="background-color: #fdfdfd;">
                                        <td style="font-weight: bold; vertical-align: top;">Seçilen Kullanıcılar:</td>
                                        <td colspan="5">{{ implode(', ', $uNames) }}</td>
                                    </tr>
                                @endif
                            @endif
                        @endforeach
                    @endif
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
