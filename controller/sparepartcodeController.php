<?php

Class sparepartcodeController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý mã vật tư';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'code';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $spare_part_code_model = $this->model->get('sparepartcodeModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        

        $tongsodong = count($spare_part_code_model->getAllStock());

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

            $search = '( code LIKE "%'.$keyword.'%" OR name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['sparepartcodes'] = $spare_part_code_model->getAllStock($data);



        return $this->view->show('sparepartcode/index');

    }


    public function addsparepartcode(){
        $spare_part_code_model = $this->model->get('sparepartcodeModel');

        if (isset($_POST['code'])) {
            if($spare_part_code_model->getStockByWhere(array('code'=>trim($_POST['code'])))){
                echo 'Mã vật tư đã tồn tại';
                return false;
            }

            $data = array(
                'code' => trim($_POST['code']),
                'name' => trim($_POST['name']),
            );
            $spare_part_code_model->createStock($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$spare_part_code_model->getLastStock()->spare_part_code_id."|spare_part_code|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'spare_part_code',
                'user_log_table_name' => 'Mã vật tư',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparepartcode) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới mã vật tư';

        return $this->view->show('sparepartcode/add');
    }

    public function editsparepartcode(){
        $spare_part_code_model = $this->model->get('sparepartcodeModel');

        if (isset($_POST['spare_part_code_id'])) {
            $id = $_POST['spare_part_code_id'];
            if($spare_part_code_model->getAllStockByWhere($id.' AND code = "'.trim($_POST['code']).'"')){
                echo 'Mã vật tư đã tồn tại';
                return false;
            }

            $data = array(
                'code' => trim($_POST['code']),
                'name' => trim($_POST['name']),
            );
            $spare_part_code_model->updateStock($data,array('spare_part_code_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|spare_part_code|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'spare_part_code',
                'user_log_table_name' => 'Mã vật tư',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparepartcode) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('sparepartcode');

        }

        $this->view->data['title'] = 'Cập nhật mã vật tư';

        $spare_part_code_model = $this->model->get('sparepartcodeModel');

        $spare_part_code_data = $spare_part_code_model->getStock($id);

        $this->view->data['spare_part_code_data'] = $spare_part_code_data;

        if (!$spare_part_code_data) {

            $this->view->redirect('sparepartcode');

        }


        return $this->view->show('sparepartcode/edit');

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

            $this->view->redirect('sparepartcode');

        }

        $this->view->data['title'] = 'Thông tin mã vật tư';

        $spare_part_code_model = $this->model->get('sparepartcodeModel');

        $spare_part_code_data = $spare_part_code_model->getStock($id);

        $this->view->data['spare_part_code_data'] = $spare_part_code_data;

        if (!$spare_part_code_data) {

            $this->view->redirect('sparepartcode');

        }


        return $this->view->show('sparepartcode/view');

    }

    public function getsparepartcode(){
        $spare_part_code_model = $this->model->get('sparepartcodeModel');

        $sparepartcodes = $spare_part_code_model->getAllStock(array('order_by'=>'code','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($sparepartcodes as $sparepartcode) {
            $result[$i]['id'] = $sparepartcode->spare_part_code_id;
            $result[$i]['text'] = $sparepartcode->code;
            $i++;
        }
        echo json_encode($result);
    }
    public function getcode(){
        if (isset($_GET['data'])) {
            $result = "";

            $spare_part_code_model = $this->model->get('sparepartcodeModel');

            $sparepartcodes = $spare_part_code_model->getStock($_GET['data']);
            $result = $sparepartcodes->name;

            echo $result;
        }
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->sparepartcode) || json_decode($_SESSION['user_permission_action'])->sparepartcode != "sparepartcode") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $spare_part_code_model = $this->model->get('sparepartcodeModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $spare_part_code_model->deleteStock($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|spare_part_code|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'spare_part_code',
                    'user_log_table_name' => 'Mã vật tư',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $spare_part_code_model->deleteStock($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|spare_part_code|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'spare_part_code',
                    'user_log_table_name' => 'Mã vật tư',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importsparepartcode(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparepartcode) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('sparepartcode/import');

    }


}

?>