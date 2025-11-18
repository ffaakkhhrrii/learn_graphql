<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GraphQLController;

Route::match(['get', 'post'], '/graphql', GraphQLController::class);
