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
                $data['where'] .= ' AND dispatch_customer IN ('.implode(',',$_POST['dispatch_customer']).')';
            }
            if (isset($_POST['dispatch_type'])) {
                $data['where'] .= ' AND dispatch_booking_type IN ('.implode(',',$_POST['dispatch_type']).')';
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
                $data['where'] .= ' AND dispatch_customer IN ('.implode(',',$_POST['dispatch_customer']).')';
            }
            if (isset($_POST['dispatch_type'])) {
                $data['where'] .= ' AND dispatch_booking_type IN ('.implode(',',$_POST['dispatch_type']).')';
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
                $dispatch_data[$dispatch->dispatch_id]['booking_type'] = $book->booking_type;
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
                'dispatch_port_from'=>trim($_POST['dispatch_port_from']),
                'dispatch_port_to'=>trim($_POST['dispatch_port_to']),
                'dispatch_ton'=>str_replace(',', '', $_POST['dispatch_ton']),
                'dispatch_unit'=>trim($_POST['dispatch_unit']),
                'dispatch_comment'=>trim($_POST['dispatch_comment']),
                'dispatch_shipment_temp'=>trim($_POST['dispatch_shipment_temp']),
                'dispatch_customer'=>trim($_POST['dispatch_customer']),
                'dispatch_booking'=>trim($_POST['dispatch_booking']),
                'dispatch_booking_type'=>trim($_POST['dispatch_booking_type']),
                'dispatch_place_from_sub'=>trim($_POST['dispatch_place_from_sub']),
                'dispatch_place_to_sub'=>trim($_POST['dispatch_place_to_sub']),
                'dispatch_start_date_sub' => strtotime(str_replace('/', '-', $_POST['dispatch_start_date_sub'])),
                'dispatch_end_date_sub' => strtotime(str_replace('/', '-', $_POST['dispatch_end_date_sub'])),
                'dispatch_port_from_sub'=>trim($_POST['dispatch_port_from_sub']),
                'dispatch_port_to_sub'=>trim($_POST['dispatch_port_to_sub']),
                'dispatch_ton_sub'=>str_replace(',', '', $_POST['dispatch_ton_sub']),
                'dispatch_unit_sub'=>trim($_POST['dispatch_unit_sub']),
                'dispatch_comment_sub'=>trim($_POST['dispatch_comment_sub']),
                'dispatch_shipment_temp_sub'=>trim($_POST['dispatch_shipment_temp_sub']),
                'dispatch_customer_sub'=>trim($_POST['dispatch_customer_sub']),
                'dispatch_booking_sub'=>trim($_POST['dispatch_booking_sub']),
                'dispatch_booking_type_sub'=>trim($_POST['dispatch_booking_type_sub']),
                'dispatch_create_user'=>$_SESSION['userid_logined'],
            );

            $dispatch_model->createDispatch($data);
            $id_dispatch = $dispatch_model->getLastDispatch()->dispatch_id;

            $shipment_temp_model = $this->model->get('shipmenttempModel');
            $booking_model = $this->model->get('bookingModel');

            if ($data['dispatch_shipment_temp']>0) {
                
                $shipment_temp = $shipment_temp_model->getShipment($data['dispatch_shipment_temp']);
                $data_shipment_temp = array(
                    'shipment_temp_status'=>1,
                );
                $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$data['dispatch_shipment_temp']));
            }

            if ($data['dispatch_booking']>0) {
                
                $booking = $booking_model->getBooking($data['dispatch_booking']);
                $data_booking = array(
                    'booking_status'=>2,
                );
                $booking_model->updateBooking($data_booking,array('booking_id'=>$data['dispatch_booking']));
            }

            if ($data['dispatch_shipment_temp_sub']>0) {
                
                $shipment_temp = $shipment_temp_model->getShipment($data['dispatch_shipment_temp_sub']);
                $data_shipment_temp = array(
                    'shipment_temp_status'=>1,
                );
                $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$data['dispatch_shipment_temp_sub']));
            }

            if ($data['dispatch_booking_sub']>0) {
                
                $booking = $booking_model->getBooking($data['dispatch_booking_sub']);
                $data_booking = array(
                    'booking_status'=>2,
                );
                $booking_model->updateBooking($data_booking,array('booking_id'=>$data['dispatch_booking_sub']));
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

      

        $ports = $place_model->getAllPlace(array('where'=>'place_port=1','order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['ports'] = $ports;

        $unit_model = $this->model->get('unitModel');

        $units = $unit_model->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));

        $this->view->data['units'] = $units;

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
                'dispatch_port_from'=>trim($_POST['dispatch_port_from']),
                'dispatch_port_to'=>trim($_POST['dispatch_port_to']),
                'dispatch_ton'=>str_replace(',', '', $_POST['dispatch_ton']),
                'dispatch_unit'=>trim($_POST['dispatch_unit']),
                'dispatch_comment'=>trim($_POST['dispatch_comment']),
                'dispatch_shipment_temp'=>trim($_POST['dispatch_shipment_temp']),
                'dispatch_customer'=>trim($_POST['dispatch_customer']),
                'dispatch_booking'=>trim($_POST['dispatch_booking']),
                'dispatch_booking_type'=>trim($_POST['dispatch_booking_type']),
                'dispatch_place_from_sub'=>trim($_POST['dispatch_place_from_sub']),
                'dispatch_place_to_sub'=>trim($_POST['dispatch_place_to_sub']),
                'dispatch_start_date_sub' => strtotime(str_replace('/', '-', $_POST['dispatch_start_date_sub'])),
                'dispatch_end_date_sub' => strtotime(str_replace('/', '-', $_POST['dispatch_end_date_sub'])),
                'dispatch_port_from_sub'=>trim($_POST['dispatch_port_from_sub']),
                'dispatch_port_to_sub'=>trim($_POST['dispatch_port_to_sub']),
                'dispatch_ton_sub'=>str_replace(',', '', $_POST['dispatch_ton_sub']),
                'dispatch_unit_sub'=>trim($_POST['dispatch_unit_sub']),
                'dispatch_comment_sub'=>trim($_POST['dispatch_comment_sub']),
                'dispatch_shipment_temp_sub'=>trim($_POST['dispatch_shipment_temp_sub']),
                'dispatch_customer_sub'=>trim($_POST['dispatch_customer_sub']),
                'dispatch_booking_sub'=>trim($_POST['dispatch_booking_sub']),
                'dispatch_booking_type_sub'=>trim($_POST['dispatch_booking_type_sub']),
                'dispatch_update_user'=>$_SESSION['userid_logined'],
            );

            $dispatch_model->updateDispatch($data,array('dispatch_id'=>$id));

            
            $shipment_temp_model = $this->model->get('shipmenttempModel');
            $booking_model = $this->model->get('bookingModel');

            if ($data['dispatch_shipment_temp']>0) {
                
                $shipment_temp = $shipment_temp_model->getShipment($data['dispatch_shipment_temp']);
                $data_shipment_temp = array(
                    'shipment_temp_status'=>1,
                );
                $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$data['dispatch_shipment_temp']));
            }

            if ($data['dispatch_booking']>0) {
                
                $booking = $booking_model->getBooking($data['dispatch_booking']);
                $data_booking = array(
                    'booking_status'=>2,
                );
                $booking_model->updateBooking($data_booking,array('booking_id'=>$data['dispatch_booking']));
            }

            if ($data['dispatch_shipment_temp_sub']>0) {
                
                $shipment_temp = $shipment_temp_model->getShipment($data['dispatch_shipment_temp_sub']);
                $data_shipment_temp = array(
                    'shipment_temp_status'=>1,
                );
                $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$data['dispatch_shipment_temp_sub']));
            }

            if ($data['dispatch_booking_sub']>0) {
                
                $booking = $booking_model->getBooking($data['dispatch_booking_sub']);
                $data_booking = array(
                    'booking_status'=>2,
                );
                $booking_model->updateBooking($data_booking,array('booking_id'=>$data['dispatch_booking_sub']));
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

        $customers = $customer_model->getCustomer($dispatch_data->dispatch_customer);

        $this->view->data['customers'] = $customers;

        if ($dispatch_data->dispatch_booking_sub>0) {
            $booking_subs = $booking_model->getBooking($dispatch_data->dispatch_booking_sub);

            $this->view->data['booking_subs'] = $booking_subs;

            $customer_subs = $customer_model->getCustomer($dispatch_data->dispatch_customer_sub);

            $this->view->data['customer_subs'] = $customer_subs;
        }
        
        $customer_lists = $customer_model->getAllCustomer(array('where'=>'customer_type=1','order_by'=>'customer_name','order'=>'ASC'));

        $this->view->data['customer_lists'] = $customer_lists;


        $place_data = array();

        foreach ($places as $place) {
            $place_data[$place->place_id] = $place->place_name;
            $place_data['name'][$place->place_name] = $place->place_id;
        }

        $this->view->data['place_data'] = $place_data;

        $shipment_temp_model = $this->model->get('shipmenttempModel');

        $join = array('table'=>'booking','where'=>'shipment_temp_booking=booking_id','join'=>'LEFT JOIN');

        if ($dispatch_data->dispatch_shipment_temp>0) {
            $shipment_temps = $shipment_temp_model->getAllShipment(array('where'=>'shipment_temp_id='.$dispatch_data->dispatch_shipment_temp.' OR shipment_temp_status IS NULL OR shipment_temp_status!=2'),$join);
        }
        else{
            $shipment_temps = $shipment_temp_model->getAllShipment(array('where'=>'shipment_temp_status IS NULL OR shipment_temp_status!=2'),$join);
        }

        $this->view->data['shipment_temps'] = $shipment_temps;

        if ($dispatch_data->dispatch_shipment_temp_sub>0) {
            $shipment_temp_subs = $shipment_temp_model->getAllShipment(array('where'=>'shipment_temp_id='.$dispatch_data->dispatch_shipment_temp_sub.' OR shipment_temp_status IS NULL OR shipment_temp_status!=2'),$join);
        }
        else{
            $shipment_temp_subs = $shipment_temp_model->getAllShipment(array('where'=>'shipment_temp_status IS NULL OR shipment_temp_status!=2'),$join);
        }
        

        $this->view->data['shipment_temp_subs'] = $shipment_temp_subs;

        $port_model = $this->model->get('portModel');

        $ports = $place_model->getAllPlace(array('where'=>'place_port=1','order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['ports'] = $ports;

        $unit_model = $this->model->get('unitModel');

        $units = $unit_model->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));

        $this->view->data['units'] = $units;


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

        $customers = $customer_model->getCustomer($dispatch_data->dispatch_customer);

        $this->view->data['customers'] = $customers;

        if ($dispatch_data->dispatch_booking_sub>0) {
            $booking_subs = $booking_model->getBooking($dispatch_data->dispatch_booking_sub);

            $this->view->data['booking_subs'] = $booking_subs;

            $customer_subs = $customer_model->getCustomer($dispatch_data->dispatch_customer_sub);

            $this->view->data['customer_subs'] = $customer_subs;
        }
        
        $customer_lists = $customer_model->getAllCustomer(array('where'=>'customer_type=1','order_by'=>'customer_name','order'=>'ASC'));

        $this->view->data['customer_lists'] = $customer_lists;


        $place_data = array();

        foreach ($places as $place) {
            $place_data[$place->place_id] = $place->place_name;
            $place_data['name'][$place->place_name] = $place->place_id;
        }

        $this->view->data['place_data'] = $place_data;

        $shipment_temp_model = $this->model->get('shipmenttempModel');

        $join = array('table'=>'booking','where'=>'shipment_temp_booking=booking_id','join'=>'LEFT JOIN');

        if ($dispatch_data->dispatch_shipment_temp>0) {
            $shipment_temps = $shipment_temp_model->getAllShipment(array('where'=>'shipment_temp_id='.$dispatch_data->dispatch_shipment_temp.' OR shipment_temp_status IS NULL OR shipment_temp_status!=2'),$join);
        }
        else{
            $shipment_temps = $shipment_temp_model->getAllShipment(array('where'=>'shipment_temp_status IS NULL OR shipment_temp_status!=2'),$join);
        }

        $this->view->data['shipment_temps'] = $shipment_temps;

        if ($dispatch_data->dispatch_shipment_temp_sub>0) {
            $shipment_temp_subs = $shipment_temp_model->getAllShipment(array('where'=>'shipment_temp_id='.$dispatch_data->dispatch_shipment_temp_sub.' OR shipment_temp_status IS NULL OR shipment_temp_status!=2'),$join);
        }
        else{
            $shipment_temp_subs = $shipment_temp_model->getAllShipment(array('where'=>'shipment_temp_status IS NULL OR shipment_temp_status!=2'),$join);
        }
        

        $this->view->data['shipment_temp_subs'] = $shipment_temp_subs;

        $port_model = $this->model->get('portModel');

        $ports = $place_model->getAllPlace(array('where'=>'place_port=1','order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['ports'] = $ports;

        $unit_model = $this->model->get('unitModel');

        $units = $unit_model->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));

        $this->view->data['units'] = $units;


        return $this->view->show('dispatch/view');

    }
    public function shipment($id){

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

        $booking_detail_model = $this->model->get('bookingdetailModel');

        $booking_details = $booking_detail_model->getAllBooking(array('where'=>'booking='.$bookings->booking_id.' AND booking_detail_id NOT IN (SELECT shipment_booking_detail FROM shipment WHERE shipment_booking_detail=booking_detail_id)'));

        $this->view->data['booking_details'] = $booking_details;

        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getCustomer($dispatch_data->dispatch_customer);

        $this->view->data['customers'] = $customers;

        $customer_lists = $customer_model->getAllCustomer(array('order_by'=>'customer_code','order'=>'ASC'));

        $this->view->data['customer_lists'] = $customer_lists;


        $unit_model = $this->model->get('unitModel');

        $units = $unit_model->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));

        $this->view->data['units'] = $units;

        $costlist_model = $this->model->get('costlistModel');

        $cost_lists = $costlist_model->getAllCost(array('order_by'=>'cost_list_name','order'=>'ASC'));

        $this->view->data['cost_lists'] = $cost_lists;

        $route_model = $this->model->get('routeModel');

        $routes = $route_model->getAllRoute(array('order_by'=>'route_name','order'=>'ASC'));

        $route_data = array();

        foreach ($routes as $route) {
            $route_data[$route->route_id] = $route->route_name;
            $route_data['name'][$route->route_name] = $route->route_id;
            $route_data['lat'][$route->route_id] = $route->route_lat;
            $route_data['long'][$route->route_id] = $route->route_long;
        }
        $this->view->data['route_data'] = $route_data;

        $road_model = $this->model->get('roadModel');
        $road_toll_model = $this->model->get('roadtollModel');
        

        $data = array(
            'where'=>'road_place_from = '.$dispatch_data->dispatch_place_from.' AND road_place_to = '.$dispatch_data->dispatch_place_to.' AND road_start_date <= '.$dispatch_data->dispatch_date.' AND (road_end_date IS NULL OR road_end_date=0 OR road_end_date >= '.$dispatch_data->dispatch_date.')',
        );

        $road_data = array();
        $km=0;$time=0;$oil=0;$police=0;$tire=0;$roadtoll=0;

        $roads = $road_model->getAllRoad($data);

        foreach ($roads as $road) {
            $road_data[] = array($route_data[$road->road_route_from],$route_data['lat'][$road->road_route_from],$route_data['long'][$road->road_route_from]);
            $road_data[] = array($route_data[$road->road_route_to],$route_data['lat'][$road->road_route_to],$route_data['long'][$road->road_route_to]);

            $km += $road->road_km;
            $time += $road->road_time;
            $oil += $road->road_oil;
            $police += $road->road_police;
            $tire += $road->road_tire;

            $tolls = $road_toll_model->getAllRoad(array('where'=>'road='.$road->road_id));
            foreach ($tolls as $toll) {
                $roadtoll += $toll->road_toll_money;
            }
        }

        $this->view->data['roads'] = $roads;
        $this->view->data['road_data'] = $road_data;

        $this->view->data['km'] = $km;
        $this->view->data['time'] = $time;
        $this->view->data['oil'] = $oil;
        $this->view->data['police'] = $police;
        $this->view->data['tire'] = $tire;
        $this->view->data['roadtoll'] = $roadtoll;

        $port_model = $this->model->get('portModel');

        $ports = $place_model->getAllPlace(array('where'=>'place_port=1','order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['ports'] = $ports;

        $warehouse_model = $this->model->get('warehouseModel');

        $data = array(
            'where'=>'(warehouse_place = '.$dispatch_data->dispatch_place_from.' OR warehouse_place = '.$dispatch_data->dispatch_place_to.') AND warehouse_start_date <= '.$dispatch_data->dispatch_date.' AND (warehouse_end_date IS NULL OR warehouse_end_date=0 OR warehouse_end_date >= '.$dispatch_data->dispatch_date.')',
        );

        $warehouses = $warehouse_model->getAllWarehouse($data);

        $warehouse_cont=0; $warehouse_ton=0;
        foreach ($warehouses as $warehouse) {
            $warehouse_cont += $warehouse->warehouse_cont;
            $warehouse_ton += $warehouse->warehouse_ton;
        }
        $this->view->data['warehouse_cont'] = $warehouse_cont;
        $this->view->data['warehouse_ton'] = $warehouse_ton;

        $lift_model = $this->model->get('liftModel');

        $data = array(
            'where'=>'(lift_place = '.$dispatch_data->dispatch_place_from.' OR lift_place = '.$dispatch_data->dispatch_place_to.' OR lift_place = '.$dispatch_data->dispatch_port_from.' OR lift_place = '.$dispatch_data->dispatch_port_to.') AND lift_unit = '.$dispatch_data->dispatch_unit.' AND lift_start_date <= '.$dispatch_data->dispatch_date.' AND (lift_end_date IS NULL OR lift_end_date=0 OR lift_end_date >= '.$dispatch_data->dispatch_date.')',
        );

        $lifts = $lift_model->getAllLift($data);

        $lift_on=0; $lift_off=0;
        $lift_on_null=0; $lift_off_null=0;
        foreach ($lifts as $lift) {
            if ($dispatch_data->dispatch_place_from == $lift->lift_place) {
                $lift_on += $lift->lift_on;
            }
            if ($dispatch_data->dispatch_place_to == $lift->lift_place) {
                $lift_off += $lift->lift_off;
            }
            if ($dispatch_data->dispatch_port_from == $lift->lift_place) {
                $lift_on_null += $lift->lift_on_null;
            }
            if ($dispatch_data->dispatch_port_to == $lift->lift_place) {
                $lift_off_null += $lift->lift_off_null;
            }
        }
        $this->view->data['lift_on'] = $lift_on;
        $this->view->data['lift_off'] = $lift_off;
        $this->view->data['lift_on_null'] = $lift_on_null;
        $this->view->data['lift_off_null'] = $lift_off_null;

        $shipdeposit_model = $this->model->get('shipdepositModel');

        $shipdeposits = $shipdeposit_model->getAllShipping(array('where'=>'shipdeposit_shipping='.$bookings->booking_shipping.' AND shipdeposit_unit = '.$dispatch_data->dispatch_unit.' AND shipdeposit_start_date <= '.$dispatch_data->dispatch_date.' AND (shipdeposit_end_date IS NULL OR shipdeposit_end_date=0 OR shipdeposit_end_date >= '.$dispatch_data->dispatch_date.')'));

        $deposit=0;
        foreach ($shipdeposits as $shipdeposit) {
            $deposit += $shipdeposit->shipdeposit_money;
        }
        $this->view->data['deposit'] = $deposit;

        $this->view->data['clean'] = 0;
        $this->view->data['trans'] = 0;
        $this->view->data['weight'] = 0;
        $this->view->data['document'] = 0;
        $this->view->data['release'] = 0;
        $this->view->data['park'] = 0;

        $sumcost = $police+$tire+$roadtoll+$warehouse_cont+$warehouse_ton+$lift_on+$lift_off+$lift_on_null+$lift_off_null+$deposit;
        $this->view->data['sumcost'] = $sumcost;

        return $this->view->show('dispatch/shipment');

    }

    public function viewshipment($id){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace();
        $place_data = array();

        foreach ($places as $place) {
            $place_data[$place->place_id] = $place->place_name;
            $place_data['name'][$place->place_name] = $place->place_id;
        }

        $this->view->data['place_data'] = $place_data;


        $shipment_model = $this->model->get('shipmentModel');

        $join = array('table'=>'vehicle','where'=>'shipment_vehicle=vehicle_id LEFT JOIN customer ON shipment_customer=customer_id LEFT JOIN romooc ON shipment_romooc=romooc_id LEFT JOIN staff ON shipment_staff=staff_id','join'=>'LEFT JOIN');
        $data = array(
            'where'=>'shipment_dispatch = '.$id,
            'order_by'=>'shipment_date',
            'order'=>'DESC',

            );

        $shipments = $shipment_model->getAllShipment($data,$join);

        $this->view->data['shipments'] = $shipments;

        $booking_model = $this->model->get('bookingModel');
        $shipping_model = $this->model->get('shippingModel');

        $shipment_data = array();
        foreach ($shipments as $shipment) {
            $book = $booking_model->getBooking($shipment->shipment_booking);
            if ($book) {
                $shipment_data[$shipment->shipment_id]['booking'] = $book->booking_number;
                $shipping = $shipping_model->getShipping($book->booking_shipping);
                if ($shipping) {
                    $shipment_data[$shipment->shipment_id]['shipping'] = $shipping->shipping_name;
                }
            }
        }
        $this->view->data['shipment_data'] = $shipment_data;


        return $this->view->show('dispatch/viewshipment');
    }

    public function getroad(){
        $this->view->disableLayout();

        $from = $_GET['from'];
        $to = $_GET['to'];
        $date = strtotime(str_replace('/', '-', $_GET['date']));

        $route_model = $this->model->get('routeModel');

        $routes = $route_model->getAllRoute(array('order_by'=>'route_name','order'=>'ASC'));

        $route_data = array();

        foreach ($routes as $route) {
            $route_data[$route->route_id] = $route->route_name;
            $route_data['name'][$route->route_name] = $route->route_id;
            $route_data['lat'][$route->route_id] = $route->route_lat;
            $route_data['long'][$route->route_id] = $route->route_long;
        }

        $road_model = $this->model->get('roadModel');

        $data = array(
            'where'=>'road_place_from = '.$from.' AND road_place_to = '.$to.' AND road_start_date <= '.$date.' AND (road_end_date IS NULL OR road_end_date=0 OR road_end_date >= '.$date.')',
        );

        $road_data = array();
        $km=0;$time=0;$oil=0;

        $roads = $road_model->getAllRoad($data);

        foreach ($roads as $road) {
            $road_data[] = array($route_data[$road->road_route_from],$route_data['lat'][$road->road_route_from],$route_data['long'][$road->road_route_from]);
            $road_data[] = array($route_data[$road->road_route_to],$route_data['lat'][$road->road_route_to],$route_data['long'][$road->road_route_to]);

            $km += $road->road_km;
            $time += $road->road_time;
            $oil += $road->road_oil;
        }

        $this->view->data['route_data'] = $route_data;
        $this->view->data['roads'] = $roads;
        $this->view->data['road_data'] = $road_data;

        $this->view->data['km'] = $km;
        $this->view->data['time'] = $time;
        $this->view->data['oil'] = $oil;

        return $this->view->show('dispatch/getroad');
    }
    public function getcost(){
        $this->view->disableLayout();
        $this->view->data['lib'] = $this->lib;

        $from = $_GET['from'];
        $to = $_GET['to'];
        $port_from = $_GET['port_from'];
        $port_to = $_GET['port_to'];
        $id = $_GET['dispatch'];
        $book = $_GET['book'];
        $ton = str_replace(',', '', $_GET['ton']);
        $unit = $_GET['unit'];
        $sub = $_GET['sub'];

        $date = strtotime(str_replace('/', '-', $_GET['date']));

        $booking_model = $this->model->get('bookingModel');
        $road_model = $this->model->get('roadModel');
        $road_toll_model = $this->model->get('roadtollModel');
        $warehouse_model = $this->model->get('warehouseModel');
        $lift_model = $this->model->get('liftModel');
        $shipdeposit_model = $this->model->get('shipdepositModel');


        

        $data_road = array(
            'where'=>'road_place_from = '.$from.' AND road_place_to = '.$to.' AND road_start_date <= '.$date.' AND (road_end_date IS NULL OR road_end_date=0 OR road_end_date >= '.$date.')',
        );

        $data_warehouse = array(
            'where'=>'(warehouse_place = '.$from.' OR warehouse_place = '.$to.') AND warehouse_start_date <= '.$date.' AND (warehouse_end_date IS NULL OR warehouse_end_date=0 OR warehouse_end_date >= '.$date.')',
        );

        $data_lift = array(
            'where'=>'(lift_place = "'.$from.'" OR lift_place = "'.$to.'" OR lift_place = "'.$port_from.'" OR lift_place = "'.$port_to.'") AND lift_unit = '.$unit.' AND lift_start_date <= '.$date.' AND (lift_end_date IS NULL OR lift_end_date=0 OR lift_end_date >= '.$date.')',
        );

        $lifts = $lift_model->getAllLift($data_lift);

        $lift_on=0; $lift_off=0;
        $lift_on_null=0; $lift_off_null=0;
        foreach ($lifts as $lift) {
            if ($from == $lift->lift_place) {
                $lift_on += $lift->lift_on;
            }
            if ($to == $lift->lift_place) {
                $lift_off += $lift->lift_off;
            }
            if ($port_from == $lift->lift_place) {
                $lift_on_null += $lift->lift_on_null;
            }
            if ($port_to == $lift->lift_place) {
                $lift_off_null += $lift->lift_off_null;
            }
        }

        
        
        $police=0;$tire=0;$roadtoll=0;

        $roads = $road_model->getAllRoad($data_road);

        foreach ($roads as $road) {
            
            $police += $road->road_police;
            $tire += $road->road_tire;

            $tolls = $road_toll_model->getAllRoad(array('where'=>'road='.$road->road_id));
            foreach ($tolls as $toll) {
                $roadtoll += $toll->road_toll_money;
            }
        }

        $warehouses = $warehouse_model->getAllWarehouse($data_warehouse);

        $warehouse_cont=0; $warehouse_ton=0;
        foreach ($warehouses as $warehouse) {
            $warehouse_cont += $warehouse->warehouse_cont;
            $warehouse_ton += $warehouse->warehouse_ton;
        }

        $bookings = array();
        $shipdeposits = array();

        if ($book>0) {
            $bookings = $booking_model->getBooking($book);

            $shipdeposits = $shipdeposit_model->getAllShipping(array('where'=>'shipdeposit_shipping='.$bookings->booking_shipping.' AND shipdeposit_unit = '.$unit.' AND shipdeposit_start_date <= '.$date.' AND (shipdeposit_end_date IS NULL OR shipdeposit_end_date=0 OR shipdeposit_end_date >= '.$date.')'));
        }
        

        $deposit=0;
        foreach ($shipdeposits as $shipdeposit) {
            $deposit += $shipdeposit->shipdeposit_money;
        }

        if ($sub==1) {
            $police=0;$tire=0;$roadtoll=0;
        }

        $this->view->data['roads'] = $roads;

        $this->view->data['police'] = $police;
        $this->view->data['tire'] = $tire;
        $this->view->data['roadtoll'] = $roadtoll;

        $this->view->data['bookings'] = $bookings;

        $this->view->data['warehouse_cont'] = $warehouse_cont;
        $this->view->data['warehouse_ton'] = $warehouse_ton;
        
        $this->view->data['lift_on'] = $lift_on;
        $this->view->data['lift_off'] = $lift_off;
        $this->view->data['lift_on_null'] = $lift_on_null;
        $this->view->data['lift_off_null'] = $lift_off_null;

        $this->view->data['deposit'] = $deposit;

        $this->view->data['clean'] = 0;
        $this->view->data['trans'] = 0;
        $this->view->data['weight'] = 0;
        $this->view->data['document'] = 0;
        $this->view->data['release'] = 0;
        $this->view->data['park'] = 0;

        $sumcost = $police+$tire+$roadtoll+$warehouse_cont+$warehouse_ton+$lift_on+$lift_off+$lift_on_null+$lift_off_null+$deposit;
        $this->view->data['sumcost'] = $sumcost;

        $customer_model = $this->model->get('customerModel');
        $customer_lists = $customer_model->getAllCustomer(array('order_by'=>'customer_code','order'=>'ASC'));

        $this->view->data['customer_lists'] = $customer_lists;

        $costlist_model = $this->model->get('costlistModel');

        $cost_lists = $costlist_model->getAllCost(array('order_by'=>'cost_list_name','order'=>'ASC'));

        $this->view->data['cost_lists'] = $cost_lists;

        return $this->view->show('dispatch/getcost');
    }

    public function getdispatch(){

        $id = $_GET['data'];
        $type = $_GET['type'];

        $result = array(
            'customer'=>null,
            'booking_type'=>null,
            'booking'=>null,
            'place_from'=>null,
            'place_to'=>null,
            'comment'=>null,
            'start_date'=>null,
            'end_date'=>null,
            'container'=>null,
            'port_from'=>null,
            'port_to'=>null
        );

        $dispatch_model = $this->model->get('dispatchModel');
        $booking_model = $this->model->get('bookingModel');
        $booking_detail_model = $this->model->get('bookingdetailModel');
        $customer_model = $this->model->get('customerModel');

        $dispatch_data = $dispatch_model->getDispatch($id);

        if ($type==1) {
            if ($dispatch_data->dispatch_customer_sub>0) {
               $bookings = $booking_model->getBooking($dispatch_data->dispatch_booking_sub);
                $customers = $customer_model->getCustomer($dispatch_data->dispatch_customer_sub);

                $result = array(
                    'customer'=>'<option value="'.$customers->customer_id.'">'.$customers->customer_name.'</option>',
                    'booking_type'=>$dispatch_data->dispatch_booking_type_sub,
                    'booking'=>'<option value="'.$bookings->booking_id.'">'.$bookings->booking_number.'</option>',
                    'place_from'=>$dispatch_data->dispatch_place_from_sub,
                    'place_to'=>$dispatch_data->dispatch_place_to_sub,
                    'comment'=>$dispatch_data->dispatch_comment_sub,
                    'start_date'=>date('d/m/Y H:m:s',$dispatch_data->dispatch_start_date_sub),
                    'end_date'=>date('d/m/Y H:m:s',$dispatch_data->dispatch_end_date_sub),
                    'container'=>'<option value="">Chọn</option>',
                    'port_from'=>$dispatch_data->dispatch_port_from_sub,
                    'port_to'=>$dispatch_data->dispatch_port_to_sub,
                );
            }
            
        }
        else{
            $bookings = $booking_model->getBooking($dispatch_data->dispatch_booking);
            $customers = $customer_model->getCustomer($dispatch_data->dispatch_customer);

            $result = array(
                'customer'=>'<option value="'.$customers->customer_id.'">'.$customers->customer_name.'</option>',
                'booking_type'=>$dispatch_data->dispatch_booking_type,
                'booking'=>'<option value="'.$bookings->booking_id.'">'.$bookings->booking_number.'</option>',
                'place_from'=>$dispatch_data->dispatch_place_from,
                'place_to'=>$dispatch_data->dispatch_place_to,
                'comment'=>$dispatch_data->dispatch_comment,
                'start_date'=>date('d/m/Y H:m:s',$dispatch_data->dispatch_start_date),
                'end_date'=>date('d/m/Y H:m:s',$dispatch_data->dispatch_end_date),
                'container'=>'<option value="">Chọn</option>',
                'port_from'=>$dispatch_data->dispatch_port_from,
                'port_to'=>$dispatch_data->dispatch_port_to,
            );
        }

        if (isset($bookings->booking_id)) {
            $booking_details = $booking_detail_model->getAllBooking(array('where'=>'booking='.$bookings->booking_id.' AND booking_detail_id NOT IN (SELECT shipment_booking_detail FROM shipment WHERE shipment_booking_detail=booking_detail_id)'));

        
            foreach ($booking_details as $detail) {
                $result['container'] .= '<option value="'.$detail->booking_detail_id.'">'.$detail->booking_detail_container.'</option>';
            }
        }
        
        


        echo json_encode($result);

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

                    if ($temps->dispatch_shipment_temp>0) {
                        $shipment_temp = $shipment_temp_model->getShipment($temps->dispatch_shipment_temp);
                        $data_shipment_temp = array(
                            'shipment_temp_status'=>null,
                        );
                        $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$shipment_temp->shipment_temp_id));
                    }
                    
                    if ($temps->dispatch_shipment_temp_sub>0) {
                        $shipment_temp_sub = $shipment_temp_model->getShipment($temps->dispatch_shipment_temp_sub);
                        $data_shipment_temp_sub = array(
                            'shipment_temp_status'=>null,
                        );
                        $shipment_temp_model->updateShipment($data_shipment_temp_sub,array('shipment_temp_id'=>$shipment_temp_sub->shipment_temp_id));
                    }

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

                if ($temps->dispatch_shipment_temp>0) {
                    $shipment_temp = $shipment_temp_model->getShipment($temps->dispatch_shipment_temp);
                    $data_shipment_temp = array(
                        'shipment_temp_status'=>null,
                    );
                    $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$shipment_temp->shipment_temp_id));
                }
                
                if ($temps->dispatch_shipment_temp_sub>0) {
                    $shipment_temp_sub = $shipment_temp_model->getShipment($temps->dispatch_shipment_temp_sub);
                    $data_shipment_temp_sub = array(
                        'shipment_temp_status'=>null,
                    );
                    $shipment_temp_model->updateShipment($data_shipment_temp_sub,array('shipment_temp_id'=>$shipment_temp_sub->shipment_temp_id));
                }
                

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