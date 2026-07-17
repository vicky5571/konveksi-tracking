<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\QcController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('orders', OrderController::class)
    ->only(['index', 'create', 'store', 'show']);

Route::get('/jahit', [WorkflowController::class, 'jahit'])->name('jahit.index');
Route::patch('/jahit/{order}/assign', [WorkflowController::class, 'assignTailor'])->name('jahit.assign');
Route::patch('/jahit/{order}/finish', [WorkflowController::class, 'finishSewing'])->name('jahit.finish');

Route::get('/qc-awal', [WorkflowController::class, 'qcAwal'])->name('qc-awal.index');
Route::post('/qc-awal/{order}/split', [QcController::class, 'split'])->name('qc-awal.split');

Route::get('/permak', [WorkflowController::class, 'permak'])->name('permak.index');
Route::patch('/permak/{order}/qc-ulang', [QcController::class, 'moveToQcUlang'])->name('permak.qc-ulang');

Route::get('/cuci', [WorkflowController::class, 'cuci'])->name('cuci.index');
Route::patch('/cuci/{order}/qc-ulang', [QcController::class, 'moveToQcUlang'])->name('cuci.qc-ulang');

Route::get('/qc-ulang', [WorkflowController::class, 'qcUlang'])->name('qc-ulang.index');
Route::patch('/qc-ulang/{order}/pass', [QcController::class, 'passQcUlang'])->name('qc-ulang.pass');

Route::get('/packing', [WorkflowController::class, 'packing'])->name('packing.index');
Route::patch('/packing/{order}/ship', [WorkflowController::class, 'ship'])->name('packing.ship');
Route::get('/tracking', [WorkflowController::class, 'tracking'])->name('tracking.index');
Route::get('/reports', [WorkflowController::class, 'reports'])->name('reports.index');

