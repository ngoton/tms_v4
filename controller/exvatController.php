<?php



Class exvatController Extends baseController {



    public function index() {



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->exvat) || json_decode($_SESSION['user_permission_action'])->exvat != "exvat") {

            $this->view->data['disable_control'] = 1;

        }



        $this->view->data['lib'] = $this->lib;



        $this->view->data['title'] = 'Hóa đơn vận chuyển';







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



            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'vat_date';



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



        $vat_model = $this->model->get('vatModel');



        $sonews = $limit;



        $x = ($page-1) * $sonews;



        $pagination_stages = 2;







        $data = array(



            'where' => "in_out = 2",



            );



        if($batdau != "" && $ketthuc != "" ){



            $data['where'] = $data['where'].' AND vat_date >= '.strtotime($batdau).' AND vat_date < '.strtotime($ngayketthuc);



        }




        if($kh > 0){



            $data['where'] = $data['where'].' AND customer = '.$kh;



        }







        $tongsodong = count($vat_model->getAllVAT($data,$join));



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



            'where' => "in_out = 2",



            );



        if($batdau != "" && $ketthuc != "" ){



            $data['where'] = $data['where'].' AND vat_date >= '.strtotime($batdau).' AND vat_date < '.strtotime($ngayketthuc);



        }



        if($kh > 0){



            $data['where'] = $data['where'].' AND customer = '.$kh;



        }



        





        if ($keyword != '') {



            $ngay = (strtotime(str_replace("/", "-", $keyword))!="")?(' OR vat_date LIKE "%'.strtotime(str_replace("/", "-", $keyword)).'%"'):"";



            $search = '(



                    vat_number LIKE "%'.$keyword.'%"



                    OR customer_name LIKE "%'.$keyword.'%"



                    '.$ngay.'



                        )';



            $data['where'] = $data['where']." AND ".$search;



        }







        $vats = $vat_model->getAllVAT($data,$join);



        

        $this->view->data['vats'] = $vats;





        $this->view->data['lastID'] = isset($vat_model->getLastVAT()->vat_id)?$vat_model->getLastVAT()->vat_id:0;





        $this->view->show('exvat/index');



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

    public function getshipment(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $shipment_model = $this->model->get('shipmentlistModel');

            $vat_model = $this->model->get('vatModel');



            $data = array(

                'where'=>'customer = '.trim($_POST['customer']),

                'order_by'=>'shipment_list_date ASC',

                );

            $shipments = $shipment_model->getAllShipment($data);



            $str = "";



            foreach ($shipments as $shipment) {

                $vat = $vat_model->queryVAT('SELECT shipment_list FROM vat WHERE shipment_list LIKE "'.$shipment->shipment_list_id.'" OR shipment_list LIKE "'.$shipment->shipment_list_id.',%" OR shipment_list LIKE "%,'.$shipment->shipment_list_id.',%" OR shipment_list LIKE "%,'.$shipment->shipment_list_id.'"');



                if (!$vat) {

                    $str .= '<option title="'.$shipment->shipment_list_price.'" value="'.$shipment->shipment_list_id.'">'.$this->lib->hien_thi_ngay_thang($shipment->shipment_list_date).': '.$shipment->shipment_list_number.' - '.$this->lib->formatMoney($shipment->shipment_list_price).'</option>';

                }

                

            }



            echo $str;



        }



    }

    public function getshipmentadd(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $shipment_model = $this->model->get('shipmentlistModel');

            $vat_model = $this->model->get('vatModel');

            $shipment_add = explode(',', trim($_POST['shipment_list']));

            $data = array(

                'where'=>'customer = '.trim($_POST['customer']),

                'order_by'=>'shipment_list_date ASC',

                );

            $shipments = $shipment_model->getAllShipment($data);



            $str = "";



            foreach ($shipments as $shipment) {

                $check = null;

                foreach ($shipment_add as $key) {

                    if ($shipment->shipment_list_id == $key) {

                        $check = "selected";

                        $str .= '<option '.$check.' title="'.$shipment->shipment_list_price.'" value="'.$shipment->shipment_list_id.'">'.$this->lib->hien_thi_ngay_thang($shipment->shipment_list_date).': '.$shipment->shipment_list_number.' - '.$this->lib->formatMoney($shipment->shipment_list_price).'</option>';

                        break;

                        

                    }

                }



                $vat = $vat_model->queryVAT('SELECT shipment_list FROM vat WHERE shipment_list LIKE "'.$shipment->shipment_list_id.'" OR shipment_list LIKE "'.$shipment->shipment_list_id.',%" OR shipment_list LIKE "%,'.$shipment->shipment_list_id.',%" OR shipment_list LIKE "%,'.$shipment->shipment_list_id.'"');



                if (!$vat) {

                    $str .= '<option title="'.$shipment->shipment_list_price.'" value="'.$shipment->shipment_list_id.'">'.$this->lib->hien_thi_ngay_thang($shipment->shipment_list_date).': '.$shipment->shipment_list_number.' - '.$this->lib->formatMoney($shipment->shipment_list_price).'</option>';

                }

                

            }



            echo $str;



        }



    }

    public function add(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->exvat) || json_decode($_SESSION['user_permission_action'])->exvat != "exvat") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $vat_model = $this->model->get('vatModel');



            $debit = $this->model->get('debitModel');



            $vat_out_model = $this->model->get('vatoutModel');



            /**************/



            $vat_out_list = $_POST['vat_out'];



            /**************/



            $data = array(

                        'in_out'=>2,

                        'vat_number' => trim($_POST['vat_number']),

                        'vat_date' => strtotime($_POST['vat_date']),

                        'customer' => trim($_POST['customer']),

                        'payment' => trim($_POST['payment']),

                        'vat_percent' => trim($_POST['vat_percent']),

                        );



            $contributor = "";

            if(is_array($_POST['shipment_list'])){

                foreach ($_POST['shipment_list'] as $key) {

                    if ($contributor == "")

                        $contributor .= $key;

                    else

                        $contributor .= ','.$key;

                }
            }

            $data['shipment_list'] = $contributor;





            if ($_POST['yes'] != "") {

                if ($vat_model->checkVAT($_POST['yes'].' AND vat_number = "'.trim($_POST['vat_number']).'"')) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $vat_model->updateVAT($data,array('vat_id' => $_POST['yes']));

                    $id_vat = $_POST['yes'];

                    /*Log*/

                    /**/

                    

                    echo "Cập nhật thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|vat|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    }

                

                

            }

            else{



                if ($vat_model->getVATByWhere(array('vat_number'=>$data['vat_number']))) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $vat_model->createVAT($data);

                    $id_vat = $vat_model->getLastVAT()->vat_id;

                    /*Log*/

                    /**/



                    echo "Thêm thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$vat_model->getLastVAT()->vat_id."|vat|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    }

                

                

            }


            $vat_sum = 0;
            $vat_price = 0;
            $vat_comment = "";


            foreach ($vat_out_list as $v) {



                if (isset($v['vat_out_id']) && $v['vat_out_id'] > 0) {



                    $data_vat = array(

                        'vat'=>$id_vat,

                        'vat_out_comment'=>trim($v['vat_out_comment']),

                        'vat_out_unit'=>trim($v['vat_out_unit']),

                        'vat_out_number'=>trim($v['vat_out_number']),

                        'vat_out_price'=>trim(str_replace(',','',$v['vat_out_price'])),

                    );



                    $vat_out_model->updateVAT($data_vat,array('vat_out_id'=>$v['vat_out_id']));



                }

                else{

                    $data_vat = array(

                        'vat'=>$id_vat,

                        'vat_out_comment'=>trim($v['vat_out_comment']),

                        'vat_out_unit'=>trim($v['vat_out_unit']),

                        'vat_out_number'=>trim($v['vat_out_number']),

                        'vat_out_price'=>trim(str_replace(',','',$v['vat_out_price'])),

                    );

                    $vat_out_model->createVAT($data_vat);

                }


                $vat_sum += $data_vat['vat_out_number']*$data_vat['vat_out_price'];
                $vat_price += round(($data_vat['vat_out_number']*$data_vat['vat_out_price'])*($data['vat_percent']/100));
                $vat_comment .= $data_vat['vat_out_comment'].'.';  

            }

            $vat_model->updateVAT(array('vat_sum'=>$vat_sum,'vat_price'=>$vat_price),array('vat_id' => $id_vat));

            if (!$debit->getDebitByWhere(array('vat'=>$id_vat))) {
                $data_debit = array(
                    'debit_date' => $data['vat_date'],
                    'customer' => $data['customer'],
                    'money' => $vat_sum,
                    'money_vat' => 1,
                    'money_vat_price' => $vat_price,
                    'comment' => $vat_comment.' (HD số: '.$data['vat_number'].')',
                    'check_debit' => 1,
                    'vat' => $id_vat,
                );
                $debit->createDebit($data_debit);
            }
            else{
                $data_debit = array(
                    'debit_date' => $data['vat_date'],
                    'customer' => $data['customer'],
                    'money' => $vat_sum,
                    'money_vat' => 1,
                    'money_vat_price' => $vat_price,
                    'comment' => $vat_comment.' (HD số: '.$data['vat_number'].')',
                    'check_debit' => 1,
                    'vat' => $id_vat,
                );
                $debit->updateDebit($data_debit,array('vat'=>$id_vat));
            }

            /*$arr = explode(',', $data['shipment']);



            foreach ($arr as $key) {

                $d = $debit->getDebitByWhere(array('shipment'=>$key));

                $data_debit = array(
                    'comment'=>$d->comment.' (HD số: '.$data['vat_number'].')',
                    'money_vat_price'=>round($d->money*0.1),

                );

                $debit->updateDebit($data_debit,array('shipment'=>$key));

            }*/



            

                    

        }

    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->exvat) || json_decode($_SESSION['user_permission_action'])->exvat != "exvat") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $vat_model = $this->model->get('vatModel');

            $debit = $this->model->get('debitModel');

            $vat_out_model = $this->model->get('vatoutModel');



            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {



                    /*$vat = $vat_model->getVAT($data);



                    $arr = explode(',', $vat->shipment);



                    foreach ($arr as $key) {

                        $d = $debit->getDebitByWhere(array('shipment'=>$key));

                        $data_debit = array(

                            'money_vat_price'=>0,

                        );

                        $debit->updateDebit($data_debit,array('shipment'=>$key));

                    }*/

                    $debit->queryDebit('DELETE FROM debit WHERE vat = '.$data);

                    $vat_out_model->queryVAT('DELETE FROM vat_out WHERE vat = '.$data);



                    $vat_model->deleteVAT($data);

                    

                    

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|vat|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }



                /*Log*/

                    /**/



                return true;

            }

            else{



                /*$vat = $vat_model->getVAT($_POST['data']);



                $arr = explode(',', $vat->shipment);



                foreach ($arr as $key) {

                    $d = $debit->getDebitByWhere(array('shipment'=>$key));

                    $data_debit = array(

                        'money_vat_price'=>0,

                    );

                    $debit->updateDebit($data_debit,array('shipment'=>$key));

                }*/

                $debit->queryDebit('DELETE FROM debit WHERE vat = '.$_POST['data']);

                $vat_out_model->queryVAT('DELETE FROM vat_out WHERE vat = '.$_POST['data']);

                /*Log*/

                    /**/

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|vat|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $vat_model->deleteVAT($_POST['data']);

            }

            

        }

    }



    public function getout(){



        if(isset($_POST['vat'])){



            



            $vat_out_model = $this->model->get('vatoutModel');





            $join = array('table'=>'vat','where'=>'vat = vat_id');



            $data = array(



                'where' => 'vat = '.$_POST['vat'],



            );



            $vats = $vat_out_model->getAllVAT($data,$join);







            $str = "";



            if (!$vats) {



                $str .= '<tr class="'.$_POST['vat'].'">';



                $str .= '<td><input type="checkbox"  name="chk" ></td>';



                $str .= '<td><table style="width: 100%">';



                $str .= '<tr class="'.$_POST['vat'] .'">';



                $str .= '<td>Tên hàng hóa, dịch vụ <br> <input type="text" class="vat_out_comment" name="vat_out_comment[]" value="Cước vận chuyển" ></td>';

                $str .= '<td>Đơn vị tính <br> <input type="text" class="vat_out_unit" name="vat_out_unit[]" style="width:80px" ></td>';

                $str .= '<td>Số lượng <br> <input type="text" class="vat_out_number number" name="vat_out_number[]" style="width:80px" ></td>';

                $str .= '<td>Đơn giá <br> <input type="text" class="vat_out_price numbers" name="vat_out_number[]" style="width:120px" ></td>';

                $str .= '<td>Thành tiền <br> <input type="text" class="vat_out_total numbers" name="vat_out_total[]" style="width:120px" disabled ></td>';



                $str .= '</tr></table></td></tr>';



            }



            else{



                foreach ($vats as $v) {



                    $str .= '<tr class="'.$_POST['vat'].'">';



                    $str .= '<td><input type="checkbox" name="chk" data="'.$v->vat_out_id.'"  alt="'.$v->vat.'"  ></td>';



                    $str .= '<td><table style="width: 100%">';



                    $str .= '<tr class="'.$_POST['vat'] .'">';



                    $str .= '<td>Tên hàng hóa, dịch vụ <br> <input type="text" class="vat_out_comment" name="vat_out_comment[]"  data="'.$v->vat_out_id.'" value="'.$v->vat_out_comment.'" ></td>';

                    $str .= '<td>Đơn vị tính <br> <input type="text" class="vat_out_unit" name="vat_out_unit[]" style="width:80px" value="'.$v->vat_out_unit.'" ></td>';

                    $str .= '<td>Số lượng <br> <input type="text" class="vat_out_number number" name="vat_out_number[]" style="width:80px" value="'.$v->vat_out_number.'" ></td>';

                    $str .= '<td>Đơn giá <br> <input type="text" class="vat_out_price numbers" name="vat_out_number[]" style="width:120px" value="'.$this->lib->formatMoney($v->vat_out_price).'" ></td>';

                    $str .= '<td>Thành tiền <br> <input type="text" class="vat_out_total numbers" name="vat_out_total[]" style="width:120px" disabled value="'.$this->lib->formatMoney($v->vat_out_number*$v->vat_out_price).'" ></td>';



                    $str .= '</tr></table></td></tr>';



                }



            }







            echo $str;



        }



    }



    public function deletevat(){



        if (isset($_POST['vat_out'])) {



            $vat_out_model = $this->model->get('vatoutModel');

            $vat_out_model->queryVAT('DELETE FROM vat_out WHERE vat_out_id = '.$_POST['vat_out']);



            echo 'Đã xóa thành công';



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