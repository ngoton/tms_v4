<?php

Class oilreportController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->oilreport) || json_decode($_SESSION['user_permission_action'])->oilreport != "oilreport") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Báo cáo dầu';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;

            $vehicle = isset($_POST['vehicle']) ? $_POST['vehicle'] : null;

            $loc = isset($_POST['loc']) ? $_POST['loc'] : null;

            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;

            $trangthai = isset($_POST['trangthai']) ? $_POST['trangthai'] : null;

        }

        else{

            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

            $vong = (int)date('m',strtotime($batdau));

            $trangthai = date('Y',strtotime($batdau));

            $vehicle = 0;

            $loc = 0;

        }

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));

        $trangthai = date('Y',strtotime($batdau));



        $data = array(
            'where' => 'vehicle_id NOT IN (SELECT vehicle FROM vehicle_work WHERE start_work >= '.strtotime($batdau).' AND end_work < '.strtotime($ngayketthuc).')',
            'order_by' => 'vehicle_number',
            'order' => 'ASC',
        );

        if ($vehicle != 0) {

            $data['where'] = 'vehicle_id = '.$vehicle;

        }



        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle($data);



        



        $shipment_model = $this->model->get('shipmentModel');

        $join = array('table'=>'vehicle','where'=>'vehicle.vehicle_id = shipment.vehicle  ORDER BY shipment_date ASC');



        $data = array(

            'where'=>'shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc),

            );

        if ($vehicle != 0) {

            $data['where'] .= ' AND vehicle = '.$vehicle;

        }

        if ($loc != 0) {

            $data['where'] .= ' AND (oil_add_dc = '.$loc.' OR oil_add_dc2 = '.$loc.')';

        }



        $shipments = $shipment_model->getAllShipment($data,$join);



        $oil_data = array();



        $check_lock = array();

        $check_lock_all = array();

        $k=0;

        foreach ($shipments as $shipment) {
            $qr = "SELECT * FROM vehicle_work WHERE vehicle = ".$shipment->vehicle." AND start_work <= ".$shipment->shipment_date." AND end_work >= ".$shipment->shipment_date;
            if ($shipment_model->queryShipment($qr)) {
                unset($shipments[$k]);
            }
            else{
                if ($shipment->approve_oil != 1) {

                    $check_lock[$shipment->vehicle] = 0;

                    $check_lock_all[$batdau.$ketthuc] = 0;

                }



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

           $k++; 
        }



        $join = array('table'=>'vehicle','where'=>'vehicle.vehicle_id = shipment.vehicle GROUP BY shipment_date  ORDER BY shipment_date ASC');

        $shipments = $shipment_model->getAllShipment($data,$join);



        $xe = $vehicle_model->getAllVehicle();

        $this->view->data['list_vehicle'] = $xe;



        $this->view->data['oil_data'] = $oil_data;

        $this->view->data['shipments'] = $shipments;

        $this->view->data['vehicles'] = $vehicles;

        $this->view->data['batdau'] = $batdau;

        $this->view->data['ketthuc'] = $ketthuc;

        $this->view->data['xe'] = $vehicle;

        $this->view->data['loc'] = $loc;

        $this->view->data['vong'] = $vong;

        $this->view->data['trangthai'] = $trangthai;

        $this->view->data['check_lock'] = $check_lock;

        $this->view->data['check_lock_all'] = $check_lock_all;

        $this->view->show('oilreport/index');

    }



    public function approve(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['xe'])) {



            $shipment = $this->model->get('shipmentModel');

            

            $data = array(

                'where'=> 'shipment_date>='.$_POST['batdau'].' AND shipment_date<='.$_POST['ketthuc'],

            );



            if ($_POST['xe'] > 0) {

                $data['where'] .= ' AND vehicle='.$_POST['xe'];

            }



            if ($_POST['diadiem'] > 0) {

                $data['where'] .= ' AND (oil_add_dc='.$_POST['diadiem'].' OR oil_add_dc2='.$_POST['diadiem'].')';

            }



            $shipments = $shipment->getAllShipment($data);



            $lock = $_POST['value']==1?0:1;



            foreach ($shipments as $ship) {

                $shipment->updateShipment(array('approve_oil'=>$lock),array('shipment_id'=>$ship->shipment_id));

            }

          



            date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."approve_oil"."|".$_POST['xe']."|shipment|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



            return true;

                    

        }

    }



   

   function export(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->oilreport) || json_decode($_SESSION['user_permission_action'])->oilreport != "oilreport") {
            return $this->view->redirect('user/login');
        }

        

        if ($this->registry->router->param_id != null && $this->registry->router->page != null) {

            $batdau = $this->registry->router->param_id;

            $ketthuc = $this->registry->router->page;

            $vehicle = $this->registry->router->order_by;

            $loc = $this->registry->router->order;

            $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

            $data = array(
                'where' => 'vehicle_id NOT IN (SELECT vehicle FROM vehicle_work WHERE start_work >= '.$batdau.' AND end_work < '.$ngayketthuc.')',
                'order_by' => 'vehicle_number',
                'order' => 'ASC',
            );

        if ($vehicle != 0) {

            $data['where'] = 'vehicle_id = '.$vehicle;

        }



        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle($data);



       



        $shipment_model = $this->model->get('shipmentModel');

        $join = array('table'=>'vehicle','where'=>'vehicle.vehicle_id = shipment.vehicle ORDER BY shipment_date ASC');



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

            else if ($loc == 5) {

                $diadiem = " do tai Doc duong";

            }

        }



        $shipments = $shipment_model->getAllShipment($data,$join);



        $oil_data = array();

        $k=0;

        foreach ($shipments as $shipment) {
            $qr = "SELECT * FROM vehicle_work WHERE vehicle = ".$shipment->vehicle." AND start_work <= ".$shipment->shipment_date." AND end_work >= ".$shipment->shipment_date;
            if ($shipment_model->queryShipment($qr)) {
                unset($shipments[$k]);
            }
            else{
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
            $k++;
            
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

                        'allborders' => array(

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