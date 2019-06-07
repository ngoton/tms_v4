<?php
Class usedController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->used) || json_decode($_SESSION['user_permission_action'])->used != "used") {
            $this->view->data['disable_control'] = 1;
        }
        
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Quản lý tình trạng sử dụng vật  tư';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $xe = isset($_POST['xe']) ? $_POST['xe'] : null;

            $mooc = isset($_POST['trangthai']) ? $_POST['trangthai'] : null;
        }

        else{

            $keyword = "";

            $xe = 0;

            $mooc = 0;

        }

        $vehicle_model = $this->model->get('vehicleModel');
        $romooc_model = $this->model->get('romoocModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $this->view->data['vehicle_lists'] = $vehicles;

        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));
        $this->view->data['romooc_lists'] = $romoocs;
        
        $this->view->data['xe'] = $xe;
        $this->view->data['mooc'] = $mooc;

        $this->view->data['keyword'] = $keyword;

        $this->view->data['page'] = 1;
        $this->view->data['order_by'] = "";
        $this->view->data['order'] = "";

        $data = array(
            'where' => '1=1',
            'order_by' => 'vehicle_number',
            'order' => 'ASC',
            );

        if ($xe > 0) {
            $data['where'] .= ' AND vehicle_id = '.$xe;
        }
        
        if ($keyword != '') {
            $search = ' AND ( vehicle_number LIKE "%'.$keyword.'%"  )';
            $data['where'] .= $search;
        }
        
        $vehicles = $vehicle_model->getAllVehicle($data);
        $this->view->data['vehicles'] = $vehicles;

        $data = array(
            'where' => '1=1',
            'order_by' => 'romooc_number',
            'order' => 'ASC',
            );

        if ($mooc > 0) {
            $data['where'] .= ' AND romooc_id = '.$mooc;
        }
        
        if ($keyword != '') {
            $search = ' AND ( romooc_number LIKE "%'.$keyword.'%"  )';
            $data['where'] .= $search;
        }
        
        $romoocs = $romooc_model->getAllVehicle($data);
        $this->view->data['romoocs'] = $romoocs;



        $shipment_model = $this->model->get('shipmentModel');
        $road_model = $this->model->get('roadModel');

        $spare_vehicle_model = $this->model->get('sparevehicleModel');
        $data_vehicle = array();
        $data_romooc = array();

        foreach ($vehicles as $vehicle) {
            $qr = 'SELECT *, (COALESCE(start_time,0)+COALESCE(end_time,0)) AS day FROM spare_vehicle WHERE vehicle = '.$vehicle->vehicle_id.' ORDER BY day ASC';
            $data_vehicle[$vehicle->vehicle_id] = $spare_vehicle_model->queryStock($qr);
        }

        foreach ($romoocs as $romooc) {
            $qr = 'SELECT *, (COALESCE(start_time,0)+COALESCE(end_time,0)) AS day FROM spare_vehicle WHERE romooc = '.$romooc->romooc_id.' ORDER BY day ASC';
            $data_romooc[$romooc->romooc_id] = $spare_vehicle_model->queryStock($qr);
        }

        $this->view->data['data_vehicle'] = $data_vehicle;
        $this->view->data['data_romooc'] = $data_romooc;

        $spare_model = $this->model->get('sparepartModel');
        $spare_data = array();
        $data = array(
            'where' => 'spare_part_id IN (SELECT spare_part FROM spare_vehicle)',
        );
        $spares = $spare_model->getAllStock($data);
        

        foreach ($spares as $spare) {

            $spare_data[$spare->spare_part_id]['code'] = $spare->spare_part_code;
            $spare_data[$spare->spare_part_id]['name'] = $spare->spare_part_name;
            $spare_data[$spare->spare_part_id]['seri'] = $spare->spare_part_seri;
           
            $data_im = array(
                'where' => 'spare_part = '.$spare->spare_part_id.' AND start_time > 0 ',
            );
            $stock_ims = $spare_vehicle_model->getAllStock($data_im);
            foreach ($stock_ims as $im) {

                $end_time = 0;
                $data_ex = array(
                    'where' => 'spare_part = '.$spare->spare_part_id.' AND end_time > 0 AND end_time >= '.$im->start_time,
                    'order_by' => 'end_time ASC',
                    'limit' => 1,
                );
                $stock_exs = $spare_vehicle_model->getAllStock($data_ex);
                foreach ($stock_exs as $ex) {
                    $end_time = $ex->end_time;
                }

                if ($im->vehicle > 0) {
                    $data_ship = array(
                        'where'=>'vehicle = '.$im->vehicle.' AND shipment_date >= '.$im->start_time,
                        'field'=>'shipment_id,shipment_sub,route',
                    );
                    if ($end_time > 0) {
                        $data_ship['where'] .= ' AND shipment_date <= '.$end_time;
                    }
                    $shipments = $shipment_model->getAllShipment($data_ship);
                    foreach ($shipments as $ship) {
                        $check_sub = 1;
                        if ($ship->shipment_sub==1) {
                           $check_sub = 0;
                        }
                        $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));
                        foreach ($roads as $road) {
                            $spare_data[$spare->spare_part_id]['km']['vehicle'] = isset($spare_data[$spare->spare_part_id]['km']['vehicle'])?$spare_data[$spare->spare_part_id]['km']['vehicle']+$road->road_km*$check_sub:$road->road_km*$check_sub;
                        }
                    }
                }
                if ($im->romooc > 0) {
                    $data_ship = array(
                        'where'=>'romooc = '.$im->romooc.' AND shipment_date >= '.$im->start_time,
                        'field'=>'shipment_id,shipment_sub,route',
                    );
                    if ($end_time > 0) {
                        $data_ship['where'] .= ' AND shipment_date <= '.$end_time;
                    }
                    $shipments = $shipment_model->getAllShipment($data_ship);
                    foreach ($shipments as $ship) {
                        $check_sub = 1;
                        if ($ship->shipment_sub==1) {
                           $check_sub = 0;
                        }
                        $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));
                        foreach ($roads as $road) {
                            $spare_data[$spare->spare_part_id]['km']['romooc'] = isset($spare_data[$spare->spare_part_id]['km']['romooc'])?$spare_data[$spare->spare_part_id]['km']['romooc']+$road->road_km*$check_sub:$road->road_km*$check_sub;
                        }
                    }
                }
            }

        }
        
        $this->view->data['spare_data'] = $spare_data;
        
        $this->view->show('used/index');
    }
    

    public function index1() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->used) || json_decode($_SESSION['user_permission_action'])->used != "used") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Quản lý thông tin vật tư';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'spare_part_code';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
        }

        $id = $this->registry->router->param_id;

        $spare_model = $this->model->get('sparepartModel');
        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $data = array(
            'where' => 'spare_part_id IN (SELECT spare_part FROM spare_vehicle)',
        );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND spare_part_id = '.$id;
        }

        $tongsodong = count($spare_model->getAllStock($data));
        $tongsotrang = ceil($tongsodong / $sonews);
        

        $this->view->data['page'] = $page;
        $this->view->data['order_by'] = $order_by;
        $this->view->data['order'] = $order;
        $this->view->data['keyword'] = $keyword;
        $this->view->data['pagination_stages'] = $pagination_stages;
        $this->view->data['tongsotrang'] = $tongsotrang;
        $this->view->data['sonews'] = $sonews;
        $this->view->data['limit'] = $limit;

        $data = array(
            'order_by'=>$order_by,
            'order'=>$order,
            'limit'=>$x.','.$sonews,
            'where' => 'spare_part_id IN (SELECT spare_part FROM spare_vehicle)',
            );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND spare_part_id = '.$id;
        }
        
        if ($keyword != '') {
            $search = ' AND ( spare_part_code LIKE "%'.$keyword.'%" 
                        OR spare_part_name LIKE "%'.$keyword.'%" 
                        OR spare_part_seri LIKE "%'.$keyword.'%" 
                        OR spare_part_brand LIKE "%'.$keyword.'%" )';
            $data['where'] .= $search;
        }
        
        $spares = $spare_model->getAllStock($data);
        $this->view->data['spares'] = $spares;

        $shipment_model = $this->model->get('shipmentModel');
        $road_model = $this->model->get('roadModel');

        $spare_vehicle_model = $this->model->get('sparevehicleModel');
        $data_vehicle = array();

        foreach ($spares as $spare) {
           
            $data_im = array(
                'where' => 'spare_part = '.$spare->spare_part_id.' AND start_time > 0 ',
            );
            $stock_ims = $spare_vehicle_model->getAllStock($data_im);
            foreach ($stock_ims as $im) {
                $data_vehicle[$spare->spare_part_id]['import'] = $im->start_time;
                
                $data_vehicle[$spare->spare_part_id]['vehicle'] = $im->vehicle;
                $data_vehicle[$spare->spare_part_id]['romooc'] = $im->romooc;

                $end_time = 0;
                $data_ex = array(
                    'where' => 'spare_part = '.$spare->spare_part_id.' AND end_time > 0 AND end_time >= '.$im->start_time,
                    'order_by' => 'end_time ASC',
                    'limit' => 1,
                );
                $stock_exs = $spare_vehicle_model->getAllStock($data_ex);
                foreach ($stock_exs as $ex) {
                    $end_time = $ex->end_time;
                }

                if ($im->vehicle > 0) {
                    $data_ship = array(
                        'where'=>'vehicle = '.$im->vehicle.' AND shipment_date >= '.$im->start_time,
                    );
                    if ($end_time > 0) {
                        $data_ship['where'] .= ' AND shipment_date <= '.$end_time;
                    }
                    $shipments = $shipment_model->getAllShipment($data_ship);
                    foreach ($shipments as $ship) {
                        $check_sub = 1;
                        if ($ship->shipment_sub==1) {
                           $check_sub = 0;
                        }
                        $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));
                        foreach ($roads as $road) {
                            $data_vehicle[$spare->spare_part_id]['km'] = isset($data_vehicle[$spare->spare_part_id]['km'])?$data_vehicle[$spare->spare_part_id]['km']+$road->road_km*$check_sub:$road->road_km*$check_sub;
                        }
                    }
                }
                if ($im->romooc > 0) {
                    $data_ship = array(
                        'where'=>'romooc = '.$im->romooc.' AND shipment_date >= '.$im->start_time,
                    );
                    if ($end_time > 0) {
                        $data_ship['where'] .= ' AND shipment_date <= '.$end_time;
                    }
                    $shipments = $shipment_model->getAllShipment($data_ship);
                    foreach ($shipments as $ship) {
                        $check_sub = 1;
                        if ($ship->shipment_sub==1) {
                           $check_sub = 0;
                        }
                        $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));
                        foreach ($roads as $road) {
                            $data_vehicle[$spare->spare_part_id]['km'] = isset($data_vehicle[$spare->spare_part_id]['km'])?$data_vehicle[$spare->spare_part_id]['km']+$road->road_km*$check_sub:$road->road_km*$check_sub;
                        }
                    }
                }
            }

            $data_ex = array(
                'where' => 'spare_part = '.$spare->spare_part_id.' AND end_time > 0 ',
            );
            $stock_exs = $spare_vehicle_model->getAllStock($data_ex);
            foreach ($stock_exs as $ex) {
                $data_vehicle[$spare->spare_part_id]['export'] = $ex->end_time;
            }

        }
        

        $this->view->data['data_vehicle'] = $data_vehicle;

        

        

        $this->view->data['lastID'] = isset($spare_model->getLastStock()->spare_part_id)?$spare_model->getLastStock()->spare_part_id:0;
        
        $this->view->show('used/index');
    }

   


}
?>