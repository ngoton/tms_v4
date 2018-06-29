<?php

Class checkingcostController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý phí kiểm định';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;
            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;
            $nv = isset($_POST['nv']) ? $_POST['nv'] : null;
            $tha = isset($_POST['tha']) ? $_POST['tha'] : null;
            $na = isset($_POST['na']) ? $_POST['na'] : null;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'checking_cost_date';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

            $batdau = '01/'.date('m/Y');
            $ketthuc = date('t/m/Y');
            $nv = 1;
            $tha = date('m');
            $na = date('Y');

        }

        $ngaybatdau = strtotime(str_replace('/', '-', $batdau));
        $ngayketthuc = strtotime(str_replace('/', '-', $ketthuc). ' + 1 days');
        $tha = (int)date('m',$ngaybatdau);
        $na = (int)date('Y',$ngaybatdau);
        $nv = ceil($tha/3);

        $vehicle = $this->model->get('vehicleModel');

        $vehicles = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $vehicle_data = array();
        foreach ($vehicles as $vehicle) {
            $vehicle_data[$vehicle->vehicle_id] = $vehicle->vehicle_number;
        }
        $this->view->data['vehicle_data'] = $vehicle_data;

        $romooc = $this->model->get('romoocModel');

        $romoocs = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));
        $romooc_data = array();
        foreach ($romoocs as $romooc) {
            $romooc_data[$romooc->romooc_id] = $romooc->romooc_number;
        }
        $this->view->data['romooc_data'] = $romooc_data;


        $checkingcost_model = $this->model->get('checkingcostModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        if ($batdau!="") {
            $data['where'] .= ' AND checking_cost_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND checking_cost_date < '.$ngayketthuc;
        }

        $join = array('table'=>'customer', 'where'=>'checking_cost_customer = customer_id','join'=>'LEFT JOIN');

        if (isset($_POST['filter'])) {
            if (isset($_POST['checking_cost_customer'])) {
                $data['where'] .= ' AND checking_cost_customer IN ('.implode(',',$_POST['checking_cost_customer']).')';
            }
            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($checkingcost_model->getAllCost($data,$join));

        $tongsotrang = ceil($tongsodong / $sonews);

        



        $this->view->data['page'] = $page;

        $this->view->data['order_by'] = $order_by;

        $this->view->data['order'] = $order;

        $this->view->data['keyword'] = $keyword;

        $this->view->data['limit'] = $limit;

        $this->view->data['pagination_stages'] = $pagination_stages;

        $this->view->data['tongsotrang'] = $tongsotrang;

        $this->view->data['sonews'] = $sonews;

        $this->view->data['batdau'] = $batdau;
        $this->view->data['ketthuc'] = $ketthuc;
        $this->view->data['nv'] = $nv;
        $this->view->data['tha'] = $tha;
        $this->view->data['na'] = $na;



        $data = array(
            'where'=>'1=1',

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            );

        if ($batdau!="") {
            $data['where'] .= ' AND checking_cost_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND checking_cost_date < '.$ngayketthuc;
        }

        if (isset($_POST['filter'])) {
            if (isset($_POST['checking_cost_customer'])) {
                $data['where'] .= ' AND checking_cost_customer IN ('.implode(',',$_POST['checking_cost_customer']).')';
            }
            $this->view->data['filter'] = 1;
        }

        if ($keyword != '') {

            $search = '( checking_cost_code LIKE "%'.$keyword.'%" OR customer_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['checking_costs'] = $checkingcost_model->getAllCost($data,$join);



        return $this->view->show('checkingcost/index');

    }


    public function addcheckingcost(){
        $checkingcost_model = $this->model->get('checkingcostModel');

        if (isset($_POST['checking_cost_price'])) {
           

            $data = array(
                'checking_cost_date' => strtotime(str_replace('/', '-', $_POST['checking_cost_date'])),
                'checking_cost_start_date' => strtotime(str_replace('/', '-', $_POST['checking_cost_start_date'])),
                'checking_cost_end_date' => strtotime(str_replace('/', '-', $_POST['checking_cost_end_date'])),
                'checking_cost_vehicle' => isset($_POST['checking_cost_vehicle'])?implode(',',$_POST['checking_cost_vehicle']):null,
                'checking_cost_romooc' => isset($_POST['checking_cost_romooc'])?implode(',',$_POST['checking_cost_romooc']):null,
                'checking_cost_customer' => trim($_POST['checking_cost_customer']),
                'checking_cost_price' => str_replace(',', '', $_POST['checking_cost_price']),
                'checking_cost_vat' => str_replace(',', '', $_POST['checking_cost_vat']),
                'checking_cost_comment' => trim($_POST['checking_cost_comment']),
                'checking_cost_code' => trim($_POST['checking_cost_code']),
                'checking_cost_total_number' => (isset($_POST['checking_cost_vehicle'])?count($_POST['checking_cost_vehicle']):0)+(isset($_POST['checking_cost_romooc'])?count($_POST['checking_cost_romooc']):0),
            );

            $checkingcost_model->createCost($data);
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$checkingcost_model->getLastCost()->checking_cost_id."|checking_cost|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'checking_cost',
                'user_log_table_name' => 'Phí kiểm định',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->checkingcost) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới phí kiểm định';

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('where'=>'customer_type=2','order_by'=>'customer_name','order'=>'ASC'));

        return $this->view->show('checkingcost/add');
    }

    public function editcheckingcost(){
        $checkingcost_model = $this->model->get('checkingcostModel');

        if (isset($_POST['checking_cost_id'])) {
            $id = $_POST['checking_cost_id'];
            
            $data = array(
                'checking_cost_date' => strtotime(str_replace('/', '-', $_POST['checking_cost_date'])),
                'checking_cost_start_date' => strtotime(str_replace('/', '-', $_POST['checking_cost_start_date'])),
                'checking_cost_end_date' => strtotime(str_replace('/', '-', $_POST['checking_cost_end_date'])),
                'checking_cost_vehicle' => isset($_POST['checking_cost_vehicle'])?implode(',',$_POST['checking_cost_vehicle']):null,
                'checking_cost_romooc' => isset($_POST['checking_cost_romooc'])?implode(',',$_POST['checking_cost_romooc']):null,
                'checking_cost_customer' => trim($_POST['checking_cost_customer']),
                'checking_cost_price' => str_replace(',', '', $_POST['checking_cost_price']),
                'checking_cost_vat' => str_replace(',', '', $_POST['checking_cost_vat']),
                'checking_cost_comment' => trim($_POST['checking_cost_comment']),
                'checking_cost_code' => trim($_POST['checking_cost_code']),
                'checking_cost_total_number' => (isset($_POST['checking_cost_vehicle'])?count($_POST['checking_cost_vehicle']):0)+(isset($_POST['checking_cost_romooc'])?count($_POST['checking_cost_romooc']):0),
            );

            $checkingcost_model->updateCost($data,array('checking_cost_id'=>$id));
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|checking_cost|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'checking_cost',
                'user_log_table_name' => 'Phí kiểm định',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->checkingcost) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('checkingcost');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật phí kiểm định';

        $checkingcost_model = $this->model->get('checkingcostModel');

        $checkingcost_data = $checkingcost_model->getCost($id);

        $this->view->data['checkingcost_data'] = $checkingcost_data;

        if (!$checkingcost_data) {

            $this->view->redirect('checkingcost');

        }

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('where'=>'customer_type=2','order_by'=>'customer_name','order'=>'ASC'));

        return $this->view->show('checkingcost/edit');

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

            $this->view->redirect('checkingcost');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật phí kiểm định';

        $checkingcost_model = $this->model->get('checkingcostModel');

        $checkingcost_data = $checkingcost_model->getCost($id);

        $this->view->data['checkingcost_data'] = $checkingcost_data;

        if (!$checkingcost_data) {

            $this->view->redirect('checkingcost');

        }

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('where'=>'customer_type=2','order_by'=>'customer_name','order'=>'ASC'));

        return $this->view->show('checkingcost/view');

    }
    public function viewcheckingcost(){

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
        $this->view->data['title'] = 'Thông tin phí kiểm định';

        $id = $_GET['id'];

        $info = explode('~', $id);

        $checkingcost_model = $this->model->get('checkingcostModel');

        $data = array(
            'where'=>'checkingcost_vehicle = '.$info[0].' AND checkingcost_start_date <= '.strtotime(str_replace('/', '-', $info[1])).' AND (checkingcost_end_date IS NULL OR checkingcost_end_date=0 OR checkingcost_end_date >= '.strtotime(str_replace('/', '-', $info[1])).')',
            'order_by'=>'checkingcost_start_date',
            'order'=>'DESC',
            'limit'=>1
        );

        $checkingcosts = $checkingcost_model->getAllVehicle($data);
        foreach ($checkingcosts as $checkingcost) {
            $checkingcost_data = $checkingcost;
        }

        $this->view->data['checkingcost_data'] = $checkingcost_data;

        if (!$checkingcost_data) {

            $this->view->redirect('checkingcost');

        }

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllcustomer(array('order_by'=>'customer_name','order'=>'ASC'));

        return $this->view->show('checkingcost/view');

    }

    public function getcheckingcost(){

        $vehicle = $_GET['vehicle'];

        $date = $_GET['date'];

        $checkingcost_model = $this->model->get('checkingcostModel');

        $data = array(
            'where'=>'checkingcost_vehicle = '.$vehicle.' AND checkingcost_start_date <= '.strtotime(str_replace('/', '-', $date)).' AND (checkingcost_end_date IS NULL OR checkingcost_end_date=0 OR checkingcost_end_date >= '.strtotime(str_replace('/', '-', $date)).')',
            'order_by'=>'checkingcost_start_date',
            'order'=>'DESC',
            'limit'=>1
        );
        $join = array('table'=>'customer','where'=>'checkingcost_customer=customer_id');

        $checkingcosts = $checkingcost_model->getAllVehicle($data,$join);
        $checkingcost_data = array(
            'customer_id'=>null,
            'customer_name'=>null,
        );
        foreach ($checkingcosts as $checkingcost) {
            $checkingcost_data['customer_id'] = $checkingcost->customer_id;
            $checkingcost_data['customer_name'] = $checkingcost->customer_name;
        }

        echo json_encode($checkingcost_data);

    }

    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('where'=>'customer_type=2','order_by'=>'customer_name','order'=>'ASC'));

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('checkingcost/filter');
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->checkingcost) || json_decode($_SESSION['user_permission_action'])->checkingcost != "checkingcost") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $checkingcost_model = $this->model->get('checkingcostModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $checkingcost_model->deleteCost($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|checking_cost|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'checking_cost',
                    'user_log_table_name' => 'Phí kiểm định',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $checkingcost_model->deleteCost($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|checking_cost|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'checking_cost',
                    'user_log_table_name' => 'Phí kiểm định',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importcheckingcost(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->checkingcost) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('checkingcost/import');

    }


}

?>