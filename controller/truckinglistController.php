<?php

Class truckinglistController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->truckinglist) || json_decode($_SESSION['user_permission_action'])->truckinglist != "truckinglist") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Bảng kê vận chuyển';



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

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'bill_receive_date ASC, ';

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


        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));



        $this->view->data['vehicles'] = $vehicles;

        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getAllCustomer(array('order_by'=>'customer_name','order'=>'ASC'));



        $this->view->data['customers'] = $customers;




        $join = array('table'=>'customer, vehicle, cont_unit','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND cont_unit=cont_unit_id');



        $shipment_model = $this->model->get('shipmentModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;



        $data = array(

            'where' => 'bill_receive_date >= '.strtotime($batdau).' AND bill_receive_date < '.strtotime($ngayketthuc),

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

            'where' => 'bill_receive_date >= '.strtotime($batdau).' AND bill_receive_date < '.strtotime($ngayketthuc),

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

            $ngay = (strtotime(str_replace("/", "-", $keyword))!="")?(' OR bill_receive_date LIKE "%'.strtotime(str_replace("/", "-", $keyword)).'%"'):"";

            $search = '(

                    vehicle_number LIKE "%'.$keyword.'%"

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


        $this->view->show('truckinglist/index');

    }

    function export(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }



        $batdau = $this->registry->router->param_id;

        $ketthuc = $this->registry->router->page;

        $xe = $this->registry->router->order_by;

        $kh = $this->registry->router->order;

        $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

        $info_model = $this->model->get('infoModel');
        $infos = $info_model->getLastInfo();

        $contunit_model = $this->model->get('contunitModel');
        $cost_list_model = $this->model->get('costlistModel');

        $cont_units = $contunit_model->getAllUnit();
        $loan_units = $cost_list_model->getAllCost(array('where'=>'cost_list_type = 8'));

        $shipment_model = $this->model->get('shipmentModel');

        $join = array('table'=>'customer, vehicle, cont_unit','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND cont_unit = cont_unit_id');



        $data = array(

            'where' => "shipment_ton > 0",

            );

        if($batdau != "" && $ketthuc != "" ){

            $data['where'] = $data['where'].' AND bill_receive_date >= '.$batdau.' AND bill_receive_date < '.$ngayketthuc;

        }

        if($xe > 0){

            $data['where'] = $data['where'].' AND vehicle = '.$xe;

        }

        if($kh > 0){

            $data['where'] = $data['where'].' AND customer = '.$kh;

        }

        



        /*if ($_SESSION['role_logined'] == 3) {

            $data['where'] = $data['where'].' AND shipment_create_user = '.$_SESSION['userid_logined'];

            

        }*/



        





        $data['order_by'] = 'bill_receive_date';

        $data['order'] = 'ASC';



        

        

        $place_model = $this->model->get('placeModel');

        $place_data = array();


        $customer_sub_model = $this->model->get('customersubModel');
        $customer_types = array();


        $shipment_cost_model = $this->model->get('shipmentcostModel');
        $loan_shipment_data = array();


        



        $number_sheet = $shipment_model->queryShipment('SELECT customer,customer_name FROM shipment,customer WHERE customer=customer_id AND bill_receive_date >= '.$batdau.' AND bill_receive_date < '.$ngayketthuc.' GROUP BY customer ');



        



            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();

        $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

        if ($kh == 0) {
            foreach ($number_sheet as $cus) {
                
                $data = array(

                'where' => 'shipment_ton > 0 AND customer = '.$cus->customer.' AND bill_receive_date >= '.$batdau.' AND bill_receive_date < '.$ngayketthuc,

                );
                if($xe > 0){

                    $data['where'] = $data['where'].' AND vehicle = '.$xe;

                }

                $shipments = $shipment_model->getAllShipment($data,$join);

                $objPHPExcel->createSheet();

                $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', mb_strtoupper($infos->info_company, "UTF-8"))

                ->setCellValue('A2', 'ĐỘI VẬN TẢI')

                ->setCellValue('H1', 'CỘNG HÒA XÃ CHỦ NGHĨA VIỆT NAM')

                ->setCellValue('H2', 'Độc lập - Tự do - Hạnh phúc')

                ->setCellValue('A4', 'BẢNG KÊ SẢN LƯỢNG VẬN CHUYỂN')

                ->setCellValue('A6', 'STT')

               ->setCellValue('B6', 'Ngày')

               ->setCellValue('C6', 'Khách hàng')

               ->setCellValue('D6', 'Mặt hàng')

               ->setCellValue('E6', 'Tuyến đường')

               ->setCellValue('F6', 'Số xe')

               ->setCellValue('G6', 'Sản lượng');

               
            $dvt = "G";
            foreach ($cont_units as $cont) {
                $objPHPExcel->setActiveSheetIndex($index_worksheet)
                ->setCellValue($dvt.'7', $cont->cont_unit_name);

                $dvt++;
            }

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue(($dvt++).'6', 'Đơn giá')

                ->setCellValue(($dvt++).'6', 'Thành tiền')

                ->setCellValue($dvt.'6', 'Phí phát sinh');

            $ascii = ord($dvt);
            $tt = chr($ascii -1);
            $dg = chr($ascii -2);
            $sln = chr($ascii -3);
            $ps = $dvt;
            $phatsinh = $dvt;

            foreach ($loan_units as $loan) {
                $objPHPExcel->setActiveSheetIndex($index_worksheet)
                ->setCellValue($phatsinh.'7', $loan->cost_list_name);

                $phatsinh++;
            }




            if ($shipments) {



                $hang = 8;

                $i=1;



                $kho_data = array();

                $k=0;
                foreach ($shipments as $row) {

                    $qr = "SELECT * FROM vehicle_work WHERE vehicle = ".$row->vehicle." AND start_work <= ".$row->shipment_date." AND end_work >= ".$row->shipment_date;
                    if (!$shipment_model->queryShipment($qr)) {
                    
                        $loans = $shipment_cost_model->getAllShipment(array('where'=>'shipment = '.$row->shipment_id),array('table'=>'cost_list','where'=>'cost_list = cost_list_id AND cost_list_type = 8'));
                        foreach ($loans as $loan) {
                            $loan_shipment_data[$row->shipment_id][$loan->cost_list] = isset($loan_shipment_data[$row->shipment_id][$loan->cost_list])?$loan_shipment_data[$row->shipment_id][$loan->cost_list]+$loan->cost:$loan->cost;
                            
                        }

                        $places = $place_model->getAllPlace(array('where'=>'place_id = '.$row->shipment_from.' OR place_id = '.$row->shipment_to));

                        foreach ($places as $place) {

                                $place_data['place_id'][$place->place_id] = $place->place_id;

                                $place_data['place_name'][$place->place_id] = $place->place_name;  

                        }

                        $customer_sub = "";
                        $sts = explode(',', $row->customer_type);
                        foreach ($sts as $key) {
                            $subs = $customer_sub_model->getCustomer($key);
                            if ($subs) {
                                if ($customer_sub == "")
                                    $customer_sub .= $subs->customer_sub_name;
                                else
                                    $customer_sub .= ','.$subs->customer_sub_name;
                            }
                            
                        }
                        $customer_types[$row->shipment_id] = $customer_sub;





                        //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                         $objPHPExcel->setActiveSheetIndex($index_worksheet)

                            ->setCellValue('A' . $hang, $i++)

                            ->setCellValueExplicit('B' . $hang, $this->lib->hien_thi_ngay_thang($row->bill_receive_date))

                            ->setCellValue('C' . $hang, $row->customer_name)

                            ->setCellValue('D' . $hang, $customer_types[$row->shipment_id])

                            ->setCellValue('E' . $hang, $place_data['place_name'][$row->shipment_from].'-'.$place_data['place_name'][$row->shipment_to])

                            ->setCellValue('F' . $hang, $row->vehicle_number);


                        $dvt = "G";
                        foreach ($cont_units as $cont) {
                            $ton = $row->cont_unit==$cont->cont_unit_id?$row->shipment_ton:null;

                            $objPHPExcel->setActiveSheetIndex($index_worksheet)
                            ->setCellValue($dvt.$hang, $ton);

                            $dvt++;
                        }

                        $ascii = ord($dvt);
                        $dvt = chr($ascii -1);
                        $l = $dvt;
                        $dvt++;

                        $s = '=SUM(G'.$hang.':'.$l.$hang.')*'.$dvt.$hang;

                        $objPHPExcel->setActiveSheetIndex($index_worksheet)

                        ->setCellValue(($dvt++).$hang, $row->shipment_charge)
                        ->setCellValue(($dvt++).$hang, $s);


                        $phatsinh = $dvt;

                        foreach ($loan_units as $loan) {
                            $chiho = isset($loan_shipment_data[$row->shipment_id][$loan->cost_list_id])?$loan_shipment_data[$row->shipment_id][$loan->cost_list_id]:null;

                            $objPHPExcel->setActiveSheetIndex($index_worksheet)
                            ->setCellValue($phatsinh.$hang, $chiho);

                            $phatsinh++;
                        }


                         $hang++;



                        $tencongty = $row->customer_company;



                      }

                }

            }



            $check_customer = 0;



            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.$hang, 'TỔNG')


               ->setCellValue('K'.$hang, '=SUM(K8:K'.($hang-1).')');

            $phatsinh = $ps;

            foreach ($loan_units as $loan) {
                $objPHPExcel->setActiveSheetIndex($index_worksheet)
               ->setCellValue($phatsinh.$hang, '=SUM('.$phatsinh.'8:'.$phatsinh.($hang-1).')');

                $phatsinh++;
            }

            

            $hang++;

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.$hang, 'Thuế GTGT 10%')


               ->setCellValue($tt.$hang, '='.$tt.($hang-1).'*10%');

            $hang++;

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.$hang, 'Tổng cộng')


               ->setCellValue($tt.$hang, '=SUM('.$tt.($hang-1).':'.$tt.($hang-2).')')

               ->setCellValue($ps.$hang, '=SUM('.$ps.($hang-2).':'.$phatsinh.($hang-2).')');


            $objPHPExcel->getActiveSheet()->getStyle('A6:'.$phatsinh.$hang)->applyFromArray(

                array(

                    

                    'borders' => array(

                        'allborders' => array(

                          'style' => PHPExcel_Style_Border::BORDER_THIN

                        )

                    )

                )

            );


            $highestColumn = $objPHPExcel->getActiveSheet()->getHighestDataColumn();

            $cell2 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(10, $hang)->getCalculatedValue();
            $cell1 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(11, $hang)->getCalculatedValue();
            $cell = (int)($cell2+$cell1);

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.($hang+1), 'Bằng chữ: '.$this->lib->convert_number_to_words(round($cell)).' đồng');

             

            $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':F'.$hang);
            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang-1).':F'.($hang-1));
            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang-2).':F'.($hang-2));
            $objPHPExcel->getActiveSheet()->mergeCells($ps.$hang.':'.$phatsinh.$hang);
            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+1).':'.$phatsinh.($hang+1));


            $objPHPExcel->getActiveSheet()->getStyle($ps.$hang.':'.$phatsinh.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle($ps.$hang.':'.$phatsinh.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang-2).':A'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang-2).':A'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);





            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.($hang+3), 'NGƯỜI LẬP BIỂU')

                ->setCellValue('E'.($hang+3), mb_strtoupper($infos->info_company, "UTF-8"))

               ->setCellValue('I'.($hang+3), mb_strtoupper($tencongty, "UTF-8"));



            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+3).':D'.($hang+3));

            $objPHPExcel->getActiveSheet()->mergeCells('E'.($hang+3).':H'.($hang+3));

            $objPHPExcel->getActiveSheet()->mergeCells('I'.($hang+3).':M'.($hang+3));



            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':M'.($hang+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':M'.($hang+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang-2).':'.$phatsinh.($hang+3))->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );





            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;



            $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');

            $objPHPExcel->getActiveSheet()->mergeCells('H1:M1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:E2');

            $objPHPExcel->getActiveSheet()->mergeCells('H2:M2');



            $objPHPExcel->getActiveSheet()->mergeCells('A4:M4');

            $objPHPExcel->getActiveSheet()->mergeCells('A6:A7');
            $objPHPExcel->getActiveSheet()->mergeCells('B6:B7');
            $objPHPExcel->getActiveSheet()->mergeCells('C6:C7');
            $objPHPExcel->getActiveSheet()->mergeCells('D6:D7');
            $objPHPExcel->getActiveSheet()->mergeCells('E6:E7');
            $objPHPExcel->getActiveSheet()->mergeCells('F6:F7');
            $objPHPExcel->getActiveSheet()->mergeCells($dg.'6:'.$dg.'7');
            $objPHPExcel->getActiveSheet()->mergeCells($tt.'6:'.$tt.'7');

            $objPHPExcel->getActiveSheet()->mergeCells('G6:'.$sln.'6');
            $objPHPExcel->getActiveSheet()->mergeCells($ps.'6:'.$phatsinh.'6');



            $objPHPExcel->getActiveSheet()->getStyle('A1:'.$phatsinh.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:'.$phatsinh.'4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(16);



            $objPHPExcel->getActiveSheet()->getStyle('A1:'.$phatsinh.'4')->applyFromArray(

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



            $objPHPExcel->getActiveSheet()->getStyle('I8:'.$phatsinh.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A6:'.$phatsinh.'7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:'.$phatsinh.'7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:'.$phatsinh.'7')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(26);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);

            //$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);



            

            $objPHPExcel->getActiveSheet()->setTitle($cus->customer_name);



            $objPHPExcel->getActiveSheet()->freezePane('A8');

            $objPHPExcel->setActiveSheetIndex($index_worksheet);

            $index_worksheet++;
            }
        }
        else{
            

                $shipments = $shipment_model->getAllShipment($data,$join);


                $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', mb_strtoupper($infos->info_company, "UTF-8"))

                ->setCellValue('A2', 'ĐỘI VẬN TẢI')

                ->setCellValue('H1', 'CỘNG HÒA XÃ CHỦ NGHĨA VIỆT NAM')

                ->setCellValue('H2', 'Độc lập - Tự do - Hạnh phúc')

                ->setCellValue('A4', 'BẢNG KÊ SẢN LƯỢNG VẬN CHUYỂN')

                ->setCellValue('A6', 'STT')

               ->setCellValue('B6', 'Ngày')

               ->setCellValue('C6', 'Khách hàng')

               ->setCellValue('D6', 'Mặt hàng')

               ->setCellValue('E6', 'Tuyến đường')

               ->setCellValue('F6', 'Số xe')

               ->setCellValue('G6', 'Sản lượng');

               
            $dvt = "G";
            foreach ($cont_units as $cont) {
                $objPHPExcel->setActiveSheetIndex($index_worksheet)
                ->setCellValue($dvt.'7', $cont->cont_unit_name);

                $dvt++;
            }
            
            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue(($dvt++).'6', 'Đơn giá')

                ->setCellValue(($dvt++).'6', 'Thành tiền')

                ->setCellValue($dvt.'6', 'Phí phát sinh');

           
            $ascii = ord($dvt);
            $tt = chr($ascii -1);
            $dg = chr($ascii -2);
            $sln = chr($ascii -3);
            $ps = $dvt;
            $phatsinh = $dvt;

            foreach ($loan_units as $loan) {
                $objPHPExcel->setActiveSheetIndex($index_worksheet)
                ->setCellValue($phatsinh.'7', $loan->cost_list_name);

                $phatsinh++;
            }




            if ($shipments) {



                $hang = 8;

                $i=1;



                $kho_data = array();

                $k=0;
                foreach ($shipments as $row) {

                    $qr = "SELECT * FROM vehicle_work WHERE vehicle = ".$row->vehicle." AND start_work <= ".$row->shipment_date." AND end_work >= ".$row->shipment_date;
                    if (!$shipment_model->queryShipment($qr)) {
                    
                        $loans = $shipment_cost_model->getAllShipment(array('where'=>'shipment = '.$row->shipment_id),array('table'=>'cost_list','where'=>'cost_list = cost_list_id AND cost_list_type = 8'));
                        foreach ($loans as $loan) {
                            $loan_shipment_data[$row->shipment_id][$loan->cost_list] = isset($loan_shipment_data[$row->shipment_id][$loan->cost_list])?$loan_shipment_data[$row->shipment_id][$loan->cost_list]+$loan->cost:$loan->cost;
                            
                        }

                        $places = $place_model->getAllPlace(array('where'=>'place_id = '.$row->shipment_from.' OR place_id = '.$row->shipment_to));

                        foreach ($places as $place) {

                                $place_data['place_id'][$place->place_id] = $place->place_id;

                                $place_data['place_name'][$place->place_id] = $place->place_name;  

                        }

                        $customer_sub = "";
                        $sts = explode(',', $row->customer_type);
                        foreach ($sts as $key) {
                            $subs = $customer_sub_model->getCustomer($key);
                            if ($subs) {
                                if ($customer_sub == "")
                                    $customer_sub .= $subs->customer_sub_name;
                                else
                                    $customer_sub .= ','.$subs->customer_sub_name;
                            }
                            
                        }
                        $customer_types[$row->shipment_id] = $customer_sub;





                        //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                         $objPHPExcel->setActiveSheetIndex($index_worksheet)

                            ->setCellValue('A' . $hang, $i++)

                            ->setCellValueExplicit('B' . $hang, $this->lib->hien_thi_ngay_thang($row->bill_receive_date))

                            ->setCellValue('C' . $hang, $row->customer_name)

                            ->setCellValue('D' . $hang, $customer_types[$row->shipment_id])

                            ->setCellValue('E' . $hang, $place_data['place_name'][$row->shipment_from].'-'.$place_data['place_name'][$row->shipment_to])

                            ->setCellValue('F' . $hang, $row->vehicle_number);


                        $dvt = "G";
                        foreach ($cont_units as $cont) {
                            $ton = $row->cont_unit==$cont->cont_unit_id?$row->shipment_ton:null;

                            $objPHPExcel->setActiveSheetIndex($index_worksheet)
                            ->setCellValue($dvt.$hang, $ton);

                            $dvt++;
                        }
                        $ascii = ord($dvt);
                        $dvt = chr($ascii -1);
                        $l = $dvt;
                        $dvt++;

                        $s = '=SUM(G'.$hang.':'.$l.$hang.')*'.$dvt.$hang;

                        $objPHPExcel->setActiveSheetIndex($index_worksheet)

                        ->setCellValue(($dvt++).$hang, $row->shipment_charge)
                        ->setCellValue(($dvt++).$hang, $s);


                        $phatsinh = $dvt;

                        foreach ($loan_units as $loan) {
                            $chiho = isset($loan_shipment_data[$row->shipment_id][$loan->cost_list_id])?$loan_shipment_data[$row->shipment_id][$loan->cost_list_id]:null;

                            $objPHPExcel->setActiveSheetIndex($index_worksheet)
                            ->setCellValue($phatsinh.$hang, $chiho);

                            $phatsinh++;
                        }


                         $hang++;



                        $tencongty = $row->customer_company;



                      }

                }

            }



            $check_customer = 0;



            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.$hang, 'TỔNG')


               ->setCellValue($tt.$hang, '=SUM('.$tt.'8:'.$tt.($hang-1).')');

            $phatsinh = $ps;

            foreach ($loan_units as $loan) {
                $objPHPExcel->setActiveSheetIndex($index_worksheet)
               ->setCellValue($phatsinh.$hang, '=SUM('.$phatsinh.'8:'.$phatsinh.($hang-1).')');

                $phatsinh++;
            }

            

            $hang++;

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.$hang, 'Thuế GTGT 10%')


               ->setCellValue($tt.$hang, '='.$tt.($hang-1).'*10%');

            $hang++;

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.$hang, 'Tổng cộng')


               ->setCellValue($tt.$hang, '=SUM('.$tt.($hang-1).':'.$tt.($hang-2).')')

               ->setCellValue($ps.$hang, '=SUM('.$ps.($hang-2).':'.$phatsinh.($hang-2).')');


            $objPHPExcel->getActiveSheet()->getStyle('A6:'.$phatsinh.$hang)->applyFromArray(

                array(

                    

                    'borders' => array(

                        'allborders' => array(

                          'style' => PHPExcel_Style_Border::BORDER_THIN

                        )

                    )

                )

            );


            $highestColumn = $objPHPExcel->getActiveSheet()->getHighestDataColumn();

            $cell2 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(10, $hang)->getCalculatedValue();
            $cell1 = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(11, $hang)->getCalculatedValue();
            $cell = (int)($cell2+$cell1);

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.($hang+1), 'Bằng chữ: '.$this->lib->convert_number_to_words(round($cell)).' đồng');

            

            $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':F'.$hang);
            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang-1).':F'.($hang-1));
            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang-2).':F'.($hang-2));
            $objPHPExcel->getActiveSheet()->mergeCells($ps.$hang.':'.$phatsinh.$hang);
            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+1).':'.$phatsinh.($hang+1));


            $objPHPExcel->getActiveSheet()->getStyle($ps.$hang.':'.$phatsinh.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle($ps.$hang.':'.$phatsinh.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang-2).':A'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang-2).':A'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);





            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.($hang+3), 'NGƯỜI LẬP BIỂU')

                ->setCellValue('E'.($hang+3), mb_strtoupper($infos->info_company, "UTF-8"))

               ->setCellValue('I'.($hang+3), mb_strtoupper($tencongty, "UTF-8"));



            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+3).':D'.($hang+3));

            $objPHPExcel->getActiveSheet()->mergeCells('E'.($hang+3).':H'.($hang+3));

            $objPHPExcel->getActiveSheet()->mergeCells('I'.($hang+3).':M'.($hang+3));



            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':M'.($hang+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':M'.($hang+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang-2).':'.$phatsinh.($hang+3))->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );





            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;



            $objPHPExcel->getActiveSheet()->mergeCells('A1:E1');

            $objPHPExcel->getActiveSheet()->mergeCells('H1:M1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:E2');

            $objPHPExcel->getActiveSheet()->mergeCells('H2:M2');



            $objPHPExcel->getActiveSheet()->mergeCells('A4:M4');

            $objPHPExcel->getActiveSheet()->mergeCells('A6:A7');
            $objPHPExcel->getActiveSheet()->mergeCells('B6:B7');
            $objPHPExcel->getActiveSheet()->mergeCells('C6:C7');
            $objPHPExcel->getActiveSheet()->mergeCells('D6:D7');
            $objPHPExcel->getActiveSheet()->mergeCells('E6:E7');
            $objPHPExcel->getActiveSheet()->mergeCells('F6:F7');
            $objPHPExcel->getActiveSheet()->mergeCells($dg.'6:'.$dg.'7');
            $objPHPExcel->getActiveSheet()->mergeCells($tt.'6:'.$tt.'7');

            $objPHPExcel->getActiveSheet()->mergeCells('G6:'.$sln.'6');
            $objPHPExcel->getActiveSheet()->mergeCells($ps.'6:'.$phatsinh.'6');



            $objPHPExcel->getActiveSheet()->getStyle('A1:'.$phatsinh.'4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:'.$phatsinh.'4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(16);



            $objPHPExcel->getActiveSheet()->getStyle('A1:'.$phatsinh.'4')->applyFromArray(

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



            $objPHPExcel->getActiveSheet()->getStyle('I8:'.$phatsinh.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A6:'.$phatsinh.'7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:'.$phatsinh.'7')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:'.$phatsinh.'7')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(26);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);

            //$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);



            

            $objPHPExcel->getActiveSheet()->setTitle($row->customer_name);



            $objPHPExcel->getActiveSheet()->freezePane('A8');

            $objPHPExcel->setActiveSheetIndex($index_worksheet);
        }



            

            







            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Sale Report")

                            ->setSubject("Sale Report")

                            ->setDescription("Sale Report.")

                            ->setKeywords("Sale Report")

                            ->setCategory("Sale Report");



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BẢNG KÊ SẢN LƯỢNG VẬN CHUYỂN.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }


}

?>