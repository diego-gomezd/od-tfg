<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewCurriculumAcademicYear extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement(
            'CREATE OR REPLACE VIEW curriculum_academic_year AS SELECT ac.id AS academic_year_id, ac.name AS academic_year_name, c.id AS curriculum_id, c.code AS curriculum_code, c.name AS curriculum_name, COUNT(cs.id) AS number_subjects '
            .'FROM curriculum_subjects cs JOIN academic_years ac ON (ac.id = cs.academic_year_id) JOIN curricula c ON (c.id = cs.curriculum_id) GROUP BY ac.name, c.code, c.name;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW curriculum_academic_year');
    }
}
