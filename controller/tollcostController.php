<?php
Class tollcostController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->tollcost) || json_decode($_SESSION['user_permission_action'])->tollcost != "tollcost") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Tổng hợp Phiếu cầu đường';

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
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'vehicle_number';
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

        $place_model = $this->model->get('placeModel');

        $place_data = array();

        $places = $place_model->getAllPlace();


        foreach ($places as $place) {

                $place_data['place_id'][$place->place_id] = $place->place_id;

                $place_data['place_name'][$place->place_id] = $place->place_name;

        }

        $this->view->data['place'] = $place_data;

        $shipment_model = $this->model->get('shipmentModel');
        $join = array('table'=>'customer, vehicle','where'=>'vehicle = vehicle_id AND customer = customer_id AND shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc));
        $shipments = $shipment_model->getAllShipment(null,$join);
        $this->view->data['shipments'] = $shipments;


        $vehicle_model = $this->model->get('vehicleModel');
        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $toll_cost_model = $this->model->get('tollcostModel');

        $join = array('table'=>'toll, shipment, vehicle','where'=>'vehicle = vehicle_id AND toll_cost.toll=toll.toll_id AND shipment=shipment_id AND shipment_date >= '.strtotime($batdau).' AND shipment_date < '.strtotime($ngayketthuc));

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

        $tongsodong = count($toll_cost_model->getAllToll($data,$join));
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
        
        if ($keyword != '') {
            $ngay = (strtotime(str_replace("/", "-", $keyword))!="")?(' OR shipment_date LIKE "%'.strtotime(str_replace("/", "-", $keyword)).'%"'):"";
            $search = '(
                    vehicle_number LIKE "%'.$keyword.'%"
                    OR customer_name LIKE "%'.$keyword.'%"
                    '.$ngay.'
                        )';
            $data['where'] = $data['where']." AND ".$search;
        }

        $toll_costs = $toll_cost_model->getAllToll($data,$join);


        $v = array();
        foreach ($toll_costs as $toll) {

            $v[$toll->vehicle] = isset($v[$toll->vehicle])?($v[$toll->vehicle] + 1) : (0+1) ;
        }


        

        $this->view->data['toll_costs'] = $toll_costs;
        $this->view->data['arr'] = $v;


        $this->view->show('tollcost/index');
    }

    public function gettoll(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $toll_model = $this->model->get('tollModel');
            $bridge_model = $this->model->get('bridgecostModel');
            
            if ($_POST['keyword'] == "*") {

                $list = $toll_model->getAllToll();
            }
            else{
                $data = array(
                'where'=>'( toll_name LIKE "%'.$_POST['keyword'].'%")',
                );
                $list = $toll_model->getAllToll($data);
            }
            
            foreach ($list as $rs) {
                // put in bold the written text
                $toll_name = $rs->toll_name;
                if ($_POST['keyword'] != "*") {
                    $toll_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->toll_name);
                }
                
                $check_vat = $bridge_model->getBridgecostByWhere(array('toll_booth'=>$rs->toll_id));
                // add new option
                echo '<li onclick="set_item(\''.$rs->toll_id.'\',\''.$rs->toll_name.'\',\''.$check_vat->toll_booth_cost.'\',\''.$check_vat->check_vat.'\')">'.$toll_name.'</li>';
            }
        }
    }

    public function check(){
        $id = $_POST['shipment'];

        $shipment_model = $this->model->get('shipmentModel');
        $road_model = $this->model->get('roadModel');
        $toll_model = $this->model->get('tollModel');
        $toll_cost_model = $this->model->get('tollcostModel');
        $bridge_cost_model = $this->model->get('bridgecostModel');

        $shipments = $shipment_model->getShipment($id);
        $roads = $road_model->queryRoad('SELECT * FROM road WHERE road_id IN ("'.str_replace(',', '","', $shipments->route).'")');
        $data = array(
            'where'=>'shipment = '.$id,
        );
        $toll_costs = $toll_cost_model->getAllToll($data);

        $data = array(
            'toll_id' => "",
            'toll_name' => "",
            'toll_cost' => "",
            'check_vat' => "",
        );

        $toll = '0';
        foreach ($toll_costs as $toll_cost) {  
            $toll .= ','.$toll_cost->toll;
        }

        foreach ($roads as $road) {
            $bridge_costs = $bridge_cost_model->queryBridgecost('SELECT * FROM bridge_cost, toll WHERE toll_id = toll_booth AND check_vat = 1 AND road = '.$road->road_id.' AND toll_booth not in ('.$toll.')');
            if ($bridge_costs) {
                foreach ($bridge_costs as $bridge_cost) {
                    $data = array(
                        'toll_id' => $bridge_cost->toll_id,
                        'toll_name' => $bridge_cost->toll_name,
                        'toll_cost' => $this->lib->formatMoney($bridge_cost->toll_booth_cost),
                        'check_vat' => $bridge_cost->check_vat,
                    );
                }
            }
            else{
                $bridge_costs = $bridge_cost_model->queryBridgecost('SELECT * FROM bridge_cost, toll WHERE toll_id = toll_booth AND check_vat = 0 AND road = '.$road->road_id);
                foreach ($bridge_costs as $bridge_cost) {
                    $data = array(
                        'toll_id' => $bridge_cost->toll_id,
                        'toll_name' => $bridge_cost->toll_name,
                        'toll_cost' => $this->lib->formatMoney($bridge_cost->toll_booth_cost),
                        'check_vat' => $bridge_cost->check_vat,
                    );
                }
            }
            
        }
        

        echo json_encode($data);
    }

    public function receive(){
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (isset($_POST['shipment'])) {
            $toll_model = $this->model->get('tollModel');
            $toll_cost_model = $this->model->get('tollcostModel');
            $toll_cost_other_model = $this->model->get('tollcostotherModel');

            $invoice = ($_POST['invoice_number']!="0000000")?$_POST['invoice_number']:null;

            $data = array(
                'shipment' => trim($_POST['shipment']),
                'toll_cost' => trim(str_replace(',','',$_POST['toll_cost'])),
                'toll_number' => trim($_POST['toll_number']),
                'invoice_number' => $invoice,

            );

            if (trim($_POST['toll']) != "") {
                $data['toll'] = trim($_POST['toll']);
            }
            else if (trim($_POST['toll_name']) != "") {
                $data_toll = array(
                    'toll_name' => trim($_POST['toll_name']),
                );
                $toll_model->createToll($data_toll);
                $data['toll'] = $toll_model->getLastToll()->toll_id;
            }

            if (trim($_POST['toll_cost_other']) != "" && trim($_POST['toll_cost_other']) != 0) {
                $data_other = array(
                    'toll_cost_other' => trim(str_replace(',','',$_POST['toll_cost_other'])),
                    'shipment' => trim($_POST['shipment']),
                );
                $toll_cost_other_model->createToll($data_other);
            }

            if ($data['toll_cost'] > 0) {
                $toll_cost_model->createToll($data);
            }
        }
    }

    public function add(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->tollcost) || json_decode($_SESSION['user_permission_action'])->tollcost != "tollcost") {
            return $this->view->redirect('user/login');
        }
        if (isset($_POST['yes'])) {
            $toll_cost_model = $this->model->get('tollcostModel');
            $debit = $this->model->get('debitModel');
            $vat = $this->model->get('vatModel');
            $shipment_model = $this->model->get('shipmentModel');

            $data = array(
                        
                        'toll_number' => trim($_POST['toll_number']),
                        'invoice_number' => trim($_POST['invoice_number']),
                        'invoice_date' => strtotime(trim($_POST['invoice_date'])),
                        'toll' => trim($_POST['toll']),
                        'toll_cost' => trim(str_replace(',','',$_POST['toll_cost'])),
                        'toll_cost_vat' => trim(str_replace(',','',$_POST['toll_cost_vat'])),
                        'shipment' => trim($_POST['shipment']),
                        );


            if ($_POST['yes'] != "") {
                $toll = $toll_cost_model->getToll($_POST['yes']);
                $shipment = $shipment_model->getShipment($data['shipment']);

                $toll_cost_model->updateToll($data,array('toll_cost_id' => $_POST['yes']));

                $id_toll = $_POST['yes'];
                
                if (($toll->toll == "" || $toll->toll == 0 ) && $data['toll'] > 0) {
                    if ($data['toll_cost_vat'] > 0) {
                        $data_debit = array(
                            'debit_date'=>$shipment->shipment_date,
                            'toll'=>$data['toll'],
                            'money'=>$data['toll_cost'],
                            'money_vat_price'=>$data['toll_cost_vat'],
                            'money_vat'=>1,
                            'comment'=>'Phí cầu đường '.$data['invoice_number'],
                            'check_debit'=>2,
                            'toll_cost'=>$id_toll,
                        );
                        $debit->createDebit($data_debit);
                    }
                    else{
                        $data_debit = array(
                            'debit_date'=>$shipment->shipment_date,
                            'toll'=>$data['toll'],
                            'money'=>$data['toll_cost'],
                            'money_vat'=>0,
                            'comment'=>'Phí cầu đường ',
                            'check_debit'=>2,
                            'toll_cost'=>$id_toll,
                        );
                        $debit->createDebit($data_debit);
                    }
                        
                }
                else if($toll->toll > 0 && $data['toll'] > 0){
                    
                    if ($data['toll_cost_vat'] > 0) {
                        $data_debit = array(
                            'debit_date'=>$shipment->shipment_date,
                            'toll'=>$data['toll'],
                            'money'=>$data['toll_cost'],
                            'money_vat_price'=>$data['toll_cost_vat'],
                            'money_vat'=>1,
                            'comment'=>'Phí cầu đường '.$data['invoice_number'],
                            'check_debit'=>2,
                            'toll_cost'=>$id_toll,
                        );
                        $debit->updateDebit($data_debit,array('toll_cost'=>$id_toll));
                    }
                    else{
                        $data_debit = array(
                            'debit_date'=>$shipment->shipment_date,
                            'toll'=>$data['toll'],
                            'money'=>$data['toll_cost'],
                            'money_vat_price'=>0,
                            'money_vat'=>0,
                            'comment'=>'Phí cầu đường '.$data['invoice_number'],
                            'check_debit'=>2,
                            'toll_cost'=>$id_toll,
                        );
                        $debit->updateDebit($data_debit,array('toll_cost'=>$id_toll));
                    }
                }
                else if($toll->toll > 0 && ($data['toll'] == 0 || $data['toll'] == "")){
                    $debit->queryDebit('DELETE FROM debit WHERE toll_cost = '.$id_toll);
                }   

                if ($toll->toll_cost_vat > 0 && $data['toll_cost_vat'] >0) {
                    $data_vat = array(
                        'in_out'=>1,
                        'vat_number'=>$data['invoice_number'],
                        'vat_date'=>$data['invoice_date'],
                        'toll_cost'=>$id_toll,
                    );
                    $vat->updateVAT($data_vat,array('toll_cost'=>$id_toll));
                }
                else if(($toll->toll_cost_vat == 0 || $toll->toll_cost_vat == "" )&& $data['toll_cost_vat'] >0) {
                    $data_vat = array(
                        'in_out'=>1,
                        'vat_number'=>$data['invoice_number'],
                        'vat_date'=>$data['invoice_date'],
                        'toll_cost'=>$id_toll,
                    );
                    $vat->createVAT($data_vat);
                }
                else if($toll->toll_cost_vat > 0 && ($data['toll_cost_vat'] == 0 || $data['toll_cost_vat'] == "")){
                    $vat->queryVAT('DELETE FROM vat WHERE toll_cost = '.$id_toll);
                }
                    /*Log*/
                    /**/
                    echo "Cập nhật thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|toll_cost|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
            }
            else{

               $toll_cost_model->createToll($data);
                $id_toll = $toll_cost_model->getLastToll()->toll_cost_id;
                $shipment = $shipment_model->getShipment($data['shipment']);

                    if ($data['toll_cost_vat'] > 0) {
                        $data_debit = array(
                            'debit_date'=>$shipment->shipment_date,
                            'toll'=>$data['toll'],
                            'money'=>$data['toll_cost'],
                            'money_vat_price'=>$data['toll_cost_vat'],
                            'money_vat'=>1,
                            'comment'=>'Phí cầu đường '.$data['invoice_number'],
                            'check_debit'=>2,
                            'toll_cost'=>$id_toll,
                        );
                        $debit->createDebit($data_debit);

                        $data_vat = array(
                            'in_out'=>1,
                            'vat_number'=>$data['invoice_number'],
                            'vat_date'=>$data['invoice_date'],
                            'toll_cost'=>$id_toll,
                        );
                        $vat->createVAT($data_vat);
                    }
                    else{
                        $data_debit = array(
                            'debit_date'=>$shipment->shipment_date,
                            'toll'=>$data['toll'],
                            'money'=>$data['toll_cost'],
                            'money_vat'=>0,
                            'comment'=>'Phí cầu đường ',
                            'check_debit'=>2,
                            'toll_cost'=>$id_toll,
                        );
                        $debit->createDebit($data_debit);
                    }  
                    /*Log*/
                    /**/

                    echo "Thêm thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$toll_cost_model->getLastToll()->toll_cost_id."|export_stock|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                
            }  
        }
    }


    public function delete(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->tollcost) || json_decode($_SESSION['user_permission_action'])->tollcost != "tollcost") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $toll_cost_model = $this->model->get('tollcostModel');
            $debit = $this->model->get('debitModel');
            $vat = $this->model->get('vatModel');
           
            if (isset($_POST['xoa'])) {
                $data = explode(',', $_POST['xoa']);
                foreach ($data as $data) {
                       $toll_cost_model->deleteToll($data);
                       $debit->queryDebit('DELETE FROM debit WHERE toll_cost = '.$data);
                       $vat->queryVAT('DELETE FROM vat WHERE toll_cost = '.$data);

                        echo "Xóa thành công";
                        date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|toll_cost|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                    
                    
                }
                return true;
            }
            else{
                        $toll_cost_model->deleteToll($_POST['data']);
                        $debit->queryDebit('DELETE FROM debit WHERE toll_cost = '.$_POST['data']);
                        $vat->queryVAT('DELETE FROM vat WHERE toll_cost = '.$_POST['data']);

                        echo "Xóa thành công";
                        date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|toll_cost|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                    
            }
            
        }
    }


    function export(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }



        $this->view->data['lib'] = $this->lib;

        $batdau = $this->registry->router->param_id;
        $ketthuc = $this->registry->router->page;
        $xe = $this->registry->router->order_by;

        $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

        $toll_cost_model = $this->model->get('tollcostModel');
        $toll_cost_other_model = $this->model->get('tollcostotherModel');

        $join = array('table'=>'toll, shipment, vehicle','where'=>'vehicle = vehicle_id AND toll_cost.toll=toll.toll_id AND shipment=shipment_id AND shipment_date >= '.$batdau.' AND shipment_date < '.$ngayketthuc);

        $data = array(
            'where' => '1 = 1',
            'order_by' =>'vehicle_number',
            'order' => 'ASC',
        );

        if ($xe > 0) {
            $data['where'] .= ' AND vehicle = '.$xe;
        }

        $toll_costs = $toll_cost_model->getAllToll($data,$join);

        $join = array('table'=>'shipment, vehicle','where'=>'vehicle = vehicle_id AND shipment=shipment_id AND shipment_date >= '.$batdau.' AND shipment_date < '.$ngayketthuc);

        //$toll_cost_others = $toll_cost_other_model->getAllToll($data);

        $other = array(); $v = array();
        foreach ($toll_costs as $toll) {
            $data = array(
                'where' => 'vehicle = '.$toll->vehicle,
            );
            $other_vehicle = $toll_cost_other_model->getAllToll($data,$join);
            foreach ($other_vehicle as $other_cost) {
                $other[$toll->vehicle] = isset($other[$toll->vehicle])?$other[$toll->vehicle]+$other_cost->toll_cost_other:$other_cost->toll_cost_other;
            }

            $v[$toll->vehicle] = isset($v[$toll->vehicle])?($v[$toll->vehicle] + 1) : (0+1) ;
        }


        

            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();



            



            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', 'CÔNG TY CỔ PHẦN TÂN CẢNG MIỀN TRUNG')

                ->setCellValue('A2', 'PHÊ DUYỆT')

                ->setCellValue('H1', 'CỘNG HÒA XÃ CHỦ NGHĨA VIỆT NAM')

                ->setCellValue('H2', 'Độc lập - Tự do - Hạnh phúc')

                ->setCellValue('A6', 'BẢNG KÊ TỔNG HỢP CHI TIẾT PHÍ CẦU ĐƯỜNG')

                

               ->setCellValue('A8', 'STT')

               ->setCellValue('B8', 'MẪU SỐ')

               ->setCellValue('C8', 'KÝ HIỆU')

               ->setCellValue('D8', 'SỐ HÓA ĐƠN')

               ->setCellValue('E8', 'TÊN NGƯỜI BÁN')

               ->setCellValue('F8', 'MÃ SỐ THUẾ')

               ->setCellValue('G8', 'MẶT HÀNG')

               ->setCellValue('H8', 'GIÁ TRỊ HHDV MUA VÀO CHƯA THUẾ')

               ->setCellValue('I8', 'THUẾ SUẤT')
               ->setCellValue('J8', 'THUẾ GTGT')
               ->setCellValue('K8', 'CỘNG');

               



            if ($toll_costs) {

                $hangbatdau = 0;
                $sumthue = "=0";
                $sumkthue = "=0";

                $hang = 9;

                $i=1;

                $tonggt=0; $tongthue=0; $stt=1; $vehicle = 0; $v = array(); $gt = array(); $thue = array(); $tongkthue = 0;

                foreach ($toll_costs as $row) {

                $tonggt += round($row->toll_cost/1.1);
                $tongthue += round(round($row->toll_cost/1.1)*0.1);

                if($row->vehicle != $vehicle){
                $vehicle = $row->vehicle;

                $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue('A' . $hang, 'PHÍ CẦU ĐƯỜNG - XE '.$row->vehicle_number);

                $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':K'.$hang);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getFont()->setBold(true);

                    $hang++;

                    $hangbatdau = $hang;
                }

                $arr[$row->vehicle] = isset($arr[$row->vehicle])?($arr[$row->vehicle] + 1) : (0+1) ;

                $other[$row->vehicle] = isset($other[$row->vehicle])?$other[$row->vehicle]:0;

                $tongkthue += $other[$row->vehicle];

                    //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                     $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue('A' . $hang, $i++)

                        ->setCellValueExplicit('B' . $hang, $row->toll_symbol)

                        ->setCellValue('C' . $hang, $row->toll_number)

                        ->setCellValue('D' . $hang, "'".$row->invoice_number)

                        ->setCellValue('E' . $hang, $row->toll_name)

                        ->setCellValue('F' . $hang, $row->toll_mst)

                        ->setCellValue('G' . $hang, $row->toll_type==1?'Vé thu phí':($row->toll_type==1?'Thu tiền bến bãi':'Cước đường bộ') )

                        ->setCellValue('H' . $hang, round($row->toll_cost/1.1))

                        ->setCellValue('I' . $hang, '10%')
                        ->setCellValue('J' . $hang, '=I'.$hang.'*H'.$hang)
                        ->setCellValue('K' . $hang, '=H'.$hang.'+J'.$hang);

                     $hang++;

                     if ($arr[$row->vehicle] == $v[$row->vehicle]) { 

                         $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValue('C' . $hang, 'XE '.$row->vehicle_number.' HÓA ĐƠN VAT')


                            ->setCellValue('H' . $hang, '=SUM(H'.$hangbatdau.':H'.($hang-1).')')

                            ->setCellValue('J' . $hang, '=SUM(J'.$hangbatdau.':J'.($hang-1).')')

                            ->setCellValue('K' . $hang, '=SUM(K'.$hangbatdau.':K'.($hang-1).')');

                        $objPHPExcel->getActiveSheet()->mergeCells('C'.$hang.':E'.$hang);
                        $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getFont()->setBold(true);

                        $sumthue .= '+H'.$hang;

                         $hang++;

                         $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValue('C' . $hang, 'XE '.$row->vehicle_number.' VÉ KHÔNG THUẾ')


                            ->setCellValue('H' . $hang, $other[$row->vehicle])

                            ->setCellValue('K' . $hang, $other[$row->vehicle]);

                        $objPHPExcel->getActiveSheet()->mergeCells('C'.$hang.':E'.$hang);
                        $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getFont()->setBold(true);

                        $sumkthue .= '+H'.$hang;

                         $hang++;

                         $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValue('C' . $hang, 'XE '.$row->vehicle_number.' TỔNG CỘNG')


                            ->setCellValue('H' . $hang, '=SUM(H'.($hang-2).':H'.($hang-1).')')

                            ->setCellValue('J' . $hang, '=SUM(J'.($hang-2).':J'.($hang-1).')')

                            ->setCellValue('K' . $hang, '=SUM(K'.($hang-2).':K'.($hang-1).')');

                        $objPHPExcel->getActiveSheet()->mergeCells('C'.$hang.':E'.$hang);
                        $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getFont()->setBold(true);

                         $hang++;


                         $objPHPExcel->setActiveSheetIndex(0)

                            ->setCellValue('A' . $hang, 'Xác nhận của tài xế xe '.$row->vehicle_number);

                        $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':K'.$hang);
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                        $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getFont()->setBold(true);

                        $hang++;
                    }


                  }



                   $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue('C' . $hang, 'TỔNG 29 XE HÓA ĐƠN VAT')


                        ->setCellValue('H' . $hang, $sumthue)

                        ->setCellValue('J' . $hang, '=H'.$hang.'*10%')

                        ->setCellValue('K' . $hang, '=H'.$hang.'+J'.$hang);

                    $objPHPExcel->getActiveSheet()->mergeCells('C'.$hang.':E'.$hang);
                    $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getFont()->setBold(true);

                     $hang++;

                     $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue('C' . $hang, 'TỔNG 29 XE VÉ KHÔNG THUẾ')


                        ->setCellValue('H' . $hang, $sumkthue)

                        ->setCellValue('K' . $hang, '=H'.$hang.'+J'.$hang);

                    $objPHPExcel->getActiveSheet()->mergeCells('C'.$hang.':E'.$hang);
                    $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getFont()->setBold(true);

                     $hang++;

                     $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue('C' . $hang, 'TỔNG CỘNG')


                        ->setCellValue('H' . $hang, '=SUM(H'.($hang-2).':H'.($hang-1).')')

                        ->setCellValue('J' . $hang, '=SUM(J'.($hang-2).':J'.($hang-1).')')

                        ->setCellValue('K' . $hang, '=SUM(K'.($hang-2).':K'.($hang-1).')');

                    $objPHPExcel->getActiveSheet()->mergeCells('C'.$hang.':E'.$hang);
                    $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('C'.$hang)->getFont()->setBold(true);

                    $hang++;

                    $objPHPExcel->getActiveSheet()->getStyle('A8:K'.($hang-1))->applyFromArray(

                        array(

                            

                            'borders' => array(

                                'allborders' => array(

                                  'style' => PHPExcel_Style_Border::BORDER_THIN

                                )

                            )

                        )

                    );


                    $objPHPExcel->setActiveSheetIndex(0)

                        ->setCellValue('A' . $hang, 'Bằng chữ: '.$this->lib->convert_number_to_words($tonggt+$tongthue+$tongkthue));

                    $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':K'.$hang);




                    



                  $objPHPExcel->getActiveSheet()->getStyle('K'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle('K'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);





                    $objPHPExcel->setActiveSheetIndex($index_worksheet)

                        ->setCellValue('A'.($hang+3), 'LẬP BIỂU')

                        ->setCellValue('E'.($hang+3), 'ĐỘI VẬN TẢI VÒNG NGOÀI')
                        ->setCellValue('H'.($hang+3), 'KẾ TOÁN TRƯỞNG');



                    $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+3).':C'.($hang+3));

                    $objPHPExcel->getActiveSheet()->mergeCells('E'.($hang+3).':F'.($hang+3));
                    $objPHPExcel->getActiveSheet()->mergeCells('H'.($hang+3).':K'.($hang+3));



                    $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':K'.($hang+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                    $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':K'.($hang+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



                    $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':K'.($hang+3))->applyFromArray(

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

            $objPHPExcel->getActiveSheet()->mergeCells('H1:J1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:D2');

            $objPHPExcel->getActiveSheet()->mergeCells('H2:J2');



            $objPHPExcel->getActiveSheet()->mergeCells('A6:K6');



            $objPHPExcel->getActiveSheet()->getStyle('A1:K8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:K8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A6")->getFont()->setSize(16);



            $objPHPExcel->getActiveSheet()->getStyle('A1:K8')->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('A2:J2')->applyFromArray(

                array(

                    

                    'font' => array(

                        'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':C'.$hang);



            



            $objPHPExcel->getActiveSheet()->getStyle('H2:K'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");


            $objPHPExcel->getActiveSheet()->getStyle('A1:J8')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:J8')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:J8')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(16);

            $objPHPExcel->getActiveSheet()->getColumnDimension('1')->setAutoSize(true);



            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Report")

                            ->setSubject("Report")

                            ->setDescription("Report.")

                            ->setKeywords("Report")

                            ->setCategory("Report");

            $objPHPExcel->getActiveSheet()->setTitle("Cau duong");



            $objPHPExcel->getActiveSheet()->freezePane('A9');

            $objPHPExcel->setActiveSheetIndex(0);







            



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BANG KE PHI CAU DUONG.xlsx");

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
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES['import']['name'] != null) {

            require("lib/Classes/PHPExcel/IOFactory.php");
            require("lib/Classes/PHPExcel.php");

            $toll = $this->model->get('tollModel');

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

                for ($row = 3; $row <= $highestRow; ++ $row) {
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
                    if ($val[5] != null && $val[7] != null) {

                        $type = trim($val[7])=="Vé thu phí"?1:(trim($val[7])=="Thu Tiền Bến Bãi"?3:2);
                            
                            if(!$toll->getTollByWhere(array('toll_name'=>trim($val[5]),'toll_type'=>$type))) {
                                $data = array(
                                'toll_name' => trim($val[5]),
                                'toll_mst' => trim($val[6]),
                                'toll_type' => $type,
                                'toll_symbol' => trim($val[1]),
                                );
                                $toll->createToll($data);
                            }
                            else if($toll->getTollByWhere(array('toll_name'=>trim($val[5]),'toll_type'=>$type))) {
                                $id = $toll->getTollByWhere(array('toll_name'=>trim($val[5]),'toll_type'=>$type))->toll_id;
                                $data = array(
                                'toll_name' => trim($val[5]),
                                'toll_mst' => trim($val[6]),
                                'toll_type' => $type,
                                'toll_symbol' => trim($val[1]),
                                );
                                $toll->updateToll($data,array('toll_id'=>$id));
                            }

                    }
                    
                    //var_dump($this->getNameDistrict($this->lib->stripUnicode($val[1])));
                    // insert


                }
                //return $this->view->redirect('transport');
            
            return $this->view->redirect('tollcost');
        }
        $this->view->show('tollcost/import');

    }

    

}
?>