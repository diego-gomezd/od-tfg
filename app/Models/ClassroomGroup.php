<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassroomGroup extends Model
{
    use HasFactory;

    protected $fillable = ['academic_year_id', 'subject_id', 'name', 'activity_id', 'activity_group', 'duration', 'language', 'capacity', 'capacity_left', 'location'];


    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
