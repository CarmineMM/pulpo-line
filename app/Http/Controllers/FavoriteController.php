<?php

namespace App\Http\Controllers;

use App\Models\FavoriteCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Tag(
 *     name="Favorites",
 *     description="Gestión de ciudades favoritas"
 * )
 */
class FavoriteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/favorites",
     *     summary="Obtener las ciudades favoritas del usuario",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de ciudades favoritas",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="user_id", type="integer", example=1),
     *                 @OA\Property(property="city_name", type="string", example="Madrid"),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     )
     * )
     *
     * Obtiene la lista de ciudades favoritas del usuario autenticado.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->success(Auth::user()->favoriteCities);
    }

    /**
     * @OA\Post(
     *     path="/api/favorites",
     *     summary="Añadir una ciudad a favoritos",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"city_name"},
     *             @OA\Property(property="city_name", type="string", example="Barcelona")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Ciudad añadida a favoritos",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer"),
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="city_name", type="string"),
     *             @OA\Property(property="created_at", type="string", format="date-time"),
     *             @OA\Property(property="updated_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="La ciudad ya está en favoritos"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autenticado"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación"
     *     )
     * )
     *
     * Añade una ciudad a la lista de favoritos del usuario.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'city_name' => 'required|string|max:255',
        ]);

        // Check if the city is already a favorite for this user
        $existingFavorite = $user->favoriteCities()->where('city_name', $validated['city_name'])->first();

        if ($existingFavorite) {
            return $this->error(__('favorites.city_already_in_favorites'), code: Response::HTTP_CONFLICT);
        }

        $favoriteCity = $user->favoriteCities()->create($validated);

        return $this->success([$favoriteCity], code: Response::HTTP_CREATED);
    }

    /**
     * @OA\Delete(
     *     path="/api/favorites/{favoriteCity}",
     *     summary="Eliminar una ciudad de favoritos",
     *     tags={"Favorites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="favoriteCity",
     *         in="path",
     *         required=true,
     *         description="ID de la ciudad favorita",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Ciudad eliminada de favoritos"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="No autorizado"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Recurso no encontrado"
     *     )
     * )
     *
     * Elimina una ciudad de la lista de favoritos del usuario.
     *
     * @param  \App\Models\FavoriteCity  $favoriteCity
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(FavoriteCity $favoriteCity): JsonResponse
    {
        if ($favoriteCity->user_id !== Auth::id()) {
            return $this->error('Unauthorized', code: Response::HTTP_FORBIDDEN);
        }

        $favoriteCity->delete();

        return $this->success(code: Response::HTTP_NO_CONTENT);
    }
}
