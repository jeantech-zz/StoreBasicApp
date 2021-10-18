<?php

use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Orders;

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

Route::get('/', function () {
    return view('welcome');
});

//Route::get('/products/index',[ProductController::class,'index'])->name('products.index');

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');



Route::middleware('auth')->group( callback: function () {
    Route::view('products', 'livewire.products.index')->middleware('auth');
	Route::view('orders', 'livewire.orders.index')->middleware('auth');
    //Route::post(uri:'payment'. action:'livewire.orders.pay')->name(name:'payment');
    Route::post('transactions/showstatus',[TransactionController::class,'showstatus'])->name('transactions.showstatus');
});
	//Route::view('products', 'livewire.products.index')->middleware('auth');