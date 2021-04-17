<?php

namespace App\ExcelFileHandler;

use App\Models\UploadedFileResult;

interface IExcelFileFormat
{
    public function proces_excel($data, UploadedFileResult &$file_result);
    public function getFormat() : string;

    public static function build(array $header) : ?IExcelFileFormat;
}
