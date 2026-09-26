<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecommendationFieldsToProfilesAndPrograms extends Migration
{
    public function up()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->decimal('gpa', 4, 2)->nullable()->after('target_field');
            $table->string('language_test_type')->nullable()->after('gpa');
            $table->decimal('language_test_score', 5, 2)->nullable()->after('language_test_type');
            $table->decimal('annual_budget', 10, 2)->nullable()->after('language_test_score');
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->decimal('minimum_gpa', 4, 2)->nullable()->after('language_proficiency_requirement');
        });
    }

    public function down()
    {
        Schema::table('user_profiles', function (Blueprint $table) {
            $table->dropColumn(['gpa', 'language_test_type', 'language_test_score', 'annual_budget']);
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn('minimum_gpa');
        });
    }
}
