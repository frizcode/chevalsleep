# ChevalSleep — Master Architectural Blueprint & Agent Execution Guide

## 1. Project Identity & Environment

- **Project Name:** ChevalSleep
- **Local Development URL:** `[http://chevalsleep.test](http://chevalsleep.test)` (Laragon)
- **Database:** MySQL (`chevalsleep`)
- **Installed Stack:** Laravel (PHP 8.2+), Laravel Breeze (Blade stack), Tailwind CSS, Alpine.js, Vite
- **Domain Focus:** Competitive Tier List & Meta Guide for **Uma Musume: Pretty Derby (Global Server)**, specializing in **Champions Meeting (CM)** and **League of Heroes** tournaments.
- **Core Goal:** Deliver an ultra-fast, read-heavy public tier list with instant client-side filtering and detailed build modals, powered by a clean Admin CMS with automatic meta changelog tracking.

---

## 2. Strict Domain Terminology (Uma Musume Global Server)

All database enums, seeders, dropdown options, and UI badges MUST strictly use Global Server English localization:

- **Tier Ranks (Ordered Highest to Lowest):** `S+`, `S`, `A`, `B`, `C`
- **Running Styles:** `Front Runner`, `Pace Chaser`, `Late Surger`, `End Closer`
- **Track Distances:** `Sprint`, `Mile`, `Medium`, `Long`
- **Track Surfaces:** `Turf`, `Dirt`
- **Competitive Roles:** `Ace`, `Parent`, `Debuffer`

---

## 3. Database Schema & Migrations (`database/migrations/`)

Inspect existing migration files in `database/migrations/`. Update or create the 4 core tables below so they match this exact specification:

### Table 1: `characters`

- `id`: `bigIncrements`
- `name`: `string` (e.g., `Mejiro Dober`, `Smart Falcon`, `Agnes Digital`)
- `variant_title`: `string`, nullable (e.g., `Original`, `Alter`, `Summer`, `New Year`)
- `slug`: `string`, unique (e.g., `agnes-digital-alter`)
- `avatar_webp`: `string`, nullable (relative storage path, e.g., `characters/agnes-digital-alter.webp`)
- `default_running_style`: `enum('Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer')`
- `rarity`: `unsignedTinyInteger`, default `3` (valid values: `1`, `2`, `3`)
- `timestamps`

### Table 2: `events`

- `id`: `bigIncrements`
- `title`: `string` (e.g., `CM #22 Aquarius Cup`)
- `slug`: `string`, unique (e.g., `cm-22-aquarius-cup`)
- `track_name`: `string` (e.g., `Tokyo 1600m`)
- `distance_type`: `enum('Sprint', 'Mile', 'Medium', 'Long')`
- `distance_meters`: `unsignedSmallInteger` (e.g., `1600`)
- `surface`: `enum('Turf', 'Dirt')`
- `is_active`: `boolean`, default `false` (marks the default event displayed on `/`)
- `timestamps`

### Table 3: `tier_entries`

- `id`: `bigIncrements`
- `event_id`: `foreignId('event_id')->constrained()->cascadeOnDelete()`
- `character_id`: `foreignId('character_id')->constrained()->cascadeOnDelete()`
- `tier_rank`: `enum('S+', 'S', 'A', 'B', 'C')`
- `role`: `enum('Ace', 'Parent', 'Debuffer')`, default `'Ace'`
- `recommended_style`: `enum('Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer')`
- `build_notes`: `json`, nullable
- `sort_order`: `unsignedInteger`, default `0`
- `timestamps`
- **Unique Index:** `unique(['event_id', 'character_id', 'role'])`

#### Required JSON Structure for `tier_entries.build_notes`:

```json
{
    "target_stats": "1200 / 600+ / 1100+ / 450+ / 1000+",
    "mandatory_skills": [
        "No Stopping Me!",
        "Nimble Navigator",
        "Aquarius Straightaways"
    ],
    "inherited_uniques": ["Red Shift/LP1211-M", "Shooting for Victory!"],
    "summary": "Premier Dirt Mile Late Surger/Pace Chaser with high acceleration consistency on Tokyo 1600m."
}
```

### Table 4: `changelogs`

- `id`: `bigIncrements`
- `event_id`: `foreignId('event_id')->constrained()->cascadeOnDelete()`
- `character_id`: `foreignId('character_id')->constrained()->cascadeOnDelete()`
- `previous_tier`: `string`, nullable (`null` indicates newly added to the tier list)
- `new_tier`: `string`
- `reason`: `text`
- `timestamps`

---

## 4. Eloquent Models & Business Logic (`app/Models/`)

### `App\Models\Character`

- **`$fillable`:** `['name', 'variant_title', 'slug', 'avatar_webp', 'default_running_style', 'rarity']`
- **Relationships:** `tierEntries()` (`hasMany(TierEntry::class)`), `changelogs()` (`hasMany(Changelog::class)`)
- **Accessor `full_name`:** Returns `"{$this->name}" . ($this->variant_title && $this->variant_title !== 'Original' ? " ({$this->variant_title})" : "")`
- **Accessor `avatar_url`:** Returns `Storage::url($this->avatar_webp)` if `avatar_webp` exists in public disk, otherwise `null` (handled gracefully in Blade with a styled initials badge). ### `App\Models\Event` - **`$fillable`:** `['title', 'slug', 'track_name', 'distance_type', 'distance_meters', 'surface', 'is_active']`
- **`$casts`:** `['is_active' => 'boolean']` - **Relationships:** - `tierEntries()` -> `hasMany(TierEntry::class)->orderBy('sort_order')->orderBy('id')` - `changelogs()` -> `hasMany(Changelog::class)->latest()` - **Helper Method `activate()`:** Wraps in a DB transaction: sets `is_active = false` on all other events, sets `is_active = true` on `$this`, and flushes event caches.

### `App\Models\TierEntry`

- **`$fillable`:** `['event_id', 'character_id', 'tier_rank', 'role', 'recommended_style', 'build_notes', 'sort_order']` - **`$casts`:** `['build_notes' => 'array']`
- **Relationships:** `event()` (`belongsTo(Event::class)`), `character()` (`belongsTo(Character::class)`)

### `App\Models\Changelog`

- **`$fillable`:** `['event_id', 'character_id', 'previous_tier', 'new_tier', 'reason']` - **Relationships:** `event()` (`belongsTo(Event::class)`), `character()` (`belongsTo(Character::class)`) ### Cache Invalidation Strategy - Cache public tier list data per event slug using `Cache::remember("chevalsleep:event:{$slug}", 3600, ...)` and the active event list using `Cache::remember('chevalsleep:events:all', 3600, ...)`.
- Register model `saved` and `deleted` hooks (in `AppServiceProvider::boot()` or dedicated Observers) on `Event`, `Character`, `TierEntry`, and `Changelog` to automatically forget these cache keys whenever data is modified in the Admin CMS.

---

## 5. Initial Database Seeder (`database/seeders/DatabaseSeeder.php`)

Populate realistic starter data so the application is immediately testable and visually rich:

1. **Default Admin User:**
    - Name: `Admin ChevalSleep`
    - Email: `admin@chevalsleep.test`
    - Password: `password`
2. **Events (2 events):**
    - **Active Event (`is_active = true`):** `CM #22 Aquarius Cup` (`slug: cm-22-aquarius-cup`, `track_name: Tokyo 1600m`, `distance_type: Mile`, `distance_meters: 1600`, `surface: Dirt`)
    - **Past Event (`is_active = false`):** `CM #21 Capricorn Cup` (`slug: cm-21-capricorn-cup`, `track_name: Chukyo 1200m`, `distance_type: Sprint`, `distance_meters: 1200`, `surface: Turf`)
3. **Characters (10 characters):**
    - `Agnes Digital` (`Alter`, `Late Surger`, 3★)
    - `Smart Falcon` (`Original`, `Front Runner`, 3★)
    - `Oguri Cap` (`Original`, `Pace Chaser`, 3★)
    - `Taiki Shuttle` (`Original`, `Pace Chaser`, 3★)
    - `El Condor Pasa` (`Original`, `Pace Chaser`, 2★)
    - `Gold Ship` (`Original`, `End Closer`, 2★)
    - `Maruzensky` (`Summer`, `Front Runner`, 3★)
    - `Mejiro Dober` (`Original`, `Late Surger`, 3★)
    - `Seiun Sky` (`Original`, `Front Runner`, 3★)
    - `Haru Urara` (`New Year`, `Late Surger`, 3★)
4. **Tier Entries & Changelogs:**
    - Assign 8–10 entries across `S+`, `S`, `A`, `B` for `CM #22 Aquarius Cup` with complete `build_notes` JSON (`target_stats`, `mandatory_skills`, `inherited_uniques`, `summary`) and varying roles (`Ace`, `Parent`, `Debuffer`).
    - Add 3 entries for `CM #21 Capricorn Cup` so the Event Switcher dropdown can be tested.
    - Add 3 realistic `Changelog` entries for `CM #22 Aquarius Cup`.

---

## 6. Routing & Controllers

### Public Routes (`routes/web.php`)

- `GET /` -> `App\Http\Controllers\Public\TierListController@index` (`name: home`)
    - Accepts optional query string `?event={slug}`. If omitted, loads the event where `is_active = true` (fallback to latest event).
    - Passes serialized JSON array of tier entries, grouped tier ranks (`['S+', 'S', 'A', 'B', 'C']`), event list, active event metadata, and recent changelogs to `resources/views/public/index.blade.php`.

### Admin CMS Routes (`routes/web.php` — Middleware: `['auth']`, Prefix: `/admin`, Name Prefix: `admin.`)

Keep existing Laravel Breeze `/login`, `/register`, `/profile`, and `/dashboard` (redirect `/dashboard` to `route('admin.dashboard')`).

- `GET /admin` -> `Admin\DashboardController@index` (`admin.dashboard`)
- `Resource /admin/events` -> `Admin\EventController` (`admin.events.*`)
- `POST /admin/events/{event}/activate` -> `Admin\EventController@activate` (`admin.events.activate`)
- `Resource /admin/characters` -> `Admin\CharacterController` (`admin.characters.*`)
- `GET /admin/events/{event}/tiers` -> `Admin\TierBuilderController@index` (`admin.events.tiers.index`)
- `POST /admin/events/{event}/tiers` -> `Admin\TierBuilderController@store` (`admin.events.tiers.store`)
- `PUT /admin/events/{event}/tiers/{tierEntry}` -> `Admin\TierBuilderController@update` (`admin.events.tiers.update`)
- `DELETE /admin/events/{event}/tiers/{tierEntry}` -> `Admin\TierBuilderController@destroy` (`admin.events.tiers.destroy`)

---

## 7. Frontend & Admin UI/UX Specifications

### A. Public Tier List Page (`resources/views/public/index.blade.php`)

- **Theme:** Sleek dark-mode competitive gaming aesthetic (`bg-slate-950`, card surfaces `bg-slate-900/90 border-slate-800`, crisp typography, responsive mobile-first layout).
- **Top Navigation Bar:**
    - Brand logo/title: **ChevalSleep** with subtle subtitle _Uma Musume Global Meta_.
    - **Event Switcher Dropdown:** Switches between tournaments (`?event=slug`).
    - **Admin Link:** Shows `Admin CMS` button if authenticated, or subtle `Staff Login` link if guest.
- **Event Hero Banner:**
    - Displays Event Title (e.g., `CM #22 Aquarius Cup`), Track Name (`Tokyo 1600m`), and styled pills for **Distance Type** (`Mile`), **Distance** (`1600m`), and **Surface** (`Dirt`).
- **Alpine.js Interactive Filter Toolbar (Zero Page Reload):**
    - Search input box (filters by character `name` or `variant_title` in real time).
    - Running Style filter pills: `All`, `Front Runner`, `Pace Chaser`, `Late Surger`, `End Closer`.
    - Role filter pills: `All`, `Ace`, `Parent`, `Debuffer`.
    - "Reset Filters" button when any filter is active.
- **Tier List Board (`S+`, `S`, `A`, `B`, `C`):**
    - Distinct tier rank badge colors:
        - `S+`: Rose/Crimson-Gold gradient (`from-rose-600 to-amber-500`)
        - `S`: Amber/Gold (`bg-amber-500 text-slate-950`)
        - `A`: Violet/Purple (`bg-violet-600 text-white`)
        - `B`: Sky/Blue (`bg-sky-600 text-white`)
        - `C`: Slate/Neutral (`bg-slate-700 text-slate-200`)
    - Each character card displays: Avatar image (or styled 2-letter monogram fallback if no image uploaded), Character Name, Variant badge (if not `Original`), Recommended Running Style pill, and Role badge (`Ace`, `Parent`, `Debuffer`).
    - Empty tiers automatically hide or show a muted _"No entries matching current filters"_ state cleanly.
- **Character Build Notes Modal (Alpine.js):**
    - Triggered upon clicking any character card.
    - Displays Character header, Tier Rank, Role, Recommended Style, **Target Stats** highlighted bar, **Mandatory Skills** tags, **Inherited Unique Skills** tags, and **Meta Analysis Summary** paragraph.
    - Closes on Escape key, backdrop click, or close button.
- **Meta Changelog Feed:**
    - Clean section below the tier board displaying recent tier movements for the selected event (e.g., `[New -> S+]` or `[A -> S]`, character name, reason, and `diffForHumans()` timestamp).

### B. Admin CMS Views (`resources/views/admin/`)

Use a clean, cohesive dark/neutral admin layout (`resources/views/layouts/admin.blade.php`) with navigation links for **Dashboard**, **Events**, **Characters**, and **View Public Site**:

1. **Dashboard (`admin/dashboard.blade.php`):**
    - Summary metric cards: Active Event Name, Total Characters, Total Tier Entries in Active Event, Recent Changelogs count.
    - Quick action buttons: _"Manage Active Event Tiers"_, _"Add New Character"_, _"Create New Event"_.
2. **Events Management (`admin/events/index.blade.php`, `create.blade.php`, `edit.blade.php`):**
    - Table listing all events with track specs, active status badge, **"Set Active"** button, **"Manage Tiers"** button, Edit, and Delete.
    - Auto-generate `slug` from `title` if left blank.
3. **Characters Management (`admin/characters/index.blade.php`, `create.blade.php`, `edit.blade.php`):**
    - Grid or table with avatar preview, full name, variant, default running style, rarity stars, Edit, and Delete.
    - File upload validation (`image|mimes:webp,png,jpg,jpeg|max:2048`), stored in `storage/app/public/characters` (delete old file on update/delete).
4. **Tier Builder (`admin/events/tiers.blade.php`):**
    - Single-page workspace per event displaying current entries grouped by Tier (`S+` to `C`) alongside an **"Add Character to Tier List"** form and inline/modal **"Edit Entry"** form.
    - Form fields for `build_notes` MUST be separate human-friendly inputs:
        - `target_stats` (text input, e.g., `1200 / 600+ / 1100+ / 450+ / 1000+`)
        - `mandatory_skills` (text input, comma-separated, converted to JSON array on save)
        - `inherited_uniques` (text input, comma-separated, converted to JSON array on save)
        - `summary` (textarea)
    - **Automatic Changelog Recording:** Include an optional `changelog_reason` text input on both Add and Edit forms.
        - When creating a new `TierEntry` and `changelog_reason` is provided (or by default with `"Added to {$tier_rank} tier"`), create a `Changelog` row with `previous_tier = null` and `new_tier = $tier_rank`.
        - When updating an existing `TierEntry` and `tier_rank` changed (or `changelog_reason` is filled), automatically create a `Changelog` row recording `previous_tier`, `new_tier`, and `reason`.

---

## 8. Agent Execution & Verification Checklist

Execute in this exact order and verify each milestone before proceeding:

1. **Inspect & Configure:** Check `database/migrations/` and `app/Models/`. Implement the 4 migrations and 4 models with relationships, accessors, and cache invalidation hooks.
2. **Seed & Storage:** Create `DatabaseSeeder`, run `php artisan migrate:fresh --seed`, and verify `php artisan storage:link` is active.
3. **Admin CMS:** Implement `Admin\DashboardController`, `Admin\EventController`, `Admin\CharacterController`, `Admin\TierBuilderController`, Form Requests, and Blade views under `resources/views/admin/`.
4. **Public Tier List:** Implement `Public\TierListController` and `resources/views/public/index.blade.php` with Alpine.js filtering and build modal.
5. **Compile & Verify:** Run `npm run build` and `php artisan route:list` to confirm zero syntax, Vite compilation, or routing errors.
