<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['api' => 'error'];
});

require __DIR__.'/auth.php';
