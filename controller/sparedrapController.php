<?php
Class sparedrapController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->sparedrap) || json_decode($_SESSION['user_permission_action'])->sparedrap != "sparedrap") {
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
            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;
            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;

            $trangthai = isset($_POST['staff']) ? $_POST['staff'] : null;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'spare_part_code';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

            $vong = (int)date('m',strtotime($batdau));

            $trangthai = date('Y',strtotime($batdau));
        }

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));

        $trangthai = date('Y',strtotime($batdau));
        
        $vehicle_model = $this->model->get('vehicleModel');
        $romooc_model = $this->model->get('romoocModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $vehicle_data = array();
        foreach ($vehicles as $ve) {
            $vehicle_data[$ve->vehicle_id] = $ve->vehicle_number;
        }
        $this->view->data['vehicle_data'] = $vehicle_data;
        
        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));
        $romooc_data = array();
        foreach ($romoocs as $ro) {
            $romooc_data[$ro->romooc_id] = $ro->romooc_number;
        }
        $this->view->data['romooc_data'] = $romooc_data;

        $spare_model = $this->model->get('sparedrapModel');
        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $json = array('table'=>'spare_part, spare_vehicle','where'=>'spare_drap.spare_part = spare_part_id AND spare_vehicle = spare_vehicle_id');
        $data = array(
            'where' => 'end_time >= '.strtotime($batdau).' AND end_time < '.strtotime($ngayketthuc),
        );
        
        $tongsodong = count($spare_model->getAllStock($data,$json));
        $tongsotrang = ceil($tongsodong / $sonews);
        

        $this->view->data['page'] = $page;
        $this->view->data['order_by'] = $order_by;
        $this->view->data['order'] = $order;
        $this->view->data['keyword'] = $keyword;
        $this->view->data['pagination_stages'] = $pagination_stages;
        $this->view->data['tongsotrang'] = $tongsotrang;
        $this->view->data['sonews'] = $sonews;
        $this->view->data['limit'] = $limit;
        $this->view->data['batdau'] = $batdau;

        $this->view->data['ketthuc'] = $ketthuc;

        $this->view->data['vong'] = $vong;

        $this->view->data['trangthai'] = $trangthai;

        $data = array(
            'order_by'=>$order_by,
            'order'=>$order,
            'limit'=>$x.','.$sonews,
            'where' => 'end_time >= '.strtotime($batdau).' AND end_time < '.strtotime($ngayketthuc),
            );

        
        if ($keyword != '') {
            $search = ' AND ( spare_part_code LIKE "%'.$keyword.'%" 
                        OR spare_part_name LIKE "%'.$keyword.'%" 
                        OR spare_part_seri LIKE "%'.$keyword.'%" 
                        OR spare_part_brand LIKE "%'.$keyword.'%" )';
            $data['where'] .= $search;
        }
        
        $spares = $spare_model->getAllStock($data,$json);
        $this->view->data['spares'] = $spares;

        $spare_sub_model = $this->model->get('sparesubModel');

        $shipment_model = $this->model->get('shipmentModel');
        $road_model = $this->model->get('roadModel');

        $spare_vehicle_model = $this->model->get('sparevehicleModel');
        $data_vehicle = array();

        $spare_part_types = array();
        foreach ($spares as $spare) {
            $spare_sub = "";
            $sts = explode(',', $spare->spare_part_type);
            foreach ($sts as $key) {
                $subs = $spare_sub_model->getStock($key);
                if ($subs) {
                    if ($spare_sub == "")
                        $spare_sub .= $subs->spare_sub_name;
                    else
                        $spare_sub .= ','.$subs->spare_sub_name;
                }
                
            }
            $spare_part_types[$spare->spare_drap_id] = $spare_sub;

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
        }

        $this->view->data['data_vehicle'] = $data_vehicle;
        
        $this->view->data['spare_part_types'] = $spare_part_types;

        $this->view->data['lastID'] = isset($spare_model->getLastStock()->spare_drap_id)?$spare_model->getLastStock()->spare_drap_id:0;
        
        $this->view->show('sparedrap/index');
    }

    public function getSub(){
        header('Content-type: application/json');
        $q = $_GET["search"];

        $sub_model = $this->model->get('sparesubModel');
        $data = array(
            'where' => 'spare_sub_name LIKE "%'.$q.'%"',
        );
        $subs = $sub_model->getAllStock($data);
        $arr = array();
        foreach ($subs as $sub) {
            $arr[] = $sub->spare_sub_name;
        }
        
        echo json_encode($arr);
    }


}
?>