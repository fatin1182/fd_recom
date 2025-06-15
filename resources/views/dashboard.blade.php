<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HEALTHY Kitchen - User Dashboard</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js for visualizations -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --primary-dark: #4338ca;
            --secondary: #10b981;
            --accent: #f59e0b;
            --light-bg: #f8fafc;
            --dark-text: #1e293b;
            --soft-gray: #f1f5f9;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
        }
        .dashboard-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        .health-value {
            font-weight: 600;
            color: var(--dark-text);
            font-size: 1.05rem;
        }
        .health-label {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
            display: block;
        }
        .nav-link {
            transition: all 0.2s ease;
        }
        .nav-link:hover {
            color: var(--primary);
        }
        .btn-primary {
            background-color: var(--primary);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        .health-status {
            display: inline-block;
            padding: 0.35rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .status-normal {
            background-color: #d1fae5;
            color: #065f46;
        }
        .status-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .status-danger {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .profile-header {
            font-family: 'Playfair Display', serif;
            color: #1e293b;
            position: relative;
            padding-bottom: 1rem;
        }
        .profile-header:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }
        .metric-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
            transition: all 0.2s ease;
            border: 1px solid rgba(0, 0, 0, 0.03);
        }
        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.07);
        }
        .metric-value {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1.2;
        }
        .metric-label {
            font-size: 0.85rem;
            color: #64748b;
        }
        .bmi-indicator {
            height: 8px;
            border-radius: 4px;
            background: linear-gradient(90deg, #3b82f6 0%, #10b981 40%, #f59e0b 70%, #ef4444 100%);
            position: relative;
            margin-top: 0.5rem;
        }
        .bmi-marker {
            position: absolute;
            top: -4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: white;
            border: 3px solid var(--primary);
            transform: translateX(-50%);
        }
        .bmi-labels {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
            color: #64748b;
            margin-top: 0.25rem;
        }
        .health-section {
            background: var(--soft-gray);
            border-radius: 12px;
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }
        .health-section-title {
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        .health-section-title i {
            margin-right: 0.5rem;
            color: var(--primary);
        }
        .divider {
            border: none;
            height: 1px;
            background: #e2e8f0;
            margin: 1.5rem 0;
        }
    </style>
</head>
<body class="min-h-screen">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <i class="fas fa-utensils text-2xl text-indigo-600 mr-2"></i>
                        <span class="text-xl font-bold text-gray-800">HEALTHY Kitchen</span>
                    </div>
                </div>
                <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-8">
                    <a href="{{ route('home') }}" class="nav-link text-gray-700 hover:text-indigo-600 font-medium transition-colors duration-200">Home</a>
                    <a href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard') ? 'text-indigo-600 font-semibold' : 'text-gray-700' }} hover:text-indigo-600 font-medium transition-colors duration-200">
                    Profile Information
                    </a>
                    <a href="{{ route('food.recommendations') }}"
                        class="{{ request()->routeIs('food.recommendations') ? 'text-indigo-600 font-semibold' : 'text-gray-700' }} hover:text-indigo-600 font-medium transition-colors duration-200">
                    Food Recommendation
                    </a>
                    <a href="{{ route('saved.recipes') }}"
                        class="{{ request()->routeIs('saved.recipes') ? 'text-indigo-600 font-semibold' : 'text-gray-700' }} hover:text-indigo-600 font-medium">
                    Saved Recipes
                    </a>
                </div>
                <div class="flex items-center">
                    @auth
                    <div class="relative" x-data="{ open: false }">
                        <button
                            type="button"
                            class="flex items-center focus:outline-none"
                            @click="open = !open"
                            @click.outside="open = false"
                        >
                            <span class="text-gray-600 mr-2">{{ Auth::user()->name }}</span>
                            <img class="h-8 w-8 rounded-full"
                                src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff"
                                alt="User avatar" />
                        </button>

                        <!-- Dropdown menu -->
                        <div
                            class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none transition-all duration-200 z-50"
                            x-show="open"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform -translate-y-2"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 transform translate-y-0"
                            x-transition:leave-end="opacity-0 transform -translate-y-2"
                            style="display: none;"
                        >
                            <div class="py-1">
                                <!-- Logout option -->
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 flex items-center">
                                        <i class="fas fa-sign-out-alt mr-2 text-indigo-500"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>


    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Health Profile Card - Redesigned -->
            <div class="dashboard-card p-6 lg:col-span-1">
                <h1 class="text-2xl font-bold mb-6">Your Health Profile</h1>

                <!-- Personal Info Section -->
                <div class="health-section">
                    <h2 class="health-section-title">
                        <i class="fas fa-user-circle"></i> Personal Information
                    </h2>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="health-label">Full Name</span>
                            <p class="health-value">{{ $user->name }}</p>
                        </div>

                        <div>
                            <span class="health-label">Age</span>
                            <p class="health-value">{{ $user->age ?? '--' }} years</p>
                        </div>

                        <div>
                            <span class="health-label">Gender</span>
                            <p class="health-value">{{ $user->gender ?? '--' }}</p>
                        </div>

                        <div>
                            <span class="health-label">Email</span>
                            <p class="health-value text-sm truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Body Metrics Section -->
                <div class="health-section">
                    <h2 class="health-section-title">
                        <i class="fas fa-weight"></i> Body Metrics
                    </h2>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <span class="health-label">Height</span>
                            <p class="health-value">{{ $user->height ? $user->height . ' cm' : '--' }}</p>
                        </div>

                        <div>
                            <span class="health-label">Weight</span>
                            <p class="health-value">{{ $user->weight ? $user->weight . ' kg' : '--' }}</p>
                        </div>
                    </div>

                    @if ($user->height && $user->weight)
                        @php
                            $heightInMeters = $user->height / 100;
                            $bmi = $user->weight / ($heightInMeters * $heightInMeters);
                            $bmiPosition = min(max(($bmi - 15) / 25 * 100, 0), 100); // Scale BMI 15-40 to 0-100%
                        @endphp

                        <div>
                            <div class="flex justify-between items-baseline">
                                <span class="health-label">BMI</span>
                                <span class="health-value">{{ number_format($bmi, 1) }}</span>
                            </div>

                            <div class="bmi-indicator">
                                <div class="bmi-marker" style="left: {{ $bmiPosition }}%"></div>
                            </div>
                            <div class="bmi-labels">
                                <span>Underweight</span>
                                <span>Normal</span>
                                <span>Overweight</span>
                                <span>Obese</span>
                            </div>

                            <div class="mt-2 text-center">
                                <span class="health-status
                                    @if ($bmi < 18.5) status-warning
                                    @elseif ($bmi < 25) status-normal
                                    @elseif ($bmi < 30) status-warning
                                    @else status-danger @endif">
                                    @if ($bmi < 18.5) Underweight
                                    @elseif ($bmi < 25) Healthy
                                    @elseif ($bmi < 30) Overweight
                                    @else Obese
                                    @endif
                                </span>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">BMI not available (height and weight required)</p>
                    @endif
                </div>

                <!-- Quick Metrics Overview -->
                <div class="grid grid-cols-3 gap-3 mb-6">
                    <!-- Blood Pressure -->
                    <div class="metric-card p-3 text-center">
                        <div class="metric-value">
                            @if($user->blood_pressure_systolic && $user->blood_pressure_diastolic)
                                {{ $user->blood_pressure_systolic }}/{{ $user->blood_pressure_diastolic }}
                            @else
                                --
                            @endif
                        </div>
                        <div class="metric-label">Blood Pressure</div>
                        @if($user->blood_pressure_systolic && $user->blood_pressure_diastolic)
                            <div class="mt-1">
                                <span class="health-status
                                    @if($user->blood_pressure_systolic > 140 || $user->blood_pressure_diastolic > 90) status-danger
                                    @elseif($user->blood_pressure_systolic > 120 || $user->blood_pressure_diastolic > 80) status-warning
                                    @else status-normal @endif">
                                    @if($user->blood_pressure_systolic > 140 || $user->blood_pressure_diastolic > 90) High
                                    @elseif($user->blood_pressure_systolic > 120 || $user->blood_pressure_diastolic > 80) Elevated
                                    @else Normal
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Blood Sugar -->
                    <div class="metric-card p-3 text-center">
                        <div class="metric-value">
                            {{ $user->blood_sugar ? $user->blood_sugar : '--' }}
                        </div>
                        <div class="metric-label">Blood Sugar</div>
                        @if($user->blood_sugar)
                            <div class="mt-1">
                                <span class="health-status
                                    @if($user->blood_sugar > 7) status-danger
                                    @elseif($user->blood_sugar > 5.5) status-warning
                                    @else status-normal @endif">
                                    @if($user->blood_sugar > 7) High
                                    @elseif($user->blood_sugar > 5.5) Elevated
                                    @else Normal
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Cholesterol -->
                    <div class="metric-card p-3 text-center">
                        <div class="metric-value">
                            {{ $user->cholesterol ? $user->cholesterol : '--' }}
                        </div>
                        <div class="metric-label">Cholesterol</div>
                        @if($user->cholesterol)
                            <div class="mt-1">
                                <span class="health-status
                                    @if($user->cholesterol > 200) status-danger
                                    @elseif($user->cholesterol > 180) status-warning
                                    @else status-normal @endif">
                                    @if($user->cholesterol > 200) High
                                    @elseif($user->cholesterol > 180) Elevated
                                    @else Normal
                                    @endif
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col space-y-3">
                    <a href="{{ route('health-data.edit') }}"
                       class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        <i class="fas fa-edit mr-2 text-indigo-600"></i> Update Health Data
                    </a>

                    <a href="{{ route('food.recommendations') }}"
                       class="w-full btn-primary flex items-center justify-center px-4 py-3 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all">
                        <i class="fas fa-utensils mr-2"></i> Get Personalized Recommendations
                    </a>
                </div>
            </div>

            <!-- Health Metrics Visualization -->
            <div class="dashboard-card p-6 lg:col-span-2">
                <h2 class="text-2xl font-bold mb-6">Health Metrics Overview</h2>

                <!-- Health Status Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                    <!-- Blood Pressure Card -->
                    <div class="metric-card p-5 text-center">
                        <div class="flex justify-center mb-3">
                            <div class="bg-indigo-100 p-3 rounded-full text-indigo-600">
                                <i class="fas fa-heartbeat text-lg"></i>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-1">Blood Pressure</p>
                        <p class="text-2xl font-bold mb-2">
                            @if($user->blood_pressure_systolic && $user->blood_pressure_diastolic)
                                {{ $user->blood_pressure_systolic }}/{{ $user->blood_pressure_diastolic }} mmHg
                            @else
                                --/-- mmHg
                            @endif
                        </p>
                        <span class="health-status
                            @if($user->blood_pressure_systolic > 140 || $user->blood_pressure_diastolic > 90) status-danger
                            @elseif($user->blood_pressure_systolic > 120 || $user->blood_pressure_diastolic > 80) status-warning
                            @else status-normal @endif">
                            @if($user->blood_pressure_systolic && $user->blood_pressure_diastolic)
                                @if($user->blood_pressure_systolic > 140 || $user->blood_pressure_diastolic > 90) High
                                @elseif($user->blood_pressure_systolic > 120 || $user->blood_pressure_diastolic > 80) Elevated
                                @else Normal @endif
                            @else -- @endif
                        </span>
                    </div>

                    <!-- Blood Sugar Card -->
                    <div class="metric-card p-5 text-center">
                        <div class="flex justify-center mb-3">
                            <div class="bg-green-100 p-3 rounded-full text-green-600">
                                <i class="fas fa-tint text-lg"></i>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-1">Blood Sugar</p>
                        <p class="text-2xl font-bold mb-2">
                            {{ $user->blood_sugar ? $user->blood_sugar.' mmol/L' : '-- mmol/L' }}
                        </p>
                        <span class="health-status
                            @if($user->blood_sugar > 7) status-danger
                            @elseif($user->blood_sugar > 5.5) status-warning
                            @else status-normal @endif">
                            @if($user->blood_sugar)
                                @if($user->blood_sugar > 7) High
                                @elseif($user->blood_sugar > 5.5) Elevated
                                @else Normal @endif
                            @else -- @endif
                        </span>
                    </div>

                    <!-- Cholesterol Card -->
                    <div class="metric-card p-5 text-center">
                        <div class="flex justify-center mb-3">
                            <div class="bg-amber-100 p-3 rounded-full text-amber-600">
                                <i class="fas fa-vial text-lg"></i>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-1">Cholesterol</p>
                        <p class="text-2xl font-bold mb-2">
                            {{ $user->cholesterol ? $user->cholesterol.' mg/dL' : '-- mg/dL' }}
                        </p>
                        <span class="health-status
                            @if($user->cholesterol > 200) status-danger
                            @elseif($user->cholesterol > 180) status-warning
                            @else status-normal @endif">
                            @if($user->cholesterol)
                                @if($user->cholesterol > 200) High
                                @elseif($user->cholesterol > 180) Elevated
                                @else Normal @endif
                            @else -- @endif
                        </span>
                    </div>
                </div>

                <!-- Reference Tables (always visible) -->
                <div class="space-y-6">
                    <!-- Blood Pressure Reference Table -->
                    <div class="metric-card p-5">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-heartbeat mr-2 text-indigo-500"></i> Blood Pressure Categories
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Systolic (upper)</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">and/or</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diastolic (lower)</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="{{ $user->blood_pressure_systolic < 120 && $user->blood_pressure_diastolic < 80 ? 'bg-green-50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium">Normal</td>
                                        <td class="px-4 py-3 whitespace-nowrap">Less than 120</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">and</td>
                                        <td class="px-4 py-3 whitespace-nowrap">Less than 80</td>
                                    </tr>
                                    <tr class="{{ ($user->blood_pressure_systolic >= 120 && $user->blood_pressure_systolic <= 129) && $user->blood_pressure_diastolic < 80 ? 'bg-yellow-50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium">Elevated</td>
                                        <td class="px-4 py-3 whitespace-nowrap">120 - 129</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">and</td>
                                        <td class="px-4 py-3 whitespace-nowrap">Less than 80</td>
                                    </tr>
                                    <tr class="{{ ($user->blood_pressure_systolic >= 130 && $user->blood_pressure_systolic <= 139) || ($user->blood_pressure_diastolic >= 80 && $user->blood_pressure_diastolic <= 89) ? 'bg-orange-50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium">High (Stage 1)</td>
                                        <td class="px-4 py-3 whitespace-nowrap">130 - 139</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">or</td>
                                        <td class="px-4 py-3 whitespace-nowrap">80 - 89</td>
                                    </tr>
                                    <tr class="{{ $user->blood_pressure_systolic >= 140 || $user->blood_pressure_diastolic >= 90 ? 'bg-red-50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium">High (Stage 2)</td>
                                        <td class="px-4 py-3 whitespace-nowrap">140 or higher</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center">or</td>
                                        <td class="px-4 py-3 whitespace-nowrap">90 or higher</td>
                                    </tr>
                                    <tr class="{{ $user->blood_pressure_systolic > 180 || $user->blood_pressure_diastolic > 120 ? 'bg-red-100' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium text-red-600">Hypertensive Crisis</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-red-600">Higher than 180</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-center text-red-600">and/or</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-red-600">Higher than 120</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-sm text-gray-500 mt-3 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-indigo-400"></i> Your current reading:
                            @if($user->blood_pressure_systolic && $user->blood_pressure_diastolic)
                                {{ $user->blood_pressure_systolic }}/{{ $user->blood_pressure_diastolic }} mmHg
                            @else
                                Not available
                            @endif
                        </p>
                    </div>

                    <!-- Blood Sugar Reference Table -->
                    <div class="metric-card p-5">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-tint mr-2 text-green-500"></i> Blood Sugar Levels (Fasting)
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">mmol/L</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">mg/dL</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="{{ $user->blood_sugar < 3.9 ? 'bg-blue-50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium">Low</td>
                                        <td class="px-4 py-3 whitespace-nowrap">Below 3.9</td>
                                        <td class="px-4 py-3 whitespace-nowrap">Below 70</td>
                                    </tr>
                                    <tr class="{{ $user->blood_sugar >= 3.9 && $user->blood_sugar <= 5.5 ? 'bg-green-50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium">Normal</td>
                                        <td class="px-4 py-3 whitespace-nowrap">3.9 - 5.5</td>
                                        <td class="px-4 py-3 whitespace-nowrap">70 - 99</td>
                                    </tr>
                                    <tr class="{{ $user->blood_sugar > 5.5 && $user->blood_sugar <= 6.9 ? 'bg-yellow-50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium">Prediabetes</td>
                                        <td class="px-4 py-3 whitespace-nowrap">5.6 - 6.9</td>
                                        <td class="px-4 py-3 whitespace-nowrap">100 - 125</td>
                                    </tr>
                                    <tr class="{{ $user->blood_sugar >= 7 ? 'bg-red-50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium">Diabetes</td>
                                        <td class="px-4 py-3 whitespace-nowrap">7.0 or higher</td>
                                        <td class="px-4 py-3 whitespace-nowrap">126 or higher</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-sm text-gray-500 mt-3 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-green-400"></i> Your current reading:
                            @if($user->blood_sugar)
                                {{ $user->blood_sugar }} mmol/L ({{ round($user->blood_sugar * 18) }} mg/dL)
                            @else
                                Not available
                            @endif
                        </p>
                    </div>

                    <!-- Cholesterol Reference Table -->
                    <div class="metric-card p-5">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-vial mr-2 text-amber-500"></i> Cholesterol Levels (Total)
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">mg/dL</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">mmol/L</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr class="{{ $user->cholesterol < 200 ? 'bg-green-50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium">Desirable</td>
                                        <td class="px-4 py-3 whitespace-nowrap">Below 200</td>
                                        <td class="px-4 py-3 whitespace-nowrap">Below 5.2</td>
                                    </tr>
                                    <tr class="{{ $user->cholesterol >= 200 && $user->cholesterol <= 239 ? 'bg-yellow-50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium">Borderline High</td>
                                        <td class="px-4 py-3 whitespace-nowrap">200 - 239</td>
                                        <td class="px-4 py-3 whitespace-nowrap">5.2 - 6.2</td>
                                    </tr>
                                    <tr class="{{ $user->cholesterol >= 240 ? 'bg-red-50' : '' }}">
                                        <td class="px-4 py-3 whitespace-nowrap font-medium">High</td>
                                        <td class="px-4 py-3 whitespace-nowrap">240 or higher</td>
                                        <td class="px-4 py-3 whitespace-nowrap">6.2 or higher</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <p class="text-sm text-gray-500 mt-3 flex items-center">
                            <i class="fas fa-info-circle mr-2 text-amber-400"></i> Your current reading:
                            @if($user->cholesterol)
                                {{ $user->cholesterol }} mg/dL ({{ round($user->cholesterol * 0.02586, 2) }} mmol/L)
                            @else
                                Not available
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-8 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} HEALTHY Kitchen. All rights reserved. <br>
            Recipe data powered by <a href="https://spoonacular.com/food-api" target="_blank" class="text-blue-500 hover:underline">Spoonacular API</a>. <br>
        </div>
    </footer>
</body>
</html>
