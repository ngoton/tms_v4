<?php

Class repaircodeController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý danh mục sửa chữa';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'repair_code_name';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $repair_code_model = $this->model->get('repaircodeModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        

        $tongsodong = count($repair_code_model->getAllRepair());

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

            $search = '( repair_code_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['repaircodes'] = $repair_code_model->getAllRepair($data);



        return $this->view->show('repaircode/index');

    }


    public function addrepaircode(){
        $repair_code_model = $this->model->get('repaircodeModel');

        if (isset($_POST['repair_code_name'])) {
            if($repair_code_model->getRepairByWhere(array('repair_code_name'=>trim($_POST['repair_code_name'])))){
                echo 'Danh mục sửa chữa đã tồn tại';
                return false;
            }

            $data = array(
                'repair_code_name' => trim($_POST['repair_code_name']),
            );
            $repair_code_model->createRepair($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$repair_code_model->getLastRepair()->repair_code_id."|repair_code|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'repair_code',
                'user_log_table_name' => 'Danh mục sửa chữa',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->repaircode) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới danh mục sửa chữa';

        return $this->view->show('repaircode/add');
    }

    public function editrepaircode(){
        $repair_code_model = $this->model->get('repaircodeModel');

        if (isset($_POST['repair_code_id'])) {
            $id = $_POST['repair_code_id'];
            if($repair_code_model->getAllRepairByWhere($id.' AND repair_code_name = "'.trim($_POST['repair_code_name']).'"')){
                echo 'danh mục sửa chữa đã tồn tại';
                return false;
            }

            $data = array(
                'repair_code_name' => trim($_POST['repair_code_name']),
            );
            $repair_code_model->updateRepair($data,array('repair_code_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|repair_code|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'repair_code',
                'user_log_table_name' => 'Danh mục sửa chữa',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->repaircode) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('repaircode');

        }

        $this->view->data['title'] = 'Cập nhật danh mục sửa chữa';

        $repair_code_model = $this->model->get('repaircodeModel');

        $repair_code_data = $repair_code_model->getRepair($id);

        $this->view->data['repair_code_data'] = $repair_code_data;

        if (!$repair_code_data) {

            $this->view->redirect('repaircode');

        }


        return $this->view->show('repaircode/edit');

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

            $this->view->redirect('repaircode');

        }

        $this->view->data['title'] = 'Thông tin danh mục sửa chữa';

        $repair_code_model = $this->model->get('repaircodeModel');

        $repair_code_data = $repair_code_model->getRepair($id);

        $this->view->data['repair_code_data'] = $repair_code_data;

        if (!$repair_code_data) {

            $this->view->redirect('repaircode');

        }


        return $this->view->show('repaircode/view');

    }

    public function getrepaircode(){
        $repair_code_model = $this->model->get('repaircodeModel');

        $repaircodes = $repair_code_model->getAllRepair(array('order_by'=>'repair_code_name','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($repaircodes as $repaircode) {
            $result[$i]['id'] = $repaircode->repair_code_id;
            $result[$i]['text'] = $repaircode->repair_code_name;
            $i++;
        }
        echo json_encode($result);
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->repaircode) || json_decode($_SESSION['user_permission_action'])->repaircode != "repaircode") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $repair_code_model = $this->model->get('repaircodeModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $repair_code_model->deleteRepair($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|repair_code|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'repair_code',
                    'user_log_table_name' => 'Danh mục sửa chữa',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $repair_code_model->deleteRepair($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|repair_code|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'repair_code',
                    'user_log_table_name' => 'Danh mục sửa chữa',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importrepaircode(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->repaircode) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('repaircode/import');

    }


}

?>