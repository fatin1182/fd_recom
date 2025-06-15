<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavedRecipe;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Services\SpoonacularService;


class FoodRecommendationController extends Controller
{
    public function index(Request $request)
    {

        $user = auth()->user();
        $page = max((int)$request->input('page', 1), 1);
        $searchQuery = $request->input('search');

        // Combine systolic and diastolic for blood pressure string
        $bloodPressure = null;
        if ($user->blood_pressure_systolic && $user->blood_pressure_diastolic) {
            $bloodPressure = $user->blood_pressure_systolic . '/' . $user->blood_pressure_diastolic;
        }

        $categories = $this->getHealthCategories($bloodPressure, $user->blood_sugar, $user->cholesterol, $user->weight, $user->height);
        $recommendedRecipesData = $this->getRecipeRecommendations($categories, $page, $user->id, $searchQuery);
        $savedRecipeIds = SavedRecipe::where('user_id', $user->id)->pluck('recipe_id')->toArray();
        $recipesForPage = $recommendedRecipesData['recipes'];

        return view('food-recommendations', [
            'recommendedRecipes' => $recipesForPage,
            'healthTips' => $this->getGeneralHealthTips($bloodPressure, $user->blood_sugar, $user->cholesterol, null),
            'bloodPressure' => $bloodPressure,
            'bloodSugar' => $user->blood_sugar,
            'cholesterol' => $user->cholesterol,
            'currentPage' => $page,
            'totalPages' => $recommendedRecipesData['totalPages'],
            'savedRecipes' => $savedRecipeIds,
            'search' => $searchQuery,
        ]);
    }

    // Added $cholesterol parameter and handling
    private function getHealthCategories($bloodPressure, $bloodSugar, $cholesterol, $weight, $height)
    {
        $categories = [];

        if ($bloodPressure !== null && strpos($bloodPressure, '/') !== false) {
            list($systolic, $diastolic) = explode('/', $bloodPressure);
            $systolic = (int) $systolic;
            $diastolic = (int) $diastolic;

            if ($systolic >= 130 || $diastolic >= 80) {
                $categories[] = 'high_bp';
            } elseif ($systolic >= 120 && $diastolic < 80) {
                $categories[] = 'elevated_bp';
            } else {
                $categories[] = 'normal_bp';
            }
        }

        if ($bloodSugar !== null) {
            if ($bloodSugar >= 7) {
                $categories[] = 'diabetes';
            } elseif ($bloodSugar >= 5.6) {
                $categories[] = 'prediabetes';
            } else {
                $categories[] = 'normal_sugar';
            }
        }

        if ($cholesterol !== null) {
            if ($cholesterol >= 240) {
                $categories[] = 'high_cholesterol';
            } elseif ($cholesterol >= 200) {
                $categories[] = 'borderline_cholesterol';
            } else {
                $categories[] = 'normal_cholesterol';
            }
        }

         // Only add underweight if no high-risk metabolic condition is present
        $hasMetabolic = !empty(array_intersect($categories, ['high_bp', 'diabetes', 'high_cholesterol']));

        if (!$hasMetabolic && $weight && $height) {
            $heightInMeters = $height / 100;
            $bmi = $weight / ($heightInMeters * $heightInMeters);

            if ($bmi < 18.5) {
                $categories[] = 'underweight';
            }
        }

        if (app()->environment('local')) {
            \Log::info('🧠 Health Categories:', $categories);
        }
        return $categories;
    }

    private function mapFuzzyCategoriesToSpoonacularFilters(array $categories)
    {
        $filters = [];
        $queries = [];

        $highRiskCategories = ['high_bp', 'diabetes', 'high_cholesterol'];

        $hasHighRisk = collect($categories)->intersect($highRiskCategories)->isNotEmpty();

        foreach ($categories as $category) {
            if (!in_array($category, ['high_bp', 'diabetes', 'high_cholesterol', 'obese', 'overweight', 'underweight'])) {
                continue;
            }

            switch ($category) {
                case 'high_bp':
                    $filters['maxSodium'] = 100;
                    $filters['maxSaturatedFat'] = 8;
                    $filters['minFiber'] = 4;
                    $queries[] = 'low sodium, high fiber';
                    break;

                case 'diabetes':
                    $filters['maxCarbs'] = 70;
                    $filters['minFiber'] = 4;
                    $filters['maxSugar'] = 15;
                    $filters['maxCalories'] = 500;
                    $queries[] = 'high fiber, low carb, low sugar';
                    break;

                case 'high_cholesterol':
                    $filters['maxSaturatedFat'] = 15;
                    $filters['maxCholesterol'] = 200; // Dietary cholesterol cap (mg)
                    $filters['maxCalories'] = 700;
                    $filters['minFiber'] = 4;
                    $queries[] = 'high fiber, low fat';
                    break;

                case 'obese':
                    $filters['maxCalories'] = 600;
                    $filters['maxFat'] = 20;
                    $queries[] = 'low calorie, low fat';
                    break;

                case 'overweight':
                    $filters['maxCalories'] = 700;
                    $queries[] = 'low calorie, high fiber';
                    break;

                case 'normal_bmi':
                    $queries[] = 'healthy';
                    break;

                case 'underweight':
                if (!$hasHighRisk) {
                    $filters['minCalories'] = 700;
                    $filters['minFat'] = 20;
                    $filters['minProtein'] = 20;
                    $filters['minCarbs'] = 40;
                    $queries[] = 'weight gain, mass gainer, high calorie, high protein';
                }
                break;
            }
        }

        $filters['query'] = implode(', ', array_unique($queries)) ?: 'healthy';
        if (app()->environment('local')) {
            \Log::info('📊 Spoonacular filters:', $filters);
        }
        return $filters;
    }

    public function getRecipeRecommendations(array $categories, $page = 1, $userId, $searchQuery = null, $maxResults = 9)
    {
        if (empty($categories)) {
            return [
                'recipes' => [],
                'totalPages' => 0,
            ];
        }

        $recipesPerPage = 9;

        $cacheKey = 'recipes_' . md5(json_encode([
            'user' => $userId,
            'categories' => $categories,
            'page' => $page,
            'search' => $searchQuery
        ]));

        if (app()->environment('local')) {
            \Log::info('📦 Cache key used:', ['key' => $cacheKey]);
        }

        // ✅ Check if cached data exists first
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        // 🔄 No cache found — do API work
        $filters = $this->mapFuzzyCategoriesToSpoonacularFilters($categories);
        $apiKeys = config('services.spoonacular.api_keys');
        if (app()->environment('local')) {
            \Log::info('📌 Loaded Spoonacular API keys:', $apiKeys);
        }
        $response = null;
        $usedKey = null;

        foreach ($apiKeys as $apiKey) {
            $apiKey = trim($apiKey);

            $queryParams = array_merge([
                'apiKey' => $apiKey,
                'number' => $maxResults,
                'addRecipeInformation' => true,
                'includeNutrition' => true,
                'offset' => ($page - 1) * $recipesPerPage,
            ], $filters);

            if ($searchQuery) {
                $queryParams['query'] = $searchQuery;
            }

            if (app()->environment('local')) {
                \Log::info("🔍 Query Params for API key: {$apiKey}", $queryParams);
            }

            try {
                $response = Http::retry(2, 500)->get('https://api.spoonacular.com/recipes/complexSearch', $queryParams);
                if ($response->successful()) {
                    $usedKey = $apiKey;
                    break;
                }
            } catch (\Exception $e) {
                continue; // try next key
            }
        }

        if (!$response || !$response->successful()) {
            return [
                'recipes' => [],
                'totalPages' => 0,
            ];
        }

        $recipes = collect($response->json()['results'] ?? [])
            ->filter(fn($r) => !empty($r['image']) && !str_contains($r['image'], 'placeholder.jpg'))
            ->values()
            ->all();

        $totalResults = $response->json()['totalResults'] ?? 0;
        $totalPages = min(25, ceil($totalResults / $recipesPerPage));
        $detailedRecipes = [];

        foreach ($recipes as $recipe) {
            try {
                $detailsResponse = Http::retry(2, 500)->get("https://api.spoonacular.com/recipes/{$recipe['id']}/information", [
                    'apiKey' => $usedKey,
                    'includeNutrition' => true,
                ]);

                if (!$detailsResponse->successful()) {
                    continue;
                }

                $details = $detailsResponse->json();

                if (empty($details['image']) || str_contains($details['image'], 'placeholder.jpg')) {
                    continue;
                }

                // Nutrition
                $nutrition = [];
                if (!empty($details['nutrition']['nutrients'])) {
                    $nutrition = $this->extractBasicNutrition($details['nutrition']['nutrients']);
                } else {
                    try {
                        $widget = Http::retry(2, 500)->get("https://api.spoonacular.com/recipes/{$recipe['id']}/nutritionWidget.json", [
                            'apiKey' => $usedKey,
                        ]);
                        if ($widget->successful()) {
                            $json = $widget->json();
                            $nutrition = [
                                'calories' => $json['calories'] ?? 'N/A',
                                'carbohydrates' => $json['carbs'] ?? 'N/A',
                                'protein' => $json['protein'] ?? 'N/A',
                            ];
                        }
                    } catch (\Exception $e) {
                        // fail silently
                    }
                }

                $detailedRecipes[] = [
                    'id' => $details['id'],
                    'title' => $details['title'],
                    'image' => $details['image'],
                    'readyInMinutes' => $details['readyInMinutes'] ?? '--',
                    'servings' => $details['servings'] ?? '--',
                    'summary' => $details['summary'] ?? 'No summary available',
                    'sourceUrl' => $details['sourceUrl'],
                    'instructions' => collect($details['analyzedInstructions'][0]['steps'] ?? [])->pluck('step')->toArray(),
                    'ingredients' => array_map(fn($i) => $i['original'], $details['extendedIngredients'] ?? []),
                    'nutrition' => $nutrition,
                    'explanation' => $this->generateRecommendationExplanation($categories),
                ];
            } catch (\Exception $e) {
                continue;
            }
        }

        // ✅ Final result
        $result = [
            'recipes' => $detailedRecipes,
            'totalPages' => $totalPages,
        ];

        // ✅ Store only if there are recipes
        if (!empty($detailedRecipes)) {
            Cache::put($cacheKey, $result, now()->addMinutes(30));
        }

        return $result;
    }

    private function getGeneralHealthTips($bloodPressure, $bloodSugar, $cholesterol, $bmiCategory)
    {
        $tips = [];

        if (!empty($bloodPressure) && strpos($bloodPressure, '/') !== false) {
            list($systolic, $diastolic) = explode('/', $bloodPressure);
            $systolic = (int)$systolic;
            $diastolic = (int)$diastolic;

            if ($systolic >= 130 || $diastolic >= 80) {
                $tips[] = 'Your blood pressure is high. Try oats, spinach, and foods low in sodium.';
            } elseif ($systolic < 90 || $diastolic < 60) {
                $tips[] = 'Your blood pressure is low. Consider increasing sodium intake with foods like olives or broth.';
            } else {
                $tips[] = 'Your blood pressure is normal. Maintain a healthy diet and lifestyle.';
            }
        }

        if (!is_null($bloodSugar)) {
            if ($bloodSugar >= 7) {
                $tips[] = 'Your blood sugar is high. Focus on low glycemic foods like whole grains, legumes, and vegetables.';
            } elseif ($bloodSugar >= 5.6) {
                $tips[] = 'Your blood sugar is elevated. Include moderate glycemic foods like quinoa and sweet potatoes in your diet.';
            } else {
                $tips[] = 'Your blood sugar is normal. Maintain a balanced diet with fiber-rich foods.';
            }
        }

        if ($cholesterol !== null) {
            if ($cholesterol >= 240) {
                $tips[] = 'Your cholesterol is high. Increase intake of fiber-rich foods and reduce saturated fats.';
            } elseif ($cholesterol >= 200) {
                $tips[] = 'Your cholesterol is borderline high. Consider a diet moderate in fiber and low in saturated fats.';
            } else {
                $tips[] = 'Your cholesterol level is within the healthy range.';
            }
        }

        return $tips;
    }

    private function extractBasicNutrition(array $nutrients)
    {

        $map = ['Calories', 'Carbohydrates', 'Protein', 'Fat', 'Fiber', 'Sugar'];
        $result = [];

        foreach ($map as $label) {
            $nutrient = collect($nutrients)->firstWhere('name', $label);
            $result[strtolower($label)] = $nutrient ? "{$nutrient['amount']} {$nutrient['unit']}" : 'N/A';
        }

        return $result;
    }

    private function generateRecommendationExplanation(array $categories)
    {
        $explanation = [];

        foreach ($categories as $category) {
            switch ($category) {
                case 'high_bp':
                    $explanation[] = 'This recipe is low in sodium and high in potassium, which can help lower blood pressure.';
                    break;
                case 'diabetes':
                    $explanation[] = 'This recipe is low in carbs and helps regulate blood sugar levels.';
                    break;
                case 'high_cholesterol':
                    $explanation[] = 'This recipe is high in fiber and low in saturated fat, helping reduce cholesterol.';
                    break;
                case 'obese':
                    $explanation[] = 'This recipe is low in calories and helps with weight loss.';
            }
        }

        return implode(' ', $explanation);
    }

    public function saveRecipe(Request $request, $recipeId)
    {
        $user = auth()->user();

        $existingSave = SavedRecipe::where('user_id', $user->id)
            ->where('recipe_id', $recipeId)
            ->first();

        if ($existingSave) {
            return response()->json(['success' => false, 'message' => 'Recipe is already saved.']);
        }

        // Get user health data
        $bloodPressure = null;
        if ($user->blood_pressure_systolic && $user->blood_pressure_diastolic) {
            $bloodPressure = $user->blood_pressure_systolic . '/' . $user->blood_pressure_diastolic;
        }

        $categories = $this->getHealthCategories($bloodPressure, $user->blood_sugar, $user->cholesterol, $user->weight, $user->height);
        $explanation = $this->generateRecommendationExplanation($categories);

        SavedRecipe::create([
            'user_id' => $user->id,
            'recipe_id' => $recipeId,
            'explanation' => $explanation, // 💬 save it
        ]);

        return response()->json(['success' => true, 'message' => 'Recipe saved successfully!']);
    }


    public function showSavedRecipes(Request $request)
    {
        $user = auth()->user();
        $page = max(1, (int)$request->input('page', 1));
        $search = $request->input('search');
        $recipesPerPage = 9;

        $savedRecipes = SavedRecipe::where('user_id', $user->id)->get();
        $pagedRecipesRaw = $savedRecipes->slice(($page - 1) * $recipesPerPage, $recipesPerPage);

        $apiKeys = config('services.spoonacular.api_keys');
        if (app()->environment('local')) {
            \Log::info('🔑 Loaded Spoonacular API keys:', $apiKeys);
        }
        $recipes = [];

        foreach ($pagedRecipesRaw as $savedRecipe) {
            $recipeId = $savedRecipe->recipe_id;
            $cacheKey = "saved_recipe_{$recipeId}";

            $recipeData = Cache::get($cacheKey);

            if (!$recipeData) {
                foreach ($apiKeys as $apiKey) {
                    try {
                        $response = Http::retry(1, 500)->get("https://api.spoonacular.com/recipes/{$recipeId}/information", [
                            'apiKey' => $apiKey,
                            'includeNutrition' => true,
                        ]);

                        if ($response->successful()) {
                            $recipeData = $response->json();

                            // ✅ Only cache valid arrays
                            if ($recipeData && is_array($recipeData)) {
                                Cache::put($cacheKey, $recipeData, now()->addHours(6));

                                if (app()->environment('local')) {
                                    \Log::info("📥 Cached recipe {$recipeId}", ['key' => $cacheKey]);
                                }
                            }

                            break;
                        }
                    } catch (\Exception $e) {
                        \Log::debug("⚠️ Error for recipe {$recipeId} with API key {$apiKey}: " . $e->getMessage());
                    }
                }
            }


            if (!$recipeData || !is_array($recipeData)) {
                continue;
            }

            if ($search && !str_contains(strtolower($recipeData['title']), strtolower($search))) {
                continue;
            }

            $instructions = collect($recipeData['analyzedInstructions'][0]['steps'] ?? [])->pluck('step')->toArray();

            // Try extracting nutrition
            $nutrition = [];
            if (!empty($recipeData['nutrition']['nutrients'])) {
                $nutrition = $this->extractBasicNutrition($recipeData['nutrition']['nutrients']);
            }

            $recipes[] = [
                'id' => $recipeData['id'],
                'title' => $recipeData['title'],
                'image' => $recipeData['image'] ?? asset('images/placeholder.jpg'),
                'sourceUrl' => $recipeData['sourceUrl'],
                'readyInMinutes' => $recipeData['readyInMinutes'] ?? '--',
                'servings' => $recipeData['servings'] ?? '--',
                'nutrition' => $nutrition,
                'ingredients' => array_map(fn($i) => $i['original'], $recipeData['extendedIngredients'] ?? []),
                'instructions' => $instructions,
                'explanation' => $savedRecipe->explanation ?? 'Recommended based on your past health preferences.',
            ];
        }

        $totalPages = ceil($savedRecipes->count() / $recipesPerPage);

        return view('saved-recipes', [
            'recommendedRecipes' => $recipes,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'savedRecipes' => $savedRecipes->pluck('recipe_id')->toArray(),
            'search' => $search,
        ]);
    }

    public function deleteSavedRecipe(Request $request, $id)
    {
        $user = auth()->user();

        $savedRecipe = \App\Models\SavedRecipe::where('user_id', $user->id)
            ->where('recipe_id', $id)
            ->first();

        if ($savedRecipe) {
            $savedRecipe->delete();

            // ✅ Always return JSON for frontend fetch()
            return response()->json([
                'success' => true,
                'message' => 'Recipe deleted successfully!'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Recipe not found or already deleted.'
        ]);
    }


    public function unsaveRecipe($id)
    {
        $user = auth()->user();

        $savedRecipe = SavedRecipe::where('user_id', $user->id)
            ->where('recipe_id', $id)
            ->first();

        if ($savedRecipe) {
            $savedRecipe->delete();

            return response()->json([
                'success' => true,
                'message' => 'Recipe unsaved successfully!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Recipe not found or already unsaved.',
        ]);
    }

    public function refreshRecommendations()
    {
        $user = auth()->user();
        $versionKey = 'recommendations_version_user_' . $user->id;

        $newVersion = Cache::increment($versionKey);

        return response()->json([
            'success' => true,
            'message' => 'Recommendations refreshed.',
            'newVersion' => $newVersion
        ]);
    }

}
