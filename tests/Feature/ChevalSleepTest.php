<?php

namespace Tests\Feature;

use App\Models\Character;
use App\Models\Event;
use App\Models\TierEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChevalSleepTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Event $event;
    protected Character $character;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create([
            'email' => 'admin@chevalsleep.test',
        ]);

        $this->event = Event::create([
            'title' => 'CM #22 Aquarius Cup',
            'slug' => 'cm-22-aquarius-cup',
            'track_name' => 'Tokyo 1600m',
            'distance_type' => 'Mile',
            'distance_meters' => 1600,
            'surface' => 'Dirt',
            'is_active' => true,
        ]);

        $this->character = Character::create([
            'name' => 'Agnes Digital',
            'variant_title' => 'Alter',
            'slug' => 'agnes-digital-alter',
            'default_running_style' => 'Late Surger',
            'rarity' => 3,
        ]);
    }

    public function test_public_homepage_renders_active_event_and_tier_entries(): void
    {
        TierEntry::create([
            'event_id' => $this->event->id,
            'character_id' => $this->character->id,
            'tier_rank' => 'S+',
            'role' => 'Ace',
            'recommended_style' => 'Late Surger',
            'sort_order' => 1,
            'build_notes' => [
                'target_stats' => '1200 / 600+ / 1100+ / 450+ / 1000+',
                'mandatory_skills' => ['No Stopping Me!'],
                'inherited_uniques' => ['Red Shift/LP1211-M'],
                'summary' => 'Premier dirt mile ace.',
            ],
        ]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('CM #22 Aquarius Cup');
        $response->assertSee('Tokyo 1600m');
        $response->assertSee('Agnes Digital');
    }

    public function test_consecutive_homepage_requests_hit_cache_without_incomplete_class_error(): void
    {
        // First request populates cache
        $this->get('/')->assertStatus(200);

        // Second request reads from cache
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('CM #22 Aquarius Cup');
    }

    public function test_guest_is_redirected_from_admin(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Admin Dashboard');
    }

    public function test_admin_can_create_event(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/events', [
            'title' => 'CM #23 Taurus Cup',
            'track_name' => 'Tokyo 2400m',
            'distance_type' => 'Medium',
            'distance_meters' => 2400,
            'surface' => 'Turf',
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.events.index'));
        $this->assertDatabaseHas('events', [
            'title' => 'CM #23 Taurus Cup',
            'is_active' => true,
        ]);

        // Prior active event should now be inactive
        $this->assertDatabaseHas('events', [
            'id' => $this->event->id,
            'is_active' => false,
        ]);
    }

    public function test_admin_can_add_character_and_manage_tiers_with_changelog(): void
    {
        // 1. Add tier entry
        $response = $this->actingAs($this->admin)->post("/admin/events/{$this->event->id}/tiers", [
            'character_id' => $this->character->id,
            'tier_rank' => 'S+',
            'role' => 'Ace',
            'recommended_style' => 'Late Surger',
            'sort_order' => 1,
            'target_stats' => '1200 / 600+ / 1100+ / 450+ / 1000+',
            'mandatory_skills' => 'No Stopping Me!, Nimble Navigator',
            'inherited_uniques' => 'Red Shift/LP1211-M',
            'summary' => 'Premier dirt mile ace.',
            'changelog_reason' => 'Placed into S+ apex tier.',
        ]);

        $response->assertRedirect(route('admin.events.tiers.index', $this->event));

        $this->assertDatabaseHas('tier_entries', [
            'event_id' => $this->event->id,
            'character_id' => $this->character->id,
            'tier_rank' => 'S+',
        ]);

        $this->assertDatabaseHas('changelogs', [
            'event_id' => $this->event->id,
            'character_id' => $this->character->id,
            'previous_tier' => null,
            'new_tier' => 'S+',
            'reason' => 'Placed into S+ apex tier.',
        ]);

        $entry = TierEntry::where('event_id', $this->event->id)->first();

        // 2. Update tier entry
        $updateResponse = $this->actingAs($this->admin)->put("/admin/events/{$this->event->id}/tiers/{$entry->id}", [
            'tier_rank' => 'S',
            'role' => 'Ace',
            'recommended_style' => 'Late Surger',
            'sort_order' => 2,
            'target_stats' => '1200 / 600+ / 1100+ / 450+ / 1000+',
            'mandatory_skills' => 'No Stopping Me!',
            'inherited_uniques' => 'Red Shift/LP1211-M',
            'summary' => 'Adjusted to S tier.',
            'changelog_reason' => 'Shifted down due to front runner dominance.',
        ]);

        $updateResponse->assertRedirect(route('admin.events.tiers.index', $this->event));

        $this->assertDatabaseHas('changelogs', [
            'event_id' => $this->event->id,
            'character_id' => $this->character->id,
            'previous_tier' => 'S+',
            'new_tier' => 'S',
            'reason' => 'Shifted down due to front runner dominance.',
        ]);
    }

    public function test_language_switcher_switches_session_locale(): void
    {
        $response = $this->get('/lang/id');
        $response->assertRedirect();
        $response->assertSessionHas('locale', 'id');

        $responseEn = $this->get('/lang/en');
        $responseEn->assertRedirect();
        $responseEn->assertSessionHas('locale', 'en');
    }

    public function test_regular_user_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    public function test_user_can_bookmark_and_unbookmark_cm(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        // Bookmark
        $response = $this->actingAs($user)->post("/cm/{$this->event->slug}/bookmark");
        $response->assertRedirect();
        $this->assertTrue($user->fresh()->bookmarkedEvents->contains($this->event->id));

        // View bookmarks
        $viewResponse = $this->actingAs($user)->get('/my-bookmarks');
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee($this->event->title);

        // Toggle / unbookmark
        $unbookmarkResponse = $this->actingAs($user)->post("/cm/{$this->event->slug}/bookmark");
        $unbookmarkResponse->assertRedirect();
        $this->assertFalse($user->fresh()->bookmarkedEvents->contains($this->event->id));
    }

    public function test_public_guides_pages_are_accessible(): void
    {
        $this->get('/guides/umas')->assertStatus(200);
        $this->get('/guides/skills')->assertStatus(200);
        $this->get('/guides/supoka')->assertStatus(200);
        $this->get('/cm-archive')->assertStatus(200);
    }

    public function test_user_can_access_custom_profile_page(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
        $response->assertSee($user->email);
        $response->assertSee('CHEVAL');
    }

    public function test_admin_can_create_skill_and_support_card(): void
    {
        // 1. Create skill
        $skillResponse = $this->actingAs($this->admin)->post('/admin/skills', [
            'name' => 'Professor of Curvature',
            'slug' => 'professor-of-curvature',
            'rarity' => 'Rare',
            'skill_type' => 'Velocity',
            'running_style' => 'Universal',
            'distance_type' => 'Universal',
            'activation_condition' => 'Any corner',
            'description' => 'Velocity boost on corners.',
        ]);

        $skillResponse->assertRedirect(route('admin.skills.index'));
        $this->assertDatabaseHas('skills', [
            'name' => 'Professor of Curvature',
            'rarity' => 'Rare',
        ]);

        // 2. Create support card
        $cardResponse = $this->actingAs($this->admin)->post('/admin/support-cards', [
            'name' => 'Kitasan Black',
            'epithet' => 'Fire at My Heels',
            'slug' => 'ssr-kitasan-black',
            'rarity' => 'SSR',
            'card_type' => 'Speed',
            'specialty_priority' => 80,
            'key_skills' => 'Professor of Curvature, Corner Specialist',
            'summary' => 'Premier speed card.',
        ]);

        $cardResponse->assertRedirect(route('admin.support-cards.index'));
        $this->assertDatabaseHas('support_cards', [
            'name' => 'Kitasan Black',
            'rarity' => 'SSR',
        ]);
    }
}
