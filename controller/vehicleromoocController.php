<?php

Class vehicleromoocController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý thay lắp mooc';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'vehicle_number,start_time';

            $order = $this->registry->router->order ? $this->registry->router->order : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $vehicle_romooc_model = $this->model->get('vehicleromoocModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        $join = array('table'=>'vehicle,romooc', 'where'=>'vehicle=vehicle_id AND romooc=romooc_id');

        if (isset($_POST['filter'])) {
            if (isset($_POST['vehicle'])) {
                $data['where'] .= ' AND vehicle IN ('.implode(',',$_POST['vehicle']).')';
            }
            if (isset($_POST['romooc'])) {
                $data['where'] .= ' AND romooc IN ('.implode(',',$_POST['romooc']).')';
            }
            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($vehicle_romooc_model->getAllVehicle($data,$join));

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
            'where'=>'1=1',

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            );

        if (isset($_POST['filter'])) {
            if (isset($_POST['vehicle'])) {
                $data['where'] .= ' AND vehicle IN ('.implode(',',$_POST['vehicle']).')';
            }
            if (isset($_POST['romooc'])) {
                $data['where'] .= ' AND romooc IN ('.implode(',',$_POST['romooc']).')';
            }
            $this->view->data['filter'] = 1;
        }

        if ($keyword != '') {

            $search = '( vehicle_number LIKE "%'.$keyword.'%" OR romooc_number LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['vehicles'] = $vehicle_romooc_model->getAllVehicle($data,$join);



        return $this->view->show('vehicleromooc/index');

    }


    public function addvehicleromooc(){
        $vehicle_romooc_model = $this->model->get('vehicleromoocModel');

        if (isset($_POST['start_time'])) {
            if($vehicle_romooc_model->getVehicleByWhere(array('vehicle'=>$_POST['vehicle'],'start_time'=>strtotime(str_replace('/', '-', $_POST['start_time']))))){
                echo 'Thông tin đã tồn tại';
                return false;
            }

            $data = array(
                'start_time' => strtotime(str_replace('/', '-', $_POST['start_time'])),
                'end_time' => $_POST['end_time']!=""?strtotime(str_replace('/', '-', $_POST['end_time'])):null,
                'vehicle' => trim($_POST['vehicle']),
                'romooc' => trim($_POST['romooc']),
            );

            $ngaytruoc = strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['start_time']).' -1 day')));

            if ($data['end_time'] == null) {
                $vehicle_romooc_model->queryVehicle('UPDATE vehicleromooc SET end_time = '.$ngaytruoc.' WHERE vehicle='.$data['vehicle'].' AND (end_time IS NULL OR end_time = 0)');
                $vehicle_romooc_model->createVehicle($data);
            }
            else{
                $dm1 = $vehicle_romooc_model->queryVehicle('SELECT * FROM vehicleromooc WHERE vehicle='.$data['vehicle'].' AND start_time <= '.$data['start_time'].' AND end_time <= '.$data['end_time'].' AND end_time >= '.$data['start_time'].' ORDER BY end_time ASC LIMIT 1');
                $dm2 = $vehicle_romooc_model->queryVehicle('SELECT * FROM vehicleromooc WHERE vehicle='.$data['vehicle'].' AND end_time >= '.$data['end_time'].' AND start_time >= '.$data['start_time'].' AND start_time <= '.$data['end_time'].' ORDER BY end_time ASC LIMIT 1');
                $dm3 = $vehicle_romooc_model->queryVehicle('SELECT * FROM vehicleromooc WHERE vehicle='.$data['vehicle'].' AND start_time <= '.$data['start_time'].' AND end_time >= '.$data['end_time'].' ORDER BY end_time ASC LIMIT 1');

                if ($dm3) {
                    foreach ($dm3 as $row) {
                        $d = array(
                            'end_time' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['start_time']).' -1 day'))),
                            );
                        $vehicle_romooc_model->updateVehicle($d,array('vehicle_romooc_id'=>$row->vehicle_romooc_id));

                        $c = array(
                            'vehicle' => $row->vehicle,
                            'romooc' => $row->romooc,
                            'start_time' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['end_time']).' +1 day'))),
                            'end_time' => $row->end_time,
                            );
                        $vehicle_romooc_model->createVehicle($c);

                    }
                    $vehicle_romooc_model->createVehicle($data);

                }
                else if ($dm1 || $dm2) {
                    if($dm1){
                        foreach ($dm1 as $row) {
                            $d = array(
                                'end_time' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['start_time']).' -1 day'))),
                                );
                            $vehicle_romooc_model->updateVehicle($d,array('vehicle_romooc_id'=>$row->vehicle_romooc_id));
                        }
                    }
                    if($dm2){
                        foreach ($dm2 as $row) {
                            $d = array(
                                'start_time' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['end_time']).' +1 day'))),
                                );
                            $vehicle_romooc_model->updateVehicle($d,array('vehicle_romooc_id'=>$row->vehicle_romooc_id));
                        }
                    }
                    $vehicle_romooc_model->createVehicle($data);
                }
                else{
                    $vehicle_romooc_model->createVehicle($data);
                }
            }
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$vehicle_romooc_model->getLastVehicle()->vehicle_romooc_id."|vehicle_romooc|".implode("-",$data)."\n"."\r\n";
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


            echo "Thêm thành công";
        }

    }

    public function add(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehicleromooc) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới thay lắp mooc';

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        return $this->view->show('vehicleromooc/add');
    }

    public function editvehicleromooc(){
        $vehicle_romooc_model = $this->model->get('vehicleromoocModel');

        if (isset($_POST['vehicle_romooc_id'])) {
            $id = $_POST['vehicle_romooc_id'];
            
            $data = array(
                'start_time' => strtotime(str_replace('/', '-', $_POST['start_time'])),
                'end_time' => $_POST['end_time']!=""?strtotime(str_replace('/', '-', $_POST['end_time'])):null,
                'vehicle' => trim($_POST['vehicle']),
                'romooc' => trim($_POST['romooc']),
            );

            $vehicle_romooc_model->updateVehicle($data,array('vehicle_romooc_id'=>$id));
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|vehicle_romooc|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'vehicle_romooc',
                'user_log_table_name' => 'Thay lắp mooc',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehicleromooc) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('vehicleromooc');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật thay lắp mooc';

        $vehicle_romooc_model = $this->model->get('vehicleromoocModel');

        $vehicle_romooc_data = $vehicle_romooc_model->getVehicle($id);

        $this->view->data['vehicle_romooc_data'] = $vehicle_romooc_data;

        if (!$vehicle_romooc_data) {

            $this->view->redirect('vehicleromooc');

        }

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        return $this->view->show('vehicleromooc/edit');

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

            $this->view->redirect('vehicleromooc');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin thay lắp mooc';

        $vehicle_romooc_model = $this->model->get('vehicleromoocModel');

        $vehicle_romooc_data = $vehicle_romooc_model->getVehicle($id);

        $this->view->data['vehicle_romooc_data'] = $vehicle_romooc_data;

        if (!$vehicle_romooc_data) {

            $this->view->redirect('vehicleromooc');

        }

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        return $this->view->show('vehicleromooc/view');

    }
    public function viewromooc(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin thay lắp mooc';

        $id = $_GET['id'];

        $info = explode('~', $id);

        $vehicle_romooc_model = $this->model->get('vehicleromoocModel');

        $data = array(
            'where'=>'vehicle = '.$info[0].' AND start_time <= '.strtotime(str_replace('/', '-', $info[1])).' AND (end_time IS NULL OR end_time=0 OR end_time >= '.strtotime(str_replace('/', '-', $info[1])).')',
            'order_by'=>'start_time',
            'order'=>'DESC',
            'limit'=>1
        );

        $vehicle_romoocs = $vehicle_romooc_model->getAllVehicle($data);
        foreach ($vehicle_romoocs as $vehicle_romooc) {
            $vehicle_romooc_data = $vehicle_romooc;
        }

        $this->view->data['vehicle_romooc_data'] = $vehicle_romooc_data;

        if (!$vehicle_romooc_data) {

            $this->view->redirect('vehicleromooc');

        }

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        return $this->view->show('vehicleromooc/viewromooc');

    }

    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('vehicleromooc/filter');
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->vehicleromooc) || json_decode($_SESSION['user_permission_action'])->vehicleromooc != "vehicleromooc") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $vehicle_romooc_model = $this->model->get('vehicleromoocModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $vehicle_romooc_model->deleteVehicle($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|vehicle_romooc|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'vehicle_romooc',
                    'user_log_table_name' => 'Thay lắp mooc',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $vehicle_romooc_model->deleteVehicle($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|vehicle_romooc|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'vehicle_romooc',
                    'user_log_table_name' => 'Thay lắp mooc',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importvehicleromooc(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehicleromooc) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('vehicleromooc/import');

    }


}

?>