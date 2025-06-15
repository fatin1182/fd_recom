<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - HEALTHY Kitchen</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
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
        }
        .register-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        .input-field {
            transition: all 0.3s ease;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
        }
        .input-field:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }
        .btn-primary {
            background-color: var(--primary);
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        .password-strength {
            height: 4px;
            margin-top: 4px;
            background-color: #e2e8f0;
            border-radius: 2px;
            overflow: hidden;
        }
        .password-strength-bar {
            height: 100%;
            transition: width 0.3s ease, background-color 0.3s ease;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="register-card w-full max-w-md p-8 sm:p-10">
        <div class="text-center mb-8">
            <div class="flex justify-center">
                <i class="fas fa-utensils text-3xl text-indigo-600 mr-2"></i>
                <span class="text-2xl font-bold text-gray-800">HEALTHY Kitchen</span>
            </div>
            <h2 class="mt-6 text-2xl font-bold text-gray-900">Create your account</h2>
            <p class="mt-2 text-sm text-gray-600">
                Already have an account? <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Log in here</a>
            </p>
        </div>

        <form method="POST" action="{{ route('register') }}" class="space-y-6" id="registrationForm">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <div class="mt-1">
                    <input id="name" name="name" type="text" autocomplete="name" required
                           class="input-field w-full px-4 py-2"
                           value="{{ old('name') }}"
                           placeholder="John Doe">
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email address</label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="input-field w-full px-4 py-2"
                           value="{{ old('email') }}"
                           placeholder="you@example.com">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="mt-1 relative">
                    <input id="password" name="password" type="password" autocomplete="new-password" required
                           class="input-field w-full px-4 py-2"
                           placeholder="••••••••">
                    <div class="password-strength">
                        <div class="password-strength-bar" id="passwordStrengthBar"></div>
                    </div>
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <p class="mt-1 text-xs text-gray-500">Use 8 or more characters with a mix of letters, numbers & symbols</p>
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <div class="mt-1">
                    <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required
                           class="input-field w-full px-4 py-2"
                           placeholder="••••••••">
                    @error('password_confirmation')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit"
                        class="btn-primary w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Account
                    <i class="fas fa-user-plus ml-2 mt-0.5"></i>
                </button>
            </div>
        </form>
    </div>

    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const password = this.value;
            const strengthBar = document.getElementById('passwordStrengthBar');
            let strength = 0;

            // Length check
            if (password.length >= 8) strength += 1;
            if (password.length >= 12) strength += 1;

            // Character variety checks
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;

            // Update strength bar
            const width = (strength / 5) * 100;
            let color;

            if (strength <= 2) color = '#ef4444'; // red
            else if (strength <= 3) color = '#f59e0b'; // amber
            else color = '#10b981'; // green

            strengthBar.style.width = width + '%';
            strengthBar.style.backgroundColor = color;
        });
    </script>
</body>
</html>
