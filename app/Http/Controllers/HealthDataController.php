<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HealthDataController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        // Optionally cache the user's profile data view or other data if needed
        // For now, just return view directly
        return view('health-data.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'blood_pressure_systolic' => 'nullable|integer|min:50|max:300',
            'blood_pressure_diastolic' => 'nullable|integer|min:30|max:200',
            'cholesterol' => 'nullable|numeric',
            'blood_sugar' => 'nullable|numeric',
            'age' => 'nullable|integer',
            'gender' => 'nullable|string|in:male,female',
            'height' => 'nullable|numeric|min:30|max:300',
            'weight' => 'nullable|numeric|min:1|max:500',
        ]);

        // Check if all health fields are empty
        $allHealthFieldsEmpty = empty($validated['blood_pressure_systolic']) &&
                                empty($validated['blood_pressure_diastolic']) &&
                                empty($validated['cholesterol']) &&
                                empty($validated['blood_sugar']);

        if ($allHealthFieldsEmpty) {
            $errorMessage = 'Please provide at least one health measurement (blood pressure, blood sugar, or cholesterol).';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['health' => [$errorMessage]]
                ], 422);
            }

            return back()->withErrors(['health' => $errorMessage])->withInput();
        }

        // Handle blood pressure logic
        if (empty($validated['blood_pressure_systolic']) && empty($validated['blood_pressure_diastolic'])) {
            // Set null if both systolic and diastolic are empty
            $validated['blood_pressure'] = null;
        } elseif (!empty($validated['blood_pressure_systolic']) && !empty($validated['blood_pressure_diastolic'])) {
            if ($validated['blood_pressure_diastolic'] >= $validated['blood_pressure_systolic']) {
                $errorMessage = 'Diastolic pressure should be less than systolic.';

                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage,
                        'errors' => ['blood_pressure' => [$errorMessage]]
                    ], 422);
                }

                return back()->withErrors(['blood_pressure' => $errorMessage])->withInput();
            }

            // Combine systolic and diastolic into a single string
            $validated['blood_pressure'] = "{$validated['blood_pressure_systolic']}/{$validated['blood_pressure_diastolic']}";
        } else {
            $errorMessage = 'Both systolic and diastolic blood pressure values are required.';

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'errors' => ['blood_pressure' => [$errorMessage]]
                ], 422);
            }

            return back()->withErrors(['blood_pressure' => $errorMessage])->withInput();
        }

        // Remove individual systolic and diastolic before updating DB
        unset($validated['blood_pressure_systolic']);
        unset($validated['blood_pressure_diastolic']);

        // Update user health data
        auth()->user()->update($validated);

        // Cache invalidation: increment version key for this user to invalidate cached recommendations or any cached health data
        $versionKey = 'recommendations_version_user_' . auth()->id();
        Cache::increment($versionKey);

        $successMessage = 'Health data updated successfully!';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'redirect' => route('dashboard')
            ]);
        }

        return redirect()->route('dashboard')->with('status', $successMessage);
    }
}

