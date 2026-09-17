<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApplicationPersonalizationFieldsToApplicationStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('application_steps', function (Blueprint $table) {
            $table->json('applicable_countries')->nullable()->after('step_order');
            $table->string('related_document_type')->nullable()->after('applicable_countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('application_steps', function (Blueprint $table) {
            $table->dropColumn(['applicable_countries', 'related_document_type']);
        });
    }
}
