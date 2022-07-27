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
        Schema::create('law_policy_source_regime_assessment', function (Blueprint $table) {
            $table->foreignId('regime_assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('law_policy_source_id')->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('law_policy_source_regime_assessment');
    }
};
