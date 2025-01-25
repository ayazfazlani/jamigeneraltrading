<?php


use App\Livewire\Adjust;
use App\Livewire\Summary;
use App\Livewire\Analytic;
use App\Livewire\ItemList;

use App\Livewire\Dashboard;
use App\Livewire\Auth\Login;
use App\Livewire\InviteUser;
use App\Livewire\ManageRoles;
use App\Livewire\Transactions;
use App\Livewire\TeamManagement;
use App\Livewire\UserManagement;
use App\Livewire\StockInComponent;
use App\Livewire\StockOutComponent;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\RegisterController;

// Route::get('/', function () {
//   return view('livewire.sidebar');
// });

// Route::get('/stock', function () {
//   return view('stock');
// });

Route::middleware(['auth'])->group(function () {
  Route::get('/', ItemList::class)->name('home');
  Route::get('/itemlist', ItemList::class)->name('items');
  Route::get('/stockin', StockInComponent::class)->name('stock-in');
  Route::get('/stockout', StockOutComponent::class)->name('stock-out');
  Route::get('/transactions', Transactions::class)->name('transactions');
  Route::get('/analytics', Analytic::class)->name('analytics');
  Route::get('/adjust', Adjust::class)->name('adjust');
  Route::get('/dashboard', Dashboard::class)->name('dashboard');
  Route::get('/summary', Summary::class)->name('summary');
  Route::get('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');
});


Route::middleware(['auth'])->group(function () {

  // Route::get('/user', UserManagement::class)->name('user');
  Route::get('/admin', TeamManagement::class)
    ->name('admin');
});

Route::get('/invite', InviteUser::class)->name('invite');
Route::get('/login', Login::class)->name('login');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);




// ->middleware('can:admin');



// Route::get('/in', [InviteController::class, 'sendInvitation']);
// Route::get('/register', [InviteController::class, 'registerForm']);
// Route::post('/register', [InviteController::class, 'register']);




Route::get('/roles/{userId}', ManageRoles::class)->name('manage-roles');







Route::get('/forgot-password', App\Livewire\Auth\ForgotPassword::class)
  ->name('password.request')
  ->middleware('guest');

Route::get('/reset-password/{token}', App\Livewire\Auth\ResetPassword::class)
  ->name('password.reset')
  ->middleware('guest');


// Route::middleware(['auth'])->middleware('can:super admin')->group(function () {

// Route::get('/admin', UserManagement::class)->name('admin');
//   Route::get('/team', TeamManagement::class)
//     ->name('team-management');
// });



Route::get('/user', UserManagement::class)->name('user');
