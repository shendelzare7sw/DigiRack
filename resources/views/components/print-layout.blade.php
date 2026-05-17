@props([
    'title' => 'Dokumen',
    'subtitle' => null,
    'docLabel' => 'DOKUMEN',
    'backUrl' => null,
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — DigiRack</title>
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo.jpg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        :root { --brand-navy: #14213d; --brand-blue: #2563eb; --brand-orange: #f97316; }
        html, body { background:#f3f4f6; font-family:'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }

        /* Full-page diagonal watermark — also rendered when printing */
        .dr-watermark {
            position: fixed; inset: 0; z-index: 0; overflow: hidden;
            pointer-events: none; user-select: none;
            display: flex; align-items: center; justify-content: center;
        }
        .dr-watermark__logo {
            position: absolute; top: 50%; left: 50%;
            width: min(60vw, 460px); height: auto;
            transform: translate(-50%, -50%) rotate(-30deg);
            opacity: .06;
        }
        .dr-watermark__grid {
            position: absolute; inset: -25%;
            transform: rotate(-30deg);
            display: flex; flex-direction: column; gap: 18px;
        }
        .dr-watermark__row {
            white-space: nowrap;
            font-family: 'Sora', sans-serif; font-weight: 800;
            font-size: 34px; letter-spacing: 8px;
            color: #14213d; opacity: .045;
        }
        .dr-sheet { position: relative; z-index: 1; }

        @media print {
            html, body { background:#fff !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .no-print { display: none !important; }
            .dr-watermark__logo { opacity: .07; }
            .dr-watermark__row { opacity: .05; }
            .dr-sheet { box-shadow: none !important; border: none !important; margin: 0 !important; max-width: 100% !important; }
            .print-page { padding: 0 !important; }
            @page { margin: 12mm; }
            tr, .avoid-break { break-inside: avoid; }
        }
    </style>
</head>
<body class="text-gray-900 antialiased">

    <div class="dr-watermark" aria-hidden="true">
        <div class="dr-watermark__grid">
            @for ($i = 0; $i < 14; $i++)
                <div class="dr-watermark__row">DIGIRACK&nbsp;&nbsp;DIGIRACK&nbsp;&nbsp;DIGIRACK&nbsp;&nbsp;DIGIRACK&nbsp;&nbsp;DIGIRACK&nbsp;&nbsp;DIGIRACK</div>
            @endfor
        </div>
        <img class="dr-watermark__logo" src="{{ asset('images/logo-digirack.png') }}" alt="">
    </div>

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
                <div class="flex items-center gap-3 min-w-0">
                    <img src="{{ asset('images/logo-digirack.png') }}" alt="DigiRack" class="h-9 sm:h-11 w-auto shrink-0">
                    <div class="min-w-0">
                        <p class="font-display font-extrabold text-base sm:text-lg text-brand-navy leading-tight">DigiRack</p>
                        <p class="text-[10px] sm:text-xs text-gray-500 leading-tight">PT DigiRack Infrastruktur Digital</p>
                    </div>
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
                    Dokumen ini dihasilkan secara otomatis oleh sistem DigiRack pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.
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
