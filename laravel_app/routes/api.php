<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->get('/hello', function (Request $request) {
    return response()->json(['message' => 'Hello from Laravel API']);
});
