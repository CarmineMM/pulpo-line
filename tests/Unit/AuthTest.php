<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Prueba que un usuario puede registrarse exitosamente.
     * @test
     */
    public function test_can_user_register_successfully()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(201)
            ->assertJson(['message' => 'User registered successfully']);

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Prueba que el registro falla si los datos son inválidos.
     * @test
     */
    public function test_can_register_fall_with_invalid_data()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'password' => '123',
        ];

        $response = $this->postJson('/api/register', $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email', 'password']);
    }

    /**
     * Prueba que un usuario puede iniciar sesión con credenciales correctas.
     * @test
     */
    public function test_can_user_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password123'),
        ]);

        $credentials = [
            'email' => $user->email,
            'password' => $password,
        ];

        $response = $this->postJson('/api/login', $credentials);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'message',
        ]);
    }

    /**
     * Prueba que el login falla con credenciales incorrectas.
     * @test
     */
    public function test_can_login_fail_with_incorrect_credentials()
    {
        $user = User::factory()->create();

        $credentials = [
            'email' => $user->email,
            'password' => 'wrong-password',
        ];

        $response = $this->postJson('/api/login', $credentials);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Prueba que un usuario autenticado puede cerrar sesión.
     * @test
     */
    public function test_can_authenticated_user_logout()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Logged out successfully']);
    }
}
