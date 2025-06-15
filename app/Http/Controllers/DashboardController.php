<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // In DashboardController.php

    public function index()
    {
        $user = auth()->user(); // Retrieve the logged-in user

        // Assuming you have these fields in your User model
        $gender = $user->gender;
        $age = $user->age;
        $name = $user->name;

        return view('dashboard', compact('user', 'gender', 'age', 'name'));
    }
}
