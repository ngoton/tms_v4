<?php

Class driverController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->driver) || json_decode($_SESSION['user_permission_action'])->driver != "driver") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý tài xế';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'vehicle_number ASC, start_work';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

        }



        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;



        $join = array('table'=>'vehicle, steersman','where'=>'vehicle.vehicle_id = driver.vehicle AND steersman.steersman_id = driver.steersman');



        $driver_model = $this->model->get('driverModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        

        $tongsodong = count($driver_model->getAllDriver(null,$join));

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

            $search = '( vehicle_number LIKE "%'.$keyword.'%" OR steersman_name LIKE "%'.$keyword.'%" OR steersman_phone LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }

        

        

        

        $this->view->data['drivers'] = $driver_model->getAllDriver($data,$join);



        $this->view->data['lastID'] = isset($driver_model->getLastDriver()->driver_id)?$driver_model->getLastDriver()->driver_id:0;

        

        $this->view->show('driver/index');

    }



    public function getdriver(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $steersman_model = $this->model->get('steersmanModel');

            

            if ($_POST['keyword'] == "*") {



                $list = $steersman_model->getAllSteersman();

            }

            else{

                $data = array(

                'where'=>'( steersman_name LIKE "%'.$_POST['keyword'].'%" )',

                );

                $list = $steersman_model->getAllSteersman($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text

                $steersman_name = $rs->steersman_name;

                if ($_POST['keyword'] != "*") {

                    $steersman_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->steersman_name);

                }

                

                // add new option

                echo '<li onclick="set_item_driver(\''.$rs->steersman_id.'\',\''.$rs->steersman_name.'\',\''.$rs->steersman_code.'\',\''.$rs->steersman_cmnd.'\',\''.date('d-m-Y',$rs->steersman_birth).'\',\''.$rs->steersman_phone.'\',\''.$rs->steersman_bank.'\')">'.$steersman_name.'</li>';

            }

        }

    }



    public function add(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->driver) || json_decode($_SESSION['user_permission_action'])->driver != "driver") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $driver = $this->model->get('driverModel');
            $driver_temp = $this->model->get('drivertempModel');
            $shipment = $this->model->get('shipmentModel');

            $data = array(

                        'vehicle' => trim($_POST['vehicle']),

                        'start_work' => strtotime(trim($_POST['start_work'])),

                        'end_work' => strtotime(trim($_POST['end_work'])),

                        'steersman' => trim($_POST['steersman']),

                        );

            if ($_POST['yes'] != "") {

                //$data['driver_update_user'] = $_SESSION['userid_logined'];

                //$data['driver_update_time'] = time();

                //var_dump($data);

                

                    $driver_d = $driver->getDriver($_POST['yes']);

                    $driver1 = $driver->getDriverByWhere(array('vehicle'=>$driver_d->vehicle,'end_work'=>(strtotime(date('d-m-Y',strtotime(date('d-m-Y',$driver_d->start_work).' -1 day'))))));
                    $driver2 = $driver->getDriverByWhere(array('vehicle'=>$driver_d->vehicle,'start_work'=>(strtotime(date('d-m-Y',strtotime(date('d-m-Y',$driver_d->end_work).' +1 day'))))));
                    if($driver1)
                        $driver->updateDriver(array('vehicle'=>$driver_d->vehicle,'end_work'=>(strtotime(date('d-m-Y',strtotime($_POST['start_work'].' -1 day'))))),array('driver_id' => $driver1->driver_id));
                    if($driver2)
                        $driver->updateDriver(array('vehicle'=>$driver_d->vehicle,'start_work'=>(strtotime(date('d-m-Y',strtotime($_POST['end_work'].' +1 day'))))),array('driver_id' => $driver2->driver_id));


                    $driver->updateDriver($data,array('driver_id' => trim($_POST['yes'])));

                    $s_data = array(
                        'where'=> 'vehicle = '.$data['vehicle'].' AND shipment_date >= '.$data['start_work'].' AND shipment_date <= '.$data['end_work'],
                    );
                    $shipments = $shipment->getAllShipment($s_data);

                    foreach ($shipments as $ship) {
                        $shipment->updateShipment(array('steersman'=>$data['steersman']),array('shipment_id'=>$ship->shipment_id));
                    }


                    echo "Cập nhật thành công";

                    $data2 = array('driver_id'=>$_POST['yes'],'driver_temp_date'=>strtotime(date('d-m-Y')),'driver_temp_action'=>2,'driver_temp_user'=>$_SESSION['userid_logined'],'name'=>'Bàn giao xe');
                    $data_temp = array_merge($data, $data2);
                    $driver_temp->createDriver($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|driver|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    

            }

            else{

                //$data['driver_create_user'] = $_SESSION['userid_logined'];

                //$data['staff'] = $_POST['staff'];

                //var_dump($data);

                if ($driver->getDriverByWhere(array('steersman'=>$data['steersman'],'vehicle'=>$data['vehicle'],'start_work'=>$data['start_work'],'end_work'=>$data['end_work']))) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $dm1 = $driver->queryDriver('SELECT * FROM driver WHERE vehicle='.$data['vehicle'].' AND start_work <= '.$data['start_work'].' AND end_work <= '.$data['end_work'].' AND end_work >= '.$data['start_work'].' ORDER BY end_work ASC LIMIT 1');
                    $dm2 = $driver->queryDriver('SELECT * FROM driver WHERE vehicle='.$data['vehicle'].' AND end_work >= '.$data['end_work'].' AND start_work >= '.$data['start_work'].' AND start_work <= '.$data['end_work'].' ORDER BY end_work ASC LIMIT 1');
                    $dm3 = $driver->queryDriver('SELECT * FROM driver WHERE vehicle='.$data['vehicle'].' AND start_work <= '.$data['start_work'].' AND end_work >= '.$data['end_work'].' ORDER BY end_work ASC LIMIT 1');

                    if ($dm3) {
                            foreach ($dm3 as $row) {
                                $d = array(
                                    'end_work' => strtotime(date('d-m-Y',strtotime($_POST['start_work'].' -1 day'))),
                                    );
                                $driver->updateDriver($d,array('driver_id'=>$row->driver_id));

                                $c = array(
                                    'vehicle' => $row->vehicle,
                                    'steersman' => $row->steersman,
                                    'start_work' => strtotime(date('d-m-Y',strtotime($_POST['end_work'].' +1 day'))),
                                    'end_work' => $row->end_work,
                                    );
                                $driver->createDriver($c);

                            }

                            

                            
                            $driver->createDriver($data);

                        }
                        else if ($dm1 || $dm2) {
                            if($dm1){
                                foreach ($dm1 as $row) {
                                    $d = array(
                                        'end_work' => strtotime(date('d-m-Y',strtotime($_POST['start_work'].' -1 day'))),
                                        );
                                    $driver->updateDriver($d,array('driver_id'=>$row->driver_id));

                                    
                                }
                            }
                            if($dm2){
                                foreach ($dm2 as $row) {
                                    $d = array(
                                        'start_work' => strtotime(date('d-m-Y',strtotime($_POST['end_work'].' +1 day'))),
                                        );
                                    $driver->updateDriver($d,array('driver_id'=>$row->driver_id));


                                }
                            }


                            
                            $driver->createDriver($data);

                        
                    }
                    else{
                        $driver->createDriver($data);

                    }

                    $s_data = array(
                        'where'=> 'vehicle = '.$data['vehicle'].' AND shipment_date >= '.$data['start_work'].' AND shipment_date <= '.$data['end_work'],
                    );
                    $shipments = $shipment->getAllShipment($s_data);

                    foreach ($shipments as $ship) {
                        $shipment->updateShipment(array('steersman'=>$data['steersman']),array('shipment_id'=>$ship->shipment_id));
                    }

                    echo "Thêm thành công";

                    $data2 = array('driver_id'=>$driver->getLastDriver()->driver_id,'driver_temp_date'=>strtotime(date('d-m-Y')),'driver_temp_action'=>1,'driver_temp_user'=>$_SESSION['userid_logined'],'name'=>'Bàn giao xe');
                    $data_temp = array_merge($data, $data2);
                    $driver_temp->createDriver($data_temp);


                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$driver->getLastDriver()->driver_id."|driver|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }

                

            }

                    

        }

    }



    

    



    public function delete(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->driver) || json_decode($_SESSION['user_permission_action'])->driver != "driver") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $driver = $this->model->get('driverModel');
            $driver_temp = $this->model->get('drivertempModel');

            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {
                    $driver_data = (array)$driver->getDriver($data);

                    $driver->deleteDriver($data);
                    
                    $data2 = array('driver_id'=>$data,'driver_temp_date'=>strtotime(date('d-m-Y')),'driver_temp_action'=>3,'driver_temp_user'=>$_SESSION['userid_logined'],'name'=>'Bàn giao xe');
                    $data_temp = array_merge($driver_data, $data2);
                    $driver_temp->createDriver($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|driver|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }

                return true;

            }

            else{

                $driver_data = (array)$driver->getDriver($_POST['data']);
                $data2 = array('driver_id'=>$_POST['data'],'driver_temp_date'=>strtotime(date('d-m-Y')),'driver_temp_action'=>3,'driver_temp_user'=>$_SESSION['userid_logined'],'name'=>'Bàn giao xe');
                    $data_temp = array_merge($driver_data, $data2);
                    $driver_temp->createDriver($data_temp);

                date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|driver|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $driver->deleteDriver($_POST['data']);

            }

            

        }

    }



    

    public function import(){

        $this->view->disableLayout();

        header('Content-Type: text/html; charset=utf-8');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->driver) || json_decode($_SESSION['user_permission_action'])->driver != "driver") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES['import']['name'] != null) {



            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $driver = $this->model->get('driverModel');



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



                            if(!$driver->getDriverByWhere(array('driver_number'=>trim($val[1])))) {

                                $driver_data = array(

                                'driver_number' => trim($val[1]),

                                'driver_name' => trim($val[2]),

                                'driver_phone' => trim($val[3]),

                                );

                                $driver->createDriver($driver_data);

                            }

                            else if($driver->getDriverByWhere(array('driver_number'=>trim($val[1])))){

                                $id_driver = $driver->getDriverByWhere(array('driver_number'=>trim($val[1])))->driver_id;

                                $driver_data = array(

                                'driver_name' => trim($val[2]),

                                'driver_phone' => trim($val[3]),

                                );

                                $driver->updateDriver($driver_data,array('driver_id' => $id_driver));

                            }





                        

                    }

                    

                    //var_dump($this->getNameDistrict($this->lib->stripUnicode($val[1])));

                    // insert





                }

                //return $this->view->redirect('transport');

            

            return $this->view->redirect('driver');

        }

        $this->view->show('driver/import');



    }

    



    public function view() {

        

        $this->view->show('handling/view');

    }



}

?>