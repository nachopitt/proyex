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
        Schema::withoutForeignKeyConstraints(function () {
            Schema::create('project_tag', function (Blueprint $table) {
                $table->bigInteger('id')
                    ->unsigned()
                    ->autoIncrement();
                $table->bigInteger('project_id')
                    ->unsigned();
                $table->bigInteger('tag_id')
                    ->unsigned();
                $table->timestamp('created_at')
                    ->nullable();
                $table->timestamp('updated_at')
                    ->nullable();
                $table->primary('id', null);
                $table->index('project_id', 'fk_project_tag_projects1_idx');
                $table->index('tag_id', 'fk_project_tag_tags1_idx');
                $table->foreign('project_id', 'fk_project_tag_projects1')
                    ->references('id')
                    ->on('projects')->onDelete('no action')->onUpdate('no action');
                $table->foreign('tag_id', 'fk_project_tag_tags1')
                    ->references('id')
                    ->on('tags')->onDelete('no action')->onUpdate('no action');
            });
            Schema::create('project_updates', function (Blueprint $table) {
                $table->bigInteger('id')
                    ->unsigned()
                    ->autoIncrement();
                $table->text('description');
                $table->string('status', 20);
                $table->tinyInteger('progress_percentage')
                    ->unsigned();
                $table->bigInteger('project_id')
                    ->unsigned();
                $table->bigInteger('updater_user_id')
                    ->unsigned();
                $table->timestamp('created_at')
                    ->nullable();
                $table->timestamp('updated_at')
                    ->nullable();
                $table->timestamp('deleted_at')
                    ->nullable();
                $table->primary('id', null);
                $table->index('project_id', 'fk_project_updates_projects1_idx');
                $table->index('updater_user_id', 'fk_project_updates_users1_idx');
                $table->foreign('project_id', 'fk_project_updates_projects1')
                    ->references('id')
                    ->on('projects')->onDelete('no action')->onUpdate('no action');
                $table->foreign('updater_user_id', 'fk_project_updates_users1')
                    ->references('id')
                    ->on('users')->onDelete('no action')->onUpdate('no action');
            });
            Schema::create('projects', function (Blueprint $table) {
                $table->bigInteger('id')
                    ->unsigned()
                    ->autoIncrement();
                $table->string('title', 255);
                $table->text('description')
                    ->nullable();
                $table->string('priority', 20);
                $table->string('current_status', 20);
                $table->tinyInteger('current_progress_percentage')
                    ->unsigned();
                $table->date('start_date')
                    ->nullable();
                $table->date('due_date')
                    ->nullable();
                $table->date('end_date')
                    ->nullable();
                $table->bigInteger('parent_id')
                    ->unsigned()
                    ->nullable();
                $table->bigInteger('reporter_user_id')
                    ->unsigned();
                $table->bigInteger('assigned_user_id')
                    ->unsigned()
                    ->nullable();
                $table->timestamp('created_at')
                    ->nullable();
                $table->timestamp('updated_at')
                    ->nullable();
                $table->timestamp('deleted_at')
                    ->nullable();
                $table->primary('id', null);
                $table->index('parent_id', 'fk_projects_projects1_idx');
                $table->index('reporter_user_id', 'fk_projects_users1_idx');
                $table->index('assigned_user_id', 'fk_projects_users2_idx');
                $table->fullText(['title', 'description'], 'projects_title_description_FULLTEXT');
                $table->foreign('parent_id', 'fk_projects_projects1')
                    ->references('id')
                    ->on('projects')->onDelete('no action')->onUpdate('no action');
                $table->foreign('reporter_user_id', 'fk_projects_users1')
                    ->references('id')
                    ->on('users')->onDelete('no action')->onUpdate('no action');
                $table->foreign('assigned_user_id', 'fk_projects_users2')
                    ->references('id')
                    ->on('users')->onDelete('no action')->onUpdate('no action');
            });
            Schema::create('tags', function (Blueprint $table) {
                $table->bigInteger('id')
                    ->unsigned()
                    ->autoIncrement();
                $table->string('name', 255);
                $table->timestamp('created_at')
                    ->nullable();
                $table->timestamp('updated_at')
                    ->nullable();
                $table->timestamp('deleted_at')
                    ->nullable();
                $table->primary('id', null);
                $table->unique('name', 'name_UNIQUE');
            });
            Schema::create('user_profiles', function (Blueprint $table) {
                $table->bigInteger('id')
                    ->unsigned()
                    ->autoIncrement();
                $table->string('first_name', 255);
                $table->string('last_name', 255);
                $table->tinyInteger('active')
                    ->unsigned();
                $table->bigInteger('user_id')
                    ->unsigned();
                $table->timestamp('created_at')
                    ->nullable();
                $table->timestamp('updated_at')
                    ->nullable();
                $table->primary('id', null);
                $table->index('user_id', 'fk_user_profiles_users1_idx');
                $table->unique('user_id', 'user_id_UNIQUE');
                $table->foreign('user_id', 'fk_user_profiles_users1')
                    ->references('id')
                    ->on('users')->onDelete('no action')->onUpdate('no action');
            });
            Schema::create('user_roles', function (Blueprint $table) {
                $table->bigInteger('id')
                    ->unsigned()
                    ->autoIncrement();
                $table->string('role', 20);
                $table->bigInteger('user_id')
                    ->unsigned();
                $table->timestamp('created_at')
                    ->nullable();
                $table->timestamp('updated_at')
                    ->nullable();
                $table->primary('id', null);
                $table->index('user_id', 'fk_user_roles_users1_idx');
                $table->foreign('user_id', 'fk_user_roles_users1')
                    ->references('id')
                    ->on('users')->onDelete('no action')->onUpdate('no action');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::withoutForeignKeyConstraints(function () {
            Schema::dropIfExists('project_tag');
            Schema::dropIfExists('project_updates');
            Schema::dropIfExists('projects');
            Schema::dropIfExists('tags');
            Schema::dropIfExists('user_profiles');
            Schema::dropIfExists('user_roles');
        });
    }
};
