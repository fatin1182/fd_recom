<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Health Data - HEALTHY Kitchen</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                        'primary-dark': '#4338ca',
                        secondary: '#10b981',
                        accent: '#f59e0b',
                        'light-bg': '#f8fafc',
                        'dark-text': '#1e293b',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Custom toggle switch */
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }

        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .toggle-slider {
            background-color: #4f46e5;
        }

        input:checked + .toggle-slider:before {
            transform: translateX(26px);
        }

        /* Card styling */
        .health-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .health-card:hover {
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }

        /* Input styling */
        .health-input {
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 16px;
        }

        .health-input:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        /* Button styling */
        .health-btn {
            transition: all 0.3s ease;
            border-radius: 10px;
            padding: 12px 24px;
            font-weight: 500;
            font-size: 16px;
        }

        .health-btn-primary {
            background-color: #4f46e5;
            color: white;
        }

        .health-btn-primary:hover {
            background-color: #4338ca;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .health-btn-secondary {
            background-color: white;
            border: 1px solid #e2e8f0;
            color: #4f46e5;
        }

        .health-btn-secondary:hover {
            background-color: #f8fafc;
            transform: translateY(-1px);
        }

        /* Notification Toast */
        .health-toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 50;
            display: flex;
            align-items: center;
            padding: 16px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            background: white;
            max-width: 400px;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .health-toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .health-toast.success {
            border-left: 4px solid #10b981;
        }

        .health-toast.error {
            border-left: 4px solid #ef4444;
        }

        .health-toast-icon {
            font-size: 20px;
            margin-right: 12px;
        }

        .health-toast.success .health-toast-icon {
            color: #10b981;
        }

        .health-toast.error .health-toast-icon {
            color: #ef4444;
        }

        /* Section styling */
        .health-section {
            margin-bottom: 32px;
        }

        .health-section-title {
            font-size: 20px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f1f5f9;
        }

        /* Label styling */
        .health-label {
            display: block;
            font-size: 16px;
            font-weight: 500;
            color: #334155;
            margin-bottom: 8px;
        }

        /* Helper text */
        .health-helper {
            font-size: 14px;
            color: #64748b;
            margin-top: 4px;
        }

        /* Animation for form fields */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-field {
            animation: fadeInUp 0.3s ease forwards;
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .health-input {
                padding: 10px 14px;
                font-size: 15px;
            }

            .health-btn {
                padding: 10px 20px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-gray-50 font-sans">
    <!-- Notification Toast -->
    <div id="notificationToast" class="health-toast hidden">
        <i id="toastIcon" class="health-toast-icon fas fa-check-circle"></i>
        <div>
            <h4 id="toastTitle" class="font-semibold text-gray-800"></h4>
            <p id="toastMessage" class="text-gray-600 mt-1"></p>
        </div>
        <button onclick="hideNotification()" class="ml-4 text-gray-400 hover:text-gray-600">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Session Notification -->
    @if(session('status'))
    <div class="health-toast success show fixed top-4 right-4">
        <i class="health-toast-icon fas fa-check-circle"></i>
        <div>
            <h4 class="font-semibold text-gray-800">Success</h4>
            <p class="text-gray-600 mt-1">{{ session('status') }}</p>
        </div>
    </div>
    @endif

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
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Home</a>
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Profile Information</a>
                    <a href="{{ route('food.recommendations') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Food Recommendation</a>
                    <a href="{{ route('saved.recipes') }}" class="text-gray-700 hover:text-indigo-600 font-medium">Saved Recipes</a>
                </div>
                <div class="flex items-center">
                    <div class="relative group">
                        <button type="button" class="flex items-center focus:outline-none">
                            <span class="text-gray-600 mr-2">{{ Auth::user()->name }}</span>
                            <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff" alt="User avatar">
                        </button>
                        <div class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-1 group-hover:translate-y-0 z-50">
                            <div class="py-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600 transition-colors duration-200 flex items-center">
                                        <i class="fas fa-sign-out-alt mr-2 text-indigo-500"></i>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Health Data Header (outside form) -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Your Health Profile</h1>
                <p class="text-gray-600 mt-2">Keep your health information up to date for personalized recommendations</p>
            </div>
            <div class="flex space-x-4">
                <button onclick="resetForm()" class="health-btn health-btn-secondary flex items-center">
                    <i class="fas fa-redo mr-2"></i> Reset
                </button>
                <a href="{{ url()->previous() }}" class="health-btn health-btn-secondary flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>

        <!-- Error Display -->
        @if ($errors->any())
        <div class="health-card p-6 mb-8 bg-red-50 border-l-4 border-red-500 animate-field">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500 mt-1 mr-3 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium text-red-800">There were {{ $errors->count() }} error(s) with your submission</h3>
                    <div class="mt-2 text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('health-data.update') }}" id="healthDataForm" class="space-y-8">
            @csrf
            @method('PATCH')

            <!-- Basic Information Section -->
            <div class="health-card p-6 md:p-8 animate-field" style="animation-delay: 0.1s">
                <h2 class="health-section-title">Basic Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div>
                        <label for="name" class="health-label">Full Name</label>
                        <input id="name" name="name" type="text"
                               class="health-input w-full"
                               value="{{ old('name', $user->name) }}"
                               readonly>
                    </div>

                    <!-- Age -->
                    <div>
                        <label for="age" class="health-label">Age</label>
                        <input id="age" name="age" type="number"
                            class="health-input w-full"
                            value="{{ old('age', $user->age) }}"
                            placeholder="Enter your age">
                        <p class="health-helper">We use this to personalize your recommendations</p>
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="health-label">Gender</label>
                        <select id="gender" name="gender" class="health-input w-full">
                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>

                    <!-- Empty column for alignment -->
                    <div></div>

                    <!-- Height -->
                    <div>
                        <label for="height" class="health-label">Height (cm)</label>
                        <input id="height" name="height" type="number" step="0.1"
                            class="health-input w-full"
                            value="{{ old('height', auth()->user()->height) }}"
                            placeholder="e.g. 170">
                    </div>

                    <!-- Weight -->
                    <div>
                        <label for="weight" class="health-label">Weight (kg)</label>
                        <input id="weight" name="weight" type="number" step="0.1"
                            class="health-input w-full"
                            value="{{ old('weight', auth()->user()->weight) }}"
                            placeholder="e.g. 65">
                        <p class="health-helper">We use this to calculate your BMI</p>
                    </div>
                </div>
            </div>

            <!-- Health Conditions Section -->
            <div class="health-card p-6 md:p-8 animate-field" style="animation-delay: 0.2s">
                <h2 class="health-section-title">Health Conditions</h2>
                <p class="text-gray-600 mb-6">Please let us know if you have any of these conditions</p>

                <!-- High Blood Pressure -->
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-medium text-gray-800">High Blood Pressure</h3>
                            <p class="text-gray-500 text-sm">Also known as hypertension</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="has_blood_pressure" id="has_blood_pressure"
                                {{ old('blood_pressure_systolic', auth()->user()->blood_pressure_systolic) || old('blood_pressure_diastolic', auth()->user()->blood_pressure_diastolic) ? 'checked' : '' }}>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div id="blood_pressure_fields" class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4"
                        style="{{ !old('blood_pressure_systolic', auth()->user()->blood_pressure_systolic) && !old('blood_pressure_diastolic', auth()->user()->blood_pressure_diastolic) ? 'display: none;' : '' }}">
                        <!-- Systolic -->
                        <div>
                            <label for="blood_pressure_systolic" class="health-label">
                                Systolic Pressure
                                <span class="health-helper">Upper number (mmHg)</span>
                            </label>
                            <input id="blood_pressure_systolic" name="blood_pressure_systolic" type="number" min="50" max="300"
                                   class="health-input w-full"
                                   value="{{ old('blood_pressure_systolic', auth()->user()->blood_pressure_systolic) }}"
                                   placeholder="120">
                        </div>

                        <!-- Diastolic -->
                        <div>
                            <label for="blood_pressure_diastolic" class="health-label">
                                Diastolic Pressure
                                <span class="health-helper">Lower number (mmHg)</span>
                            </label>
                            <input id="blood_pressure_diastolic" name="blood_pressure_diastolic" type="number" min="30" max="200"
                                   class="health-input w-full"
                                   value="{{ old('blood_pressure_diastolic', auth()->user()->blood_pressure_diastolic) }}"
                                   placeholder="80">
                        </div>
                    </div>
                </div>

                <!-- Blood Sugar and Cholesterol -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- High Blood Sugar -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-medium text-gray-800">High Blood Sugar</h3>
                                <p class="text-gray-500 text-sm">Diabetes or pre-diabetes</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="has_blood_sugar" id="has_blood_sugar"
                                    {{ old('blood_sugar', auth()->user()->blood_sugar) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div id="blood_sugar_field" class="mt-4"
                            style="{{ !old('blood_sugar', auth()->user()->blood_sugar) ? 'display: none;' : '' }}">
                            <label for="blood_sugar" class="health-label">
                                Blood Sugar Level
                                <span class="health-helper">mmol/L</span>
                            </label>
                            <input id="blood_sugar" name="blood_sugar" type="text"
                                   class="health-input w-full"
                                   value="{{ old('blood_sugar', auth()->user()->blood_sugar) }}"
                                   placeholder="e.g. 5.4">
                        </div>
                    </div>

                    <!-- Cholesterol -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="font-medium text-gray-800">High Cholesterol</h3>
                                <p class="text-gray-500 text-sm">Elevated lipid levels</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" name="has_cholesterol" id="has_cholesterol"
                                    {{ old('cholesterol', auth()->user()->cholesterol) ? 'checked' : '' }}>
                                <span class="toggle-slider"></span>
                            </label>
                        </div>

                        <div id="cholesterol_field" class="mt-4"
                            style="{{ !old('cholesterol', auth()->user()->cholesterol) ? 'display: none;' : '' }}">
                            <label for="cholesterol" class="health-label">
                                Cholesterol Level
                                <span class="health-helper">mg/dL</span>
                            </label>
                            <input id="cholesterol" name="cholesterol" type="text"
                                   class="health-input w-full"
                                   value="{{ old('cholesterol', auth()->user()->cholesterol) }}"
                                   placeholder="e.g. 200">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Submission -->
            <div class="flex justify-center animate-field" style="animation-delay: 0.3s">
                <button type="submit" class="health-btn health-btn-primary flex items-center px-8 py-3 text-lg">
                    <i class="fas fa-save mr-2"></i> Save Health Profile
                </button>
            </div>
        </form>
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-12 py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} HEALTHY Kitchen. All rights reserved. <br>
            Recipe data powered by <a href="https://spoonacular.com/food-api" target="_blank" class="text-blue-500 hover:underline">Spoonacular API</a>. <br>
        </div>
    </footer>

    <script>
        // Toast Notification functions
        function showToastNotification(message, type = "success") {
            const toast = document.getElementById("notificationToast");
            const toastTitle = document.getElementById("toastTitle");
            const toastMessage = document.getElementById("toastMessage");
            const toastIcon = document.getElementById("toastIcon");

            // Set content and styles
            toastTitle.textContent = type === "success" ? "Success" : "Error";
            toastMessage.textContent = message;

            // Update styling
            toast.className = `health-toast ${type} show`;
            toastIcon.className = `health-toast-icon fas ${type === "success" ? "fa-check-circle" : "fa-exclamation-circle"}`;

            // Auto-hide after 5 seconds
            setTimeout(hideNotification, 5000);
        }

        function hideNotification() {
            const toast = document.getElementById("notificationToast");
            toast.classList.remove("show");
            setTimeout(() => {
                toast.className = "health-toast hidden";
            }, 300);
        }

        // Toggle blood pressure fields
        document.getElementById("has_blood_pressure").addEventListener("change", function() {
            const fields = document.getElementById("blood_pressure_fields");
            if (this.checked) {
                fields.style.display = "grid";
                setTimeout(() => {
                    fields.classList.add("animate-field");
                }, 10);
            } else {
                fields.style.display = "none";
                document.getElementById("blood_pressure_systolic").value = "";
                document.getElementById("blood_pressure_diastolic").value = "";
            }
        });

        // Toggle blood sugar field
        document.getElementById("has_blood_sugar").addEventListener("change", function() {
            const field = document.getElementById("blood_sugar_field");
            if (this.checked) {
                field.style.display = "block";
                setTimeout(() => {
                    field.classList.add("animate-field");
                }, 10);
            } else {
                field.style.display = "none";
                document.getElementById("blood_sugar").value = "";
            }
        });

        // Toggle cholesterol field
        document.getElementById("has_cholesterol").addEventListener("change", function() {
            const field = document.getElementById("cholesterol_field");
            if (this.checked) {
                field.style.display = "block";
                setTimeout(() => {
                    field.classList.add("animate-field");
                }, 10);
            } else {
                field.style.display = "none";
                document.getElementById("cholesterol").value = "";
            }
        });

        // Reset form function
        function resetForm() {
            const form = document.getElementById("healthDataForm");
            form.reset();

            // Clear health input fields
            document.getElementById("blood_pressure_systolic").value = "";
            document.getElementById("blood_pressure_diastolic").value = "";
            document.getElementById("blood_sugar").value = "";
            document.getElementById("cholesterol").value = "";

            // Hide all conditional fields
            document.getElementById("blood_pressure_fields").style.display = "none";
            document.getElementById("blood_sugar_field").style.display = "none";
            document.getElementById("cholesterol_field").style.display = "none";

            // Uncheck all toggle switches
            document.getElementById("has_blood_pressure").checked = false;
            document.getElementById("has_blood_sugar").checked = false;
            document.getElementById("has_cholesterol").checked = false;

            // Reset gender to original value
            document.getElementById("gender").value = '{{ old("gender", $user->gender) }}';

            showToastNotification("Form has been reset.", "success");
        }

        // Handle form submission with AJAX
        document.getElementById("healthDataForm").addEventListener("submit", async function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
            submitBtn.disabled = true;

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "Accept": "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                });

                const data = await response.json();

                if (!response.ok) {
                    throw data;
                }

                showToastNotification(data.message, "success");

                // Redirect after showing notification
                if (data.redirect) {
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                }
            } catch (error) {
                console.error("Error:", error);
                let errorMessage = "";

                if (error.errors) {
                    // Get first error message
                    const firstError = Object.values(error.errors)[0][0];
                    errorMessage = firstError;
                } else {
                    errorMessage = error.message || "There was an error updating your profile.";
                }

                showToastNotification(errorMessage, "error");
            } finally {
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            }
        });

        // Animate form fields on load
        document.addEventListener("DOMContentLoaded", function() {
            const fields = document.querySelectorAll(".animate-field");
            fields.forEach((field, index) => {
                field.style.opacity = "0";
                setTimeout(() => {
                    field.style.animation = "fadeInUp 0.5s ease forwards";
                    field.style.opacity = "1";
                }, index * 100);
            });

            // Auto-hide session notification
            const sessionNotification = document.querySelector(".health-toast.show");
            if (sessionNotification) {
                setTimeout(() => {
                    sessionNotification.classList.remove("show");
                }, 5000);
            }
        });
    </script>
</body>
</html>
