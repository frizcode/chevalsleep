# ChevalSleep v2.0 — Cheval Grand Edition, Dual Theme & Command Center CMS

## 1. Visual Identity & Dual-Theme System (Light & Dark Mode)

Apply the installed **UI/UX Pro Max** and **Addy Osmani Agent Skills** standards to build a signature aesthetic inspired by **Cheval Grand** (_Uma Musume: Pretty Derby_), supporting persistent **Light Mode** and **Dark Mode** across both the Public site and Admin CMS.

### A. Tailwind & Anti-FOUC Setup

1. **`tailwind.config.js`:** Explicitly set `darkMode: 'class'`.
2. **Inline Head Script (`<head>` in both `layouts/public.blade.php` and `layouts/admin.blade.php`):** Add this blocking script before CSS loads to prevent white-flash on reload:
    ```html
    <script>
        if (localStorage.getItem("chevalsleep_theme") === "light") {
            document.documentElement.classList.remove("dark");
        } else {
            document.documentElement.classList.add("dark"); // Default to Admiral Dark Mode
        }
    </script>
    ```
3. **Alpine.js Theme Toggle Button:**
    - Add a clean Sun/Moon icon toggle button in the top navigation bar of both Public and Admin layouts.
    - Toggling switches `document.documentElement.classList.toggle('dark')` and persists `'dark'` or `'light'` to `localStorage.setItem('chevalsleep_theme', ...)`.

### B. Cheval Grand Dual-Palette Tokens

Every public and admin component MUST support both themes cleanly:

- **Dark Mode (`dark:` — Admiral Night):**
    - Page Canvas: `dark:bg-[#090E1A]` (`slate-950`)
    - Elevated Cards / Modals: `dark:bg-[#11192E]` with `dark:border-sky-900/40`
    - Primary Text: `dark:text-slate-100` | Secondary Text: `dark:text-slate-400`
    - Signature Accents: Champagne / Starlight Gold (`dark:text-amber-400`, `dark:border-amber-500/30`) & Cerulean Marine (`dark:text-sky-400`)
- **Light Mode (Default classes — Seafoam & Porcelain Day):**
    - Page Canvas: `bg-slate-50` (`#F8FAFC`)
    - Elevated Cards / Modals: `bg-white` with `border-sky-200/80 shadow-sm shadow-sky-950/5`
    - Primary Text: `text-slate-900` | Secondary Text: `text-slate-600`
    - Signature Accents: Deep Marine Blue (`text-sky-700`, `bg-sky-50`) & Warm Gold (`text-amber-600`, `border-amber-300`)
- **Tier Rank Badges (High Contrast in Both Modes):**
    - `SS`: `bg-gradient-to-r from-amber-400 via-yellow-300 to-rose-400 text-slate-950 font-extrabold shadow-md shadow-amber-500/20`
    - `S+`: `bg-gradient-to-r from-rose-500 to-amber-500 text-white font-bold`
    - `S`: `bg-amber-500 text-slate-950 font-bold`
    - `A`: `bg-indigo-600 text-white font-bold`
    - `B`: `bg-sky-600 text-white font-bold`
    - `C`: `bg-slate-600 text-white font-bold`
- **Typography & Mood:** Clean geometric sans-serif (`Plus Jakarta Sans` / `Inter`) paired with monospace (`JetBrains Mono` / `font-mono`) for stat numbers, calm oceanic atmosphere, and subtle marine cap/feather SVG brand emblem.

---

## 2. Database Schema & Migrations (`database/migrations/`)

Create a new migration to expand `tier_entries` and `events` so they support `SS` rank, complete CM track conditions, and all structured meta guide sections:

### A. Update `tier_entries` Table

- Expand `tier_rank` enum/column to support: `['SS', 'S+', 'S', 'A', 'B', 'C']`.

### B. Expand `events` Table (Champions Meeting Meta Data)

Add the following columns to `events`:

- `cm_number`: `unsignedSmallInteger`, nullable (e.g., `22` for CM #22, used for descending chronological sorting)
- `event_date`: `date`, nullable (e.g., `2026-11-15`)
- `season`: `enum('Spring', 'Summer', 'Autumn', 'Winter')`, default `'Winter'`
- `weather`: `enum('Sunny', 'Cloudy', 'Rainy', 'Snowy')`, default `'Sunny'`
- `track_condition`: `enum('Firm', 'Good', 'Soft', 'Heavy')`, default `'Firm'`
- `stat_recommendations`: `json`, nullable — stores Minimum vs Recommended stats:
    ```json
    {
        "minimum": {
            "speed": "1150",
            "stamina": "550",
            "power": "1000",
            "guts": "400",
            "wit": "900"
        },
        "recommended": {
            "speed": "1200+",
            "stamina": "650+",
            "power": "1150+",
            "guts": "450+",
            "wit": "1100+"
        },
        "notes": "Requires 1 gold recovery or 650+ Stamina if rushing occurs on Tokyo 1600m."
    }
    ```
- `skill_inheritance`: `json`, nullable — recommended Parent Unique Skills grouped by Running Style:
    ```json
    {
        "Front Runner": [
            "Angling and Scheming (Seiun Sky)",
            "Victoria por plancha (El Condor Pasa)"
        ],
        "Pace Chaser": [
            "Shooting for Victory! (Taiki Shuttle)",
            "Red Shift/LP1211-M (Maruzensky)"
        ],
        "Late Surger": [
            "Let's Pump Some Iron! (Mejiro Ryan)",
            "Red Shift/LP1211-M (Maruzensky)"
        ],
        "End Closer": [
            "Let's Pump Some Iron! (Mejiro Ryan)",
            "Shadow Break (Narita Brian)"
        ]
    }
    ```
- `recommended_skills`: `json`, nullable — core accel/velocity skills grouped by Running Style:
    ```json
    {
        "Front Runner": {
            "core_accel": ["Groundwork", "Early Lead"],
            "velocity_other": ["Mile Corners", "Front Runner Straightaways"]
        },
        "Pace Chaser": {
            "core_accel": ["No Stopping Me!", "Head-On"],
            "velocity_other": ["Speed Star", "Mile Straightaways"]
        },
        "Late Surger": {
            "core_accel": [
                "On Your Left!",
                "No Stopping Me!",
                "Nimble Navigator"
            ],
            "velocity_other": ["Outer Swell", "Furious Feat"]
        },
        "End Closer": {
            "core_accel": ["Straightaway Spurt", "No Stopping Me!"],
            "velocity_other": ["Encroaching Shadow"]
        }
    }
    ```
- `supoka_decks`: `json`, nullable — array of recommended deck templates:
    ```json
    [
        {
            "archetype": "2 Speed / 2 Power / 2 Wit (Standard Ace)",
            "cards": [
                "SSR Kitasan Black (Spd)",
                "SSR Agnes Tachyon (Spd)",
                "SSR El Condor Pasa (Pwr)",
                "SR Daiwa Scarlet (Pwr)",
                "SSR Fine Motion (Wit)",
                "SSR Nice Nature (Wit)"
            ],
            "notes": "Ideal for hitting 1200 Speed / 1100+ Power / 1100+ Wit without Stamina cards."
        }
    ]
    ```
- `team_compositions`: `json`, nullable — array of recommended 3-Uma team setups:
    ```json
    [
        {
            "name": "Double Front + Late Ace",
            "lineup": [
                "Smart Falcon (Front Ace)",
                "Seiun Sky (Front Ace/Pace)",
                "Agnes Digital (Late Ace)"
            ],
            "strategy": "Denies solo Front Runner Angling & Scheming while Digital cleans up from behind."
        }
    ]
    ```

Update `App\Models\Event` `$fillable` and `$casts` to cast all 5 JSON columns (`stat_recommendations`, `skill_inheritance`, `recommended_skills`, `supoka_decks`, `team_compositions`) as `'array'`, and ensure cache invalidation hooks flush event caches when updated.

---

## 3. Public Navigation & Pages

### A. Global Public Layout (`resources/views/layouts/public.blade.php`)

Persistent top navbar supporting Light/Dark mode:

1. **Brand Identity:** **ChevalSleep** with custom marine/feather SVG emblem + _Uma Musume Global Meta_ subtitle.
2. **Navigation Menu:**
    - **Home** (`/` -> `HomeController@index`, `name: home`)
    - **Latest CM** (`/cm` -> redirects to active CM `cm.show`, or lists CMs)
    - **Uma Guides (Future Wiki Placeholders with clean "Coming Soon" views):**
        - `Uma List` (`/guides/umas`, `name: guides.umas`)
        - `Support Cards` (`/guides/supoka`, `name: guides.supoka`)
        - `Skills & Mechanics` (`/guides/skills`, `name: guides.skills`)
3. **Right Utility Controls:**
    - **Light / Dark Mode Toggle** (Sun/Moon icon button)
    - **Admin CMS Link:** Shows `Admin CMS` pill if authenticated, or subtle `Staff Login` link if guest.

### B. Home Menu (`GET /` -> `resources/views/public/home.blade.php`)

1. **Hero Spotlight (Latest / Active CM):**
    - Cheval Grand-styled banner highlighting the currently active CM (e.g., `CM #22 Aquarius Cup`).
    - Displays full track conditions (`Tokyo • Mile 1600m • Dirt • Winter • Sunny • Firm`), preview badges of top `SS` / `S+` Umas, and primary CTA: **"Open Full CM Meta Guide →"**.
2. **CM Archive Grid (Latest to Oldest):**
    - Client-side Alpine.js filters by Distance (`All`, `Sprint`, `Mile`, `Medium`, `Long`) and Surface (`All`, `Turf`, `Dirt`).
    - Lists all CMs ordered descending from newest (`cm_number` DESC / `event_date` DESC) to oldest, showing status badge (`ACTIVE` vs `ARCHIVE`), track conditions, and ranked Uma count.
    - Clicking any card opens `/cm/{event:slug}`.
3. **Future Encyclopedia Teaser Grid:**
    - 3-column preview cards linking to `/guides/umas`, `/guides/supoka`, and `/guides/skills`.

### C. CM Meta Detail Page (`GET /cm/{event:slug}` -> `CmController@show`, `name: cm.show`)

Render in this **exact top-to-bottom vertical sequence**:

1. **Section 1 — CM Header & Track Conditions:**
    - Event Title (`CM #22 Aquarius Cup`), Edition Badge (`CM #22`), Date, and structured condition pills: **Racecourse**, **Distance Type & Meters**, **Surface**, **Season**, **Weather**, and **Track Condition**.
2. **Section 2 — Tier List by Running Style (`SS` down to `C`):**
    - Interactive Alpine.js style filter tabs: **All Styles** | **Front Runner** | **Pace Chaser** | **Late Surger** | **End Closer** (plus Search input & Role filter `All` / `Ace` / `Parent` / `Debuffer`).
    - Displays horizontal tier rows ordered strictly from **`SS` → `S+` → `S` → `A` → `B` → `C`**.
    - Clicking any Uma card opens the **Individual Build Notes Modal** (target stats, mandatory skills, inherited uniques, summary).
3. **Section 3 — Minimum & Recommended Stats:**
    - Clean comparison table/cards showing **Minimum Target** vs **Recommended Target** for all 5 stats (`Speed`, `Stamina`, `Power`, `Guts`, `Wit`) plus stamina/guts threshold notes.
4. **Section 4 — Skill Inheritance (Parent Unique Skills):**
    - 4-column responsive grid (one card per Running Style: `Front Runner`, `Pace Chaser`, `Late Surger`, `End Closer`) listing priority inherited unique skills.
5. **Section 5 — Recommended Skills per Running Style:**
    - Tabbed or 4-card breakdown separating **Core Acceleration Skills** vs **Velocity & Recovery Skills** for each Running Style (`Front Runner`, `Pace Chaser`, `Late Surger`, `End Closer`).
6. **Section 6 — Recommended Support Card (Supoka) Decks:**
    - Deck template cards showing the stat ratio header (e.g., `2 Speed / 2 Power / 2 Wit`), 6 recommended cards as styled pills, and deck-building notes.
7. **Section 7 — Recommended Team Compositions:**
    - Bottom showcase cards detailing 3-Uma synergy lineups (e.g., `Double Front + Late Ace`) and tactical win conditions.
8. **Meta Changelog Accordion:**
    - Collapsible history log of tier movements for that CM.

---

## 4. Modern Admin CMS Redesign (`resources/views/layouts/admin.blade.php` & `resources/views/admin/`)

Redesign the Admin panel into a sleek **ChevalSleep Command Center** supporting both Light and Dark mode:

### A. Sidebar + Topbar Shell (`layouts/admin.blade.php`)

- **Responsive Left Sidebar (Fixed on Desktop, Slide-over Drawer on Mobile via Alpine.js):**
    - Brand header with ChevalSleep emblem + `ADMIN CMS` gold badge.
    - Categorized navigation with active state highlighting (`request()->routeIs(...)`):
        - _Overview:_ **Dashboard** (`admin.dashboard`)
        - _Meta Management:_ **Events & CM Guides** (`admin.events.*`), **Uma Characters** (`admin.characters.*`)
        - _Quick Shortcut:_ **Active CM Tier Builder** (direct link to `admin.events.tiers.index` for the active CM)
    - Sidebar footer with logged-in user info (`Admin ChevalSleep`) + Logout button.
- **Sticky Top Header Bar:**
    - Mobile hamburger trigger, page title/breadcrumb slot, **Light/Dark Mode Toggle**, and **"View Live Site ↗"** button opening `/` in a new tab.
- **Floating Toast Notification:**
    - Sleek auto-dismissing Alpine.js toast component (`session('success')` / `session('error')`) in the bottom-right corner.

### B. Redesigned Admin Dashboard (`admin/dashboard.blade.php`)

1. **Hero Banner:** Highlights the active CM (`CM #22 Aquarius Cup`), track pills, and quick buttons: **"Manage Tier List"** and **"Edit CM Meta Guide"**.
2. **4 KPI Metric Cards (with SVG icons & tinted badges):**
    - Active Event Ranked Umas, Total Uma Characters, Total CM Editions, and Total Meta Changelogs.
3. **Two-Column Workspace:**
    - **Left:** Tier Distribution bar breakdown (`SS` to `C`) for the active CM + Quick Actions.
    - **Right:** Recent Meta Changelogs feed (`previous_tier → new_tier` with character avatar/initials and relative time).

### C. Upgraded Events, Meta Guide Editor & Tier Builder

1. **Events Index (`admin/events/index.blade.php`):**
    - Modern table/card list showing CM Number, Title, Track/Season/Weather pills, `LIVE` pulsing emerald badge vs `Archive`, and clear buttons: **Tiers**, **Meta Guide**, **Set Active**, **Edit**, **Delete**.
2. **CM Meta Guide & Event Editor (`admin/events/create.blade.php` & `edit.blade.php`):**
    - Structured tabbed or sectioned form with Alpine.js dynamic repeater rows for **Supoka Decks** and **Team Compositions**, plus clean inputs for Track Conditions, Min/Recommended 5-Stats, Skill Inheritance per style, and Recommended Skills per style.
3. **Characters Index (`admin/characters/index.blade.php`):**
    - Instant Alpine.js search & running-style filter bar above the character grid/table, displaying avatar previews, rarity stars, and running style badges.
4. **Tier Builder (`admin/events/tiers.blade.php`):**
    - 2-column layout on desktop:
        - **Left/Main:** Live preview of the Tier Board (`SS` down to `C`) with quick **Edit** modal and **Delete** button.
        - **Right Sticky Panel:** Compact **"Add Uma to Tier"** form with `SS`–`C` rank selector, Role, Style, `build_notes` fields, and `changelog_reason`.

---

## 5. Seeder & Execution Checklist

1. **Database Migration:** Create and run the new migration (`php artisan migrate`) updating `tier_entries` (`SS` rank) and `events` (conditions + 5 JSON meta columns).
2. **Update `DatabaseSeeder`:** Populate complete 7-section sample data for `CM #22 Aquarius Cup` (active) and `CM #21 Capricorn Cup` (archived), including `SS` tier entries, stat targets, skill inheritance, style skills, supoka decks, and team comps. Run `php artisan db:seed` (or `php artisan migrate:fresh --seed`).
3. **Public & Admin Implementation:** Build `HomeController`, `CmController`, placeholder guide routes, dual-theme `layouts/public.blade.php` & `layouts/admin.blade.php`, and all upgraded Admin views.
4. **Compile & Verify:** Run `php artisan cache:clear`, `php artisan view:clear`, and `npm run build` to verify zero compilation or runtime errors.

---

## 6. GameTora Asset Integration (Uma, Skills & Support Cards)

Enable seamless asset sourcing from **GameTora (`gametora.com`)** for Uma avatars, Skill icons, and Support Card artworks across both the Admin CMS and Public views.

### A. Backend Asset Fetcher Service (`app/Services/GameToraAssetService.php`)

Create a dedicated service class to handle external GameTora image URLs safely:

- **Method `importFromUrl(?string $url, string $folder = 'uma'): ?string`:**
    1. Validates that `$url` is a valid image URL (e.g., from `gametora.com` or standard `.webp`/`.png`/`.jpg` URL).
    2. Uses Laravel `Http::timeout(10)->withHeaders(['Referer' => 'https://gametora.com/'])->get($url)` to fetch the image binary cleanly without hotlink rejection.
    3. Generates a clean filename (e.g., `{$folder}/" . Str::random(12) . '.' . $extension`) and saves it to `Storage::disk('public')`.
    4. Returns the relative storage path (e.g., `uma/abc123def456.webp`) so it resolves via `Storage::url(...)` identically to manual uploads.
    5. If `$url` is already a local path or download fails gracefully, returns the fallback URL/path without crashing form submission.

### B. Dual Input Support (File Upload OR GameTora Image URL)

1. **Characters (`Admin\CharacterController` & `admin/characters/create.blade.php` / `edit.blade.php`):**
    - Provide **two** clean options side-by-side in the form:
        - **Option 1 — Paste GameTora Image URL:** Text input (`gametora_avatar_url`) where the admin can paste a direct image URL copied from GameTora (e.g., `https://gametora.com/images/umamusume/characters/chara_stand_1015_101501.png` or icon URL). When submitted, `GameToraAssetService` automatically downloads and stores it into `storage/app/public/characters/` and saves the path to `avatar_webp`.
        - **Option 2 — Manual File Upload:** Existing `avatar` file input (`webp/png/jpg`).
    - Show a live Alpine.js image preview thumbnail immediately when pasting a URL or selecting a file.

2. **Support Card (Supoka) Decks with Card Artworks (`events.supoka_decks` JSON):**
    - Upgrade each card entry inside `supoka_decks[*].cards` from a plain string to a structured object supporting optional GameTora card icons:

```json
{
    "archetype": "2 Speed / 2 Power / 2 Wit (Standard Ace)",
    "cards": [
        {
            "name": "SSR Kitasan Black (Speed)",
            "icon": "supoka/kitasan-spd.webp"
        }
    ],
    "notes": "Ideal for hitting 1200 Speed / 1100+ Power / 1100+ Wit."
}
```

- In the Admin CM Meta Editor (`admin/events/edit.blade.php`), each Supoka card slot includes a **Card Name** input and a **GameTora Image URL** input (auto-imported to `storage/app/public/supoka/` via `GameToraAssetService`).
- On the Public CM page (`cm/show.blade.php`), render each Supoka card as a sleek mini card thumbnail with its GameTora artwork (or styled rarity placeholder if no icon is set) and card name.

3. **Recommended Skills & Inherited Uniques with Skill Icons (`events.skill_inheritance` & `events.recommended_skills` JSON):**
    - Support structured skill items `{ "name": "No Stopping Me!", "icon": "skills/accel-gold.webp" }` (while remaining backwards-compatible with plain string arrays in Blade so existing seeded entries still render cleanly).
    - In the Admin CM Meta Editor, allow pasting a **GameTora Skill Icon URL** next to each skill name (auto-imported to `storage/app/public/skills/` via `GameToraAssetService`).
    - On the Public CM page (`cm/show.blade.php`), display the crisp 28x28px / 32x32px GameTora skill icon badge right beside each skill name in **Section 4 (Skill Inheritance)**, **Section 5 (Recommended Skills)**, and the **Character Build Notes Modal**.

### C. Quick Asset Importer Helper in Admin CMS

- Add a small reusable **Asset URL Importer** endpoint (`POST /admin/assets/import-url` -> `Admin\AssetImportController@store`) so Alpine.js repeaters (Supoka Decks & Skills) can paste a GameTora image link, click **"Fetch"**, and immediately see the imported local thumbnail preview before saving the form.
