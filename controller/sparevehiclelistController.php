<?php

Class sparevehiclelistController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparevehiclelist) || json_decode($_SESSION['user_permission_action'])->sparevehiclelist != "sparevehiclelist") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý bảng thay thế vật tư';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

            $xe = isset($_POST['xe']) ? $_POST['xe'] : null;
            $mooc = isset($_POST['nv']) ? $_POST['nv'] : null;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'spare_part ASC, start_time DESC, end_time';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

            $xe = 0;
            $mooc = 0;

        }


        $vehicle_model = $this->model->get('vehicleModel');
        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $this->view->data['vehicles'] = $vehicles;

        $vehicle_data = array();
        foreach ($vehicles as $vehicle) {
            $vehicle_data['id'][$vehicle->vehicle_id] = $vehicle->vehicle_id;
            $vehicle_data['name'][$vehicle->vehicle_id] = $vehicle->vehicle_number;
        }
        $this->view->data['vehicle_data'] = $vehicle_data;

        $romooc_model = $this->model->get('romoocModel');
        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));
        $this->view->data['romoocs'] = $romoocs;

        $romooc_data = array();
        foreach ($romoocs as $romooc) {
            $romooc_data['id'][$romooc->romooc_id] = $romooc->romooc_id;
            $romooc_data['name'][$romooc->romooc_id] = $romooc->romooc_number;
        }
        $this->view->data['romooc_data'] = $romooc_data;

        $exportstock_model = $this->model->get('exportstockModel');
        $exports = $exportstock_model->getAllStock(array('order_by'=>'export_stock_code','order'=>'ASC'));
        $this->view->data['exports'] = $exports;

        $export_data = array();
        foreach ($exports as $export) {
            $export_data['id'][$export->export_stock_id] = $export->export_stock_id;
            $export_data['name'][$export->export_stock_id] = $export->export_stock_code;
        }
        $this->view->data['export_data'] = $export_data;
        
        $join = array('table'=>'spare_part','where'=>'spare_part=spare_part_id');


        $sparevehicle_model = $this->model->get('sparevehicleModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        if($xe > 0){
            $data['where'] = $data['where'].' AND vehicle = '.$xe;
        }

        if($mooc > 0){
            $data['where'] = $data['where'].' AND romooc = '.$mooc;
        }

        $tongsodong = count($sparevehicle_model->getAllStock($data,$join));

        $tongsotrang = ceil($tongsodong / $sonews);

        



        $this->view->data['page'] = $page;

        $this->view->data['order_by'] = $order_by;

        $this->view->data['order'] = $order;

        $this->view->data['keyword'] = $keyword;

        $this->view->data['limit'] = $limit;

        $this->view->data['pagination_stages'] = $pagination_stages;

        $this->view->data['tongsotrang'] = $tongsotrang;

        $this->view->data['sonews'] = $sonews;

        $this->view->data['xe'] = $xe;
        $this->view->data['mooc'] = $mooc;



        $data = array(

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            'where'=>'1=1',

            );

        if($xe > 0){
            $data['where'] = $data['where'].' AND vehicle = '.$xe;
        }

        if($mooc > 0){
            $data['where'] = $data['where'].' AND romooc = '.$mooc;
        }

        if ($keyword != '') {

            $search = '( vehicle_number LIKE "%'.$keyword.'%" OR romooc_number LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }

        

        

        

        $this->view->data['spare_vehicles'] = $sparevehicle_model->getAllStock($data,$join);



        $this->view->data['lastID'] = isset($sparevehicle_model->getLastStock()->spare_vehicle_id)?$sparevehicle_model->getLastStock()->spare_vehicle_id:0;

        

        $this->view->show('sparevehiclelist/index');

    }

   public function getsparepart(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $spare_model = $this->model->get('sparepartModel');

            

            if ($_POST['keyword'] == "*") {



                $list = $spare_model->getAllStock();

            }

            else{

                $data = array(

                'where'=>'( spare_part_name LIKE "%'.$_POST['keyword'].'%" )',

                );

                $list = $spare_model->getAllStock($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text

                $spare_part_name = '['.$rs->spare_part_code.']-'.$rs->spare_part_name;

                if ($_POST['keyword'] != "*") {

                    $spare_part_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', '['.$rs->spare_part_code.']-'.$rs->spare_part_name);

                }

                

                // add new option

                echo '<li onclick="set_item_spare(\''.$rs->spare_part_id.'\',\''.$rs->spare_part_name.'\',\''.$rs->spare_part_code.'\',\''.$rs->spare_part_seri.'\',\''.$rs->spare_part_brand.'\')">'.$spare_part_name.'</li>';

            }

        }

    }

    public function getexport(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $spare_stock_model = $this->model->get('sparestockModel');

            $export_stock = $_POST['export_stock'];
            $spare_part = isset($_POST['spare_part'])?$_POST['spare_part']:0;
            $num = 0;
            $spare_parts = $spare_stock_model->getStockByWhere(array('export_stock'=>$export_stock,'spare_part'=>$spare_part));
            if ($spare_parts) {
                $num = $spare_parts->spare_stock_number;
            }
            echo $num;
        }
    }


    public function add(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparevehiclelist) || json_decode($_SESSION['user_permission_action'])->sparevehiclelist != "sparevehiclelist") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $sparevehicle = $this->model->get('sparevehicleModel');

            $data = array(

                'vehicle' => trim($_POST['vehicle']),
                'romooc' => trim($_POST['romooc']),
                'spare_part' => trim($_POST['spare_part']),
                'spare_part_number' => trim(str_replace(',','',$_POST['spare_part_number'])),
                'export_stock' => trim($_POST['export_stock']),
                'start_time' => strtotime($_POST['start_time']),
                'end_time' => strtotime($_POST['end_time']),
            );

            if ($_POST['yes'] != "") {

                $sparevehicle->updateStock($data,array('spare_vehicle_id' => trim($_POST['yes'])));

                echo "Cập nhật thành công";

                date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|sparevehicle|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);
            }
            else{
                
                $sparevehicle->createStock($data);

                

                echo "Thêm thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$sparevehicle->getLastStock()->spare_vehicle_id."|sparevehicle|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

            }

                

        }

    }



    

    



    public function delete(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparevehiclelist) || json_decode($_SESSION['user_permission_action'])->sparevehiclelist != "sparevehiclelist") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $sparevehicle = $this->model->get('sparevehicleModel');

            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {

                    $sparevehicle->deleteStock($data);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|sparevehicle|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }

                return true;

            }

            else{

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|sparevehicle|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $sparevehicle->deleteStock($_POST['data']);

            }

            

        }

    }



    function export(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }



        $driver_model = $this->model->get('driverModel');

        $d_data = array(

            'where'=> 'end_work > '.strtotime(date('d-m-Y')),

        );

        $drivers = $driver_model->getAllDriver($d_data);

        $driver_data = array();

        foreach ($drivers as $driver) {

            $driver_data[$driver->vehicle]['driver_name'] = $driver->driver_name;

            $driver_data[$driver->vehicle]['driver_phone'] = $driver->driver_phone;

        }



        $romooc_model = $this->model->get('romoocModel');



        $vehicles = $romooc_model->getAllVehicle();



            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', 'DANH SÁCH XE')

                ->setCellValue('A3', 'STT')

               ->setCellValue('B3', 'Số xe')

               ->setCellValue('C3', 'Tài xế')

               ->setCellValue('D3', 'SĐT tài xế')

               ->setCellValue('E3', 'Số cont');

               



            if ($vehicles) {



                $hang = 4;

                $i=1;



                foreach ($vehicles as $row) {

                    

                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                     $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue('A' . $hang, $i++)

                        ->setCellValue('B' . $hang, $row->romooc_number)

                        ->setCellValue('C' . $hang, isset($driver_data[$row->romooc_id]['driver_name'])?$driver_data[$row->romooc_id]['driver_name']:null)

                        ->setCellValue('D' . $hang, isset($driver_data[$row->romooc_id]['driver_phone'])?$driver_data[$row->romooc_id]['driver_phone']:null)

                        ->setCellValue('E' . $hang, $row->cont_number);

                     $hang++;





                  }



          }



            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;



            $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');



            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(18);



            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => 'FF0000')

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('H4:E'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A3:E3')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(26);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(28);



            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Vehicle Report")

                            ->setSubject("Vehicle Report")

                            ->setDescription("Vehicle Report.")

                            ->setKeywords("Vehicle Report")

                            ->setCategory("Vehicle Report");

            $objPHPExcel->getActiveSheet()->setTitle("Danh sach xe");



            $objPHPExcel->getActiveSheet()->freezePane('A4');

            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= DANH SACH XE.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }



    

    public function import(){

        $this->view->disableLayout();

        header('Content-Type: text/html; charset=utf-8');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparevehiclelist) || json_decode($_SESSION['user_permission_action'])->sparevehiclelist != "sparevehiclelist") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES['import']['name'] != null) {



            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $vehicle = $this->model->get('romoocModel');



            $objPHPExcel = new PHPExcel();

            // Set properties

            if (pathinfo($_FILES['import']['name'], PATHINFO_EXTENSION) == "xls") {

                $objReader = PHPExcel_IOFactory::createReader('Excel5');

            }

            else if (pathinfo($_FILES['import']['name'], PATHINFO_EXTENSION) == "xlsx") {

                $objReader = PHPExcel_IOFactory::createReader('Excel2007');

            }

            

            $objReader->setReadDataOnly(false);



            $objPHPExcel = $objReader->load($_FILES['import']['tmp_name']);

            $objWorksheet = $objPHPExcel->getActiveSheet();



            



            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10

            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'



            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5



            //var_dump($objWorksheet->getMergeCells());die();

            

             



                for ($row = 2; $row <= $highestRow; ++ $row) {

                    $val = array();

                    for ($col = 0; $col < $highestColumnIndex; ++ $col) {

                        $cell = $objWorksheet->getCellByColumnAndRow($col, $row);

                        // Check if cell is merged

                        foreach ($objWorksheet->getMergeCells() as $cells) {

                            if ($cell->isInRange($cells)) {

                                $currMergedCellsArray = PHPExcel_Cell::splitRange($cells);

                                $cell = $objWorksheet->getCell($currMergedCellsArray[0][0]);

                                break;

                                

                            }

                        }

                        //$val[] = $cell->getValue();

                        $val[] = is_numeric($cell->getCalculatedValue()) ? round($cell->getCalculatedValue()) : $cell->getCalculatedValue();

                        //here's my prob..

                        //echo $val;

                    }

                    if ($val[1] != null && $val[2] != null) {



                            if(!$vehicle->getVehicleByWhere(array('romooc_number'=>trim($val[1])))) {

                                $romooc_data = array(

                                'romooc_number' => trim($val[1]),

                                'driver_name' => trim($val[2]),

                                'driver_phone' => trim($val[3]),

                                );

                                $vehicle->createVehicle($romooc_data);

                            }

                            else if($vehicle->getVehicleByWhere(array('romooc_number'=>trim($val[1])))){

                                $id_vehicle = $vehicle->getVehicleByWhere(array('romooc_number'=>trim($val[1])))->romooc_id;

                                $romooc_data = array(

                                'driver_name' => trim($val[2]),

                                'driver_phone' => trim($val[3]),

                                );

                                $vehicle->updateVehicle($romooc_data,array('romooc_id' => $id_vehicle));

                            }





                        

                    }

                    

                    //var_dump($this->getNameDistrict($this->lib->stripUnicode($val[1])));

                    // insert





                }

                //return $this->view->redirect('transport');

            

            return $this->view->redirect('vehicle');

        }

        $this->view->show('vehicle/import');



    }

    



    public function view() {

        

        $this->view->show('handling/view');

    }



}

?>