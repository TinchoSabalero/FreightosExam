<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/classrooms', 'App\Http\Controllers\ClassroomController@getClassrooms');

Route::post('/newBooking', 'App\Http\Controllers\ClassroomController@addBooking');

Route::delete('/removeBooking/{id}', 'App\Http\Controllers\ClassroomController@deleteBooking');

Route::get('/csrf-token', function () {
    return response()->json(['csrf_token' => csrf_token()]);
});
