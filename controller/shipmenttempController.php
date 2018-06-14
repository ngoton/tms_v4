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

        $this->view->data['title'] = 'Quản lý đơn hàng đã nhận';



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

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'shipment_temp_date';

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


        $shipment_temp_model = $this->model->get('shipmenttempModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'shipment_temp_date >= '.$ngaybatdau.' AND shipment_temp_date < '.$ngayketthuc,
        );

        $join = array('table'=>'booking','where'=>'shipment_temp_booking=booking_id LEFT JOIN customer ON booking_customer=customer_id','join'=>'LEFT JOIN');

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

        $tongsodong = count($shipment_temp_model->getAllShipment($data,$join));

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
            'where'=>'shipment_temp_date >= '.$ngaybatdau.' AND shipment_temp_date < '.$ngayketthuc,

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
                        OR booking_number  LIKE "%'.$keyword.'%" 
                        OR booking_code  LIKE "%'.$keyword.'%" 
                    )';

            $data['where'] = $search;

        }

        $shipment_temps = $shipment_temp_model->getAllShipment($data,$join);

        $this->view->data['shipment_temps'] = $shipment_temps;


        return $this->view->show('shipmenttemp/index');

    }


    public function editshipmenttemp(){
        $shipment_temp_model = $this->model->get('shipmenttempModel');

        if (isset($_POST['shipment_temp_id'])) {
            $id = $_POST['shipment_temp_id'];

            $temps = $shipment_temp_model->getShipment($id);
            
            $data = array(
                'shipment_temp_ton' => trim(str_replace(',','',$_POST['shipment_temp_ton'])),
                'shipment_temp_number' => trim(str_replace(',','',$_POST['shipment_temp_number'])),
            );

            $shipment_temp_model->updateShipment($data,array('shipment_temp_id'=>$id));
            
            $booking_model = $this->model->get('bookingModel');

            $booking = $booking_model->getBooking($temps->shipment_temp_booking);

            $data_booking = array(
                'booking_sum_receive' => $booking->booking_sum_receive-$temps->shipment_temp_ton+$data['shipment_temp_ton'],
            );
            $booking_model->updateBooking($data_booking,array('booking_id'=>$booking->booking_id));

            $booking = $booking_model->getBooking($temps->shipment_temp_booking);

            if ( ($booking->booking_sum-$booking->booking_sum_receive) <= 0 && $booking->booking_status=="") {
                $booking_model->updateBooking(array('booking_status'=>1),array('booking_id'=>$booking->booking_id));
            }

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|shipment_temp|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'shipment_temp',
                'user_log_table_name' => 'Đơn hàng nhận',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipmenttemp) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('shipmenttemp');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật đơn hàng';

        $shipment_temp_model = $this->model->get('shipmenttempModel');

        $shipment_temp_data = $shipment_temp_model->getShipment($id);

        $this->view->data['shipment_temp_data'] = $shipment_temp_data;

        if (!$shipment_temp_data) {

            $this->view->redirect('shipmenttemp');

        }

        return $this->view->show('shipmenttemp/edit');

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

            $this->view->redirect('shipmenttemp');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin đơn hàng';

        $shipment_temp_model = $this->model->get('shipmenttempModel');

        $shipment_temp_data = $shipment_temp_model->getShipment($id);

        $this->view->data['shipment_temp_data'] = $shipment_temp_data;

        if (!$shipment_temp_data) {

            $this->view->redirect('shipmenttemp');

        }

        return $this->view->show('shipmenttemp/view');

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
        $this->view->data['nv'] = $_GET['nv'];
        $this->view->data['tha'] = $_GET['tha'];
        $this->view->data['na'] = $_GET['na'];
        $this->view->data['batdau'] = $_GET['batdau'];
        $this->view->data['ketthuc'] = $_GET['ketthuc'];

        return $this->view->show('shipmenttemp/filter');
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->shipmenttemp) || json_decode($_SESSION['user_permission_action'])->shipmenttemp != "shipmenttemp") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $shipment_temp_model = $this->model->get('shipmenttempModel');
            $user_log_model = $this->model->get('userlogModel');
            $booking_model = $this->model->get('bookingModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {
                    $temps = $shipment_temp_model->getShipment($data);

                    $shipment_temp_model->deleteShipment($data);

                    $booking = $booking_model->getBooking($temps->shipment_temp_booking);

                    $data_booking = array(
                        'booking_sum_receive' => $booking->booking_sum_receive-$temps->shipment_temp_ton,
                        'booking_status' => null,
                    );
                    $booking_model->updateBooking($data_booking,array('booking_id'=>$booking->booking_id));


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|shipment_temp|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'shipment_temp',
                    'user_log_table_name' => 'Đơn hàng nhận',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{
                $temps = $shipment_temp_model->getShipment($_POST['data']);

                $shipment_temp_model->deleteShipment($_POST['data']);

                $booking = $booking_model->getBooking($temps->shipment_temp_booking);

                    $data_booking = array(
                        'booking_sum_receive' => $booking->booking_sum_receive-$temps->shipment_temp_ton,
                        'booking_status' => null,
                    );
                    $booking_model->updateBooking($data_booking,array('booking_id'=>$booking->booking_id));

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|shipment_temp|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'shipment_temp',
                    'user_log_table_name' => 'Đơn hàng nhận',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importshipmenttemp(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipmenttemp) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('shipmenttemp/import');

    }


}

?>