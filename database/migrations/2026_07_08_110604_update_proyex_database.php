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
        Schema::table('tags', function (Blueprint $table) {
            $table->dropIndex('name_UNIQUE');
            $table->unique('name', 'tags_name_UNIQUE');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_email_unique');
            $table->unique('email', 'users_email_UNIQUE');
            $table->unique('name', 'users_name_UNIQUE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropUnique('tags_name_UNIQUE');
            $table->unique('name', 'name_UNIQUE');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_email_UNIQUE');
            $table->dropUnique('users_name_UNIQUE');
            $table->unique('email', 'users_email_unique');
        });
    }
};
