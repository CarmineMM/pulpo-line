<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    protected $apiKey;
    protected $baseUrl = 'http://api.weatherapi.com/v1/';

    public function __construct()
    {
        $this->apiKey = config('services.weatherapi.key');
    }

    /**
     * Obtiene el clima actual para una ciudad específica.
     *
     * @param string $city
     * @return array
     */
    public function getCurrentWeather(string $city): array
    {
        return Cache::remember(
            "weather.{$city}",
            30,  // Cache por 30 minutos
            function () use ($city) {
                $response = Http::get($this->baseUrl . 'current.json', [
                    'key' => $this->apiKey,
                    'q' => $city,
                ]);

                // Manejo de errores de la API
                $response->throw(); // Lanza una excepción si la respuesta no es 2xx

                return $this->formatResponse($response->json());
            }
        );
    }

    /**
     * Formatea la respuesta de la API para devolver solo lo necesario.
     * Y de forma estandarizada.
     *
     * @param array $data
     * @return array
     */
    protected function formatResponse(array $data): array
    {
        return [
            'location' => [
                'name' => $data['location']['name'],
                'region' => $data['location']['region'],
                'country' => $data['location']['country'],
                'localtime' => $data['location']['localtime'],
            ],
            'weather' => [
                'temp_c' => $data['current']['temp_c'],
                'condition' => $data['current']['condition']['text'],
                'wind_kph' => $data['current']['wind_kph'],
                'humidity' => $data['current']['humidity'],
            ],
        ];
    }
}
