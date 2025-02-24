<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PdfExportController;
use App\Http\Controllers\ExcelExportController;
use App\Http\Controllers\DdexValidatorController;

Route::post('/validate-metadata', [DdexValidatorController::class, 'validateData']);
Route::post('/validate-music', [DdexValidatorController::class, 'validateMusic']);
Route::post('/validate-xml', [DdexValidatorController::class, 'uploadXml']);
Route::get('/export-reports-excel', [ExcelExportController::class, 'export']);
Route::post('/reports', [ReportController::class, 'store']);
Route::get('/reports', [ReportController::class, 'index']);
Route::post('/report/update-status-report', [ReportController::class, 'update']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('/update-report', [ReportController::class, 'updateReport']);
Route::post('/delete-report', [ReportController::class, 'destroy']);
Route::get('/export-reports', [PdfExportController::class, 'export']);
Route::get('/tes', function () {
    return response()->json([
        'success' => true,
        'message' => 'Test API export reports',
        'data' => []
    ]);
});