<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\CurriculumSubject;
use Illuminate\Routing\Controller;
use App\Models\CurriculumAcademicYear;
use App\ExcelFileHandler\ExelFileFormatGD;
use App\ExcelFileHandler\ExelFileFormatOD;
use App\Models\ClassroomGroup;
use App\Models\CurriculumClassroomGroup;

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

        $last = AcademicYear::orderBy('name', 'desc')->first();

        $curriculumAcademicYears = $query->orderBy('academic_year_name', 'asc')->orderBy('curriculum_code', 'asc')->paginate(10)->withQueryString();
        return view('curriculumAcademicYears.index', [
            'curriculumAcademicYears' => $curriculumAcademicYears,
            'filter_academic_year_id' => $filter_academic_year_id,
            'filter_curriculum_id' => $filter_curriculum_id,
            'academic_years' => AcademicYear::all(['id', 'name'])->sortBy('name'),
            'curriculums' => Curriculum::all(['id', 'code', 'name'])->sortBy('name'),
            'next_year' => $last != null ? $last->name : null,
        ]);
    }

    public function export(Request $request)
    {
        $format = $request->export_format;
        $academic_year = AcademicYear::find($request->academic_year_id);
        $curriculum = Curriculum::find($request->curriculum_id);

        $file_name = $format . '_' . $curriculum->code . '_' . $academic_year->name . '.xlsx';
        $file_path = public_path($file_name);

        if ($format == 'OD') {
            $subjects = CurriculumSubject::where('academic_year_id', $academic_year->id)->where('curriculum_id', $curriculum->id)->get();

            $excelFileFormat = new ExelFileFormatOD();
            $excelFileFormat->generateExcelFile($academic_year, $curriculum, $subjects, $file_path);
        } else if ($format == 'GD') {
            $groups = CurriculumClassroomGroup::whereHas('curriculumSubject', function ($q) use ($academic_year) {
                $q->where('academic_year_id', $academic_year->id);
            })->whereHas('curriculumSubject', function ($q) use ($curriculum) {
                $q->where('curriculum_id', $curriculum->id);
            })->get();

            $excelFileFormat = new ExelFileFormatGD();
            $excelFileFormat->generateExcelFile($academic_year, $curriculum, $groups, $file_path);
        }

        return response()->download($file_path);
    }

    public function duplicate(Request $request)
    {
        $past_academic_year_id = $request->input('academic_year_id');
        $curriculum_id = $request->input('curriculum_id');
        $next_year = $request->input('next_year');

        $curriculum = Curriculum::find($curriculum_id);
        $next_academic_year = AcademicYear::firstOrCreate(['name' =>  $next_year]);

        $curriculumAcademicYears = CurriculumAcademicYear::where('academic_year_name', $next_academic_year)->where('curriculum_id', $curriculum_id)->get();
        if ($curriculumAcademicYears->count() > 0) {
            return redirect()->route('curriculumAcademicYears.index')->with('warning', 'Ya existe una Oferta Docente generada para el Plan de Esudios ' . $curriculum->name . ' para el curso académico ' . $next_academic_year->name);
        }

        //Buscar la curriculum_subjects y duplicarlas
        $old_curriculum_subjects = CurriculumSubject::where(['academic_year_id' => $past_academic_year_id, 'curriculum_id' => $curriculum_id])->get();
        foreach ($old_curriculum_subjects as $old_curriculum_subject) {
            $new_curriculum_subject = $old_curriculum_subject->replicate();
            $new_curriculum_subject->academic_year_id = $next_academic_year->id;
            $new_curriculum_subject->save();

            //Se buscan los grupos antiguos y se duplican
            $old_classroom_groups = ClassroomGroup::where(['academic_year_id' => $past_academic_year_id, 'subject_id' => $new_curriculum_subject->subject_id])->get();
            foreach ($old_classroom_groups as $old_classroom_group) {
                $new_classroom_group = $old_classroom_group->replicate();
                $new_classroom_group->academic_year_id = $next_academic_year->id;
                $new_classroom_group->save();

                $old_curriculum_classroom_groups = CurriculumClassroomGroup::where(['curriculum_subject_id' => $old_curriculum_subject->id, 'classroom_group_id' => $old_classroom_group->id])->get();
                foreach ($old_curriculum_classroom_groups as $old_curriculum_classroom_group) {
                    $new_curriculum_classroom_group = $old_curriculum_classroom_group->replicate();
                    $new_curriculum_classroom_group->curriculum_subject_id = $new_curriculum_subject->id;
                    $new_curriculum_classroom_group->classroom_group_id = $new_classroom_group->id;
                    $new_curriculum_classroom_group->save();
                }
            }
        }
        return redirect()->route('curriculumAcademicYears.index')->with('success', 'Oferta Docente para el Plan de Esudios ' .$curriculum->name. ' generada para el curso académico ' . $next_academic_year->name);

    }
}
