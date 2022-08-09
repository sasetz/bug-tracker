<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InviteController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TicketController;
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

Route::middleware(['auth:sanctum', 'verified'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Invites Rest Routes
    |--------------------------------------------------------------------------
     */

    Route::get('/invites', [InviteController::class, 'index'])->name('invite.index');
    Route::post('/projects/{project}/invites', [InviteController::class, 'store'])->name('invite.store');
    Route::get('/invites/{invite}', [InviteController::class, 'show'])->name('invite.show');
    Route::delete('/invites/{invite}', [InviteController::class, 'destroy'])->name('invite.destroy');
    Route::patch('/invites/{invite}/accept', [InviteController::class, 'accept'])->name('invite.accept');
    Route::patch('/invites/{invite}/reject', [InviteController::class, 'reject'])->name('invite.reject');

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
    Route::post('/projects/{project}/users/{user}/admin', [ProjectController::class, 'makeAdmin'])
        ->name('project.make_admin');

    /*
    |--------------------------------------------------------------------------
    | Tickets Rest Routes
    |--------------------------------------------------------------------------
     */

    Route::get('/projects/{project}/tickets', [TicketController::class, 'index'])->name('ticket.index');
    Route::post('/projects/{project}/tickets', [TicketController::class, 'store'])->name('ticket.store');
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('ticket.show');
    Route::get('/tickets/{ticket}/updates', [TicketController::class, 'showUpdates'])
        ->name('ticket.show_updates');
    Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])->name('ticket.update');
    Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('ticket.destroy');
    Route::patch('/tickets/{ticket}/subscribe', [TicketController::class, 'subscribe'])
        ->name('ticket.subscribe');
    Route::patch('/tickets/{ticket}/unsubscribe', [TicketController::class, 'unsubscribe'])
        ->name('ticket.unsubscribe');
});