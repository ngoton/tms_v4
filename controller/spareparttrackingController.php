<?php
Class spareparttrackingController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->spareparttracking) || json_decode($_SESSION['user_permission_action'])->spareparttracking != "spareparttracking") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Theo dõi tình trạng sử dụng';

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
            $limit = 18446744073709;
        }

        $id = $this->registry->router->param_id;

        $vehicle_model = $this->model->get('vehicleModel');
        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $this->view->data['vehicles'] = $vehicles;

        $vehicle_data = array();
        foreach ($vehicles as $vehicle) {
            $vehicle_data['id'][$vehicle->vehicle_id] = $vehicle->vehicle_id;
            $vehicle_data['name'][$vehicle->vehicle_id] = $vehicle->vehicle_number;
        }
        $this->view->data['vehicle_data'] = $vehicle_data;

        $romooc_model = $this->model->get('romoocModel');
        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));
        $this->view->data['romoocs'] = $romoocs;

        $romooc_data = array();
        foreach ($romoocs as $romooc) {
            $romooc_data['id'][$romooc->romooc_id] = $romooc->romooc_id;
            $romooc_data['name'][$romooc->romooc_id] = $romooc->romooc_number;
        }
        $this->view->data['romooc_data'] = $romooc_data;

        $road_cost_model = $this->model->get('roadcostModel');
        $checking_cost_model = $this->model->get('checkingcostModel');
        $insurance_cost_model = $this->model->get('insurancecostModel');

        $road_cost_data = array();
        $checking_cost_data = array();
        $insurance_cost_data = array();

        foreach ($vehicles as $vehicle) {
            $rc = $road_cost_model->queryCost('SELECT * FROM road_cost WHERE (vehicle LIKE "'.$vehicle->vehicle_id.'" OR vehicle LIKE "%,'.$vehicle->vehicle_id.'" OR vehicle LIKE "%,'.$vehicle->vehicle_id.',%" OR vehicle LIKE "'.$vehicle->vehicle_id.',%") ORDER BY end_time DESC LIMIT 1');
            foreach ($rc as $r) {
                $road_cost_data['vehicle'][$vehicle->vehicle_id] = $r->end_time;

                $today = strtotime(date('d-m-Y', strtotime(date('d-m-Y'). ' + 1 days')));
                $thismonth = strtotime(date('d-m-Y', strtotime(date('t-m-Y'). ' + 1 days')));
                $threemonth = strtotime(date('d-m-Y', strtotime(date('d-m-Y'). ' + 90 days')));
                if ($today > $r->end_time) {
                    $road_cost_data['end_vehicle'][$vehicle->vehicle_id] = $r->end_time;
                }
                else if($today < $r->end_time && $r->end_time < $thismonth){
                    $road_cost_data['month_vehicle'][$vehicle->vehicle_id] = $r->end_time;
                }
                else if($thismonth < $r->end_time && $r->end_time < $threemonth){
                    $road_cost_data['3month_vehicle'][$vehicle->vehicle_id] = $r->end_time;
                }
            }

            $cc = $checking_cost_model->queryCost('SELECT * FROM checking_cost WHERE (vehicle LIKE "'.$vehicle->vehicle_id.'" OR vehicle LIKE "%,'.$vehicle->vehicle_id.'" OR vehicle LIKE "%,'.$vehicle->vehicle_id.',%" OR vehicle LIKE "'.$vehicle->vehicle_id.',%") ORDER BY end_time DESC LIMIT 1');
            foreach ($cc as $c) {
                $checking_cost_data['vehicle'][$vehicle->vehicle_id] = $c->end_time;

                $today = strtotime(date('d-m-Y', strtotime(date('d-m-Y'). ' + 1 days')));
                $thismonth = strtotime(date('d-m-Y', strtotime(date('t-m-Y'). ' + 1 days')));
                $threemonth = strtotime(date('d-m-Y', strtotime(date('d-m-Y'). ' + 90 days')));
                if ($today > $c->end_time) {
                    $checking_cost_data['end_vehicle'][$vehicle->vehicle_id] = $c->end_time;
                }
                else if($today < $c->end_time && $c->end_time < $thismonth){
                    $checking_cost_data['month_vehicle'][$vehicle->vehicle_id] = $c->end_time;
                }
                else if($thismonth < $c->end_time && $c->end_time < $threemonth){
                    $checking_cost_data['3month_vehicle'][$vehicle->vehicle_id] = $c->end_time;
                }
            }

            $ic = $insurance_cost_model->queryCost('SELECT * FROM insurance_cost WHERE (vehicle LIKE "'.$vehicle->vehicle_id.'" OR vehicle LIKE "%,'.$vehicle->vehicle_id.'" OR vehicle LIKE "%,'.$vehicle->vehicle_id.',%" OR vehicle LIKE "'.$vehicle->vehicle_id.',%") ORDER BY end_time DESC LIMIT 1');
            foreach ($ic as $in) {
                $insurance_cost_data['vehicle'][$vehicle->vehicle_id] = $in->end_time;

                $today = strtotime(date('d-m-Y', strtotime(date('d-m-Y'). ' + 1 days')));
                $thismonth = strtotime(date('d-m-Y', strtotime(date('t-m-Y'). ' + 1 days')));
                $threemonth = strtotime(date('d-m-Y', strtotime(date('d-m-Y'). ' + 90 days')));
                if ($today > $in->end_time) {
                    $insurance_cost_data['end_vehicle'][$vehicle->vehicle_id] = $in->end_time;
                }
                else if($today < $in->end_time && $in->end_time < $thismonth){
                    $insurance_cost_data['month_vehicle'][$vehicle->vehicle_id] = $in->end_time;
                }
                else if($thismonth < $in->end_time && $in->end_time < $threemonth){
                    $insurance_cost_data['3month_vehicle'][$vehicle->vehicle_id] = $in->end_time;
                }
            }
        }
        foreach ($romoocs as $romooc) {
            $rc = $road_cost_model->queryCost('SELECT * FROM road_cost WHERE (romooc LIKE "'.$romooc->romooc_id.'" OR romooc LIKE "%,'.$romooc->romooc_id.'" OR romooc LIKE "%,'.$romooc->romooc_id.',%" OR romooc LIKE "'.$romooc->romooc_id.',%") ORDER BY end_time DESC LIMIT 1');
            foreach ($rc as $r) {
                $road_cost_data['romooc'][$romooc->romooc_id] = $r->end_time;

                $today = strtotime(date('d-m-Y', strtotime(date('d-m-Y'). ' + 1 days')));
                $thismonth = strtotime(date('d-m-Y', strtotime(date('t-m-Y'). ' + 1 days')));
                $threemonth = strtotime(date('d-m-Y', strtotime(date('d-m-Y'). ' + 90 days')));
                if ($today > $r->end_time) {
                    $road_cost_data['end_romooc'][$romooc->romooc_id] = $r->end_time;
                }
                else if($today < $r->end_time && $r->end_time < $thismonth){
                    $road_cost_data['month_romooc'][$romooc->romooc_id] = $r->end_time;
                }
                else if($thismonth < $r->end_time && $r->end_time < $threemonth){
                    $road_cost_data['3month_romooc'][$romooc->romooc_id] = $r->end_time;
                }
            }

            $cc = $checking_cost_model->queryCost('SELECT * FROM checking_cost WHERE (romooc LIKE "'.$romooc->romooc_id.'" OR romooc LIKE "%,'.$romooc->romooc_id.'" OR romooc LIKE "%,'.$romooc->romooc_id.',%" OR romooc LIKE "'.$romooc->romooc_id.',%") ORDER BY end_time DESC LIMIT 1');
            foreach ($cc as $c) {
                $checking_cost_data['romooc'][$romooc->romooc_id] = $c->end_time;

                $today = strtotime(date('d-m-Y', strtotime(date('d-m-Y'). ' + 1 days')));
                $thismonth = strtotime(date('d-m-Y', strtotime(date('t-m-Y'). ' + 1 days')));
                $threemonth = strtotime(date('d-m-Y', strtotime(date('d-m-Y'). ' + 90 days')));
                if ($today > $c->end_time) {
                    $checking_cost_data['end_romooc'][$romooc->romooc_id] = $c->end_time;
                }
                else if($today < $c->end_time && $c->end_time < $thismonth){
                    $checking_cost_data['month_romooc'][$romooc->romooc_id] = $c->end_time;
                }
                else if($thismonth < $c->end_time && $c->end_time < $threemonth){
                    $checking_cost_data['3month_romooc'][$romooc->romooc_id] = $c->end_time;
                }
            }

            $ic = $insurance_cost_model->queryCost('SELECT * FROM insurance_cost WHERE (romooc LIKE "'.$romooc->romooc_id.'" OR romooc LIKE "%,'.$romooc->romooc_id.'" OR romooc LIKE "%,'.$romooc->romooc_id.',%" OR romooc LIKE "'.$romooc->romooc_id.',%") ORDER BY end_time DESC LIMIT 1');
            foreach ($ic as $in) {
                $insurance_cost_data['romooc'][$romooc->romooc_id] = $in->end_time;

                $today = strtotime(date('d-m-Y', strtotime(date('d-m-Y'). ' + 1 days')));
                $thismonth = strtotime(date('d-m-Y', strtotime(date('t-m-Y'). ' + 1 days')));
                $threemonth = strtotime(date('d-m-Y', strtotime(date('d-m-Y'). ' + 90 days')));
                if ($today > $in->end_time) {
                    $insurance_cost_data['end_romooc'][$romooc->romooc_id] = $in->end_time;
                }
                else if($today < $in->end_time && $in->end_time < $thismonth){
                    $insurance_cost_data['month_romooc'][$romooc->romooc_id] = $in->end_time;
                }
                else if($thismonth < $in->end_time && $in->end_time < $threemonth){
                    $insurance_cost_data['3month_romooc'][$romooc->romooc_id] = $in->end_time;
                }
            }
        }

        $this->view->data['road_cost_data'] = $road_cost_data;
        $this->view->data['checking_cost_data'] = $checking_cost_data;
        $this->view->data['insurance_cost_data'] = $insurance_cost_data;

        $spare_model = $this->model->get('sparepartModel');
        $spare_vehicle_model = $this->model->get('sparevehicleModel');
        $spare_vehicles = $spare_vehicle_model->getAllStock(array('where'=>'end_time IS NULL OR end_time=0'));
        $str = "";
        foreach ($spare_vehicles as $sp) {
            if ($str=="") {
                $str .= $sp->spare_part;
            }
            else{
                $str .= ",".$sp->spare_part;
            }
        }

        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;


        $this->view->data['page'] = $page;
        $this->view->data['order_by'] = $order_by;
        $this->view->data['order'] = $order;
        $this->view->data['keyword'] = $keyword;
        $this->view->data['pagination_stages'] = $pagination_stages;
        $this->view->data['tongsotrang'] = 1000;
        $this->view->data['sonews'] = $sonews;
        $this->view->data['limit'] = $limit;

        $data = array(
            'order_by'=>$order_by,
            'order'=>$order,
            'limit'=>$x.','.$sonews,
            'where' => 'spare_part_id IN ('.$str.')',
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

        
        $data_vehicle = array();
        $thang1 = array(); $thang3 = array(); $thang6 = array(); $thang12 = array(); $thang24 = array(); $thang36 = array(); $thang48 = array();

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


            $ngaythayvao = $data_vehicle[$spare->spare_part_id]['import'];
            $timeDiff = strtotime(date('d-m-Y')) - $ngaythayvao;
            $numberDays = $timeDiff/86400;  // 86400 seconds in one day
            $numberDays = intval($numberDays)/30;

            if ($numberDays >= 3 && $numberDays < 6) {
                $thang3[] = $spare;
            }
            else if ($numberDays >= 6 && $numberDays < 12) {
                $thang6[] = $spare;
            }
            else if ($numberDays >= 12 && $numberDays < 24) {
                $thang12[] = $spare;
            }
            else if ($numberDays >= 24 && $numberDays < 36) {
                $thang24[] = $spare;
            }
            else if ($numberDays >= 36 && $numberDays < 48) {
                $thang36[] = $spare;
            }
            else if ($numberDays >= 48) {
                $thang48[] = $spare;
            }
            else{
                $thang1[] = $spare;
            }
        }
        

        $this->view->data['data_vehicle'] = $data_vehicle;

        $this->view->data['thang1'] = $thang1;        
        $this->view->data['thang3'] = $thang3;        
        $this->view->data['thang6'] = $thang6;        
        $this->view->data['thang12'] = $thang12;        
        $this->view->data['thang24'] = $thang24;        
        $this->view->data['thang36'] = $thang36;    
        $this->view->data['thang48'] = $thang48;            

        

        $this->view->data['lastID'] = isset($spare_model->getLastStock()->spare_part_id)?$spare_model->getLastStock()->spare_part_id:0;
        
        $this->view->show('spareparttracking/index');
    }

   


}
?>