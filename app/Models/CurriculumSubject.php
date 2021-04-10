<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurriculumSubject extends Model
{
    use HasFactory;

    protected $fillable = ['academic_year_id', 'curriculum_id', 'subject_id', 'type', 'duration', 'course', 'part_time_course', 'comments'];


    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    public function curriculum()
    {
        return $this->belongsTo(Curriculum::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
