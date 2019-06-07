<?php

Class tollsController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->tolls) || json_decode($_SESSION['user_permission_action'])->tolls != "tolls") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Tổng hợp chi phí cầu đường';



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

            $limit = 18446744073709;

            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

            $xe = "";

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

        if($xe != ""){

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

        if($xe != ""){

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



        //$data['where'] = $data['where'].' AND way != 0';

        $place_model = $this->model->get('placeModel');

        $place_data = array();

        

        $warehouse_model = $this->model->get('warehouseModel');

        $road_model = $this->model->get('roadModel');

        
        $bridgecost_model = $this->model->get('bridgecostModel');

        $toll_model = $this->model->get('tollModel');

        $ts = $toll_model->getAllToll();
        
        $toll_datas = array();

        foreach ($ts as $tt) {
            $toll_datas[$tt->toll_id] = $tt->toll_name;
        }



        $warehouse_data = array();

        $road_data = array();

        $bridge_data = array();

        

        $shipments = $shipment_model->getAllShipment($data,$join);

        $max_colspan = 0;


        $k=0;

        foreach ($shipments as $ship) {

           $qr = "SELECT * FROM vehicle_work WHERE vehicle = ".$ship->vehicle." AND start_work <= ".$ship->shipment_date." AND end_work >= ".$ship->shipment_date;
            if ($shipment_model->queryShipment($qr)) {
                unset($shipments[$k]);
            }
            else{
                $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $ship->route).'")'));

            

                    $chek_rong = 0;

                    

                    foreach ($roads as $road) {

                        $road_data['bridge_cost'][$ship->shipment_id] = $road->bridge_cost;

                        $chek_rong = ($road->way == 0)?1:0;


                        $bridge_costs = $bridgecost_model->getAllBridgecost(array('where'=>'road = '.$road->road_id));

                        $max_colspan = count($bridge_costs)>$max_colspan?count($bridge_costs)*2:$max_colspan;

                        foreach ($bridge_costs as $bridge_cost) {
                            $bridge_data['toll_name'][$ship->shipment_id][] = isset($toll_datas[$bridge_cost->toll_booth])?$toll_datas[$bridge_cost->toll_booth]:"";
                            $bridge_data['toll_cost'][$ship->shipment_id][] = $bridge_cost->toll_booth_cost;
                        }

                    }



                    $warehouses = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$ship->shipment_from.' OR warehouse_code = '.$ship->shipment_to.') AND start_time <= '.$ship->shipment_date.' AND end_time >= '.$ship->shipment_date));

                
                    

                    foreach ($warehouses as $warehouse) {

                        

                            $warehouse_data['warehouse_id'][$warehouse->warehouse_code] = $warehouse->warehouse_code;

                            $warehouse_data['warehouse_name'][$warehouse->warehouse_code] = $warehouse->warehouse_name;



                        

                    }

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

        $this->view->data['bridge'] = $bridge_data;

        $this->view->data['max_colspan'] = $max_colspan;

        $this->view->data['place'] = $place_data;

        

        $this->view->show('tolls/index');

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

        $shipment_model = $this->model->get('shipmentModel');

        $join = array('table'=>'customer, vehicle','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle');



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

        $bridgecost_model = $this->model->get('bridgecostModel');

        $toll_model = $this->model->get('tollModel');

        $ts = $toll_model->getAllToll();
        
        $toll_datas = array();

        foreach ($ts as $tt) {
            $toll_datas[$tt->toll_id] = $tt->toll_name;
        }

        



        $warehouse_data = array();

        $road_data = array();

        $bridge_data = array();
        

        $shipments = $shipment_model->getAllShipment($data,$join);


        $max_colspan = 0;
        



            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', 'TỔNG HỢP CHI PHÍ CẦU ĐƯỜNG')

                ->setCellValue('A3', 'STT')

               ->setCellValue('B3', 'Ngày')

               ->setCellValue('C3', 'Xe')

               ->setCellValue('D3', 'Sản lượng')

               ->setCellValue('E3', 'Điểm lấy hàng')

               ->setCellValue('F3', 'Điểm giao hàng')

               ->setCellValue('G3', 'Cầu đường');

               






            if ($shipments) {



                $hang = 4;

                $i=1;



                $kho = array();

                $k=0;
                foreach ($shipments as $row) {

                    $qr = "SELECT * FROM vehicle_work WHERE vehicle = ".$row->vehicle." AND start_work <= ".$row->shipment_date." AND end_work >= ".$row->shipment_date;
                    if (!$shipment_model->queryShipment($qr)) {
                        $roads = $road_model->getAllRoad(array('where'=>'road_id IN ("'.str_replace(',', '","', $row->route).'")'));

            

                        $chek_rong = 0;

                        

                        foreach ($roads as $road) {

                            $road_data['bridge_cost'][$row->shipment_id] = $road->bridge_cost;

                            $chek_rong = ($road->way == 0)?1:0;

                            $bridge_costs = $bridgecost_model->getAllBridgecost(array('where'=>'road = '.$road->road_id));

                            $max_colspan = count($bridge_costs)>$max_colspan?count($bridge_costs)*2:$max_colspan;

                            foreach ($bridge_costs as $bridge_cost) {
                                $bridge_data['toll_name'][$row->shipment_id][] = isset($toll_datas[$bridge_cost->toll_booth])?$toll_datas[$bridge_cost->toll_booth]:"";
                                $bridge_data['toll_cost'][$row->shipment_id][] = $bridge_cost->toll_booth_cost;
                            }

                        }



                        $warehouses = $warehouse_model->getAllWarehouse(array('where'=>'(warehouse_code = '.$row->shipment_from.' OR warehouse_code = '.$row->shipment_to.') AND start_time <= '.$row->shipment_date.' AND end_time >= '.$row->shipment_date));

                    



                        $boiduong_cont = 0;

                        $boiduong_tan = 0;



                        

                        foreach ($warehouses as $warehouse) {

                            

                                $warehouse_data['warehouse_id'][$warehouse->warehouse_code] = $warehouse->warehouse_code;

                                $warehouse_data['warehouse_name'][$warehouse->warehouse_code] = $warehouse->warehouse_name;


                            

                        }

                        

                    $current_column = 'G';


                        //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                         $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValue('A' . $hang, $i++)

                            ->setCellValueExplicit('B' . $hang, $this->lib->hien_thi_ngay_thang($row->shipment_date))

                            ->setCellValue('C' . $hang, $row->vehicle_number)

                            ->setCellValue('D' . $hang, $row->shipment_ton)

                            ->setCellValue('E' . $hang, $warehouse_data['warehouse_name'][$row->shipment_from])

                            ->setCellValue('F' . $hang, $warehouse_data['warehouse_name'][$row->shipment_to])

                            ->setCellValue('G' . $hang, (round($road_data['bridge_cost'][$row->shipment_id]*1.1)+$row->bridge_cost_add));

                        if (isset($bridge_data['toll_cost'][$row->shipment_id])) {
                            for ($t=0; $t < count($bridge_data['toll_cost'][$row->shipment_id]) ; $t++) { 
                                ++$current_column;
                                $f = $current_column;
                                ++$current_column;
                                $s = $current_column;

                                $objPHPExcel->setActiveSheetIndex(0)

                                ->setCellValue($f . $hang, $bridge_data['toll_name'][$row->shipment_id][$t])

                                ->setCellValue($s . $hang, $bridge_data['toll_cost'][$row->shipment_id][$t]);

                                $objPHPExcel->getActiveSheet()->getStyle($s . $hang)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");
                            }
                        }

                         $hang++;
                    }

                    

                  }



          }

          $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValue('A' . $hang, "Tổng cộng")
                            ->setCellValue('G' . $hang, "=SUM(G4:G".($hang-1).")");

            $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':F'.$hang);
            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':G'.$hang)->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => 'FF0000')

                    )

                )

            );

          $current_column = "H";

            for ($r=0; $r <= ($max_colspan/2); $r++) {

                $f = $current_column;
                ++$current_column;

                $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValue($f . "3", "Trạm ".($r+1));

                $objPHPExcel->getActiveSheet()->mergeCells($f . '3:'.$current_column.'3');

                ++$current_column;

            }

            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;



            $objPHPExcel->getActiveSheet()->mergeCells('A1:G1');



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



            $objPHPExcel->getActiveSheet()->getStyle('G4:G'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A3:'.$current_column.'3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A3:'.$current_column.'3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A3:'.$current_column.'3')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(26);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);

            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(25);

            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);





            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Sale Report")

                            ->setSubject("Sale Report")

                            ->setDescription("Sale Report.")

                            ->setKeywords("Sale Report")

                            ->setCategory("Sale Report");

            $objPHPExcel->getActiveSheet()->setTitle("Bang ke chi phi cau duong");



            $objPHPExcel->getActiveSheet()->freezePane('H4');

            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BẢNG KÊ CHI PHÍ CẦU ĐƯỜNG.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }



    



}

?>