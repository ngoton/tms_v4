<?php

Class roadController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý định mức tuyến đường';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'road_place_from,road_start_date';

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

        $route_model = $this->model->get('routeModel');

        $routes = $route_model->getAllRoute();
        $route_data = array();

        foreach ($routes as $route) {
            $route_data[$route->route_id] = $route->route_name;
        }

        $this->view->data['route_data'] = $route_data;


        $road_model = $this->model->get('roadModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        if (isset($_POST['filter'])) {
            if (isset($_POST['road_place_from'])) {
                $data['where'] .= ' AND road_place_from IN ('.implode(',',$_POST['road_place_from']).')';
            }
            if (isset($_POST['road_place_to'])) {
                $data['where'] .= ' AND road_place_to IN ('.implode(',',$_POST['road_place_to']).')';
            }
            if (isset($_POST['road_route_from'])) {
                $data['where'] .= ' AND road_route_from IN ('.implode(',',$_POST['road_route_from']).')';
            }
            if (isset($_POST['road_route_to'])) {
                $data['where'] .= ' AND road_route_to IN ('.implode(',',$_POST['road_route_to']).')';
            }

            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($road_model->getAllRoad($data));

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
            if (isset($_POST['road_place_from'])) {
                $data['where'] .= ' AND road_place_from IN ('.implode(',',$_POST['road_place_from']).')';
            }
            if (isset($_POST['road_place_to'])) {
                $data['where'] .= ' AND road_place_to IN ('.implode(',',$_POST['road_place_to']).')';
            }
            if (isset($_POST['road_route_from'])) {
                $data['where'] .= ' AND road_route_from IN ('.implode(',',$_POST['road_route_from']).')';
            }
            if (isset($_POST['road_route_to'])) {
                $data['where'] .= ' AND road_route_to IN ('.implode(',',$_POST['road_route_to']).')';
            }
        }
        

        if ($keyword != '') {

            $search = '( road_place_from IN (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%") 
                        OR road_place_to IN (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%") 
                        OR road_route_from IN (SELECT route_id FROM route WHERE route_name LIKE "%'.$keyword.'%") 
                        OR road_route_to IN (SELECT route_id FROM route WHERE route_name LIKE "%'.$keyword.'%") 
                    )';

            $data['where'] = $search;

        }

        $roads = $road_model->getAllRoad($data);

        $this->view->data['roads'] = $roads;

        $arr = array();
        foreach ($roads as $road) {
            $arr[$place_data[$road->road_place_from]][$place_data[$road->road_place_to]][] = $road; 
        }
        $this->view->data['arr'] = $arr;


        return $this->view->show('road/index');

    }


    public function addroad(){
        $road_model = $this->model->get('roadModel');

        if (isset($_POST['road_place_from']) && isset($_POST['road_place_to']) && isset($_POST['road_route_from']) && isset($_POST['road_route_to'])) {
            if($road_model->getRoadByWhere(array('road_place_from'=>$_POST['road_place_from'],'road_place_to'=>$_POST['road_place_to'],'road_route_from'=>$_POST['road_route_from'],'road_route_to'=>$_POST['road_route_to'],'road_start_date'=>strtotime(str_replace('/', '-', $_POST['road_start_date']))))){
                echo 'Thông tin đã tồn tại';
                return false;
            }

            $data = array(
                'road_place_from'=>trim($_POST['road_place_from']),
                'road_place_to'=>trim($_POST['road_place_to']),
                'road_route_from'=>trim($_POST['road_route_from']),
                'road_route_to'=>trim($_POST['road_route_to']),
                'road_start_date' => strtotime(str_replace('/', '-', $_POST['road_start_date'])),
                'road_end_date' => $_POST['road_end_date']!=""?strtotime(str_replace('/', '-', $_POST['road_end_date'])):null,
                'road_time' => str_replace(',', '', $_POST['road_time']),
                'road_km' => str_replace(',', '', $_POST['road_km']),
                'road_oil' => str_replace(',', '', $_POST['road_oil']),
                'road_oil_ton' => str_replace(',', '', $_POST['road_oil_ton']),
                'road_bridge' => str_replace(',', '', $_POST['road_bridge']),
                'road_police' => str_replace(',', '', $_POST['road_police']),
                'road_tire' => str_replace(',', '', $_POST['road_tire']),
                'road_over' => str_replace(',', '', $_POST['road_over']),
                'road_add' => str_replace(',', '', $_POST['road_add']),
                'road_salary' => str_replace(',', '', $_POST['road_salary']),
            );

            $ngaytruoc = strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['road_start_date']).' -1 day')));

            if ($data['road_end_date'] == null) {
                $road_model->queryRoad('UPDATE road SET road_end_date = '.$ngaytruoc.' WHERE road_place_from='.$data['road_place_from'].' AND road_place_to='.$data['road_place_to'].' AND road_route_from='.$data['road_route_from'].' AND road_route_to='.$data['road_route_to'].' AND (road_end_date IS NULL OR road_end_date = 0)');
                $road_model->createRoad($data);
            }
            else{
                $dm1 = $road_model->queryRoad('SELECT * FROM road WHERE road_place_from='.$data['road_place_from'].' AND road_place_to='.$data['road_place_to'].' AND road_route_from='.$data['road_route_from'].' AND road_route_to='.$data['road_route_to'].' AND road_start_date <= '.$data['road_start_date'].' AND road_end_date <= '.$data['road_end_date'].' AND road_end_date >= '.$data['road_start_date'].' ORDER BY road_end_date ASC LIMIT 1');
                $dm2 = $road_model->queryRoad('SELECT * FROM road WHERE road_place_from='.$data['road_place_from'].' AND road_place_to='.$data['road_place_to'].' AND road_route_from='.$data['road_route_from'].' AND road_route_to='.$data['road_route_to'].' AND road_end_date >= '.$data['road_end_date'].' AND road_start_date >= '.$data['road_start_date'].' AND road_start_date <= '.$data['road_end_date'].' ORDER BY road_end_date ASC LIMIT 1');
                $dm3 = $road_model->queryRoad('SELECT * FROM road WHERE road_place_from='.$data['road_place_from'].' AND road_place_to='.$data['road_place_to'].' AND road_route_from='.$data['road_route_from'].' AND road_route_to='.$data['road_route_to'].' AND road_start_date <= '.$data['road_start_date'].' AND road_end_date >= '.$data['road_end_date'].' ORDER BY road_end_date ASC LIMIT 1');

                if ($dm3) {
                    foreach ($dm3 as $row) {
                        $d = array(
                            'road_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['road_start_date']).' -1 day'))),
                            );
                        $road_model->updateRoad($d,array('road_id'=>$row->road_id));

                        $c = array(
                            'road_place_from' => $row->road_place_from,
                            'road_place_to' => $row->road_place_to,
                            'road_route_from' => $row->road_route_from,
                            'road_route_to' => $row->road_route_to,
                            'road_time' => $row->road_time,
                            'road_km' => $row->road_km,
                            'road_oil' => $row->road_oil,
                            'road_oil_ton' => $row->road_oil_ton,
                            'road_bridge' => $row->road_bridge,
                            'road_police' => $row->road_police,
                            'road_tire' => $row->road_tire,
                            'road_over' => $row->road_over,
                            'road_add' => $row->road_add,
                            'road_salary' => $row->road_salary,
                            'road_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['road_end_date']).' +1 day'))),
                            'road_end_date' => $row->road_end_date,
                            );
                        $road_model->createRoad($c);

                    }
                    $road_model->createRoad($data);

                }
                else if ($dm1 || $dm2) {
                    if($dm1){
                        foreach ($dm1 as $row) {
                            $d = array(
                                'road_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['road_start_date']).' -1 day'))),
                                );
                            $road_model->updateRoad($d,array('road_id'=>$row->road_id));
                        }
                    }
                    if($dm2){
                        foreach ($dm2 as $row) {
                            $d = array(
                                'road_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['road_end_date']).' +1 day'))),
                                );
                            $road_model->updateRoad($d,array('road_id'=>$row->road_id));
                        }
                    }
                    $road_model->createRoad($data);
                }
                else{
                    $road_model->createRoad($data);
                }
            }
            $id_road = $road_model->getLastRoad()->road_id;

            $road_oil_model = $this->model->get('roadoilModel');
            $road_toll_model = $this->model->get('roadtollModel');

            $road_oil_data = json_decode($_POST['road_oil_data']);
            $road_toll_data = json_decode($_POST['road_toll_data']);

            if (isset($id_road)) {
                foreach ($road_oil_data as $v) {
                    $data_road_oil = array(
                        'road' => $id_road,
                        'road_oil_way' => trim($v->road_oil_way),
                        'road_oil_km' => str_replace(',', '', $v->road_oil_km),
                        'road_oil_lit' => str_replace(',', '', $v->road_oil_lit),
                    );

                    if ($v->id_road_oil>0) {
                        $road_oil_model->updateRoad($data_road_oil,array('road_oil_id'=>$v->id_road_oil));
                    }
                    else{
                        if ($data_road_oil['road_oil_km']!="") {
                            $road_oil_model->createRoad($data_road_oil);
                        }
                        
                    }
                }

                foreach ($road_toll_data as $v2) {
                    $data_road_toll = array(
                        'road' => $id_road,
                        'toll' => trim($v2->toll),
                        'road_toll_money' => str_replace(',', '', $v2->road_toll_money),
                        'road_toll_vat' => trim($v2->road_toll_vat),
                    );

                    if ($v2->id_road_toll>0) {
                        $road_toll_model->updateRoad($data_road_toll,array('road_toll_id'=>$v2->id_road_toll));
                    }
                    else{
                        if ($data_road_toll['road_toll_money']!="") {
                            $road_toll_model->createRoad($data_road_toll);
                        }
                        
                    }
                }
            }

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$id_road."|road|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'road',
                'user_log_table_name' => 'Định mức tuyến đường',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->road) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới định mức tuyến đường';

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace();

        $this->view->data['places'] = $places;

        $route_model = $this->model->get('routeModel');

        $routes = $route_model->getAllRoute();

        $this->view->data['routes'] = $routes;

        $oil_model = $this->model->get('oilModel');

        $oils = $oil_model->getAllOil();

        $this->view->data['oils'] = $oils;

        $toll_model = $this->model->get('tollModel');

        $tolls = $toll_model->getAllToll(array('order_by'=>'toll_code','order'=>'ASC'));

        $this->view->data['tolls'] = $tolls;

        return $this->view->show('road/add');
    }

    public function editroad(){
        $road_model = $this->model->get('roadModel');

        if (isset($_POST['road_id'])) {
            $id = $_POST['road_id'];
            
            $data = array(
                'road_place_from'=>trim($_POST['road_place_from']),
                'road_place_to'=>trim($_POST['road_place_to']),
                'road_route_from'=>trim($_POST['road_route_from']),
                'road_route_to'=>trim($_POST['road_route_to']),
                'road_start_date' => strtotime(str_replace('/', '-', $_POST['road_start_date'])),
                'road_end_date' => $_POST['road_end_date']!=""?strtotime(str_replace('/', '-', $_POST['road_end_date'])):null,
                'road_time' => str_replace(',', '', $_POST['road_time']),
                'road_km' => str_replace(',', '', $_POST['road_km']),
                'road_oil' => str_replace(',', '', $_POST['road_oil']),
                'road_oil_ton' => str_replace(',', '', $_POST['road_oil_ton']),
                'road_bridge' => str_replace(',', '', $_POST['road_bridge']),
                'road_police' => str_replace(',', '', $_POST['road_police']),
                'road_tire' => str_replace(',', '', $_POST['road_tire']),
                'road_over' => str_replace(',', '', $_POST['road_over']),
                'road_add' => str_replace(',', '', $_POST['road_add']),
                'road_salary' => str_replace(',', '', $_POST['road_salary']),
            );

            $road_model->updateRoad($data,array('road_id'=>$id));
            
            $id_road = $id;

            $road_oil_model = $this->model->get('roadoilModel');
            $road_toll_model = $this->model->get('roadtollModel');

            $road_oil_data = json_decode($_POST['road_oil_data']);
            $road_toll_data = json_decode($_POST['road_toll_data']);

            if (isset($id_road)) {
                foreach ($road_oil_data as $v) {
                    $data_road_oil = array(
                        'road' => $id_road,
                        'road_oil_way' => trim($v->road_oil_way),
                        'road_oil_km' => str_replace(',', '', $v->road_oil_km),
                        'road_oil_lit' => str_replace(',', '', $v->road_oil_lit),
                    );

                    if ($v->id_road_oil>0) {
                        $road_oil_model->updateRoad($data_road_oil,array('road_oil_id'=>$v->id_road_oil));
                    }
                    else{
                        if ($data_road_oil['road_oil_km']!="") {
                            $road_oil_model->createRoad($data_road_oil);
                        }
                        
                    }
                }

                foreach ($road_toll_data as $v2) {
                    $data_road_toll = array(
                        'road' => $id_road,
                        'toll' => trim($v2->toll),
                        'road_toll_money' => str_replace(',', '', $v2->road_toll_money),
                        'road_toll_vat' => trim($v2->road_toll_vat),
                    );

                    if ($v2->id_road_toll>0) {
                        $road_toll_model->updateRoad($data_road_toll,array('road_toll_id'=>$v2->id_road_toll));
                    }
                    else{
                        if ($data_road_toll['road_toll_money']!="") {
                            $road_toll_model->createRoad($data_road_toll);
                        }
                        
                    }
                }
            }

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|road|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'road',
                'user_log_table_name' => 'Định mức tuyến đường',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->road) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('road');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật định mức tuyến đường';

        $road_model = $this->model->get('roadModel');

        $road_data = $road_model->getRoad($id);

        $this->view->data['road_data'] = $road_data;

        if (!$road_data) {

            $this->view->redirect('road');

        }

        $road_oil_model = $this->model->get('roadoilModel');
        $road_toll_model = $this->model->get('roadtollModel');

        $road_oils = $road_oil_model->getAllRoad(array('where'=>'road='.$id));
        $this->view->data['road_oils'] = $road_oils;

        $road_tolls = $road_toll_model->getAllRoad(array('where'=>'road='.$id));
        $this->view->data['road_tolls'] = $road_tolls;

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace();

        $this->view->data['places'] = $places;

        $route_model = $this->model->get('routeModel');

        $routes = $route_model->getAllRoute();

        $this->view->data['routes'] = $routes;

        $oil_model = $this->model->get('oilModel');

        $oils = $oil_model->getAllOil();

        $this->view->data['oils'] = $oils;

        $toll_model = $this->model->get('tollModel');

        $tolls = $toll_model->getAllToll(array('order_by'=>'toll_code','order'=>'ASC'));

        $this->view->data['tolls'] = $tolls;


        return $this->view->show('road/edit');

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

            $this->view->redirect('road');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin định mức tuyến đường';

        $road_model = $this->model->get('roadModel');

        $road_data = $road_model->getRoad($id);

        $this->view->data['road_data'] = $road_data;

        if (!$road_data) {

            $this->view->redirect('road');

        }

        $road_oil_model = $this->model->get('roadoilModel');
        $road_toll_model = $this->model->get('roadtollModel');

        $road_oils = $road_oil_model->getAllRoad(array('where'=>'road='.$id));
        $this->view->data['road_oils'] = $road_oils;

        $road_tolls = $road_toll_model->getAllRoad(array('where'=>'road='.$id));
        $this->view->data['road_tolls'] = $road_tolls;

        
        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace();

        $this->view->data['places'] = $places;

        $route_model = $this->model->get('routeModel');

        $routes = $route_model->getAllRoute();

        $this->view->data['routes'] = $routes;

        $oil_model = $this->model->get('oilModel');

        $oils = $oil_model->getAllOil();

        $this->view->data['oils'] = $oils;

        $toll_model = $this->model->get('tollModel');

        $tolls = $toll_model->getAllToll(array('order_by'=>'toll_code','order'=>'ASC'));

        $this->view->data['tolls'] = $tolls;


        return $this->view->show('road/view');

    }

    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $route_model = $this->model->get('routeModel');
        $place_model = $this->model->get('placeModel');

        $routes = $route_model->getAllRoute(array('order_by'=>'route_name','order'=>'ASC'));
        $places = $place_model->getAllPlace(array('order_by'=>'place_code','order'=>'ASC'));

        $this->view->data['routes'] = $routes;
        $this->view->data['places'] = $places;

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('road/filter');
    }

    public function deleteroadoil(){
        if (isset($_POST['data'])) {
            $road_oil_model = $this->model->get('roadoilModel');

            $road_oil_model->queryRoad('DELETE FROM road_oil WHERE road_oil_id='.$_POST['data'].' AND road='.$_POST['road']);
        }
    }
    public function deleteroadtoll(){
        if (isset($_POST['data'])) {
            $road_toll_model = $this->model->get('roadtollModel');

            $road_toll_model->queryRoad('DELETE FROM road_toll WHERE road_toll_id='.$_POST['data'].' AND road='.$_POST['road']);
        }
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->road) || json_decode($_SESSION['user_permission_action'])->road != "road") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $road_model = $this->model->get('roadModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $road_model->deleteRoad($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|road|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'road',
                    'user_log_table_name' => 'Định mức tuyến đường',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $road_model->deleteRoad($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|road|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'road',
                    'user_log_table_name' => 'Định mức tuyến đường',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importroad(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->road) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('road/import');

    }


}

?>