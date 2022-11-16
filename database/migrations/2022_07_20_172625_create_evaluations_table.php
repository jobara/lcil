<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regime_assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('measure_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provision_id')->constrained()->cascadeOnDelete();
            $table->string('assessment'); // EvaluationAssessments
            $table->text('comment')->fulltext()->nullable();
            $table->unique(['regime_assessment_id', 'measure_id', 'provision_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('evaluations');
    }
};
