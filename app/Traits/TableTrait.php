<?php

namespace App\Traits;

use App\Models\Dokumen;
use PhpOffice\PhpSpreadsheet\IOFactory;

trait TableTrait
{
    public function EDTable($id)
    {
        $data = Dokumen::find($id);
        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));
        $maxCell = $file->getSheet(0)->getHighestRowAndColumn();
        $sheetData = $file->getSheet(0)->rangeToArray('A1:' . $maxCell['column'] . $maxCell['row'] - 1);
        return [$data, $sheetData];
    }

    public function KSTable($id)
    {
        $data = Dokumen::find($id);
        $headers = [];
        $sheetData = [];
        $file = IOFactory::load(storage_path('app/public/' . $data->file_data));
        $sheetCount = $file->getSheetCount();
        $sheetName = $file->getSheetNames();

        for ($i = 0; $i < $sheetCount - 2; $i++) {
            $sheet = $file->getSheet($i)->toArray(null, true, true, true);
            $header = array_shift($sheet);
            array_push($sheetData, $sheet);
            array_push($headers, $header);
        }

        return [$data, $headers, $sheetCount, $sheetName, $sheetData];
    }
}