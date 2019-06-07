<?php
Class repairController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->repair) || json_decode($_SESSION['user_permission_action'])->repair != "repair") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Phiếu sửa chữa bảo dưỡng';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;
            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;

            $trangthai = isset($_POST['staff']) ? $_POST['staff'] : null;
            $xe = isset($_POST['xe']) ? $_POST['xe'] : null;
            $mooc = isset($_POST['nv']) ? $_POST['nv'] : null;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'repair_date';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'DESC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

            $vong = (int)date('m',strtotime($batdau));

            $trangthai = date('Y',strtotime($batdau));
            $xe = 0;
            $mooc = 0;
        }

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));

        $trangthai = date('Y',strtotime($batdau));

        $id = $this->registry->router->param_id;
        
        $vehicle_model = $this->model->get('vehicleModel');
        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $this->view->data['vehicles'] = $vehicles;

        $vehicle_data = array();
        foreach ($vehicles as $vehicle) {
            $vehicle_data['id'][$vehicle->vehicle_id] = $vehicle->vehicle_id;
            $vehicle_data['name'][$vehicle->vehicle_id] = $vehicle->vehicle_number;
        }
        $this->view->data['vehicle_data'] = $vehicle_data;

        $romooc_model = $this->model->get('romoocModel');
        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));
        $this->view->data['romoocs'] = $romoocs;

        $romooc_data = array();
        foreach ($romoocs as $romooc) {
            $romooc_data['id'][$romooc->romooc_id] = $romooc->romooc_id;
            $romooc_data['name'][$romooc->romooc_id] = $romooc->romooc_number;
        }
        $this->view->data['romooc_data'] = $romooc_data;

        $repair_model = $this->model->get('repairModel');
        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $join = array('table'=>'staff','where'=>'staff=staff_id');

        $data = array(
            'where' => 'repair_date >= '.strtotime($batdau).' AND repair_date < '.strtotime($ngayketthuc),
        );

        if (isset($id) && $id > 0) {
            $data['where'] = 'repair_id = '.$id;
        }

        if($xe > 0){
            $data['where'] = $data['where'].' AND vehicle = '.$xe;
        }

        if($mooc > 0){
            $data['where'] = $data['where'].' AND romooc = '.$mooc;
        }

        $tongsodong = count($repair_model->getAllRepair($data,$join));
        $tongsotrang = ceil($tongsodong / $sonews);
        

        $this->view->data['page'] = $page;
        $this->view->data['order_by'] = $order_by;
        $this->view->data['order'] = $order;
        $this->view->data['keyword'] = $keyword;
        $this->view->data['pagination_stages'] = $pagination_stages;
        $this->view->data['tongsotrang'] = $tongsotrang;
        $this->view->data['sonews'] = $sonews;
        $this->view->data['limit'] = $limit;
        $this->view->data['batdau'] = $batdau;

        $this->view->data['ketthuc'] = $ketthuc;

        $this->view->data['vong'] = $vong;

        $this->view->data['trangthai'] = $trangthai;
        $this->view->data['xe'] = $xe;
        $this->view->data['mooc'] = $mooc;

        $data = array(
            'order_by'=>$order_by,
            'order'=>$order,
            'limit'=>$x.','.$sonews,
            'where' => 'repair_date >= '.strtotime($batdau).' AND repair_date < '.strtotime($ngayketthuc),
            );

       if (isset($id) && $id > 0) {
            $data['where'] = 'repair_id = '.$id;
        }

        if($xe > 0){
            $data['where'] = $data['where'].' AND vehicle = '.$xe;
        }

        if($mooc > 0){
            $data['where'] = $data['where'].' AND romooc = '.$mooc;
        }
        
        if ($keyword != '') {
            $search = ' AND ( repair_code LIKE "%'.$keyword.'%" 
                        OR staff_name LIKE "%'.$keyword.'%" )';
            $data['where'] .= $search;
        }
        
        $this->view->data['repairs'] = $repair_model->getAllRepair($data,$join);

        $this->view->data['lastID'] = isset($repair_model->getLastRepair()->repair_id)?$repair_model->getLastRepair()->repair_id:0;
        
        $this->view->show('repair/index');
    }

    public function getstaff(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $staff_model = $this->model->get('staffModel');

            

            if ($_POST['keyword'] == "*") {



                $list = $staff_model->getAllStaff();

            }

            else{

                $data = array(

                'where'=>'( staff_name LIKE "%'.$_POST['keyword'].'%" )',

                );

                $list = $staff_model->getAllStaff($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text

                $staff_name = $rs->staff_name;

                if ($_POST['keyword'] != "*") {

                    $staff_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->staff_name);

                }

                

                // add new option

                echo '<li onclick="set_item_staff(\''.$rs->staff_id.'\',\''.$rs->staff_name.'\')">'.$staff_name.'</li>';

            }

        }

    }

    public function add(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->repair) || json_decode($_SESSION['user_permission_action'])->repair != "repair") {
            return $this->view->redirect('user/login');
        }
        if (isset($_POST['yes'])) {
            $repair_model = $this->model->get('repairModel');
            $repair_list_model = $this->model->get('repairlistModel');

            $debit = $this->model->get('debitModel');

            $data = array(
                        
                        'repair_code' => trim($_POST['repair_code']),
                        'repair_date' => strtotime($_POST['repair_date']),
                        'staff' => trim($_POST['staff']),
                        'vehicle' => trim($_POST['vehicle']),
                        'romooc' => trim($_POST['romooc']),
                        'repair_comment' => trim($_POST['repair_comment']),
                        );


            if ($_POST['yes'] != "") {
                if ($repair_model->checkRepair($_POST['yes'],trim($_POST['repair_code']))) {
                    echo "Thông tin này đã tồn tại";
                    return false;
                }
                else{
                    $repair = $repair_model->getRepair($_POST['yes']);

                    $repair_model->updateRepair($data,array('repair_id' => $_POST['yes']));
                    $id_repair = $_POST['yes'];
                    /*Log*/
                    /**/
                    $data_debit = array(
                            'debit_date'=>$data['repair_date'],
                            'staff'=>$data['staff'],
                            'money'=>0,
                            'money_vat'=>0,
                            'comment'=>$data['repair_comment'],
                            'check_debit'=>2,
                            'repair'=>$id_repair,
                        );
                        $debit->updateDebit($data_debit,array('repair'=>$id_repair));

                    echo "Cập nhật thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|repair|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                    }
                
                
            }
            else{

                if ($repair_model->getRepairByWhere(array('repair_code'=>$data['repair_code']))) {
                    echo "Thông tin này đã tồn tại";
                    return false;
                }
                else{
                    $repair_model->createRepair($data);
                    $id_repair = $repair_model->getLastRepair()->repair_id;
                    /*Log*/
                    /**/

                    
                        $data_debit = array(
                            'debit_date'=>$data['repair_date'],
                            'staff'=>$data['staff'],
                            'money'=>0,
                            'money_vat'=>0,
                            'comment'=>$data['repair_comment'],
                            'check_debit'=>2,
                            'repair'=>$id_repair,
                        );
                            $debit->createDebit($data_debit);
                    

                    echo "Thêm thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$repair_model->getLastRepair()->repair_id."|repair|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                    }
                
                
            }

            $total_number = 0;
            $total_price = 0;

            $repair_list = $_POST['repair_list'];

            foreach ($repair_list as $v) {

                $id_repair_list = 0;

                    if (isset($v['repair_list_id']) && $v['repair_list_id'] != "") {

                        $id_repair_list = $v['repair_list_id'];

                    }


                    $data_repair = array(

                        'repair' => $id_repair,

                        'repair_list_price' => trim(str_replace(',','',$v['repair_list_price'])),

                        'repair_list_comment' => trim($v['repair_list_comment']),
                        

                    );

                    if (!$repair_list_model->getRepairByWhere(array('repair_list_id'=>$id_repair_list))) {
                        $repair_list_model->createRepair($data_repair);
                    }
                    else{
                        $repair_list_model->updateRepair($data_repair,array('repair_list_id'=>$id_repair_list));
                    }

                    $total_price += $data_repair['repair_list_price'];
                }

                $repair_model->updateRepair(array('repair_price'=>$total_price),array('repair_id'=>$id_repair));

                $data_debit = array(
                    'money'=>$total_price,
                );
                $debit->updateDebit($data_debit,array('repair'=>$id_repair));
                    
        }
    }
    public function delete(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->repair) || json_decode($_SESSION['user_permission_action'])->repair != "repair") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $repair_model = $this->model->get('repairModel');
            $repair_list_model = $this->model->get('repairlistModel');
            $debit = $this->model->get('debitModel');
            if (isset($_POST['xoa'])) {
                $data = explode(',', $_POST['xoa']);
                foreach ($data as $data) {
                    $repair_model->deleteRepair($data);
                    $repair_list_model->query('DELETE FROM repair_list WHERE repair = '.$data);
                    $debit->queryDebit('DELETE FROM debit WHERE repair = '.$data);
                    
                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|repair|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }

                /*Log*/
                    /**/

                return true;
            }
            else{
                $repair_list_model->query('DELETE FROM repair_list WHERE repair = '.$_POST['data']);
                $debit->queryDebit('DELETE FROM debit WHERE repair = '.$_POST['data']);
                /*Log*/
                    /**/
                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|repair|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);

                return $repair_model->deleteRepair($_POST['data']);
            }
            
        }
    }

    
    public function deletelist(){

        if (isset($_POST['repair_list'])) {

            $repair_list_model = $this->model->get('repairlistModel');



            $repair_list_model->queryRepair('DELETE FROM repair_list WHERE repair_list_id = '.$_POST['repair_list']);

            echo 'Đã xóa thành công';

        }

    }
    public function repairlist(){

        if(isset($_POST['repair'])){

            

            $repair_list_model = $this->model->get('repairlistModel');



            $data = array(

                'where' => 'repair = '.$_POST['repair'],

            );

            $repairs = $repair_list_model->getAllRepair($data);



            $str = "";

            if (!$repairs) {

                $str .= '<tr class="'.$_POST['repair'].'">';

                $str .= '<td><input type="checkbox"  name="chk"></td>';

                $str .= '<td><table style="width: 100%">';

                $str .= '<tr class="'.$_POST['repair'] .'">';

                $str .= '<td>Nội dung</td>';

                $str .= '<td><input type="text" class="repair_list_comment" name="repair_list_comment[]" tabindex="9" ></td>';

                $str .= '<td>Đơn giá</td>';

                $str .= '<td><input type="text" class="repair_list_price numbers" name="repair_list_price[]" tabindex="10" ></td>';

                $str .= '</tr></table></td></tr>';

            }

            else{

                foreach ($repairs as $v) {


                    $str .= '<tr class="'.$_POST['repair'].'">';

                    $str .= '<td><input type="checkbox"  name="chk" data="'.$v->repair_list_id.'" ></td>';

                    $str .= '<td><table style="width: 100%">';

                    $str .= '<tr class="'.$_POST['repair'] .'">';

                    $str .= '<td>Nội dung</td>';

                    $str .= '<td><input type="text" class="repair_list_comment" name="repair_list_comment[]" tabindex="9" data="'.$v->repair_list_id.'" value="'.$v->repair_list_comment.'"></td>';

                    $str .= '<td>Đơn giá</td>';

                    $str .= '<td><input type="text" class="repair_list_price numbers" name="repair_list_price[]" tabindex="10" value="'.$this->lib->formatMoney($v->repair_list_price).'"></td>';

                    $str .= '</tr></table></td></tr>';

                }

            }



            echo $str;

        }

    }

    function export(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }



        $batdau = $this->registry->router->param_id;

        $ketthuc = $this->registry->router->page;

        $xe = $this->registry->router->order_by;

        $mooc = $this->registry->router->order;

        $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

        $info_model = $this->model->get('infoModel');
        $infos = $info_model->getLastInfo();

        $vehicle_model = $this->model->get('vehicleModel');
        $vehicles = $vehicle_model->getAllVehicle();

        $vehicle_data = array();
        foreach ($vehicles as $vehicle) {
            $vehicle_data['id'][$vehicle->vehicle_id] = $vehicle->vehicle_id;
            $vehicle_data['name'][$vehicle->vehicle_id] = $vehicle->vehicle_number;
        }

        $romooc_model = $this->model->get('romoocModel');
        $romoocs = $romooc_model->getAllVehicle();

        $romooc_data = array();
        foreach ($romoocs as $romooc) {
            $romooc_data['id'][$romooc->romooc_id] = $romooc->romooc_id;
            $romooc_data['name'][$romooc->romooc_id] = $romooc->romooc_number;
        }

        $repair_list_model = $this->model->get('repairlistModel');


        $shipment_model = $this->model->get('shipmentModel');

        $join = array('table'=>'repair, staff','where'=>'staff = staff_id AND repair = repair_id');



        $data = array(

            'where' => "1=1",

            );

        if($batdau != "" && $ketthuc != "" ){

            $data['where'] = $data['where'].' AND repair_date >= '.$batdau.' AND repair_date < '.$ngayketthuc;

        }

        if($xe > 0){

            $data['where'] = $data['where'].' AND vehicle = '.$xe;

        }

        if($mooc > 0){

            $data['where'] = $data['where'].' AND romooc = '.$mooc;

        }

        



        /*if ($_SESSION['role_logined'] == 3) {

            $data['where'] = $data['where'].' AND shipment_create_user = '.$_SESSION['userid_logined'];

            

        }*/



        





        $data['order_by'] = 'repair_date';

        $data['order'] = 'ASC';



        $repair_lists = $repair_list_model->getAllRepair($data,$join);

        


            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', mb_strtoupper($infos->info_company, "UTF-8"))

                ->setCellValue('A2', 'PHÒNG VẬT TƯ KỸ THUẬT')

                ->setCellValue('F1', 'CỘNG HÒA XÃ CHỦ NGHĨA VIỆT NAM')

                ->setCellValue('F2', 'Độc lập - Tự do - Hạnh phúc')

                ->setCellValue('A4', 'BẢNG KÊ CHI PHÍ SỬA CHỮA BẢO DƯỠNG')

                ->setCellValue('A6', 'STT')

               ->setCellValue('B6', 'Phiếu sửa chữa')

               ->setCellValue('C6', 'Ngày')

               ->setCellValue('D6', 'Xe')

               ->setCellValue('E6', 'Ro-mooc')

               ->setCellValue('F6', 'Nội dung')

               ->setCellValue('G6', 'Chi phí')

               ->setCellValue('H6', 'Nhân viên');

               




            if ($repair_lists) {



                $hang = 7;

                $i=1;



                $k=0;
                foreach ($repair_lists as $row) {

                    


                        //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                         $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValue('A' . $hang, $i++)

                            ->setCellValueExplicit('B' . $hang, $row->repair_code)

                            ->setCellValue('C' . $hang, $this->lib->hien_thi_ngay_thang($row->repair_date))

                            ->setCellValue('D' . $hang, isset($vehicle_data['id'][$row->vehicle])?$vehicle_data['name'][$row->vehicle]:null)

                            ->setCellValue('E' . $hang, isset($romooc_data['id'][$row->romooc])?$romooc_data['name'][$row->romooc]:null)

                            ->setCellValue('F' . $hang, $row->repair_list_comment)

                            ->setCellValue('G' . $hang, $row->repair_list_price)

                            ->setCellValue('H' . $hang, $row->staff_name);

                         $hang++;



                      

                }

            }





            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.$hang, 'TỔNG')


               ->setCellValue('G'.$hang, '=SUM(G7:G'.($hang-1).')');



            $objPHPExcel->getActiveSheet()->getStyle('A6:H'.$hang)->applyFromArray(

                array(

                    

                    'borders' => array(

                        'allborders' => array(

                          'style' => PHPExcel_Style_Border::BORDER_THIN

                        )

                    )

                )

            );





            $cell = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(6, $hang)->getCalculatedValue();

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.($hang+1), 'Bằng chữ: '.$this->lib->convert_number_to_words(round($cell)).' đồng');



            $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':F'.$hang);

            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+1).':H'.($hang+1));





            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);





            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.($hang+3), 'NGƯỜI LẬP BIỂU')

                ->setCellValue('F'.($hang+3), mb_strtoupper($infos->info_company, "UTF-8"));



            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+3).':C'.($hang+3));

            $objPHPExcel->getActiveSheet()->mergeCells('F'.($hang+3).':H'.($hang+3));



            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':H'.($hang+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':H'.($hang+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':H'.($hang+3))->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );





            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;



            $objPHPExcel->getActiveSheet()->mergeCells('A1:C1');

            $objPHPExcel->getActiveSheet()->mergeCells('F1:H1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:C2');

            $objPHPExcel->getActiveSheet()->mergeCells('F2:H2');



            $objPHPExcel->getActiveSheet()->mergeCells('A4:H4');



            $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(16);



            $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->applyFromArray(

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



            $objPHPExcel->getActiveSheet()->getStyle('G7:G'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(26);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);

            //$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);



            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Sale Report")

                            ->setSubject("Sale Report")

                            ->setDescription("Sale Report.")

                            ->setKeywords("Sale Report")

                            ->setCategory("Sale Report");

            $objPHPExcel->getActiveSheet()->setTitle("Chi phi sua chua bao duong");



            $objPHPExcel->getActiveSheet()->freezePane('A7');

            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BẢNG KÊ CHI PHÍ SỬA CHỮA BẢO DƯỠNG.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }


}
?>