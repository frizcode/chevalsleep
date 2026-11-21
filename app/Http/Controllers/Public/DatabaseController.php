<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Character;
use App\Models\Skill;
use App\Models\SupportCard;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DatabaseController extends Controller
{
    /**
     * Public Uma Musume Character Encyclopedia.
     */
    public function umas(Request $request): View
    {
        $characters = Character::withCount('tierEntries')
            ->orderBy('rarity', 'desc')
            ->orderBy('name')
            ->get();

        return view('public.guides.umas', [
            'characters' => $characters,
            'runningStyles' => ['Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'],
        ]);
    }

    /**
     * Public Skills Encyclopedia.
     */
    public function skills(Request $request): View
    {
        $skills = Skill::query()
            ->orderByRaw("CASE rarity WHEN 'Unique' THEN 1 WHEN 'Rare' THEN 2 WHEN 'Inherited' THEN 3 WHEN 'Normal' THEN 4 ELSE 5 END")
            ->orderBy('name')
            ->get();

        return view('public.guides.skills', [
            'skills' => $skills,
            'rarities' => ['Unique', 'Rare', 'Inherited', 'Normal'],
            'skillTypes' => ['Acceleration', 'Velocity', 'Recovery', 'Debuff', 'Passive'],
            'runningStyles' => ['Universal', 'Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer'],
        ]);
    }

    /**
     * Public Support Card (Supoka) Compendium.
     */
    public function supoka(Request $request): View
    {
        $cards = SupportCard::query()
            ->orderByRaw("CASE rarity WHEN 'SSR' THEN 1 WHEN 'SR' THEN 2 WHEN 'R' THEN 3 ELSE 4 END")
            ->orderBy('name')
            ->get();

        return view('public.guides.supoka', [
            'cards' => $cards,
            'rarities' => ['SSR', 'SR', 'R'],
            'cardTypes' => ['Speed', 'Stamina', 'Power', 'Guts', 'Wit', 'Friend', 'Group'],
        ]);
    }
}
