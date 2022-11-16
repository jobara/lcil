<?php

use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\JoinController;
use App\Http\Controllers\JurisdictionController;
use App\Http\Controllers\LawPolicySourceController;
use App\Http\Controllers\MeasureController;
use App\Http\Controllers\ProvisionController;
use App\Http\Controllers\RegimeAssessmentController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
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

// Redirect to locale prefixed homepage for default language if, default language prefix is included in the URL
if (config('laravel-multilingual-routes.prefix_default') && config('laravel-multilingual-routes.prefix_default_home')) {
    Route::redirect('/', \locale());
}

// Landing Page
Route::multilingual('/', [WelcomeController::class, 'show'])->name('welcome');

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
    ->middleware(['auth', 'verified']);

Route::multilingual('law-policy-sources', [LawPolicySourceController::class, 'index'])
    ->name('lawPolicySources.index');

Route::multilingual('law-policy-sources/create', [LawPolicySourceController::class, 'create'])
    ->name('lawPolicySources.create')
    ->middleware(['auth', 'verified']);

Route::multilingual('law-policy-sources/{lawPolicySource}', [LawPolicySourceController::class, 'show'])
    ->name('lawPolicySources.show');

Route::post('law-policy-sources', [LawPolicySourceController::class, 'store'])
    ->name('lawPolicySources.store')
    ->middleware(['auth', 'verified']);

Route::patch('law-policy-sources/{lawPolicySource}', [LawPolicySourceController::class, 'update'])
    ->name('lawPolicySources.update')
    ->middleware(['auth', 'verified']);

// Law and Policy Sources - Provisions
Route::multilingual('law-policy-sources/{lawPolicySource}/create', [ProvisionController::class, 'create'])
    ->name('provisions.create')
    ->middleware(['auth', 'verified']);

Route::multilingual('law-policy-sources/{lawPolicySource}/provisions/{slug}/edit', [ProvisionController::class, 'edit'])
    ->name('provisions.edit')
    ->middleware(['auth', 'verified']);

Route::post('law-policy-sources/{lawPolicySource}', [ProvisionController::class, 'store'])
    ->name('provisions.store')
    ->middleware(['auth', 'verified']);

Route::patch('law-policy-sources/{lawPolicySource}/provisions/{slug}', [ProvisionController::class, 'update'])
    ->name('provisions.update')
    ->middleware(['auth', 'verified']);

// Measures Page
Route::multilingual('measures', [MeasureController::class, 'index'])->name('measures');

// Regime Assessments
Route::multilingual('regime-assessments/create', [RegimeAssessmentController::class, 'create'])
    ->name('regimeAssessments.create')
    ->middleware(['auth', 'verified']);

Route::multilingual('regime-assessments/{regimeAssessment}/edit', [RegimeAssessmentController::class, 'edit'])
    ->name('regimeAssessments.edit')
    ->middleware(['auth', 'verified']);

Route::multilingual('regime-assessments', [RegimeAssessmentController::class, 'index'])
    ->name('regimeAssessments.index');

Route::multilingual('regime-assessments/{regimeAssessment}', [RegimeAssessmentController::class, 'show'])
    ->name('regimeAssessments.show');

Route::post('regime-assessments', [RegimeAssessmentController::class, 'store'])
    ->name('regimeAssessments.store')
    ->middleware(['auth', 'verified']);

Route::patch('regime-assessments/{regimeAssessment}', [RegimeAssessmentController::class, 'update'])
    ->name('regimeAssessments.update')
    ->middleware(['auth', 'verified']);

Route::patch('regime-assessments/{regimeAssessment}/updateStatus', [RegimeAssessmentController::class, 'updateStatus'])
    ->name('regimeAssessments.updateStatus')
    ->middleware(['auth', 'verified']);

// Regime Assessments - Measure Evaluations
Route::multilingual('regime-assessments/{regimeAssessment}/evaluations/{measure}', [EvaluationController::class, 'show'])
    ->name('evaluations.show');

Route::post('regime-assessments/{regimeAssessment}/evaluations/{measure}', [EvaluationController::class, 'update'])
    ->name('evaluations.update')
    ->middleware(['auth', 'verified']);

// API
Route::post('tokens/{id}', [TokenController::class, 'destroy'])
    ->name('tokens.destroy')
    ->middleware(['auth', 'verified']);

Route::multilingual('tokens', [TokenController::class, 'show'])
    ->name('tokens.show')
    ->middleware(['auth', 'verified']);

Route::post('tokens', [TokenController::class, 'store'])
    ->name('tokens.store')
    ->middleware(['auth', 'verified']);

Route::multilingual('api', [TokenController::class, 'showDocs'])
    ->name('api.show')
    ->middleware(['auth', 'verified']);

// e-mail verification notice
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

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
