<?php

use App\Http\Controllers\CinemaController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\ScheduleController;
use App\Models\Cinema;
use App\Models\Promo;
use App\Models\Schedule;
use Illuminate\Support\Facades\Route;


Route::get('/', [MovieController::class, 'home'])->name('home');
Route::get('/schedules/movies_id)', [MovieController::class, 'movieSchedules'])->name('movies.schedules');
Route::get('/movies/active', [MovieController::class, 'homeMovies'])->name('home.movies.active');
Route::get('/detail/{id}', [MovieController::class, 'movieSchedules'])->name('detail');


Route::get('/schedules/{id}', [ScheduleController::class, 'show'])->name('schedules.detail');
Route::view('/login', 'auth.login')->name('login');
Route::view('/signup', 'auth.signup')->name('signup');

Route::post('/signup', [UserController::class, 'register'])->name('signup.register');
Route::post('/login', [UserController::class, 'loginAuth'])->name('login.auth');
Route::get('/logout', [UserController::class, 'logout'])->name('logout');


Route::middleware('isAdmin')->prefix('admin')->name('admin.')->group(function () {

    // Dashboard
    Route::view('/dashboard', 'admin.dashboard')->name('dashboard');

    // Data Bioskop
    Route::prefix('cinemas')->name('cinemas.')->group(function () {
        Route::get('/datatables', [CinemaController::class, 'datatables'])->name('datatables');
        Route::get('/', [CinemaController::class, 'index'])->name('index');
        Route::get('/create', [CinemaController::class, 'create'])->name('create');
        Route::post('/store', [CinemaController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [CinemaController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [CinemaController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [CinemaController::class, 'destroy'])->name('delete');
        Route::get('/trash', [CinemaController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [CinemaController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [CinemaController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/admin/cinemas/export', [CinemaController::class, 'export'])->name('export');
    });
    // Data Film
    Route::prefix('movies')->name('movies.')->group(function () {
        Route::get('/datatables', [MovieController::class, 'datatables'])->name('datatables');
        Route::get('/', [MovieController::class, 'index'])->name('index');
        Route::get('/create', [MovieController::class, 'create'])->name('create');
        Route::post('/store', [MovieController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [MovieController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [MovieController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [MovieController::class, 'destroy'])->name('delete');
        Route::patch('/active/{id}', [MovieController::class, 'actived'])->name('actived');
        Route::get('/trash', [MovieController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [MovieController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [MovieController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('ecport', [MovieController::class, 'exportExcel'])->name('export');
    });

    // Data Petugas
    Route::prefix('users')->name('users.')->group(function () {
        
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/datatables', [UserController::class, 'datatables'])->name('datatables');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/store', [UserController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [UserController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [UserController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [UserController::class, 'destroy'])->name('delete');
        Route::get('/trash', [UserController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [UserController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [UserController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/export', [UserController::class, 'exportExcel'])->name('export');
    });
});





// =================== STAFF ROUTES ===================
Route::middleware('isStaff')->prefix('staff')->name('staff.')->group(function () {

    // Dashboard Staff
    Route::view('/dashboard', 'staff.dashboard')->name('dashboard');

    // Data Promo (resource style)
    Route::prefix('/promos')->name('promos.')->group(function () {
        Route::get('/', [PromoController::class, 'index'])->name('index');
        Route::get('/datatables', [PromoController::class, 'datatables'])->name('datatables');      // staff.promos.index
        Route::get('/create', [PromoController::class, 'create'])->name('create'); // staff.promos.create
        Route::post('/', [PromoController::class, 'store'])->name('store');       // staff.promos.store
        Route::get('/{id}/edit', [PromoController::class, 'edit'])->name('edit'); // staff.promos.edit
        Route::put('/{id}', [PromoController::class, 'update'])->name('update');  // staff.promos.update
        Route::delete('/{id}', [PromoController::class, 'destroy'])->name('destroy'); // staff.promos.destroy
        Route::get('/trash', [PromoController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [PromoController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [PromoController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/export', [PromoController::class, 'export'])->name('export');
    });

    Route::prefix('/schedules')->name('schedules.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::get('/datatables', [ScheduleController::class, 'datatables'])->name('datatables');
        Route::post('store', [ScheduleController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ScheduleController::class, 'edit'])->name('edit');
        Route::patch('/update/{id}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ScheduleController::class, 'destroy'])->name('delete');
        Route::get('/trash', [ScheduleController::class, 'trash'])->name('trash');
        Route::patch('/restore/{id}', [ScheduleController::class, 'restore'])->name('restore');
        Route::delete('/delete-permanent/{id}', [ScheduleController::class, 'deletePermanent'])->name('delete_permanent');
        Route::get('/export', [ScheduleController::class, 'export'])->name('export');
    });
});
