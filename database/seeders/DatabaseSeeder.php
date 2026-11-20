<?php

namespace Database\Seeders;

use App\Models\Changelog;
use App\Models\Character;
use App\Models\Event;
use App\Models\TierEntry;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Default Admin User
        User::updateOrCreate(
            ['email' => 'admin@chevalsleep.test'],
            [
                'name' => 'Admin ChevalSleep',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Events (CM #22 Active & CM #21 Archived)
        $aquariusCup = Event::updateOrCreate(
            ['slug' => 'cm-22-aquarius-cup'],
            [
                'title' => 'CM #22 Aquarius Cup',
                'cm_number' => 22,
                'event_date' => '2026-11-15',
                'track_name' => 'Tokyo 1600m',
                'distance_type' => 'Mile',
                'distance_meters' => 1600,
                'surface' => 'Dirt',
                'season' => 'Winter',
                'weather' => 'Sunny',
                'track_condition' => 'Firm',
                'is_active' => true,
                'stat_recommendations' => [
                    'minimum' => [
                        'speed' => '1150',
                        'stamina' => '550',
                        'power' => '1000',
                        'guts' => '400',
                        'wit' => '900',
                    ],
                    'recommended' => [
                        'speed' => '1200+',
                        'stamina' => '650+',
                        'power' => '1150+',
                        'guts' => '450+',
                        'wit' => '1100+',
                    ],
                    'notes' => 'Requires 1 gold recovery skill or 650+ Stamina if intense position rushing occurs on Tokyo 1600m Dirt.',
                ],
                'skill_inheritance' => [
                    'Front Runner' => [
                        ['name' => 'Angling and Scheming (Seiun Sky)', 'icon' => null],
                        ['name' => 'Victoria por plancha (El Condor Pasa)', 'icon' => null],
                    ],
                    'Pace Chaser' => [
                        ['name' => 'Shooting for Victory! (Taiki Shuttle)', 'icon' => null],
                        ['name' => 'Red Shift/LP1211-M (Maruzensky)', 'icon' => null],
                    ],
                    'Late Surger' => [
                        ['name' => "Let's Pump Some Iron! (Mejiro Ryan)", 'icon' => null],
                        ['name' => 'Shooting for Victory! (Taiki Shuttle)', 'icon' => null],
                    ],
                    'End Closer' => [
                        ['name' => "Let's Pump Some Iron! (Mejiro Ryan)", 'icon' => null],
                        ['name' => 'Shadow Break (Narita Brian)', 'icon' => null],
                    ],
                ],
                'recommended_skills' => [
                    'Front Runner' => [
                        'core_accel' => [
                            ['name' => 'Groundwork', 'icon' => null],
                            ['name' => 'Early Lead', 'icon' => null],
                        ],
                        'velocity_other' => [
                            ['name' => 'Mile Corners◯', 'icon' => null],
                            ['name' => 'Front Runner Straightaways◯', 'icon' => null],
                            ['name' => 'Escape Artist', 'icon' => null],
                        ],
                    ],
                    'Pace Chaser' => [
                        'core_accel' => [
                            ['name' => 'No Stopping Me!', 'icon' => null],
                            ['name' => 'Head-On', 'icon' => null],
                        ],
                        'velocity_other' => [
                            ['name' => 'Speed Star', 'icon' => null],
                            ['name' => 'Mile Straightaways◯', 'icon' => null],
                            ['name' => 'Fast Pace', 'icon' => null],
                        ],
                    ],
                    'Late Surger' => [
                        'core_accel' => [
                            ['name' => 'On Your Left!', 'icon' => null],
                            ['name' => 'No Stopping Me!', 'icon' => null],
                            ['name' => 'Nimble Navigator', 'icon' => null],
                        ],
                        'velocity_other' => [
                            ['name' => 'Outer Swell', 'icon' => null],
                            ['name' => 'Furious Feat', 'icon' => null],
                            ['name' => 'Mile Corners◯', 'icon' => null],
                        ],
                    ],
                    'End Closer' => [
                        'core_accel' => [
                            ['name' => 'Straightaway Spurt', 'icon' => null],
                            ['name' => 'No Stopping Me!', 'icon' => null],
                        ],
                        'velocity_other' => [
                            ['name' => 'Encroaching Shadow', 'icon' => null],
                            ['name' => 'Late Surge Prep', 'icon' => null],
                        ],
                    ],
                ],
                'supoka_decks' => [
                    [
                        'archetype' => '2 Speed / 2 Power / 2 Wit (Standard Ace)',
                        'cards' => [
                            ['name' => 'SSR Kitasan Black (Speed)', 'icon' => null],
                            ['name' => 'SSR Agnes Tachyon (Speed)', 'icon' => null],
                            ['name' => 'SSR El Condor Pasa (Power)', 'icon' => null],
                            ['name' => 'SR Daiwa Scarlet (Power)', 'icon' => null],
                            ['name' => 'SSR Fine Motion (Wit)', 'icon' => null],
                            ['name' => 'SSR Nice Nature (Wit)', 'icon' => null],
                        ],
                        'notes' => 'Ideal for reaching 1200 Speed / 1100+ Power / 1100+ Wit without dedicated Stamina cards on Tokyo Dirt.',
                    ],
                    [
                        'archetype' => '3 Speed / 1 Stamina / 2 Wit (Budget / Consistency)',
                        'cards' => [
                            ['name' => 'SSR Kitasan Black (Speed)', 'icon' => null],
                            ['name' => 'SR Sweep Tosho (Speed)', 'icon' => null],
                            ['name' => 'SR Shinko Windy (Speed)', 'icon' => null],
                            ['name' => 'SSR Super Creek (Stamina)', 'icon' => null],
                            ['name' => 'SR Marvelous Sunday (Wit)', 'icon' => null],
                            ['name' => 'SSR Fine Motion (Wit)', 'icon' => null],
                        ],
                        'notes' => 'Ensures Maestro recovery for guaranteed stamina safety even with high rushing pace.',
                    ],
                ],
                'team_compositions' => [
                    [
                        'name' => 'Double Front + Late Surger Ace',
                        'lineup' => [
                            'Smart Falcon (Front Ace)',
                            'Seiun Sky (Front Ace/Pace Setter)',
                            'Agnes Digital (Late Surger Ace)',
                        ],
                        'strategy' => 'Secures lead from start to deny solo front runners while Agnes Digital cleans up the stretch.',
                    ],
                    [
                        'name' => 'Front Runner + 2 Pace Chasers',
                        'lineup' => [
                            'Smart Falcon (Front Runner)',
                            'Oguri Cap (Pace Chaser)',
                            'Taiki Shuttle (Pace Chaser)',
                        ],
                        'strategy' => 'Dominates mid-leg speed and creates early acceleration wall at the final corner.',
                    ],
                ],
            ]
        );

        $capricornCup = Event::updateOrCreate(
            ['slug' => 'cm-21-capricorn-cup'],
            [
                'title' => 'CM #21 Capricorn Cup',
                'cm_number' => 21,
                'event_date' => '2026-10-15',
                'track_name' => 'Chukyo 1200m',
                'distance_type' => 'Sprint',
                'distance_meters' => 1200,
                'surface' => 'Turf',
                'season' => 'Winter',
                'weather' => 'Cloudy',
                'track_condition' => 'Good',
                'is_active' => false,
                'stat_recommendations' => [
                    'minimum' => [
                        'speed' => '1200',
                        'stamina' => '450',
                        'power' => '1100',
                        'guts' => '400',
                        'wit' => '1000',
                    ],
                    'recommended' => [
                        'speed' => '1200+',
                        'stamina' => '500+',
                        'power' => '1150+',
                        'guts' => '450+',
                        'wit' => '1100+',
                    ],
                    'notes' => 'Sprint distance requires zero recovery skills if stamina exceeds 450.',
                ],
                'skill_inheritance' => [
                    'Front Runner' => [
                        ['name' => 'Angling and Scheming (Seiun Sky)', 'icon' => null],
                        ['name' => 'Operation: Plan C (Curren Chan)', 'icon' => null],
                    ],
                    'Pace Chaser' => [
                        ['name' => 'Shooting for Victory! (Taiki Shuttle)', 'icon' => null],
                        ['name' => 'Victory Shot! (Taiki Shuttle)', 'icon' => null],
                    ],
                    'Late Surger' => [
                        ['name' => "Let's Pump Some Iron! (Mejiro Ryan)", 'icon' => null],
                    ],
                    'End Closer' => [
                        ['name' => "Let's Pump Some Iron! (Mejiro Ryan)", 'icon' => null],
                    ],
                ],
                'recommended_skills' => [
                    'Front Runner' => [
                        'core_accel' => [['name' => 'Groundwork', 'icon' => null]],
                        'velocity_other' => [['name' => 'Sprint Corners◯', 'icon' => null]],
                    ],
                    'Pace Chaser' => [
                        'core_accel' => [['name' => 'Victory Shot!', 'icon' => null]],
                        'velocity_other' => [['name' => 'Straightaway Sprint', 'icon' => null]],
                    ],
                    'Late Surger' => [
                        'core_accel' => [['name' => 'Sprint Straightaways◯', 'icon' => null]],
                        'velocity_other' => [['name' => 'Nimble Navigator', 'icon' => null]],
                    ],
                    'End Closer' => [
                        'core_accel' => [['name' => 'Straightaway Spurt', 'icon' => null]],
                        'velocity_other' => [['name' => 'Sprint Corners◯', 'icon' => null]],
                    ],
                ],
                'supoka_decks' => [
                    [
                        'archetype' => '3 Speed / 1 Power / 2 Wit (Sprint Focus)',
                        'cards' => [
                            ['name' => 'SSR Kitasan Black (Speed)', 'icon' => null],
                            ['name' => 'SSR Biko Pegasus (Speed)', 'icon' => null],
                            ['name' => 'SR Shinko Windy (Speed)', 'icon' => null],
                            ['name' => 'SSR Vodka (Power)', 'icon' => null],
                            ['name' => 'SSR Fine Motion (Wit)', 'icon' => null],
                            ['name' => 'SSR Nice Nature (Wit)', 'icon' => null],
                        ],
                        'notes' => 'Maximum sprint speed and power deck for Chukyo 1200m.',
                    ],
                ],
                'team_compositions' => [
                    [
                        'name' => 'Front Runner + 2 Pace Chasers',
                        'lineup' => [
                            'Seiun Sky (Front Ace)',
                            'Taiki Shuttle (Pace Chaser)',
                            'Oguri Cap (Pace Chaser)',
                        ],
                        'strategy' => 'Overwhelms the final stretch with instant sprint acceleration.',
                    ],
                ],
            ]
        );

        // 3. Characters (10 Characters)
        $charactersData = [
            [
                'name' => 'Agnes Digital',
                'variant_title' => 'Alter',
                'slug' => 'agnes-digital-alter',
                'avatar_webp' => null,
                'default_running_style' => 'Late Surger',
                'rarity' => 3,
            ],
            [
                'name' => 'Smart Falcon',
                'variant_title' => 'Original',
                'slug' => 'smart-falcon',
                'avatar_webp' => null,
                'default_running_style' => 'Front Runner',
                'rarity' => 3,
            ],
            [
                'name' => 'Oguri Cap',
                'variant_title' => 'Original',
                'slug' => 'oguri-cap',
                'avatar_webp' => null,
                'default_running_style' => 'Pace Chaser',
                'rarity' => 3,
            ],
            [
                'name' => 'Taiki Shuttle',
                'variant_title' => 'Original',
                'slug' => 'taiki-shuttle',
                'avatar_webp' => null,
                'default_running_style' => 'Pace Chaser',
                'rarity' => 3,
            ],
            [
                'name' => 'El Condor Pasa',
                'variant_title' => 'Original',
                'slug' => 'el-condor-pasa',
                'avatar_webp' => null,
                'default_running_style' => 'Pace Chaser',
                'rarity' => 2,
            ],
            [
                'name' => 'Gold Ship',
                'variant_title' => 'Original',
                'slug' => 'gold-ship',
                'avatar_webp' => null,
                'default_running_style' => 'End Closer',
                'rarity' => 2,
            ],
            [
                'name' => 'Maruzensky',
                'variant_title' => 'Summer',
                'slug' => 'maruzensky-summer',
                'avatar_webp' => null,
                'default_running_style' => 'Front Runner',
                'rarity' => 3,
            ],
            [
                'name' => 'Mejiro Dober',
                'variant_title' => 'Original',
                'slug' => 'mejiro-dober',
                'avatar_webp' => null,
                'default_running_style' => 'Late Surger',
                'rarity' => 3,
            ],
            [
                'name' => 'Seiun Sky',
                'variant_title' => 'Original',
                'slug' => 'seiun-sky',
                'avatar_webp' => null,
                'default_running_style' => 'Front Runner',
                'rarity' => 3,
            ],
            [
                'name' => 'Haru Urara',
                'variant_title' => 'New Year',
                'slug' => 'haru-urara-new-year',
                'avatar_webp' => null,
                'default_running_style' => 'Late Surger',
                'rarity' => 3,
            ],
        ];

        $characters = [];
        foreach ($charactersData as $c) {
            $characters[$c['slug']] = Character::updateOrCreate(['slug' => $c['slug']], $c);
        }

        // 4. Tier Entries for CM #22 Aquarius Cup (With SS rank)
        $aquariusEntries = [
            [
                'event_id' => $aquariusCup->id,
                'character_id' => $characters['agnes-digital-alter']->id,
                'tier_rank' => 'SS',
                'role' => 'Ace',
                'recommended_style' => 'Late Surger',
                'sort_order' => 1,
                'build_notes' => [
                    'target_stats' => '1200 / 650+ / 1150+ / 450+ / 1000+',
                    'mandatory_skills' => [
                        'No Stopping Me!',
                        'Nimble Navigator',
                        'Aquarius Straightaways',
                        'Mile Corners◯',
                    ],
                    'inherited_uniques' => ['Red Shift/LP1211-M', 'Shooting for Victory!'],
                    'summary' => 'Undisputed Apex Dirt Mile Late Surger with high acceleration consistency on Tokyo 1600m.',
                ],
            ],
            [
                'event_id' => $aquariusCup->id,
                'character_id' => $characters['smart-falcon']->id,
                'tier_rank' => 'SS',
                'role' => 'Ace',
                'recommended_style' => 'Front Runner',
                'sort_order' => 2,
                'build_notes' => [
                    'target_stats' => '1200 / 700+ / 1100+ / 400+ / 950+',
                    'mandatory_skills' => [
                        'Groundwork',
                        'Escape Artist',
                        'Dirt Corners◯',
                        'Front Runner Straightaways◯',
                    ],
                    'inherited_uniques' => ['Angling and Scheming', 'Operation: Plan C'],
                    'summary' => 'Dominant front-runner with intrinsic dirt aptitude. Dictates pace from the start and secures lead into the final stretch.',
                ],
            ],
            [
                'event_id' => $aquariusCup->id,
                'character_id' => $characters['oguri-cap']->id,
                'tier_rank' => 'S+',
                'role' => 'Ace',
                'recommended_style' => 'Pace Chaser',
                'sort_order' => 3,
                'build_notes' => [
                    'target_stats' => '1200 / 600+ / 1200 / 450+ / 900+',
                    'mandatory_skills' => [
                        'Gourmet',
                        'Fast Pace',
                        'Tokyo Racecourse◯',
                        'Mile Straightaways◯',
                    ],
                    'inherited_uniques' => ['Victory Shot!', 'Shooting for Victory!'],
                    'summary' => 'Extremely reliable Pace Chaser. Clutches late races with high burst acceleration and built-in stamina recovery.',
                ],
            ],
            [
                'event_id' => $aquariusCup->id,
                'character_id' => $characters['taiki-shuttle']->id,
                'tier_rank' => 'S+',
                'role' => 'Parent',
                'recommended_style' => 'Pace Chaser',
                'sort_order' => 4,
                'build_notes' => [
                    'target_stats' => '1150 / 600+ / 1100+ / 400+ / 900+',
                    'mandatory_skills' => [
                        'Victory Shot!',
                        'Pace Chaser Corners◯',
                        'Dirt Straightaways◯',
                    ],
                    'inherited_uniques' => ['Shooting for Victory!', 'Angling and Scheming'],
                    'summary' => "Essential parent character providing 'Victory Shot!' inheritance to all Dirt Mile pace chasers.",
                ],
            ],
            [
                'event_id' => $aquariusCup->id,
                'character_id' => $characters['maruzensky-summer']->id,
                'tier_rank' => 'S',
                'role' => 'Ace',
                'recommended_style' => 'Front Runner',
                'sort_order' => 5,
                'build_notes' => [
                    'target_stats' => '1200 / 700+ / 1050+ / 400+ / 1000+',
                    'mandatory_skills' => [
                        'Groundwork',
                        'Mid-leg Dominance',
                        'Mile Corners◯',
                    ],
                    'inherited_uniques' => ['Angling and Scheming', 'Red Shift/LP1211-M'],
                    'summary' => 'Strong contender if given Dirt factor conversion. Consistent mid-leg unique activation keeps position 1 secured.',
                ],
            ],
            [
                'event_id' => $aquariusCup->id,
                'character_id' => $characters['mejiro-dober']->id,
                'tier_rank' => 'A',
                'role' => 'Parent',
                'recommended_style' => 'Late Surger',
                'sort_order' => 6,
                'build_notes' => [
                    'target_stats' => '1100 / 600+ / 1050+ / 450+ / 850+',
                    'mandatory_skills' => [
                        'Late Surger Corners◯',
                        'Calm & Collected',
                        'Nimble Navigator',
                    ],
                    'inherited_uniques' => ['Shooting for Victory!', 'Victory Shot!'],
                    'summary' => "Primary parent choice for Late Surgers and End Closers thanks to 'Shooting for Victory!' inherited unique.",
                ],
            ],
            [
                'event_id' => $aquariusCup->id,
                'character_id' => $characters['el-condor-pasa']->id,
                'tier_rank' => 'A',
                'role' => 'Ace',
                'recommended_style' => 'Pace Chaser',
                'sort_order' => 7,
                'build_notes' => [
                    'target_stats' => '1150 / 650+ / 1050+ / 400+ / 800+',
                    'mandatory_skills' => [
                        'No Stopping Me!',
                        'Aggressive Drive',
                        'Mile Straightaways◯',
                    ],
                    'inherited_uniques' => ['Victory Shot!', 'Red Shift/LP1211-M'],
                    'summary' => 'Solid accessible Pace Chaser pick with natural Dirt aptitude and powerful final stretch unique.',
                ],
            ],
            [
                'event_id' => $aquariusCup->id,
                'character_id' => $characters['gold-ship']->id,
                'tier_rank' => 'B',
                'role' => 'Debuffer',
                'recommended_style' => 'End Closer',
                'sort_order' => 8,
                'build_notes' => [
                    'target_stats' => '1000 / 700+ / 950+ / 400+ / 900+',
                    'mandatory_skills' => [
                        'Hesitation: Front Runner',
                        'Gaze of the Glimpse',
                        'Stamina Eater',
                    ],
                    'inherited_uniques' => ['Shooting for Victory!'],
                    'summary' => 'A disruption debuffer pick utilized to cripple leading front runners stamina and disrupt positioning.',
                ],
            ],
            [
                'event_id' => $aquariusCup->id,
                'character_id' => $characters['haru-urara-new-year']->id,
                'tier_rank' => 'B',
                'role' => 'Ace',
                'recommended_style' => 'Late Surger',
                'sort_order' => 9,
                'build_notes' => [
                    'target_stats' => '1100 / 650+ / 1000+ / 400+ / 850+',
                    'mandatory_skills' => [
                        'Nimble Navigator',
                        'Late Surger Straightaways◯',
                        'Cheer Up!',
                    ],
                    'inherited_uniques' => ['Shooting for Victory!'],
                    'summary' => 'Cult favorite dirt runner with decent late acceleration potential when properly built with support cards.',
                ],
            ],
        ];

        foreach ($aquariusEntries as $entry) {
            TierEntry::updateOrCreate(
                [
                    'event_id' => $entry['event_id'],
                    'character_id' => $entry['character_id'],
                    'role' => $entry['role'],
                ],
                $entry
            );
        }

        // 5. Tier Entries for CM #21 Capricorn Cup (Chukyo 1200m Sprint Turf)
        $capricornEntries = [
            [
                'event_id' => $capricornCup->id,
                'character_id' => $characters['seiun-sky']->id,
                'tier_rank' => 'SS',
                'role' => 'Ace',
                'recommended_style' => 'Front Runner',
                'sort_order' => 1,
                'build_notes' => [
                    'target_stats' => '1200 / 450+ / 1150+ / 400+ / 1100+',
                    'mandatory_skills' => ['Angling and Scheming', 'Groundwork', 'Sprint Corners◯'],
                    'inherited_uniques' => ['Operation: Plan C'],
                    'summary' => 'Premier sprint front runner with lethal corner acceleration unique.',
                ],
            ],
            [
                'event_id' => $capricornCup->id,
                'character_id' => $characters['taiki-shuttle']->id,
                'tier_rank' => 'S+',
                'role' => 'Ace',
                'recommended_style' => 'Pace Chaser',
                'sort_order' => 2,
                'build_notes' => [
                    'target_stats' => '1200 / 450+ / 1100+ / 400+ / 900+',
                    'mandatory_skills' => ['Victory Shot!', 'Sprint Straightaways◯', 'Sprint Corners◯'],
                    'inherited_uniques' => ['Angling and Scheming'],
                    'summary' => "Ideal track layout for Taiki's unique to trigger at the exact acceleration phase.",
                ],
            ],
            [
                'event_id' => $capricornCup->id,
                'character_id' => $characters['oguri-cap']->id,
                'tier_rank' => 'S',
                'role' => 'Ace',
                'recommended_style' => 'Pace Chaser',
                'sort_order' => 3,
                'build_notes' => [
                    'target_stats' => '1150 / 500+ / 1100+ / 400+ / 850+',
                    'mandatory_skills' => ['Gourmet', 'Sprint Corners◯', 'Straightaway Sprint'],
                    'inherited_uniques' => ['Victory Shot!'],
                    'summary' => 'Viable with Sprint factor upgrade, strong closer in the final 200m.',
                ],
            ],
        ];

        foreach ($capricornEntries as $entry) {
            TierEntry::updateOrCreate(
                [
                    'event_id' => $entry['event_id'],
                    'character_id' => $entry['character_id'],
                    'role' => $entry['role'],
                ],
                $entry
            );
        }

        // 6. Changelog entries for CM #22 Aquarius Cup
        $changelogs = [
            [
                'event_id' => $aquariusCup->id,
                'character_id' => $characters['agnes-digital-alter']->id,
                'previous_tier' => 'S+',
                'new_tier' => 'SS',
                'reason' => 'Elevated to SS Apex rank: Late Surger acceleration skills proved overwhelmingly consistent on Tokyo 1600m Dirt.',
                'created_at' => now()->subDays(2),
            ],
            [
                'event_id' => $aquariusCup->id,
                'character_id' => $characters['maruzensky-summer']->id,
                'previous_tier' => null,
                'new_tier' => 'S',
                'reason' => 'Added to S tier after community testing confirmed viability with 10★ Dirt factor conversion.',
                'created_at' => now()->subDay(),
            ],
            [
                'event_id' => $aquariusCup->id,
                'character_id' => $characters['gold-ship']->id,
                'previous_tier' => 'A',
                'new_tier' => 'B',
                'reason' => 'Demoted to B tier as dedicated debuff strategies proved less impactful against dual-lead Front Runner comps.',
                'created_at' => now()->subHours(6),
            ],
        ];

        foreach ($changelogs as $log) {
            Changelog::create($log);
        }
    }
}
