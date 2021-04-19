<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'department_id', 'branch_id', 'english_name', 'comments', 'ects' ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public static function getAndUpdate($subject_code, $subject_name, $department_id, $subject_ects)
    {
        $subject = Subject::firstOrCreate(['code' => trim($subject_code)]);

        $mod = false;
        if (empty($subject->name) && $subject_name != null) {
            $subject->name = $subject_name;
            $mod = true;
        }
        if (empty($subject->department_id) && $department_id != null) {
            $subject->department_id = $department_id;
            $mod = true;
        }
        if (empty($subject->ects) && $subject_ects != null) {
            $subject->ects = $subject_ects;
            $mod = true;
        }

        if ($mod) {
            $subject->update();
        }
        return $subject;
    }
}
