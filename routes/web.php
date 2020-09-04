<?php

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


Route::get('/', function () {
    return view('welcome');
});


/* Using HTTP Parameters
mandatory:
Route::get('ID/{id}',function($id) {
   echo 'ID: '.$id;
});

optional:
Route::get('ID/{id?}',function($id) {
   echo 'ID: '.$id;
   return view('user/:id')
});
*/

/* Using Controllers
               user/dan     Controller  @   Ctrlr Fn  ->  fn: name(arg)
Route::get('user/profile', 'UserController@showProfile')->name('profile');
*/