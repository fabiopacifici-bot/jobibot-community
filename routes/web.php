<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('dashboard'))->name('dashboard');
Route::get('/cv', fn () => view('cv'))->name('cv');
Route::get('/jobs', fn () => view('jobs'))->name('jobs');
Route::get('/interview', fn () => view('interview'))->name('interview');
Route::get('/settings', fn () => view('settings'))->name('settings');
Route::get('/login', fn () => view('dashboard'))->name('login');
Route::get('/register', fn () => view('dashboard'))->name('register');
