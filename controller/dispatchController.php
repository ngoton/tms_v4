<?php

Class dispatchController Extends baseController {

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

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'dispatch_date';

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

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace();
        $place_data = array();

        foreach ($places as $place) {
            $place_data[$place->place_id] = $place->place_name;
            $place_data['name'][$place->place_name] = $place->place_id;
        }

        $this->view->data['place_data'] = $place_data;


        $dispatch_model = $this->model->get('dispatchModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        if ($batdau!="") {
            $data['where'] .= ' AND dispatch_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND dispatch_date < '.$ngayketthuc;
        }

        $join = array('table'=>'vehicle','where'=>'dispatch_vehicle=vehicle_id','join'=>'LEFT JOIN');

        if (isset($_POST['filter'])) {
            if (isset($_POST['dispatch_place_from'])) {
                $data['where'] .= ' AND dispatch_place_from IN ('.implode(',',$_POST['dispatch_place_from']).')';
            }
            if (isset($_POST['dispatch_place_to'])) {
                $data['where'] .= ' AND dispatch_place_to IN ('.implode(',',$_POST['dispatch_place_to']).')';
            }
            if (isset($_POST['dispatch_vehicle'])) {
                $data['where'] .= ' AND dispatch_vehicle IN ('.implode(',',$_POST['dispatch_vehicle']).')';
            }
            if (isset($_POST['dispatch_customer'])) {
                $data['where'] .= ' AND dispatch_booking_detail IN (SELECT booking_detail_id FROM booking_detail WHERE booking IN (SELECT booking_id FROM booking WHERE booking_customer IN ('.implode(',',$_POST['dispatch_customer']).')))';
            }
            if (isset($_POST['dispatch_type'])) {
                $data['where'] .= ' AND dispatch_booking_detail IN (SELECT booking_detail_id FROM booking_detail WHERE booking IN (SELECT booking_id FROM booking WHERE booking_type IN ('.implode(',',$_POST['dispatch_type']).')))';
            }

            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($dispatch_model->getAllDispatch($data,$join));

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
            $data['where'] .= ' AND dispatch_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND dispatch_date < '.$ngayketthuc;
        }

        if (isset($_POST['filter'])) {
            if (isset($_POST['dispatch_place_from'])) {
                $data['where'] .= ' AND dispatch_place_from IN ('.implode(',',$_POST['dispatch_place_from']).')';
            }
            if (isset($_POST['dispatch_place_to'])) {
                $data['where'] .= ' AND dispatch_place_to IN ('.implode(',',$_POST['dispatch_place_to']).')';
            }
            if (isset($_POST['dispatch_vehicle'])) {
                $data['where'] .= ' AND dispatch_vehicle IN ('.implode(',',$_POST['dispatch_vehicle']).')';
            }
            if (isset($_POST['dispatch_customer'])) {
                $data['where'] .= ' AND dispatch_booking_detail IN (SELECT booking_detail_id FROM booking_detail WHERE booking IN (SELECT booking_id FROM booking WHERE booking_customer IN ('.implode(',',$_POST['dispatch_customer']).')))';
            }
            if (isset($_POST['dispatch_type'])) {
                $data['where'] .= ' AND dispatch_booking_detail IN (SELECT booking_detail_id FROM booking_detail WHERE booking IN (SELECT booking_id FROM booking WHERE booking_type IN ('.implode(',',$_POST['dispatch_type']).')))';
            }
        }
        

        if ($keyword != '') {

            $search = '( dispatch_place_from IN (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%") 
                        OR dispatch_place_to IN (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%") 
                        OR vehicle_number  LIKE "%'.$keyword.'%" 
                        OR dispatch_code  LIKE "%'.$keyword.'%" 
                    )';

            $data['where'] = $search;

        }

        $dispatchs = $dispatch_model->getAllDispatch($data,$join);

        $this->view->data['dispatchs'] = $dispatchs;

        $booking_model = $this->model->get('bookingModel');
        $shipping_model = $this->model->get('shippingModel');

        $dispatch_data = array();
        foreach ($dispatchs as $dispatch) {
            $book = $booking_model->getBooking($dispatch->dispatch_booking);
            if ($book) {
                $dispatch_data[$dispatch->dispatch_id]['booking'] = $book->booking_number;
                $shipping = $shipping_model->getShipping($book->booking_shipping);
                if ($shipping) {
                    $dispatch_data[$dispatch->dispatch_id]['shipping'] = $shipping->shipping_name;
                }
            }
        }
        $this->view->data['dispatch_data'] = $dispatch_data;


        return $this->view->show('dispatch/index');

    }


    public function adddispatch(){
        $dispatch_model = $this->model->get('dispatchModel');

        if (isset($_POST['dispatch_vehicle']) ) {
            

            $data = array(
                'dispatch_date' => strtotime(str_replace('/', '-', $_POST['dispatch_date'])),
                'dispatch_code'=>trim($_POST['dispatch_code']),
                'dispatch_vehicle'=>trim($_POST['dispatch_vehicle']),
                'dispatch_romooc'=>trim($_POST['dispatch_romooc']),
                'dispatch_staff'=>trim($_POST['dispatch_staff']),
                'dispatch_place_from'=>trim($_POST['dispatch_place_from']),
                'dispatch_place_to'=>trim($_POST['dispatch_place_to']),
                'dispatch_start_date' => strtotime(str_replace('/', '-', $_POST['dispatch_start_date'])),
                'dispatch_end_date' => strtotime(str_replace('/', '-', $_POST['dispatch_end_date'])),
                'dispatch_comment'=>trim($_POST['dispatch_comment']),
                'dispatch_shipment_temp'=>trim($_POST['dispatch_shipment_temp']),
                'dispatch_booking'=>trim($_POST['dispatch_booking']),
                'dispatch_create_user'=>$_SESSION['userid_logined'],
            );

            $dispatch_model->createDispatch($data);
            $id_dispatch = $dispatch_model->getLastDispatch()->dispatch_id;

            $shipment_temp_model = $this->model->get('shipmenttempModel');
            if ($data['dispatch_shipment_temp']>0) {
                $shipment_temp = $shipment_temp_model->getShipment($data['dispatch_shipment_temp']);
                $data_shipment_temp = array(
                    'shipment_temp_status'=>1,
                );
                $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$data['dispatch_shipment_temp']));
            }


            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$id_dispatch."|dispatch|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'dispatch',
                'user_log_table_name' => 'Lệnh điều xe',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->dispatch) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới lệnh điều xe';

        $dispatch_model = $this->model->get('dispatchModel');
        $lastID = isset($dispatch_model->getLastDispatch()->dispatch_code)?$dispatch_model->getLastDispatch()->dispatch_code:'DX00';
        $lastID++;
        $this->view->data['lastID'] = $lastID;

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;

        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getAllCustomer(array('where'=>'customer_type=1','order_by'=>'customer_name','order'=>'ASC'));

        $this->view->data['customers'] = $customers;

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $place_data = array();

        foreach ($places as $place) {
            $place_data[$place->place_id] = $place->place_name;
            $place_data['name'][$place->place_name] = $place->place_id;
        }

        $this->view->data['place_data'] = $place_data;

        $shipment_temp_model = $this->model->get('shipmenttempModel');

        $join = array('table'=>'booking','where'=>'shipment_temp_booking=booking_id','join'=>'LEFT JOIN');
        $data = array(
            'where'=>'(shipment_temp_status IS NULL OR shipment_temp_status!=2)',
            'order_by'=>'shipment_temp_date',
            'order'=>'ASC'
        );
        if ($_SESSION['role_logined'] == 5) {
            $data['where'] .= ' AND shipment_temp_owner = '.$_SESSION['userid_logined'];
        }

        $shipment_temps = $shipment_temp_model->getAllShipment($data,$join);

        $this->view->data['shipment_temps'] = $shipment_temps;

        return $this->view->show('dispatch/add');
    }

    public function editdispatch(){
        $dispatch_model = $this->model->get('dispatchModel');

        if (isset($_POST['dispatch_id'])) {
            $id = $_POST['dispatch_id'];
            $temps = $dispatch_model->getDispatch($id);

            $data = array(
                'dispatch_date' => strtotime(str_replace('/', '-', $_POST['dispatch_date'])),
                'dispatch_code'=>trim($_POST['dispatch_code']),
                'dispatch_vehicle'=>trim($_POST['dispatch_vehicle']),
                'dispatch_romooc'=>trim($_POST['dispatch_romooc']),
                'dispatch_staff'=>trim($_POST['dispatch_staff']),
                'dispatch_place_from'=>trim($_POST['dispatch_place_from']),
                'dispatch_place_to'=>trim($_POST['dispatch_place_to']),
                'dispatch_start_date' => strtotime(str_replace('/', '-', $_POST['dispatch_start_date'])),
                'dispatch_end_date' => strtotime(str_replace('/', '-', $_POST['dispatch_end_date'])),
                'dispatch_comment'=>trim($_POST['dispatch_comment']),
                'dispatch_shipment_temp'=>trim($_POST['dispatch_shipment_temp']),
                'dispatch_booking'=>trim($_POST['dispatch_booking']),
                'dispatch_update_user'=>$_SESSION['userid_logined'],
            );

            $dispatch_model->updateDispatch($data,array('dispatch_id'=>$id));

            $shipment_temp_model = $this->model->get('shipmenttempModel');
            if ($data['dispatch_shipment_temp']>0) {
                $shipment_temp = $shipment_temp_model->getShipment($data['dispatch_shipment_temp']);
                $data_shipment_temp = array(
                    'shipment_temp_status'=>1,
                );
                $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$data['dispatch_shipment_temp']));
            }
            
            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|dispatch|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'dispatch',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->dispatch) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('dispatch');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật lệnh điều xe';

        $dispatch_model = $this->model->get('dispatchModel');

        $dispatch_data = $dispatch_model->getDispatch($id);

        $this->view->data['dispatch_data'] = $dispatch_data;

        if (!$dispatch_data) {

            $this->view->redirect('dispatch');

        }


        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $romooc_model = $this->model->get('romoocModel');

        $romoocs = $romooc_model->getRomooc($dispatch_data->dispatch_romooc);

        $this->view->data['romoocs'] = $romoocs;

        $staff_model = $this->model->get('staffModel');

        $staffs = $staff_model->getStaff($dispatch_data->dispatch_staff);

        $this->view->data['staffs'] = $staffs;

        $booking_model = $this->model->get('bookingModel');

        $bookings = $booking_model->getBooking($dispatch_data->dispatch_booking);

        $this->view->data['bookings'] = $bookings;

        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getCustomer($bookings->booking_customer);

        $this->view->data['customers'] = $customers;


        $place_data = array();

        foreach ($places as $place) {
            $place_data[$place->place_id] = $place->place_name;
            $place_data['name'][$place->place_name] = $place->place_id;
        }

        $this->view->data['place_data'] = $place_data;

        $shipment_temp_model = $this->model->get('shipmenttempModel');

        $join = array('table'=>'booking','where'=>'shipment_temp_booking=booking_id','join'=>'LEFT JOIN');

        $shipment_temps = $shipment_temp_model->getAllShipment(array('where'=>'shipment_temp_id='.$dispatch_data->dispatch_shipment_temp),$join);

        $this->view->data['shipment_temps'] = $shipment_temps;


        return $this->view->show('dispatch/edit');

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

            $this->view->redirect('dispatch');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin lệnh điều xe';

        $dispatch_model = $this->model->get('dispatchModel');

        $dispatch_data = $dispatch_model->getdispatch($id);

        $this->view->data['dispatch_data'] = $dispatch_data;

        if (!$dispatch_data) {

            $this->view->redirect('dispatch');

        }

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $romooc_model = $this->model->get('romoocModel');

        $romoocs = $romooc_model->getRomooc($dispatch_data->dispatch_romooc);

        $this->view->data['romoocs'] = $romoocs;

        $staff_model = $this->model->get('staffModel');

        $staffs = $staff_model->getStaff($dispatch_data->dispatch_staff);

        $this->view->data['staffs'] = $staffs;

        $booking_model = $this->model->get('bookingModel');

        $bookings = $booking_model->getBooking($dispatch_data->dispatch_booking);

        $this->view->data['bookings'] = $bookings;

        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getCustomer($bookings->booking_customer);

        $this->view->data['customers'] = $customers;

        $place_data = array();

        foreach ($places as $place) {
            $place_data[$place->place_id] = $place->place_name;
            $place_data['name'][$place->place_name] = $place->place_id;
        }

        $this->view->data['place_data'] = $place_data;

        $shipment_temp_model = $this->model->get('shipmenttempModel');

        $join = array('table'=>'booking','where'=>'shipment_temp_booking=booking_id','join'=>'LEFT JOIN');

        $shipment_temps = $shipment_temp_model->getAllShipment(array('where'=>'shipment_temp_id='.$dispatch_data->dispatch_shipment_temp),$join);

        $this->view->data['shipment_temps'] = $shipment_temps;


        return $this->view->show('dispatch/view');

    }

    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $customer_model = $this->model->get('customerModel');
        $place_model = $this->model->get('placeModel');
        $vehicle_model = $this->model->get('vehicleModel');

        $customers = $customer_model->getAllCustomer(array('where'=>'customer_type=1','order_by'=>'customer_name','order'=>'ASC'));
        $places = $place_model->getAllPlace(array('order_by'=>'place_code','order'=>'ASC'));
        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['customers'] = $customers;
        $this->view->data['places'] = $places;
        $this->view->data['vehicles'] = $vehicles;

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

        return $this->view->show('dispatch/filter');
    }


    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->dispatch) || json_decode($_SESSION['user_permission_action'])->dispatch != "dispatch") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $dispatch_model = $this->model->get('dispatchModel');
            $user_log_model = $this->model->get('userlogModel');
            $shipment_temp_model = $this->model->get('shipmenttempModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {
                    $temps = $dispatch_model->getDispatch($data);

                    $dispatch_model->deleteDispatch($data);

                    $shipment_temp = $shipment_temp_model->getShipment($temps->dispatch_shipment_temp);
                        $data_shipment_temp = array(
                            'shipment_temp_status'=>null,
                        );
                        $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$shipment_temp->shipment_temp_id));

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|dispatch|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'dispatch',
                    'user_log_table_name' => 'Lệnh điều xe',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{
                $temps = $dispatch_model->getDispatch($_POST['data']);

                $dispatch_model->deleteDispatch($_POST['data']);

                $shipment_temp = $shipment_temp_model->getShipment($temps->dispatch_shipment_temp);
                $data_shipment_temp = array(
                    'shipment_temp_status'=>null,
                );
                $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$shipment_temp->shipment_temp_id));

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|dispatch|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'dispatch',
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

    public function importdispatch(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->dispatch) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('dispatch/import');

    }


}

?>