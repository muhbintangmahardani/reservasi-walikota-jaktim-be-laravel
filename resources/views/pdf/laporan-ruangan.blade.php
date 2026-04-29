<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        /* CSS KHUSUS DOMPDF */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
        }
        
        /* Kop Surat Header */
        .header-table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 20px; 
            border-bottom: 3px solid #0f172a; 
            padding-bottom: 10px;
        }
        .header-table td { 
            border: none; 
            vertical-align: middle; 
            padding: 0;
        }
        .logo-img { 
            width: 75px; 
            display: block; 
            margin: 0 auto;
        }
        .kop-text { 
            text-align: center; 
        }
        .kop-text h2 { 
            margin: 0; 
            font-size: 18px; 
            color: #0f172a; 
            letter-spacing: 1px;
        }
        .kop-text p { 
            margin: 3px 0 0 0; 
            font-size: 13px; 
            color: #334155; 
            font-weight: bold;
        }
        .kop-text .address {
            font-size: 10px;
            color: #64748b;
            font-weight: normal;
        }
        
        .report-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            color: #2563eb;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        /* Tabel Data */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 6px;
            text-align: left;
            vertical-align: top;
        }
        table.data-table th {
            background-color: #f8fafc;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            text-align: center;
        }
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; color: #0f172a; }
        
        /* Status Badges */
        .badge-verified { color: #15803d; font-weight: bold; }
        .badge-pending { color: #b45309; font-weight: bold; }
        .badge-rejected { color: #b91c1c; font-weight: bold; }
        
        .footer {
            margin-top: 30px;
            font-size: 10px;
            color: #94a3b8;
            text-align: right;
            font-style: italic;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td width="15%" class="text-center">
                @if(!empty($logo))
                    <img src="{{ $logo }}" class="logo-img" alt="Logo">
                @endif
            </td>
            <td width="70%" class="kop-text">
                <h2>PEMERINTAH KOTA ADMINISTRASI JAKARTA TIMUR</h2>
                <p>SEKRETARIAT KOTA JAKARTA TIMUR</p>
                <p class="address">Jl. Dr. Sumarno No.29, Pulo Gebang, Cakung, Jakarta Timur, Daerah Khusus Ibukota Jakarta 13950</p>
            </td>
            <td width="15%"></td>
        </tr>
    </table>

    <div class="report-title">
        REKAPITULASI PENGGUNAAN RUANGAN & AGENDA PIMPINAN<br>
        <span style="font-size: 12px; color: #475569;">{{ $title }}</span>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="22%">Nama Kegiatan</th>
                <th width="15%">Asal Pemohon</th>
                <th width="18%">Waktu Pelaksanaan</th>
                <th width="20%">Lokasi Ruangan</th>
                <th width="10%">Pelaksana</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reservations as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-bold">{{ $item->event_name }}</td>
                    <td>{{ $item->origin_unit ?: 'Sekretariat Pimpinan' }}</td>
                    
                    <td>
                        @if(!empty($item->start_time) && !empty($item->end_time))
                            {{ \Carbon\Carbon::parse($item->start_time)->translatedFormat('d M Y') }}<br>
                            <span style="color: #475569; font-size: 10px;">
                                Pukul {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }} WIB
                            </span>
                        @else
                            <span style="color: #ef4444; font-style: italic;">Waktu tidak valid</span>
                        @endif
                    </td>
                    
                    <td>
                        @if($item->location_type == 'ruangan_terdaftar' && !empty($item->room))
                            {{ $item->room->room_name ?? $item->room->name }}
                        @else
                            {{ $item->other_location ?: ($item->location ?: '-') }}
                        @endif
                    </td>
                    
                    <td>{{ $item->pejabat_pelaksana ?: 'Pimpinan' }}</td>
                    
                    <td class="text-center">
                        @if($item->status == 'verified')
                            <span class="badge-verified">DISETUJUI</span>
                        @elseif($item->status == 'rejected')
                            <span class="badge-rejected">DITOLAK</span>
                        @else
                            <span class="badge-pending">MENUNGGU</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center" style="padding: 30px 10px; color: #64748b;">
                        <i>Tidak ada data reservasi yang ditemukan pada periode ini.</i>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak pada: {{ \Carbon\Carbon::now('Asia/Jakarta')->translatedFormat('d F Y') }} Pukul {{ \Carbon\Carbon::now('Asia/Jakarta')->format('H:i') }} WIB<br>
        Generate by Smart Room App
    </div>

</body>
</html>