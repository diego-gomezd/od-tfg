<?php 
namespace App\Models\Combos;

use App\Models\Combos\BasicCombo;


    abstract class Course
    {
        const _1 = ["id" => '1º', "title" => '1º Curso'];
        const _2 = ["id" => '2º', "title" => '2º Curso'];
        const _3 = ["id" => '3º', "title" => '3º Curso'];
        const _4 = ["id" => '4º', "title" => '4º Curso'];

        static function getCombo() {
            return [Course::_1, Course::_2, Course::_3, Course::_4];
        }
    
        static function getCourse($value) {
            return BasicCombo::find($value, Course::getCombo());
        }
    }
