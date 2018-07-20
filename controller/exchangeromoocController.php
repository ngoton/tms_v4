<?php

Class exchangeromoocController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Thay lắp mooc';

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
        $romoocs = $romooc_model->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));
        $this->view->data['romooc_lists'] = $romoocs;

        $data = array('order_by'=>'romooc_number','order'=>'ASC');
        if ($romooc > 0) {
            $data = array('where'=>'romooc_id = '.$romooc);
        }
        $romoocs = $romooc_model->getAllRomooc($data);
        $this->view->data['romoocs'] = $romoocs;

        

        $vehicleromooc_model = $this->model->get('vehicleromoocModel');
        $join = array('table'=>'vehicle, romooc','where'=>'vehicle = vehicle_id AND romooc = romooc_id');
        $data = array(
            'where' => '((end_time IS NULL OR end_time = 0) OR end_time >= '.strtotime(date('d-m-Y')).')',
        );
        $vehicle_romoocs = $vehicleromooc_model->getAllVehicle($data,$join);
        $this->view->data['vehicle_romoocs'] = $vehicle_romoocs;

        $this->view->show('exchangeromooc/index');

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

            $vehicleromooc = $this->model->get('vehicleromoocModel');

            $data = array(

                'vehicle' => trim($_POST['vehicle']),
                'romooc' => trim($_POST['romooc']),
                'start_time' => strtotime(str_replace('/', '-', $_POST['start_time'])),

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
            
            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$vehicleromooc->getLastVehicle()->vehicle_romooc_id."|vehicle_romooc|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'vehicle_romooc',
                'user_log_table_name' => 'Thay lắp mooc',
                'user_log_action' => 'Thêm mới',
                'user_log_data' => json_encode($data),
            );
            $user_log_model->createUser($data_log);

            echo "Thay thế thành công";

        }
    }


}

?>