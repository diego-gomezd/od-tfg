<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\UploadedFile;
use App\Models\UploadedFileResult;

class DashboardController extends Controller
{
    public function index()
    {
        $excelFiles = UploadedFile::orderBy('status', 'asc')->orderBy('created_at', 'desc')->get();

        return view('dashboard', [
            'excelFiles' => $excelFiles
        ]);
    }
}
