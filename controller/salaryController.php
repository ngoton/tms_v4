<?php

Class salaryController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Bảng lương tài xế';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;

            $trangthai = isset($_POST['sl_vehicle']) ? $_POST['sl_vehicle'] : null;

        }

        else{

           

            $batdau = date('m');

            

            $ketthuc = date('Y'); //cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')).'-'.date('m-Y');

            $trangthai = -1;

            

        }



        $dauthang = '01-'.$batdau.'-'.$ketthuc;

        $cuoithang = date('t-'.$batdau.'-'.$ketthuc);

        $ngayketthuc = date('d-m-Y', strtotime($cuoithang. ' + 1 days'));



        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle();

        foreach ($vehicles as $vehicle) {

            $vehicle_data[$vehicle->vehicle_id] = $vehicle->vehicle_number;

        }

        $this->view->data['vehicle_data'] = $vehicle_data;


        $d_join = array('table'=>'steersman','where'=>'steersman = steersman_id');
        $d_data = array(

            'where'=> 'end_work > '.strtotime($dauthang),

            'order_by'=>'steersman_name ASC',

        );

        if ($trangthai > 0) {

            $d_data['where'] .= ' AND steersman_id = '.$trangthai;

        }

        $driver_model = $this->model->get('driverModel');

        $drivers = $driver_model->getAllDriver($d_data,$d_join);

        $steersman_model = $this->model->get('steersmanModel');

        $steersmans_data = $steersman_model->getAllSteersman(array('order_by'=>'steersman_name ASC'));

        $this->view->data['steersmans_data'] = $steersmans_data;

        
        $s_data = array(

            'where'=> ' (steersman_end_time IS NULL OR steersman_end_time = 0 OR steersman_end_time > '.strtotime($dauthang).')',

            'order_by'=>'steersman_name ASC',

        );
        if ($trangthai > 0) {
            $s_data['where'] .= ' AND steersman_id = '.$trangthai;
        }

        $steersmans = $steersman_model->getAllSteersman($s_data);

        $this->view->data['steersmans'] = $steersmans;



        $shipment_model = $this->model->get('shipmentModel');

        $warehouse_model = $this->model->get('warehouseModel');

        $road_model = $this->model->get('roadModel');

        $bonus_model = $this->model->get('salarybonusModel');

        $shipment_bonuss = $bonus_model->getAllSalary(array('where'=>'start_time <= '.strtotime($dauthang).' AND end_time >= '.strtotime($cuoithang)));

        $thuongphat = array();
        foreach ($shipment_bonuss as $bonus) {
            $thuongphat['thuong'] = $bonus->bonus;
            $thuongphat['phat'] = $bonus->deduct;
        }


        $luongchuyen = array();
        $daudinhmuc = array();
        $dauthuclanh = array();



        foreach ($drivers as $driver) {


            $join = array('table'=>'customer, vehicle','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle');

            $shipments = $shipment_model->getAllShipment(array('where'=>'steersman = '.$driver->steersman_id.' AND vehicle = '.$driver->vehicle.' AND shipment_date >= '.strtotime($dauthang).' AND shipment_date <= '.strtotime($ngayketthuc)),$join);



            foreach ($shipments as $shipment) {
                $check_sub = 1;
                if ($shipment->shipment_sub==1) {
                   $check_sub = 0;
                }

                $luongchuyen[$driver->steersman_id] = isset($luongchuyen[$driver->steersman_id])?($luongchuyen[$driver->steersman_id]+$shipment->shipment_salary) : (0+$shipment->shipment_salary);
                $dauthuclanh[$driver->steersman_id] = isset($dauthuclanh[$driver->steersman_id])?($dauthuclanh[$driver->steersman_id]+$shipment->shipment_oil) : (0+$shipment->shipment_oil);

                $daudinhmuc[$driver->steersman_id] = isset($daudinhmuc[$driver->steersman_id])?$daudinhmuc[$driver->steersman_id]+$shipment->shipment_road_oil_add:$shipment->shipment_road_oil_add;

                $roads = $road_model->queryRoad('SELECT * FROM road WHERE road_id IN ("'.str_replace(',', '","', $shipment->route).'")');

                foreach ($roads as $road) {
                    if ($road->road_oil_ton > 0) {
                        $daudinhmuc[$driver->steersman_id] = isset($daudinhmuc[$driver->steersman_id])?($daudinhmuc[$driver->steersman_id]+$road->road_oil_ton*$check_sub) : (0+$road->road_oil_ton*$check_sub);
                    }
                    else{
                        $daudinhmuc[$driver->steersman_id] = isset($daudinhmuc[$driver->steersman_id])?($daudinhmuc[$driver->steersman_id]+$road->road_oil*$check_sub) : (0+$road->road_oil*$check_sub);
                    }
                    
                }


            }

        }



        $this->view->data['drivers'] = $drivers;

        $this->view->data['luongchuyen'] = $luongchuyen;

        $this->view->data['daudinhmuc'] = $daudinhmuc;

        $this->view->data['dauthuclanh'] = $dauthuclanh;

        $this->view->data['thuongphat'] = $thuongphat;



        $this->view->data['batdau'] = $batdau;

        $this->view->data['ketthuc'] = $ketthuc;

        $this->view->data['trangthai'] = $trangthai;



        $this->view->show('salary/index');

    }

    public function shipment() {

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }


        $this->view->data['lib'] = $this->lib;


        $steersman = $this->registry->router->param_id;
        $bd = $this->registry->router->page;
        $kt = $this->registry->router->order_by;

        $batdau = '01-'.$bd.'-'.$kt;
        $ketthuc = date('t-'.$bd.'-'.$kt);

        
        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));


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

        $join = array('table'=>'customer, vehicle, cont_unit, steersman','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND cont_unit=cont_unit_id AND steersman = steersman_id');



        $shipment_model = $this->model->get('shipmentModel');



        $data = array(

            'order_by'=>'shipment_date',

            'order'=>'ASC',

            'where' => 'steersman = '.$steersman.' AND shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc),

            );




        $road_model = $this->model->get('roadModel');
        $warehouse_model = $this->model->get('warehouseModel');

       

        $road_data = array();
        $warehouse_data = array();

        

        $datas = $shipment_model->getAllShipment($data,$join);



        $this->view->data['shipments'] = $datas;



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

        $this->view->show('salary/shipment');

    }



    public function driver() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Bảng lương tài xế';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;

            $trangthai = isset($_POST['sl_trangthai']) ? $_POST['sl_trangthai'] : null;

        }

        else{

           

            $batdau = date('m');

            

            $ketthuc = date('Y'); //cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')).'-'.date('m-Y');

            $trangthai = -1;

            

        }



        $dauthang = '01-'.$batdau.'-'.$ketthuc;

        $cuoithang = date('t-'.$batdau.'-'.$ketthuc);



        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle();

        foreach ($vehicles as $vehicle) {

            $vehicle_data[$vehicle->vehicle_id] = $vehicle->vehicle_number;

        }

        $this->view->data['vehicle_data'] = $vehicle_data;


        $d_join = array('table'=>'steersman','where'=>'steersman = steersman_id');
        $d_data = array(

            'where'=> 'end_work > '.strtotime($dauthang),

            'order_by'=>'steersman_name ASC',

        );

        if ($trangthai > 0) {

            $d_data['where'] .= ' AND steersman_id = '.$trangthai;

        }

        $driver_model = $this->model->get('driverModel');

        $drivers = $driver_model->getAllDriver($d_data,$d_join);

        $steersman_model = $this->model->get('steersmanModel');

        $steersmans_data = $steersman_model->getAllSteersman(array('order_by'=>'steersman_name ASC'));

        $this->view->data['steersmans_data'] = $steersmans_data;



        $shipment_model = $this->model->get('shipmentModel');

        $warehouse_model = $this->model->get('warehouseModel');

        $road_model = $this->model->get('roadModel');

        $tax_model = $this->model->get('taxModel');

        $insurance_model = $this->model->get('insuranceModel');

        $overtime_model = $this->model->get('overtimeModel');

        $toxic_model = $this->model->get('toxicModel');



        $doanhthu = array();

        $chiphiphatsinh = array();

        $vuottai = array();

        $hoahong = array();



        $doanhthuthem = array();



        $warehouse_data = array();

        $road_data = array();

        $insurances = array();
        $taxs = array();
        $overtimes = array();
        $toxics = array();
        $steersmans = array();

        foreach ($drivers as $driver) {



            $insurances[$driver->steersman_id][$driver->vehicle] = $insurance_model->getInsuranceByWhere(array('insurance_date'=>strtotime($dauthang),'driver'=>$driver->steersman_id));

            $taxs[$driver->steersman_id][$driver->vehicle] = $tax_model->getTaxByWhere(array('tax_date'=>strtotime($dauthang),'driver'=>$driver->steersman_id));

            $overtimes[$driver->steersman_id][$driver->vehicle] = $overtime_model->getOvertimeByWhere(array('overtime_date'=>strtotime($dauthang),'driver'=>$driver->steersman_id));

            $toxics[$driver->steersman_id][$driver->vehicle] = $toxic_model->getToxicByWhere(array('toxic_date'=>strtotime($dauthang),'driver'=>$driver->steersman_id));



            $steersmans[$driver->steersman_id][$driver->vehicle] = $steersman_model->getSteersman($driver->steersman);

               

            $join = array('table'=>'customer, vehicle','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle');

            $shipments = $shipment_model->getAllShipment(array('where'=>'vehicle = '.$driver->vehicle.' AND shipment_date >= '.strtotime($dauthang).' AND shipment_date < '.strtotime($cuoithang)),$join);



            





            foreach ($shipments as $shipment) {

                $check_sub = 1;

               if ($shipment->shipment_sub==1) {

                   $check_sub = 0;

               }

                

                if($driver->end_work > $shipment->shipment_date && $shipment->shipment_date >= $driver->start_work){

                    if ($shipment->sub_driver > 0) {

                        $doanhthu['sub_driver'][$driver->steersman_id][$shipment->vehicle] = isset($doanhthu['sub_driver'][$driver->steersman_id][$shipment->vehicle])?($doanhthu['sub_driver'][$driver->steersman_id][$shipment->vehicle]+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess) : (0+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess);

                    }

                    if ($shipment->sub_driver2 > 0) {

                        $doanhthu['sub_driver'][$driver->steersman_id][$shipment->vehicle] = isset($doanhthu['sub_driver'][$driver->steersman_id][$shipment->vehicle])?($doanhthu['sub_driver'][$driver->steersman_id][$shipment->vehicle]+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess) : (0+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess);

                    }



                    $doanhthu[$driver->steersman_id][$shipment->vehicle] = isset($doanhthu[$driver->steersman_id][$shipment->vehicle])?($doanhthu[$driver->steersman_id][$shipment->vehicle]+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess) : (0+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess);

                   

                    $chiphiphatsinh[$driver->steersman_id][$shipment->vehicle] = isset($chiphiphatsinh[$driver->steersman_id][$shipment->vehicle])?($chiphiphatsinh[$driver->steersman_id][$shipment->vehicle]+(($shipment->approve==1)?$shipment->cost_add:0)) : (0+(($shipment->approve==1)?$shipment->cost_add:0));

                    $vuottai[$driver->steersman_id][$shipment->vehicle] = isset($vuottai[$driver->steersman_id][$shipment->vehicle])?($vuottai[$driver->steersman_id][$shipment->vehicle]+($shipment->shipment_bonus*$check_sub)) : (0+($shipment->shipment_bonus*$check_sub));

                    $hoahong[$driver->steersman_id][$shipment->vehicle] = isset($hoahong[$driver->steersman_id][$shipment->vehicle])?($hoahong[$driver->steersman_id][$shipment->vehicle]+($shipment->commission*$shipment->commission_number)) : (0+$shipment->commission*$shipment->commission_number);



                    $roads = $road_model->getAllRoad(array('where'=>'road_from = '.$shipment->shipment_from.' AND road_to = '.$shipment->shipment_to.' AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));

                    

                   

                    $check_rong = 0;

                    

                    foreach ($roads as $road) {

                        $road_data['bridge_cost'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['bridge_cost'][$driver->steersman_id][$shipment->vehicle])?($road_data['bridge_cost'][$driver->steersman_id][$shipment->vehicle]+$road->bridge_cost*$check_sub):0+$road->bridge_cost*$check_sub;

                        $road_data['police_cost'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['police_cost'][$driver->steersman_id][$shipment->vehicle])?($road_data['police_cost'][$driver->steersman_id][$shipment->vehicle]+$road->police_cost*$check_sub):0+$road->police_cost*$check_sub;

                        $road_data['oil_cost'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['oil_cost'][$driver->steersman_id][$shipment->vehicle])?($road_data['oil_cost'][$driver->steersman_id][$shipment->vehicle]+($road->road_oil*$shipment->oil_cost)*$check_sub):0+($road->road_oil*$shipment->oil_cost)*$check_sub;

                        $road_data['road_time'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['road_time'][$driver->steersman_id][$shipment->vehicle])?($road_data['road_time'][$driver->steersman_id][$shipment->vehicle]+$road->road_time*$check_sub):0+$road->road_time*$check_sub;

                        $road_data['tire_cost'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['tire_cost'][$driver->steersman_id][$shipment->vehicle])?($road_data['tire_cost'][$driver->steersman_id][$shipment->vehicle]+$road->tire_cost*$check_sub):0+$road->tire_cost*$check_sub;

                        if ($shipment->sub_driver > 0) {

                            $road_data['sub_driver']['police_cost'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['sub_driver']['police_cost'][$driver->steersman_id][$shipment->vehicle])?($road_data['sub_driver']['police_cost'][$driver->steersman_id][$shipment->vehicle]+$road->police_cost*$check_sub):0+$road->police_cost*$check_sub;

                            $road_data['sub_driver']['road_time'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['sub_driver']['road_time'][$driver->steersman_id][$shipment->vehicle])?($road_data['sub_driver']['road_time'][$driver->steersman_id][$shipment->vehicle]+$road->road_time*$check_sub):0+$road->road_time*$check_sub;

                        }



                        if ($shipment->sub_driver2 > 0) {

                            $road_data['sub_driver']['police_cost'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['sub_driver']['police_cost'][$driver->steersman_id][$shipment->vehicle])?($road_data['sub_driver']['police_cost'][$driver->steersman_id][$shipment->vehicle]+$road->police_cost*$check_sub):0+$road->police_cost*$check_sub;

                            $road_data['sub_driver']['road_time'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['sub_driver']['road_time'][$driver->steersman_id][$shipment->vehicle])?($road_data['sub_driver']['road_time'][$driver->steersman_id][$shipment->vehicle]+$road->road_time*$check_sub):0+$road->road_time*$check_sub;

                        }



                        $chek_rong = ($road->way == 0)?1:0;

                    }





                    $warehouse = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$shipment->shipment_from.' OR warehouse_code = '.$shipment->shipment_to.') AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));

                



                    $boiduong_cont = 0;

                    $boiduong_tan = 0;



                    $canxe = 0;

                    $quetcont = 0;

                    $vecong = 0;

                    $boiduong = 0;



                    

                    foreach ($warehouse as $warehouse) {

                        if($shipment->shipment_to == $warehouse->warehouse_code){

                            $vecong += $warehouse->warehouse_gate;

                        }

                        if($shipment->shipment_ton > 0 && $chek_rong == 0){

                            $canxe += $warehouse->warehouse_weight;

                            $quetcont += $warehouse->warehouse_clean;

                        }



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

                                $boiduong += $warehouse->warehouse_add;

                            }

                            if ($warehouse->warehouse_ton != 0){

                                $boiduong_tan += $trongluong * $warehouse->warehouse_ton;

                                $boiduong += $trongluong * $warehouse->warehouse_ton;

                            }



                        }

                        else{

                            if ($shipment->shipment_ton > 0) {

                                $boiduong_cont += $warehouse->warehouse_add;

                                $boiduong += $warehouse->warehouse_add;

                            }

                        }

                    }

                    $warehouse_data['boiduong_cn'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['boiduong_cn'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['boiduong_cn'][$driver->steersman_id][$shipment->vehicle]+($boiduong_cont+$boiduong_tan)*$check_sub):0+($boiduong_cont+$boiduong_tan)*$check_sub;

                    $warehouse_data['boiduong'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['boiduong'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['boiduong'][$driver->steersman_id][$shipment->vehicle]+$boiduong*$check_sub):0+$boiduong*$check_sub;

                    $warehouse_data['canxe'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['canxe'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['canxe'][$driver->steersman_id][$shipment->vehicle]+$canxe*$check_sub):0+$canxe*$check_sub;

                    $warehouse_data['quetcont'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['quetcont'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['quetcont'][$driver->steersman_id][$shipment->vehicle]+$quetcont*$check_sub):0+$quetcont*$check_sub;

                    $warehouse_data['vecong'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['vecong'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['vecong'][$driver->steersman_id][$shipment->vehicle]+$vecong*$check_sub):0+$vecong*$check_sub;

                    

                    if ($shipment->sub_driver > 0) {

                        $warehouse_data['sub_driver']['boiduong_cn'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['boiduong_cn'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['boiduong_cn'][$driver->steersman_id][$shipment->vehicle]+($boiduong_cont+$boiduong_tan)*$check_sub):0+($boiduong_cont+$boiduong_tan)*$check_sub;    

                        $warehouse_data['sub_driver']['boiduong'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['boiduong'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['boiduong'][$driver->steersman_id][$shipment->vehicle]+$boiduong*$check_sub):0+$boiduong*$check_sub;

                        $warehouse_data['sub_driver']['canxe'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['canxe'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['canxe'][$driver->steersman_id][$shipment->vehicle]+$canxe*$check_sub):0+$canxe*$check_sub;

                        $warehouse_data['sub_driver']['quetcont'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['quetcont'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['quetcont'][$driver->steersman_id][$shipment->vehicle]+$quetcont*$check_sub):0+$quetcont*$check_sub;

                        $warehouse_data['sub_driver']['vecong'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['vecong'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['vecong'][$driver->steersman_id][$shipment->vehicle]+$vecong*$check_sub):0+$vecong*$check_sub;

                    }



                    if ($shipment->sub_driver2 > 0) {

                        $warehouse_data['sub_driver']['boiduong_cn'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['boiduong_cn'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['boiduong_cn'][$driver->steersman_id][$shipment->vehicle]+($boiduong_cont+$boiduong_tan)*$check_sub):0+($boiduong_cont+$boiduong_tan)*$check_sub;    

                        $warehouse_data['sub_driver']['boiduong'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['boiduong'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['boiduong'][$driver->steersman_id][$shipment->vehicle]+$boiduong*$check_sub):0+$boiduong*$check_sub;

                        $warehouse_data['sub_driver']['canxe'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['canxe'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['canxe'][$driver->steersman_id][$shipment->vehicle]+$canxe*$check_sub):0+$canxe*$check_sub;

                        $warehouse_data['sub_driver']['quetcont'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['quetcont'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['quetcont'][$driver->steersman_id][$shipment->vehicle]+$quetcont*$check_sub):0+$quetcont*$check_sub;

                        $warehouse_data['sub_driver']['vecong'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['vecong'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['vecong'][$driver->steersman_id][$shipment->vehicle]+$vecong*$check_sub):0+$vecong*$check_sub;

                    }

                }

                

                

            }

        }



        $this->view->data['drivers'] = $drivers;

        $this->view->data['warehouse'] = $warehouse_data;

        $this->view->data['road'] = $road_data;



        $this->view->data['doanhthu'] = $doanhthu;

        $this->view->data['doanhthuthem'] = $doanhthuthem;

        $this->view->data['chiphiphatsinh'] = $chiphiphatsinh;



        $this->view->data['vuottai'] = $vuottai;

        $this->view->data['hoahong'] = $hoahong;



        $this->view->data['steersmans'] = $steersmans;

        $this->view->data['insurances'] = $insurances;

        $this->view->data['taxs'] = $taxs;

        $this->view->data['overtimes'] = $overtimes;

        $this->view->data['toxics'] = $toxics;



        $this->view->data['batdau'] = $batdau;

        $this->view->data['ketthuc'] = $ketthuc;

        $this->view->data['trangthai'] = $trangthai;

        $this->view->show('salary/driver');

    }



    public function insurance() {

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $insurance_model = $this->model->get('insuranceModel');

            $data = array(

                'insurance_date' => strtotime($_POST['insurance_date']),

                'driver' => $_POST['driver'],

                'money' => trim(str_replace(',','',$_POST['money'])),



            );



            if ($insurance_model->getInsuranceByWhere(array('insurance_date'=>$data['insurance_date'],'driver'=>$data['driver']))) {

                $insurance_model->updateInsurance($data,array('insurance_date'=>$data['insurance_date'],'driver'=>$data['driver']));

            }

            elseif (!$insurance_model->getInsuranceByWhere(array('insurance_date'=>$data['insurance_date'],'driver'=>$data['driver']))) {

                $insurance_model->createInsurance($data);

            }

        }

    }



    public function tax() {

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $tax_model = $this->model->get('taxModel');

            $data = array(

                'tax_date' => strtotime($_POST['tax_date']),

                'driver' => $_POST['driver'],

                'money' => trim(str_replace(',','',$_POST['money'])),



            );



            if ($tax_model->getTaxByWhere(array('tax_date'=>$data['tax_date'],'driver'=>$data['driver']))) {

                $tax_model->updateTax($data,array('tax_date'=>$data['tax_date'],'driver'=>$data['driver']));

            }

            elseif (!$tax_model->getTaxByWhere(array('tax_date'=>$data['tax_date'],'driver'=>$data['driver']))) {

                $tax_model->createTax($data);

            }

        }

    }



    public function overtime() {

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $overtime_model = $this->model->get('overtimeModel');

            $data = array(

                'overtime_date' => strtotime($_POST['overtime_date']),

                'driver' => $_POST['driver'],

                'money' => trim(str_replace(',','',$_POST['money'])),



            );



            if ($overtime_model->getOvertimeByWhere(array('overtime_date'=>$data['overtime_date'],'driver'=>$data['driver']))) {

                $overtime_model->updateOvertime($data,array('overtime_date'=>$data['overtime_date'],'driver'=>$data['driver']));

            }

            elseif (!$overtime_model->getOvertimeByWhere(array('overtime_date'=>$data['overtime_date'],'driver'=>$data['driver']))) {

                $overtime_model->createOvertime($data);

            }

        }

    }



    public function toxic() {

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $toxic_model = $this->model->get('toxicModel');

            $data = array(

                'toxic_date' => strtotime($_POST['toxic_date']),

                'driver' => $_POST['driver'],

                'money' => trim(str_replace(',','',$_POST['money'])),



            );



            if ($toxic_model->getToxicByWhere(array('toxic_date'=>$data['toxic_date'],'driver'=>$data['driver']))) {

                $toxic_model->updateToxic($data,array('toxic_date'=>$data['toxic_date'],'driver'=>$data['driver']));

            }

            elseif (!$toxic_model->getToxicByWhere(array('toxic_date'=>$data['toxic_date'],'driver'=>$data['driver']))) {

                $toxic_model->createToxic($data);

            }

        }

    }



    public function view() {

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Bảng lương tài xế';



        if ($this->registry->router->param_id != null && $this->registry->router->page != null && $this->registry->router->order_by != null) {

            $trangthai = $this->registry->router->param_id;

            $batdau = $this->registry->router->page;

            $ketthuc = $this->registry->router->order_by;



            $dauthang = '01-'.$batdau.'-'.$ketthuc;

            $cuoithang = date('t-'.$batdau.'-'.$ketthuc);

            $d_join = array('table'=>'steersman','where'=>'steersman = steersman_id');

            $d_data = array(

                'where'=> 'end_work > '.strtotime($dauthang).' AND steersman_id = '.$trangthai,

                

            );

            $driver_model = $this->model->get('driverModel');

            $drivers = $driver_model->getAllDriver($d_data,$d_join);

            

            $shipment_model = $this->model->get('shipmentModel');

            $warehouse_model = $this->model->get('warehouseModel');

            $road_model = $this->model->get('roadModel');

            $place_model = $this->model->get('placeModel');

            $place_data = array();

            $shipments = array();



            $warehouse_data = array();

            $road_data = array();



            foreach ($drivers as $driver) {

                $join = array('table'=>'customer, vehicle, cont_unit','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND cont_unit = cont_unit_id');

                $shipments = $shipment_model->getAllShipment(array('where'=>'vehicle = '.$driver->vehicle.' AND shipment_date >= '.strtotime($dauthang).' AND shipment_date < '.strtotime($cuoithang)),$join);



                





                foreach ($shipments as $shipment) {

                    $check_sub = 1;

                   if ($shipment->shipment_sub==1) {

                       $check_sub = 0;

                   }

                    

                    if($driver->end_work > $shipment->shipment_date && $shipment->shipment_date >= $driver->start_work){

                        

                        $roads = $road_model->getAllRoad(array('where'=>'road_from = '.$shipment->shipment_from.' AND road_to = '.$shipment->shipment_to.' AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));

                        

                        $check_rong = 0;

                        

                        foreach ($roads as $road) {

                            $road_data['bridge_cost'][$shipment->shipment_id] = isset($road_data['bridge_cost'][$shipment->shipment_id])?($road_data['bridge_cost'][$shipment->shipment_id]+$road->bridge_cost*$check_sub):0+$road->bridge_cost*$check_sub;

                            $road_data['police_cost'][$shipment->shipment_id] = isset($road_data['police_cost'][$shipment->shipment_id])?($road_data['police_cost'][$shipment->shipment_id]+$road->police_cost*$check_sub):0+$road->police_cost*$check_sub;

                            $road_data['oil_cost'][$shipment->shipment_id] = isset($road_data['oil_cost'][$shipment->shipment_id])?($road_data['oil_cost'][$shipment->shipment_id]+($road->road_oil*$shipment->oil_cost)*$check_sub):0+($road->road_oil*$shipment->oil_cost)*$check_sub;

                            $road_data['road_time'][$shipment->shipment_id] = isset($road_data['road_time'][$shipment->shipment_id])?($road_data['road_time'][$shipment->shipment_id]+$road->road_time*$check_sub):0+$road->road_time*$check_sub;

                            $road_data['tire_cost'][$shipment->shipment_id] = isset($road_data['tire_cost'][$shipment->shipment_id])?($road_data['tire_cost'][$shipment->shipment_id]+$road->tire_cost*$check_sub):0+$road->tire_cost*$check_sub;

                            if ($shipment->sub_driver > 0) {

                                $road_data['sub_driver']['police_cost'][$shipment->shipment_id] = isset($road_data['sub_driver']['police_cost'][$shipment->shipment_id])?($road_data['sub_driver']['police_cost'][$shipment->shipment_id]+$road->police_cost*$check_sub):0+$road->police_cost*$check_sub;

                                $road_data['sub_driver']['road_time'][$shipment->shipment_id] = isset($road_data['sub_driver']['road_time'][$shipment->shipment_id])?($road_data['sub_driver']['road_time'][$shipment->shipment_id]+$road->road_time*$check_sub):0+$road->road_time*$check_sub;

                            }



                            if ($shipment->sub_driver2 > 0) {

                                $road_data['sub_driver']['police_cost'][$shipment->shipment_id] = isset($road_data['sub_driver']['police_cost'][$shipment->shipment_id])?($road_data['sub_driver']['police_cost'][$shipment->shipment_id]+$road->police_cost*$check_sub):0+$road->police_cost*$check_sub;

                                $road_data['sub_driver']['road_time'][$shipment->shipment_id] = isset($road_data['sub_driver']['road_time'][$shipment->shipment_id])?($road_data['sub_driver']['road_time'][$shipment->shipment_id]+$road->road_time*$check_sub):0+$road->road_time*$check_sub;

                            }



                            $chek_rong = ($road->way == 0)?1:0;

                        }



                        $places = $place_model->getAllPlace(array('where'=>'place_id = '.$shipment->shipment_from.' OR place_id = '.$shipment->shipment_to));


                        foreach ($places as $place) {

                            

                                $place_data['place_id'][$place->place_id] = $place->place_id;

                                $place_data['place_name'][$place->place_id] = $place->place_name;

                            

                            

                        }


                        $warehouse = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$shipment->shipment_from.' OR warehouse_code = '.$shipment->shipment_to.') AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));

                    



                        $boiduong_cont = 0;

                        $boiduong_tan = 0;



                        $canxe = 0;

                        $quetcont = 0;

                        $vecong = 0;

                        $boiduong = 0;



                        

                        foreach ($warehouse as $warehouse) {

                            $warehouse_data['warehouse_id'][$warehouse->warehouse_code] = $warehouse->warehouse_code;

                            $warehouse_data['warehouse_name'][$warehouse->warehouse_code] = $warehouse->warehouse_name; 

                            if($shipment->shipment_to == $warehouse->warehouse_code){

                                $vecong += $warehouse->warehouse_gate;

                            }

                            if($shipment->shipment_ton > 0 && $chek_rong == 0){

                                $canxe += $warehouse->warehouse_weight;

                                $quetcont += $warehouse->warehouse_clean;

                            }



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

                                    $boiduong += $warehouse->warehouse_add;

                                }

                                if ($warehouse->warehouse_ton != 0){

                                    $boiduong_tan += $trongluong * $warehouse->warehouse_ton;

                                    $boiduong += $trongluong * $warehouse->warehouse_ton;

                                }



                            }

                            else{

                                if ($shipment->shipment_ton > 0) {

                                    $boiduong_cont += $warehouse->warehouse_add;

                                    $boiduong += $warehouse->warehouse_add;

                                }

                            }

                        }

                        $warehouse_data['boiduong_cn'][$shipment->shipment_id] = isset($warehouse_data['boiduong_cn'][$shipment->shipment_id])?($warehouse_data['boiduong_cn'][$shipment->shipment_id]+($boiduong_cont+$boiduong_tan)*$check_sub):0+($boiduong_cont+$boiduong_tan)*$check_sub;

                        $warehouse_data['boiduong'][$shipment->shipment_id] = isset($warehouse_data['boiduong'][$shipment->shipment_id])?($warehouse_data['boiduong'][$shipment->shipment_id]+$boiduong*$check_sub):0+$boiduong*$check_sub;

                        $warehouse_data['canxe'][$shipment->shipment_id] = isset($warehouse_data['canxe'][$shipment->shipment_id])?($warehouse_data['canxe'][$shipment->shipment_id]+$canxe*$check_sub):0+$canxe*$check_sub;

                        $warehouse_data['quetcont'][$shipment->shipment_id] = isset($warehouse_data['quetcont'][$shipment->shipment_id])?($warehouse_data['quetcont'][$shipment->shipment_id]+$quetcont*$check_sub):0+$quetcont*$check_sub;

                        $warehouse_data['vecong'][$shipment->shipment_id] = isset($warehouse_data['vecong'][$shipment->shipment_id])?($warehouse_data['vecong'][$shipment->shipment_id]+$vecong*$check_sub):0+$vecong*$check_sub;

                        

                        if ($shipment->sub_driver > 0) {

                            $warehouse_data['sub_driver']['boiduong_cn'][$shipment->shipment_id] = isset($warehouse_data['sub_driver']['boiduong_cn'][$shipment->shipment_id])?($warehouse_data['sub_driver']['boiduong_cn'][$shipment->shipment_id]+($boiduong_cont+$boiduong_tan)*$check_sub):0+($boiduong_cont+$boiduong_tan)*$check_sub;    

                            $warehouse_data['sub_driver']['boiduong'][$shipment->shipment_id] = isset($warehouse_data['sub_driver']['boiduong'][$shipment->shipment_id])?($warehouse_data['sub_driver']['boiduong'][$shipment->shipment_id]+$boiduong*$check_sub):0+$boiduong*$check_sub;

                            $warehouse_data['sub_driver']['canxe'][$shipment->shipment_id] = isset($warehouse_data['sub_driver']['canxe'][$shipment->shipment_id])?($warehouse_data['sub_driver']['canxe'][$shipment->shipment_id]+$canxe*$check_sub):0+$canxe*$check_sub;

                            $warehouse_data['sub_driver']['quetcont'][$shipment->shipment_id] = isset($warehouse_data['sub_driver']['quetcont'][$shipment->shipment_id])?($warehouse_data['sub_driver']['quetcont'][$shipment->shipment_id]+$quetcont*$check_sub):0+$quetcont*$check_sub;

                            $warehouse_data['sub_driver']['vecong'][$shipment->shipment_id] = isset($warehouse_data['sub_driver']['vecong'][$shipment->shipment_id])?($warehouse_data['sub_driver']['vecong'][$shipment->shipment_id]+$vecong*$check_sub):0+$vecong*$check_sub;

                        }



                        if ($shipment->sub_driver2 > 0) {

                            $warehouse_data['sub_driver']['boiduong_cn'][$shipment->shipment_id] = isset($warehouse_data['sub_driver']['boiduong_cn'][$shipment->shipment_id])?($warehouse_data['sub_driver']['boiduong_cn'][$shipment->shipment_id]+($boiduong_cont+$boiduong_tan)*$check_sub):0+($boiduong_cont+$boiduong_tan)*$check_sub;    

                            $warehouse_data['sub_driver']['boiduong'][$shipment->shipment_id] = isset($warehouse_data['sub_driver']['boiduong'][$shipment->shipment_id])?($warehouse_data['sub_driver']['boiduong'][$shipment->shipment_id]+$boiduong*$check_sub):0+$boiduong*$check_sub;

                            $warehouse_data['sub_driver']['canxe'][$shipment->shipment_id] = isset($warehouse_data['sub_driver']['canxe'][$shipment->shipment_id])?($warehouse_data['sub_driver']['canxe'][$shipment->shipment_id]+$canxe*$check_sub):0+$canxe*$check_sub;

                            $warehouse_data['sub_driver']['quetcont'][$shipment->shipment_id] = isset($warehouse_data['sub_driver']['quetcont'][$shipment->shipment_id])?($warehouse_data['sub_driver']['quetcont'][$shipment->shipment_id]+$quetcont*$check_sub):0+$quetcont*$check_sub;

                            $warehouse_data['sub_driver']['vecong'][$shipment->shipment_id] = isset($warehouse_data['sub_driver']['vecong'][$shipment->shipment_id])?($warehouse_data['sub_driver']['vecong'][$shipment->shipment_id]+$vecong*$check_sub):0+$vecong*$check_sub;

                        }

                    }

                    

                    

                }

            }



            $this->view->data['drivers'] = $drivers;

            $this->view->data['warehouse'] = $warehouse_data;

            $this->view->data['road'] = $road_data;

            $this->view->data['place'] = $place_data;

            $this->view->data['shipments'] = $shipments;



            $this->view->data['batdau'] = $batdau;

            $this->view->data['ketthuc'] = $ketthuc;

            $this->view->data['trangthai'] = $trangthai;



        }



        

        $this->view->show('salary/view');

    }







   function exportdriver(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        $info_model = $this->model->get('infoModel');
        $infos = $info_model->getLastInfo();

        $batdau = $this->registry->router->param_id;

        $ketthuc = $this->registry->router->page;

        $trangthai = $this->registry->router->order_by;

        

        $dauthang = '01-'.$batdau.'-'.$ketthuc;

        $cuoithang = date('t-'.$batdau.'-'.$ketthuc);



        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle();

        foreach ($vehicles as $vehicle) {

            $vehicle_data[$vehicle->vehicle_id] = $vehicle->vehicle_number;

        }

        


        $d_join = array('table'=>'steersman','where'=>'steersman = steersman_id');
        $d_data = array(

            'where'=> 'end_work > '.strtotime($dauthang),

            'order_by'=>'steersman_name ASC',

        );

        if ($trangthai > 0) {

            $d_data['where'] .= ' AND steersman_id = '.$trangthai;

        }

        $driver_model = $this->model->get('driverModel');

        $drivers = $driver_model->getAllDriver($d_data,$d_join);





        $shipment_model = $this->model->get('shipmentModel');

        $warehouse_model = $this->model->get('warehouseModel');

        $road_model = $this->model->get('roadModel');

        $steersman_model = $this->model->get('steersmanModel');

        $tax_model = $this->model->get('taxModel');

        $insurance_model = $this->model->get('insuranceModel');

        $overtime_model = $this->model->get('overtimeModel');

        $toxic_model = $this->model->get('toxicModel');



        $doanhthu = array();

        $doanhthuthem = array();

        $chiphiphatsinh = array();

        $vuottai = array();

        $hoahong = array();



        $warehouse_data = array();

        $road_data = array();



        foreach ($drivers as $driver) {



            $insurances[$driver->steersman_id][$driver->vehicle] = $insurance_model->getInsuranceByWhere(array('insurance_date'=>strtotime($dauthang),'driver'=>$driver->steersman_id));

            $taxs[$driver->steersman_id][$driver->vehicle] = $tax_model->getTaxByWhere(array('tax_date'=>strtotime($dauthang),'driver'=>$driver->steersman_id));

            $overtimes[$driver->steersman_id][$driver->vehicle] = $overtime_model->getOvertimeByWhere(array('overtime_date'=>strtotime($dauthang),'driver'=>$driver->steersman_id));

            $toxics[$driver->steersman_id][$driver->vehicle] = $toxic_model->getToxicByWhere(array('toxic_date'=>strtotime($dauthang),'driver'=>$driver->steersman_id));



            $steersmans[$driver->steersman_id][$driver->vehicle] = $steersman_model->getSteersman($driver->steersman);

               

            $join = array('table'=>'customer, vehicle','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle');

            $shipments = $shipment_model->getAllShipment(array('where'=>'vehicle = '.$driver->vehicle.' AND shipment_date >= '.strtotime($dauthang).' AND shipment_date < '.strtotime($cuoithang)),$join);



            





            foreach ($shipments as $shipment) {

                $check_sub = 1;

               if ($shipment->shipment_sub==1) {

                   $check_sub = 0;

               }

                

                if($driver->end_work > $shipment->shipment_date && $shipment->shipment_date >= $driver->start_work){

                    if ($shipment->sub_driver > 0) {

                        $doanhthu['sub_driver'][$driver->steersman_id][$shipment->vehicle] = isset($doanhthu['sub_driver'][$driver->steersman_id][$shipment->vehicle])?($doanhthu['sub_driver'][$driver->steersman_id][$shipment->vehicle]+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess) : (0+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess);

                    }

                    if ($shipment->sub_driver2 > 0) {

                        $doanhthu['sub_driver'][$driver->steersman_id][$shipment->vehicle] = isset($doanhthu['sub_driver'][$driver->steersman_id][$shipment->vehicle])?($doanhthu['sub_driver'][$driver->steersman_id][$shipment->vehicle]+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess) : (0+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess);

                    }



                    $doanhthu[$driver->steersman_id][$shipment->vehicle] = isset($doanhthu[$driver->steersman_id][$shipment->vehicle])?($doanhthu[$driver->steersman_id][$shipment->vehicle]+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess) : (0+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess);


                    $chiphiphatsinh[$driver->steersman_id][$shipment->vehicle] = isset($chiphiphatsinh[$driver->steersman_id][$shipment->vehicle])?($chiphiphatsinh[$driver->steersman_id][$shipment->vehicle]+(($shipment->approve==1)?$shipment->cost_add:0)) : (0+(($shipment->approve==1)?$shipment->cost_add:0));

                    $vuottai[$driver->steersman_id][$shipment->vehicle] = isset($vuottai[$driver->steersman_id][$shipment->vehicle])?($vuottai[$driver->steersman_id][$shipment->vehicle]+($shipment->shipment_bonus*$check_sub)) : (0+($shipment->shipment_bonus*$check_sub));

                    $hoahong[$driver->steersman_id][$shipment->vehicle] = isset($hoahong[$driver->steersman_id][$shipment->vehicle])?($hoahong[$driver->steersman_id][$shipment->vehicle]+($shipment->commission*$shipment->commission_number)) : (0+$shipment->commission*$shipment->commission_number);



                    $roads = $road_model->getAllRoad(array('where'=>'road_from = '.$shipment->shipment_from.' AND road_to = '.$shipment->shipment_to.' AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));

                    

                   

                    $check_rong = 0;

                    

                    foreach ($roads as $road) {

                        $road_data['bridge_cost'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['bridge_cost'][$driver->steersman_id][$shipment->vehicle])?($road_data['bridge_cost'][$driver->steersman_id][$shipment->vehicle]+$road->bridge_cost*$check_sub):0+$road->bridge_cost*$check_sub;

                        $road_data['police_cost'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['police_cost'][$driver->steersman_id][$shipment->vehicle])?($road_data['police_cost'][$driver->steersman_id][$shipment->vehicle]+$road->police_cost*$check_sub):0+$road->police_cost*$check_sub;

                        $road_data['oil_cost'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['oil_cost'][$driver->steersman_id][$shipment->vehicle])?($road_data['oil_cost'][$driver->steersman_id][$shipment->vehicle]+($road->road_oil*$shipment->oil_cost)*$check_sub):0+($road->road_oil*$shipment->oil_cost)*$check_sub;

                        $road_data['road_time'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['road_time'][$driver->steersman_id][$shipment->vehicle])?($road_data['road_time'][$driver->steersman_id][$shipment->vehicle]+$road->road_time*$check_sub):0+$road->road_time*$check_sub;

                        $road_data['tire_cost'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['tire_cost'][$driver->steersman_id][$shipment->vehicle])?($road_data['tire_cost'][$driver->steersman_id][$shipment->vehicle]+$road->tire_cost*$check_sub):0+$road->tire_cost*$check_sub;

                        if ($shipment->sub_driver > 0) {

                            $road_data['sub_driver']['police_cost'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['sub_driver']['police_cost'][$driver->steersman_id][$shipment->vehicle])?($road_data['sub_driver']['police_cost'][$driver->steersman_id][$shipment->vehicle]+$road->police_cost*$check_sub):0+$road->police_cost*$check_sub;

                            $road_data['sub_driver']['road_time'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['sub_driver']['road_time'][$driver->steersman_id][$shipment->vehicle])?($road_data['sub_driver']['road_time'][$driver->steersman_id][$shipment->vehicle]+$road->road_time*$check_sub):0+$road->road_time*$check_sub;

                        }



                        if ($shipment->sub_driver2 > 0) {

                            $road_data['sub_driver']['police_cost'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['sub_driver']['police_cost'][$driver->steersman_id][$shipment->vehicle])?($road_data['sub_driver']['police_cost'][$driver->steersman_id][$shipment->vehicle]+$road->police_cost*$check_sub):0+$road->police_cost*$check_sub;

                            $road_data['sub_driver']['road_time'][$driver->steersman_id][$shipment->vehicle] = isset($road_data['sub_driver']['road_time'][$driver->steersman_id][$shipment->vehicle])?($road_data['sub_driver']['road_time'][$driver->steersman_id][$shipment->vehicle]+$road->road_time*$check_sub):0+$road->road_time*$check_sub;

                        }



                        $chek_rong = ($road->way == 0)?1:0;

                    }





                    $warehouse = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$shipment->shipment_from.' OR warehouse_code = '.$shipment->shipment_to.') AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));

                



                    $boiduong_cont = 0;

                    $boiduong_tan = 0;



                    $canxe = 0;

                    $quetcont = 0;

                    $vecong = 0;

                    $boiduong = 0;



                    

                    foreach ($warehouse as $warehouse) {

                        if($shipment->shipment_to == $warehouse->warehouse_code){

                            $vecong += $warehouse->warehouse_gate;

                        }

                        if($shipment->shipment_ton > 0 && $chek_rong == 0){

                            $canxe += $warehouse->warehouse_weight;

                            $quetcont += $warehouse->warehouse_clean;

                        }



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

                                $boiduong += $warehouse->warehouse_add;

                            }

                            if ($warehouse->warehouse_ton != 0){

                                $boiduong_tan += $trongluong * $warehouse->warehouse_ton;

                                $boiduong += $trongluong * $warehouse->warehouse_ton;

                            }



                        }

                        else{

                            if ($shipment->shipment_ton > 0) {

                                $boiduong_cont += $warehouse->warehouse_add;

                                $boiduong += $warehouse->warehouse_add;

                            }

                        }

                    }

                    $warehouse_data['boiduong_cn'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['boiduong_cn'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['boiduong_cn'][$driver->steersman_id][$shipment->vehicle]+($boiduong_cont+$boiduong_tan)*$check_sub):0+($boiduong_cont+$boiduong_tan)*$check_sub;

                    $warehouse_data['boiduong'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['boiduong'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['boiduong'][$driver->steersman_id][$shipment->vehicle]+$boiduong*$check_sub):0+$boiduong*$check_sub;

                    $warehouse_data['canxe'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['canxe'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['canxe'][$driver->steersman_id][$shipment->vehicle]+$canxe*$check_sub):0+$canxe*$check_sub;

                    $warehouse_data['quetcont'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['quetcont'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['quetcont'][$driver->steersman_id][$shipment->vehicle]+$quetcont*$check_sub):0+$quetcont*$check_sub;

                    $warehouse_data['vecong'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['vecong'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['vecong'][$driver->steersman_id][$shipment->vehicle]+$vecong*$check_sub):0+$vecong*$check_sub;

                    

                    if ($shipment->sub_driver > 0) {

                        $warehouse_data['sub_driver']['boiduong_cn'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['boiduong_cn'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['boiduong_cn'][$driver->steersman_id][$shipment->vehicle]+($boiduong_cont+$boiduong_tan)*$check_sub):0+($boiduong_cont+$boiduong_tan)*$check_sub;    

                        $warehouse_data['sub_driver']['boiduong'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['boiduong'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['boiduong'][$driver->steersman_id][$shipment->vehicle]+$boiduong*$check_sub):0+$boiduong*$check_sub;

                        $warehouse_data['sub_driver']['canxe'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['canxe'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['canxe'][$driver->steersman_id][$shipment->vehicle]+$canxe*$check_sub):0+$canxe*$check_sub;

                        $warehouse_data['sub_driver']['quetcont'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['quetcont'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['quetcont'][$driver->steersman_id][$shipment->vehicle]+$quetcont*$check_sub):0+$quetcont*$check_sub;

                        $warehouse_data['sub_driver']['vecong'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['vecong'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['vecong'][$driver->steersman_id][$shipment->vehicle]+$vecong*$check_sub):0+$vecong*$check_sub;

                    }



                    if ($shipment->sub_driver2 > 0) {

                        $warehouse_data['sub_driver']['boiduong_cn'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['boiduong_cn'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['boiduong_cn'][$driver->steersman_id][$shipment->vehicle]+($boiduong_cont+$boiduong_tan)*$check_sub):0+($boiduong_cont+$boiduong_tan)*$check_sub;    

                        $warehouse_data['sub_driver']['boiduong'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['boiduong'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['boiduong'][$driver->steersman_id][$shipment->vehicle]+$boiduong*$check_sub):0+$boiduong*$check_sub;

                        $warehouse_data['sub_driver']['canxe'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['canxe'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['canxe'][$driver->steersman_id][$shipment->vehicle]+$canxe*$check_sub):0+$canxe*$check_sub;

                        $warehouse_data['sub_driver']['quetcont'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['quetcont'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['quetcont'][$driver->steersman_id][$shipment->vehicle]+$quetcont*$check_sub):0+$quetcont*$check_sub;

                        $warehouse_data['sub_driver']['vecong'][$driver->steersman_id][$shipment->vehicle] = isset($warehouse_data['sub_driver']['vecong'][$driver->steersman_id][$shipment->vehicle])?($warehouse_data['sub_driver']['vecong'][$driver->steersman_id][$shipment->vehicle]+$vecong*$check_sub):0+$vecong*$check_sub;

                    }

                }

                

                

            }

        }



        



        

            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', mb_strtoupper($infos->info_company, "UTF-8"))

                ->setCellValue('A2', 'ĐỘI VẬN TẢI')

                ->setCellValue('H1', 'CỘNG HÒA XÃ CHỦ NGHĨA VIỆT NAM')

                ->setCellValue('H2', 'Độc lập - Tự do - Hạnh phúc')

                ->setCellValue('A4', 'BẢNG LƯƠNG TÀI XẾ')

               ->setCellValue('A6', 'STT')

               ->setCellValue('B6', 'MÃ NV')

               ->setCellValue('C6', 'XE')

               ->setCellValue('D6', 'HỌ TÊN')

               ->setCellValue('E6', 'TK NGÂN HÀNG')

               ->setCellValue('F6', 'TỔNG NGÀY CÔNG')

               ->setCellValue('G6', 'LƯƠNG')

               ->setCellValue('G7', 'DOANH THU')

               ->setCellValue('H7', 'CHI PHÍ 0 VAT')

               ->setCellValue('I7', 'DOANH THU TÍNH LƯƠNG')

               ->setCellValue('J7', 'LƯƠNG CỐ ĐỊNH')

               ->setCellValue('K7', 'LƯƠNG SẢN PHẨM')

               ->setCellValue('L6', 'PHỤ CẤP')

               ->setCellValue('L7', 'ĂN CA')

               ->setCellValue('M7', 'LÀM ĐÊM')

               ->setCellValue('N7', 'ĐỘC HẠI')

               ->setCellValue('O6', 'KHẤU TRỪ')

               ->setCellValue('O7', 'BẢO HIỂM')

               ->setCellValue('P7', 'THUẾ')

               ->setCellValue('Q7', 'CÔNG ĐOÀN')

               ->setCellValue('R6', 'TỔNG CỘNG');

               



            



            

            

            



            if ($drivers) {



                $tongdoanhthu = 0; $tongchiphi=0; $tongluongsp=0; $tongluongcd=0; $tongbh=0; $tongthue=0; $tongcongdoan=0; $tongsk=0; $tongphucap=0; $tongcong=0; $tongthuong=0; $tonghoahong=0;  $tonganca=0;

           $luongt13 =  0; $bh =  640000; $thue =  175729; $sk =  130417;

        $tongdoanhthuthem=0; $tongdoanhthutinhluong=0;

    

            

                $hang = 8;

                $i=1;



                foreach ($drivers as $driver) {

                    

                    $chiphi = isset($road_data['police_cost'][$driver->steersman_id][$driver->vehicle])?$road_data['police_cost'][$driver->steersman_id][$driver->vehicle]:0;

                    $chiphi += isset($road_data['tire_cost'][$driver->steersman_id][$driver->vehicle])?$road_data['tire_cost'][$driver->steersman_id][$driver->vehicle]:0;

                    $chiphi += isset($chiphiphatsinh[$driver->steersman_id][$driver->vehicle])?$chiphiphatsinh[$driver->steersman_id][$driver->vehicle]:0;

                    

                    $chiphi += isset($hoahong[$driver->steersman_id][$driver->vehicle])?$hoahong[$driver->steersman_id][$driver->vehicle]:0;

                    

                    $chiphi += isset($warehouse_data['boiduong'][$driver->steersman_id][$driver->vehicle])?$warehouse_data['boiduong'][$driver->steersman_id][$driver->vehicle]:0;

                    $chiphi += isset($warehouse_data['canxe'][$driver->steersman_id][$driver->vehicle])?$warehouse_data['canxe'][$driver->steersman_id][$driver->vehicle]:0;

                    $chiphi += isset($warehouse_data['quetcont'][$driver->steersman_id][$driver->vehicle])?$warehouse_data['quetcont'][$driver->steersman_id][$driver->vehicle]:0;

                    $chiphi += isset($warehouse_data['vecong'][$driver->steersman_id][$driver->vehicle])?$warehouse_data['vecong'][$driver->steersman_id][$driver->vehicle]:0;



                    



                    $ngaycong = round(isset($road_data['road_time'][$driver->steersman_id][$driver->vehicle])?$road_data['road_time'][$driver->steersman_id][$driver->vehicle]:0);



                    $bh = isset($insurances[$driver->steersman_id][$driver->vehicle]->money)?$insurances[$driver->steersman_id][$driver->vehicle]->money:0;

                    $thue = isset($taxs[$driver->steersman_id][$driver->vehicle]->money)?$taxs[$driver->steersman_id][$driver->vehicle]->money:0;

                    $lamdem = isset($overtimes[$driver->steersman_id][$driver->vehicle]->money)?$overtimes[$driver->steersman_id][$driver->vehicle]->money:0;

                    $dochai = isset($toxics[$driver->steersman_id][$driver->vehicle]->money)?$toxics[$driver->steersman_id][$driver->vehicle]->money:0;



                    $ngayvaocang = isset($steersmans[$driver->steersman_id][$driver->vehicle])?$steersmans[$driver->steersman_id][$driver->vehicle]->steersman_start_time:0;



                    $timeDiff = strtotime(date('t-m-Y',strtotime('01-'.$batdau.'-'.$ketthuc))) - $ngayvaocang;



                    $numberDays = $timeDiff/86400;  // 86400 seconds in one day



                    // and you might want to convert to integer

                    $numberDays = intval($numberDays); 



                    $timeDiff2 = strtotime(date('t-m-Y',strtotime('01-'.$batdau.'-'.$ketthuc))) - $driver->start_work;



                    $numberDays2 = $timeDiff2/86400;  // 86400 seconds in one day



                    // and you might want to convert to integer

                    $numberDays2 = intval($numberDays2); 



                    if ($numberDays >= 30) {

                        $luongcd = 2000000;

                    }

                    else{

                        $luongcd = $numberDays2>0?round(2000000*$numberDays2/26):0;

                    }



                    if($luongcd>2000000)

                        $luongcd = 2000000;



        

                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                     $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue('A' . $hang, $i++)

                        ->setCellValue('B' . $hang, $driver->steersman_code)

                        ->setCellValueExplicit('C' . $hang, $vehicle_data[$driver->vehicle])

                        ->setCellValue('D' . $hang, $driver->steersman_name)

                        ->setCellValue('E' . $hang, "'".$driver->steersman_bank)

                        ->setCellValue('F' . $hang, $ngaycong)

                        ->setCellValue('G' . $hang, isset($doanhthu[$driver->steersman_id][$driver->vehicle])?$doanhthu[$driver->steersman_id][$driver->vehicle]:0)

                        ->setCellValue('H' . $hang, $chiphi)

                        ->setCellValue('I' . $hang, '=G'.$hang.'-H'.$hang.'+'.(isset($doanhthuthem[$driver->steersman_id][$driver->vehicle])?$doanhthuthem[$driver->steersman_id][$driver->vehicle]:0))

                        ->setCellValue('J' . $hang, $luongcd)

                        ->setCellValue('K' . $hang, '=ROUND(I'.$hang.'*10%,0)')

                        ->setCellValue('L' . $hang, '=F'.$hang.'*25000')

                        ->setCellValue('M' . $hang, $lamdem)

                        ->setCellValue('N' . $hang, $dochai)

                        ->setCellValue('O' . $hang, $bh)

                        ->setCellValue('P' . $hang, $thue)

                        ->setCellValue('Q' . $hang, '=IF(((J'.$hang.'+K'.$hang.'-O'.$hang.'-P'.$hang.')*1%) > 115000,115000,ROUND((J'.$hang.'+K'.$hang.'-O'.$hang.'-P'.$hang.')*1%,0) )')

                        ->setCellValue('R' . $hang, '=SUM(J'.$hang.':N'.$hang.')-SUM(O'.$hang.':Q'.$hang.')');

                     $hang++;





                  }



          }




            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;

            $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');

            $objPHPExcel->getActiveSheet()->mergeCells('H1:M1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:E2');

            $objPHPExcel->getActiveSheet()->mergeCells('H2:M2');



            $objPHPExcel->getActiveSheet()->mergeCells('A4:M4');



            $objPHPExcel->getActiveSheet()->getStyle('A1:R4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:R4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(16);



            $objPHPExcel->getActiveSheet()->getStyle('A1:R4')->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('A2:H2')->applyFromArray(

                array(

                    

                    'font' => array(

                        'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->mergeCells('A6:A7');

            $objPHPExcel->getActiveSheet()->mergeCells('B6:B7');

            $objPHPExcel->getActiveSheet()->mergeCells('C6:C7');

            $objPHPExcel->getActiveSheet()->mergeCells('D6:D7');

            $objPHPExcel->getActiveSheet()->mergeCells('E6:E7');

            $objPHPExcel->getActiveSheet()->mergeCells('F6:F7');

            $objPHPExcel->getActiveSheet()->mergeCells('G6:K6');

            $objPHPExcel->getActiveSheet()->mergeCells('L6:N6');

            $objPHPExcel->getActiveSheet()->mergeCells('O6:Q6');

            $objPHPExcel->getActiveSheet()->mergeCells('R6:R7');

            $objPHPExcel->getActiveSheet()->getStyle('A6:R'.$hang)->applyFromArray(

                array(

                    

                    'borders' => array(

                        'allborders' => array(

                          'style' => PHPExcel_Style_Border::BORDER_THIN

                        )

                    )

                )

            );


            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.($hang+3), 'NGƯỜI LẬP BIỂU')

                ->setCellValue('I'.($hang+3), mb_strtoupper($infos->info_company, "UTF-8"));



            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+3).':D'.($hang+3));

            $objPHPExcel->getActiveSheet()->mergeCells('I'.($hang+3).':M'.($hang+3));



            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':M'.($hang+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':M'.($hang+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':N'.($hang+3))->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('G7:R'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A6:R6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:R6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:R6')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(16);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(15);



            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Salary Report")

                            ->setSubject("Salary Report")

                            ->setDescription("Salary Report.")

                            ->setKeywords("Salary Report")

                            ->setCategory("Salary Report");

            $objPHPExcel->getActiveSheet()->setTitle("Bang luong tai xe");



            $objPHPExcel->getActiveSheet()->freezePane('A8');

            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BẢNG LƯƠNG TÀI XẾ.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }



}

?>