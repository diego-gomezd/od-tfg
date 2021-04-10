<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use App\Models\UploadedFileResult;
use App\Models\AcademicYear;
use App\Models\Department;
use App\Models\Curriculum;
use App\Models\Subject;
use App\Models\CurriculumSubject;
use App\Models\ClassroomGroup;
use App\Models\CurriculumClassroomGroup;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UploadFilesRequest;
use App\Jobs\ProcessUploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\QueryException;

use PhpOffice\PhpSpreadsheet\IOFactory;

class UploadedFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $excelFiles = UploadedFile::orderBy('status', 'asc')->orderBy('created_at', 'desc')->get();

        return view('dashboard', [
            'excelFiles' => $excelFiles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\UploadFilesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UploadFilesRequest $request)
    {
        foreach ($request->file("excelFiles") as $excelFile) {
            //Guardar fichero en SFTP
            $path = $excelFile->store('uploads', 'sftp');
            if ($path)
            {
                //Almacenar registro en BBDD
                $uploadedFile = new UploadedFile;
                $uploadedFile->file_name = $excelFile->getClientOriginalName();
                $uploadedFile->full_path = $path;
                $uploadedFile->status = 'UPLOADED';
                $uploadedFile->save();
                
                //Lanzar tarea para procesarlo
                //ProcessUploadedFile::dispatch($uploadedFile);
                $this->launchProcessUploadedFile($uploadedFile);
            }
        }
    }

    private function launchProcessUploadedFile(UploadedFile $uploaded_file)
    {
        //Buscar fichero en el storage
        $file_path = $uploaded_file->full_path;
        $exists = Storage::disk('sftp')->exists($file_path);
        if ($exists)
        {
            try {
                Log::debug('File exists');
                $file_content = Storage::disk('sftp')->get($file_path);

                $temp_file = tmpfile();
                fwrite($temp_file, $file_content);
                $file = stream_get_meta_data($temp_file)['uri']; 
        
                //Abrir EXCEL
                $spreadsheet = IOFactory::load($file);
                $sheet = $spreadsheet->getSheet(0);
                $data = $sheet->toArray(null, true, false, false);
                fclose($temp_file);

                //Procesar filas
                $result_file = new UploadedFileResult();
                $result_file->uploaded_file_id = $uploaded_file->id;
                $this->proces_excel($data, $result_file);
                $result_file->save();
                
                //Modificar el registro
                $uploaded_file->status = 'FINISHED';
                $uploaded_file->save();
            } catch (Exception $e) {
                Log::debug($e->getMessage());
                //  $this->fail($e);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\UploadedFile  $uploadedFile
     * @return \Illuminate\Http\Response
     */
    public function show(UploadedFile $uploadedFile)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UploadedFile  $uploadedFile
     * @return \Illuminate\Http\Response
     */
    public function edit(UploadedFile $uploadedFile)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UploadedFile  $uploadedFile
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, UploadedFile $uploadedFile)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UploadedFile  $uploadedFile
     * @return \Illuminate\Http\Response
     */
    public function destroy(UploadedFile $uploadedFile)
    {
        //
    }


    const OD_COLUMNS_FORMAT = array('AÑO','DPTO','NOM-DPTO','PLAN','NOM-PLAN','ASIG','NOM-ASIG','CRED','ESTADO','DURAC','TIPO-ASIG','OBSERVACIONES');
    const GD_COLUMNS_FORMAT = array('AÑO','PLAN','NOM_PLAN','ID-ACTIVIDAD','GRUPO_ACTIV','NOMBRE_GRUPO','ASIG','NOM_ASIGNATURA','IDIOMA','DURACIÓN','CAP','CAP_RES','OBSERVACIONES');

    private function proces_excel(array $data, UploadedFileResult &$file_result)
    {
        $format = $this->determine_excel_format($data[0]);
        $this->proces_excel_format(array_slice($data, 1), $format, $file_result);
        if ($file_result->result_description != null && count($file_result->result_description) > 0)
        {
            $with_error = array_key_exists('ERROR', $file_result->result_description);
            $file_result->result_status = $with_error ? 'ERROR' : 'WARNING';
        } else {
            $file_result->result_status = 'OK';
        }
    }

    private function determine_excel_format(array $header)
    {
        $format = $this->check_columns_format($header, self::OD_COLUMNS_FORMAT, 'OD');
        if ($format == null) 
        {
            $format = $this->check_columns_format($header, self::GD_COLUMNS_FORMAT, 'GD');
        }
        return $format;
    }

    private function check_columns_format(array $array, array $header, string $format)
    {
        $i = 0;
        while ($format != null && $i < count($array) && $i < count($header))
        {
            if ($array[$i] != $header[$i]) 
            {
                $format = null;
            }
            $i++;
        }
        return $format;
    }

    private function proces_excel_format(array $array, $format, UploadedFileResult &$file_result)
    {
        foreach ($array as $row_num => $row)
        {
           
            if ($row[0] != null)
            {
                $status_value = null;
                switch ($format) {
                    case 'OD':
                        $status_value = $this->process_row_OD_format($row, $row_num + 2);
                        break;
                    case 'GD':
                        $status_value = $this->process_row_GD_format($row, $row_num + 2);
                        break;
                }

                if ($status_value != null && count($status_value) > 0) 
                {
                    $file_result->addResult($status_value);
                }             
            }       
        }
    }

    private function process_row_OD_format($row, $row_num)
    {
        $status = array();
        $with_error = false;

        $academic_year_name = $row[0];
        if (!$this->validateColumn($academic_year_name, self::OD_COLUMNS_FORMAT[0], $row_num, true, false, 45, null, $status))
        {
            $with_error = true;
        }

        $department_code = $row[1];
        if (!$this->validateColumn($department_code, self::OD_COLUMNS_FORMAT[1], $row_num, true, false, 15, null, $status))
        {
            $with_error = true;
        }

        $department_name = $row[2];
        if (!$this->validateColumn($department_name, self::OD_COLUMNS_FORMAT[2], $row_num, false, true, 100, null, $status))
        {
            $with_error = true;
        }
       

        $curriculum_code = $row[3];
        if (!$this->validateColumn($curriculum_code, self::OD_COLUMNS_FORMAT[3], $row_num, true, false, 15, null, $status))
        {
            $with_error = true;
        }
        
        $curriculum_name = $row[4];
        if (!$this->validateColumn($curriculum_name, self::OD_COLUMNS_FORMAT[4], $row_num, false, true, 100, null, $status))
        {
            $with_error = true;
        }

        $subject_code = $row[5];
        if (!$this->validateColumn($subject_code, self::OD_COLUMNS_FORMAT[5], $row_num, true, false, 15, null, $status))
        {
            $with_error = true;
        }

        $subject_name = $row[6];
        if (!$this->validateColumn($subject_name, self::OD_COLUMNS_FORMAT[6], $row_num, false, true, 200, null, $status))
        {
            $with_error = true;
        }

        $subject_ects = $row[7];
        if (!$this->validateColumn($subject_ects, self::OD_COLUMNS_FORMAT[7], $row_num, false, true, null, true, $status))
        {
            $with_error = true;
        }

        $subject_duration = $row[9];
        if (!$this->validateColumn($subject_duration, self::OD_COLUMNS_FORMAT[9], $row_num, false, true, 5, null, $status))
        {
            $with_error = true;
        }

        $subject_type = $row[10];
        if (!$this->validateColumn($subject_type, self::OD_COLUMNS_FORMAT[10], $row_num, false, true, 5, null, $status))
        {
            $with_error = true;
        }

        $subject_comments = $row[11];
        if (!$this->validateColumn($subject_comments, self::OD_COLUMNS_FORMAT[11], $row_num, false, false, 65535, null, $status))
        {
            $with_error = true;
        }

        
        if (!$with_error)
        {
            try {
                $academic_year = AcademicYear::firstOrCreate(['name' => trim($academic_year_name)]);

                $department = Department::firstOrCreate(
                    ['code' => trim($department_code)], ['name' => trim($department_name)]
                );
                $curriculum = Curriculum::firstOrCreate(
                    ['code' => trim($curriculum_code)], ['name' => trim($curriculum_name)]
                );
                $subject = Subject::firstOrCreate(
                    ['code' => trim($subject_code)],
                    ['name' => trim($subject_name), 'ects' => $subject_ects, 'department_id' => $department->id]
                );
        
                $curriculum_subject = CurriculumSubject::firstOrCreate(
                    ['curriculum_id' => $curriculum->id, 'academic_year_id' => $academic_year->id, 'subject_id' => $subject->id],
                    ['duration' => trim($subject_duration), 'type' => trim($subject_type), 'comments' => trim($subject_comments)]
                );
            } catch (QueryException $e) {
                Log::error($e->getMessage());
                $status[] = array("ERROR", 'La fila '.$row_num.' no se ha podido procesar');
            }
        }
        return $status;
    }

    private function process_row_GD_format($row, $row_num)
    {
        $status = array();
        $with_error = false;

        $academic_year_name = $row[0];
        if (!$this->validateColumn($academic_year_name, self::GD_COLUMNS_FORMAT[0], $row_num, true, false, 45, null, $status))
        {
            $with_error = true;
        }

        $curriculum_code = $row[1];
        if (!$this->validateColumn($curriculum_code, self::GD_COLUMNS_FORMAT[1], $row_num, true, false, 15, null, $status))
        {
            $with_error = true;
        }

        $curriculum_name = $row[2];
        if (!$this->validateColumn($curriculum_name, self::GD_COLUMNS_FORMAT[2], $row_num, false, true, 100, null, $status))
        {
            $with_error = true;
        }

        $classroom_activity_id = $row[3];
        if (!$this->validateColumn($classroom_activity_id, self::GD_COLUMNS_FORMAT[3], $row_num, false, true, 45, null, $status))
        {
            $with_error = true;
        }

        $classroom_code = $row[4];
        if (!$this->validateColumn($classroom_code, self::GD_COLUMNS_FORMAT[4], $row_num, false, true, 45, null, $status))
        {
            $with_error = true;
        }

        $classroom_name = $row[5];
        if (!$this->validateColumn($classroom_name, self::GD_COLUMNS_FORMAT[5], $row_num, false, true, 45, null, $status))
        {
            $with_error = true;
        }

        $subject_code = $row[6];
        if (!$this->validateColumn($subject_code, self::GD_COLUMNS_FORMAT[6], $row_num, true, false, 15, null, $status))
        {
            $with_error = true;
        }

        $subject_name = $row[7];
        if (!$this->validateColumn($subject_name, self::GD_COLUMNS_FORMAT[7], $row_num, false, true, 200, null, $status))
        {
            $with_error = true;
        }

        $classroom_language = $row[8];
        if (!$this->validateColumn($classroom_language, self::GD_COLUMNS_FORMAT[8], $row_num, false, true, 5, null, $status))
        {
            $with_error = true;
        }

        $subject_duration = $row[9];
        if (!$this->validateColumn($subject_duration, self::GD_COLUMNS_FORMAT[9], $row_num, false, true, 5, null, $status))
        {
            $with_error = true;
        }

        $classroom_capacity = $row[10];
        if (!$this->validateColumn($classroom_capacity, self::GD_COLUMNS_FORMAT[10], $row_num, false, true, null, true, $status))
        {
            $with_error = true;
        }

        $classroom_capacity_left = $row[11];
        if (!$this->validateColumn($classroom_capacity_left, self::GD_COLUMNS_FORMAT[11], $row_num, false, true, null, true, $status))
        {
            $with_error = true;
        }

        $classroom_comments = $row[12];
        if (!$this->validateColumn($classroom_comments, self::GD_COLUMNS_FORMAT[12], $row_num, false, false, 65535, null, $status))
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

    private function validateEmptyColumn($value, $mandatory, $errorMsg, &$status)
    {
        $validate = true; 
        if (empty(trim($value)))
        {
            $status[] = array('status' => $mandatory ? 'ERROR' : 'WARNING', 'msg' => $errorMsg);
            if ($mandatory)
            {
                $validate = false; 
            }
        }
        return $validate;
    }

    private function validateLenghtColumn($value, $max_lenght, $errorMsg, &$status)
    {
        $validate = true; 
        if (!empty(trim($value)) && strlen(trim($value)) > $max_lenght)
        {
            $status[] = array('status' => 'ERROR', 'msg' => $errorMsg);
            $validate = false; 
        }
        return $validate;
    }
    
    private function validateColumn($value, $header_name, $row_num, $mandatory, $warning_empty, $max_lenght, $only_integer, &$status)
    {
        $validate = true;
        if ($mandatory || $warning_empty) {
            $validate = $this->validateEmptyColumn($value, $mandatory, 'Columna '.$header_name.' vacia en fila '.$row_num, $status);
        }

        if ($only_integer && !(is_numeric($value) && ctype_digit($value) && intval($value) > 0))
        {
            $status[] = array('status' => 'ERROR', 'msg' => 'El valor de la celda ['.$row_num.', '.$header_name.'] debe ser un número entero.');
            $validate = false; 
        }

        if ($max_lenght != null && $max_lenght > 0)
        {
            $validate_lenght = $this->validateLenghtColumn($value, $max_lenght, 'El tamaño de la celda ['.$row_num.', '.$header_name.'] supera el máximo permitido ('.$max_lenght.').', $status);
            if (!$validate_lenght) {
                $validate = false;
            }
        }
        return $validate;
    }
}