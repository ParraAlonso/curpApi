<?php

use App\Http\Controllers\CurpController;
use Illuminate\Support\Facades\Route;

Route::get('consultar-curp', [CurpController::class, 'consultarCurp'])->name('api.consultarCurp');
Route::get('descargar-curp', [CurpController::class, 'downloadCurp'])->name('api.downloadCurp');
