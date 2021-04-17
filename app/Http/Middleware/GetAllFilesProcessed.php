<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GetAllFilesProcessed
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
     
        $response = $next($request);
        $fileInProgress = UploadedFile::where('status', 'IN_PROGRESS')->first();
        
        if ($fileInProgress != null) {
            $num = UploadedFile::whereIn('status', ['UPLOADED', 'IN_PROGRESS'])->count();
            $request->session()->flash('file_status', ['message' => 'Se procesando el  fichero '.$fileInProgress->file_name.'...', 'pending' => $num]); 
        }
        return $response;
    }
}
