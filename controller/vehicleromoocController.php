<?php

Class vehicleromoocController Extends baseController {

    public function vehicle() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehicleromooc) || json_decode($_SESSION['user_permission_action'])->vehicleromooc != "vehicleromooc") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý bảng thay đổi mooc';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'vehicle_number ASC, start_time';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';

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


        $vehicleromooc_model = $this->model->get('vehicleromoocModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        

        $tongsodong = count($vehicleromooc_model->getAllVehicle(null,$join));

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

        

        

        

        $this->view->data['vehicle_romoocs'] = $vehicleromooc_model->getAllVehicle($data,$join);



        $this->view->data['lastID'] = isset($vehicleromooc_model->getLastVehicle()->vehicle_romooc_id)?$vehicleromooc_model->getLastVehicle()->vehicle_romooc_id:0;

        

        $this->view->show('vehicleromooc/vehicle');

    }

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->vehicleromooc) || json_decode($_SESSION['user_permission_action'])->vehicleromooc != "vehicleromooc") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý xe';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : null;

            $romooc = isset($_POST['romooc']) ? $_POST['romooc'] : null;

        }

        else{

            $vehicle = 0;

            $romooc = 0;

        }

        $this->view->data['xe'] = $vehicle;
        $this->view->data['mooc'] = $romooc;


        $vehicle_model = $this->model->get('vehicleModel');
        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $this->view->data['vehicle_lists'] = $vehicles;

        $data = array('order_by'=>'vehicle_number','order'=>'ASC');
        if ($vehicle > 0) {
            $data = array('where'=>'vehicle_id = '.$vehicle);
        }
        $vehicles = $vehicle_model->getAllVehicle($data);
        $this->view->data['vehicles'] = $vehicles;

        $romooc_model = $this->model->get('romoocModel');
        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));
        $this->view->data['romooc_lists'] = $romoocs;

        $data = array('order_by'=>'romooc_number','order'=>'ASC');
        if ($romooc > 0) {
            $data = array('where'=>'romooc_id = '.$romooc);
        }
        $romoocs = $romooc_model->getAllVehicle($data);
        $this->view->data['romoocs'] = $romoocs;

        

        $vehicleromooc_model = $this->model->get('vehicleromoocModel');
        $join = array('table'=>'vehicle, romooc','where'=>'vehicle = vehicle_id AND romooc = romooc_id');
        $data = array(
            'where' => '((end_time IS NULL OR end_time = 0) OR end_time >= '.strtotime(date('d-m-Y')).')',
        );
        $vehicle_romoocs = $vehicleromooc_model->getAllVehicle($data,$join);
        $this->view->data['vehicle_romoocs'] = $vehicle_romoocs;

        $this->view->show('vehicleromooc/index');

    }



    public function exchange(){
        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehicleromooc) || json_decode($_SESSION['user_permission_action'])->vehicleromooc != "vehicleromooc") {

            return $this->view->redirect('user/login');

        }
        if (isset($_POST['yes'])) {

            $vehicleromooc = $this->model->get('vehicleromoocModel');

            $data = array(

                'vehicle' => trim($_POST['vehicle']),
                'romooc' => trim($_POST['romooc']),
                'start_time' => strtotime($_POST['start_time']),

            );

            $dm1 = $vehicleromooc->queryVehicle('SELECT * FROM vehicle_romooc WHERE romooc='.$data['romooc'].' AND start_time <= '.$data['start_time'].' AND (end_time IS NULL OR end_time > '.$data['start_time'].') ORDER BY start_time DESC LIMIT 1');
            $dm2 = $vehicleromooc->queryVehicle('SELECT * FROM vehicle_romooc WHERE romooc='.$data['romooc'].' AND start_time > '.$data['start_time'].' AND (end_time IS NULL OR end_time > '.$data['start_time'].') ORDER BY start_time ASC LIMIT 1');
            $dm3 = $vehicleromooc->queryVehicle('SELECT * FROM vehicle_romooc WHERE vehicle='.$data['vehicle'].' AND start_time <= '.$data['start_time'].' AND (end_time IS NULL OR end_time > '.$data['start_time'].') ORDER BY start_time DESC LIMIT 1');
            $dm4 = $vehicleromooc->queryVehicle('SELECT * FROM vehicle_romooc WHERE vehicle='.$data['vehicle'].' AND start_time > '.$data['start_time'].' AND (end_time IS NULL OR end_time > '.$data['start_time'].') ORDER BY start_time ASC LIMIT 1');

            if ($dm1 || $dm2 || $dm3 || $dm4) {
                if($dm1){
                    foreach ($dm1 as $row) {
                        $d = array(
                            'end_time' => strtotime(date('d-m-Y',strtotime($_POST['start_time'].' -1 day'))),
                            );
                        $vehicleromooc->updateVehicle($d,array('vehicle_romooc_id'=>$row->vehicle_romooc_id));
                    }
                }
                else if ($dm2) {
                    foreach ($dm2 as $row) {
                        $data['end_time'] = strtotime(date('d-m-Y',strtotime(date('d-m-Y',$row->start_time).' -1 day')));
                    }
                }

                
                if($dm3){
                    foreach ($dm3 as $row) {
                        $d = array(
                            'end_time' => strtotime(date('d-m-Y',strtotime($_POST['start_time'].' -1 day'))),
                            );
                        $vehicleromooc->updateVehicle($d,array('vehicle_romooc_id'=>$row->vehicle_romooc_id));
                    }
                }
                else if ($dm4) {
                    foreach ($dm4 as $row) {
                        $data['end_time'] = strtotime(date('d-m-Y',strtotime(date('d-m-Y',$row->start_time).' -1 day')));
                    }
                }

            }
            
            $vehicleromooc->createVehicle($data);
            
            

            echo "Thay thế thành công";

            date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$vehicleromooc->getLastVehicle()->vehicle_romooc_id."|vehicleromooc|".implode("-",$data)."\n"."\r\n";

                        

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

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehicleromooc) || json_decode($_SESSION['user_permission_action'])->vehicleromooc != "vehicleromooc") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $vehicleromooc = $this->model->get('vehicleromoocModel');

            $shipment = $this->model->get('shipmentModel');

            $data = array(

                'vehicle' => trim($_POST['vehicle']),
                'romooc' => trim($_POST['romooc']),
                'start_time' => strtotime($_POST['start_time']),
                'end_time' => strtotime($_POST['end_time']),

            );

            if ($_POST['yes'] != "") {

                $vehicleromooc->updateVehicle($data,array('vehicle_romooc_id' => trim($_POST['yes'])));

                echo "Cập nhật thành công";

                date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|vehicleromooc|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);
            }
            else{
                
                $vehicleromooc->createVehicle($data);

                

                echo "Thêm thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$vehicleromooc->getLastVehicle()->vehicle_romooc_id."|vehicleromooc|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

            }

            $shipments = $shipment->getAllShipment(array('where'=>'vehicle = '.$data['vehicle'].' AND shipment_date >= '.$data['start_time'].' AND shipment_date <= '.$data['end_time']));

            if($shipments){
                foreach ($shipments as $ship) {

                    $data_edit = array(

                        'romooc' => $data['romooc'],

                        );

                    $shipment->updateShipment($data_edit,array('shipment_id' => $ship->shipment_id));

                }
            }

                

        }

    }



    

    



    public function delete(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehicleromooc) || json_decode($_SESSION['user_permission_action'])->vehicleromooc != "vehicleromooc") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $vehicleromooc = $this->model->get('vehicleromoocModel');

            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {

                    $vehicleromooc->deleteVehicle($data);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|vehicleromooc|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }

                return true;

            }

            else{

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|vehicleromooc|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $vehicleromooc->deleteVehicle($_POST['data']);

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

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehicleromooc) || json_decode($_SESSION['user_permission_action'])->vehicleromooc != "vehicleromooc") {

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