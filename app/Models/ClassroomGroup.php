<?php

namespace App\Models;

use App\Models\Combos\Duration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClassroomGroup extends Model
{
    use HasFactory;

    protected $fillable = ['academic_year_id', 'subject_id', 'name', 'activity_id', 'activity_group', 'duration', 'language', 'capacity', 'capacity_left', 'location'];

    public $offered;

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function isCapacityRemainingMoreThan($percent) {
        $remainig = 0;
        if ($this->capacity != null) {
            if ($this->capacity_left != null) {
                $remainig = ($this->capacity_left / $this->capacity) * 100;
            } else {
                $remainig = 100;
            }

        }
        return $remainig > $percent;
    }

    public function durationTitle() 
    {
        return $this->duration != null ? Duration::getDuration($this->duration)['title'] : null;
    }
}
