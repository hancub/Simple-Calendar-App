<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CalendarController;

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

/*Route::get('/', function () {
	return view('welcome');
});*/

Route::get('/',[CalendarController::class, 'index']);
Route::get('/eventList',[CalendarController::class, 'getEventList']);
Route::post('/eventListAuth',[CalendarController::class, 'getEventListAuth']);
Route::post('/createEvent',[CalendarController::class, 'createEvent']);
Route::post('/deleteEvent',[CalendarController::class, 'deleteEvent']);
Route::post('/updateEvent',[CalendarController::class, 'updateEvent']);
