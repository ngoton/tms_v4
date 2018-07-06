<?php

Class repairController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý phiếu sửa chữa, bảo dưỡng';



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

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'repair_date';

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

        $repair_model = $this->model->get('repairModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        if ($batdau!="") {
            $data['where'] .= ' AND repair_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND repair_date < '.$ngayketthuc;
        }

        $join = array('table'=>'repair_code','where'=>'repair_code=repair_code_id LEFT JOIN staff ON repair_staff=staff_id','join'=>'LEFT JOIN');

        if (isset($_POST['filter'])) {
            if (isset($_POST['repair_code'])) {
                $data['where'] .= ' AND repair_code IN ('.implode(',',$_POST['repair_code']).')';
            }
            if (isset($_POST['repair_staff'])) {
                $data['where'] .= ' AND repair_staff IN ('.implode(',',$_POST['repair_staff']).')';
            }
            if (isset($_POST['repair_vehicle'])) {
                $data['where'] .= ' AND repair_vehicle IN ('.implode(',',$_POST['repair_vehicle']).')';
            }
            if (isset($_POST['repair_romooc'])) {
                $data['where'] .= ' AND repair_romooc IN ('.implode(',',$_POST['repair_romooc']).')';
            }
            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($repair_model->getAllRepair($data,$join));

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
            $data['where'] .= ' AND repair_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND repair_date < '.$ngayketthuc;
        }

        if (isset($_POST['filter'])) {
            if (isset($_POST['repair_code'])) {
                $data['where'] .= ' AND repair_code IN ('.implode(',',$_POST['repair_code']).')';
            }
            if (isset($_POST['repair_staff'])) {
                $data['where'] .= ' AND repair_staff IN ('.implode(',',$_POST['repair_staff']).')';
            }
            if (isset($_POST['repair_vehicle'])) {
                $data['where'] .= ' AND repair_vehicle IN ('.implode(',',$_POST['repair_vehicle']).')';
            }
            if (isset($_POST['repair_romooc'])) {
                $data['where'] .= ' AND repair_romooc IN ('.implode(',',$_POST['repair_romooc']).')';
            }
        }

        if ($keyword != '') {

            $search = '( repair_code_name LIKE "%'.$keyword.'%" OR staff_name  LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }

        $repairs = $repair_model->getAllRepair($data,$join);;

        $this->view->data['repairs'] = $repairs;


        return $this->view->show('repair/index');

    }


    public function addrepair(){
        $repair_model = $this->model->get('repairModel');

        if (isset($_POST['repair_code'])) {
            if($repair_model->getRepairByWhere(array('repair_number'=>trim($_POST['repair_number'])))){
                echo 'Số phiếu đã tồn tại';
                return false;
            }
            

            $data = array(
                'repair_date' => strtotime(str_replace('/', '-', $_POST['repair_date'])),
                'repair_code' => trim($_POST['repair_code']),
                'repair_number' => trim($_POST['repair_number']),
                'repair_vehicle' => trim($_POST['repair_vehicle']),
                'repair_romooc' => trim($_POST['repair_romooc']),
                'repair_price' => trim($_POST['repair_price']),
                'repair_staff' => trim($_POST['repair_staff']),
                'repair_create_user' => $_SESSION['userid_logined'],
            );
            $repair_model->createRepair($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$repair_model->getLastRepair()->repair_id."|repair|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'repair',
                'user_log_table_name' => 'Phiếu sửa chữa',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->repair) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới phiếu sửa chữa';

        $repair_code = $this->model->get('repaircodeModel');

        $this->view->data['codes'] = $repair_code->getAllRepair(array('order_by'=>'repair_code_name','order'=>'ASC'));

        $staff = $this->model->get('staffModel');

        $this->view->data['staffs'] = $staff->getAllStaff(array('order_by'=>'staff_name','order'=>'ASC'));

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        return $this->view->show('repair/add');
    }

    public function editrepair(){
        $repair_model = $this->model->get('repairModel');

        if (isset($_POST['repair_id'])) {
            $id = $_POST['repair_id'];
            if($repair_model->getAllRepairByWhere($id.' AND repair_number = '.trim($_POST['repair_number']))){
                echo 'Số phiếu đã tồn tại';
                return false;
            }

            $data = array(
                'repair_date' => strtotime(str_replace('/', '-', $_POST['repair_date'])),
                'repair_code' => trim($_POST['repair_code']),
                'repair_number' => trim($_POST['repair_number']),
                'repair_vehicle' => trim($_POST['repair_vehicle']),
                'repair_romooc' => trim($_POST['repair_romooc']),
                'repair_price' => trim($_POST['repair_price']),
                'repair_staff' => trim($_POST['repair_staff']),
                'repair_update_user' => $_SESSION['userid_logined'],
            );
            $repair_model->updateRepair($data,array('repair_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|repair|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'repair',
                'user_log_table_name' => 'Phiếu sửa chữa',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->repair) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('repair');

        }

        $this->view->data['title'] = 'Cập nhật phiếu sửa chữa';
        $this->view->data['lib'] = $this->lib;

        $repair_model = $this->model->get('repairModel');

        $repair_data = $repair_model->getRepair($id);

        $this->view->data['repair_data'] = $repair_data;

        if (!$repair_data) {

            $this->view->redirect('repair');

        }

        $repair_code = $this->model->get('repaircodeModel');

        $this->view->data['codes'] = $repair_code->getAllRepair(array('order_by'=>'repair_code_name','order'=>'ASC'));

        $staff = $this->model->get('staffModel');

        $this->view->data['staffs'] = $staff->getAllStaff(array('order_by'=>'staff_name','order'=>'ASC'));

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        return $this->view->show('repair/edit');

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

            $this->view->redirect('repair');

        }

        $this->view->data['title'] = 'Cập nhật phiếu sửa chữa';
        $this->view->data['lib'] = $this->lib;

        $repair_model = $this->model->get('repairModel');

        $repair_data = $repair_model->getRepair($id);

        $this->view->data['repair_data'] = $repair_data;

        if (!$repair_data) {

            $this->view->redirect('repair');

        }

        $repair_code = $this->model->get('repaircodeModel');

        $this->view->data['codes'] = $repair_code->getAllRepair(array('order_by'=>'repair_code_name','order'=>'ASC'));

        $staff = $this->model->get('staffModel');

        $this->view->data['staffs'] = $staff->getAllStaff(array('order_by'=>'staff_name','order'=>'ASC'));

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        return $this->view->show('repair/view');

    }

    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $repair_code = $this->model->get('repaircodeModel');

        $this->view->data['codes'] = $repair_code->getAllRepair(array('order_by'=>'repair_code_name','order'=>'ASC'));

        $staff = $this->model->get('staffModel');

        $this->view->data['staffs'] = $staff->getAllStaff(array('order_by'=>'staff_name','order'=>'ASC'));

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('repair/filter');
    }

    public function getrepair(){
        $repair_model = $this->model->get('repairModel');

        $repairs = $repair_model->getAllRepair(array('order_by'=>'repair_name','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($repairs as $repair) {
            $result[$i]['id'] = $repair->repair_id;
            $result[$i]['text'] = $repair->repair_number;
            $i++;
        }
        echo json_encode($result);
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->repair) || json_decode($_SESSION['user_permission_action'])->repair != "repair") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $repair_model = $this->model->get('repairModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $repair_model->deleteRepair($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|repair|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'repair',
                    'user_log_table_name' => 'Phiếu sửa chữa',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $repair_model->deleteRepair($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|repair|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'repair',
                    'user_log_table_name' => 'Phiếu sửa chữa',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importrepair(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->repair) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('repair/import');

    }


}

?>