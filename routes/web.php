<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthDataController;
use App\Http\Controllers\FoodRecommendationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/home', [HomeController::class, 'index'])->name('home');

// Dashboard route
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// Health data routes
Route::middleware('auth')->group(function () {
    Route::get('/health-data/edit', [HealthDataController::class, 'edit'])->name('health-data.edit');
    Route::patch('/health-data/update', [HealthDataController::class, 'update'])->name('health-data.update');

});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->get('/food-recommendations', [FoodRecommendationController::class, 'index'])->name('food.recommendations');
Route::middleware('auth')->post('/food-recommendations', [FoodRecommendationController::class, 'store'])->name('food.recommendations.store');

Route::post('/save-recipe/{recipe}', [FoodRecommendationController::class, 'saveRecipe'])->name('recipe.save');
Route::get('/saved-recipes', [FoodRecommendationController::class, 'showSavedRecipes'])->name('saved.recipes');
Route::delete('/saved-recipes/{id}', [FoodRecommendationController::class, 'deleteSavedRecipe'])->name('recipe.delete');
Route::post('/recipe/{idno}/unsave', [FoodRecommendationController::class, 'unsaveRecipe'])->name('recipe.unsave');

Route::post('/refresh-recommendations', [FoodRecommendationController::class, 'refreshRecommendations'])->middleware('auth');

// Laravel authentication routes (login, logout, etc.)
require __DIR__.'/auth.php';


// TEMP: Test Spoonacular API connectivity
Route::get('/test-spoonacular', function () {
    $apiKeys = explode(',', env('SPOONACULAR_API_KEYS'));
    $apiKey = trim($apiKeys[0]); // test the first key only

    \Log::info('🔑 API key used:', ['key' => $apiKey]); // log here

    $response = Http::get('https://api.spoonacular.com/recipes/complexSearch', [
        'apiKey' => $apiKey,
        'query' => 'chicken',
        'number' => 1,
    ]);

    return $response->json();
});
