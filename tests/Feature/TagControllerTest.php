<?php

namespace Tests\Feature;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Test index page with list of tags.
     */
    public function test_index_page_can_be_rendered(): void
    {
        $tag = Tag::create(['name' => 'laravel-framework']);

        $response = $this->actingAs($this->user)
            ->get(route('tags.index'));

        $response->assertStatus(200);
        $response->assertSee('laravel-framework');
    }

    /**
     * Test search tag functionality.
     */
    public function test_tags_can_be_searched(): void
    {
        Tag::create(['name' => 'vue-js']);
        Tag::create(['name' => 'react-js']);

        $response = $this->actingAs($this->user)
            ->get(route('tags.index', ['search' => 'vue']));

        $response->assertStatus(200);
        $response->assertSee('vue-js');
        $response->assertDontSee('react-js');
    }

    /**
     * Test tag creation form rendering.
     */
    public function test_create_page_can_be_rendered(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('tags.create'));

        $response->assertStatus(200);
    }

    /**
     * Test tag can be stored successfully.
     */
    public function test_tag_can_be_stored(): void
    {
        $payload = [
            'name' => 'new-tag',
        ];

        $response = $this->actingAs($this->user)
            ->post(route('tags.store'), $payload);

        $response->assertRedirect(route('tags.index'));
        $response->assertSessionHas('success', 'Tag created successfully.');
        $this->assertDatabaseHas('tags', ['name' => 'new-tag']);
    }

    /**
     * Test tag edit page rendering.
     */
    public function test_edit_page_can_be_rendered(): void
    {
        $tag = Tag::create(['name' => 'editable-tag']);

        $response = $this->actingAs($this->user)
            ->get(route('tags.edit', $tag));

        $response->assertStatus(200);
    }

    /**
     * Test tag can be updated successfully.
     */
    public function test_tag_can_be_updated(): void
    {
        $tag = Tag::create(['name' => 'original-tag']);

        $payload = [
            'name' => 'modified-tag',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('tags.update', $tag), $payload);

        $response->assertRedirect(route('tags.index'));
        $response->assertSessionHas('success', 'Tag updated successfully.');
        $this->assertDatabaseHas('tags', [
            'id' => $tag->id,
            'name' => 'modified-tag',
        ]);
    }

    /**
     * Test tag can be deleted successfully.
     */
    public function test_tag_can_be_deleted(): void
    {
        $tag = Tag::create(['name' => 'to-be-deleted']);

        $response = $this->actingAs($this->user)
            ->delete(route('tags.destroy', $tag));

        $response->assertRedirect(route('tags.index'));
        $response->assertSessionHas('success', 'Tag deleted successfully.');
        $this->assertSoftDeleted('tags', ['id' => $tag->id]);
    }
}
