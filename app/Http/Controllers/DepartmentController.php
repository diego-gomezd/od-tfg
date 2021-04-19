<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Requests\DepartmentRequest;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('departments.index', [
            'departments' => Department::orderBy('name', 'asc')->paginate(10)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('departments.create', [
            'department' => new Department()
        ]);
    }

    public function store(DepartmentRequest $request)
    {
        $department = new Department();
        $department->code = $request->input('code');
        $department->name = $request->input('name');

        if (Department::where('name', $department->name)->orWhere('code', $department->code)->first() == null) 
        {
            $department->save();
            return redirect()->route('departments.index')->with('success', 'Departamento insertado.');
        } else
        {
            $request->session()->flash('warning','Ya existe un departamento con ese nombre o código.'); 
            return view('departments.create', ['department' => $department]);
        }
    }

    public function show(Department $department)
    {
        return view('departments.show', [
            'department' => Department::find($department->id)
        ]);
    }

    public function edit(Department $department)
    {
        return view('departments.edit', [
            'department' => Department::find($department->id)
        ]);
    }

    public function update(Request $request, Department $department)
    {
        $department->code = $request->input('code');
        $department->name = $request->input('name');

        if (Department::where('id', '!=', $department->id)->where(function($query) use ($request) {
                $query->orWhere('name', $request->input('name'))
                      ->orWhere('code', $request->input('code'));
            })->first() == null)
        {
            $department->update();
            return redirect()->route('departments.index')->with('success', 'Departamento actualizado.');
        }
        $request->session()->flash('warning','Ya existe un departamento con ese nombre o código.'); 
        return view('departments.edit', ['department' => $department]);
    }

    public function destroy(Department $department)
    {
        $count = Subject::where('department_id', $department->id)->count();
        if ($count == 0)
        {
            $department->delete();
            return redirect()->route('departments.index')->with('success', 'Departamento eliminado.');
        }
        return redirect()->route('departments.index')->with('error', 'No se puede eliminar el departamento '.$department->name.' porque hay asignaturas registrados en ese departamento.');
    }
}