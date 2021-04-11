<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\CurriculumAcademicYear;

class CurriculumAcademicYearController extends Controller
{

    public function index(Request $request)
    {
        return $this->filterCurriculumAcademicYears($request->filter_academic_year_id, $request->filter_curriculum_id);
    }

    public function filter(Request $request)
    {
        return $this->filterCurriculumAcademicYears($request->input('filter_academic_year_id'), $request->input('filter_curriculum_id'));
    }

    private function filterCurriculumAcademicYears($filter_academic_year_id, $filter_curriculum_id)
    {
        $query = CurriculumAcademicYear::query();

        if (!empty($filter_academic_year_id)) {
            $query->where('academic_year_id', $filter_academic_year_id);
        }
        if (!empty($filter_curriculum_id)) {
            $query->where('curriculum_id', $filter_curriculum_id);
        }

        $curriculumAcademicYears = $query->orderBy('academic_year_name', 'asc')->orderBy('curriculum_code', 'asc')->paginate(10)->withQueryString();
        return view('curriculumAcademicYears.index', [
            'curriculumAcademicYears' => $curriculumAcademicYears,
            'filter_academic_year_id' => $filter_academic_year_id,
            'filter_curriculum_id' => $filter_curriculum_id,
            'academic_years' => AcademicYear::all(['id', 'name'])->sortBy('name'),
            'curriculums' => Curriculum::all(['id', 'name'])->sortBy('name'),
        ]);


        return view('subjects.index', [
            'subjects' => $subjects,
            'departments' => Department::all(['id', 'name'])->sortBy('name'),
            'filter_subject_name' => $filter_name,
            'filter_department_id' => $filter_department_id,
        ]);
    }
}