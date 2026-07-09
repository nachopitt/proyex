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
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->dropFullText('projects_description_FULLTEXT');
            }
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->dropFullText('projects_title_FULLTEXT');
            }
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText(['title', 'description'], 'projects_title_description_FULLTEXT');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->dropFullText('projects_title_description_FULLTEXT');
            }
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText('description', 'projects_description_FULLTEXT');
            }
            if (Schema::getConnection()->getDriverName() !== 'sqlite') {
                $table->fullText('title', 'projects_title_FULLTEXT');
            }
        });
    }
};
