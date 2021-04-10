<?php

namespace App\Jobs;

use App\Models\UploadedFile;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

use PhpOffice\PhpSpreadsheet\IOFactory;

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
        Log::debug('ProcessUploadedFile handle');
        //Buscar fichero en el storage
        $file_path = $this->uploadedFile->full_path;
        Log::debug($file_path);

        $exists = Storage::disk('sftp')->exists($file_path);
        if (Storage::disk('sftp')->exists($file_path)) 
        {
            try {
            Log::debug('File exists');
            $file = Storage::disk('sftp')->get($file_path);

            $file.
            
            //Abrir EXCEL
         //   $spreadsheet = IOFactory::load($file);
            
         ///   print_r($spreadsheet);

            //DETERMINAR FORMAT
            //PROCESAR FILAS
            
            //Modificar el registro
           // $uploadedFile->status = 'FINISHED';
           // $uploadedFile->save();
           $this->fail();
        } catch (Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
            Log::debug($e->getMessage());
            $this->fail($e);
        }
        }
        else {
            Log::debug('Doesnt exists');
        }
    }
}
