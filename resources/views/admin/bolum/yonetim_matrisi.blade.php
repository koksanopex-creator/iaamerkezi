@push('pageTitle')
    Bölüm Yönetim Matrisi | 
@endpush

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 leading-tight tracking-tighter flex items-center gap-3">
            <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            {{ __('Bölüm Yönetim Matrisi') }} ({{ $bolum->ad }})
        </h2>
    </x-slot>

    @push('styles')
    <style>
        :root {
            --color-background-primary: #ffffff;
            --color-background-secondary: #f8fafc;
            --color-border-tertiary: #e2e8f0;
            --color-text-primary: #1e293b;
            --color-text-secondary: #64748b;
            --border-radius-lg: 1rem;
        }

        .matrix-page-root { padding: 5px 0; font-family: 'Figtree', sans-serif; position: relative; }

        .info-bar {
            display: flex; align-items: center; justify-content: space-between;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            padding: 16px 24px; margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .info-left { display: flex; align-items: center; gap: 14px; }
        .shield {
            width: 42px; height: 42px; border-radius: 12px;
            background: #eef2ff;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
            border: 1px solid #e0e7ff;
        }
        .info-title { font-size: 14px; font-weight: 700; color: #1e293b; text-transform: uppercase; letter-spacing: 0.025em; }
        .info-sub   { font-size: 11.5px; color: #64748b; margin-top: 3px; max-width: 550px; line-height: 1.5; }
        .info-sub strong { color: #4f46e5; font-weight: 700; }
        
        .stats { display: flex; align-items: center; gap: 24px; }
        .stat { text-align: center; }
        .stat-n { font-size: 24px; font-weight: 800; line-height: 1; }
        .stat-n.a { color: #64748b; }
        .stat-n.i { color: #4f46e5; }
        .stat-l { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 4px; font-weight: 600; }
        .vdiv { width: 1px; height: 36px; background: #e2e8f0; }

        .matrix-wrap {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .matrix-scroll { 
            overflow-x: auto; 
            max-height: calc(100vh - 280px);
            overflow-y: auto;
            border-radius: 1rem;
        }

        .matrix-table { border-collapse: separate; border-spacing: 0; width: 100%; table-layout: auto; }

        .matrix-table thead th {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: bottom;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .th-icon {
            left: 0; z-index: 15 !important;
            padding: 15px 10px;
            border-right: 1px solid #e2e8f0;
        }
        .accent-bar { height: 4px; width: 20px; background: #4f46e5; border-radius: 2px; margin: 0 auto; }

        .th-label {
            left: 44px; z-index: 15 !important;
            padding: 15px 20px;
            text-align: left;
            border-right: 1px solid #e2e8f0;
            font-size: 11px; font-weight: 800; color: #64748b;
            text-transform: uppercase; letter-spacing: 0.1em;
        }

        .th-person {
            padding: 15px 10px; text-align: center;
            border-right: 1px solid #e2e8f0;
            vertical-align: bottom;
        }
        .th-person:last-child { border-right: none; }

        .avatar {
            width: 38px; height: 38px; border-radius: 10px;
            background: #e0e7ff;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; color: #4f46e5;
            margin: 0 auto 8px; position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 10px; }
        
        .avatar-badge {
            position: absolute; top: -4px; right: -4px;
            width: 15px; height: 15px; border-radius: 5px;
            background: #4f46e5; color: white;
            font-size: 7px; font-weight: 900;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid #f8fafc;
        }
        .th-group { 
            text-align: center; font-size: 10px; font-weight: 900; letter-spacing: 0.15em; 
            padding: 8px 0; border-bottom: 1px solid #e2e8f0; border-right: 1px solid #e2e8f0;
            position: sticky; top: 0; z-index: 20;
        }
        .th-group-white { background: #eef2ff; color: #4338ca; }
        .th-group-blue  { background: #ecfdf5; color: #047857; }

        .p-name { 
            font-size: 11px; font-weight: 800; color: #1e293b; line-height: 1.2; 
            max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            margin: 0 auto;
        }
        .p-dept { font-size: 9px; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 3px; font-weight: 800; }
        .p-dept-white { color: #6366f1; }
        .p-dept-blue  { color: #10b981; }

        /* User Hover Card */
        .user-card-trigger { position: relative; cursor: help; }
        .th-person:hover { z-index: 50 !important; } /* Sütun başlığının z-index değerini hover'da yükselt */
        
        .user-hover-card {
            position: absolute; top: calc(100% - 10px); left: 50%; transform: translateX(-50%) translateY(10px);
            width: 220px; background: white; border-radius: 12px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0; padding: 12px; z-index: 1000;
            opacity: 0; visibility: hidden; transition: all 0.2s ease;
            text-align: left; pointer-events: none;
        }
        .user-card-trigger:hover .user-hover-card { opacity: 1; visibility: visible; transform: translateX(-50%) translateY(5px); }
        .uhc-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
        .uhc-avatar { width: 40px; height: 40px; border-radius: 8px; object-fit: cover; }
        .uhc-name { font-size: 13px; font-weight: 800; color: #1e293b; line-height: 1.2; }
        .uhc-unvan { font-size: 10px; font-weight: 600; color: #64748b; text-transform: uppercase; }
        .uhc-info { display: flex; flex-direction: column; gap: 4px; border-top: 1px solid #f1f5f9; pt: 8px; }
        .uhc-item { display: flex; align-items: center; gap: 6px; font-size: 11px; color: #475569; }
        .uhc-icon { width: 14px; height: 14px; color: #94a3b8; }

        .mgr-btn {
            margin-top: 10px; padding: 4px 10px;
            border-radius: 8px; font-size: 9px; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em;
            border: none; cursor: pointer; transition: all 0.2s ease;
            display: inline-block;
        }
        .mgr-btn.on  { background: #4f46e5; color: white; box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3); }
        .mgr-btn.on:hover { background: #4338ca; transform: translateY(-1px); }
        .mgr-btn.off { background: #ffffff; color: #64748b; border: 1px solid #e2e8f0; }
        .mgr-btn.off:hover { background: #f1f5f9; border-color: #cbd5e1; }

        .matrix-table tbody tr:hover td { background: #f8fafc; }
        
        .td-icon {
            position: sticky; left: 0; z-index: 5;
            padding: 0; border-right: 1px solid #e2e8f0;
            text-align: center; vertical-align: middle; background: white;
        }
        .group-stripe { width: 44px; height: 100%; display: flex; align-items: center; justify-content: center; padding: 12px 0; min-height: 50px; }
        .group-dot { width: 7px; height: 7px; border-radius: 50%; }

        .td-label {
            position: sticky; left: 44px; z-index: 5;
            background: #ffffff; padding: 12px 20px;
            border-right: 1px solid #e2e8f0; vertical-align: middle;
        }
        .label-group { font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 4px; }
        .label-name  { font-size: 12px; font-weight: 500; color: #1e293b; line-height: 1.4; }

        .td-switch { text-align: center; vertical-align: middle; padding: 0 10px; border-right: 1px solid #f1f5f9; }

        .toggle { position: relative; display: inline-block; width: 32px; height: 16px; cursor: pointer; }
        .toggle input { opacity: 0; width: 0; height: 0; position: absolute; }
        .t-track {
            position: absolute; inset: 0;
            background: #e2e8f0;
            border-radius: 999px; transition: background 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .toggle input:checked + .t-track { background: #4f46e5; }
        .toggle input:disabled + .t-track { opacity: 0.25; cursor: not-allowed; }
        .t-thumb {
            position: absolute; top: 2px; left: 2px;
            width: 12px; height: 12px;
            border-radius: 50%; background: white;
            transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            pointer-events: none;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .toggle input:checked ~ .t-thumb { transform: translateX(16px); }

        .gc-indigo { color: #4338ca; } .dot-indigo { background: #4f46e5; } .bg-indigo { background: #eef2ff; }
        .gc-emerald { color: #047857; } .dot-emerald { background: #10b981; } .bg-emerald { background: #ecfdf5; }
        .gc-amber { color: #b45309; } .dot-amber { background: #f59e0b; } .bg-amber { background: #fffbeb; }

        .footer {
            padding: 12px 24px; border-top: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: space-between;
            background: #f8fafc;
        }
        .legend { display: flex; align-items: center; gap: 20px; }
        .legend-item { display: flex; align-items: center; gap: 8px; font-size: 10px; color: #64748b; font-weight: 600; }
        .ldot { width: 10px; height: 10px; border-radius: 3px; }

        .toast-container { position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; pointer-events: none; }
        .toast {
            background: #1e1b4b; color: white; padding: 10px 20px; border-radius: 12px;
            font-size: 13px; font-weight: 600; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.2);
            display: flex; align-items: center; gap: 10px; pointer-events: auto;
        }

        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: #f1f5f9; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
    @endpush

    <div class="matrix-page-root" x-data="{
        showNotification: false,
        notificationMsg: '',
        
        showToast(msg) {
            this.notificationMsg = msg;
            this.showNotification = true;
            setTimeout(() => { this.showNotification = false; }, 3000);
        },

        toggleRelation(userId, type, value, state) {
            let url = '{{ route('admin.bolum-yonetim.update', ['user' => ':userId']) }}';
            url = url.replace(':userId', userId);

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    type: type,
                    value: value,
                    status: state
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    this.showToast(data.message);
                    
                    // Eğer rol (ATA/YARDIMCI) değişikliği yapıldıysa sayfayı yenile
                    // Böylece alttaki yetki düğmeleri aktifleşir ve etiketler güncellenir
                    if (type === 'role') {
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    }
                } else {
                    alert(data.message);
                }
            });
        }
    }">
        <!-- Notification Toast -->
        <div class="toast-container" x-show="showNotification" x-cloak x-transition>
            <div class="toast">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span x-text="notificationMsg"></span>
            </div>
        </div>

        <!-- Bölüm Seçici (Sadece Superadmin/Yönetim için) -->
        @if($tumBolumler->isNotEmpty())
        <div class="mb-6 flex items-center justify-between bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 uppercase tracking-tight">Bölüm Seçimi</h3>
                    <p class="text-xs text-slate-500">Yönetmek istediğiniz bölümü listeden seçin.</p>
                </div>
            </div>
            <div class="w-72">
                <select onchange="window.location.href = '{{ route('admin.bolum-yonetim.index') }}?bolum_id=' + this.value" 
                        class="w-full bg-slate-50 border-slate-200 rounded-xl text-sm font-bold text-slate-700 focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($tumBolumler as $tb)
                        <option value="{{ $tb->id }}" @if($bolum->id == $tb->id) selected @endif>{{ $tb->ad }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        <!-- Bilgi Paneli -->
        <div class="info-bar">
            <div class="info-left">
                <div class="shield">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <div class="info-title">Bölüm Yönetim Matrisi: {{ $bolum->ad }}</div>
                    <div class="info-sub">Bölüm personelleri arasından <strong>Müdür Yardımcısı</strong> atayabilir ve her birine özel yetkiler devredebilirsiniz.</div>
                </div>
            </div>
            <div class="stats">
                <div class="stat">
                    <div class="stat-n a">{{ count($personel) }}</div>
                    <div class="stat-l">Bölüm Personeli</div>
                </div>
                <div class="vdiv"></div>
                <div class="stat">
                    <div class="stat-n i">{{ $personel->filter(fn($p) => $p->hasRole('Bölüm Lider Yardımcısı'))->count() }}</div>
                    <div class="stat-l">Aktif Yardımcı</div>
                </div>
            </div>
        </div>

        <!-- Matris Tablosu -->
        <div class="matrix-wrap">
            <div class="matrix-scroll custom-scrollbar">
                <table class="matrix-table">
                    @php
                        $beyazYakaCount = $personel->where('is_mavi_yaka', false)->count();
                        $maviYakaCount = $personel->where('is_mavi_yaka', true)->count();
                    @endphp
                    <thead>
                        <!-- Grup Başlığı Satırı -->
                        <tr>
                            <th class="th-icon col-icon" style="top: 0; height: 35px; border-bottom: none;"></th>
                            <th class="th-label col-label" style="top: 0; height: 35px; border-bottom: none; border-bottom: 1px solid #e2e8f0;">GRUPLAR</th>
                            @if($beyazYakaCount > 0)
                                <th colspan="{{ $beyazYakaCount }}" class="th-group th-group-white">BEYAZ YAKA PERSONEL</th>
                            @endif
                            @if($maviYakaCount > 0)
                                <th colspan="{{ $maviYakaCount }}" class="th-group th-group-blue">MAVİ YAKA PERSONEL</th>
                            @endif
                        </tr>
                        <!-- İsim Satırı -->
                        <tr>
                            <th class="th-icon col-icon" style="top: 35px;"><div class="accent-bar"></div></th>
                            <th class="th-label col-label" style="top: 35px;">Delegasyon Listesi</th>
                            @foreach($personel as $p)
                                <th class="th-person" style="width: 140px; min-width: 140px; top: 35px;">
                                    <div class="user-card-trigger">
                                        <div class="avatar shadow-sm" style="border: 2px solid {{ $p->is_mavi_yaka ? '#10b981' : '#6366f1' }}">
                                            @if($p->profile_photo_url)
                                                <img src="{{ $p->profile_photo_url }}" alt="{{ $p->name }}">
                                            @else
                                                {{ strtoupper(substr($p->name, 0, 1)) }}
                                            @endif
                                            @if($p->hasRole('Bölüm Lider Yardımcısı'))
                                                <div class="avatar-badge">Y</div>
                                            @endif
                                        </div>
                                        <div class="p-name" title="{{ $p->name }}">{{ $p->name }}</div>
                                        <div class="p-dept {{ $p->is_mavi_yaka ? 'p-dept-blue' : 'p-dept-white' }}">
                                            {{ $p->is_mavi_yaka ? 'MAVİ YAKA' : 'BEYAZ YAKA' }}
                                        </div>
                                        
                                        <!-- Hover Card Content -->
                                        <div class="user-hover-card shadow-2xl">
                                            <div class="uhc-header">
                                                @if($p->profile_photo_url)
                                                    <img src="{{ $p->profile_photo_url }}" class="uhc-avatar">
                                                @else
                                                    <div class="uhc-avatar bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                                                        {{ strtoupper(substr($p->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="uhc-name">{{ $p->name }}</div>
                                                    <div class="uhc-unvan">{{ $p->unvan ?? 'Personel' }}</div>
                                                </div>
                                            </div>
                                            <div class="uhc-info mt-2 pt-2">
                                                <div class="uhc-item">
                                                    <svg class="uhc-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                                    {{ $p->email }}
                                                </div>
                                                @if($p->bolum)
                                                    <div class="uhc-item">
                                                        <svg class="uhc-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                                        {{ $p->bolum->ad }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <button 
                                        @click="toggleRelation('{{ $p->id }}', 'role', 'Bölüm Lider Yardımcısı', !{{ $p->hasRole('Bölüm Lider Yardımcısı') ? 'true' : 'false' }})"
                                        class="mgr-btn {{ $p->hasRole('Bölüm Lider Yardımcısı') ? 'on' : 'off' }}">
                                        {{ $p->hasRole('Bölüm Lider Yardımcısı') ? 'YARDIMCI' : 'ATA' }}
                                    </button>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $colors = ['indigo', 'emerald', 'amber'];
                            $cIdx = 0;
                        @endphp
                        @foreach($managedPermissions as $groupName => $perms)
                            @php $color = $colors[$cIdx++ % 3]; @endphp
                            @foreach($perms as $slug => $label)
                                <tr>
                                    <td class="td-icon bg-{{ $color }}">
                                        <div class="group-stripe"><div class="group-dot dot-{{ $color }}"></div></div>
                                    </td>
                                    <td class="td-label">
                                        @if($loop->first)<div class="label-group gc-{{ $color }}">{{ $groupName }}</div>@endif
                                        <div class="label-name">{{ $label }}</div>
                                    </td>
                                    @foreach($personel as $p)
                                        <td class="td-switch">
                                            <label class="toggle">
                                                <input type="checkbox" 
                                                    @if($p->hasPermissionTo($slug)) checked @endif
                                                    @if(!$p->hasRole('Bölüm Lider Yardımcısı')) disabled @endif
                                                    @change="toggleRelation('{{ $p->id }}', 'permission', '{{ $slug }}', $event.target.checked)">
                                                <div class="t-track"></div>
                                                <div class="t-thumb"></div>
                                            </label>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="footer">
                <div class="legend">
                    <div class="legend-item"><div class="ldot" style="background:#4f46e5"></div>Yetki Aktif</div>
                    <div class="legend-item"><div class="ldot" style="background:#e2e8f0"></div>Yetki Kapalı</div>
                </div>
                <div class="footer-brand">Kurumsal Yönetim Paneli &copy; 2026</div>
            </div>
        </div>
    </div>
</x-app-layout>
