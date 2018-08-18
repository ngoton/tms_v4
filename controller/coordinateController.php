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


    public function addcoordinate(){
        $coordinate_model = $this->model->get('coordinateModel');

        if (isset($_POST['coordinate_customer']) ) {
            

            $data = array(
                'coordinate_date' => strtotime(str_replace('/', '-', $_POST['coordinate_date'])),
                'coordinate_code'=>trim($_POST['coordinate_code']),
                'coordinate_customer'=>trim($_POST['coordinate_customer']),
                'coordinate_number'=>trim($_POST['coordinate_number']),
                'coordinate_type'=>trim($_POST['coordinate_type']),
                'coordinate_shipping'=>trim($_POST['coordinate_shipping']),
                'coordinate_shipping_name'=>trim($_POST['coordinate_shipping_name']),
                'coordinate_shipping_number'=>trim($_POST['coordinate_shipping_number']),
                'coordinate_place_from'=>trim($_POST['coordinate_place_from']),
                'coordinate_place_to'=>trim($_POST['coordinate_place_to']),
                'coordinate_start_date' => strtotime(str_replace('/', '-', $_POST['coordinate_start_date'])),
                'coordinate_end_date' => strtotime(str_replace('/', '-', $_POST['coordinate_end_date'])),
                'coordinate_sum' => str_replace(',', '', $_POST['coordinate_sum']),
                'coordinate_total' => str_replace(',', '', $_POST['coordinate_total']),
                'coordinate_comment'=>trim($_POST['coordinate_comment']),
                'coordinate_create_user'=>$_SESSION['userid_logined'],
            );

            $coordinate_model->createcoordinate($data);
            $id_coordinate = $coordinate_model->getLastcoordinate()->coordinate_id;

            $customer_sub_model = $this->model->get('customersubModel');

            $coordinate_detail_model = $this->model->get('coordinatedetailModel');

            $coordinate_detail_data = json_decode($_POST['coordinate_detail_data']);

            if (isset($id_coordinate)) {
                foreach ($coordinate_detail_data as $v) {
                    $data_coordinate_detail = array(
                        'coordinate' => $id_coordinate,
                        'coordinate_detail_container' => trim($v->coordinate_detail_container),
                        'coordinate_detail_seal' => trim($v->coordinate_detail_seal),
                        'coordinate_detail_number' => str_replace(',', '', $v->coordinate_detail_number),
                        'coordinate_detail_unit' => trim($v->coordinate_detail_unit),
                        'coordinate_detail_price' => str_replace(',', '', $v->coordinate_detail_price),
                    );

                    $contributor = "";
                    if(trim($v->coordinate_detail_customer_sub) != ""){
                        $support = explode(',', trim($v->coordinate_detail_customer_sub));

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
                    $data_coordinate_detail['coordinate_detail_customer_sub'] = $contributor;

                    if ($v->id_coordinate_detail>0) {
                        $coordinate_detail_model->updatecoordinate($data_coordinate_detail,array('coordinate_detail_id'=>$v->id_coordinate_detail));
                    }
                    else{
                        if ($data_coordinate_detail['coordinate_detail_number']!="") {
                            $coordinate_detail_model->createcoordinate($data_coordinate_detail);
                        }
                        
                    }
                }

            }

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$id_coordinate."|coordinate|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'coordinate',
                'user_log_table_name' => 'lệnh điều xe',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->coordinate) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới lệnh điều xe';

        $coordinate_model = $this->model->get('coordinateModel');
        $lastID = isset($coordinate_model->getLastcoordinate()->coordinate_code)?$coordinate_model->getLastcoordinate()->coordinate_code:'DH00';
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
                'coordinate_customer'=>trim($_POST['coordinate_customer']),
                'coordinate_number'=>trim($_POST['coordinate_number']),
                'coordinate_type'=>trim($_POST['coordinate_type']),
                'coordinate_shipping'=>trim($_POST['coordinate_shipping']),
                'coordinate_shipping_name'=>trim($_POST['coordinate_shipping_name']),
                'coordinate_shipping_number'=>trim($_POST['coordinate_shipping_number']),
                'coordinate_place_from'=>trim($_POST['coordinate_place_from']),
                'coordinate_place_to'=>trim($_POST['coordinate_place_to']),
                'coordinate_start_date' => strtotime(str_replace('/', '-', $_POST['coordinate_start_date'])),
                'coordinate_end_date' => strtotime(str_replace('/', '-', $_POST['coordinate_end_date'])),
                'coordinate_sum' => str_replace(',', '', $_POST['coordinate_sum']),
                'coordinate_total' => str_replace(',', '', $_POST['coordinate_total']),
                'coordinate_comment'=>trim($_POST['coordinate_comment']),
                'coordinate_update_user'=>$_SESSION['userid_logined'],
            );

            $coordinate_model->updatecoordinate($data,array('coordinate_id'=>$id));
            
            $id_coordinate = $id;

            $customer_sub_model = $this->model->get('customersubModel');

            $coordinate_detail_model = $this->model->get('coordinatedetailModel');

            $coordinate_detail_data = json_decode($_POST['coordinate_detail_data']);

            if (isset($id_coordinate)) {
                foreach ($coordinate_detail_data as $v) {
                    $data_coordinate_detail = array(
                        'coordinate' => $id_coordinate,
                        'coordinate_detail_container' => trim($v->coordinate_detail_container),
                        'coordinate_detail_seal' => trim($v->coordinate_detail_seal),
                        'coordinate_detail_number' => str_replace(',', '', $v->coordinate_detail_number),
                        'coordinate_detail_unit' => trim($v->coordinate_detail_unit),
                        'coordinate_detail_price' => str_replace(',', '', $v->coordinate_detail_price),
                    );

                    $contributor = "";
                    if(trim($v->coordinate_detail_customer_sub) != ""){
                        $support = explode(',', trim($v->coordinate_detail_customer_sub));

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
                    $data_coordinate_detail['coordinate_detail_customer_sub'] = $contributor;

                    if ($v->id_coordinate_detail>0) {
                        $coordinate_detail_model->updatecoordinate($data_coordinate_detail,array('coordinate_detail_id'=>$v->id_coordinate_detail));
                    }
                    else{
                        if ($data_coordinate_detail['coordinate_detail_number']!="") {
                            $coordinate_detail_model->createcoordinate($data_coordinate_detail);
                        }
                        
                    }
                }

            }

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|coordinate|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'coordinate',
                'user_log_table_name' => 'lệnh điều xe',
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

        $coordinate_data = $coordinate_model->getcoordinate($id);

        $this->view->data['coordinate_data'] = $coordinate_data;

        if (!$coordinate_data) {

            $this->view->redirect('coordinate');

        }

        $coordinate_detail_model = $this->model->get('coordinatedetailModel');

        $coordinate_details = $coordinate_detail_model->getAllcoordinate(array('where'=>'coordinate='.$id));
        $this->view->data['coordinate_details'] = $coordinate_details;

        $customer_sub_model = $this->model->get('customersubModel');
        $customer_sub = array();
        foreach ($coordinate_details as $coordinate_detail) {
            $sts = explode(',', $coordinate_detail->coordinate_detail_customer_sub);
            foreach ($sts as $key) {
                $subs = $customer_sub_model->getCustomer($key);
                if($subs){
                    if (!isset($customer_sub[$coordinate_detail->coordinate_detail_id]))
                        $customer_sub[$coordinate_detail->coordinate_detail_id] = $subs->customer_sub_name;
                    else
                        $customer_sub[$coordinate_detail->coordinate_detail_id] .= ','.$subs->customer_sub_name;
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

        $coordinate_data = $coordinate_model->getcoordinate($id);

        $this->view->data['coordinate_data'] = $coordinate_data;

        if (!$coordinate_data) {

            $this->view->redirect('coordinate');

        }

        $coordinate_detail_model = $this->model->get('coordinatedetailModel');

        $coordinate_details = $coordinate_detail_model->getAllcoordinate(array('where'=>'coordinate='.$id));
        $this->view->data['coordinate_details'] = $coordinate_details;

        $customer_sub_model = $this->model->get('customersubModel');
        $customer_sub = array();
        foreach ($coordinate_details as $coordinate_detail) {
            $sts = explode(',', $coordinate_detail->coordinate_detail_customer_sub);
            foreach ($sts as $key) {
                $subs = $customer_sub_model->getCustomer($key);
                if($subs){
                    if (!isset($customer_sub[$coordinate_detail->coordinate_detail_id]))
                        $customer_sub[$coordinate_detail->coordinate_detail_id] = $subs->customer_sub_name;
                    else
                        $customer_sub[$coordinate_detail->coordinate_detail_id] .= ','.$subs->customer_sub_name;
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

        $units = $unit_model->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));

        $this->view->data['units'] = $units;


        return $this->view->show('coordinate/view');

    }

    public function getcoordinate(){

        $customer = $_GET['customer'];

        $type = $_GET['type'];

        $coordinate_model = $this->model->get('coordinateModel');

        $data = array(
            'where'=>'(coordinate_sum_run IS NULL OR coordinate_sum_run=0 OR coordinate_sum_run<coordinate_sum)',
            'order_by'=>'coordinate_date',
            'order'=>'ASC',
        );
        
        if ($customer>0) {
            $data['where'] .= ' AND coordinate_customer='.$customer;
        }
        if ($type>0) {
            $data['where'] .= ' AND coordinate_type='.$type;
        }

        $coordinates = $coordinate_model->getAllcoordinate($data);

        $str = "";
        foreach ($coordinates as $coordinate) {
            $str .= '<option value="'.$coordinate->coordinate_id.'">'.$coordinate->coordinate_number.'-['.$coordinate->coordinate_code.']</option>';
        }

        $coordinate_data = array(
            'coordinate'=>$str,
        );

        echo json_encode($coordinate_data);

    }
    public function getcoordinatedetail(){

        $coordinate = $_GET['coordinate'];

        $coordinate_model = $this->model->get('coordinateModel');
        $customer_model = $this->model->get('customerModel');
        $coordinate_detail_model = $this->model->get('coordinatedetailModel');

        $books = $coordinate_model->getcoordinate($coordinate);
        $customers = $customer_model->getCustomer($books->coordinate_customer);

        $data = array(
            'where'=>'coordinate = '.$coordinate,
        );

        $coordinates = $coordinate_detail_model->getAllcoordinate($data);

        $str = "";
        foreach ($coordinates as $coordinate) {
            $str .= '<option value="'.$coordinate->coordinate_detail_id.'">'.$coordinate->coordinate_detail_container.'</option>';
        }

        $coordinate_data = array(
            'container'=>$str,
            'customer'=>$customers->customer_id,
            'type'=>$books->coordinate_type,
            'from'=>$books->coordinate_place_from,
            'to'=>$books->coordinate_place_to,
            'start'=>$this->lib->hien_thi_ngay_thang($books->coordinate_start_date),
            'end'=>$this->lib->hien_thi_ngay_thang($books->coordinate_end_date),
        );

        echo json_encode($coordinate_data);

    }
    public function getcoordinatecont(){

        if (!isset($_GET['detail'])) {
            $coordinate_data = array(
                'number'=>null,
                'unit'=>null,
                'price'=>null
            );

            echo json_encode($coordinate_data);
            return;
        }
        
        $detail = $_GET['detail'];

        $coordinate_detail_model = $this->model->get('coordinatedetailModel');
        $unit_model = $this->model->get('unitModel');

        $coordinates = $coordinate_detail_model->getcoordinate($detail);
        $units = $unit_model->getUnit($coordinates->coordinate_detail_unit);

        $coordinate_data = array(
            'number'=>$coordinates->coordinate_detail_number,
            'unit'=>$units->unit_id,
            'price'=>$coordinates->coordinate_detail_price,
        );

        echo json_encode($coordinate_data);

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

        return $this->view->show('coordinate/filter');
    }

    public function deletecoordinatedetail(){
        if (isset($_POST['data'])) {
            $coordinate_detail_model = $this->model->get('coordinatedetailModel');
            $user_log_model = $this->model->get('userlogModel');

            $coordinate_detail_model->querycoordinate('DELETE FROM coordinate_detail WHERE coordinate_detail_id='.$_POST['data'].' AND coordinate='.$_POST['coordinate']);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|coordinate_detail|"."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);

            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'coordinate_detail',
                'user_log_table_name' => 'Chi tiết lệnh điều xe',
                'user_log_action' => 'Xóa',
                'user_log_data' => json_encode($_POST['data']),
            );
            $user_log_model->createUser($data_log);
        }
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
            $coordinate_detail_model = $this->model->get('coordinatedetailModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {
                    
                    $coordinate_model->deletecoordinate($data);

                    $coordinate_detail_model->querycoordinate('DELETE FROM coordinate_detail WHERE coordinate='.$data);

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|coordinate|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'coordinate',
                    'user_log_table_name' => 'lệnh điều xe',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $coordinate_model->deletecoordinate($_POST['data']);

                $coordinate_detail_model->querycoordinate('DELETE FROM coordinate_detail WHERE coordinate='.$_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|coordinate|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'coordinate',
                    'user_log_table_name' => 'lệnh điều xe',
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