<?php

use App\Http\Controllers\{
    ActivityController,
    ActivitySegmentController,
    DashboardController,
    SegmentController,
    TeamToggleController,
    UserProfileController,
    UserMetricsController,
    UserIntegrationsController,
    UserFollowersController,
};
use App\Http\Controllers\Activity\{
    AnalysisController,
    GeoJsonController as ActivityGeoJsonController,
    LapGeoJsonController,
    LoFiMapController,
    PerformanceController,
    ProcessController,
};
use App\Http\Controllers\Segment\GeoJsonController as SegmentGeoJsonController;
use App\Http\Livewire\Activities\Show as ActivityShow;
use Illuminate\Support\Facades\Route;
use Dcblogdev\Dropbox\Facades\Dropbox;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/', function () {
        return redirect('dashboard');
    });

    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::put('team-toggle', TeamToggleController::class)->name('team.toggle');

    Route::get('u/{user:slug}', [UserProfileController::class, 'show'])->name('user.profile');
    Route::get('user/metrics', [UserMetricsController::class, 'show'])->name('user.metrics');
    Route::get('user/integrations', [UserIntegrationsController::class, 'show'])->name('user.integrations');
    Route::get('user/followers', [UserFollowersController::class, 'show'])->name('user.followers');

    Route::get('dropbox/oauth', function(){
        return Dropbox::connect();
    })->name('user.integrations.dropbox');

    Route::resource('activities', ActivityController::class)->except(['show', 'create', 'store', 'edit']);
    Route::get('activities/{activity}', ActivityShow::class)->name('activities.show');
    Route::get('segments', [SegmentController::class, 'index'])->name('segments.index');
    Route::get('segments/{segment}/geojson', SegmentGeoJsonController::class)->name('segments.geojson');
    Route::resource('activities.segments', SegmentController::class)->shallow()->except(['edit', 'update', 'destroy']);
    Route::get('activities/{activity}/segment/{segment}/{start}', ActivitySegmentController::class)->name('activities.segment');
    Route::get('activities/{activity}/lofimap', LoFiMapController::class)->name('activities.lofimap');
    Route::get('activities/{activity}/geojson/{start?}/{end?}', ActivityGeoJsonController::class)->name('activities.geojson');
    Route::get('activities/{activity}/lap-geojson/{lap}', LapGeoJsonController::class)->name('activities.lap-geojson');
    Route::get('activities/{activity}/performance/{start?}/{end?}', PerformanceController::class)->name('activities.performance');
    Route::get('activities/{activity}/analysis', AnalysisController::class)->name('activities.analysis');
    Route::post('activities/{activity}/process', ProcessController::class)->name('activities.process');
});
