<?php

Class routeController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý địa điểm';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'route_name';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $route_model = $this->model->get('routeModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $join = array('table'=>'province','where'=>'route_province=province_id');

        $tongsodong = count($route_model->getAllRoute(null,$join));

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

            $search = '( route_name LIKE "%'.$keyword.'%" OR province_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['routes'] = $route_model->getAllRoute($data,$join);



        return $this->view->show('route/index');

    }


    public function addroute(){
        $route_model = $this->model->get('routeModel');

        if (isset($_POST['route_name'])) {
            if($route_model->getRouteByWhere(array('route_name'=>trim($_POST['route_name']),'route_province'=>trim($_POST['route_province'])))){
                echo 'Tên địa điểm đã tồn tại';
                return false;
            }

            $data = array(
                'route_province' => trim($_POST['route_province']),
                'route_name' => trim($_POST['route_name']),
                'route_lat' => trim($_POST['route_lat']),
                'route_long' => trim($_POST['route_long']),
            );
            $route_model->createRoute($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$route_model->getLastRoute()->route_id."|route|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'route',
                'user_log_table_name' => 'Địa điểm',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->route) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới địa điểm';

        $province = $this->model->get('provinceModel');

        $this->view->data['provinces'] = $province->getAllProvince();

        return $this->view->show('route/add');
    }

    public function editroute(){
        $route_model = $this->model->get('routeModel');

        if (isset($_POST['route_id'])) {
            $id = $_POST['route_id'];
            if($route_model->getAllRouteByWhere($id.' AND route_name = "'.trim($_POST['route_name']).'"'.' AND route_province = '.trim($_POST['route_province']))){
                echo 'Tên địa điểm đã tồn tại';
                return false;
            }

            $data = array(
                'route_province' => trim($_POST['route_province']),
                'route_name' => trim($_POST['route_name']),
                'route_lat' => trim($_POST['route_lat']),
                'route_long' => trim($_POST['route_long']),
            );
            $route_model->updateRoute($data,array('route_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|route|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'route',
                'user_log_table_name' => 'Địa điểm',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->route) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('route');

        }

        $this->view->data['title'] = 'Cập nhật địa điểm';

        $route_model = $this->model->get('routeModel');

        $route_data = $route_model->getRoute($id);

        $this->view->data['route_data'] = $route_data;

        if (!$route_data) {

            $this->view->redirect('route');

        }

        $province = $this->model->get('provinceModel');

        $this->view->data['provinces'] = $province->getAllProvince();

        return $this->view->show('route/edit');

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

            $this->view->redirect('route');

        }

        $this->view->data['title'] = 'Thông tin địa điểm';

        $route_model = $this->model->get('routeModel');

        $route_data = $route_model->getRoute($id);

        $this->view->data['route_data'] = $route_data;

        if (!$route_data) {

            $this->view->redirect('route');

        }

        $province = $this->model->get('provinceModel');

        $this->view->data['provinces'] = $province->getAllProvince();

        return $this->view->show('route/view');

    }

    public function getroute(){
        $route_model = $this->model->get('routeModel');

        $routes = $route_model->getAllRoute();
        $result = array();
        $i = 0;
        foreach ($routes as $route) {
            $result[$i]['id'] = $route->route_id;
            $result[$i]['text'] = $route->route_name;
            $i++;
        }
        echo json_encode($result);
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->route) || json_decode($_SESSION['user_permission_action'])->route != "route") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $route_model = $this->model->get('routeModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $route_model->deleteRoute($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|route|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'route',
                    'user_log_table_name' => 'Địa điểm',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $route_model->deleteRoute($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|route|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'route',
                    'user_log_table_name' => 'Địa điểm',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importroute(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->route) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('route/import');

    }


}

?>