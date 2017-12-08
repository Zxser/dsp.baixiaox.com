<?php

class Export {

    public static function excel($title_arr, $data_arr, $file_name = "exportdata") {
        if (!class_exists('ExportDataExcel')) {
            require APPPATH . "libraries/ExportData/exportdataexcel.php";
        }
        setlocale(LC_ALL, 'zh_CN.UTF-8');
        $export_data = new ExportDataExcel("browser", $file_name);
        $export_data->initialize();
        $export_data->addRow($title_arr);
        foreach ($data_arr as $row) {
            $export_data->addRow($row);
        }
        $export_data->finalize();
    }

}
