<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;
use App\Http\Controllers\AdoptablePetsController;
use App\Http\Controllers\AdoptionApplicationController;
use App\Http\Controllers\ApplicationList;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/homepage', [UserController::class, 'homepage'])->name('homepage');
Route::get('/Adoptable/pets', [UserController::class, 'adoptlist'])->name('adoptlist');

Route::get('/adopt-list', [AdoptablePetsController::class, 'adoptList'])->name('pets.adoptList');
Route::post('/adoption/store', [AdoptablePetsController::class, 'store'])->name('adoption.store');
  

// Authenticated user routes
Route::middleware(['auth'])->group(function () {
    Route::get('/applications', [AdoptionApplicationController::class, 'index'])->name('user.applications');
    Route::get('/applications/data', [AdoptionApplicationController::class, 'getData'])->name('admin.applications.data');
    Route::post('/applications/{id}/approve', [AdoptionApplicationController::class, 'approve'])->name('admin.applications.approve');
    Route::post('/applications/{id}/reject', [AdoptionApplicationController::class, 'reject'])->name('admin.applications.reject');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
  
    Route::get('/dashboard', [PetController::class, 'index'])->name('dashboard');

    Route::get('/pets/create', [PetController::class, 'create'])->name('pets.create');
    Route::post('/pets', [PetController::class, 'store'])->name('pets.store');
    Route::get('/pets/my-pets', [PetController::class, 'myPets'])->name('pets.myPets');
    Route::get('/pets/{pet}', [PetController::class, 'show'])->name('pets.show');
    Route::get('/pets/{pet}/edit', [PetController::class, 'edit'])->name('pets.edit');
    Route::put('/pets/{pet}', [PetController::class, 'update'])->name('pets.update');
    Route::delete('/pets/{pet}', [PetController::class, 'destroy'])->name('pets.destroy');
    Route::post('/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('auth');

     
});
/*
// Pet listing and creation
Route::get('/pets', [PetController::class, 'index'])->name('pets.index');
Route::get('/pets/create', [PetController::class, 'create'])->name('pets.create');
Route::post('/pets', [PetController::class, 'store'])->name('pets.store');

// My pets listing
Route::get('/pets/my-pets', [PetController::class, 'myPets'])->name('pets.myPets');

// Pet details, edit, update, delete
Route::get('/pets/{pet}', [PetController::class, 'show'])->name('pets.show');
Route::get('/pets/{pet}/edit', [PetController::class, 'edit'])->name('pets.edit');
Route::put('/pets/{pet}', [PetController::class, 'update'])->name('pets.update');
Route::delete('/pets/{pet}', [PetController::class, 'destroy'])->name('pets.destroy');
*/

 // Payment routes
    //Route::get('/applications/{application}/payment', [PaymentController::class, 'create'])->name('payments.create');
    //Route::post('/applications/{application}/payment', [PaymentController::class, 'store'])->name('payments.store');

  //Route::post('/pets/{pet}/apply', [AdoptionApplicationController::class, 'store'])->name('applications.store');
    //Route::get('/my-applications', [AdoptionApplicationController::class, 'index'])->name('applications.index');
    //Route::get('/my-applications/{application}', [AdoptionApplicationController::class, 'show'])->name('applications.show');
require __DIR__.'/auth.php';