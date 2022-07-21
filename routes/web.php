<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UpdateController;
use App\Http\Controllers\UserController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
 */

Route::get('/', function () {
    return 'guest dashboard goes here';
})->name('home');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');
Route::post('/confirm-password', [AuthController::class, 'password'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('password.confirm');

Route::middleware(['auth:sanctum'])->group(function () {
    /*
    |--------------------------------------------------------------------------
    | SPA Routes
    |--------------------------------------------------------------------------
     */
    
    Route::get('/dashboard', function () {
        return 'React here';
    })->name('dashboard');
    
    Route::get('/confirm-password', function () {
        return 'React here';
    })->name('password.confirm');
    
    /*
    |--------------------------------------------------------------------------
    | Email Verification Routes
    |--------------------------------------------------------------------------
     */
    
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('dashboard');
    })->middleware(['signed'])->name('verification.verify');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('dashboard');
    })->middleware(['signed'])->name('verification.verify');
    
    /*
    |--------------------------------------------------------------------------
    | User Rest Routes
    |--------------------------------------------------------------------------
     */
    
    Route::get('/user', [UserController::class, 'self'])->name('user.self');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('user.show');
    Route::patch('/user', [UserController::class, 'update'])
        ->middleware(['password.confirm'])
        ->name('user.update');
    Route::delete('/user', [UserController::class, 'destroy'])
        ->middleware(['password.confirm'])
        ->name('user.destroy');
});

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    
    /*
    |--------------------------------------------------------------------------
    | Invites Rest Routes
    |--------------------------------------------------------------------------
     */
    
    Route::get('/invites', [InviteController::class, 'index'])->name('invite.index');
    Route::post('/invites', [InviteController::class, 'store'])->name('invite.store');
    Route::get('/invites/{invite}', [InviteController::class, 'show'])->name('invite.show');
    Route::delete('/invites/{invite}', [InviteController::class, 'destroy'])->name('invite.destroy');

    /*
    |--------------------------------------------------------------------------
    | Projects Rest Routes
    |--------------------------------------------------------------------------
     */
    
    Route::get('/projects', [ProjectController::class, 'index'])->name('project.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('project.store');
    Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('project.show');
    Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('project.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])
        ->middleware(['password.confirmed'])
        ->name('project.destroy');
    Route::get('/projects/{project}/users', [ProjectController::class, 'users'])->name('project.users');

    /*
    |--------------------------------------------------------------------------
    | Updates Rest Routes
    |--------------------------------------------------------------------------
     */

    Route::get('/tickets/{ticket}/updates', [UpdateController::class, 'index'])->name('update.index');
    Route::get('/updates/{update}', [UpdateController::class, 'show'])->name('update.show');
});