@props([
    'title' => 'Dokumen',
    'subtitle' => null,
    'docLabel' => 'DOKUMEN',
    'backUrl' => null,
    'watermark' => false,
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — Digital Hook</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        :root { --brand-navy: #14213d; --brand-blue: #2563eb; --brand-orange: #f97316; }
        html, body { background:#f3f4f6; font-family:'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }

        /* Full-page diagonal watermark — also rendered when printing.
           A square larger than the viewport diagonal is rotated and clipped,
           so the repeating text covers every corner (no empty right/bottom). */
        .dr-watermark {
            position: fixed; inset: 0; z-index: 0; overflow: hidden;
            pointer-events: none; user-select: none;
        }
        .dr-watermark__text {
            position: absolute; top: 50%; left: 50%;
            width: 240vmax; height: 240vmax;
            transform: translate(-50%, -50%) rotate(-30deg);
            display: flex; align-items: center; justify-content: center;
            text-align: center; word-break: break-word;
            font-family: 'Sora', sans-serif; font-weight: 800;
            font-size: 2.6vmax; line-height: 3.2; letter-spacing: .42em;
            text-transform: uppercase; color: #14213d; opacity: .05;
        }
        .dr-watermark__logo {
            position: absolute; top: 50%; left: 50%;
            width: min(55vw, 420px); height: auto;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: .06;
        }
        .dr-sheet { position: relative; z-index: 1; }

        @media print {
            html, body { background:#fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .dr-watermark__logo { opacity: .07; }
            .dr-watermark__text { opacity: .055; }
            .dr-sheet { box-shadow: none !important; border: none !important; margin: 0 !important; max-width: 100% !important; }
            .print-page { padding: 0 !important; }
            /* Always print the full table layout, never the mobile cards */
            .dr-print-table { display: block !important; }
            .dr-print-cards { display: none !important; }
            @page { margin: 12mm; }
            tr, .avoid-break { break-inside: avoid; }
        }
    </style>
</head>
<body class="text-gray-900 antialiased">

    @if($watermark)
        <div class="dr-watermark" aria-hidden="true">
            <div class="dr-watermark__text">{!! str_repeat('Digital Hook&nbsp; ', 700) !!}</div>
            <img class="dr-watermark__logo" src="{{ asset('images/digital-hook-logo.png') }}" alt="">
        </div>
    @endif

    {{-- Toolbar (hidden on print) --}}
    <div class="no-print sticky top-0 z-20 bg-brand-navy text-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-3">
            <a href="{{ $backUrl ?? url()->previous() }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/80 hover:text-white transition-colors">
                <x-icon name="arrow-left" class="w-4 h-4" /> <span class="hidden sm:inline">Kembali</span>
            </a>
            <span class="text-xs sm:text-sm font-bold tracking-wide truncate">{{ $title }}</span>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 bg-brand-blue hover:bg-blue-600 text-white text-sm font-bold px-4 py-2 rounded-xl transition-colors shrink-0">
                <x-icon name="printer" class="w-4 h-4" /> <span class="hidden sm:inline">Cetak / Simpan PDF</span><span class="sm:hidden">Cetak</span>
            </button>
        </div>
    </div>

    <div class="print-page max-w-3xl mx-auto px-3 sm:px-6 py-5 sm:py-8">
        <div class="dr-sheet bg-white rounded-2xl border border-gray-200 shadow-lg overflow-hidden">

            {{-- Document header --}}
            <div class="px-5 sm:px-8 py-5 sm:py-7 border-b border-gray-100 flex items-start justify-between gap-4">
                <div class="flex flex-col items-start gap-1.5 min-w-0">
                    <img src="{{ asset('images/digital-hook-logo.png') }}" alt="Digital Hook" class="h-9 sm:h-11 w-auto">
                    <p class="text-[10px] sm:text-xs text-gray-500 leading-tight">PT Infrakarsa Sinergi Digital</p>
                </div>
                <div class="text-right shrink-0">
                    <p class="font-display font-extrabold text-sm sm:text-xl text-brand-navy tracking-wider">{{ $docLabel }}</p>
                    @if($subtitle)
                        <p class="text-[10px] sm:text-xs text-gray-500 mt-0.5">{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            <div class="px-5 sm:px-8 py-5 sm:py-7">
                {{ $slot }}
            </div>

            <div class="px-5 sm:px-8 py-4 border-t border-gray-100 text-center">
                <p class="text-[10px] sm:text-[11px] text-gray-400 leading-relaxed">
                    Dokumen ini dihasilkan secara otomatis oleh sistem Digital Hook pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.
                    Sah tanpa tanda tangan & stempel basah.
                </p>
            </div>
        </div>
    </div>

    <script>
        // Allow ?print=1 to auto-open the print dialog (used by "Unduh" buttons).
        if (new URLSearchParams(window.location.search).get('print') === '1') {
            window.addEventListener('load', function () { setTimeout(function () { window.print(); }, 350); });
        }
    </script>
</body>
</html>
