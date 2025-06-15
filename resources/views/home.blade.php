<!-- resources/views/home.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MetabolicSync - Food Recommendation System</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #10b981;
            --accent: #f59e0b;
            --light-bg: #f8fafc;
            --dark-text: #1e293b;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: var(--dark-text);
        }
        .hero-gradient {
            background: linear-gradient(135deg, rgba(248,250,252,1) 0%, rgba(226,232,240,1) 100%);
        }
        .btn-primary {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: var(--primary);
        }
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .feature-card {
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
        .nutrition-label {
            border-left: 4px solid var(--secondary);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
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
                    <a href="{{ route('home') }}"
                        class="{{ request()->routeIs('home') ? 'text-indigo-600 font-semibold' : 'text-gray-700' }} hover:text-indigo-600 font-medium transition-colors duration-200">
                    Home
                    </a>
                    @auth
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
                    @endauth
                </div>

                <div class="flex items-center">
                    @auth
                        <!-- Enhanced Dropdown for Logout -->
                        <div class="relative ml-3" x-data="{ open: false }">
                            <div class="flex items-center space-x-1">
                                <div class="relative">
                                    <button
                                        type="button"
                                        class="flex items-center focus:outline-none"
                                        id="user-menu-button"
                                        aria-expanded="false"
                                        aria-haspopup="true"
                                        @click="open = !open"
                                        @click.outside="open = false"
                                    >
                                        <span class="text-gray-600 mr-2">{{ Auth::user()->name }}</span>
                                        <div class="relative">
                                            <img class="h-8 w-8 rounded-full object-cover"
                                                src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff"
                                                alt="User avatar">
                                            <div class="absolute inset-0 rounded-full shadow-inner" aria-hidden="true"></div>
                                        </div>
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
                            </div>
                        </div>
                    @else
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-indigo-600 transition-colors duration-200 rounded-lg">
                                Login
                            </a>
                            <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 transition-colors duration-200 rounded-lg shadow-sm">
                                Register
                            </a>
                        </div>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="sm:hidden flex items-center">
                    <button type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" aria-controls="mobile-menu" aria-expanded="false" onclick="toggleMobileMenu()">
                        <span class="sr-only">Open main menu</span>
                        <i class="fas fa-bars"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>



    <!-- Hero Section -->
    <main class="flex-grow hero-gradient">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-24">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4 leading-tight">
                    Healthy Kitchen: <span class="text-indigo-600">Food Guidance for Metabolic Syndrome</span>
                </h1>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                    Dietary suggestions for patients with high blood pressure, high blood sugar, and high cholesterol
                </p>

                <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 max-w-3xl mx-auto mb-8 rounded">
                    <p class="font-semibold">Important Notice:</p>
                    <p>This system provides general guidance only and is not a substitute for professional medical advice. Always consult with your doctor before making dietary changes.</p>
                </div>

                <div class="flex justify-center space-x-4 mb-12">
                    @auth
                        <a href="{{ route('dashboard') }}"
                        class="btn-primary inline-flex items-center px-8 py-3 text-lg font-semibold text-white rounded-lg shadow-md">
                            View Recommendations <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                        class="btn-primary inline-flex items-center px-8 py-3 text-lg font-semibold text-white rounded-lg shadow-md">
                            Get Started <i class="fas fa-sign-in-alt ml-2"></i>
                        </a>
                    @endauth
                </div>

                <div class="bg-white rounded-xl shadow-lg overflow-hidden max-w-4xl mx-auto p-2">
                    <img src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80"
                        alt="Healthy foods for metabolic syndrome"
                        class="w-full h-64 md:h-96 object-cover rounded-lg">
                </div>
            </div>
        </div>
    </main>

    <!-- Features Section -->
    <section class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">How Healthy Kitchen Works</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    A simple tool to help patients with elevated metabolic values
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="bg-indigo-100 p-3 rounded-full mr-4">
                            <i class="fas fa-file-medical text-indigo-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold">Health Profile</h3>
                    </div>
                    <p class="text-gray-600">
                        Input your current health metrics including blood pressure, glucose levels, and cholesterol readings.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="bg-green-100 p-3 rounded-full mr-4">
                            <i class="fas fa-lightbulb text-green-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold">Instant Guidance</h3>
                    </div>
                    <p class="text-gray-600">
                        Receive immediate food recommendations based on your specific metabolic values.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-white p-6 rounded-lg shadow-md">
                    <div class="flex items-center mb-4">
                        <div class="bg-amber-100 p-3 rounded-full mr-4">
                            <i class="fas fa-exclamation-triangle text-amber-600 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold">Medical Disclaimer</h3>
                    </div>
                    <p class="text-gray-600">
                        Clear reminders that this is guidance only and medical consultation is essential.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Medical Information Section -->
    <section class="bg-indigo-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Understanding Metabolic Syndrome</h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    Why dietary changes matter for your health
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-3">The Risks of High Metabolic Markers</h3>
                    <p class="text-gray-600 mb-4">
                        Consistently high blood pressure, blood sugar, and cholesterol levels significantly increase your risk for:
                    </p>
                    <ul class="list-disc pl-5 text-gray-600 space-y-2">
                        <li>Heart disease and stroke</li>
                        <li>Type 2 diabetes complications</li>
                        <li>Kidney damage</li>
                        <li>Circulatory problems</li>
                    </ul>
                </div>

                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h3 class="text-xl font-semibold mb-3">How Diet Can Help</h3>
                    <p class="text-gray-600 mb-4">
                        While medication is often necessary, dietary changes can support your treatment by:
                    </p>
                    <ul class="list-disc pl-5 text-gray-600 space-y-2">
                        <li>Reducing sodium intake to lower blood pressure</li>
                        <li>Choosing complex carbs to manage blood sugar</li>
                        <li>Selecting heart-healthy fats to improve cholesterol</li>
                        <li>Increasing fiber for overall metabolic health</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section with Strong Warning -->
    <section class="bg-indigo-600 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="bg-white text-red-600 p-4 rounded-lg shadow-md max-w-4xl mx-auto mb-8">
                <h3 class="text-xl font-bold mb-2">Medical Disclaimer</h3>
                <p>Healthy Kitchen offers general food suggestions based on metabolic health values. These recommendations are not medical advice, do not constitute a doctor-patient relationship, and should not replace professional medical treatment. While our system considers your health metrics, the suggestions are not fully personalized medical nutrition therapy and may not address your individual health needs. Always consult with your healthcare provider before making dietary changes.</p>
            </div>

            <h2 class="text-3xl font-bold mb-4">Need Help With Your Diet?</h2>
            <p class="text-xl mb-8 max-w-3xl mx-auto">
                Use Healthy Kitchen as a starting point for discussions with your doctor or dietitian
            </p>
            <div class="flex justify-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center px-8 py-3 text-lg font-semibold bg-white text-indigo-600 rounded-lg shadow-md hover:bg-gray-100">
                        View Food Suggestions <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                @else
                    <a href="{{ route('register') }}"
                    class="inline-flex items-center px-8 py-3 text-lg font-semibold bg-white text-indigo-600 rounded-lg shadow-md hover:bg-gray-100">
                        Create Account <i class="fas fa-user-plus ml-2"></i>
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-400">
            &copy; {{ date('Y') }} HEALTHY Kitchen. All rights reserved. <br>
            Recipe data powered by <a href="https://spoonacular.com/food-api" target="_blank" class="text-blue-500 hover:underline">Spoonacular API</a>. <br>
        </div>
    </footer>
</body>
</html>
