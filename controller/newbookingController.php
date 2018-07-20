<?php

Class newbookingController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Danh sách lô hàng';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'booking_id';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 18446744073709;

        }



        $join = array('table'=>'customer','where'=>'customer.customer_id = booking.booking_customer');



        $booking_model = $this->model->get('bookingModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;



        $data = array(

            'where' => '(booking_sum_receive IS NULL OR booking_sum>booking_sum_receive) AND booking_end_date >= '.strtotime(date('d-m-Y')),

        );

        

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

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            'where' => '(booking_sum_receive IS NULL OR booking_sum>booking_sum_receive) AND booking_end_date >= '.strtotime(date('d-m-Y')),

            );

        

        if ($keyword != '') {

            $search = '( 

                    OR customer_name LIKE "%'.$keyword.'%"

                    OR booking_place_from in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                    OR booking_place_to in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                 )';

            $data['where'] = $search;

        }


        $booking_data = $booking_model->getAllBooking($data, $join);

        $this->view->data['bookings'] = $booking_data;



        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace();
        $place_data = array();

        foreach ($places as $place) {
            $place_data['place_id'][$place->place_id] = $place->place_id;
            $place_data['place_name'][$place->place_id] = $place->place_name;
        }

        $this->view->data['place'] = $place_data;

        
        $this->view->show('newbooking/index');

    }



    public function complete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->newbooking) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (isset($_POST['data'])) {


            $booking_model = $this->model->get('bookingModel');

            $booking = $booking_model->getBooking(trim($_POST['data']));


            $data_booking = array(

                'booking_sum_receive' => $booking->booking_sum_receive+trim(str_replace(',','',$_POST['ton'])),

            );


            $booking_model->updateBooking($data_booking,array('booking_id'=>$booking->booking_id));


            $booking = $booking_model->getBooking(trim($_POST['data']));

            if ( ($booking->booking_sum-$booking->booking_sum_receive) <= 0 && $booking->booking_status=="") {

                $booking_model->updateBooking(array('booking_status'=>1),array('booking_id'=>$booking->booking_id));

            }


            $shipment = $this->model->get('shipmenttempModel');

            $data = array(

                        'shipment_temp_date' => strtotime(str_replace('/', '-', $_POST['date'])),

                        'shipment_temp_owner' => $_SESSION['userid_logined'],

                        'shipment_temp_booking' => trim($_POST['data']),

                        'shipment_temp_status' => 0,

                        'shipment_temp_ton' => trim(str_replace(',','',$_POST['ton'])),

                        'shipment_temp_number' => trim(str_replace(',','',$_POST['number'])),

                        );


            $shipment->createShipment($data);

            echo "Nhận hàng thành công";

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."complete"."|".$_POST['data']."|newbooking|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'shipment_temp',
                'user_log_table_name' => 'Lô hàng mới',
                'user_log_action' => 'Nhận',
                'user_log_data' => json_encode($data),
            );
            $user_log_model->createUser($data_log);

                    

        }

    }



    

    

}

?>