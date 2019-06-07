<?php



Class loanlistController Extends baseController {



    public function index() {



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->loanlist) || json_decode($_SESSION['user_permission_action'])->loanlist != "loanlist") {

            $this->view->data['disable_control'] = 1;

        }



        $this->view->data['lib'] = $this->lib;



        $this->view->data['title'] = 'Bảng kê chi hộ';







        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;



            $order = isset($_POST['order']) ? $_POST['order'] : null;



            $page = isset($_POST['page']) ? $_POST['page'] : null;



            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;



            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;



            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;



            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;



            $kh = isset($_POST['nv']) ? $_POST['nv'] : null;



            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;



            $trangthai = isset($_POST['staff']) ? $_POST['staff'] : null;



            



        }



        else{



            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'loan_list_date';



            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';



            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;



            $keyword = "";



            $limit = 50;



            $batdau = '01-'.date('m-Y');



            $ketthuc = date('t-m-Y');



            $kh = 0;



            $vong = (int)date('m',strtotime($batdau));



            $trangthai = date('Y',strtotime($batdau));



            



        }





        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));



        $vong = (int)date('m',strtotime($batdau));



        $trangthai = date('Y',strtotime($batdau));






        $customer_model = $this->model->get('customerModel');



        $customers = $customer_model->getAllCustomer();



        $this->view->data['customers'] = $customers;



        $join = array('table'=>'customer','where'=>'customer = customer_id');



        $loan_list_model = $this->model->get('loanlistModel');



        $sonews = $limit;



        $x = ($page-1) * $sonews;



        $pagination_stages = 2;







        $data = array(



            'where' => "1=1",



            );



        if($batdau != "" && $ketthuc != "" ){



            $data['where'] = $data['where'].' AND loan_list_date >= '.strtotime($batdau).' AND loan_list_date < '.strtotime($ngayketthuc);



        }




        if($kh > 0){



            $data['where'] = $data['where'].' AND customer = '.$kh;



        }







        $tongsodong = count($loan_list_model->getAllShipment($data,$join));



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




        $this->view->data['kh'] = $kh;







        $this->view->data['limit'] = $limit;











        $data = array(



            'order_by'=>$order_by,



            'order'=>$order,



            'limit'=>$x.','.$sonews,



            'where' => "1=1",



            );



        if($batdau != "" && $ketthuc != "" ){



            $data['where'] = $data['where'].' AND loan_list_date >= '.strtotime($batdau).' AND loan_list_date < '.strtotime($ngayketthuc);



        }



        if($kh > 0){



            $data['where'] = $data['where'].' AND customer = '.$kh;



        }



        





        if ($keyword != '') {



            $ngay = (strtotime(str_replace("/", "-", $keyword))!="")?(' OR loan_list_date LIKE "%'.strtotime(str_replace("/", "-", $keyword)).'%"'):"";



            $search = '(



                    loan_list_number LIKE "%'.$keyword.'%"



                    OR customer_name LIKE "%'.$keyword.'%"



                    '.$ngay.'



                        )';



            $data['where'] = $data['where']." AND ".$search;



        }







        $loan_lists = $loan_list_model->getAllShipment($data,$join);



        

        $this->view->data['loan_lists'] = $loan_lists;





        $this->view->data['lastID'] = isset($loan_list_model->getLastShipment()->loan_list_id)?$loan_list_model->getLastShipment()->loan_list_id:0;





        $this->view->show('loanlist/index');



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



                echo '<li onclick="set_item_customer(\''.$rs->customer_id.'\',\''.$rs->customer_name.'\',\''.$rs->customer_mst.'\',\''.$rs->customer_address.'\',\''.$rs->customer_sub.'\')">'.$customer_name.'</li>';



            }



        }



    }

    public function getSub(){

        header('Content-type: application/json');

        $q = $_GET["search"];



        $sub_model = $this->model->get('customersubModel');

        $data = array(

            'where' => 'customer_sub_name LIKE "%'.$q.'%"',

        );

        $subs = $sub_model->getAllCustomer($data);

        $arr = array();

        foreach ($subs as $sub) {

            $arr[] = $sub->customer_sub_name;

        }

        

        echo json_encode($arr);

    }

    public function getcustomersub(){

        $sub_model = $this->model->get('customersubModel');

        $data = array(

            'where' => 'customer_sub_id IN ('.$_POST['sub'].')',

        );

        $subs = $sub_model->getAllCustomer($data);

        $str = "";

        foreach ($subs as $sub) {

            $str .= '<option title="'.$sub->customer_sub_name.'" value="'.$sub->customer_sub_id.'">'.$sub->customer_sub_name.'</option>';

        }

        

        echo $str;

    }

    public function getshipment(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $id = trim($_POST['id']);
            $id = $id>0?$id:0;

            $customer = trim($_POST['customer']);
            $batdau = trim($_POST['start_time']);
            $ketthuc = trim($_POST['end_time']);
            $giobatdau = trim($_POST['start_work']);
            $gioketthuc = trim($_POST['end_work']);
            $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));
            $customer_type = $_POST['customer_type'];

            $and = "";

            if($customer_type != "null"){
                foreach ($customer_type as $key) {
                    if($and == "")
                        $and .= ' AND ( (customer_type LIKE "'.$key.'" OR customer_type LIKE "'.$key.',%" OR customer_type LIKE "%,'.$key.',%" OR customer_type LIKE "%,'.$key.'")';
                    else
                        $and .= ' OR (customer_type LIKE "'.$key.'" OR customer_type LIKE "'.$key.',%" OR customer_type LIKE "%,'.$key.',%" OR customer_type LIKE "%,'.$key.'")';
                }
                $and .= ' )';
            }



            $shipment_model = $this->model->get('shipmentcostModel');

            $place_model = $this->model->get('placeModel');

            $loan_list_model = $this->model->get('loanlistModel');

            $customer_sub_model = $this->model->get('customersubModel');

            $export_stock_model = $this->model->get('exportstockModel');

            $customer_types = array();



            $place_data = array();



            $places = $place_model->getAllPlace();





            foreach ($places as $place) {



                    $place_data['place_id'][$place->place_id] = $place->place_id;



                    $place_data['place_name'][$place->place_id] = $place->place_name;



            }



            $join = array('table'=>'cost_list,vehicle,cont_unit,shipment,customer','where'=>'cost_list_id=cost_list AND shipment.cont_unit=cont_unit_id AND shipment.customer=customer_id AND shipment_cost.shipment=shipment_id AND shipment.vehicle=vehicle_id');

            $data = array(

                'where'=>'cost_list_type = 8 AND customer = '.$customer.' AND bill_out >= '.strtotime($batdau.' '.$giobatdau).' AND bill_out <= '.strtotime($ketthuc.' '.$gioketthuc).$and,

                'order_by'=>'bill_delivery_date ASC',

                );

            $shipments = $shipment_model->getAllShipment($data,$join);



            $str = '<table class="table_data" id="tblExport2">';
            $str .= '<thead><tr><th class="fix"><input checked type="checkbox" onclick="checkall(\'checkbox\', this)" name="checkall"/></th><th class="fix">STT</th><th class="fix">Ngày giao</th><th class="fix">Giờ ra</th><th class="fix">Số DO</th><th class="fix">Xe</th><th class="fix">Kho đi</th><th class="fix">Kho đến</th><th class="fix">Mặt hàng</th><th class="fix">Sản lượng nhận</th><th class="fix">Sản lượng giao</th><th class="fix">ĐVT</th><th class="fix">Nội dung chi</th><th class="fix">Số tiền</th></tr></thead>';
            $str .= '<tbody>';

            $i = 1; $tongtien = 0; $tongnhan=0; $tonggiao=0;
            foreach ($shipments as $shipment) {
                $customer_sub = "";

                $sts = explode(',', $shipment->customer_type);

                foreach ($sts as $key) {

                    $subs = $customer_sub_model->getCustomer($key);

                    if ($subs) {

                        if ($customer_sub == "")

                            $customer_sub .= $subs->customer_sub_name;

                        else

                            $customer_sub .= ','.$subs->customer_sub_name;

                    }

                    

                }


                $loan_lists = $loan_list_model->queryShipment('SELECT shipment_cost FROM loan_list WHERE shipment_cost LIKE "'.$shipment->shipment_cost_id.'" OR shipment_cost LIKE "'.$shipment->shipment_cost_id.',%" OR shipment_cost LIKE "%,'.$shipment->shipment_cost_id.',%" OR shipment_cost LIKE "%,'.$shipment->shipment_cost_id.'"');

                $loan_list_adds = $loan_list_model->queryShipment('SELECT shipment_cost FROM loan_list WHERE loan_list_id = '.$id.' AND (shipment_cost LIKE "'.$shipment->shipment_cost_id.'" OR shipment_cost LIKE "'.$shipment->shipment_cost_id.',%" OR shipment_cost LIKE "%,'.$shipment->shipment_cost_id.',%" OR shipment_cost LIKE "%,'.$shipment->shipment_cost_id.'")');



                if (!$loan_lists || $loan_list_adds) {
                    $tien = $shipment->cost;
                    $tongtien += $tien;
                    
                    $str .= '<tr class="tr" data="'.$shipment->shipment_id.'"><td><input checked name="check_i[]" type="checkbox" class="checkbox" value="'.$shipment->shipment_cost_id.'" data="'.$tien.'" ></td><td class="fix">'.$i++.'</td><td class="fix">'.$this->lib->hien_thi_ngay_thang($shipment->bill_delivery_date).'</td><td class="fix">'.date('H:i:s',$shipment->bill_out).'</td><td class="fix">'.$shipment->bill_number.'</td><td class="fix">'.$shipment->vehicle_number.'</td><td class="fix">'.$place_data['place_name'][$shipment->shipment_from].'</td><td class="fix">'.$place_data['place_name'][$shipment->shipment_to].'</td><td class="fix">'.$customer_sub.'</td><td class="fix">'.$shipment->bill_receive_ton.'</td><td class="fix">'.$shipment->bill_delivery_ton.'</td><td class="fix">'.$shipment->cont_unit_name.'</td><td class="fix">'.$shipment->comment.'</td><td class="fix">'.$this->lib->formatMoney($tien).'</td></tr>';
                }

                

            }

            $str .= '<tr style="font-weight:bold"><td colspan="11">Tổng cộng</td><td class="fix"></td><td class="fix"></td><td class="fix">'.$this->lib->formatMoney($tongtien).'</td></tr>';

            $str .= '</tbody></table>';

            echo $str;



        }



    }

    public function getshipmentadd(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $customer_model = $this->model->get('customerModel');

            $sub_model = $this->model->get('customersubModel');

            $customers = $customer_model->getCustomer($_POST['customer']);

            $data = array(

                'where' => 'customer_sub_id IN ('.$_POST['customer_type'].')',

            );

            $subs = $sub_model->getAllCustomer($data);

            $str = "";

            foreach ($subs as $sub) {

                $str .= '<option selected title="'.$sub->customer_sub_name.'" value="'.$sub->customer_sub_id.'">'.$sub->customer_sub_name.'</option>';

            }

            $data = array(

                'where' => 'customer_sub_id NOT IN ('.$_POST['customer_type'].') AND customer_sub_id IN ('.$customers->customer_sub.')',

            );

            $subs = $sub_model->getAllCustomer($data);

            foreach ($subs as $sub) {

                $str .= '<option title="'.$sub->customer_sub_name.'" value="'.$sub->customer_sub_id.'">'.$sub->customer_sub_name.'</option>';

            }

            

            echo $str;



        }



    }

    public function add(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->loanlist) || json_decode($_SESSION['user_permission_action'])->loanlist != "loanlist") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $loan_list_model = $this->model->get('loanlistModel');
            $debit = $this->model->get('debitModel');


            $data = array(

                        'loan_list_number' => trim($_POST['loan_list_number']),

                        'loan_list_date' => strtotime($_POST['loan_list_date']),

                        'customer' => trim($_POST['customer']),

                        'loan_list_price' => trim(str_replace(',','',$_POST['loan_list_price'])),

                        'shipment_cost' => trim($_POST['shipment_cost']),

                        'start_time' => strtotime($_POST['start_time']),

                        'end_time' => strtotime($_POST['end_time']),

                        'start_work' => strtotime(trim($_POST['start_time']).' '.trim($_POST['start_work'])),

                        'end_work' => strtotime(trim($_POST['end_time']).' '.trim($_POST['end_work'])),

                        );



            $contributor = "";

            if(is_array($_POST['customer_type'])){

                foreach ($_POST['customer_type'] as $key) {

                    if ($contributor == "")

                        $contributor .= $key;

                    else

                        $contributor .= ','.$key;

                }
            }

            $data['customer_type'] = $contributor;





            if ($_POST['yes'] != "") {

                if ($loan_list_model->checkShipment($_POST['yes'].' AND loan_list_number = "'.trim($_POST['loan_list_number']).'"')) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $loan_list_model->updateShipment($data,array('loan_list_id' => $_POST['yes']));

                    $id_loan_list = $_POST['yes'];

                    /*Log*/

                    /**/
                    $data_debit = array(

                        'debit_date'=>$data['loan_list_date'],

                        'money'=>$data['loan_list_price'],

                        'comment'=>'Chi hộ - '.$data['loan_list_number'],

                        'check_debit'=>1,

                        'check_loan'=>1,

                        'loan'=>$id_loan_list,

                    );

                    $debit->updateDebit($data_debit,array('loan'=>$id_loan_list,'check_debit'=>1));

                    $data_debit = array(

                        'debit_date'=>$data['loan_list_date'],

                        'money'=>$data['loan_list_price'],

                        'comment'=>'Chi hộ - '.$data['loan_list_number'],

                        'check_debit'=>2,

                        'check_loan'=>1,

                        'loan'=>$id_loan_list,

                    );

                    $debit->updateDebit($data_debit,array('loan'=>$id_loan_list,'check_debit'=>2));
                    

                    echo "Cập nhật thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|loan_list|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    }

                

                

            }

            else{



                if ($loan_list_model->getShipmentByWhere(array('loan_list_number'=>$data['loan_list_number']))) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $loan_list_model->createShipment($data);

                    $id_loan_list = $loan_list_model->getLastShipment()->loan_list_id;

                    /*Log*/

                    /**/
                    $data_debit = array(

                        'debit_date'=>$data['loan_list_date'],

                        'money'=>$data['loan_list_price'],

                        'comment'=>'Chi hộ - '.$data['loan_list_number'],

                        'check_debit'=>1,

                        'check_loan'=>1,

                        'loan'=>$id_loan_list,

                    );

                    $debit->createDebit($data_debit);

                    $data_debit = array(

                        'debit_date'=>$data['loan_list_date'],

                        'money'=>$data['loan_list_price'],

                        'comment'=>'Chi hộ - '.$data['loan_list_number'],

                        'check_debit'=>2,

                        'check_loan'=>1,

                        'loan'=>$id_loan_list,

                    );

                    $debit->createDebit($data_debit);


                    echo "Thêm thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$loan_list_model->getLastShipment()->loan_list_id."|loan_list|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    }

                

                

            }


            /*$loan_list_price = 0;


            $arr = explode(',', $data['shipment']);



            foreach ($arr as $key) {

                $d = $shipment_model->getShipment($key);

                $loan_list_price += $d->shipment_ton*$d->shipment_charge;

            }


            $loan_list_model->updateShipment(array('loan_list_price'=>$loan_list_price),array('loan_list_id' => $id_loan_list));*/
            

                    

        }

    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->loanlist) || json_decode($_SESSION['user_permission_action'])->loanlist != "loanlist") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $loan_list_model = $this->model->get('loanlistModel');
            $debit = $this->model->get('debitModel');

            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {

                    $debit->queryDebit('DELETE FROM debit WHERE loan = '.$data);

                    $loan_list_model->deleteShipment($data);

                    

                    

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|loan_list|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }



                /*Log*/

                    /**/



                return true;

            }

            else{

                $debit->queryDebit('DELETE FROM debit WHERE loan = '.$_POST['data']);
                /*Log*/

                    /**/

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|loan_list|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $loan_list_model->deleteShipment($_POST['data']);

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







        $loan_lists = $shipment_model->getAllShipment($data,$join);







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