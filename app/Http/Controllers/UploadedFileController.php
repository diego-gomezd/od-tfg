<?php

namespace App\Http\Controllers;

use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Jobs\ProcessUploadedFile;
use Illuminate\Routing\Controller;
use App\Http\Requests\UploadFilesRequest;

class UploadedFileController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $excelFiles = UploadedFile::orderBy('status', 'asc')->orderBy('created_at', 'desc')->paginate(10);

        return view('uploadedFiles.index', [
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
                ProcessUploadedFile::dispatch($uploadedFile);
              //  ProcessUploadedFile::dispatchAfterResponse($uploadedFile);
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


}