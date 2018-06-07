<?php

Class costlistController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý danh mục chi phí';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'cost_list_code';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $cost_list_model = $this->model->get('costlistModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $join = array('table'=>'cost_type','where'=>'cost_list_type=cost_type_id');

        $tongsodong = count($cost_list_model->getAllCost(null,$join));

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

            $search = '( cost_list_code LIKE "%'.$keyword.'%" OR cost_list_name LIKE "%'.$keyword.'%" OR cost_type_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['cost_lists'] = $cost_list_model->getAllCost($data,$join);



        return $this->view->show('costlist/index');

    }


    public function addcostlist(){
        $cost_list_model = $this->model->get('costlistModel');

        if (isset($_POST['cost_list_code'])) {
            if($cost_list_model->getCostByWhere(array('cost_list_code'=>trim($_POST['cost_list_code'])))){
                echo 'Mã danh mục chi phí đã tồn tại';
                return false;
            }
            if($cost_list_model->getCostByWhere(array('cost_list_name'=>trim($_POST['cost_list_name']),'cost_list_type'=>trim($_POST['cost_list_type'])))){
                echo 'Tên danh mục chi phí đã tồn tại';
                return false;
            }

            $data = array(
                'cost_list_type' => trim($_POST['cost_list_type']),
                'cost_list_name' => trim($_POST['cost_list_name']),
                'cost_list_code' => trim($_POST['cost_list_code']),
            );
            $cost_list_model->createCost($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$cost_list_model->getLastCost()->cost_list_id."|cost_list|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'cost_list',
                'user_log_table_name' => 'Danh mục chi phí',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->cost_list) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới danh mục chi phí';

        $cost_type = $this->model->get('costtypeModel');

        $this->view->data['cost_types'] = $cost_type->getAllCost();

        return $this->view->show('costlist/add');
    }

    public function editcostlist(){
        $cost_list_model = $this->model->get('costlistModel');

        if (isset($_POST['cost_list_id'])) {
            $id = $_POST['cost_list_id'];
            if($cost_list_model->getAllCostByWhere($id.' AND cost_list_code = "'.trim($_POST['cost_list_code']))){
                echo 'Mã danh mục chi phí đã tồn tại';
                return false;
            }
            if($cost_list_model->getAllCostByWhere($id.' AND cost_list_name = "'.trim($_POST['cost_list_name']).'"'.' AND cost_list_type = '.trim($_POST['cost_list_type']))){
                echo 'Tên danh mục chi phí đã tồn tại';
                return false;
            }

            $data = array(
                'cost_list_type' => trim($_POST['cost_list_type']),
                'cost_list_name' => trim($_POST['cost_list_name']),
                'cost_list_code' => trim($_POST['cost_list_code']),
            );
            $cost_list_model->updateCost($data,array('cost_list_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|cost_list|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'cost_list',
                'user_log_table_name' => 'Danh mục chi phí',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->cost_list) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('costlist');

        }

        $this->view->data['title'] = 'Cập nhật danh mục chi phí';

        $cost_list_model = $this->model->get('costlistModel');

        $cost_list_data = $cost_list_model->getCost($id);

        $this->view->data['cost_list_data'] = $cost_list_data;

        if (!$cost_list_data) {

            $this->view->redirect('costlist');

        }

        $cost_type = $this->model->get('costtypeModel');

        $this->view->data['cost_types'] = $cost_type->getAllCost();

        return $this->view->show('costlist/edit');

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

            $this->view->redirect('costlist');

        }

        $this->view->data['title'] = 'Thông tin danh mục chi phí';

        $cost_list_model = $this->model->get('costlistModel');

        $cost_list_data = $cost_list_model->getCost($id);

        $this->view->data['cost_list_data'] = $cost_list_data;

        if (!$cost_list_data) {

            $this->view->redirect('costlist');

        }

        $cost_type = $this->model->get('costtypeModel');

        $this->view->data['cost_types'] = $cost_type->getAllCost();

        return $this->view->show('costlist/view');

    }

    public function getcostlist(){
        $cost_list_model = $this->model->get('costlistModel');

        $cost_lists = $cost_list_model->getAllCost(array('order_by'=>'cost_list_code','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($cost_lists as $cost_list) {
            $result[$i]['id'] = $cost_list->cost_list_id;
            $result[$i]['text'] = $cost_list->cost_list_name;
            $i++;
        }
        echo json_encode($result);
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->cost_list) || json_decode($_SESSION['user_permission_action'])->cost_list != "cost_list") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $cost_list_model = $this->model->get('costlistModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $cost_list_model->deleteCost($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|cost_list|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'cost_list',
                    'user_log_table_name' => 'Danh mục chi phí',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $cost_list_model->deleteCost($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|cost_list|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'cost_list',
                    'user_log_table_name' => 'Danh mục chi phí',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importcostlist(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->cost_list) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('costlist/import');

    }


}

?>