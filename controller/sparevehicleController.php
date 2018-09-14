<?php

Class sparevehicleController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Thay lắp phụ tùng';

        $vehicle_model = $this->model->get('vehicleModel');
        $romooc_model = $this->model->get('romoocModel');
        $export_stock_model = $this->model->get('exportstockModel');
        $spare_stock_model = $this->model->get('sparestockModel');
        $sparevehicle_model = $this->model->get('sparevehicleModel');
        $house_model = $this->model->get('houseModel');

        $export_stock = 0;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $vehicle = $_POST['vehicle'];
            $romooc = $_POST['romooc'];
            $house = $_POST['house'];
            $export_stock = $_POST['export_stock'];
            $tab_active = $_POST['tab_active'];
        }
        else{
            $vehicle = $vehicle_model->getLastVehicle()->vehicle_id;
            $romooc = $romooc_model->getLastRomooc()->romooc_id;
            $house = $house_model->getLastHouse()->house_id;
            $export_stocks = $export_stock_model->getAllStock(array('where'=>'export_stock_house='.$house,'order_by'=>'export_stock_id DESC','limit'=>1));
            foreach ($export_stocks as $key) {
                $export_stock = $key->export_stock_id;
            }
            $tab_active = 1;
        }

        if (isset($_POST['house_change'])) {
            $export_stocks = $export_stock_model->getAllStock(array('where'=>'export_stock_house='.$house,'order_by'=>'export_stock_id DESC','limit'=>1));
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
            'where'=>'export_stock_house='.$house,
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
        $romooc_selected = $romooc_model->getRomooc($romooc);
        $this->view->data['romooc_selected'] = $romooc_selected;


        
        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $this->view->data['vehicles'] = $vehicles;
        
        $romoocs = $romooc_model->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));
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
        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->exchangeromooc) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (isset($_POST['yes'])) {

            $sparevehicle = $this->model->get('sparevehicleModel');
            $sparedrap = $this->model->get('sparedrapModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['vehicle_in'])) {
                $vehicle_in = $_POST['vehicle_in'];
                foreach ($vehicle_in as $v) {
                    $data = array(

                        'vehicle' => trim($_POST['vehicle']),
                        'start_time' => strtotime(str_replace('/', '-', $_POST['start_time'])),
                        'spare_part' => $v['vehicle_in_id'],
                        'spare_part_number' => $v['vehicle_in_num'],
                        'export_stock' => $_POST['export_stock'],

                    );
                    $sparevehicle->createStock($data);

                    $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$sparevehicle->getLastStock()->spare_vehicle_id."|spare_vehicle|".implode("-",$data)."\n"."\r\n";
                    $this->lib->ghi_file("action_logs.txt",$text);

                    $data_log = array(
                        'user_log' => $_SESSION['userid_logined'],
                        'user_log_date' => time(),
                        'user_log_table' => 'spare_vehicle',
                        'user_log_table_name' => 'Thay lắp phụ tùng',
                        'user_log_action' => 'Thêm mới',
                        'user_log_data' => json_encode($data),
                    );
                    $user_log_model->createUser($data_log);
                }
            }
            if (isset($_POST['romooc_in'])) {
                $romooc_in = $_POST['romooc_in'];
                foreach ($romooc_in as $v) {
                    $data = array(

                        'romooc' => trim($_POST['romooc']),
                        'start_time' => strtotime(str_replace('/', '-', $_POST['start_time'])),
                        'spare_part' => $v['romooc_in_id'],
                        'spare_part_number' => $v['romooc_in_num'],
                        'export_stock' => $_POST['export_stock'],

                    );
                    $sparevehicle->createStock($data);

                    $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$sparevehicle->getLastStock()->spare_vehicle_id."|spare_vehicle|".implode("-",$data)."\n"."\r\n";
                    $this->lib->ghi_file("action_logs.txt",$text);

                    $data_log = array(
                        'user_log' => $_SESSION['userid_logined'],
                        'user_log_date' => time(),
                        'user_log_table' => 'spare_vehicle',
                        'user_log_table_name' => 'Thay lắp phụ tùng',
                        'user_log_action' => 'Thêm mới',
                        'user_log_data' => json_encode($data),
                    );
                    $user_log_model->createUser($data_log);
                }
            }
            if (isset($_POST['vehicle_out'])) {
                $vehicle_out = $_POST['vehicle_out'];
                foreach ($vehicle_out as $v) {
                    $data = array(

                        'vehicle' => trim($_POST['vehicle']),
                        'end_time' => strtotime(str_replace('/', '-', $_POST['end_time'])),
                        'spare_part' => $v['vehicle_out_id'],
                        'spare_part_number' => $v['vehicle_out_num'],

                    );
                    $sparevehicle->createStock($data);

                    $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$sparevehicle->getLastStock()->spare_vehicle_id."|spare_vehicle|".implode("-",$data)."\n"."\r\n";
                    $this->lib->ghi_file("action_logs.txt",$text);

                    $data_log = array(
                        'user_log' => $_SESSION['userid_logined'],
                        'user_log_date' => time(),
                        'user_log_table' => 'spare_vehicle',
                        'user_log_table_name' => 'Thay lắp phụ tùng',
                        'user_log_action' => 'Thay ra',
                        'user_log_data' => json_encode($data),
                    );
                    $user_log_model->createUser($data_log);

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
                        'end_time' => strtotime(str_replace('/', '-', $_POST['end_time'])),
                        'spare_part' => $v['romooc_out_id'],
                        'spare_part_number' => $v['romooc_out_num'],

                    );
                    $sparevehicle->createStock($data);

                    $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$sparevehicle->getLastStock()->spare_vehicle_id."|spare_vehicle|".implode("-",$data)."\n"."\r\n";
                    $this->lib->ghi_file("action_logs.txt",$text);

                    $data_log = array(
                        'user_log' => $_SESSION['userid_logined'],
                        'user_log_date' => time(),
                        'user_log_table' => 'spare_vehicle',
                        'user_log_table_name' => 'Thay lắp phụ tùng',
                        'user_log_action' => 'Thay ra',
                        'user_log_data' => json_encode($data),
                    );
                    $user_log_model->createUser($data_log);

                    $data = array(

                        'spare_vehicle' => $sparevehicle->getLastStock()->spare_vehicle_id,
                        'spare_part' => $v['romooc_out_id'],
                        'spare_part_number' => $v['romooc_out_num'],

                    );
                    $sparedrap->createStock($data);
                }
            }
            

            

            echo "Thay thế thành công";

        }
    }


}

?>