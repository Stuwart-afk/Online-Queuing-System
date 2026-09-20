<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserControllers;

Route::get('/', function () {
    return view('welcome');
});

use Livewire\Volt\Volt;

Volt::route('/cashier', 'cashier-dashboard');
Volt::route('/display', 'student-display');

Route::post('/submit', [UserControllers::class ,'store'])->name('submit.form');

// Route::post('/submit-form', function (Request $request) {
//     $validatedData = $request->validate([
//         'name' => 'required|string|max:255',
//     ]);
//     return response()->json(['message' => 'Form submitted successfully!', 'data' => $validatedData]);

//     return redirect('/')->with('success', 'Form submitted successfully!');
// })->name('submit.form');
