{{-- resources/views/dashboard/shop.blade.php --}}
<x-layouts.dashboard :title="'Shop'" active="shop">
    <style>
        @keyframes sparkle1 {
            0%, 100% { opacity: 1; transform: scale(1) rotate(0deg); }
            50% { opacity: 0.4; transform: scale(0.6) rotate(20deg); }
        }
        @keyframes sparkle2 {
            0%, 100% { opacity: 0.5; transform: scale(0.7) rotate(0deg); }
            50% { opacity: 1; transform: scale(1.1) rotate(-15deg); }
        }
        @keyframes sparkle3 {
            0%, 100% { opacity: 0.3; transform: scale(0.8); }
            50% { opacity: 0.9; transform: scale(1.2); }
        }
        .animate-sparkle-1 { animation: sparkle1 2s ease-in-out infinite; }
        .animate-sparkle-2 { animation: sparkle2 2.4s ease-in-out infinite 0.6s; }
        .animate-sparkle-3 { animation: sparkle3 1.8s ease-in-out infinite 1.2s; }

        @keyframes rainbow-spin {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }
        .animate-rainbow-border {
            background: linear-gradient(90deg, #ff6b6b, #ffd93d, #6bcb77, #4d96ff, #9b59b6, #ff6b6b);
            background-size: 200% 200%;
            animation: rainbow-spin 3s linear infinite;
            -webkit-mask: radial-gradient(circle, transparent 62%, black 65%);
            mask: radial-gradient(circle, transparent 62%, black 65%);
        }

        @keyframes bounce-subtle {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(-3px); }
        }
        .animate-bounce-subtle { animation: bounce-subtle 2s ease-in-out infinite; }

        @keyframes fire-ring-spin {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }
        .animate-fire-ring {
            background: linear-gradient(90deg, #ff6b35, #ff2d2d, #ff8c00, #ff4500, #ff6b35);
            background-size: 200% 200%;
            animation: fire-ring-spin 2s linear infinite;
            -webkit-mask: radial-gradient(circle, transparent 62%, black 65%);
            mask: radial-gradient(circle, transparent 62%, black 65%);
        }

        @keyframes electric-pulse {
            0%, 100% { box-shadow: 0 0 4px rgba(56, 189, 248, 0.3); }
            50% { box-shadow: 0 0 12px rgba(56, 189, 248, 0.6), 0 0 20px rgba(56, 189, 248, 0.2); }
        }
        .animate-electric-pulse {
            border: 2px solid rgba(56, 189, 248, 0.4);
            animation: electric-pulse 1.5s ease-in-out infinite;
        }

        @keyframes galaxy-spin {
            0% { transform: rotate(0deg); background-position: 0% 50%; }
            100% { transform: rotate(360deg); background-position: 200% 50%; }
        }
        .animate-galaxy {
            background: conic-gradient(from 0deg, #6366f1, #8b5cf6, #d946ef, #3b82f6, #6366f1);
            animation: galaxy-spin 6s linear infinite;
            -webkit-mask: radial-gradient(circle, transparent 62%, black 65%);
            mask: radial-gradient(circle, transparent 62%, black 65%);
        }

        /* Shadow Aura — soft dark pulsing glow */
        @keyframes shadow-pulse {
            0%, 100% { box-shadow: 0 0 8px rgba(0,0,0,0.3), inset 0 0 8px rgba(0,0,0,0.1); }
            50% { box-shadow: 0 0 20px rgba(0,0,0,0.5), inset 0 0 12px rgba(0,0,0,0.2); }
        }
        .animate-shadow-aura {
            background: radial-gradient(circle, transparent 55%, rgba(0,0,0,0.4) 70%, rgba(0,0,0,0.15) 100%);
            animation: shadow-pulse 3s ease-in-out infinite;
        }

        /* Void Rift — purple/violet tearing energy */
        @keyframes void-rift {
            0%, 100% { box-shadow: 0 0 6px rgba(139,92,246,0.4), 0 0 15px rgba(88,28,135,0.2); }
            33% { box-shadow: -3px 0 12px rgba(139,92,246,0.6), 3px 0 8px rgba(88,28,135,0.3); }
            66% { box-shadow: 3px 0 12px rgba(139,92,246,0.6), -3px 0 8px rgba(88,28,135,0.3); }
        }
        .animate-void-rift {
            background: radial-gradient(circle, transparent 50%, rgba(88,28,135,0.5) 65%, rgba(139,92,246,0.2) 100%);
            border: 1px dashed rgba(139,92,246,0.4);
            animation: void-rift 2s ease-in-out infinite;
        }

        /* Black Hole — dark inward-pulling spiral */
        @keyframes black-hole-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(-360deg); }
        }
        @keyframes black-hole-pull {
            0%, 100% { box-shadow: inset 0 0 15px rgba(0,0,0,0.6), 0 0 5px rgba(0,0,0,0.3); }
            50% { box-shadow: inset 0 0 25px rgba(0,0,0,0.8), 0 0 10px rgba(0,0,0,0.4); }
        }
        .animate-black-hole {
            background: conic-gradient(from 0deg, transparent 0%, rgba(0,0,0,0.6) 25%, transparent 50%, rgba(30,0,50,0.4) 75%, transparent 100%);
            animation: black-hole-spin 4s linear infinite, black-hole-pull 2s ease-in-out infinite;
            -webkit-mask: radial-gradient(circle, transparent 50%, black 60%);
            mask: radial-gradient(circle, transparent 50%, black 60%);
        }

        /* Celestial — warm golden slow rotation */
        @keyframes celestial-glow {
            0%, 100% { box-shadow: 0 0 8px rgba(251,191,36,0.3); }
            50% { box-shadow: 0 0 18px rgba(251,191,36,0.5), 0 0 30px rgba(251,191,36,0.15); }
        }
        .animate-celestial {
            background: conic-gradient(from 0deg, rgba(251,191,36,0.1), rgba(251,191,36,0.3), rgba(245,158,11,0.1), rgba(251,191,36,0.3), rgba(251,191,36,0.1));
            animation: galaxy-spin 10s linear infinite, celestial-glow 3s ease-in-out infinite;
            -webkit-mask: radial-gradient(circle, transparent 62%, black 65%);
            mask: radial-gradient(circle, transparent 62%, black 65%);
        }

        /* Supernova Burst — explosive red-orange-white expanding glow */
        @keyframes supernova-burst {
            0%, 100% { box-shadow: 0 0 6px rgba(239,68,68,0.3); transform: scale(1); }
            25% { box-shadow: 0 0 15px rgba(249,115,22,0.5), 0 0 25px rgba(239,68,68,0.2); transform: scale(1.04); }
            50% { box-shadow: 0 0 20px rgba(255,255,255,0.4), 0 0 35px rgba(249,115,22,0.3); transform: scale(1.06); }
            75% { box-shadow: 0 0 15px rgba(249,115,22,0.5), 0 0 25px rgba(239,68,68,0.2); transform: scale(1.04); }
        }
        .animate-supernova {
            background: radial-gradient(circle, transparent 50%, rgba(249,115,22,0.4) 65%, rgba(239,68,68,0.3) 80%, transparent 100%);
            animation: supernova-burst 2.5s ease-in-out infinite;
        }

        /* Neon Rings — cyan/magenta alternating ring */
        @keyframes neon-ring-pulse {
            0%, 100% { box-shadow: 0 0 6px rgba(6,182,212,0.5), inset 0 0 4px rgba(6,182,212,0.2); border-color: rgba(6,182,212,0.6); }
            50% { box-shadow: 0 0 14px rgba(217,70,239,0.5), inset 0 0 6px rgba(217,70,239,0.2); border-color: rgba(217,70,239,0.6); }
        }
        .animate-neon-rings {
            border: 2px solid rgba(6,182,212,0.6);
            animation: neon-ring-pulse 2s ease-in-out infinite;
        }

        /* Sonic Waves — expanding ring pulses */
        @keyframes sonic-wave {
            0% { transform: scale(0.9); opacity: 0.6; box-shadow: 0 0 0 2px rgba(34,197,94,0.4); }
            50% { transform: scale(1.05); opacity: 0.3; box-shadow: 0 0 0 5px rgba(34,197,94,0.2); }
            100% { transform: scale(0.9); opacity: 0.6; box-shadow: 0 0 0 2px rgba(34,197,94,0.4); }
        }
        .animate-sonic-waves {
            border: 2px solid rgba(34,197,94,0.3);
            animation: sonic-wave 1.8s ease-in-out infinite;
        }

        /* Plasma Orbs — warm pink/orange orbiting glow */
        @keyframes plasma-orbit {
            0% { box-shadow: 5px 0 10px rgba(244,114,182,0.5), -5px 0 10px rgba(251,146,60,0.3); }
            25% { box-shadow: 0 5px 10px rgba(251,146,60,0.5), 0 -5px 10px rgba(244,114,182,0.3); }
            50% { box-shadow: -5px 0 10px rgba(244,114,182,0.5), 5px 0 10px rgba(251,146,60,0.3); }
            75% { box-shadow: 0 -5px 10px rgba(251,146,60,0.5), 0 5px 10px rgba(244,114,182,0.3); }
            100% { box-shadow: 5px 0 10px rgba(244,114,182,0.5), -5px 0 10px rgba(251,146,60,0.3); }
        }
        .animate-plasma-orbs {
            animation: plasma-orbit 3s linear infinite;
        }

        /* ── Animated name colors ── */
        @keyframes name-rainbow {
            0% { color: #ff6b6b; }
            16% { color: #ffd93d; }
            33% { color: #6bcb77; }
            50% { color: #4d96ff; }
            66% { color: #9b59b6; }
            83% { color: #ff6b6b; }
            100% { color: #ff6b6b; }
        }
        .animate-name-rainbow {
            animation: name-rainbow 4s linear infinite;
        }

        @keyframes name-fire {
            0%, 100% { color: #ff4500; text-shadow: 0 0 6px rgba(255,69,0,0.4); }
            25% { color: #ff8c00; text-shadow: 0 0 10px rgba(255,140,0,0.5); }
            50% { color: #ffd700; text-shadow: 0 0 14px rgba(255,215,0,0.5); }
            75% { color: #ff6b35; text-shadow: 0 0 10px rgba(255,107,53,0.4); }
        }
        .animate-name-fire {
            animation: name-fire 2s ease-in-out infinite;
        }

        @keyframes name-neon-pulse {
            0%, 100% { color: #38bdf8; text-shadow: 0 0 4px rgba(56,189,248,0.3); }
            50% { color: #7dd3fc; text-shadow: 0 0 12px rgba(56,189,248,0.6), 0 0 24px rgba(56,189,248,0.2); }
        }
        .animate-name-neon-pulse {
            animation: name-neon-pulse 2s ease-in-out infinite;
        }

        @keyframes name-glitch {
            0%, 100% { color: #22d3ee; text-shadow: none; }
            5% { color: #f43f5e; text-shadow: -2px 0 #22d3ee; }
            10% { color: #22d3ee; text-shadow: 2px 0 #f43f5e; }
            15% { color: #a855f7; text-shadow: none; }
            20% { color: #22d3ee; text-shadow: -1px 0 #a855f7, 1px 0 #f43f5e; }
            25%, 100% { color: #22d3ee; text-shadow: none; }
        }
        .animate-name-glitch {
            animation: name-glitch 3s steps(1) infinite;
        }

        /* ── Animated rainbow badge ── */
        @keyframes badge-rainbow {
            0% { background-position: 0% 50%; }
            100% { background-position: 200% 50%; }
        }
        .animate-badge-rainbow {
            background: linear-gradient(90deg, #ff6b6b, #ffd93d, #6bcb77, #4d96ff, #9b59b6, #ff6b6b);
            background-size: 200% 200%;
            animation: badge-rainbow 3s linear infinite;
            color: white;
            border-color: transparent;
            text-shadow: 0 1px 2px rgba(0,0,0,0.15);
        }
    </style>
    {{-- Tailwind safelist for dynamic custom badge colors --}}
    {{-- bg-red-100 text-red-700 border-red-200 bg-red-400 --}}
    {{-- bg-orange-100 text-orange-700 border-orange-200 bg-orange-400 --}}
    {{-- bg-yellow-100 text-yellow-700 border-yellow-200 bg-yellow-400 --}}
    {{-- bg-green-100 text-green-700 border-green-200 bg-green-400 --}}
    {{-- bg-emerald-100 text-emerald-700 border-emerald-200 bg-emerald-400 --}}
    {{-- bg-cyan-100 text-cyan-700 border-cyan-200 bg-cyan-400 --}}
    {{-- bg-blue-100 text-blue-700 border-blue-200 bg-blue-400 --}}
    {{-- bg-indigo-100 text-indigo-700 border-indigo-200 bg-indigo-400 --}}
    {{-- bg-purple-100 text-purple-700 border-purple-200 bg-purple-400 --}}
    {{-- bg-pink-100 text-pink-700 border-pink-200 bg-pink-400 --}}
    {{-- bg-slate-100 text-slate-700 border-slate-200 bg-slate-400 --}}
    @php
        $u = auth()->user();
        $coins = (int) $u->coins;

        $rarityColors = [
            'common'    => ['bg' => 'bg-[#564D4A]/8',  'text' => 'text-[#564D4A]/60', 'border' => 'border-[#564D4A]/10', 'label' => 'Gewoon'],
            'rare'      => ['bg' => 'bg-[#3B82F6]/10', 'text' => 'text-[#3B82F6]',    'border' => 'border-[#3B82F6]/15', 'label' => 'Zeldzaam'],
            'epic'      => ['bg' => 'bg-[#8B5CF6]/10', 'text' => 'text-[#8B5CF6]',    'border' => 'border-[#8B5CF6]/15', 'label' => 'Episch'],
            'legendary' => ['bg' => 'bg-[#F59E0B]/10', 'text' => 'text-[#B88B2A]',    'border' => 'border-[#F59E0B]/15', 'label' => 'Legendarisch'],
        ];

        $typeLabels = [
            'border'      => 'Avatar Border',
            'hat'         => 'Hoedje',
            'effect'      => 'Effect',
            'badge_flair' => 'Profiel Badge',
            'name_color'  => 'Naam Kleur',
        ];

        $typeIcons = [
            'border'      => 'fa-solid fa-circle-dot',
            'hat'         => 'fa-solid fa-hat-wizard',
            'effect'      => 'fa-solid fa-wand-magic-sparkles',
            'badge_flair' => 'fa-solid fa-certificate',
            'name_color'  => 'fa-solid fa-palette',
        ];

        $isPro = ($u->plan ?? 'free') === 'pro';

        $avatarUrl = $u->profile_picture ? asset('storage/' . $u->profile_picture) : null;
        $firstName = explode(' ', $u->name)[0];

        $hatEmojis = [
            // Common
            'hat-party'         => '🎉',
            'hat-cap'           => '🧢',
            'hat-beanie'        => '🧶',
            'hat-headband'      => '🎽',
            'hat-backwards-cap' => '🔄',
            'hat-bucket'        => '🪣',
            'hat-beret'         => '🎨',
            'hat-flower-crown'  => '🌺',
            'hat-bandana'       => '🏴',
            'hat-straw'         => '🌾',
            'hat-propeller'     => '🚁',
            'hat-ear-muffs'     => '🎧',
            'hat-visor'         => '🕶️',
            'hat-newsboy'       => '📰',
            'hat-bow'           => '🎀',
            // Rare
            'hat-wizard'        => '🧙',
            'hat-santa'         => '🎅',
            'hat-cowboy'        => '🤠',
            'hat-pirate'        => '🏴‍☠️',
            'hat-chef'          => '👨‍🍳',
            'hat-top-hat'       => '🎩',
            'hat-detective'     => '🔍',
            'hat-nurse'         => '⚕️',
            'hat-graduation'    => '🎓',
            'hat-viking'        => '⚔️',
            'hat-mushroom'      => '🍄',
            'hat-cat-ears'      => '🐱',
            'hat-bunny-ears'    => '🐰',
            'hat-pumpkin'       => '🎃',
            'hat-hard-hat'      => '⛑️',
            // Epic
            'hat-crown'         => '👑',
            'hat-horns'         => '😈',
            'hat-astronaut'     => '🚀',
            'hat-samurai'       => '⛩️',
            'hat-knight'        => '🛡️',
            'hat-pharaoh'       => '🏛️',
            'hat-robot'         => '🤖',
            'hat-witch'         => '🧹',
            'hat-pilot'         => '✈️',
            'hat-antlers'       => '🦌',
            'hat-alien'         => '👽',
            'hat-spartan'       => '🗡️',
            // Legendary
            'hat-halo'          => '😇',
            'hat-dragon-skull'  => '🐉',
            'hat-flame-crown'   => '🔥',
            'hat-ice-crown'     => '🧊',
            'hat-galaxy-helmet' => '🌌',
            'hat-thunder'       => '⚡',
            'hat-shadow-hood'   => '🌑',
            'hat-nature-crown'  => '🌿',
        ];

        $flairMeta = [
            // Common (15)
            'flair-noob'           => ['emoji' => '🐣', 'bg' => 'bg-green-100',    'text' => 'text-green-700',    'border' => 'border-green-200'],
            'flair-oeps'           => ['emoji' => '🫣', 'bg' => 'bg-orange-100',   'text' => 'text-orange-700',   'border' => 'border-orange-200'],
            'flair-zzz'            => ['emoji' => '😴', 'bg' => 'bg-indigo-100',   'text' => 'text-indigo-700',   'border' => 'border-indigo-200'],
            'flair-sus'            => ['emoji' => '👀', 'bg' => 'bg-red-100',      'text' => 'text-red-700',      'border' => 'border-red-200'],
            'flair-lol'            => ['emoji' => '😂', 'bg' => 'bg-yellow-100',   'text' => 'text-yellow-700',   'border' => 'border-yellow-200'],
            'flair-brb'            => ['emoji' => '🏃', 'bg' => 'bg-sky-100',      'text' => 'text-sky-700',      'border' => 'border-sky-200'],
            'flair-yolo'           => ['emoji' => '🤙', 'bg' => 'bg-pink-100',     'text' => 'text-pink-700',     'border' => 'border-pink-200'],
            'flair-rip'            => ['emoji' => '⚰️', 'bg' => 'bg-slate-100',    'text' => 'text-slate-700',    'border' => 'border-slate-200'],
            'flair-help'           => ['emoji' => '🆘', 'bg' => 'bg-rose-100',     'text' => 'text-rose-700',     'border' => 'border-rose-200'],
            'flair-meh'            => ['emoji' => '😐', 'bg' => 'bg-stone-100',    'text' => 'text-stone-600',    'border' => 'border-stone-200'],
            'flair-omg'            => ['emoji' => '😱', 'bg' => 'bg-fuchsia-100',  'text' => 'text-fuchsia-700',  'border' => 'border-fuchsia-200'],
            'flair-nerd'           => ['emoji' => '🤓', 'bg' => 'bg-blue-100',     'text' => 'text-blue-700',     'border' => 'border-blue-200'],
            'flair-chill'          => ['emoji' => '🧊', 'bg' => 'bg-cyan-100',     'text' => 'text-cyan-700',     'border' => 'border-cyan-200'],
            'flair-newbie'         => ['emoji' => '🌱', 'bg' => 'bg-lime-100',     'text' => 'text-lime-700',     'border' => 'border-lime-200'],
            'flair-afk'            => ['emoji' => '💤', 'bg' => 'bg-violet-100',   'text' => 'text-violet-700',   'border' => 'border-violet-200'],
            // Rare (15)
            'flair-skill-issue'    => ['emoji' => '💀', 'bg' => 'bg-slate-100',    'text' => 'text-slate-700',    'border' => 'border-slate-200'],
            'flair-tryhard'        => ['emoji' => '😤', 'bg' => 'bg-red-100',      'text' => 'text-red-700',      'border' => 'border-red-200'],
            'flair-touch-grass'    => ['emoji' => '🌿', 'bg' => 'bg-emerald-100',  'text' => 'text-emerald-700',  'border' => 'border-emerald-200'],
            'flair-gg-ez'          => ['emoji' => '😎', 'bg' => 'bg-sky-100',      'text' => 'text-sky-700',      'border' => 'border-sky-200'],
            'flair-carried'        => ['emoji' => '🧳', 'bg' => 'bg-amber-100',    'text' => 'text-amber-700',    'border' => 'border-amber-200'],
            'flair-rage-quit'      => ['emoji' => '🎮', 'bg' => 'bg-rose-100',     'text' => 'text-rose-700',     'border' => 'border-rose-200'],
            'flair-no-cap'         => ['emoji' => '🧢', 'bg' => 'bg-blue-100',     'text' => 'text-blue-700',     'border' => 'border-blue-200'],
            'flair-clutch'         => ['emoji' => '🎯', 'bg' => 'bg-teal-100',     'text' => 'text-teal-700',     'border' => 'border-teal-200'],
            'flair-based'          => ['emoji' => '💊', 'bg' => 'bg-purple-100',   'text' => 'text-purple-700',   'border' => 'border-purple-200'],
            'flair-w'              => ['emoji' => '🏆', 'bg' => 'bg-yellow-100',   'text' => 'text-yellow-700',   'border' => 'border-yellow-200'],
            'flair-l'              => ['emoji' => '📉', 'bg' => 'bg-zinc-100',     'text' => 'text-zinc-600',     'border' => 'border-zinc-200'],
            'flair-toxic'          => ['emoji' => '☠️', 'bg' => 'bg-lime-100',     'text' => 'text-lime-700',     'border' => 'border-lime-200'],
            'flair-og'             => ['emoji' => '👴', 'bg' => 'bg-orange-100',   'text' => 'text-orange-700',   'border' => 'border-orange-200'],
            'flair-beta'           => ['emoji' => '🧪', 'bg' => 'bg-indigo-100',   'text' => 'text-indigo-700',   'border' => 'border-indigo-200'],
            'flair-salty'          => ['emoji' => '🧂', 'bg' => 'bg-stone-100',    'text' => 'text-stone-600',    'border' => 'border-stone-200'],
            // Epic (12)
            'flair-big-brain'      => ['emoji' => '🧠', 'bg' => 'bg-pink-100',     'text' => 'text-pink-700',     'border' => 'border-pink-200'],
            'flair-geen-leven'     => ['emoji' => '💻', 'bg' => 'bg-violet-100',   'text' => 'text-violet-700',   'border' => 'border-violet-200'],
            'flair-speedrunner'    => ['emoji' => '⚡', 'bg' => 'bg-yellow-100',   'text' => 'text-yellow-700',   'border' => 'border-yellow-200'],
            'flair-1iq'            => ['emoji' => '🪱', 'bg' => 'bg-orange-100',   'text' => 'text-orange-700',   'border' => 'border-orange-200'],
            'flair-built-different'=> ['emoji' => '🦾', 'bg' => 'bg-slate-100',    'text' => 'text-slate-700',    'border' => 'border-slate-200'],
            'flair-main-character' => ['emoji' => '🎬', 'bg' => 'bg-fuchsia-100',  'text' => 'text-fuchsia-700',  'border' => 'border-fuchsia-200'],
            'flair-sigma'          => ['emoji' => '🐺', 'bg' => 'bg-zinc-100',     'text' => 'text-zinc-700',     'border' => 'border-zinc-200'],
            'flair-galaxy-brain'   => ['emoji' => '🌌', 'bg' => 'bg-purple-100',   'text' => 'text-purple-700',   'border' => 'border-purple-200'],
            'flair-menace'         => ['emoji' => '😈', 'bg' => 'bg-red-100',      'text' => 'text-red-700',      'border' => 'border-red-200'],
            'flair-npc'            => ['emoji' => '🗿', 'bg' => 'bg-stone-100',    'text' => 'text-stone-600',    'border' => 'border-stone-200'],
            'flair-goated'         => ['emoji' => '🐐', 'bg' => 'bg-amber-100',    'text' => 'text-amber-700',    'border' => 'border-amber-200'],
            'flair-final-boss'     => ['emoji' => '👹', 'bg' => 'bg-rose-100',     'text' => 'text-rose-700',     'border' => 'border-rose-200'],
            // Legendary (8)
            'flair-custom-gold'    => ['emoji' => '✏️', 'bg' => 'bg-yellow-100',   'text' => 'text-yellow-800',   'border' => 'border-yellow-300'],
            'flair-custom-rainbow' => ['emoji' => '🌈', 'bg' => 'bg-gradient-to-r from-pink-100 via-purple-100 to-cyan-100', 'text' => 'text-purple-700', 'border' => 'border-purple-200'],
            'flair-goat'           => ['emoji' => '🏅', 'bg' => 'bg-amber-100',    'text' => 'text-amber-800',    'border' => 'border-amber-300'],
            'flair-legendary'      => ['emoji' => '⭐', 'bg' => 'bg-yellow-100',   'text' => 'text-yellow-800',   'border' => 'border-yellow-300'],
            'flair-god-mode'       => ['emoji' => '🔱', 'bg' => 'bg-cyan-100',     'text' => 'text-cyan-800',     'border' => 'border-cyan-300'],
            'flair-prestige'       => ['emoji' => '💎', 'bg' => 'bg-sky-100',      'text' => 'text-sky-800',      'border' => 'border-sky-300'],
            'flair-mythic'         => ['emoji' => '🐲', 'bg' => 'bg-violet-100',   'text' => 'text-violet-800',   'border' => 'border-violet-300'],
            'flair-gg-wp'          => ['emoji' => '🤝', 'bg' => 'bg-emerald-100',  'text' => 'text-emerald-800',  'border' => 'border-emerald-300'],
        ];
    @endphp

    @php
        // Build a type map so JS can look up item types without DOM queries (avoids quote issues in x-data)
        $itemTypeMap = $items->pluck('type', 'id');
    @endphp

    <div class="flex flex-col gap-8"
         x-data="shopPage()"
         x-init="init()">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4">
            <div>
                <h1 class="text-[1.5rem] md:text-[1.8rem] font-black text-[#564D4A] tracking-tight leading-tight">
                    Cosmetica Shop
                </h1>
                <p class="mt-1 text-xs md:text-sm font-semibold text-[#564D4A]/50 leading-[1.3]">
                    Koop en rust cosmetische items uit met je verdiende coins.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#F59E0B]/10 border border-[#F59E0B]/15 text-xs font-bold text-[#B88B2A]">
                    <i class="fa-solid fa-coins text-[#F59E0B] text-[11px]"></i>
                    <span x-text="coins.toLocaleString('nl-NL')">{{ number_format($coins, 0, ',', '.') }}</span> coins
                </span>
            </div>
        </div>

        {{-- TOAST --}}
        <div x-show="message" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed top-20 right-6 z-50 px-5 py-3 rounded-xl shadow-lg text-sm font-semibold"
             :class="msgType === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200'">
            <span x-text="message"></span>
        </div>

        {{-- HOW IT WORKS --}}
        <div class="w-full bg-white rounded-2xl p-6 border border-[#564D4A]/6">
            <div class="flex flex-wrap items-center gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#F59E0B]/10 flex items-center justify-center">
                        <i class="fa-solid fa-arrow-up text-[#B88B2A]"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[#564D4A]">Level up</p>
                        <p class="text-[11px] font-semibold text-[#564D4A]/45">Verdien 25 coins per level</p>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-[#564D4A]/15 text-xs hidden sm:block"></i>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#5B2333]/10 flex items-center justify-center">
                        <i class="fa-solid fa-bag-shopping text-[#5B2333]"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[#564D4A]">Koop items</p>
                        <p class="text-[11px] font-semibold text-[#564D4A]/45">Borders, hoedjes & meer</p>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-right text-[#564D4A]/15 text-xs hidden sm:block"></i>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#3B82F6]/10 flex items-center justify-center">
                        <i class="fa-solid fa-wand-magic-sparkles text-[#3B82F6]"></i>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-[#564D4A]">Rust uit</p>
                        <p class="text-[11px] font-semibold text-[#564D4A]/45">Pas je profiel aan</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- VIEW TOGGLE: Shop / Mijn Items --}}
        <div class="flex items-center gap-3">
            <div class="inline-flex rounded-xl bg-[#564D4A]/5 p-1">
                <button @click="view = 'shop'"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition cursor-pointer"
                    :class="view === 'shop' ? 'bg-white text-[#564D4A] shadow-sm' : 'text-[#564D4A]/50 hover:text-[#564D4A]'">
                    <i class="fa-solid fa-bag-shopping text-[11px]"></i> Shop
                </button>
                <button @click="view = 'owned'"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold transition cursor-pointer"
                    :class="view === 'owned' ? 'bg-white text-[#564D4A] shadow-sm' : 'text-[#564D4A]/50 hover:text-[#564D4A]'">
                    <i class="fa-solid fa-box-open text-[11px]"></i> Mijn Items
                    <span class="px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-[#5B2333]/10 text-[#5B2333]"
                          x-text="ownedIds.length"></span>
                </button>
            </div>
        </div>

        {{-- FILTER TABS --}}
        <div class="flex flex-wrap items-center gap-2">
            @php
                $filters = [
                    'all' => ['label' => 'Alles', 'icon' => 'fa-solid fa-grid-2'],
                    'border' => ['label' => 'Borders', 'icon' => 'fa-solid fa-circle-dot'],
                    'hat' => ['label' => 'Hoedjes', 'icon' => 'fa-solid fa-hat-wizard'],
                    'effect' => ['label' => 'Effecten', 'icon' => 'fa-solid fa-wand-magic-sparkles'],
                    'badge_flair' => ['label' => 'Badges', 'icon' => 'fa-solid fa-certificate'],
                    'name_color' => ['label' => 'Naam Kleur', 'icon' => 'fa-solid fa-palette'],
                ];
            @endphp
            @foreach ($filters as $filterKey => $f)
                <button @click="filter = '{{ $filterKey }}'"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold transition cursor-pointer"
                    :class="filter === '{{ $filterKey }}'
                        ? 'bg-[#5B2333] text-white'
                        : 'bg-[#564D4A]/5 text-[#564D4A]/60 hover:bg-[#564D4A]/10 hover:text-[#564D4A]'">
                    <i class="{{ $f['icon'] }} text-[11px]"></i>
                    {{ $f['label'] }}
                </button>
            @endforeach
        </div>

        {{-- ITEMS GRID --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse ($items as $itemIdx => $item)
                @php
                    $rc = $rarityColors[$item->rarity] ?? $rarityColors['common'];
                    $tIcon = $typeIcons[$item->type] ?? 'fa-solid fa-star';
                    $tLabel = $typeLabels[$item->type] ?? $item->type;
                @endphp
                <div x-show="isItemVisible({{ $item->id }}, '{{ $item->type }}')"
                     x-transition
                     data-item-id="{{ $item->id }}"
                     data-item-type="{{ $item->type }}"
                     data-item-idx="{{ $itemIdx }}"
                     class="bg-white rounded-2xl border border-[#564D4A]/6 overflow-hidden flex flex-col hover:border-[#564D4A]/15 transition group">

                    {{-- Preview area — mini profile mockup --}}
                    <div class="relative h-44 {{ $rc['bg'] }} flex items-center justify-center overflow-hidden">
                        <div class="flex flex-col items-center gap-2.5 relative z-[1]">

                            {{-- Avatar container --}}
                            <div class="relative">
                                {{-- Hat (floating above avatar) --}}
                                @if ($item->type === 'hat')
                                    <span class="absolute -top-5 left-1/2 -translate-x-1/2 text-2xl z-10 drop-shadow-sm
                                        {{ $item->slug === 'hat-party' ? 'animate-bounce-subtle' : '' }}
                                        {{ $item->slug === 'hat-wizard' ? '-rotate-6' : '' }}">
                                        {{ $hatEmojis[$item->slug] ?? '🎩' }}
                                    </span>
                                @endif

                                {{-- Effect particles per type --}}
                                @if ($item->type === 'effect')
                                    @php
                                        // Each effect maps to its own unique animation class
                                        $effectClasses = [
                                            'effect-rainbow' => 'animate-rainbow-border',
                                            'effect-fire-ring' => 'animate-fire-ring',
                                            'effect-aurora-borealis' => 'animate-rainbow-border',
                                            'effect-electric-arc' => 'animate-electric-pulse',
                                            'effect-neon-rings' => 'animate-neon-rings',
                                            'effect-sonic-waves' => 'animate-sonic-waves',
                                            'effect-plasma-orbs' => 'animate-plasma-orbs',
                                            'effect-shadow-aura' => 'animate-shadow-aura',
                                            'effect-void-rift' => 'animate-void-rift',
                                            'effect-black-hole' => 'animate-black-hole',
                                            'effect-galaxy' => 'animate-galaxy',
                                            'effect-celestial' => 'animate-celestial',
                                            'effect-supernova-burst' => 'animate-supernova',
                                        ];
                                        // Decorative emojis per effect class group
                                        $effectEmojis = [
                                            'animate-electric-pulse' => ['⚡','⚡'],
                                            'animate-neon-rings' => ['💜','💠'],
                                            'animate-sonic-waves' => ['〰️','🔊'],
                                            'animate-plasma-orbs' => ['🩷','🔶'],
                                            'animate-void-rift' => ['🌀','💜'],
                                            'animate-black-hole' => ['🕳️','⬛'],
                                            'animate-galaxy' => ['⭐','✨'],
                                            'animate-celestial' => ['☀️','🌙'],
                                            'animate-supernova' => ['💥','🔥'],
                                        ];
                                        // Particle emoji mapping for everything else
                                        $particleEmojis = [
                                            'effect-sparkle' => ['✨','✨','💫'], 'effect-hearts' => ['💕','💗','❤️'],
                                            'effect-snowflakes' => ['❄️','❄️','🌨️'], 'effect-stars' => ['⭐','🌟','✨'],
                                            'effect-cherry-blossom' => ['🌸','🌸','🌺'], 'effect-butterflies' => ['🦋','🦋','🦋'],
                                            'effect-fireflies' => ['💛','💚','💛'], 'effect-diamonds' => ['💎','💎','✨'],
                                            'effect-coins' => ['🪙','🪙','💰'], 'effect-embers' => ['🔥','🧡','🔥'],
                                            'effect-sakura' => ['🌸','🌸','🎀'], 'effect-lightning-bugs' => ['⚡','💡','⚡'],
                                            'effect-crystals' => ['🔮','💎','🔮'], 'effect-pixel-dust' => ['▪️','▫️','▪️'],
                                            'effect-soap-bubbles' => ['🫧','🫧','🫧'], 'effect-confetti' => ['🎊','🎉','✨'],
                                            'effect-bubbles' => ['🫧','🫧','💧'], 'effect-dots' => ['●','●','●'],
                                            'effect-leaves' => ['🍃','🍂','🌿'], 'effect-petals' => ['🌷','🌸','🌹'],
                                            'effect-rain' => ['💧','💧','🌧️'], 'effect-dust' => ['✦','✧','✦'],
                                            'effect-music-notes' => ['🎵','🎶','♪'], 'effect-feathers' => ['🪶','🪶','🕊️'],
                                            'effect-twinkle' => ['✦','✧','✦'], 'effect-wind' => ['🌬️','💨','🌬️'],
                                            'effect-dandelion' => ['🌼','✿','🌼'],
                                            'effect-ice-shards' => ['🧊','❄️','💠'], 'effect-toxic-cloud' => ['☠️','💚','☠️'],
                                            'effect-blood-drip' => ['🩸','🩸','💀'], 'effect-golden-aura' => ['✨','⭐','✨'],
                                            'effect-spirit-wisps' => ['👻','💙','👻'], 'effect-matrix' => ['🟢','🟢','⬛'],
                                            'effect-cherry-flames' => ['🌸','🔥','🌸'], 'effect-rune-circle' => ['🔯','✡️','🔯'],
                                            'effect-phoenix' => ['🔥','🦅','🔥'], 'effect-dragon-fire' => ['🐉','🔥','🐉'],
                                        ];
                                    @endphp
                                    @if (isset($effectClasses[$item->slug]))
                                        @php $ec = $effectClasses[$item->slug]; @endphp
                                        <div class="absolute -inset-1.5 rounded-full {{ $ec }} opacity-70"></div>
                                        @if (isset($effectEmojis[$ec]))
                                            <span class="absolute -top-2 right-0 text-sm animate-sparkle-1">{{ $effectEmojis[$ec][0] }}</span>
                                            <span class="absolute bottom-0 -left-2 text-xs animate-sparkle-2">{{ $effectEmojis[$ec][1] }}</span>
                                        @endif
                                    @elseif (isset($particleEmojis[$item->slug]))
                                        @php $pe = $particleEmojis[$item->slug]; @endphp
                                        <span class="absolute -top-1 -right-1 text-sm animate-sparkle-1">{{ $pe[0] }}</span>
                                        <span class="absolute -bottom-1 -left-1 text-xs animate-sparkle-2">{{ $pe[1] }}</span>
                                        <span class="absolute top-1/2 -right-3 text-[10px] animate-sparkle-3">{{ $pe[2] }}</span>
                                    @else
                                        <span class="absolute -top-1 -right-1 text-sm animate-sparkle-1">✨</span>
                                        <span class="absolute -bottom-1 -left-1 text-xs animate-sparkle-2">✨</span>
                                    @endif
                                @endif

                                {{-- The avatar itself --}}
                                <div class="w-16 h-16 rounded-full overflow-hidden bg-white relative
                                    {{ $item->type === 'border' ? $item->css_class : 'ring-2 ring-[#564D4A]/10' }}">
                                    @if ($avatarUrl)
                                        <img src="{{ $avatarUrl }}" class="w-full h-full object-cover" alt="">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-[#564D4A]/10">
                                            <span class="text-[#564D4A] font-black text-lg">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2 mt-2">
                                @if ($item->type === 'badge_flair')
                                    @php
                                    $fm = $flairMeta[$item->slug] ?? ['emoji' => '✦', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200'];
                                    $isCustomFlairPreview = str_contains($item->slug, 'custom');
                                    $flairLabel = $isCustomFlairPreview ? 'Jouw tekst' : $item->name;
                                @endphp
                                @if ($isCustomFlairPreview && $item->slug === 'flair-custom-rainbow')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border animate-badge-rainbow">
                                        <span class="text-xs leading-none">🌈</span> Jouw tekst
                                    </span>
                                @elseif ($isCustomFlairPreview)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border bg-purple-100 text-purple-700 border-purple-200">
                                        <span class="text-xs leading-none">🎮</span> Jouw tekst
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $fm['bg'] }} {{ $fm['text'] }} {{ $fm['border'] }}">
                                        <span class="text-xs leading-none">{{ $fm['emoji'] }}</span> {{ $flairLabel }}
                                    </span>
                                @endif
                                @endif
                                {{-- Name --}}
                                <span class="text-sm font-bold leading-none
                                    {{ $item->type === 'name_color' ? $item->css_class : 'text-[#564D4A]' }}">
                                    {{ $firstName }}
                                </span>
                            </div>
                        </div>

                        {{-- Subtle pattern overlay --}}
                        <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(circle, currentColor 1px, transparent 1px); background-size: 16px 16px;"></div>

                        {{-- Rarity badge --}}
                        <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold uppercase tracking-wider {{ $rc['bg'] }} {{ $rc['text'] }} {{ $rc['border'] }} border backdrop-blur-sm">
                            @if (!$isPro && in_array($item->rarity, ['epic', 'legendary']))
                                <i class="fa-solid fa-crown text-[8px] text-yellow-500"></i>
                            @endif
                            {{ $rc['label'] }}
                        </span>

                        {{-- Equipped badge --}}
                        <template x-if="equippedIds.includes({{ $item->id }})">
                            <span class="absolute top-3 right-3 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[9px] font-bold bg-green-100 text-green-700 border border-green-200">
                                <i class="fa-solid fa-check text-[7px]"></i> Uitgerust
                            </span>
                        </template>
                    </div>

                    {{-- Content --}}
                    <div class="p-4 flex flex-col flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-sm font-extrabold text-[#564D4A] leading-tight truncate">{{ $item->name }}</p>
                                <p class="text-[11px] font-semibold text-[#564D4A]/45 mt-0.5">{{ $tLabel }}</p>
                            </div>
                        </div>

                        @if ($item->description)
                            <p class="mt-2 text-[11px] font-medium text-[#564D4A]/50 leading-[1.4] line-clamp-2">{{ $item->description }}</p>
                        @endif

                        <div class="mt-auto pt-3">
                            {{-- Price & level requirement --}}
                            <div class="flex items-center justify-between mb-3">
                                <span class="inline-flex items-center gap-1.5 text-sm font-bold text-[#B88B2A]">
                                    <i class="fa-solid fa-coins text-[#F59E0B] text-[11px]"></i>
                                    {{ number_format($item->price, 0, ',', '.') }}
                                </span>
                                @if ($item->level_required > 0)
                                    <span class="text-[10px] font-semibold text-[#564D4A]/40">
                                        Level {{ $item->level_required }}+
                                    </span>
                                @endif
                            </div>

                            {{-- Action button --}}
                            <template x-if="!ownedIds.includes({{ $item->id }})">
                                @if (!$isPro && in_array($item->rarity, ['epic', 'legendary']))
                                    <button @click="showUpgrade = true"
                                        class="w-full py-2.5 rounded-xl text-xs font-bold bg-gradient-to-r from-[#5B2333] to-[#7a3349] text-white hover:opacity-90 transition cursor-pointer">
                                        <i class="fa-solid fa-crown text-yellow-300 text-[10px] mr-1"></i> Pro nodig
                                    </button>
                                @else
                                    <button
                                        @click="buy({{ $item->id }}, {{ $item->price }}, {{ $item->level_required }})"
                                        :disabled="buying === {{ $item->id }} || coins < {{ $item->price }} || {{ $u->level }} < {{ $item->level_required }}"
                                        class="w-full py-2.5 rounded-xl text-xs font-bold transition cursor-pointer disabled:opacity-40 disabled:cursor-not-allowed"
                                        :class="coins >= {{ $item->price }} && {{ $u->level }} >= {{ $item->level_required }}
                                            ? 'bg-[#5B2333] hover:bg-[#5B2333]/85 text-white'
                                            : 'bg-[#564D4A]/8 text-[#564D4A]/40'">
                                        <span x-show="buying !== {{ $item->id }}">
                                            <template x-if="{{ $u->level }} < {{ $item->level_required }}">
                                                <span><i class="fa-solid fa-lock text-[10px] mr-1"></i> Level {{ $item->level_required }} nodig</span>
                                            </template>
                                            <template x-if="{{ $u->level }} >= {{ $item->level_required }} && coins < {{ $item->price }}">
                                                <span>Niet genoeg coins</span>
                                            </template>
                                            <template x-if="{{ $u->level }} >= {{ $item->level_required }} && coins >= {{ $item->price }}">
                                                <span><i class="fa-solid fa-bag-shopping text-[10px] mr-1"></i> Kopen</span>
                                            </template>
                                        </span>
                                        <span x-show="buying === {{ $item->id }}">
                                            <i class="fa-solid fa-spinner fa-spin text-[10px]"></i>
                                        </span>
                                    </button>
                                @endif
                            </template>

                            <template x-if="ownedIds.includes({{ $item->id }})">
                                <div>
                                    <template x-if="equippedIds.includes({{ $item->id }})">
                                        <button @click="unequip({{ $item->id }})"
                                            class="w-full py-2.5 rounded-xl text-xs font-bold bg-green-50 text-green-700 hover:bg-green-100 transition cursor-pointer">
                                            <i class="fa-solid fa-check text-[10px] mr-1"></i> Uitgerust
                                        </button>
                                    </template>
                                    <template x-if="!equippedIds.includes({{ $item->id }})">
                                        <div>
                                            @if ($item->slug === 'flair-custom-rainbow')
                                                {{-- Rainbow badge builder --}}
                                                <div x-data="{ open: false, customText: '', customEmoji: '🌈', showEmojis: false }">
                                                    <button x-show="!open" @click="open = true"
                                                        class="w-full py-2.5 rounded-xl text-xs font-bold bg-[#564D4A]/5 text-[#564D4A] hover:bg-[#564D4A]/10 transition cursor-pointer">
                                                        <i class="fa-solid fa-wand-magic-sparkles text-[10px] mr-1"></i> Uitrusten
                                                    </button>
                                                    <div x-show="open" x-cloak x-transition class="space-y-2">
                                                        {{-- Live preview --}}
                                                        <div class="flex justify-center py-2">
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border animate-badge-rainbow">
                                                                <span class="text-xs leading-none" x-text="customEmoji"></span>
                                                                <span x-text="customText || 'Jouw tekst'"></span>
                                                            </span>
                                                        </div>
                                                        <input type="text" x-model="customText" maxlength="20" placeholder="Typ je tekst..."
                                                            class="w-full px-3 py-2 rounded-lg bg-[#F7F4F3] border border-[#564D4A]/10 text-xs font-semibold text-[#564D4A] placeholder:text-[#564D4A]/30 focus:outline-none focus:ring-2 focus:ring-[#5B2333]/20 focus:border-[#5B2333]/30 transition">
                                                        {{-- Emoji picker --}}
                                                        <div class="relative">
                                                            <button @click="showEmojis = !showEmojis" type="button"
                                                                class="w-full flex items-center justify-between px-3 py-2 rounded-lg bg-[#F7F4F3] border border-[#564D4A]/10 text-xs font-semibold text-[#564D4A] hover:bg-[#564D4A]/5 transition cursor-pointer">
                                                                <span><span x-text="customEmoji" class="mr-1"></span> Kies icon</span>
                                                                <i class="fa-solid fa-chevron-down text-[9px] text-[#564D4A]/40"></i>
                                                            </button>
                                                            <div x-show="showEmojis" x-cloak @click.outside="showEmojis = false"
                                                                x-transition
                                                                class="absolute bottom-full left-0 right-0 mb-1 p-2 bg-white rounded-xl border border-[#564D4A]/10 shadow-lg z-20 grid grid-cols-7 gap-1">
                                                                @foreach (['✦','⭐','🔥','💀','🎮','🏆','💎','⚡','🎯','👑','🐐','🧠','💪','🎵','🌟','💫','❤️','💜','🦊','🐉','🌈','🍀','🎲','🪐','🤖','🦄','🎪'] as $e)
                                                                    <button type="button" @click="customEmoji = '{{ $e }}'; showEmojis = false"
                                                                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-[#F7F4F3] text-base cursor-pointer transition"
                                                                        :class="customEmoji === '{{ $e }}' ? 'bg-[#5B2333]/10 ring-1 ring-[#5B2333]/20' : ''">
                                                                        {{ $e }}
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="flex gap-2">
                                                            <button @click="if(customText.trim()) equipCustom({{ $item->id }}, customText.trim(), customEmoji, 'rainbow')"
                                                                :disabled="!customText.trim()"
                                                                class="flex-1 py-2.5 rounded-xl text-xs font-bold bg-[#5B2333] text-white hover:bg-[#5B2333]/85 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                                                                <i class="fa-solid fa-check text-[10px] mr-1"></i> Opslaan
                                                            </button>
                                                            <button @click="open = false"
                                                                class="px-3 py-2.5 rounded-xl text-xs font-bold bg-[#564D4A]/5 text-[#564D4A]/50 hover:bg-[#564D4A]/10 transition cursor-pointer">
                                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @elseif (str_contains($item->slug, 'custom'))
                                                {{-- Custom badge builder (with color picker) --}}
                                                <div x-data="{
                                                    open: false,
                                                    customText: '',
                                                    customEmoji: '✦',
                                                    customColor: 'slate',
                                                    showEmojis: false,
                                                    showColors: false,
                                                    colorClasses: {
                                                        red:     'bg-red-100 text-red-700 border-red-200',
                                                        orange:  'bg-orange-100 text-orange-700 border-orange-200',
                                                        yellow:  'bg-yellow-100 text-yellow-700 border-yellow-200',
                                                        green:   'bg-green-100 text-green-700 border-green-200',
                                                        emerald: 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                        cyan:    'bg-cyan-100 text-cyan-700 border-cyan-200',
                                                        blue:    'bg-blue-100 text-blue-700 border-blue-200',
                                                        indigo:  'bg-indigo-100 text-indigo-700 border-indigo-200',
                                                        purple:  'bg-purple-100 text-purple-700 border-purple-200',
                                                        pink:    'bg-pink-100 text-pink-700 border-pink-200',
                                                        slate:   'bg-slate-100 text-slate-700 border-slate-200',
                                                    },
                                                    dotClasses: {
                                                        red: 'bg-red-400', orange: 'bg-orange-400', yellow: 'bg-yellow-400',
                                                        green: 'bg-green-400', emerald: 'bg-emerald-400', cyan: 'bg-cyan-400',
                                                        blue: 'bg-blue-400', indigo: 'bg-indigo-400', purple: 'bg-purple-400',
                                                        pink: 'bg-pink-400', slate: 'bg-slate-400',
                                                    }
                                                }">
                                                    <button x-show="!open" @click="open = true"
                                                        class="w-full py-2.5 rounded-xl text-xs font-bold bg-[#564D4A]/5 text-[#564D4A] hover:bg-[#564D4A]/10 transition cursor-pointer">
                                                        <i class="fa-solid fa-wand-magic-sparkles text-[10px] mr-1"></i> Uitrusten
                                                    </button>
                                                    <div x-show="open" x-cloak x-transition class="space-y-2">
                                                        {{-- Live preview --}}
                                                        <div class="flex justify-center py-2">
                                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold border"
                                                                  :class="colorClasses[customColor]">
                                                                <span class="text-xs leading-none" x-text="customEmoji"></span>
                                                                <span x-text="customText || 'Jouw tekst'"></span>
                                                            </span>
                                                        </div>
                                                        <input type="text" x-model="customText" maxlength="20" placeholder="Typ je tekst..."
                                                            class="w-full px-3 py-2 rounded-lg bg-[#F7F4F3] border border-[#564D4A]/10 text-xs font-semibold text-[#564D4A] placeholder:text-[#564D4A]/30 focus:outline-none focus:ring-2 focus:ring-[#5B2333]/20 focus:border-[#5B2333]/30 transition">
                                                        {{-- Emoji picker --}}
                                                        <div class="relative">
                                                            <button @click="showEmojis = !showEmojis; showColors = false" type="button"
                                                                class="w-full flex items-center justify-between px-3 py-2 rounded-lg bg-[#F7F4F3] border border-[#564D4A]/10 text-xs font-semibold text-[#564D4A] hover:bg-[#564D4A]/5 transition cursor-pointer">
                                                                <span><span x-text="customEmoji" class="mr-1"></span> Kies icon</span>
                                                                <i class="fa-solid fa-chevron-down text-[9px] text-[#564D4A]/40"></i>
                                                            </button>
                                                            <div x-show="showEmojis" x-cloak @click.outside="showEmojis = false"
                                                                x-transition
                                                                class="absolute bottom-full left-0 right-0 mb-1 p-2 bg-white rounded-xl border border-[#564D4A]/10 shadow-lg z-20 grid grid-cols-7 gap-1">
                                                                @foreach (['✦','⭐','🔥','💀','🎮','🏆','💎','⚡','🎯','👑','🐐','🧠','💪','🎵','🌟','💫','❤️','💜','🦊','🐉','🌈','🍀','🎲','🪐','🤖','🦄','🎪'] as $e)
                                                                    <button type="button" @click="customEmoji = '{{ $e }}'; showEmojis = false"
                                                                        class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-[#F7F4F3] text-base cursor-pointer transition"
                                                                        :class="customEmoji === '{{ $e }}' ? 'bg-[#5B2333]/10 ring-1 ring-[#5B2333]/20' : ''">
                                                                        {{ $e }}
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        {{-- Color picker --}}
                                                        <div class="relative">
                                                            <button @click="showColors = !showColors; showEmojis = false" type="button"
                                                                class="w-full flex items-center justify-between px-3 py-2 rounded-lg bg-[#F7F4F3] border border-[#564D4A]/10 text-xs font-semibold text-[#564D4A] hover:bg-[#564D4A]/5 transition cursor-pointer">
                                                                <span class="flex items-center gap-2">
                                                                    <span class="w-3 h-3 rounded-full" :class="dotClasses[customColor]"></span>
                                                                    Kies kleur
                                                                </span>
                                                                <i class="fa-solid fa-chevron-down text-[9px] text-[#564D4A]/40"></i>
                                                            </button>
                                                            <div x-show="showColors" x-cloak @click.outside="showColors = false"
                                                                x-transition
                                                                class="absolute bottom-full left-0 right-0 mb-1 p-2 bg-white rounded-xl border border-[#564D4A]/10 shadow-lg z-20 flex flex-wrap gap-1.5 justify-center">
                                                                @foreach (['red','orange','yellow','green','emerald','cyan','blue','indigo','purple','pink','slate'] as $c)
                                                                    <button type="button" @click="customColor = '{{ $c }}'; showColors = false"
                                                                        class="w-7 h-7 rounded-full bg-{{ $c }}-400 hover:scale-110 transition cursor-pointer ring-offset-1"
                                                                        :class="customColor === '{{ $c }}' ? 'ring-2 ring-[#564D4A]/40' : ''">
                                                                    </button>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <div class="flex gap-2">
                                                            <button @click="if(customText.trim()) equipCustom({{ $item->id }}, customText.trim(), customEmoji, customColor)"
                                                                :disabled="!customText.trim()"
                                                                class="flex-1 py-2.5 rounded-xl text-xs font-bold bg-[#5B2333] text-white hover:bg-[#5B2333]/85 transition cursor-pointer disabled:opacity-30 disabled:cursor-not-allowed">
                                                                <i class="fa-solid fa-check text-[10px] mr-1"></i> Opslaan
                                                            </button>
                                                            <button @click="open = false"
                                                                class="px-3 py-2.5 rounded-xl text-xs font-bold bg-[#564D4A]/5 text-[#564D4A]/50 hover:bg-[#564D4A]/10 transition cursor-pointer">
                                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <button @click="equip({{ $item->id }})"
                                                    class="w-full py-2.5 rounded-xl text-xs font-bold bg-[#564D4A]/5 text-[#564D4A] hover:bg-[#564D4A]/10 transition cursor-pointer">
                                                    <i class="fa-solid fa-wand-magic-sparkles text-[10px] mr-1"></i> Uitrusten
                                                </button>
                                            @endif
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-[#564D4A]/5 flex items-center justify-center mx-auto">
                        <i class="fa-solid fa-bag-shopping text-[#564D4A]/30 text-2xl"></i>
                    </div>
                    <p class="mt-4 text-sm font-bold text-[#564D4A]/50">Nog geen items beschikbaar</p>
                    <p class="mt-1 text-xs font-semibold text-[#564D4A]/35">Check later terug!</p>
                </div>
            @endforelse

            {{-- Infinite scroll sentinel --}}
            <div x-ref="scrollSentinel" x-show="hasMore" x-cloak class="col-span-full flex justify-center py-6">
                <span class="inline-flex items-center gap-2 text-xs font-semibold text-[#564D4A]/40">
                    <i class="fa-solid fa-spinner fa-spin text-[11px]"></i> Meer laden...
                </span>
            </div>

            {{-- Empty state for owned view --}}
            <div x-show="view === 'owned' && visibleOwned === 0" x-cloak class="col-span-full py-16 text-center">
                <div class="w-16 h-16 rounded-2xl bg-[#564D4A]/5 flex items-center justify-center mx-auto">
                    <i class="fa-solid fa-box-open text-[#564D4A]/30 text-2xl"></i>
                </div>
                <p class="mt-4 text-sm font-bold text-[#564D4A]/50">Nog geen items in je collectie</p>
                <p class="mt-1 text-xs font-semibold text-[#564D4A]/35">Koop items in de shop om ze hier te zien!</p>
                <button @click="view = 'shop'" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#5B2333] text-white text-xs font-semibold hover:bg-[#5B2333]/85 transition cursor-pointer">
                    <i class="fa-solid fa-bag-shopping text-[10px]"></i> Naar de Shop
                </button>
            </div>
        </div>

        {{-- PRO UPGRADE MODAL --}}
        <template x-if="showUpgrade">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4" x-cloak
                 @keydown.escape.window="showUpgrade = false">
                <div class="absolute inset-0 bg-[#564D4A]/60 backdrop-blur-md" @click="showUpgrade = false"></div>
                <div class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full p-8 text-center" @click.stop>
                    <button @click="showUpgrade = false" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-[#564D4A]/5 hover:bg-[#564D4A]/10 flex items-center justify-center transition cursor-pointer">
                        <i class="fa-solid fa-xmark text-[#564D4A]/50 text-sm"></i>
                    </button>

                    <div class="w-14 h-14 rounded-2xl bg-[#5B2333] flex items-center justify-center mx-auto mb-5">
                        <i class="fa-solid fa-crown text-yellow-300 text-xl"></i>
                    </div>

                    <h3 class="text-xl font-black text-[#564D4A]">Pro-exclusieve cosmetics</h3>
                    <p class="mt-2 text-sm text-[#564D4A]/50 font-medium leading-relaxed">
                        <span class="font-bold text-[#8B5CF6]">Epische</span> en <span class="font-bold text-[#B88B2A]">Legendarische</span> items zijn exclusief voor
                        <span class="font-bold text-[#5B2333]">Pro</span>-leden.
                    </p>

                    <div class="mt-6 space-y-2.5">
                        <div class="flex items-center gap-3 text-left px-4 py-3 rounded-xl bg-[#F7F4F3]">
                            <i class="fa-solid fa-wand-magic-sparkles text-[#8B5CF6] text-sm"></i>
                            <span class="text-sm font-semibold text-[#564D4A]">Alle epische & legendarische cosmetics</span>
                        </div>
                        <div class="flex items-center gap-3 text-left px-4 py-3 rounded-xl bg-[#F7F4F3]">
                            <i class="fa-solid fa-infinity text-[#5B2333] text-sm"></i>
                            <span class="text-sm font-semibold text-[#564D4A]">Onbeperkt spellen per dag</span>
                        </div>
                        <div class="flex items-center gap-3 text-left px-4 py-3 rounded-xl bg-[#F7F4F3]">
                            <i class="fa-solid fa-bolt text-[#E8A838] text-sm"></i>
                            <span class="text-sm font-semibold text-[#564D4A]">Exclusieve Pro badge op je profiel</span>
                        </div>
                    </div>

                    <div class="mt-7 flex flex-col gap-3">
                        <a href="{{ route('pages.pricing') }}" class="block w-full py-3.5 rounded-xl bg-[#5B2333] text-white font-bold text-sm text-center hover:bg-[#5B2333]/90 transition">
                            <i class="fa-solid fa-crown text-yellow-300 mr-2"></i> Upgrade naar Pro — 1,99/maand
                        </a>
                        <button @click="showUpgrade = false" class="w-full py-3 rounded-xl bg-[#564D4A]/5 text-[#564D4A]/50 font-semibold text-sm hover:bg-[#564D4A]/10 transition cursor-pointer">
                            Misschien later
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <script>
        function shopPage() {
            const ITEMS_PER_PAGE = 12; // 3 rows × 4 cols
            return {
                coins: @json($coins),
                ownedIds: @json($ownedIds),
                equippedIds: @json($equippedIds),
                typeMap: @json($itemTypeMap),
                isPro: @json($isPro),
                showUpgrade: false,
                buying: null,
                message: '',
                msgType: '',
                view: 'shop',
                filter: 'all',
                visibleCount: ITEMS_PER_PAGE,
                allItems: @json($items->map(fn($i) => ['id' => $i->id, 'type' => $i->type])->values()),

                get filteredItems() {
                    return this.allItems.filter(i => {
                        const matchesFilter = this.filter === 'all' || this.filter === i.type;
                        const matchesView = this.view === 'shop' ? !this.ownedIds.includes(i.id) : this.ownedIds.includes(i.id);
                        return matchesFilter && matchesView;
                    });
                },

                get hasMore() {
                    return this.visibleCount < this.filteredItems.length;
                },

                get visibleOwned() {
                    return this.ownedIds.filter(id => {
                        if (this.filter === 'all') return true;
                        return this.typeMap[id] === this.filter;
                    }).length;
                },

                isItemVisible(itemId, itemType) {
                    const filtered = this.filteredItems;
                    const pos = filtered.findIndex(i => i.id === itemId);
                    return pos !== -1 && pos < this.visibleCount;
                },

                loadMore() {
                    this.visibleCount = Math.min(this.visibleCount + ITEMS_PER_PAGE, this.filteredItems.length);
                },

                init() {
                    // Reset visible count when filter or view changes
                    this.$watch('filter', () => { this.visibleCount = ITEMS_PER_PAGE; });
                    this.$watch('view', () => { this.visibleCount = ITEMS_PER_PAGE; });

                    // IntersectionObserver for infinite scroll
                    this.$nextTick(() => {
                        const sentinel = this.$refs.scrollSentinel;
                        if (!sentinel) return;
                        const observer = new IntersectionObserver((entries) => {
                            if (entries[0].isIntersecting && this.hasMore) {
                                this.loadMore();
                            }
                        }, { rootMargin: '200px' });
                        observer.observe(sentinel);
                    });
                },

                async buy(itemId, price, levelReq) {
                    if (this.buying) return;
                    if (this.ownedIds.includes(itemId)) return;
                    if (this.coins < price) {
                        this.flash('Niet genoeg coins!', 'error');
                        return;
                    }
                    this.buying = itemId;
                    try {
                        const res = await fetch('{{ route("shop.buy") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ item_id: itemId })
                        });
                        const data = await res.json();
                        if (data.ok) {
                            this.coins = data.coins;
                            this.ownedIds.push(itemId);
                            this.flash('Item gekocht!', 'success');
                        } else {
                            this.flash(data.error || 'Er ging iets mis.', 'error');
                        }
                    } catch(e) {
                        this.flash('Er ging iets mis.', 'error');
                    }
                    this.buying = null;
                },

                async buyBundle(bundleId, price, levelReq, itemIds) {
                    if (this.buying) return;
                    if (this.coins < price) {
                        this.flash('Niet genoeg coins!', 'error');
                        return;
                    }
                    this.buying = bundleId;
                    try {
                        const res = await fetch('{{ route("shop.buyBundle") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ bundle_id: bundleId })
                        });
                        const data = await res.json();
                        if (data.ok) {
                            this.coins = data.coins;
                            data.newItemIds.forEach(id => {
                                if (!this.ownedIds.includes(id)) this.ownedIds.push(id);
                            });
                            this.flash('Pakket gekocht! ' + data.newItemIds.length + ' items toegevoegd.', 'success');
                        } else {
                            this.flash(data.error || 'Er ging iets mis.', 'error');
                        }
                    } catch(e) {
                        this.flash('Er ging iets mis.', 'error');
                    }
                    this.buying = null;
                },

                async equip(itemId) {
                    try {
                        const res = await fetch('{{ route("shop.equip") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ item_id: itemId })
                        });
                        const data = await res.json();
                        if (data.ok) {
                            const type = this.typeMap[itemId];
                            if (type) {
                                const sameTypeIds = Object.keys(this.typeMap)
                                    .filter(k => this.typeMap[k] === type)
                                    .map(Number);
                                this.equippedIds = this.equippedIds.filter(i => !sameTypeIds.includes(i));
                            }
                            this.equippedIds.push(itemId);
                            this.flash('Item uitgerust!', 'success');
                        }
                    } catch(e) {}
                },

                async equipCustom(itemId, customText, customEmoji, customColor) {
                    try {
                        const res = await fetch('{{ route("shop.equip") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ item_id: itemId, custom_text: customText, custom_emoji: customEmoji || '✦', custom_color: customColor || 'slate' })
                        });
                        const data = await res.json();
                        if (data.ok) {
                            const type = this.typeMap[itemId];
                            if (type) {
                                const sameTypeIds = Object.keys(this.typeMap)
                                    .filter(k => this.typeMap[k] === type)
                                    .map(Number);
                                this.equippedIds = this.equippedIds.filter(i => !sameTypeIds.includes(i));
                            }
                            this.equippedIds.push(itemId);
                            this.flash('Badge met custom tekst uitgerust!', 'success');
                        } else {
                            this.flash(data.error || 'Er ging iets mis.', 'error');
                        }
                    } catch(e) {}
                },

                async unequip(itemId) {
                    try {
                        const res = await fetch('{{ route("shop.unequip") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                            body: JSON.stringify({ item_id: itemId })
                        });
                        const data = await res.json();
                        if (data.ok) {
                            this.equippedIds = this.equippedIds.filter(i => i !== itemId);
                            this.flash('Item afgedaan.', 'success');
                        }
                    } catch(e) {}
                },

                flash(msg, type) {
                    this.message = msg;
                    this.msgType = type;
                    setTimeout(() => { this.message = ''; }, 2500);
                }
            };
        }
    </script>
</x-layouts.dashboard>
