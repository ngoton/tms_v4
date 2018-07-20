<?php

Class tollController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý trạm thu phí';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'toll_code';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $toll_model = $this->model->get('tollModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $join = array('table'=>'province','where'=>'toll_province=province_id');

        $tongsodong = count($toll_model->getAllToll(null,$join));

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

            $search = '( toll_code LIKE "%'.$keyword.'%" OR toll_name LIKE "%'.$keyword.'%" OR toll_mst LIKE "%'.$keyword.'%" OR province_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['tolls'] = $toll_model->getAllToll($data,$join);



        return $this->view->show('toll/index');

    }


    public function addtoll(){
        $toll_model = $this->model->get('tollModel');

        if (isset($_POST['toll_code'])) {
            if($toll_model->getTollByWhere(array('toll_code'=>trim($_POST['toll_code']),'toll_province'=>trim($_POST['toll_province'])))){
                echo 'Tên trạm thu phí đã tồn tại';
                return false;
            }

            $data = array(
                'toll_province' => trim($_POST['toll_province']),
                'toll_name' => trim($_POST['toll_name']),
                'toll_code' => trim($_POST['toll_code']),
                'toll_mst' => trim($_POST['toll_mst']),
                'toll_type' => trim($_POST['toll_type']),
                'toll_symbol' => trim($_POST['toll_symbol']),
                'toll_lat' => trim($_POST['toll_lat']),
                'toll_long' => trim($_POST['toll_long']),
            );
            $toll_model->createToll($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$toll_model->getLastToll()->toll_id."|toll|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'toll',
                'user_log_table_name' => 'Trạm thu phí',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->toll) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới trạm thu phí';

        $province = $this->model->get('provinceModel');

        $this->view->data['provinces'] = $province->getAllProvince();

        return $this->view->show('toll/add');
    }

    public function edittoll(){
        $toll_model = $this->model->get('tollModel');

        if (isset($_POST['toll_id'])) {
            $id = $_POST['toll_id'];
            if($toll_model->getAllTollByWhere($id.' AND toll_code = "'.trim($_POST['toll_code']).'"'.' AND toll_province = '.trim($_POST['toll_province']))){
                echo 'Tên trạm thu phí đã tồn tại';
                return false;
            }

            $data = array(
                'toll_province' => trim($_POST['toll_province']),
                'toll_name' => trim($_POST['toll_name']),
                'toll_code' => trim($_POST['toll_code']),
                'toll_mst' => trim($_POST['toll_mst']),
                'toll_type' => trim($_POST['toll_type']),
                'toll_symbol' => trim($_POST['toll_symbol']),
                'toll_lat' => trim($_POST['toll_lat']),
                'toll_long' => trim($_POST['toll_long']),
            );
            $toll_model->updateToll($data,array('toll_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|toll|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'toll',
                'user_log_table_name' => 'Trạm thu phí',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->toll) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('toll');

        }

        $this->view->data['title'] = 'Cập nhật trạm thu phí';

        $toll_model = $this->model->get('tollModel');

        $toll_data = $toll_model->getToll($id);

        $this->view->data['toll_data'] = $toll_data;

        if (!$toll_data) {

            $this->view->redirect('toll');

        }

        $province = $this->model->get('provinceModel');

        $this->view->data['provinces'] = $province->getAllProvince();

        return $this->view->show('toll/edit');

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

            $this->view->redirect('toll');

        }

        $this->view->data['title'] = 'Thông tin trạm thu phí';

        $toll_model = $this->model->get('tollModel');

        $toll_data = $toll_model->getToll($id);

        $this->view->data['toll_data'] = $toll_data;

        if (!$toll_data) {

            $this->view->redirect('toll');

        }

        $province = $this->model->get('provinceModel');

        $this->view->data['provinces'] = $province->getAllProvince();

        return $this->view->show('toll/view');

    }

    public function gettoll(){
        $toll_model = $this->model->get('tollModel');

        $tolls = $toll_model->getAllToll(array('order_by'=>'toll_code','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($tolls as $toll) {
            $result[$i]['id'] = $toll->toll_id;
            $result[$i]['text'] = $toll->toll_code;
            $i++;
        }
        echo json_encode($result);
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->toll) || json_decode($_SESSION['user_permission_action'])->toll != "toll") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $toll_model = $this->model->get('tollModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $toll_model->deleteToll($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|toll|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'toll',
                    'user_log_table_name' => 'Trạm thu phí',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $toll_model->deleteToll($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|toll|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'toll',
                    'user_log_table_name' => 'Trạm thu phí',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importtoll(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->toll) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('toll/import');

    }


}

?>