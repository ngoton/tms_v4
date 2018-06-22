<?php

Class shipmentController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý phiếu vận chuyển';



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

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'shipment_date';

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


        $shipment_model = $this->model->get('shipmentModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        if ($batdau!="") {
            $data['where'] .= ' AND shipment_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND shipment_date < '.$ngayketthuc;
        }

        $join = array('table'=>'vehicle','where'=>'shipment_vehicle=vehicle_id LEFT JOIN customer ON shipment_customer=customer_id','join'=>'LEFT JOIN');

        if (isset($_POST['filter'])) {
            if (isset($_POST['shipment_place_from'])) {
                $data['where'] .= ' AND shipment_place_from IN ('.implode(',',$_POST['shipment_place_from']).')';
            }
            if (isset($_POST['shipment_place_to'])) {
                $data['where'] .= ' AND shipment_place_to IN ('.implode(',',$_POST['shipment_place_to']).')';
            }
            if (isset($_POST['shipment_vehicle'])) {
                $data['where'] .= ' AND shipment_vehicle IN ('.implode(',',$_POST['shipment_vehicle']).')';
            }
            if (isset($_POST['shipment_customer'])) {
                $data['where'] .= ' AND shipment_customer IN ('.implode(',',$_POST['shipment_customer']).')';
            }
            if (isset($_POST['shipment_type'])) {
                $data['where'] .= ' AND shipment_type IN ('.implode(',',$_POST['shipment_type']).')';
            }

            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($shipment_model->getAllShipment($data,$join));

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
            $data['where'] .= ' AND shipment_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND shipment_date < '.$ngayketthuc;
        }

        if (isset($_POST['filter'])) {
            if (isset($_POST['shipment_place_from'])) {
                $data['where'] .= ' AND shipment_place_from IN ('.implode(',',$_POST['shipment_place_from']).')';
            }
            if (isset($_POST['shipment_place_to'])) {
                $data['where'] .= ' AND shipment_place_to IN ('.implode(',',$_POST['shipment_place_to']).')';
            }
            if (isset($_POST['shipment_vehicle'])) {
                $data['where'] .= ' AND shipment_vehicle IN ('.implode(',',$_POST['shipment_vehicle']).')';
            }
            if (isset($_POST['shipment_customer'])) {
                $data['where'] .= ' AND shipment_customer IN ('.implode(',',$_POST['shipment_customer']).')';
            }
            if (isset($_POST['shipment_type'])) {
                $data['where'] .= ' AND shipment_type IN ('.implode(',',$_POST['shipment_type']).')';
            }
        }
        

        if ($keyword != '') {

            $search = '( shipment_place_from IN (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%") 
                        OR shipment_place_to IN (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%") 
                        OR vehicle_number  LIKE "%'.$keyword.'%" 
                        OR customer_name  LIKE "%'.$keyword.'%" 
                        OR shipment_do  LIKE "%'.$keyword.'%" 
                        OR shipment_container  LIKE "%'.$keyword.'%" 
                    )';

            $data['where'] = $search;

        }

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


        return $this->view->show('shipment/index');

    }


    public function addshipment(){
        $shipment_model = $this->model->get('shipmentModel');

        if (isset($_POST['shipment_vehicle']) ) {
            

            $data = array(
                'shipment_date' => strtotime(str_replace('/', '-', $_POST['shipment_date'])),
                'shipment_dispatch'=>trim($_POST['shipment_dispatch']),
                'shipment_customer'=>trim($_POST['shipment_customer']),
                'shipment_type'=>trim($_POST['shipment_type']),
                'shipment_do'=>trim($_POST['shipment_do']),
                'shipment_vehicle'=>trim($_POST['shipment_vehicle']),
                'shipment_romooc'=>trim($_POST['shipment_romooc']),
                'shipment_staff'=>trim($_POST['shipment_staff']),
                'shipment_booking'=>trim($_POST['shipment_booking']),
                'shipment_booking_detail'=>trim($_POST['shipment_booking_detail']),
                'shipment_container'=>trim($_POST['shipment_container']),
                'shipment_ton_receive'=>str_replace(',', '', $_POST['shipment_ton_receive']),
                'shipment_ton'=>str_replace(',', '', $_POST['shipment_ton']),
                'shipment_unit'=>trim($_POST['shipment_unit']),
                'shipment_place_from'=>trim($_POST['shipment_place_from']),
                'shipment_place_to'=>trim($_POST['shipment_place_to']),
                'shipment_start_date' => strtotime(str_replace('/', '-', $_POST['shipment_start_date'])),
                'shipment_end_date' => strtotime(str_replace('/', '-', $_POST['shipment_end_date'])),
                'shipment_comment'=>trim($_POST['shipment_comment']),
                'shipment_price'=>str_replace(',', '', $_POST['shipment_price']),
                'shipment_sub'=>trim($_POST['shipment_sub']),
                'shipment_road'=>implode(',', $_POST['shipment_road']),
                'shipment_create_user'=>$_SESSION['userid_logined'],
            );

            $shipment_model->createShipment($data);
            $id_shipment = $shipment_model->getLastShipment()->shipment_id;

            $dispatch_model = $this->model->get('dispatchModel');
            $dispatch = $dispatch_model->getDispatch($data['shipment_dispatch']);
            $dispatch_model->updateDispatch(array('dispatch_status'=>1,'dispatch_shipment_number'=>($dispatch->dispatch_shipment_number+1)),array('dispatch_id'=>$data['shipment_dispatch']));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$id_shipment."|shipment|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'shipment',
                'user_log_table_name' => 'Phiếu vận chuyển',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới phiếu vận chuyển';

        $shipment_model = $this->model->get('shipmentModel');
        $lastID = isset($shipment_model->getLastshipment()->shipment_code)?$shipment_model->getLastshipment()->shipment_code:'DX00';
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

        return $this->view->show('shipment/add');
    }

    public function editshipment(){
        $shipment_model = $this->model->get('shipmentModel');

        if (isset($_POST['shipment_id'])) {
            $id = $_POST['shipment_id'];
            $temps = $shipment_model->getShipment($id);

            $data = array(
                'shipment_date' => strtotime(str_replace('/', '-', $_POST['shipment_date'])),
                'shipment_dispatch'=>trim($_POST['shipment_dispatch']),
                'shipment_customer'=>trim($_POST['shipment_customer']),
                'shipment_type'=>trim($_POST['shipment_type']),
                'shipment_do'=>trim($_POST['shipment_do']),
                'shipment_vehicle'=>trim($_POST['shipment_vehicle']),
                'shipment_romooc'=>trim($_POST['shipment_romooc']),
                'shipment_staff'=>trim($_POST['shipment_staff']),
                'shipment_booking'=>trim($_POST['shipment_booking']),
                'shipment_booking_detail'=>trim($_POST['shipment_booking_detail']),
                'shipment_container'=>trim($_POST['shipment_container']),
                'shipment_ton_receive'=>str_replace(',', '', $_POST['shipment_ton_receive']),
                'shipment_ton'=>str_replace(',', '', $_POST['shipment_ton']),
                'shipment_unit'=>trim($_POST['shipment_unit']),
                'shipment_place_from'=>trim($_POST['shipment_place_from']),
                'shipment_place_to'=>trim($_POST['shipment_place_to']),
                'shipment_start_date' => strtotime(str_replace('/', '-', $_POST['shipment_start_date'])),
                'shipment_end_date' => strtotime(str_replace('/', '-', $_POST['shipment_end_date'])),
                'shipment_comment'=>trim($_POST['shipment_comment']),
                'shipment_price'=>str_replace(',', '', $_POST['shipment_price']),
                'shipment_sub'=>trim($_POST['shipment_sub']),
                'shipment_road'=>implode(',', $_POST['shipment_road']),
                'shipment_update_user'=>$_SESSION['userid_logined'],
            );

            $shipment_model->updateShipment($data,array('shipment_id'=>$id));

            
            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|shipment|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'shipment',
                'user_log_table_name' => 'Phiếu vận chuyển',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('shipment');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật phiếu vận chuyển';

        $shipment_model = $this->model->get('shipmentModel');

        $shipment_data = $shipment_model->getshipment($id);

        $this->view->data['shipment_data'] = $shipment_data;

        if (!$shipment_data) {

            $this->view->redirect('shipment');

        }


        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $romooc_model = $this->model->get('romoocModel');

        $romoocs = $romooc_model->getRomooc($shipment_data->shipment_romooc);

        $this->view->data['romoocs'] = $romoocs;

        $staff_model = $this->model->get('staffModel');

        $staffs = $staff_model->getStaff($shipment_data->shipment_staff);

        $this->view->data['staffs'] = $staffs;

        $booking_model = $this->model->get('bookingModel');

        $bookings = $booking_model->getBooking($shipment_data->shipment_booking);

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

        $shipment_temps = $shipment_temp_model->getAllShipment(array('where'=>'shipment_temp_id='.$shipment_data->shipment_shipment_temp),$join);

        $this->view->data['shipment_temps'] = $shipment_temps;


        return $this->view->show('shipment/edit');

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

            $this->view->redirect('shipment');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin phiếu vận chuyển';

        $shipment_model = $this->model->get('shipmentModel');

        $shipment_data = $shipment_model->getshipment($id);

        $this->view->data['shipment_data'] = $shipment_data;

        if (!$shipment_data) {

            $this->view->redirect('shipment');

        }

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $romooc_model = $this->model->get('romoocModel');

        $romoocs = $romooc_model->getRomooc($shipment_data->shipment_romooc);

        $this->view->data['romoocs'] = $romoocs;

        $staff_model = $this->model->get('staffModel');

        $staffs = $staff_model->getStaff($shipment_data->shipment_staff);

        $this->view->data['staffs'] = $staffs;

        $booking_model = $this->model->get('bookingModel');

        $bookings = $booking_model->getBooking($shipment_data->shipment_booking);

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

        $shipment_temps = $shipment_temp_model->getAllShipment(array('where'=>'shipment_temp_id='.$shipment_data->shipment_shipment_temp),$join);

        $this->view->data['shipment_temps'] = $shipment_temps;


        return $this->view->show('shipment/view');

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

        return $this->view->show('shipment/filter');
    }


    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->shipment) || json_decode($_SESSION['user_permission_action'])->shipment != "shipment") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $shipment_model = $this->model->get('shipmentModel');
            $user_log_model = $this->model->get('userlogModel');
            $dispatch_model = $this->model->get('dispatchModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {
                    $temps = $shipment_model->getShipment($data);

                    $shipment_model->deleteShipment($data);

                    $dispatch = $dispatch_model->getDispatch($temps->shipment_dispatch);
                    $data_dispatch = array(
                        'dispatch_shipment_number'=>($dispatch->dispatch_shipment_number-1),
                        'dispatch_status'=>$dispatch->dispatch_shipment_number==1?0:$dispatch->dispatch_status,
                    );
                    $dispatch_model->updateDispatch($data_dispatch,array('dispatch_id'=>$temps->shipment_dispatch));

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|shipment|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'shipment',
                    'user_log_table_name' => 'Phiếu vận chuyển',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{
                $temps = $shipment_model->getShipment($_POST['data']);

                $shipment_model->deleteShipment($_POST['data']);

                $dispatch = $dispatch_model->getDispatch($temps->shipment_dispatch);
                $data_dispatch = array(
                    'dispatch_shipment_number'=>($dispatch->dispatch_shipment_number-1),
                    'dispatch_status'=>$dispatch->dispatch_shipment_number==1?0:$dispatch->dispatch_status,
                );
                $dispatch_model->updateDispatch($data_dispatch,array('dispatch_id'=>$temps->shipment_dispatch));


                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|shipment|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'shipment',
                    'user_log_table_name' => 'phiếu vận chuyển',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importshipment(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('shipment/import');

    }


}

?>