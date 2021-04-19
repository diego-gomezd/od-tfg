<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\CurriculumSubject;
use App\Http\Controllers\Controller;
use App\Http\Requests\AcademicYearRequest;

class AcademicYearController extends Controller
{
    public function index()
    {
        return view('academicYears.index', [
            'academicYears' => AcademicYear::paginate(10)
        ]);
    }

    public function create()
    {
        $academicYear = new AcademicYear();

        $last = AcademicYear::orderBy('name', 'desc')->first();
        $years = explode('-', $last->name);
        if (count($years) > 1) {
            $last_year = intVal($years[1]) + 2000;
            $next_year = intVal($years[1]) + 1;
            $academicYear->name = $last_year . '-' . $next_year;
        }
        return view('academicYears.create', [
            'academicYear' => $academicYear
        ]);
    }

    public function store(AcademicYearRequest $request)
    {
        $academic_year = new AcademicYear();
        $academic_year->name = $request->input('name');

        if (AcademicYear::where('name', $academic_year->name)->first() == null) {
            $academic_year->save();
            return redirect()->route('academicYears.index')->with('success', 'Curso académico insertado.');
        } else {
            $request->session()->flash('warning', 'Ya existe un curso académico con ese nombre.');
            return view('academicYears.create', ['academicYear' => $academic_year]);
        }
    }

    public function show(AcademicYear $academicYear)
    {
        return view('academicYears.show', [
            'academicYear' => AcademicYear::find($academicYear->id)
        ]);
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('academicYears.edit', [
            'academicYear' => AcademicYear::find($academicYear->id)
        ]);
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $academicYear->name = $request->input('name');
        if (AcademicYear::where('name', $request->input('name'))->where('id', '!=', $academicYear->id)->first() == null) {
            $academicYear->update();
            return redirect()->route('academicYears.index')->with('success', 'Curso académico actualizado.');
        }
        $request->session()->flash('warning', 'Ya existe un curso académico con ese nombre.');
        return view('academicYears.edit', ['academicYear' => $academicYear]);
    }

    public function destroy(AcademicYear $academicYear)
    {
        $count = CurriculumSubject::where('academic_year_id', $academicYear->id)->count();
        if ($count == 0) {
            $academicYear->delete();
            return redirect()->route('academicYears.index')->with('success', 'Curso académico eliminado.');
        }
        return redirect()->route('academicYears.index')->with('error', 'No se puede eliminar el curso porque hay planes de estudio registrados en ese curso.');
    }
}
