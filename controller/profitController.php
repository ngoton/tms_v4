<?php

Class profitController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->profit) || json_decode($_SESSION['user_permission_action'])->profit != "profit") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Tổng hợp chi phí';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;

            $xe = isset($_POST['xe']) ? $_POST['xe'] : null;

            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;

            $trangthai = isset($_POST['staff']) ? $_POST['staff'] : null;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'vehicle_number ASC, shipment_date ASC, ';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'shipment_round ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 18446744073709;



            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y'); //cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')).'-'.date('m-Y');

            $xe = 0;

            $vong = (int)date('m',strtotime($batdau));

            $trangthai = date('Y',strtotime($batdau));

        }
        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));


        $vong = (int)date('m',strtotime($batdau));

        $trangthai = date('Y',strtotime($batdau));



        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));



        $this->view->data['vehicles'] = $vehicles;



        $join = array('table'=>'customer, vehicle','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle');



        $shipment_model = $this->model->get('shipmentModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;



        $data = array(

            'where' => 'shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc),

            );

        if($xe > 0){

            $data['where'] = $data['where'].' AND vehicle = '.$xe;

        }



        /*if ($_SESSION['role_logined'] == 3) {

            $data['where'] = $data['where'].' AND shipment_create_user = '.$_SESSION['userid_logined'];

            

        }*/



        $tongsodong = count($shipment_model->getAllShipment($data,$join));

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

        $this->view->data['vong'] = $vong;

        $this->view->data['trangthai'] = $trangthai;



        $this->view->data['xe'] = $xe;





        $data = array(

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            'where' => 'shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc),

            );

        if($xe > 0){

            $data['where'] = $data['where'].' AND vehicle = '.$xe;

        }



        /*if ($_SESSION['role_logined'] == 3) {

            $data['where'] = $data['where'].' AND shipment_create_user = '.$_SESSION['userid_logined'];

            

        }*/



        if ($keyword != '') {

            $ngay = (strtotime(str_replace("/", "-", $keyword))!="")?(' OR shipment_date LIKE "%'.strtotime(str_replace("/", "-", $keyword)).'%"'):"";

            $search = '(

                    vehicle_number LIKE "%'.$keyword.'%"

                    OR customer_name LIKE "%'.$keyword.'%"

                    OR shipment_from in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                    OR shipment_to in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                    '.$ngay.'

                        )';

            $data['where'] = $data['where']." AND ".$search;

        }



        

        

        $warehouse_model = $this->model->get('warehouseModel');

        $road_model = $this->model->get('roadModel');

        

        $place_model = $this->model->get('placeModel');

        $place_data = array();



        $warehouse_data = array();

        $road_data = array();

        

        $datas = $shipment_model->getAllShipment($data,$join);



        $this->view->data['shipments'] = $datas;



        $this->view->data['lastID'] = isset($shipment_model->getLastShipment()->shipment_id)?$shipment_model->getLastShipment()->shipment_id:0;



        $v = array();



        foreach ($datas as $ship) {

            $month = date('m',$ship->shipment_date);

            $year = date('Y',$ship->shipment_date);

            

           $v[$ship->vehicle.$ship->shipment_round.$month.$year][] = $ship->shipment_from.'-'.$ship->shipment_to;





           $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));

            

            $road_data['road_revenue'][$ship->shipment_from.'-'.$ship->shipment_to] = $ship->shipment_revenue;

            $road_data['oil_cost'][$ship->shipment_from.'-'.$ship->shipment_to] = $ship->oil_cost;



            $chek_rong = 0;

            

            foreach ($roads as $road) {

                $road_data['bridge_cost'][$ship->shipment_from.'-'.$ship->shipment_to] = $road->bridge_cost;

                $road_data['police_cost'][$ship->shipment_from.'-'.$ship->shipment_to] = $road->police_cost;

                $road_data['tire_cost'][$ship->shipment_from.'-'.$ship->shipment_to] = $road->tire_cost;

                $road_data['road_oil'][$ship->shipment_from.'-'.$ship->shipment_to] = $road->road_oil;

                $road_data['road_time'][$ship->shipment_from.'-'.$ship->shipment_to] = $road->road_time;



                $chek_rong = ($road->way == 0)?1:0;



            }

            $places = $place_model->getAllPlace(array('where'=>'place_id = '.$ship->shipment_from.' OR place_id = '.$ship->shipment_to));

        



                foreach ($places as $place) {

                    

                        $place_data['place_id'][$place->place_id] = $place->place_id;

                        $place_data['place_name'][$place->place_id] = $place->place_name;

                    

                    

                }

            $warehouse = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$ship->shipment_from.' OR warehouse_code = '.$ship->shipment_to.') AND start_time <= '.$ship->shipment_date.' AND end_time >= '.$ship->shipment_date));

        



            $boiduong_cont = 0;

            $boiduong_tan = 0;



            

            foreach ($warehouse as $warehouse) {

                

                    $warehouse_data['warehouse_id'][$warehouse->warehouse_code] = $warehouse->warehouse_code;

                    $warehouse_data['warehouse_name'][$warehouse->warehouse_code] = $warehouse->warehouse_name;



                    $tan = explode(".",$ship->shipment_ton);

                    if (isset($tan[1]) && substr($tan[1], 0, 1) > 5 ) {

                        $trongluong = $tan[0] + 1;

                    }

                    elseif (isset($tan[1]) && substr($tan[1], 0, 1) < 5 ) {

                        $trongluong = $tan[0];

                    }

                    else{

                        $trongluong = $tan[0]+('0.'.(isset($tan[1])?substr($tan[1], 0, 1):0));

                    }





                if($chek_rong == 0){

                    if ($warehouse->warehouse_cont != 0) {

                        $boiduong_cont += $warehouse->warehouse_cont;

                    }

                    if ($warehouse->warehouse_ton != 0){

                        $boiduong_tan += $trongluong * $warehouse->warehouse_ton;

                    }

                }

                

            }

            $warehouse_data['boiduong_cn'][$ship->shipment_from.'-'.$ship->shipment_to] = $boiduong_cont+$boiduong_tan;

        }



        $tam = array('mang'=>array());

        $sum = array();

        $mang = array_values($v);

        

        for ($i = 0; $i < count($mang); $i++):

            $dem=1;

            for ($j = $i + 1; $j < count($mang); $j++):

                if($this->kiemtra($mang[$i],$mang[$j] ) == true){

                    $dem = $dem+1;

                }

            endfor;



            $temp["mang"] = $mang[$i];

            $temp["soluong"] = $dem;



            $arrays = array(

                'mang' => $mang[$i]

            );



            if(!in_array($arrays,$tam )){

                $sum[]=$temp;

                $tam[]['mang']=$temp['mang'];

            }



        endfor;





        $this->view->data['warehouse'] = $warehouse_data;

        $this->view->data['road'] = $road_data;

        $this->view->data['arr'] = $sum;

        $this->view->data['place'] = $place_data;



        $this->view->show('profit/index');

    }





    function kiemtra($array1, $array2) {

        foreach ($array1 as $value) {

            if (!in_array($value, $array2)) {

                return false;

            }

        }

        return true;

    }

    



}





?>