<?php

Class marketingController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->marketing) || json_decode($_SESSION['user_permission_action'])->marketing != "marketing") {
            $this->view->data['disable_control'] = 1;
        }

        

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Kinh doanh';



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

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'marketing_date';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

        }

        $id = $this->registry->router->param_id;

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $contunit_model = $this->model->get('contunitModel');

        $this->view->data['cont_units'] = $contunit_model->getAllUnit();

        $join = array('table'=>'customer, cont_unit','where'=>'customer.customer_id = marketing.customer AND cont_unit=cont_unit_id');



        $marketing_model = $this->model->get('marketingModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;



        $data = array(

            'where' => 'marketing_date >= '.strtotime($batdau).' AND marketing_date < '.strtotime($ngayketthuc),

            );

        if ($id>0) {
            $data['where'] = 'marketing_id = '.$id;
        }
        

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



        $this->view->data['batdau'] = $batdau;

        $this->view->data['ketthuc'] = $ketthuc;



        $data = array(

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            'where' => 'marketing_date >= '.strtotime($batdau).' AND marketing_date < '.strtotime($ngayketthuc),

            );

        if ($id>0) {
            $data['where'] = 'marketing_id = '.$id;
        }
        

        if ($keyword != '') {

            $search = ' AND ( 

                    customer_name LIKE "%'.$keyword.'%"

                    OR marketing_from in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                    OR marketing_to in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                 )';

            $data['where'] .= $search;

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

        $customer_types = array();
        foreach ($marketing_data as $marketing) {
            $customer_sub = "";
            $sts = explode(',', $marketing->customer_type);
            foreach ($sts as $key) {
                $subs = $customer_sub_model->getCustomer($key);
                if ($subs) {
                    if ($customer_sub == "")
                        $customer_sub .= $subs->customer_sub_name;
                    else
                        $customer_sub .= ','.$subs->customer_sub_name;
                }
                
            }
            $customer_types[$marketing->marketing_id] = $customer_sub;
        }
        
        $this->view->data['customer_types'] = $customer_types;

        $this->view->show('marketing/index');

    }



    public function view($id) {

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }



        if (!$id) {

            return $this->view->redirect('marketing');

        }

        

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Lô hàng đã nhận';



        $join = array('table'=>'user,cont_unit','where'=>'user.user_id = shipment_temp.owner AND shipment_temp_cont_unit=cont_unit_id');



        $shipment_temp_model = $this->model->get('shipmenttempModel');



        $data = array(

            'where' => 'marketing = '.$id,

            'order_by' => 'shipment_temp_date ASC',

            );

    

        

        $shipment_temp_data = $shipment_temp_model->getAllShipment($data, $join);

        

        $this->view->data['shipment_temps'] = $shipment_temp_data;

        

        $this->view->show('marketing/view');

    }



    public function getshipmentfrom(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $place_model = $this->model->get('placeModel');

            

            if ($_POST['keyword'] == "*") {

                $list = $place_model->getAllPlace();

            }

            else{

                $data = array(

                'where'=>'( place_name LIKE "'.$_POST['keyword'].'%" )',

                );

                $list = $place_model->getAllPlace($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text

                $place_name = $rs->place_name;

                if ($_POST['keyword'] != "*") {

                    $place_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->place_name);

                }

                

                // add new option

                echo '<li onclick="set_item_shipment_from(\''.$rs->place_id.'\',\''.$rs->place_name.'\')">'.$place_name.'</li>';

            }

        }

    }

    public function getshipmentto(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $place_model = $this->model->get('placeModel');

            

            if ($_POST['keyword'] == "*") {

                $list = $place_model->getAllPlace();

            }

            else{

                $data = array(

                'where'=>'( place_name LIKE "'.$_POST['keyword'].'%" )',

                );

                $list = $place_model->getAllPlace($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text

                $place_name = $rs->place_name;

                if ($_POST['keyword'] != "*") {

                    $place_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->place_name);

                }

                

                // add new option

                echo '<li onclick="set_item_shipment_to(\''.$rs->place_id.'\',\''.$rs->place_name.'\')">'.$place_name.'</li>';

            }

        }

    }

    public function getcustomer(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $customer_model = $this->model->get('customerModel');

            

            if ($_POST['keyword'] == "*") {



                $list = $customer_model->getAllCustomer();

            }

            else{

                $data = array(

                'where'=>'( customer_name LIKE "%'.$_POST['keyword'].'%" )',

                );

                $list = $customer_model->getAllCustomer($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text

                $customer_name = $rs->customer_name;

                if ($_POST['keyword'] != "*") {

                    $customer_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->customer_name);

                }

                

                // add new option

                echo '<li onclick="set_item_customer(\''.$rs->customer_id.'\',\''.$rs->customer_name.'\')">'.$customer_name.'</li>';

            }

        }

    }


    public function getSub(){
        header('Content-type: application/json');
        $q = $_GET["search"];

        $sub_model = $this->model->get('customersubModel');
        $data = array(
            'where' => 'customer_sub_name LIKE "%'.$q.'%"',
        );
        $subs = $sub_model->getAllCustomer($data);
        $arr = array();
        foreach ($subs as $sub) {
            $arr[] = $sub->customer_sub_name;
        }
        
        echo json_encode($arr);
    }


    public function contract() {

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }



        $customer_model = $this->model->get('customerModel');

        $bank_model = $this->model->get('bankModel');



        $customers = $customer_model->getCustomer($this->registry->router->param_id);



        $tk = "";

        $banks = $bank_model->getBank($customers->company_bank);

        if ($banks) {

            $tk = $banks->bank_name;

        }



        $info = $this->registry->router->addition;

        

        $arr = explode('@', $info);



        $this->view->data['company'] = strtoupper($customers->company_name);

        $this->view->data['mst'] = $customers->mst;

        $this->view->data['address'] = $customers->company_address;

        $this->view->data['phone'] = $customers->company_phone;

        $this->view->data['fax'] = $customers->company_fax;

        $this->view->data['bank_number'] = $customers->company_bank_number;

        $this->view->data['bank'] = $tk;

        $this->view->data['branch'] = $customers->company_bank_branch;

        $this->view->data['name'] = $customers->company_present;

        $this->view->data['position'] = $customers->company_position;



        $this->view->data['from'] = str_replace('$', ' ', $arr[0]);

        $this->view->data['to'] = str_replace('$', ' ', $arr[1]);

        $this->view->data['contract_date'] = explode('-', $arr[2]);

        $this->view->data['contract_number'] = $arr[3];

        $this->view->data['contract_pay'] = str_replace('$', ' ', $arr[4]);

        $this->view->data['contract_valid'] = str_replace('-', '/', $arr[5]);

                

        $this->view->show('marketing/contract');

    }



    public function add(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->marketing) || json_decode($_SESSION['user_permission_action'])->marketing != "marketing") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $marketing = $this->model->get('marketingModel');
            $shipment = $this->model->get('shipmentModel');
            $shipment_temp = $this->model->get('shipmenttempModel');


            $data = array(

                        'marketing_date' => strtotime(trim($_POST['marketing_date'])),

                        'marketing_from' => trim($_POST['marketing_from']),

                        'marketing_to' => trim($_POST['marketing_to']),

                        'customer' => trim($_POST['customer']),

                        'marketing_ton' => trim($_POST['marketing_ton']),

                        'marketing_charge' => trim(str_replace(',','',$_POST['marketing_charge'])),

                        'commission' => trim(str_replace(',','',$_POST['commission'])),

                        'commission_number' => trim($_POST['commission_number']),

                        'marketing_start' => strtotime(trim($_POST['marketing_start'])),

                        'marketing_end' => strtotime(trim($_POST['marketing_end'])),

                        'cont_unit' => trim($_POST['cont_unit']),

                        );

            $customer_sub_model = $this->model->get('customersubModel');
            $customer_model = $this->model->get('customerModel');

            $contributor = "";
            if(trim($_POST['customer_type']) != ""){
                $support = explode(',', trim($_POST['customer_type']));

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
                            
                            $cus = $customer_model->getCustomer($data['customer']);

                            $customer_sub_model->createCustomer(array('customer_sub_name'=>trim($key)));

                            $con = $customer_sub_model->getLastCustomer()->customer_sub_id;

                            if ($contributor == "")
                                $contributor .= $con;
                            else
                                $contributor .= ','.$con;

                            if ($cus->customer_sub == "") {
                                $customer_model->updateCustomer(array('customer_sub'=>$con),array('customer_id'=>$data['customer']));
                            }
                            else{
                                $customer_model->updateCustomer(array('customer_sub'=>($cus->customer_sub.','.$con)),array('customer_id'=>$data['customer']));
                            }
                        }
                        
                    }
                }

            }
            $data['customer_type'] = $contributor;



            if ($_POST['yes'] != "") {

                //$data['supplies_update_user'] = $_SESSION['userid_logined'];

                //$data['supplies_update_time'] = time();

                //var_dump($data);

                

                    $marketing->updateMarketing($data,array('marketing_id' => $_POST['yes']));

                    $join_ship = array('table'=>'shipment_temp, marketing','where'=>'shipment_temp = shipment_temp_id AND marketing = marketing_id');
                    $shipments = $shipment->getAllShipment(array('where'=>'marketing = '.$_POST['yes']),$join_ship);
                    foreach ($shipments as $ship) {
                        $data_ship = array(
                            'shipment_charge' => $data['marketing_charge'],
                            'shipment_revenue' => $ship->shipment_ton*$data['marketing_charge'],
                            'commission' => $data['commission'],
                            'commission_number' => $data['commission_number'],
                        );
                        $shipment->updateShipment($data_ship,array('shipment_id'=>$ship->shipment_id));
                    }

                    $join_ship = array('table'=>'marketing','where'=>'marketing = marketing_id');
                    $shipments = $shipment_temp->getAllShipment(array('where'=>'marketing = '.$_POST['yes']),$join_ship);
                    foreach ($shipments as $ship) {
                        $data_ship = array(
                            'shipment_temp_commission' => $data['commission'],
                            'shipment_temp_commission_number' => $data['commission_number'],
                        );
                        $shipment_temp->updateShipment($data_ship,array('shipment_temp_id'=>$ship->shipment_temp_id));
                    }

                    echo "Cập nhật thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|marketing|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    

            }

            else{

                $data['marketing_create_user'] = $_SESSION['userid_logined'];

                //$data['staff'] = $_POST['staff'];

                //var_dump($data);

                

                    $marketing->createMarketing($data);

                    echo "Thêm thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$marketing->getLastMarketing()->marketing_id."|marketing|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                

                

            }

                    

        }

    }



    public function approve(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->marketing) || json_decode($_SESSION['user_permission_action'])->marketing != "marketing") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['data'])) {



            $supplies = $this->model->get('suppliesModel');



            $data = array(

                        

                        'approve' => 1,

                        'user_approve' => $_SESSION['userid_logined'],

                        );

          

            $supplies->updateSupplies($data,array('supplies_id' => $_POST['data']));



            date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."approve"."|".$_POST['data']."|supplies|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



            return true;

                    

        }

    }

    



    public function delete(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->marketing) || json_decode($_SESSION['user_permission_action'])->marketing != "marketing") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $marketing = $this->model->get('marketingModel');

            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {

                    $marketing->deleteMarketing($data);



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|marketing|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }

                return true;

            }

            else{



                date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|marketing|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $marketing->deleteMarketing($_POST['data']);

            }

            

        }

    }



    

    

}

?>