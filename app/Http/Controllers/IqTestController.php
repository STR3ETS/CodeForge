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

        $questions = $this->getQuestions();

        return view('games.iq-test', [
            'user' => $user,
            'questions' => $questions,
        ]);
    }

    private function getQuestions(): array
    {
        return [
            // --- REEKSEN (Numerical Reasoning) ---
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal volgt? 2, 4, 8, 16, 32, ...',
                'options' => ['48', '64', '56', '36'],
                'answer' => 1,
            ],
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal volgt? 3, 6, 11, 18, 27, ...',
                'options' => ['36', '38', '35', '40'],
                'answer' => 1,
            ],
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal volgt? 1, 1, 2, 3, 5, 8, ...',
                'options' => ['11', '12', '13', '10'],
                'answer' => 2,
            ],
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal ontbreekt? 4, 9, 16, ?, 36, 49',
                'options' => ['20', '24', '25', '28'],
                'answer' => 2,
            ],
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal volgt? 100, 81, 64, 49, 36, ...',
                'options' => ['16', '24', '25', '30'],
                'answer' => 2,
            ],
            [
                'category' => 'Reeksen',
                'question' => 'Welk getal volgt? 2, 6, 12, 20, 30, ...',
                'options' => ['40', '42', '44', '38'],
                'answer' => 1,
            ],

            // --- LOGICA (Logical Reasoning) ---
            [
                'category' => 'Logica',
                'question' => 'Alle rozen zijn bloemen. Sommige bloemen verwelken snel. Welke conclusie klopt?',
                'options' => [
                    'Alle rozen verwelken snel',
                    'Sommige rozen verwelken mogelijk snel',
                    'Geen enkele roos verwelkt snel',
                    'Bloemen zijn altijd rozen',
                ],
                'answer' => 1,
            ],
            [
                'category' => 'Logica',
                'question' => 'Als het regent, is de straat nat. De straat is nat. Wat weet je zeker?',
                'options' => [
                    'Het regent',
                    'Het heeft geregend',
                    'De straat kan ook om een andere reden nat zijn',
                    'Het stopt binnenkort met regenen',
                ],
                'answer' => 2,
            ],
            [
                'category' => 'Logica',
                'question' => 'A is groter dan B. B is groter dan C. C is groter dan D. Welke uitspraak klopt?',
                'options' => [
                    'D is groter dan A',
                    'B is groter dan D',
                    'C is groter dan A',
                    'D is groter dan B',
                ],
                'answer' => 1,
            ],
            [
                'category' => 'Logica',
                'question' => 'In een groep van 5 mensen schudt iedereen elke andere persoon precies één keer de hand. Hoeveel handdrukken zijn er in totaal?',
                'options' => ['20', '15', '10', '25'],
                'answer' => 2,
            ],
            [
                'category' => 'Logica',
                'question' => 'Een klok loopt 5 minuten achter per uur. Na 12 uur, hoeveel minuten loopt hij achter?',
                'options' => ['55 minuten', '60 minuten', '50 minuten', '65 minuten'],
                'answer' => 1,
            ],
            [
                'category' => 'Logica',
                'question' => 'Je hebt 3 dozen. Eén bevat appels, één sinaasappels, en één beide. Alle labels zijn FOUT. Je pakt één vrucht uit de doos met label "Beide". Het is een appel. Wat zit er in de doos met label "Sinaasappels"?',
                'options' => ['Sinaasappels', 'Appels', 'Beide', 'Leeg'],
                'answer' => 2,
            ],

            // --- WISKUNDE (Mathematical Reasoning) ---
            [
                'category' => 'Wiskunde',
                'question' => 'Als 3x + 7 = 22, wat is x?',
                'options' => ['3', '4', '5', '6'],
                'answer' => 2,
            ],
            [
                'category' => 'Wiskunde',
                'question' => 'Een trein rijdt 120 km in 1,5 uur. Wat is de gemiddelde snelheid?',
                'options' => ['60 km/u', '70 km/u', '80 km/u', '90 km/u'],
                'answer' => 2,
            ],
            [
                'category' => 'Wiskunde',
                'question' => 'Wat is 15% van 240?',
                'options' => ['32', '34', '36', '38'],
                'answer' => 2,
            ],
            [
                'category' => 'Wiskunde',
                'question' => 'Een winkel geeft 20% korting. Daarna nog eens 10% op de verlaagde prijs. Hoeveel procent korting heb je in totaal?',
                'options' => ['30%', '28%', '27%', '25%'],
                'answer' => 1,
            ],
            [
                'category' => 'Wiskunde',
                'question' => 'Wat is de volgende priemgetal na 29?',
                'options' => ['31', '33', '35', '37'],
                'answer' => 0,
            ],
            [
                'category' => 'Wiskunde',
                'question' => 'Een rechthoek heeft een omtrek van 36 cm en een breedte van 6 cm. Wat is de oppervlakte?',
                'options' => ['60 cm²', '66 cm²', '72 cm²', '78 cm²'],
                'answer' => 2,
            ],

            // --- ANALOGIEËN (Verbal Reasoning) ---
            [
                'category' => 'Analogieën',
                'question' => 'Boek is voor lezen als vork is voor ...',
                'options' => ['keuken', 'eten', 'metaal', 'mes'],
                'answer' => 1,
            ],
            [
                'category' => 'Analogieën',
                'question' => 'Piloot is voor vliegtuig als kapitein is voor ...',
                'options' => ['zee', 'schip', 'haven', 'uniform'],
                'answer' => 1,
            ],
            [
                'category' => 'Analogieën',
                'question' => 'Welk woord past niet in de reeks? Tafel, stoel, lamp, kast',
                'options' => ['Tafel', 'Stoel', 'Lamp', 'Kast'],
                'answer' => 2,
            ],
            [
                'category' => 'Analogieën',
                'question' => 'Warm is voor koud als licht is voor ...',
                'options' => ['lamp', 'donker', 'zon', 'helder'],
                'answer' => 1,
            ],
            [
                'category' => 'Analogieën',
                'question' => 'Welk woord past niet? Dolfijn, haai, walvis, zalm',
                'options' => ['Dolfijn', 'Haai', 'Walvis', 'Zalm'],
                'answer' => 1,
            ],
            [
                'category' => 'Analogieën',
                'question' => 'Schilder is voor penseel als schrijver is voor ...',
                'options' => ['boek', 'pen', 'papier', 'verhaal'],
                'answer' => 1,
            ],

            // --- RUIMTELIJK INZICHT (Spatial/Pattern) ---
            [
                'category' => 'Patronen',
                'question' => 'Een kubus heeft 6 vlakken, 12 ribben en ... hoekpunten?',
                'options' => ['4', '6', '8', '10'],
                'answer' => 2,
            ],
            [
                'category' => 'Patronen',
                'question' => 'Als je een vierkant papier diagonaal vouwt en dan de punt afknipt, hoeveel gaten zitten er in het papier als je het openvouwt?',
                'options' => ['1', '2', '3', '4'],
                'answer' => 0,
            ],
            [
                'category' => 'Patronen',
                'question' => 'Hoeveel driehoeken zitten er in een ster van David (hexagram)?',
                'options' => ['6', '8', '10', '20'],
                'answer' => 1,
            ],
            [
                'category' => 'Patronen',
                'question' => 'Een spiegelbeeld van de klok toont 3:00. Hoe laat is het echt?',
                'options' => ['9:00', '3:00', '6:00', '12:00'],
                'answer' => 0,
            ],
            [
                'category' => 'Patronen',
                'question' => 'Je draait 90° naar rechts, dan 180°, dan 90° naar links. Hoeveel graden ben je in totaal gedraaid ten opzichte van je startpositie?',
                'options' => ['0°', '90°', '180°', '360°'],
                'answer' => 2,
            ],
            [
                'category' => 'Patronen',
                'question' => 'Hoeveel blokken zijn er nodig om een kubus van 3×3×3 te bouwen?',
                'options' => ['9', '18', '24', '27'],
                'answer' => 3,
            ],
        ];
    }
}
