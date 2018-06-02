<?php

Class placeController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý kho hàng';



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

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $join = array('table'=>'province','where'=>'place_province=province_id');

        $tongsodong = count($place_model->getAllPlace(null,$join));

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

            $search = '( place_code LIKE "%'.$keyword.'%" OR place_name LIKE "%'.$keyword.'%" OR province_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['places'] = $place_model->getAllPlace($data,$join);



        return $this->view->show('place/index');

    }


    public function addplace(){
        $place_model = $this->model->get('placeModel');

        if (isset($_POST['place_code'])) {
            if($place_model->getPlaceByWhere(array('place_code'=>trim($_POST['place_code'])))){
                echo 'Mã kho hàng đã tồn tại';
                return false;
            }
            if($place_model->getPlaceByWhere(array('place_name'=>trim($_POST['place_name']),'place_province'=>trim($_POST['place_province'])))){
                echo 'Tên kho hàng đã tồn tại';
                return false;
            }

            $data = array(
                'place_province' => trim($_POST['place_province']),
                'place_name' => trim($_POST['place_name']),
                'place_code' => trim($_POST['place_code']),
            );
            $place_model->createPlace($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$place_model->getLastPlace()->place_id."|place|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'place',
                'user_log_table_name' => 'Kho hàng',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->place) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới kho hàng';

        $province = $this->model->get('provinceModel');

        $this->view->data['provinces'] = $province->getAllProvince();

        return $this->view->show('place/add');
    }

    public function editplace(){
        $place_model = $this->model->get('placeModel');

        if (isset($_POST['place_id'])) {
            $id = $_POST['place_id'];
            if($place_model->getAllPlaceByWhere($id.' AND place_code = "'.trim($_POST['place_code']))){
                echo 'Mã kho hàng đã tồn tại';
                return false;
            }
            if($place_model->getAllPlaceByWhere($id.' AND place_name = "'.trim($_POST['place_name']).'"'.' AND place_province = '.trim($_POST['place_province']))){
                echo 'Tên kho hàng đã tồn tại';
                return false;
            }

            $data = array(
                'place_province' => trim($_POST['place_province']),
                'place_name' => trim($_POST['place_name']),
                'place_code' => trim($_POST['place_code']),
            );
            $place_model->updatePlace($data,array('place_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|place|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'place',
                'user_log_table_name' => 'Kho hàng',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->place) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('place');

        }

        $this->view->data['title'] = 'Cập nhật kho hàng';

        $place_model = $this->model->get('placeModel');

        $place_data = $place_model->getPlace($id);

        $this->view->data['place_data'] = $place_data;

        if (!$place_data) {

            $this->view->redirect('place');

        }

        $province = $this->model->get('provinceModel');

        $this->view->data['provinces'] = $province->getAllProvince();

        return $this->view->show('place/edit');

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

            $this->view->redirect('place');

        }

        $this->view->data['title'] = 'Thông tin kho hàng';

        $place_model = $this->model->get('placeModel');

        $place_data = $place_model->getPlace($id);

        $this->view->data['place_data'] = $place_data;

        if (!$place_data) {

            $this->view->redirect('place');

        }

        $province = $this->model->get('provinceModel');

        $this->view->data['provinces'] = $province->getAllProvince();

        return $this->view->show('place/view');

    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->place) || json_decode($_SESSION['user_permission_action'])->place != "place") && $_SESSION['user_permission_action'] != '["all"]') {

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
                    'user_log_table_name' => 'Kho hàng',
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
                    'user_log_table_name' => 'Kho hàng',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importplace(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->place) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('place/import');

    }


}

?>