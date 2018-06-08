<?php

Class vehicleworkController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý tạm dừng xe';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'vehicle_number,vehicle_work_start_date';

            $order = $this->registry->router->order ? $this->registry->router->order : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $vehicle_model = $this->model->get('vehicleworkModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $join = array('table'=>'vehicle', 'where'=>'vehicle=vehicle_id');

        $tongsodong = count($vehicle_model->getAllVehicle(null,$join));

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

            $search = '( vehicle_number LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['vehicles'] = $vehicle_model->getAllVehicle($data,$join);



        return $this->view->show('vehiclework/index');

    }


    public function addvehiclework(){
        $vehicle_model = $this->model->get('vehicleworkModel');

        if (isset($_POST['vehicle_work_start_date'])) {
            if($vehicle_model->getVehicleByWhere(array('vehicle'=>$_POST['vehicle'],'vehicle_work_start_date'=>strtotime(str_replace('/', '-', $_POST['vehicle_work_start_date']))))){
                echo 'Thông tin đã tồn tại';
                return false;
            }

            $data = array(
                'vehicle_work_start_date' => strtotime(str_replace('/', '-', $_POST['vehicle_work_start_date'])),
                'vehicle_work_end_date' => $_POST['vehicle_work_end_date']!=""?strtotime(str_replace('/', '-', $_POST['vehicle_work_end_date'])):null,
                'vehicle' => trim($_POST['vehicle']),
            );

            $ngaytruoc = strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['vehicle_work_start_date']).' -1 day')));

            if ($data['vehicle_work_end_date'] == null) {
                $vehicle_model->queryVehicle('UPDATE vehicle_work SET vehicle_work_end_date = '.$ngaytruoc.' WHERE vehicle='.$data['vehicle'].' AND (vehicle_work_end_date IS NULL OR vehicle_work_end_date = 0)');
                $vehicle_model->createVehicle($data);
            }
            else{
                $dm1 = $vehicle_model->queryVehicle('SELECT * FROM vehicle_work WHERE vehicle='.$data['vehicle'].' AND vehicle_work_start_date <= '.$data['vehicle_work_start_date'].' AND vehicle_work_end_date <= '.$data['vehicle_work_end_date'].' AND vehicle_work_end_date >= '.$data['vehicle_work_start_date'].' ORDER BY vehicle_work_end_date ASC LIMIT 1');
                $dm2 = $vehicle_model->queryVehicle('SELECT * FROM vehicle_work WHERE vehicle='.$data['vehicle'].' AND vehicle_work_end_date >= '.$data['vehicle_work_end_date'].' AND vehicle_work_start_date >= '.$data['vehicle_work_start_date'].' AND vehicle_work_start_date <= '.$data['vehicle_work_end_date'].' ORDER BY vehicle_work_end_date ASC LIMIT 1');
                $dm3 = $vehicle_model->queryVehicle('SELECT * FROM vehicle_work WHERE vehicle='.$data['vehicle'].' AND vehicle_work_start_date <= '.$data['vehicle_work_start_date'].' AND vehicle_work_end_date >= '.$data['vehicle_work_end_date'].' ORDER BY vehicle_work_end_date ASC LIMIT 1');

                if ($dm3) {
                    foreach ($dm3 as $row) {
                        $d = array(
                            'vehicle_work_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['vehicle_work_start_date']).' -1 day'))),
                            );
                        $vehicle_model->updateVehicle($d,array('vehicle_work_id'=>$row->vehicle_work_id));

                        $c = array(
                            'vehicle' => $row->vehicle,
                            'vehicle_work_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['vehicle_work_end_date']).' +1 day'))),
                            'vehicle_work_end_date' => $row->vehicle_work_end_date,
                            );
                        $vehicle_model->createVehicle($c);

                    }
                    $vehicle_model->createVehicle($data);

                }
                else if ($dm1 || $dm2) {
                    if($dm1){
                        foreach ($dm1 as $row) {
                            $d = array(
                                'vehicle_work_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['vehicle_work_start_date']).' -1 day'))),
                                );
                            $vehicle_model->updateVehicle($d,array('vehicle_work_id'=>$row->vehicle_work_id));
                        }
                    }
                    if($dm2){
                        foreach ($dm2 as $row) {
                            $d = array(
                                'vehicle_work_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['vehicle_work_end_date']).' +1 day'))),
                                );
                            $vehicle_model->updateVehicle($d,array('vehicle_work_id'=>$row->vehicle_work_id));
                        }
                    }
                    $vehicle_model->createVehicle($data);
                }
                else{
                    $vehicle_model->createVehicle($data);
                }
            }
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$vehicle_model->getLastVehicle()->vehicle_work_id."|vehicle_work|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'vehicle_work',
                'user_log_table_name' => 'Tạm dừng xe',
                'user_log_action' => 'Thêm mới',
                'user_log_data' => json_encode($data),
            );
            $user_log_model->createUser($data_log);


            echo "Thêm thành công";
        }

    }

    public function add(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehiclework) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới tạm dừng xe';

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        return $this->view->show('vehiclework/add');
    }

    public function editvehiclework(){
        $vehicle_model = $this->model->get('vehicleworkModel');

        if (isset($_POST['vehicle_work_id'])) {
            $id = $_POST['vehicle_work_id'];
            
            $data = array(
                'vehicle_work_start_date' => strtotime(str_replace('/', '-', $_POST['vehicle_work_start_date'])),
                'vehicle_work_end_date' => $_POST['vehicle_work_end_date']!=""?strtotime(str_replace('/', '-', $_POST['vehicle_work_end_date'])):null,
                'vehicle' => trim($_POST['vehicle']),
            );

            $vehicle_model->updateVehicle($data,array('vehicle_work_id'=>$id));
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|vehicle_work|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'vehicle_work',
                'user_log_table_name' => 'Tạm dừng xe',
                'user_log_action' => 'Cập nhật',
                'user_log_data' => json_encode($data),
            );
            $user_log_model->createUser($data_log);


            echo "Cập nhật thành công";
        }
    }

    public function edit($id){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehiclework) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('vehiclework');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật tạm dừng xe';

        $vehicle_model = $this->model->get('vehicleworkModel');

        $vehicle_data = $vehicle_model->getVehicle($id);

        $this->view->data['vehicle_data'] = $vehicle_data;

        if (!$vehicle_data) {

            $this->view->redirect('vehiclework');

        }

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        return $this->view->show('vehiclework/edit');

    }

    public function view($id){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('vehiclework');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin tạm dừng xe';

        $vehicle_model = $this->model->get('vehicleworkModel');

        $vehicle_data = $vehicle_model->getVehicle($id);

        $this->view->data['vehicle_data'] = $vehicle_data;

        if (!$vehicle_data) {

            $this->view->redirect('vehiclework');

        }

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        return $this->view->show('vehiclework/view');

    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->vehiclework) || json_decode($_SESSION['user_permission_action'])->vehiclework != "vehiclework") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $vehicle_model = $this->model->get('vehicleworkModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $vehicle_model->deleteVehicle($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|vehicle_work|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'vehicle_work',
                    'user_log_table_name' => 'Tạm dừng xe',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $vehicle_model->deleteVehicle($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|vehicle_work|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'vehicle_work',
                    'user_log_table_name' => 'Tạm dừng xe',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importvehiclework(){
        if (isset($_FILES['import']['name'])) {
            $total = count($_FILES['import']['name']);
            for( $i=0 ; $i < $total ; $i++ ) {
              $tmpFilePath = $_FILES['import']['name'][$i];
              echo $tmpFilePath;
            }
        }
    }
    public function import(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehiclework) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('vehiclework/import');

    }


}

?>