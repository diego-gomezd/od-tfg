<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\UploadedFile;
use Illuminate\Http\Request;

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
            $num = UploadedFile::where('status', 'UPLOADED')->count();
        }
        $request->session()->flash('file_status', ['message' => 'Se estan procesando ficheros...', 'pending' => 15]); 
   
        return $response;
    }
}
