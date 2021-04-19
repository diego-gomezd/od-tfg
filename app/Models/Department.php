<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public static function getAndUpdate($department_code, $department_name)
    {
        $department = Department::firstOrCreate(['code' => trim($department_code)]);

        $mod = false;
        if (empty($department->name) && $department_name != null) {
            $department->name = $department_name;
            $mod = true;
        }
        if ($mod) {
            $department->update();
        }
        return $department;
    }
}
