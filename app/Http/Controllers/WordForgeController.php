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
            // Tech (50)
            // =========================
            ['category' => 'Tech', 'word' => 'API'],
            ['category' => 'Tech', 'word' => 'CACHE'],
            ['category' => 'Tech', 'word' => 'FRAMEWORK'],
            ['category' => 'Tech', 'word' => 'APPLICATION'],
            ['category' => 'Tech', 'word' => 'SERVER'],
            ['category' => 'Tech', 'word' => 'DATABASE'],
            ['category' => 'Tech', 'word' => 'JAVASCRIPT'],
            ['category' => 'Tech', 'word' => 'LARAVEL'],
            ['category' => 'Tech', 'word' => 'ALGORITHM'],
            ['category' => 'Tech', 'word' => 'FUNCTION'],
            ['category' => 'Tech', 'word' => 'PACKAGE'],
            ['category' => 'Tech', 'word' => 'LIBRARY'],
            ['category' => 'Tech', 'word' => 'COMPILER'],
            ['category' => 'Tech', 'word' => 'VARIABLE'],
            ['category' => 'Tech', 'word' => 'INTERFACE'],
            ['category' => 'Tech', 'word' => 'ENDPOINT'],
            ['category' => 'Tech', 'word' => 'AUTH'],
            ['category' => 'Tech', 'word' => 'ENCRYPTION'],
            ['category' => 'Tech', 'word' => 'FIREWALL'],
            ['category' => 'Tech', 'word' => 'PROTOCOL'],
            ['category' => 'Tech', 'word' => 'VIRTUAL'],
            ['category' => 'Tech', 'word' => 'CONTAINER'],
            ['category' => 'Tech', 'word' => 'DOCKER'],
            ['category' => 'Tech', 'word' => 'KUBERNETES'],
            ['category' => 'Tech', 'word' => 'GITHUB'],
            ['category' => 'Tech', 'word' => 'DEPLOY'],
            ['category' => 'Tech', 'word' => 'PIPELINE'],
            ['category' => 'Tech', 'word' => 'BUILD'],
            ['category' => 'Tech', 'word' => 'RELEASE'],
            ['category' => 'Tech', 'word' => 'BACKEND'],
            ['category' => 'Tech', 'word' => 'FRONTEND'],
            ['category' => 'Tech', 'word' => 'BROWSER'],
            ['category' => 'Tech', 'word' => 'COOKIE'],
            ['category' => 'Tech', 'word' => 'SESSION'],
            ['category' => 'Tech', 'word' => 'TOKEN'],
            ['category' => 'Tech', 'word' => 'OAUTH'],
            ['category' => 'Tech', 'word' => 'WEBHOOK'],
            ['category' => 'Tech', 'word' => 'MONITORING'],
            ['category' => 'Tech', 'word' => 'LOGGING'],
            ['category' => 'Tech', 'word' => 'LATENCY'],
            ['category' => 'Tech', 'word' => 'BANDWIDTH'],
            ['category' => 'Tech', 'word' => 'SCALABILITY'],
            ['category' => 'Tech', 'word' => 'MICROSERVICE'],
            ['category' => 'Tech', 'word' => 'REPOSITORY'],
            ['category' => 'Tech', 'word' => 'BRANCH'],
            ['category' => 'Tech', 'word' => 'MERGE'],
            ['category' => 'Tech', 'word' => 'COMMIT'],
            ['category' => 'Tech', 'word' => 'PULLREQUEST'],
            ['category' => 'Tech', 'word' => 'DEBUGGER'],
            ['category' => 'Tech', 'word' => 'TERMINAL'],

            // =========================
            // Cities (50)
            // =========================
            ['category' => 'Cities', 'word' => 'ROME'],
            ['category' => 'Cities', 'word' => 'AMSTERDAM'],
            ['category' => 'Cities', 'word' => 'COPENHAGEN'],
            ['category' => 'Cities', 'word' => 'UTRECHT'],
            ['category' => 'Cities', 'word' => 'PARIS'],
            ['category' => 'Cities', 'word' => 'BERLIN'],
            ['category' => 'Cities', 'word' => 'MADRID'],
            ['category' => 'Cities', 'word' => 'LONDON'],
            ['category' => 'Cities', 'word' => 'DUBAI'],
            ['category' => 'Cities', 'word' => 'VIENNA'],
            ['category' => 'Cities', 'word' => 'PRAGUE'],
            ['category' => 'Cities', 'word' => 'BUDAPEST'],
            ['category' => 'Cities', 'word' => 'WARSAW'],
            ['category' => 'Cities', 'word' => 'LISBON'],
            ['category' => 'Cities', 'word' => 'OSLO'],
            ['category' => 'Cities', 'word' => 'STOCKHOLM'],
            ['category' => 'Cities', 'word' => 'HELSINKI'],
            ['category' => 'Cities', 'word' => 'ATHENS'],
            ['category' => 'Cities', 'word' => 'SOFIA'],
            ['category' => 'Cities', 'word' => 'ZAGREB'],
            ['category' => 'Cities', 'word' => 'BRUSSELS'],
            ['category' => 'Cities', 'word' => 'ANTWERP'],
            ['category' => 'Cities', 'word' => 'ROTTERDAM'],
            ['category' => 'Cities', 'word' => 'THEHAGUE'],
            ['category' => 'Cities', 'word' => 'MUNICH'],
            ['category' => 'Cities', 'word' => 'HAMBURG'],
            ['category' => 'Cities', 'word' => 'COLOGNE'],
            ['category' => 'Cities', 'word' => 'FRANKFURT'],
            ['category' => 'Cities', 'word' => 'MILAN'],
            ['category' => 'Cities', 'word' => 'VENICE'],
            ['category' => 'Cities', 'word' => 'FLORENCE'],
            ['category' => 'Cities', 'word' => 'NAPLES'],
            ['category' => 'Cities', 'word' => 'TORINO'],
            ['category' => 'Cities', 'word' => 'BARCELONA'],
            ['category' => 'Cities', 'word' => 'VALENCIA'],
            ['category' => 'Cities', 'word' => 'SEVILLE'],
            ['category' => 'Cities', 'word' => 'MARSEILLE'],
            ['category' => 'Cities', 'word' => 'LYON'],
            ['category' => 'Cities', 'word' => 'NICE'],
            ['category' => 'Cities', 'word' => 'GENEVA'],
            ['category' => 'Cities', 'word' => 'ZURICH'],
            ['category' => 'Cities', 'word' => 'BASEL'],
            ['category' => 'Cities', 'word' => 'DUBLIN'],
            ['category' => 'Cities', 'word' => 'EDINBURGH'],
            ['category' => 'Cities', 'word' => 'GLASGOW'],
            ['category' => 'Cities', 'word' => 'NEWYORK'],
            ['category' => 'Cities', 'word' => 'CHICAGO'],
            ['category' => 'Cities', 'word' => 'TORONTO'],
            ['category' => 'Cities', 'word' => 'VANCOUVER'],
            ['category' => 'Cities', 'word' => 'SYDNEY'],

            // =========================
            // Food (50)
            // =========================
            ['category' => 'Food', 'word' => 'TEA'],
            ['category' => 'Food', 'word' => 'SUSHI'],
            ['category' => 'Food', 'word' => 'CHOCOLATE'],
            ['category' => 'Food', 'word' => 'PASTA'],
            ['category' => 'Food', 'word' => 'BURGER'],
            ['category' => 'Food', 'word' => 'PIZZA'],
            ['category' => 'Food', 'word' => 'CHEESE'],
            ['category' => 'Food', 'word' => 'SALMON'],
            ['category' => 'Food', 'word' => 'PANCAKE'],
            ['category' => 'Food', 'word' => 'NOODLES'],
            ['category' => 'Food', 'word' => 'CURRY'],
            ['category' => 'Food', 'word' => 'TACO'],
            ['category' => 'Food', 'word' => 'BAGEL'],
            ['category' => 'Food', 'word' => 'MUFFIN'],
            ['category' => 'Food', 'word' => 'YOGURT'],
            ['category' => 'Food', 'word' => 'OMELETTE'],
            ['category' => 'Food', 'word' => 'RISOTTO'],
            ['category' => 'Food', 'word' => 'LASAGNA'],
            ['category' => 'Food', 'word' => 'KIMCHI'],
            ['category' => 'Food', 'word' => 'HUMMUS'],
            ['category' => 'Food', 'word' => 'FALAFEL'],
            ['category' => 'Food', 'word' => 'BROWNIE'],
            ['category' => 'Food', 'word' => 'CROISSANT'],
            ['category' => 'Food', 'word' => 'BISCUIT'],
            ['category' => 'Food', 'word' => 'SAUSAGE'],
            ['category' => 'Food', 'word' => 'STEAK'],
            ['category' => 'Food', 'word' => 'CHICKEN'],
            ['category' => 'Food', 'word' => 'TOFU'],
            ['category' => 'Food', 'word' => 'AVOCADO'],
            ['category' => 'Food', 'word' => 'BANANA'],
            ['category' => 'Food', 'word' => 'ORANGE'],
            ['category' => 'Food', 'word' => 'MANGO'],
            ['category' => 'Food', 'word' => 'PINEAPPLE'],
            ['category' => 'Food', 'word' => 'STRAWBERRY'],
            ['category' => 'Food', 'word' => 'BLUEBERRY'],
            ['category' => 'Food', 'word' => 'WATERMELON'],
            ['category' => 'Food', 'word' => 'CUCUMBER'],
            ['category' => 'Food', 'word' => 'TOMATO'],
            ['category' => 'Food', 'word' => 'CARROT'],
            ['category' => 'Food', 'word' => 'SPINACH'],
            ['category' => 'Food', 'word' => 'BROCCOLI'],
            ['category' => 'Food', 'word' => 'POTATO'],
            ['category' => 'Food', 'word' => 'GARLIC'],
            ['category' => 'Food', 'word' => 'GINGER'],
            ['category' => 'Food', 'word' => 'CINNAMON'],
            ['category' => 'Food', 'word' => 'VANILLA'],
            ['category' => 'Food', 'word' => 'COFFEE'],
            ['category' => 'Food', 'word' => 'MATCHA'],
            ['category' => 'Food', 'word' => 'POPCORN'],
            ['category' => 'Food', 'word' => 'ICECREAM'],

            // =========================
            // Nature (50)
            // =========================
            ['category' => 'Nature', 'word' => 'OAK'],
            ['category' => 'Nature', 'word' => 'MOUNTAIN'],
            ['category' => 'Nature', 'word' => 'RIVER'],
            ['category' => 'Nature', 'word' => 'FOREST'],
            ['category' => 'Nature', 'word' => 'OCEAN'],
            ['category' => 'Nature', 'word' => 'DESERT'],
            ['category' => 'Nature', 'word' => 'FLOWER'],
            ['category' => 'Nature', 'word' => 'THUNDER'],
            ['category' => 'Nature', 'word' => 'VALLEY'],
            ['category' => 'Nature', 'word' => 'GLACIER'],
            ['category' => 'Nature', 'word' => 'VOLCANO'],
            ['category' => 'Nature', 'word' => 'WATERFALL'],
            ['category' => 'Nature', 'word' => 'ISLAND'],
            ['category' => 'Nature', 'word' => 'REEF'],
            ['category' => 'Nature', 'word' => 'CANYON'],
            ['category' => 'Nature', 'word' => 'MEADOW'],
            ['category' => 'Nature', 'word' => 'SAVANNA'],
            ['category' => 'Nature', 'word' => 'JUNGLE'],
            ['category' => 'Nature', 'word' => 'PRAIRIE'],
            ['category' => 'Nature', 'word' => 'TUNDRA'],
            ['category' => 'Nature', 'word' => 'STREAM'],
            ['category' => 'Nature', 'word' => 'LAKE'],
            ['category' => 'Nature', 'word' => 'POND'],
            ['category' => 'Nature', 'word' => 'BAY'],
            ['category' => 'Nature', 'word' => 'COAST'],
            ['category' => 'Nature', 'word' => 'CLIFF'],
            ['category' => 'Nature', 'word' => 'CAVE'],
            ['category' => 'Nature', 'word' => 'GROVE'],
            ['category' => 'Nature', 'word' => 'MOSS'],
            ['category' => 'Nature', 'word' => 'FERN'],
            ['category' => 'Nature', 'word' => 'CEDAR'],
            ['category' => 'Nature', 'word' => 'PINE'],
            ['category' => 'Nature', 'word' => 'MAPLE'],
            ['category' => 'Nature', 'word' => 'BIRCH'],
            ['category' => 'Nature', 'word' => 'WILLOW'],
            ['category' => 'Nature', 'word' => 'IVY'],
            ['category' => 'Nature', 'word' => 'ROSE'],
            ['category' => 'Nature', 'word' => 'LILY'],
            ['category' => 'Nature', 'word' => 'ORCHID'],
            ['category' => 'Nature', 'word' => 'DAISY'],
            ['category' => 'Nature', 'word' => 'BREEZE'],
            ['category' => 'Nature', 'word' => 'STORM'],
            ['category' => 'Nature', 'word' => 'RAINBOW'],
            ['category' => 'Nature', 'word' => 'SUNSET'],
            ['category' => 'Nature', 'word' => 'MOONLIGHT'],
            ['category' => 'Nature', 'word' => 'AVALANCHE'],
            ['category' => 'Nature', 'word' => 'EARTHQUAKE'],
            ['category' => 'Nature', 'word' => 'HURRICANE'],
            ['category' => 'Nature', 'word' => 'MONSOON'],
            ['category' => 'Nature', 'word' => 'AURORA'],

            // =========================
            // Sports (50)
            // =========================
            ['category' => 'Sports', 'word' => 'SOCCER'],
            ['category' => 'Sports', 'word' => 'TENNIS'],
            ['category' => 'Sports', 'word' => 'CRICKET'],
            ['category' => 'Sports', 'word' => 'HOCKEY'],
            ['category' => 'Sports', 'word' => 'BOXING'],
            ['category' => 'Sports', 'word' => 'RACING'],
            ['category' => 'Sports', 'word' => 'CYCLING'],
            ['category' => 'Sports', 'word' => 'SWIMMING'],
            ['category' => 'Sports', 'word' => 'RUNNING'],
            ['category' => 'Sports', 'word' => 'SKIING'],
            ['category' => 'Sports', 'word' => 'SNOWBOARD'],
            ['category' => 'Sports', 'word' => 'BASEBALL'],
            ['category' => 'Sports', 'word' => 'BASKETBALL'],
            ['category' => 'Sports', 'word' => 'VOLLEYBALL'],
            ['category' => 'Sports', 'word' => 'RUGBY'],
            ['category' => 'Sports', 'word' => 'GOLF'],
            ['category' => 'Sports', 'word' => 'BADMINTON'],
            ['category' => 'Sports', 'word' => 'KARATE'],
            ['category' => 'Sports', 'word' => 'JUDO'],
            ['category' => 'Sports', 'word' => 'TAEKWONDO'],
            ['category' => 'Sports', 'word' => 'ROWING'],
            ['category' => 'Sports', 'word' => 'SAILING'],
            ['category' => 'Sports', 'word' => 'SURFING'],
            ['category' => 'Sports', 'word' => 'SKATEBOARD'],
            ['category' => 'Sports', 'word' => 'CLIMBING'],
            ['category' => 'Sports', 'word' => 'ARCHERY'],
            ['category' => 'Sports', 'word' => 'FENCING'],
            ['category' => 'Sports', 'word' => 'WRESTLING'],
            ['category' => 'Sports', 'word' => 'GYMNASTICS'],
            ['category' => 'Sports', 'word' => 'TRIATHLON'],
            ['category' => 'Sports', 'word' => 'MARATHON'],
            ['category' => 'Sports', 'word' => 'SPRINT'],
            ['category' => 'Sports', 'word' => 'PENTATHLON'],
            ['category' => 'Sports', 'word' => 'PADDLE'],
            ['category' => 'Sports', 'word' => 'HANDBALL'],
            ['category' => 'Sports', 'word' => 'LACROSSE'],
            ['category' => 'Sports', 'word' => 'FOOTBALL'],
            ['category' => 'Sports', 'word' => 'DARTS'],
            ['category' => 'Sports', 'word' => 'BOWLING'],
            ['category' => 'Sports', 'word' => 'POOL'],
            ['category' => 'Sports', 'word' => 'CHESS'],
            ['category' => 'Sports', 'word' => 'ESPORTS'],
            ['category' => 'Sports', 'word' => 'KICKBOX'],
            ['category' => 'Sports', 'word' => 'MMA'],
            ['category' => 'Sports', 'word' => 'PILATES'],
            ['category' => 'Sports', 'word' => 'YOGA'],
            ['category' => 'Sports', 'word' => 'CROSSFIT'],
            ['category' => 'Sports', 'word' => 'HIKING'],
            ['category' => 'Sports', 'word' => 'DIVING'],
            ['category' => 'Sports', 'word' => 'KAYAKING'],

            // =========================
            // Business (50)
            // =========================
            ['category' => 'Business', 'word' => 'STARTUP'],
            ['category' => 'Business', 'word' => 'INVESTOR'],
            ['category' => 'Business', 'word' => 'MARKETING'],
            ['category' => 'Business', 'word' => 'STRATEGY'],
            ['category' => 'Business', 'word' => 'BRANDING'],
            ['category' => 'Business', 'word' => 'REVENUE'],
            ['category' => 'Business', 'word' => 'PROFIT'],
            ['category' => 'Business', 'word' => 'INVOICE'],
            ['category' => 'Business', 'word' => 'BUDGET'],
            ['category' => 'Business', 'word' => 'FORECAST'],
            ['category' => 'Business', 'word' => 'CONTRACT'],
            ['category' => 'Business', 'word' => 'NEGOTIATE'],
            ['category' => 'Business', 'word' => 'PITCH'],
            ['category' => 'Business', 'word' => 'PRESENTATION'],
            ['category' => 'Business', 'word' => 'CUSTOMER'],
            ['category' => 'Business', 'word' => 'CLIENT'],
            ['category' => 'Business', 'word' => 'SUPPLIER'],
            ['category' => 'Business', 'word' => 'LOGISTICS'],
            ['category' => 'Business', 'word' => 'OPERATIONS'],
            ['category' => 'Business', 'word' => 'STAFF'],
            ['category' => 'Business', 'word' => 'HIRING'],
            ['category' => 'Business', 'word' => 'PAYROLL'],
            ['category' => 'Business', 'word' => 'ACCOUNTING'],
            ['category' => 'Business', 'word' => 'FINANCE'],
            ['category' => 'Business', 'word' => 'AUDIT'],
            ['category' => 'Business', 'word' => 'TAX'],
            ['category' => 'Business', 'word' => 'VALUATION'],
            ['category' => 'Business', 'word' => 'EQUITY'],
            ['category' => 'Business', 'word' => 'SHARES'],
            ['category' => 'Business', 'word' => 'DIVIDEND'],
            ['category' => 'Business', 'word' => 'MERGER'],
            ['category' => 'Business', 'word' => 'ACQUISITION'],
            ['category' => 'Business', 'word' => 'PARTNERSHIP'],
            ['category' => 'Business', 'word' => 'NETWORK'],
            ['category' => 'Business', 'word' => 'LEAD'],
            ['category' => 'Business', 'word' => 'PIPELINE'],
            ['category' => 'Business', 'word' => 'CRM'],
            ['category' => 'Business', 'word' => 'KPI'],
            ['category' => 'Business', 'word' => 'ROADMAP'],
            ['category' => 'Business', 'word' => 'PRODUCT'],
            ['category' => 'Business', 'word' => 'PRICING'],
            ['category' => 'Business', 'word' => 'SUBSCRIPTION'],
            ['category' => 'Business', 'word' => 'RETENTION'],
            ['category' => 'Business', 'word' => 'CHURN'],
            ['category' => 'Business', 'word' => 'GROWTH'],
            ['category' => 'Business', 'word' => 'SCALING'],
            ['category' => 'Business', 'word' => 'OUTSOURCING'],
            ['category' => 'Business', 'word' => 'MEETING'],
            ['category' => 'Business', 'word' => 'DEADLINE'],
            ['category' => 'Business', 'word' => 'REPORT'],

            // =========================
            // Entertainment (50)
            // =========================
            ['category' => 'Entertainment', 'word' => 'MOVIE'],
            ['category' => 'Entertainment', 'word' => 'MUSIC'],
            ['category' => 'Entertainment', 'word' => 'CONCERT'],
            ['category' => 'Entertainment', 'word' => 'FESTIVAL'],
            ['category' => 'Entertainment', 'word' => 'THEATER'],
            ['category' => 'Entertainment', 'word' => 'COMEDY'],
            ['category' => 'Entertainment', 'word' => 'DRAMA'],
            ['category' => 'Entertainment', 'word' => 'ACTION'],
            ['category' => 'Entertainment', 'word' => 'THRILLER'],
            ['category' => 'Entertainment', 'word' => 'ROMANCE'],
            ['category' => 'Entertainment', 'word' => 'ANIMATION'],
            ['category' => 'Entertainment', 'word' => 'DOCUMENTARY'],
            ['category' => 'Entertainment', 'word' => 'SERIES'],
            ['category' => 'Entertainment', 'word' => 'EPISODE'],
            ['category' => 'Entertainment', 'word' => 'SEASON'],
            ['category' => 'Entertainment', 'word' => 'TRAILER'],
            ['category' => 'Entertainment', 'word' => 'SOUNDTRACK'],
            ['category' => 'Entertainment', 'word' => 'ALBUM'],
            ['category' => 'Entertainment', 'word' => 'SINGLE'],
            ['category' => 'Entertainment', 'word' => 'PLAYLIST'],
            ['category' => 'Entertainment', 'word' => 'DISCJOCKEY'],
            ['category' => 'Entertainment', 'word' => 'DANCEFLOOR'],
            ['category' => 'Entertainment', 'word' => 'CLUB'],
            ['category' => 'Entertainment', 'word' => 'STAGE'],
            ['category' => 'Entertainment', 'word' => 'SPOTLIGHT'],
            ['category' => 'Entertainment', 'word' => 'BACKSTAGE'],
            ['category' => 'Entertainment', 'word' => 'AUDIENCE'],
            ['category' => 'Entertainment', 'word' => 'APPLAUSE'],
            ['category' => 'Entertainment', 'word' => 'ENCORE'],
            ['category' => 'Entertainment', 'word' => 'CINEMA'],
            ['category' => 'Entertainment', 'word' => 'POP'],
            ['category' => 'Entertainment', 'word' => 'ROCK'],
            ['category' => 'Entertainment', 'word' => 'JAZZ'],
            ['category' => 'Entertainment', 'word' => 'HIPHOP'],
            ['category' => 'Entertainment', 'word' => 'CLASSICAL'],
            ['category' => 'Entertainment', 'word' => 'OPERA'],
            ['category' => 'Entertainment', 'word' => 'BALLET'],
            ['category' => 'Entertainment', 'word' => 'CIRCUS'],
            ['category' => 'Entertainment', 'word' => 'MAGIC'],
            ['category' => 'Entertainment', 'word' => 'PUZZLE'],
            ['category' => 'Entertainment', 'word' => 'GAMING'],
            ['category' => 'Entertainment', 'word' => 'ARCADE'],
            ['category' => 'Entertainment', 'word' => 'STREAM'],
            ['category' => 'Entertainment', 'word' => 'PODCAST'],
            ['category' => 'Entertainment', 'word' => 'RADIO'],
            ['category' => 'Entertainment', 'word' => 'KARAOKE'],
            ['category' => 'Entertainment', 'word' => 'COSPLAY'],
            ['category' => 'Entertainment', 'word' => 'COMIC'],
            ['category' => 'Entertainment', 'word' => 'NOVEL'],
            ['category' => 'Entertainment', 'word' => 'POETRY'],

            // =========================
            // Travel (50)
            // =========================
            ['category' => 'Travel', 'word' => 'PASSPORT'],
            ['category' => 'Travel', 'word' => 'LUGGAGE'],
            ['category' => 'Travel', 'word' => 'ITINERARY'],
            ['category' => 'Travel', 'word' => 'HOTEL'],
            ['category' => 'Travel', 'word' => 'HOSTEL'],
            ['category' => 'Travel', 'word' => 'RESORT'],
            ['category' => 'Travel', 'word' => 'BOOKING'],
            ['category' => 'Travel', 'word' => 'CHECKIN'],
            ['category' => 'Travel', 'word' => 'DEPARTURE'],
            ['category' => 'Travel', 'word' => 'ARRIVAL'],
            ['category' => 'Travel', 'word' => 'FLIGHT'],
            ['category' => 'Travel', 'word' => 'TRAIN'],
            ['category' => 'Travel', 'word' => 'METRO'],
            ['category' => 'Travel', 'word' => 'TICKET'],
            ['category' => 'Travel', 'word' => 'MAP'],
            ['category' => 'Travel', 'word' => 'GUIDE'],
            ['category' => 'Travel', 'word' => 'TOUR'],
            ['category' => 'Travel', 'word' => 'EXCURSION'],
            ['category' => 'Travel', 'word' => 'BACKPACK'],
            ['category' => 'Travel', 'word' => 'SUITCASE'],
            ['category' => 'Travel', 'word' => 'VISA'],
            ['category' => 'Travel', 'word' => 'CUSTOMS'],
            ['category' => 'Travel', 'word' => 'CURRENCY'],
            ['category' => 'Travel', 'word' => 'EXCHANGE'],
            ['category' => 'Travel', 'word' => 'SOUVENIR'],
            ['category' => 'Travel', 'word' => 'BEACH'],
            ['category' => 'Travel', 'word' => 'MUSEUM'],
            ['category' => 'Travel', 'word' => 'TEMPLE'],
            ['category' => 'Travel', 'word' => 'CASTLE'],
            ['category' => 'Travel', 'word' => 'BRIDGE'],
            ['category' => 'Travel', 'word' => 'MARKET'],
            ['category' => 'Travel', 'word' => 'HARBOR'],
            ['category' => 'Travel', 'word' => 'AIRPORT'],
            ['category' => 'Travel', 'word' => 'STATION'],
            ['category' => 'Travel', 'word' => 'RENTAL'],
            ['category' => 'Travel', 'word' => 'CARAVAN'],
            ['category' => 'Travel', 'word' => 'CAMPING'],
            ['category' => 'Travel', 'word' => 'TREKKING'],
            ['category' => 'Travel', 'word' => 'SAFARI'],
            ['category' => 'Travel', 'word' => 'CRUISE'],
            ['category' => 'Travel', 'word' => 'ISLANDHOP'],
            ['category' => 'Travel', 'word' => 'SUNSCREEN'],
            ['category' => 'Travel', 'word' => 'SNORKEL'],
            ['category' => 'Travel', 'word' => 'HIKETRAIL'],
            ['category' => 'Travel', 'word' => 'VIEWPOINT'],
            ['category' => 'Travel', 'word' => 'CITYPASS'],
            ['category' => 'Travel', 'word' => 'CHECKOUT'],
            ['category' => 'Travel', 'word' => 'LAYOVER'],
            ['category' => 'Travel', 'word' => 'JETLAG'],
            ['category' => 'Travel', 'word' => 'ROADTRIP'],

            // =========================
            // Health (50)
            // =========================
            ['category' => 'Health', 'word' => 'WELLNESS'],
            ['category' => 'Health', 'word' => 'FITNESS'],
            ['category' => 'Health', 'word' => 'NUTRITION'],
            ['category' => 'Health', 'word' => 'PROTEIN'],
            ['category' => 'Health', 'word' => 'VITAMIN'],
            ['category' => 'Health', 'word' => 'MINERAL'],
            ['category' => 'Health', 'word' => 'HYDRATION'],
            ['category' => 'Health', 'word' => 'SLEEP'],
            ['category' => 'Health', 'word' => 'RECOVERY'],
            ['category' => 'Health', 'word' => 'STRETCH'],
            ['category' => 'Health', 'word' => 'MEDICINE'],
            ['category' => 'Health', 'word' => 'THERAPY'],
            ['category' => 'Health', 'word' => 'CLINIC'],
            ['category' => 'Health', 'word' => 'HOSPITAL'],
            ['category' => 'Health', 'word' => 'DOCTOR'],
            ['category' => 'Health', 'word' => 'NURSE'],
            ['category' => 'Health', 'word' => 'VACCINE'],
            ['category' => 'Health', 'word' => 'IMMUNITY'],
            ['category' => 'Health', 'word' => 'CARDIO'],
            ['category' => 'Health', 'word' => 'MUSCLE'],
            ['category' => 'Health', 'word' => 'POSTURE'],
            ['category' => 'Health', 'word' => 'BREATHING'],
            ['category' => 'Health', 'word' => 'MINDFUL'],
            ['category' => 'Health', 'word' => 'MEDITATE'],
            ['category' => 'Health', 'word' => 'BALANCE'],
            ['category' => 'Health', 'word' => 'STRESS'],
            ['category' => 'Health', 'word' => 'ANXIETY'],
            ['category' => 'Health', 'word' => 'RELAX'],
            ['category' => 'Health', 'word' => 'MASSAGE'],
            ['category' => 'Health', 'word' => 'PHYSIO'],
            ['category' => 'Health', 'word' => 'REHAB'],
            ['category' => 'Health', 'word' => 'INJURY'],
            ['category' => 'Health', 'word' => 'HEALING'],
            ['category' => 'Health', 'word' => 'SYMPTOM'],
            ['category' => 'Health', 'word' => 'DIAGNOSIS'],
            ['category' => 'Health', 'word' => 'CHECKUP'],
            ['category' => 'Health', 'word' => 'PULSE'],
            ['category' => 'Health', 'word' => 'BLOODPRESS'],
            ['category' => 'Health', 'word' => 'HEARTRATE'],
            ['category' => 'Health', 'word' => 'CALORIES'],
            ['category' => 'Health', 'word' => 'METABOLISM'],
            ['category' => 'Health', 'word' => 'ALLERGY'],
            ['category' => 'Health', 'word' => 'DENTAL'],
            ['category' => 'Health', 'word' => 'VISION'],
            ['category' => 'Health', 'word' => 'HEARING'],
            ['category' => 'Health', 'word' => 'YOGANIDRA'],
            ['category' => 'Health', 'word' => 'PILATES'],
            ['category' => 'Health', 'word' => 'RUNNERHIGH'],
            ['category' => 'Health', 'word' => 'ENDURANCE'],
            ['category' => 'Health', 'word' => 'FLEXIBILITY'],

            // =========================
            // Art (50)
            // =========================
            ['category' => 'Art', 'word' => 'PAINTING'],
            ['category' => 'Art', 'word' => 'DRAWING'],
            ['category' => 'Art', 'word' => 'SCULPTURE'],
            ['category' => 'Art', 'word' => 'SKETCH'],
            ['category' => 'Art', 'word' => 'CANVAS'],
            ['category' => 'Art', 'word' => 'PORTRAIT'],
            ['category' => 'Art', 'word' => 'LANDSCAPE'],
            ['category' => 'Art', 'word' => 'ABSTRACT'],
            ['category' => 'Art', 'word' => 'MURAL'],
            ['category' => 'Art', 'word' => 'GRAFFITI'],
            ['category' => 'Art', 'word' => 'PHOTOGRAPHY'],
            ['category' => 'Art', 'word' => 'FILM'],
            ['category' => 'Art', 'word' => 'DESIGN'],
            ['category' => 'Art', 'word' => 'ILLUSTRATION'],
            ['category' => 'Art', 'word' => 'TYPOGRAPHY'],
            ['category' => 'Art', 'word' => 'CALLIGRAPHY'],
            ['category' => 'Art', 'word' => 'CERAMICS'],
            ['category' => 'Art', 'word' => 'POTTERY'],
            ['category' => 'Art', 'word' => 'WEAVING'],
            ['category' => 'Art', 'word' => 'TEXTILE'],
            ['category' => 'Art', 'word' => 'COLLAGE'],
            ['category' => 'Art', 'word' => 'PRINTMAKING'],
            ['category' => 'Art', 'word' => 'ETCHING'],
            ['category' => 'Art', 'word' => 'LITHOGRAPH'],
            ['category' => 'Art', 'word' => 'WATERCOLOR'],
            ['category' => 'Art', 'word' => 'OILPAINT'],
            ['category' => 'Art', 'word' => 'ACRYLIC'],
            ['category' => 'Art', 'word' => 'CHARCOAL'],
            ['category' => 'Art', 'word' => 'PASTEL'],
            ['category' => 'Art', 'word' => 'INK'],
            ['category' => 'Art', 'word' => 'MODEL'],
            ['category' => 'Art', 'word' => 'ARCHITECTURE'],
            ['category' => 'Art', 'word' => 'FASHION'],
            ['category' => 'Art', 'word' => 'JEWELRY'],
            ['category' => 'Art', 'word' => 'MOSAIC'],
            ['category' => 'Art', 'word' => 'GLASSWORK'],
            ['category' => 'Art', 'word' => 'WOODWORK'],
            ['category' => 'Art', 'word' => 'CARVING'],
            ['category' => 'Art', 'word' => 'ORIGAMI'],
            ['category' => 'Art', 'word' => 'ANIMATIONART'],
            ['category' => 'Art', 'word' => 'CONCEPTART'],
            ['category' => 'Art', 'word' => 'STORYBOARD'],
            ['category' => 'Art', 'word' => 'GALLERY'],
            ['category' => 'Art', 'word' => 'EXHIBIT'],
            ['category' => 'Art', 'word' => 'CURATOR'],
            ['category' => 'Art', 'word' => 'MASTERPIECE'],
            ['category' => 'Art', 'word' => 'AESTHETIC'],
            ['category' => 'Art', 'word' => 'PALETTE'],
            ['category' => 'Art', 'word' => 'COMPOSITION'],
            ['category' => 'Art', 'word' => 'SYMMETRY'],

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
            ['key' => 'global',  'label' => 'Worldwide', 'icon' => 'fa-solid fa-globe'],
            ['key' => 'friends', 'label' => 'Friends',   'icon' => 'fa-solid fa-user-group'],
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