<?php

use App\Http\Controllers\AlbumController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/albums');

// Один resource создаёт семь маршрутов для CRUD.
Route::resource('albums', AlbumController::class);
