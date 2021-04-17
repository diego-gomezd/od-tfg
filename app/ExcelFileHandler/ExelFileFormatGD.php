<?php

namespace App\ExcelFileHandler;

use App\Models\Subject;
use App\Models\Curriculum;
use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\ClassroomGroup;
use App\Models\CurriculumSubject;
use App\Models\UploadedFileResult;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;
use App\Models\CurriculumClassroomGroup;
use App\ExcelFileHandler\ExcelFileFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\ExcelFileHandler\IExcelFileFormat;

class ExelFileFormatGD extends ExcelFileFormat implements IExcelFileFormat {
    protected const COLUMNS_FORMAT = array('AÑO','PLAN','NOM_PLAN','ID-ACTIVIDAD','GRUPO_ACTIV','NOMBRE_GRUPO','ASIG','NOM_ASIGNATURA','IDIOMA','DURACIÓN','CAP','CAP_RES','OBSERVACIONES');
    protected const GD_FORMAT = ['name' => 'GD', 'columns' => self::COLUMNS_FORMAT];

    public function proces_excel($data, UploadedFileResult &$file_result) {
        foreach ($data as $row_num => $row)
        {
            if ($row[0] != null)
            {
                $status_value = $this->process_row($row, $row_num + 2);
                if ($status_value != null && count($status_value) > 0) 
                {
                    $file_result->addResult($status_value);
                }             
            }       
        }
    }

    public function getFormat() : string {
        return self::GD_FORMAT['name'];
    }

    public static function build(array $header) : ?IExcelFileFormat {
        $excelFileFormat = null;
        if (self::isValidExcelFormat($header, self::COLUMNS_FORMAT)) {
            $excelFileFormat = new self();
        }
        return $excelFileFormat;
    }

    private function process_row($row, $row_num)
    {
        $status = array();
        $with_error = false;

        $academic_year_name = $row[0];
        if (!$this->validateColumn($academic_year_name, self::COLUMNS_FORMAT[0], $row_num, true, false, 45, null, $status))
        {
            $with_error = true;
        }

        $curriculum_code = $row[1];
        if (!$this->validateColumn($curriculum_code, self::COLUMNS_FORMAT[1], $row_num, true, false, 15, null, $status))
        {
            $with_error = true;
        }

        $curriculum_name = $row[2];
        if (!$this->validateColumn($curriculum_name, self::COLUMNS_FORMAT[2], $row_num, false, true, 100, null, $status))
        {
            $with_error = true;
        }

        $classroom_activity_id = $row[3];
        if (!$this->validateColumn($classroom_activity_id, self::COLUMNS_FORMAT[3], $row_num, false, true, 45, null, $status))
        {
            $with_error = true;
        }

        $classroom_code = $row[4];
        if (!$this->validateColumn($classroom_code, self::COLUMNS_FORMAT[4], $row_num, false, true, 45, null, $status))
        {
            $with_error = true;
        }

        $classroom_name = $row[5];
        if (!$this->validateColumn($classroom_name, self::COLUMNS_FORMAT[5], $row_num, false, true, 200, null, $status))
        {
            $with_error = true;
        }

        $subject_code = $row[6];
        if (!$this->validateColumn($subject_code, self::COLUMNS_FORMAT[6], $row_num, true, false, 15, null, $status))
        {
            $with_error = true;
        }

        $subject_name = $row[7];
        if (!$this->validateColumn($subject_name, self::COLUMNS_FORMAT[7], $row_num, false, true, 200, null, $status))
        {
            $with_error = true;
        }

        $classroom_language = $row[8];
        if (!$this->validateColumn($classroom_language, self::COLUMNS_FORMAT[8], $row_num, false, false, 5, null, $status))
        {
            $with_error = true;
        }

        $subject_duration = $row[9];
        if (!$this->validateColumn($subject_duration, self::COLUMNS_FORMAT[9], $row_num, false, true, 5, null, $status))
        {
            $with_error = true;
        }

        $classroom_capacity = $row[10];
        if (!$this->validateColumn($classroom_capacity, self::COLUMNS_FORMAT[10], $row_num, false, false, null, true, $status))
        {
            $with_error = true;
        }

        $classroom_capacity_left = $row[11];
        if (!$this->validateColumn($classroom_capacity_left, self::COLUMNS_FORMAT[11], $row_num, false, false, null, true, $status))
        {
            $with_error = true;
        }

        $classroom_comments = $row[12];
        if (!$this->validateColumn($classroom_comments, self::COLUMNS_FORMAT[12], $row_num, false, false, 65535, null, $status))
        {
            $with_error = true;
        }

        if (!$with_error)
        {
            try {
                $academic_year = AcademicYear::firstOrCreate(['name' => trim($academic_year_name)]);
                $curriculum = Curriculum::firstOrCreate(
                    ['code' => trim($curriculum_code)], ['name' => trim($curriculum_name)]
                );
               
                $subject = Subject::firstOrCreate(['code' => trim($subject_code)],
                    ['name' => trim($subject_name), 'duration' => trim($subject_duration)]
                );
        
                $curriculum_subject = CurriculumSubject::firstOrCreate(
                    ['curriculum_id' => $curriculum->id, 'academic_year_id' => $academic_year->id, 'subject_id' => $subject->id],
                    ['duration' => trim($subject_duration)]
                );
        
                $classroom_group = ClassroomGroup::firstOrCreate(
                    ['academic_year_id' => $academic_year->id, 'subject_id' => $subject->id],
                    [
                        'name' => trim($classroom_name),
                        'activity_group' => trim($classroom_code),
                        'activity_id' => $classroom_activity_id,
                        'language' => trim($classroom_language),
                        'capacity' => $classroom_capacity,
                        'capacity_left' => $classroom_capacity_left,
                        'duration' => trim($subject_duration)
                    ]
                );
        
                $curriculum_classroom = CurriculumClassroomGroup::firstOrCreate(
                    ['classroom_group_id' => $classroom_group->id, 'curriculum_subject_id' => $curriculum_subject->id]
                );
            } catch (QueryException $e) {
                Log::error($e->getMessage());
                $status[] = array("ERROR", 'La fila '.$row_num.' no se ha podido procesar');
            }
        }
        return $status;
    }

    public function generateExcelFile($academic_year, $curriculum, $groups, $file_path) {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $i = 1;
        $sheet->setCellValue('A'.strval($i), self::COLUMNS_FORMAT[0]);
        $sheet->setCellValue('B'.strval($i), self::COLUMNS_FORMAT[1]);
        $sheet->setCellValue('C'.strval($i), self::COLUMNS_FORMAT[2]);
        $sheet->setCellValue('D'.strval($i), self::COLUMNS_FORMAT[3]);
        $sheet->setCellValue('E'.strval($i), self::COLUMNS_FORMAT[4]);
        $sheet->setCellValue('F'.strval($i), self::COLUMNS_FORMAT[5]);
        $sheet->setCellValue('G'.strval($i), self::COLUMNS_FORMAT[6]);
        $sheet->setCellValue('H'.strval($i), self::COLUMNS_FORMAT[7]);
        $sheet->setCellValue('I'.strval($i), self::COLUMNS_FORMAT[8]);
        $sheet->setCellValue('J'.strval($i), self::COLUMNS_FORMAT[9]);
        $sheet->setCellValue('K'.strval($i), self::COLUMNS_FORMAT[10]);
        $sheet->setCellValue('L'.strval($i), self::COLUMNS_FORMAT[11]);
        $sheet->setCellValue('M'.strval($i), self::COLUMNS_FORMAT[12]);

        $i++;
        foreach ($groups as $group) {
            $sheet->setCellValue('A'.strval($i), $academic_year->name);
            $sheet->setCellValue('B'.strval($i), $curriculum->code);
            $sheet->setCellValue('C'.strval($i), $curriculum->name);
            $sheet->setCellValue('D'.strval($i), $group->classroomGroup->activity_id);
            $sheet->setCellValue('E'.strval($i), $group->classroomGroup->activity_group);
            $sheet->setCellValue('F'.strval($i), $group->classroomGroup->name);
            $sheet->setCellValue('G'.strval($i), $group->classroomGroup->subject->code);
            $sheet->setCellValue('H'.strval($i), $group->classroomGroup->subject->name);
            $sheet->setCellValue('I'.strval($i), $group->classroomGroup->language);
            $sheet->setCellValue('J'.strval($i), $group->classroomGroup->duration);
            $sheet->setCellValue('K'.strval($i), $group->classroomGroup->capacity);
            $sheet->setCellValue('L'.strval($i), $group->classroomGroup->capacity_left);
            $sheet->setCellValue('M'.strval($i), $group->classroomGroup->comments);
            $i++;
        }
        
        $writer = new Xlsx($spreadsheet);
        $writer->save($file_path);
    }
}