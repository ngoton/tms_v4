<?php

Class commissionController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Báo cáo chi hoa hồng';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;

            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;

            $trangthai = isset($_POST['trangthai']) ? $_POST['trangthai'] : null;

            $kh = isset($_POST['sl_vehicle']) ? $_POST['sl_vehicle'] : null;

            

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'shipment_date';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

            $vong = (int)date('m',strtotime($batdau));

            $trangthai = date('Y',strtotime($batdau));

            $kh = 0;

            

        }

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));

        $trangthai = date('Y',strtotime($batdau));



        $shipment_model = $this->model->get('shipmentModel');



        $customer_model = $this->model->get('customerModel');



        //$join = array('table'=>'shipment, vehicle, user','where'=>'user.user_id = shipment.shipment_create_user AND customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle GROUP BY customer_id');



        $this->view->data['batdau'] = $batdau;

        $this->view->data['ketthuc'] = $ketthuc;

        $this->view->data['vong'] = $vong;

        $this->view->data['trangthai'] = $trangthai;

        $this->view->data['kh'] = $kh;



        $customer_lists = $customer_model->getAllCustomer(array('order_by'=>'customer_name','order'=>'ASC'));

        $this->view->data['customer_lists'] = $customer_lists;



        // $data = array(

        //     'where' => 'commission > 0',

        //     );

        

        // if($kh>0){

        //     $data['where'] .= ' AND customer = '.$kh;

        // }

        

        // if($batdau != "" && $ketthuc != "" ){

        //     $data['where'] = $data['where'].' AND shipment_date >= '.strtotime($batdau).' AND shipment_date <= '.strtotime($ketthuc);

        // }

      



        // if ($keyword != '') {

        //     $search = '( customer_name LIKE "%'.$keyword.'%" OR username LIKE "%'.$keyword.'%")';

        //     $data['where'] = $search;

        // }

        

        // $customers = $customer_model->getAllCustomer($data,$join);

        

        // $this->view->data['customers'] = $customers;



        // $ships = $shipment_model->queryShipment('SELECT *, SUM(commission_number) as hoahong, SUM(COALESCE(shipment_revenue,0)+COALESCE(shipment_charge_excess,0)+COALESCE(revenue_other,0)) as doanhthu FROM customer,shipment,user,vehicle WHERE user.user_id = shipment.shipment_create_user AND customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND commission > 0 AND shipment_date >= '.strtotime($batdau).' AND shipment_date <= '.strtotime($ketthuc).' GROUP BY customer_name');



        // $ship_data = array();



        // foreach ($ships as $shipment) {

        //     $ship_data[$shipment->customer_id]['hoahong'] = $shipment->hoahong;

        //     $ship_data[$shipment->customer_id]['doanhthu'] = $shipment->doanhthu;

        // }



        // $this->view->data['hoahong'] = $ship_data;

        

        $query = 'SELECT * FROM customer,shipment,user,vehicle WHERE user.user_id = shipment.shipment_create_user AND customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND commission > 0 AND shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc);

        if ($kh>0) {

            $query .= ' AND customer = '.$kh;

        }

        $query .= ' ORDER BY customer_name ASC';



        $ships = $shipment_model->queryShipment($query);



        $commission_data = array();

        $i = 0;



        foreach ($ships as $shipment) {

            if (!isset($commission_data[$i]['customer'])) {

                $commission_data[$i]['date'] = $shipment->shipment_date;

                $commission_data[$i]['end'] = "";

                $commission_data[$i]['customer'] = $shipment->customer_name;

                $commission_data[$i]['username'] = $shipment->username;

                $commission_data[$i]['commission'] = $shipment->commission;

                $commission_data[$i]['commission_number'] = $shipment->commission_number;

                $commission_data[$i]['revenue'] = $shipment->shipment_revenue+$shipment->shipment_charge_excess+$shipment->revenue_other;

            }

            else{  

                if ($commission_data[$i]['customer'] == $shipment->customer_name && $commission_data[$i]['commission'] != $shipment->commission) {

                    $commission_data[$i]['end'] = strtotime('-1 day',$shipment->shipment_date);

                }



                if ($commission_data[$i]['customer'] != $shipment->customer_name || $commission_data[$i]['commission'] != $shipment->commission) {

                    $i++;

                    $commission_data[$i]['end'] = "";

                    $commission_data[$i]['date'] = $shipment->shipment_date;

                    $commission_data[$i]['customer'] = $shipment->customer_name;

                    $commission_data[$i]['username'] = $shipment->username;

                    $commission_data[$i]['commission'] = $shipment->commission;

                    $commission_data[$i]['commission_number'] = $shipment->commission_number;

                    $commission_data[$i]['revenue'] = $shipment->shipment_revenue+$shipment->shipment_charge_excess+$shipment->revenue_other;

                }

                else{

                    $commission_data[$i]['end'] = $shipment->shipment_date;

                    $commission_data[$i]['commission_number'] += $shipment->commission_number;

                    $commission_data[$i]['revenue'] += $shipment->shipment_revenue+$shipment->shipment_charge_excess+$shipment->revenue_other;

                }

            }

        }



        $this->view->data['commission_data'] = $commission_data;

        

        

        $this->view->show('commission/index');

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

        $kh = $this->registry->router->order_by;

        $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

        $shipment_model = $this->model->get('shipmentModel');



        $query = 'SELECT * FROM customer,shipment,user,vehicle WHERE user.user_id = shipment.shipment_create_user AND customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND commission > 0 AND shipment_date >= '.$batdau.' AND shipment_date < '.$ngayketthuc;

        if ($kh>0) {

            $query .= ' AND customer = '.$kh;

        }

        $query .= ' ORDER BY customer_name ASC';



        $ships = $shipment_model->queryShipment($query);



        $commission_data = array();

        $i = 0;



        foreach ($ships as $shipment) {

            if (!isset($commission_data[$i]['customer'])) {

                $commission_data[$i]['date'] = $shipment->shipment_date;

                $commission_data[$i]['end'] = "";

                $commission_data[$i]['customer'] = $shipment->customer_name;

                $commission_data[$i]['username'] = $shipment->username;

                $commission_data[$i]['commission'] = $shipment->commission;

                $commission_data[$i]['commission_number'] = $shipment->commission_number;

                $commission_data[$i]['revenue'] = $shipment->shipment_revenue+$shipment->shipment_charge_excess+$shipment->revenue_other;

            }

            else{  

                if ($commission_data[$i]['customer'] == $shipment->customer_name && $commission_data[$i]['commission'] != $shipment->commission) {

                    $commission_data[$i]['end'] = strtotime('-1 day',$shipment->shipment_date);

                }

                   

                if ($commission_data[$i]['customer'] != $shipment->customer_name || $commission_data[$i]['commission'] != $shipment->commission) {

                    $i++;

                    $commission_data[$i]['end'] = "";

                    $commission_data[$i]['date'] = $shipment->shipment_date;

                    $commission_data[$i]['customer'] = $shipment->customer_name;

                    $commission_data[$i]['username'] = $shipment->username;

                    $commission_data[$i]['commission'] = $shipment->commission;

                    $commission_data[$i]['commission_number'] = $shipment->commission_number;

                    $commission_data[$i]['revenue'] = $shipment->shipment_revenue+$shipment->shipment_charge_excess+$shipment->revenue_other;

                }

                else{

                    $commission_data[$i]['end'] = $shipment->shipment_date;

                    $commission_data[$i]['commission_number'] += $shipment->commission_number;

                    $commission_data[$i]['revenue'] += $shipment->shipment_revenue+$shipment->shipment_charge_excess+$shipment->revenue_other;

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

                ->setCellValue('G1', 'CỘNG HÒA XÃ CHỦ NGHĨA VIỆT NAM')

                ->setCellValue('G2', 'Độc lập - Tự do - Hạnh phúc')

                ->setCellValue('A4', 'BẢNG KÊ CHI PHÍ HOA HỒNG')

                

               ->setCellValue('A6', 'STT')

               ->setCellValue('B6', 'NGÀY')

               ->setCellValue('C6', 'KHÁCH HÀNG')

               ->setCellValue('D6', 'TỔNG DOANH THU')

               ->setCellValue('E6', 'SẢN LƯỢNG')

               ->setCellValue('F6', 'ĐƠN GIÁ')

               ->setCellValue('G6', 'TỔNG TIỀN')

               ->setCellValue('H6', 'NGƯỜI NHẬN')

               ->setCellValue('I6', 'NGƯỜI ĐỀ NGHỊ');

               



            



            

            

            



            if ($commission_data) {



                $hang = 7;

                $i=1;



                foreach ($commission_data as $row) {

                    

                    $kt = $row['end'] != "" ? ' - '.$this->lib->hien_thi_ngay_thang($row['end']) : null;





                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                     $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue('A' . $hang, $i++)

                        ->setCellValueExplicit('B' . $hang, $this->lib->hien_thi_ngay_thang($row['date']).$kt)

                        ->setCellValue('C' . $hang, $row['customer'])

                        ->setCellValue('D' . $hang, $row['revenue'])

                        ->setCellValue('E' . $hang, $row['commission_number'])

                        ->setCellValue('F' . $hang, $row['commission'])

                        ->setCellValue('G' . $hang, '=E'.$hang.'*F'.$hang)

                        ->setCellValue('H' . $hang, "")

                        ->setCellValue('I' . $hang, $row['username']);

                     $hang++;





                  }



                  $objPHPExcel->setActiveSheetIndex($index_worksheet)

                        ->setCellValue('A'.$hang, 'TỔNG')

                        ->setCellValue('D'.$hang, '=SUM(D7:D'.($hang-1).')')

                       ->setCellValue('E'.$hang, '=SUM(E7:E'.($hang-1).')')

                       ->setCellValue('F'.$hang, '=SUM(F7:F'.($hang-1).')')

                       ->setCellValue('G'.$hang, '=SUM(G7:G'.($hang-1).')');



                    $objPHPExcel->getActiveSheet()->getStyle('A6:I'.$hang)->applyFromArray(

                        array(

                            

                            'borders' => array(

                                'outline' => array(

                                  'style' => PHPExcel_Style_Border::BORDER_THIN

                                )

                            )

                        )

                    );



                  $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);





                    $objPHPExcel->setActiveSheetIndex($index_worksheet)

                        ->setCellValue('A'.($hang+3), 'NGƯỜI LẬP BIỂU')

                        ->setCellValue('G'.($hang+3), 'NGƯỜI DUYỆT');



                    $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+3).':D'.($hang+3));

                    $objPHPExcel->getActiveSheet()->mergeCells('G'.($hang+3).':I'.($hang+3));



                    $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':I'.($hang+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':I'.($hang+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



                    $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':I'.($hang+3))->applyFromArray(

                        array(

                            

                            'font' => array(

                                'bold'  => true,

                                'color' => array('rgb' => '000000')

                            )

                        )

                    );



          }



            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;



            $objPHPExcel->getActiveSheet()->mergeCells('A1:D1');

            $objPHPExcel->getActiveSheet()->mergeCells('G1:I1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:D2');

            $objPHPExcel->getActiveSheet()->mergeCells('G2:I2');



            $objPHPExcel->getActiveSheet()->mergeCells('A4:I4');



            $objPHPExcel->getActiveSheet()->getStyle('A1:I6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:I6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(16);



            $objPHPExcel->getActiveSheet()->getStyle('A1:I6')->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('A2:I2')->applyFromArray(

                array(

                    

                    'font' => array(

                        'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':C'.$hang);



            



            $objPHPExcel->getActiveSheet()->getStyle('D2:D'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('F2:G'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A1:I6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:I6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:I6')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(16);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(20);



            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Commission Report")

                            ->setSubject("Commission Report")

                            ->setDescription("Commission Report.")

                            ->setKeywords("Commission Report")

                            ->setCategory("Commission Report");

            $objPHPExcel->getActiveSheet()->setTitle("Thong ke tam chi hoa hong");



            $objPHPExcel->getActiveSheet()->freezePane('A7');

            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= THỐNG KÊ CHI HOA HỒNG.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }





}

?>