<?php

namespace App\Models;

use App\Models\Subject;
use App\Models\Curriculum;
use App\Models\AcademicYear;
use App\Models\Combos\Course;
use App\Models\Combos\Duration;
use App\Models\Combos\SubjectType;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CurriculumSubject extends Model
{
    use HasFactory;

    protected $fillable = ['academic_year_id', 'curriculum_id', 'subject_id', 'type', 'duration', 'course', 'part_time_course', 'comments'];

    public $num_groups;

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

    public function typeTitle() 
    {
        return $this->type != null ? SubjectType::getType($this->type)['title'] : null;
    }
    public function durationTitle() 
    {
        return $this->duration != null ? Duration::getDuration($this->duration)['title'] : null;
    }
    public function courseTitle() 
    {
        return $this->course != null ? Course::getCourse($this->course)['title'] : null;
    }
}