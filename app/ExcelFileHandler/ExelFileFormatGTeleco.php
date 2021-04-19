<?php

namespace App\ExcelFileHandler;

use Error;
use Exception;
use App\Models\Subject;
use App\Models\Curriculum;
use App\Models\AcademicYear;
use App\Models\ClassroomGroup;
use App\Models\CurriculumSubject;
use App\Models\UploadedFileResult;
use Illuminate\Support\Facades\Log;
use App\Models\CurriculumClassroomGroup;
use App\ExcelFileHandler\ExcelFileFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\ExcelFileHandler\IExcelFileFormat;

class ExelFileFormatGTeleco extends ExcelFileFormat implements IExcelFileFormat
{
    protected const COLUMNS_FORMAT = array('ANO_ACADEMICO', 'ID_ACTIVIDAD', 'GRUPO_ACTIV', 'NOMBRE_GRUPO_ACTIV', 'ASIGN', 'NOMBRE_ASIGNATURA', 'IDIOMA', 'DURACION', 'CAPAC_GRUPO', 'PLAZA_RESERV_GRUPO', '', '', '', '', '', 'OBSERVACIONES');
    protected const GT_FORMAT = ['name' => 'GTele', 'columns' => self::COLUMNS_FORMAT];

    public function proces_excel($data, UploadedFileResult &$file_result)
    {
        foreach ($data as $row_num => $row) {
            if ($row[0] != null && !empty(trim($row[0]))) {
                $status_value = $this->process_row($row, $row_num + 2);
                if ($status_value != null && count($status_value) > 0) {
                    $file_result->addResult($status_value);
                }
            }
        }
    }

    public function getFormat(): string
    {
        return self::GT_FORMAT['name'];
    }

    public static function build(array $header): ?IExcelFileFormat
    {
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
        if (!$this->validateColumn($academic_year_name, self::COLUMNS_FORMAT[0], $row_num, true, false, 45, null, $status)) {
            $with_error = true;
        }
        $classroom_activity_id = $row[1];
        if (!$this->validateColumn($classroom_activity_id, self::COLUMNS_FORMAT[1], $row_num, false, true, 45, null, $status)) {
            $with_error = true;
        }
        $classroom_code = $row[2];
        if (!$this->validateColumn($classroom_code, self::COLUMNS_FORMAT[2], $row_num, false, true, 45, null, $status)) {
            $with_error = true;
        }
        $classroom_name = $row[3];
        if (!$this->validateColumn($classroom_name, self::COLUMNS_FORMAT[3], $row_num, false, true, 200, null, $status)) {
            $with_error = true;
        }
        $subject_code = $row[4];
        if (!$this->validateColumn($subject_code, self::COLUMNS_FORMAT[4], $row_num, true, false, 15, null, $status)) {
            $with_error = true;
        }
        $subject_name = $row[5];
        if (!$this->validateColumn($subject_name, self::COLUMNS_FORMAT[5], $row_num, false, true, 200, null, $status)) {
            $with_error = true;
        }
        $classroom_language = $row[6];
        if (!$this->validateColumn($classroom_language, self::COLUMNS_FORMAT[6], $row_num, false, false, 5, null, $status)) {
            $with_error = true;
        }
        $subject_duration = $row[7];
        if (!$this->validateColumn($subject_duration, self::COLUMNS_FORMAT[7], $row_num, false, true, 5, null, $status)) {
            $with_error = true;
        }
        $classroom_capacity = $row[8];
        if (!$this->validateColumn($classroom_capacity, self::COLUMNS_FORMAT[8], $row_num, false, false, null, true, $status)) {
            $with_error = true;
        }
        $classroom_num_reservas = $row[9];
        if (!$this->validateColumn($classroom_num_reservas, self::COLUMNS_FORMAT[9], $row_num, false, false, null, true, $status)) {
            $with_error = true;
        }
        $curriculum_code_1 = $row[10];
        if (!$this->validateColumn($curriculum_code_1, self::COLUMNS_FORMAT[10], $row_num, false, false, 15, null, $status)) {
            $with_error = true;
        }
        $curriculum_code_2 = $row[11];
        if (!$this->validateColumn($curriculum_code_2, self::COLUMNS_FORMAT[11], $row_num, false, false, 15, null, $status)) {
            $with_error = true;
        }
        $curriculum_code_3 = $row[12];
        if (!$this->validateColumn($curriculum_code_3, self::COLUMNS_FORMAT[12], $row_num, false, false, 15, null, $status)) {
            $with_error = true;
        }
        $curriculum_code_4 = $row[13];
        if (!$this->validateColumn($curriculum_code_4, self::COLUMNS_FORMAT[13], $row_num, false, false, 15, null, $status)) {
            $with_error = true;
        }
        $curriculum_code_5 = $row[14];
        if (!$this->validateColumn($curriculum_code_5, self::COLUMNS_FORMAT[14], $row_num, false, false, 15, null, $status)) {
            $with_error = true;
        }
        $classroom_comments = $row[15];
        if (!$this->validateColumn($classroom_comments, self::COLUMNS_FORMAT[15], $row_num, false, false, 65535, null, $status)) {
            $with_error = true;
        }

        $classroom_capacity_left = null;
        if ($classroom_capacity != null && $classroom_num_reservas != null) {
            $classroom_capacity_left = $classroom_capacity - $classroom_num_reservas;
        }

        if (!$with_error) {
            try {
                $academic_year = AcademicYear::firstOrCreate(['name' => trim($academic_year_name)]);
                $subject = Subject::getAndUpdate($subject_code, $subject_name, null, null);

                $classroom_group = ClassroomGroup::getAnUpdate(
                    $academic_year->id,
                    $subject->id,
                    $classroom_code,
                    $classroom_name,
                    $classroom_activity_id,
                    $classroom_language,
                    $classroom_capacity,
                    $classroom_capacity_left,
                    $subject_duration,
                    null
                );

                $course = $this->getCourseFromClassgroup($classroom_code);

                if ($curriculum_code_1 != null) {
                    $this->insertSubjectClassgroup($curriculum_code_1, $academic_year, $subject, $course, $subject_duration, $classroom_group);
                    if ($curriculum_code_2 != null) {
                        $this->insertSubjectClassgroup($curriculum_code_1, $academic_year, $subject, $course, $subject_duration, $classroom_group);
                    }
                    if ($curriculum_code_3 != null) {
                        $this->insertSubjectClassgroup($curriculum_code_3, $academic_year, $subject, $course, $subject_duration, $classroom_group);
                    }
                    if ($curriculum_code_4 != null) {
                        $this->insertSubjectClassgroup($curriculum_code_4, $academic_year, $subject, $course, $subject_duration, $classroom_group);
                    }
                    if ($curriculum_code_5 != null) {
                        $this->insertSubjectClassgroup($curriculum_code_5, $academic_year, $subject, $course, $subject_duration, $classroom_group);
                    }
                } else {
                    $curriculum_code = 'G' . substr($subject_code, 0, 2);
                    if ($curriculum_code != null) {
                        $this->insertSubjectClassgroup($curriculum_code, $academic_year, $subject, $course, $subject_duration, $classroom_group);
                    }
                }
            } catch (Exception | Error $e) {
                Log::error($e->getMessage());
                $status[] = array('status' => 'ERROR', 'msg' => 'La fila ' . $row_num . ' no se ha podido procesar');
            }
        }
        return $status;
    }

    private function insertSubjectClassgroup($curriculum_code, $academic_year, $subject, $course, $subject_duration, $classroom_group)
    {
        $curriculum = Curriculum::where('code', $curriculum_code)->first();

        if ($curriculum != null) {
            $curriculum_subject = CurriculumSubject::getAndUpdate(
                $academic_year->id,
                $curriculum->id,
                $subject->id,
                $course,
                $subject_duration,
                null,
                null
            );
            CurriculumClassroomGroup::firstOrCreate(
                ['classroom_group_id' => $classroom_group->id, 'curriculum_subject_id' => $curriculum_subject->id]
            );
        }
    }

    public function generateExcelFile($academic_year, $curriculum, $groups, $file_path)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $i = 1;
        $sheet->setCellValue('A' . strval($i), self::COLUMNS_FORMAT[0]);
        $sheet->setCellValue('B' . strval($i), self::COLUMNS_FORMAT[1]);
        $sheet->setCellValue('C' . strval($i), self::COLUMNS_FORMAT[2]);
        $sheet->setCellValue('D' . strval($i), self::COLUMNS_FORMAT[3]);
        $sheet->setCellValue('E' . strval($i), self::COLUMNS_FORMAT[4]);
        $sheet->setCellValue('F' . strval($i), self::COLUMNS_FORMAT[5]);
        $sheet->setCellValue('G' . strval($i), self::COLUMNS_FORMAT[6]);
        $sheet->setCellValue('H' . strval($i), self::COLUMNS_FORMAT[7]);
        $sheet->setCellValue('I' . strval($i), self::COLUMNS_FORMAT[8]);
        $sheet->setCellValue('J' . strval($i), self::COLUMNS_FORMAT[9]);
        $sheet->setCellValue('K' . strval($i), self::COLUMNS_FORMAT[10]);
        $sheet->setCellValue('L' . strval($i), self::COLUMNS_FORMAT[11]);
        $sheet->setCellValue('M' . strval($i), self::COLUMNS_FORMAT[12]);

        $i++;
        foreach ($groups as $group) {
            $sheet->setCellValue('A' . strval($i), $academic_year->name);
            $sheet->setCellValue('B' . strval($i), $curriculum->code);
            $sheet->setCellValue('C' . strval($i), $curriculum->name);
            $sheet->setCellValue('D' . strval($i), $group->classroomGroup->activity_id);
            $sheet->setCellValue('E' . strval($i), $group->classroomGroup->activity_group);
            $sheet->setCellValue('F' . strval($i), $group->classroomGroup->name);
            $sheet->setCellValue('G' . strval($i), $group->classroomGroup->subject->code);
            $sheet->setCellValue('H' . strval($i), $group->classroomGroup->subject->name);
            $sheet->setCellValue('I' . strval($i), $group->classroomGroup->language);
            $sheet->setCellValue('J' . strval($i), $group->classroomGroup->duration);
            $sheet->setCellValue('K' . strval($i), $group->classroomGroup->capacity);
            $sheet->setCellValue('L' . strval($i), $group->classroomGroup->capacity_left);
            $sheet->setCellValue('M' . strval($i), $group->classroomGroup->comments);
            $i++;
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($file_path);
    }
}
