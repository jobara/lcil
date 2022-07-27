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
        Schema::create('regime_assessments', function (Blueprint $table) {
            $table->id();
            $table->string('jurisdiction'); // ISO 3166-1 alpha-2 code or ISO 3166-2 code
            $table->string('municipality')->nullable();
            $table->text('description')->fulltext()->nullable();
            $table->unsignedSmallInteger('year_in_effect')->nullable(); // the year type only satisfies years from 1901 - 2155
            $table->string('status'); // RegimeAssessmentStatuses
            $table->string('ra_id')->unique();
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
        Schema::dropIfExists('regime_assessments');
    }
};
