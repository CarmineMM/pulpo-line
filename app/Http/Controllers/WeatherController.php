<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\Request;
use App\Models\SearchHistory;

class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function search(Request $request, string $city)
    {
        $weatherData = $this->weatherService->getCurrentWeather($city);

        // Guardar en el historial
        $request->user()->searchHistories()->create([
            'city_name' => $city,
            'weather_data' => $weatherData,
        ]);

        return $this->success($weatherData);
    }
}
