<?php

use App\Http\Controllers\EvaluationAPIController;
use App\Http\Controllers\LawPolicySourceAPIController;
use App\Http\Controllers\RegimeAssessmentAPIController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')
    ->name('api.')
    ->group(function () {
        // Evaluations
        Route::get('evaluations', [EvaluationAPIController::class, 'index'])
            ->name('evaluations.index');

        Route::get('evaluations/{evaluation}', [EvaluationAPIController::class, 'show'])
            ->name('evaluations.show');

        // Law and Policy Sources
        Route::get('law-policy-sources', [LawPolicySourceAPIController::class, 'index'])
            ->name('lawPolicySources.index');

        Route::get('law-policy-sources/{lawPolicySource}', [LawPolicySourceAPIController::class, 'show'])
            ->name('lawPolicySources.show');

        // Regime Assessments
        Route::get('regime-assessments', [RegimeAssessmentAPIController::class, 'index'])
            ->name('regimeAssessments.index');

        Route::get('regime-assessments/{regimeAssessment}', [RegimeAssessmentAPIController::class, 'show'])
            ->name('regimeAssessments.show');

        Route::get('regime-assessments/{regimeAssessment}/evaluations', [RegimeAssessmentAPIController::class, 'evaluations'])
            ->name('regimeAssessments.evaluations');
    });
