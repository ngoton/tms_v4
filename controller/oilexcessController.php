<?php

Class oilexcessController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->oilexcess) || json_decode($_SESSION['user_permission_action'])->oilexcess != "oilexcess") {

            return $this->view->redirect('user/login');

        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Tổng hợp dầu phát sinh';



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

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'shipment_date';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 18446744073709;

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



        $join = array('table'=>'customer, vehicle','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle');



        $shipment_model = $this->model->get('shipmentModel');

        $sonews = 50;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;



        $data = array(

            'where' => "oil_excess > 0",

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

        $this->view->data['vong'] = $vong;

        $this->view->data['trangthai'] = $trangthai;



        $this->view->data['xe'] = $xe;





        $data = array(

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            'where' => "oil_excess > 0",

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

                    OR customer_name LIKE "%'.$keyword.'%"

                    OR shipment_from in (SELECT warehouse_id FROM warehouse WHERE warehouse_name LIKE "%'.$keyword.'%" ) 

                    OR shipment_to in (SELECT warehouse_id FROM warehouse WHERE warehouse_name LIKE "%'.$keyword.'%" ) 

                    '.$ngay.'

                        )';

            $data['where'] = $data['where']." AND ".$search;

        }



        

        

        

        $this->view->data['shipments'] = $shipment_model->getAllShipment($data,$join);



        $this->view->data['lastID'] = isset($shipment_model->getLastShipment()->shipment_id)?$shipment_model->getLastShipment()->shipment_id:0;





        $this->view->show('oilexcess/index');

    }



   

   function export(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->oilexcess) || json_decode($_SESSION['user_permission_action'])->oilexcess != "oilexcess") {

            return $this->view->redirect('user/login');

        }

        

        if ($this->registry->router->param_id != null && $this->registry->router->page != null) {

            $batdau = $this->registry->router->param_id;

            $ketthuc = $this->registry->router->page;

            $vehicle = $this->registry->router->order_by;

            $loc = $this->registry->router->order;

            $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

            $data = array();

        if ($vehicle != 0) {

            $data['where'] = 'vehicle_id = '.$vehicle;

        }



        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle($data);



       



        $shipment_model = $this->model->get('shipmentModel');

        $join = array('table'=>'vehicle','where'=>'vehicle.vehicle_id = shipment.vehicle  ORDER BY shipment_date ASC');



        $data = array(

            'where'=>'shipment_date >= '.$batdau.' AND shipment_date < '.$ngayketthuc,

            );

        if ($vehicle != 0) {

            $data['where'] .= ' AND vehicle = '.$vehicle;

        }



        $diadiem = "";



        if ($loc != 0) {

            $data['where'] .= ' AND (oil_add_dc = '.$loc.' OR oil_add_dc2 = '.$loc.')';



            if ($loc == 1) {

                $diadiem = " do tai Bai";

            }

            else if ($loc == 2) {

                $diadiem = " do tai Long Binh";

            }

            else if ($loc == 3) {

                $diadiem = " do tai Dak Lak";

            }

            else if ($loc == 4) {

                $diadiem = " do tai Quy Nhon";

            }

            else if ($loc == 6) {

                $diadiem = " do tai Quynh Trung";

            }

            else if ($loc == 7) {

                $diadiem = " do tai GL-78-Chuprong";

            }

            else if ($loc == 5) {

                $diadiem = " do tai Doc duong";

            }

        }



        $shipments = $shipment_model->getAllShipment($data,$join);



        $oil_data = array();



        foreach ($shipments as $shipment) {

            $dauung = $shipment->oil_add;

            if ($shipment->oil_add_dc != $loc && $loc > 0) {

                $dauung = 0;

            }

            

            $dauung2 = $shipment->oil_add2;

            if ($shipment->oil_add_dc2 != $loc && $loc > 0) {

                $dauung2 = 0;

            }



            $oil_data[$shipment->vehicle][$shipment->shipment_date] = isset($oil_data[$shipment->vehicle][$shipment->shipment_date])?($oil_data[$shipment->vehicle][$shipment->shipment_date]+$dauung+$dauung2):0+$dauung+$dauung2;

        }



        $join = array('table'=>'vehicle','where'=>'vehicle.vehicle_id = shipment.vehicle GROUP BY shipment_date  ORDER BY shipment_date ASC');

        $shipments = $shipment_model->getAllShipment($data,$join);











            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', 'BÁO CÁO DẦU')

                ->setCellValue('A2', 'Từ ngày '.$this->lib->hien_thi_ngay_thang($batdau).' đến ngày '.$this->lib->hien_thi_ngay_thang($ketthuc))

               ->setCellValue('A4', 'Ngày');



        

               $tiendau = array();

        if ($vehicles) {

                $current_column = 'A';

                foreach ($vehicles as $vehicle) {

                    ++$current_column;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."4", $vehicle->vehicle_number);

                }

                

            if($shipments){

                $hang = 5;

                foreach ($shipments as $shipment) {

                    $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValue("A".$hang, $this->lib->hien_thi_ngay_thang($shipment->shipment_date));



                    $current_column = 'A';

                    foreach ($vehicles as $vehicle) {

                        ++$current_column;

                        $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValue($current_column .$hang, isset($oil_data[$vehicle->vehicle_id][$shipment->shipment_date])?$oil_data[$vehicle->vehicle_id][$shipment->shipment_date]:0);

                    $dau[$vehicle->vehicle_id] = isset($dau[$vehicle->vehicle_id])?$dau[$vehicle->vehicle_id]+(isset($oil_data[$vehicle->vehicle_id][$shipment->shipment_date])?$oil_data[$vehicle->vehicle_id][$shipment->shipment_date]:0):0+(isset($oil_data[$vehicle->vehicle_id][$shipment->shipment_date])?$oil_data[$vehicle->vehicle_id][$shipment->shipment_date]:0) ;

                    $tiendau[$vehicle->vehicle_id] = isset($tiendau[$vehicle->vehicle_id])?($tiendau[$vehicle->vehicle_id] + ( isset($oil_data[$vehicle->vehicle_id][$shipment->shipment_date])?$oil_data[$vehicle->vehicle_id][$shipment->shipment_date]:0) * round($shipment->oil_cost*1.1)) : (0 + ( isset($oil_data[$vehicle->vehicle_id][$shipment->shipment_date])?$oil_data[$vehicle->vehicle_id][$shipment->shipment_date]:0) * round($shipment->oil_cost*1.1)) ;

                    }



                    $lastColumn = $current_column;

                    ++$lastColumn;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($lastColumn .$hang, '=SUM(B'.$hang.':'.$current_column.$hang.')');



                    ++$lastColumn;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($lastColumn .$hang, round($shipment->oil_cost*1.1));



                    $hang ++;





                }



               

                ++$current_column;



                $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."4", 'CỘNG'); 



                ++$current_column;



                $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column ."4", 'GIÁ DẦU');



                $objPHPExcel->setActiveSheetIndex(0)

                ->setCellValue("A".$hang, 'Tổng dầu');



                $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getFont()->setBold(true);

                $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);





                $current_column2 = 'A';

                foreach ($vehicles as $vehicle) {

                    ++$current_column2;

                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column2 .$hang, '=SUM('.$current_column2.'5:'.$current_column2.($hang-1).')');



                    $objPHPExcel->getActiveSheet()->getStyle($current_column2.$hang)->getFont()->setBold(true);



                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column2 .($hang+1), $tiendau[$vehicle->vehicle_id]);



                    $objPHPExcel->getActiveSheet()->getStyle($current_column2.($hang+1))->applyFromArray(

                        array(

                            

                            'font' => array(

                                'bold'  => true,

                                'color' => array('rgb' => 'FF0000')

                            )

                        )

                    );

                }



                ++$hang;



                $lastColumn2 = $current_column2;

                ++$current_column2;

                $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column2 .($hang-1), '=SUM(B'.($hang-1).':'.$lastColumn2.($hang-1).')');



                $objPHPExcel->getActiveSheet()->getStyle($current_column2.($hang-1))->getFont()->setBold(true);



                $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue($current_column2 .$hang, '=SUM(B'.$hang.':'.$lastColumn2.$hang.')');



                $objPHPExcel->setActiveSheetIndex(0)

                ->setCellValue("A".$hang, 'Thành tiền');

                $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



                $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':'.$current_column2.$hang)->applyFromArray(

                        array(

                            

                            'font' => array(

                                'bold'  => true,

                                'color' => array('rgb' => 'FF0000')

                            )

                        )

                    );

                

            }

        }



            $objPHPExcel->getActiveSheet()->mergeCells('A1:H1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:H2');

            $objPHPExcel->getActiveSheet()->getStyle('A4:'.$lastColumn."4")->getFont()->setBold(true);



            $objPHPExcel->getActiveSheet()->getStyle('A1:'.$lastColumn."4")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:'.$lastColumn."4")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A1:".$lastColumn.$hang)->getFont()->setName('Times New Roman');

            $objPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setSize(18);

            $objPHPExcel->getActiveSheet()->getStyle("A4:".$lastColumn."4")->getAlignment()->setWrapText(true);

            $objPHPExcel->getActiveSheet()->getStyle("A4:".$lastColumn."4")->getFont()->setSize(14);

            $objPHPExcel->getActiveSheet()->getStyle("A5:".$lastColumn.$hang)->getFont()->setSize(13);



            $objPHPExcel->getActiveSheet()->getStyle('B5:'.$lastColumn.$hang)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('B21:'.$lastColumn.$hang)->getNumberFormat()->setFormatCode("#,##0;[Black](-#,##0)");

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(25);



            

            

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

            $objPHPExcel->getActiveSheet()->getStyle('A4:'.$lastColumn.$hang)->applyFromArray(

                array(

                    'borders' => array(

                        'outline' => array(

                            'style' => PHPExcel_Style_Border::BORDER_THIN,

                            'color' => array('argb' => '000000'),

                        ),

                    ),

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A4:'.$lastColumn."4")->applyFromArray(

                array(

                    'borders' => array(

                        'outline' => array(

                            'style' => PHPExcel_Style_Border::BORDER_THIN,

                            'color' => array('argb' => '000000'),

                        ),

                    ),

                )

            );

            $objPHPExcel->getActiveSheet()->getStyle('A1:A'.$hang)->applyFromArray(

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

            $objPHPExcel->getActiveSheet()->setTitle("Dau ".$diadiem);



            



            $objPHPExcel->getActiveSheet()->freezePane('B5');



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