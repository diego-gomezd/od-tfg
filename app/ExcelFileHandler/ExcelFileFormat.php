<?php

namespace App\ExcelFileHandler;

class ExcelFileFormat {

    protected static function isValidExcelFormat(array $header, array $columns)
    {
        $valid = true;
        $i = 0;
        while ($valid && $i < count($header) && $i < count($columns))
        {
            if ($header[$i] != $columns[$i]) 
            {
                $valid = false;
            }
            $i++;
        }
        return $valid;
    }

    protected function validateEmptyColumn($value, $mandatory, $errorMsg, &$status)
    {
        $validate = true; 
        if (empty(trim($value)))
        {
            $status[] = array('status' => $mandatory ? 'ERROR' : 'WARNING', 'msg' => $errorMsg);
            if ($mandatory)
            {
                $validate = false; 
            }
        }
        return $validate;
    }

    protected function validateLenghtColumn($value, $max_lenght, $errorMsg, &$status)
    {
        $validate = true; 
        if (!empty(trim($value)) && strlen(trim($value)) > $max_lenght)
        {
            $status[] = array('status' => 'ERROR', 'msg' => $errorMsg);
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
            $validate = $this->validateEmptyColumn($value, $mandatory, 'Columna '.$header_name.' vacia en fila '.$row_num, $status);
        }

        if (!empty($value) && $only_integer && !(is_numeric($value) && ctype_digit($value) && intval($value) > 0))
        {

            $status[] = array('status' => 'ERROR', 'msg' => 'El valor de la celda ['.$row_num.', '.$header_name.'] debe ser un número entero.');
            $validate = false; 
        }

        if ($max_lenght != null && $max_lenght > 0)
        {
            $validate_lenght = $this->validateLenghtColumn($value, $max_lenght, 'El tamaño de la celda ['.$row_num.', '.$header_name.'] supera el máximo permitido ('.$max_lenght.').', $status);
            if (!$validate_lenght) {
                $validate = false;
            }
        }
        return $validate;
    }


}