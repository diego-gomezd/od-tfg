<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassroomGroup extends Model
{
    use HasFactory;

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
