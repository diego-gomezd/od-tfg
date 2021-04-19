<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Jobs\ProcessUploadedFile;
use App\Models\UploadedFileResult;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UploadFilesRequest;

class UploadedFileController extends Controller
{
    public function index()
    {
        $excelFiles = UploadedFile::orderBy('updated_at', 'desc')->paginate(10);

        foreach ($excelFiles as $file) {
            $file->uploaded_file_result = UploadedFileResult::where('uploaded_file_id', $file->id)->orderBy('id', 'desc')->first();
        }

        return view('uploadedFiles.index', [
            'excelFiles' => $excelFiles,
            'academic_years' => AcademicYear::all(),
            'formats' => [
                '' => 'Deteccion automatica',
                'OD' =>  'Oferta Docente',
                'GD' =>  'Grupos ofertados',
                'GTeleco' =>  'Grupos (Teleco)',
                'OTrans' =>  'Oferta Transversales',
            ],
        ]);
    }

    public function store(UploadFilesRequest $request)
    {

        $format = $request->input('format');
        $academic_year_id = $request->input('academic_year_id');

        foreach ($request->file("excelFiles") as $excelFile) {
            //Guardar fichero en SFTP
            $path = $excelFile->store('uploads');
            if ($path) {
                //Almacenar registro en BBDD
                $uploadedFile = new UploadedFile;
                $uploadedFile->file_name = $excelFile->getClientOriginalName();
                $uploadedFile->full_path = $path;
                $uploadedFile->file_format = $format;
                $uploadedFile->academic_year_id = $academic_year_id;
                $uploadedFile->status = 'UPLOADED';
                $uploadedFile->save();

                //Lanzar tarea para procesarlo
                ProcessUploadedFile::dispatch($uploadedFile);
            }
        }
        return redirect()->route('uploadedFiles.index')->with('success', 'Fichero(s) almacenado(s). Se procesaran automáticamente');
    }

    public function show(UploadedFile $uploadedFile)
    {
        $uploadedFileResult = UploadedFileResult::where('uploaded_file_id', $uploadedFile->id)->orderBy('id', 'desc')->first();

        return view('uploadedFiles.show', [
            'uploadedFile' => $uploadedFile,
            'uploadedFileResult' => $uploadedFileResult,
        ]);
    }

    public function download(Request $request)
    {
        $uploadedFile = UploadedFile::find($request->uploaded_file_id);

        //Buscar fichero en el storage
        $file_path = $uploadedFile->full_path;
        $exists = Storage::exists($file_path);
        if ($exists) {
            return Storage::download($file_path, $uploadedFile->file_name);
        }
        return redirect()->route('uploadedFiles.show', ['uploadedFile' => $uploadedFile])->with('error', 'No se ha podido descargar el fichero');
    }

    public function process(Request $request)
    {
        $uploadedFile = UploadedFile::find($request->uploaded_file_id);
        $uploadedFile->status = 'UPLOADED';
        $uploadedFile->file_format = null;
        $uploadedFile->update();

        ProcessUploadedFile::dispatchAfterResponse($uploadedFile);

        return redirect()->route('uploadedFiles.index')->with('success', 'Fichero añadido a la colar para ser reprocesado.');
    }
}
