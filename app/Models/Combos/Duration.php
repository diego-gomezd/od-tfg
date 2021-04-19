<?php 
namespace App\Models\Combos;


    abstract class Duration extends BasicCombo
    {
        const C1 = ["id" => 'C1', "title" => '1º Cuatrimestre'];
        const C2 = ["id" => 'C2', "title" => '2º Cuatrimestre'];
        const A = ["id" => 'A', "title" => 'Anual'];
        const TF = ["id" => 'TF', "title" => 'Trabajo Fin de Grado'];
        
        static function getCombo() {
            return [Duration::C1, Duration::C2, Duration::A, Duration::TF];
        }
    
        static function getDuration($value) {
            return Duration::find($value, Duration::getCombo());
        }
    }
