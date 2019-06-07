<?php

Class costController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->cost) || json_decode($_SESSION['user_permission_action'])->cost != "cost") {
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

            $trangthai = isset($_POST['trangthai']) ? $_POST['trangthai'] : null;

            

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'shipment_date ASC, shipment_id ';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

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



        $join = array('table'=>'customer, vehicle, cont_unit','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND cont_unit = cont_unit_id');



        $shipment_model = $this->model->get('shipmentModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;



        $data = array(

            'where' => "(shipment_sub IS NULL OR shipment_sub != 1)",

            );

        if($batdau != "" && $ketthuc != "" ){

            $data['where'] = $data['where'].' AND shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc);

        }

        if($xe > 0){

            $data['where'] = $data['where'].' AND vehicle = '.$xe;

        }



        /*if ($_SESSION['role_logined'] == 3) {

            $data['where'] = $data['where'].' AND shipment_create_user = '.$_SESSION['userid_logined'];

            

        }*/

        //$data['where'] = $data['where'].' AND way != 0';



        $tongsodong = count($shipment_model->getAllShipment($data,$join));

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

        $this->view->data['vong'] = $vong;

        $this->view->data['trangthai'] = $trangthai;



        $this->view->data['limit'] = $limit;





        $data = array(

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            'where' => "(shipment_sub IS NULL OR shipment_sub != 1)",

            );

        if($batdau != "" && $ketthuc != "" ){

            $data['where'] = $data['where'].' AND shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc);

        }

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

                    OR bill_number LIKE "%'.$keyword.'%"

                    OR customer_name LIKE "%'.$keyword.'%"

                    OR shipment_from in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                    OR shipment_to in (SELECT place_id FROM place WHERE place_name LIKE "%'.$keyword.'%" ) 

                    '.$ngay.'

                        )';

            $data['where'] = $data['where']." AND ".$search;

        }



        //$data['where'] = $data['where'].' AND way != 0';

        

        $warehouse_model = $this->model->get('warehouseModel');

        $road_model = $this->model->get('roadModel');

        $shipment_cost_model = $this->model->get('shipmentcostModel');

        $place_model = $this->model->get('placeModel');

        $place_data = array();

        $cost_data = array();

        $warehouse_data = array();

        $road_data = array();

        

        $shipments = $shipment_model->getAllShipment($data,$join);




        $k=0;

        foreach ($shipments as $ship) {
            $check_sub = 1;
            if ($ship->shipment_sub==1) {
               $check_sub = 0;
            }

           $qr = "SELECT * FROM vehicle_work WHERE vehicle = ".$ship->vehicle." AND start_work <= ".$ship->shipment_date." AND end_work >= ".$ship->shipment_date;
            if ($shipment_model->queryShipment($qr)) {
                unset($shipments[$k]);
            }
            else{

                $cost_join = array('table'=>'cost_list','where'=>'cost_list = cost_list_id');
                $shipment_costs = $shipment_cost_model->getAllShipment(array('where'=>'shipment='.$ship->shipment_id),$cost_join);

                foreach ($shipment_costs as $cost) {
                    $cost_data[$cost->cost_list_type][$ship->shipment_id] = isset($cost_data[$cost->cost_list_type][$ship->shipment_id])?$cost_data[$cost->cost_list_type][$ship->shipment_id]+$cost->cost:$cost->cost;
                }

                $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));

            

                   $road_data['oil_add'][$ship->shipment_id] = ($ship->oil_add_dc == 5)?$ship->oil_add:0;

                   $road_data['oil_add2'][$ship->shipment_id] = ($ship->oil_add_dc2 == 5)?$ship->oil_add2:0;



                    $chek_rong = 0;

                    

                    foreach ($roads as $road) {

                        $road_data['bridge_cost'][$ship->shipment_id] = isset($road_data['bridge_cost'][$ship->shipment_id])?$road_data['bridge_cost'][$ship->shipment_id]+$road->bridge_cost*$check_sub:$road->bridge_cost*$check_sub;

                        $road_data['police_cost'][$ship->shipment_id] = isset($road_data['police_cost'][$ship->shipment_id])?$road_data['police_cost'][$ship->shipment_id]+$road->police_cost*$check_sub:$road->police_cost*$check_sub;

                        $road_data['tire_cost'][$ship->shipment_id] = isset($road_data['tire_cost'][$ship->shipment_id])?$road_data['tire_cost'][$ship->shipment_id]+$road->tire_cost*$check_sub:$road->tire_cost*$check_sub;

                        
                        if($road->road_oil_ton > 0){
                            $road_data['oil_cost'][$ship->shipment_id] = isset($road_data['oil_cost'][$ship->shipment_id])?$road_data['oil_cost'][$ship->shipment_id]+$road->road_oil_ton*$check_sub:$road->road_oil_ton*$check_sub;
                        }
                        else{
                            $road_data['oil_cost'][$ship->shipment_id] = isset($road_data['oil_cost'][$ship->shipment_id])?$road_data['oil_cost'][$ship->shipment_id]+$road->road_oil*$check_sub:$road->road_oil*$check_sub;
                        }

                        $road_data['road_add'][$ship->shipment_id] = isset($road_data['road_add'][$ship->shipment_id])?$road_data['road_add'][$ship->shipment_id]+$road->road_add*$check_sub:$road->road_add*$check_sub;

                        $road_data['way'][$ship->shipment_id] = $road->way;

                        $chek_rong = ($road->way == 0)?1:0;



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

                            if ($warehouse->warehouse_ton != 0 && $chek_rong==0){

                                $boiduong_tan += $trongluong * $warehouse->warehouse_ton;

                            }



                            $warehouse_data['warehouse_weight'][$warehouse->warehouse_code] = $warehouse->warehouse_weight*$check_sub;

                            $warehouse_data['warehouse_clean'][$warehouse->warehouse_code] = $warehouse->warehouse_clean*$check_sub;

                            $warehouse_data['warehouse_gate'][$warehouse->warehouse_code] = $warehouse->warehouse_gate*$check_sub;

                            $warehouse_data['warehouse_add'][$warehouse->warehouse_code] = $warehouse->warehouse_add*$check_sub;



                    

                        

                    }

                    $warehouse_data['boiduong_cn'][$ship->shipment_id] = ($boiduong_cont+$boiduong_tan)*$check_sub;


                    $places = $place_model->getAllPlace(array('where'=>'place_id = '.$ship->shipment_from.' OR place_id = '.$ship->shipment_to));


                    foreach ($places as $place) {

                        

                            $place_data['place_id'][$place->place_id] = $place->place_id;

                            $place_data['place_name'][$place->place_id] = $place->place_name;

                        

                        

                    }
            } 

           $k++;

        }


        $this->view->data['shipments'] = $shipments;

        $this->view->data['warehouse'] = $warehouse_data;

        $this->view->data['road'] = $road_data;

        $this->view->data['place'] = $place_data;

        $this->view->data['cost_data'] = $cost_data;

        $this->view->show('cost/index');

    }



    function export(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        $info_model = $this->model->get('infoModel');
        $infos = $info_model->getLastInfo();

        $batdau = $this->registry->router->param_id;

        $ketthuc = $this->registry->router->page;

        $xe = $this->registry->router->order_by;

        $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

        $shipment_model = $this->model->get('shipmentModel');

        $join = array('table'=>'customer, vehicle, cont_unit','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND cont_unit = cont_unit_id');



        $data = array(

            'where' => "(shipment_sub IS NULL OR shipment_sub != 1)",

            );

        if($batdau != "" && $ketthuc != "" ){

            $data['where'] = $data['where'].' AND shipment_date >= '.$batdau.' AND shipment_date < '.$ngayketthuc;

        }

        if($xe != ""){

            $data['where'] = $data['where'].' AND vehicle = '.$xe;

        }

        



        $data['order_by'] = 'shipment_date';

        $data['order'] = 'ASC';

        

        $warehouse_model = $this->model->get('warehouseModel');

        $road_model = $this->model->get('roadModel');

        $shipment_cost_model = $this->model->get('shipmentcostModel');

        $place_model = $this->model->get('placeModel');

        $place_data = array();

        $cost_data = array();

        $warehouse_data = array();

        $road_data = array();

        

        $shipments = $shipment_model->getAllShipment($data,$join);



        



            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', mb_strtoupper($infos->info_company, "UTF-8"))

                ->setCellValue('A2', 'ĐỘI VẬN TẢI')

                ->setCellValue('H1', 'CỘNG HÒA XÃ CHỦ NGHĨA VIỆT NAM')

                ->setCellValue('H2', 'Độc lập - Tự do - Hạnh phúc')

                ->setCellValue('A4', 'BẢNG TỔNG HỢP CHI PHÍ')

                ->setCellValue('A6', 'STT')

               ->setCellValue('B6', 'Ngày')

               ->setCellValue('C6', 'Xe')

               ->setCellValue('D6', 'Khách hàng')

               ->setCellValue('E6', 'Sản lượng')

               ->setCellValue('F6', 'ĐVT')

               ->setCellValue('G6', 'Điểm lấy hàng')

               ->setCellValue('H6', 'Điểm giao hàng')

               ->setCellValue('I6', 'Dầu')

               ->setCellValue('J6', 'Tiền dầu')

               ->setCellValue('K6', 'Cầu đường')

               ->setCellValue('L6', 'Công an')

               ->setCellValue('M6', 'Bồi dưỡng')

               ->setCellValue('N6', 'Cân xe')

               ->setCellValue('O6', 'Quét cont')

               ->setCellValue('P6', 'Vé cổng')

               ->setCellValue('Q6', 'Hoa hồng')

               ->setCellValue('R6', 'Đi đường')

               ->setCellValue('S6', 'Sửa chữa')

               ->setCellValue('T6', 'Cộng');

               



            



            

            

            



            if ($shipments) {



                $hang = 7;

                $i=1;



                $kho = array();

                $k=0;
                foreach ($shipments as $row) {
                    $check_sub = 1;
                    if ($row->shipment_sub==1) {
                       $check_sub = 0;
                    }

                    $qr = "SELECT * FROM vehicle_work WHERE vehicle = ".$row->vehicle." AND start_work <= ".$row->shipment_date." AND end_work >= ".$row->shipment_date;
                    if (!$shipment_model->queryShipment($qr)) {

                        $cost_join = array('table'=>'cost_list','where'=>'cost_list = cost_list_id');
                        $shipment_costs = $shipment_cost_model->getAllShipment(array('where'=>'shipment='.$row->shipment_id),$cost_join);

                        foreach ($shipment_costs as $cost) {
                            $cost_data[$cost->cost_list_type][$row->shipment_id] = isset($cost_data[$cost->cost_list_type][$row->shipment_id])?$cost_data[$cost->cost_list_type][$row->shipment_id]+$cost->cost:$cost->cost;
                        }

                        $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $row->route).'")'));

            

                       $road_data['oil_add'][$row->shipment_id] = ($row->oil_add_dc == 5)?$row->oil_add:0;

                       $road_data['oil_add2'][$row->shipment_id] = ($row->oil_add_dc2 == 5)?$row->oil_add2:0;



                        $chek_rong = 0;

                        

                        foreach ($roads as $road) {

                            $road_data['bridge_cost'][$row->shipment_id] = isset($road_data['bridge_cost'][$row->shipment_id])?$road_data['bridge_cost'][$row->shipment_id]+$road->bridge_cost*$check_sub:$road->bridge_cost*$check_sub;

                            $road_data['police_cost'][$row->shipment_id] = isset($road_data['police_cost'][$row->shipment_id])?$road_data['police_cost'][$row->shipment_id]+$road->police_cost*$check_sub:$road->police_cost*$check_sub;

                            $road_data['tire_cost'][$row->shipment_id] = isset($road_data['tire_cost'][$row->shipment_id])?$road_data['tire_cost'][$row->shipment_id]+$road->tire_cost*$check_sub:$road->tire_cost*$check_sub;

                            
                            if($road->road_oil_ton > 0){
                                $road_data['oil_cost'][$row->shipment_id] = isset($road_data['oil_cost'][$row->shipment_id])?$road_data['oil_cost'][$row->shipment_id]+$road->road_oil_ton*$check_sub:$road->road_oil_ton*$check_sub;
                            }
                            else{
                                $road_data['oil_cost'][$row->shipment_id] = isset($road_data['oil_cost'][$row->shipment_id])?$road_data['oil_cost'][$row->shipment_id]+$road->road_oil*$check_sub:$road->road_oil*$check_sub;
                            }

                            $road_data['road_add'][$row->shipment_id] = isset($road_data['road_add'][$row->shipment_id])?$road_data['road_add'][$row->shipment_id]+$road->road_add*$check_sub:$road->road_add*$check_sub;

                            $road_data['way'][$row->shipment_id] = $road->way;

                            $chek_rong = ($road->way == 0)?1:0;



                        }

                        $places = $place_model->getAllPlace(array('where'=>'place_id = '.$row->shipment_from.' OR place_id = '.$row->shipment_to));


                        foreach ($places as $place) {

                            

                                $place_data['place_id'][$place->place_id] = $place->place_id;

                                $place_data['place_name'][$place->place_id] = $place->place_name;

                            

                            

                        }


                        $warehouses = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$row->shipment_from.' OR warehouse_code = '.$row->shipment_to.') AND start_time <= '.$row->shipment_date.' AND end_time >= '.$row->shipment_date));

                    



                        $boiduong_cont = 0;

                        $boiduong_tan = 0;



                        

                        foreach ($warehouses as $warehouse) {

                            

                                $warehouse_data['warehouse_id'][$warehouse->warehouse_code] = $warehouse->warehouse_code;

                                $warehouse_data['warehouse_name'][$warehouse->warehouse_code] = $warehouse->warehouse_name;



                                $tan = explode(".",$row->shipment_ton);

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

                                if ($warehouse->warehouse_ton != 0 && $chek_rong==0){

                                    $boiduong_tan += $trongluong * $warehouse->warehouse_ton;

                                }



                                $warehouse_data['warehouse_weight'][$warehouse->warehouse_code] = $warehouse->warehouse_weight*$check_sub;

                                $warehouse_data['warehouse_clean'][$warehouse->warehouse_code] = $warehouse->warehouse_clean*$check_sub;

                                $warehouse_data['warehouse_gate'][$warehouse->warehouse_code] = $warehouse->warehouse_gate*$check_sub;

                                $warehouse_data['warehouse_add'][$warehouse->warehouse_code] = $warehouse->warehouse_add*$check_sub;



                        

                            

                        }

                        $warehouse_data['boiduong_cn'][$row->shipment_id] = ($boiduong_cont+$boiduong_tan)*$check_sub;



                        $kho['boiduong_cn'][$row->shipment_id] = $warehouse_data['boiduong_cn'][$row->shipment_id];

                        $kho['warehouse_weight'][$row->shipment_from] = isset($warehouse_data['warehouse_weight'][$row->shipment_from])?$warehouse_data['warehouse_weight'][$row->shipment_from]:0;

                        $kho['warehouse_clean'][$row->shipment_from] = isset($warehouse_data['warehouse_clean'][$row->shipment_from])?$warehouse_data['warehouse_clean'][$row->shipment_from]:0;

                        $kho['warehouse_weight'][$row->shipment_to] = isset($warehouse_data['warehouse_weight'][$row->shipment_to])?$warehouse_data['warehouse_weight'][$row->shipment_to]:0;

                        $kho['warehouse_clean'][$row->shipment_to] = isset($warehouse_data['warehouse_clean'][$row->shipment_to])?$warehouse_data['warehouse_clean'][$row->shipment_to]:0;



                        $kho['warehouse_gate'][$row->shipment_to] = isset($warehouse_data['warehouse_gate'][$row->shipment_to])?$warehouse_data['warehouse_gate'][$row->shipment_to]:0;

                        $kho['warehouse_gate'][$row->shipment_from] = isset($warehouse_data['warehouse_gate'][$row->shipment_from])?$warehouse_data['warehouse_gate'][$row->shipment_from]:0;



                        if ($row->shipment_ton <= 0) {



                            $kho['boiduong_cn'][$row->shipment_id] = 0;

                            $kho['warehouse_weight'][$row->shipment_from] = 0;

                            $kho['warehouse_clean'][$row->shipment_from] = 0;



                            $kho['warehouse_weight'][$row->shipment_to] = 0;

                            $kho['warehouse_clean'][$row->shipment_to] = 0;



                            

                        }



                        if ($road_data['way'][$row->shipment_id] == 0) {



                            $kho['warehouse_weight'][$row->shipment_from] = 0;

                            $kho['warehouse_clean'][$row->shipment_from] = 0;



                            $kho['warehouse_weight'][$row->shipment_to] = 0;

                            $kho['warehouse_clean'][$row->shipment_to] = 0;



                            

                        }

                        





                            if ($kho['warehouse_gate'][$row->shipment_to] > 0 ) {

                                $kho['warehouse_gate'][$row->shipment_to] = $kho['warehouse_gate'][$row->shipment_to];

                                $kho['warehouse_gate'][$row->shipment_from] = 0;

                            }

                            $bd = $kho['boiduong_cn'][$row->shipment_id];
                            $dau = $row->shipment_oil*$row->oil_cost;
                            $ca = isset($road_data['police_cost'][$row->shipment_id])?$road_data['police_cost'][$row->shipment_id]:null;
                            $cd = isset($road_data['bridge_cost'][$row->shipment_id])?$road_data['bridge_cost'][$row->shipment_id]:null;
                            $vv = isset($road_data['tire_cost'][$row->shipment_id])?$road_data['tire_cost'][$row->shipment_id]:null;
                            $dd = $row->shipment_road_add;
                            $hh = isset($cost_data[9][$row->shipment_id])?$cost_data[9][$row->shipment_id]:null;

                            if(isset($cost_data[4][$row->shipment_id]))
                                $vv += $cost_data[4][$row->shipment_id];
                            if(isset($cost_data[5][$row->shipment_id]))
                                $dau += $cost_data[5][$row->shipment_id];
                            if(isset($cost_data[6][$row->shipment_id]))
                                $cd += $cost_data[6][$row->shipment_id];
                            if(isset($cost_data[9][$row->shipment_id]))
                                $hh += $cost_data[9][$row->shipment_id];
                            if(isset($cost_data[10][$row->shipment_id]))
                                $ca += $cost_data[10][$row->shipment_id];
                            if(isset($cost_data[11][$row->shipment_id]))
                                $bd += $cost_data[11][$row->shipment_id];
                            if(isset($cost_data[12][$row->shipment_id]))
                                $dd += $cost_data[12][$row->shipment_id];



                        //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                         $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValue('A' . $hang, $i++)

                            ->setCellValueExplicit('B' . $hang, $this->lib->hien_thi_ngay_thang($row->shipment_date))

                            ->setCellValue('C' . $hang, $row->vehicle_number)

                            ->setCellValue('D' . $hang, $row->customer_name)

                            ->setCellValue('E' . $hang, $row->shipment_ton)

                            ->setCellValue('F' . $hang, $row->cont_unit_name)

                            ->setCellValue('G' . $hang, $place_data['place_name'][$row->shipment_from])

                            ->setCellValue('H' . $hang, $place_data['place_name'][$row->shipment_to])

                            ->setCellValue('I' . $hang, $row->shipment_oil)

                            ->setCellValue('J' . $hang, $dau)

                            ->setCellValue('K' . $hang, $cd)

                            ->setCellValue('L' . $hang, $ca)

                            ->setCellValue('M' . $hang, $bd)

                            ->setCellValue('N' . $hang, $kho['warehouse_weight'][$row->shipment_from]+$kho['warehouse_weight'][$row->shipment_to])

                            ->setCellValue('O' . $hang, $kho['warehouse_clean'][$row->shipment_from]+$kho['warehouse_clean'][$row->shipment_to])

                            ->setCellValue('P' . $hang, $kho['warehouse_gate'][$row->shipment_to])

                            ->setCellValue('Q' . $hang, $hh)

                            ->setCellValue('R' . $hang, $dd)

                            ->setCellValue('S' . $hang, $vv)

                            ->setCellValue('T' . $hang, '=SUM(J'.$hang.':S'.$hang.')');

                         $hang++;
                    }

                    

                  }



          }



            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;



            $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');

            $objPHPExcel->getActiveSheet()->mergeCells('H1:M1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:E2');

            $objPHPExcel->getActiveSheet()->mergeCells('H2:M2');



            $objPHPExcel->getActiveSheet()->mergeCells('A4:M4');



            $objPHPExcel->getActiveSheet()->getStyle('A1:T4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:T4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(16);



            $objPHPExcel->getActiveSheet()->getStyle('A1:T4')->applyFromArray(

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

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.$hang, 'TỔNG')

                ->setCellValue('I'.$hang, '=SUM(I4:I'.($hang-1).')')

               ->setCellValue('J'.$hang, '=SUM(J4:J'.($hang-1).')')

               ->setCellValue('K'.$hang, '=SUM(K4:K'.($hang-1).')')

               ->setCellValue('L'.$hang, '=SUM(L4:L'.($hang-1).')')

               ->setCellValue('M'.$hang, '=SUM(M4:M'.($hang-1).')')

               ->setCellValue('N'.$hang, '=SUM(N4:N'.($hang-1).')')

               ->setCellValue('O'.$hang, '=SUM(O4:O'.($hang-1).')')

               ->setCellValue('P'.$hang, '=SUM(P4:P'.($hang-1).')')

               ->setCellValue('Q'.$hang, '=SUM(Q4:Q'.($hang-1).')')

               ->setCellValue('R'.$hang, '=SUM(R4:R'.($hang-1).')')

               ->setCellValue('S'.$hang, '=SUM(S4:S'.($hang-1).')')

               ->setCellValue('T'.$hang, '=SUM(T4:T'.($hang-1).')');


            $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':H'.$hang);
            $objPHPExcel->getActiveSheet()->getStyle('A6:A'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:A'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':T'.$hang)->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getStyle('A6:T'.$hang)->applyFromArray(

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



            $objPHPExcel->getActiveSheet()->getStyle('J4:T'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A6:T6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:T6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:T6')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(26);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);

            //$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(25);

            //$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(25);





            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Sale Report")

                            ->setSubject("Sale Report")

                            ->setDescription("Sale Report.")

                            ->setKeywords("Sale Report")

                            ->setCategory("Sale Report");

            $objPHPExcel->getActiveSheet()->setTitle("Bang ke chi phi");



            $objPHPExcel->getActiveSheet()->freezePane('A7');

            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BẢNG KÊ CHI PHÍ.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }



    



}

?>