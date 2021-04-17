<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\ClassroomGroup;
use App\Models\Combos\Duration;
use App\Http\Controllers\Controller;
use App\Models\CurriculumClassroomGroup;
use App\Http\Requests\ClassroomGroupRequest;

class ClassroomGroupController extends Controller
{
    public function index(Request $request)
    {
        return $this->filterClassroomGroups($request->filter_subject_id, $request->filter_location, $request->filter_academic_year_id, $request->filter_duration);
    }

    public function filter(Request $request)
    {
        return $this->filterClassroomGroups($request->input('filter_subject_id'), $request->input('filter_location'), $request->input('filter_academic_year_id'), $request->input('filter_duration'));
    }

    private function filterClassroomGroups($filter_subject_id, $filter_location, $filter_academic_year_id, $filter_duration)
    {
        $academic_year = $filter_academic_year_id ? AcademicYear::find($filter_academic_year_id) : AcademicYear::all()->last();
        $filter_academic_year_id = $academic_year->id;

        $query = ClassroomGroup::query();
        if (!empty($filter_subject_id)) {
            $query->where('subject_id', $filter_subject_id);
        }
        if (!empty($filter_location)) {
            $query->where('location', $filter_location);
        }
        if (!empty($filter_academic_year_id)) {
            $query->where('academic_year_id', $filter_academic_year_id);
        }
        if (!empty($filter_duration)) {
            $query->where('duration', $filter_duration);
        }

        $groups = $query->orderBy('academic_year_id', 'asc')
            ->orderBy('subject_id', 'asc')
            ->orderBy('subject_id', 'asc')
            ->orderBy('location', 'asc')
            ->orderBy('name', 'asc')->paginate(10)->withQueryString();
            
        return view('classroomGroups.index', [
            'groups' => $groups,
            'academic_year' => $academic_year,
            'subjects' => Subject::all()->sortBy('name'),
            'academic_years' => AcademicYear::all(['id', 'name'])->sortBy('name'),
            'locations' => ClassroomGroup::whereNotNull('location')->distinct()->get(['location']),
            'durations' => Duration::getCombo(),
            'filter_subject_id' => $filter_subject_id,
            'filter_location' => $filter_location,
            'filter_academic_year_id' => $filter_academic_year_id,
            'filter_duration' => $filter_duration,
        ]);
    }

    public function create(Request $request)
    {
        $academic_year = AcademicYear::find($request->academic_year_id);
        $classroomGroup = new ClassroomGroup();
        $classroomGroup->academic_year_id = $academic_year->id;

        return view('classroomGroups.create', [
            'academic_year' => $academic_year,
            'classroomGroup' => $classroomGroup,
            'durations' => Duration::getCombo(),
            'subjects' => Subject::all()->sortBy('name'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClassroomGroupRequest $request)
    {
        $classroomGroup = new ClassroomGroup();
        $classroomGroup->academic_year_id = $request->academic_year_id;
        $classroomGroup->subject_id = $request->input('subject_id');
        $classroomGroup->name = $request->input('name');
        $classroomGroup->location = $request->input('location');
        $classroomGroup->activity_id = $request->input('activity_id');
        $classroomGroup->activity_group = $request->input('activity_group');
        $classroomGroup->language = $request->input('language');
        $classroomGroup->duration = $request->input('duration');
        $classroomGroup->capacity = $request->input('capacity');
        $classroomGroup->capacity_left = $request->input('capacity_left');
        $classroomGroup->location = $request->input('location');

        if (ClassroomGroup::where('academic_year_id',  $classroomGroup->academic_year_id)
            ->where('subject_id',  $classroomGroup->subject_id)->where('activity_group',  $classroomGroup->activity_group)->first() == null) {
                $classroomGroup->save();
                return redirect()->route('classroomGroups.index', [
                    'filter_academic_year_id' => $request->academic_year_id,
                ])->with('success', 'Grupo '.$classroomGroup->activity_id.' - '.$classroomGroup->name.' creado para la asignatura '.$classroomGroup->subject->name);  
            }

        $request->session()->flash('warning', 'El grupo '.$classroomGroup->activity_group.' ya existe para la asignatura '.$classroomGroup->subject->name);
        
        return view('classroomGroups.create', [
            'academic_year' => $classroomGroup->academicYear,
            'classroomGroup' => $classroomGroup,
            'durations' => Duration::getCombo(),
            'subjects' => Subject::all()->sortBy('name'),
        ]);
    }

    public function edit(ClassroomGroup $classroomGroup)
    {
        return view('classroomGroups.edit', [
            'academic_year' => $classroomGroup->academicYear,
            'classroomGroup' => $classroomGroup,
            'durations' => Duration::getCombo(),
            'subjects' => Subject::all()->sortBy('name'),
        ]);
    }

    public function update(ClassroomGroupRequest $request, ClassroomGroup $classroomGroup)
    {
        $classroomGroup->name = $request->input('name');
        $classroomGroup->location = $request->input('location');
        $classroomGroup->activity_id = $request->input('activity_id');
        $classroomGroup->activity_group = $request->input('activity_group');
        $classroomGroup->language = $request->input('language');
        $classroomGroup->duration = $request->input('duration');
        $classroomGroup->capacity = $request->input('capacity');
        $classroomGroup->capacity_left = $request->input('capacity_left');
        $classroomGroup->location = $request->input('location');

        if (ClassroomGroup::where('id', '!=', $classroomGroup->id)
            ->where('academic_year_id', $classroomGroup->academic_year_id)
            ->where('subject_id',  $classroomGroup->subject_id)->where('activity_group',  $classroomGroup->activity_group)->first() == null) {
                $classroomGroup->update();
                return redirect()->route('classroomGroups.index', [
                    'filter_academic_year_id' => $classroomGroup->academic_year_id,
                ])->with('success', 'Grupo '.$classroomGroup->activity_id.' - '.$classroomGroup->name.' actualizado para la asignatura '.$classroomGroup->subject->name);  
            }

        $request->session()->flash('warning', 'El grupo '.$classroomGroup->activity_group.' ya existe para la asignatura '.$classroomGroup->subject->name);
        return view('classroomGroups.edit', [
            'academic_year' => $classroomGroup->academicYear,
            'classroomGroup' => $classroomGroup,
            'durations' => Duration::getCombo(),
            'subjects' => Subject::all()->sortBy('name'),
        ]);
    }

    public function destroy(ClassroomGroup $classroomGroup)
    {
        {
            $count = CurriculumClassroomGroup::where('classroom_group_id', $classroomGroup->id)->count();
            if ($count == 0)
            {
                $classroomGroup->delete();
                return redirect()->route('classroomGroups.index', [
                    'filter_academic_year_id' => $classroomGroup->academic_year_id,
                ])->with('success', 'Grupo de clase '.$classroomGroup->subject->name.' ' .$classroomGroup->activity_id.' - '.$classroomGroup->name.' eliminado.');
            }
            return redirect()->route('classroomGroups.index', [
                'filter_academic_year_id' => $classroomGroup->academic_year_id,
            ])->with('error', 'No se puede eliminar el grupo '.$classroomGroup->subject->name.' ' .$classroomGroup->activity_id.' - '.$classroomGroup->name.' porque hay asignaturas ofertadas que lo incluyen.');
        }
    }
}
