<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedFileResult extends Model
{
    use HasFactory;

    public function uploadedFile()
    {
        return $this->belongsTo(UploadedFile::class);
    }
}
