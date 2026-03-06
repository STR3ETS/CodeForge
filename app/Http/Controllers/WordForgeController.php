<?php

namespace App\Http\Controllers;

use App\Models\DailyGameRun;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WordForgeController extends Controller
{
    private const GAME_KEY = 'word-forge';
    private const MAX_ATTEMPTS = 5;
    private const MIN_LEN = 3;
    private const MAX_LEN = 12;

    private function pool(): array
    {
        return [
            // =========================
            // Technologie (50)
            // =========================
            ['category' => 'Technologie', 'word' => 'API'],
            ['category' => 'Technologie', 'word' => 'CACHE'],
            ['category' => 'Technologie', 'word' => 'KADER'],
            ['category' => 'Technologie', 'word' => 'TOEPASSING'],
            ['category' => 'Technologie', 'word' => 'SERVER'],
            ['category' => 'Technologie', 'word' => 'DATABASE'],
            ['category' => 'Technologie', 'word' => 'JAVASCRIPT'],
            ['category' => 'Technologie', 'word' => 'LARAVEL'],
            ['category' => 'Technologie', 'word' => 'ALGORITME'],
            ['category' => 'Technologie', 'word' => 'FUNCTIE'],
            ['category' => 'Technologie', 'word' => 'PAKKET'],
            ['category' => 'Technologie', 'word' => 'BIBLIOTHEEK'],
            ['category' => 'Technologie', 'word' => 'COMPILER'],
            ['category' => 'Technologie', 'word' => 'VARIABELE'],
            ['category' => 'Technologie', 'word' => 'INTERFACE'],
            ['category' => 'Technologie', 'word' => 'EINDPUNT'],
            ['category' => 'Technologie', 'word' => 'BEVEILIGING'],
            ['category' => 'Technologie', 'word' => 'FIREWALL'],
            ['category' => 'Technologie', 'word' => 'PROTOCOL'],
            ['category' => 'Technologie', 'word' => 'VIRTUEEL'],
            ['category' => 'Technologie', 'word' => 'CONTAINER'],
            ['category' => 'Technologie', 'word' => 'DOCKER'],
            ['category' => 'Technologie', 'word' => 'KUBERNETES'],
            ['category' => 'Technologie', 'word' => 'GITHUB'],
            ['category' => 'Technologie', 'word' => 'VRIJGAVE'],
            ['category' => 'Technologie', 'word' => 'PIJPLIJN'],
            ['category' => 'Technologie', 'word' => 'BOUWEN'],
            ['category' => 'Technologie', 'word' => 'BACKEND'],
            ['category' => 'Technologie', 'word' => 'FRONTEND'],
            ['category' => 'Technologie', 'word' => 'BROWSER'],
            ['category' => 'Technologie', 'word' => 'COOKIE'],
            ['category' => 'Technologie', 'word' => 'SESSIE'],
            ['category' => 'Technologie', 'word' => 'TOKEN'],
            ['category' => 'Technologie', 'word' => 'OAUTH'],
            ['category' => 'Technologie', 'word' => 'WEBHOOK'],
            ['category' => 'Technologie', 'word' => 'BEWAKING'],
            ['category' => 'Technologie', 'word' => 'LOGBOEK'],
            ['category' => 'Technologie', 'word' => 'LATENTIE'],
            ['category' => 'Technologie', 'word' => 'BANDBREEDTE'],
            ['category' => 'Technologie', 'word' => 'MICROSERVICE'],
            ['category' => 'Technologie', 'word' => 'OPSLAG'],
            ['category' => 'Technologie', 'word' => 'BRANCH'],
            ['category' => 'Technologie', 'word' => 'SAMENVOEGEN'],
            ['category' => 'Technologie', 'word' => 'COMMIT'],
            ['category' => 'Technologie', 'word' => 'VERZOEK'],
            ['category' => 'Technologie', 'word' => 'DEBUGGER'],
            ['category' => 'Technologie', 'word' => 'TERMINAL'],
            ['category' => 'Technologie', 'word' => 'NETWERK'],
            ['category' => 'Technologie', 'word' => 'CODERING'],
            ['category' => 'Technologie', 'word' => 'PROGRAMMA'],

            // =========================
            // Steden (50)
            // =========================
            ['category' => 'Steden', 'word' => 'ROME'],
            ['category' => 'Steden', 'word' => 'AMSTERDAM'],
            ['category' => 'Steden', 'word' => 'KOPENHAGEN'],
            ['category' => 'Steden', 'word' => 'UTRECHT'],
            ['category' => 'Steden', 'word' => 'PARIJS'],
            ['category' => 'Steden', 'word' => 'BERLIJN'],
            ['category' => 'Steden', 'word' => 'MADRID'],
            ['category' => 'Steden', 'word' => 'LONDEN'],
            ['category' => 'Steden', 'word' => 'DUBAI'],
            ['category' => 'Steden', 'word' => 'WENEN'],
            ['category' => 'Steden', 'word' => 'PRAAG'],
            ['category' => 'Steden', 'word' => 'BOEDAPEST'],
            ['category' => 'Steden', 'word' => 'WARSCHAU'],
            ['category' => 'Steden', 'word' => 'LISSABON'],
            ['category' => 'Steden', 'word' => 'OSLO'],
            ['category' => 'Steden', 'word' => 'STOCKHOLM'],
            ['category' => 'Steden', 'word' => 'HELSINKI'],
            ['category' => 'Steden', 'word' => 'ATHENE'],
            ['category' => 'Steden', 'word' => 'SOFIA'],
            ['category' => 'Steden', 'word' => 'ZAGREB'],
            ['category' => 'Steden', 'word' => 'BRUSSEL'],
            ['category' => 'Steden', 'word' => 'ANTWERPEN'],
            ['category' => 'Steden', 'word' => 'ROTTERDAM'],
            ['category' => 'Steden', 'word' => 'DENHAAG'],
            ['category' => 'Steden', 'word' => 'MUNCHEN'],
            ['category' => 'Steden', 'word' => 'HAMBURG'],
            ['category' => 'Steden', 'word' => 'KEULEN'],
            ['category' => 'Steden', 'word' => 'FRANKFURT'],
            ['category' => 'Steden', 'word' => 'MILAAN'],
            ['category' => 'Steden', 'word' => 'VENETIE'],
            ['category' => 'Steden', 'word' => 'FLORENCE'],
            ['category' => 'Steden', 'word' => 'NAPELS'],
            ['category' => 'Steden', 'word' => 'TURIJN'],
            ['category' => 'Steden', 'word' => 'BARCELONA'],
            ['category' => 'Steden', 'word' => 'VALENCIA'],
            ['category' => 'Steden', 'word' => 'SEVILLA'],
            ['category' => 'Steden', 'word' => 'MARSEILLE'],
            ['category' => 'Steden', 'word' => 'LYON'],
            ['category' => 'Steden', 'word' => 'NICE'],
            ['category' => 'Steden', 'word' => 'GENEVE'],
            ['category' => 'Steden', 'word' => 'ZURICH'],
            ['category' => 'Steden', 'word' => 'BAZEL'],
            ['category' => 'Steden', 'word' => 'DUBLIN'],
            ['category' => 'Steden', 'word' => 'EDINBURGH'],
            ['category' => 'Steden', 'word' => 'GLASGOW'],
            ['category' => 'Steden', 'word' => 'NEWYORK'],
            ['category' => 'Steden', 'word' => 'CHICAGO'],
            ['category' => 'Steden', 'word' => 'TORONTO'],
            ['category' => 'Steden', 'word' => 'VANCOUVER'],
            ['category' => 'Steden', 'word' => 'SYDNEY'],

            // =========================
            // Eten (50)
            // =========================
            ['category' => 'Eten', 'word' => 'THEE'],
            ['category' => 'Eten', 'word' => 'SUSHI'],
            ['category' => 'Eten', 'word' => 'CHOCOLADE'],
            ['category' => 'Eten', 'word' => 'PASTA'],
            ['category' => 'Eten', 'word' => 'HAMBURGER'],
            ['category' => 'Eten', 'word' => 'PIZZA'],
            ['category' => 'Eten', 'word' => 'KAAS'],
            ['category' => 'Eten', 'word' => 'ZALM'],
            ['category' => 'Eten', 'word' => 'PANNENKOEK'],
            ['category' => 'Eten', 'word' => 'NOEDELS'],
            ['category' => 'Eten', 'word' => 'KERRIE'],
            ['category' => 'Eten', 'word' => 'TACO'],
            ['category' => 'Eten', 'word' => 'BAGEL'],
            ['category' => 'Eten', 'word' => 'MUFFIN'],
            ['category' => 'Eten', 'word' => 'YOGHURT'],
            ['category' => 'Eten', 'word' => 'OMELET'],
            ['category' => 'Eten', 'word' => 'RISOTTO'],
            ['category' => 'Eten', 'word' => 'LASAGNE'],
            ['category' => 'Eten', 'word' => 'KIMCHI'],
            ['category' => 'Eten', 'word' => 'HUMMUS'],
            ['category' => 'Eten', 'word' => 'FALAFEL'],
            ['category' => 'Eten', 'word' => 'BROWNIE'],
            ['category' => 'Eten', 'word' => 'CROISSANT'],
            ['category' => 'Eten', 'word' => 'KOEKJE'],
            ['category' => 'Eten', 'word' => 'WORST'],
            ['category' => 'Eten', 'word' => 'BIEFSTUK'],
            ['category' => 'Eten', 'word' => 'KIP'],
            ['category' => 'Eten', 'word' => 'TOFU'],
            ['category' => 'Eten', 'word' => 'AVOCADO'],
            ['category' => 'Eten', 'word' => 'BANAAN'],
            ['category' => 'Eten', 'word' => 'SINAASAPPEL'],
            ['category' => 'Eten', 'word' => 'MANGO'],
            ['category' => 'Eten', 'word' => 'ANANAS'],
            ['category' => 'Eten', 'word' => 'AARDBEI'],
            ['category' => 'Eten', 'word' => 'BOSBES'],
            ['category' => 'Eten', 'word' => 'WATERMELOEN'],
            ['category' => 'Eten', 'word' => 'KOMKOMMER'],
            ['category' => 'Eten', 'word' => 'TOMAAT'],
            ['category' => 'Eten', 'word' => 'WORTEL'],
            ['category' => 'Eten', 'word' => 'SPINAZIE'],
            ['category' => 'Eten', 'word' => 'BROCCOLI'],
            ['category' => 'Eten', 'word' => 'AARDAPPEL'],
            ['category' => 'Eten', 'word' => 'KNOFLOOK'],
            ['category' => 'Eten', 'word' => 'GEMBER'],
            ['category' => 'Eten', 'word' => 'KANEEL'],
            ['category' => 'Eten', 'word' => 'VANILLE'],
            ['category' => 'Eten', 'word' => 'KOFFIE'],
            ['category' => 'Eten', 'word' => 'MATCHA'],
            ['category' => 'Eten', 'word' => 'POPCORN'],
            ['category' => 'Eten', 'word' => 'ROOMIJS'],

            // =========================
            // Natuur (50)
            // =========================
            ['category' => 'Natuur', 'word' => 'EIK'],
            ['category' => 'Natuur', 'word' => 'BERG'],
            ['category' => 'Natuur', 'word' => 'RIVIER'],
            ['category' => 'Natuur', 'word' => 'BOS'],
            ['category' => 'Natuur', 'word' => 'OCEAAN'],
            ['category' => 'Natuur', 'word' => 'WOESTIJN'],
            ['category' => 'Natuur', 'word' => 'BLOEM'],
            ['category' => 'Natuur', 'word' => 'ONWEER'],
            ['category' => 'Natuur', 'word' => 'DAL'],
            ['category' => 'Natuur', 'word' => 'GLETSJER'],
            ['category' => 'Natuur', 'word' => 'VULKAAN'],
            ['category' => 'Natuur', 'word' => 'WATERVAL'],
            ['category' => 'Natuur', 'word' => 'EILAND'],
            ['category' => 'Natuur', 'word' => 'RIF'],
            ['category' => 'Natuur', 'word' => 'KLOOF'],
            ['category' => 'Natuur', 'word' => 'WEIDE'],
            ['category' => 'Natuur', 'word' => 'SAVANNE'],
            ['category' => 'Natuur', 'word' => 'JUNGLE'],
            ['category' => 'Natuur', 'word' => 'PRAIRIE'],
            ['category' => 'Natuur', 'word' => 'TOENDRA'],
            ['category' => 'Natuur', 'word' => 'BEEK'],
            ['category' => 'Natuur', 'word' => 'MEER'],
            ['category' => 'Natuur', 'word' => 'VIJVER'],
            ['category' => 'Natuur', 'word' => 'BAAI'],
            ['category' => 'Natuur', 'word' => 'KUST'],
            ['category' => 'Natuur', 'word' => 'KLIF'],
            ['category' => 'Natuur', 'word' => 'GROT'],
            ['category' => 'Natuur', 'word' => 'BOSJE'],
            ['category' => 'Natuur', 'word' => 'MOS'],
            ['category' => 'Natuur', 'word' => 'VAREN'],
            ['category' => 'Natuur', 'word' => 'CEDER'],
            ['category' => 'Natuur', 'word' => 'DEN'],
            ['category' => 'Natuur', 'word' => 'ESDOORN'],
            ['category' => 'Natuur', 'word' => 'BERK'],
            ['category' => 'Natuur', 'word' => 'WILG'],
            ['category' => 'Natuur', 'word' => 'KLIMOP'],
            ['category' => 'Natuur', 'word' => 'ROOS'],
            ['category' => 'Natuur', 'word' => 'LELIE'],
            ['category' => 'Natuur', 'word' => 'ORCHIDEE'],
            ['category' => 'Natuur', 'word' => 'MADELIEFJE'],
            ['category' => 'Natuur', 'word' => 'BRIES'],
            ['category' => 'Natuur', 'word' => 'STORM'],
            ['category' => 'Natuur', 'word' => 'REGENBOOG'],
            ['category' => 'Natuur', 'word' => 'MAANLICHT'],
            ['category' => 'Natuur', 'word' => 'ZONSOPGANG'],
            ['category' => 'Natuur', 'word' => 'LAWINE'],
            ['category' => 'Natuur', 'word' => 'AARDBEVING'],
            ['category' => 'Natuur', 'word' => 'ORKAAN'],
            ['category' => 'Natuur', 'word' => 'MOESSON'],
            ['category' => 'Natuur', 'word' => 'AURORA'],

            // =========================
            // Sport (50)
            // =========================
            ['category' => 'Sport', 'word' => 'VOETBAL'],
            ['category' => 'Sport', 'word' => 'TENNIS'],
            ['category' => 'Sport', 'word' => 'CRICKET'],
            ['category' => 'Sport', 'word' => 'HOCKEY'],
            ['category' => 'Sport', 'word' => 'BOKSEN'],
            ['category' => 'Sport', 'word' => 'RACEN'],
            ['category' => 'Sport', 'word' => 'FIETSEN'],
            ['category' => 'Sport', 'word' => 'ZWEMMEN'],
            ['category' => 'Sport', 'word' => 'HARDLOPEN'],
            ['category' => 'Sport', 'word' => 'SKIEEN'],
            ['category' => 'Sport', 'word' => 'SNOWBOARD'],
            ['category' => 'Sport', 'word' => 'HONKBAL'],
            ['category' => 'Sport', 'word' => 'BASKETBAL'],
            ['category' => 'Sport', 'word' => 'VOLLEYBAL'],
            ['category' => 'Sport', 'word' => 'RUGBY'],
            ['category' => 'Sport', 'word' => 'GOLF'],
            ['category' => 'Sport', 'word' => 'BADMINTON'],
            ['category' => 'Sport', 'word' => 'KARATE'],
            ['category' => 'Sport', 'word' => 'JUDO'],
            ['category' => 'Sport', 'word' => 'TAEKWONDO'],
            ['category' => 'Sport', 'word' => 'ROEIEN'],
            ['category' => 'Sport', 'word' => 'ZEILEN'],
            ['category' => 'Sport', 'word' => 'SURFEN'],
            ['category' => 'Sport', 'word' => 'SKATEBOARD'],
            ['category' => 'Sport', 'word' => 'KLIMMEN'],
            ['category' => 'Sport', 'word' => 'SCHERMEN'],
            ['category' => 'Sport', 'word' => 'WORSTELEN'],
            ['category' => 'Sport', 'word' => 'GYMNASTIEK'],
            ['category' => 'Sport', 'word' => 'TRIATLON'],
            ['category' => 'Sport', 'word' => 'MARATHON'],
            ['category' => 'Sport', 'word' => 'SPRINT'],
            ['category' => 'Sport', 'word' => 'PENTATHLON'],
            ['category' => 'Sport', 'word' => 'TAFELTENNIS'],
            ['category' => 'Sport', 'word' => 'HANDBAL'],
            ['category' => 'Sport', 'word' => 'LACROSSE'],
            ['category' => 'Sport', 'word' => 'DARTEN'],
            ['category' => 'Sport', 'word' => 'BOWLEN'],
            ['category' => 'Sport', 'word' => 'BILJART'],
            ['category' => 'Sport', 'word' => 'SCHAKEN'],
            ['category' => 'Sport', 'word' => 'ESPORTS'],
            ['category' => 'Sport', 'word' => 'KICKBOKSEN'],
            ['category' => 'Sport', 'word' => 'PILATES'],
            ['category' => 'Sport', 'word' => 'YOGA'],
            ['category' => 'Sport', 'word' => 'CROSSFIT'],
            ['category' => 'Sport', 'word' => 'WANDELEN'],
            ['category' => 'Sport', 'word' => 'DUIKEN'],
            ['category' => 'Sport', 'word' => 'KAJAKKEN'],
            ['category' => 'Sport', 'word' => 'MMA'],
            ['category' => 'Sport', 'word' => 'WIELRENNEN'],
            ['category' => 'Sport', 'word' => 'BOOGSCHIETEN'],

            // =========================
            // Zakelijk (50)
            // =========================
            ['category' => 'Zakelijk', 'word' => 'STARTUP'],
            ['category' => 'Zakelijk', 'word' => 'INVESTEERDER'],
            ['category' => 'Zakelijk', 'word' => 'MARKETING'],
            ['category' => 'Zakelijk', 'word' => 'STRATEGIE'],
            ['category' => 'Zakelijk', 'word' => 'BRANDING'],
            ['category' => 'Zakelijk', 'word' => 'OMZET'],
            ['category' => 'Zakelijk', 'word' => 'WINST'],
            ['category' => 'Zakelijk', 'word' => 'FACTUUR'],
            ['category' => 'Zakelijk', 'word' => 'BEGROTING'],
            ['category' => 'Zakelijk', 'word' => 'PROGNOSE'],
            ['category' => 'Zakelijk', 'word' => 'CONTRACT'],
            ['category' => 'Zakelijk', 'word' => 'PITCH'],
            ['category' => 'Zakelijk', 'word' => 'PRESENTATIE'],
            ['category' => 'Zakelijk', 'word' => 'KLANT'],
            ['category' => 'Zakelijk', 'word' => 'LEVERANCIER'],
            ['category' => 'Zakelijk', 'word' => 'LOGISTIEK'],
            ['category' => 'Zakelijk', 'word' => 'PERSONEEL'],
            ['category' => 'Zakelijk', 'word' => 'WERVING'],
            ['category' => 'Zakelijk', 'word' => 'BOEKHOUDING'],
            ['category' => 'Zakelijk', 'word' => 'FINANCIEN'],
            ['category' => 'Zakelijk', 'word' => 'AUDIT'],
            ['category' => 'Zakelijk', 'word' => 'BELASTING'],
            ['category' => 'Zakelijk', 'word' => 'WAARDERING'],
            ['category' => 'Zakelijk', 'word' => 'AANDELEN'],
            ['category' => 'Zakelijk', 'word' => 'DIVIDEND'],
            ['category' => 'Zakelijk', 'word' => 'FUSIE'],
            ['category' => 'Zakelijk', 'word' => 'OVERNAME'],
            ['category' => 'Zakelijk', 'word' => 'PARTNERSCHAP'],
            ['category' => 'Zakelijk', 'word' => 'NETWERK'],
            ['category' => 'Zakelijk', 'word' => 'CRM'],
            ['category' => 'Zakelijk', 'word' => 'KPI'],
            ['category' => 'Zakelijk', 'word' => 'ROUTEKAART'],
            ['category' => 'Zakelijk', 'word' => 'PRODUCT'],
            ['category' => 'Zakelijk', 'word' => 'ABONNEMENT'],
            ['category' => 'Zakelijk', 'word' => 'RETENTIE'],
            ['category' => 'Zakelijk', 'word' => 'GROEI'],
            ['category' => 'Zakelijk', 'word' => 'SCHALING'],
            ['category' => 'Zakelijk', 'word' => 'VERGADERING'],
            ['category' => 'Zakelijk', 'word' => 'DEADLINE'],
            ['category' => 'Zakelijk', 'word' => 'RAPPORT'],
            ['category' => 'Zakelijk', 'word' => 'DOELSTELLING'],
            ['category' => 'Zakelijk', 'word' => 'RESULTAAT'],
            ['category' => 'Zakelijk', 'word' => 'INKOOP'],
            ['category' => 'Zakelijk', 'word' => 'AANBIEDING'],
            ['category' => 'Zakelijk', 'word' => 'SAMENWERKING'],
            ['category' => 'Zakelijk', 'word' => 'MARKTAANDEEL'],
            ['category' => 'Zakelijk', 'word' => 'SUBSIDIE'],
            ['category' => 'Zakelijk', 'word' => 'VERKOOPTEAM'],
            ['category' => 'Zakelijk', 'word' => 'CONCURRENTIE'],
            ['category' => 'Zakelijk', 'word' => 'COMMISSIE'],

            // =========================
            // Vermaak (50)
            // =========================
            ['category' => 'Vermaak', 'word' => 'FILM'],
            ['category' => 'Vermaak', 'word' => 'MUZIEK'],
            ['category' => 'Vermaak', 'word' => 'CONCERT'],
            ['category' => 'Vermaak', 'word' => 'FESTIVAL'],
            ['category' => 'Vermaak', 'word' => 'THEATER'],
            ['category' => 'Vermaak', 'word' => 'KOMEDIE'],
            ['category' => 'Vermaak', 'word' => 'DRAMA'],
            ['category' => 'Vermaak', 'word' => 'ACTIE'],
            ['category' => 'Vermaak', 'word' => 'THRILLER'],
            ['category' => 'Vermaak', 'word' => 'ROMANTIEK'],
            ['category' => 'Vermaak', 'word' => 'ANIMATIE'],
            ['category' => 'Vermaak', 'word' => 'DOCUMENTAIRE'],
            ['category' => 'Vermaak', 'word' => 'SERIE'],
            ['category' => 'Vermaak', 'word' => 'AFLEVERING'],
            ['category' => 'Vermaak', 'word' => 'SEIZOEN'],
            ['category' => 'Vermaak', 'word' => 'TRAILER'],
            ['category' => 'Vermaak', 'word' => 'SOUNDTRACK'],
            ['category' => 'Vermaak', 'word' => 'ALBUM'],
            ['category' => 'Vermaak', 'word' => 'SINGLE'],
            ['category' => 'Vermaak', 'word' => 'AFSPEELLIJST'],
            ['category' => 'Vermaak', 'word' => 'DISCJOCKEY'],
            ['category' => 'Vermaak', 'word' => 'DANSVLOER'],
            ['category' => 'Vermaak', 'word' => 'CLUB'],
            ['category' => 'Vermaak', 'word' => 'PODIUM'],
            ['category' => 'Vermaak', 'word' => 'SCHIJNWERPER'],
            ['category' => 'Vermaak', 'word' => 'BACKSTAGE'],
            ['category' => 'Vermaak', 'word' => 'PUBLIEK'],
            ['category' => 'Vermaak', 'word' => 'APPLAUS'],
            ['category' => 'Vermaak', 'word' => 'TOEGIFT'],
            ['category' => 'Vermaak', 'word' => 'BIOSCOOP'],
            ['category' => 'Vermaak', 'word' => 'POP'],
            ['category' => 'Vermaak', 'word' => 'ROCK'],
            ['category' => 'Vermaak', 'word' => 'JAZZ'],
            ['category' => 'Vermaak', 'word' => 'HIPHOP'],
            ['category' => 'Vermaak', 'word' => 'KLASSIEK'],
            ['category' => 'Vermaak', 'word' => 'OPERA'],
            ['category' => 'Vermaak', 'word' => 'BALLET'],
            ['category' => 'Vermaak', 'word' => 'CIRCUS'],
            ['category' => 'Vermaak', 'word' => 'MAGIE'],
            ['category' => 'Vermaak', 'word' => 'PUZZEL'],
            ['category' => 'Vermaak', 'word' => 'GAMEN'],
            ['category' => 'Vermaak', 'word' => 'ARCADE'],
            ['category' => 'Vermaak', 'word' => 'STREAMEN'],
            ['category' => 'Vermaak', 'word' => 'PODCAST'],
            ['category' => 'Vermaak', 'word' => 'RADIO'],
            ['category' => 'Vermaak', 'word' => 'KARAOKE'],
            ['category' => 'Vermaak', 'word' => 'COSPLAY'],
            ['category' => 'Vermaak', 'word' => 'STRIPBOEK'],
            ['category' => 'Vermaak', 'word' => 'ROMAN'],
            ['category' => 'Vermaak', 'word' => 'POEZIE'],

            // =========================
            // Reizen (50)
            // =========================
            ['category' => 'Reizen', 'word' => 'PASPOORT'],
            ['category' => 'Reizen', 'word' => 'BAGAGE'],
            ['category' => 'Reizen', 'word' => 'REISPLAN'],
            ['category' => 'Reizen', 'word' => 'HOTEL'],
            ['category' => 'Reizen', 'word' => 'HOSTEL'],
            ['category' => 'Reizen', 'word' => 'RESORT'],
            ['category' => 'Reizen', 'word' => 'BOEKING'],
            ['category' => 'Reizen', 'word' => 'INCHECKEN'],
            ['category' => 'Reizen', 'word' => 'VERTREK'],
            ['category' => 'Reizen', 'word' => 'AANKOMST'],
            ['category' => 'Reizen', 'word' => 'VLUCHT'],
            ['category' => 'Reizen', 'word' => 'TREIN'],
            ['category' => 'Reizen', 'word' => 'METRO'],
            ['category' => 'Reizen', 'word' => 'TICKET'],
            ['category' => 'Reizen', 'word' => 'KAART'],
            ['category' => 'Reizen', 'word' => 'GIDS'],
            ['category' => 'Reizen', 'word' => 'RONDLEIDING'],
            ['category' => 'Reizen', 'word' => 'EXCURSIE'],
            ['category' => 'Reizen', 'word' => 'RUGZAK'],
            ['category' => 'Reizen', 'word' => 'KOFFER'],
            ['category' => 'Reizen', 'word' => 'VISUM'],
            ['category' => 'Reizen', 'word' => 'DOUANE'],
            ['category' => 'Reizen', 'word' => 'VALUTA'],
            ['category' => 'Reizen', 'word' => 'WISSELKOERS'],
            ['category' => 'Reizen', 'word' => 'SOUVENIR'],
            ['category' => 'Reizen', 'word' => 'STRAND'],
            ['category' => 'Reizen', 'word' => 'MUSEUM'],
            ['category' => 'Reizen', 'word' => 'TEMPEL'],
            ['category' => 'Reizen', 'word' => 'KASTEEL'],
            ['category' => 'Reizen', 'word' => 'BRUG'],
            ['category' => 'Reizen', 'word' => 'MARKT'],
            ['category' => 'Reizen', 'word' => 'HAVEN'],
            ['category' => 'Reizen', 'word' => 'LUCHTHAVEN'],
            ['category' => 'Reizen', 'word' => 'STATION'],
            ['category' => 'Reizen', 'word' => 'VERHUUR'],
            ['category' => 'Reizen', 'word' => 'CARAVAN'],
            ['category' => 'Reizen', 'word' => 'KAMPEREN'],
            ['category' => 'Reizen', 'word' => 'TREKKEN'],
            ['category' => 'Reizen', 'word' => 'SAFARI'],
            ['category' => 'Reizen', 'word' => 'CRUISE'],
            ['category' => 'Reizen', 'word' => 'ZONNEBRAND'],
            ['category' => 'Reizen', 'word' => 'SNORKELEN'],
            ['category' => 'Reizen', 'word' => 'WANDELPAD'],
            ['category' => 'Reizen', 'word' => 'UITKIJKPUNT'],
            ['category' => 'Reizen', 'word' => 'UITCHECKEN'],
            ['category' => 'Reizen', 'word' => 'TUSSENSTOP'],
            ['category' => 'Reizen', 'word' => 'JETLAG'],
            ['category' => 'Reizen', 'word' => 'ROADTRIP'],
            ['category' => 'Reizen', 'word' => 'VAKANTIE'],
            ['category' => 'Reizen', 'word' => 'AVONTUUR'],

            // =========================
            // Gezondheid (50)
            // =========================
            ['category' => 'Gezondheid', 'word' => 'WELZIJN'],
            ['category' => 'Gezondheid', 'word' => 'FITNESS'],
            ['category' => 'Gezondheid', 'word' => 'VOEDING'],
            ['category' => 'Gezondheid', 'word' => 'EIWIT'],
            ['category' => 'Gezondheid', 'word' => 'VITAMINE'],
            ['category' => 'Gezondheid', 'word' => 'MINERAAL'],
            ['category' => 'Gezondheid', 'word' => 'HYDRATATIE'],
            ['category' => 'Gezondheid', 'word' => 'SLAAP'],
            ['category' => 'Gezondheid', 'word' => 'HERSTEL'],
            ['category' => 'Gezondheid', 'word' => 'STRETCHEN'],
            ['category' => 'Gezondheid', 'word' => 'MEDICIJN'],
            ['category' => 'Gezondheid', 'word' => 'THERAPIE'],
            ['category' => 'Gezondheid', 'word' => 'KLINIEK'],
            ['category' => 'Gezondheid', 'word' => 'ZIEKENHUIS'],
            ['category' => 'Gezondheid', 'word' => 'DOKTER'],
            ['category' => 'Gezondheid', 'word' => 'VERPLEEGSTER'],
            ['category' => 'Gezondheid', 'word' => 'VACCIN'],
            ['category' => 'Gezondheid', 'word' => 'IMMUNITEIT'],
            ['category' => 'Gezondheid', 'word' => 'CARDIO'],
            ['category' => 'Gezondheid', 'word' => 'SPIER'],
            ['category' => 'Gezondheid', 'word' => 'HOUDING'],
            ['category' => 'Gezondheid', 'word' => 'ADEMHALING'],
            ['category' => 'Gezondheid', 'word' => 'MEDITATIE'],
            ['category' => 'Gezondheid', 'word' => 'BALANS'],
            ['category' => 'Gezondheid', 'word' => 'STRESS'],
            ['category' => 'Gezondheid', 'word' => 'ANGST'],
            ['category' => 'Gezondheid', 'word' => 'ONTSPANNEN'],
            ['category' => 'Gezondheid', 'word' => 'MASSAGE'],
            ['category' => 'Gezondheid', 'word' => 'REVALIDATIE'],
            ['category' => 'Gezondheid', 'word' => 'BLESSURE'],
            ['category' => 'Gezondheid', 'word' => 'GENEZING'],
            ['category' => 'Gezondheid', 'word' => 'SYMPTOOM'],
            ['category' => 'Gezondheid', 'word' => 'DIAGNOSE'],
            ['category' => 'Gezondheid', 'word' => 'CONTROLE'],
            ['category' => 'Gezondheid', 'word' => 'POLS'],
            ['category' => 'Gezondheid', 'word' => 'BLOEDDRUK'],
            ['category' => 'Gezondheid', 'word' => 'HARTSLAG'],
            ['category' => 'Gezondheid', 'word' => 'CALORIEEN'],
            ['category' => 'Gezondheid', 'word' => 'METABOLISME'],
            ['category' => 'Gezondheid', 'word' => 'ALLERGIE'],
            ['category' => 'Gezondheid', 'word' => 'TANDARTS'],
            ['category' => 'Gezondheid', 'word' => 'VISIE'],
            ['category' => 'Gezondheid', 'word' => 'GEHOOR'],
            ['category' => 'Gezondheid', 'word' => 'YOGA'],
            ['category' => 'Gezondheid', 'word' => 'PILATES'],
            ['category' => 'Gezondheid', 'word' => 'WEERBAARHEID'],
            ['category' => 'Gezondheid', 'word' => 'BEWUSTZIJN'],
            ['category' => 'Gezondheid', 'word' => 'CONDITIE'],
            ['category' => 'Gezondheid', 'word' => 'PREVENTIE'],
            ['category' => 'Gezondheid', 'word' => 'VERZORGING'],

            // =========================
            // Kunst (50)
            // =========================
            ['category' => 'Kunst', 'word' => 'SCHILDERIJ'],
            ['category' => 'Kunst', 'word' => 'TEKENING'],
            ['category' => 'Kunst', 'word' => 'SCULPTUUR'],
            ['category' => 'Kunst', 'word' => 'SCHETS'],
            ['category' => 'Kunst', 'word' => 'CANVAS'],
            ['category' => 'Kunst', 'word' => 'PORTRET'],
            ['category' => 'Kunst', 'word' => 'LANDSCHAP'],
            ['category' => 'Kunst', 'word' => 'ABSTRACT'],
            ['category' => 'Kunst', 'word' => 'GRAFFITI'],
            ['category' => 'Kunst', 'word' => 'FOTOGRAFIE'],
            ['category' => 'Kunst', 'word' => 'FILM'],
            ['category' => 'Kunst', 'word' => 'ONTWERP'],
            ['category' => 'Kunst', 'word' => 'ILLUSTRATIE'],
            ['category' => 'Kunst', 'word' => 'TYPOGRAFIE'],
            ['category' => 'Kunst', 'word' => 'KALLIGRAFIE'],
            ['category' => 'Kunst', 'word' => 'KERAMIEK'],
            ['category' => 'Kunst', 'word' => 'POTTENBAKKEN'],
            ['category' => 'Kunst', 'word' => 'WEVEN'],
            ['category' => 'Kunst', 'word' => 'TEXTIEL'],
            ['category' => 'Kunst', 'word' => 'COLLAGE'],
            ['category' => 'Kunst', 'word' => 'DRUKWERK'],
            ['category' => 'Kunst', 'word' => 'ETSING'],
            ['category' => 'Kunst', 'word' => 'LITHOGRAFIE'],
            ['category' => 'Kunst', 'word' => 'AQUAREL'],
            ['category' => 'Kunst', 'word' => 'OLIEVERF'],
            ['category' => 'Kunst', 'word' => 'ACRYL'],
            ['category' => 'Kunst', 'word' => 'HOUTSKOOL'],
            ['category' => 'Kunst', 'word' => 'PASTEL'],
            ['category' => 'Kunst', 'word' => 'INKT'],
            ['category' => 'Kunst', 'word' => 'MODEL'],
            ['category' => 'Kunst', 'word' => 'ARCHITECTUUR'],
            ['category' => 'Kunst', 'word' => 'MODE'],
            ['category' => 'Kunst', 'word' => 'SIERADEN'],
            ['category' => 'Kunst', 'word' => 'MOZAIEK'],
            ['category' => 'Kunst', 'word' => 'GLASWERK'],
            ['category' => 'Kunst', 'word' => 'HOUTWERK'],
            ['category' => 'Kunst', 'word' => 'SNIJWERK'],
            ['category' => 'Kunst', 'word' => 'ORIGAMI'],
            ['category' => 'Kunst', 'word' => 'ANIMATIE'],
            ['category' => 'Kunst', 'word' => 'STORYBOARD'],
            ['category' => 'Kunst', 'word' => 'GALERIE'],
            ['category' => 'Kunst', 'word' => 'CURATOR'],
            ['category' => 'Kunst', 'word' => 'MEESTERWERK'],
            ['category' => 'Kunst', 'word' => 'ESTHETIEK'],
            ['category' => 'Kunst', 'word' => 'KLEURENPALET'],
            ['category' => 'Kunst', 'word' => 'COMPOSITIE'],
            ['category' => 'Kunst', 'word' => 'SYMMETRIE'],
            ['category' => 'Kunst', 'word' => 'PERSPECTIEF'],
            ['category' => 'Kunst', 'word' => 'VERLICHTING'],
            ['category' => 'Kunst', 'word' => 'TENTOONSTELLING'],

        ];
    }

    private function dailyPuzzle(Carbon $date): array
    {
        $pool = array_values(array_filter($this->pool(), function ($e) {
            $w = strtoupper((string)($e['word'] ?? ''));
            $len = strlen($w);
            return $len >= self::MIN_LEN && $len <= self::MAX_LEN;
        }));

        $byCategory = [];
        foreach ($pool as $entry) {
            $cat = (string) $entry['category'];
            $byCategory[$cat] ??= [];
            $byCategory[$cat][] = strtoupper((string) $entry['word']);
        }

        $categories = array_values(array_keys($byCategory));
        sort($categories);

        $seed = crc32(self::GAME_KEY . '|' . $date->toDateString());
        $catIndex = $seed % max(1, count($categories));
        $category = $categories[$catIndex];

        $words = $byCategory[$category];
        $seed2 = crc32('W|' . self::GAME_KEY . '|' . $date->toDateString());
        $wordIndex = $seed2 % max(1, count($words));
        $word = $words[$wordIndex];

        $number = 100 + ($seed % 900);

        return [
            'number'   => $number,
            'date'     => $date->toDateString(),
            'category' => $category,
            'word'     => $word,
            'length'   => strlen($word),
            'first'    => substr($word, 0, 1),
        ];
    }

    private function initialState(array $puzzle): array
    {
        $pattern = $puzzle['first'] . str_repeat('_', $puzzle['length'] - 1);

        return [
            'pattern'      => $pattern,
            'attempts'     => [],
            'max_attempts' => self::MAX_ATTEMPTS,
            'length'       => $puzzle['length'],
            'first'        => $puzzle['first'],
            'started_ms'   => now()->getTimestampMs(),
        ];
    }

    private function wantsJson(Request $request): bool
    {
        return $request->expectsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';
    }

    private function mmss(?int $ms): ?string
    {
        if ($ms === null) return null;

        $sec = (int) round($ms / 1000);
        $mm = str_pad((string) floor($sec / 60), 2, '0', STR_PAD_LEFT);
        $ss = str_pad((string) ($sec % 60), 2, '0', STR_PAD_LEFT);

        return $mm . ':' . $ss;
    }

    private function leaderboardPayload(Request $request, Carbon $today, $user, ?int $myDurationMs): array
    {
        $scope = (string) $request->query('scope', 'global');
        if (!in_array($scope, ['global', 'friends'], true)) $scope = 'global';

        $tabs = [
            ['key' => 'global',  'label' => 'Wereldwijd', 'icon' => 'fa-solid fa-globe'],
            ['key' => 'friends', 'label' => 'Vrienden',   'icon' => 'fa-solid fa-user-group'],
        ];

        $lbQuery = DailyGameRun::query()
            ->where('game_key', self::GAME_KEY)
            ->where('puzzle_date', $today->toDateString())
            ->where('solved', true)
            ->whereNotNull('duration_ms');

        if ($scope === 'friends') {
            if (method_exists($user, 'friends')) {
                $friendIds = $user->friends()->pluck('users.id')->all();
                $ids = array_values(array_unique(array_merge([$user->id], $friendIds)));
                $lbQuery->whereIn('user_id', $ids);
            } else {
                $lbQuery->where('user_id', $user->id);
            }
        }

        $topTimes = (clone $lbQuery)
            ->with(['user:id,name,profile_picture,plan,level,xp'])
            ->orderBy('duration_ms')
            ->orderBy('finished_at')
            ->limit(10)
            ->get();

        $myRank = null;
        if ($myDurationMs !== null) {
            $myRank = (clone $lbQuery)->where('duration_ms', '<', $myDurationMs)->count() + 1;
        }

        $rows = $topTimes->map(function ($r) {
            $u = $r->user;

            return [
                'duration_ms' => (int) $r->duration_ms,
                'time' => $this->mmss((int) $r->duration_ms),
                'user' => [
                    'id' => (int) $u->id,
                    'name' => (string) $u->name,
                    'level' => (int) ($u->level ?? 1),
                    'plan' => (string) ($u->plan ?? ''),
                    'profile_picture_url' => !empty($u->profile_picture) ? asset('storage/' . $u->profile_picture) : null,
                ],
            ];
        })->values()->all();

        return [
            'scope' => $scope,
            'tabs' => $tabs,
            'rows' => $rows,
            'my_rank' => $myRank,
        ];
    }

    public function show(Request $request)
    {
        $user = $request->user();
        $today = now()->startOfDay();
        $puzzle = $this->dailyPuzzle($today);

        $run = DailyGameRun::firstOrCreate(
            [
                'user_id'     => $user->id,
                'game_key'    => self::GAME_KEY,
                'puzzle_date' => $today->toDateString(),
            ],
            [
                'started_at' => now(),
                'state'      => null,
                'attempts'   => 0,
                'solved'     => false,
            ]
        );

        $state = $run->state ?: $this->initialState($puzzle);

        if (!isset($state['started_ms'])) {
            $state['started_ms'] = $run->started_at?->getTimestampMs() ?? now()->getTimestampMs();
            $run->state = $state;
            $run->save();
        }

        if (!$run->state) {
            $run->state = $state;
            $run->attempts = 0;
            $run->save();
        }

        $attemptsUsed = count($state['attempts'] ?? []);
        $attemptsLeft = max(0, (int)($state['max_attempts'] ?? self::MAX_ATTEMPTS) - $attemptsUsed);

        $lb = $this->leaderboardPayload($request, $today, $user, $run->solved ? (int) $run->duration_ms : null);

        $streak = app(\App\Services\DailyGameStreakService::class)
            ->uiPayload($user, self::GAME_KEY, $today);

        return view('games.word-forge', [
            'user'         => $user,
            'puzzle'       => $puzzle,
            'run'          => $run,
            'state'        => $state,
            'attemptsUsed' => $attemptsUsed,
            'attemptsLeft' => $attemptsLeft,

            'scope' => $lb['scope'],
            'tabs' => $lb['tabs'],
            'topTimes' => collect($lb['rows']),
            'myRank' => $lb['my_rank'],

            'streak' => $streak,
        ]);
    }

    public function guess(Request $request)
    {
        $user = $request->user();
        $today = now()->startOfDay();
        $puzzle = $this->dailyPuzzle($today);

        $run = DailyGameRun::where('user_id', $user->id)
            ->where('game_key', self::GAME_KEY)
            ->where('puzzle_date', $today->toDateString())
            ->firstOrFail();

        // Already solved/failed
        if ($run->solved || ($run->finished_at && !$run->solved)) {
            if ($this->wantsJson($request)) {
                $state = $run->state ?: $this->initialState($puzzle);
                $attemptsUsed = count($state['attempts'] ?? []);
                $attemptsLeft = max(0, self::MAX_ATTEMPTS - $attemptsUsed);
                $failed = (!$run->solved && $run->finished_at && $attemptsLeft <= 0);

                $payload = [
                    'ok' => true,
                    'pattern' => (string)($state['pattern'] ?? ''),
                    'attempts' => (array)($state['attempts'] ?? []),
                    'attemptsUsed' => $attemptsUsed,
                    'attemptsLeft' => $attemptsLeft,
                    'solved' => (bool)$run->solved,
                    'failed' => (bool)$failed,
                    'final_time' => $this->mmss($run->duration_ms),
                    'answer' => $run->solved ? $puzzle['word'] : ($failed ? $puzzle['word'] : null),
                ];

                // if solved already, include leaderboard too (no reload)
                if ($run->solved && $run->duration_ms !== null) {
                    $lb = $this->leaderboardPayload($request, $today, $user, (int) $run->duration_ms);
                    $payload['leaderboard'] = $lb;
                }

                $payload['streak'] = app(\App\Services\DailyGameStreakService::class)
                    ->uiPayload($user, self::GAME_KEY, $today);

                return response()->json($payload);
            }
            return back();
        }

        $state = $run->state ?: $this->initialState($puzzle);

        $attemptsUsed = count($state['attempts'] ?? []);
        if ($attemptsUsed >= (int)($state['max_attempts'] ?? self::MAX_ATTEMPTS)) {
            $run->finished_at = now();
            $run->duration_ms = null;
            $run->solved = false;
            $run->save();

            if ($this->wantsJson($request)) {
                return response()->json([
                    'ok' => true,
                    'pattern' => (string)($state['pattern'] ?? ''),
                    'attempts' => (array)($state['attempts'] ?? []),
                    'attemptsUsed' => $attemptsUsed,
                    'attemptsLeft' => 0,
                    'solved' => false,
                    'failed' => true,
                    'final_time' => null,
                    'answer' => $puzzle['word'],
                    'streak' => app(\App\Services\DailyGameStreakService::class)->uiPayload($user, self::GAME_KEY, $today),
                ]);
            }

            return back();
        }

        $guess = strtoupper(trim((string) $request->input('guess', '')));
        $guess = preg_replace('/\s+/', '', $guess);

        $fail422 = function (string $msg) use ($request) {
            if ($this->wantsJson($request)) {
                return response()->json([
                    'message' => 'Validation error',
                    'errors' => ['guess' => [$msg]],
                ], 422);
            }
            return back()->withErrors(['guess' => $msg]);
        };

        if ($guess === '' || !preg_match('/^[A-Z]+$/', $guess)) {
            return $fail422('Use letters A–Z only.');
        }

        if (strlen($guess) !== (int)$puzzle['length']) {
            return $fail422('Your guess must be exactly ' . $puzzle['length'] . ' letters.');
        }

        if (substr($guess, 0, 1) !== $puzzle['first']) {
            return $fail422('Your guess must start with "' . $puzzle['first'] . '".');
        }

        $answer = $puzzle['word'];

        $mask = [];
        for ($i = 0; $i < $puzzle['length']; $i++) {
            $mask[$i] = ($guess[$i] === $answer[$i]) ? 1 : 0;
        }

        $pattern = str_split((string)($state['pattern'] ?? ($puzzle['first'] . str_repeat('_', $puzzle['length'] - 1))));
        for ($i = 0; $i < $puzzle['length']; $i++) {
            if ($mask[$i] === 1) $pattern[$i] = $answer[$i];
        }

        $state['pattern'] = implode('', $pattern);
        $state['attempts'][] = [
            'guess' => $guess,
            'mask'  => $mask,
        ];

        $attemptsUsed++;
        $justSolved = ($guess === $answer);

        $justFinishedToday = false;

        $run->state = $state;
        $run->attempts = $attemptsUsed;

        if ($justSolved) {
            $run->solved = true;
            $run->finished_at = now();

            $nowMs = now()->getTimestampMs();
            $startedMs = (int)($state['started_ms'] ?? ($run->started_at?->getTimestampMs() ?? $nowMs));
            $run->duration_ms = max(0, $nowMs - $startedMs);

            // reward logic
            $todayStr = now()->toDateString();
            if (!$user->daily_challenges_date || $user->daily_challenges_date->toDateString() !== $todayStr) {
                $user->daily_challenges_done = 0;
                $user->daily_challenges_date = $todayStr;
            }

            $limit = $user->plan === 'pro' ? null : 5;
            $canReward = is_null($limit) || ((int)$user->daily_challenges_done < (int)$limit);

            if ($canReward) {
                $user->daily_challenges_done = (int)$user->daily_challenges_done + 1;
                $user->addXp(150);
            } else {
                $user->save();
            }

            $justFinishedToday = true;
        } else {
            $max = (int)($state['max_attempts'] ?? self::MAX_ATTEMPTS);
            if ($attemptsUsed >= $max) {
                $run->finished_at = now();
                $run->duration_ms = null;
                $run->solved = false;

                $justFinishedToday = true; // ✅ game klaar (failed)
            }
        }

        $run->save();

        // ✅ STAP 4.4: streak registreren zodra de game klaar is (solved OF failed)
        if ($justFinishedToday) {
            app(\App\Services\DailyGameStreakService::class)
                ->recordSolved($user, self::GAME_KEY, $today);
        }

        $attemptsLeft = max(0, self::MAX_ATTEMPTS - $attemptsUsed);
        $failed = (!$run->solved && $run->finished_at && $attemptsLeft <= 0);

        if ($this->wantsJson($request)) {
            $payload = [
                'ok' => true,
                'pattern' => (string)($state['pattern'] ?? ''),
                'attempts' => (array)($state['attempts'] ?? []),
                'attemptsUsed' => $attemptsUsed,
                'attemptsLeft' => $attemptsLeft,
                'solved' => (bool)$run->solved,
                'failed' => (bool)$failed,
                'final_time' => $this->mmss($run->duration_ms),
                'answer' => $run->solved ? $puzzle['word'] : ($failed ? $puzzle['word'] : null),
            ];

            // ✅ if solved, return live leaderboard too (no reload)
            if ($run->solved && $run->duration_ms !== null) {
                $lb = $this->leaderboardPayload($request, $today, $user, (int) $run->duration_ms);
                $payload['leaderboard'] = $lb;
            }

            // ✅ ADD: altijd meest recente streak meegeven (na eventuele recordSolved)
            $payload['streak'] = app(\App\Services\DailyGameStreakService::class)
                ->uiPayload($user, self::GAME_KEY, $today);

            return response()->json($payload);
        }

        return back();
    }
}