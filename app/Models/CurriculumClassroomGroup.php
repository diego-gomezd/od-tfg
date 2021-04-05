<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurriculumClassroomGroup extends Model
{
    use HasFactory;

    public function classroomGroup()
    {
        return $this->belongsTo(ClassroomGroup::class);
    }
    public function curriculumSubject()
    {
        return $this->belongsTo(CurriculumSubject::class);
    }
}
