<?php

namespace App\Jobs;

use Exception;

use App\Models\UploadedFile;
use Illuminate\Bus\Queueable;
use App\Models\UploadedFileResult;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

use Illuminate\Queue\InteractsWithQueue;
use App\ExcelFileHandler\ExelFileFormatGD;
use App\ExcelFileHandler\ExelFileFormatOD;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessUploadedFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $uploadedFile;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UploadedFile $uploadedFile)
    {
        $this->uploadedFile = $uploadedFile;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //Marcamo que estamos procesando el fichero
        $this->uploadedFile->status = 'IN_PROGRESS';
        $this->uploadedFile->save();

        $result_file = new UploadedFileResult();
        $result_file->uploaded_file_id = $this->uploadedFile->id;

        //Buscar fichero en el storage
        $file_path = $this->uploadedFile->full_path;
        $exists = Storage::exists($file_path);
        if ($exists)
        {
            try {
                $file_content = Storage::get($file_path);

                $temp_file = tmpfile();
                fwrite($temp_file, $file_content);
                $file = stream_get_meta_data($temp_file)['uri']; 
        
                //Abrimos el excel
                $spreadsheet = IOFactory::load($file);
                $sheet = $spreadsheet->getSheet(0);
                $data = $sheet->toArray(null, true, false, false);
                fclose($temp_file);

                //Procesar filas
                $format = $this->proces_excel($data, $result_file);
                $this->uploadedFile->file_format = $format;

            } catch (Exception $e) {
                Log::debug($e->getMessage());
                //Añadimos error al resultado
                $result_file->addResult(array("ERROR", 'No se ha podido procesar el fichero'));
                $result_file->result_status = 'ERROR';
            }
        } else {
            //Añadir al resultado que no se ha encontrado el fichero
            $result_file->addResult(array("ERROR", 'No se encuentra el fichero'));
            $result_file->result_status = 'ERROR';
        }
        //Guardamos el resultado del proceso
        $result_file->save();

         //Modificar el registro del fichero
        $this->uploadedFile->status = 'FINISHED';
        $this->uploadedFile->update();
    }

    private function proces_excel(array $data, UploadedFileResult &$file_result)
    {
        $excelFileFormat = $this->determine_excel_format($data[0]);
        $excelFileFormat->proces_excel(array_slice($data, 1), $file_result);

        if ($file_result->result_description != null && count($file_result->result_description) > 0)
        {
            $with_error = array_key_exists('ERROR', $file_result->result_description);
            $file_result->result_status = $with_error ? 'ERROR' : 'WARNING';
        } else {
            $file_result->result_status = 'OK';
        }

        return $excelFileFormat->getFormat();
    }

    private function determine_excel_format(array $header) {
        $excelFileFormat = ExelFileFormatOD::build($header);
        if ($excelFileFormat == null) {
            $excelFileFormat = ExelFileFormatGD::build($header);
        }
        return $excelFileFormat;
    }
}
