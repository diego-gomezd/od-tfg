<?php

namespace App\Models;

use App\Models\Combos\Duration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use function Ramsey\Uuid\v1;

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

    public function isCapacityRemainingMoreThan($percent)
    {
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

    public static function getAnUpdate($academic_year_id, $subject_id, $classroom_code, $classroom_name, $classroom_activity_id, $classroom_language, $classroom_capacity, $classroom_capacity_left, $subject_duration, $subject_location)
    {
        $classroom_group = ClassroomGroup::firstOrCreate(['academic_year_id' => $academic_year_id, 'subject_id' => $subject_id, 'activity_group' => trim($classroom_code)]);

        $mod = false;

        if (empty($classroom_group->name) && !empty($classroom_name)) {
            $classroom_group->name = trim($classroom_name);
            $mod = true;
        }
        if (empty($classroom_group->activity_id) && !empty($classroom_activity_id)) {
            $classroom_group->activity_id = trim($classroom_activity_id);
            $mod = true;
        }
        if (empty($classroom_group->language) && !empty($classroom_language)) {
            $classroom_group->language = trim($classroom_language);
            $mod = true;
        }
        if (empty($classroom_group->language) && !empty($classroom_language)) {
            $classroom_group->language = trim($classroom_language);
            $mod = true;
        }
        if (empty($classroom_group->capacity) && $classroom_capacity != null) {
            $classroom_group->capacity = $classroom_capacity;
            $mod = true;
        }
        if (empty($classroom_group->capacity_left) && $classroom_capacity_left != null) {
            $classroom_group->capacity_left = $classroom_capacity_left;
            $mod = true;
        }
        if (empty($classroom_group->duration) && !empty($subject_duration)) {
            $classroom_group->duration = trim($subject_duration);
            $mod = true;
        }
        if (empty($classroom_group->location) && !empty($subject_location)) {
            $classroom_group->location = trim($subject_location);
            $mod = true;
        }
        if ($mod) {
            $classroom_group->update();
        }
        return $classroom_group;
    }
}
