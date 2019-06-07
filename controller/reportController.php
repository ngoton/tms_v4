<?php

Class reportController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->report) || json_decode($_SESSION['user_permission_action'])->report != "report") {

            return $this->view->redirect('user/login');

        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Báo cáo kết quả hoạt động của đội xe';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;

            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;

            $trangthai = isset($_POST['trangthai']) ? $_POST['trangthai'] : null;

            $xe = isset($_POST['sl_vehicle']) ? $_POST['sl_vehicle'] : null;

        }

        else{

            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

            $vong = (int)date('m',strtotime($batdau));

            $trangthai = date('Y',strtotime($batdau));

            $xe = 0;

        }

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));

        $trangthai = date('Y',strtotime($batdau));

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicle_datas = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $this->view->data['vehicle_datas'] = $vehicle_datas;

        $v_data = array(
            'where' => 'vehicle_id NOT IN (SELECT vehicle FROM vehicle_work WHERE start_work >= '.strtotime($batdau).' AND end_work < '.strtotime($ngayketthuc).')',
            'order_by'=>'vehicle_number',
            'order'=>'ASC',
        );

        if ($xe>0) {
            $v_data['where'] .= ' AND vehicle_id = '.$xe;
        }

        $vehicles = $vehicle_model->getAllVehicle($v_data);



        $shipment_model = $this->model->get('shipmentModel');

        $join = array('table'=>'customer, vehicle','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle');

        $s_data = array(
            'where'=>'shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc)
        );

        if ($xe>0) {
            $s_data['where'] .= ' AND vehicle_id = '.$xe;
        }

        $shipments = $shipment_model->getAllShipment($s_data,$join);



        $doanhthu = array();

        $warehouse_model = $this->model->get('warehouseModel');

        $road_model = $this->model->get('roadModel');

        $shipment_cost_model = $this->model->get('shipmentcostModel');

        $repair_model = $this->model->get('repairModel');

        $road_cost_model = $this->model->get('roadcostModel');
        $checking_cost_model = $this->model->get('checkingcostModel');
        $insurance_cost_model = $this->model->get('insurancecostModel');

        $cost_data = array();

        $warehouse_data = array();

        $road_data = array();

        $repair_data = array();

        $road_cost_data = array();

        $checking_cost_data = array();

        $insurance_cost_data = array();

        $dauthuclanh = array();

        $luongchuyen = array();

        $diduong = array();

        $chiphidau = array();

        $dauvat = array();
        $cauduongvat = array();
        $cauduongkvat = array();
        $suachuavat = array();
        $suachuakvat = array();
        $khacvat = array();
        $khackvat = array();
        $congankvat = array();
        $boiduongkvat = array();
        $hoahongkvat = array();


        $repairs = $repair_model->getAllRepair(array('where'=>'vehicle > 0 AND repair_date >= '.strtotime($batdau).' AND repair_date < '.strtotime($ngayketthuc)));
        foreach ($repairs as $repair) {
            $repair_data[$repair->vehicle] = isset($repair_data[$repair->vehicle])?$repair_data[$repair->vehicle]+$repair->repair_price:$repair->repair_price;
        }

        $road_costs = $road_cost_model->getAllCost(array('where'=>'vehicle != "" AND road_cost_date >= '.strtotime('01-01-'.date('Y')).' AND road_cost_date <= '.strtotime('31-12-'.date('Y'))));
        foreach ($road_costs as $road_cost) {
            $arr = explode(',',$road_cost->vehicle);
            foreach ($arr as $key => $value) {
                $road_cost_data[$key] = isset($road_cost_data[$key])?$road_cost_data[$key]+round(($road_cost->road_cost_price+$road_cost->road_cost_vat)/$road_cost->total_number):round(($road_cost->road_cost_price+$road_cost->road_cost_vat)/$road_cost->total_number);
            }
            
        }
        $checking_costs = $checking_cost_model->getAllCost(array('where'=>'vehicle != "" AND checking_cost_date >= '.strtotime('01-01-'.date('Y')).' AND checking_cost_date <= '.strtotime('31-12-'.date('Y'))));
        foreach ($checking_costs as $checking_cost) {
            $arr = explode(',',$checking_cost->vehicle);
            foreach ($arr as $key => $value) {
                $checking_cost_data[$key] = isset($checking_cost_data[$key])?$checking_cost_data[$key]+round(($checking_cost->checking_cost_price+$checking_cost->checking_cost_vat)/$checking_cost->total_number):round(($checking_cost->checking_cost_price+$checking_cost->checking_cost_vat)/$checking_cost->total_number);
            }
            
        }
        $insurance_costs = $insurance_cost_model->getAllCost(array('where'=>'vehicle != "" AND insurance_cost_date >= '.strtotime('01-01-'.date('Y')).' AND insurance_cost_date <= '.strtotime('31-12-'.date('Y'))));
        foreach ($insurance_costs as $insurance_cost) {
            $arr = explode(',',$insurance_cost->vehicle);
            foreach ($arr as $key => $value) {
                $insurance_cost_data[$key] = isset($insurance_cost_data[$key])?$insurance_cost_data[$key]+round(($insurance_cost->insurance_cost_price+$insurance_cost->insurance_cost_vat)/$insurance_cost->total_number):round(($insurance_cost->insurance_cost_price+$insurance_cost->insurance_cost_vat)/$insurance_cost->total_number);
            }
            
        }

        $bonus_model = $this->model->get('salarybonusModel');

        $shipment_bonuss = $bonus_model->getAllSalary(array('where'=>'start_time <= '.strtotime($batdau).' AND end_time >= '.strtotime($ketthuc)));

        $thuongphat = array();
        foreach ($shipment_bonuss as $bonus) {
            $thuongphat['thuong'] = $bonus->bonus;
            $thuongphat['phat'] = $bonus->deduct;
        }



        $k=0;
        foreach ($shipments as $shipment) {
            $qr = "SELECT * FROM vehicle_work WHERE vehicle = ".$shipment->vehicle." AND start_work <= ".$shipment->shipment_date." AND end_work >= ".$shipment->shipment_date;
            if ($shipment_model->queryShipment($qr)) {
                unset($shipments[$k]);
            }
            else{
                $check_sub = 1;

                if ($shipment->shipment_sub==1) {
                   $check_sub = 0;
                }



                $doanhthu[$shipment->vehicle] = isset($doanhthu[$shipment->vehicle])?($doanhthu[$shipment->vehicle]+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess) : (0+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess);

                $diduong[$shipment->vehicle] = isset($diduong[$shipment->vehicle])?$diduong[$shipment->vehicle]+$shipment->shipment_road_add:$shipment->shipment_road_add;

                $cost_join = array('table'=>'cost_list','where'=>'cost_list = cost_list_id');
                $shipment_costs = $shipment_cost_model->getAllShipment(array('where'=>'shipment='.$shipment->shipment_id),$cost_join);

                foreach ($shipment_costs as $cost) {
                    if ($cost->cost_list_type != 7 && $cost->cost_list_type != 8 ) {
                        if ($cost->cost_list_type == 5){
                            $dauvat[$shipment->vehicle] = isset($dauvat[$shipment->vehicle])?$dauvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                        }
                        else if ($cost->cost_list_type == 9){
                            $hoahongkvat[$shipment->vehicle] = isset($hoahongkvat[$shipment->vehicle])?$hoahongkvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                        }
                        else if ($cost->cost_list_type == 10){
                            $congankvat[$shipment->vehicle] = isset($congankvat[$shipment->vehicle])?$congankvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                        }
                        else if ($cost->cost_list_type == 11 ) {
                            $boiduongkvat[$shipment->vehicle] = isset($boiduongkvat[$shipment->vehicle])?$boiduongkvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                        }
                        else if ($cost->cost_list_type == 6 ) {
                            if ($cost->check_vat == 1) {
                                $cauduongvat[$shipment->vehicle] = isset($cauduongvat[$shipment->vehicle])?$cauduongvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                            }
                            else{
                                $cauduongkvat[$shipment->vehicle] = isset($cauduongkvat[$shipment->vehicle])?$cauduongkvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                            }
                        }
                        else if ($cost->cost_list_type == 4 ) {
                            if ($cost->check_vat == 1) {
                                $suachuavat[$shipment->vehicle] = isset($suachuavat[$shipment->vehicle])?$suachuavat[$shipment->vehicle]+$cost->cost:$cost->cost;
                            }
                            else{
                                $suachuakvat[$shipment->vehicle] = isset($suachuakvat[$shipment->vehicle])?$suachuakvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                            }
                        }
                        else{
                            if ($cost->check_vat == 1) {
                                $khacvat[$shipment->vehicle] = isset($khacvat[$shipment->vehicle])?$khacvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                            }
                            else{
                                $khackvat[$shipment->vehicle] = isset($khackvat[$shipment->vehicle])?$khackvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                            }
                        }
                        
                    }
                    
                }

                $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $shipment->route).'")'));
               

                $chiphidau[$shipment->vehicle] = isset($chiphidau[$shipment->vehicle])?$chiphidau[$shipment->vehicle]+round($shipment->shipment_oil*$shipment->oil_cost):round($shipment->shipment_oil*$shipment->oil_cost);

                $check_rong = 0;

                $luongchuyen[$shipment->vehicle] = isset($luongchuyen[$shipment->vehicle])?($luongchuyen[$shipment->vehicle]+$shipment->shipment_salary) : (0+$shipment->shipment_salary);

                $dauthuclanh[$shipment->vehicle] = isset($dauthuclanh[$shipment->vehicle])?($dauthuclanh[$shipment->vehicle]+$shipment->shipment_oil) : (0+$shipment->shipment_oil);
                
                $road_data['oil_cost'][$shipment->vehicle] = isset($road_data['oil_cost'][$shipment->vehicle])?$road_data['oil_cost'][$shipment->vehicle]+$shipment->shipment_road_oil_add:$shipment->shipment_road_oil_add;
                

                foreach ($roads as $road) {

                    $road_data['bridge_cost'][$shipment->vehicle] = isset($road_data['bridge_cost'][$shipment->vehicle])?$road_data['bridge_cost'][$shipment->vehicle]+$road->bridge_cost*$check_sub:$road->bridge_cost*$check_sub;

                    $road_data['police_cost'][$shipment->vehicle] = isset($road_data['police_cost'][$shipment->vehicle])?$road_data['police_cost'][$shipment->vehicle]+$road->police_cost*$check_sub:$road->police_cost*$check_sub;

                    $road_data['tire_cost'][$shipment->vehicle] = isset($road_data['tire_cost'][$shipment->vehicle])?$road_data['tire_cost'][$shipment->vehicle]+$road->tire_cost*$check_sub:$road->tire_cost*$check_sub;

                    
                    if($road->road_oil_ton > 0){
                        $road_data['oil_cost'][$shipment->vehicle] = isset($road_data['oil_cost'][$shipment->vehicle])?$road_data['oil_cost'][$shipment->vehicle]+$road->road_oil_ton*$check_sub:$road->road_oil_ton*$check_sub;
                    }
                    else{
                        $road_data['oil_cost'][$shipment->vehicle] = isset($road_data['oil_cost'][$shipment->vehicle])?$road_data['oil_cost'][$shipment->vehicle]+$road->road_oil*$check_sub:$road->road_oil*$check_sub;
                    }

                    //$road_data['road_add'][$shipment->vehicle] = isset($road_data['road_add'][$shipment->vehicle])?$road_data['road_add'][$shipment->vehicle]+$road->road_add*$check_sub:$road->road_add*$check_sub;

                    $road_data['way'][$shipment->vehicle] = $road->way;

                    $chek_rong = ($road->way == 0)?1:0;

                }


                $warehouses = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$shipment->shipment_from.' OR warehouse_code = '.$shipment->shipment_to.') AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));

            



                $boiduong_cont = 0;

                $boiduong_tan = 0;



                $canxe = 0;

                $quetcont = 0;

                $vecong = 0;

                $boiduong = 0;

                

                foreach ($warehouses as $warehouse) {

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
                        if ($shipment->shipment_ton > 0) {

                            if ($warehouse->warehouse_cont != 0) {

                                $boiduong_cont += $warehouse->warehouse_cont;

                                $boiduong += $warehouse->warehouse_add;

                            }

                            if ($warehouse->warehouse_ton != 0){

                                $boiduong_tan += $trongluong * $warehouse->warehouse_ton;

                                $boiduong += $trongluong * $warehouse->warehouse_ton;

                            }
                        }



                    }

                    else{

                        if ($shipment->shipment_ton > 0) {

                            $boiduong_cont += $warehouse->warehouse_add;

                            $boiduong += $warehouse->warehouse_add;

                        }

                    }

                }

                $warehouse_data['boiduong_cn'][$shipment->vehicle] = isset($warehouse_data['boiduong_cn'][$shipment->vehicle])?($warehouse_data['boiduong_cn'][$shipment->vehicle]+($boiduong_cont+$boiduong_tan)*$check_sub):0+($boiduong_cont+$boiduong_tan)*$check_sub;



                $warehouse_data['boiduong'][$shipment->vehicle] = isset($warehouse_data['boiduong'][$shipment->vehicle])?($warehouse_data['boiduong'][$shipment->vehicle]+$boiduong*$check_sub):0+$boiduong*$check_sub;

                $warehouse_data['canxe'][$shipment->vehicle] = isset($warehouse_data['canxe'][$shipment->vehicle])?($warehouse_data['canxe'][$shipment->vehicle]+$canxe*$check_sub):0+$canxe*$check_sub;

                $warehouse_data['quetcont'][$shipment->vehicle] = isset($warehouse_data['quetcont'][$shipment->vehicle])?($warehouse_data['quetcont'][$shipment->vehicle]+$quetcont*$check_sub):0+$quetcont*$check_sub;

                $warehouse_data['vecong'][$shipment->vehicle] = isset($warehouse_data['vecong'][$shipment->vehicle])?($warehouse_data['vecong'][$shipment->vehicle]+$vecong*$check_sub):0+$vecong*$check_sub;

                
            }
            $k++;
            
        }



        $this->view->data['vehicles'] = $vehicles;

        $this->view->data['warehouse'] = $warehouse_data;

        $this->view->data['road'] = $road_data;

        $this->view->data['dauvat'] = $dauvat;
        $this->view->data['cauduongvat'] = $cauduongvat;
        $this->view->data['cauduongkvat'] = $cauduongkvat;
        $this->view->data['suachuavat'] = $suachuavat;
        $this->view->data['suachuakvat'] = $suachuakvat;
        $this->view->data['khacvat'] = $khacvat;
        $this->view->data['khackvat'] = $khackvat;
        $this->view->data['boiduongkvat'] = $boiduongkvat;
        $this->view->data['congankvat'] = $congankvat;
        $this->view->data['hoahongkvat'] = $hoahongkvat;

        $this->view->data['repair'] = $repair_data;

        $this->view->data['road_cost'] = $road_cost_data;

        $this->view->data['checking_cost'] = $checking_cost_data;

        $this->view->data['insurance_cost'] = $insurance_cost_data;



        $this->view->data['doanhthu'] = $doanhthu;

        $this->view->data['luongchuyen'] = $luongchuyen;

        $this->view->data['dauthuclanh'] = $dauthuclanh;

        $this->view->data['thuongphat'] = $thuongphat;

        $this->view->data['chiphidau'] = $chiphidau;

        $this->view->data['diduong'] = $diduong;


        $this->view->data['batdau'] = $batdau;

        $this->view->data['ketthuc'] = $ketthuc;

        $this->view->data['vong'] = $vong;

        $this->view->data['trangthai'] = $trangthai;

        $this->view->data['xe'] = $xe;

        $this->view->show('report/index');

    }


   

    function exportreport(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->report) || json_decode($_SESSION['user_permission_action'])->report != "report") {

            return $this->view->redirect('user/login');

        }

        

        if ($this->registry->router->param_id != null && $this->registry->router->page != null) {

            $batdau = $this->registry->router->param_id;

            $ketthuc = $this->registry->router->page;

            $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

            $xe = $this->registry->router->order_by;

            $v_data = array(
                'where' => 'vehicle_id NOT IN (SELECT vehicle FROM vehicle_work WHERE start_work >= '.$batdau.' AND end_work < '.$ngayketthuc.')',
                'order_by'=>'vehicle_number',
                'order'=>'ASC',
            );

            if ($xe>0) {
                $v_data['where'] .= ' AND vehicle_id = '.$xe;
            }

            $vehicle_model = $this->model->get('vehicleModel');

            $vehicles = $vehicle_model->getAllVehicle($v_data);



            $shipment_model = $this->model->get('shipmentModel');

            $join = array('table'=>'customer, vehicle','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle');

            $s_data = array(
                'where'=>'shipment_date >= '.$batdau.' AND shipment_date < '.$ngayketthuc
            );

            if ($xe>0) {
                $s_data['where'] .= ' AND vehicle_id = '.$xe;
            }

            $shipments = $shipment_model->getAllShipment($s_data,$join);



            $doanhthu = array();

            $warehouse_model = $this->model->get('warehouseModel');

            $road_model = $this->model->get('roadModel');

            $shipment_cost_model = $this->model->get('shipmentcostModel');

            $repair_model = $this->model->get('repairModel');

            $road_cost_model = $this->model->get('roadcostModel');
            $checking_cost_model = $this->model->get('checkingcostModel');
            $insurance_cost_model = $this->model->get('insurancecostModel');

            $cost_data = array();

            $warehouse_data = array();

            $road_data = array();

            $repair_data = array();

            $road_cost_data = array();

            $checking_cost_data = array();

            $insurance_cost_data = array();

            $dauthuclanh = array();

            $luongchuyen = array();

            $diduong = array();

            $chiphidau = array();

            $dauvat = array();
            $cauduongvat = array();
            $cauduongkvat = array();
            $suachuavat = array();
            $suachuakvat = array();
            $khacvat = array();
            $khackvat = array();
            $congankvat = array();
            $boiduongkvat = array();
            $hoahongkvat = array();


            $repairs = $repair_model->getAllRepair(array('where'=>'vehicle > 0 AND repair_date >= '.$batdau.' AND repair_date < '.$ngayketthuc));
            foreach ($repairs as $repair) {
                $repair_data[$repair->vehicle] = isset($repair_data[$repair->vehicle])?$repair_data[$repair->vehicle]+$repair->repair_price:$repair->repair_price;
            }

            $road_costs = $road_cost_model->getAllCost(array('where'=>'vehicle != "" AND road_cost_date >= '.strtotime('01-01-'.date('Y')).' AND road_cost_date <= '.strtotime('31-12-'.date('Y'))));
            foreach ($road_costs as $road_cost) {
                $arr = explode(',',$road_cost->vehicle);
                foreach ($arr as $key => $value) {
                    $road_cost_data[$key] = isset($road_cost_data[$key])?$road_cost_data[$key]+round(($road_cost->road_cost_price+$road_cost->road_cost_vat)/$road_cost->total_number):round(($road_cost->road_cost_price+$road_cost->road_cost_vat)/$road_cost->total_number);
                }
                
            }
            $checking_costs = $checking_cost_model->getAllCost(array('where'=>'vehicle != "" AND checking_cost_date >= '.strtotime('01-01-'.date('Y')).' AND checking_cost_date <= '.strtotime('31-12-'.date('Y'))));
            foreach ($checking_costs as $checking_cost) {
                $arr = explode(',',$checking_cost->vehicle);
                foreach ($arr as $key => $value) {
                    $checking_cost_data[$key] = isset($checking_cost_data[$key])?$checking_cost_data[$key]+round(($checking_cost->checking_cost_price+$checking_cost->checking_cost_vat)/$checking_cost->total_number):round(($checking_cost->checking_cost_price+$checking_cost->checking_cost_vat)/$checking_cost->total_number);
                }
                
            }
            $insurance_costs = $insurance_cost_model->getAllCost(array('where'=>'vehicle != "" AND insurance_cost_date >= '.strtotime('01-01-'.date('Y')).' AND insurance_cost_date <= '.strtotime('31-12-'.date('Y'))));
            foreach ($insurance_costs as $insurance_cost) {
                $arr = explode(',',$insurance_cost->vehicle);
                foreach ($arr as $key => $value) {
                    $insurance_cost_data[$key] = isset($insurance_cost_data[$key])?$insurance_cost_data[$key]+round(($insurance_cost->insurance_cost_price+$insurance_cost->insurance_cost_vat)/$insurance_cost->total_number):round(($insurance_cost->insurance_cost_price+$insurance_cost->insurance_cost_vat)/$insurance_cost->total_number);
                }
                
            }

            $bonus_model = $this->model->get('salarybonusModel');

            $shipment_bonuss = $bonus_model->getAllSalary(array('where'=>'start_time <= '.$batdau.' AND end_time >= '.$ketthuc));

            $thuongphat = array();
            foreach ($shipment_bonuss as $bonus) {
                $thuongphat['thuong'] = $bonus->bonus;
                $thuongphat['phat'] = $bonus->deduct;
            }



            $k=0;
        foreach ($shipments as $shipment) {
            $qr = "SELECT * FROM vehicle_work WHERE vehicle = ".$shipment->vehicle." AND start_work <= ".$shipment->shipment_date." AND end_work >= ".$shipment->shipment_date;
            
            if (!$shipment_model->queryShipment($qr)) {
                $check_sub = 1;

                if ($shipment->shipment_sub==1) {
                   $check_sub = 0;
                }



                $doanhthu[$shipment->vehicle] = isset($doanhthu[$shipment->vehicle])?($doanhthu[$shipment->vehicle]+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess) : (0+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess);

                $diduong[$shipment->vehicle] = isset($diduong[$shipment->vehicle])?$diduong[$shipment->vehicle]+$shipment->shipment_road_add:$shipment->shipment_road_add;

                $cost_join = array('table'=>'cost_list','where'=>'cost_list = cost_list_id');
                $shipment_costs = $shipment_cost_model->getAllShipment(array('where'=>'shipment='.$shipment->shipment_id),$cost_join);

                foreach ($shipment_costs as $cost) {
                    if ($cost->cost_list_type != 7 && $cost->cost_list_type != 8 ) {
                        if ($cost->cost_list_type == 5){
                            $dauvat[$shipment->vehicle] = isset($dauvat[$shipment->vehicle])?$dauvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                        }
                        else if ($cost->cost_list_type == 9){
                            $hoahongkvat[$shipment->vehicle] = isset($hoahongkvat[$shipment->vehicle])?$hoahongkvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                        }
                        else if ($cost->cost_list_type == 10){
                            $congankvat[$shipment->vehicle] = isset($congankvat[$shipment->vehicle])?$congankvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                        }
                        else if ($cost->cost_list_type == 11 ) {
                            $boiduongkvat[$shipment->vehicle] = isset($boiduongkvat[$shipment->vehicle])?$boiduongkvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                        }
                        else if ($cost->cost_list_type == 6 ) {
                            if ($cost->check_vat == 1) {
                                $cauduongvat[$shipment->vehicle] = isset($cauduongvat[$shipment->vehicle])?$cauduongvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                            }
                            else{
                                $cauduongkvat[$shipment->vehicle] = isset($cauduongkvat[$shipment->vehicle])?$cauduongkvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                            }
                        }
                        else if ($cost->cost_list_type == 4 ) {
                            if ($cost->check_vat == 1) {
                                $suachuavat[$shipment->vehicle] = isset($suachuavat[$shipment->vehicle])?$suachuavat[$shipment->vehicle]+$cost->cost:$cost->cost;
                            }
                            else{
                                $suachuakvat[$shipment->vehicle] = isset($suachuakvat[$shipment->vehicle])?$suachuakvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                            }
                        }
                        else{
                            if ($cost->check_vat == 1) {
                                $khacvat[$shipment->vehicle] = isset($khacvat[$shipment->vehicle])?$khacvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                            }
                            else{
                                $khackvat[$shipment->vehicle] = isset($khackvat[$shipment->vehicle])?$khackvat[$shipment->vehicle]+$cost->cost:$cost->cost;
                            }
                        }
                        
                    }
                    
                }

                $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $shipment->route).'")'));
               

                $chiphidau[$shipment->vehicle] = isset($chiphidau[$shipment->vehicle])?$chiphidau[$shipment->vehicle]+round($shipment->shipment_oil*$shipment->oil_cost):round($shipment->shipment_oil*$shipment->oil_cost);

                $check_rong = 0;

                $luongchuyen[$shipment->vehicle] = isset($luongchuyen[$shipment->vehicle])?($luongchuyen[$shipment->vehicle]+$shipment->shipment_salary) : (0+$shipment->shipment_salary);

                $dauthuclanh[$shipment->vehicle] = isset($dauthuclanh[$shipment->vehicle])?($dauthuclanh[$shipment->vehicle]+$shipment->shipment_oil) : (0+$shipment->shipment_oil);
                
                $road_data['oil_cost'][$shipment->vehicle] = isset($road_data['oil_cost'][$shipment->vehicle])?$road_data['oil_cost'][$shipment->vehicle]+$shipment->shipment_road_oil_add:$shipment->shipment_road_oil_add;
                

                foreach ($roads as $road) {

                    $road_data['bridge_cost'][$shipment->vehicle] = isset($road_data['bridge_cost'][$shipment->vehicle])?$road_data['bridge_cost'][$shipment->vehicle]+$road->bridge_cost*$check_sub:$road->bridge_cost*$check_sub;

                    $road_data['police_cost'][$shipment->vehicle] = isset($road_data['police_cost'][$shipment->vehicle])?$road_data['police_cost'][$shipment->vehicle]+$road->police_cost*$check_sub:$road->police_cost*$check_sub;

                    $road_data['tire_cost'][$shipment->vehicle] = isset($road_data['tire_cost'][$shipment->vehicle])?$road_data['tire_cost'][$shipment->vehicle]+$road->tire_cost*$check_sub:$road->tire_cost*$check_sub;

                    
                    if($road->road_oil_ton > 0){
                        $road_data['oil_cost'][$shipment->vehicle] = isset($road_data['oil_cost'][$shipment->vehicle])?$road_data['oil_cost'][$shipment->vehicle]+$road->road_oil_ton*$check_sub:$road->road_oil_ton*$check_sub;
                    }
                    else{
                        $road_data['oil_cost'][$shipment->vehicle] = isset($road_data['oil_cost'][$shipment->vehicle])?$road_data['oil_cost'][$shipment->vehicle]+$road->road_oil*$check_sub:$road->road_oil*$check_sub;
                    }

                    //$road_data['road_add'][$shipment->vehicle] = isset($road_data['road_add'][$shipment->vehicle])?$road_data['road_add'][$shipment->vehicle]+$road->road_add*$check_sub:$road->road_add*$check_sub;

                    $road_data['way'][$shipment->vehicle] = $road->way;

                    $chek_rong = ($road->way == 0)?1:0;

                }


                $warehouses = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$shipment->shipment_from.' OR warehouse_code = '.$shipment->shipment_to.') AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));

            



                $boiduong_cont = 0;

                $boiduong_tan = 0;



                $canxe = 0;

                $quetcont = 0;

                $vecong = 0;

                $boiduong = 0;

                

                foreach ($warehouses as $warehouse) {

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
                        if ($shipment->shipment_ton > 0) {

                            if ($warehouse->warehouse_cont != 0) {

                                $boiduong_cont += $warehouse->warehouse_cont;

                                $boiduong += $warehouse->warehouse_add;

                            }

                            if ($warehouse->warehouse_ton != 0){

                                $boiduong_tan += $trongluong * $warehouse->warehouse_ton;

                                $boiduong += $trongluong * $warehouse->warehouse_ton;

                            }
                        }



                    }

                    else{

                        if ($shipment->shipment_ton > 0) {

                            $boiduong_cont += $warehouse->warehouse_add;

                            $boiduong += $warehouse->warehouse_add;

                        }

                    }

                }

                $warehouse_data['boiduong_cn'][$shipment->vehicle] = isset($warehouse_data['boiduong_cn'][$shipment->vehicle])?($warehouse_data['boiduong_cn'][$shipment->vehicle]+($boiduong_cont+$boiduong_tan)*$check_sub):0+($boiduong_cont+$boiduong_tan)*$check_sub;



                $warehouse_data['boiduong'][$shipment->vehicle] = isset($warehouse_data['boiduong'][$shipment->vehicle])?($warehouse_data['boiduong'][$shipment->vehicle]+$boiduong*$check_sub):0+$boiduong*$check_sub;

                $warehouse_data['canxe'][$shipment->vehicle] = isset($warehouse_data['canxe'][$shipment->vehicle])?($warehouse_data['canxe'][$shipment->vehicle]+$canxe*$check_sub):0+$canxe*$check_sub;

                $warehouse_data['quetcont'][$shipment->vehicle] = isset($warehouse_data['quetcont'][$shipment->vehicle])?($warehouse_data['quetcont'][$shipment->vehicle]+$quetcont*$check_sub):0+$quetcont*$check_sub;

                $warehouse_data['vecong'][$shipment->vehicle] = isset($warehouse_data['vecong'][$shipment->vehicle])?($warehouse_data['vecong'][$shipment->vehicle]+$vecong*$check_sub):0+$vecong*$check_sub;

                
            }

            $k++;
        }







            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', 'BÁO CÁO KẾT QUẢ HOẠT ĐỘNG CỦA ĐỘI XE')

                ->setCellValue('A2', 'Từ ngày '.$this->lib->hien_thi_ngay_thang($batdau).' đến ngày '.$this->lib->hien_thi_ngay_thang($ketthuc))

               ->setCellValue('A4', 'TT')

               ->setCellValue('B4', 'Nội dung')

               ->setCellValue('A5', 'I')

               ->setCellValue('B5', 'Doanh thu')

               ->setCellValue('A6', 'II')

               ->setCellValue('B6', 'Chi phí')

               ->setCellValue('A7', 'A')

               ->setCellValue('B7', 'Chi phí cố định')

               ->setCellValue('A8', '1.')

               ->setCellValue('B8', 'Phí sử dụng đường bộ')

               ->setCellValue('A9', '2.')

               ->setCellValue('B9', 'Kiểm định')

               ->setCellValue('A10', '3.')

               ->setCellValue('B10', 'Bảo hiểm')

               ->setCellValue('A11', 'B')

               ->setCellValue('B11', 'Có hóa đơn chứng từ')

               ->setCellValue('A12', '1.')

               ->setCellValue('B12', 'Lương tài xế')

               ->setCellValue('A13', '2.')

               ->setCellValue('B13', 'Chi phí nhiên liệu')

               ->setCellValue('A14', '3.')

               ->setCellValue('B14', 'Chi phí cầu đường')

               ->setCellValue('A15', '4.')

               ->setCellValue('B15', 'Sửa chữa, bảo dưỡng')

               ->setCellValue('A16', '5.')

               ->setCellValue('B16', 'Chi phí khác')

               ->setCellValue('A17', 'C')

               ->setCellValue('B17', 'Không hóa đơn chứng từ')

               ->setCellValue('A18', '1.')

               ->setCellValue('B18', 'Tiền đi đường')

               ->setCellValue('A19', '2.')

               ->setCellValue('B19', 'Chi phí công an')

               ->setCellValue('A20', '3.')

               ->setCellValue('B20', 'Chi phí bồi dưỡng')

               ->setCellValue('A21', '4.')

               ->setCellValue('B21', 'Cân xe')

               ->setCellValue('A22', '5.')

               ->setCellValue('B22', 'Quét cont')

               ->setCellValue('A23', '6.')

               ->setCellValue('B23', 'Vé cổng')

               ->setCellValue('A24', '7.')

               ->setCellValue('B24', 'Hoa hồng')

               ->setCellValue('A25', '8.')

               ->setCellValue('B25', 'Sửa chữa')

               ->setCellValue('A26', '9.')

               ->setCellValue('B26', 'Chi phí khác')

               ->setCellValue('A27', 'III')

               ->setCellValue('B27', 'LN trước thuế');





            if ($vehicles) {

                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."4", $vehicle->vehicle_number);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."5", isset($doanhthu[$vehicle->vehicle_id])?$doanhthu[$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."8", (isset($road_cost_data[$vehicle->vehicle_id])?$road_cost_data[$vehicle->vehicle_id]:0) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."9", (isset($checking_cost_data[$vehicle->vehicle_id])?$checking_cost_data[$vehicle->vehicle_id]:0) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."10", (isset($insurance_cost_data[$vehicle->vehicle_id])?$insurance_cost_data[$vehicle->vehicle_id]:0) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    $lc = isset($luongchuyen[$vehicle->vehicle_id])?$luongchuyen[$vehicle->vehicle_id]:0;
                    $dm = isset($road_data['oil_cost'][$vehicle->vehicle_id])?$road_data['oil_cost'][$vehicle->vehicle_id]:0;
                    $dl = isset($dauthuclanh[$vehicle->vehicle_id])?$dauthuclanh[$vehicle->vehicle_id]:0;
                    $sl = $dm-$dl;
                    $tp = $sl<0?$thuongphat['phat']:($sl>0?$thuongphat['thuong']:0);
                    $tt = $sl*$tp;
                    $lanh = $lc+$tt;

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."12", $lanh );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."13", ((isset($chiphidau[$vehicle->vehicle_id])?$chiphidau[$vehicle->vehicle_id]:0)+(isset($dauvat[$vehicle->vehicle_id])?$dauvat[$vehicle->vehicle_id]:0)) );

                }




                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."14", ((isset($road_data['bridge_cost'][$vehicle->vehicle_id])?$road_data['bridge_cost'][$vehicle->vehicle_id]:0)+(isset($cauduongvat[$vehicle->vehicle_id])?$cauduongvat[$vehicle->vehicle_id]:0)));

                }



                



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."15", ((isset($repair_data[$vehicle->vehicle_id])?$repair_data[$vehicle->vehicle_id]:0)+(isset($suachuavat[$vehicle->vehicle_id])?$suachuavat[$vehicle->vehicle_id]:0)) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."16", (isset($khacvat[$vehicle->vehicle_id])?$khacvat[$vehicle->vehicle_id]:0));

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."18", ((isset($road_data['road_add'][$vehicle->vehicle_id])?$road_data['road_add'][$vehicle->vehicle_id]:0)+(isset($diduong[$vehicle->vehicle_id])?$diduong[$vehicle->vehicle_id]:0)));

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."19", ((isset($road_data['police_cost'][$vehicle->vehicle_id])?$road_data['police_cost'][$vehicle->vehicle_id]:0)+(isset($congankvat[$vehicle->vehicle_id])?$congankvat[$vehicle->vehicle_id]:0)));

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."20", ((isset($warehouse_data['boiduong'][$vehicle->vehicle_id])?$warehouse_data['boiduong'][$vehicle->vehicle_id]:0)+(isset($boiduongkvat[$vehicle->vehicle_id])?$boiduongkvat[$vehicle->vehicle_id]:0)));

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."21", isset($warehouse_data['canxe'][$vehicle->vehicle_id])?$warehouse_data['canxe'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."22", isset($warehouse_data['quetcont'][$vehicle->vehicle_id])?$warehouse_data['quetcont'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."23", isset($warehouse_data['vecong'][$vehicle->vehicle_id])?$warehouse_data['vecong'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."24", (isset($hoahongkvat[$vehicle->vehicle_id])?$hoahongkvat[$vehicle->vehicle_id]:0));

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."25", ((isset($road_data['tire_cost'][$vehicle->vehicle_id])?$road_data['tire_cost'][$vehicle->vehicle_id]:0)+(isset($suachuakvat[$vehicle->vehicle_id])?$suachuakvat[$vehicle->vehicle_id]:0)));

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."26", (isset($khackvat[$vehicle->vehicle_id])?$khackvat[$vehicle->vehicle_id]:0));

                }

                




                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."7",  '=SUM('.$current_column.'8:'.$current_column.'10)');

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."6",  '='.$current_column.'7+'.$current_column.'11+'.$current_column.'17');

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."11", '=SUM('.$current_column.'12:'.$current_column.'16)');

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."17", '=SUM('.$current_column.'18:'.$current_column.'26)');

                }

                



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."27", '='.$current_column.'5-'.$current_column.'6');

                }

                



            }



            $lastColumn = $current_column;

            ++$current_column;



            $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."4", 'CỘNG');



            for ($m=5; $m < 28; $m++) { 

                $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column .$m, '=SUM(C'.$m.':'.$lastColumn.$m.')');

            }





            $objPHPExcel->getActiveSheet()->getStyle('A1:A27')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:A27')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('B4:'.$current_column."4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('B4:'.$current_column."4")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A4:'.$current_column."7")->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle("A1:".$current_column."27")->getFont()->setName('Times New Roman');

            $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(18);

            $objPHPExcel->getActiveSheet()->getStyle("A4:".$current_column."4")->getAlignment()->setWrapText(true);

            $objPHPExcel->getActiveSheet()->getStyle("A4:".$current_column."4")->getFont()->setSize(14);

            $objPHPExcel->getActiveSheet()->getStyle("A5:".$current_column."27")->getFont()->setSize(13);



            $objPHPExcel->getActiveSheet()->getStyle('C5:'.$current_column."27")->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            //$objPHPExcel->getActiveSheet()->getStyle('C21:'.$current_column."27")->getNumberFormat()->setFormatCode("#,##0;[Black](-#,##0)");

            //$objPHPExcel->getActiveSheet()->getStyle('C22:'.$current_column."22")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);



            $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:H2');



            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(25);

            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(33);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);



            $objPHPExcel->getActiveSheet()->getStyle('A7:'.$current_column."7")->getFont()->setItalic(true);

            $objPHPExcel->getActiveSheet()->getStyle('A11:'.$current_column."11")->getFont()->setItalic(true);

            $objPHPExcel->getActiveSheet()->getStyle('A17:'.$current_column."17")->getFont()->setItalic(true);

            $objPHPExcel->getActiveSheet()->getStyle('A27:'.$current_column."27")->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle('A11:'.$current_column."11")->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle('A17:'.$current_column."17")->getFont()->setBold(true);



            $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray(

                array(

                    

                    'fill' => array(

                        'type' => PHPExcel_Style_Fill::FILL_SOLID,

                        'color' => array('rgb' => 'FFFD51')

                    )

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => 'FF0000')

                    )

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A4:'.$current_column."27")->applyFromArray(

                array(

                    'borders' => array(

                        'outline' => array(

                            'style' => PHPExcel_Style_Border::BORDER_THIN,

                            'color' => array('argb' => '000000'),

                        ),

                    ),

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A4:'.$current_column."4")->applyFromArray(

                array(

                    'borders' => array(

                        'outline' => array(

                            'style' => PHPExcel_Style_Border::BORDER_THIN,

                            'color' => array('argb' => '000000'),

                        ),

                    ),

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A1:A27')->applyFromArray(

                array(

                    'borders' => array(

                        'outline' => array(

                            'style' => PHPExcel_Style_Border::BORDER_THIN,

                            'color' => array('argb' => '000000'),

                        ),

                    ),

                )

            );



            



            // Set properties

            $objPHPExcel->getProperties()->setCreator("Tan Cang Mien Trung")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Report")

                            ->setSubject("Report")

                            ->setDescription("Report.")

                            ->setKeywords("Report")

                            ->setCategory("Report");

            $objPHPExcel->getActiveSheet()->setTitle("Bao cao ket qua");



            



            $objPHPExcel->getActiveSheet()->freezePane('C5');



            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BAO CAO ".$this->lib->hien_thi_ngay_thang($batdau)."_".$this->lib->hien_thi_ngay_thang($ketthuc).".xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        }

    }



    function exportfirst(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->report) || json_decode($_SESSION['user_permission_action'])->report != "report") {

            return $this->view->redirect('user/login');

        }

        

        if ($this->registry->router->param_id != null && $this->registry->router->page != null) {

            $batdau = $this->registry->router->param_id;

            $ketthuc = $this->registry->router->page;

            $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

            $data_v = array(

                'where'=>'vehicle_id IN (SELECT vehicle FROM control WHERE status=1 AND control_number=1) AND vehicle_id IN (SELECT vehicle FROM vehicle_work WHERE end_work > '.$batdau.')',

            );



            $vehicle_model = $this->model->get('vehicleModel');

            $vehicles = $vehicle_model->getAllVehicle($data_v);



            $shipment_model = $this->model->get('shipmentModel');

            $join = array('table'=>'customer, vehicle','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND vehicle.vehicle_id IN (SELECT vehicle FROM control WHERE status=1 AND control_number=1) ');

            $shipments = $shipment_model->getAllShipment(array('where'=>'shipment_date >= '.$batdau.' AND shipment_date < '.$ngayketthuc),$join);



            $doanhthu = array();

            $chiphiphatsinh = array();



            $warehouse_model = $this->model->get('warehouseModel');

            



            $road_model = $this->model->get('roadModel');



            $warehouse_data = array();

            $road_data = array();





            $vuottai = array();

            $chiphihd = array();

            $hoahong = array();

            $chiphidau = array();

            $daudocduong = array();

            $cauduongphatsinh = array();

            $phichuyentien = array();



            $k=0;
        foreach ($shipments as $shipment) {
            $qr = "SELECT * FROM vehicle_work WHERE vehicle = ".$shipment->vehicle." AND start_work <= ".$shipment->shipment_date." AND end_work >= ".$shipment->shipment_date;
            if (!$shipment_model->queryShipment($qr)) {
                unset($shipments[$k]);
            }
            else{
                $check_sub = 1;

                if ($shipment->shipment_sub==1) {

                   $check_sub = 0;

                }



                $doanhthu[$shipment->vehicle] = isset($doanhthu[$shipment->vehicle])?($doanhthu[$shipment->vehicle]+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess) : (0+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess);

                $chiphiphatsinh[$shipment->vehicle] = isset($chiphiphatsinh[$shipment->vehicle])?($chiphiphatsinh[$shipment->vehicle]+(($shipment->approve==1)?$shipment->cost_add:0)) : (0+(($shipment->approve==1)?$shipment->cost_add:0));

                $vuottai[$shipment->vehicle] = isset($vuottai[$shipment->vehicle])?($vuottai[$shipment->vehicle]+$shipment->shipment_bonus*$check_sub) : (0+$shipment->shipment_bonus*$check_sub);

                $chiphihd[$shipment->vehicle] = isset($chiphihd[$shipment->vehicle])?($chiphihd[$shipment->vehicle]+$shipment->cost_vat) : (0+$shipment->cost_vat);

                $hoahong[$shipment->vehicle] = isset($hoahong[$shipment->vehicle])?($hoahong[$shipment->vehicle]+($shipment->commission*$shipment->commission_number)) : (0+$shipment->commission*$shipment->commission_number);

                $cauduongphatsinh[$shipment->vehicle] = isset($cauduongphatsinh[$shipment->vehicle])?($cauduongphatsinh[$shipment->vehicle]+round($shipment->bridge_cost_add/1.1)) : (0+round($shipment->bridge_cost_add/1.1));

                if ($shipment->shipment_ton > 0 && $shipment->shipment_date >= strtotime('01-01-2016')) {
                    $phichuyentien[$shipment->vehicle] = isset($phichuyentien[$shipment->vehicle])?($phichuyentien[$shipment->vehicle]+3300) : (0+3300);
                }


                $daubai1 = ($shipment->oil_add_dc == 5)?($shipment->oil_add*$shipment->oil_cost):0;

                $daubai2 = ($shipment->oil_add_dc2 == 5)?($shipment->oil_add2*$shipment->oil_cost):0;

                $dau1 = $shipment->oil_add*$shipment->oil_cost;

                $dau2 = $shipment->oil_add2*$shipment->oil_cost;



                $chiphidau[$shipment->vehicle] = isset($chiphidau[$shipment->vehicle])?($chiphidau[$shipment->vehicle]+($dau1+$dau2)*$check_sub):($dau1+$dau2)*$check_sub;

                $daudocduong[$shipment->vehicle] = isset($daudocduong[$shipment->vehicle])?($daudocduong[$shipment->vehicle]+($daubai1+$daubai2)*$check_sub):($daubai1+$daubai2)*$check_sub;



                $roads = $road_model->getAllRoad(array('where'=>'road_from = '.$shipment->shipment_from.' AND road_to = '.$shipment->shipment_to.' AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));

                

               

                $check_rong = 0;



                

                

                foreach ($roads as $road) {

                    $road_data['bridge_cost'][$shipment->vehicle] = isset($road_data['bridge_cost'][$shipment->vehicle])?($road_data['bridge_cost'][$shipment->vehicle]+$road->bridge_cost*$check_sub):0+$road->bridge_cost*$check_sub;

                    $road_data['police_cost'][$shipment->vehicle] = isset($road_data['police_cost'][$shipment->vehicle])?($road_data['police_cost'][$shipment->vehicle]+$road->police_cost*$check_sub):0+$road->police_cost*$check_sub;

                    $road_data['oil_cost'][$shipment->vehicle] = isset($road_data['oil_cost'][$shipment->vehicle])?($road_data['oil_cost'][$shipment->vehicle]+($road->road_oil*$shipment->oil_cost)*$check_sub):0+($road->road_oil*$shipment->oil_cost)*$check_sub;

                    $road_data['road_time'][$shipment->vehicle] = isset($road_data['road_time'][$shipment->vehicle])?($road_data['road_time'][$shipment->vehicle]+$road->road_time*$check_sub):0+$road->road_time*$check_sub;

                    $road_data['tire_cost'][$shipment->vehicle] = isset($road_data['tire_cost'][$shipment->vehicle])?($road_data['tire_cost'][$shipment->vehicle]+$road->tire_cost*$check_sub):0+$road->tire_cost*$check_sub;

                    



                    $chek_rong = ($road->way == 0)?1:0;

                }





                $warehouses = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$shipment->shipment_from.' OR warehouse_code = '.$shipment->shipment_to.') AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));

            



                $boiduong_cont = 0;

                $boiduong_tan = 0;



                $canxe = 0;

                $quetcont = 0;

                $vecong = 0;

                $boiduong = 0;

                

                foreach ($warehouses as $warehouse) {

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
                        if ($shipment->shipment_ton > 0) {

                            if ($warehouse->warehouse_cont != 0) {

                                $boiduong_cont += $warehouse->warehouse_cont;

                                $boiduong += $warehouse->warehouse_add;

                            }

                            if ($warehouse->warehouse_ton != 0){

                                $boiduong_tan += $trongluong * $warehouse->warehouse_ton;

                                $boiduong += $trongluong * $warehouse->warehouse_ton;

                            }
                        }



                    }

                    else{

                        if ($shipment->shipment_ton > 0) {

                            $boiduong_cont += $warehouse->warehouse_add;

                            $boiduong += $warehouse->warehouse_add;

                        }

                    }

                }

                $warehouse_data['boiduong_cn'][$shipment->vehicle] = isset($warehouse_data['boiduong_cn'][$shipment->vehicle])?($warehouse_data['boiduong_cn'][$shipment->vehicle]+($boiduong_cont+$boiduong_tan)*$check_sub):0+($boiduong_cont+$boiduong_tan)*$check_sub;



                $warehouse_data['boiduong'][$shipment->vehicle] = isset($warehouse_data['boiduong'][$shipment->vehicle])?($warehouse_data['boiduong'][$shipment->vehicle]+$boiduong*$check_sub):0+$boiduong*$check_sub;

                $warehouse_data['canxe'][$shipment->vehicle] = isset($warehouse_data['canxe'][$shipment->vehicle])?($warehouse_data['canxe'][$shipment->vehicle]+$canxe*$check_sub):0+$canxe*$check_sub;

                $warehouse_data['quetcont'][$shipment->vehicle] = isset($warehouse_data['quetcont'][$shipment->vehicle])?($warehouse_data['quetcont'][$shipment->vehicle]+$quetcont*$check_sub):0+$quetcont*$check_sub;

                $warehouse_data['vecong'][$shipment->vehicle] = isset($warehouse_data['vecong'][$shipment->vehicle])?($warehouse_data['vecong'][$shipment->vehicle]+$vecong*$check_sub):0+$vecong*$check_sub;

                
            }
            $k++;
            
        }







            $timeDiff = abs($ketthuc - $batdau);



            $numberDays = $timeDiff/86400;  // 86400 seconds in one day



            // and you might want to convert to integer

            $numberDays = intval($numberDays)+1;





            $xethue = 1475410*$numberDays;



            $bh = 85545.8033*$numberDays;

            $phiduongbo = 35226.0328*$numberDays;

            $laivay = 260266.557*$numberDays;

            $khauhao = 736909.082*$numberDays;



            $bh_moi = 85545.8033*$numberDays;

            $phiduongbo_moi = 35226.0328*$numberDays;

            $laivay_moi = 260266.557*$numberDays;

            $khauhao_moi = 736909.082*$numberDays;





            if ( (substr($ketthuc, 3,2) - substr($batdau, 3,2) == 1 ) && (substr($batdau, 0,2) - substr($ketthuc, 0,2) == 1 ) && $xethue>45000000) {

                $xethue = 45000000;

            }

            if (substr($batdau, 3,2)=="01" && substr($ketthuc, 3,2)=="02") {

                if ( substr($ketthuc, 6,4)%100 != 0 && substr($ketthuc, 6,4)%4 == 0 && $numberDays == 31 ) {

                    $xethue = 45000000;

                }

                if ( substr($ketthuc, 6,4)%100 != 0 && substr($ketthuc, 6,4)%4 != 0 && $numberDays == 30) {

                    $xethue = 45000000;

                }

            }



            if ( (date('m',$startTimeStamp) == date('m',$endTimeStamp)) && $numberDays == date('t',$endTimeStamp) ) {

                $xethue = 45000000;

                $bh_moi = 2609147;

                $phiduongbo_moi = 1074394;

                $laivay_moi = 7938130;

                $khauhao_moi = 22475727;

                $bh = 2609147;

                $phiduongbo = 1074394;

                $laivay = 7938130;

                $khauhao = 22475727;

                $anca = 1500000;

            }





            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', 'BÁO CÁO KẾT QUẢ HOẠT ĐỘNG CỦA ĐỘI XE')

                ->setCellValue('A2', 'Từ ngày '.$this->lib->hien_thi_ngay_thang($batdau).' đến ngày '.$this->lib->hien_thi_ngay_thang($ketthuc))

               ->setCellValue('A4', 'TT')

               ->setCellValue('B4', 'Nội dung')

               ->setCellValue('A5', 'I')

               ->setCellValue('B5', 'Doanh thu')

               ->setCellValue('A6', 'II')

               ->setCellValue('B6', 'Chi phí')

               ->setCellValue('A7', 'A')

               ->setCellValue('B7', 'Chi phí cố định')

               ->setCellValue('A8', '1.')

               ->setCellValue('B8', 'Khấu hao')

               ->setCellValue('A9', '2.')

               ->setCellValue('B9', 'Lãi vay')

               ->setCellValue('A10', '3.')

               ->setCellValue('B10', 'Phí sử dụng đường bộ')

               ->setCellValue('A11', '4.')

               ->setCellValue('B11', 'K.định, B.hiểm')

               ->setCellValue('A12', 'B')

               ->setCellValue('B12', 'Có hóa đơn chứng từ')

               ->setCellValue('A13', '1.')

               ->setCellValue('B13', 'Lương tài xế')

               ->setCellValue('A14', '2.')

               ->setCellValue('B14', 'Chi phí nhiên liệu')

               ->setCellValue('A15', '')

               ->setCellValue('B15', 'Dầu dọc đường')

               ->setCellValue('A16', '3.')

               ->setCellValue('B16', 'Chi phí cầu đường')

               ->setCellValue('A17', '4.')

               ->setCellValue('B17', 'SCTX, v.tư, vá vỏ, thay vỏ')

               ->setCellValue('A18', '5.')

               ->setCellValue('B18', 'Ăn ca, công tác phí')

               ->setCellValue('A19', '6.')

               ->setCellValue('B19', 'Chi phí khác')

               ->setCellValue('A20', 'C')

               ->setCellValue('B20', 'Không hóa đơn chứng từ')

               ->setCellValue('A21', '1.')

               ->setCellValue('B21', 'Chi phí phát sinh')

               ->setCellValue('A22', '2.')

               ->setCellValue('B22', 'Chi phí công an')

               ->setCellValue('A23', '3.')

               ->setCellValue('B23', 'Chi phí bồi dưỡng')

               ->setCellValue('A24', '4.')

               ->setCellValue('B24', 'Cân xe')

               ->setCellValue('A25', '5.')

               ->setCellValue('B25', 'Quét cont')

               ->setCellValue('A26', '6.')

               ->setCellValue('B26', 'Vé cổng')

               ->setCellValue('A27', '7.')

               ->setCellValue('B27', 'Rửa xe - vá vỏ')

               ->setCellValue('A28', '8.')

               ->setCellValue('B28', 'Thưởng vượt')

               ->setCellValue('A29', '9.')

               ->setCellValue('B29', 'Hoa hồng')

               ->setCellValue('A30', '10.')

               ->setCellValue('B30', 'Phí chuyển tiền')

               ->setCellValue('A31', 'III')

               ->setCellValue('B31', 'LN trước thuế');





            if ($vehicles) {

                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."4", $vehicle->vehicle_number);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."5", isset($doanhthu[$vehicle->vehicle_id])?$doanhthu[$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."8", round($vehicle->vehicle_type==2?$xethue:($vehicle->vehicle_type==1?($khauhao_moi):($vehicle->vehicle_type==3?($khauhao):0))) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."9", round($vehicle->vehicle_type==2?0:($vehicle->vehicle_type==1?($laivay_moi):($vehicle->vehicle_type==3?($laivay):0))) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."10", round($vehicle->vehicle_type==2?0:($vehicle->vehicle_type==1?($phiduongbo_moi):($vehicle->vehicle_type==3?($phiduongbo):0))) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."11", round($vehicle->vehicle_type==2?0:($vehicle->vehicle_type==1?($bh_moi):($vehicle->vehicle_type==3?($bh):0))) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."14", isset($chiphidau[$vehicle->vehicle_id])?round($chiphidau[$vehicle->vehicle_id]):0 );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."15", isset($daudocduong[$vehicle->vehicle_id])?round($daudocduong[$vehicle->vehicle_id]):0 );

                }



                



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."17", isset($doanhthu[$vehicle->vehicle_id])?$doanhthu[$vehicle->vehicle_id]*(7/100):0);

                }



                



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."16", (isset($road_data['bridge_cost'][$vehicle->vehicle_id])?$road_data['bridge_cost'][$vehicle->vehicle_id]:0)+(isset($cauduongphatsinh[$vehicle->vehicle_id])?$cauduongphatsinh[$vehicle->vehicle_id]:0));

                }



                



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."18", round( isset($anca)?$anca:((isset($road_data['road_time'][$vehicle->vehicle_id])?$road_data['road_time'][$vehicle->vehicle_id]:0)*120000) ) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."19", isset($chiphihd[$vehicle->vehicle_id])?$chiphihd[$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."21", isset($chiphiphatsinh[$vehicle->vehicle_id])?$chiphiphatsinh[$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."22", isset($road_data['police_cost'][$vehicle->vehicle_id])?$road_data['police_cost'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."23", isset($warehouse_data['boiduong'][$vehicle->vehicle_id])?$warehouse_data['boiduong'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."24", isset($warehouse_data['canxe'][$vehicle->vehicle_id])?$warehouse_data['canxe'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."25", isset($warehouse_data['quetcont'][$vehicle->vehicle_id])?$warehouse_data['quetcont'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."26", isset($warehouse_data['vecong'][$vehicle->vehicle_id])?$warehouse_data['vecong'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."27", isset($road_data['tire_cost'][$vehicle->vehicle_id])?$road_data['tire_cost'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."28", isset($vuottai[$vehicle->vehicle_id])?$vuottai[$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."29", isset($hoahong[$vehicle->vehicle_id])?$hoahong[$vehicle->vehicle_id]:0);

                }

                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."30", isset($phichuyentien[$vehicle->vehicle_id])?$phichuyentien[$vehicle->vehicle_id]:0);

                }



                



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."13", '=('.$current_column.'5-'.$current_column.'20)*11%');

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."7",  '=SUM('.$current_column.'8:'.$current_column.'11)');

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."6",  '='.$current_column.'7+'.$current_column.'12+'.$current_column.'20');

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."12", '=SUM('.$current_column.'13:'.$current_column.'19)-'.$current_column.'15');

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."20", '=SUM('.$current_column.'21:'.$current_column.'30)');

                }

                



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."31", '='.$current_column.'5-'.$current_column.'6');

                }

                



            }



            $lastColumn = $current_column;

            ++$current_column;



            $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."4", 'CỘNG');



            for ($m=5; $m < 32; $m++) { 

                $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column .$m, '=SUM(C'.$m.':'.$lastColumn.$m.')');

            }





            $objPHPExcel->getActiveSheet()->getStyle('A1:A31')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:A31')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('B4:'.$current_column."4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('B4:'.$current_column."4")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A4:'.$current_column."7")->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle("A1:".$current_column."31")->getFont()->setName('Times New Roman');

            $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(18);

            $objPHPExcel->getActiveSheet()->getStyle("A4:".$current_column."4")->getAlignment()->setWrapText(true);

            $objPHPExcel->getActiveSheet()->getStyle("A4:".$current_column."4")->getFont()->setSize(14);

            $objPHPExcel->getActiveSheet()->getStyle("A5:".$current_column."31")->getFont()->setSize(13);



            $objPHPExcel->getActiveSheet()->getStyle('C5:'.$current_column."31")->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('C21:'.$current_column."31")->getNumberFormat()->setFormatCode("#,##0;[Black](-#,##0)");

            //$objPHPExcel->getActiveSheet()->getStyle('C22:'.$current_column."22")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);



            $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:H2');



            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(25);

            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(33);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);



            $objPHPExcel->getActiveSheet()->getStyle('A7:'.$current_column."7")->getFont()->setItalic(true);

            $objPHPExcel->getActiveSheet()->getStyle('A12:'.$current_column."12")->getFont()->setItalic(true);

            $objPHPExcel->getActiveSheet()->getStyle('A20:'.$current_column."20")->getFont()->setItalic(true);

            $objPHPExcel->getActiveSheet()->getStyle('A15:'.$current_column."15")->getFont()->setItalic(true);

            $objPHPExcel->getActiveSheet()->getStyle('A31:'.$current_column."31")->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle('A12:'.$current_column."12")->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle('A20:'.$current_column."20")->getFont()->setBold(true);



            $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray(

                array(

                    

                    'fill' => array(

                        'type' => PHPExcel_Style_Fill::FILL_SOLID,

                        'color' => array('rgb' => 'FFFD51')

                    )

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => 'FF0000')

                    )

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A4:'.$current_column."31")->applyFromArray(

                array(

                    'borders' => array(

                        'outline' => array(

                            'style' => PHPExcel_Style_Border::BORDER_THIN,

                            'color' => array('argb' => '000000'),

                        ),

                    ),

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A4:'.$current_column."4")->applyFromArray(

                array(

                    'borders' => array(

                        'outline' => array(

                            'style' => PHPExcel_Style_Border::BORDER_THIN,

                            'color' => array('argb' => '000000'),

                        ),

                    ),

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A1:A31')->applyFromArray(

                array(

                    'borders' => array(

                        'outline' => array(

                            'style' => PHPExcel_Style_Border::BORDER_THIN,

                            'color' => array('argb' => '000000'),

                        ),

                    ),

                )

            );



            



            // Set properties

            $objPHPExcel->getProperties()->setCreator("Tan Cang Mien Trung")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Report")

                            ->setSubject("Report")

                            ->setDescription("Report.")

                            ->setKeywords("Report")

                            ->setCategory("Report");

            $objPHPExcel->getActiveSheet()->setTitle("Bao cao ket qua");



            



            $objPHPExcel->getActiveSheet()->freezePane('C1');



            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BAO CAO ".$this->lib->hien_thi_ngay_thang($batdau)."_".$this->lib->hien_thi_ngay_thang($ketthuc).".xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        }

    }



    function exportsecond(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->report) || json_decode($_SESSION['user_permission_action'])->report != "report") {

            return $this->view->redirect('user/login');

        }

        

        if ($this->registry->router->param_id != null && $this->registry->router->page != null) {

            $batdau = $this->registry->router->param_id;

            $ketthuc = $this->registry->router->page;

            $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

            $data_v = array(

                'where'=>'vehicle_id IN (SELECT vehicle FROM control WHERE status=1 AND control_number=2) AND vehicle_id IN (SELECT vehicle FROM vehicle_work WHERE end_work > '.$batdau.')',

            );



            $vehicle_model = $this->model->get('vehicleModel');

            $vehicles = $vehicle_model->getAllVehicle($data_v);



            $shipment_model = $this->model->get('shipmentModel');

            $join = array('table'=>'customer, vehicle','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND vehicle.vehicle_id IN (SELECT vehicle FROM control WHERE status=1 AND control_number=2)');

            $shipments = $shipment_model->getAllShipment(array('where'=>'shipment_date >= '.$batdau.' AND shipment_date < '.$ngayketthuc),$join);



            $doanhthu = array();

            $chiphiphatsinh = array();



            $warehouse_model = $this->model->get('warehouseModel');

            



            $road_model = $this->model->get('roadModel');



            $warehouse_data = array();

            $road_data = array();





            $vuottai = array();

            $chiphihd = array();

            $hoahong = array();

            $chiphidau = array();

            $daudocduong = array();

            $cauduongphatsinh = array();

            $phichuyentien = array();



            $k=0;
        foreach ($shipments as $shipment) {
            $qr = "SELECT * FROM vehicle_work WHERE vehicle = ".$shipment->vehicle." AND start_work <= ".$shipment->shipment_date." AND end_work >= ".$shipment->shipment_date;
            if (!$shipment_model->queryShipment($qr)) {
                unset($shipments[$k]);
            }
            else{
                $check_sub = 1;

                if ($shipment->shipment_sub==1) {

                   $check_sub = 0;

                }



                $doanhthu[$shipment->vehicle] = isset($doanhthu[$shipment->vehicle])?($doanhthu[$shipment->vehicle]+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess) : (0+$shipment->shipment_revenue+$shipment->revenue_other+$shipment->shipment_charge_excess);

                $chiphiphatsinh[$shipment->vehicle] = isset($chiphiphatsinh[$shipment->vehicle])?($chiphiphatsinh[$shipment->vehicle]+(($shipment->approve==1)?$shipment->cost_add:0)) : (0+(($shipment->approve==1)?$shipment->cost_add:0));

                $vuottai[$shipment->vehicle] = isset($vuottai[$shipment->vehicle])?($vuottai[$shipment->vehicle]+$shipment->shipment_bonus*$check_sub) : (0+$shipment->shipment_bonus*$check_sub);

                $chiphihd[$shipment->vehicle] = isset($chiphihd[$shipment->vehicle])?($chiphihd[$shipment->vehicle]+$shipment->cost_vat) : (0+$shipment->cost_vat);

                $hoahong[$shipment->vehicle] = isset($hoahong[$shipment->vehicle])?($hoahong[$shipment->vehicle]+($shipment->commission*$shipment->commission_number)) : (0+$shipment->commission*$shipment->commission_number);

                $cauduongphatsinh[$shipment->vehicle] = isset($cauduongphatsinh[$shipment->vehicle])?($cauduongphatsinh[$shipment->vehicle]+round($shipment->bridge_cost_add/1.1)) : (0+round($shipment->bridge_cost_add/1.1));

                if ($shipment->shipment_ton > 0 && $shipment->shipment_date >= strtotime('01-01-2016')) {
                    $phichuyentien[$shipment->vehicle] = isset($phichuyentien[$shipment->vehicle])?($phichuyentien[$shipment->vehicle]+3300) : (0+3300);
                }


                $daubai1 = ($shipment->oil_add_dc == 5)?($shipment->oil_add*$shipment->oil_cost):0;

                $daubai2 = ($shipment->oil_add_dc2 == 5)?($shipment->oil_add2*$shipment->oil_cost):0;

                $dau1 = $shipment->oil_add*$shipment->oil_cost;

                $dau2 = $shipment->oil_add2*$shipment->oil_cost;



                $chiphidau[$shipment->vehicle] = isset($chiphidau[$shipment->vehicle])?($chiphidau[$shipment->vehicle]+($dau1+$dau2)*$check_sub):($dau1+$dau2)*$check_sub;

                $daudocduong[$shipment->vehicle] = isset($daudocduong[$shipment->vehicle])?($daudocduong[$shipment->vehicle]+($daubai1+$daubai2)*$check_sub):($daubai1+$daubai2)*$check_sub;



                $roads = $road_model->getAllRoad(array('where'=>'road_from = '.$shipment->shipment_from.' AND road_to = '.$shipment->shipment_to.' AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));

                

               

                $check_rong = 0;



                

                

                foreach ($roads as $road) {

                    $road_data['bridge_cost'][$shipment->vehicle] = isset($road_data['bridge_cost'][$shipment->vehicle])?($road_data['bridge_cost'][$shipment->vehicle]+$road->bridge_cost*$check_sub):0+$road->bridge_cost*$check_sub;

                    $road_data['police_cost'][$shipment->vehicle] = isset($road_data['police_cost'][$shipment->vehicle])?($road_data['police_cost'][$shipment->vehicle]+$road->police_cost*$check_sub):0+$road->police_cost*$check_sub;

                    $road_data['oil_cost'][$shipment->vehicle] = isset($road_data['oil_cost'][$shipment->vehicle])?($road_data['oil_cost'][$shipment->vehicle]+($road->road_oil*$shipment->oil_cost)*$check_sub):0+($road->road_oil*$shipment->oil_cost)*$check_sub;

                    $road_data['road_time'][$shipment->vehicle] = isset($road_data['road_time'][$shipment->vehicle])?($road_data['road_time'][$shipment->vehicle]+$road->road_time*$check_sub):0+$road->road_time*$check_sub;

                    $road_data['tire_cost'][$shipment->vehicle] = isset($road_data['tire_cost'][$shipment->vehicle])?($road_data['tire_cost'][$shipment->vehicle]+$road->tire_cost*$check_sub):0+$road->tire_cost*$check_sub;

                    



                    $chek_rong = ($road->way == 0)?1:0;

                }





                $warehouses = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$shipment->shipment_from.' OR warehouse_code = '.$shipment->shipment_to.') AND start_time <= '.$shipment->shipment_date.' AND end_time >= '.$shipment->shipment_date));

            



                $boiduong_cont = 0;

                $boiduong_tan = 0;



                $canxe = 0;

                $quetcont = 0;

                $vecong = 0;

                $boiduong = 0;

                

                foreach ($warehouses as $warehouse) {

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
                        if ($shipment->shipment_ton > 0) {

                            if ($warehouse->warehouse_cont != 0) {

                                $boiduong_cont += $warehouse->warehouse_cont;

                                $boiduong += $warehouse->warehouse_add;

                            }

                            if ($warehouse->warehouse_ton != 0){

                                $boiduong_tan += $trongluong * $warehouse->warehouse_ton;

                                $boiduong += $trongluong * $warehouse->warehouse_ton;

                            }
                        }



                    }

                    else{

                        if ($shipment->shipment_ton > 0) {

                            $boiduong_cont += $warehouse->warehouse_add;

                            $boiduong += $warehouse->warehouse_add;

                        }

                    }

                }

                $warehouse_data['boiduong_cn'][$shipment->vehicle] = isset($warehouse_data['boiduong_cn'][$shipment->vehicle])?($warehouse_data['boiduong_cn'][$shipment->vehicle]+($boiduong_cont+$boiduong_tan)*$check_sub):0+($boiduong_cont+$boiduong_tan)*$check_sub;



                $warehouse_data['boiduong'][$shipment->vehicle] = isset($warehouse_data['boiduong'][$shipment->vehicle])?($warehouse_data['boiduong'][$shipment->vehicle]+$boiduong*$check_sub):0+$boiduong*$check_sub;

                $warehouse_data['canxe'][$shipment->vehicle] = isset($warehouse_data['canxe'][$shipment->vehicle])?($warehouse_data['canxe'][$shipment->vehicle]+$canxe*$check_sub):0+$canxe*$check_sub;

                $warehouse_data['quetcont'][$shipment->vehicle] = isset($warehouse_data['quetcont'][$shipment->vehicle])?($warehouse_data['quetcont'][$shipment->vehicle]+$quetcont*$check_sub):0+$quetcont*$check_sub;

                $warehouse_data['vecong'][$shipment->vehicle] = isset($warehouse_data['vecong'][$shipment->vehicle])?($warehouse_data['vecong'][$shipment->vehicle]+$vecong*$check_sub):0+$vecong*$check_sub;

                
            }
            $k++;
            
        }







            $timeDiff = abs($ketthuc - $batdau);



            $numberDays = $timeDiff/86400;  // 86400 seconds in one day



            // and you might want to convert to integer

            $numberDays = intval($numberDays)+1;





            $xethue = 1475410*$numberDays;



            $bh = 85545.8033*$numberDays;

            $phiduongbo = 35226.0328*$numberDays;

            $laivay = 260266.557*$numberDays;

            $khauhao = 736909.082*$numberDays;



            $bh_moi = 85545.8033*$numberDays;

            $phiduongbo_moi = 35226.0328*$numberDays;

            $laivay_moi = 260266.557*$numberDays;

            $khauhao_moi = 736909.082*$numberDays;





            if ( (substr($ketthuc, 3,2) - substr($batdau, 3,2) == 1 ) && (substr($batdau, 0,2) - substr($ketthuc, 0,2) == 1 ) && $xethue>45000000) {

                $xethue = 45000000;

            }

            if (substr($batdau, 3,2)=="01" && substr($ketthuc, 3,2)=="02") {

                if ( substr($ketthuc, 6,4)%100 != 0 && substr($ketthuc, 6,4)%4 == 0 && $numberDays == 31 ) {

                    $xethue = 45000000;

                }

                if ( substr($ketthuc, 6,4)%100 != 0 && substr($ketthuc, 6,4)%4 != 0 && $numberDays == 30) {

                    $xethue = 45000000;

                }

            }



            if ( (date('m',$startTimeStamp) == date('m',$endTimeStamp)) && $numberDays == date('t',$endTimeStamp) ) {

                $xethue = 45000000;

                $bh_moi = 2609147;

                $phiduongbo_moi = 1074394;

                $laivay_moi = 7938130;

                $khauhao_moi = 22475727;

                $bh = 2609147;

                $phiduongbo = 1074394;

                $laivay = 7938130;

                $khauhao = 22475727;

                $anca = 1500000;

            }





            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', 'BÁO CÁO KẾT QUẢ HOẠT ĐỘNG CỦA ĐỘI XE')

                ->setCellValue('A2', 'Từ ngày '.$this->lib->hien_thi_ngay_thang($batdau).' đến ngày '.$this->lib->hien_thi_ngay_thang($ketthuc))

               ->setCellValue('A4', 'TT')

               ->setCellValue('B4', 'Nội dung')

               ->setCellValue('A5', 'I')

               ->setCellValue('B5', 'Doanh thu')

               ->setCellValue('A6', 'II')

               ->setCellValue('B6', 'Chi phí')

               ->setCellValue('A7', 'A')

               ->setCellValue('B7', 'Chi phí cố định')

               ->setCellValue('A8', '1.')

               ->setCellValue('B8', 'Khấu hao')

               ->setCellValue('A9', '2.')

               ->setCellValue('B9', 'Lãi vay')

               ->setCellValue('A10', '3.')

               ->setCellValue('B10', 'Phí sử dụng đường bộ')

               ->setCellValue('A11', '4.')

               ->setCellValue('B11', 'K.định, B.hiểm')

               ->setCellValue('A12', 'B')

               ->setCellValue('B12', 'Có hóa đơn chứng từ')

               ->setCellValue('A13', '1.')

               ->setCellValue('B13', 'Lương tài xế')

               ->setCellValue('A14', '2.')

               ->setCellValue('B14', 'Chi phí nhiên liệu')

               ->setCellValue('A15', '')

               ->setCellValue('B15', 'Dầu dọc đường')

               ->setCellValue('A16', '3.')

               ->setCellValue('B16', 'Chi phí cầu đường')

               ->setCellValue('A17', '4.')

               ->setCellValue('B17', 'SCTX, v.tư, vá vỏ, thay vỏ')

               ->setCellValue('A18', '5.')

               ->setCellValue('B18', 'Ăn ca, công tác phí')

               ->setCellValue('A19', '6.')

               ->setCellValue('B19', 'Chi phí khác')

               ->setCellValue('A20', 'C')

               ->setCellValue('B20', 'Không hóa đơn chứng từ')

               ->setCellValue('A21', '1.')

               ->setCellValue('B21', 'Chi phí phát sinh')

               ->setCellValue('A22', '2.')

               ->setCellValue('B22', 'Chi phí công an')

               ->setCellValue('A23', '3.')

               ->setCellValue('B23', 'Chi phí bồi dưỡng')

               ->setCellValue('A24', '4.')

               ->setCellValue('B24', 'Cân xe')

               ->setCellValue('A25', '5.')

               ->setCellValue('B25', 'Quét cont')

               ->setCellValue('A26', '6.')

               ->setCellValue('B26', 'Vé cổng')

               ->setCellValue('A27', '7.')

               ->setCellValue('B27', 'Rửa xe - vá vỏ')

               ->setCellValue('A28', '8.')

               ->setCellValue('B28', 'Thưởng vượt')

               ->setCellValue('A29', '9.')

               ->setCellValue('B29', 'Hoa hồng')

               ->setCellValue('A30', '10.')

               ->setCellValue('B30', 'Phí chuyển tiền')

               ->setCellValue('A31', 'III')

               ->setCellValue('B31', 'LN trước thuế');





            if ($vehicles) {

                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."4", $vehicle->vehicle_number);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."5", isset($doanhthu[$vehicle->vehicle_id])?$doanhthu[$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."8", round($vehicle->vehicle_type==2?$xethue:($vehicle->vehicle_type==1?($khauhao_moi):($vehicle->vehicle_type==3?($khauhao):0))) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."9", round($vehicle->vehicle_type==2?0:($vehicle->vehicle_type==1?($laivay_moi):($vehicle->vehicle_type==3?($laivay):0))) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."10", round($vehicle->vehicle_type==2?0:($vehicle->vehicle_type==1?($phiduongbo_moi):($vehicle->vehicle_type==3?($phiduongbo):0))) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."11", round($vehicle->vehicle_type==2?0:($vehicle->vehicle_type==1?($bh_moi):($vehicle->vehicle_type==3?($bh):0))) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."14", isset($chiphidau[$vehicle->vehicle_id])?round($chiphidau[$vehicle->vehicle_id]):0 );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."15", isset($daudocduong[$vehicle->vehicle_id])?round($daudocduong[$vehicle->vehicle_id]):0 );

                }



                



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."17", isset($doanhthu[$vehicle->vehicle_id])?$doanhthu[$vehicle->vehicle_id]*(7/100):0);

                }



                



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."16", (isset($road_data['bridge_cost'][$vehicle->vehicle_id])?$road_data['bridge_cost'][$vehicle->vehicle_id]:0)+(isset($cauduongphatsinh[$vehicle->vehicle_id])?$cauduongphatsinh[$vehicle->vehicle_id]:0));

                }



                



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."18", round( isset($anca)?$anca:((isset($road_data['road_time'][$vehicle->vehicle_id])?$road_data['road_time'][$vehicle->vehicle_id]:0)*120000) ) );

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."19", isset($chiphihd[$vehicle->vehicle_id])?$chiphihd[$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."21", isset($chiphiphatsinh[$vehicle->vehicle_id])?$chiphiphatsinh[$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."22", isset($road_data['police_cost'][$vehicle->vehicle_id])?$road_data['police_cost'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."23", isset($warehouse_data['boiduong'][$vehicle->vehicle_id])?$warehouse_data['boiduong'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."24", isset($warehouse_data['canxe'][$vehicle->vehicle_id])?$warehouse_data['canxe'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."25", isset($warehouse_data['quetcont'][$vehicle->vehicle_id])?$warehouse_data['quetcont'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."26", isset($warehouse_data['vecong'][$vehicle->vehicle_id])?$warehouse_data['vecong'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."27", isset($road_data['tire_cost'][$vehicle->vehicle_id])?$road_data['tire_cost'][$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."28", isset($vuottai[$vehicle->vehicle_id])?$vuottai[$vehicle->vehicle_id]:0);

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."29", isset($hoahong[$vehicle->vehicle_id])?$hoahong[$vehicle->vehicle_id]:0);

                }

                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."30", isset($phichuyentien[$vehicle->vehicle_id])?$phichuyentien[$vehicle->vehicle_id]:0);

                }



                



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."13", '=('.$current_column.'5-'.$current_column.'20)*11%');

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."7",  '=SUM('.$current_column.'8:'.$current_column.'11)');

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."6",  '='.$current_column.'7+'.$current_column.'12+'.$current_column.'20');

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."12", '=SUM('.$current_column.'13:'.$current_column.'19)-'.$current_column.'15');

                }



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."20", '=SUM('.$current_column.'21:'.$current_column.'30)');

                }

                



                $current_column = 'B';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."31", '='.$current_column.'5-'.$current_column.'6');

                }

                



            }



            $lastColumn = $current_column;

            ++$current_column;



            $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."4", 'CỘNG');



            for ($m=5; $m < 32; $m++) { 

                $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column .$m, '=SUM(C'.$m.':'.$lastColumn.$m.')');

            }





            $objPHPExcel->getActiveSheet()->getStyle('A1:A31')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:A31')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('B4:'.$current_column."4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('B4:'.$current_column."4")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A4:'.$current_column."7")->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle("A1:".$current_column."31")->getFont()->setName('Times New Roman');

            $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(18);

            $objPHPExcel->getActiveSheet()->getStyle("A4:".$current_column."4")->getAlignment()->setWrapText(true);

            $objPHPExcel->getActiveSheet()->getStyle("A4:".$current_column."4")->getFont()->setSize(14);

            $objPHPExcel->getActiveSheet()->getStyle("A5:".$current_column."31")->getFont()->setSize(13);



            $objPHPExcel->getActiveSheet()->getStyle('C5:'.$current_column."31")->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('C21:'.$current_column."31")->getNumberFormat()->setFormatCode("#,##0;[Black](-#,##0)");

            //$objPHPExcel->getActiveSheet()->getStyle('C22:'.$current_column."22")->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE);



            $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:H2');



            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(25);

            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(33);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);



            $objPHPExcel->getActiveSheet()->getStyle('A7:'.$current_column."7")->getFont()->setItalic(true);

            $objPHPExcel->getActiveSheet()->getStyle('A12:'.$current_column."12")->getFont()->setItalic(true);

            $objPHPExcel->getActiveSheet()->getStyle('A20:'.$current_column."20")->getFont()->setItalic(true);

            $objPHPExcel->getActiveSheet()->getStyle('A15:'.$current_column."15")->getFont()->setItalic(true);

            $objPHPExcel->getActiveSheet()->getStyle('A31:'.$current_column."31")->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle('A12:'.$current_column."12")->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle('A20:'.$current_column."20")->getFont()->setBold(true);



            $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray(

                array(

                    

                    'fill' => array(

                        'type' => PHPExcel_Style_Fill::FILL_SOLID,

                        'color' => array('rgb' => 'FFFD51')

                    )

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => 'FF0000')

                    )

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A4:'.$current_column."31")->applyFromArray(

                array(

                    'borders' => array(

                        'outline' => array(

                            'style' => PHPExcel_Style_Border::BORDER_THIN,

                            'color' => array('argb' => '000000'),

                        ),

                    ),

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A4:'.$current_column."4")->applyFromArray(

                array(

                    'borders' => array(

                        'outline' => array(

                            'style' => PHPExcel_Style_Border::BORDER_THIN,

                            'color' => array('argb' => '000000'),

                        ),

                    ),

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A1:A31')->applyFromArray(

                array(

                    'borders' => array(

                        'outline' => array(

                            'style' => PHPExcel_Style_Border::BORDER_THIN,

                            'color' => array('argb' => '000000'),

                        ),

                    ),

                )

            );



            



            // Set properties

            $objPHPExcel->getProperties()->setCreator("Tan Cang Mien Trung")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Report")

                            ->setSubject("Report")

                            ->setDescription("Report.")

                            ->setKeywords("Report")

                            ->setCategory("Report");

            $objPHPExcel->getActiveSheet()->setTitle("Bao cao ket qua");



            



            $objPHPExcel->getActiveSheet()->freezePane('C1');



            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BAO CAO ".$this->lib->hien_thi_ngay_thang($batdau)."_".$this->lib->hien_thi_ngay_thang($ketthuc).".xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        }

    }



}

?>