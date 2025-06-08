<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WeatherApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_search_for_weather_data()
    {
        // Simula la autenticación
        Sanctum::actingAs(
            User::factory()->create(),
            ['*']
        );

        $response = $this->getJson('/api/weather/London');

        $response->assertStatus(200);
        $response->assertJsonStructure([ // Verifica la estructura de la respuesta
            'data' => [
                'location' => ['name', 'region'],
                'weather' => ['temp_c', 'condition']
            ]
        ]);

        // Verifica que se guardó en el historial
        $this->assertDatabaseHas('search_histories', [
            'city_name' => 'London'
        ]);
    }
}
