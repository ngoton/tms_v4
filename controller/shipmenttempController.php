<?php

Class shipmenttempController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý đơn hàng';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'booking_date';

            $order = $this->registry->router->order ? $this->registry->router->order : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace();
        $place_data = array();

        foreach ($places as $place) {
            $place_data[$place->place_id] = $place->place_name;
            $place_data['name'][$place->place_name] = $place->place_id;
        }

        $this->view->data['place_data'] = $place_data;


        $booking_model = $this->model->get('bookingModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        $join = array('table'=>'customer','where'=>'booking_customer=customer_id','join'=>'LEFT JOIN');

        if (isset($_POST['filter'])) {
            if (isset($_POST['booking_place_from'])) {
                $data['where'] .= ' AND booking_place_from IN ('.implode(',',$_POST['booking_place_from']).')';
            }
            if (isset($_POST['booking_place_to'])) {
                $data['where'] .= ' AND booking_place_to IN ('.implode(',',$_POST['booking_place_to']).')';
            }
            if (isset($_POST['booking_customer'])) {
                $data['where'] .= ' AND booking_customer IN ('.implode(',',$_POST['booking_customer']).')';
            }
            if (isset($_POST['booking_type'])) {
                $data['where'] .= ' AND booking_type IN ('.implode(',',$_POST['booking_type']).')';
            }

            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($booking_model->getAllBooking($data,$join));

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
            if (isset($_POST['booking_place_from'])) {
                $data['where'] .= ' AND booking_place_from IN ('.implode(',',$_POST['booking_place_from']).')';
            }
            if (isset($_POST['booking_place_to'])) {
                $data['where'] .= ' AND booking_place_to IN ('.implode(',',$_POST['booking_place_to']).')';
            }
            if (isset($_POST['booking_customer'])) {
                $data['where'] .= ' AND booking_customer IN ('.implode(',',$_POST['booking_customer']).')';
            }
            if (isset($_POST['booking_type'])) {
                $data['where'] .= ' AND booking_type IN ('.implode(',',$_POST['booking_type']).')';
            }
        }
        

        if ($keyword != '') {

            $search = '( booking_place_from IN (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%") 
                        OR booking_place_to IN (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%") 
                        OR customer_name  LIKE "%'.$keyword.'%" 
                    )';

            $data['where'] = $search;

        }

        $bookings = $booking_model->getAllBooking($data,$join);

        $this->view->data['bookings'] = $bookings;


        return $this->view->show('booking/index');

    }


    public function addbooking(){
        $booking_model = $this->model->get('bookingModel');

        if (isset($_POST['booking_customer']) ) {
            

            $data = array(
                'booking_date' => strtotime(str_replace('/', '-', $_POST['booking_date'])),
                'booking_code'=>trim($_POST['booking_code']),
                'booking_customer'=>trim($_POST['booking_customer']),
                'booking_number'=>trim($_POST['booking_number']),
                'booking_type'=>trim($_POST['booking_type']),
                'booking_shipping'=>trim($_POST['booking_shipping']),
                'booking_shipping_name'=>trim($_POST['booking_shipping_name']),
                'booking_shipping_number'=>trim($_POST['booking_shipping_number']),
                'booking_place_from'=>trim($_POST['booking_place_from']),
                'booking_place_to'=>trim($_POST['booking_place_to']),
                'booking_start_date' => strtotime(str_replace('/', '-', $_POST['booking_start_date'])),
                'booking_end_date' => strtotime(str_replace('/', '-', $_POST['booking_end_date'])),
                'booking_sum' => str_replace(',', '', $_POST['booking_sum']),
                'booking_total' => str_replace(',', '', $_POST['booking_total']),
                'booking_comment'=>trim($_POST['booking_comment']),
                'booking_create_user'=>$_SESSION['userid_logined'],
            );

            $booking_model->createBooking($data);
            $id_booking = $booking_model->getLastBooking()->booking_id;

            $customer_sub_model = $this->model->get('customersubModel');

            $booking_detail_model = $this->model->get('bookingdetailModel');

            $booking_detail_data = json_decode($_POST['booking_detail_data']);

            if (isset($id_booking)) {
                foreach ($booking_detail_data as $v) {
                    $data_booking_detail = array(
                        'booking' => $id_booking,
                        'booking_detail_container' => trim($v->booking_detail_container),
                        'booking_detail_seal' => trim($v->booking_detail_seal),
                        'booking_detail_number' => str_replace(',', '', $v->booking_detail_number),
                        'booking_detail_unit' => trim($v->booking_detail_unit),
                        'booking_detail_price' => str_replace(',', '', $v->booking_detail_price),
                    );

                    $contributor = "";
                    if(trim($v->booking_detail_customer_sub) != ""){
                        $support = explode(',', trim($v->booking_detail_customer_sub));

                        if ($support) {
                            foreach ($support as $key) {
                                $name = $customer_sub_model->getCustomerByWhere(array('customer_sub_name'=>trim($key)));
                                if ($name) {
                                    if ($contributor == "")
                                        $contributor .= $name->customer_sub_id;
                                    else
                                        $contributor .= ','.$name->customer_sub_id;
                                }
                                else{
                                    $customer_sub_model->createCustomer(array('customer_sub_name'=>trim($key)));
                                    if ($contributor == "")
                                        $contributor .= $customer_sub_model->getLastCustomer()->customer_sub_id;
                                    else
                                        $contributor .= ','.$customer_sub_model->getLastCustomer()->customer_sub_id;
                                }
                                
                            }
                        }

                    }
                    $data_booking_detail['booking_detail_customer_sub'] = $contributor;

                    if ($v->id_booking_detail>0) {
                        $booking_detail_model->updateBooking($data_booking_detail,array('booking_detail_id'=>$v->id_booking_detail));
                    }
                    else{
                        if ($data_booking_detail['booking_detail_number']!="") {
                            $booking_detail_model->createBooking($data_booking_detail);
                        }
                        
                    }
                }

            }

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$id_booking."|booking|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'booking',
                'user_log_table_name' => 'Đơn hàng',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->booking) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới đơn hàng';

        $booking_model = $this->model->get('bookingModel');
        $lastID = isset($booking_model->getLastBooking()->booking_code)?$booking_model->getLastBooking()->booking_code:'DH00';
        $lastID++;
        $this->view->data['lastID'] = $lastID;

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;

        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getAllCustomer(array('where'=>'customer_type=1','order_by'=>'customer_name','order'=>'ASC'));

        $this->view->data['customers'] = $customers;

        $shipping_model = $this->model->get('shippingModel');

        $shippings = $shipping_model->getAllShipping(array('order_by'=>'shipping_name','order'=>'ASC'));

        $this->view->data['shippings'] = $shippings;

        $unit_model = $this->model->get('unitModel');

        $units = $unit_model->getAllunit(array('order_by'=>'unit_name','order'=>'ASC'));

        $this->view->data['units'] = $units;

        return $this->view->show('booking/add');
    }

    public function editbooking(){
        $booking_model = $this->model->get('bookingModel');

        if (isset($_POST['booking_id'])) {
            $id = $_POST['booking_id'];
            
            $data = array(
                'booking_date' => strtotime(str_replace('/', '-', $_POST['booking_date'])),
                'booking_code'=>trim($_POST['booking_code']),
                'booking_customer'=>trim($_POST['booking_customer']),
                'booking_number'=>trim($_POST['booking_number']),
                'booking_type'=>trim($_POST['booking_type']),
                'booking_shipping'=>trim($_POST['booking_shipping']),
                'booking_shipping_name'=>trim($_POST['booking_shipping_name']),
                'booking_shipping_number'=>trim($_POST['booking_shipping_number']),
                'booking_place_from'=>trim($_POST['booking_place_from']),
                'booking_place_to'=>trim($_POST['booking_place_to']),
                'booking_start_date' => strtotime(str_replace('/', '-', $_POST['booking_start_date'])),
                'booking_end_date' => strtotime(str_replace('/', '-', $_POST['booking_end_date'])),
                'booking_sum' => str_replace(',', '', $_POST['booking_sum']),
                'booking_total' => str_replace(',', '', $_POST['booking_total']),
                'booking_comment'=>trim($_POST['booking_comment']),
                'booking_update_user'=>$_SESSION['userid_logined'],
            );

            $booking_model->updateBooking($data,array('booking_id'=>$id));
            
            $id_booking = $id;

            $customer_sub_model = $this->model->get('customersubModel');

            $booking_detail_model = $this->model->get('bookingdetailModel');

            $booking_detail_data = json_decode($_POST['booking_detail_data']);

            if (isset($id_booking)) {
                foreach ($booking_detail_data as $v) {
                    $data_booking_detail = array(
                        'booking' => $id_booking,
                        'booking_detail_container' => trim($v->booking_detail_container),
                        'booking_detail_seal' => trim($v->booking_detail_seal),
                        'booking_detail_number' => str_replace(',', '', $v->booking_detail_number),
                        'booking_detail_unit' => trim($v->booking_detail_unit),
                        'booking_detail_price' => str_replace(',', '', $v->booking_detail_price),
                    );

                    $contributor = "";
                    if(trim($v->booking_detail_customer_sub) != ""){
                        $support = explode(',', trim($v->booking_detail_customer_sub));

                        if ($support) {
                            foreach ($support as $key) {
                                $name = $customer_sub_model->getCustomerByWhere(array('customer_sub_name'=>trim($key)));
                                if ($name) {
                                    if ($contributor == "")
                                        $contributor .= $name->customer_sub_id;
                                    else
                                        $contributor .= ','.$name->customer_sub_id;
                                }
                                else{
                                    $customer_sub_model->createCustomer(array('customer_sub_name'=>trim($key)));
                                    if ($contributor == "")
                                        $contributor .= $customer_sub_model->getLastCustomer()->customer_sub_id;
                                    else
                                        $contributor .= ','.$customer_sub_model->getLastCustomer()->customer_sub_id;
                                }
                                
                            }
                        }

                    }
                    $data_booking_detail['booking_detail_customer_sub'] = $contributor;

                    if ($v->id_booking_detail>0) {
                        $booking_detail_model->updateBooking($data_booking_detail,array('booking_detail_id'=>$v->id_booking_detail));
                    }
                    else{
                        if ($data_booking_detail['booking_detail_number']!="") {
                            $booking_detail_model->createBooking($data_booking_detail);
                        }
                        
                    }
                }

            }

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|booking|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'booking',
                'user_log_table_name' => 'Đơn hàng',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->booking) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('booking');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật đơn hàng';

        $booking_model = $this->model->get('bookingModel');

        $booking_data = $booking_model->getBooking($id);

        $this->view->data['booking_data'] = $booking_data;

        if (!$booking_data) {

            $this->view->redirect('booking');

        }

        $booking_detail_model = $this->model->get('bookingdetailModel');

        $booking_details = $booking_detail_model->getAllBooking(array('where'=>'booking='.$id));
        $this->view->data['booking_details'] = $booking_details;

        $customer_sub_model = $this->model->get('customersubModel');
        $customer_sub = array();
        foreach ($booking_details as $booking_detail) {
            $sts = explode(',', $booking_detail->booking_detail_customer_sub);
            foreach ($sts as $key) {
                $subs = $customer_sub_model->getCustomer($key);
                if($subs){
                    if (!isset($customer_sub[$booking_detail->booking_detail_id]))
                        $customer_sub[$booking_detail->booking_detail_id] = $subs->customer_sub_name;
                    else
                        $customer_sub[$booking_detail->booking_detail_id] .= ','.$subs->customer_sub_name;
                }
                
            }
        }
        
        
        $this->view->data['customer_sub'] = $customer_sub;

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;

        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getAllCustomer(array('where'=>'customer_type=1','order_by'=>'customer_name','order'=>'ASC'));

        $this->view->data['customers'] = $customers;

        $shipping_model = $this->model->get('shippingModel');

        $shippings = $shipping_model->getAllShipping(array('order_by'=>'shipping_name','order'=>'ASC'));

        $this->view->data['shippings'] = $shippings;

        $unit_model = $this->model->get('unitModel');

        $units = $unit_model->getAllunit(array('order_by'=>'unit_name','order'=>'ASC'));

        $this->view->data['units'] = $units;


        return $this->view->show('booking/edit');

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

            $this->view->redirect('booking');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin đơn hàng';

        $booking_model = $this->model->get('bookingModel');

        $booking_data = $booking_model->getBooking($id);

        $this->view->data['booking_data'] = $booking_data;

        if (!$booking_data) {

            $this->view->redirect('booking');

        }

        $booking_detail_model = $this->model->get('bookingdetailModel');

        $booking_details = $booking_detail_model->getAllBooking(array('where'=>'booking='.$id));
        $this->view->data['booking_details'] = $booking_details;

        $customer_sub_model = $this->model->get('customersubModel');
        $customer_sub = array();
        foreach ($booking_details as $booking_detail) {
            $sts = explode(',', $booking_detail->booking_detail_customer_sub);
            foreach ($sts as $key) {
                $subs = $customer_sub_model->getCustomer($key);
                if($subs){
                    if (!isset($customer_sub[$booking_detail->booking_detail_id]))
                        $customer_sub[$booking_detail->booking_detail_id] = $subs->customer_sub_name;
                    else
                        $customer_sub[$booking_detail->booking_detail_id] .= ','.$subs->customer_sub_name;
                }
                
            }
        }
        
        
        $this->view->data['customer_sub'] = $customer_sub;

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;

        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getAllCustomer(array('where'=>'customer_type=1','order_by'=>'customer_name','order'=>'ASC'));

        $this->view->data['customers'] = $customers;

        $shipping_model = $this->model->get('shippingModel');

        $shippings = $shipping_model->getAllShipping(array('order_by'=>'shipping_name','order'=>'ASC'));

        $this->view->data['shippings'] = $shippings;

        $unit_model = $this->model->get('unitModel');

        $units = $unit_model->getAllunit(array('order_by'=>'unit_name','order'=>'ASC'));

        $this->view->data['units'] = $units;


        return $this->view->show('booking/view');

    }

    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $customer_model = $this->model->get('customerModel');
        $place_model = $this->model->get('placeModel');

        $customers = $customer_model->getAllCustomer(array('where'=>'customer_type=1','order_by'=>'customer_name','order'=>'ASC'));
        $places = $place_model->getAllPlace(array('order_by'=>'place_code','order'=>'ASC'));

        $this->view->data['customers'] = $customers;
        $this->view->data['places'] = $places;

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('booking/filter');
    }

    public function deletebookingdetail(){
        if (isset($_POST['data'])) {
            $booking_detail_model = $this->model->get('bookingdetailModel');

            $booking_detail_model->queryBooking('DELETE FROM booking_detail WHERE booking_detail_id='.$_POST['data'].' AND booking='.$_POST['booking']);
        }
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->booking) || json_decode($_SESSION['user_permission_action'])->booking != "booking") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $booking_model = $this->model->get('bookingModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $booking_model->deleteBooking($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|booking|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'booking',
                    'user_log_table_name' => 'Đơn hàng',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $booking_model->deleteBooking($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|booking|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'booking',
                    'user_log_table_name' => 'Đơn hàng',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importbooking(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->booking) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('booking/import');

    }


}

?>