<?php

namespace App\ExcelFileHandler;

use App\Models\Combos\Course;
use Illuminate\Support\Facades\Log;

class ExcelFileFormat
{

    protected static function isValidExcelFormat(array $header, array $columns)
    {
        $valid = true;
        $i = 0;
        while ($valid && $i < count($header) && $i < count($columns)) {
            if ($header[$i] != $columns[$i]) {
                $valid = false;
            }
            $i++;
        }
        return $valid;
    }

    protected function validateEmptyColumn($value, $mandatory, $errorMsg, &$status)
    {
        $validate = true;
        if (empty(trim($value))) {
            $status[] = array('status' => $mandatory ? 'ERROR' : 'WARNING', 'msg' => $errorMsg. '(\''.$value.'\')');
            if ($mandatory) {
                $validate = false;
            }
        }
        return $validate;
    }

    protected function validateLenghtColumn($value, $max_lenght, $errorMsg, &$status)
    {
        $validate = true;
        if (!empty(trim($value)) && strlen(trim($value)) > $max_lenght) {
            $status[] = array('status' => 'ERROR', 'msg' => $errorMsg. '(\''.$value.'\')');
            $validate = false;
        }
        return $validate;
    }
    /**
     * @param $value Valor de la columna
     * @param $header_name Nombre de la columna
     * @param $row_num Posicion de la fila
     * @param $mandatory Indica si el campo es obligatorio
     * @param $warning_empty Indica si el campo puede ser vacio pero se muestra una incidencia
     * @param $max_lenght Longitud máxima
     * @param $only_integer Si el campo solo puede ser un numero
     * @param &$status array con los errores de validacion
     */
    protected function validateColumn($value, $header_name, $row_num, $mandatory, $warning_empty, $max_lenght, $only_integer, &$status)
    {
        $validate = true;
        if ($mandatory || $warning_empty) {
            $validate = $this->validateEmptyColumn($value, $mandatory, 'Columna ' . $header_name . ' vacia en fila ' . $row_num, $status);
        }

        if (!empty($value) && $only_integer && !(is_int($value) || ctype_digit($value))) {
            $status[] = array('status' => 'ERROR', 'msg' => 'El valor de la celda [' . $row_num . ', ' . $header_name . '] debe ser un número entero. (\''.$value.'\')');
            $validate = false;
        }

        if ($max_lenght != null && $max_lenght > 0) {
            $validate_lenght = $this->validateLenghtColumn($value, $max_lenght, 'El tamaño de la celda [' . $row_num . ', ' . $header_name . '] supera el máximo permitido (' . $max_lenght . ').', $status);
            if (!$validate_lenght) {
                $validate = false;
            }
        }
        return $validate;
    }

    protected function getCourseFromClassgroup($classgroup_code)
    {
        $course = null;

        if (!empty($classgroup_code)) {
            $to_parse = null;
            $parts = explode("_", $classgroup_code);
            if (count($parts) == 1) {
                $to_parse = $parts[0];
            } else if (count($parts) > 1) {
                $to_parse = $parts[1];
            }
            if (!empty($to_parse)) {
                if ($to_parse[0] == '1') {
                    $course = Course::_1;
                } else if ($to_parse[0] == '2') {
                    $course = Course::_2;
                } else if ($to_parse[0] == '3') {
                    $course = Course::_3;
                } else if ($to_parse[0] == '4') {
                    $course = Course::_4;
                }
            }
        }

        return $course;
    }

    protected const title_style = [
        'font' => [
            'bold' => true,
            'name' => 'Arial',
            'size' => 10,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ];

    protected const normal_style = [
        'font' => [
            'bold' => false,
            'name' => 'Arial',
            'size' => 10,
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ];
}
