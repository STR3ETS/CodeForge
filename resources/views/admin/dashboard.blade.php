<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800,900" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('fontawesome/css/all.min.css') }}">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F7F4F3] min-h-screen" style="font-family: 'Instrument Sans', sans-serif;">

    {{-- Top bar --}}
    <header class="bg-white border-b border-[#564D4A]/6">
        <div class="max-w-7xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#5B2333] flex items-center justify-center">
                    <img src="/assets/logo-wit.png" class="max-h-4" alt="Logo">
                </div>
                <span class="font-black text-lg tracking-tight text-[#564D4A]">
                    Brain<span class="text-[#5B2333]">Forge.</span>
                    <span class="text-xs font-bold text-[#564D4A]/30 ml-1">ADMIN</span>
                </span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-[#564D4A]/50 hover:text-[#564D4A] transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Terug naar Dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="text-xs font-semibold text-red-500/60 hover:text-red-500 transition cursor-pointer">
                        Uitloggen
                    </button>
                </form>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-8">

        {{-- Flash messages --}}
        @if(session('flash'))
            <div class="mb-6 px-5 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold">
                {{ session('flash') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 px-5 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        {{-- Revenue overview --}}
        <h2 class="text-xl font-black text-[#564D4A] tracking-tight mb-6">Overzicht</h2>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">
            @php
                $stats = [
                    ['label' => 'Totaal gebruikers', 'value' => $totalUsers, 'icon' => 'fa-solid fa-users', 'color' => 'bg-[#5B2333]/10 text-[#5B2333]'],
                    ['label' => 'Pro leden', 'value' => $proUsers, 'icon' => 'fa-solid fa-crown', 'color' => 'bg-yellow-100 text-yellow-600'],
                    ['label' => 'Free leden', 'value' => $freeUsers, 'icon' => 'fa-solid fa-user', 'color' => 'bg-[#564D4A]/8 text-[#564D4A]/60'],
                    ['label' => 'Actieve subs', 'value' => $activeSubscriptions, 'icon' => 'fa-solid fa-credit-card', 'color' => 'bg-green-100 text-green-600'],
                    ['label' => 'MRR', 'value' => number_format($mrr, 2, ',', '.'), 'icon' => 'fa-solid fa-euro-sign', 'color' => 'bg-emerald-100 text-emerald-600'],
                    ['label' => 'Maandelijks / Jaar', 'value' => $monthlyCount . ' / ' . $yearlyCount, 'icon' => 'fa-solid fa-chart-pie', 'color' => 'bg-blue-100 text-blue-600'],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div class="bg-white rounded-2xl border border-[#564D4A]/6 p-5">
                    <div class="w-9 h-9 rounded-xl {{ $stat['color'] }} flex items-center justify-center mb-3">
                        <i class="{{ $stat['icon'] }} text-sm"></i>
                    </div>
                    <p class="text-[1.1rem] font-black text-[#564D4A]">{{ $stat['value'] }}</p>
                    <p class="text-[10px] font-semibold text-[#564D4A]/40 uppercase tracking-wider mt-0.5">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Users table --}}
        <div class="mb-10" x-data="{ search: '' }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-black text-[#564D4A] tracking-tight">Alle Gebruikers</h2>
                <input type="text" x-model="search" placeholder="Zoeken op naam of email..."
                    class="text-xs px-4 py-2.5 rounded-xl border border-[#564D4A]/10 bg-white focus:outline-none focus:border-[#5B2333]/30 w-64">
            </div>

            <div class="bg-white rounded-2xl border border-[#564D4A]/6 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-[#564D4A]/4 text-left">
                                <th class="px-5 py-3 text-[10px] uppercase tracking-wider font-bold text-[#564D4A]/40">ID</th>
                                <th class="px-5 py-3 text-[10px] uppercase tracking-wider font-bold text-[#564D4A]/40">Naam</th>
                                <th class="px-5 py-3 text-[10px] uppercase tracking-wider font-bold text-[#564D4A]/40">Email</th>
                                <th class="px-5 py-3 text-[10px] uppercase tracking-wider font-bold text-[#564D4A]/40">Plan</th>
                                <th class="px-5 py-3 text-[10px] uppercase tracking-wider font-bold text-[#564D4A]/40">Sub status</th>
                                <th class="px-5 py-3 text-[10px] uppercase tracking-wider font-bold text-[#564D4A]/40">Sinds</th>
                                <th class="px-5 py-3 text-[10px] uppercase tracking-wider font-bold text-[#564D4A]/40">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#564D4A]/6">
                            @foreach ($users as $u)
                                <tr x-show="!search || '{{ strtolower($u['name'] . ' ' . $u['email']) }}'.includes(search.toLowerCase())"
                                    class="hover:bg-[#564D4A]/2 transition">
                                    <td class="px-5 py-3 text-[#564D4A]/40 font-mono text-xs">#{{ $u['id'] }}</td>
                                    <td class="px-5 py-3 font-semibold text-[#564D4A]">
                                        {{ $u['name'] }}
                                        @if($u['is_admin'])
                                            <span class="ml-1 px-1.5 py-0.5 rounded bg-red-100 text-red-600 text-[9px] font-bold">ADMIN</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-[#564D4A]/60">{{ $u['email'] }}</td>
                                    <td class="px-5 py-3">
                                        @if($u['plan'] === 'pro')
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-[#5B2333]/10 text-[#5B2333] text-[10px] font-bold">
                                                <i class="fa-solid fa-crown text-[8px]"></i> PRO
                                            </span>
                                        @else
                                            <span class="text-[#564D4A]/30 text-xs font-semibold">Free</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        @if($u['sub_status'] === 'active')
                                            <span class="inline-flex items-center gap-1 text-green-600 text-xs font-semibold">
                                                <i class="fa-solid fa-circle text-[5px]"></i> Actief
                                            </span>
                                        @elseif($u['sub_status'] === 'canceled')
                                            <span class="text-orange-500 text-xs font-semibold">Opgezegd</span>
                                        @elseif($u['sub_status'])
                                            <span class="text-[#564D4A]/40 text-xs font-semibold">{{ $u['sub_status'] }}</span>
                                        @else
                                            <span class="text-[#564D4A]/20 text-xs">—</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-[#564D4A]/40 text-xs">{{ $u['created_at'] }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2">
                                            @if($u['plan'] === 'pro')
                                                <form method="POST" action="{{ route('admin.downgrade', $u['id']) }}"
                                                      onsubmit="return confirm('{{ $u['name'] }} downgraden naar Free?')">
                                                    @csrf
                                                    <button class="cursor-pointer px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 text-[10px] font-bold transition">
                                                        Downgrade
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.toggle-admin', $u['id']) }}"
                                                  onsubmit="return confirm('Admin-status wijzigen voor {{ $u['name'] }}?')">
                                                @csrf
                                                <button class="cursor-pointer px-2.5 py-1.5 rounded-lg bg-[#564D4A]/5 hover:bg-[#564D4A]/10 text-[#564D4A]/60 text-[10px] font-bold transition">
                                                    {{ $u['is_admin'] ? 'Verwijder admin' : 'Maak admin' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Cancelled / churned subscriptions --}}
        <div class="mb-10">
            <h2 class="text-xl font-black text-[#564D4A] tracking-tight mb-4">Opgezegde & Verlopen Abonnementen</h2>

            <div class="bg-white rounded-2xl border border-[#564D4A]/6 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-[#564D4A]/4 text-left">
                                <th class="px-5 py-3 text-[10px] uppercase tracking-wider font-bold text-[#564D4A]/40">Gebruiker</th>
                                <th class="px-5 py-3 text-[10px] uppercase tracking-wider font-bold text-[#564D4A]/40">Email</th>
                                <th class="px-5 py-3 text-[10px] uppercase tracking-wider font-bold text-[#564D4A]/40">Status</th>
                                <th class="px-5 py-3 text-[10px] uppercase tracking-wider font-bold text-[#564D4A]/40">Gestart</th>
                                <th class="px-5 py-3 text-[10px] uppercase tracking-wider font-bold text-[#564D4A]/40">Eindigt op</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#564D4A]/6">
                            @forelse ($cancelledSubs as $sub)
                                <tr class="hover:bg-[#564D4A]/2 transition">
                                    <td class="px-5 py-3 font-semibold text-[#564D4A]">{{ $sub['user_name'] }}</td>
                                    <td class="px-5 py-3 text-[#564D4A]/60">{{ $sub['user_email'] }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold
                                            {{ $sub['status'] === 'canceled' ? 'bg-orange-100 text-orange-600' : 'bg-red-100 text-red-600' }}">
                                            {{ ucfirst($sub['status']) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-[#564D4A]/40 text-xs">{{ $sub['created_at'] }}</td>
                                    <td class="px-5 py-3 text-[#564D4A]/40 text-xs">{{ $sub['ends_at'] ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-8 text-center text-[#564D4A]/30 text-sm">
                                        Nog geen opgezegde abonnementen.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</body>
</html>
