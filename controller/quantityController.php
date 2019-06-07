<?php

Class quantityController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->quantity) || json_decode($_SESSION['user_permission_action'])->quantity != "quantity") {
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

            $kh = isset($_POST['trangthai']) ? $_POST['trangthai'] : null;

            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;

            $trangthai = isset($_POST['staff']) ? $_POST['staff'] : null;

            

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'tongchuyen';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y'); //cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')).'-'.date('m-Y');

            $xe = 0;

            $kh = 0;

            $vong = (int)date('m',strtotime($batdau));

            $trangthai = date('Y',strtotime($batdau));

            

        }

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));

        $trangthai = date('Y',strtotime($batdau));



        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));



        $this->view->data['vehicles'] = $vehicles;



        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getAllCustomer(array('order_by'=>'customer_name','order'=>'ASC'));



        $this->view->data['customers'] = $customers;





        //$join = array('table'=>'customer, vehicle, road','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND road_from = shipment_from AND road_to = shipment_to AND shipment_date >= start_time AND shipment_date <= end_time');

        $query = 'SELECT *, count(*) AS tongchuyen, max(shipment_charge) AS giacaonhat, min(shipment_charge) AS giathapnhat, round(avg(shipment_charge)) AS giabinhquan FROM shipment, customer, vehicle WHERE customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle';



        $shipment_model = $this->model->get('shipmentModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;



        

        if($batdau != "" && $ketthuc != "" ){

            $query = $query.' AND shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc);

        }

        if($xe > 0){

            $query = $query.' AND vehicle = '.$xe;

        }

        if($kh > 0){

            $query = $query.' AND customer = '.$kh;

        }



        /*if ($_SESSION['role_logined'] == 3) {

            $query = $query.' AND shipment_create_user = '.$_SESSION['userid_logined'];

            

        }*/



        $query .= ' GROUP BY shipment_from, shipment_to';

        //$data['where'] = $data['where'].' AND way != 0';



        $tongsodong = count($shipment_model->queryShipment($query));

        $tongsotrang = ceil($tongsodong / $sonews);

        



        $this->view->data['page'] = $page;

        $this->view->data['order_by'] = $order_by;

        $this->view->data['order'] = $order;

        $this->view->data['keyword'] = $keyword;

        $this->view->data['pagination_stages'] = $pagination_stages;

        $this->view->data['tongsotrang'] = $tongsotrang;

        $this->view->data['sonews'] = $sonews;



        $this->view->data['batdau'] = $batdau;

        $this->view->data['ketthuc'] = $ketthuc;



        $this->view->data['xe'] = $xe;

        $this->view->data['kh'] = $kh;

        $this->view->data['vong'] = $vong;

        $this->view->data['trangthai'] = $trangthai;



        $this->view->data['limit'] = $limit;



        $query = 'SELECT *, count(*) AS tongchuyen, max(shipment_charge) AS giacaonhat, min(shipment_charge) AS giathapnhat, round(avg(shipment_charge)) AS giabinhquan FROM shipment, customer, vehicle WHERE customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND shipment_revenue > 0';



        

        

        if($batdau != "" && $ketthuc != "" ){

            $query = $query.' AND shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc);

        }

        if($xe > 0){

            $query = $query.' AND vehicle = '.$xe;

        }

        if($kh > 0){

            $query = $query.' AND customer = '.$kh;

        }



        /*if ($_SESSION['role_logined'] == 3) {

            $query = $query.' AND shipment_create_user = '.$_SESSION['userid_logined'];

            

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

            $query = $query." AND ".$search;

        }



        $query .= ' GROUP BY shipment_from, shipment_to';

        $query .= ' ORDER BY '.$order_by.' '.$order.' LIMIT '.$x.','.$sonews;







        //$data['where'] = $data['where'].' AND way != 0';

        

        $warehouse_model = $this->model->get('warehouseModel');

        $road_model = $this->model->get('roadModel');

        

        $place_model = $this->model->get('placeModel');

        $place_data = array();



        $warehouse_data = array();

        $road_data = array();

        

        $shipments = $shipment_model->queryShipment($query);



        $this->view->data['shipments'] = $shipments;



        $giathitruong = array();



        foreach ($shipments as $ship) {



            $query2 = 'SELECT *, max(shipment_charge) AS giathitruong FROM shipment WHERE shipment_from = '.$ship->shipment_from.' AND shipment_to = '.$ship->shipment_to.' AND shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc).' ORDER BY shipment_date DESC LIMIT 3';

            

            $shipments2 = $shipment_model->queryShipment($query2);



            foreach ($shipments2 as $ship2) {

                $giathitruong[$ship->shipment_from][$ship->shipment_to] = $ship2->giathitruong;

            }



            

           $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));

            

           $road_data['oil_add'][$ship->shipment_id] = ($ship->oil_add_dc == 5)?$ship->oil_add:0;

           $road_data['oil_add2'][$ship->shipment_id] = ($ship->oil_add_dc2 == 5)?$ship->oil_add2:0;



            $chek_rong = 0;

            

            foreach ($roads as $road) {

                $road_data['bridge_cost'][$ship->shipment_id] = $road->bridge_cost;

                $road_data['police_cost'][$ship->shipment_id] = $road->police_cost;

                $road_data['tire_cost'][$ship->shipment_id] = $road->tire_cost;

                $road_data['way'][$ship->shipment_id] = $road->way;

                $road_data['road_time'][$ship->shipment_id] = $road->road_time;

                $road_data['road_oil'][$ship->shipment_id] = $road->road_oil;

                $chek_rong = ($road->way == 0)?1:0;



            }

            $places = $place_model->getAllPlace(array('where'=>'place_id = '.$ship->shipment_from.' OR place_id = '.$ship->shipment_to));

        



                foreach ($places as $place) {

                    

                        $place_data['place_id'][$place->place_id] = $place->place_id;

                        $place_data['place_name'][$place->place_id] = $place->place_name;

                    

                    

                }


            $warehouses = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$ship->shipment_from.' OR warehouse_code = '.$ship->shipment_to.') AND start_time <= '.$ship->shipment_date.' AND end_time >= '.$ship->shipment_date));

        



            $boiduong_cont = 0;

            $boiduong_tan = 0;



            

            foreach ($warehouses as $warehouse) {

                

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





                    if ($warehouse->warehouse_add != 0) {

                        $boiduong_cont += $warehouse->warehouse_add;

                    }

                    if ($warehouse->warehouse_ton != 0){

                        $boiduong_tan += $trongluong * $warehouse->warehouse_ton;

                    }



                    $warehouse_data['warehouse_weight'][$warehouse->warehouse_code] = $warehouse->warehouse_weight;

                    $warehouse_data['warehouse_clean'][$warehouse->warehouse_code] = $warehouse->warehouse_clean;

                    $warehouse_data['warehouse_gate'][$warehouse->warehouse_code] = $warehouse->warehouse_gate;

                    $warehouse_data['warehouse_add'][$warehouse->warehouse_code] = $warehouse->warehouse_add;



            

                

            }

            $warehouse_data['boiduong_cn'][$ship->shipment_id] = $boiduong_cont+$boiduong_tan;

        }



        $this->view->data['warehouse'] = $warehouse_data;

        $this->view->data['road'] = $road_data;

        $this->view->data['giathitruong'] = $giathitruong;

        $this->view->data['place'] = $place_data;

        

        $this->view->show('quantity/index');

    }



    



}

?>