<?php

Class newshipmentController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Danh sách chuyến hàng';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'marketing_id';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 18446744073709;

        }



        $join = array('table'=>'customer, cont_unit','where'=>'customer.customer_id = marketing.customer AND cont_unit=cont_unit_id');



        $marketing_model = $this->model->get('marketingModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;



        $data = array(

            'where' => '(status IS NULL OR status != 1) AND marketing_end >= '.strtotime(date('d-m-Y')),

        );

        

        $tongsodong = count($marketing_model->getAllMarketing($data,$join));

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

            'where' => '(status IS NULL OR status != 1) AND marketing_end >= '.strtotime(date('d-m-Y')),

            );

        

        if ($keyword != '') {

            $search = '( 

                    OR customer_name LIKE "%'.$keyword.'%"

                    OR marketing_from in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                    OR marketing_to in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                 )';

            $data['where'] = $search;

        }

        

        $marketing_data = $marketing_model->getAllMarketing($data, $join);

        

        $this->view->data['marketings'] = $marketing_data;



        $this->view->data['lastID'] = isset($marketing_model->getLastMarketing()->marketing_id)?$marketing_model->getLastMarketing()->marketing_id:0;



        $place_model = $this->model->get('placeModel');



        $place_data = array();



        foreach ($marketing_data as $ship) {

            



            $places = $place_model->getAllPlace(array('where'=>'(place_id = '.$ship->marketing_from.' OR place_id = '.$ship->marketing_to.')'));

        



            foreach ($places as $place) {

                

                    $place_data['place_id'][$place->place_id] = $place->place_id;

                    $place_data['place_name'][$place->place_id] = $place->place_name;

                

                

            }

        }



        $this->view->data['place'] = $place_data;

        $customer_sub_model = $this->model->get('customersubModel');
        $customer_subs = $customer_sub_model->getAllCustomer();
        $customer_sub_data = array();
        foreach ($customer_subs as $customer_sub) {
            $customer_sub_data['name'][$customer_sub->customer_sub_id] = $customer_sub->customer_sub_name;
        }
        $this->view->data['customer_sub_data'] = $customer_sub_data;

        

        $this->view->show('newshipment/index');

    }



    public function checkroad(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['data'])) {

            $road_model = $this->model->get('roadModel');

            $marketing_model = $this->model->get('marketingModel');



            $marketing = $marketing_model->getMarketing(trim($_POST['data']));



            $road = $road_model->getRoadByWhere(array('road_from'=>$marketing->marketing_from,'road_to'=>$marketing->marketing_to));



            if (!$road) {

                echo 1;

            }

            else{

                echo 0;

            }

        }

    }



    public function complete(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['data'])) {



            $marketing_model = $this->model->get('marketingModel');



            $marketing = $marketing_model->getMarketing(trim($_POST['data']));



            $data_marketing = array(

                'marketing_ton_use' => $marketing->marketing_ton_use+trim(str_replace(',','',$_POST['ton'])),

            );



            

            $marketing_model->updateMarketing($data_marketing,array('marketing_id'=>$marketing->marketing_id));

            

            $marketing = $marketing_model->getMarketing(trim($_POST['data']));

            if ( ($marketing->marketing_ton-$marketing->marketing_ton_use) <= 0) {

                $marketing_model->updateMarketing(array('status'=>1),array('marketing_id'=>$marketing->marketing_id));

            }



            $shipment = $this->model->get('shipmenttempModel');



            $commission = 0;

            $commission_number = 1;

            $cont_unit = 1;




            if (!$shipment->getShipmentByWhere(array('marketing'=>trim($_POST['data'])))) {

                $commission = $marketing->commission;

                $commission_number = $marketing->commission_number;

                $cont_unit = $marketing->cont_unit;

            }



            $data = array(

                        'shipment_temp_date' => strtotime($_POST['date']),

                        'owner' => $_SESSION['userid_logined'],

                        'marketing' => trim($_POST['data']),

                        'shipment_temp_status' => 0,

                        'shipment_temp_ton' => trim(str_replace(',','',$_POST['ton'])),

                        'shipment_temp_number' => trim($_POST['number']),

                        'shipment_temp_commission' => $commission,

                        'shipment_temp_commission_number' => $commission_number,

                        'shipment_temp_cont_unit' => $cont_unit,

                        );

          

            $shipment->createShipment($data);





            date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$shipment->getLastShipment()->shipment_temp_id."|shipment_temp|".$_POST['data']."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



            return true;

                    

        }

    }



    

    

}

?>