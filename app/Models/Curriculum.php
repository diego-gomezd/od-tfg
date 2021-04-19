<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Curriculum extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name'];

    public static function getAndUpdate($curriculum_code, $curriculum_name)
    {
        $curriculum = Curriculum::firstOrCreate(['code' => trim($curriculum_code)]);
        $mod = false;
        if (empty($curriculum->name) && $curriculum_name != null) {
            $curriculum->name = $curriculum_name;
            $mod = true;
        }
        if ($mod) {
            $curriculum->update();
        }
        return $curriculum;
    }
}
