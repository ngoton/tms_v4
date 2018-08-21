<?php

Class coordinateController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý lệnh điều xe';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;
            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;
            $nv = isset($_POST['nv']) ? $_POST['nv'] : null;
            $tha = isset($_POST['tha']) ? $_POST['tha'] : null;
            $na = isset($_POST['na']) ? $_POST['na'] : null;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'coordinate_date';

            $order = $this->registry->router->order ? $this->registry->router->order : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

            $batdau = '01/'.date('m/Y');
            $ketthuc = date('t/m/Y');
            $nv = 1;
            $tha = date('m');
            $na = date('Y');

        }

        $ngaybatdau = strtotime(str_replace('/', '-', $batdau));
        $ngayketthuc = strtotime(str_replace('/', '-', $ketthuc). ' + 1 days');
        $tha = (int)date('m',$ngaybatdau);
        $na = (int)date('Y',$ngaybatdau);
        $nv = ceil($tha/3);

        $coordinate_model = $this->model->get('coordinateModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        if ($batdau!="") {
            $data['where'] .= ' AND coordinate_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND coordinate_date < '.$ngayketthuc;
        }

        $join = array('table'=>'vehicle','where'=>'coordinate_vehicle=vehicle_id LEFT JOIN booking ON coordinate_booking=booking_id LEFT JOIN place ON coordinate_place=place_id LEFT JOIN unit ON coordinate_unit=unit_id','join'=>'LEFT JOIN');

        if (isset($_POST['filter'])) {
            if (isset($_POST['coordinate_place'])) {
                $data['where'] .= ' AND coordinate_place IN ('.implode(',',$_POST['coordinate_place']).')';
            }
            if (isset($_POST['coordinate_vehicle'])) {
                $data['where'] .= ' AND coordinate_place_to IN ('.implode(',',$_POST['coordinate_vehicle']).')';
            }
            if (isset($_POST['coordinate_booking'])) {
                $data['where'] .= ' AND coordinate_booking IN ('.implode(',',$_POST['coordinate_booking']).')';
            }
            if (isset($_POST['coordinate_type'])) {
                $data['where'] .= ' AND coordinate_type IN ('.implode(',',$_POST['coordinate_type']).')';
            }

            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($coordinate_model->getAllCoordinate($data,$join));

        $tongsotrang = ceil($tongsodong / $sonews);

        



        $this->view->data['page'] = $page;

        $this->view->data['order_by'] = $order_by;

        $this->view->data['order'] = $order;

        $this->view->data['keyword'] = $keyword;

        $this->view->data['limit'] = $limit;

        $this->view->data['pagination_stages'] = $pagination_stages;

        $this->view->data['tongsotrang'] = $tongsotrang;

        $this->view->data['sonews'] = $sonews;

        $this->view->data['batdau'] = $batdau;
        $this->view->data['ketthuc'] = $ketthuc;
        $this->view->data['nv'] = $nv;
        $this->view->data['tha'] = $tha;
        $this->view->data['na'] = $na;



        $data = array(
            'where'=>'1=1',

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            );

        if ($batdau!="") {
            $data['where'] .= ' AND coordinate_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND coordinate_date < '.$ngayketthuc;
        }

        if (isset($_POST['filter'])) {
            if (isset($_POST['coordinate_place'])) {
                $data['where'] .= ' AND coordinate_place IN ('.implode(',',$_POST['coordinate_place']).')';
            }
            if (isset($_POST['coordinate_vehicle'])) {
                $data['where'] .= ' AND coordinate_place_to IN ('.implode(',',$_POST['coordinate_vehicle']).')';
            }
            if (isset($_POST['coordinate_booking'])) {
                $data['where'] .= ' AND coordinate_booking IN ('.implode(',',$_POST['coordinate_booking']).')';
            }
            if (isset($_POST['coordinate_type'])) {
                $data['where'] .= ' AND coordinate_type IN ('.implode(',',$_POST['coordinate_type']).')';
            }
        }
        

        if ($keyword != '') {

            $search = '( 
                        vehicle_number  LIKE "%'.$keyword.'%" 
                        OR coordinate_number  LIKE "%'.$keyword.'%" 
                        OR booking_number  LIKE "%'.$keyword.'%" 
                        OR place_name  LIKE "%'.$keyword.'%" 
                        OR coordinate_comment  LIKE "%'.$keyword.'%" 
                    )';

            $data['where'] = $search;

        }

        $coordinates = $coordinate_model->getAllCoordinate($data,$join);

        $this->view->data['coordinates'] = $coordinates;


        return $this->view->show('coordinate/index');

    }

    public function getBooking(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $booking_model = $this->model->get('bookingModel');

            if ($_GET['keyword'] == "*") {
                $list = $booking_model->getAllBooking();
            }
            else{
                $data = array(
                'where'=>'( booking_number LIKE "%'.$_GET['keyword'].'%" )',
                );
                $list = $booking_model->getAllBooking($data);
            }

            foreach ($list as $rs) {
                $booking_number = $rs->booking_number;

                if ($_GET['keyword'] != "*") {
                    $booking_number = str_replace($_GET['keyword'], '<b>'.$_GET['keyword'].'</b>', $rs->booking_number);
                }

                echo '<li onclick="set_item(\''.$rs->booking_id.'\',\''.$rs->booking_number.'\',\''.$rs->booking_type.'\',\''.$_GET['offset'].'\')">'.$booking_number.'</li>';
            }
        }
    }

    public function addcoordinate(){
        $coordinate_model = $this->model->get('coordinateModel');
        $user_log_model = $this->model->get('userlogModel');

        if (isset($_POST['coordinate_data']) ) {
            
            $coordinate_data = json_decode($_POST['coordinate_data']);
            foreach ($coordinate_data as $v) {
                $data = array(
                    'coordinate_date' => strtotime(str_replace('/', '-', $_POST['coordinate_date'])),
                    'coordinate_code'=>trim($_POST['coordinate_code']),
                    'coordinate_vehicle'=>trim($v->coordinate_vehicle),
                    'coordinate_booking'=>trim($v->coordinate_booking),
                    'coordinate_booking_number'=>trim($v->coordinate_booking_number),
                    'coordinate_place'=>trim($v->coordinate_place),
                    'coordinate_unit'=>trim($v->coordinate_unit),
                    'coordinate_number'=>trim($v->coordinate_number),
                    'coordinate_type'=>trim($v->coordinate_type),
                    'coordinate_comment'=>trim($v->coordinate_comment),
                    'coordinate_create_user'=>$_SESSION['userid_logined'],
                );

                $coordinate_model->createCoordinate($data);
                $id_coordinate = $coordinate_model->getLastCoordinate()->coordinate_id;


                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$id_coordinate."|coordinate|".implode("-",$data)."\n"."\r\n";
                $this->lib->ghi_file("action_logs.txt",$text);


                
                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'coordinate',
                    'user_log_table_name' => 'Lệnh điều xe',
                    'user_log_action' => 'Thêm mới',
                    'user_log_data' => json_encode($data),
                );
                $user_log_model->createUser($data_log);
            }
            


            echo "Thêm thành công";
        }

    }

    public function add(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->coordinate) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới lệnh điều xe';

        $coordinate_model = $this->model->get('coordinateModel');
        $lastID = isset($coordinate_model->getLastCoordinate()->coordinate_code)?$coordinate_model->getLastCoordinate()->coordinate_code:'DX00';
        $lastID++;
        $this->view->data['lastID'] = $lastID;

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $unit_model = $this->model->get('unitModel');

        $units = $unit_model->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));

        $this->view->data['units'] = $units;

        return $this->view->show('coordinate/add');
    }

    public function editcoordinate(){
        $coordinate_model = $this->model->get('coordinateModel');

        if (isset($_POST['coordinate_id'])) {
            $id = $_POST['coordinate_id'];
            
            $data = array(
                'coordinate_date' => strtotime(str_replace('/', '-', $_POST['coordinate_date'])),
                'coordinate_code'=>trim($_POST['coordinate_code']),
                'coordinate_vehicle'=>trim($_POST['coordinate_vehicle']),
                'coordinate_booking'=>trim($_POST['coordinate_booking']),
                'coordinate_booking_number'=>trim($_POST['coordinate_booking_number']),
                'coordinate_place'=>trim($_POST['coordinate_place']),
                'coordinate_unit'=>trim($_POST['coordinate_unit']),
                'coordinate_number'=>trim($_POST['coordinate_number']),
                'coordinate_type'=>trim($_POST['coordinate_type']),
                'coordinate_comment'=>trim($_POST['coordinate_comment']),
                'coordinate_update_user'=>$_SESSION['userid_logined'],
            );

            $coordinate_model->updateCoordinate($data,array('coordinate_id'=>$id));
            
            $id_coordinate = $id;


            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|coordinate|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'coordinate',
                'user_log_table_name' => 'Lệnh điều xe',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->coordinate) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('coordinate');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật lệnh điều xe';

        $coordinate_model = $this->model->get('coordinateModel');

        $coordinate_data = $coordinate_model->getCoordinate($id);

        $this->view->data['coordinate_data'] = $coordinate_data;

        if (!$coordinate_data) {

            $this->view->redirect('coordinate');

        }


        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $booking_model = $this->model->get('bookingModel');

        $bookings = $booking_model->getAllBooking(array('order_by'=>'booking_number','order'=>'ASC'));

        $this->view->data['bookings'] = $bookings;

        $unit_model = $this->model->get('unitModel');

        $units = $unit_model->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));

        $this->view->data['units'] = $units;


        return $this->view->show('coordinate/edit');

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

            $this->view->redirect('coordinate');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin lệnh điều xe';

        $coordinate_model = $this->model->get('coordinateModel');

        $coordinate_data = $coordinate_model->getCoordinate($id);

        $this->view->data['coordinate_data'] = $coordinate_data;

        if (!$coordinate_data) {

            $this->view->redirect('coordinate');

        }

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $booking_model = $this->model->get('bookingModel');

        $bookings = $booking_model->getAllBooking(array('order_by'=>'booking_number','order'=>'ASC'));

        $this->view->data['bookings'] = $bookings;

        $unit_model = $this->model->get('unitModel');

        $units = $unit_model->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));

        $this->view->data['units'] = $units;


        return $this->view->show('coordinate/view');

    }

    
    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $vehicle_model = $this->model->get('vehicleModel');
        $place_model = $this->model->get('placeModel');
        $booking_model = $this->model->get('bookingModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $places = $place_model->getAllPlace(array('order_by'=>'place_code','order'=>'ASC'));
        $bookings = $booking_model->getAllBooking(array('order_by'=>'booking_number','order'=>'ASC'));

        $this->view->data['bookings'] = $bookings;
        $this->view->data['vehicles'] = $vehicles;
        $this->view->data['places'] = $places;

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];
        $this->view->data['nv'] = $_GET['nv'];
        $this->view->data['tha'] = $_GET['tha'];
        $this->view->data['na'] = $_GET['na'];
        $this->view->data['batdau'] = $_GET['batdau'];
        $this->view->data['ketthuc'] = $_GET['ketthuc'];

        return $this->view->show('coordinate/filter');
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->coordinate) || json_decode($_SESSION['user_permission_action'])->coordinate != "coordinate") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $coordinate_model = $this->model->get('coordinateModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {
                    
                    $coordinate_model->deleteCoordinate($data);

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|coordinate|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'coordinate',
                    'user_log_table_name' => 'Lệnh điều xe',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $coordinate_model->deleteCoordinate($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|coordinate|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'coordinate',
                    'user_log_table_name' => 'Lệnh điều xe',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importcoordinate(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->coordinate) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('coordinate/import');

    }


}

?>