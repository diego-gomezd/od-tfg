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

    protected $casts = [
        'result_description' => 'array'
    ];

    public function addResult(array $row)
    {
        if ($this->result_description == null)
        {
            $this->result_description = array();
        }
        $this->result_description = array_merge($this->result_description, $row);
    }

    public function array_descriptions() {
        $array = null;
        if ($this->result_description != null && !empty($this->result_description)) {
            if (array_key_exists('status', $this->result_description)) {
                $array = array();
                $array[] = $this->result_description;
            } else {
                $array = $this->result_description; 
            }
        }
        return $array;
    }
}
