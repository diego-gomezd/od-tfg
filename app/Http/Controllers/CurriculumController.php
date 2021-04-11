<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use Illuminate\Http\Request;
use App\Models\CurriculumSubject;
use Illuminate\Routing\Controller;
use App\Http\Requests\CurriculumRequest;

class CurriculumController extends Controller
{
    public function index()
    {
        return view('curriculums.index', [
            'curriculums' => Curriculum::paginate(10)
        ]);
    }

    public function create()
    {
        return view('curriculums.create', [
            'curriculum' => new Curriculum()
        ]);
    }

    public function store(CurriculumRequest $request)
    {
        $curriculum = new Curriculum();
        $curriculum->code = $request->input('code');
        $curriculum->name = $request->input('name');
        $curriculum->save();

        return redirect()->route('curriculums.index')->with('success', 'Plan de Estudios \''.$curriculum->name.'\'insertado.');
    }


    public function show(Curriculum $curriculum)
    {
        return view('curriculums.show', [
            'curriculum' => Curriculum::find($curriculum->id)
        ]);
    }

    public function edit(Curriculum $curriculum)
    {
        return view('curriculums.edit', [
            'curriculum' => Curriculum::find($curriculum->id)
        ]);
    }

    public function update(Request $request, Curriculum $curriculum)
    {
        $curriculum->code = $request->input('code');
        $curriculum->name = $request->input('name');

        if (Curriculum::where('id', '!=', $curriculum->id)->where(function($query) use ($request) {
                $query->orWhere('name', $request->input('name'))
                      ->orWhere('code', $request->input('code'));
            })->first() == null)
        {
            $curriculum->update();
            return redirect()->route('curriculums.index')->with('success', 'Plan de Estudios actualizado.');
        }
        $request->session()->flash('warning','Ya existe un Plan de Estudios con ese nombre o código.'); 
        return view('curriculums.edit', ['curriculum' => $curriculum]);
    }

    public function destroy(Curriculum $curriculum)
    {
        $count = CurriculumSubject::where('curriculum_id', $curriculum->id)->count();
        if ($count == 0)
        {
            $curriculum->delete();
            return redirect()->route('curriculums.index')->with('success', 'Plan de Estudios \''.$curriculum->name.'\' eliminado.');
        }
        return redirect()->route('curriculums.index')->with('error', 'No se puede eliminar el Plan de Estudios '.$curriculum->name.' porque hay Ofertas Docentes que lo incluyen.');
    }
}
