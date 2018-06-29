<?php

Class insurancecostController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý phí bảo hiểm';



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

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'insurance_cost_date';

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


        $insurancecost_model = $this->model->get('insurancecostModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        if ($batdau!="") {
            $data['where'] .= ' AND insurance_cost_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND insurance_cost_date < '.$ngayketthuc;
        }

        $join = array('table'=>'customer', 'where'=>'insurance_cost_customer = customer_id','join'=>'LEFT JOIN');

        if (isset($_POST['filter'])) {
            if (isset($_POST['insurance_cost_customer'])) {
                $data['where'] .= ' AND insurance_cost_customer IN ('.implode(',',$_POST['insurance_cost_customer']).')';
            }
            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($insurancecost_model->getAllCost($data,$join));

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
            $data['where'] .= ' AND insurance_cost_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND insurance_cost_date < '.$ngayketthuc;
        }

        if (isset($_POST['filter'])) {
            if (isset($_POST['insurance_cost_customer'])) {
                $data['where'] .= ' AND insurance_cost_customer IN ('.implode(',',$_POST['insurance_cost_customer']).')';
            }
            $this->view->data['filter'] = 1;
        }

        if ($keyword != '') {

            $search = '( insurance_cost_code LIKE "%'.$keyword.'%" OR customer_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['insurance_costs'] = $insurancecost_model->getAllCost($data,$join);



        return $this->view->show('insurancecost/index');

    }


    public function addinsurancecost(){
        $insurancecost_model = $this->model->get('insurancecostModel');

        if (isset($_POST['insurance_cost_price'])) {
           

            $data = array(
                'insurance_cost_date' => strtotime(str_replace('/', '-', $_POST['insurance_cost_date'])),
                'insurance_cost_start_date' => strtotime(str_replace('/', '-', $_POST['insurance_cost_start_date'])),
                'insurance_cost_end_date' => strtotime(str_replace('/', '-', $_POST['insurance_cost_end_date'])),
                'insurance_cost_vehicle' => isset($_POST['insurance_cost_vehicle'])?implode(',',$_POST['insurance_cost_vehicle']):null,
                'insurance_cost_romooc' => isset($_POST['insurance_cost_romooc'])?implode(',',$_POST['insurance_cost_romooc']):null,
                'insurance_cost_customer' => trim($_POST['insurance_cost_customer']),
                'insurance_cost_price' => str_replace(',', '', $_POST['insurance_cost_price']),
                'insurance_cost_vat' => str_replace(',', '', $_POST['insurance_cost_vat']),
                'insurance_cost_comment' => trim($_POST['insurance_cost_comment']),
                'insurance_cost_code' => trim($_POST['insurance_cost_code']),
                'insurance_cost_total_number' => (isset($_POST['insurance_cost_vehicle'])?count($_POST['insurance_cost_vehicle']):0)+(isset($_POST['insurance_cost_romooc'])?count($_POST['insurance_cost_romooc']):0),
            );

            $insurancecost_model->createCost($data);
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$insurancecost_model->getLastCost()->insurance_cost_id."|insurance_cost|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'insurance_cost',
                'user_log_table_name' => 'Phí bảo hiểm',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->insurancecost) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới phí bảo hiểm';

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('where'=>'customer_type=2','order_by'=>'customer_name','order'=>'ASC'));

        return $this->view->show('insurancecost/add');
    }

    public function editinsurancecost(){
        $insurancecost_model = $this->model->get('insurancecostModel');

        if (isset($_POST['insurance_cost_id'])) {
            $id = $_POST['insurance_cost_id'];
            
            $data = array(
                'insurance_cost_date' => strtotime(str_replace('/', '-', $_POST['insurance_cost_date'])),
                'insurance_cost_start_date' => strtotime(str_replace('/', '-', $_POST['insurance_cost_start_date'])),
                'insurance_cost_end_date' => strtotime(str_replace('/', '-', $_POST['insurance_cost_end_date'])),
                'insurance_cost_vehicle' => isset($_POST['insurance_cost_vehicle'])?implode(',',$_POST['insurance_cost_vehicle']):null,
                'insurance_cost_romooc' => isset($_POST['insurance_cost_romooc'])?implode(',',$_POST['insurance_cost_romooc']):null,
                'insurance_cost_customer' => trim($_POST['insurance_cost_customer']),
                'insurance_cost_price' => str_replace(',', '', $_POST['insurance_cost_price']),
                'insurance_cost_vat' => str_replace(',', '', $_POST['insurance_cost_vat']),
                'insurance_cost_comment' => trim($_POST['insurance_cost_comment']),
                'insurance_cost_code' => trim($_POST['insurance_cost_code']),
                'insurance_cost_total_number' => (isset($_POST['insurance_cost_vehicle'])?count($_POST['insurance_cost_vehicle']):0)+(isset($_POST['insurance_cost_romooc'])?count($_POST['insurance_cost_romooc']):0),
            );

            $insurancecost_model->updateCost($data,array('insurance_cost_id'=>$id));
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|insurance_cost|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'insurance_cost',
                'user_log_table_name' => 'Phí bảo hiểm',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->insurancecost) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('insurancecost');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật phí bảo hiểm';

        $insurancecost_model = $this->model->get('insurancecostModel');

        $insurancecost_data = $insurancecost_model->getCost($id);

        $this->view->data['insurancecost_data'] = $insurancecost_data;

        if (!$insurancecost_data) {

            $this->view->redirect('insurancecost');

        }

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('where'=>'customer_type=2','order_by'=>'customer_name','order'=>'ASC'));

        return $this->view->show('insurancecost/edit');

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

            $this->view->redirect('insurancecost');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật phí bảo hiểm';

        $insurancecost_model = $this->model->get('insurancecostModel');

        $insurancecost_data = $insurancecost_model->getCost($id);

        $this->view->data['insurancecost_data'] = $insurancecost_data;

        if (!$insurancecost_data) {

            $this->view->redirect('insurancecost');

        }

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('where'=>'customer_type=2','order_by'=>'customer_name','order'=>'ASC'));

        return $this->view->show('insurancecost/view');

    }
    public function viewinsurancecost(){

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
        $this->view->data['title'] = 'Thông tin phí bảo hiểm';

        $id = $_GET['id'];

        $info = explode('~', $id);

        $insurancecost_model = $this->model->get('insurancecostModel');

        $data = array(
            'where'=>'insurancecost_vehicle = '.$info[0].' AND insurancecost_start_date <= '.strtotime(str_replace('/', '-', $info[1])).' AND (insurancecost_end_date IS NULL OR insurancecost_end_date=0 OR insurancecost_end_date >= '.strtotime(str_replace('/', '-', $info[1])).')',
            'order_by'=>'insurancecost_start_date',
            'order'=>'DESC',
            'limit'=>1
        );

        $insurancecosts = $insurancecost_model->getAllVehicle($data);
        foreach ($insurancecosts as $insurancecost) {
            $insurancecost_data = $insurancecost;
        }

        $this->view->data['insurancecost_data'] = $insurancecost_data;

        if (!$insurancecost_data) {

            $this->view->redirect('insurancecost');

        }

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllcustomer(array('order_by'=>'customer_name','order'=>'ASC'));

        return $this->view->show('insurancecost/view');

    }

    public function getinsurancecost(){

        $vehicle = $_GET['vehicle'];

        $date = $_GET['date'];

        $insurancecost_model = $this->model->get('insurancecostModel');

        $data = array(
            'where'=>'insurancecost_vehicle = '.$vehicle.' AND insurancecost_start_date <= '.strtotime(str_replace('/', '-', $date)).' AND (insurancecost_end_date IS NULL OR insurancecost_end_date=0 OR insurancecost_end_date >= '.strtotime(str_replace('/', '-', $date)).')',
            'order_by'=>'insurancecost_start_date',
            'order'=>'DESC',
            'limit'=>1
        );
        $join = array('table'=>'customer','where'=>'insurancecost_customer=customer_id');

        $insurancecosts = $insurancecost_model->getAllVehicle($data,$join);
        $insurancecost_data = array(
            'customer_id'=>null,
            'customer_name'=>null,
        );
        foreach ($insurancecosts as $insurancecost) {
            $insurancecost_data['customer_id'] = $insurancecost->customer_id;
            $insurancecost_data['customer_name'] = $insurancecost->customer_name;
        }

        echo json_encode($insurancecost_data);

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

        return $this->view->show('insurancecost/filter');
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->insurancecost) || json_decode($_SESSION['user_permission_action'])->insurancecost != "insurancecost") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $insurancecost_model = $this->model->get('insurancecostModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $insurancecost_model->deleteCost($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|insurance_cost|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'insurance_cost',
                    'user_log_table_name' => 'Phí bảo hiểm',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $insurancecost_model->deleteCost($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|insurance_cost|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'insurance_cost',
                    'user_log_table_name' => 'Phí bảo hiểm',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importinsurancecost(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->insurancecost) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('insurancecost/import');

    }


}

?>