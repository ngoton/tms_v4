<?php

Class houseController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý kho vật tư';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'house_code';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $house_model = $this->model->get('houseModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        

        $tongsodong = count($house_model->getAllHouse());

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

            $search = '( house_code LIKE "%'.$keyword.'%" OR house_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['houses'] = $house_model->getAllHouse($data);



        return $this->view->show('house/index');

    }


    public function addhouse(){
        $house_model = $this->model->get('houseModel');

        if (isset($_POST['house_code'])) {
            if($house_model->getHouseByWhere(array('house_code'=>trim($_POST['house_code'])))){
                echo 'Mã kho vật tư đã tồn tại';
                return false;
            }
            if($house_model->getHouseByWhere(array('house_name'=>trim($_POST['house_name'])))){
                echo 'Tên kho vật tư đã tồn tại';
                return false;
            }

            $data = array(
                'house_code' => trim($_POST['house_code']),
                'house_name' => trim($_POST['house_name']),
            );
            $house_model->createHouse($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$house_model->getLastHouse()->house_id."|house|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'house',
                'user_log_table_name' => 'Kho vật tư',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->house) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới kho vật tư';

        return $this->view->show('house/add');
    }

    public function edithouse(){
        $house_model = $this->model->get('houseModel');

        if (isset($_POST['house_id'])) {
            $id = $_POST['house_id'];
            if($house_model->getAllHouseByWhere($id.' AND house_code = "'.trim($_POST['house_code']).'"')){
                echo 'Mã kho vật tư đã tồn tại';
                return false;
            }
            if($house_model->getAllHouseByWhere($id.' AND house_name = "'.trim($_POST['house_name']).'"')){
                echo 'Tên kho vật tư đã tồn tại';
                return false;
            }

            $data = array(
                'house_code' => trim($_POST['house_code']),
                'house_name' => trim($_POST['house_name']),
            );
            $house_model->updateHouse($data,array('house_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|house|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'house',
                'user_log_table_name' => 'Kho vật tư',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->house) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('house');

        }

        $this->view->data['title'] = 'Cập nhật kho vật tư';

        $house_model = $this->model->get('houseModel');

        $house_data = $house_model->getHouse($id);

        $this->view->data['house_data'] = $house_data;

        if (!$house_data) {

            $this->view->redirect('house');

        }


        return $this->view->show('house/edit');

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

            $this->view->redirect('house');

        }

        $this->view->data['title'] = 'Thông tin kho vật tư';

        $house_model = $this->model->get('houseModel');

        $house_data = $house_model->getHouse($id);

        $this->view->data['house_data'] = $house_data;

        if (!$house_data) {

            $this->view->redirect('house');

        }


        return $this->view->show('house/view');

    }

    public function gethouse(){
        $house_model = $this->model->get('houseModel');

        $houses = $house_model->getAllHouse(array('order_by'=>'house_code','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($houses as $house) {
            $result[$i]['id'] = $house->house_id;
            $result[$i]['text'] = $house->house_name;
            $i++;
        }
        echo json_encode($result);
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->house) || json_decode($_SESSION['user_permission_action'])->house != "house") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $house_model = $this->model->get('houseModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $house_model->deleteHouse($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|house|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'house',
                    'user_log_table_name' => 'Kho vật tư',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $house_model->deleteHouse($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|house|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'house',
                    'user_log_table_name' => 'Kho vật tư',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importhouse(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->house) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('house/import');

    }


}

?>