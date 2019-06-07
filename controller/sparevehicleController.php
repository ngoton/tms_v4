<?php

Class sparevehicleController Extends baseController {

    public function vehicle() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparevehicle) || json_decode($_SESSION['user_permission_action'])->sparevehicle != "sparevehicle") {
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

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'start_time';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

        }


        $vehicle_model = $this->model->get('vehicleModel');
        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $this->view->data['vehicles'] = $vehicles;

        $romooc_model = $this->model->get('romoocModel');
        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));
        $this->view->data['romoocs'] = $romoocs;
        
        $join = array('table'=>'vehicle,romooc','where'=>'vehicle=vehicle_id AND romooc=romooc_id');


        $sparevehicle_model = $this->model->get('sparevehicleModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        

        $tongsodong = count($sparevehicle_model->getAllStock(null,$join));

        $tongsotrang = ceil($tongsodong / $sonews);

        



        $this->view->data['page'] = $page;

        $this->view->data['order_by'] = $order_by;

        $this->view->data['order'] = $order;

        $this->view->data['keyword'] = $keyword;

        $this->view->data['limit'] = $limit;

        $this->view->data['pagination_stages'] = $pagination_stages;

        $this->view->data['tongsotrang'] = $tongsotrang;

        $this->view->data['sonews'] = $sonews;



        $data = array(

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            );

        

        if ($keyword != '') {

            $search = '( vehicle_number LIKE "%'.$keyword.'%" OR romooc_number LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }

        

        

        

        $this->view->data['spare_vehicles'] = $sparevehicle_model->getAllStock($data,$join);



        $this->view->data['lastID'] = isset($sparevehicle_model->getLastStock()->spare_vehicle_id)?$sparevehicle_model->getLastStock()->spare_vehicle_id:0;

        

        $this->view->show('sparevehicle/vehicle');

    }

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->sparevehicle) || json_decode($_SESSION['user_permission_action'])->sparevehicle != "sparevehicle") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý vật tư thiết bị';

        $vehicle_model = $this->model->get('vehicleModel');
        $romooc_model = $this->model->get('romoocModel');
        $export_stock_model = $this->model->get('exportstockModel');
        $spare_stock_model = $this->model->get('sparestockModel');
        $sparevehicle_model = $this->model->get('sparevehicleModel');
        $house_model = $this->model->get('houseModel');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $vehicle = $_POST['vehicle'];
            $romooc = $_POST['romooc'];
            $house = $_POST['house'];
            $export_stock = $_POST['export_stock'];
            $tab_active = $_POST['tab_active'];
        }
        else{
            $vehicle = $vehicle_model->getLastVehicle()->vehicle_id;
            $romooc = $romooc_model->getLastVehicle()->romooc_id;
            $house = $house_model->getLastHouse()->house_id;
            $export_stocks = $export_stock_model->getAllStock(array('where'=>'house='.$house,'order_by'=>'export_stock_id DESC','limit'=>1));
            foreach ($export_stocks as $key) {
                $export_stock = $key->export_stock_id;
            }
            $tab_active = 1;
        }

        if (isset($_POST['house_change'])) {
            $export_stocks = $export_stock_model->getAllStock(array('where'=>'house='.$house,'order_by'=>'export_stock_id DESC','limit'=>1));
            foreach ($export_stocks as $key) {
                $export_stock = $key->export_stock_id;
            }
        }

        $houses = $house_model->getAllHouse();

        $this->view->data['houses'] = $houses;

        $this->view->data['house'] = $house;

        $spare_vehicles = $sparevehicle_model->getAllStock();
        $arr = array();
        $arr_stock = array();
        foreach ($spare_vehicles as $spare_vehicle) {
            $arr[$spare_vehicle->export_stock] = isset($arr[$spare_vehicle->export_stock])?$arr[$spare_vehicle->export_stock]+$spare_vehicle->spare_part_number:$spare_vehicle->spare_part_number;
            $arr_stock[$spare_vehicle->export_stock][$spare_vehicle->spare_part] = isset($arr_stock[$spare_vehicle->export_stock][$spare_vehicle->spare_part])?$arr_stock[$spare_vehicle->export_stock][$spare_vehicle->spare_part]+$spare_vehicle->spare_part_number:$spare_vehicle->spare_part_number;
        }
        $this->view->data['arr_stock'] = $arr_stock;
        /////////////////// Lấy số lượng theo phiếu xuất kho và loại phụ tùng
        $data = array(
            'where'=>'house='.$house,
            'order_by'=>'export_stock_code',
            'order'=>'DESC'
        );
        $export_stocks = $export_stock_model->getAllStock($data);
        $last_id = 0;
        foreach ($export_stocks as $key => $export) {
            if (isset($arr[$export->export_stock_id]) && $arr[$export->export_stock_id] >= $export->export_stock_total) {
                unset($export_stocks[$key]);
                if ($export_stock == $export->export_stock_id) {
                    $export_stock = $last_id;
                }
            }
            $last_id = $export->export_stock_id;
            
        }

        $this->view->data['export_stocks'] = $export_stocks;
        ////////////// Lấy danh sách phiếu xuất kho loại trừ đã dùng hết

        $this->view->data['vehicle'] = $vehicle;
        $this->view->data['romooc'] = $romooc;
        $this->view->data['export_stock'] = $export_stock;
        $this->view->data['tab_active'] = $tab_active;

        $vehicle_selected = $vehicle_model->getVehicle($vehicle);
        $this->view->data['vehicle_selected'] = $vehicle_selected;
        $romooc_selected = $romooc_model->getVehicle($romooc);
        $this->view->data['romooc_selected'] = $romooc_selected;


        
        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $this->view->data['vehicles'] = $vehicles;
        
        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));
        $this->view->data['romoocs'] = $romoocs;


        //$spare_part_model = $this->model->get('sparepartModel');
        $join = array('table'=>'spare_part','where'=>'spare_part=spare_part_id');
        $data = array(
            'where' => 'export_stock = '.$export_stock,
            'order_by'=>'spare_part_name ASC, spare_part_code',
            'order'=>'ASC'
        );
        $spare_parts = $spare_stock_model->getAllStock($data,$join);
        $this->view->data['spare_parts'] = $spare_parts;
        ////////////// Lấy phụ tùng đã được xuất kho dựa vào số phiếu

        $join = array('table'=>'vehicle,spare_part','where'=>'vehicle = vehicle_id AND spare_part=spare_part_id');
        
        $qr = 'SELECT *,SUM(spare_part_number) as total FROM vehicle,spare_part,spare_vehicle WHERE vehicle = vehicle_id AND spare_part=spare_part_id AND (end_time IS NULL OR end_time = 0) AND vehicle = '.$vehicle.' GROUP BY vehicle,spare_part';
        $spare_vehicles = $sparevehicle_model->queryStock($qr);
        $this->view->data['spare_vehicles'] = $spare_vehicles;
        ///////////////// Lấy phụ tùng kèm số lượng đã dùng cho từng đầu xe

        $data = array(
            'where' => '(end_time > 0) AND vehicle = '.$vehicle,
        );
        $spare_vehicles = $sparevehicle_model->getAllStock($data,$join);
        $spare_vehicle_outs = array();
        foreach ($spare_vehicles as $spare) {
            $spare_vehicle_outs[$spare->vehicle][$spare->spare_part] = isset($spare_vehicle_outs[$spare->vehicle][$spare->spare_part])?$spare_vehicle_outs[$spare->vehicle][$spare->spare_part]+$spare->spare_part_number:$spare->spare_part_number;
        }
        $this->view->data['spare_vehicle_outs'] = $spare_vehicle_outs;
        ///////////////// Phụ tùng đã thay ra kèm số lượng theo đầu xe

        $join = array('table'=>'romooc,spare_part','where'=>'romooc = romooc_id AND spare_part=spare_part_id');
        
        $qr = 'SELECT *,SUM(spare_part_number) as total FROM romooc,spare_part,spare_vehicle WHERE romooc = romooc_id AND spare_part=spare_part_id AND (end_time IS NULL OR end_time = 0) AND romooc = '.$romooc.' GROUP BY romooc,spare_part';
        $spare_romoocs = $sparevehicle_model->queryStock($qr);
        $this->view->data['spare_romoocs'] = $spare_romoocs;
        ///////////////// Lấy phụ tùng kèm số lượng đã dùng cho từng mooc
        $data = array(
            'where' => '(end_time > 0) AND romooc = '.$romooc,
        );
        $spare_romoocs = $sparevehicle_model->getAllStock($data,$join);
        $spare_romooc_outs = array();
        foreach ($spare_romoocs as $spare) {
            $spare_romooc_outs[$spare->romooc][$spare->spare_part] = isset($spare_romooc_outs[$spare->romooc][$spare->spare_part])?$spare_romooc_outs[$spare->romooc][$spare->spare_part]+$spare->spare_part_number:$spare->spare_part_number;
        }
        $this->view->data['spare_romooc_outs'] = $spare_romooc_outs;
        ///////////////// Phụ tùng đã thay ra kèm số lượng theo mooc

        $this->view->show('sparevehicle/index');

    }



    public function exchange(){
        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparevehicle) || json_decode($_SESSION['user_permission_action'])->sparevehicle != "sparevehicle") {

            return $this->view->redirect('user/login');

        }
        if (isset($_POST['yes'])) {

            $sparevehicle = $this->model->get('sparevehicleModel');
            $sparedrap = $this->model->get('sparedrapModel');

            if (isset($_POST['vehicle_in'])) {
                $vehicle_in = $_POST['vehicle_in'];
                foreach ($vehicle_in as $v) {
                    $data = array(

                        'vehicle' => trim($_POST['vehicle']),
                        'start_time' => strtotime($_POST['start_time']),
                        'spare_part' => $v['vehicle_in_id'],
                        'spare_part_number' => $v['vehicle_in_num'],
                        'export_stock' => $_POST['export_stock'],

                    );
                    $sparevehicle->createStock($data);
                }
            }
            if (isset($_POST['romooc_in'])) {
                $romooc_in = $_POST['romooc_in'];
                foreach ($romooc_in as $v) {
                    $data = array(

                        'romooc' => trim($_POST['romooc']),
                        'start_time' => strtotime($_POST['start_time']),
                        'spare_part' => $v['romooc_in_id'],
                        'spare_part_number' => $v['romooc_in_num'],
                        'export_stock' => $_POST['export_stock'],

                    );
                    $sparevehicle->createStock($data);
                }
            }
            if (isset($_POST['vehicle_out'])) {
                $vehicle_out = $_POST['vehicle_out'];
                foreach ($vehicle_out as $v) {
                    $data = array(

                        'vehicle' => trim($_POST['vehicle']),
                        'end_time' => strtotime($_POST['end_time']),
                        'spare_part' => $v['vehicle_out_id'],
                        'spare_part_number' => $v['vehicle_out_num'],

                    );
                    $sparevehicle->createStock($data);

                    $data = array(

                        'spare_vehicle' => $sparevehicle->getLastStock()->spare_vehicle_id,
                        'spare_part' => $v['vehicle_out_id'],
                        'spare_part_number' => $v['vehicle_out_num'],

                    );
                    $sparedrap->createStock($data);
                }
            }
            if (isset($_POST['romooc_out'])) {
                $romooc_out = $_POST['romooc_out'];
                foreach ($romooc_out as $v) {
                    $data = array(

                        'romooc' => trim($_POST['romooc']),
                        'end_time' => strtotime($_POST['end_time']),
                        'spare_part' => $v['romooc_out_id'],
                        'spare_part_number' => $v['romooc_out_num'],

                    );
                    $sparevehicle->createStock($data);

                    $data = array(

                        'spare_vehicle' => $sparevehicle->getLastStock()->spare_vehicle_id,
                        'spare_part' => $v['romooc_out_id'],
                        'spare_part_number' => $v['romooc_out_num'],

                    );
                    $sparedrap->createStock($data);
                }
            }
            

            echo "Thay thế thành công";

            date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$sparevehicle->getLastStock()->spare_vehicle_id."|sparevehicle|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

        }
    }



    public function add(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparevehicle) || json_decode($_SESSION['user_permission_action'])->sparevehicle != "sparevehicle") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $sparevehicle = $this->model->get('sparevehicleModel');

            $data = array(

                'vehicle' => trim($_POST['vehicle']),
                'romooc' => trim($_POST['romooc']),
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparevehicle) || json_decode($_SESSION['user_permission_action'])->sparevehicle != "sparevehicle") {

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

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparevehicle) || json_decode($_SESSION['user_permission_action'])->sparevehicle != "sparevehicle") {

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