<?php

use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MovilController;
use App\Http\Controllers\WordController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/home');
    }
    return redirect('/login');
});

Route::get('/movil',[MovilController::class,'camara'])->name('camara');

Auth::routes();

// Rutas que requieren autenticación
Route::middleware(['auth'])->group(function () {

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    //Chats
    Route::get('/chats/show/{id}',[ChatController::class, 'show'])->name('chats.show');
    Route::post('/chats', [ChatController::class,'store'])->name('chats.store');
    Route::post('/chats/add', [ChatController::class,'add'])->name('chats.add');
    Route::post('chats/{chat}/send',[ChatController::class,'sendMessage'])->name('chats.send');

    //Message chatgpt
    Route::post('/review-message',[MessageController::class, 'review'])->name('review.message');
    
    //Categories
    Route::get('/categories',[CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/manage',[CategoryController::class, 'manage'])->name('categories.manage');
    Route::get('/categories/create',[CategoryController::class, 'create'])->name('categories.create');
    Route::post('/categories',[CategoryController::class, 'store'])->name('categories.store');
    Route::get('/categories/{category}/edit',[CategoryController::class, 'edit'])->name('categories.edit');
    Route::put('/categories/{category}',[CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}',[CategoryController::class, 'destroy'])->name('categories.destroy');

    //Words
    Route::prefix('categories/{category}')->group(function () {
        Route::get('/words',[WordController::class, 'index'])->name('words.index');
        Route::get('/words/manage',[WordController::class, 'manage'])->name('words.manage');
        Route::get('/words/create',[WordController::class, 'create'])->name('words.create');
        Route::get('/words/{word}',[WordController::class, 'show'])->name('words.show');
        Route::post('/words',[WordController::class, 'store'])->name('words.store');
        Route::get('/words/{word}/edit',[WordController::class, 'edit'])->name('words.edit');
        Route::put('/words/{word}',[WordController::class, 'update'])->name('words.update');
        Route::delete('/words/{word}',[WordController::class, 'destroy'])->name('words.destroy');
    });

});