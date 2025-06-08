<?php

namespace App\Http\Controllers;

use App\Services\WeatherService;
use Illuminate\Http\Request;
use App\Models\SearchHistory;

/**
 * @OA\Tag(
 *     name="Weather",
 *     description="Consultas de información meteorológica"
 * )
 */
class WeatherController extends Controller
{
    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    /**
     * @OA\Get(
     *     path="/api/weather/{city}",
     *     summary="Obtener el clima actual de una ciudad",
     *     tags={"Weather"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="city",
     *         in="path",
     *         required=true,
     *         description="Nombre de la ciudad a consultar",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Datos meteorológicos obtenidos exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="city", type="string", example="Madrid"),
     *             @OA\Property(property="temperature", type="number", format="float", example=22.5),
     *             @OA\Property(property="condition", type="string", example="Soleado"),
     *             @OA\Property(property="humidity", type="integer", example=45),
     *             @OA\Property(property="wind_speed", type="number", format="float", example=5.2)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Ciudad no encontrada"
     *     )
     * )
     *
     * Obtiene la información meteorológica actual para una ciudad específica.
     *
     * @param Request $request
     * @param string $city Nombre de la ciudad a consultar
     * @return \Illuminate\Http\JsonResponse
     */
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
