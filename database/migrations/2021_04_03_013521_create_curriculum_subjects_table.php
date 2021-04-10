<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurriculumSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('curriculum_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained();
            $table->foreignId('curriculum_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->string('type', 5)->nullable();
            $table->string('duration', 5)->nullable();
            $table->string('course')->nullable();
            $table->integer('part_time_course')->nullable();
            $table->text('comments')->nullable();
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
        Schema::dropIfExists('curriculum_subjects');
    }
}
