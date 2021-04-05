<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassroomGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classroom_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->string('name', 200);
            $table->string('activity_id', 45);
            $table->string('activity_group', 45);
            $table->string('language', 5);
            $table->string('duration', 5);
            $table->integer('capacity')->nullable();
            $table->integer('capacity_left')->nullable();
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
        Schema::dropIfExists('classroom_groups');
    }
}
