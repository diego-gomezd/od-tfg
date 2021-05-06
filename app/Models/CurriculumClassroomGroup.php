<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurriculumClassroomGroup extends Model
{
    use HasFactory;

    protected $fillable = ['classroom_group_id', 'curriculum_subject_id', 'creation_type'];


    public function classroomGroup()
    {
        return $this->belongsTo(ClassroomGroup::class);
    }
    public function curriculumSubject()
    {
        return $this->belongsTo(CurriculumSubject::class);
    }
}
