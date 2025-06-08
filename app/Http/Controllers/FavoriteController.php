<?php

namespace App\Http\Controllers;

use App\Models\FavoriteCity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the user's favorite cities.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->success(Auth::user()->favoriteCities);
    }

    /**
     * Store a newly created favorite city in storage.
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
     * Remove the specified favorite city from storage.
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
