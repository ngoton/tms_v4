<?php



Class insurancecostController Extends baseController {



    public function index() {



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->insurancecost) || json_decode($_SESSION['user_permission_action'])->insurancecost != "insurancecost") {

            $this->view->data['disable_control'] = 1;

        }



        $this->view->data['lib'] = $this->lib;



        $this->view->data['title'] = 'Phí bảo hiểm';







        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;



            $order = isset($_POST['order']) ? $_POST['order'] : null;



            $page = isset($_POST['page']) ? $_POST['page'] : null;



            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;



            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;



            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;



            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;



            $xe = isset($_POST['xe']) ? $_POST['xe'] : null;

            $mooc = isset($_POST['nv']) ? $_POST['nv'] : null;



            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;



            $trangthai = isset($_POST['staff']) ? $_POST['staff'] : null;



            



        }



        else{



            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'insurance_cost_date';



            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';



            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;



            $keyword = "";



            $limit = 50;



            $batdau = '01-'.date('m-Y');



            $ketthuc = date('t-m-Y');



            $xe = 0;

            $mooc = 0;



            $vong = (int)date('m',strtotime($batdau));



            $trangthai = date('Y',strtotime($batdau));



            



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

                $vehicle_data['vehicle_id'][$vehicle->vehicle_id] = $vehicle->vehicle_id;

                $vehicle_data['vehicle_number'][$vehicle->vehicle_id] = $vehicle->vehicle_number;

        }

        $this->view->data['vehicle_data'] = $vehicle_data;


        $romooc_model = $this->model->get('romoocModel');



        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));



        $this->view->data['romoocs'] = $romoocs;

        $romooc_data = array();
        foreach ($romoocs as $romooc) {

                $romooc_data['romooc_id'][$romooc->romooc_id] = $romooc->romooc_id;

                $romooc_data['romooc_number'][$romooc->romooc_id] = $romooc->romooc_number;

        }

        $this->view->data['romooc_data'] = $romooc_data;



        $join = array('table'=>'customer','where'=>'customer = customer_id');



        $insurance_cost_model = $this->model->get('insurancecostModel');



        $sonews = $limit;



        $x = ($page-1) * $sonews;



        $pagination_stages = 2;







        $data = array(



            'where' => 'insurance_cost_date >= '.strtotime($batdau).' AND insurance_cost_date < '.strtotime($ngayketthuc),



            );


        if (isset($id) && $id > 0) {
            $data['where'] = 'insurance_cost_id = '.$id;
        }

        if($xe > 0){



            $data['where'] = $data['where'].' AND (vehicle LIKE "'.$xe.'" OR vehicle LIKE "'.$xe.',%" OR vehicle LIKE "%,'.$xe.',%" OR vehicle LIKE "%,'.$xe.'")';



        }
        if($mooc > 0){



            $data['where'] = $data['where'].' AND (romooc LIKE "'.$mooc.'" OR romooc LIKE "'.$mooc.',%" OR romooc LIKE "%,'.$mooc.',%" OR romooc LIKE "%,'.$mooc.'")';



        }







        $tongsodong = count($insurance_cost_model->getAllCost($data,$join));



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
        $this->view->data['mooc'] = $mooc;







        $this->view->data['limit'] = $limit;











        $data = array(



            'order_by'=>$order_by,



            'order'=>$order,



            'limit'=>$x.','.$sonews,



            'where' => 'insurance_cost_date >= '.strtotime($batdau).' AND insurance_cost_date < '.strtotime($ngayketthuc),



            );

        if (isset($id) && $id > 0) {
            $data['where'] = 'insurance_cost_id = '.$id;
        }

        if($xe > 0){



            $data['where'] = $data['where'].' AND (vehicle LIKE "'.$xe.'" OR vehicle LIKE "'.$xe.',%" OR vehicle LIKE "%,'.$xe.',%" OR vehicle LIKE "%,'.$xe.'")';



        }
        if($mooc > 0){



            $data['where'] = $data['where'].' AND (romooc LIKE "'.$mooc.'" OR romooc LIKE "'.$mooc.',%" OR romooc LIKE "%,'.$mooc.',%" OR romooc LIKE "%,'.$mooc.'")';



        }


        





        if ($keyword != '') {



            $ngay = (strtotime(str_replace("/", "-", $keyword))!="")?(' OR insurance_cost_date LIKE "%'.strtotime(str_replace("/", "-", $keyword)).'%"'):"";



            $search = '(



                    insurance_cost_number LIKE "%'.$keyword.'%"

                    OR insurance_cost_comment LIKE "%'.$keyword.'%" 

                    OR customer_name LIKE "%'.$keyword.'%"



                    '.$ngay.'



                        )';



            $data['where'] = $data['where']." AND ".$search;



        }







        $insurance_costs = $insurance_cost_model->getAllCost($data,$join);



        

        $this->view->data['insurance_costs'] = $insurance_costs;





        $this->view->data['lastID'] = isset($insurance_cost_model->getLastCost()->insurance_cost_id)?$insurance_cost_model->getLastCost()->insurance_cost_id:0;





        $this->view->show('insurancecost/index');



    }



    public function getcustomer(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $customer_model = $this->model->get('customerModel');



            



            if ($_POST['keyword'] == "*") {







                $list = $customer_model->getAllCustomer();



            }



            else{



                $data = array(



                'where'=>'( customer_name LIKE "%'.$_POST['keyword'].'%" )',



                );



                $list = $customer_model->getAllCustomer($data);



            }



            



            foreach ($list as $rs) {



                // put in bold the written text



                $customer_name = $rs->customer_name;



                if ($_POST['keyword'] != "*") {



                    $customer_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->customer_name);



                }



                



                // add new option



                echo '<li onclick="set_item_customer(\''.$rs->customer_id.'\',\''.$rs->customer_name.'\',\''.$rs->customer_mst.'\',\''.$rs->customer_address.'\')">'.$customer_name.'</li>';



            }



        }



    }

    

    public function getvehicleadd(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $vehicle_model = $this->model->get('vehicleModel');


            $vehicles = $vehicle_model->getAllVehicle();


            $vehicle_add = explode(',', trim($_POST['vehicle']));


            $str = "";



            foreach ($vehicles as $vehicle) {

                $check = null;

                foreach ($vehicle_add as $key) {

                    if ($vehicle->vehicle_id == $key) {

                        $check = "selected";

                        break;

                    }

                }

                $str .= '<option title="'.$vehicle->vehicle_number.'" '.$check.' value="'.$vehicle->vehicle_id.'">'.$vehicle->vehicle_number.'</option>';

            }



            echo $str;



        }



    }

    public function getromoocadd(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $romooc_model = $this->model->get('romoocModel');


            $romoocs = $romooc_model->getAllVehicle();


            $romooc_add = explode(',', trim($_POST['romooc']));


            $str = "";



            foreach ($romoocs as $romooc) {

                $check = null;

                foreach ($romooc_add as $key) {

                    if ($romooc->romooc_id == $key) {

                        $check = "selected";

                        break;

                    }

                }

                $str .= '<option title="'.$romooc->romooc_number.'" '.$check.' value="'.$romooc->romooc_id.'">'.$romooc->romooc_number.'</option>';

            }



            echo $str;



        }



    }

    public function add(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->insurancecost) || json_decode($_SESSION['user_permission_action'])->insurancecost != "insurancecost") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $vat_model = $this->model->get('vatModel');



            $debit_model = $this->model->get('debitModel');



            $insurance_cost_model = $this->model->get('insurancecostModel');





            $data = array(

                        'insurance_cost_date'=>strtotime($_POST['insurance_cost_date']),

                        'insurance_cost_number' => trim($_POST['insurance_cost_number']),

                        'insurance_cost_vat' => trim(str_replace(',', '', $_POST['insurance_cost_vat'])),

                        'insurance_cost_price' => trim(str_replace(',', '', $_POST['insurance_cost_price'])),

                        'customer' => trim($_POST['customer']),

                        'insurance_cost_comment' => trim($_POST['insurance_cost_comment']),

                        'start_time'=>strtotime($_POST['start_time']),

                        'end_time'=>strtotime($_POST['end_time']),

                        );

            $total_number = 0;

            $contributor = "";

            if(is_array($_POST['vehicle'])){

                foreach ($_POST['vehicle'] as $key) {

                    $total_number++;

                    if ($contributor == "")

                        $contributor .= $key;

                    else

                        $contributor .= ','.$key;

                }
            }

            $data['vehicle'] = $contributor;


            $contributor = "";

            if(is_array($_POST['romooc'])){

                foreach ($_POST['romooc'] as $key) {

                    $total_number++;

                    if ($contributor == "")

                        $contributor .= $key;

                    else

                        $contributor .= ','.$key;

                }
            }

            $data['romooc'] = $contributor;


            $data['total_number'] = $total_number;


            if ($_POST['yes'] != "") {

                if ($insurance_cost_model->checkCost($_POST['yes'].' AND insurance_cost_number = "'.trim($_POST['insurance_cost_number']).'"')) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $insurance = $insurance_cost_model->getCost($_POST['yes']);

                    $insurance_cost_model->updateCost($data,array('insurance_cost_id' => $_POST['yes']));

                    $id_insurance_cost = $_POST['yes'];

                    /*Log*/

                    /**/

                    $data_debit = array(

                        'debit_date'=>$data['insurance_cost_date'],

                        'customer'=>$data['customer'],

                        'money'=>$data['insurance_cost_price'],

                        'money_vat_price'=>$data['insurance_cost_vat'],

                        'comment'=>$data['insurance_cost_comment'].' '.$data['insurance_cost_number'],

                        'check_debit'=>2,

                        'insurance_cost'=>$id_insurance_cost,

                    );

                    $debit_model->updateDebit($data_debit,array('insurance_cost'=>$id_insurance_cost));


                    if ($insurance->insurance_cost_vat > 0 && $data['insurance_cost_vat'] > 0) {
                        $data_vat = array(

                            'in_out'=>1,

                            'vat_number'=>$data['insurance_cost_number'],

                            'vat_date'=>$data['insurance_cost_date'],

                            'insurance_cost'=>$id_insurance_cost,

                            'vat_sum'=>$data['insurance_cost_price'],

                            'vat_price'=>$data['insurance_cost_vat'],

                        );

                        $vat_model->updateVAT($data_vat,array('insurance_cost'=>$id_insurance_cost));
                    }
                    else if (($insurance->insurance_cost_vat == 0 || $insurance->insurance_cost_vat == "") && $data['insurance_cost_vat'] > 0) {
                        $data_vat = array(

                            'in_out'=>1,

                            'vat_number'=>$data['insurance_cost_number'],

                            'vat_date'=>$data['insurance_cost_date'],

                            'insurance_cost'=>$id_insurance_cost,

                            'vat_sum'=>$data['insurance_cost_price'],

                            'vat_price'=>$data['insurance_cost_vat'],

                        );

                        $vat_model->createVAT($data_vat);
                    }
                    else if ($insurance->insurance_cost_vat > 0 && ($data['insurance_cost_vat'] == 0 || $data['insurance_cost_vat'] == "")) {
                        $vat_model->queryVAT('DELETE FROM vat WHERE insurance_cost = '.$id_insurance_cost);
                    }

                    

                    echo "Cập nhật thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|insurance_cost|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    }

                

                

            }

            else{



                if ($insurance_cost_model->getCostByWhere(array('insurance_cost_number'=>$data['insurance_cost_number']))) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $insurance_cost_model->createCost($data);

                    $id_insurance_cost = $insurance_cost_model->getLastCost()->insurance_cost_id;

                    /*Log*/

                    /**/

                    $data_debit = array(

                        'debit_date'=>$data['insurance_cost_date'],

                        'customer'=>$data['customer'],

                        'money'=>$data['insurance_cost_price'],

                        'money_vat_price'=>$data['insurance_cost_vat'],

                        'comment'=>$data['insurance_cost_comment'].' '.$data['insurance_cost_number'],

                        'check_debit'=>2,

                        'insurance_cost'=>$id_insurance_cost,

                    );

                    $debit_model->createDebit($data_debit);


                    if ($data['insurance_cost_vat'] > 0) {
                        $data_vat = array(

                            'in_out'=>1,

                            'vat_number'=>$data['insurance_cost_number'],

                            'vat_date'=>$data['insurance_cost_date'],

                            'insurance_cost'=>$id_insurance_cost,

                            'vat_sum'=>$data['insurance_cost_price'],

                            'vat_price'=>$data['insurance_cost_vat'],

                        );

                        $vat_model->createVAT($data_vat);
                    }
                    



                    echo "Thêm thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$insurance_cost_model->getLastCost()->insurance_cost_id."|insurance_cost|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    }

                

                

            }



                    

        }

    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->insurancecost) || json_decode($_SESSION['user_permission_action'])->insurancecost != "insurancecost") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $vat_model = $this->model->get('vatModel');

            $debit_model = $this->model->get('debitModel');

            $insurance_cost_model = $this->model->get('insurancecostModel');



            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {



                    $insurance_cost = $insurance_cost_model->getCost($data);


                    $vat_model->queryVAT('DELETE FROM vat WHERE insurance_cost = '.$data);

                    $debit_model->queryDebit('DELETE FROM debit WHERE insurance_cost = '.$data);


                    $insurance_cost_model->deleteCost($data);

                    

                    

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|insurance_cost|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }



                /*Log*/

                    /**/



                return true;

            }

            else{



                $insurance_cost = $insurance_cost_model->getCost($_POST['data']);


                $vat_model->queryVAT('DELETE FROM vat WHERE insurance_cost = '.$_POST['data']);

                $debit_model->queryDebit('DELETE FROM debit WHERE insurance_cost = '.$_POST['data']);

                /*Log*/

                    /**/

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|insurance_cost|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $insurance_cost_model->deleteCost($_POST['data']);

            }

            

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



        $kh = $this->registry->router->order;



        $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));



        $info_model = $this->model->get('infoModel');

        $infos = $info_model->getLastInfo();





        $shipment_model = $this->model->get('shipmentModel');



        $join = array('table'=>'customer, vehicle, cont_unit','where'=>'customer.customer_id = shipment.customer AND vehicle.vehicle_id = shipment.vehicle AND cont_unit = cont_unit_id');







        $data = array(



            'where' => "shipment_ton > 0",



            );



        if($batdau != "" && $ketthuc != "" ){



            $data['where'] = $data['where'].' AND shipment_date >= '.$batdau.' AND shipment_date < '.$ngayketthuc;



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







        











        $data['order_by'] = 'shipment_date';



        $data['order'] = 'ASC';







        $shipments = $shipment_model->getAllShipment($data,$join);



        



        $place_model = $this->model->get('placeModel');



        $place_data = array();





        $customer_sub_model = $this->model->get('customersubModel');

        $customer_types = array();









        $data['order_by'] = 'customer';



        $data['order'] = 'ASC';







        $shipment_lists = $shipment_model->getAllShipment($data,$join);







        $number_sheet = $shipment_model->queryShipment('SELECT COUNT(DISTINCT customer) AS customer FROM shipment WHERE shipment_date >= '.$batdau.' AND shipment_date < '.$ngayketthuc);







        







            require("lib/Classes/PHPExcel/IOFactory.php");



            require("lib/Classes/PHPExcel.php");







            $objPHPExcel = new PHPExcel();







            







            $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)



            $objPHPExcel->setActiveSheetIndex($index_worksheet)



                ->setCellValue('A1', mb_strtoupper($infos->info_company, "UTF-8"))



                ->setCellValue('A2', 'ĐỘI VẬN TẢI')



                ->setCellValue('H1', 'CỘNG HÒA XÃ CHỦ NGHĨA VIỆT NAM')



                ->setCellValue('H2', 'Độc lập - Tự do - Hạnh phúc')



                ->setCellValue('A4', 'BẢNG KÊ QUYẾT TOÁN SẢN LƯỢNG VÀ DOANH THU VẬN CHUYỂN HÀNG')



                ->setCellValue('A6', 'STT')



               ->setCellValue('B6', 'Ngày')



               ->setCellValue('C6', 'Xe')



               ->setCellValue('D6', 'Khách hàng')



               ->setCellValue('E6', 'Nhận hàng')



               ->setCellValue('F6', 'Giao hàng')



               ->setCellValue('G6', 'Loại hàng')



               ->setCellValue('H6', 'Sản lượng')



               ->setCellValue('I6', 'ĐVT')



               ->setCellValue('J6', 'Đơn giá')



               ->setCellValue('K6', 'Doanh thu khác')



               ->setCellValue('L6', 'Thành tiền')



               ->setCellValue('M6', 'Thuế VAT')



               ->setCellValue('N6', 'Tổng tiền');



               







            







            



            



            







            if ($shipments) {







                $hang = 7;



                $i=1;







                $kho_data = array();



                $k=0;

                foreach ($shipments as $row) {



                    $qr = "SELECT * FROM vehicle_work WHERE vehicle = ".$row->vehicle." AND start_work <= ".$row->shipment_date." AND end_work >= ".$row->shipment_date;

                    if ($shipment_model->queryShipment($qr)) {

                    

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



                         $objPHPExcel->setActiveSheetIndex(0)



                            ->setCellValue('A' . $hang, $i++)



                            ->setCellValueExplicit('B' . $hang, $this->lib->hien_thi_ngay_thang($row->shipment_date))



                            ->setCellValue('C' . $hang, $row->vehicle_number)



                            ->setCellValue('D' . $hang, $row->customer_name)



                            ->setCellValue('E' . $hang, $place_data['place_name'][$row->shipment_from])



                            ->setCellValue('F' . $hang, $place_data['place_name'][$row->shipment_to])



                            ->setCellValue('G' . $hang, $customer_types[$row->shipment_id])



                            ->setCellValue('H' . $hang, $row->shipment_ton)



                            ->setCellValue('I' . $hang, $row->cont_unit_name)



                            ->setCellValue('J' . $hang, $row->shipment_charge)



                            ->setCellValue('K' . $hang, $row->revenue_other+$row->shipment_charge_excess)



                            ->setCellValue('L' . $hang, '=(H'.$hang.'*J'.$hang.')+K'.$hang)



                            ->setCellValue('M' . $hang, '=L'.$hang.'*10%')



                            ->setCellValue('N' . $hang, '=M'.$hang.'+L'.$hang);



                         $hang++;







                        $tencongty = $row->customer_company;







                      }



                }



            }







            $check_customer = 0;







            $objPHPExcel->setActiveSheetIndex($index_worksheet)



                ->setCellValue('A'.$hang, 'TỔNG')





               ->setCellValue('L'.$hang, '=SUM(L7:L'.($hang-1).')')



               ->setCellValue('M'.$hang, '=SUM(M7:M'.($hang-1).')')



               ->setCellValue('N'.$hang, '=SUM(N7:N'.($hang-1).')');







            $objPHPExcel->getActiveSheet()->getStyle('A6:N'.$hang)->applyFromArray(



                array(



                    



                    'borders' => array(



                        'allborders' => array(



                          'style' => PHPExcel_Style_Border::BORDER_THIN



                        )



                    )



                )



            );











            $cell = $objPHPExcel->getActiveSheet()->getCellByColumnAndRow(13, $hang)->getCalculatedValue();



            $objPHPExcel->setActiveSheetIndex($index_worksheet)



                ->setCellValue('A'.($hang+1), 'Bằng chữ: '.$this->lib->convert_number_to_words(round($cell)).' đồng');







            $objPHPExcel->getActiveSheet()->mergeCells('A'.$hang.':J'.$hang);



            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+1).':N'.($hang+1));











            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);











            $objPHPExcel->setActiveSheetIndex($index_worksheet)



                ->setCellValue('A'.($hang+3), 'NGƯỜI LẬP BIỂU')



                ->setCellValue('E'.($hang+3), mb_strtoupper($infos->info_company, "UTF-8"))



               ->setCellValue('I'.($hang+3), mb_strtoupper($tencongty, "UTF-8"));







            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+3).':D'.($hang+3));



            $objPHPExcel->getActiveSheet()->mergeCells('E'.($hang+3).':H'.($hang+3));



            $objPHPExcel->getActiveSheet()->mergeCells('I'.($hang+3).':M'.($hang+3));







            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':M'.($hang+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':M'.($hang+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);







            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':N'.($hang+3))->applyFromArray(



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







            $objPHPExcel->getActiveSheet()->getStyle('A1:N4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A1:N4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);







            $objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(16);







            $objPHPExcel->getActiveSheet()->getStyle('A1:N4')->applyFromArray(



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







            $objPHPExcel->getActiveSheet()->getStyle('I7:N'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");



            $objPHPExcel->getActiveSheet()->getStyle('A6:N6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A6:N6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A6:N6')->getFont()->setBold(true);



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



            $objPHPExcel->getActiveSheet()->setTitle("Bang ke san luong");







            $objPHPExcel->getActiveSheet()->freezePane('A7');



            $objPHPExcel->setActiveSheetIndex(0);















            







            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');







            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");



            header("Content-Disposition: attachment; filename= BẢNG KÊ SẢN LƯỢNG.xlsx");



            header("Cache-Control: max-age=0");



            ob_clean();



            $objWriter->save("php://output");



        



    }











    







}



?>