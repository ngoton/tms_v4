<?php

Class vehicleController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý xe';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'vehicle_number';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $vehicle_model = $this->model->get('vehicleModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        $join = array('table'=>'brand, country','where'=>'vehicle_brand=brand_id AND vehicle_country=country_id');

        if (isset($_POST['filter'])) {
            if (isset($_POST['vehicle_brand'])) {
                $data['where'] .= ' AND vehicle_brand IN ('.implode(',',$_POST['vehicle_brand']).')';
            }
            if (isset($_POST['vehicle_country'])) {
                $data['where'] .= ' AND vehicle_country IN ('.implode(',',$_POST['vehicle_country']).')';
            }
            if (isset($_POST['vehicle_owner'])) {
                if ($_POST['vehicle_owner']==0) {
                    $data['where'] .= ' AND (vehicle_owner IS NULL OR vehicle_owner=0)';
                }
                else{
                    $data['where'] .= ' AND vehicle_owner IN ('.implode(',',$_POST['vehicle_owner']).')';
                }
                
            }
            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($vehicle_model->getAllVehicle($data,$join));

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
            if (isset($_POST['vehicle_brand'])) {
                $data['where'] .= ' AND vehicle_brand IN ('.implode(',',$_POST['vehicle_brand']).')';
            }
            if (isset($_POST['vehicle_country'])) {
                $data['where'] .= ' AND vehicle_country IN ('.implode(',',$_POST['vehicle_country']).')';
            }
            if (isset($_POST['vehicle_owner'])) {
                if ($_POST['vehicle_owner']==0) {
                    $data['where'] .= ' AND (vehicle_owner IS NULL OR vehicle_owner=0)';
                }
                else{
                    $data['where'] .= ' AND vehicle_owner IN ('.implode(',',$_POST['vehicle_owner']).')';
                }
                
            }
        }

        if ($keyword != '') {

            $search = '( vehicle_number LIKE "%'.$keyword.'%" OR vehicle_model  LIKE "%'.$keyword.'%" OR country_name LIKE "%'.$keyword.'%" OR brand_name LIKE "%'.$keyword.'%" OR vehicle_year LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['vehicles'] = $vehicle_model->getAllVehicle($data,$join);



        return $this->view->show('vehicle/index');

    }


    public function addvehicle(){
        $vehicle_model = $this->model->get('vehicleModel');

        if (isset($_POST['vehicle_number'])) {
            if($vehicle_model->getVehicleByWhere(array('vehicle_number'=>trim($_POST['vehicle_number'])))){
                echo 'Số xe đã tồn tại';
                return false;
            }

            $data = array(
                'vehicle_brand' => trim($_POST['vehicle_brand']),
                'vehicle_model' => trim($_POST['vehicle_model']),
                'vehicle_year' => trim($_POST['vehicle_year']),
                'vehicle_country' => trim($_POST['vehicle_country']),
                'vehicle_owner' => isset($_POST['vehicle_owner'])?$_POST['vehicle_owner']:null,
                'vehicle_number' => trim($_POST['vehicle_number']),
                'vehicle_oil' => str_replace(',', '', $_POST['vehicle_oil']),
                'vehicle_volume' => str_replace(',', '', $_POST['vehicle_volume']),
            );
            $vehicle_model->createVehicle($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$vehicle_model->getLastVehicle()->vehicle_id."|vehicle|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'vehicle',
                'user_log_table_name' => 'Xe',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehicle) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới xe';

        $brand = $this->model->get('brandModel');

        $this->view->data['brands'] = $brand->getAllBrand();

        $country = $this->model->get('countryModel');

        $this->view->data['countrys'] = $country->getAllCountry();

        return $this->view->show('vehicle/add');
    }

    public function editvehicle(){
        $vehicle_model = $this->model->get('vehicleModel');

        if (isset($_POST['vehicle_id'])) {
            $id = $_POST['vehicle_id'];
            if($vehicle_model->getAllVehicleByWhere($id.' AND vehicle_number = "'.trim($_POST['vehicle_number']))){
                echo 'Số xe đã tồn tại';
                return false;
            }

            $data = array(
                'vehicle_brand' => trim($_POST['vehicle_brand']),
                'vehicle_model' => trim($_POST['vehicle_model']),
                'vehicle_year' => trim($_POST['vehicle_year']),
                'vehicle_country' => trim($_POST['vehicle_country']),
                'vehicle_owner' => isset($_POST['vehicle_owner'])?$_POST['vehicle_owner']:null,
                'vehicle_number' => trim($_POST['vehicle_number']),
                'vehicle_oil' => str_replace(',', '', $_POST['vehicle_oil']),
                'vehicle_volume' => str_replace(',', '', $_POST['vehicle_volume']),
            );
            $vehicle_model->updateVehicle($data,array('vehicle_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|vehicle|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'vehicle',
                'user_log_table_name' => 'Xe',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehicle) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('vehicle');

        }

        $this->view->data['title'] = 'Cập nhật xe';
        $this->view->data['lib'] = $this->lib;

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicle_data = $vehicle_model->getVehicle($id);

        $this->view->data['vehicle_data'] = $vehicle_data;

        if (!$vehicle_data) {

            $this->view->redirect('vehicle');

        }

        $brand = $this->model->get('brandModel');

        $this->view->data['brands'] = $brand->getAllbrand();

        $country = $this->model->get('countryModel');

        $this->view->data['countrys'] = $country->getAllCountry();

        return $this->view->show('vehicle/edit');

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

            $this->view->redirect('vehicle');

        }

        $this->view->data['title'] = 'Cập nhật xe';
        $this->view->data['lib'] = $this->lib;

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicle_data = $vehicle_model->getVehicle($id);

        $this->view->data['vehicle_data'] = $vehicle_data;

        if (!$vehicle_data) {

            $this->view->redirect('vehicle');

        }

        $brand = $this->model->get('brandModel');

        $this->view->data['brands'] = $brand->getAllbrand();

        $country = $this->model->get('countryModel');

        $this->view->data['countrys'] = $country->getAllCountry();

        return $this->view->show('vehicle/view');

    }

    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $brand_model = $this->model->get('brandModel');
        $country_model = $this->model->get('countryModel');

        $brands = $brand_model->getAllBrand();
        $countrys = $country_model->getAllCountry();

        $this->view->data['brands'] = $brands;
        $this->view->data['countrys'] = $countrys;

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('vehicle/filter');
    }

    public function getvehicle(){
        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($vehicles as $vehicle) {
            $result[$i]['id'] = $vehicle->vehicle_id;
            $result[$i]['text'] = $vehicle->vehicle_number;
            $i++;
        }
        echo json_encode($result);
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->vehicle) || json_decode($_SESSION['user_permission_action'])->vehicle != "vehicle") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $vehicle_model = $this->model->get('vehicleModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $vehicle_model->deleteVehicle($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|vehicle|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'vehicle',
                    'user_log_table_name' => 'Xe',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $vehicle_model->deleteVehicle($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|vehicle|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'vehicle',
                    'user_log_table_name' => 'Xe',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importvehicle(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehicle) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('vehicle/import');

    }


}

?>