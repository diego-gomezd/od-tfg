<?php 
namespace App\Models\Combos;

abstract class BasicCombo {
    static function find($value, $array) {
        $item = null;
        if ($value != null)
        {
            foreach ($array as $element) {
                if ($element['id'] == $value) {
                    $item = $element;
                    break;
                }
            }
            if ($item == null) {
                $item = ["id" => $value, "title" => $value];
            }
        }
        return $item;
    }
}