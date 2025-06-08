<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\FavoriteCity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class FavoriteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        // Sanctum::actingAs($this->user); // Will be called per test needing auth
    }

    public function test_user_can_list_their_favorite_cities(): void
    {
        Sanctum::actingAs($this->user);
        FavoriteCity::factory()->count(3)->create(['user_id' => $this->user->id]);
        FavoriteCity::factory()->count(2)->create(); // Other user's favorites

        $response = $this->getJson(route('favorites.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'message',
            ])
            ->assertJsonCount(3, 'data');
    }

    public function test_guest_cannot_list_favorite_cities(): void
    {
        // No Sanctum::actingAs() for guest tests
        $response = $this->getJson(route('favorites.index'));
        $response->assertUnauthorized();
    }

    public function test_user_can_add_a_favorite_city(): void
    {
        Sanctum::actingAs($this->user);
        $cityName = 'London';
        $response = $this->postJson(route('favorites.store'), ['city_name' => $cityName]);

        $response->assertStatus(201)
            ->assertJsonFragment(['city_name' => $cityName]);

        $this->assertDatabaseHas('favorite_cities', [
            'user_id' => $this->user->id,
            'city_name' => $cityName,
        ]);
    }

    public function test_user_cannot_add_a_duplicate_favorite_city(): void
    {
        Sanctum::actingAs($this->user);
        $cityName = 'Paris';
        FavoriteCity::factory()->create(['user_id' => $this->user->id, 'city_name' => $cityName]);

        $response = $this->postJson(route('favorites.store'), ['city_name' => $cityName]);

        $response->assertStatus(409); // Conflict
        $this->assertDatabaseCount('favorite_cities', 1); // Ensure no new entry was created
    }

    public function test_user_cannot_add_favorite_city_with_invalid_data(): void
    {
        Sanctum::actingAs($this->user);
        $response = $this->postJson(route('favorites.store'), ['city_name' => '']);
        $response->assertStatus(422) // Unprocessable Entity for validation errors
            ->assertJsonValidationErrors(['city_name']);
    }

    public function test_user_can_delete_their_favorite_city(): void
    {
        Sanctum::actingAs($this->user);
        $favoriteCity = FavoriteCity::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson(route('favorites.destroy', $favoriteCity));

        $response->assertStatus(204);
        $this->assertSoftDeleted('favorite_cities', ['id' => $favoriteCity->id]);
    }

    public function test_user_cannot_delete_others_favorite_city(): void
    {
        Sanctum::actingAs($this->user);
        $otherUser = User::factory()->create();
        $favoriteCity = FavoriteCity::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->deleteJson(route('favorites.destroy', $favoriteCity));

        $response->assertStatus(403); // Forbidden
        $this->assertDatabaseHas('favorite_cities', ['id' => $favoriteCity->id, 'deleted_at' => null]);
    }

    public function test_guest_cannot_add_a_favorite_city(): void
    {
        // No Sanctum::actingAs() for guest tests
        $response = $this->postJson(route('favorites.store'), ['city_name' => 'Berlin']);
        $response->assertUnauthorized();
    }

    public function test_guest_cannot_delete_a_favorite_city(): void
    {
        // No Sanctum::actingAs() for guest tests
        $favoriteCity = FavoriteCity::factory()->create(); // Belongs to some user

        $response = $this->deleteJson(route('favorites.destroy', $favoriteCity));
        $response->assertUnauthorized();
    }
}
