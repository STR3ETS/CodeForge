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
    <header class="bg-white border-b border-[#564D4A]/6 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 h-14 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-[#5B2333] flex items-center justify-center">
                    <img src="/assets/logo-wit.png" class="max-h-3.5" alt="Logo">
                </div>
                <span class="font-black text-base tracking-tight text-[#564D4A]">
                    Brain<span class="text-[#5B2333]">Forge.</span>
                    <span class="text-[9px] font-bold text-white bg-[#5B2333] px-1.5 py-0.5 rounded ml-1 uppercase">Admin</span>
                </span>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard') }}" class="text-xs font-semibold text-[#564D4A]/50 hover:text-[#564D4A] transition">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Dashboard
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-6" x-data="{ tab: 'overview' }">

        {{-- Flash --}}
        @if(session('flash'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-semibold">
                {{ session('flash') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-semibold">
                {{ session('error') }}
            </div>
        @endif

        {{-- Tabs --}}
        <div class="flex gap-1 mb-6 bg-white rounded-xl p-1 border border-[#564D4A]/6 w-fit">
            @php
                $tabs = [
                    ['key' => 'overview', 'label' => 'Overzicht', 'icon' => 'fa-solid fa-chart-line'],
                    ['key' => 'users', 'label' => 'Gebruikers', 'icon' => 'fa-solid fa-users'],
                    ['key' => 'games', 'label' => 'Games', 'icon' => 'fa-solid fa-gamepad'],
                    ['key' => 'shop', 'label' => 'Shop', 'icon' => 'fa-solid fa-bag-shopping'],
                    ['key' => 'subscriptions', 'label' => 'Abonnementen', 'icon' => 'fa-solid fa-credit-card'],
                ];
            @endphp
            @foreach($tabs as $t)
                <button @click="tab = '{{ $t['key'] }}'"
                    :class="tab === '{{ $t['key'] }}' ? 'bg-[#5B2333] text-white shadow-sm' : 'text-[#564D4A]/50 hover:text-[#564D4A] hover:bg-[#564D4A]/5'"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition cursor-pointer">
                    <i class="{{ $t['icon'] }} text-[10px]"></i> {{ $t['label'] }}
                </button>
            @endforeach
        </div>

        {{-- ═══════════════════════════════ TAB: OVERVIEW ═══════════════════════════════ --}}
        <div x-show="tab === 'overview'" x-cloak>
            {{-- Key metrics --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 mb-8">
                @php
                    $metrics = [
                        ['label' => 'Totaal gebruikers', 'value' => number_format($totalUsers, 0, ',', '.'), 'icon' => 'fa-solid fa-users', 'color' => 'bg-[#5B2333]/10 text-[#5B2333]'],
                        ['label' => 'Pro leden', 'value' => $proUsers, 'icon' => 'fa-solid fa-crown', 'color' => 'bg-yellow-100 text-yellow-600'],
                        ['label' => 'MRR', 'value' => '€' . number_format($mrr, 2, ',', '.'), 'icon' => 'fa-solid fa-euro-sign', 'color' => 'bg-emerald-100 text-emerald-600'],
                        ['label' => 'Games vandaag', 'value' => number_format($gamesToday, 0, ',', '.'), 'icon' => 'fa-solid fa-gamepad', 'color' => 'bg-blue-100 text-blue-600'],
                        ['label' => 'Actieve spelers', 'value' => $activePlayersToday, 'icon' => 'fa-solid fa-user-check', 'color' => 'bg-green-100 text-green-600'],
                        ['label' => 'Berichten vandaag', 'value' => $messagesToday, 'icon' => 'fa-solid fa-comment', 'color' => 'bg-purple-100 text-purple-600'],
                    ];
                @endphp
                @foreach($metrics as $m)
                    <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                        <div class="w-8 h-8 rounded-lg {{ $m['color'] }} flex items-center justify-center mb-2.5">
                            <i class="{{ $m['icon'] }} text-xs"></i>
                        </div>
                        <p class="text-lg font-black text-[#564D4A]">{{ $m['value'] }}</p>
                        <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider mt-0.5">{{ $m['label'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- Charts row --}}
            <div class="grid md:grid-cols-2 gap-4 mb-8">
                {{-- Registration chart --}}
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-5">
                    <h3 class="text-sm font-black text-[#564D4A] mb-4">Nieuwe registraties (14 dagen)</h3>
                    <div class="flex items-end gap-1 h-32">
                        @php $maxReg = max($registrationChart->max('count'), 1); @endphp
                        @foreach($registrationChart as $day)
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full bg-[#5B2333]/15 rounded-t relative group" style="height: {{ max(4, ($day['count'] / $maxReg) * 100) }}%">
                                    <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-[#564D4A] text-white text-[9px] font-bold px-1.5 py-0.5 rounded hidden group-hover:block whitespace-nowrap">{{ $day['count'] }}</div>
                                </div>
                                <span class="text-[8px] text-[#564D4A]/30 font-semibold">{{ $day['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Games chart --}}
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-5">
                    <h3 class="text-sm font-black text-[#564D4A] mb-4">Games gespeeld (14 dagen)</h3>
                    <div class="flex items-end gap-1 h-32">
                        @php $maxGames = max($gamesChart->max('count'), 1); @endphp
                        @foreach($gamesChart as $day)
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full bg-blue-500/15 rounded-t relative group" style="height: {{ max(4, ($day['count'] / $maxGames) * 100) }}%">
                                    <div class="absolute -top-6 left-1/2 -translate-x-1/2 bg-[#564D4A] text-white text-[9px] font-bold px-1.5 py-0.5 rounded hidden group-hover:block whitespace-nowrap">{{ $day['count'] }}</div>
                                </div>
                                <span class="text-[8px] text-[#564D4A]/30 font-semibold">{{ $day['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Quick stats grid --}}
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-5">
                    <h3 class="text-xs font-bold text-[#564D4A]/40 uppercase tracking-wider mb-3">Gebruikers</h3>
                    <div class="space-y-2.5">
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Vandaag nieuw</span><span class="text-xs font-bold text-[#564D4A]">{{ $newUsersToday }}</span></div>
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Deze week</span><span class="text-xs font-bold text-[#564D4A]">{{ $newUsersWeek }}</span></div>
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Deze maand</span><span class="text-xs font-bold text-[#564D4A]">{{ $newUsersMonth }}</span></div>
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Admins</span><span class="text-xs font-bold text-[#564D4A]">{{ $adminCount }}</span></div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-5">
                    <h3 class="text-xs font-bold text-[#564D4A]/40 uppercase tracking-wider mb-3">Games</h3>
                    <div class="space-y-2.5">
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Totaal gespeeld</span><span class="text-xs font-bold text-[#564D4A]">{{ number_format($totalGamesPlayed, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Deze week</span><span class="text-xs font-bold text-[#564D4A]">{{ number_format($gamesWeek, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Solve rate vandaag</span><span class="text-xs font-bold text-[#564D4A]">{{ $solvedRate }}%</span></div>
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Gem. duur (solved)</span><span class="text-xs font-bold text-[#564D4A]">{{ $avgDurationFormatted }}</span></div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-5">
                    <h3 class="text-xs font-bold text-[#564D4A]/40 uppercase tracking-wider mb-3">Sociaal</h3>
                    <div class="space-y-2.5">
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Vriendschappen</span><span class="text-xs font-bold text-[#564D4A]">{{ $totalFriendships }}</span></div>
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Open verzoeken</span><span class="text-xs font-bold text-[#564D4A]">{{ $pendingRequests }}</span></div>
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Chat berichten</span><span class="text-xs font-bold text-[#564D4A]">{{ number_format($totalMessages, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Score posts</span><span class="text-xs font-bold text-[#564D4A]">{{ $totalScorePosts }}</span></div>
                    </div>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-5">
                    <h3 class="text-xs font-bold text-[#564D4A]/40 uppercase tracking-wider mb-3">Economie</h3>
                    <div class="space-y-2.5">
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">ARR</span><span class="text-xs font-bold text-[#564D4A]">€{{ number_format($arr, 2, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Coins in omloop</span><span class="text-xs font-bold text-[#564D4A]">{{ number_format($totalCoinsCirculation, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Items gekocht</span><span class="text-xs font-bold text-[#564D4A]">{{ number_format($totalItemsBought, 0, ',', '.') }}</span></div>
                        <div class="flex justify-between"><span class="text-xs text-[#564D4A]/60">Quests vandaag</span><span class="text-xs font-bold text-[#564D4A]">{{ $questsToday }}</span></div>
                    </div>
                </div>
            </div>

            {{-- Top streaks --}}
            <div class="grid md:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-5">
                    <h3 class="text-sm font-black text-[#564D4A] mb-4">Top 10 Streaks</h3>
                    <div class="space-y-2">
                        @foreach($topStreaks as $i => $s)
                            <div class="flex items-center justify-between py-1.5 {{ $i > 0 ? 'border-t border-[#564D4A]/4' : '' }}">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-5 h-5 rounded-full {{ $i < 3 ? 'bg-yellow-100 text-yellow-600' : 'bg-[#564D4A]/6 text-[#564D4A]/40' }} flex items-center justify-center text-[9px] font-black">{{ $i + 1 }}</span>
                                    <span class="text-xs font-semibold text-[#564D4A]">{{ $s['name'] }}</span>
                                </div>
                                <span class="text-xs font-bold text-[#5B2333]">{{ $s['streak'] }} dagen</span>
                            </div>
                        @endforeach
                        @if($topStreaks->isEmpty())
                            <p class="text-xs text-[#564D4A]/30 text-center py-4">Nog geen streaks.</p>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-5">
                    <h3 class="text-sm font-black text-[#564D4A] mb-4">Populairste Shop Items</h3>
                    <div class="space-y-2">
                        @foreach($topBoughtItems as $i => $item)
                            <div class="flex items-center justify-between py-1.5 {{ $i > 0 ? 'border-t border-[#564D4A]/4' : '' }}">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="w-5 h-5 rounded-full bg-[#564D4A]/6 text-[#564D4A]/40 flex items-center justify-center text-[9px] font-black">{{ $i + 1 }}</span>
                                    <span class="text-xs font-semibold text-[#564D4A] truncate">{{ $item['name'] }}</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-[#564D4A]/6 text-[#564D4A]/40 shrink-0">{{ $item['rarity'] }}</span>
                                </div>
                                <span class="text-xs font-bold text-[#5B2333] shrink-0 ml-2">{{ $item['bought'] }}x</span>
                            </div>
                        @endforeach
                        @if($topBoughtItems->isEmpty())
                            <p class="text-xs text-[#564D4A]/30 text-center py-4">Nog geen items gekocht.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════ TAB: USERS ═══════════════════════════════ --}}
        <div x-show="tab === 'users'" x-cloak x-data="{ search: '' }">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-black text-[#564D4A]">Alle Gebruikers <span class="text-[#564D4A]/30 text-sm font-semibold">({{ $totalUsers }})</span></h2>
                <input type="text" x-model="search" placeholder="Zoeken..."
                    class="text-xs px-4 py-2 rounded-lg border border-[#564D4A]/10 bg-white focus:outline-none focus:border-[#5B2333]/30 w-56">
            </div>

            <div class="bg-white rounded-xl border border-[#564D4A]/6 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-[#564D4A]/3 text-left">
                                <th class="px-4 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">ID</th>
                                <th class="px-4 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Naam</th>
                                <th class="px-4 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Email</th>
                                <th class="px-4 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Plan</th>
                                <th class="px-4 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Level</th>
                                <th class="px-4 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Coins</th>
                                <th class="px-4 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Games</th>
                                <th class="px-4 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Sub</th>
                                <th class="px-4 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Sinds</th>
                                <th class="px-4 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Acties</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#564D4A]/4">
                            @foreach ($users as $u)
                                <tr x-show="!search || '{{ strtolower($u['name'] . ' ' . $u['email']) }}'.includes(search.toLowerCase())"
                                    class="hover:bg-[#564D4A]/2 transition">
                                    <td class="px-4 py-2.5 text-[#564D4A]/30 font-mono">#{{ $u['id'] }}</td>
                                    <td class="px-4 py-2.5 font-semibold text-[#564D4A]">
                                        {{ $u['name'] }}
                                        @if($u['is_admin'])
                                            <span class="ml-1 px-1 py-0.5 rounded bg-red-100 text-red-600 text-[8px] font-bold">ADMIN</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-[#564D4A]/50">{{ $u['email'] }}</td>
                                    <td class="px-4 py-2.5">
                                        @if($u['plan'] === 'pro')
                                            <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full bg-[#5B2333]/10 text-[#5B2333] text-[9px] font-bold">
                                                <i class="fa-solid fa-crown text-[7px]"></i> PRO
                                            </span>
                                        @else
                                            <span class="text-[#564D4A]/25 font-semibold">Free</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-[#5B2333]/8 text-[#5B2333] text-[9px] font-bold">
                                            <i class="fa-solid fa-bolt text-[7px]"></i> {{ $u['level'] }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-2.5 text-[#564D4A]/60 font-semibold">{{ number_format($u['coins'], 0, ',', '.') }}</td>
                                    <td class="px-4 py-2.5 text-[#564D4A]/60 font-semibold">{{ $u['games_played'] }}</td>
                                    <td class="px-4 py-2.5">
                                        @if($u['sub_status'] === 'active')
                                            <span class="inline-flex items-center gap-1 text-green-600 font-semibold">
                                                <i class="fa-solid fa-circle text-[4px]"></i> Actief
                                            </span>
                                        @elseif($u['sub_status'] === 'canceled')
                                            <span class="text-orange-500 font-semibold">Opgezegd</span>
                                        @elseif($u['sub_status'])
                                            <span class="text-[#564D4A]/40">{{ $u['sub_status'] }}</span>
                                        @else
                                            <span class="text-[#564D4A]/15">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-[#564D4A]/35">{{ $u['created_at'] }}</td>
                                    <td class="px-4 py-2.5">
                                        <div class="flex items-center gap-1.5">
                                            @if($u['plan'] === 'pro')
                                                <form method="POST" action="{{ route('admin.downgrade', $u['id']) }}"
                                                      onsubmit="return confirm('{{ $u['name'] }} downgraden naar Free?')">
                                                    @csrf
                                                    <button class="cursor-pointer px-2 py-1 rounded-md bg-red-50 hover:bg-red-100 text-red-600 text-[9px] font-bold transition">
                                                        Downgrade
                                                    </button>
                                                </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.toggle-admin', $u['id']) }}"
                                                  onsubmit="return confirm('Admin-status wijzigen voor {{ $u['name'] }}?')">
                                                @csrf
                                                <button class="cursor-pointer px-2 py-1 rounded-md bg-[#564D4A]/5 hover:bg-[#564D4A]/10 text-[#564D4A]/50 text-[9px] font-bold transition">
                                                    {{ $u['is_admin'] ? '- Admin' : '+ Admin' }}
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

        {{-- ═══════════════════════════════ TAB: GAMES ═══════════════════════════════ --}}
        <div x-show="tab === 'games'" x-cloak>
            {{-- Game stats cards --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ number_format($totalGamesPlayed, 0, ',', '.') }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">Totaal gespeeld</p>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ $solvedRate }}%</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">Solve rate vandaag</p>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ $avgDurationFormatted }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">Gem. duur</p>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ $activePlayersWeek }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">Spelers deze week</p>
                </div>
            </div>

            {{-- Games by type table --}}
            <div class="bg-white rounded-xl border border-[#564D4A]/6 overflow-hidden mb-6">
                <div class="px-5 py-4 border-b border-[#564D4A]/6">
                    <h3 class="text-sm font-black text-[#564D4A]">Populariteit per game</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-[#564D4A]/3 text-left">
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Game</th>
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Totaal gespeeld</th>
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Opgelost</th>
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Solve rate</th>
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Populariteit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#564D4A]/4">
                            @php $maxPlayed = $gamesByType->max('total') ?: 1; @endphp
                            @foreach($gamesByType as $game)
                                @php $rate = $game->total > 0 ? round(($game->solved_count / $game->total) * 100) : 0; @endphp
                                <tr class="hover:bg-[#564D4A]/2 transition">
                                    <td class="px-5 py-3 font-semibold text-[#564D4A]">{{ $game->game_key }}</td>
                                    <td class="px-5 py-3 text-[#564D4A]/60 font-semibold">{{ number_format($game->total, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-[#564D4A]/60">{{ number_format($game->solved_count, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3">
                                        <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[9px] font-bold
                                            {{ $rate >= 70 ? 'bg-green-100 text-green-600' : ($rate >= 40 ? 'bg-yellow-100 text-yellow-600' : 'bg-red-100 text-red-600') }}">
                                            {{ $rate }}%
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 w-40">
                                        <div class="w-full h-2 bg-[#564D4A]/6 rounded-full overflow-hidden">
                                            <div class="h-full bg-[#5B2333]/30 rounded-full" style="width: {{ ($game->total / $maxPlayed) * 100 }}%"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Quest stats --}}
            <div class="grid sm:grid-cols-3 gap-3">
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ number_format($questsClaimed, 0, ',', '.') }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">Quests geclaimd</p>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ $questsToday }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">Quests vandaag</p>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ number_format($totalXpRewarded, 0, ',', '.') }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">XP uitgedeeld</p>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════ TAB: SHOP ═══════════════════════════════ --}}
        <div x-show="tab === 'shop'" x-cloak>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ $totalShopItems }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">Actieve items</p>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ number_format($totalItemsBought, 0, ',', '.') }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">Items gekocht</p>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ number_format($totalCoinsCirculation, 0, ',', '.') }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">Coins in omloop</p>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ $totalItemsBought > 0 && $totalUsers > 0 ? round($totalItemsBought / $totalUsers, 1) : 0 }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">Items / gebruiker</p>
                </div>
            </div>

            {{-- Top items --}}
            <div class="bg-white rounded-xl border border-[#564D4A]/6 overflow-hidden">
                <div class="px-5 py-4 border-b border-[#564D4A]/6">
                    <h3 class="text-sm font-black text-[#564D4A]">Top 10 Meest Gekochte Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-[#564D4A]/3 text-left">
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">#</th>
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Item</th>
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Type</th>
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Rarity</th>
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Gekocht</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#564D4A]/4">
                            @forelse($topBoughtItems as $i => $item)
                                <tr class="hover:bg-[#564D4A]/2 transition">
                                    <td class="px-5 py-3 text-[#564D4A]/30 font-bold">{{ $i + 1 }}</td>
                                    <td class="px-5 py-3 font-semibold text-[#564D4A]">{{ $item['name'] }}</td>
                                    <td class="px-5 py-3 text-[#564D4A]/50">{{ $item['type'] }}</td>
                                    <td class="px-5 py-3">
                                        @php
                                            $rc = match($item['rarity']) {
                                                'legendary' => 'bg-yellow-100 text-yellow-700',
                                                'epic' => 'bg-purple-100 text-purple-700',
                                                'rare' => 'bg-blue-100 text-blue-700',
                                                default => 'bg-[#564D4A]/6 text-[#564D4A]/50',
                                            };
                                        @endphp
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $rc }}">{{ ucfirst($item['rarity']) }}</span>
                                    </td>
                                    <td class="px-5 py-3 font-bold text-[#5B2333]">{{ $item['bought'] }}x</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-5 py-8 text-center text-[#564D4A]/30">Nog geen items gekocht.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════ TAB: SUBSCRIPTIONS ═══════════════════════════════ --}}
        <div x-show="tab === 'subscriptions'" x-cloak>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ $activeSubscriptions }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">Actieve subs</p>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">€{{ number_format($mrr, 2, ',', '.') }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">MRR</p>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ $monthlyCount }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">Maandelijks</p>
                </div>
                <div class="bg-white rounded-xl border border-[#564D4A]/6 p-4">
                    <p class="text-lg font-black text-[#564D4A]">{{ $yearlyCount }}</p>
                    <p class="text-[9px] font-semibold text-[#564D4A]/35 uppercase tracking-wider">Jaarlijks</p>
                </div>
            </div>

            {{-- Conversion rate --}}
            <div class="bg-white rounded-xl border border-[#564D4A]/6 p-5 mb-6">
                <h3 class="text-sm font-black text-[#564D4A] mb-3">Conversie</h3>
                <div class="flex items-center gap-6">
                    <div>
                        <p class="text-2xl font-black text-[#5B2333]">{{ $totalUsers > 0 ? round(($proUsers / $totalUsers) * 100, 1) : 0 }}%</p>
                        <p class="text-[10px] font-semibold text-[#564D4A]/40">Free → Pro conversie</p>
                    </div>
                    <div class="flex-1 h-3 bg-[#564D4A]/6 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-[#5B2333] to-[#5B2333]/60 rounded-full" style="width: {{ $totalUsers > 0 ? ($proUsers / $totalUsers) * 100 : 0 }}%"></div>
                    </div>
                    <div class="text-xs text-[#564D4A]/40 font-semibold">{{ $proUsers }} / {{ $totalUsers }}</div>
                </div>
            </div>

            {{-- Cancelled subs table --}}
            <div class="bg-white rounded-xl border border-[#564D4A]/6 overflow-hidden">
                <div class="px-5 py-4 border-b border-[#564D4A]/6">
                    <h3 class="text-sm font-black text-[#564D4A]">Opgezegde & Verlopen Abonnementen</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-[#564D4A]/3 text-left">
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Gebruiker</th>
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Email</th>
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Status</th>
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Gestart</th>
                                <th class="px-5 py-2.5 text-[9px] uppercase tracking-wider font-bold text-[#564D4A]/40">Eindigt op</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#564D4A]/4">
                            @forelse ($cancelledSubs as $sub)
                                <tr class="hover:bg-[#564D4A]/2 transition">
                                    <td class="px-5 py-3 font-semibold text-[#564D4A]">{{ $sub['user_name'] }}</td>
                                    <td class="px-5 py-3 text-[#564D4A]/50">{{ $sub['user_email'] }}</td>
                                    <td class="px-5 py-3">
                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold {{ $sub['status'] === 'canceled' ? 'bg-orange-100 text-orange-600' : 'bg-red-100 text-red-600' }}">
                                            {{ ucfirst($sub['status']) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-[#564D4A]/35">{{ $sub['created_at'] }}</td>
                                    <td class="px-5 py-3 text-[#564D4A]/35">{{ $sub['ends_at'] ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-5 py-8 text-center text-[#564D4A]/30">Nog geen opgezegde abonnementen.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </main>
</body>
</html>
