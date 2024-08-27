<?php

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

   Route::middleware('auth:api')->group(function () {
  
    Route::post('/logout',[App\Http\Controllers\AuthController::class,'logout']);

     Route::prefix('post/')->controller(App\Http\Controllers\PostController::class)->group(function(){
        Route::post('/create', 'store');
  
        Route::post('/edit', 'update');
        
        Route::post('/delete', 'destroy');
        
        });
   

    Route::prefix('comment/')->controller(App\Http\Controllers\CommentController::class)->group(function(){
      
            Route::post('/add', 'create');      
            Route::post('/remove', 'delete');
            
            });

      });


            //..........................Auth..........

       Route::post('/register',[App\Http\Controllers\AuthController::class,'register']);
        Route::post('/login',[App\Http\Controllers\AuthController::class,'login']);

             //..........post......................


        Route::get('/if_authrise',[App\Http\Controllers\AuthController::class,'authrise'])->name('login');
        Route::get('/read/posts',[App\Http\Controllers\PostController::class,'index']);

