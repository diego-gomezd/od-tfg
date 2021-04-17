<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Curriculum;
use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\Combos\Course;
use App\Models\Combos\Duration;
use App\Models\CurriculumSubject;
use App\Models\Combos\SubjectType;
use Illuminate\Routing\Controller;
use App\Models\CurriculumClassroomGroup;
use App\Http\Requests\CurriculumSubjectRequest;

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
        
        if (!empty($filter_name)) {
            $query->whereHas('subject', function($q) use ($filter_name) {
                $q->where('name', 'LIKE', '%'.strtoupper($filter_name).'%');
            });
        }
        if (!empty($filter_department_id)) {
             $query->whereHas('subject', function($q) use ($filter_department_id) {
                $q->where('department_id', $filter_department_id);
            });
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
        $curriculumSubjects = $query->orderBy('course', 'asc')->orderBy('duration', 'asc')->paginate(10)->withQueryString();

        foreach ($curriculumSubjects as $curriculum_subject) {
            $curriculum_subject->num_groups = CurriculumClassroomGroup::where('curriculum_subject_id', $curriculum_subject->id)->count();
        }
            
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
            'courses' => Course::getCombo(),
            'durations' => Duration::getCombo(),
            'types' => SubjectType::getCombo(),
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
            'courses' => Course::getCombo(),
            'durations' => Duration::getCombo(),
            'types' => SubjectType::getCombo(),
            'subjects' => Subject::all()->sortBy('name'),
        ]);
    }

    public function store(CurriculumSubjectRequest $request)
    {
        $curriculum = Curriculum::find($request->curriculum_id);
        $academic_year = AcademicYear::find($request->academic_year_id);

        $curriculumSubject = new CurriculumSubject();
        $curriculumSubject->academic_year_id = $academic_year->id;
        $curriculumSubject->curriculum_id = $curriculum->id;
        $curriculumSubject->subject_id = $request->input('subject_id');
        $curriculumSubject->type = $request->input('type');
        $curriculumSubject->duration = $request->input('duration');
        $curriculumSubject->course = $request->input('course');

        if (CurriculumSubject::where('academic_year_id', $curriculumSubject->academic_year_id)->
            where('curriculum_id', $curriculumSubject->curriculum_id)->
            where('subject_id', $curriculumSubject->subject_id)->first() == null)
            {
                $curriculumSubject->save();

                return redirect()->route('curriculumSubjects.index', [
                    $curriculumSubject->academic_year_id,
                    $curriculumSubject->curriculum_id,
                ])->with('success', 'Asignatura '.$curriculumSubject->subject->name.' insertada.');
            }
        
        return redirect()->route('curriculumSubjects.create', [
            $curriculumSubject->academic_year_id,
            $curriculumSubject->curriculum_id,
        ])->with('error', 'Ya existe la asignatura '.$curriculumSubject->subject->name.' en el Plan de Estudios.');  
    }

    public function show(CurriculumSubject $curriculumSubject)
    {
        $curriculum = Curriculum::find($curriculumSubject->curriculum_id);
        $academic_year = AcademicYear::find($curriculumSubject->academic_year_id);

        return view('curriculumSubjects.show', [
            'curriculum' => $curriculum,
            'academic_year' => $academic_year,
            'curriculumSubject' => $curriculumSubject,
            'courses' => Course::getCombo(),
            'durations' => Duration::getCombo(),
            'types' => SubjectType::getCombo(),
            'subjects' => Subject::all()->sortBy('name'),
        ]);
    }

    public function edit(CurriculumSubject $curriculumSubject)
    {
        $curriculum = Curriculum::find($curriculumSubject->curriculum_id);
        $academic_year = AcademicYear::find($curriculumSubject->academic_year_id);

        return view('curriculumSubjects.edit', [
            'curriculum' => $curriculum,
            'academic_year' => $academic_year,
            'curriculumSubject' => $curriculumSubject,
            'courses' => Course::getCombo(),
            'durations' => Duration::getCombo(),
            'types' => SubjectType::getCombo(),
            'subjects' => Subject::all()->sortBy('name'),
        ]);
    }

    public function update(CurriculumSubjectRequest $request, CurriculumSubject $curriculumSubject)
    {
        $curriculumSubject->type = $request->input('type');
        $curriculumSubject->duration = $request->input('duration');
        $curriculumSubject->course = $request->input('course');

        if (CurriculumSubject::where('id', '!=', $curriculumSubject->id)->
            where('academic_year_id', $curriculumSubject->academic_year_id)->
            where('curriculum_id', $curriculumSubject->curriculum_id)->
            where('subject_id', $curriculumSubject->subject_id)->first() == null)
            {
                $curriculumSubject->update();

                return redirect()->route('curriculumSubjects.index', [
                    $curriculumSubject->academic_year_id,
                    $curriculumSubject->curriculum_id,
                ])->with('success', 'Asignatura '.$curriculumSubject->subject->name.' actualizada.');
            }

            return redirect()->route('curriculumSubjects.create', [
                $curriculumSubject->academic_year_id,
                $curriculumSubject->curriculum_id,
            ])->with('error', 'Ya existe la asignatura '.$curriculumSubject->subject->name.' en el Plan de Estudios.');  
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
