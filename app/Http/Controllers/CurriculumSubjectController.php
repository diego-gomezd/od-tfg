<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Curriculum;
use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\CurriculumSubject;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use App\Models\CurriculumClassroomGroup;

class CurriculumSubjectController extends Controller
{

    public function index(Request $request)
    {
        return $this->filterSubjects(
            $request->academic_year_id,
            $request->curriculum_id,
            $request->filter_name, 
            $request->filter_department_id,
            $request->filter_course,
            $request->filter_duration,
            $request->filter_type
        );
    }

    public function filter(Request $request)
    {
        return $this->filterSubjects(
            $request->academic_year_id,
            $request->curriculum_id,
            $request->input('filter_name'), 
            $request->input('filter_department_id'),
            $request->input('filter_course'),
            $request->input('filter_duration'),
            $request->input('filter_type'));
    }

    private function filterSubjects($academic_year_id, $curriculum_id, $filter_name, $filter_department_id, $filter_course, $filter_duration, $filter_type)
    {
        $query = CurriculumSubject::where('academic_year_id', $academic_year_id)->where('curriculum_id', $curriculum_id);
        $query->join('subjects', 'subjects.id', '=', 'curriculum_subjects.subject_id');
        if (!empty($filter_name)) {
            $query->where('subjects.name', 'LIKE', '%'.strtoupper($filter_name).'%');
        }
        if (!empty($filter_department_id)) {
            $query->where('subjects.department_id', $filter_department_id);
        }
        if (!empty($filter_course)) {
            $query->where('course', $filter_course);
        }
        if (!empty($filter_duration)) {
            $query->where('duration', $filter_duration);
        }
        if (!empty($filter_type)) {
            $query->where('type', $filter_type);
        }

        $aux = $query->get();
        $curriculumSubjects = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        $courses = $aux->map(function ($values) {
            $values->course;
        })->unique()->all();
        $durations = $aux->map(function ($values) {
            return $values->duration;
        })->unique()->all();
        $types = $aux->map(function ($values) {
            return $values->type;
        })->unique()->all();
 

        $curriculum = Curriculum::find($curriculum_id);
        $academic_year = AcademicYear::find($academic_year_id);

        return view('curriculumSubjects.index', [
            'curriculum' => $curriculum,
            'academic_year' => $academic_year,
            'curriculumSubjects' => $curriculumSubjects,
            'academic_year_id' => $academic_year_id,
            'curriculum_id' => $curriculum_id,
            'filter_name' => $filter_name,
            'filter_department_id' => $filter_department_id,
            'filter_course' => $filter_course,
            'filter_duration' => $filter_duration,
            'filter_type' => $filter_type,
            'courses' => $courses,
            'durations' => $durations,
            'types' => $types,
            'departments' => Department::all(['id', 'name'])->sortBy('name'),
        ]);
    }

    public function create(Request $request)
    {
        $curriculum = Curriculum::find($request->curriculum_id);
        $academic_year = AcademicYear::find($request->academic_year_id);

        $curriculumSubject = new CurriculumSubject();
        $curriculumSubject->academic_year_id = $academic_year->id;
        $curriculumSubject->curriculum_id = $curriculum->id;

        return view('curriculumSubjects.create', [
            'curriculum' => $curriculum,
            'academic_year' => $academic_year,
            'curriculumSubject' => $curriculumSubject,
            'courses' => ['1º', '2º', '3º', '4º'],
            'durations' => ['C1', 'C2', 'TF'],
            'types' => ['T', 'B', 'P', 'O'],
            'subjects' => Subject::all()->sortBy('name'),
        ]);
    }

    public function store(CurriculumSubjectRequest $request)
    {
        $curriculum = Curriculum::find($request->curriculum_id);
        $academic_year = AcademicYear::find($request->academic_year_id);

        $curriculumSubject = new CurriculumSubject();
        $curriculumSubject->academic_year_id = $academic_year->id;
        $curriculumSubject->curriculum_id -> $curriculum->id;
        $curriculumSubject->subject_id -> $request->input('subject_id');
        $curriculumSubject->type -> $request->input('type');
        $curriculumSubject->duration -> $request->input('duration');
        $curriculumSubject->course -> $request->input('course');

        $curriculumSubject.save();

        return redirect()->route('curriculumSubjects.index', [
            $curriculumSubject->academic_year_id,
            $curriculumSubject->curriculum_id,
        ])->with('success', 'Asignatura '.$curriculumSubject->subject->name.' insertada.');
    }

    public function show(CurriculumSubject $curriculumSubject)
    {
        $curriculum = Curriculum::find($curriculumSubject->curriculum_id);
        $academic_year = AcademicYear::find($curriculumSubject->academic_year_id);

        return view('curriculumSubjects.show', [
            'curriculum' => $curriculum,
            'academic_year' => $academic_year,
            'curriculumSubject' => $curriculumSubject,
            'courses' => ['1º', '2º', '3º', '4º'],
            'durations' => ['C1', 'C2', 'TF'],
            'types' => ['T', 'B', 'P', 'O'],
        ]);
    }

    public function edit(CurriculumSubject $curriculumSubject)
    {
        $curriculum = Curriculum::find($curriculumSubject->curriculum_id);
        $academic_year = AcademicYear::find($curriculumSubject->academic_year_id);

        return view('curriculumSubjects.show', [
            'curriculum' => $curriculum,
            'academic_year' => $academic_year,
            'curriculumSubject' => $curriculumSubject,
            'courses' => ['1º', '2º', '3º', '4º'],
            'durations' => ['C1', 'C2', 'TF'],
            'types' => ['T', 'B', 'P', 'O'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CurriculumSubject  $curriculumSubject
     * @return \Illuminate\Http\Response
     */
    public function update(CurriculumSubjectRequest $request, CurriculumSubject $curriculumSubject)
    {
        //
    }

    public function destroy(CurriculumSubject $curriculumSubject)
    {
 
        $count_classroom = CurriculumClassroomGroup::where('curriculum_subject_id', $curriculumSubject->id)->count();
        if ($count_classroom > 0) {
            return redirect()->route('curriculumSubjects.index', 
                ['academic_year_id' => $curriculumSubject->academic_year_id,
                'curriculum_id' => $curriculumSubject->curriculum_id ])
            ->with('warning', $curriculumSubject->subject->name.' no se puede eliminar porque hay definidos '.$count_classroom.' grupos para esta asignatura');
        }

        $curriculumSubject->delete();
        return redirect()->route('curriculumSubjects.index', 
        ['academic_year_id' => $curriculumSubject->academic_year_id,
        'curriculum_id' => $curriculumSubject->curriculum_id ])->with('success', 'Asignatura '.$curriculumSubject->subject->name.' eliminada.');
    }
}
