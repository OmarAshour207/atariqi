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
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->string('title_ar', 255)->nullable();
            $table->longText('content_ar')->nullable();
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('title_ar', 255)->nullable();
            $table->text('description_ar')->nullable();
        });

        Schema::table('homepage_stats', function (Blueprint $table) {
            $table->string('label_ar')->nullable();
        });

        Schema::table('partner_achievements', function (Blueprint $table) {
            $table->string('title_ar', 255)->nullable();
            $table->longText('description_ar')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('homepage_sections', function (Blueprint $table) {
            $table->dropColumn('title_ar');
            $table->dropColumn('content_ar');
        });

        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn('title_ar');
            $table->dropColumn('description_ar');
        });

        Schema::table('homepage_stats', function (Blueprint $table) {
            $table->dropColumn('label_ar');
        });

        Schema::table('partner_achievements', function (Blueprint $table) {
            $table->dropColumn('title_ar');
            $table->dropColumn('description_ar');
        });

    }
};
