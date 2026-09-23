<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ isset($title) ? $title.' | SPK Menu MBG' : 'SPK Menu MBG' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400&family=IBM+Plex+Sans:ital,wght@0,400;0,500;0,600;1,400&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>body{margin:0;background:#F4F1EA;color:#1B1A17}a{color:inherit;text-decoration:none}</style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"IBM Plex Sans"', 'system-ui', 'sans-serif'],
                        display: ['Fraunces', 'Georgia', 'serif'],
                        mono: ['"IBM Plex Mono"', 'ui-monospace', 'monospace'],
                    },
                    colors: {
                        paper: { DEFAULT: '#F4F1EA', 2: '#ECE8DE', 3: '#E3DED2' },
                        ink: { DEFAULT: '#1B1A17', 2: '#3F3B34', 3: '#5C574D' },
                        line: { DEFAULT: '#D6D0C2', strong: '#B8B1A0' },
                        leaf: { DEFAULT: '#1E5A3C', light: '#E1ECE4', dark: '#153F2A' },
                        ember: { DEFAULT: '#B4471F', light: '#F5E4DC' },
                    },
                },
            },
        };
    </script>
    <style>
        html, body { scroll-behavior: smooth; scrollbar-width: none; -ms-overflow-style: none; overflow-x: hidden; max-width: 100vw; }
        html::-webkit-scrollbar, body::-webkit-scrollbar { display: none; width: 0; height: 0; }
        body { -webkit-font-smoothing: auto; text-rendering: optimizeLegibility; }
        .num { font-family: "IBM Plex Mono", ui-monospace, monospace; font-variant-numeric: tabular-nums; font-weight: 500; }
        .eyebrow { font-family: "IBM Plex Mono", ui-monospace, monospace; font-size: 12px; font-weight: 500; letter-spacing: 0.1em; text-transform: uppercase; color: #5C574D; }
        .ico { width: 1.125rem; height: 1.125rem; flex-shrink: 0; stroke: currentColor; fill: none; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; }
        .field { width: 100%; background: #FBFAF6; border: 1px solid #B8B1A0; border-radius: 2px; padding: 0.625rem 0.75rem; color: #1B1A17; transition: border-color .15s, box-shadow .15s; }
        .field::placeholder { color: #8A857A; }
        .field:focus { outline: none; border-color: #1E5A3C; box-shadow: 0 0 0 3px rgba(30, 90, 60, .18); }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: .5rem; border-radius: 2px; font-weight: 500; font-size: .875rem; padding: .625rem 1rem; transition: background-color .15s, color .15s, border-color .15s; white-space: nowrap; }
        .btn:focus-visible { outline: 2px solid #1E5A3C; outline-offset: 2px; }
        .btn-ink { background: #1B1A17; color: #F4F1EA; }
        .btn-ink:hover { background: #1E5A3C; }
        .btn-ink:disabled { background: #4F4B43; cursor: progress; }
        .btn-line { background: transparent; color: #1B1A17; border: 1px solid #B8B1A0; }
        .btn-line:hover { border-color: #1B1A17; background: #ECE8DE; }
        .rule { border-top: 1px solid #D6D0C2; }
        .rule-strong { border-top: 2px solid #1B1A17; }
        .dotted-bg { background-image: radial-gradient(#C9C3B4 1px, transparent 1px); background-size: 14px 14px; }
        @keyframes sweep { from { transform: translateX(-100%); } to { transform: translateX(100%); } }
        .sweep { position: relative; overflow: hidden; }
        .sweep::after { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, transparent, rgba(244, 241, 234, .35), transparent); animation: sweep 1.2s linear infinite; }
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
        details[open] .chev { transform: rotate(90deg); }
    </style>
    @livewireStyles
</head>
<body class="min-h-screen bg-paper font-sans text-ink">
    <header class="border-b border-line bg-paper">
        <div class="mx-auto flex max-w-5xl items-center justify-between gap-2 px-4 py-4 sm:px-8">
            <a href="{{ auth()->check() ? route('rekomendasi.index') : url('/') }}" class="flex min-w-0 items-center gap-2 sm:gap-2.5">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center bg-leaf text-paper">
                    <svg class="ico" viewBox="0 0 24 24"><path d="M4 11h16M5 11a7 7 0 0 0 14 0M9 3.5v3M12 3v3.5M15 3.5v3M8 21h8"/></svg>
                </span>
                <span class="truncate font-display text-base font-medium leading-none tracking-tight sm:text-lg">SPK Menu MBG</span>
            </a>
            @auth
                <nav class="flex shrink-0 items-center gap-2.5 text-sm sm:gap-5">
                    <a href="{{ route('rekomendasi.index') }}" class="hidden text-ink-2 hover:text-ink sm:inline {{ request()->routeIs('rekomendasi.*') ? 'underline decoration-leaf decoration-2 underline-offset-[6px]' : '' }}">Rekomendasi</a>
                    <a href="{{ route('dokumentasi.metode') }}" class="hidden text-ink-2 hover:text-ink sm:inline {{ request()->routeIs('dokumentasi.*') ? 'underline decoration-leaf decoration-2 underline-offset-[6px]' : '' }}">Cara hitung</a>
                    <span class="hidden h-4 w-px bg-line-strong sm:block"></span>
                    <span class="hidden leading-tight sm:block">
                        <span class="block max-w-[11rem] truncate text-ink">{{ auth()->user()->name }}</span>
                        <span class="eyebrow">{{ auth()->user()->isAdmin() ? 'Admin' : 'Operator SPPG' }}</span>
                    </span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="btn btn-line px-2.5 py-1.5 sm:px-3">
                            <svg class="ico h-4 w-4" viewBox="0 0 24 24"><path d="M10 4H5v16h5M14 8l4 4-4 4M18 12H9"/></svg>
                            Keluar
                        </button>
                    </form>
                </nav>
            @else
                <nav class="flex shrink-0 items-center gap-5 text-sm">
                    @if (! request()->routeIs('login'))
                        <a href="{{ route('login') }}" class="btn btn-line px-3 py-1.5">Masuk</a>
                    @endif
                </nav>
            @endauth
        </div>
    </header>
    <main class="mx-auto max-w-5xl px-5 py-10 sm:px-8 sm:py-14">
        {{ $slot }}
    </main>
    <footer class="mx-auto max-w-5xl px-5 pb-10 sm:px-8">
        <div class="rule flex flex-col gap-2 pt-5 text-xs text-ink-3 sm:flex-row sm:items-center sm:justify-between">
            <span>Acuan gizi: TKPI, Permenkes 28/2019, Juknis BGN 401.1/2025.</span>
            <span class="num">{{ date('Y') }}</span>
        </div>
    </footer>
    @livewireScripts
</body>
</html>
