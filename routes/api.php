<?php

use App\Http\Controllers\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
//Route::post('/register',[HomeController::class,'register']);

Route::post('/login', [HomeController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::post('/Employeecreate', [HomeController::class, 'createemployee']);
Route::get('/Employeelist', [HomeController::class, 'list']);
Route::get('/Employeelist/{id}', [HomeController::class, 'byid']);
Route::post('/Employeeupdate/{id}', [HomeController::class, 'update']);
Route::delete('/Employeedelete/{id}', [HomeController::class, 'delete']);
Route::get('/Employeesearch/{name}', [HomeController::class, 'search']);


//curd operation
Route::post('/addcheckin',[HomeController::class,'addcheckin']);
Route::post('/addcheckout',[HomeController::class,'addcheckout']);
//one to many relationship
Route::get('/employee/{id}',[HomeController::class,'employee']);
//fetch all date
Route::post('/date',[HomeController::class,'date']);
});

