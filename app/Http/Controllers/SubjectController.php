<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Models\ClassroomGroup;
use App\Models\CurriculumSubject;
use Illuminate\Routing\Controller;
use App\Http\Requests\SubjectRequest;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        return $this->filterSubjects($request->filter_name, $request->filter_department_id);
    }

    public function filter(Request $request)
    {
        return $this->filterSubjects($request->input('filter_name'), $request->input('filter_department_id'));
    }

    private function filterSubjects($filter_name, $filter_department_id)
    {
        $query = Subject::query();

        if (!empty($filter_name)) {
            $query->where(function ($queryOr) use ($filter_name) {
                $queryOr->where('name', 'LIKE', '%' . strtoupper($filter_name) . '%')
                    ->orWhere('code', 'LIKE', '%' . strtoupper($filter_name) . '%');
            });
        }
        if (!empty($filter_department_id)) {
            $query->where('department_id', $filter_department_id);
        }

        $subjects = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('subjects.index', [
            'subjects' => $subjects,
            'departments' => Department::all(['id', 'name'])->sortBy('name'),
            'filter_subject_name' => $filter_name,
            'filter_department_id' => $filter_department_id,
        ]);
    }

    public function create()
    {
        return view('subjects.create', [
            'subject' => new Subject(),
            'departments' => Department::all(['id', 'name'])->sortBy('name')
        ]);
    }

    public function store(SubjectRequest $request)
    {
        $subject = new Subject();
        $subject->department_id = $request->input('department_id');
        $subject->code = $request->input('code');
        $subject->name = $request->input('name');
        $subject->english_name = $request->input('english_name');
        $subject->comments = $request->input('comments');
        $subject->ects = $request->input('ects');
        $subject->save();

        return redirect()->route('subjects.index')->with('success', 'Asignatura ' . $subject->name . ' insertada.');
    }

    public function show(Subject $subject)
    {
        return view('subjects.show', [
            'subject' => Subject::find($subject->id),
            'departments' => Department::all(['id', 'name'])->sortBy('name')
        ]);
    }

    public function edit(Subject $subject)
    {
        return view('subjects.edit', [
            'subject' => Subject::find($subject->id),
            'departments' => Department::all(['id', 'name'])->sortBy('name')
        ]);
    }

    public function update(Request $request, Subject $subject)
    {
        $subject->department_id = $request->input('department_id');
        $subject->code = $request->input('code');
        $subject->name = $request->input('name');
        $subject->english_name = $request->input('english_name');
        $subject->comments = $request->input('comments');
        $subject->ects = $request->input('ects');

        if (Subject::where('id', '!=', $subject->id)->where('code', $request->input('code'))->first() == null) {
            $subject->update();
            return redirect()->route('subjects.index')->with('success', 'Asignatura ' . $subject->name . ' actualizada.');
        }
        $request->session()->flash('warning', 'Ya existe una asignatura con ese c??digo.');
        return view('subjects.edit', ['subject' => $subject, 'departments' => Department::all(['id', 'name'])->sortBy('name')]);
    }

    public function destroy(Subject $subject)
    {
        $count_currimulums = CurriculumSubject::where('subject_id', $subject->id)->count();
        if ($count_currimulums > 0) {
            return redirect()->route('subjects.index')->with('warning', $subject->name . ' no se puede eliminar porque est?? incluida en ' . $count_currimulums . ' plan(es) de estudio');
        }

        $count_classroom = ClassroomGroup::where('subject_id', $subject->id)->count();
        if ($count_classroom > 0) {
            return redirect()->route('subjects.index')->with('warning', $subject->name . ' no se puede eliminar porque hay definidos ' . $count_classroom . ' grupos para esta asignatura');
        }

        $subject->delete();
        return redirect()->route('subjects.index')->with('success', 'Asignatura ' . $subject->name . ' eliminada.');
    }
}
