<?php

Class truckingController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->trucking) || json_decode($_SESSION['user_permission_action'])->trucking != "trucking") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Tổng hợp phiếu vận chuyển';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;

            $xe = isset($_POST['xe']) ? $_POST['xe'] : null;
            $kh = isset($_POST['nv']) ? $_POST['nv'] : null;
            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;
            $trangthai = isset($_POST['trangthai']) ? $_POST['trangthai'] : null;
        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'shipment_date ASC, ';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'vehicle_number ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

            $xe = 0;
            $kh = 0;

            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y'); //cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')).'-'.date('m-Y');
            $vong = (int)date('m',strtotime($batdau));
            $trangthai = date('Y',strtotime($batdau));

        }
        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));
        $trangthai = date('Y',strtotime($batdau));

        $contunit_model = $this->model->get('contunitModel');
        $cost_list_model = $this->model->get('costlistModel');

        $this->view->data['cont_units'] = $contunit_model->getAllUnit();
        $this->view->data['loan_units'] = $cost_list_model->getAllCost(array('where'=>'cost_list_type = 8'));


        $place_model = $this->model->get('placeModel');

        $place_data = array();

        $places = $place_model->getAllPlace();


        foreach ($places as $place) {

                $place_data['place_id'][$place->place_id] = $place->place_id;

                $place_data['place_name'][$place->place_id] = $place->place_name;

        }

        $this->view->data['place'] = $place_data;

        $romooc_model = $this->model->get('romoocModel');

        $romooc_data = array();

        $romoocs = $romooc_model->getAllVehicle();


        foreach ($romoocs as $romooc) {

                $romooc_data['romooc_id'][$romooc->romooc_id] = $romooc->romooc_id;

                $romooc_data['romooc_number'][$romooc->romooc_id] = $romooc->romooc_number;

        }

        $this->view->data['romooc_data'] = $romooc_data;


        $warehouse_model = $this->model->get('warehouseModel');



        $warehouse_data = array();



        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));



        $this->view->data['vehicles'] = $vehicles;

        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getAllCustomer(array('order_by'=>'customer_name','order'=>'ASC'));



        $this->view->data['customers'] = $customers;



        $shipment_temp_model = $this->model->get('shipmenttempModel');

        $join = array('table'=>'customer, marketing, cont_unit','where'=>'marketing.marketing_id = shipment_temp.marketing AND customer.customer_id = marketing.customer AND cont_unit=cont_unit_id');

        $shipment_temps = $shipment_temp_model->getAllShipment(array('where'=>'shipment_temp_status=0 AND owner='.$_SESSION['userid_logined']),$join);

        $this->view->data['shipment_temps'] = $shipment_temps;



        $join = array('table'=>'customer, vehicle, cont_unit, steersman','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND cont_unit=cont_unit_id AND steersman = steersman_id');



        $shipment_model = $this->model->get('shipmentModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;



        $data = array(

            'where' => 'shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc),

            );


        if($xe != 0){

            $data['where'] = $data['where'].' AND vehicle = '.$xe;

        }
        if($kh > 0){

            $data['where'] = $data['where'].' AND customer = '.$kh;

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



        $this->view->data['xe'] = $xe;
        $this->view->data['kh'] = $kh;
        $this->view->data['vong'] = $vong;
        $this->view->data['trangthai'] = $trangthai;




        $data = array(

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            'where' => 'shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc),

            );


        if($xe != 0){

            $data['where'] = $data['where'].' AND vehicle = '.$xe;

        }
        if($kh > 0){

            $data['where'] = $data['where'].' AND customer = '.$kh;

        }


        /*if ($_SESSION['role_logined'] == 3) {

            $data['where'] = $data['where'].' AND shipment_create_user = '.$_SESSION['userid_logined'];

            

        }*/



        if ($keyword != '') {

            $ngay = (strtotime(str_replace("/", "-", $keyword))!="")?(' OR shipment_date LIKE "%'.strtotime(str_replace("/", "-", $keyword)).'%"'):"";

            $search = '(

                    vehicle_number LIKE "%'.$keyword.'%"

                    OR bill_number LIKE "%'.$keyword.'%"

                    OR steersman_name LIKE "%'.$keyword.'%" 

                    OR customer_name LIKE "%'.$keyword.'%"

                    OR shipment_from in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                    OR shipment_to in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                    '.$ngay.'

                        )';

            $data['where'] = $data['where']." AND ".$search;

        }



        $road_model = $this->model->get('roadModel');

       

        $road_data = array();

        

        $datas = $shipment_model->getAllShipment($data,$join);



        $this->view->data['shipments'] = $datas;



        $this->view->data['lastID'] = isset($shipment_model->getLastShipment()->shipment_id)?$shipment_model->getLastShipment()->shipment_id:0;

        

        $customer_sub_model = $this->model->get('customersubModel');

        $export_stock_model = $this->model->get('exportstockModel');

        $shipment_cost_model = $this->model->get('shipmentcostModel');

        $customer_types = array();

        $export_stocks = array();

        

        $loan_shipment_data = array();

        $v = array();


        foreach ($datas as $ship) {

            $loans = $shipment_cost_model->getAllShipment(array('where'=>'shipment = '.$ship->shipment_id),array('table'=>'cost_list','where'=>'cost_list = cost_list_id AND cost_list_type = 8'));
            foreach ($loans as $loan) {
                $loan_shipment_data[$ship->shipment_id][$loan->cost_list] = isset($loan_shipment_data[$ship->shipment_id][$loan->cost_list])?$loan_shipment_data[$ship->shipment_id][$loan->cost_list]+$loan->cost:$loan->cost;
                
            }

            


           $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));

            

           $road_data['oil_add'][$ship->shipment_id] = ($ship->oil_add_dc == 5)?$ship->oil_add:0;

           $road_data['oil_add2'][$ship->shipment_id] = ($ship->oil_add_dc2 == 5)?$ship->oil_add2:0;



           $check_sub = 1;

           if ($ship->shipment_sub==1) {

               $check_sub = 0;

           }



            $chek_rong = 0;

            

            foreach ($roads as $road) {


                $road_data['bridge_cost'][$ship->shipment_id] = isset($road_data['bridge_cost'][$ship->shipment_id])?$road_data['bridge_cost'][$ship->shipment_id]+$road->bridge_cost*$check_sub:$road->bridge_cost*$check_sub;

                $road_data['police_cost'][$ship->shipment_id] = isset($road_data['police_cost'][$ship->shipment_id])?$road_data['police_cost'][$ship->shipment_id]+($road->police_cost)*$check_sub:($road->police_cost)*$check_sub;

                $road_data['tire_cost'][$ship->shipment_id] = isset($road_data['tire_cost'][$ship->shipment_id])?$road_data['tire_cost'][$ship->shipment_id]+($road->tire_cost)*$check_sub:($road->tire_cost)*$check_sub;

                if($road->road_oil_ton > 0){
                    $road_data['oil_cost'][$ship->shipment_id] = isset($road_data['oil_cost'][$ship->shipment_id])?$road_data['oil_cost'][$ship->shipment_id]+($road->road_oil_ton*round($ship->oil_cost))*$check_sub:($road->road_oil_ton*round($ship->oil_cost))*$check_sub;

                    $road_data['road_oil'][$ship->shipment_id] = isset($road_data['road_oil'][$ship->shipment_id])?$road_data['road_oil'][$ship->shipment_id]+($road->road_oil_ton)*$check_sub:($road->road_oil_ton)*$check_sub;
                }
                else{
                    $road_data['oil_cost'][$ship->shipment_id] = isset($road_data['oil_cost'][$ship->shipment_id])?$road_data['oil_cost'][$ship->shipment_id]+($road->road_oil*round($ship->oil_cost))*$check_sub:($road->road_oil*round($ship->oil_cost))*$check_sub;

                    $road_data['road_oil'][$ship->shipment_id] = isset($road_data['road_oil'][$ship->shipment_id])?$road_data['road_oil'][$ship->shipment_id]+($road->road_oil)*$check_sub:($road->road_oil)*$check_sub;
                }
                

                $road_data['road_time'][$ship->shipment_id] = isset($road_data['road_time'][$ship->shipment_id])?$road_data['road_time'][$ship->shipment_id]+($road->road_time)*$check_sub:($road->road_time)*$check_sub;

                $road_data['road_km'][$ship->shipment_id] = isset($road_data['road_km'][$ship->shipment_id])?$road_data['road_km'][$ship->shipment_id]+$road->road_km*$check_sub:$road->road_km*$check_sub;



                $chek_rong = ($road->way == 0)?1:0;



            }

            $cds = $shipment_cost_model->getAllShipment(array('where'=>'shipment = '.$ship->shipment_id),array('table'=>'cost_list','where'=>'cost_list = cost_list_id AND cost_list_type = 6'));
            foreach ($cds as $cd) {
                $road_data['bridge_cost'][$ship->shipment_id] = isset($road_data['bridge_cost'][$ship->shipment_id])?$road_data['bridge_cost'][$ship->shipment_id]+$cd->cost:$cd->cost;
            }

            $cas = $shipment_cost_model->getAllShipment(array('where'=>'shipment = '.$ship->shipment_id),array('table'=>'cost_list','where'=>'cost_list = cost_list_id AND cost_list_type = 10'));
            foreach ($cas as $ca) {
                $road_data['police_cost'][$ship->shipment_id] = isset($road_data['police_cost'][$ship->shipment_id])?$road_data['police_cost'][$ship->shipment_id]+$ca->cost:$ca->cost;
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

                else{

                    if ($ship->shipment_ton > 0) {

                        $boiduong_cont += $warehouse->warehouse_add;

                    }

                }

                

                

            }

            $warehouse_data['boiduong_cn'][$ship->shipment_id] = ($boiduong_cont+$boiduong_tan)*$check_sub;

            $bds = $shipment_cost_model->getAllShipment(array('where'=>'shipment = '.$ship->shipment_id),array('table'=>'cost_list','where'=>'cost_list = cost_list_id AND cost_list_type = 11'));
            foreach ($bds as $bd) {
                $warehouse_data['boiduong_cn'][$ship->shipment_id] = isset($warehouse_data['boiduong_cn'][$ship->shipment_id])?$warehouse_data['boiduong_cn'][$ship->shipment_id]+$bd->cost:$bd->cost;
            }


            $customer_sub = "";
            $sts = explode(',', $ship->customer_type);
            foreach ($sts as $key) {
                $subs = $customer_sub_model->getCustomer($key);
                if ($subs) {
                    if ($customer_sub == "")
                        $customer_sub .= $subs->customer_sub_name;
                    else
                        $customer_sub .= ','.$subs->customer_sub_name;
                }
                
            }
            $customer_types[$ship->shipment_id] = $customer_sub;

            $export_sub = "";
            $sts = explode(',', $ship->export_stock);
            foreach ($sts as $key) {
                $subs = $export_stock_model->getStock($key);
                if ($subs) {
                    if ($export_sub == "")
                        $export_sub .= $subs->export_stock_code;
                    else
                        $export_sub .= ','.$subs->export_stock_code;
                }
                
            }
            $export_stocks[$ship->shipment_id] = $export_sub;

        }

        $this->view->data['customer_types'] = $customer_types;

        $this->view->data['export_stocks'] = $export_stocks;

        $this->view->data['loan_shipment_data'] = $loan_shipment_data;

        $this->view->data['warehouse'] = $warehouse_data;

        $this->view->data['road'] = $road_data;

        $this->view->show('trucking/index');

    }




}

?>