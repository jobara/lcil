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
        Schema::create('provisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('law_policy_source_id')->constrained()->cascadeOnDelete();
            $table->string('section');
            $table->text('body');
            $table->string('reference')->nullable(); // URL
            $table->string('type_of_decision')->nullable(); // LegalChallengeTypeEnum
            $table->boolean('is_subject_to_challenge')->nullable();
            $table->boolean('is_result_of_challenge')->nullable();
            $table->text('decision_citation')->nullable();
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
        Schema::dropIfExists('provisions');
    }
};
