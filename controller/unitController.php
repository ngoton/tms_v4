<?php

Class unitController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý đơn vị tính';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'unit_name';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $unit_model = $this->model->get('unitModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        

        $tongsodong = count($unit_model->getAllUnit());

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

            $search = '( unit_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['units'] = $unit_model->getAllUnit($data);



        return $this->view->show('unit/index');

    }


    public function addunit(){
        $unit_model = $this->model->get('unitModel');

        if (isset($_POST['unit_name'])) {
            if($unit_model->getUnitByWhere(array('unit_name'=>trim($_POST['unit_name'])))){
                echo 'Tên đơn vị tính đã tồn tại';
                return false;
            }

            $data = array(
                'unit_name' => trim($_POST['unit_name']),
            );
            $unit_model->createunit($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$unit_model->getLastUnit()->unit_id."|unit|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'unit',
                'user_log_table_name' => 'Đơn vị tính',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->unit) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới đơn vị tính';

        return $this->view->show('unit/add');
    }

    public function editunit(){
        $unit_model = $this->model->get('unitModel');

        if (isset($_POST['unit_id'])) {
            $id = $_POST['unit_id'];
            if($unit_model->getAllUnitByWhere($id.' AND unit_name = "'.trim($_POST['unit_name']).'"')){
                echo 'Tên đơn vị tính đã tồn tại';
                return false;
            }

            $data = array(
                'unit_name' => trim($_POST['unit_name']),
            );
            $unit_model->updateUnit($data,array('unit_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|unit|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'unit',
                'user_log_table_name' => 'Đơn vị tính',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->unit) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('unit');

        }

        $this->view->data['title'] = 'Cập nhật đơn vị tính';

        $unit_model = $this->model->get('unitModel');

        $unit_data = $unit_model->getUnit($id);

        $this->view->data['unit_data'] = $unit_data;

        if (!$unit_data) {

            $this->view->redirect('unit');

        }


        return $this->view->show('unit/edit');

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

            $this->view->redirect('unit');

        }

        $this->view->data['title'] = 'Thông tin đơn vị tính';

        $unit_model = $this->model->get('unitModel');

        $unit_data = $unit_model->getUnit($id);

        $this->view->data['unit_data'] = $unit_data;

        if (!$unit_data) {

            $this->view->redirect('unit');

        }


        return $this->view->show('unit/view');

    }

    public function getunit(){
        $unit_model = $this->model->get('unitModel');

        $units = $unit_model->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($units as $unit) {
            $result[$i]['id'] = $unit->unit_id;
            $result[$i]['text'] = $unit->unit_name;
            $i++;
        }
        echo json_encode($result);
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->unit) || json_decode($_SESSION['user_permission_action'])->unit != "unit") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $unit_model = $this->model->get('unitModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $unit_model->deleteUnit($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|unit|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'unit',
                    'user_log_table_name' => 'Đơn vị tính',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $unit_model->deleteUnit($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|unit|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'unit',
                    'user_log_table_name' => 'đơn vị tính',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importunit(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->unit) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('unit/import');

    }


}

?>