<?php



Class shipmentController Extends baseController {



    public function index() {



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) || json_decode($_SESSION['user_permission_action'])->shipment != "shipment") {

            $this->view->data['disable_control'] = 1;

        }



        $this->view->data['lib'] = $this->lib;



        $this->view->data['title'] = 'Điều xe';







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



        }



        else{



            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'vehicle_number ASC, shipment_date ASC, ';



            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'shipment_round ASC';



            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;



            $keyword = "";



            $limit = 50;





            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y'); //cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')).'-'.date('m-Y');





            $xe = 0;



            $vong = 0;



        }

        $id = $this->registry->router->param_id;

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $thangchon = (int)date('m',strtotime($batdau));

        $namchon = date('Y',strtotime($batdau));


        $contunit_model = $this->model->get('contunitModel');

        $loanunit_model = $this->model->get('loanunitModel');



        $this->view->data['cont_units'] = $contunit_model->getAllUnit();

        $this->view->data['loan_units'] = $loanunit_model->getAllUnit();



        $costlist_model = $this->model->get('costlistModel');

        $this->view->data['cost_lists'] = $costlist_model->getAllCost();





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







        //$giadauhientai = $this->craw();

        $giadauhientai = 0;

        $this->view->data['giadauhientai'] = $giadauhientai;







        $vehicle_model = $this->model->get('vehicleModel');



        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));







        $this->view->data['vehicles'] = $vehicles;







        $shipment_temp_model = $this->model->get('shipmenttempModel');



        $join = array('table'=>'customer, marketing, cont_unit','where'=>'marketing.marketing_id = shipment_temp.marketing AND customer.customer_id = marketing.customer AND cont_unit=cont_unit_id');

        $temp_data = array(
            'where'=>'shipment_temp_status=0',
        );

        if ($_SESSION['role_logined'] > 2) {
            $temp_data['where'] .= ' AND owner='.$_SESSION['userid_logined'];
        }

        $shipment_temps = $shipment_temp_model->getAllShipment($temp_data,$join);



        $this->view->data['shipment_temps'] = $shipment_temps;







        $join = array('table'=>'customer, vehicle, cont_unit, steersman','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND cont_unit=cont_unit_id AND steersman = steersman_id');







        $shipment_model = $this->model->get('shipmentModel');



        $sonews = $limit;



        $x = ($page-1) * $sonews;



        $pagination_stages = 2;







        $data = array(



            'where' => 'shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc),



            );


        if (isset($id) && $id > 0) {
            $data['where'] = 'shipment_id = '.$id;
        }


        if($xe != 0){



            $data['where'] = $data['where'].' AND vehicle = '.$xe;



        }



        if($vong != 0){



            $data['where'] = $data['where'].' AND shipment_round = '.$vong;



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


        $this->view->data['thangchon'] = $thangchon;

        $this->view->data['namchon'] = $namchon;




        $this->view->data['xe'] = $xe;



        $this->view->data['vong'] = $vong;











        $data = array(



            'order_by'=>$order_by,



            'order'=>$order,



            'limit'=>$x.','.$sonews,



            'where' => 'shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc),



            );


        if (isset($id) && $id > 0) {
            $data['where'] = 'shipment_id = '.$id;
        }


        if($xe != 0){



            $data['where'] = $data['where'].' AND vehicle = '.$xe;



        }



        if($vong != 0){



            $data['where'] = $data['where'].' AND shipment_round = '.$vong;



        }







        /*if ($_SESSION['role_logined'] == 3) {



            $data['where'] = $data['where'].' AND shipment_create_user = '.$_SESSION['userid_logined'];



            



        }*/







        if ($keyword != '') {



            $ngay = (strtotime(str_replace("/", "-", $keyword))!="")?(' OR shipment_date LIKE "%'.strtotime(str_replace("/", "-", $keyword)).'%"'):"";



            $search = '(

                    bill_number LIKE "%'.$keyword.'%" 

                    OR shipment_id IN (SELECT shipment_link FROM shipment WHERE 
                        bill_number LIKE "%'.$keyword.'%" 

                     ) 
                    
                    OR steersman_name LIKE "%'.$keyword.'%" 


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



        $customer_types = array();



        $export_stocks = array();


       


        $v = array();





        foreach ($datas as $ship) {

            



            $month = intval(date('m',$ship->shipment_date));



            $year = date('Y',$ship->shipment_date);









           $v[$ship->vehicle.$ship->shipment_round.$month.$year] = isset($v[$ship->vehicle.$ship->shipment_round.$month.$year])?($v[$ship->vehicle.$ship->shipment_round.$month.$year] + 1) : (0+1) ;







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



        $this->view->data['warehouse'] = $warehouse_data;



        $this->view->data['road'] = $road_data;



        $this->view->data['arr'] = $v;





        $this->view->show('shipment/index');



    }
    public function index2() {



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) || json_decode($_SESSION['user_permission_action'])->shipment != "shipment") {

            $this->view->data['disable_control'] = 1;

        }



        $this->view->data['lib'] = $this->lib;



        $this->view->data['title'] = 'Điều xe';







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



        }



        else{



            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'vehicle_number ASC, shipment_date ASC, ';



            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'shipment_round ASC';



            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;



            $keyword = "";



            $limit = 50;





            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y'); //cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')).'-'.date('m-Y');





            $xe = 0;



            $vong = 0;



        }

        $id = $this->registry->router->param_id;

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $thangchon = (int)date('m',strtotime($batdau));

        $namchon = date('Y',strtotime($batdau));


        $contunit_model = $this->model->get('contunitModel');

        $loanunit_model = $this->model->get('loanunitModel');



        $this->view->data['cont_units'] = $contunit_model->getAllUnit();

        $this->view->data['loan_units'] = $loanunit_model->getAllUnit();



        $costlist_model = $this->model->get('costlistModel');

        $this->view->data['cost_lists'] = $costlist_model->getAllCost();





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







        $giadauhientai = $this->craw();



        $this->view->data['giadauhientai'] = $giadauhientai;







        $vehicle_model = $this->model->get('vehicleModel');



        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));







        $this->view->data['vehicles'] = $vehicles;







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


        if (isset($id) && $id > 0) {
            $data['where'] = 'shipment_id = '.$id;
        }


        if($xe != 0){



            $data['where'] = $data['where'].' AND vehicle = '.$xe;



        }



        if($vong != 0){



            $data['where'] = $data['where'].' AND shipment_round = '.$vong;



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


        $this->view->data['thangchon'] = $thangchon;

        $this->view->data['namchon'] = $namchon;




        $this->view->data['xe'] = $xe;



        $this->view->data['vong'] = $vong;











        $data = array(



            'order_by'=>$order_by,



            'order'=>$order,



            'limit'=>$x.','.$sonews,



            'where' => 'shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc),



            );


        if (isset($id) && $id > 0) {
            $data['where'] = 'shipment_id = '.$id;
        }


        if($xe != 0){



            $data['where'] = $data['where'].' AND vehicle = '.$xe;



        }



        if($vong != 0){



            $data['where'] = $data['where'].' AND shipment_round = '.$vong;



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

                    OR customer_type IN (SELECT customer_sub_id FROM customer_sub WHERE customer_sub_name LIKE "%'.$keyword.'%" ) 

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



        $customer_types = array();



        $export_stocks = array();


       


        $v = array();





        foreach ($datas as $ship) {

            



            $month = intval(date('m',$ship->shipment_date));



            $year = date('Y',$ship->shipment_date);









           $v[$ship->vehicle.$ship->shipment_round.$month.$year] = isset($v[$ship->vehicle.$ship->shipment_round.$month.$year])?($v[$ship->vehicle.$ship->shipment_round.$month.$year] + 1) : (0+1) ;







           $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));



            



           $road_data['oil_add'][$ship->shipment_id] = ($ship->oil_add_dc == 5)?$ship->oil_add:0;



           $road_data['oil_add2'][$ship->shipment_id] = ($ship->oil_add_dc2 == 5)?$ship->oil_add2:0;







           $check_sub = 1;



           /*if ($ship->shipment_sub==1) {



               $check_sub = 0;



           }*/







            $chek_rong = 0;



            



            foreach ($roads as $road) {



                $road_data['bridge_cost'][$ship->shipment_id] = isset($road_data['bridge_cost'][$ship->shipment_id])?$road_data['bridge_cost'][$ship->shipment_id]+$road->bridge_cost*$check_sub:$road->bridge_cost*$check_sub;



                $road_data['police_cost'][$ship->shipment_id] = isset($road_data['police_cost'][$ship->shipment_id])?$road_data['police_cost'][$ship->shipment_id]+($road->police_cost)*$check_sub:($road->police_cost)*$check_sub;



                $road_data['tire_cost'][$ship->shipment_id] = isset($road_data['tire_cost'][$ship->shipment_id])?$road_data['tire_cost'][$ship->shipment_id]+($road->tire_cost)*$check_sub:($road->tire_cost)*$check_sub;

                if($road->road_oil_ton > 0){
                    $road_data['oil_cost'][$ship->shipment_id] = isset($road_data['oil_cost'][$ship->shipment_id])?$road_data['oil_cost'][$ship->shipment_id]+($road->road_oil_ton*round($ship->oil_cost*1.1))*$check_sub:($road->road_oil_ton*round($ship->oil_cost*1.1))*$check_sub;

                    $road_data['road_oil'][$ship->shipment_id] = isset($road_data['road_oil'][$ship->shipment_id])?$road_data['road_oil'][$ship->shipment_id]+($road->road_oil_ton)*$check_sub:($road->road_oil_ton)*$check_sub;
                }
                else{
                    $road_data['oil_cost'][$ship->shipment_id] = isset($road_data['oil_cost'][$ship->shipment_id])?$road_data['oil_cost'][$ship->shipment_id]+($road->road_oil*round($ship->oil_cost*1.1))*$check_sub:($road->road_oil*round($ship->oil_cost*1.1))*$check_sub;

                    $road_data['road_oil'][$ship->shipment_id] = isset($road_data['road_oil'][$ship->shipment_id])?$road_data['road_oil'][$ship->shipment_id]+($road->road_oil)*$check_sub:($road->road_oil)*$check_sub;
                }


                $road_data['road_time'][$ship->shipment_id] = isset($road_data['road_time'][$ship->shipment_id])?$road_data['road_time'][$ship->shipment_id]+($road->road_time)*$check_sub:($road->road_time)*$check_sub;



                $road_data['road_km'][$ship->shipment_id] = isset($road_data['road_km'][$ship->shipment_id])?$road_data['road_km'][$ship->shipment_id]+$road->road_km:$road->road_km;







                $chek_rong = ($road->way == 0)?1:0;







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



        $this->view->data['warehouse'] = $warehouse_data;



        $this->view->data['road'] = $road_data;



        $this->view->data['arr'] = $v;





        $this->view->show('shipment/index2');



    }



    public function loan(){

        $this->view->disableLayout();



        $this->view->data['lib'] = $this->lib;



        $loanunit_model = $this->model->get('loanunitModel');



        $this->view->data['loan_units'] = $loanunit_model->getAllUnit();



        $id = $this->registry->router->param_id;



        $loan_shipment_model = $this->model->get('loanshipmentModel');



        $join = array('table'=>'loan_unit','where'=>'loan_unit = loan_unit_id');



        $data = array(

            'where' => '(shipment IS NULL OR shipment = 0) AND loan_shipment_user = '.$_SESSION['userid_logined'],

        );



        if ($id > 0) {

            $data = array(

                'where' => 'shipment = '.$id,

            );

        }

        



        $loan_shipments = $loan_shipment_model->getAllUnit($data,$join);



        $this->view->data['loan_shipments'] = $loan_shipments;



        $this->view->data['shipment'] = $id;



        $this->view->show('shipment/loan');        

    }



    public function addloan(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $loan_shipment_model = $this->model->get('loanshipmentModel');

            $data = array(

                        

                        'loan_cost' => trim(str_replace(',','',$_POST['loan_cost'])),



                        'loan_unit' => trim($_POST['loan_unit']),



                        'loan_comment' => trim($_POST['loan_comment']),



                        'shipment' => trim($_POST['shipment']),



                        'loan_shipment_user' => $_SESSION['userid_logined'],

                        );



            

            if ($_POST['yes'] != "") {



                

                    $loan_shipment_model->updateUnit($data,array('loan_shipment_id' => $_POST['yes']));



                    /*Log*/

                    /**/

                    $result = array(

                        'ms' => 'Cập nhật thành công',

                        'id' => $_POST['yes'],

                    );



                    echo json_encode($result);



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|loan_shipment|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                

                    

                

                

            }

            else{



                

                    $loan_shipment_model->createUnit($data);



                    /*Log*/

                    /**/



                    $result = array(

                        'ms' => 'Thêm thành công',

                        'id' => $loan_shipment_model->getLastUnit()->loan_shipment_id,

                    );



                    echo json_encode($result);



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$loan_shipment_model->getLastUnit()->loan_shipment_id."|loan_shipment|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                

                    

                

                

            }

                    

        }

    }

    public function deleteloan(){



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $loan = $this->model->get('loanshipmentModel');



            if (isset($_POST['xoa'])) {



                $data = explode(',', $_POST['xoa']);



                foreach ($data as $data) {



                    $loan->deleteUnit($data);







                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 



                        $filename = "action_logs.txt";



                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|loan_shipment|"."\n"."\r\n";



                        



                        $fh = fopen($filename, "a") or die("Could not open log file.");



                        fwrite($fh, $text) or die("Could not write file!");



                        fclose($fh);



                }



                return true;



            }



            else{







                date_default_timezone_set("Asia/Ho_Chi_Minh"); 



                        $filename = "action_logs.txt";



                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|loan_shipment|"."\n"."\r\n";



                        



                        $fh = fopen($filename, "a") or die("Could not open log file.");



                        fwrite($fh, $text) or die("Could not write file!");



                        fclose($fh);







                return $loan->deleteUnit($_POST['data']);



            }



            



        }



    }





    public function lock(){



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (isset($_POST['data'])) {











            $shipment = $this->model->get('shipmentModel');



            $shipment_data = $shipment->getShipment($_POST['data']);







            $data = array(



                        



                        'shipment_complete' => trim($_POST['value']),



                        'not_del' => trim($_POST['value']),



                        );



          



            $shipment->updateShipment($data,array('shipment_id' => $_POST['data']));











            date_default_timezone_set("Asia/Ho_Chi_Minh"); 



                        $filename = "action_logs.txt";



                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."lock"."|".$_POST['data']."|shipment|".$_POST['value']."\n"."\r\n";



                        



                        $fh = fopen($filename, "a") or die("Could not open log file.");



                        fwrite($fh, $text) or die("Could not write file!");



                        fclose($fh);







            return true;



                    



        }



    }







    public function lock_oil(){



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (isset($_POST['data'])) {











            $shipment = $this->model->get('shipmentModel');



            $shipment_data = $shipment->getShipment($_POST['data']);







            $data = array(



                        



                        'approve_oil' => trim($_POST['value']),



                        );



          



            $shipment->updateShipment($data,array('shipment_id' => $_POST['data']));











            date_default_timezone_set("Asia/Ho_Chi_Minh"); 



                        $filename = "action_logs.txt";



                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."lock_oil"."|".$_POST['data']."|shipment|".$_POST['value']."\n"."\r\n";



                        



                        $fh = fopen($filename, "a") or die("Could not open log file.");



                        fwrite($fh, $text) or die("Could not write file!");



                        fclose($fh);







            return true;



                    



        }



    }




    public function getOilcost(){
        $this->view->disableLayout();
        echo $this->craw();
    }


    public function getshipmenttemp(){



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (isset($_POST['data']) && $_POST['data']>0) {



            $join = array('table'=>'customer, marketing, cont_unit','where'=>'marketing.marketing_id = shipment_temp.marketing AND customer.customer_id = marketing.customer AND cont_unit=cont_unit_id');





            $shipment = $this->model->get('shipmenttempModel');



            $place_model = $this->model->get('placeModel');


            $customer_sub_model = $this->model->get('customersubModel');




            $shipment_datas = $shipment->getAllShipment(array('where'=>'shipment_temp_id='.$_POST['data']),$join);



            foreach ($shipment_datas as $shipment_data) {



                $places = $place_model->getAllPlace(array('where'=>'(place_id = '.$shipment_data->marketing_from.' OR place_id = '.$shipment_data->marketing_to.') '));



        



                foreach ($places as $place) {



                        $place_data['place_id'][$place->place_id] = $place->place_id;



                        $place_data['place_name'][$place->place_id] = $place->place_name; 



                }


                $customer_sub = "";

                $sts = explode(',', $shipment_data->customer_type);

                foreach ($sts as $key) {

                    $subs = $customer_sub_model->getCustomer($key);

                    if ($subs) {

                        if ($customer_sub == "")

                            $customer_sub .= $subs->customer_sub_name;

                        else

                            $customer_sub .= ','.$subs->customer_sub_name;

                    }

                    

                }




                $data = array(   



                    'shipment_temp' => $shipment_data->shipment_temp_id,



                    'shipment_from' => $shipment_data->marketing_from,



                    'shipment_to' => $shipment_data->marketing_to,



                    'shipment_from_name' => $place_data['place_name'][$shipment_data->marketing_from],



                    'shipment_to_name' => $place_data['place_name'][$shipment_data->marketing_to],



                    'customer' => $shipment_data->customer,



                    'customer_name' => $shipment_data->customer_name,



                    'shipment_ton' => $shipment_data->shipment_temp_ton-$shipment_data->shipment_ton_use,



                    'shipment_charge' => $shipment_data->marketing_charge,



                    'commission' => $shipment_data->shipment_temp_commission,



                    'commission_number' => $shipment_data->shipment_temp_commission_number,



                    'cont_unit' => $shipment_data->shipment_temp_cont_unit,


                    'customer_sub' => $customer_sub,



                    );



            }



            



            echo json_encode($data);







            return true;



                    



        }

        else{

            $data = array(   



                'shipment_temp' => null,



                'shipment_from' => null,



                'shipment_to' => null,



                'shipment_from_name' => null,



                'shipment_to_name' => null,



                'customer' => null,



                'customer_name' => null,



                'shipment_ton' => null,



                'shipment_charge' => null,



                'commission' => null,



                'commission_number' => null,



                'cont_unit' => null,



                );



            echo json_encode($data);

        }



    }







    public function complete(){



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (isset($_POST['data'])) {











            $shipment = $this->model->get('shipmentModel');



            $shipment_data = $shipment->getShipment($_POST['data']);







            $data = array(



                        



                        'shipment_complete' => 1,



                        'not_del' => 1,



                        );



          



            $shipment->updateShipment($data,array('shipment_id' => $_POST['data']));











            date_default_timezone_set("Asia/Ho_Chi_Minh"); 



                        $filename = "action_logs.txt";



                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."complete"."|".$_POST['data']."|shipment|"."\n"."\r\n";



                        



                        $fh = fopen($filename, "a") or die("Could not open log file.");



                        fwrite($fh, $text) or die("Could not write file!");



                        fclose($fh);







            return true;



                    



        }



    }







    







    public function add(){



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) || json_decode($_SESSION['user_permission_action'])->shipment != "shipment") {



            return $this->view->redirect('user/login');



        }



        if (isset($_POST['yes'])) {



            $debit = $this->model->get('debitModel');

            $vat = $this->model->get('vatModel');



            $shipment = $this->model->get('shipmentModel');



            $shipment_cost_model = $this->model->get('shipmentcostModel');

            $cost_list_model = $this->model->get('costlistModel');



            /**************/



            $shipment_cost_list = $_POST['shipment_cost'];



            /**************/



            //$ad = mktime(date("H"), date("i"), date("s"), date("m"), date("d")+1, date("Y")); 



            $tomorrow = strtotime(trim($_POST['shipment_date']));



            $data = array(



                        



                        'shipment_from' => trim($_POST['shipment_from']),



                        'shipment_to' => trim($_POST['shipment_to']),



                        'vehicle' => trim($_POST['vehicle']),



                        'romooc' => trim($_POST['romooc']),


                        'steersman' => trim($_POST['steersman']),



                        'customer' => trim($_POST['customer']),



                        'shipment_ton' => trim($_POST['shipment_ton']),



                        'cont_unit' => trim($_POST['cont_unit']),



                        'shipment_charge' => trim(str_replace(',','',$_POST['shipment_charge'])),



                        'oil_add' => trim(str_replace(',','',$_POST['oil_add'])),



                        'oil_add2' => trim(str_replace(',','',$_POST['oil_add2'])),



                        'cost_add' => trim(str_replace(',','',$_POST['cost_add'])),



                        'oil_cost' => round((trim(str_replace(',','',$_POST['oil_cost'])))/1.1),



                        'shipment_date' => $tomorrow,



                        'shipment_round' => trim($_POST['shipment_round']),



                        'oil_add_dc' => trim($_POST['oil_add_dc']),



                        'oil_add_dc2' => trim($_POST['oil_add_dc2']),



                        'cost_add_comment' => trim($_POST['cost_add_comment']),



                        'shipment_loan' => trim(str_replace(',','',$_POST['shipment_loan'])),



                        'sub_driver' => trim($_POST['sub_driver']),



                        'sub_driver_name' => trim($_POST['sub_driver_name']),



                        'sub_driver2' => trim($_POST['sub_driver2']),



                        'loan_content' => trim($_POST['loan_content']),



                        'oil_excess' => trim(str_replace(',','',$_POST['oil_excess'])),



                        'oil_excess_comment' => trim($_POST['oil_excess_comment']),



                        'oil_excess_dc' => trim($_POST['oil_excess_dc']),



                        'advance' => trim(str_replace(',','',$_POST['advance'])),



                        'commission' => trim(str_replace(',','',$_POST['commission'])),



                        'advance_comment' => trim($_POST['advance_comment']),



                        'commission_number' => trim($_POST['commission_number']),



                        'oil_residual' => trim(str_replace(',','',$_POST['oil_residual'])),



                        'oil_residual_comment' => trim($_POST['oil_residual_comment']),



                        'cost_vat' => trim(str_replace(',','',$_POST['cost_vat'])),



                        'cost_vat_comment' => trim($_POST['cost_vat_comment']),



                        'cost_excess' => trim(str_replace(',','',$_POST['cost_excess'])),



                        'cost_excess_comment' => trim($_POST['cost_excess_comment']),



                        'shipment_charge_excess' => trim(str_replace(',','',$_POST['shipment_charge_excess'])),



                        'shipment_charge_excess_comment' => trim($_POST['shipment_charge_excess_comment']),



                        'bridge_cost_add' => trim(str_replace(',','',$_POST['bridge_cost_add'])),



                        'shipment_salary' => trim(str_replace(',','',$_POST['shipment_salary'])),
                        'shipment_road_add' => trim(str_replace(',','',$_POST['shipment_road_add'])),

                        

                        'bill_number' => trim($_POST['bill_number']),

                        'bill_receive_date' => strtotime(trim($_POST['bill_receive_date'])),

                        'bill_delivery_date' => strtotime(trim($_POST['bill_delivery_date'])),

                        'bill_receive_ton' => trim($_POST['bill_receive_ton']),

                        'bill_receive_unit' => trim($_POST['bill_receive_unit']),

                        'bill_delivery_ton' => trim($_POST['bill_delivery_ton']),

                        'bill_delivery_unit' => trim($_POST['bill_delivery_unit']),

                        'bill_in' => strtotime(trim($_POST['bill_delivery_date']).' '.trim($_POST['bill_in'])),

                        'bill_out' => strtotime(trim($_POST['bill_delivery_date']).' '.trim($_POST['bill_out'])),

                        'shipment_oil' => trim($_POST['shipment_oil']),

                        'shipment_road_oil_add' => trim($_POST['shipment_road_oil_add']),

                        'shipment_road_oil' => trim($_POST['shipment_road_oil']),

                        'shipment_link' => trim($_POST['shipment_link']),

                        );

            $data['shipment_ton'] = $data['bill_delivery_ton'];
            $data['cont_unit'] = $data['bill_delivery_unit'];
            $data['shipment_ton_net'] = $data['bill_receive_ton'];
            $data['cont_unit_net'] = $data['bill_receive_unit'];
            $data['oil_add'] = $data['shipment_oil'];


            $export_stock_model = $this->model->get('exportstockModel');



            $contributor = "";

            if(trim($_POST['export_stock']) != ""){

                $support = explode(',', trim($_POST['export_stock']));



                if ($support) {

                    foreach ($support as $key) {

                        $name = $export_stock_model->getStockByWhere(array('export_stock_code'=>trim($key)));

                        if ($name) {

                            if ($contributor == "")

                                $contributor .= $name->export_stock_id;

                            else

                                $contributor .= ','.$name->export_stock_id;

                        }

                        

                    }

                }



            }

            $data['export_stock'] = $contributor;


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

                            $check = $customer_model->queryCustomer('SELECT customer_sub FROM customer WHERE customer_id = '.$data['customer'].' AND (customer_sub LIKE "'.$name->customer_sub_id.'" OR customer_sub LIKE "'.$name->customer_sub_id.',%" OR customer_sub LIKE "%,'.$name->customer_sub_id.',%" OR customer_sub LIKE "%,'.$name->customer_sub_id.'")');
                            if (!$check) {
                                $cus = $customer_model->getCustomer($data['customer']);
                                if ($cus->customer_sub == "") {

                                    $customer_model->updateCustomer(array('customer_sub'=>$name->customer_sub_id),array('customer_id'=>$data['customer']));

                                }

                                else{

                                    $customer_model->updateCustomer(array('customer_sub'=>($cus->customer_sub.','.$name->customer_sub_id)),array('customer_id'=>$data['customer']));

                                }
                            }

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



            $contributor = "";

            if(is_array($_POST['route'])){

                foreach ($_POST['route'] as $key) {

                    if ($contributor == "")

                        $contributor .= $key;

                    else

                        $contributor .= ','.$key;

                }

            }

            $data['route'] = $contributor;





            $road_model = $this->model->get('roadModel');



            $warehouse_model = $this->model->get('warehouseModel');







            $warehouse_datas = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$data['shipment_from'].' OR warehouse_code = '.$data['shipment_to'].') AND start_time <= '.$data['shipment_date'].' AND end_time >= '.$data['shipment_date']));



            //$road_datas = $road_model->getAllRoad(array('where'=>'road_from = '.$data['shipment_from'].' AND road_to = '.$data['shipment_to'].' AND start_time <= '.$data['shipment_date'].' AND end_time >= '.$data['shipment_date']));



            $road_datas = $road_model->queryRoad('SELECT * FROM road WHERE road_id IN ('.$data['route'].')');





            /*if (trim($_POST['shipment_ton_net']) != "") {



                    $data['shipment_ton'] = trim($_POST['shipment_ton_net']);



                    $data['cont_unit'] = trim($_POST['cont_unit_net']);



                    $data['shipment_update'] = 1;



                }



            $data['shipment_ton_net'] = trim($_POST['shipment_ton']);



            $data['cont_unit_net'] = trim($_POST['cont_unit']);*/







                $boiduong_cont = 0;



                $boiduong_tan = 0;



                $doanhthu = 0;



                $chiphi = 0;



                $loinhuan = 0;







                $chiphi_tam = 0;







                $giadau = round($data['oil_cost']*1.1); //gia dau hien tai







                if ($warehouse_datas) {





                    $trongluong = round($data['shipment_ton']/1000,2);







                    foreach ($warehouse_datas as $warehouse_data) {



                        if ($warehouse_data->warehouse_cont != 0) {



                            $boiduong_cont += $warehouse_data->warehouse_cont;



                        }



                        if ($warehouse_data->warehouse_ton != 0){



                            $boiduong_tan += $trongluong * $warehouse_data->warehouse_ton;



                        }



                    }



                }



                $boiduong = $boiduong_tan+$boiduong_cont;



                $thuong = 0;



                $chiphi += $boiduong;

                $chiphi_tam += $boiduong;

                $chiphididuong = 0;



                if ($road_datas) {



                    foreach ($road_datas as $road_data) {

                        if ($road_data->road_oil_ton > 0) {
                            $chiphi += $road_data->police_cost+$road_data->bridge_cost+$road_data->tire_cost+($road_data->road_oil_ton*$giadau)+$road_data->road_add;
                        }
                        else{
                            $chiphi += $road_data->police_cost+$road_data->bridge_cost+$road_data->tire_cost+($road_data->road_oil*$giadau)+$road_data->road_add;
                        }



                        //$thuong = ($data['shipment_ton']>29.7)?round(($data['shipment_ton']-29.7)+0.4)*$road_data->charge_add:0;



                        $thuong = ($data['shipment_ton']>29000)?round($data['shipment_ton']-29000)*$road_data->charge_add:0;



                        $chiphi_tam += $road_data->police_cost+$road_data->bridge_cost+$road_data->tire_cost+$road_data->road_add;


                        $chiphididuong += $road_data->road_add;

                    }



                }







                $chiphi = $chiphi+$data['bridge_cost_add']+$data['shipment_loan']+$data['advance']+($data['commission']*$data['commission_number'])+$data['cost_vat']-$data['cost_excess'];







                if ($data['oil_add_dc'] == 5) {



                    $chiphi_tam = $chiphi_tam+$data['bridge_cost_add']+$data['shipment_loan']+$data['advance']+($data['commission']*$data['commission_number'])+$data['cost_vat']+($data['oil_add']*$giadau);



                }



                else{



                    $chiphi_tam = $chiphi_tam+$data['bridge_cost_add']+$data['shipment_loan']+$data['advance']+($data['commission']*$data['commission_number'])+$data['cost_vat'];



                }







                if ($data['oil_add_dc2'] == 5) {



                    $chiphi_tam = $chiphi_tam+($data['oil_add2']*$giadau);



                }



                







                $doanhthu = $data['shipment_charge']*$data['shipment_ton'];



                $loinhuan = $doanhthu - $chiphi;







                $data['shipment_revenue'] = $doanhthu;



                $data['shipment_cost'] = $chiphi;



                $data['shipment_profit'] = $loinhuan;



                $data['shipment_bonus'] = $thuong;







                $data['shipment_cost_temp'] = $chiphi_tam;







            $result = array();







            if ($_POST['yes'] != "") {



                //$data['shipment_update_user'] = $_SESSION['userid_logined'];



                //$data['shipment_update_time'] = time();



                //var_dump($data);



                


                if ($shipment->checkShipment($_POST['yes'],trim($_POST['bill_number']))) {



                    $result['err'] = "Bảng này đã tồn tại";



                    $result['revenue'] = 0;



                    $result['cost'] = 0;



                    $result['profit'] = 0;



                    echo json_encode($result);



                    return false;



                }



                else{

                    $data_shipments = $shipment->getShipment($_POST['yes']);

                    $shipment_temp_id = $data_shipments->shipment_temp;



                    if($shipment_temp_id > 0){



                        $shipment_temp_model = $this->model->get('shipmenttempModel');



                        $shipment_temp_data = $shipment_temp_model->getShipment($shipment_temp_id);







                        $data_shipment_temp = array(



                            'shipment_ton_use' => $shipment_temp_data->shipment_ton_use-$data_shipments->shipment_ton_net+$data['shipment_ton_net'],



                        );







                        $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$shipment_temp_id));







                        $shipment_temp_data = $shipment_temp_model->getShipment($shipment_temp_id);







                        if ( ($shipment_temp_data->shipment_temp_ton-$shipment_temp_data->shipment_ton_use) <= 0) {



                            $shipment_temp_model->updateShipment(array('shipment_temp_status'=>1), array('shipment_temp_id'=>$shipment_temp_id));



                        }



                    }




                        $cptruoc = $shipment->getShipment($_POST['yes'])->shipment_cost_temp;



                        $sodu = $chiphi_tam-$cptruoc;



                    if ($sodu != 0) {







                        $thang = substr($_POST['shipment_date'], 3, 2);



                        $nam = substr($_POST['shipment_date'], 6, 4);







                        $vong = $data['shipment_round']+1;







                        if(substr($_POST['shipment_date'], 0, 2) >= 27 && substr($_POST['shipment_date'], 0, 2) < 30){



                            $vong = 1;



                            if ($thang == 12) {



                                $thang = 1;



                                $nam = $nam+1;



                            }



                            else{



                                $thang = $thang+1;



                            }                            



                        }



                        if(substr($_POST['shipment_date'], 0, 2) >= 30){







                            if ($thang == 12) {



                                $thang = 1;



                                $nam = $nam+1;



                            }



                            else{



                                $thang = $thang+1;



                            }  



                        }







                        if ($shipment->getShipment($_POST['yes'])->shipment_pay != 1) {



                            $sodu = 0;



                        }



                        











                        $excess_model = $this->model->get('excessModel');



                        if ($excess_model->checkExcess($_POST['yes'])) {



                            $ex_data = array(



                                'vehicle' => $data['vehicle'],



                                'round' => $vong,



                                'month' => $thang,



                                'year' => $nam,



                                'excess_cost' => $sodu,



                                'bonus' => $thuong,



                                'used' => 0,



                                'shipment' => $_POST['yes'],



                                );



                            $excess_model->updateExcess($ex_data,array('shipment' => $_POST['yes']));



                        }



                        else{



                            $ex_data = array(



                                'vehicle' => $data['vehicle'],



                                'round' => $vong,



                                'month' => $thang,



                                'year' => $nam,



                                'excess_cost' => $sodu,



                                'bonus' => $thuong,



                                'used' => 0,



                                'shipment' => $_POST['yes'],



                                );



                            $excess_model->createExcess($ex_data);



                        }







                        date_default_timezone_set("Asia/Ho_Chi_Minh"); 



                        $filename = "action_logs.txt";



                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$excess_model->getLastExcess()->excess_id."|excess|".implode("-",$ex_data)."\n"."\r\n";



                        



                        $fh = fopen($filename, "a") or die("Could not open log file.");



                        fwrite($fh, $text) or die("Could not write file!");



                        fclose($fh);







                        //$data['shipment_excess'] = $sodu;



                    }



                    



                    if ($data['cost_add'] > 0 && $data['cost_add'] > $data_shipments->cost_add) {



                        $data['approve'] = 0;



                    }







                    



                    $id_shipment = $_POST['yes'];

                    if ($data_shipments->shipment_road_add > 0 && $data['shipment_road_add'] > 0) {
                        $data_debit = array(

                            'debit_date'=>$data['shipment_date'],

                            'steersman'=>$data['steersman'],

                            'money'=>$data['shipment_road_add'],

                            'money_vat'=>0,

                            'comment'=>'Tiền đi đường '.$data['bill_number'],

                            'check_debit'=>2,

                            'shipment'=>$id_shipment,

                        );

                        $debit->updateDebit($data_debit,array('shipment'=>$id_shipment));
                    }
                    else if (($data_shipments->shipment_road_add == 0 || $data_shipments->shipment_road_add == "") && $data['shipment_road_add'] > 0) {
                        $data_debit = array(

                            'debit_date'=>$data['shipment_date'],

                            'steersman'=>$data['steersman'],

                            'money'=>$data['shipment_road_add'],

                            'money_vat'=>0,

                            'comment'=>'Tiền đi đường '.$data['bill_number'],

                            'check_debit'=>2,

                            'shipment'=>$id_shipment,

                        );

                        $debit->createDebit($data_debit);
                    }
                    else if ($data_shipments->shipment_road_add > 0 && ($data['shipment_road_add'] == 0 || $data['shipment_road_add'] == "")) {
                        $debit->queryDebit("DELETE FROM debit WHERE shipment = ".$id_shipment);
                    }

                    /*$data_debit = array(

                        'debit_date'=>$data['shipment_date'],

                        'customer'=>$data['customer'],

                        'money'=>$data['shipment_revenue']+$data['shipment_charge_excess'],

                        'money_vat'=>0,

                        'comment'=>'Vận chuyển',

                        'check_debit'=>1,

                        'shipment'=>$id_shipment,

                    );

                    $debit->updateDebit($data_debit,array('shipment'=>$id_shipment));*/





                    foreach ($shipment_cost_list as $v) {

                        $cost_type = $cost_list_model->getCost($v['cost_list']);

                        $data_cost = array(



                            'shipment' => $id_shipment,



                            'cost' => trim(str_replace(',','',$v['cost'])),



                            'cost_list' => $v['cost_list'],



                            'check_vat' => $v['check_vat'],



                            'comment' => trim($v['comment']),



                            'receiver' => isset($v['receiver'])?$v['receiver']:null,



                            'cost_document' => trim($v['cost_document']),



                            'cost_document_date' => strtotime(trim($v['cost_document_date'])),



                        );



                        if ($data_cost['receiver'] > 0 && $cost_type->cost_list_type != 8) {

                            $chiphi += trim(str_replace(',','',$v['cost']));

                            $loinhuan -= trim(str_replace(',','',$v['cost']));

                        }



                        if ($v['shipment_cost_id'] == "") {



                            if ($data_cost['receiver'] > 0) {

                                $shipment_cost_model->createShipment($data_cost);

                                $id_shipment_cost = $shipment_cost_model->getLastShipment()->shipment_cost_id;

                                

                                if ($data_cost['check_vat'] == 1) {

                                    $data_debit = array(

                                        'debit_date'=>$data['shipment_date'],

                                        'customer'=>$data_cost['receiver'],

                                        'money'=>round($data_cost['cost']/1.1),

                                        'money_vat_price'=>$data_cost['cost']-round($data_cost['cost']/1.1),

                                        'money_vat'=>$data_cost['check_vat'],

                                        'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                        'check_debit'=>2,

                                        'shipment_cost'=>$id_shipment_cost,

                                    );

                                    $debit->createDebit($data_debit);

                                    if ($cost_type->cost_list_type != 8) {

                                        $data_vat = array(

                                            'in_out'=>1,

                                            'vat_number'=>$data_cost['cost_document'],

                                            'vat_date'=>$data_cost['cost_document_date'],

                                            'shipment_cost'=>$id_shipment_cost,

                                        );

                                        $vat->createVAT($data_vat);
                                    }



                                }

                                else{

                                    $data_debit = array(

                                        'debit_date'=>$data['shipment_date'],

                                        'customer'=>$data_cost['receiver'],

                                        'money'=>$data_cost['cost'],

                                        'money_vat'=>$data_cost['check_vat'],

                                        'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                        'check_debit'=>2,

                                        'shipment_cost'=>$id_shipment_cost,

                                    );

                                    $debit->createDebit($data_debit);

                                    

                                }

                                if ($cost_type->cost_list_type == 8) {
                                    $data_debit = array(

                                        'debit_date'=>$data['shipment_date'],

                                        'customer'=>$data['customer'],

                                        'money'=>$data_cost['cost'],

                                        'money_vat'=>0,

                                        'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                        'check_debit'=>1,

                                        'shipment_cost'=>$id_shipment_cost,

                                        'check_loan'=>1,

                                    );

                                    $debit->createDebit($data_debit);
                                }


                                $vat_sum = round($data_cost['cost']/1.1);
                                $vat_price = $data_cost['cost']-round($data_cost['cost']/1.1);

                                $vat->updateVAT(array('vat_sum'=>$vat_sum,'vat_price'=>$vat_price),array('shipment_cost' => $id_shipment_cost));


                            }


                        }



                        else if ($v['shipment_cost_id'] > 0) {

                            $check = $shipment_cost_model->getShipment($v['shipment_cost_id']);

                            $shipment_cost_model->updateShipment($data_cost,array('shipment_cost_id'=>$v['shipment_cost_id']));


                            $check_cost_type = $cost_list_model->getCost($check->cost_list);
                            

                            if ($data_cost['check_vat'] == 1) {

                                $data_debit = array(

                                    'debit_date'=>$data['shipment_date'],

                                    'customer'=>$data_cost['receiver'],

                                    'money'=>round($data_cost['cost']/1.1),

                                    'money_vat_price'=>$data_cost['cost']-round($data_cost['cost']/1.1),

                                    'money_vat'=>$data_cost['check_vat'],

                                    'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                    'check_debit'=>2,

                                    'shipment_cost'=>$v['shipment_cost_id'],

                                );

                                $debit->updateDebit($data_debit,array('shipment_cost'=>$v['shipment_cost_id'],'check_loan'=>2));

                            }

                            else{

                                $data_debit = array(

                                    'debit_date'=>$data['shipment_date'],

                                    'customer'=>$data_cost['receiver'],

                                    'money'=>$data_cost['cost'],

                                    'money_vat_price'=>0,

                                    'money_vat'=>$data_cost['check_vat'],

                                    'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                    'check_debit'=>2,

                                    'shipment_cost'=>$v['shipment_cost_id'],

                                );

                                $debit->updateDebit($data_debit,array('shipment_cost'=>$v['shipment_cost_id'],'check_loan'=>2));

                            }



                            if ($check->check_vat == 1 && $data_cost['check_vat'] == 1) {

                                 $data_vat = array(

                                    'in_out'=>1,

                                    'vat_number'=>$data_cost['cost_document'],

                                    'vat_date'=>$data_cost['cost_document_date'],

                                    'shipment_cost'=>$v['shipment_cost_id'],

                                );

                                $vat->updateVAT($data_vat,array('shipment_cost'=>$v['shipment_cost_id']));

                            }

                            else if ($check->check_vat == 1 && $data_cost['check_vat'] != 1) {

                                $vat->queryVAT('DELETE FROM vat WHERE shipment_cost = '.$v['shipment_cost_id']);

                            }

                            else if ($check->check_vat != 1 && $data_cost['check_vat'] == 1) {

                                if ($cost_type->cost_list_type != 8) {
                                     $data_vat = array(

                                        'in_out'=>1,

                                        'vat_number'=>$data_cost['cost_document'],

                                        'vat_date'=>$data_cost['cost_document_date'],

                                        'shipment_cost'=>$v['shipment_cost_id'],

                                    );

                                    $vat->createVAT($data_vat);
                                }

                            }

                            $vat_sum = round($data_cost['cost']/1.1);
                            $vat_price = $data_cost['cost']-round($data_cost['cost']/1.1);

                            $vat->updateVAT(array('vat_sum'=>$vat_sum,'vat_price'=>$vat_price),array('shipment_cost' => $v['shipment_cost_id']));


                            if ($check_cost_type->cost_list_type == 8 && $cost_type->cost_list_type == 8) {
                                $data_debit = array(

                                    'debit_date'=>$data['shipment_date'],

                                    'customer'=>$data['customer'],

                                    'money'=>$data_cost['cost'],

                                    'money_vat'=>0,

                                    'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                    'check_debit'=>1,

                                    'shipment_cost'=>$v['shipment_cost_id'],

                                    'check_loan'=>1,

                                );

                                $debit->updateDebit($data_debit,array('shipment_cost'=>$v['shipment_cost_id'],'check_loan'=>1));
                            }
                            else if ($check_cost_type->cost_list_type == 8 && $cost_type->cost_list_type != 8) {
                                $debit->queryDebit('DELETE FROM debit WHERE check_loan = 1 AND shipment_cost = '.$v['shipment_cost_id']);
                            }
                            else if ($check_cost_type->cost_list_type != 8 && $cost_type->cost_list_type == 8) {
                                $data_debit = array(

                                    'debit_date'=>$data['shipment_date'],

                                    'customer'=>$data['customer'],

                                    'money'=>$data_cost['cost'],

                                    'money_vat'=>0,

                                    'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                    'check_debit'=>1,

                                    'shipment_cost'=>$v['shipment_cost_id'],

                                    'check_loan'=>1,

                                );

                                $debit->createDebit($data_debit);
                            }

                        }



                    }



                    $data['shipment_cost'] = $chiphi;

                    $result['shipment_profit'] = $loinhuan;



                    $shipment->updateShipment($data,array('shipment_id' => $_POST['yes']));



                    $result['err'] =  "Cập nhật thành công";



                    $result['revenue'] = $doanhthu;



                    $result['cost'] = $chiphi;



                    $result['profit'] = $loinhuan;



                    echo json_encode($result);





                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 



                        $filename = "action_logs.txt";



                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|shipment|".implode("-",$data)."\n"."\r\n";



                        



                        $fh = fopen($filename, "a") or die("Could not open log file.");



                        fwrite($fh, $text) or die("Could not write file!");



                        fclose($fh);



                    }



            }



            else{



                



                



                //$data['staff'] = $_POST['staff'];



                //var_dump($data);



                /*if ($shipment->checkUpdate(trim($_POST['vehicle']),trim($_POST['shipment_round']),$tomorrow)) {



                    $result['err'] = "Vui lòng cập nhật trọng tải vòng trước";



                    $result['revenue'] = 0;



                    $result['cost'] = 0;



                    $result['profit'] = 0;



                    echo json_encode($result);



                    return false;



                }*/



                if ($shipment->checkComplete(trim($_POST['vehicle']),trim($_POST['shipment_round']),$tomorrow)) {



                    $result['err'] = "Vui lòng hoàn tất vòng trước";



                    $result['revenue'] = 0;



                    $result['cost'] = 0;



                    $result['profit'] = 0;



                    echo json_encode($result);



                    return false;



                }







                if ($shipment->checkShipment(0,trim($_POST['bill_number']))) {



                    $result['err'] = "Bảng này đã tồn tại";



                    $result['revenue'] = 0;



                    $result['cost'] = 0;



                    $result['profit'] = 0;



                    echo json_encode($result);



                    return false;



                }



                else{


                    $data['shipment_create_user'] = $_SESSION['userid_logined'];



                    $data['shipment_temp'] = trim($_POST['check_temp']);







                    if($data['shipment_temp'] > 0){



                        $shipment_temp_model = $this->model->get('shipmenttempModel');



                        $shipment_temp_data = $shipment_temp_model->getShipment($data['shipment_temp']);







                        $data_shipment_temp = array(



                            'shipment_ton_use' => $shipment_temp_data->shipment_ton_use+$data['shipment_ton_net'],



                        );







                        $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$data['shipment_temp']));







                        $shipment_temp_data = $shipment_temp_model->getShipment($data['shipment_temp']);







                        if ( ($shipment_temp_data->shipment_temp_ton-$shipment_temp_data->shipment_ton_use) <= 0) {



                            $shipment_temp_model->updateShipment(array('shipment_temp_status'=>1), array('shipment_temp_id'=>$data['shipment_temp']));



                        }



                    }



                    $excess_model = $this->model->get('excessModel');



                    $dk = array(



                        'where' => 'used = 0 AND vehicle = '.$_POST['vehicle'].' AND round != '.$_POST['shipment_round'],



                        );



                    $cp = $excess_model->getAllExcess($dk);



                    $bd_truoc = 0;



                    $thuong_truoc = 0;



                    foreach ($cp as $cp_truoc) {



                        $bd_truoc += $cp_truoc->excess_cost;



                        $thuong_truoc += $cp_truoc->bonus;



                        $excess_model->deleteExcess($cp_truoc->excess_id);



                    }







                    $data['shipment_excess'] = $bd_truoc;



                    //$data['shipment_bonus'] = $thuong_truoc;







                    $data['shipment_cost'] = $chiphi;







                    $data['shipment_cost_temp'] = $chiphi_tam;







                    $data['shipment_update'] = 0;







                    $data['shipment_pay'] = 0;







                    $data['shipment_complete'] = 0;



                    



                    $data['approve'] = 1;



                    if ($data['cost_add'] > 0) {



                        $data['approve'] = 0;



                    }











                    //$shipment->updateShipment(array('not_del'=>1),array('vehicle'=>$data['vehicle'].' AND shipment_date <= '.$data['shipment_date'].' AND shipment_round != '.$data['shipment_round']));



                   



                    $shipment->createShipment($data);



                    





                    $id_shipment = $shipment->getLastShipment()->shipment_id;


                    if ($data['shipment_road_add'] > 0) {
                        $data_debit = array(

                            'debit_date'=>$data['shipment_date'],

                            'steersman'=>$data['steersman'],

                            'money'=>$data['shipment_road_add'],

                            'money_vat'=>0,

                            'comment'=>'Tiền đi đường '.$data['bill_number'],

                            'check_debit'=>2,

                            'shipment'=>$id_shipment,

                        );

                        $debit->createDebit($data_debit);
                    }

                    /*$data_debit = array(

                        'debit_date'=>$data['shipment_date'],

                        'customer'=>$data['customer'],

                        'money'=>$data['shipment_revenue']+$data['shipment_charge_excess'],

                        'money_vat'=>0,

                        'comment'=>'Vận chuyển',

                        'check_debit'=>1,

                        'shipment'=>$id_shipment,

                    );

                    $debit->createDebit($data_debit,array('shipment'=>$id_shipment));*/





                    foreach ($shipment_cost_list as $v) {

                        $cost_type = $cost_list_model->getCost($v['cost_list']);

                        $data_cost = array(



                            'shipment' => $id_shipment,



                            'cost' => trim(str_replace(',','',$v['cost'])),



                            'cost_list' => $v['cost_list'],



                            'check_vat' => $v['check_vat'],



                            'comment' => trim($v['comment']),



                            'receiver' => isset($v['receiver'])?$v['receiver']:null,



                            'cost_document' => trim($v['cost_document']),



                            'cost_document_date' => strtotime(trim($v['cost_document_date'])),



                        );



                        if ($data_cost['receiver'] > 0 && $cost_type->cost_list_type != 8) {

                            $chiphi += trim(str_replace(',','',$v['cost']));

                            $loinhuan -= trim(str_replace(',','',$v['cost']));

                        }



                        if ($v['shipment_cost_id'] == "") {



                            if ($data_cost['receiver'] > 0) {

                                

                                $shipment_cost_model->createShipment($data_cost);

                                $id_shipment_cost = $shipment_cost_model->getLastShipment()->shipment_cost_id;

                                

                                if ($data_cost['check_vat'] == 1) {

                                    $data_debit = array(

                                        'debit_date'=>$data['shipment_date'],

                                        'customer'=>$data_cost['receiver'],

                                        'money'=>round($data_cost['cost']/1.1),

                                        'money_vat_price'=>$data_cost['cost']-round($data_cost['cost']/1.1),

                                        'money_vat'=>$data_cost['check_vat'],

                                        'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                        'check_debit'=>2,

                                        'shipment_cost'=>$id_shipment_cost,

                                    );

                                    $debit->createDebit($data_debit);

                                    if ($cost_type->cost_list_type != 8) {
                                        
                                        $data_vat = array(

                                            'in_out'=>1,

                                            'vat_number'=>$data_cost['cost_document'],

                                            'vat_date'=>$data_cost['cost_document_date'],

                                            'shipment_cost'=>$id_shipment_cost,

                                        );

                                        $vat->createVAT($data_vat);
                                    }

                                }

                                else{

                                     $data_debit = array(

                                        'debit_date'=>$data['shipment_date'],

                                        'customer'=>$data_cost['receiver'],

                                        'money'=>$data_cost['cost'],

                                        'money_vat'=>$data_cost['check_vat'],

                                        'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                        'check_debit'=>2,

                                        'shipment_cost'=>$id_shipment_cost,

                                    );

                                    $debit->createDebit($data_debit);

                                }

                                if ($cost_type->cost_list_type == 8) {
                                    $data_debit = array(

                                        'debit_date'=>$data['shipment_date'],

                                        'customer'=>$data['customer'],

                                        'money'=>$data_cost['cost'],

                                        'money_vat'=>0,

                                        'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                        'check_debit'=>1,

                                        'shipment_cost'=>$id_shipment_cost,

                                        'check_loan'=>1,

                                    );

                                    $debit->createDebit($data_debit);
                                }

                                $vat_sum = round($data_cost['cost']/1.1);
                                $vat_price = $data_cost['cost']-round($data_cost['cost']/1.1);

                                $vat->updateVAT(array('vat_sum'=>$vat_sum,'vat_price'=>$vat_price),array('shipment_cost' => $id_shipment_cost));

                            }

                            

                        }



                    }



                    $data['shipment_cost'] = $chiphi;

                    $data['shipment_profit'] = $loinhuan;



                    $shipment->updateShipment(array('shipment_cost'=>$chiphi),array('shipment_id'=>$id_shipment));





                    $result['err'] = "Thêm thành công";



                    $result['revenue'] = $doanhthu;



                    $result['cost'] = $chiphi;



                    $result['profit'] = $loinhuan;



                    echo json_encode($result);



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 



                        $filename = "action_logs.txt";



                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$shipment->getLastShipment()->shipment_id."|shipment|".implode("-",$data)."\n"."\r\n";



                        



                        $fh = fopen($filename, "a") or die("Could not open log file.");



                        fwrite($fh, $text) or die("Could not write file!");



                        fclose($fh);



                }



                



            }


            if ($data['shipment_link'] > 0) {
                $shipment->updateShipment(array('shipment_link'=>$id_shipment),array('shipment_id'=>$data['shipment_link']));
            }

            $loan_shipment_model = $this->model->get('loanshipmentModel');



            $loans = explode(',', $_POST['loan']);



            if ($loans) {

                if ($id_shipment) {

                    foreach ($loans as $loan) {

                        $loan_shipment_model->updateUnit(array('shipment'=>$id_shipment),array('loan_shipment_id'=>$loan));

                    }

                }

                

            }

            



                    



        }



    }







    public function addsub(){



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) || json_decode($_SESSION['user_permission_action'])->shipment != "shipment") {



            return $this->view->redirect('user/login');



        }



        if (isset($_POST['yes'])) {



            $debit = $this->model->get('debitModel');

            $vat = $this->model->get('vatModel');



            $shipment = $this->model->get('shipmentModel');



            $shipment_cost_model = $this->model->get('shipmentcostModel');


            $cost_list_model = $this->model->get('costlistModel');

            /**************/



            $shipment_cost_list = $_POST['shipment_cost'];



            /**************/



            //$ad = mktime(date("H"), date("i"), date("s"), date("m"), date("d")+1, date("Y")); 



            $tomorrow = strtotime(trim($_POST['shipment_date']));



            $data = array(



                        



                        'shipment_from' => trim($_POST['shipment_from']),



                        'shipment_to' => trim($_POST['shipment_to']),



                        'vehicle' => trim($_POST['vehicle']),



                        'romooc' => trim($_POST['romooc']),


                        'steersman' => trim($_POST['steersman']),



                        'customer' => trim($_POST['customer']),



                        'shipment_ton' => trim($_POST['shipment_ton']),



                        'cont_unit' => trim($_POST['cont_unit']),



                        'shipment_charge' => trim(str_replace(',','',$_POST['shipment_charge'])),



                        'oil_add' => trim(str_replace(',','',$_POST['oil_add'])),



                        'oil_add2' => trim(str_replace(',','',$_POST['oil_add2'])),



                        'cost_add' => trim(str_replace(',','',$_POST['cost_add'])),



                        'oil_cost' => round((trim(str_replace(',','',$_POST['oil_cost'])))/1.1),



                        'shipment_date' => $tomorrow,



                        'shipment_round' => trim($_POST['shipment_round']),



                        'shipment_create_user' => $_SESSION['userid_logined'],



                        'oil_add_dc' => trim($_POST['oil_add_dc']),



                        'oil_add_dc2' => trim($_POST['oil_add_dc2']),



                        'cost_add_comment' => trim($_POST['cost_add_comment']),



                        'shipment_loan' => trim(str_replace(',','',$_POST['shipment_loan'])),



                        'sub_driver' => trim($_POST['sub_driver']),



                        'sub_driver_name' => trim($_POST['sub_driver_name']),



                        'sub_driver2' => trim($_POST['sub_driver2']),



                        'loan_content' => trim($_POST['loan_content']),



                        'shipment_sub' => 1,



                        'shipment_salary' => trim(str_replace(',','',$_POST['shipment_salary'])),
                        'shipment_road_add' => trim(str_replace(',','',$_POST['shipment_road_add'])),

                        'bill_number' => trim($_POST['bill_number']),

                        'bill_receive_date' => strtotime(trim($_POST['bill_receive_date'])),

                        'bill_delivery_date' => strtotime(trim($_POST['bill_delivery_date'])),

                        'bill_receive_ton' => trim($_POST['bill_receive_ton']),

                        'bill_receive_unit' => trim($_POST['bill_receive_unit']),

                        'bill_delivery_ton' => trim($_POST['bill_delivery_ton']),

                        'bill_delivery_unit' => trim($_POST['bill_delivery_unit']),

                        'bill_in' => strtotime(trim($_POST['bill_delivery_date']).' '.trim($_POST['bill_in'])),

                        'bill_out' => strtotime(trim($_POST['bill_delivery_date']).' '.trim($_POST['bill_out'])),

                        'shipment_oil' => trim($_POST['shipment_oil']),

                        'shipment_road_oil_add' => trim($_POST['shipment_road_oil_add']),

                        'shipment_road_oil' => trim($_POST['shipment_road_oil']),

                        );

            $data['shipment_ton'] = $data['bill_delivery_ton'];
            $data['cont_unit'] = $data['bill_delivery_unit'];
            $data['shipment_ton_net'] = $data['bill_receive_ton'];
            $data['cont_unit_net'] = $data['bill_receive_unit'];
            $data['oil_add'] = $data['shipment_oil'];

            $export_stock_model = $this->model->get('exportstockModel');



            $contributor = "";

            if(trim($_POST['export_stock']) != ""){

                $support = explode(',', trim($_POST['export_stock']));



                if ($support) {

                    foreach ($support as $key) {

                        $name = $export_stock_model->getStockByWhere(array('export_stock_code'=>trim($key)));

                        if ($name) {

                            if ($contributor == "")

                                $contributor .= $name->export_stock_id;

                            else

                                $contributor .= ','.$name->export_stock_id;

                        }

                        

                    }

                }



            }

            $data['export_stock'] = $contributor;



            $customer_sub_model = $this->model->get('customersubModel');



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

                            $customer_sub_model->createCustomer(array('customer_sub_name'=>trim($key)));

                            if ($contributor == "")

                                $contributor .= $customer_sub_model->getLastCustomer()->customer_sub_id;

                            else

                                $contributor .= ','.$customer_sub_model->getLastCustomer()->customer_sub_id;

                        }

                        

                    }

                }



            }

            $data['customer_type'] = $contributor;



            $contributor = "";

            if(is_array($_POST['route'])){
                foreach ($_POST['route'] as $key) {

                    if ($contributor == "")

                        $contributor .= $key;

                    else

                        $contributor .= ','.$key;

                }
            }

            $data['route'] = $contributor;







            /*if (trim($_POST['shipment_ton_net']) != "") {



                    $data['shipment_ton'] = trim($_POST['shipment_ton_net']);



                    $data['shipment_update'] = 1;



                }*/







                $doanhthu = 0;



                $chiphi = 0;



                $loinhuan = 0;







                $chiphi_tam = 0;







                $giadau = round($data['oil_cost']*1.1); //gia dau hien tai











                $chiphi = $chiphi+$data['shipment_loan'];







                if ($data['oil_add_dc'] == 5) {



                    $chiphi_tam = $chiphi_tam+$data['shipment_loan']+($data['oil_add']*$giadau);



                }



                else{



                    $chiphi_tam = $chiphi_tam+$data['shipment_loan'];



                }







                if ($data['oil_add_dc2'] == 5) {



                    $chiphi_tam = $chiphi_tam+($data['oil_add2']*$giadau);



                }



                







                $doanhthu = $data['shipment_charge']*$data['shipment_ton'];



                $loinhuan = $doanhthu - $chiphi;







                $data['shipment_revenue'] = $doanhthu;



                $data['shipment_cost'] = $chiphi;



                $data['shipment_profit'] = $loinhuan;







                $data['shipment_cost_temp'] = $chiphi_tam;







            $result = array();







            if ($_POST['yes'] != "") {



                //$data['shipment_update_user'] = $_SESSION['userid_logined'];



                //$data['shipment_update_time'] = time();



                //var_dump($data);



                







                if ($shipment->checkShipment($_POST['yes'],trim($_POST['bill_number']))) {



                    $result['err'] = "Bảng này đã tồn tại";



                    $result['revenue'] = 0;



                    $result['cost'] = 0;



                    $result['profit'] = 0;



                    echo json_encode($result);



                    return false;



                }



                else{


                    $data_shipments = $shipment->getShipment($_POST['yes']);


                    $shipment_temp_id = $data_shipments->shipment_temp;



                    if($shipment_temp_id > 0){



                        $shipment_temp_model = $this->model->get('shipmenttempModel');



                        $shipment_temp_data = $shipment_temp_model->getShipment($shipment_temp_id);







                        $data_shipment_temp = array(



                            'shipment_ton_use' => $shipment_temp_data->shipment_ton_use-$data_shipments->shipment_ton_net+$data['shipment_ton_net'],



                        );







                        $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$shipment_temp_id));







                        $shipment_temp_data = $shipment_temp_model->getShipment($shipment_temp_id);







                        if ( ($shipment_temp_data->shipment_temp_ton-$shipment_temp_data->shipment_ton_use) <= 0) {



                            $shipment_temp_model->updateShipment(array('shipment_temp_status'=>1), array('shipment_temp_id'=>$shipment_temp_id));



                        }



                    }





                    $shipment->updateShipment($data,array('shipment_id' => $_POST['yes']));



                    $result['err'] =  "Cập nhật thành công";



                    $result['revenue'] = $doanhthu;



                    $result['cost'] = $chiphi;



                    $result['profit'] = $loinhuan;


                    $result['id'] = $_POST['yes'];



                    echo json_encode($result);



                    $id_shipment = $_POST['yes'];


                    if ($data_shipments->shipment_road_add > 0 && $data['shipment_road_add'] > 0) {
                        $data_debit = array(

                            'debit_date'=>$data['shipment_date'],

                            'steersman'=>$data['steersman'],

                            'money'=>$data['shipment_road_add'],

                            'money_vat'=>0,

                            'comment'=>'Tiền đi đường '.$data['bill_number'],

                            'check_debit'=>2,

                            'shipment'=>$id_shipment,

                        );

                        $debit->updateDebit($data_debit,array('shipment'=>$id_shipment));
                    }
                    else if (($data_shipments->shipment_road_add == 0 || $data_shipments->shipment_road_add == "") && $data['shipment_road_add'] > 0) {
                        $data_debit = array(

                            'debit_date'=>$data['shipment_date'],

                            'steersman'=>$data['steersman'],

                            'money'=>$data['shipment_road_add'],

                            'money_vat'=>0,

                            'comment'=>'Tiền đi đường '.$data['bill_number'],

                            'check_debit'=>2,

                            'shipment'=>$id_shipment,

                        );

                        $debit->createDebit($data_debit);
                    }
                    else if ($data_shipments->shipment_road_add > 0 && ($data['shipment_road_add'] == 0 || $data['shipment_road_add'] == "")) {
                        $debit->queryDebit("DELETE FROM debit WHERE shipment = ".$id_shipment);
                    }

                    /*$data_debit = array(

                        'debit_date'=>$data['shipment_date'],

                        'customer'=>$data['customer'],

                        'money'=>$data['shipment_revenue']+$data['shipment_charge_excess'],

                        'money_vat'=>0,

                        'comment'=>'Vận chuyển',

                        'check_debit'=>1,

                        'shipment'=>$id_shipment,

                    );

                    $debit->updateDebit($data_debit,array('shipment'=>$id_shipment));*/





                    foreach ($shipment_cost_list as $v) {

                        $cost_type = $cost_list_model->getCost($v['cost_list']);

                        $data_cost = array(



                            'shipment' => $id_shipment,



                            'cost' => trim(str_replace(',','',$v['cost'])),



                            'cost_list' => $v['cost_list'],



                            'check_vat' => $v['check_vat'],



                            'comment' => trim($v['comment']),



                            'receiver' => isset($v['receiver'])?$v['receiver']:null,



                            'cost_document' => trim($v['cost_document']),



                            'cost_document_date' => strtotime(trim($v['cost_document_date'])),



                        );



                        if ($data_cost['receiver'] > 0 && $cost_type->cost_list_type != 8) {

                            $chiphi += trim(str_replace(',','',$v['cost']));

                            $loinhuan -= trim(str_replace(',','',$v['cost']));

                        }



                        if ($v['shipment_cost_id'] == "") {



                            if ($data_cost['receiver'] > 0) {

                                $shipment_cost_model->createShipment($data_cost);

                                $id_shipment_cost = $shipment_cost_model->getLastShipment()->shipment_cost_id;

                                

                                if ($data_cost['check_vat'] == 1) {

                                    $data_debit = array(

                                        'debit_date'=>$data['shipment_date'],

                                        'customer'=>$data_cost['receiver'],

                                        'money'=>round($data_cost['cost']/1.1),

                                        'money_vat_price'=>$data_cost['cost']-round($data_cost['cost']/1.1),

                                        'money_vat'=>$data_cost['check_vat'],

                                        'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                        'check_debit'=>2,

                                        'shipment_cost'=>$id_shipment_cost,

                                    );

                                    $debit->createDebit($data_debit);

                                    if ($cost_type->cost_list_type != 8) {

                                        $data_vat = array(

                                            'in_out'=>1,

                                            'vat_number'=>$data_cost['cost_document'],

                                            'vat_date'=>$data_cost['cost_document_date'],

                                            'shipment_cost'=>$id_shipment_cost,

                                        );

                                        $vat->createVAT($data_vat);
                                    }



                                }

                                else{

                                    $data_debit = array(

                                        'debit_date'=>$data['shipment_date'],

                                        'customer'=>$data_cost['receiver'],

                                        'money'=>$data_cost['cost'],

                                        'money_vat'=>$data_cost['check_vat'],

                                        'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                        'check_debit'=>2,

                                        'shipment_cost'=>$id_shipment_cost,

                                    );

                                    $debit->createDebit($data_debit);

                                    

                                }

                                if ($cost_type->cost_list_type == 8) {
                                    $data_debit = array(

                                        'debit_date'=>$data['shipment_date'],

                                        'customer'=>$data['customer'],

                                        'money'=>$data_cost['cost'],

                                        'money_vat'=>0,

                                        'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                        'check_debit'=>1,

                                        'shipment_cost'=>$id_shipment_cost,

                                        'check_loan'=>1,

                                    );

                                    $debit->createDebit($data_debit);
                                }


                                $vat_sum = round($data_cost['cost']/1.1);
                                $vat_price = $data_cost['cost']-round($data_cost['cost']/1.1);

                                $vat->updateVAT(array('vat_sum'=>$vat_sum,'vat_price'=>$vat_price),array('shipment_cost' => $id_shipment_cost));

                            }



                        }



                        else if ($v['shipment_cost_id'] > 0) {

                            $check = $shipment_cost_model->getShipment($v['shipment_cost_id']);

                            $shipment_cost_model->updateShipment($data_cost,array('shipment_cost_id'=>$v['shipment_cost_id']));


                            $check_cost_type = $cost_list_model->getCost($check->cost_list);
                            

                            if ($data_cost['check_vat'] == 1) {

                                $data_debit = array(

                                    'debit_date'=>$data['shipment_date'],

                                    'customer'=>$data_cost['receiver'],

                                    'money'=>round($data_cost['cost']/1.1),

                                    'money_vat_price'=>$data_cost['cost']-round($data_cost['cost']/1.1),

                                    'money_vat'=>$data_cost['check_vat'],

                                    'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                    'check_debit'=>2,

                                    'shipment_cost'=>$v['shipment_cost_id'],

                                );

                                $debit->updateDebit($data_debit,array('shipment_cost'=>$v['shipment_cost_id'],'check_loan'=>2));

                            }

                            else{

                                $data_debit = array(

                                    'debit_date'=>$data['shipment_date'],

                                    'customer'=>$data_cost['receiver'],

                                    'money'=>$data_cost['cost'],

                                    'money_vat_price'=>0,

                                    'money_vat'=>$data_cost['check_vat'],

                                    'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                    'check_debit'=>2,

                                    'shipment_cost'=>$v['shipment_cost_id'],

                                );

                                $debit->updateDebit($data_debit,array('shipment_cost'=>$v['shipment_cost_id'],'check_loan'=>2));

                            }



                            if ($check->check_vat == 1 && $data_cost['check_vat'] == 1) {

                                 $data_vat = array(

                                    'in_out'=>1,

                                    'vat_number'=>$data_cost['cost_document'],

                                    'vat_date'=>$data_cost['cost_document_date'],

                                    'shipment_cost'=>$v['shipment_cost_id'],

                                );

                                $vat->updateVAT($data_vat,array('shipment_cost'=>$v['shipment_cost_id']));

                            }

                            else if ($check->check_vat == 1 && $data_cost['check_vat'] != 1) {

                                $vat->queryVAT('DELETE FROM vat WHERE shipment_cost = '.$v['shipment_cost_id']);

                            }

                            else if ($check->check_vat != 1 && $data_cost['check_vat'] == 1) {

                                if ($cost_type->cost_list_type != 8) {
                                     $data_vat = array(

                                        'in_out'=>1,

                                        'vat_number'=>$data_cost['cost_document'],

                                        'vat_date'=>$data_cost['cost_document_date'],

                                        'shipment_cost'=>$v['shipment_cost_id'],

                                    );

                                    $vat->createVAT($data_vat);
                                }

                            }

                            $vat_sum = round($data_cost['cost']/1.1);
                            $vat_price = $data_cost['cost']-round($data_cost['cost']/1.1);

                            $vat->updateVAT(array('vat_sum'=>$vat_sum,'vat_price'=>$vat_price),array('shipment_cost' => $v['shipment_cost_id']));


                            if ($check_cost_type->cost_list_type == 8 && $cost_type->cost_list_type == 8) {
                                $data_debit = array(

                                    'debit_date'=>$data['shipment_date'],

                                    'customer'=>$data['customer'],

                                    'money'=>$data_cost['cost'],

                                    'money_vat'=>0,

                                    'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                    'check_debit'=>1,

                                    'shipment_cost'=>$v['shipment_cost_id'],

                                    'check_loan'=>1,

                                );

                                $debit->updateDebit($data_debit,array('shipment_cost'=>$v['shipment_cost_id'],'check_loan'=>1));
                            }
                            else if ($check_cost_type->cost_list_type == 8 && $cost_type->cost_list_type != 8) {
                                $debit->queryDebit('DELETE FROM debit WHERE check_loan = 1 AND shipment_cost = '.$v['shipment_cost_id']);
                            }
                            else if ($check_cost_type->cost_list_type != 8 && $cost_type->cost_list_type == 8) {
                                $data_debit = array(

                                    'debit_date'=>$data['shipment_date'],

                                    'customer'=>$data['customer'],

                                    'money'=>$data_cost['cost'],

                                    'money_vat'=>0,

                                    'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                    'check_debit'=>1,

                                    'shipment_cost'=>$v['shipment_cost_id'],

                                    'check_loan'=>1,

                                );

                                $debit->createDebit($data_debit);
                            }

                        }



                    }



                    $data['shipment_cost'] = $chiphi;



                    $shipment->updateShipment($data,array('shipment_id' => $_POST['yes']));







                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 



                        $filename = "action_logs.txt";



                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|shipment|".implode("-",$data)."\n"."\r\n";



                        



                        $fh = fopen($filename, "a") or die("Could not open log file.");



                        fwrite($fh, $text) or die("Could not write file!");



                        fclose($fh);



                    }



            }



            else{



                //$data['shipment_create_user'] = $_SESSION['userid_logined'];



                //$data['staff'] = $_POST['staff'];



                //var_dump($data);



                /*if ($shipment->checkUpdate(trim($_POST['vehicle']),trim($_POST['shipment_round']),$tomorrow)) {



                    $result['err'] = "Vui lòng cập nhật trọng tải vòng trước";



                    $result['revenue'] = 0;



                    $result['cost'] = 0;



                    $result['profit'] = 0;



                    echo json_encode($result);



                    return false;



                }*/



                if ($shipment->checkComplete(trim($_POST['vehicle']),trim($_POST['shipment_round']),$tomorrow)) {



                    $result['err'] = "Vui lòng hoàn tất vòng trước";



                    $result['revenue'] = 0;



                    $result['cost'] = 0;



                    $result['profit'] = 0;



                    echo json_encode($result);



                    return false;



                }







                if ($shipment->checkShipment(0,trim($_POST['bill_number']))) {



                    $result['err'] = "Bảng này đã tồn tại";



                    $result['revenue'] = 0;



                    $result['cost'] = 0;



                    $result['profit'] = 0;



                    echo json_encode($result);



                    return false;



                }



                else{



                    $data['shipment_create_user'] = $_SESSION['userid_logined'];



                    $data['shipment_temp'] = trim($_POST['check_temp']);







                    if($data['shipment_temp'] > 0){



                        $shipment_temp_model = $this->model->get('shipmenttempModel');



                        $shipment_temp_data = $shipment_temp_model->getShipment($data['shipment_temp']);







                        $data_shipment_temp = array(



                            'shipment_ton_use' => $shipment_temp_data->shipment_ton_use+$data['shipment_ton_net'],



                        );







                        $shipment_temp_model->updateShipment($data_shipment_temp,array('shipment_temp_id'=>$data['shipment_temp']));







                        $shipment_temp_data = $shipment_temp_model->getShipment($data['shipment_temp']);







                        if ( ($shipment_temp_data->shipment_temp_ton-$shipment_temp_data->shipment_ton_use) <= 0) {



                            $shipment_temp_model->updateShipment(array('shipment_temp_status'=>1), array('shipment_temp_id'=>$data['shipment_temp']));



                        }



                    }



                    $data['shipment_cost'] = $chiphi;







                    $data['shipment_cost_temp'] = $chiphi_tam;







                    $data['shipment_update'] = 0;







                    $data['shipment_pay'] = 0;







                    $data['shipment_complete'] = 0;



                    



                    $data['approve'] = 1;



                    if ($data['cost_add'] > 0) {



                        $data['approve'] = 0;



                    }











                    //$shipment->updateShipment(array('not_del'=>1),array('vehicle'=>$data['vehicle'].' AND shipment_date <= '.$data['shipment_date'].' AND shipment_round != '.$data['shipment_round']));



                   



                    $shipment->createShipment($data);



                    $result['err'] = "Thêm thành công";



                    $result['revenue'] = $doanhthu;



                    $result['cost'] = $chiphi;



                    $result['profit'] = $loinhuan;


                    $id_shipment = $shipment->getLastShipment()->shipment_id;


                    $result['id'] = $id_shipment;

                    echo json_encode($result);


                    if ($data['shipment_road_add'] > 0) {
                        $data_debit = array(

                            'debit_date'=>$data['shipment_date'],

                            'steersman'=>$data['steersman'],

                            'money'=>$data['shipment_road_add'],

                            'money_vat'=>0,

                            'comment'=>'Tiền đi đường '.$data['bill_number'],

                            'check_debit'=>2,

                            'shipment'=>$id_shipment,

                        );

                        $debit->createDebit($data_debit);
                    }

                    /*$data_debit = array(

                        'debit_date'=>$data['shipment_date'],

                        'customer'=>$data['customer'],

                        'money'=>$data['shipment_revenue']+$data['shipment_charge_excess'],

                        'money_vat'=>0,

                        'comment'=>'Vận chuyển',

                        'check_debit'=>1,

                        'shipment'=>$id_shipment,

                    );

                    $debit->createDebit($data_debit,array('shipment'=>$id_shipment));*/





                    foreach ($shipment_cost_list as $v) {

                        $cost_type = $cost_list_model->getCost($v['cost_list']);

                        $data_cost = array(



                            'shipment' => $id_shipment,



                            'cost' => trim(str_replace(',','',$v['cost'])),



                            'cost_list' => $v['cost_list'],



                            'check_vat' => $v['check_vat'],



                            'comment' => trim($v['comment']),



                            'receiver' => isset($v['receiver'])?$v['receiver']:null,



                            'cost_document' => trim($v['cost_document']),



                            'cost_document_date' => strtotime(trim($v['cost_document_date'])),



                        );



                        if ($data_cost['receiver'] > 0 && $cost_type->cost_list_type != 8) {

                            $chiphi += trim(str_replace(',','',$v['cost']));
                            $loinhuan -= trim(str_replace(',','',$v['cost']));

                        }



                        if ($v['shipment_cost_id'] == "") {



                            if ($data_cost['receiver'] > 0) {



                                $shipment_cost_model->createShipment($data_cost);

                                $id_shipment_cost = $shipment_cost_model->getLastShipment()->shipment_cost_id;

                                

                                if ($data_cost['check_vat'] == 1) {

                                    $data_debit = array(

                                        'debit_date'=>$data['shipment_date'],

                                        'customer'=>$data_cost['receiver'],

                                        'money'=>round($data_cost['cost']/1.1),

                                        'money_vat_price'=>$data_cost['cost']-round($data_cost['cost']/1.1),

                                        'money_vat'=>$data_cost['check_vat'],

                                        'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                        'check_debit'=>2,

                                        'shipment_cost'=>$id_shipment_cost,

                                    );

                                    $debit->createDebit($data_debit);

                                    if ($cost_type->cost_list_type != 8) {
                                        
                                        $data_vat = array(

                                            'in_out'=>1,

                                            'vat_number'=>$data_cost['cost_document'],

                                            'vat_date'=>$data_cost['cost_document_date'],

                                            'shipment_cost'=>$id_shipment_cost,

                                        );

                                        $vat->createVAT($data_vat);
                                    }

                                }

                                else{

                                    $data_debit = array(

                                        'debit_date'=>$data['shipment_date'],

                                        'customer'=>$data_cost['receiver'],

                                        'money'=>$data_cost['cost'],

                                        'money_vat'=>$data_cost['check_vat'],

                                        'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                        'check_debit'=>2,

                                        'shipment_cost'=>$id_shipment_cost,

                                    );

                                    $debit->createDebit($data_debit);

                                }

                                if ($cost_type->cost_list_type == 8) {
                                    $data_debit = array(

                                        'debit_date'=>$data['shipment_date'],

                                        'customer'=>$data['customer'],

                                        'money'=>$data_cost['cost'],

                                        'money_vat'=>0,

                                        'comment'=>$data_cost['comment'].' ('.$cost_type->cost_list_name.' '.$data_cost['cost_document'].') ['.$data['bill_number'].']',

                                        'check_debit'=>1,

                                        'shipment_cost'=>$id_shipment_cost,

                                        'check_loan'=>1,

                                    );

                                    $debit->createDebit($data_debit);
                                }

                                $vat_sum = round($data_cost['cost']/1.1);
                                $vat_price = $data_cost['cost']-round($data_cost['cost']/1.1);

                                $vat->updateVAT(array('vat_sum'=>$vat_sum,'vat_price'=>$vat_price),array('shipment_cost' => $id_shipment_cost));


                            }



                        }



                    }



                    $data['shipment_cost'] = $chiphi;

                    $data['shipment_profit'] = $loinhuan;



                    $shipment->updateShipment(array('shipment_cost'=>$chiphi),array('shipment_id'=>$id_shipment));



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 



                        $filename = "action_logs.txt";



                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$shipment->getLastShipment()->shipment_id."|shipment|".implode("-",$data)."\n"."\r\n";



                        



                        $fh = fopen($filename, "a") or die("Could not open log file.");



                        fwrite($fh, $text) or die("Could not write file!");



                        fclose($fh);



                }



                



            }



            $loan_shipment_model = $this->model->get('loanshipmentModel');



            $loans = explode(',', $_POST['loan']);



            if ($loans) {

                if ($id_shipment) {

                    foreach ($loans as $loan) {

                        $loan_shipment_model->updateUnit(array('shipment'=>$id_shipment),array('loan_shipment_id'=>$loan));

                    }

                }

                

            }



                    



        }



    }







    public function oiladd(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) || json_decode($_SESSION['user_permission_action'])->shipment != "shipment") {



            return $this->view->redirect('user/login');



        }



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $shipment_model = $this->model->get('shipmentModel');



            $road_model = $this->model->get('roadModel');







            $batdau = '30-'.date('m-Y', strtotime("last month"));



            $ketthuc = trim($_POST['ngay']);



            $xe = trim($_POST['xe']);



            $vong = trim($_POST['vong']);







            $total_oil_cost = 0;



            $total_oil_add = 0;







            $total_oil_cost_before = 0;



            $total_oil_add_before = 0;







            $shipments = $shipment_model->getAllShipment(array('where'=>'vehicle = '.$xe.' AND shipment_round = '.$vong.' AND shipment_date >= '.strtotime($batdau).' AND shipment_date <= '.$ketthuc));



            foreach ($shipments as $ship) {



               $total_oil_add += ($ship->oil_add+$ship->oil_add2);







                $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));



                



                foreach ($roads as $road) {



                    
                    if ($road->road_oil_ton > 0) {
                        $total_oil_cost += $road->road_oil_ton;
                    }
                    else{
                        $total_oil_cost += $road->road_oil;
                    }




                }



            }







            $shipments = $shipment_model->getAllShipment(array('where'=>'vehicle = '.$xe.' AND shipment_round != '.$vong.' AND shipment_date >= '.strtotime('29-11-2014').' AND shipment_date <= '.$ketthuc));



            foreach ($shipments as $ship) {



               $total_oil_add_before += ($ship->oil_add+$ship->oil_add2);







                $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));



                



                foreach ($roads as $road) {



                    if ($road->road_oil_ton > 0) {
                        $total_oil_cost_before += $road->road_oil_ton;
                    }
                    else{
                        $total_oil_cost_before += $road->road_oil;
                    }




                }



            }







            $oil = $total_oil_cost_before - $total_oil_add_before;







            echo $oil+$total_oil_cost-$total_oil_add;



        }



    }







    public function approve(){



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if ($_SESSION['role_logined'] != 1) {



            return $this->view->redirect('user/login');



        }



        if (isset($_POST['data'])) {







            $shipment = $this->model->get('shipmentModel');



            $shipment_data = $shipment->getShipment($_POST['data']);







            $data = array(



                        



                        'shipment_cost' => trim($shipment_data->cost_add)+trim($shipment_data->shipment_cost),



                        'approve' => 1,



                        'user_approve' => $_SESSION['userid_logined'],



                        );



          



            $shipment->updateShipment($data,array('shipment_id' => $_POST['data']));







            date_default_timezone_set("Asia/Ho_Chi_Minh"); 



                        $filename = "action_logs.txt";



                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."approve"."|".$_POST['data']."|shipment|"."\n"."\r\n";



                        



                        $fh = fopen($filename, "a") or die("Could not open log file.");



                        fwrite($fh, $text) or die("Could not write file!");



                        fclose($fh);







            return true;



                    



        }



    }











    public function checkroad(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) || json_decode($_SESSION['user_permission_action'])->shipment != "shipment") {



            return $this->view->redirect('user/login');



        }



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $road_model = $this->model->get('roadModel');

            $from = isset($_POST['road_from'])?trim($_POST['road_from']):0;
            $to = isset($_POST['road_to'])?trim($_POST['road_to']):0;

            $road = $road_model->getRoadByWhere(array('road_from' => $from,'road_to' => $to));







            echo isset($road->way)?$road->way:0;



        }



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



                'where'=>'( place_name LIKE "'.$_POST['keyword'].'%")',



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



    public function getshipmentfromsub(){



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



                'where'=>'( place_name LIKE "'.$_POST['keyword'].'%")',



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



                echo '<li onclick="set_item_shipment_from_sub(\''.$rs->place_id.'\',\''.$rs->place_name.'\')">'.$place_name.'</li>';



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



                'where'=>'( place_name LIKE "'.$_POST['keyword'].'%")',



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



    public function getshipmenttosub(){



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



                'where'=>'( place_name LIKE "'.$_POST['keyword'].'%")',



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



                echo '<li onclick="set_item_shipment_to_sub(\''.$rs->place_id.'\',\''.$rs->place_name.'\')">'.$place_name.'</li>';



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



    public function getcustomersub(){



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



                echo '<li onclick="set_item_customer_sub(\''.$rs->customer_id.'\',\''.$rs->customer_name.'\')">'.$customer_name.'</li>';



            }



        }



    }



    public function getvehicle(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $vehicle_model = $this->model->get('vehicleModel');

            $vehicle_romooc_model = $this->model->get('vehicleromoocModel');

            $driver_model = $this->model->get('driverModel');

            

            $ngay = isset($_POST['ngay'])?$_POST['ngay']:date('d-m-Y');



            if ($_POST['keyword'] == "*") {







                $list = $vehicle_model->getAllVehicle();



            }



            else{



                $data = array(



                'where'=>'( vehicle_number LIKE "%'.$_POST['keyword'].'%" )',



                );



                $list = $vehicle_model->getAllVehicle($data);



            }



            



            foreach ($list as $rs) {



                // put in bold the written text



                $vehicle_number = $rs->vehicle_number;



                if ($_POST['keyword'] != "*") {



                    $vehicle_number = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->vehicle_number);



                }

                $data_driver = array(

                    'where'=>'vehicle='.$rs->vehicle_id.' AND (start_work <= '.strtotime($ngay).' AND (end_work >= '.strtotime($ngay).' OR (end_work IS NULL OR end_work=0) ) )',

                    'order_by'=>'start_work DESC',

                    'limit'=>1,

                );

                $join = array('table'=>'steersman','where'=>'steersman=steersman_id');

                $drivers = $driver_model->getAllDriver($data_driver,$join);

                $steersman_name = "";
                $steersman_id = "";

                foreach ($drivers as $driver) {
                    $steersman_name = $driver->steersman_name;
                    $steersman_id = $driver->steersman_id;
                }



                $data_romooc = array(

                    'where'=>'vehicle='.$rs->vehicle_id.' AND (start_time <= '.strtotime($ngay).' AND (end_time >= '.strtotime($ngay).' OR (end_time IS NULL OR end_time=0) ) )',

                    'order_by'=>'start_time DESC',

                    'limit'=>1,

                );

                $join = array('table'=>'romooc','where'=>'romooc=romooc_id');

                $romoocs = $vehicle_romooc_model->getAllVehicle($data_romooc,$join);



                if ($romoocs) {

                    foreach ($romoocs as $romooc) {

                        echo '<li onclick="set_item_vehicle(\''.$rs->vehicle_id.'\',\''.$rs->vehicle_number.'\',\''.$romooc->romooc_id.'\',\''.$romooc->romooc_number.'\',\''.$steersman_name.'\',\''.$steersman_id.'\')">'.$vehicle_number.'</li>';

                    }

                }

                else{

                    echo '<li onclick="set_item_vehicle(\''.$rs->vehicle_id.'\',\''.$rs->vehicle_number.'\',\'\',\'\',\''.$steersman_name.'\',\''.$steersman_id.'\')">'.$vehicle_number.'</li>';

                }

                



                // add new option



                



            }



        }



    }

    public function getromooc(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $romooc_model = $this->model->get('romoocModel');



            



            if ($_POST['keyword'] == "*") {







                $list = $romooc_model->getAllVehicle();



            }



            else{



                $data = array(



                'where'=>'( romooc_number LIKE "%'.$_POST['keyword'].'%" )',



                );



                $list = $romooc_model->getAllVehicle($data);



            }



            



            foreach ($list as $rs) {



                // put in bold the written text



                $romooc_number = $rs->romooc_number;



                if ($_POST['keyword'] != "*") {



                    $romooc_number = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->romooc_number);



                }



                



                // add new option



                echo '<li onclick="set_item_romooc(\''.$rs->romooc_id.'\',\''.$rs->romooc_number.'\')">'.$romooc_number.'</li>';



            }



        }



    }



    public function getvehiclesub(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $vehicle_model = $this->model->get('vehicleModel');

            $vehicle_romooc_model = $this->model->get('vehicleromoocModel');

            $driver_model = $this->model->get('driverModel');

            

            $ngay = isset($_POST['ngay'])?$_POST['ngay']:date('d-m-Y');



            if ($_POST['keyword'] == "*") {







                $list = $vehicle_model->getAllVehicle();



            }



            else{



                $data = array(



                'where'=>'( vehicle_number LIKE "%'.$_POST['keyword'].'%" )',



                );



                $list = $vehicle_model->getAllVehicle($data);



            }



            



            foreach ($list as $rs) {



                // put in bold the written text



                $vehicle_number = $rs->vehicle_number;



                if ($_POST['keyword'] != "*") {



                    $vehicle_number = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->vehicle_number);



                }

                $data_driver = array(

                    'where'=>'vehicle='.$rs->vehicle_id.' AND (start_work <= '.strtotime($ngay).' AND (end_work >= '.strtotime($ngay).' OR (end_work IS NULL OR end_work=0) ) )',

                    'order_by'=>'start_work DESC',

                    'limit'=>1,

                );

                $join = array('table'=>'steersman','where'=>'steersman=steersman_id');

                $drivers = $driver_model->getAllDriver($data_driver,$join);

                $steersman_name = "";
                $steersman_id = "";

                foreach ($drivers as $driver) {
                    $steersman_name = $driver->steersman_name;
                    $steersman_id = $driver->steersman_id;
                }



                $data_romooc = array(

                    'where'=>'vehicle='.$rs->vehicle_id.' AND (start_time <= '.strtotime($ngay).' AND (end_time >= '.strtotime($ngay).' OR (end_time IS NULL OR end_time=0) ) )',

                    'order_by'=>'start_time DESC',

                    'limit'=>1,

                );

                $join = array('table'=>'romooc','where'=>'romooc=romooc_id');

                $romoocs = $vehicle_romooc_model->getAllVehicle($data_romooc,$join);



                if ($romoocs) {

                    foreach ($romoocs as $romooc) {

                        echo '<li onclick="set_item_vehicle_sub(\''.$rs->vehicle_id.'\',\''.$rs->vehicle_number.'\',\''.$romooc->romooc_id.'\',\''.$romooc->romooc_number.'\',\''.$steersman_name.'\',\''.$steersman_id.'\')">'.$vehicle_number.'</li>';

                    }

                }

                else{

                    echo '<li onclick="set_item_vehicle_sub(\''.$rs->vehicle_id.'\',\''.$rs->vehicle_number.'\',\'\',\'\',\''.$steersman_name.'\',\''.$steersman_id.'\')">'.$vehicle_number.'</li>';

                }

                



                // add new option



                



            }



        }



    }



    public function getdriver(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $steersman_model = $this->model->get('steersmanModel');



            



            if ($_POST['keyword'] == "*") {







                $list = $steersman_model->getAllSteersman();



            }



            else{



                $data = array(



                'where'=>'( steersman_name LIKE "%'.$_POST['keyword'].'%" )',



                );



                $list = $steersman_model->getAllSteersman($data);



            }



            



            foreach ($list as $rs) {



                // put in bold the written text



                $steersman_name = $rs->steersman_name;



                if ($_POST['keyword'] != "*") {



                    $steersman_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->steersman_name);



                }



                



                // add new option



                echo '<li onclick="set_item_driver(\''.$rs->steersman_id.'\',\''.$rs->steersman_name.'\')">'.$steersman_name.'</li>';



            }



        }



    }

    public function getdriversub(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $steersman_model = $this->model->get('steersmanModel');



            



            if ($_POST['keyword'] == "*") {







                $list = $steersman_model->getAllSteersman();



            }



            else{



                $data = array(



                'where'=>'( steersman_name LIKE "%'.$_POST['keyword'].'%" )',



                );



                $list = $steersman_model->getAllSteersman($data);



            }



            



            foreach ($list as $rs) {



                // put in bold the written text



                $steersman_name = $rs->steersman_name;



                if ($_POST['keyword'] != "*") {



                    $steersman_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->steersman_name);



                }



                



                // add new option



                echo '<li onclick="set_item_driver_sub(\''.$rs->steersman_id.'\',\''.$rs->steersman_name.'\')">'.$steersman_name.'</li>';



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



    public function getExport(){

        header('Content-type: application/json');

        $q = $_GET["search"];
        $v = $_GET["vehicle"];

        $shipment_model = $this->model->get('shipmentModel');
        $sub_model = $this->model->get('exportstockModel');
        $spare_stock_model = $this->model->get('sparestockModel');

        $data = array(

            'where' => 'export_stock_code LIKE "%'.$q.'%" AND vehicle = '.$v,

        );

        $subs = $sub_model->getAllStock($data);

        $arr = array();

        foreach ($subs as $sub) {
            $num = 0;
            $spare = $spare_stock_model->getAllStock(array('where'=>'export_stock = '.$sub->export_stock_id));
            foreach ($spare as $sp) {
                $num += $sp->spare_stock_number;
            }
            $ex = $shipment_model->queryShipment('SELECT shipment_oil FROM shipment WHERE export_stock LIKE "'.$sub->export_stock_id.'" OR export_stock LIKE "'.$sub->export_stock_id.',%" OR export_stock LIKE "%,'.$sub->export_stock_id.',%" OR export_stock LIKE "%,'.$sub->export_stock_id.'"');
            if (!$ex) {
                $arr[] = $sub->export_stock_code;
            }
            else{
                foreach ($ex as $e) {
                    if ($e->shipment_oil < $num) {
                        $arr[] = $sub->export_stock_code;
                    }
                }
            }
            
            

        }

        

        echo json_encode($arr);

    }



    public function getreceiver(){



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



                $customer_name = '['.$rs->customer_code.']-'.$rs->customer_name;



                if ($_POST['keyword'] != "*") {



                    $customer_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', '['.$rs->customer_code.']-'.$rs->customer_name);



                }



                



                // add new option



                echo '<li onclick="set_item_receiver(\''.$rs->customer_id.'\',\''.$rs->customer_name.'\',\''.$_POST['offset'].'\')">'.$customer_name.'</li>';



            }



        }



    }

    public function getreceiversub(){



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



                $customer_name = '['.$rs->customer_code.']-'.$rs->customer_name;



                if ($_POST['keyword'] != "*") {



                    $customer_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', '['.$rs->customer_code.']-'.$rs->customer_name);



                }



                



                // add new option



                echo '<li onclick="set_item_receiver_sub(\''.$rs->customer_id.'\',\''.$rs->customer_name.'\',\''.$_POST['offset'].'\')">'.$customer_name.'</li>';



            }



        }



    }



    public function deletecost(){



        if (isset($_POST['shipment_cost_id'])) {



            $shipment_cost_model = $this->model->get('shipmentcostModel');

            $debit = $this->model->get('debitModel');

            $vat = $this->model->get('vatModel');



            $debit->queryDebit('DELETE FROM debit WHERE shipment_cost = '.$_POST['shipment_cost_id']);

            $vat->queryVAT('DELETE FROM vat WHERE shipment_cost = '.$_POST['shipment_cost_id']);





            $shipment_cost_model->queryShipment('DELETE FROM shipment_cost WHERE shipment_cost_id = '.$_POST['shipment_cost_id']);



            echo 'Đã xóa thành công';



        }



    }



    public function getcost(){



        if(isset($_POST['shipment'])){



            



            $shipment_cost_model = $this->model->get('shipmentcostModel');

            $cost_list_model = $this->model->get('costlistModel');



            $cost_lists = $cost_list_model->getAllCost();





            $join = array('table'=>'customer, cost_list','where'=>'receiver = customer_id AND cost_list = cost_list_id');



            $data = array(



                'where' => 'shipment = '.$_POST['shipment'],



            );



            $costs = $shipment_cost_model->getAllShipment($data,$join);







            $str = "";



            if (!$costs) {



                $cost_data = "";

                foreach ($cost_lists as $cost) {

                    $cost_data .= '<option value="'.$cost->cost_list_id.'">'.$cost->cost_list_name.'</option>';

                }



                $str .= '<tr class="'.$_POST['shipment'].'">';



                $str .= '<td><input type="checkbox"  name="chk" data=""></td>';



                $str .= '<td><table style="width: 100%">';



                $str .= '<tr class="'.$_POST['shipment'] .'">';



                $str .= '<td>Chi phí <a target="_blank" title="Thêm chi phí" href="'.BASE_URL.'/costlist"><i class="fa fa-plus"></i></a></td>';



                $str .= '<td><select style="width:150px" name="cost_list[]" class="cost_list" >';



                    $str .= $cost_data;



                $str .= '</select></td>';



                $str .= '<td>Số tiền</td>';



                $str .= '<td><input style="width:120px" type="text" class="cost number" name="cost[]"><input type="checkbox" class="check_vat" name="check_vat[]" value="1"> VAT</td></tr>';



                $str .= '<tr><td>Nội dung</td>';



                $str .= '<td><textarea class="comment" name="comment[]"></textarea></td>';



                $str .= '<td>Người nhận <a target="_blank" title="Thêm người nhận" href="'.BASE_URL.'/customer/newcus"><i class="fa fa-plus"></i></a></td>';



                $str .= '<td><input type="text" autocomplete="off" class="receiver" name="receiver[]" placeholder="Nhập tên hoặc * để chọn" >';

                $str .= '<ul class="name_list_id"></ul></td></tr>';



                $str .= '<tr><td>Số Hóa đơn chứng từ</td>';



                $str .= '<td><input type="text" class="cost_document" name="cost_document[]" style="width:100px" > Ngày<input type="text" class="cost_document_date ngay" name="cost_document_date[]" style="width:60px" ></td>';



                $str .= '</tr></table></td></tr>';



            }



            else{



                foreach ($costs as $v) {



                    $cost_data = "";

                    foreach ($cost_lists as $cost) {

                        $cost_data .= '<option '.($v->cost_list==$cost->cost_list_id?'selected="selected"':null).' value="'.$cost->cost_list_id.'">'.$cost->cost_list_name.'</option>';

                    }



                    $checked = $v->check_vat==1?'checked="checked"':null;



                    $str .= '<tr class="'.$_POST['shipment'].'">';



                    $str .= '<td><input type="checkbox" name="chk" data="'.$v->shipment_cost_id.'"  ></td>';



                    $str .= '<td><table style="width: 100%">';



                    $str .= '<tr class="'.$_POST['shipment'] .'">';



                    $str .= '<td>Chi phí <a target="_blank" title="Thêm chi phí" href="'.BASE_URL.'/costlist"><i class="fa fa-plus"></i></a></td>';



                    $str .= '<td><select style="width:150px" name="cost_list[]" class="cost_list" >';



                        $str .= $cost_data;



                    $str .= '</select></td>';



                    $str .= '<td>Số tiền</td>';



                    $str .= '<td><input style="width:120px" type="text" class="cost number" name="cost[]" value="'.$this->lib->formatMoney($v->cost).'" ><input '.$checked.' type="checkbox" class="check_vat" name="check_vat[]" value="1"> VAT</td></tr>';



                    $str .= '<tr><td>Nội dung</td>';



                    $str .= '<td><textarea class="comment" name="comment[]">'.$v->comment.'</textarea></td>';



                    $str .= '<td>Người nhận <a target="_blank" title="Thêm người nhận" href="'.BASE_URL.'/customer/newcus"><i class="fa fa-plus"></i></a></td>';



                    $str .= '<td><input type="text" autocomplete="off" class="receiver" name="receiver[]" placeholder="Nhập tên hoặc * để chọn" value="'.$v->customer_name.'" data="'.$v->customer_id.'" >';

                    $str .= '<ul class="name_list_id"></ul></td></tr>';



                    $str .= '<tr><td>Số Hóa đơn chứng từ</td>';



                    $str .= '<td><input type="text" class="cost_document" name="cost_document[]" style="width:100px" value="'.$v->cost_document.'" > Ngày<input type="text" class="cost_document_date ngay" name="cost_document_date[]" style="width:60px" value="'.($v->cost_document_date>0?date('d-m-Y',$v->cost_document_date):null).'" ></td>';



                    $str .= '</tr></table></td></tr>';



                }



            }







            echo $str;



        }



    }

    public function getcostsub(){



        if(isset($_POST['shipment'])){



            



            $shipment_cost_model = $this->model->get('shipmentcostModel');

            $cost_list_model = $this->model->get('costlistModel');



            $cost_lists = $cost_list_model->getAllCost();





            $join = array('table'=>'customer, cost_list','where'=>'receiver = customer_id AND cost_list = cost_list_id');



            $data = array(



                'where' => 'shipment = '.$_POST['shipment'],



            );



            $costs = $shipment_cost_model->getAllShipment($data,$join);







            $str = "";



            if (!$costs) {



                $cost_data = "";

                foreach ($cost_lists as $cost) {

                    $cost_data .= '<option value="'.$cost->cost_list_id.'">'.$cost->cost_list_name.'</option>';

                }



                $str .= '<tr class="'.$_POST['shipment'].'">';



                $str .= '<td><input type="checkbox"  name="chk2" data=""></td>';



                $str .= '<td><table style="width: 100%">';



                $str .= '<tr class="'.$_POST['shipment'] .'">';



                $str .= '<td>Chi phí <a target="_blank" title="Thêm chi phí" href="'.BASE_URL.'/costlist"><i class="fa fa-plus"></i></a></td>';



                $str .= '<td><select style="width:150px" name="cost_list_sub[]" class="cost_list_sub" >';



                    $str .= $cost_data;



                $str .= '</select></td>';



                $str .= '<td>Số tiền</td>';



                $str .= '<td><input style="width:120px" type="text" class="cost_sub number" name="cost_sub[]"><input type="checkbox" class="check_vat_sub" name="check_vat_sub[]" value="1"> VAT</td></tr>';



                $str .= '<tr><td>Nội dung</td>';



                $str .= '<td><textarea class="comment_sub" name="comment_sub[]"></textarea></td>';



                $str .= '<td>Người nhận <a target="_blank" title="Thêm người nhận" href="'.BASE_URL.'/customer/newcus"><i class="fa fa-plus"></i></a></td>';



                $str .= '<td><input type="text" autocomplete="off" class="receiver_sub" name="receiver_sub[]" placeholder="Nhập tên hoặc * để chọn" >';

                $str .= '<ul class="name_list_id_2"></ul></td></tr>';



                $str .= '<tr><td>Số Hóa đơn chứng từ</td>';



                $str .= '<td><input type="text" class="cost_document_sub" name="cost_document_sub[]" style="width:100px" > Ngày<input type="text" class="cost_document_date_sub ngay" name="cost_document_date_sub[]" style="width:60px" ></td>';



                $str .= '</tr></table></td></tr>';



            }



            else{



                foreach ($costs as $v) {



                    $cost_data = "";

                    foreach ($cost_lists as $cost) {

                        $cost_data .= '<option '.($v->cost_list==$cost->cost_list_id?'selected="selected"':null).' value="'.$cost->cost_list_id.'">'.$cost->cost_list_name.'</option>';

                    }



                    $checked = $v->check_vat==1?'checked="checked"':null;



                    $str .= '<tr class="'.$_POST['shipment'].'">';



                    $str .= '<td><input type="checkbox" name="chk2" data="'.$v->shipment_cost_id.'"  ></td>';



                    $str .= '<td><table style="width: 100%">';



                    $str .= '<tr class="'.$_POST['shipment'] .'">';



                    $str .= '<td>Chi phí <a target="_blank" title="Thêm chi phí" href="'.BASE_URL.'/costlist"><i class="fa fa-plus"></i></a></td>';



                    $str .= '<td><select style="width:150px" name="cost_list_sub[]" class="cost_list_sub" >';



                        $str .= $cost_data;



                    $str .= '</select></td>';



                    $str .= '<td>Số tiền</td>';



                    $str .= '<td><input style="width:120px" type="text" class="cost_sub number" name="cost_sub[]" value="'.$this->lib->formatMoney($v->cost).'" ><input '.$checked.' type="checkbox" class="check_vat_sub" name="check_vat_sub[]" value="1"> VAT</td></tr>';



                    $str .= '<tr><td>Nội dung</td>';



                    $str .= '<td><textarea class="comment_sub" name="comment_sub[]">'.$v->comment.'</textarea></td>';



                    $str .= '<td>Người nhận <a target="_blank" title="Thêm người nhận" href="'.BASE_URL.'/customer/newcus"><i class="fa fa-plus"></i></a></td>';



                    $str .= '<td><input type="text" autocomplete="off" class="receiver_sub" name="receiver_sub[]" placeholder="Nhập tên hoặc * để chọn" value="'.$v->customer_name.'" data="'.$v->customer_id.'" >';

                    $str .= '<ul class="name_list_id_2"></ul></td></tr>';



                    $str .= '<tr><td>Số Hóa đơn chứng từ</td>';



                    $str .= '<td><input type="text" class="cost_document_sub" name="cost_document_sub[]" style="width:120px" value="'.$v->cost_document.'" > Ngày<input type="text" class="cost_document_date_sub ngay" name="cost_document_date_sub[]" style="width:60px" value="'.($v->cost_document_date>0?date('d-m-Y',$v->cost_document_date):null).'"></td>';



                    $str .= '</tr></table></td></tr>';



                }



            }







            echo $str;



        }



    }



    public function getroute(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $route_model = $this->model->get('routeModel');



            $routes = $route_model->getAllPlace(array('order_by'=>'route_name','order'=>'ASC'));



            $route_data = array();



            foreach ($routes as $route) {



                $route_data['route_id'][$route->route_id] = $route->route_id;



                $route_data['route_name'][$route->route_id] = $route->route_name;



            }



            $road_model = $this->model->get('roadModel');

            $from = isset($_POST['road_from'])?trim($_POST['road_from']):0;
            $to = isset($_POST['road_to'])?trim($_POST['road_to']):0;

            $data = array(

                'where' => 'road_from = '.$from.' AND road_to = '.$to.' AND start_time <= '.strtotime($_POST['ngay']).' AND end_time >= '.strtotime($_POST['ngay']),

            );



            $roads = $road_model->getAllRoad($data);



            $str = "";

            $diduong = 0;
            $luongchuyen = 0;

            foreach ($roads as $road) {

                $str .= '<option title="'.$road->road_add.','.$road->road_salary.'" selected value="'.$road->road_id.'">'.(isset($route_data['route_id'][$road->route_from])?$route_data['route_name'][$road->route_from]:null).'-'.(isset($route_data['route_id'][$road->route_to])?$route_data['route_name'][$road->route_to]:null).' ['.$road->road_km.'km]'.'</option>';

                $diduong += $road->road_add;
                $luongchuyen += $road->road_salary;
            }


            $arr = array(
                'data' => $str,
                'road_add' => $diduong,
                'road_salary' => $luongchuyen,
            );
            echo json_encode($arr);



        }



    }



    public function getrouteadd(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $route_model = $this->model->get('routeModel');



            $routes = $route_model->getAllPlace(array('order_by'=>'route_name','order'=>'ASC'));



            $route_data = array();



            foreach ($routes as $route) {



                $route_data['route_id'][$route->route_id] = $route->route_id;



                $route_data['route_name'][$route->route_id] = $route->route_name;



            }



            $route_add = explode(',', trim($_POST['route']));



            $road_model = $this->model->get('roadModel');



            $data = array(

                'where' => 'road_from = '.trim($_POST['road_from']).' AND road_to = '.trim($_POST['road_to']).' AND start_time <= '.strtotime($_POST['ngay']).' AND end_time >= '.strtotime($_POST['ngay']),

            );



            $roads = $road_model->getAllRoad($data);



            $str = "";



            foreach ($roads as $road) {

                $check = null;

                foreach ($route_add as $key) {

                    if ($road->road_id == $key) {

                        $check = "selected";

                        break;

                        

                    }

                }



                $str .= '<option '.$check.' value="'.$road->road_id.'">'.(isset($route_data['route_id'][$road->route_from])?$route_data['route_name'][$road->route_from]:null).'-'.(isset($route_data['route_id'][$road->route_to])?$route_data['route_name'][$road->route_to]:null).' ['.$road->road_km.'km]'.'</option>';

                

            }



            echo $str;



        }



    }



    public function getExportCode(){

        if (isset($_POST['export_number'])) {

            $spare_stock_model = $this->model->get('sparestockModel');
            $shipment_model = $this->model->get('shipmentModel');

            $join = array('table'=>'export_stock','where'=>'export_stock=export_stock_id');

            $data = array(

                'where' => 'export_stock_code = "'.trim($_POST['export_number']).'"',

                'order_by'=>'spare_stock_number',

                'order'=>'DESC',

                'limit'=>1,

            );

            $spare_parts = $spare_stock_model->getAllStock($data,$join);

            foreach ($spare_parts as $spare) {
                $num = $spare->spare_stock_number;
                $ex = $shipment_model->queryShipment('SELECT shipment_oil,export_stock FROM shipment WHERE shipment_id != '.$_POST['shipment'].' AND ( export_stock LIKE "'.$spare->export_stock_id.'" OR export_stock LIKE "'.$spare->export_stock_id.',%" OR export_stock LIKE "%,'.$spare->export_stock_id.',%" OR export_stock LIKE "%,'.$spare->export_stock_id.'" )');
                foreach ($ex as $e) {
                    $exp = explode(',', $e->export_stock);
                    foreach ($exp as $key) {
                        if ($key != $spare->export_stock_id) {
                            $data = array(
                                'where' => 'export_stock = '.$key,
                                'order_by'=>'spare_stock_number',
                                'order'=>'DESC',
                                'limit'=>1,
                            );
                            $spare_part2s = $spare_stock_model->getAllStock($data,$join);
                            foreach ($spare_part2s as $spare2) {
                                $num += $spare2->spare_stock_number;
                            }
                        }
                    }
                    $num -= $e->shipment_oil;
                }

                echo $num;

            }

        }

    }





    public function bill(){



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) || json_decode($_SESSION['user_permission_action'])->shipment != "shipment") {



            return $this->view->redirect('user/login');



        }



        if (isset($_POST['data'])) {

            $shipment = $this->model->get('shipmentModel');
            $debit = $this->model->get('debitModel');

            $ship = $shipment->getShipment($_POST['data']);

            $data = array(

                'shipment_ton' => trim($_POST['bill_delivery_ton']),

                'cont_unit' => trim($_POST['bill_delivery_unit']),

                'shipment_ton_net' => trim($_POST['bill_receive_ton']),

                'cont_unit_net' => trim($_POST['bill_receive_unit']),

                'bill_number' => trim($_POST['bill_number']),

                'bill_receive_date' => strtotime(trim($_POST['bill_receive_date'])),

                'bill_delivery_date' => strtotime(trim($_POST['bill_delivery_date'])),

                'bill_receive_ton' => trim($_POST['bill_receive_ton']),

                'bill_receive_unit' => trim($_POST['bill_receive_unit']),

                'bill_delivery_ton' => trim($_POST['bill_delivery_ton']),

                'bill_delivery_unit' => trim($_POST['bill_delivery_unit']),

                'bill_in' => strtotime(trim($_POST['bill_delivery_date']).' '.trim($_POST['bill_in'])),

                'bill_out' => strtotime(trim($_POST['bill_delivery_date']).' '.trim($_POST['bill_out'])),

                'shipment_revenue' => round($ship->shipment_charge*trim($_POST['bill_delivery_ton']))+$ship->shipment_charge_excess,

                'shipment_profit' => (round($ship->shipment_charge*trim($_POST['bill_delivery_ton']))+$ship->shipment_charge_excess)-$ship->shipment_cost,

            );



            $shipment->updateShipment($data,array('shipment_id'=>$_POST['data']));

            $d = $debit->getDebitByWhere(array('shipment'=>$_POST['data']));

            $debit->updateDebit(array('money'=>$data['shipment_revenue'],'comment'=>$d->comment.' - '.$data['bill_number']),array('shipment'=>$_POST['data']));



            echo "Cập nhật thành công";



            date_default_timezone_set("Asia/Ho_Chi_Minh"); 



            $filename = "action_logs.txt";



            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['data']."|bill|".implode("-",$data)."\n"."\r\n";



            



            $fh = fopen($filename, "a") or die("Could not open log file.");



            fwrite($fh, $text) or die("Could not write file!");



            fclose($fh);

        }

    }



    public function oil(){



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) || json_decode($_SESSION['user_permission_action'])->shipment != "shipment") {



            return $this->view->redirect('user/login');



        }



        if (isset($_POST['data'])) {

            $shipment = $this->model->get('shipmentModel');



            $data = array(

                'shipment_oil' => trim($_POST['shipment_oil']),

                'oil_add' => trim($_POST['shipment_oil']),

            );



            $export_stock_model = $this->model->get('exportstockModel');



            $contributor = "";

            if(trim($_POST['export_stock']) != ""){

                $support = explode(',', trim($_POST['export_stock']));



                if ($support) {

                    foreach ($support as $key) {

                        $name = $export_stock_model->getStockByWhere(array('export_stock_code'=>trim($key)));

                        if ($name) {

                            if ($contributor == "")

                                $contributor .= $name->export_stock_id;

                            else

                                $contributor .= ','.$name->export_stock_id;

                        }

                        

                    }

                }



            }

            $data['export_stock'] = $contributor;



            $shipment->updateShipment($data,array('shipment_id'=>$_POST['data']));



            echo "Cập nhật thành công";



            date_default_timezone_set("Asia/Ho_Chi_Minh"); 



            $filename = "action_logs.txt";



            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['data']."|shipmet_oil|".implode("-",$data)."\n"."\r\n";



            



            $fh = fopen($filename, "a") or die("Could not open log file.");



            fwrite($fh, $text) or die("Could not write file!");



            fclose($fh);

        }

    }







    public function delete(){



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) || json_decode($_SESSION['user_permission_action'])->shipment != "shipment") {



            return $this->view->redirect('user/login');



        }



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $shipment = $this->model->get('shipmentModel');



            $shipment_cost = $this->model->get('shipmentcostModel');



            $debit = $this->model->get('debitModel');

            $vat = $this->model->get('vatModel');



            $shipment_temp = $this->model->get('shipmenttempModel');



            if (isset($_POST['xoa'])) {



                $data = explode(',', $_POST['xoa']);



                foreach ($data as $data) {



                    $shipment_data = $shipment->getShipment($data);



                    if ($shipment_data->shipment_temp > 0) {



                        $shipment_temp_data = $shipment_temp->getShipment($shipment_data->shipment_temp);



                        $data_shipment = array(



                            'shipment_temp_status' => 0,



                            'shipment_ton_use' => $shipment_temp_data->shipment_ton_use-$shipment_data->shipment_ton_net,



                        );



                        $shipment_temp->updateShipment($data_shipment,array('shipment_temp_id'=>$shipment_data->shipment_temp));




                    }

                    /*if ($shipment_data->shipment_link > 0) {
                        $shipment_2_data = $shipment->getShipment($shipment_data->shipment_link);

                        if ($shipment_2_data->shipment_temp > 0) {
                            $shipment_temp_data = $shipment_temp->getShipment($shipment_2_data->shipment_temp);



                            $data_shipment = array(



                                'shipment_temp_status' => 0,



                                'shipment_ton_use' => $shipment_temp_data->shipment_ton_use-$shipment_2_data->shipment_ton_net,



                            );



                            $shipment_temp->updateShipment($data_shipment,array('shipment_temp_id'=>$shipment_2_data->shipment_temp));

                        }
                    }*/
                    


                    $costs = $shipment_cost->getAllShipment(array('where'=>'shipment = '.$data));

                    foreach ($costs as $cost) {

                        $debit->queryDebit('DELETE FROM debit WHERE shipment_cost = '.$cost->shipment_cost_id);

                        $vat->queryVAT('DELETE FROM vat WHERE shipment_cost = '.$cost->shipment_cost_id);

                        $shipment_cost->deleteShipment($cost->shipment_cost_id);

                    }



                    $shipment_cost->queryShipment('DELETE FROM shipment_cost WHERE shipment = '.$data);

                    $debit->queryDebit('DELETE FROM debit WHERE shipment = '.$data);

                    $vat->queryVAT('DELETE FROM vat WHERE shipment = '.$data);


                    //$shipment->queryShipment('DELETE FROM shipment WHERE shipment_id = '.$shipment_data->shipment_link);

                    $shipment->deleteShipment($data);







                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 



                        $filename = "action_logs.txt";



                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|shipment|"."\n"."\r\n";



                        



                        $fh = fopen($filename, "a") or die("Could not open log file.");



                        fwrite($fh, $text) or die("Could not write file!");



                        fclose($fh);



                }



                return true;



            }



            else{







                $shipment_data = $shipment->getShipment($_POST['data']);



                if ($shipment_data->shipment_temp > 0) {



                    $shipment_temp_data = $shipment_temp->getShipment($shipment_data->shipment_temp);



                    



                    $data_shipment = array(



                        'shipment_temp_status' => 0,



                        'shipment_ton_use' => $shipment_temp_data->shipment_ton_use-$shipment_data->shipment_ton_net,



                    );



                    $shipment_temp->updateShipment($data_shipment,array('shipment_temp_id'=>$shipment_data->shipment_temp));







                }

                /*if ($shipment_data->shipment_link > 0) {
                    $shipment_2_data = $shipment->getShipment($shipment_data->shipment_link);

                    if ($shipment_2_data->shipment_temp > 0) {
                        $shipment_temp_data = $shipment_temp->getShipment($shipment_2_data->shipment_temp);



                        $data_shipment = array(



                            'shipment_temp_status' => 0,



                            'shipment_ton_use' => $shipment_temp_data->shipment_ton_use-$shipment_2_data->shipment_ton_net,



                        );



                        $shipment_temp->updateShipment($data_shipment,array('shipment_temp_id'=>$shipment_2_data->shipment_temp));

                    }
                }*/



                $costs = $shipment_cost->getAllShipment(array('where'=>'shipment = '.$_POST['data']));

                foreach ($costs as $cost) {

                    $debit->queryDebit('DELETE FROM debit WHERE shipment_cost = '.$cost->shipment_cost_id);

                    $vat->queryVAT('DELETE FROM vat WHERE shipment_cost = '.$cost->shipment_cost_id);

                    $shipment_cost->deleteShipment($cost->shipment_cost_id);

                }



                $shipment_cost->queryShipment('DELETE FROM shipment_cost WHERE shipment = '.$_POST['data']);

                $debit->queryDebit('DELETE FROM debit WHERE shipment = '.$_POST['data']);

                $vat->queryVAT('DELETE FROM vat WHERE shipment = '.$_POST['data']);

                //$shipment->queryShipment('DELETE FROM shipment WHERE shipment_id = '.$shipment_data->shipment_link);

                date_default_timezone_set("Asia/Ho_Chi_Minh"); 



                        $filename = "action_logs.txt";



                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|shipment|"."\n"."\r\n";



                        



                        $fh = fopen($filename, "a") or die("Could not open log file.");



                        fwrite($fh, $text) or die("Could not write file!");



                        fclose($fh);







                return $shipment->deleteShipment($_POST['data']);



            }



            



        }



    }







    



    







    public function view() {



        $this->view->disableLayout();







        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        



        $this->view->data['lib'] = $this->lib;







        $id = $this->registry->router->param_id;



        $round = $this->registry->router->page;







        $thang = $this->registry->router->order_by;



        $nam = $this->registry->router->order;







        $this->view->data['id'] = $id;



        $this->view->data['round'] = $round;



        $this->view->data['thang'] = $thang;



        $this->view->data['nam'] = $nam;







        $batdau = strtotime('01-'.$thang.'-'.$nam);



        $ketthuc = date('t-m-Y',strtotime('01-'.$thang.'-'.$nam));



        $ngayketthuc = strtotime(date('d-m-Y', strtotime($ketthuc. ' + 1 days')));




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




        $join = array('table'=>'customer, vehicle, cont_unit, steersman','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND cont_unit=cont_unit_id AND steersman = steersman_id');





        $shipment_model = $this->model->get('shipmentModel');




        $data = array(



            'where' => 'vehicle = '.$id.' AND shipment_round <= '.$round.' AND shipment_date >= '.$batdau.' AND shipment_date < '.$ngayketthuc,



            'order_by'=> 'shipment_date ASC, shipment_round ASC',



            );



        

        $shipments = $shipment_model->getAllShipment($data,$join);



        $this->view->data['shipments'] = $shipments;


        $road_model = $this->model->get('roadModel');

       

        $road_data = array();

        

        $customer_sub_model = $this->model->get('customersubModel');

        $export_stock_model = $this->model->get('exportstockModel');

        $shipment_cost_model = $this->model->get('shipmentcostModel');

        $customer_types = array();

        $export_stocks = array();

        

        $loan_shipment_data = array();

        $v = array();


        foreach ($shipments as $ship) {

            $month = intval(date('m',$ship->shipment_date));

            $year = date('Y',$ship->shipment_date);

           $v[$ship->vehicle.$ship->shipment_round.$month.$year] = isset($v[$ship->vehicle.$ship->shipment_round.$month.$year])?($v[$ship->vehicle.$ship->shipment_round.$month.$year] + 1) : (0+1) ;


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



        $this->view->data['arr'] = $v;







        $this->view->show('shipment/view');



    }







    public function import(){



        $this->view->disableLayout();



        header('Content-Type: text/html; charset=utf-8');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) || json_decode($_SESSION['user_permission_action'])->shipment != "shipment") {



            return $this->view->redirect('user/login');



        }



        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES['import']['name'] != null) {







            require("lib/Classes/PHPExcel/IOFactory.php");



            require("lib/Classes/PHPExcel.php");







            $warehouse = $this->model->get('warehouseModel');



            $shipment = $this->model->get('shipmentModel');



            $road = $this->model->get('roadModel');



            $customer = $this->model->get('customerModel');



            $vehicle = $this->model->get('vehicleModel');







            $objPHPExcel = new PHPExcel();



            // Set properties



            if (pathinfo($_FILES['import']['name'], PATHINFO_EXTENSION) == "xls") {



                $objReader = PHPExcel_IOFactory::createReader('Excel5');



            }



            else if (pathinfo($_FILES['import']['name'], PATHINFO_EXTENSION) == "xlsx") {



                $objReader = PHPExcel_IOFactory::createReader('Excel2007');



            }



            



            $objReader->setReadDataOnly(false);







            $objPHPExcel = $objReader->load($_FILES['import']['tmp_name']);



            $objWorksheet = $objPHPExcel->getActiveSheet();







            







            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10



            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'







            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5







            //var_dump($objWorksheet->getMergeCells());die();



            



             







                for ($row = 3; $row <= $highestRow; ++ $row) {



                    $val = array();



                    for ($col = 0; $col < $highestColumnIndex; ++ $col) {



                        $cell = $objWorksheet->getCellByColumnAndRow($col, $row);



                        // Check if cell is merged



                        foreach ($objWorksheet->getMergeCells() as $cells) {



                            if ($cell->isInRange($cells)) {



                                $currMergedCellsArray = PHPExcel_Cell::splitRange($cells);



                                $cell = $objWorksheet->getCell($currMergedCellsArray[0][0]);



                                break;



                                



                            }



                        }



                        $val[] = $cell->getValue();







                        //$val[] = $cell->getCalculatedValue();



                        //here's my prob..



                        //echo $val;



                    }



                    if ($val[0] != null && $val[1] != null && $val[4] != null && $val[5] != null) {







                            if(!$warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[4])))) {



                                $warehouse_data = array(



                                'warehouse_name' => trim($val[4]),



                                'warehouse_cont' => trim($val[16]),



                                'warehouse_ton' => (trim($val[17])>0)?(trim($val[17])/trim($val[11])):0,



                                );



                                $warehouse->createWarehouse($warehouse_data);







                                $warehouse_from = $warehouse->getLastWarehouse()->warehouse_id;



                            }



                            else if($warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[4])))){



                                $warehouse_from = $warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[4])))->warehouse_id;



                                $warehouse_cont = $warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[4])))->warehouse_cont;



                                $warehouse_ton = $warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[4])))->warehouse_ton;



                                $warehouse_data = array(



                                'warehouse_cont' => $warehouse_cont==0?trim($val[16]):$warehouse_cont,



                                'warehouse_ton' => $warehouse_ton==0?((trim($val[17])>0)?(trim($val[17])/trim($val[11])):0):$warehouse_ton,



                                );







                                $warehouse->updateWarehouse($warehouse_data,array('warehouse_id'=>$warehouse_from));



                                



                            }







                            if(!$warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[5])))) {



                                $warehouse_data = array(



                                'warehouse_name' => trim($val[5]),



                                'warehouse_cont' => trim($val[18]),



                                'warehouse_ton' => (trim($val[19])>0)?(trim($val[19])/trim($val[11])):0,



                                );



                                $warehouse->createWarehouse($warehouse_data);







                                $warehouse_to = $warehouse->getLastWarehouse()->warehouse_id;



                            }



                            else if($warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[5])))){



                                $warehouse_to = $warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[5])))->warehouse_id;



                                $warehouse_cont = $warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[5])))->warehouse_cont;



                                $warehouse_ton = $warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[5])))->warehouse_ton;



                                $warehouse_data = array(



                                'warehouse_cont' => $warehouse_cont==0?trim($val[18]):$warehouse_cont,



                                'warehouse_ton' => $warehouse_ton==0?((trim($val[19])>0)?(trim($val[19])/trim($val[11])):0):$warehouse_ton,



                                );







                                $warehouse->updateWarehouse($warehouse_data,array('warehouse_id'=>$warehouse_to));



                            }







                            if(!$vehicle->getVehicleByWhere(array('vehicle_number'=>trim($val[1])))) {



                                $vehicle_data = array(



                                'vehicle_number' => trim($val[1]),



                                'driver_name' => trim($val[2]),



                                );



                                $vehicle->createVehicle($vehicle_data);







                                $vehicle_id = $vehicle->getLastVehicle()->vehicle_id;



                            }



                            else if($vehicle->getVehicleByWhere(array('vehicle_number'=>trim($val[1])))){



                                $vehicle_id = $vehicle->getVehicleByWhere(array('vehicle_number'=>trim($val[1])))->vehicle_id;



                                







                            }







                            if(!$customer->getCustomerByWhere(array('customer_name'=>trim($val[7])))) {



                                $customer_data = array(



                                'customer_name' => trim($val[7]),



                                'customer_contact' => trim($val[8]),



                                'customer_phone' => trim($val[9]),



                                );



                                $customer->createCustomer($customer_data);







                                $customer_id = $customer->getLastCustomer()->customer_id;



                            }



                            else if($customer->getCustomerByWhere(array('customer_name'=>trim($val[7])))){



                                $customer_id = $customer->getCustomerByWhere(array('customer_name'=>trim($val[7])))->customer_id;



                                







                            }







                            if( trim($val[6]) == "Rỗng"){



                                $way = 0;



                            }



                            else if( trim($val[6]) == "Lên"){



                                $way = 1;



                            }



                            else if( trim($val[6]) == "Xuống"){



                                $way = 2;



                            }



                            else if( trim($val[6]) == "Bằng"){



                                $way = 3;



                            }



                            else if( trim($val[6]) == "ĐN-QN"){



                                $way = 4;



                            }







                            if(!$road->getRoadByWhere(array('road_from'=>trim($warehouse_from),'road_to'=>trim($warehouse_to)))) {



                                



                                



                                



                                $road_data = array(



                                'road_from' => $warehouse_from,



                                'road_to' => $warehouse_to,



                                'way' => $way,



                                'road_km' => trim($val[12]),



                                'road_oil' => trim($val[13]),



                                'bridge_cost' => trim($val[20]),



                                'police_cost' => trim($val[21]),



                                'tire_cost' => trim($val[22]),



                                'road_time' => trim($val[15]),



                                );



                                $road->createRoad($road_data);







                                $road_id = $road->getLastRoad()->road_id;



                            }



                            else if($road->getRoadByWhere(array('road_from'=>trim($warehouse_from),'road_to'=>trim($warehouse_to)))){



                                $road_id = $road->getRoadByWhere(array('road_from'=>trim($warehouse_from),'road_to'=>trim($warehouse_to)))->road_id;



                                $road_data = array(



                                'way' => $way,



                                'road_km' => trim($val[12]),



                                'road_oil' => trim($val[13]),



                                'bridge_cost' => trim($val[20]),



                                'police_cost' => trim($val[21]),



                                'tire_cost' => trim($val[22]),



                                'road_time' => trim($val[15]),



                                );



                                $road->updateRoad($road_data,array('road_id'=>$road_id));



                            }











                            $warehouses = $warehouse->getAllWarehouse(array('where'=>'warehouse_id = '.$warehouse_from.' OR warehouse_id = '.$warehouse_to));



                            $roads = $road->getAllRoad(array('where'=>'road_from = '.$warehouse_from.' AND road_to = '.$warehouse_to));







                            $boiduong_cont = 0;



                            $boiduong_tan = 0;



                            $doanhthu = 0;



                            $chiphi = 0;



                            $loinhuan = 0;







                            $giadau = 19; //gia dau hien tai







                            if ($warehouses) {



                                foreach ($warehouses as $warehouse_datas) {



                                    if ($warehouse_datas->warehouse_cont != 0) {



                                        $boiduong_cont += $warehouse_datas->warehouse_cont;



                                    }



                                    if ($warehouse_datas->warehouse_ton != 0){



                                        $boiduong_tan += trim($val[11]) * $warehouse_datas->warehouse_ton;



                                    }



                                }



                            }



                            $boiduong = 0;



                            if ($roads) {



                                foreach ($roads as $road_datas) {



                                    $boiduong += ($road_datas->way == 0)?0:($boiduong_tan+$boiduong_cont);



                                    $chiphi += ($road_datas->road_time*3000000)+$boiduong+$road_datas->police_cost+$road_datas->bridge_cost+$road_datas->tire_cost+($road_datas->road_oil*$giadau);



                                    



                                }



                            }







                            //$chiphi = $chiphi+$data['cost_add'];







                            $doanhthu = trim($val[10])*trim($val[11]);



                            $loinhuan = $doanhthu - $chiphi;







                            



/*



                            $ngay_ship = substr(trim($val[3]), 3,2);



                            $thang_ship = substr(trim($val[3]), 0,2);



                            $nam_ship = substr(trim($val[3]), 6,4);







                            $ngaythang = $ngay_ship.'-'.$thang_ship.'-'.$nam_ship;*/







                            $ngaythang = PHPExcel_Shared_Date::ExcelToPHP(trim($val[3]));                                      



                            $ngaythang = $ngaythang-3600;







                            if ($shipment->getShipmentByWhere(array('shipment_from'=>$warehouse_from,'shipment_to'=>$warehouse_to,'customer'=>$customer_id,'vehicle'=>$vehicle_id,'shipment_date'=>$ngaythang,'shipment_round'=>trim($val[0])))) {



                                $shipment_id = $shipment->getShipmentByWhere(array('shipment_from'=>$warehouse_from,'shipment_to'=>$warehouse_to,'customer'=>$customer_id,'vehicle'=>$vehicle_id,'shipment_date'=>$ngaythang,'shipment_round'=>trim($val[0])))->shipment_id;



                                



                                $data_shipment = array(



                        



                                'shipment_from' => $warehouse_from,



                                'shipment_to' => $warehouse_to,



                                'vehicle' => $vehicle_id,



                                'customer' => $customer_id,



                                'shipment_ton' => trim($val[11]),



                                'shipment_charge' => trim($val[10]),



                                'oil_add' => trim($val[14]),



                                'cost_add' => trim($val[23]),



                                'oil_cost' => round(19000/1.1),



                                'shipment_date' => $ngaythang,



                                'shipment_round' => trim($val[0]),



                                'shipment_create_user' => $_SESSION['userid_logined'],



                                'approve' => 1,



                                );



                                $data_shipment['shipment_revenue'] = $doanhthu;



                                $data_shipment['shipment_cost'] = $chiphi;



                                $data_shipment['shipment_profit'] = $loinhuan;







                                $shipment->updateShipment($data_shipment,array('shipment_id'=>$shipment_id));







                            }



                            else if (!$shipment->getShipmentByWhere(array('shipment_from'=>$warehouse_from,'shipment_to'=>$warehouse_to,'customer'=>$customer_id,'vehicle'=>$vehicle_id,'shipment_date'=>$ngaythang))) {



                                



                                



                                $data_shipment = array(



                        



                                'shipment_from' => $warehouse_from,



                                'shipment_to' => $warehouse_to,



                                'vehicle' => $vehicle_id,



                                'customer' => $customer_id,



                                'shipment_ton' => trim($val[11]),



                                'shipment_charge' => trim($val[10]),



                                'oil_add' => trim($val[14]),



                                'cost_add' => trim($val[23]),



                                'oil_cost' => round(19000/1.1),



                                'shipment_date' => $ngaythang,



                                'shipment_round' => trim($val[0]),



                                'shipment_create_user' => $_SESSION['userid_logined'],



                                'approve' => 1,



                                );







                                $data_shipment['shipment_revenue'] = $doanhthu;



                                $data_shipment['shipment_cost'] = $chiphi;



                                $data_shipment['shipment_profit'] = $loinhuan;











                                $shipment->createShipment($data_shipment);







                            }



                        



                    }



                    



                    //var_dump($this->getNameDistrict($this->lib->stripUnicode($val[1])));



                    // insert











                }



                //return $this->view->redirect('transport');



            



            return $this->view->redirect('shipment');



        }



        $this->view->show('shipment/import');







    }







    public function report() {



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) || json_decode($_SESSION['user_permission_action'])->shipment != "shipment") {



            return $this->view->redirect('user/login');



        }



        $this->view->data['lib'] = $this->lib;



        $this->view->data['title'] = 'Kết quả hoạt động của đội xe';







        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;



            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;



        }



        else{



            $batdau = '01-'.date('m-Y');



            $ketthuc = date('d-m-Y'); //cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')).'-'.date('m-Y');



        }



        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));



        $data = null;



        /*if ($_SESSION['role_logined'] == 3) {



            $data = array('where'=>'shipment_create_user = '.$_SESSION['userid_logined']);



        }*/



        $vehicle_model = $this->model->get('vehicleModel');



        $join_v = array('table'=>'shipment','where'=>'vehicle.vehicle_id = shipment.vehicle GROUP BY vehicle_id');



        $vehicles = $vehicle_model->getAllVehicle($data,$join_v);







        $shipment_model = $this->model->get('shipmentModel');



        $join = array('table'=>'customer, vehicle','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle');



        $data = array(



            'where'=>'shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc),



        );







        if ($_SESSION['role_logined'] == 3) {



            if( (strtotime($batdau) < strtotime('04-06-2015')) && (strtotime($ketthuc) > strtotime('04-06-2015')) )



                $data['where'] = '( (shipment_date >= '.strtotime($batdau).' AND shipment_date <= '.strtotime('04-06-2015').') OR (shipment_date > '.strtotime('04-06-2015').' AND shipment_date < '.strtotime($ngayketthuc).' AND shipment_create_user = '.$_SESSION['userid_logined'].') )';



            else if( strtotime($batdau) > strtotime('04-06-2015') )



                $data['where'] = $data['where'].' AND shipment_create_user = '.$_SESSION['userid_logined'];



        }







        /*if ($_SESSION['role_logined'] == 3) {



            $data['where'] = $data['where'].' AND shipment_create_user = '.$_SESSION['userid_logined'];



        }*/







        $shipments = $shipment_model->getAllShipment($data,$join);







        $doanhthu = array();



        $chiphiphatsinh = array();







        $warehouse_model = $this->model->get('warehouseModel');



        







        $road_model = $this->model->get('roadModel');







        $warehouse_data = array();



        $road_data = array();











        foreach ($shipments as $shipment) {



            $doanhthu[$shipment->vehicle] = isset($doanhthu[$shipment->vehicle])?($doanhthu[$shipment->vehicle]+$shipment->shipment_revenue+$shipment->revenue_other) : (0+$shipment->shipment_revenue+$shipment->revenue_other);



            $chiphiphatsinh[$shipment->vehicle] = isset($chiphiphatsinh[$shipment->vehicle])?($chiphiphatsinh[$shipment->vehicle]+(($shipment->approve==1)?$shipment->cost_add:0)) : (0+(($shipment->approve==1)?$shipment->cost_add:0));







            $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $shipment->route).'")'));



            



           



            $check_rong = 0;



            



            foreach ($roads as $road) {



                $road_data['bridge_cost'][$shipment->vehicle] = isset($road_data['bridge_cost'][$shipment->vehicle])?($road_data['bridge_cost'][$shipment->vehicle]+$road->bridge_cost):0+$road->bridge_cost;



                $road_data['police_cost'][$shipment->vehicle] = isset($road_data['police_cost'][$shipment->vehicle])?($road_data['police_cost'][$shipment->vehicle]+$road->police_cost):0+$road->police_cost;



                $road_data['oil_cost'][$shipment->vehicle] = isset($road_data['oil_cost'][$shipment->vehicle])?($road_data['oil_cost'][$shipment->vehicle]+($road->road_oil*$shipment->oil_cost)):0+($road->road_oil*$shipment->oil_cost);



                $road_data['road_time'][$shipment->vehicle] = isset($road_data['road_time'][$shipment->vehicle])?($road_data['road_time'][$shipment->vehicle]+$road->road_time):0+$road->road_time;



                $road_data['tire_cost'][$shipment->vehicle] = isset($road_data['tire_cost'][$shipment->vehicle])?($road_data['tire_cost'][$shipment->vehicle]+$road->tire_cost):0+$road->tire_cost;







                $chek_rong = ($road->way == 0)?1:0;



            }











            $warehouse = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$shipment->shipment_from.' OR warehouse_code = '.$shipment->shipment_to.') AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));



        







            $boiduong_cont = 0;



            $boiduong_tan = 0;







            



            foreach ($warehouse as $warehouse) {



                $tan = explode(".",$shipment->shipment_ton);



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



                    if ($shipment->shipment_ton > 0) {



                        $boiduong_cont += $warehouse->warehouse_add;



                    }



                }



            }



            $warehouse_data['boiduong_cn'][$shipment->vehicle] = isset($warehouse_data['boiduong_cn'][$shipment->vehicle])?($warehouse_data['boiduong_cn'][$shipment->vehicle]+$boiduong_cont+$boiduong_tan):0+$boiduong_cont+$boiduong_tan;



            



            if ($shipment->sub_driver == 1) {



                $warehouse_data['sub_driver']['boiduong_cn'][$shipment->vehicle] = isset($warehouse_data['sub_driver']['boiduong_cn'][$shipment->vehicle])?($warehouse_data['sub_driver']['boiduong_cn'][$shipment->vehicle]+$boiduong_cont+$boiduong_tan):0+$boiduong_cont+$boiduong_tan;    



            }







            if ($shipment->sub_driver2 == 1) {



                $warehouse_data['sub_driver']['boiduong_cn'][$shipment->vehicle] = isset($warehouse_data['sub_driver']['boiduong_cn'][$shipment->vehicle])?($warehouse_data['sub_driver']['boiduong_cn'][$shipment->vehicle]+$boiduong_cont+$boiduong_tan):0+$boiduong_cont+$boiduong_tan;    



            }







           



            



        }







        $this->view->data['vehicles'] = $vehicles;



        $this->view->data['warehouse'] = $warehouse_data;



        $this->view->data['road'] = $road_data;







        $this->view->data['doanhthu'] = $doanhthu;



        $this->view->data['chiphiphatsinh'] = $chiphiphatsinh;







        $this->view->data['batdau'] = $batdau;



        $this->view->data['ketthuc'] = $ketthuc;



        $this->view->show('shipment/report');



    }







    function export(){



        $this->view->disableLayout();



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->shipment) || json_decode($_SESSION['user_permission_action'])->shipment != "shipment") {



            return $this->view->redirect('user/login');



        }



        $xe = $this->registry->router->order_by;



        $vong = $this->registry->router->order;



        $batdau = date('d-m-Y',$this->registry->router->param_id);



        $ketthuc = date('d-m-Y',$this->registry->router->page);



        $ngayketthuc = strtotime(date('d-m-Y', strtotime($ketthuc. ' + 1 days')));




        $contunit_model = $this->model->get('contunitModel');
        $cost_list_model = $this->model->get('costlistModel');

        $cont_units = $contunit_model->getAllUnit();
        $loan_units = $cost_list_model->getAllCost(array('where'=>'cost_list_type = 8'));


        $place_model = $this->model->get('placeModel');

        $place_data = array();

        $places = $place_model->getAllPlace();


        foreach ($places as $place) {

                $place_data['place_id'][$place->place_id] = $place->place_id;

                $place_data['place_name'][$place->place_id] = $place->place_name;

        }

        $place = $place_data;

        $romooc_model = $this->model->get('romoocModel');

        $romooc_data = array();

        $romoocs = $romooc_model->getAllVehicle();


        foreach ($romoocs as $romooc) {

                $romooc_data['romooc_id'][$romooc->romooc_id] = $romooc->romooc_id;

                $romooc_data['romooc_number'][$romooc->romooc_id] = $romooc->romooc_number;

        }



        $warehouse_model = $this->model->get('warehouseModel');



        $warehouse_data = array();




        $join = array('table'=>'customer, vehicle, cont_unit, steersman','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND cont_unit=cont_unit_id AND steersman = steersman_id');





        $shipment_model = $this->model->get('shipmentModel');




        $data = array(



            'where' => 'shipment_date >= '.strtotime($batdau).' AND shipment_date < '.$ngayketthuc,



            'order_by'=> 'shipment_date ASC, shipment_round ASC',



            );

        if($xe != 0){

            $data['where'] = $data['where'].' AND vehicle = '.$xe;

        }

        if($vong != 0){

            $data['where'] = $data['where'].' AND shipment_round = '.$vong;
        }


        

        $shipments = $shipment_model->getAllShipment($data,$join);



        $road_model = $this->model->get('roadModel');

       

        $road_data = array();

        

        $customer_sub_model = $this->model->get('customersubModel');


        $shipment_cost_model = $this->model->get('shipmentcostModel');

        $customer_types = array();

        $export_stocks = array();

        

        $loan_shipment_data = array();

        $v = array();


            require("lib/Classes/PHPExcel/IOFactory.php");



            require("lib/Classes/PHPExcel.php");





            $objPHPExcel = new PHPExcel();




            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)



            $objPHPExcel->setActiveSheetIndex($index_worksheet)



               ->setCellValue('A1', 'STT')

               ->setCellValue('B1', 'Số DO')

               ->setCellValue('C1', 'Ngày nhận')

               ->setCellValue('D1', 'Ngày giao')

               ->setCellValue('E1', 'Số lượng nhận');


            $dvt = "E";
            foreach ($cont_units as $cont) {
                $objPHPExcel->setActiveSheetIndex($index_worksheet)
                ->setCellValue($dvt.'2', $cont->cont_unit_name);

                $dvt++;
            }

            $ascii = ord($dvt);
            $sln = chr($ascii -1);

            $sln2 = $dvt;

            $objPHPExcel->setActiveSheetIndex($index_worksheet)
               ->setCellValue($dvt.'1', 'Số lượng giao');

            foreach ($cont_units as $cont) {
                $objPHPExcel->setActiveSheetIndex($index_worksheet)
                ->setCellValue($dvt.'2', $cont->cont_unit_name);

                $dvt++;
            }

            $ascii = ord($dvt);
            $slg = chr($ascii -1);

            $gv = $dvt;


            $objPHPExcel->setActiveSheetIndex($index_worksheet)


               ->setCellValue(($dvt++).'1', 'Giờ vào')

               ->setCellValue(($dvt++).'1', 'Giờ ra')

               ->setCellValue(($dvt++).'1', 'Xe')

               ->setCellValue(($dvt++).'1', 'Mooc')

               ->setCellValue(($dvt++).'1', 'Tài xế')

               ->setCellValue(($dvt++).'1', 'Nơi nhận')

               ->setCellValue(($dvt++).'1', 'Nơi giao')

               ->setCellValue(($dvt++).'1', 'Mặt hàng')

               ->setCellValue(($dvt++).'1', 'Khách hàng')

               ->setCellValue(($dvt++).'1', 'Chiều dài')

               ->setCellValue(($dvt++).'1', 'Dầu thực lãnh')

               ->setCellValue(($dvt++).'1', 'Dầu định mức')

               ->setCellValue(($dvt++).'1', 'Chi phí đi đường')

               ->setCellValue(($dvt++).'1', 'Cầu đường')

               ->setCellValue(($dvt++).'1', 'Công an')

               ->setCellValue(($dvt++).'1', 'Bồi dưỡng')

               ->setCellValue($dvt.'1', 'Chi hộ');

            $ch = $dvt;
            $phatsinh = $dvt;

            foreach ($loan_units as $loan) {
                $objPHPExcel->setActiveSheetIndex($index_worksheet)
                ->setCellValue($phatsinh.'2', $loan->cost_list_name);

                $phatsinh++;
            }

            




            if ($shipments) {




                $hang = 3;



                $i=1;




                $kho_data = array();



                foreach ($shipments as $ship) {



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







                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );



                     $objPHPExcel->setActiveSheetIndex($index_worksheet)



                        ->setCellValue('A' . $hang, $i++)

                        ->setCellValueExplicit('B' . $hang, $ship->bill_number)

                        ->setCellValue('C' . $hang, $this->lib->hien_thi_ngay_thang($ship->bill_receive_date))

                        ->setCellValue('D' . $hang, $this->lib->hien_thi_ngay_thang($ship->bill_delivery_date));


                        $dvt = "E";
                        foreach ($cont_units as $cont) {
                            $ton = $ship->bill_receive_unit==$cont->cont_unit_id?$ship->bill_receive_ton:null;

                            $objPHPExcel->setActiveSheetIndex($index_worksheet)
                            ->setCellValue($dvt.$hang, $ton);

                            $dvt++;
                        }

                        foreach ($cont_units as $cont) {
                            $ton = $ship->bill_delivery_unit==$cont->cont_unit_id?$ship->bill_delivery_ton:null;

                            $objPHPExcel->setActiveSheetIndex($index_worksheet)
                            ->setCellValue($dvt.$hang, $ton);

                            $dvt++;
                        }


                        $objPHPExcel->setActiveSheetIndex($index_worksheet)


                           ->setCellValue(($dvt++).$hang, date('H:i:s',$ship->bill_in))

                           ->setCellValue(($dvt++).$hang, date('H:i:s',$ship->bill_out))

                           ->setCellValue(($dvt++).$hang, $ship->vehicle_number)

                           ->setCellValue(($dvt++).$hang, (isset($romooc_data['romooc_number'][$ship->romooc])?$romooc_data['romooc_number'][$ship->romooc]:null))

                           ->setCellValue(($dvt++).$hang, $ship->steersman_name)

                           ->setCellValue(($dvt++).$hang, ($ship->shipment_from==$place['place_id'][$ship->shipment_from]?$place['place_name'][$ship->shipment_from]:null))

                           ->setCellValue(($dvt++).$hang, ($ship->shipment_to==$place['place_id'][$ship->shipment_to]?$place['place_name'][$ship->shipment_to]:null))

                           ->setCellValue(($dvt++).$hang, $customer_types[$ship->shipment_id])

                           ->setCellValue(($dvt++).$hang, $ship->customer_name)

                           ->setCellValue(($dvt++).$hang, (isset($road_data['road_km'][$ship->shipment_id])?$road_data['road_km'][$ship->shipment_id]:null))

                           ->setCellValue(($dvt++).$hang, $ship->shipment_oil)

                           ->setCellValue(($dvt++).$hang, (($ship->shipment_road_oil>0?$ship->shipment_road_oil:(isset($road_data['road_oil'][$ship->shipment_id])?$road_data['road_oil'][$ship->shipment_id]:0))+$ship->shipment_road_oil_add))

                           ->setCellValue(($dvt++).$hang, $ship->shipment_road_add)

                           ->setCellValue(($dvt++).$hang, (isset($road_data['bridge_cost'][$ship->shipment_id])?$road_data['bridge_cost'][$ship->shipment_id]:null))

                           ->setCellValue(($dvt++).$hang, (isset($road_data['police_cost'][$ship->shipment_id])?$road_data['police_cost'][$ship->shipment_id]:null))

                           ->setCellValue(($dvt++).$hang, (isset($warehouse_data['boiduong_cn'][$ship->shipment_id])?$warehouse_data['boiduong_cn'][$ship->shipment_id]:null));

                        $phatsinh = $dvt;

                        foreach ($loan_units as $loan) {
                            $chiho = isset($loan_shipment_data[$ship->shipment_id][$loan->cost_list_id])?$loan_shipment_data[$ship->shipment_id][$loan->cost_list_id]:null;

                            $objPHPExcel->setActiveSheetIndex($index_worksheet)
                            ->setCellValue($phatsinh.$hang, $chiho);

                            $phatsinh++;
                        }


                     $hang++;





                  }


          }







            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();







            $highestRow ++;





            $objPHPExcel->getActiveSheet()->getStyle('A1:'.$phatsinh.$hang)->applyFromArray(

                array(

                    

                    'borders' => array(

                        'allborders' => array(

                          'style' => PHPExcel_Style_Border::BORDER_THIN

                        )

                    )

                )

            );


            $objPHPExcel->getActiveSheet()->mergeCells('A1:A2');

            $objPHPExcel->getActiveSheet()->mergeCells('B1:B2');

            $objPHPExcel->getActiveSheet()->mergeCells('C1:C2');

            $objPHPExcel->getActiveSheet()->mergeCells('D1:D2');

            $objPHPExcel->getActiveSheet()->mergeCells('E1:'.$sln.'1');

            $objPHPExcel->getActiveSheet()->mergeCells($sln2.'1:'.$slg.'1');

            for ($p=$gv; $p < $ch; $p++) { 
                $objPHPExcel->getActiveSheet()->mergeCells($p.'1:'.$p.'2');
            }

            $objPHPExcel->getActiveSheet()->mergeCells($ch.'1:'.$phatsinh.'1');


            $objPHPExcel->getActiveSheet()->getStyle('M3:'.$phatsinh.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");



            $objPHPExcel->getActiveSheet()->getStyle('A1:'.$phatsinh.'2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A1:'.$phatsinh.'2')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A1:'.$phatsinh.'2')->getFont()->setBold(true);



            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);


            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);









            // Set properties



            $objPHPExcel->getProperties()->setCreator("TCMT")



                            ->setLastModifiedBy($_SESSION['user_logined'])



                            ->setTitle("Shipment Report")



                            ->setSubject("Shipment Report")



                            ->setDescription("Shipment Report.")



                            ->setKeywords("Shipment Report")



                            ->setCategory("Shipment Report");



            $objPHPExcel->getActiveSheet()->setTitle("Thong tin lo hang");







            $objPHPExcel->getActiveSheet()->freezePane('A3');



            $objPHPExcel->setActiveSheetIndex(0);















            







            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');







            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");



            header("Content-Disposition: attachment; filename= TỔNG HỢP PHIẾU VẬN CHUYỂN.xlsx");



            header("Cache-Control: max-age=0");



            ob_clean();



            $objWriter->save("php://output");



        



    }







    function viewexport(){



        $this->view->disableLayout();



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }







            $id = $this->registry->router->param_id;



        $round = $this->registry->router->page;







        $thang = $this->registry->router->order_by;



        $nam = $this->registry->router->order;







        $batdau = strtotime('30-'.($thang-1).'-'.$nam);



        $ketthuc = strtotime('29-'.$thang.'-'.$nam);







        $shipment_model = $this->model->get('shipmentModel');







        $join = array('table'=>'customer, vehicle, cont_unit','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND cont_unit = cont_unit_id');



        $data = array(



            'where' => 'vehicle = '.$id.' AND shipment_round = '.$round.' AND shipment_date >= '.$batdau.' AND shipment_date <= '.$ketthuc,



            );



        



        $shipments = $shipment_model->getAllShipment($data,$join);







        $warehouse_model = $this->model->get('warehouseModel');



        







        $road_model = $this->model->get('roadModel');







        $warehouse_data = array();



        $road_data = array();









        $place_model = $this->model->get('placeModel');



        $place_data = array();



        $places = $place_model->getAllPlace();





        foreach ($places as $place) {



                $place_data['place_id'][$place->place_id] = $place->place_id;



                $place_data['place_name'][$place->place_id] = $place->place_name;



        }



        $this->view->data['place'] = $place_data;













            require("lib/Classes/PHPExcel/IOFactory.php");



            require("lib/Classes/PHPExcel.php");







            $objPHPExcel = new PHPExcel();







            







            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)



            $objPHPExcel->setActiveSheetIndex($index_worksheet)



               ->setCellValue('A1', 'STT')



               ->setCellValue('B1', 'Xe')



               ->setCellValue('C1', 'Ngày')



               ->setCellValue('D1', 'Kho đi')



               ->setCellValue('E1', 'Kho đến')



               ->setCellValue('F1', 'Khách hàng')



               ->setCellValue('G1', 'Chiều')



               ->setCellValue('H1', 'KM')



               ->setCellValue('I1', 'Định mức dầu')



               ->setCellValue('J1', 'Ứng dầu')



               ->setCellValue('K1', 'Dầu còn lại');



               







            







            



            



            







            if ($shipments) {







                $hang = 2;



                $i=1;







                foreach ($shipments as $shipment) {



                    $r_join = array('table'=>'oil','where'=>'road.way = oil.oil_id');



                    $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $shipment->route).'")'),$r_join);



                    



                   



                    $chek_rong = 0;



                    



                    foreach ($roads as $road) {



                        $road_data['road_id'][$shipment->shipment_id] = $road->road_id;



                        $road_data['way'][$shipment->shipment_id] = $road->way;



                        $road_data['way_name'][$shipment->shipment_id] = $road->way;



                        $road_data['bridge_cost'][$shipment->shipment_id] = isset($road_data['bridge_cost'][$shipment->shipment_id])?$road_data['bridge_cost'][$shipment->shipment_id]+$road->bridge_cost*$check_sub:$road->bridge_cost*$check_sub;



                        $road_data['police_cost'][$shipment->shipment_id] = isset($road_data['police_cost'][$shipment->shipment_id])?$road_data['police_cost'][$shipment->shipment_id]+($road->police_cost)*$check_sub:($road->police_cost)*$check_sub;



                        $road_data['tire_cost'][$shipment->shipment_id] = isset($road_data['tire_cost'][$shipment->shipment_id])?$road_data['tire_cost'][$shipment->shipment_id]+($road->tire_cost)*$check_sub:($road->tire_cost)*$check_sub;



                        $road_data['oil_cost'][$shipment->shipment_id] = isset($road_data['oil_cost'][$shipment->shipment_id])?$road_data['oil_cost'][$shipment->shipment_id]+($road->road_oil*round($shipment->oil_cost*1.1))*$check_sub:($road->road_oil*round($shipment->oil_cost*1.1))*$check_sub;



                        $road_data['road_oil'][$shipment->shipment_id] = isset($road_data['road_oil'][$shipment->shipment_id])?$road_data['road_oil'][$shipment->shipment_id]+($road->road_oil)*$check_sub:($road->road_oil)*$check_sub;



                        $road_data['road_time'][$shipment->shipment_id] = isset($road_data['road_time'][$shipment->shipment_id])?$road_data['road_time'][$shipment->shipment_id]+($road->road_time)*$check_sub:($road->road_time)*$check_sub;



                        $road_data['road_km'][$shipment->shipment_id] = isset($road_data['road_km'][$shipment->shipment_id])?$road_data['road_km'][$shipment->shipment_id]+$road->road_km:$road->road_km;







                        $chek_rong = ($road->way == 0)?1:0;







                    }







                    $warehouse = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$shipment->shipment_from.' OR warehouse_code = '.$shipment->shipment_to.') AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));



                







                    $boiduong_cont = 0;



                    $boiduong_tan = 0;







                    



                    foreach ($warehouse as $warehouse) {



                        



                            $warehouse_data['warehouse_id'][$warehouse->warehouse_code] = $warehouse->warehouse_code;



                            $warehouse_data['warehouse_name'][$warehouse->warehouse_code] = $warehouse->warehouse_name;





                            $trongluong = round($shipment->shipment_ton/1000,2);







                        if($chek_rong == 0){



                            if ($warehouse->warehouse_cont != 0) {



                                $boiduong_cont += $warehouse->warehouse_cont;



                            }



                            if ($warehouse->warehouse_ton != 0){



                                $boiduong_tan += $trongluong * $warehouse->warehouse_ton;



                            }



                        }



                        else{



                            if ($shipment->shipment_ton > 0) {



                                $boiduong_cont += $warehouse->warehouse_add;



                            }



                        }



                        



                    }



                    $warehouse_data['boiduong_cn'][$shipment->shipment_id] = $boiduong_cont+$boiduong_tan;



            











                $chieu  = $road_data['way_name'][$shipment->shipment_id];







                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );



                     $objPHPExcel->setActiveSheetIndex(0)



                        ->setCellValue('A' . $hang, $i++)



                        ->setCellValueExplicit('B' . $hang, $shipment->vehicle_number)



                        ->setCellValue('C' . $hang, $this->lib->hien_thi_ngay_thang($shipment->shipment_date))



                        ->setCellValue('D' . $hang, $place_data['place_name'][$shipment->shipment_from])



                        ->setCellValue('E' . $hang, $place_data['place_name'][$shipment->shipment_to])



                        ->setCellValue('F' . $hang, $shipment->customer_name)



                        ->setCellValue('G' . $hang, $chieu)



                        ->setCellValue('H' . $hang, $road_data['road_km'][$shipment->shipment_id])



                        ->setCellValue('I' . $hang, $road_data['road_oil'][$shipment->shipment_id])



                        ->setCellValue('J' . $hang, $shipment->oil_add)



                        ->setCellValue('K' . $hang, '=I'.$hang.'-J'.$hang);



                     $hang++;











                  }



              }







          







            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();







            $highestRow ++;











            //$objPHPExcel->getActiveSheet()->getStyle('H2:K'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");



            $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A1:K1')->getFont()->setBold(true);



            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(16);



            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);



            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);



            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);







            // Set properties



            $objPHPExcel->getProperties()->setCreator("TCMT")



                            ->setLastModifiedBy($_SESSION['user_logined'])



                            ->setTitle("Shipment Report")



                            ->setSubject("Shipment Report")



                            ->setDescription("Shipment Report.")



                            ->setKeywords("Shipment Report")



                            ->setCategory("Shipment Report");



            $objPHPExcel->getActiveSheet()->setTitle("Thong tin lo hang");







            $objPHPExcel->getActiveSheet()->freezePane('A1');



            $objPHPExcel->setActiveSheetIndex(0);















            







            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');







            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");



            header("Content-Disposition: attachment; filename= BẢNG LÔ HÀNG.xlsx");



            header("Cache-Control: max-age=0");



            ob_clean();



            $objWriter->save("php://output");



        



    }







    public function craw(){



        ini_set('max_execution_time', 100); //300 seconds = 5 minutes




        require('lib/simple_html_dom.php');



        //get link;



        $url ="http://www.petrolimex.com.vn"; 







        $content = $this->get_content_by_url($url); 



    



        $content = $this->get_content_by_tag($content,"<div id=\"vie_p5_PortletContent\">",$url); 







        $arr = array();







        $html = str_get_html($content);

        



        if (!$html) {



            return 0;



        }



        $string = $html->find('div.list-table',0)->children(5)->innertext;



        $dom = new DOMDocument;

        $dom->loadHTML($string);

        foreach($dom->getElementsByTagName('div') as $node)

        {

            $arr[] = $node->nodeValue;

        }





        return str_replace('.', '', $arr['1']);



        



    }



    /// functions lay tin



    public static function get_content_by_url($url){

        $ch = curl_init();
        curl_setopt ($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        $contents = curl_exec($ch);
        if (curl_errno($ch)) {
          echo curl_error($ch);
          echo "\n<br />";
          $contents = '';
        } else {
          curl_close($ch);
        }

        if (!is_string($contents) || !strlen($contents)) {
        echo "Failed to get contents.";
        $contents = '';
        }

        //curl_close($ch);

        return $contents;



    }



    



   



    public static function get_content_by_tag($content, $tag_and_more,$include_tag = true){



        $p = stripos($content,$tag_and_more,0);



        



        if($p===false) return "";



        $content=substr($content,$p);



        $p = stripos($content," ",0);



        if(abs($p)==0) return "";



        $open_tag = substr($content,0,$p);



        $close_tag = substr($open_tag,0,1)."/".substr($open_tag,1).">";



        



        $count_inner_tag = 0;



        $p_open_inner_tag = 1; 



        $p_close_inner_tag = 0;



        $count=1;



        do{



            $p_open_inner_tag = stripos($content,$open_tag,$p_open_inner_tag);



            $p_close_inner_tag = stripos($content,$close_tag,$p_close_inner_tag);



            $count++;



            if($p_close_inner_tag!==false) $p = $p_close_inner_tag;



            if($p_open_inner_tag!==false){



                if(abs($p_open_inner_tag)<abs($p_close_inner_tag)){



                    $count_inner_tag++;



                    $p_open_inner_tag++;



                }else{



                    $count_inner_tag--;



                    $p_close_inner_tag++;



                }



            }else{



                $count_inner_tag--;



                if($p_close_inner_tag>0) $p_close_inner_tag++;



            }



        }while($count_inner_tag>0);



        if($include_tag)



            return substr($content,0,$p+strlen($close_tag));



        else{



            $content = substr($content,0,$p);



            $p = stripos($content,">",0);



            return substr($content,$p+1);



        }



    }



   



    











}



?>