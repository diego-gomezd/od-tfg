<?php

namespace App\ExcelFileHandler;

use Error;
use Exception;
use App\Models\Subject;
use App\Models\Curriculum;
use App\Models\Department;
use App\Models\AcademicYear;
use App\Models\CurriculumSubject;
use App\Models\UploadedFileResult;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\ExcelFileHandler\ExcelFileFormat;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\ExcelFileHandler\IExcelFileFormat;
use App\Models\Combos\CreationType;

class ExelFileFormatOD extends ExcelFileFormat implements IExcelFileFormat
{
    protected const COLUMNS_FORMAT = array('AÑO', 'DPTO', 'NOM-DPTO', 'PLAN', 'NOM-PLAN', 'ASIG', 'NOM-ASIG', 'CRED', 'ESTADO', 'DURAC', 'TIPO-ASIG', 'OBSERVACIONES');
    protected const OD_FORMAT = ['name' => 'OD', 'columns' => self::COLUMNS_FORMAT];

    public function proces_excel($data, UploadedFileResult &$file_result)
    {
        foreach ($data as $row_num => $row) {
            if ($row[0] != null && !empty($row[0])) {
                $status_value = $this->process_row($row, $row_num + 2);
                if ($status_value != null && count($status_value) > 0) {
                    $file_result->addResult($status_value);
                }
            }
        }
    }

    public function getFormat(): string
    {
        return self::OD_FORMAT['name'];
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

        $department_code = $row[1];
        if (!$this->validateColumn($department_code, self::COLUMNS_FORMAT[1], $row_num, true, false, 15, null, $status)) {
            $with_error = true;
        }

        $department_name = $row[2];
        if (!$this->validateColumn($department_name, self::COLUMNS_FORMAT[2], $row_num, false, true, 100, null, $status)) {
            $with_error = true;
        }
        $curriculum_code = $row[3];
        if (!$this->validateColumn($curriculum_code, self::COLUMNS_FORMAT[3], $row_num, true, false, 15, null, $status)) {
            $with_error = true;
        }

        $curriculum_name = $row[4];
        if (!$this->validateColumn($curriculum_name, self::COLUMNS_FORMAT[4], $row_num, false, true, 100, null, $status)) {
            $with_error = true;
        }

        $subject_code = $row[5];
        if (!$this->validateColumn($subject_code, self::COLUMNS_FORMAT[5], $row_num, true, false, 15, null, $status)) {
            $with_error = true;
        }

        $subject_name = $row[6];
        if (!$this->validateColumn($subject_name, self::COLUMNS_FORMAT[6], $row_num, false, true, 200, null, $status)) {
            $with_error = true;
        }

        $subject_ects = $row[7];
        if (!$this->validateColumn($subject_ects, self::COLUMNS_FORMAT[7], $row_num, false, true, null, true, $status)) {
            $with_error = true;
        }

        $subject_duration = $row[9];
        if (!$this->validateColumn($subject_duration, self::COLUMNS_FORMAT[9], $row_num, false, true, 5, null, $status)) {
            $with_error = true;
        }

        $subject_type = $row[10];
        if (!$this->validateColumn($subject_type, self::COLUMNS_FORMAT[10], $row_num, false, true, 5, null, $status)) {
            $with_error = true;
        }

        $subject_comments = $row[11];
        if (!$this->validateColumn($subject_comments, self::COLUMNS_FORMAT[11], $row_num, false, false, 65535, null, $status)) {
            $with_error = true;
        }

        if (!$with_error) {
            try {
                $academic_year = AcademicYear::firstOrCreate(['name' => trim($academic_year_name)]);
                $department = Department::getAndUpdate($department_code, $department_name);
                $curriculum = Curriculum::getAndUpdate($curriculum_code, $curriculum_name);
                $subject = Subject::getAndUpdate($subject_code, $subject_name, $department->id, $subject_ects);

                CurriculumSubject::getAndUpdate(
                    $academic_year->id,
                    $curriculum->id,
                    $subject->id,
                    null,
                    $subject_duration,
                    $subject_type,
                    $subject_comments
                );
            } catch (Exception | Error $e) {
                Log::error($e->getMessage());
                $status[] = array('status' => 'ERROR', 'msg' => 'La fila ' . $row_num . ' no se ha podido procesar');
            }
        }
        return $status;
    }

    public function generateExcelFile($academic_year, $curriculum, $subjects, $file_path)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
        ->setCreator(Auth::user()->name)
        ->setTitle($curriculum->code.' - '.$curriculum->name.' / Oferta Docente '.$academic_year->name);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('OD '.$curriculum->code);

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

        $sheet->getStyle('A1:L1')->applyFromArray(self::title_style);

        $i++;
        foreach ($subjects as $subject) {
            $sheet->setCellValue('A' . strval($i), $academic_year->name);
            $sheet->setCellValue('B' . strval($i), $subject->subject->department != null ? $subject->subject->department->code : '');
            $sheet->setCellValue('C' . strval($i), $subject->subject->department != null ? $subject->subject->department->name : '');
            $sheet->setCellValue('D' . strval($i), $curriculum->code);
            $sheet->setCellValue('E' . strval($i), $curriculum->name);
            $sheet->setCellValue('F' . strval($i), $subject->subject->code);
            $sheet->setCellValue('G' . strval($i), $subject->subject->name);
            $sheet->setCellValue('H' . strval($i), $subject->subject->ects);
            $sheet->setCellValue('I' . strval($i), 'V');
            $sheet->setCellValue('J' . strval($i), $subject->duration);
            $sheet->setCellValue('K' . strval($i), $subject->type);
            $sheet->setCellValue('L' . strval($i), $subject->comments);

            if ($subject->creation_type == null || $subject->creation_type == CreationType::MANUAL) {
                $sheet->getStyle('A'.$i.':L'.$i)->applyFromArray(self::manual_data_style);    
            } else {
                $sheet->getStyle('A'.$i.':L'.$i)->applyFromArray(self::normal_style);
            }

            $i++;
        }


        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);



        $writer = new Xlsx($spreadsheet);
        $writer->save($file_path);
    }
}
