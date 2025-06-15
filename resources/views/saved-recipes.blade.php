<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Saved Recipes - HEALTHY Kitchen</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
    .recipe-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    .recipe-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
    }
    .recipe-buttons {
      display: flex;
      gap: 0.5rem;
      margin-top: auto;
    }
    .recipe-btn {
      background-color: var(--primary);
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 0.25rem;
      font-weight: 500;
      flex: 1;
      transition: all 0.2s ease;
      cursor: pointer;
    }
    .recipe-btn:hover {
      background-color: var(--primary-dark);
    }
    .btn-save {
      background-color: #f8fafc;
      color: var(--primary);
      border: 1px solid #e2e8f0;
      padding: 0.5rem 1rem;
      border-radius: 0.25rem;
      font-weight: 500;
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .btn-save:hover {
      background-color: #e0e7ff;
      border-color: #c7d2fe;
    }
    .btn-save.saved {
      background-color: var(--primary);
      color: white;
      border-color: var(--primary);
    }

    /* Modal Styles */
    .modal-content {
        margin: 0 auto;
        width: 90%;
        max-width: 800px;
    }

    .modal-header {
        position: relative;
        text-align: center;
        padding: 1.5rem 2rem 0;
    }

    .modal-close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: white;
        border-radius: 50%;
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        cursor: pointer;
        z-index: 10;
    }

    .modal-body {
        padding: 0 2rem 2rem;
        text-align: center;
    }

    .modal-title {
        font-size: 1.75rem;
        margin-bottom: 1rem;
        color: var(--dark-text);
    }

    .modal-image-container {
        margin: 0 auto 1.5rem;
        max-width: 100%;
    }

    .modal-image {
        max-height: 300px;
        width: auto;
        border-radius: 8px;
        margin: 0 auto;
    }

    .modal-section {
        margin-bottom: 1.5rem;
        text-align: left;
    }

    .modal-section-title {
        font-size: 1.25rem;
        margin-bottom: 1rem;
        color: var(--dark-text);
        text-align: center;
    }

    /* Delete Button Styles */
    .btn-delete {
        background-color: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
        font-weight: 500;
        flex: 1;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-delete:hover {
        background-color: #fecaca;
        border-color: #fca5a5;
    }

    .btn-delete i {
        margin-right: 0.5rem;
    }

    /* Confirmation Modal */
    #confirmationModal {
        position: fixed;
        inset: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 50;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    #confirmationModal.show {
        display: flex;
        opacity: 1;
    }

    .confirmation-dialog {
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        width: 100%;
        max-width: 24rem;
        padding: 1.5rem;
    }

    .confirmation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .confirmation-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark-text);
    }

    .confirmation-close {
        color: #6b7280;
        cursor: pointer;
    }

    .confirmation-body {
        color: #6b7280;
        margin-bottom: 1.5rem;
    }

    .confirmation-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }

    .confirmation-cancel {
        background-color: #f3f4f6;
        color: #4b5563;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        cursor: pointer;
    }

    .confirmation-confirm {
        background-color: #ef4444;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        cursor: pointer;
    }

    /* Notification Toast */
    #notificationToast {
        position: fixed;
        bottom: 1rem;
        right: 1rem;
        z-index: 50;
        display: none;
        background-color: white;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        padding: 1rem;
        min-width: 20rem;
        border-left: 4px solid;
        transform: translateY(1rem);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }

    #notificationToast.show {
        display: flex;
        transform: translateY(0);
        opacity: 1;
    }

    .toast-success {
        border-left-color: #10b981;
    }

    .toast-success #toastIcon {
        color: #10b981;
    }

    .toast-error {
        border-left-color: #ef4444;
    }

    .toast-error #toastIcon {
        color: #ef4444;
    }

    .toast-content {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .toast-close {
        color: #9ca3af;
        cursor: pointer;
    }

    #notification {
      position: fixed;
      top: 2rem;
      left: 50%;
      transform: translateX(-50%) translateY(-50px);
      background-color: var(--primary);
      color: white;
      padding: 1rem 2rem;
      border-radius: 12px;
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
      opacity: 0;
      transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      z-index: 1000;
      display: flex;
      align-items: center;
      max-width: 90%;
      width: auto;
      min-width: 300px;
    }
    #notification.success { background-color: var(--secondary); }
    #notification.error { background-color: #ef4444; }
    #notification.warning { background-color: var(--accent); }
    #notification i { margin-right: 0.75rem; font-size: 1.25rem; }
  </style>
</head>
<body class="min-h-screen">
  @if(session('status'))
  <div id="notification" class="success">
    <i class="fas fa-check-circle"></i>
    <span>{{ session('status') }}</span>
  </div>
  @endif

  <!-- Confirmation Modal -->
  <div id="confirmationModal" class="hidden">
    <div class="confirmation-dialog">
      <div class="confirmation-header">
        <h3 class="confirmation-title">Confirm Deletion</h3>
        <button onclick="closeConfirmationModal()" class="confirmation-close">
          <i class="fas fa-times"></i>
        </button>
      </div>
      <div class="confirmation-body">
        Are you sure you want to delete this recipe?
      </div>
      <div class="confirmation-footer">
        <button onclick="closeConfirmationModal()" class="confirmation-cancel">Cancel</button>
        <button id="confirmDeleteBtn" class="confirmation-confirm">Delete</button>
      </div>
    </div>
  </div>

  <!-- Notification Toast -->
  <div id="notificationToast" class="hidden">
    <div class="toast-content">
      <i id="toastIcon" class="fas fa-check-circle"></i>
      <div>
        <h4 id="toastTitle" class="font-semibold"></h4>
        <p id="toastMessage" class="text-sm text-gray-600"></p>
      </div>
    </div>
    <button onclick="hideNotification()" class="toast-close ml-4">
      <i class="fas fa-times"></i>
    </button>
  </div>

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

  <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <div class="mb-8 flex items-center justify-center space-x-4">
        <div class="hidden sm:block">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Your Personalized Recipe Library</h1>
            <div class="h-1 w-16 bg-indigo-400 mt-2 rounded-full"></div>
        </div>
        <div class="hidden sm:block">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </div>
    </div>

    <form method="GET" action="{{ route('saved.recipes') }}" class="mb-8 relative max-w-2xl mx-auto">
        <div class="relative">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="Search your saved recipes..."
                class="w-full pl-12 pr-4 py-3 border border-gray-200 rounded-full shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
            />
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                </svg>
            </div>
            <button
                type="submit"
                class="absolute right-1 top-1/2 transform -translate-y-1/2 bg-indigo-600 text-white px-4 py-2 rounded-full hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
            >
                Search
            </button>
        </div>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($recommendedRecipes as $recipe)
        @if($recipe)
        <div class="recipe-card overflow-hidden bg-white rounded-lg shadow-md flex flex-col h-full">
          <img src="{{ $recipe['image'] ?? asset('images/placeholder.jpg') }}" alt="{{ $recipe['title'] ?? 'Recipe Title' }}" class="w-full h-48 object-cover" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.jpg') }}'">
          <div class="p-4 flex flex-col flex-grow">
            <h3 class="text-xl font-semibold text-gray-800 mb-3 line-clamp-2">{{ $recipe['title'] ?? 'No Title Available' }}</h3>
            <div class="flex items-center text-sm text-gray-500 mb-4">
              <span class="mr-3"><i class="fas fa-fire mr-1"></i> {{ $recipe['nutrition']['calories'] ?? 'N/A' }} cal</span>
              <span><i class="fas fa-dumbbell mr-1"></i> {{ $recipe['nutrition']['protein'] ?? 'N/A' }}g protein</span>
            </div>

            <div class="recipe-buttons mt-auto">
                <button onclick='showModal(@json($recipe))' class="recipe-btn">
                    Get Recipe
                </button>

                <button class="btn-delete" onclick="deleteRecipe({{ $recipe['id'] }}, this)">
                    <i class="fas fa-trash-alt"></i>
                    <span>Delete</span>
                </button>
            </div>
          </div>
        </div>
        @endif
      @endforeach
    </div>
    <div class="pagination mt-8 flex justify-center">
        @if ($currentPage > 1)
            <a href="{{ route('saved.recipes', ['page' => $currentPage - 1]) }}" class="px-3 py-1 border rounded text-indigo-600 hover:bg-indigo-50 mx-1">Previous</a>
        @endif

        @if ($currentPage > 3)
            <a href="{{ route('saved.recipes', ['page' => 1]) }}" class="px-3 py-1 border rounded text-indigo-600 hover:bg-indigo-50 mx-1">1</a>
            <span class="px-2 text-gray-400">...</span>
        @endif

        @foreach (range(max(1, $currentPage - 2), min($currentPage + 2, $totalPages)) as $page)
            <a href="{{ route('saved.recipes', ['page' => $page]) }}" class="px-3 py-1 border rounded mx-1 {{ $page == $currentPage ? 'bg-indigo-600 text-white' : 'text-indigo-600 hover:bg-indigo-50' }}">{{ $page }}</a>
        @endforeach

        @if ($currentPage < $totalPages - 2)
            <span class="px-2 text-gray-400">...</span>
            <a href="{{ route('saved.recipes', ['page' => $totalPages]) }}" class="px-3 py-1 border rounded text-indigo-600 hover:bg-indigo-50 mx-1">{{ $totalPages }}</a>
        @endif

        @if ($currentPage < $totalPages)
            <a href="{{ route('saved.recipes', ['page' => $currentPage + 1]) }}" class="px-3 py-1 border rounded text-indigo-600 hover:bg-indigo-50 mx-1">Next</a>
        @endif
    </div>
    </main>

  <div id="recipeModal" class="fixed z-50 inset-0 bg-black bg-opacity-50 hidden items-center justify-center p-4">
    <div class="modal-content bg-white rounded-lg shadow-xl overflow-hidden max-h-[90vh] overflow-y-auto">
      <div class="modal-header">
        <button onclick="closeModal()" class="modal-close text-gray-500 hover:text-gray-700">
          <i class="fas fa-times text-lg"></i>
        </button>
        <h3 id="modalTitle" class="modal-title"></h3>
      </div>
      <div class="modal-body">
        <div class="modal-image-container">
          <img id="modalImage" src="" alt="" class="modal-image" />
        </div>
        <div class="flex flex-wrap gap-4 text-sm text-gray-600 mb-6 justify-center">
          <span><i class="fas fa-clock mr-1"></i> <span id="modalTime">--</span> mins</span>
          <span><i class="fas fa-utensils mr-1"></i> <span id="modalServings">--</span> servings</span>
        </div>
        <div class="modal-section bg-blue-50 p-4 rounded-lg border border-blue-100">
          <h4 class="modal-section-title">Why This Recipe Is Recommended</h4>
          <p id="modalExplanation" class="text-gray-700"></p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
          <div class="modal-section">
            <h4 class="modal-section-title">Ingredients</h4>
            <ul id="modalIngredients" class="space-y-2 text-gray-600"></ul>
          </div>
          <div class="modal-section">
            <h4 class="modal-section-title">Nutrition</h4>
            <div id="modalNutrition" class="space-y-2 text-gray-600"></div>
          </div>
        </div>
        <div class="modal-section">
          <h4 class="modal-section-title">Instructions</h4>
          <ol id="modalInstructions" class="list-decimal pl-5 space-y-2 text-gray-600 text-left max-w-2xl mx-auto"></ol>
        </div>
        <div class="mt-6">
          <a id="modalLink" href="#" target="_blank" class="recipe-btn inline-flex items-center px-6 py-3 rounded-md text-white font-medium">
            <i class="fas fa-external-link-alt mr-2"></i> View Full Recipe
          </a>
        </div>
      </div>
    </div>
  </div>

  <footer class="bg-white border-t mt-12 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-500 text-sm">
        &copy; {{ date('Y') }} HEALTHY Kitchen. All rights reserved. <br>
        Recipe data powered by <a href="https://spoonacular.com/food-api" target="_blank" class="text-blue-500 hover:underline">Spoonacular API</a>. <br>
    </div>
  </footer>

  <script>
    let currentRecipeId = null;
    let currentButtonElement = null;

    // Show modal with recipe details
    function showModal(recipe) {
        console.log('📦 Full recipe object:', recipe);

        document.getElementById('modalTitle').textContent = recipe.title || 'Untitled';
        document.getElementById('modalImage').src = recipe.image || '{{ asset('images/placeholder.jpg') }}';
        document.getElementById('modalLink').href = recipe.sourceUrl || '#';
        document.getElementById('modalTime').textContent = recipe.readyInMinutes || '--';
        document.getElementById('modalServings').textContent = recipe.servings || '--';
        document.getElementById('modalExplanation').textContent =
            recipe.explanation || 'This recipe was selected based on your health profile and dietary needs.';

        // 🍽️ Ingredients
        const ingredientsList = document.getElementById('modalIngredients');
        ingredientsList.innerHTML = '';
        if (recipe.ingredients && recipe.ingredients.length > 0) {
            recipe.ingredients.forEach(ingredient => {
                const li = document.createElement('li');
                li.className = 'flex items-start';
                li.innerHTML = `<i class="fas fa-check-circle text-green-500 mt-1 mr-2 text-sm"></i><span>${ingredient}</span>`;
                ingredientsList.appendChild(li);
            });
        } else {
            ingredientsList.innerHTML = '<li>No ingredients listed</li>';
        }

        // 🔬 Nutrition
        const nutritionContainer = document.getElementById('modalNutrition');
        nutritionContainer.innerHTML = '';

        const nutrition = recipe.nutrition || {};
        console.log('📊 Nutrition:', nutrition);

        const nutrients = [
            { name: 'Calories', key: 'calories', unit: '' },
            { name: 'Protein', key: 'protein', unit: '' },
            { name: 'Carbs', key: 'carbohydrates', unit: '' },
            { name: 'Fat', key: 'fat', unit: '' },
            { name: 'Fiber', key: 'fiber', unit: '' },
            { name: 'Sugar', key: 'sugar', unit: '' }
        ];

        let hasNutrition = false;

        nutrients.forEach(n => {
            const value = nutrition[n.key] || null;

            if (value) {
                const div = document.createElement('div');
                div.className = 'flex justify-between';
                div.innerHTML = `<span class="font-medium">${n.name}</span><span>${value}</span>`;
                nutritionContainer.appendChild(div);
                hasNutrition = true;
            }
        });

        if (!hasNutrition) {
            nutritionContainer.innerHTML = '<p class="text-sm text-gray-500">No nutrition data available</p>';
        }

        // 📋 Instructions
        const instructionsList = document.getElementById('modalInstructions');
        instructionsList.innerHTML = '';
        if (recipe.instructions && recipe.instructions.length > 0) {
            recipe.instructions.forEach(step => {
                const li = document.createElement('li');
                li.textContent = step;
                instructionsList.appendChild(li);
            });
        } else {
            instructionsList.innerHTML = '<li>No instructions available</li>';
        }

        // Show modal
        document.getElementById('recipeModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    }

    function closeModal() {
      document.getElementById('recipeModal').classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }

    function deleteRecipe(recipeId, buttonElement) {
        currentRecipeId = recipeId;
        currentButtonElement = buttonElement;
        const modal = document.getElementById('confirmationModal');
        modal.classList.remove('hidden');
        modal.classList.add('show');

        document.getElementById('confirmDeleteBtn').onclick = confirmDelete;
    }

    function closeConfirmationModal() {
        const modal = document.getElementById('confirmationModal');
        modal.classList.add('hidden');
        modal.classList.remove('show');
    }

    function confirmDelete() {
        closeConfirmationModal();

        fetch(`/saved-recipes/${currentRecipeId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const card = currentButtonElement.closest('.recipe-card');
                if (card) card.remove();
                showToastNotification(data.message, 'success');
            } else {
                showToastNotification(data.message || 'Failed to delete recipe.', 'error');
            }
        })
        .catch(() => {
            showToastNotification('Something went wrong while deleting.', 'error');
        });
    }

    function showToastNotification(message, type = 'success') {
        const toast = document.getElementById('notificationToast');
        const toastTitle = document.getElementById('toastTitle');
        const toastMessage = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');

        // Set content and style based on type
        toastTitle.textContent = type === 'success' ? 'Success' : 'Error';
        toastMessage.textContent = message;

        // Update icon
        toastIcon.className = type === 'success'
            ? 'fas fa-check-circle'
            : 'fas fa-exclamation-circle';

        // Update styling
        toast.className = `fixed bottom-4 right-4 z-50 flex items-start rounded-lg shadow-lg p-4 min-w-64 border-l-4 ${
            type === 'success' ? 'toast-success' : 'toast-error'
        } bg-white show`;

        // Auto-hide after 5 seconds
        setTimeout(hideNotification, 5000);
    }

    function hideNotification() {
        const toast = document.getElementById('notificationToast');
        toast.classList.remove('show');
        setTimeout(() => {
            toast.className = 'hidden';
        }, 300);
    }

    // Show initial notification if exists
    document.addEventListener('DOMContentLoaded', function() {
        const notification = document.getElementById('notification');
        if (notification) {
            setTimeout(() => {
                notification.style.opacity = '1';
                notification.style.transform = 'translateX(-50%) translateY(0)';

                setTimeout(() => {
                    notification.style.opacity = '0';
                    notification.style.transform = 'translateX(-50%) translateY(-50px)';
                    setTimeout(() => notification.remove(), 500);
                }, 3000);
            }, 100);
        }
    });
  </script>
</body>
</html>
