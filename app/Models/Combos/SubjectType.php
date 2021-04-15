<?php 
namespace App\Models\Combos;

abstract class SubjectType extends BasicCombo
{
    const TypeO = ["id" => 'O', "title" => 'Optativa'];
    const TypeT = ["id" => 'T', "title" => 'Transversal'];
    const TypeB = ["id" => 'B', "title" => 'Básica'];
    const TypeP = ["id" => 'P', "title" => 'Prácticas'];

    static function getCombo() {
        return [SubjectType::TypeB, SubjectType::TypeO, SubjectType::TypeT, SubjectType::TypeP];
    }

    static function getType($value) {
        return SubjectType::find($value, SubjectType::getCombo());
    }
}