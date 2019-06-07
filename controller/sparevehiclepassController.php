<?php

Class sparevehiclepassController Extends baseController {


    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->sparevehiclepass) || json_decode($_SESSION['user_permission_action'])->sparevehiclepass != "sparevehiclepass") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý vật tư thiết bị';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $xe = $_POST['vehicle'];
            $mooc = $_POST['romooc'];
        }
        else{
            $xe = 0;
            $mooc = 0;
        }

        $vehicle_model = $this->model->get('vehicleModel');
        $romooc_model = $this->model->get('romoocModel');
        $spare_stock_model = $this->model->get('sparestockModel');
        $sparevehicle_model = $this->model->get('sparevehicleModel');
        
        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $this->view->data['vehicles'] = $vehicles;
        
        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));
        $this->view->data['romoocs'] = $romoocs;

        $join = array('table'=>'vehicle,spare_part','where'=>'vehicle = vehicle_id AND spare_part=spare_part_id');

        $qr = 'SELECT *,SUM(spare_part_number) as total FROM vehicle,spare_part,spare_vehicle WHERE vehicle = vehicle_id AND spare_part=spare_part_id AND (end_time IS NULL OR end_time = 0) AND vehicle = '.$xe.' GROUP BY vehicle,spare_part';
        $spare_vehicles = $sparevehicle_model->queryStock($qr);
        $this->view->data['spare_vehicles'] = $spare_vehicles;
        ///////////////// Lấy phụ tùng kèm số lượng đã dùng cho từng đầu xe

         $data = array(
            'where' => '(end_time > 0) AND vehicle = '.$xe,
        );
        $spare_vehicles = $sparevehicle_model->getAllStock($data,$join);
        $spare_vehicle_outs = array();
        foreach ($spare_vehicles as $spare) {
            $spare_vehicle_outs[$spare->vehicle][$spare->spare_part] = isset($spare_vehicle_outs[$spare->vehicle][$spare->spare_part])?$spare_vehicle_outs[$spare->vehicle][$spare->spare_part]+$spare->spare_part_number:$spare->spare_part_number;
        }
        $this->view->data['spare_vehicle_outs'] = $spare_vehicle_outs;
        ///////////////// Phụ tùng đã thay ra kèm số lượng theo đầu xe

        $join = array('table'=>'romooc,spare_part','where'=>'romooc = romooc_id AND spare_part=spare_part_id');

        $qr = 'SELECT *,SUM(spare_part_number) as total FROM romooc,spare_part,spare_vehicle WHERE romooc = romooc_id AND spare_part=spare_part_id AND (end_time IS NULL OR end_time = 0) AND romooc = '.$mooc.' GROUP BY romooc,spare_part';
        $spare_romoocs = $sparevehicle_model->queryStock($qr);
        $this->view->data['spare_romoocs'] = $spare_romoocs;
        ///////////////// Lấy phụ tùng kèm số lượng đã dùng cho từng mooc

        $data = array(
            'where' => '(end_time > 0) AND romooc = '.$mooc,
        );
        $spare_romoocs = $sparevehicle_model->getAllStock($data,$join);
        $spare_romooc_outs = array();
        foreach ($spare_romoocs as $spare) {
            $spare_romooc_outs[$spare->romooc][$spare->spare_part] = isset($spare_romooc_outs[$spare->romooc][$spare->spare_part])?$spare_romooc_outs[$spare->romooc][$spare->spare_part]+$spare->spare_part_number:$spare->spare_part_number;
        }
        $this->view->data['spare_romooc_outs'] = $spare_romooc_outs;
        ///////////////// Phụ tùng đã thay ra kèm số lượng theo mooc

        $this->view->data['xe'] = $xe;
        $this->view->data['mooc'] = $mooc;

        $this->view->show('sparevehiclepass/index');

    }



    public function exchange(){
        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparevehiclepass) || json_decode($_SESSION['user_permission_action'])->sparevehiclepass != "sparevehiclepass") {

            return $this->view->redirect('user/login');

        }
        if (isset($_POST['yes'])) {

            $sparevehicle = $this->model->get('sparevehicleModel');

            $vehicle_in = $_POST['vehicle_in'];
            $vehicle_out = $_POST['vehicle_out'];
            $romooc_in = $_POST['romooc_in'];
            $romooc_out = $_POST['romooc_out'];
            $spare_out = $_POST['spare_out'];
            $start_time = $_POST['start_time'];

            foreach ($spare_out as $v) {

                if ($vehicle_out > 0) {
                    $data = array(

                        'vehicle' => $vehicle_out,
                        'end_time' => strtotime($start_time),
                        'spare_part' => $v['spare'],
                        'spare_part_number' => $v['num'],

                    );
                    $sparevehicle->createStock($data);
                }
                else if ($romooc_out > 0) {
                    $data = array(

                        'romooc' => $romooc_out,
                        'end_time' => strtotime($start_time),
                        'spare_part' => $v['spare'],
                        'spare_part_number' => $v['num'],

                    );
                    $sparevehicle->createStock($data);
                }

                if ($vehicle_in > 0) {
                    $data = array(

                        'vehicle' => $vehicle_in,
                        'start_time' => strtotime($start_time),
                        'spare_part' => $v['spare'],
                        'spare_part_number' => $v['num'],

                    );
                    $sparevehicle->createStock($data);
                }
                else if ($romooc_in > 0) {
                    $data = array(

                        'romooc' => $romooc_in,
                        'start_time' => strtotime($start_time),
                        'spare_part' => $v['spare'],
                        'spare_part_number' => $v['num'],

                    );
                    $sparevehicle->createStock($data);
                }
            }


            echo "Thay thế thành công";

        }
    }



}

?>