<?php

Class shipmenttempController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipmenttemp) || json_decode($_SESSION['user_permission_action'])->shipmenttemp != "shipmenttemp") {
            $this->view->data['disable_control'] = 1;
        }

        

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Lô hàng đã nhận';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'shipment_temp_id';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

        }

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $customer_sub_model = $this->model->get('customersubModel');
        $customer_subs = $customer_sub_model->getAllCustomer();
        $customer_sub_data = array();
        foreach ($customer_subs as $customer_sub) {
            $customer_sub_data['name'][$customer_sub->customer_sub_id] = $customer_sub->customer_sub_name;
        }
        $this->view->data['customer_sub_data'] = $customer_sub_data;


        $join = array('table'=>'customer, marketing, cont_unit','where'=>'marketing.marketing_id = shipment_temp.marketing AND customer.customer_id = marketing.customer AND cont_unit=cont_unit_id');



        $shipment_temp_model = $this->model->get('shipmenttempModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;



        $data = array(

            'where' => 'shipment_temp_date >= '.strtotime($batdau).' AND shipment_temp_date < '.strtotime($ngayketthuc),

            );

        if ($_SESSION['role_logined'] > 2) {
            $data['where'] .= ' AND owner = '.$_SESSION['userid_logined'];
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



        $data = array(

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            'where' => 'shipment_temp_date >= '.strtotime($batdau).' AND shipment_temp_date < '.strtotime($ngayketthuc),

            );

        if ($_SESSION['role_logined'] > 2) {
            $data['where'] .= ' AND owner = '.$_SESSION['userid_logined'];
        }
        

        if ($keyword != '') {

            $search = '( 

                    OR customer_name LIKE "%'.$keyword.'%"

                    OR marketing_from in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                    OR marketing_to in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                 )';

            $data['where'] = $search;

        }

        

        $shipment_temp_data = $shipment_temp_model->getAllShipment($data, $join);

        

        $this->view->data['shipment_temps'] = $shipment_temp_data;



        $this->view->data['lastID'] = isset($shipment_temp_model->getLastShipment()->shipment_temp_id)?$shipment_temp_model->getLastShipment()->shipment_temp_id:0;



        $place_model = $this->model->get('placeModel');



        $place_data = array();



        foreach ($shipment_temp_data as $ship) {

            



            $places = $place_model->getAllPlace(array('where'=>'(place_id = '.$ship->marketing_from.' OR place_id = '.$ship->marketing_to.')'));

        



            foreach ($places as $place) {

                

                    $place_data['place_id'][$place->place_id] = $place->place_id;

                    $place_data['place_name'][$place->place_id] = $place->place_name;

                

                

            }

        }

        $this->view->data['place'] = $place_data;

        

        $this->view->show('shipmenttemp/index');

    }



    

    public function add(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipmenttemp) || json_decode($_SESSION['user_permission_action'])->shipmenttemp != "shipmenttemp") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $marketing = $this->model->get('marketingModel');

            $shipmenttemp = $this->model->get('shipmenttempModel');





            $data = array(

                        'shipment_temp_date' => strtotime(trim($_POST['shipment_temp_date'])),

                        'shipment_temp_number' => trim(str_replace(',','',$_POST['shipment_temp_number'])),

                        'shipment_temp_ton' => trim(str_replace(',','',$_POST['shipment_temp_ton'])),

                        );





            if ($_POST['yes'] != "") {

                //$data['supplies_update_user'] = $_SESSION['userid_logined'];

                //$data['supplies_update_time'] = time();

                //var_dump($data);



                $shipmenttemp_data = $shipmenttemp->getShipment($_POST['yes']);



                $marketing_data = $marketing->getMarketing($shipmenttemp_data->marketing);



                $data_marketing = array(

                    'marketing_ton_use' => $marketing_data->marketing_ton_use-$shipmenttemp_data->shipment_temp_ton+$data['shipment_temp_ton'],

                );

                $marketing->updateMarketing($data_marketing,array('marketing_id' => $shipmenttemp_data->marketing));

                

                    $shipmenttemp->updateShipment($data,array('shipment_temp_id' => $_POST['yes']));

                    echo "Cập nhật thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|shipment_temp|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    

            }

                    

        }

    }



    public function delete(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipmenttemp) || json_decode($_SESSION['user_permission_action'])->shipmenttemp != "shipmenttemp") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $shipment_temp = $this->model->get('shipmenttempModel');

            $marketing = $this->model->get('marketingModel');

            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {

                    $ship_data = $shipment_temp->getShipment($data);

                    $ma_data = $marketing->getMarketing($ship_data->marketing);

                    $data_marketing = array(

                        'status' => 0,

                        'marketing_ton_use' => $ma_data->marketing_ton_use-$ship_data->shipment_temp_ton,

                    );

                    $marketing->updateMarketing($data_marketing,array('marketing_id'=>$ship_data->marketing));



                    $shipment_temp->deleteShipment($data);



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|shipment_temp|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }

                return true;

            }

            else{



                $ship_data = $shipment_temp->getShipment($_POST['data']);

                $ma_data = $marketing->getMarketing($ship_data->marketing);

                    $data_marketing = array(

                        'status' => 0,

                        'marketing_ton_use' => $ma_data->marketing_ton_use-$ship_data->shipment_temp_ton,

                    );

                    $marketing->updateMarketing($data_marketing,array('marketing_id'=>$ship_data->marketing));



                date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|shipment_temp|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $shipment_temp->deleteShipment($_POST['data']);

            }

            

        }

    }



    

    

}

?>