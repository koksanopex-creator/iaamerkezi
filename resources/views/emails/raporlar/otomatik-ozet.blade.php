<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f4f7f6;
            padding-bottom: 40px;
        }

        .main-table {
            background-color: #ffffff;
            margin: 0 auto;
            width: 600px;
            max-width: 600px;
            border-spacing: 0;
            font-family: sans-serif;
            color: #333333;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        /* HEADER */
        .header {
            background-color: #4f46e5;
            background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
            padding: 30px 20px;
            text-align: center;
            color: #ffffff;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #ffffff !important;
        }

        .header p {
            margin: 5px 0 0;
            font-size: 13px;
            opacity: 0.8;
            color: #e0e7ff !important;
        }

        /* SECTIONS */
        .section-title {
            padding: 20px 25px 10px;
            font-size: 16px;
            font-weight: 700;
            color: #1f2937;
            border-bottom: 2px solid #f3f4f6;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .dot {
            height: 12px;
            width: 12px;
            background-color: #374151;
            border-radius: 50%;
            display: inline-block;
            margin-right: 10px;
        }

        .content-block {
            padding: 0 25px 20px;
        }

        /* GRID / TABLES */
        .summary-grid {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
        }

        .summary-item {
            display: table-cell;
            width: 25%;
            background: #f9fafb;
            padding: 10px;
            text-align: center;
            border-radius: 6px;
            border: 1px solid #e5e7eb;
        }

        .summary-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #6b7280;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .summary-value {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
        }

        /* TABLO AYARLARI */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 13px;
        }

        /* Sayılar SAĞA yaslı */
        .data-table th,
        .data-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            text-align: right;
        }

        .data-table th {
            background-color: #f3f4f6;
            color: #4b5563;
            font-weight: 600;
            border-bottom: 2px solid #e5e7eb;
        }

        /* İlk sütun SOLA yaslı (Metinler) */
        .data-table th:first-child,
        .data-table td:first-child {
            text-align: left;
            width: 40%;
        }

        .data-table tr:last-child td {
            border-bottom: none;
        }

        /* STATUS COLORS */
        .badge {
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            color: white;
            display: inline-block;
            white-space: nowrap;
        }

        .bg-new {
            background-color: #ef4444;
        }

        .bg-process {
            background-color: #f59e0b;
        }

        .bg-done {
            background-color: #10b981;
        }

        .bg-blue {
            background-color: #3b82f6;
        }

        /* EKSTRA BİLGİ (Geçmişten Kapanan) */
        .extra-info {
            font-size: 9px;
            color: #10b981;
            display: block;
            font-weight: normal;
            margin-top: 2px;
        }

        /* HIGHLIGHT BOX */
        .highlight-box {
            background-color: #f0fdf4;
            border: 1px solid #dcfce7;
            border-radius: 6px;
            padding: 10px;
            font-size: 12px;
            color: #166534;
            margin-bottom: 5px;
            text-align: left;
        }

        .highlight-title {
            font-weight: bold;
            display: block;
            margin-bottom: 2px;
            text-transform: uppercase;
            font-size: 10px;
        }

        /* FOOTER */
        .footer {
            background-color: #f9fafb;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
        }
    </style>
</head>

<body>
    <center class="wrapper">
        <table class="main-table">
            <tr>
                <td class="header">
                    <h1>{{ $raporBasligi }}</h1>
                    <p>{{ $tarih }} tarihli sistem durum raporudur.</p>
                </td>
            </tr>

            @if(isset($raporData['sikayet_genel']))
                <tr>
                    <td>
                        <div class="section-title">
                            <span class="dot" style="background-color: #db2777;"></span> MÜŞTERİ ŞİKAYETLERİ
                        </div>
                        <div class="content-block">

                            <div class="summary-grid">
                                <div class="summary-item" style="background-color: #fdf2f8; border-color: #fce7f3;">
                                    <div class="summary-label" style="color: #be185d;">Toplam</div>
                                    <div class="summary-value" style="color: #be185d;">
                                        <a href="{{ route('admin.reports.daily_complaints') }}"
                                            style="text-decoration:none; color:inherit;">
                                            {{ $raporData['sikayet_genel']['toplam_kayit'] }}
                                        </a>
                                    </div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Yeni</div>
                                    <div class="summary-value" style="color: #ef4444;">
                                        <a href="{{ route('admin.reports.daily_complaints') }}"
                                            style="text-decoration:none; color:inherit;">
                                            {{ $raporData['sikayet_genel']['bekleyen_yeni'] }}
                                        </a>
                                    </div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">İşlemde</div>
                                    <div class="summary-value" style="color: #f59e0b;">
                                        <a href="{{ route('admin.reports.daily_complaints') }}"
                                            style="text-decoration:none; color:inherit;">
                                            {{ $raporData['sikayet_genel']['islemde_olan'] }}
                                        </a>
                                    </div>
                                </div>
                                <div class="summary-item">
                                    <div class="summary-label">Çözülen</div>
                                    <div class="summary-value" style="color: #10b981;">
                                        <a href="{{ route('admin.reports.daily_complaints') }}"
                                            style="text-decoration:none; color:inherit;">
                                            {{ $raporData['sikayet_genel']['cozumlenen'] }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <table class="data-table" style="margin-top: 15px;">
                                <thead>
                                    <tr>
                                        <th>Dönem</th>
                                        <th>Gelen</th>
                                        <th>Kapanan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><strong>Bugün</strong></td>
                                        <td>{{ $raporData['sikayet_zaman']['bugun']['gelen'] }}</td>
                                        <td>
                                            {{ $raporData['sikayet_zaman']['bugun']['kapanan'] }}
                                            @if($raporData['sikayet_zaman']['bugun']['kapanan'] > $raporData['sikayet_zaman']['bugun']['gelen'])
                                                <span class="extra-info">(Geçmişten:
                                                    +{{ $raporData['sikayet_zaman']['bugun']['kapanan'] - $raporData['sikayet_zaman']['bugun']['gelen'] }})</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bu Hafta / Geçen H.</td>
                                        <td>{{ $raporData['sikayet_zaman']['bu_hafta']['gelen'] }} / <span
                                                style="color:#9ca3af;">{{ $raporData['sikayet_zaman']['gecen_hafta']['gelen'] }}</span>
                                        </td>
                                        <td>
                                            {{ $raporData['sikayet_zaman']['bu_hafta']['kapanan'] }} / <span
                                                style="color:#9ca3af;">{{ $raporData['sikayet_zaman']['gecen_hafta']['kapanan'] }}</span>
                                            {{-- Sadece bu hafta için fark kontrolü --}}
                                            @if($raporData['sikayet_zaman']['bu_hafta']['kapanan'] > $raporData['sikayet_zaman']['bu_hafta']['gelen'])
                                                <span class="extra-info">(Geçmişten:
                                                    +{{ $raporData['sikayet_zaman']['bu_hafta']['kapanan'] - $raporData['sikayet_zaman']['bu_hafta']['gelen'] }})</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bu Ay / Geçen Ay</td>
                                        <td>{{ $raporData['sikayet_zaman']['bu_ay']['gelen'] }} / <span
                                                style="color:#9ca3af;">{{ $raporData['sikayet_zaman']['gecen_ay']['gelen'] }}</span>
                                        </td>
                                        <td>
                                            {{ $raporData['sikayet_zaman']['bu_ay']['kapanan'] }} / <span
                                                style="color:#9ca3af;">{{ $raporData['sikayet_zaman']['gecen_ay']['kapanan'] }}</span>
                                            @if($raporData['sikayet_zaman']['bu_ay']['kapanan'] > $raporData['sikayet_zaman']['bu_ay']['gelen'])
                                                <span class="extra-info">(Geçmişten:
                                                    +{{ $raporData['sikayet_zaman']['bu_ay']['kapanan'] - $raporData['sikayet_zaman']['bu_ay']['gelen'] }})</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bu Yıl / Geçen Yıl</td>
                                        <td>{{ $raporData['sikayet_zaman']['bu_yil']['gelen'] }} / <span
                                                style="color:#9ca3af;">{{ $raporData['sikayet_zaman']['gecen_yil']['gelen'] }}</span>
                                        </td>
                                        <td>
                                            {{ $raporData['sikayet_zaman']['bu_yil']['kapanan'] }} / <span
                                                style="color:#9ca3af;">{{ $raporData['sikayet_zaman']['gecen_yil']['kapanan'] }}</span>
                                            @if($raporData['sikayet_zaman']['bu_yil']['kapanan'] > $raporData['sikayet_zaman']['bu_yil']['gelen'])
                                                <span class="extra-info">(Geçmişten:
                                                    +{{ $raporData['sikayet_zaman']['bu_yil']['kapanan'] - $raporData['sikayet_zaman']['bu_yil']['gelen'] }})</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            @if(isset($raporData['sikayet_ceyrekler']))
                                <table class="data-table" style="margin-top: 15px;">
                                    <thead>
                                        <tr>
                                            <th>Çeyrek</th>
                                            <th>Gelen</th>
                                            <th>Kapanan</th>
                                            <th>Başarı</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($raporData['sikayet_ceyrekler'] as $key => $qData)
                                            <tr>
                                                <td><strong>{{ date('Y') }} {{ $key }}</strong></td>
                                                <td>{{ $qData['gelen'] }}</td>
                                                <td>
                                                    {{ $qData['kapanan'] }}
                                                    {{-- Çeyrek bazlı geçmiş kontrolü --}}
                                                    @if($qData['kapanan'] > $qData['gelen'])
                                                        <span class="extra-info" style="color:#10b981;">(Geçmişten:
                                                            +{{ $qData['kapanan'] - $qData['gelen'] }})</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($qData['gelen'] > 0)
                                                        <span
                                                            style="font-size:10px; color:#6b7280;">%{{ round(($qData['kapanan'] / $qData['gelen']) * 100) }}</span>
                                                    @else
                                                        <span style="font-size:10px; color:#ccc;">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            @if(isset($raporData['sikayet_bolumler']))
                                <div
                                    style="margin-top: 15px; font-weight: bold; font-size: 13px; color: #374151; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px;">
                                    Kategori Bazlı Dağılım</div>
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Kategori</th>
                                            <th>Top.</th>
                                            <th>Yeni</th>
                                            <th>İşl.</th>
                                            <th>Bit.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($raporData['sikayet_bolumler'] as $row)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('admin.reports.daily_complaints', ['search' => $row['kategori_adi']]) }}"
                                                        style="text-decoration:none; color:inherit;">
                                                        {{ $row['kategori_adi'] }}
                                                    </a>
                                                </td>
                                                <td style="font-weight:bold;">
                                                    <a href="{{ route('admin.reports.daily_complaints', ['search' => $row['kategori_adi']]) }}"
                                                        style="text-decoration:none; color:inherit;">
                                                        {{ $row['toplam'] }}
                                                    </a>
                                                </td>
                                                <td>@if($row['yeni'] > 0) <span class="badge bg-new">{{ $row['yeni'] }}</span> @else
                                                - @endif</td>
                                                <td>@if($row['islemde'] > 0) <span
                                                class="badge bg-process">{{ $row['islemde'] }}</span> @else - @endif</td>
                                                <td>@if($row['kapali'] > 0) <span class="badge bg-done">{{ $row['kapali'] }}</span>
                                                @else - @endif</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                        </div>
                    </td>
                </tr>
            @endif

            @if(isset($raporData['iaa_ozet']))
                <tr>
                    <td>
                        <div class="section-title">
                            <span class="dot" style="background-color: #2563eb;"></span> İAA ÖNERİ SİSTEMİ
                        </div>
                        <div class="content-block">

                            <div class="summary-grid" style="margin-bottom: 10px;">
                                <div class="summary-item"
                                    style="background-color: #eff6ff; border-color: #dbeafe; width: 33%;">
                                    <div class="summary-label" style="color: #1e40af;">Genel Toplam Tamamlanan</div>
                                    <div class="summary-value" style="color: #1e40af;">
                                        {{ $raporData['iaa_ozet']['genel_tamamlanan'] }} <span
                                            style="font-size:10px; color:#93c5fd; font-weight:normal;">/
                                            {{ $raporData['iaa_ozet']['toplam'] }}</span>
                                    </div>
                                </div>
                                <div class="summary-item" style="width: 33%;">
                                    <div class="summary-label">Bu Yıl Tamamlanan</div>
                                    <div class="summary-value" style="color: #2563eb;">
                                        {{ $raporData['iaa_ozet']['bu_yil_biten'] }}
                                    </div>
                                </div>
                                <div class="summary-item" style="width: 33%;">
                                    <div class="summary-label">Bu Ay Tamamlanan</div>
                                    <div class="summary-value" style="color: #3b82f6;">
                                        {{ $raporData['iaa_ozet']['bu_ay_biten'] }}
                                    </div>
                                </div>
                            </div>

                            <div style="font-size: 11px; color: #6b7280; margin-bottom: 5px;"><strong>Güncel
                                    Bekleyenler:</strong></div>
                            <div class="summary-grid">
                                <div class="summary-item" style="width: 33%;">
                                    <div class="summary-label">Havuzda</div>
                                    <div class="summary-value">{{ $raporData['iaa_ozet']['havuz'] }}</div>
                                </div>
                                <div class="summary-item" style="width: 33%;">
                                    <div class="summary-label">Devam Eden</div>
                                    <div class="summary-value" style="color: #f59e0b;">{{ $raporData['iaa_ozet']['devam'] }}
                                    </div>
                                </div>
                                <div class="summary-item" style="width: 33%;">
                                    <div class="summary-label">Ort. Çözüm Hızı</div>
                                    <div class="summary-value" style="color: #059669; font-size: 14px;">
                                        {{ $raporData['iaa_hiz'] }}
                                    </div>
                                </div>
                            </div>

                            <table class="data-table" style="margin-top: 15px;">
                                <thead>
                                    <tr>
                                        <th>Dönem</th>
                                        <th>Gelen Öneri</th>
                                        <th>Tamamlanan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Bu Hafta / Geçen H.</td>
                                        <td>{{ $raporData['iaa_zaman']['bu_hafta']['gelen'] }} / <span
                                                style="color:#9ca3af;">{{ $raporData['iaa_zaman']['gecen_hafta']['gelen'] }}</span>
                                        </td>
                                        <td>{{ $raporData['iaa_zaman']['bu_hafta']['biten'] }} / <span
                                                style="color:#9ca3af;">{{ $raporData['iaa_zaman']['gecen_hafta']['biten'] }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bu Ay / Geçen Ay</td>
                                        <td>{{ $raporData['iaa_zaman']['bu_ay']['gelen'] }} / <span
                                                style="color:#9ca3af;">{{ $raporData['iaa_zaman']['gecen_ay']['gelen'] }}</span>
                                        </td>
                                        <td>{{ $raporData['iaa_zaman']['bu_ay']['biten'] }} / <span
                                                style="color:#9ca3af;">{{ $raporData['iaa_zaman']['gecen_ay']['biten'] }}</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Bu Yıl / Geçen Yıl</td>
                                        <td>{{ $raporData['iaa_zaman']['bu_yil']['gelen'] }} / <span
                                                style="color:#9ca3af;">{{ $raporData['iaa_zaman']['gecen_yil']['gelen'] }}</span>
                                        </td>
                                        <td>{{ $raporData['iaa_zaman']['bu_yil']['biten'] }} / <span
                                                style="color:#9ca3af;">{{ $raporData['iaa_zaman']['gecen_yil']['biten'] }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            @if(isset($raporData['iaa_durum_detay']) && count($raporData['iaa_durum_detay']) > 0)
                                <div
                                    style="margin-top:15px; font-size:11px; font-weight:bold; color:#374151; border-bottom:1px solid #e5e7eb; padding-bottom:5px;">
                                    Detaylı Durum Dağılımı
                                </div>
                                <table class="data-table">
                                    @foreach($raporData['iaa_durum_detay'] as $durum => $sayi)
                                        <tr>
                                            <td>{{ $durum }}</td>
                                            <td><strong>{{ $sayi }}</strong></td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endif

                            <div
                                style="margin-top: 15px; background: #f9fafb; padding: 10px; border-radius: 6px; font-size: 11px; color: #4b5563;">
                                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                                    <div style="flex: 1;">
                                        <div class="highlight-box">
                                            <span class="highlight-title">🏆 En Çok Öneri Veren Bölüm</span>
                                            {{ $raporData['iaa_en_cok_bolum'] }}
                                        </div>
                                    </div>
                                    <div style="flex: 1;">
                                        <div class="highlight-box"
                                            style="background-color:#fff7ed; border-color:#ffedd5; color:#9a3412;">
                                            <span class="highlight-title">🚀 En Hızlı Çözen Takım</span>
                                            {{ $raporData['iaa_en_cok_takim'] }}
                                        </div>
                                    </div>
                                </div>
                                @if($raporData['iaa_son'])
                                    <div style="border-top: 1px dashed #e5e7eb; padding-top: 5px;">
                                        <strong>📅 Son Öneri:</strong> {{ $raporData['iaa_son']['tarih'] }}
                                        <span class="badge"
                                            style="background-color: #6b7280; font-size: 9px; padding: 1px 4px;">{{ $raporData['iaa_son']['tur'] }}</span>
                                        <br>
                                        <i>"{{ Str::limit($raporData['iaa_son']['baslik'], 50) }}"</i>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </td>
                </tr>
            @endif

            @if(isset($raporData['disiplin']) || isset($raporData['arabuluculuk']))
                <tr>
                    <td>
                        <div class="section-title">
                            <span class="dot" style="background-color: #d97706;"></span> DİĞER SÜREÇLER
                        </div>
                        <div class="content-block">
                            <table class="data-table">
                                @if(isset($raporData['disiplin']))
                                    <tr>
                                        <td><strong>Disiplin Süreçleri</strong></td>
                                        <td>
                                            <span class="badge bg-new">{{ $raporData['disiplin']['acik'] }} Açık</span>
                                            @if($raporData['disiplin']['savunma'] > 0)
                                                <span class="badge bg-process">{{ $raporData['disiplin']['savunma'] }}
                                                    Savunma</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if(isset($raporData['arabuluculuk']))
                                    <tr>
                                        <td><strong>Arabuluculuk</strong></td>
                                        <td>
                                            <span class="badge bg-blue">{{ $raporData['arabuluculuk']['aktif'] }} Aktif</span>
                                            @if($raporData['arabuluculuk']['odeme'] > 0)
                                                <span class="badge bg-process"
                                                    style="background-color: #d97706;">{{ $raporData['arabuluculuk']['odeme'] }}
                                                    Ödeme</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </td>
                </tr>
            @endif

            <tr>
                <td class="footer">
                    <p>© {{ date('Y') }} Köksan Portal Yönetim Sistemi</p>
                    <p>Bu rapor sistem tarafından otomatik oluşturulmuştur.</p>
                </td>
            </tr>
        </table>
    </center>
</body>

</html>