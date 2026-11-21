# ChevalSleep v3.0 — Authentic Cheval Grand Theme, Multi-Role Auth, Bilingual (EN/ID) & Master Game Database

## 1. Authentic Cheval Grand Visual Identity & Typography

Apply the installed **UI/UX Pro Max** and **Addy Osmani Agent Skills** standards to refine the entire design system based directly on **Cheval Grand's** character design:

### A. Character-Derived Color Palette

- **Midnight Starry Cape (Dark Mode Canvas & Elevated Surfaces):**
    - Primary Canvas: `#080E1E` (Deep Starry Navy)
    - Card / Panel Surface: `#0F182E` with subtle starry sky-blue borders (`border-sky-800/40`) and an optional subtle radial starlight-cyan gradient (`from-sky-950/50 via-[#0F182E] to-[#080E1E]`) on hero banners echoing the inside of her cape.
- **Admiral Jacket White (Light Mode Canvas & Surfaces):**
    - Primary Canvas: `#F4F7FB` (Crisp Naval Porcelain)
    - Card / Panel Surface: `#FFFFFF` with clean borders (`border-sky-200/80`) and subtle charcoal-navy headers (`#111827`) echoing her high-waisted shorts and striped knee socks.
- **Signature Accents (Shared Across Light & Dark Modes):**
    - **Ice Sky / Collar Cyan (`#38BDF8` / `#7DD3FC`):** Active navigation indicators, running style pills, hover highlights, and light-mode soft badges (`bg-sky-100 text-sky-800`).
    - **Anchor & Clasp Brass Gold (`#D4AF37` / `#EAB308`):** Primary CTAs, `SS` / `S+` tier highlights, active CM badges, and subtle 1px top/left accent trims (`border-amber-400/50`).
    - **Charcoal Shorts Dark (`#111827` / `#1E293B`):** High-contrast structural header strips and table headers.
- **Strict Sans-Serif Typography:**
    - Use **`Inter`** (weights 400, 500, 600, 700, 800) across all headings, navigation, buttons, and body text (`font-sans`).
    - Use **`JetBrains Mono`** (`font-mono tabular-nums`) strictly for stat numbers, CM edition codes, and dates.

### B. Full Mobile-First Responsiveness

- Every page (Public Home, CM Meta Detail, Wiki Databases, Login/Register, and Admin Command Center) MUST adapt cleanly across mobile (`360px+`), tablet (`768px+`), and desktop (`1280px+`):
    - Collapsible mobile hamburger drawer in the public navbar (including Language Switcher, Light/Dark Toggle, and Auth links).
    - Horizontal scroll wrappers (`overflow-x-auto`) for dense stat tables and tier rows on small screens so layout never overflows horizontally.

---

## 2. Bilingual System (English & Bahasa Indonesia) with Global Game Terms

Implement a seamless **EN / ID** language switcher stored in session (`session('locale')`, defaulting to `'en'` or `'id'`) via `GET /lang/{locale}` (`LocaleController@switch`) and a `SetLocale` middleware registered in `bootstrap/app.php`.

### Strict Translation Boundary Rule:

1. **Translated UI (`lang/en/ui.php` & `lang/id/ui.php`):**
    - Translate all website chrome, navigation links, buttons, filter labels, section titles, stat table headers, auth screens, and explanatory guide paragraphs between **English** and **Bahasa Indonesia** (e.g., _Home / Beranda_, _Tier List / Daftar Tier_, _Minimum Stats / Stat Minimal_, _Recommended Stats / Stat Rekomendasi_, _Skill Inheritance / Warisan Skill_, _Support Card Decks / Rekomendasi Deck Supoka_, _Team Compositions / Komposisi Tim_).
2. **Untranslated Uma Musume Global Terminology (ALWAYS English):**
    - NEVER translate Uma names (`Cheval Grand`, `Agnes Digital`, `Smart Falcon`), Skill names (`No Stopping Me!`, `Angling and Scheming`, `Professor of Curvature`), Support Card names (`SSR Kitasan Black`, `SSR Fine Motion`), Running Styles (`Front Runner`, `Pace Chaser`, `Late Surger`, `End Closer`), Distances (`Sprint`, `Mile`, `Medium`, `Long`), Surfaces (`Turf`, `Dirt`), or Roles (`Ace`, `Parent`, `Debuffer`). They must remain in official **Global Server English** regardless of whether the site language is set to `EN` or `ID`.

---

## 3. Multi-Role Authentication (`admin` & `user`)

Upgrade Laravel Breeze authentication so both **Admins** and regular **Users** can register and log in cleanly:

1. **Database Update (`users` table migration):**
    - Add `role`: `enum('admin', 'user')`, default `'user'`.
    - Update `DatabaseSeeder` to create:
        - Admin account: `admin@chevalsleep.test` / `password` (`role = 'admin'`)
        - Demo User account: `trainer@chevalsleep.test` / `password` (`role = 'user'`)
2. **Role Middleware & Redirects:**
    - Create `EnsureUserIsAdmin` middleware (`admin` alias in `bootstrap/app.php`) protecting all `/admin/*` routes (returns `403` or redirects non-admin users to `route('home')`).
    - Public `/login` and `/register` remain open for regular Trainers (`role = 'user'`).
    - After login: if `auth()->user()->role === 'admin'`, redirect to `route('admin.dashboard')`; if `role === 'user'`, redirect to `route('home')`.
3. **Navbar Auth UI & User Features:**
    - **Guest:** Shows clean `Login` and `Register` buttons in the navbar.
    - **Authenticated `user`:** Shows Trainer profile dropdown (`My Bookmarks / Saved CMs`, `Profile Settings`, `Logout`). Allow logged-in users to bookmark/favorite CMs (`event_user` pivot table) and view their saved CMs on a clean `/my-bookmarks` page.
    - **Authenticated `admin`:** Shows gold `Admin CMS` button + `Logout` in the navbar.

---

## 4. Master Database: Uma, Skills & Support Cards (with GameTora Importer)

Build full CRUD in the Admin CMS and live searchable public database pages for **Uma Characters**, **Skills**, and **Support Cards (Supoka)**, all integrated with `App\Services\GameToraAssetService`:

### A. Database Tables & Models

1. **`characters` (Uma Database — expand existing table if needed):**
    - `name`, `variant_title`, `slug`, `avatar_webp`, `default_running_style`, `rarity` (`1`–`3`), `aptitude_notes` (nullable JSON/string for Turf/Dirt & distance highlights).
2. **`skills` (`App\Models\Skill`):**
    - `id`, `name` (Global English, e.g., `No Stopping Me!`), `slug` (unique), `icon_webp` (nullable, stored in `storage/app/public/skills`), `rarity` (`enum('Normal', 'Rare', 'Unique', 'Inherited')`), `skill_type` (`enum('Acceleration', 'Velocity', 'Recovery', 'Debuff', 'Passive')`), `running_style` (`enum('Universal', 'Front Runner', 'Pace Chaser', 'Late Surger', 'End Closer')`), `distance_type` (`enum('Universal', 'Sprint', 'Mile', 'Medium', 'Long')`), `activation_condition` (text, nullable — e.g., _"Final corner, positioned 4th–9th"_), `description` (text, nullable), `timestamps`.
3. **`support_cards` (`App\Models\SupportCard`):**
    - `id`, `name` (Global English, e.g., `Kitasan Black`), `epithet` (nullable, e.g., `Fire at My Heels`), `slug` (unique), `card_webp` (nullable, stored in `storage/app/public/supoka`), `rarity` (`enum('SSR', 'SR', 'R')`), `card_type` (`enum('Speed', 'Stamina', 'Power', 'Guts', 'Wit', 'Friend', 'Group')`), `specialty_priority` (`unsignedSmallInteger`, nullable), `key_skills` (json, nullable — array of Global skill names), `summary` (text, nullable), `timestamps`.

### B. Admin CMS Master Database Management (`/admin`)

Add sidebar links under **Master Database** in `layouts/admin.blade.php`:

- **Uma Characters (`/admin/characters`):** Full CRUD with GameTora Image URL auto-import OR manual file upload + live preview.
- **Skills Database (`/admin/skills`):** Full CRUD with GameTora Skill Icon URL auto-import OR manual file upload, rarity badge (`Rare` Gold, `Unique` Rainbow, `Normal` White), skill type, and activation condition.
- **Support Cards (`/admin/support-cards`):** Full CRUD with GameTora Card Art URL auto-import OR manual file upload, rarity (`SSR`/`SR`/`R`), type (`Speed`/`Stamina`/`Power`/`Guts`/`Wit`/`Friend`/`Group`), and key gold skills.
- **CM Meta Editor Integration (`admin/events/edit.blade.php`):** Allow admins to either pick existing Skills & Support Cards from the Master Database (auto-filling their names & imported GameTora icons!) OR enter custom entries.

### C. Public Database Pages (Replacing "Coming Soon" Placeholders)

Connect the top navbar links to real, responsive, Alpine.js-filterable public encyclopedias (UI labels bilingual EN/ID, game data in Global English):

- **`/guides/umas` (`Public\DatabaseController@umas`):** Filterable Uma roster by Rarity, Running Style, and Search.
- **`/guides/skills` (`Public\DatabaseController@skills`):** Filterable Skill encyclopedia by Rarity (`Rare`, `Unique`, `Normal`), Type (`Acceleration`, `Velocity`, `Recovery`, `Debuff`), and Running Style, showing GameTora icons and activation conditions.
- **`/guides/supoka` (`Public\DatabaseController@supoka`):** Filterable Support Card gallery by Rarity (`SSR`, `SR`, `R`) and Card Type (`Speed`, `Stamina`, `Power`, `Guts`, `Wit`, `Friend`), displaying GameTora card artworks and key skills.

---

## 5. Seeder & Execution Verification Checklist

1. Create migrations for `users.role`, `event_user` bookmarks, `skills`, and `support_cards`.
2. Update `DatabaseSeeder` with:
    - Admin (`admin@chevalsleep.test`) & User (`trainer@chevalsleep.test`) accounts.
    - Sample Global Skills (`No Stopping Me!`, `Angling and Scheming`, `Let's Pump Some Iron!`, `Red Shift/LP1211-M`, `Professor of Curvature`, `Swinging Maestro`) and Support Cards (`SSR Kitasan Black`, `SSR Fine Motion`, `SSR El Condor Pasa`, `SR Sweep Tosho`, `SSR Super Creek`) so the public database pages are immediately populated.
3. Implement `SetLocale` middleware + `lang/en/ui.php` and `lang/id/ui.php` translation files, ensuring all Uma/Skill/Supoka terms stay in Global English.
4. Apply the Cheval Grand color palette (Starry Cape Navy, Sailor White, Ice Cyan, Anchor Brass Gold) and `Inter` + `JetBrains Mono` typography across all public, auth, and admin views.
5. Run `php artisan migrate:fresh --seed`, `php artisan storage:link`, `php artisan optimize:clear`, and `npm run build` to verify zero errors.
