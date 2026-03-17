<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IqTestController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if (($user->plan ?? 'free') !== 'pro') {
            return redirect()->route('pages.pricing')
                ->with('error', 'IQ Test is alleen beschikbaar voor Pro-leden.');
        }

        return view('games.iq-test', [
            'user' => $user,
            'simpleQuestions' => $this->getSimpleQuestions(),
            'extendedQuestions' => $this->getExtendedQuestions(),
        ]);
    }

    private function getSimpleQuestions(): array
    {
        return [
            // --- REEKSEN (2) ---
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal volgt? 5, 11, 23, 47, 95, ...',
                'options' => ['180', '191', '185', '200'],
                'answer' => 1,
            ],
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal ontbreekt? 1, 4, 9, ?, 25, 36',
                'options' => ['12', '14', '16', '18'],
                'answer' => 2,
            ],

            // --- LOGICA (2) ---
            [
                'category' => 'Logica',
                'question' => 'Jan is ouder dan Pieter. Pieter is ouder dan Klaas. Klaas is ouder dan Tom. Wie is het jongst?',
                'options' => ['Jan', 'Pieter', 'Klaas', 'Tom'],
                'answer' => 3,
            ],
            [
                'category' => 'Logica',
                'question' => 'In een race haal je de persoon op plek 2 in. Op welke plek sta je nu?',
                'options' => ['Plek 1', 'Plek 2', 'Plek 3', 'Plek 4'],
                'answer' => 1,
            ],

            // --- WISKUNDE (2) ---
            [
                'category' => 'Wiskunde',
                'question' => 'Een boer heeft 17 schapen. Alle behalve 9 gaan dood. Hoeveel schapen heeft hij nog?',
                'options' => ['8', '9', '17', '0'],
                'answer' => 1,
            ],
            [
                'category' => 'Wiskunde',
                'question' => 'Als je 2 appels pakt van 5 appels, hoeveel appels heb je dan?',
                'options' => ['3', '5', '2', '7'],
                'answer' => 2,
            ],

            // --- ANALOGIEËN (2) ---
            [
                'category' => 'Analogieën',
                'question' => 'Oog is voor zien als oor is voor ...',
                'options' => ['geluid', 'horen', 'hoofd', 'muziek'],
                'answer' => 1,
            ],
            [
                'category' => 'Analogieën',
                'question' => 'Welk woord past niet? Mars, Venus, Zon, Saturnus',
                'options' => ['Mars', 'Venus', 'Zon', 'Saturnus'],
                'answer' => 2,
            ],

            // --- PATRONEN / RUIMTELIJK (2) ---
            [
                'category' => 'Patronen',
                'question' => 'Deze kubus wordt opengevouwen. Hoeveel vlakken zie je in totaal?',
                'options' => ['4', '5', '6', '8'],
                'answer' => 2,
                'visual' => 'cube-unfold-cross',
            ],
            [
                'category' => 'Patronen',
                'question' => 'Je kijkt recht van voren naar deze piramide. Welke 2D-vorm zie je?',
                'options' => ['Vierkant', 'Driehoek', 'Rechthoek', 'Cirkel'],
                'answer' => 1,
                'visual' => 'pyramid-front',
            ],
        ];
    }

    private function getExtendedQuestions(): array
    {
        return [
            // =====================
            // REEKSEN (6)
            // =====================
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal volgt? 3, 5, 9, 17, 33, ...',
                'options' => ['49', '57', '65', '61'],
                'answer' => 2,
            ],
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal volgt? 2, 3, 5, 7, 11, 13, ...',
                'options' => ['15', '16', '17', '19'],
                'answer' => 2,
            ],
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal ontbreekt? 1, 3, 7, 15, ?, 63',
                'options' => ['27', '30', '31', '32'],
                'answer' => 2,
            ],
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal volgt? 120, 60, 20, 5, ...',
                'options' => ['2', '1', '0', '2.5'],
                'answer' => 1,
            ],
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal volgt? 1, 1, 2, 6, 24, ...',
                'options' => ['48', '72', '100', '120'],
                'answer' => 3,
            ],
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal volgt? 0, 1, 1, 2, 3, 5, 8, 13, ...',
                'options' => ['18', '20', '21', '24'],
                'answer' => 2,
            ],

            // =====================
            // LOGICA (6)
            // =====================
            [
                'category' => 'Logica',
                'question' => 'Drie vrienden (Anna, Ben, Clara) dragen elk een andere kleur: rood, blauw, groen. Anna draagt geen rood. Ben draagt blauw. Welke kleur draagt Anna?',
                'options' => ['Rood', 'Blauw', 'Groen', 'Geel'],
                'answer' => 2,
            ],
            [
                'category' => 'Logica',
                'question' => 'Alle A\'s zijn B\'s. Geen enkele B is een C. Wat klopt?',
                'options' => [
                    'Sommige A\'s zijn C\'s',
                    'Geen enkele A is een C',
                    'Alle C\'s zijn A\'s',
                    'Sommige B\'s zijn C\'s',
                ],
                'answer' => 1,
            ],
            [
                'category' => 'Logica',
                'question' => 'Er zijn 2 vaders en 2 zonen. Samen hebben ze 3 auto\'s, en ieder heeft er precies één. Hoe kan dat?',
                'options' => [
                    'Eén persoon heeft 2 auto\'s',
                    'Het zijn 3 personen: opa, vader, zoon',
                    'Eén van hen liegt',
                    'Dat kan niet',
                ],
                'answer' => 1,
            ],
            [
                'category' => 'Logica',
                'question' => 'Als alle katten dieren zijn en sommige dieren zwart zijn, welke conclusie is dan ZEKER waar?',
                'options' => [
                    'Sommige katten zijn zwart',
                    'Alle katten zijn zwart',
                    'Katten zijn dieren',
                    'Alle dieren zijn katten',
                ],
                'answer' => 2,
            ],
            [
                'category' => 'Logica',
                'question' => 'Je hebt een lucifer en loopt een donkere kamer in met een kaars, een olielamp en een open haard. Wat steek je het eerst aan?',
                'options' => ['De kaars', 'De olielamp', 'De open haard', 'De lucifer'],
                'answer' => 3,
            ],
            [
                'category' => 'Logica',
                'question' => 'Een arts zegt: "De jongen is mijn zoon, maar ik ben niet zijn vader." Hoe kan dat?',
                'options' => [
                    'De arts liegt',
                    'De arts is zijn moeder',
                    'Het is zijn stiefvader',
                    'Dat kan niet',
                ],
                'answer' => 1,
            ],

            // =====================
            // WISKUNDE (6)
            // =====================
            [
                'category' => 'Wiskunde',
                'question' => 'Een klok slaat om 6 uur 6 keer. Dat duurt 30 seconden. Hoe lang duurt het om 12 keer te slaan?',
                'options' => ['60 sec', '66 sec', '55 sec', '72 sec'],
                'answer' => 1,
            ],
            [
                'category' => 'Wiskunde',
                'question' => 'Een halve kip legt in anderhalve dag anderhalf ei. Hoeveel eieren leggen 6 kippen in 6 dagen?',
                'options' => ['6', '12', '24', '36'],
                'answer' => 2,
            ],
            [
                'category' => 'Wiskunde',
                'question' => 'Je verdubbelt elke dag het aantal bacteriën. Na 20 dagen is het glas vol. Op welke dag is het glas half vol?',
                'options' => ['Dag 10', 'Dag 15', 'Dag 18', 'Dag 19'],
                'answer' => 3,
            ],
            [
                'category' => 'Wiskunde',
                'question' => 'Hoeveel driehoeken kun je vormen met 6 punten op een cirkel?',
                'options' => ['10', '15', '20', '30'],
                'answer' => 2,
            ],
            [
                'category' => 'Wiskunde',
                'question' => 'Een item kost €100. Het gaat 10% omlaag, dan 10% omhoog. Wat is de prijs nu?',
                'options' => ['€100', '€99', '€101', '€98'],
                'answer' => 1,
            ],
            [
                'category' => 'Wiskunde',
                'question' => 'Je hebt 8 identieke ballen. Eén is zwaarder. Met een balans, wat is het minimale aantal keer wegen om de zware bal te vinden?',
                'options' => ['1', '2', '3', '4'],
                'answer' => 1,
            ],

            // =====================
            // ANALOGIEËN (6)
            // =====================
            [
                'category' => 'Analogieën',
                'question' => 'Chirurg is voor scalpel als schilder is voor ...',
                'options' => ['verf', 'kwast', 'doek', 'ezel'],
                'answer' => 1,
            ],
            [
                'category' => 'Analogieën',
                'question' => 'Welk woord past niet? Viool, piano, fluit, trommel',
                'options' => ['Viool', 'Piano', 'Fluit', 'Trommel'],
                'answer' => 3,
            ],
            [
                'category' => 'Analogieën',
                'question' => 'Water is voor stoom als ijs is voor ...',
                'options' => ['koud', 'water', 'sneeuw', 'vorst'],
                'answer' => 1,
            ],
            [
                'category' => 'Analogieën',
                'question' => 'Welk woord past niet? Parijs, London, New York, Frankrijk',
                'options' => ['Parijs', 'London', 'New York', 'Frankrijk'],
                'answer' => 3,
            ],
            [
                'category' => 'Analogieën',
                'question' => 'Kilometer is voor afstand als kilogram is voor ...',
                'options' => ['snelheid', 'gewicht', 'temperatuur', 'volume'],
                'answer' => 1,
            ],
            [
                'category' => 'Analogieën',
                'question' => 'Architect is voor gebouw als componist is voor ...',
                'options' => ['instrument', 'orkest', 'muziekstuk', 'podium'],
                'answer' => 2,
            ],

            // =====================
            // PATRONEN / RUIMTELIJK INZICHT (6)
            // =====================
            [
                'category' => 'Patronen',
                'question' => 'Bekijk deze opengevouwen kubus. Je draait de kubus 90° naar rechts. Wat zie je dan op de voorkant?',
                'options' => ['Cirkel', 'Ster', 'Vierkant', 'Driehoek'],
                'answer' => 2,
                'visual' => 'cube-symbols-rotate',
            ],
            [
                'category' => 'Patronen',
                'question' => 'Bekijk deze opengevouwen kubus in T-vorm. Het donkere vlak is gemarkeerd. Welk vlak zit tegenover het donkere vlak als de kubus wordt dichtgevouwen?',
                'options' => [
                    'Vlak 1 (boven)',
                    'Vlak 5 (onder)',
                    'Vlak 4 (links)',
                    'Vlak 6 (rechts)',
                ],
                'answer' => 0,
                'visual' => 'cube-t-shape',
            ],
            [
                'category' => 'Patronen',
                'question' => 'Je kijkt van bovenaf naar deze opstelling: een cilinder staat op een kubus. Welke vorm(en) zie je?',
                'options' => [
                    'Een cirkel in een vierkant',
                    'Alleen een cirkel',
                    'Een vierkant',
                    'Een cirkel naast een vierkant',
                ],
                'answer' => 0,
                'visual' => 'cylinder-on-cube',
            ],
            [
                'category' => 'Patronen',
                'question' => 'Bekijk deze opengevouwen kubus met cijfers 1 t/m 6. Welk cijfer zit tegenover het vlak met cijfer 2?',
                'options' => ['1', '3', '5', '6'],
                'answer' => 3,
                'visual' => 'cube-numbers',
            ],
            [
                'category' => 'Patronen',
                'question' => 'Een vierkant papier wordt 2 keer gevouwen (in kwarten) zoals hieronder. Daarna knip je de rechterbovenhoek af (rood). Hoeveel gaten zitten er als je het papier openvouwt?',
                'options' => ['1 gat (in het midden)', '2 gaten', '3 gaten', '4 gaten'],
                'answer' => 0,
                'visual' => 'paper-fold',
            ],
            [
                'category' => 'Patronen',
                'question' => 'Bekijk dit patroon. Welke figuur hoort op de plek van het vraagteken?',
                'options' => ['Gevulde cirkel', 'Lege driehoek', 'Gevulde driehoek', 'Lege cirkel'],
                'answer' => 2,
                'visual' => 'pattern-matrix',
            ],
        ];
    }
}
