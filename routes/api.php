<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\WordPressInternshipController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->post('/login', [AuthController::class, 'apiLogin']);
Route::get('/wordpress/internships', [WordPressInternshipController::class, 'index']);
