<?php

Class staffController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý nhân viên';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'staff_code';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $staff_model = $this->model->get('staffModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $join = array('table'=>'department, position','where'=>'staff_department=department_id AND staff_position=position_id');

        $data = array(
            'where'=>'1=1',
        );

        if (isset($_POST['filter'])) {
            if ($_POST['staff_gender'] != '') {
                $data['where'] .= ' AND staff_gender = '.$_POST['staff_gender'];
            }
            if ($_POST['staff_position'] != '') {
                $data['where'] .= ' AND staff_position = '.$_POST['staff_position'];
            }
            if ($_POST['staff_department'] != '') {
                $data['where'] .= ' AND staff_department = '.$_POST['staff_department'];
            }
        }

        $tongsodong = count($staff_model->getAllStaff($data,$join));

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
            if ($_POST['staff_gender'] != '') {
                $data['where'] .= ' AND staff_gender = '.$_POST['staff_gender'];
            }
            if ($_POST['staff_position'] != '') {
                $data['where'] .= ' AND staff_position = '.$_POST['staff_position'];
            }
            if ($_POST['staff_department'] != '') {
                $data['where'] .= ' AND staff_department = '.$_POST['staff_department'];
            }
        }

        if ($keyword != '') {

            $search = '( staff_code LIKE "%'.$keyword.'%" 
                OR staff_name LIKE "%'.$keyword.'%" 
                OR staff_cmnd LIKE "%'.$keyword.'%" 
                OR staff_gplx LIKE "%'.$keyword.'%" 
                OR staff_email LIKE "%'.$keyword.'%" 
                OR REPLACE(staff_phone, " ", "") LIKE "%'.str_replace(' ', '', $keyword).'%" 
            )';

            $data['where'] = $search;

        }



        $this->view->data['staffs'] = $staff_model->getAllstaff($data,$join);



        return $this->view->show('staff/index');

    }


    public function addstaff(){
        $staff_model = $this->model->get('staffModel');

        if (isset($_POST['staff_code'])) {
            if($staff_model->getStaffByWhere(array('staff_code'=>trim($_POST['staff_code'])))){
                echo 'Mã nhân viên đã tồn tại';
                return false;
            }
            if($staff_model->getStaffByWhere(array('staff_cmnd'=>trim($_POST['staff_cmnd'])))){
                echo 'Nhân viên đã tồn tại';
                return false;
            }

            $data = array(
                'staff_code' => trim($_POST['staff_code']),
                'staff_name' => trim($_POST['staff_name']),
                'staff_address' => trim($_POST['staff_address']),
                'staff_cmnd' => trim($_POST['staff_cmnd']),
                'staff_birthday' => strtotime(str_replace('/', '-', $_POST['staff_birthday'])),
                'staff_phone' => trim(str_replace('_', '', $_POST['staff_phone'])),
                'staff_email' => trim($_POST['staff_email']),
                'staff_bank_account' => trim($_POST['staff_bank_account']),
                'staff_bank' => trim($_POST['staff_bank']),
                'staff_gender' => trim($_POST['staff_gender']),
                'staff_position' => trim($_POST['staff_position']),
                'staff_department' => trim($_POST['staff_department']),
                'staff_start_date' => strtotime(str_replace('/', '-', $_POST['staff_start_date'])),
                'staff_end_date' => $_POST['staff_end_date']!="dd/mm/yyyy"?strtotime(str_replace('/', '-', $_POST['staff_end_date'])):null,
                'staff_account' => trim($_POST['staff_account']),
                'staff_gplx' => trim($_POST['staff_gplx']),
            );
            $staff_model->createStaff($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$staff_model->getLastStaff()->staff_id."|staff|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'staff',
                'user_log_table_name' => 'Nhân viên',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->staff) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới nhân viên';

        $staff_model = $this->model->get('staffModel');
        $lastID = isset($staff_model->getLastStaff()->staff_code)?$staff_model->getLastStaff()->staff_code:'NV00';
        $lastID++;
        $this->view->data['lastID'] = $lastID;

        $position_model = $this->model->get('positionModel');
        $department_model = $this->model->get('departmentModel');
        $user_model = $this->model->get('userModel');

        $positions = $position_model->getAllPosition(array('order_by'=>'position_code','order'=>'ASC'));
        $departments = $department_model->getAllDepartment(array('order_by'=>'department_code','order'=>'ASC'));
        $accounts = $user_model->getAllUser(array('order_by'=>'username','order'=>'ASC'));

        $this->view->data['positions'] = $positions;
        $this->view->data['departments'] = $departments;
        $this->view->data['accounts'] = $accounts;

        return $this->view->show('staff/add');
    }

    public function editstaff(){
        $staff_model = $this->model->get('staffModel');

        if (isset($_POST['staff_id'])) {
            $id = $_POST['staff_id'];
            if($staff_model->getAllStaffByWhere($id.' AND staff_code = "'.trim($_POST['staff_code']).'"')){
                echo 'Mã nhân viên đã tồn tại';
                return false;
            }
            if($staff_model->getAllStaffByWhere($id.' AND staff_cmnd = "'.trim($_POST['staff_cmnd']).'"')){
                echo 'Nhân viên đã tồn tại';
                return false;
            }

            $data = array(
                'staff_code' => trim($_POST['staff_code']),
                'staff_name' => trim($_POST['staff_name']),
                'staff_address' => trim($_POST['staff_address']),
                'staff_cmnd' => trim($_POST['staff_cmnd']),
                'staff_birthday' => strtotime(str_replace('/', '-', $_POST['staff_birthday'])),
                'staff_phone' => trim(str_replace('_', '', $_POST['staff_phone'])),
                'staff_email' => trim($_POST['staff_email']),
                'staff_bank_account' => trim($_POST['staff_bank_account']),
                'staff_bank' => trim($_POST['staff_bank']),
                'staff_gender' => trim($_POST['staff_gender']),
                'staff_position' => trim($_POST['staff_position']),
                'staff_department' => trim($_POST['staff_department']),
                'staff_start_date' => strtotime(str_replace('/', '-', $_POST['staff_start_date'])),
                'staff_end_date' => $_POST['staff_end_date']!="dd/mm/yyyy"?strtotime(str_replace('/', '-', $_POST['staff_end_date'])):null,
                'staff_account' => trim($_POST['staff_account']),
                'staff_gplx' => trim($_POST['staff_gplx']),
            );
            $staff_model->updateStaff($data,array('staff_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|staff|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'staff',
                'user_log_table_name' => 'Nhân viên',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->staff) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('staff');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật nhân viên';

        $staff_model = $this->model->get('staffModel');

        $staff_data = $staff_model->getStaff($id);

        $this->view->data['staff_data'] = $staff_data;

        if (!$staff_data) {

            $this->view->redirect('staff');

        }

        $position_model = $this->model->get('positionModel');
        $department_model = $this->model->get('departmentModel');
        $user_model = $this->model->get('userModel');

        $positions = $position_model->getAllPosition(array('order_by'=>'position_code','order'=>'ASC'));
        $departments = $department_model->getAllDepartment(array('order_by'=>'department_code','order'=>'ASC'));
        $accounts = $user_model->getAllUser(array('order_by'=>'username','order'=>'ASC'));

        $this->view->data['positions'] = $positions;
        $this->view->data['departments'] = $departments;
        $this->view->data['accounts'] = $accounts;

        return $this->view->show('staff/edit');

    }
    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $position_model = $this->model->get('positionModel');
        $department_model = $this->model->get('departmentModel');

        $positions = $position_model->getAllPosition(array('order_by'=>'position_code','order'=>'ASC'));
        $departments = $department_model->getAllDepartment(array('order_by'=>'department_code','order'=>'ASC'));

        $this->view->data['positions'] = $positions;
        $this->view->data['departments'] = $departments;

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('staff/filter');
    }


    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->staff) || json_decode($_SESSION['user_permission_action'])->staff != "staff") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $staff_model = $this->model->get('staffModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $staff_model->deleteStaff($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|staff|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'user',
                    'user_log_table_name' => 'Nhân viên',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $staff_model->deleteStaff($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|staff|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'user',
                    'user_log_table_name' => 'Nhân viên',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importstaff(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->staff) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('staff/import');

    }


}

?>