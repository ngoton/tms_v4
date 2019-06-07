<?php

Class warehouseController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->warehouse) || json_decode($_SESSION['user_permission_action'])->warehouse != "warehouse") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý tiền bồi dưỡng';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'warehouse_id';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

        }



        
        $join = array('table'=>'place','where'=>'warehouse_code=place_id');


        $warehouse_model = $this->model->get('warehouseModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        

        $tongsodong = count($warehouse_model->getAllWarehouse(null,$join));

        $tongsotrang = ceil($tongsodong / $sonews);

        



        $this->view->data['page'] = $page;

        $this->view->data['order_by'] = $order_by;

        $this->view->data['order'] = $order;

        $this->view->data['keyword'] = $keyword;

        $this->view->data['limit'] = $limit;

        $this->view->data['pagination_stages'] = $pagination_stages;

        $this->view->data['tongsotrang'] = $tongsotrang;

        $this->view->data['sonews'] = $sonews;



        $data = array(

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            );

        

        if ($keyword != '') {

            $search = '( warehouse_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }

        

        

        

        $this->view->data['warehouses'] = $warehouse_model->getAllWarehouse($data,$join);



        $this->view->data['lastID'] = isset($warehouse_model->getLastWarehouse()->warehouse_id)?$warehouse_model->getLastWarehouse()->warehouse_id:0;

        

        $this->view->show('warehouse/index');

    }



    public function getplace(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $place_model = $this->model->get('placeModel');
            $warehouse_model = $this->model->get('warehouseModel');
            

            if ($_POST['keyword'] == "*") {

                $list = $place_model->getAllPlace();

            }

            else{

                $data = array(

                'where'=>'( place_name LIKE "%'.$_POST['keyword'].'%" )',

                );

                $list = $place_model->getAllPlace($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text
                

                $place_name = $rs->place_name;

                if ($_POST['keyword'] != "*") {

                    $place_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->place_name);

                }

                $warehouse = $warehouse_model->getWarehouseByWhere(array('warehouse_code'=>$rs->place_id));
                $warehouses = $warehouse_model->queryWarehouse('SELECT * FROM warehouse WHERE warehouse_code = '.$rs->place_id.' ORDER BY warehouse_id DESC LIMIT 1');
                if ($warehouses) {
                    foreach ($warehouses as $warehouse) {
                        echo '<li onclick="set_item(\''.$rs->place_id.'\',\''.$rs->place_name.'\',\''.$warehouse->warehouse_cont.'\',\''.$warehouse->warehouse_ton.'\',\''.$warehouse->warehouse_weight.'\',\''.$warehouse->warehouse_clean.'\',\''.$warehouse->warehouse_gate.'\',\''.$warehouse->warehouse_add.'\')">'.$place_name.'</li>';
                    }
                    
                }
                else{
                    echo '<li onclick="set_item(\''.$rs->place_id.'\',\''.$rs->place_name.'\',0,0,0,0,0,0)">'.$place_name.'</li>';
                }

                // add new option

                

            }

        }

    }



    public function add(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->warehouse) || json_decode($_SESSION['user_permission_action'])->warehouse != "warehouse") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $warehouse = $this->model->get('warehouseModel');
            $warehouse_temp = $this->model->get('warehousetempModel');

            $data = array(

                        'warehouse_name' => trim($_POST['warehouse_name']),
                        'warehouse_code' => trim($_POST['warehouse_code']),

                        'warehouse_cont' => trim(str_replace(',','',$_POST['warehouse_cont'])),

                        'warehouse_ton' => trim(str_replace(',','',$_POST['warehouse_ton'])),

                        'warehouse_weight' => trim(str_replace(',','',$_POST['warehouse_weight'])),

                        'warehouse_clean' => trim(str_replace(',','',$_POST['warehouse_clean'])),

                        'warehouse_gate' => trim(str_replace(',','',$_POST['warehouse_gate'])),

                        'warehouse_add' => trim(str_replace(',','',$_POST['warehouse_add'])),

                        'start_time' => strtotime($_POST['start_time']),

                        'end_time' => strtotime($_POST['end_time']),

                        'status' => trim($_POST['status']),

                        );

            if ($_POST['yes'] != "") {

                $warehouse_d = $warehouse->getWarehouse($_POST['yes']);

                $warehouse1 = $warehouse->getWarehouseByWhere(array('warehouse_code'=>$warehouse_d->warehouse_code,'end_time'=>(strtotime(date('d-m-Y',strtotime(date('d-m-Y',$warehouse_d->start_time).' -1 day'))))));
                $warehouse2 = $warehouse->getWarehouseByWhere(array('warehouse_code'=>$warehouse_d->warehouse_code,'start_time'=>(strtotime(date('d-m-Y',strtotime(date('d-m-Y',$warehouse_d->end_time).' +1 day'))))));
                if($warehouse1)
                    $warehouse->updateWarehouse(array('warehouse_code'=>$warehouse_d->warehouse_code,'end_time'=>(strtotime(date('d-m-Y',strtotime($_POST['start_time'].' -1 day'))))),array('warehouse_id' => $warehouse1->warehouse_id));
                if($warehouse2)
                    $warehouse->updateWarehouse(array('warehouse_code'=>$warehouse_d->warehouse_code,'start_time'=>(strtotime(date('d-m-Y',strtotime($_POST['end_time'].' +1 day'))))),array('warehouse_id' => $warehouse2->warehouse_id));


                $warehouse->updateWarehouse($data,array('warehouse_id' => trim($_POST['yes'])));

                echo "Cập nhật thành công";

                $data2 = array('warehouse_id'=>$_POST['yes'],'warehouse_temp_date'=>strtotime(date('d-m-Y')),'warehouse_temp_action'=>2,'warehouse_temp_user'=>$_SESSION['userid_logined'],'name'=>'Bồi dưỡng kho');
                    $data_temp = array_merge($data, $data2);
                    $warehouse_temp->createWarehouse($data_temp);

                date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|warehouse|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

            }

            else{


                if ($warehouse->getWarehouseByWhere(array('warehouse_code'=>$data['warehouse_code'],'start_time'=>$data['start_time'],'end_time'=>$data['end_time']))) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $dm1 = $warehouse->queryWarehouse('SELECT * FROM warehouse WHERE warehouse_code='.$data['warehouse_code'].' AND start_time <= '.$data['start_time'].' AND end_time <= '.$data['end_time'].' AND end_time >= '.$data['start_time'].' ORDER BY end_time ASC LIMIT 1');
                    $dm2 = $warehouse->queryWarehouse('SELECT * FROM warehouse WHERE warehouse_code='.$data['warehouse_code'].' AND end_time >= '.$data['end_time'].' AND start_time >= '.$data['start_time'].' AND start_time <= '.$data['end_time'].' ORDER BY end_time ASC LIMIT 1');
                    $dm3 = $warehouse->queryWarehouse('SELECT * FROM warehouse WHERE warehouse_code='.$data['warehouse_code'].' AND start_time <= '.$data['start_time'].' AND end_time >= '.$data['end_time'].' ORDER BY end_time ASC LIMIT 1');

                    if ($dm3) {
                            foreach ($dm3 as $row) {
                                $d = array(
                                    'end_time' => strtotime(date('d-m-Y',strtotime($_POST['start_time'].' -1 day'))),
                                    );
                                $warehouse->updateWarehouse($d,array('warehouse_id'=>$row->warehouse_id));

                                $c = array(
                                    'warehouse_code' => $row->warehouse_code,
                                    'warehouse_name' => $row->warehouse_name,
                                    'warehouse_cont' => $row->warehouse_cont,
                                    'warehouse_ton' => $row->warehouse_ton,
                                    'warehouse_weight' => $row->warehouse_weight,
                                    'warehouse_clean' => $row->warehouse_clean,
                                    'warehouse_gate' => $row->warehouse_gate,
                                    'warehouse_add' => $row->warehouse_add,
                                    'status' => $row->status,
                                    'start_time' => strtotime(date('d-m-Y',strtotime($_POST['end_time'].' +1 day'))),
                                    'end_time' => $row->end_time,
                                    );
                                $warehouse->createWarehouse($c);

                            }

                            

                            
                            $warehouse->createWarehouse($data);

                        }
                        else if ($dm1 || $dm2) {
                            if($dm1){
                                foreach ($dm1 as $row) {
                                    $d = array(
                                        'end_time' => strtotime(date('d-m-Y',strtotime($_POST['start_time'].' -1 day'))),
                                        );
                                    $warehouse->updateWarehouse($d,array('warehouse_id'=>$row->warehouse_id));

                                    
                                }
                            }
                            if($dm2){
                                foreach ($dm2 as $row) {
                                    $d = array(
                                        'start_time' => strtotime(date('d-m-Y',strtotime($_POST['end_time'].' +1 day'))),
                                        );
                                    $warehouse->updateWarehouse($d,array('warehouse_id'=>$row->warehouse_id));


                                }
                            }


                            
                            $warehouse->createWarehouse($data);

                        
                    }
                    else{
                        $warehouse->createWarehouse($data);

                    }

                    echo "Thêm thành công";

                    $data2 = array('warehouse_id'=>$warehouse->getLastWarehouse()->warehouse_id,'warehouse_temp_date'=>strtotime(date('d-m-Y')),'warehouse_temp_action'=>1,'warehouse_temp_user'=>$_SESSION['userid_logined'],'name'=>'Bồi dưỡng kho');
                    $data_temp = array_merge($data, $data2);
                    $warehouse_temp->createWarehouse($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$warehouse->getLastWarehouse()->warehouse_id."|warehouse|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }

                

            }

                    

        }

    }



    

    



    public function delete(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->warehouse) || json_decode($_SESSION['user_permission_action'])->warehouse != "warehouse") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $warehouse = $this->model->get('warehouseModel');
            $warehouse_temp = $this->model->get('warehousetempModel');

            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {

                    $warehouse_data = (array)$warehouse->getWarehouse($data); 

                    $warehouse->deleteWarehouse($data);
                    
                    $data2 = array('warehouse_id'=>$data,'warehouse_temp_date'=>strtotime(date('d-m-Y')),'warehouse_temp_action'=>3,'warehouse_temp_user'=>$_SESSION['userid_logined'],'name'=>'Bồi dưỡng kho');
                    $data_temp = array_merge($warehouse_data, $data2);
                    $warehouse_temp->createWarehouse($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|warehouse|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }

                return true;

            }

            else{

                $warehouse_data = (array)$warehouse->getWarehouse($_POST['data']); 
                $data2 = array('warehouse_id'=>$_POST['data'],'warehouse_temp_date'=>strtotime(date('d-m-Y')),'warehouse_temp_action'=>3,'warehouse_temp_user'=>$_SESSION['userid_logined'],'name'=>'Bồi dưỡng kho');
                    $data_temp = array_merge($warehouse_data, $data2);
                    $warehouse_temp->createWarehouse($data_temp);

                date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|warehouse|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $warehouse->deleteWarehouse($_POST['data']);

            }

            

        }

    }



    function export(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }



        $warehouse_model = $this->model->get('warehouseModel');

        



        $warehouses = $warehouse_model->getAllWarehouse();

        



            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', 'DANH SÁCH KHO')

                ->setCellValue('A3', 'STT')

               ->setCellValue('B3', 'Tên kho')

               ->setCellValue('C3', 'Bồi dưỡng')

               ->setCellValue('D3', 'Cân xe')

               ->setCellValue('E3', 'Quét cont')

               ->setCellValue('F3', 'Vé cổng')

               ->setCellValue('G3', 'Giá theo tấn')

               ->setCellValue('H3', 'Áp dụng')

               ->setCellValue('I3', 'Hết hạn');

               



            



            

            

            



            if ($warehouses) {



                $hang = 4;

                $i=1;



                $kho_data = array();

                foreach ($warehouses as $row) {

                    



                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                     $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue('A' . $hang, $i++)

                        ->setCellValueExplicit('B' . $hang, $row->warehouse_name )

                        ->setCellValue('C' . $hang, $row->warehouse_add)

                        ->setCellValue('D' . $hang, $row->warehouse_weight)

                        ->setCellValue('E' . $hang, $row->warehouse_clean)

                        ->setCellValue('F' . $hang, $row->warehouse_gate)

                        ->setCellValue('G' . $hang, $row->warehouse_ton)

                        ->setCellValue('H' . $hang, $this->lib->hien_thi_ngay_thang($row->start_time))

                        ->setCellValue('I' . $hang, $this->lib->hien_thi_ngay_thang($row->end_time));

                     $hang++;





                  }



            }







            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;



            $objPHPExcel->getActiveSheet()->mergeCells('A1:I1');



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



            $objPHPExcel->getActiveSheet()->getStyle('C4:G'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A3:I3')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A3:I3')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A3:I3')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(26);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(14);

            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);



            $objPHPExcel->getActiveSheet()->freezePane('A4');



            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Warehouse Report")

                            ->setSubject("Warehouse Report")

                            ->setDescription("Warehouse Report.")

                            ->setKeywords("Warehouse Report")

                            ->setCategory("Warehouse Report");

            $objPHPExcel->getActiveSheet()->setTitle("Danh sach kho");



            

            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= DANH SÁCH KHO.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }



    

    public function import(){

        $this->view->disableLayout();

        header('Content-Type: text/html; charset=utf-8');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->warehouse) || json_decode($_SESSION['user_permission_action'])->warehouse != "warehouse") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES['import']['name'] != null) {



            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $warehouse = $this->model->get('warehouseModel');



            $objPHPExcel = new PHPExcel();

            // Set properties

            if (pathinfo($_FILES['import']['name'], PATHINFO_EXTENSION) == "xls") {

                $objReader = PHPExcel_IOFactory::createReader('Excel5');

            }

            else if (pathinfo($_FILES['import']['name'], PATHINFO_EXTENSION) == "xlsx") {

                $objReader = PHPExcel_IOFactory::createReader('Excel2007');

            }

            

            $objReader->setReadDataOnly(false);



            $objPHPExcel = $objReader->load($_FILES['import']['tmp_name']);

            $objWorksheet = $objPHPExcel->getActiveSheet();



            



            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10

            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'



            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5



            //var_dump($objWorksheet->getMergeCells());die();

            

             



                for ($row = 2; $row <= $highestRow; ++ $row) {

                    $val = array();

                    for ($col = 0; $col < $highestColumnIndex; ++ $col) {

                        $cell = $objWorksheet->getCellByColumnAndRow($col, $row);

                        // Check if cell is merged

                        foreach ($objWorksheet->getMergeCells() as $cells) {

                            if ($cell->isInRange($cells)) {

                                $currMergedCellsArray = PHPExcel_Cell::splitRange($cells);

                                $cell = $objWorksheet->getCell($currMergedCellsArray[0][0]);

                                break;

                                

                            }

                        }

                        //$val[] = $cell->getValue();

                        $val[] = is_numeric($cell->getCalculatedValue()) ? round($cell->getCalculatedValue()) : $cell->getCalculatedValue();

                        //here's my prob..

                        //echo $val;

                    }

                    if ($val[1] != null ) {



                            if(!$warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[1])))) {

                                $warehouse_data = array(

                                'warehouse_name' => trim($val[1]),

                                'warehouse_add' => trim($val[2]),

                                'warehouse_weight' => trim($val[3]),

                                'warehouse_clean' => trim($val[4]),

                                'warehouse_gate' => trim($val[5]),

                                'warehouse_ton' => trim($val[6]),

                                'warehouse_cont' => trim($val[2])+trim($val[3])+trim($val[4])+trim($val[5]),

                                );

                                $warehouse->createWarehouse($warehouse_data);

                            }

                            else if($warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[1])))){

                                $id_warehouse = $warehouse->getWarehouseByWhere(array('warehouse_name'=>trim($val[1])))->warehouse_id;

                                $warehouse_data = array(

                                'warehouse_add' => trim($val[2]),

                                'warehouse_weight' => trim($val[3]),

                                'warehouse_clean' => trim($val[4]),

                                'warehouse_gate' => trim($val[5]),

                                'warehouse_ton' => trim($val[6]),

                                'warehouse_cont' => trim($val[2])+trim($val[3])+trim($val[4])+trim($val[5]),

                                );

                                $warehouse->updateWarehouse($warehouse_data,array('warehouse_id' => $id_warehouse));

                            }





                        

                    }

                    

                    //var_dump($this->getNameDistrict($this->lib->stripUnicode($val[1])));

                    // insert





                }

                //return $this->view->redirect('transport');

            

            return $this->view->redirect('warehouse');

        }

        $this->view->show('warehouse/import');



    }



    public function view() {

        

        $this->view->show('handling/view');

    }



}

?>