<?php

use App\Http\Controllers\JoinController;
use App\Http\Controllers\JurisdictionController;
use App\Http\Controllers\LawPolicySourceController;
use App\Http\Controllers\MeasureController;
use App\Http\Controllers\MeasureEvaluationController;
use App\Http\Controllers\ProvisionController;
use App\Http\Controllers\RegimeAssessmentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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

// Landing Page
Route::multilingual('/', function () {
    return view('welcome');
})->name('welcome');

// About Page
Route::multilingual('about', function () {
    return view('about');
})->name('about'); // rough-in web route

// Jurisdictions
Route::get('jurisdictions', [JurisdictionController::class, 'index'])->name('jurisdictions.index');
Route::get('jurisdictions/{country}', [JurisdictionController::class, 'show'])->name('jurisdictions.show');

// Law and Policy Sources
Route::multilingual('law-policy-sources/{lawPolicySource}/edit', [LawPolicySourceController::class, 'edit'])
    ->name('lawPolicySources.edit')
    ->middleware('auth');

Route::multilingual('law-policy-sources', [LawPolicySourceController::class, 'index'])
    ->name('lawPolicySources.index');

Route::multilingual('law-policy-sources/create', [LawPolicySourceController::class, 'create'])
    ->name('lawPolicySources.create')
    ->middleware('auth');

Route::multilingual('law-policy-sources/{lawPolicySource}', [LawPolicySourceController::class, 'show'])
    ->name('lawPolicySources.show');

Route::post('law-policy-sources', [LawPolicySourceController::class, 'store'])
    ->name('lawPolicySources.store')
    ->middleware('auth');

Route::patch('law-policy-sources/{lawPolicySource}', [LawPolicySourceController::class, 'update'])
    ->name('lawPolicySources.update')
    ->middleware('auth');

// Law and Policy Sources - Provisions

Route::multilingual('law-policy-sources/{lawPolicySource}/create', [ProvisionController::class, 'create'])
    ->name('provisions.create')
    ->middleware('auth');

Route::multilingual('law-policy-sources/{lawPolicySource}/provisions/{slug}', [ProvisionController::class, 'edit'])
    ->name('provisions.edit')
    ->middleware('auth');

Route::post('law-policy-sources/{lawPolicySource}', [ProvisionController::class, 'store'])
    ->name('provisions.store')
    ->middleware('auth');

Route::patch('law-policy-sources/{lawPolicySource}/provisions/{slug}', [ProvisionController::class, 'update'])
    ->name('provisions.update')
    ->middleware('auth');

// Measures Page
Route::multilingual('measures', [MeasureController::class, 'index'])->name('measures');

// Regime Assessments
Route::multilingual('regime-assessments/create', function () {
})->name('regimeAssessments.create'); // rough-in web route

Route::multilingual('regime-assessments', [RegimeAssessmentController::class, 'index'])
    ->name('regimeAssessments.index'); // rough-in web route

Route::multilingual('regime-assessments/{regimeAssessment}', [RegimeAssessmentController::class, 'show'])
    ->name('regimeAssessments.show');

// Regime Assessments - Measure Evaluations
// Route::multilingual('regime-assessments/{regimeAssessment}/evaluations/{code}', [MeasureEvaluationController::class, 'show'])
//     ->name('regimeAssessments.show');

// Hearth generated routes
Route::multilingual('dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified:'.\locale().'.verification.notice'])->name('dashboard');

Route::multilingual('account/edit', [UserController::class, 'edit'])
    ->middleware(['auth'])
    ->name('users.edit');

Route::multilingual('account/admin', [UserController::class, 'admin'])
    ->middleware(['auth'])
    ->name('users.admin');

Route::multilingual('account/delete', [UserController::class, 'destroy'])
    ->method('delete')
    ->middleware(['auth'])
    ->name('users.destroy');

Route::multilingual('requests/cancel', [JoinController::class, 'cancel'])
    ->method('post')
    ->middleware(['auth'])
    ->name('requests.cancel');

Route::multilingual('requests/{user:id}/deny', [JoinController::class, 'deny'])
    ->method('post')
    ->middleware(['auth'])
    ->name('requests.deny');

Route::multilingual('requests/{user:id}/approve', [JoinController::class, 'approve'])
    ->method('post')
    ->middleware(['auth'])
    ->name('requests.approve');

if (config('hearth.organizations.enabled')) {
    require __DIR__.'/organizations.php';
}

if (config('hearth.resources.enabled')) {
    require __DIR__.'/resources.php';
    require __DIR__.'/resource-collections.php';
}

require __DIR__.'/fortify.php';
