<?php

Class roundController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->round) || json_decode($_SESSION['user_permission_action'])->round != "round") {
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

            if(date('d',$ship->shipment_date)>29){

                $month = intval(date('m',$ship->shipment_date)+1);
                if ($month == 13) {
                    $month = 1;
                    $year = $year+1;
                }
                

            }

            

           $v[$ship->vehicle.$ship->shipment_round.$month.$year][] = $ship->shipment_from.'-'.$ship->shipment_to;





           $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));

            

            $road_data['road_revenue'][$ship->shipment_from.'-'.$ship->shipment_to] = $ship->shipment_revenue;

            $road_data['oil_cost'][$ship->shipment_from.'-'.$ship->shipment_to] = $ship->oil_cost;



            $chek_rong = 0;

            

            foreach ($roads as $road) {

                $road_data['bridge_cost'][$ship->shipment_from.'-'.$ship->shipment_to] = $road->bridge_cost;

                $road_data['police_cost'][$ship->shipment_from.'-'.$ship->shipment_to] = $road->police_cost;

                $road_data['tire_cost'][$ship->shipment_from.'-'.$ship->shipment_to] = $road->tire_cost;

                $road_data['road_oil'][$ship->shipment_from.'-'.$ship->shipment_to] = round($road->road_oil*($ship->oil_cost));

                $road_data['road_time'][$ship->shipment_from.'-'.$ship->shipment_to] = $road->road_time;

                $road_data['charge_add'][$ship->shipment_from.'-'.$ship->shipment_to] = $road->charge_add;



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

            $canxe = 0;

            $quetcont = 0;



            

            foreach ($warehouse as $warehouse) {

                

                    $warehouse_data['warehouse_id'][$warehouse->warehouse_code] = $warehouse->warehouse_code;

                    $warehouse_data['warehouse_name'][$warehouse->warehouse_code] = $warehouse->warehouse_name;

                    $warehouse_data['warehouse_gate'][$warehouse->warehouse_code] = $warehouse->warehouse_gate;



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



                if ($ship->shipment_ton > 0) {

                    $canxe += $warehouse->warehouse_weight;

                    $quetcont += $warehouse->warehouse_clean;

                }

                

            }

            $warehouse_data['boiduong_cn'][$ship->shipment_from.'-'.$ship->shipment_to] = $boiduong_cont+$boiduong_tan;

            $warehouse_data['warehouse_weight'][$ship->shipment_from.'-'.$ship->shipment_to] = $canxe;

            $warehouse_data['warehouse_clean'][$ship->shipment_from.'-'.$ship->shipment_to] = $quetcont;

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



        $this->view->show('round/index');

    }

    function export(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }



        $batdau = $this->registry->router->param_id;

        $ketthuc = $this->registry->router->page;

        $xe = $this->registry->router->order_by;

        $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

        $join = array('table'=>'customer, vehicle','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle');



        $shipment_model = $this->model->get('shipmentModel');


        $data = array(

            'where' => 'shipment_date >= '.$batdau.' AND shipment_date <= '.$ketthuc,

            );

        if($xe != ""){

            $data['where'] = $data['where'].' AND vehicle = '.$xe;

        }

        



        $data['order_by'] = 'vehicle_number ASC, shipment_date ASC, ';

        $data['order'] = 'shipment_round ASC';

        

        $warehouse_model = $this->model->get('warehouseModel');

        $road_model = $this->model->get('roadModel');

        

        $place_model = $this->model->get('placeModel');

        $place_data = array();



        $warehouse_data = array();

        $road_data = array();

        

        $datas = $shipment_model->getAllShipment($data,$join);


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

                $road_data['road_oil'][$ship->shipment_from.'-'.$ship->shipment_to] = round($road->road_oil*($ship->oil_cost));

                $road_data['road_time'][$ship->shipment_from.'-'.$ship->shipment_to] = $road->road_time;

                $road_data['charge_add'][$ship->shipment_from.'-'.$ship->shipment_to] = $road->charge_add;



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

            $canxe = 0;

            $quetcont = 0;



            

            foreach ($warehouse as $warehouse) {

                

                    $warehouse_data['warehouse_id'][$warehouse->warehouse_code] = $warehouse->warehouse_code;

                    $warehouse_data['warehouse_name'][$warehouse->warehouse_code] = $warehouse->warehouse_name;

                    $warehouse_data['warehouse_gate'][$warehouse->warehouse_code] = $warehouse->warehouse_gate;



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



                if ($ship->shipment_ton > 0) {

                    $canxe += $warehouse->warehouse_weight;

                    $quetcont += $warehouse->warehouse_clean;

                }

                

            }

            $warehouse_data['boiduong_cn'][$ship->shipment_from.'-'.$ship->shipment_to] = $boiduong_cont+$boiduong_tan;

            $warehouse_data['warehouse_weight'][$ship->shipment_from.'-'.$ship->shipment_to] = $canxe;

            $warehouse_data['warehouse_clean'][$ship->shipment_from.'-'.$ship->shipment_to] = $quetcont;

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

        $place = $place_data;

        $warehouse = $warehouse_data;

        $road = $road_data;
        
        $arr = $sum;


            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', 'TỔNG HỢP CHI PHÍ VÒNG')

                ->setCellValue('A3', 'No.')

               ->setCellValue('B3', 'STT')

               ->setCellValue('C3', 'Kho đi')

               ->setCellValue('D3', 'Kho đến')

               ->setCellValue('E3', 'Thời gian chạy')

               ->setCellValue('F3', 'Tiền dầu')

               ->setCellValue('G3', 'Cầu đường')

               ->setCellValue('H3', 'Công an')

               ->setCellValue('I3', 'Vá vỏ - Rửa xe')

               ->setCellValue('J3', 'Cước vượt tải')

               ->setCellValue('K3', 'Bồi dưỡng')

               ->setCellValue('L3', 'Cân xe')

               ->setCellValue('M3', 'Quét cont')

               ->setCellValue('N3', 'Vé cổng')

               ->setCellValue('O3', 'Tổng cộng');

               



            if ($arr) {



                $hang = 4;

                $tt = 0;
                foreach ($arr as $row) {

                    $tt++; $i = 1; $dem = 1;

                    if(isset($row['mang'])){

                        $thoigianchay=0; $tiendau=0; $cauduong=0; $congan=0; $vavo=0; $vuottai=0; $boiduong=0; $canxe=0; $quetcont=0; $vecong=0; $chiphi=0;
                        foreach ($row['mang'] as $ship) :

                            $r = explode('-', $ship);

                        $bd = isset($warehouse['boiduong_cn'][$ship])?$warehouse['boiduong_cn'][$ship]:0;
                        $cx = isset($warehouse['warehouse_weight'][$ship])?$warehouse['warehouse_weight'][$ship]:0;
                        $qc = isset($warehouse['warehouse_clean'][$ship])?$warehouse['warehouse_clean'][$ship]:0;
                        $vc = isset($warehouse['warehouse_gate'][$r[1]])?($r[1]==$place['place_id'][$r[1]]?$warehouse['warehouse_gate'][$r[1]]:0):0;


                            if($dem==1){
                                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $hang, $tt);

                                $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':A'.($hang+count($row['mang'])-1));

                                $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                                $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                                
                            }

                        //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                         $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValueExplicit('B' . $hang, $i++)

                            ->setCellValue('C' . $hang, $r[0]==$place['place_id'][$r[0]]?$place['place_name'][$r[0]]:null)

                            ->setCellValue('D' . $hang, $r[1]==$place['place_id'][$r[1]]?$place['place_name'][$r[1]]:null)

                            ->setCellValue('E' . $hang, $road['road_time'][$ship])

                            ->setCellValue('F' . $hang, $road['road_oil'][$ship])

                            ->setCellValue('G' . $hang, $road['bridge_cost'][$ship])

                            ->setCellValue('H' . $hang, $road['police_cost'][$ship])

                            ->setCellValue('I' . $hang, $road['tire_cost'][$ship])

                            ->setCellValue('J' . $hang, $road['charge_add'][$ship])

                            ->setCellValue('K' . $hang, $bd)

                            ->setCellValue('L' . $hang, $cx)

                            ->setCellValue('M' . $hang, $qc)

                            ->setCellValue('N' . $hang, $vc)

                            ->setCellValue('O' . $hang, '=SUM(F'.$hang.':N'.$hang.')');

                         $hang++;

                         $thoigianchay += $road['road_time'][$ship];
                        $tiendau += $road['road_oil'][$ship];
                        $cauduong += $road['bridge_cost'][$ship];
                        $congan += $road['police_cost'][$ship];
                        $vavo += $road['tire_cost'][$ship];
                        $vuottai += $road['charge_add'][$ship];
                        $boiduong += $bd;
                        $canxe += $cx;
                        $quetcont += $qc;
                        $vecong += $vc;
                        $chiphi += $tong;

                        $dem ++;

                         endforeach;

                         $tongtiendau += $tiendau*$row['soluong'];
                        $tongcauduong += $cauduong*$row['soluong'];
                        $tongcongan += $congan*$row['soluong'];
                        $tongvavo += $vavo*$row['soluong'];
                        $tongvuottai += $vuottai*$row['soluong'];
                        $tongboiduong += $boiduong*$row['soluong'];
                        $tongcanxe += $canxe*$row['soluong'];
                        $tongquetcont += $quetcont*$row['soluong'];
                        $tongvecong += $vecong*$row['soluong'];
                        $tongchiphi += $chiphi*$row['soluong'];

                        $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValueExplicit('A' . $hang, 'Tổng số chuyến: '.$row['soluong'])

                            ->setCellValue('E' . $hang, $thoigianchay)

                            ->setCellValue('F' . $hang, $tiendau)

                            ->setCellValue('G' . $hang, $cauduong)

                            ->setCellValue('H' . $hang, $congan)

                            ->setCellValue('I' . $hang, $vavo)

                            ->setCellValue('J' . $hang, $vuottai)

                            ->setCellValue('K' . $hang, $boiduong)

                            ->setCellValue('L' . $hang, $canxe)

                            ->setCellValue('M' . $hang, $quetcont)

                            ->setCellValue('N' . $hang, $vecong)

                            ->setCellValue('O' . $hang, $chiphi);

                            $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':C'.$hang);
                            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':O'.$hang)->getFont()->setBold(true);

                            $hang++;
                    }

                    

                  }

                  $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValueExplicit('A' . $hang, 'Tổng cộng')

                            ->setCellValue('F' . $hang, $tongtiendau)

                            ->setCellValue('G' . $hang, $tongcauduong)

                            ->setCellValue('H' . $hang, $tongcongan)

                            ->setCellValue('I' . $hang, $tongvavo)

                            ->setCellValue('J' . $hang, $tongvuottai)

                            ->setCellValue('K' . $hang, $tongboiduong)

                            ->setCellValue('L' . $hang, $tongcanxe)

                            ->setCellValue('M' . $hang, $tongquetcont)

                            ->setCellValue('N' . $hang, $tongvecong)

                            ->setCellValue('O' . $hang, $tongchiphi);

                        $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':C'.$hang);
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

              }



          



            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            //$highestRow ++;



            $objPHPExcel->getActiveSheet()->mergeCells('A1:O1');



            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(18);



            $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => 'FF0000')

                    )

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A'.$highestRow.':O'.$highestRow)->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => 'FF0000')

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('F4:O'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A3:O3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A3:O3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A3:O3')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(26);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(8);

            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(8);





            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Cost Report")

                            ->setSubject("Cost Report")

                            ->setDescription("Cost Report.")

                            ->setKeywords("Cost Report")

                            ->setCategory("Cost Report");

            $objPHPExcel->getActiveSheet()->setTitle("Bang ke chi phi theo vong");



            $objPHPExcel->getActiveSheet()->freezePane('A4');

            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BẢNG KÊ CHI PHÍ.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

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