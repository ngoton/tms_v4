<?php

Class liftController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý phí nâng hạ';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'place_name,lift_start_date';

            $order = $this->registry->router->order ? $this->registry->router->order : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $lift_model = $this->model->get('liftModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        $join = array('table'=>'place', 'where'=>'lift_place=place_id','join'=>'LEFT JOIN');

        if (isset($_POST['filter'])) {
            if (isset($_POST['lift_place'])) {
                $data['where'] .= ' AND lift_place IN ('.implode(',',$_POST['lift_place']).')';
            }
            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($lift_model->getAllLift($data,$join));

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
            if (isset($_POST['lift_place'])) {
                $data['where'] .= ' AND lift_place IN ('.implode(',',$_POST['lift_place']).')';
            }
            $this->view->data['filter'] = 1;
        }

        if ($keyword != '') {

            $search = '( place_name LIKE "%'.$keyword.'%" OR customer_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['lifts'] = $lift_model->getAllLift($data,$join);



        return $this->view->show('lift/index');

    }


    public function addlift(){
        $lift_model = $this->model->get('liftModel');

        if (isset($_POST['lift_start_date'])) {
            if($lift_model->getVehicleByWhere(array('lift_place'=>$_POST['lift_place'],'lift_start_date'=>strtotime(str_replace('/', '-', $_POST['lift_start_date']))))){
                echo 'Thông tin đã tồn tại';
                return false;
            }

            $data = array(
                'lift_start_date' => strtotime(str_replace('/', '-', $_POST['lift_start_date'])),
                'lift_end_date' => $_POST['lift_end_date']!=""?strtotime(str_replace('/', '-', $_POST['lift_end_date'])):null,
                'lift_place' => trim($_POST['lift_place']),
                'lift_customer' => trim($_POST['lift_customer']),
                'lift_on' => str_replace(',', '', $_POST['lift_on']),
                'lift_off' => str_replace(',', '', $_POST['lift_off']),
            );

            $ngaytruoc = strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['lift_start_date']).' -1 day')));

            if ($data['lift_end_date'] == null) {
                $lift_model->queryLift('UPDATE lift SET lift_end_date = '.$ngaytruoc.' WHERE lift_place='.$data['lift_place'].' AND (lift_end_date IS NULL OR lift_end_date = 0)');
                $lift_model->createLift($data);
            }
            else{
                $dm1 = $lift_model->queryLift('SELECT * FROM lift WHERE lift_place='.$data['lift_place'].' AND lift_start_date <= '.$data['lift_start_date'].' AND lift_end_date <= '.$data['lift_end_date'].' AND lift_end_date >= '.$data['lift_start_date'].' ORDER BY lift_end_date ASC LIMIT 1');
                $dm2 = $lift_model->queryLift('SELECT * FROM lift WHERE lift_place='.$data['lift_place'].' AND lift_end_date >= '.$data['lift_end_date'].' AND lift_start_date >= '.$data['lift_start_date'].' AND lift_start_date <= '.$data['lift_end_date'].' ORDER BY lift_end_date ASC LIMIT 1');
                $dm3 = $lift_model->queryLift('SELECT * FROM lift WHERE lift_place='.$data['lift_place'].' AND lift_start_date <= '.$data['lift_start_date'].' AND lift_end_date >= '.$data['lift_end_date'].' ORDER BY lift_end_date ASC LIMIT 1');

                if ($dm3) {
                    foreach ($dm3 as $row) {
                        $d = array(
                            'lift_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['lift_start_date']).' -1 day'))),
                            );
                        $lift_model->updateLift($d,array('lift_id'=>$row->lift_id));

                        $c = array(
                            'lift_place' => $row->lift_place,
                            'lift_customer' => $row->lift_customer,
                            'lift_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['lift_end_date']).' +1 day'))),
                            'lift_end_date' => $row->lift_end_date,
                            'lift_on' => $row->lift_on,
                            'lift_off' => $row->lift_off,
                            );
                        $lift_model->createLift($c);

                    }
                    $lift_model->createLift($data);

                }
                else if ($dm1 || $dm2) {
                    if($dm1){
                        foreach ($dm1 as $row) {
                            $d = array(
                                'lift_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['lift_start_date']).' -1 day'))),
                                );
                            $lift_model->updateLift($d,array('lift_id'=>$row->lift_id));
                        }
                    }
                    if($dm2){
                        foreach ($dm2 as $row) {
                            $d = array(
                                'lift_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['lift_end_date']).' +1 day'))),
                                );
                            $lift_model->updateLift($d,array('lift_id'=>$row->lift_id));
                        }
                    }
                    $lift_model->createLift($data);
                }
                else{
                    $lift_model->createLift($data);
                }
            }
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$lift_model->getLastLift()->lift_id."|lift|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'lift',
                'user_log_table_name' => 'Phí nâng hạ',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->lift) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới phí nâng hạ';

        $place = $this->model->get('placeModel');

        $this->view->data['places'] = $place->getAllPlace(array('where'=>'place_port = 1','order_by'=>'place_name','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('order_by'=>'customer_name','order'=>'ASC'));

        return $this->view->show('lift/add');
    }

    public function editlift(){
        $lift_model = $this->model->get('liftModel');

        if (isset($_POST['lift_id'])) {
            $id = $_POST['lift_id'];
            
            $data = array(
                'lift_start_date' => strtotime(str_replace('/', '-', $_POST['lift_start_date'])),
                'lift_end_date' => $_POST['lift_end_date']!=""?strtotime(str_replace('/', '-', $_POST['lift_end_date'])):null,
                'lift_place' => trim($_POST['lift_place']),
                'lift_customer' => trim($_POST['lift_customer']),
                'lift_on' => str_replace(',', '', $_POST['lift_on']),
                'lift_off' => str_replace(',', '', $_POST['lift_off']),
            );

            $lift_model->updateLift($data,array('lift_id'=>$id));
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|lift|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'lift',
                'user_log_table_name' => 'Phí nâng hạ',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->lift) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('lift');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật phí nâng hạ';

        $lift_model = $this->model->get('liftModel');

        $lift_data = $lift_model->getLift($id);

        $this->view->data['lift_data'] = $lift_data;

        if (!$lift_data) {

            $this->view->redirect('lift');

        }

        $place = $this->model->get('placeModel');

        $this->view->data['places'] = $place->getAllPlace(array('where'=>'place_port = 1','order_by'=>'place_name','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('order_by'=>'customer_name','order'=>'ASC'));

        return $this->view->show('lift/edit');

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

            $this->view->redirect('lift');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin phí nâng hạ';

        $lift_model = $this->model->get('liftModel');

        $lift_data = $lift_model->getLift($id);

        $this->view->data['lift_data'] = $lift_data;

        if (!$lift_data) {

            $this->view->redirect('lift');

        }

        $place = $this->model->get('placeModel');

        $this->view->data['places'] = $place->getAllPlace(array('where'=>'place_port = 1','order_by'=>'place_name','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('order_by'=>'customer_name','order'=>'ASC'));

        return $this->view->show('lift/view');

    }
    public function viewlift(){

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
        $this->view->data['title'] = 'Thông tin phí nâng hạ';

        $id = $_GET['id'];

        $info = explode('~', $id);

        $lift_model = $this->model->get('liftModel');

        $data = array(
            'where'=>'lift_place = '.$info[0].' AND lift_start_date <= '.strtotime(str_replace('/', '-', $info[1])).' AND (lift_end_date IS NULL OR lift_end_date=0 OR lift_end_date >= '.strtotime(str_replace('/', '-', $info[1])).')',
            'order_by'=>'lift_start_date',
            'order'=>'DESC',
            'limit'=>1
        );

        $lifts = $lift_model->getAllLift($data);
        foreach ($lifts as $lift) {
            $lift_data = $lift;
        }

        $this->view->data['lift_data'] = $lift_data;

        if (!$lift_data) {

            $this->view->redirect('lift');

        }

        $place = $this->model->get('placeModel');

        $this->view->data['places'] = $place->getAllPlace(array('where'=>'place_port = 1','order_by'=>'place_name','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('order_by'=>'customer_name','order'=>'ASC'));

        return $this->view->show('lift/view');

    }

    public function getlift(){

        $vehicle = $_GET['vehicle'];

        $date = $_GET['date'];

        $lift_model = $this->model->get('liftModel');

        $data = array(
            'where'=>'lift_place = '.$vehicle.' AND lift_start_date <= '.strtotime(str_replace('/', '-', $date)).' AND (lift_end_date IS NULL OR lift_end_date=0 OR lift_end_date >= '.strtotime(str_replace('/', '-', $date)).')',
            'order_by'=>'lift_start_date',
            'order'=>'DESC',
            'limit'=>1
        );
        $join = array('table'=>'customer','where'=>'lift_customer=customer_id');

        $lifts = $lift_model->getAllLift($data,$join);
        $lift_data = array(
            'customer_id'=>null,
            'customer_name'=>null,
        );
        foreach ($lifts as $lift) {
            $lift_data['customer_id'] = $lift->customer_id;
            $lift_data['customer_name'] = $lift->customer_name;
        }

        echo json_encode($lift_data);

    }

    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $place = $this->model->get('placeModel');

        $this->view->data['places'] = $place->getAllPlace(array('where'=>'place_port = 1','order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('lift/filter');
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->lift) || json_decode($_SESSION['user_permission_action'])->lift != "lift") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $lift_model = $this->model->get('liftModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $lift_model->deleteLift($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|lift|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'lift',
                    'user_log_table_name' => 'Phí nâng hạ',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $lift_model->deleteLift($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|lift|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'lift',
                    'user_log_table_name' => 'Phí nâng hạ',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importlift(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->lift) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('lift/import');

    }


}

?>