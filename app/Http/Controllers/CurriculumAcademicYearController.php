<?php

namespace App\Http\Controllers;

use App\Models\Curriculum;
use App\Models\Department;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Models\CurriculumSubject;
use Illuminate\Routing\Controller;
use App\Models\CurriculumAcademicYear;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\ExcelFileHandler\ExelFileFormatOD;

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
    }


    public function export(Request $request) {
        $format = $request->export_format;
        $academic_year = AcademicYear::find($request->academic_year_id);
        $curriculum = Curriculum::find($request->curriculum_id);
        $subjects = CurriculumSubject::where('academic_year_id', $academic_year->id)->
            where('curriculum_id', $curriculum->id)->get();

        $file_name = $format.'_'.$curriculum->code.'_'.$academic_year->name.'.xlsx';
        $file_path = public_path($file_name);

        if ($format == 'OD') {
            $excelFileFormat = new ExelFileFormatOD();
            $excelFileFormat->generateExcelFile($academic_year, $curriculum, $subjects, $file_path);
        }

        return response()->download($file_path);
    }





}