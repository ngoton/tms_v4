<?php

Class portController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý cảng';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'place_code';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $place_model = $this->model->get('placeModel');

        $data = array(
            'where'=>'place_port = 1',
        );

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $join = array('table'=>'province','where'=>'place_province=province_id');

        $tongsodong = count($place_model->getAllPlace($data,$join));

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
            'where'=>'place_port = 1',

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            );

        

        if ($keyword != '') {

            $search = ' AND ( place_code LIKE "%'.$keyword.'%" OR place_name LIKE "%'.$keyword.'%" OR province_name LIKE "%'.$keyword.'%" )';

            $data['where'] .= $search;

        }



        $this->view->data['places'] = $place_model->getAllPlace($data,$join);



        return $this->view->show('port/index');

    }


    public function addport(){
        $place_model = $this->model->get('placeModel');

        if (isset($_POST['place_code'])) {
            if($place_model->getPlaceByWhere(array('place_code'=>trim($_POST['place_code'])))){
                echo 'Mã cảng đã tồn tại';
                return false;
            }
            if($place_model->getPlaceByWhere(array('place_name'=>trim($_POST['place_name']),'place_province'=>trim($_POST['place_province'])))){
                echo 'Tên cảng đã tồn tại';
                return false;
            }

            $data = array(
                'place_province' => trim($_POST['place_province']),
                'place_name' => trim($_POST['place_name']),
                'place_code' => trim($_POST['place_code']),
                'place_lat' => trim($_POST['place_lat']),
                'place_long' => trim($_POST['place_long']),
                'place_port' => isset($_POST['place_port'])?$_POST['place_port']:null,
            );
            $place_model->createPlace($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$place_model->getLastPlace()->place_id."|place|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'place',
                'user_log_table_name' => 'Cảng',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->port) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới cảng';

        $province = $this->model->get('provinceModel');

        $this->view->data['provinces'] = $province->getAllProvince();

        return $this->view->show('port/add');
    }

    public function editport(){
        $place_model = $this->model->get('placeModel');

        if (isset($_POST['place_id'])) {
            $id = $_POST['place_id'];
            if($place_model->getAllPlaceByWhere($id.' AND place_code = "'.trim($_POST['place_code']))){
                echo 'Mã cảng đã tồn tại';
                return false;
            }
            if($place_model->getAllPlaceByWhere($id.' AND place_name = "'.trim($_POST['place_name']).'"'.' AND place_province = '.trim($_POST['place_province']))){
                echo 'Tên cảng đã tồn tại';
                return false;
            }

            $data = array(
                'place_province' => trim($_POST['place_province']),
                'place_name' => trim($_POST['place_name']),
                'place_code' => trim($_POST['place_code']),
                'place_lat' => trim($_POST['place_lat']),
                'place_long' => trim($_POST['place_long']),
                'place_port' => isset($_POST['place_port'])?$_POST['place_port']:null,
            );
            $place_model->updatePlace($data,array('place_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|place|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'place',
                'user_log_table_name' => 'Cảng',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->port) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('port');

        }

        $this->view->data['title'] = 'Cập nhật cảng';

        $place_model = $this->model->get('placeModel');

        $place_data = $place_model->getPlace($id);

        $this->view->data['place_data'] = $place_data;

        if (!$place_data) {

            $this->view->redirect('port');

        }

        $province = $this->model->get('provinceModel');

        $this->view->data['provinces'] = $province->getAllProvince();

        return $this->view->show('port/edit');

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

            $this->view->redirect('port');

        }

        $this->view->data['title'] = 'Thông tin cảng';

        $place_model = $this->model->get('placeModel');

        $place_data = $place_model->getPlace($id);

        $this->view->data['place_data'] = $place_data;

        if (!$place_data) {

            $this->view->redirect('port');

        }

        $province = $this->model->get('provinceModel');

        $this->view->data['provinces'] = $province->getAllProvince();

        return $this->view->show('port/view');

    }

    public function getport(){
        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('where'=>'place_port = 1','order_by'=>'place_code','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($places as $place) {
            $result[$i]['id'] = $place->place_id;
            $result[$i]['text'] = $place->place_name;
            $i++;
        }
        echo json_encode($result);
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->port) || json_decode($_SESSION['user_permission_action'])->port != "port") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $place_model = $this->model->get('placeModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $place_model->deletePlace($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|place|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'place',
                    'user_log_table_name' => 'Cảng',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $place_model->deletePlace($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|place|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'place',
                    'user_log_table_name' => 'Cảng',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importport(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->port) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('port/import');

    }


}

?>