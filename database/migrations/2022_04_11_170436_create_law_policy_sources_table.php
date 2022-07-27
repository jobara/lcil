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
        Schema::create('law_policy_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name')->fulltext();
            $table->string('type')->nullable(); // LawPolicyTypes
            $table->boolean('is_core')->nullable();
            $table->string('reference')->nullable(); // URL
            $table->string('jurisdiction'); // ISO 3166-1 alpha-2 code or ISO 3166-2 code
            $table->string('municipality')->nullable();
            $table->string('slug')->unique();
            $table->unsignedSmallInteger('year_in_effect')->nullable(); // the year type only satisfies years from 1901 - 2155
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
        Schema::dropIfExists('law_policy_sources');
    }
};
