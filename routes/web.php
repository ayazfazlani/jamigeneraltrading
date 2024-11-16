<?php


use App\Livewire\Adjust;
use App\Livewire\Summary;
use App\Livewire\Analytic;
use App\Livewire\ItemList;

use App\Livewire\Dashboard;
use App\Livewire\InviteUser;
use App\Livewire\Transactions;
use App\Livewire\StockInComponent;
use App\Livewire\StockOutComponent;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\RegisterController;
use App\Livewire\Auth\Login;

// Route::get('/', function () {
//   return view('livewire.sidebar');
// });

// Route::get('/stock', function () {
//   return view('stock');
// });


Route::get('/', ItemList::class)->name('items');
Route::get('/itemlist', ItemList::class)->name('items');
Route::get('/stockin', StockInComponent::class)->name('stock-in');
Route::get('/stockout', StockOutComponent::class)->name('stock-out');
Route::get('/transactions', Transactions::class)->name('transactions');
Route::get('/analytics', Analytic::class)->name('analytics');
Route::get('/adjust', Adjust::class)->name('adjust');
Route::get('/dashboard', Dashboard::class)->name('dashboard');
Route::get('/summary', Summary::class)->name('summary');
Route::get('/login', Login::class)->name('login');


Route::get('/invite', InviteUser::class)->name('invite');

// routes/web.php

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);





// Route::get('/in', [InviteController::class, 'sendInvitation']);
// Route::get('/register', [InviteController::class, 'registerForm']);
// Route::post('/register', [InviteController::class, 'register']);