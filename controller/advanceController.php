<?php

Class advanceController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Báo cáo tiền tạm ứng';



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



        $driver_model = $this->model->get('driverModel');

        



        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));



        $this->view->data['vehicles'] = $vehicles;



        $join = array('table'=>'customer, vehicle, user','where'=>'user.user_id = shipment.shipment_create_user AND customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle');



        $shipment_model = $this->model->get('shipmentModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;



        $data = array(

            'where' => "advance > 0",

            );

        if($batdau != "" && $ketthuc != "" ){

            $data['where'] = $data['where'].' AND shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc);

        }

        if($xe > 0){

            $data['where'] = $data['where'].' AND vehicle = '.$xe;

        }

        

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

            'where' => 'advance > 0',

            );

        

        

        if($batdau != "" && $ketthuc != "" ){

            $data['where'] = $data['where'].' AND shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc);

        }

        if($xe > 0){

            $data['where'] = $data['where'].' AND vehicle = '.$xe;

        }



        if ($keyword != '') {

            $search = '( vehicle_number LIKE "%'.$keyword.'%" OR username LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }

        

        

        

        $this->view->data['shipments'] = $shipment_model->getAllShipment($data,$join);



        $driver_data = array();



        foreach ($this->view->data['shipments'] as $shipment) {



            $d_data = array(

                'where'=> ' start_work <= '.$shipment->shipment_date.' AND end_work > '.$shipment->shipment_date.' AND vehicle = '.$shipment->vehicle,

            );
            $d_join = array('table'=>'steersman','where'=>'steersman = steersman_id');

            $drivers = $driver_model->getAllDriver($d_data,$d_join);

            

            foreach ($drivers as $driver) {

                $driver_data[$shipment->shipment_id]['driver_name'] = $driver->steersman_name;

                $driver_data[$shipment->shipment_id]['driver_phone'] = $driver->steersman_phone;

            }

        }

        $this->view->data['driver_data'] = $driver_data;



        $this->view->data['lastID'] = isset($shipment_model->getLastShipment()->shipment_id)?$shipment_model->getLastShipment()->shipment_id:0;

        

        $this->view->show('advance/index');

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

        $driver_model = $this->model->get('driverModel');

        



        

        $shipment_model = $this->model->get('shipmentModel');

        $join = array('table'=>'user, customer, vehicle, road','where'=>'user.user_id = shipment.shipment_create_user AND customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND road_from = shipment_from AND road_to = shipment_to');



        $data = array(

            'where' => "advance > 0",

            );

        if($batdau != "" && $ketthuc != "" ){

            $data['where'] = $data['where'].' AND shipment_date >= '.$batdau.' AND shipment_date < '.$ngayketthuc;

        }

        if($xe > 0){

            $data['where'] = $data['where'].' AND vehicle = '.$xe;

        }

        



        



        $data['order_by'] = 'shipment_date';

        $data['order'] = 'ASC';

        

        

        $shipments = $shipment_model->getAllShipment($data,$join);



        $driver_data = array();

        foreach ($shipments as $ship) {

            $d_data = array(

                'where'=> ' start_work <= '.$ship->shipment_date.' AND end_work > '.$ship->shipment_date.' AND vehicle = '.$ship->vehicle,

            );
            $d_join = array('table'=>'steersman','where'=>'steersman = steersman_id');

            $drivers = $driver_model->getAllDriver($d_data,$d_join);

            

            foreach ($drivers as $driver) {

                $driver_data[$ship->shipment_id]['driver_name'] = $driver->steersman_name;

                $driver_data[$ship->shipment_id]['driver_phone'] = $driver->steersman_phone;

            }

        }



            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

               ->setCellValue('A1', 'STT')

               ->setCellValue('B1', 'Ngày')

               ->setCellValue('C1', 'Xe')

               ->setCellValue('D1', 'Tài xế')

               ->setCellValue('E1', 'Điều xe')

               ->setCellValue('F1', 'Tạm ứng')

               ->setCellValue('G1', 'Lý do');

               



            



            

            

            



            if ($shipments) {



                $hang = 2;

                $i=1;



                foreach ($shipments as $row) {

                    

                    





                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                     $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue('A' . $hang, $i++)

                        ->setCellValueExplicit('B' . $hang, $this->lib->hien_thi_ngay_thang($row->shipment_date))

                        ->setCellValue('C' . $hang, $row->vehicle_number)

                        ->setCellValue('D' . $hang, $driver_data[$row->shipment_id]['driver_name'])

                        ->setCellValue('E' . $hang, $row->username)

                        ->setCellValue('F' . $hang, $row->advance)

                        ->setCellValue('G' . $hang, $row->advance_comment);

                     $hang++;





                  }



          }



            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;





            $objPHPExcel->getActiveSheet()->getStyle('F2:F'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(16);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);

            $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);

            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(40);



            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Advance Report")

                            ->setSubject("Advance Report")

                            ->setDescription("Advance Report.")

                            ->setKeywords("Advance Report")

                            ->setCategory("Advance Report");

            $objPHPExcel->getActiveSheet()->setTitle("Thong ke tam ung");



            $objPHPExcel->getActiveSheet()->freezePane('A1');

            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= THỐNG KÊ TẠM ỨNG.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }





}

?>