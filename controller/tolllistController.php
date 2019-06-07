<?php



Class tolllistController Extends baseController {



    public function index() {



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->tolllist) || json_decode($_SESSION['user_permission_action'])->tolllist != "tolllist") {

            $this->view->data['disable_control'] = 1;

        }



        $this->view->data['lib'] = $this->lib;



        $this->view->data['title'] = 'Bảng kê phiếu cầu đường';







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



            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'toll_list_date';



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






        $steersman_model = $this->model->get('steersmanModel');



        $steersmans = $steersman_model->getAllSteersman();



        $this->view->data['steersmans'] = $steersmans;



        $join = array('table'=>'steersman','where'=>'steersman = steersman_id');



        $toll_list_model = $this->model->get('tolllistModel');



        $sonews = $limit;



        $x = ($page-1) * $sonews;



        $pagination_stages = 2;







        $data = array(



            'where' => "1=1",



            );



        if($batdau != "" && $ketthuc != "" ){



            $data['where'] = $data['where'].' AND toll_list_date >= '.strtotime($batdau).' AND toll_list_date < '.strtotime($ngayketthuc);



        }




        if($kh > 0){



            $data['where'] = $data['where'].' AND steersman = '.$kh;



        }







        $tongsodong = count($toll_list_model->getAllToll($data,$join));



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



            $data['where'] = $data['where'].' AND toll_list_date >= '.strtotime($batdau).' AND toll_list_date < '.strtotime($ngayketthuc);



        }



        if($kh > 0){



            $data['where'] = $data['where'].' AND customer = '.$kh;



        }



        





        if ($keyword != '') {



            $ngay = (strtotime(str_replace("/", "-", $keyword))!="")?(' OR toll_list_date LIKE "%'.strtotime(str_replace("/", "-", $keyword)).'%"'):"";



            $search = '(



                    toll_list_number LIKE "%'.$keyword.'%"



                    OR steersman_name LIKE "%'.$keyword.'%"



                    '.$ngay.'



                        )';



            $data['where'] = $data['where']." AND ".$search;



        }







        $toll_lists = $toll_list_model->getAllToll($data,$join);



        

        $this->view->data['toll_lists'] = $toll_lists;





        $this->view->data['lastID'] = isset($toll_list_model->getLastToll()->toll_list_id)?$toll_list_model->getLastToll()->toll_list_id:0;





        $this->view->show('tolllist/index');



    }



    public function getsteersman(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $steersman_model = $this->model->get('steersmanModel');



            



            if ($_POST['keyword'] == "*") {







                $list = $steersman_model->getAllSteersman();



            }



            else{



                $data = array(



                'where'=>'( steersman_name LIKE "%'.$_POST['keyword'].'%" )',



                );



                $list = $steersman_model->getAllSteersman($data);



            }



            



            foreach ($list as $rs) {



                // put in bold the written text



                $steersman_name = $rs->steersman_name;



                if ($_POST['keyword'] != "*") {



                    $steersman_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->steersman_name);



                }



                



                // add new option



                echo '<li onclick="set_item_steersman(\''.$rs->steersman_id.'\',\''.$rs->steersman_name.'\')">'.$steersman_name.'</li>';



            }



        }



    }


    public function gettoll(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }





        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $id = trim($_POST['id']);
            $id = $id>0?$id:0;

            $steersman = trim($_POST['steersman']);
            $batdau = trim($_POST['start_time']);
            $ketthuc = trim($_POST['end_time']);
            $giobatdau = trim($_POST['start_work']);
            $gioketthuc = trim($_POST['end_work']);
            $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

            $and = "";


            $toll_model = $this->model->get('tollcostModel');

            $toll_list_model = $this->model->get('tolllistModel');


            $join = array('table'=>'vehicle,shipment,steersman,toll','where'=>'toll_cost.shipment=shipment_id AND shipment.vehicle=vehicle_id AND shipment.steersman=steersman_id AND toll=toll_id');

            $data = array(

                'where'=>'steersman = '.$steersman.' AND bill_out >= '.strtotime($batdau.' '.$giobatdau).' AND bill_out <= '.strtotime($ketthuc.' '.$gioketthuc).$and,

                'order_by'=>'bill_delivery_date ASC',

                );

            $tolls = $toll_model->getAllToll($data,$join);



            $str = '<table class="table_data" id="tblExport2">';
            $str .= '<thead><tr><th class="fix"><input checked type="checkbox" onclick="checkall(\'checkbox\', this)" name="checkall"/></th><th class="fix">STT</th><th class="fix">Xe</th><th class="fix">Mẫu số</th><th class="fix">Ký hiệu</th><th class="fix">Số HĐ</th><th class="fix">Người bán</th><th class="fix">MST</th><th class="fix">Số tiền</th></tr></thead>';
            $str .= '<tbody>';

            $i = 1; $tongtien = 0; $tongnhan=0; $tonggiao=0;
            foreach ($tolls as $toll) {

                $toll_lists = $toll_list_model->queryToll('SELECT toll_cost FROM toll_list WHERE toll_cost LIKE "'.$toll->toll_cost_id.'" OR toll_cost LIKE "'.$toll->toll_cost_id.',%" OR toll_cost LIKE "%,'.$toll->toll_cost_id.',%" OR toll_cost LIKE "%,'.$toll->toll_cost_id.'"');

                $toll_list_adds = $toll_list_model->queryToll('SELECT toll_cost FROM toll_list WHERE toll_list_id = '.$id.' AND (toll_cost LIKE "'.$toll->toll_cost_id.'" OR toll_cost LIKE "'.$toll->toll_cost_id.',%" OR toll_cost LIKE "%,'.$toll->toll_cost_id.',%" OR toll_cost LIKE "%,'.$toll->toll_cost_id.'")');



                if (!$toll_lists || $toll_list_adds) {
                    $tien = $toll->toll_cost+$toll->toll_cost_vat;
                    $tongtien += $tien;
                    
                    $str .= '<tr class="tr" data="'.$toll->shipment_id.'"><td><input checked name="check_i[]" type="checkbox" class="checkbox" value="'.$toll->toll_cost_id.'" data="'.$tien.'" ></td><td class="fix">'.$i++.'</td><td class="fix">'.$toll->vehicle_number.'</td><td class="fix">'.$toll->toll_symbol.'</td><td class="fix">'.$toll->toll_number.'</td><td class="fix">'.$toll->invoice_number.'</td><td class="fix">'.$toll->toll_name.'</td><td class="fix">'.$toll->toll_mst.'</td><td class="fix">'.$this->lib->formatMoney($tien).'</td></tr>';
                }

                

            }

            $str .= '<tr style="font-weight:bold"><td colspan="8">Tổng cộng</td><td class="fix">'.$this->lib->formatMoney($tongtien).'</td></tr>';

            $str .= '</tbody></table>';

            echo $str;



        }



    }


    public function add(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->tolllist) || json_decode($_SESSION['user_permission_action'])->tolllist != "tolllist") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $toll_list_model = $this->model->get('tolllistModel');
            $debit = $this->model->get('debitModel');


            $data = array(

                        'toll_list_number' => trim($_POST['toll_list_number']),

                        'toll_list_date' => strtotime($_POST['toll_list_date']),

                        'steersman' => trim($_POST['steersman']),

                        'toll_list_price' => trim(str_replace(',','',$_POST['toll_list_price'])),

                        'toll_cost' => trim($_POST['toll_cost']),

                        'start_time' => strtotime($_POST['start_time']),

                        'end_time' => strtotime($_POST['end_time']),

                        'start_work' => strtotime(trim($_POST['start_time']).' '.trim($_POST['start_work'])),

                        'end_work' => strtotime(trim($_POST['end_time']).' '.trim($_POST['end_work'])),

                        );



            if ($_POST['yes'] != "") {

                if ($toll_list_model->checkToll($_POST['yes'].' AND toll_list_number = "'.trim($_POST['toll_list_number']).'"')) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $toll_list_model->updateToll($data,array('toll_list_id' => $_POST['yes']));

                    $id_toll_list = $_POST['yes'];

                    /*Log*/

                    /**/
                    $data_debit = array(

                        'debit_date'=>$data['toll_list_date'],

                        'money'=>$data['toll_list_price'],

                        'comment'=>'Phí cầu đường - '.$data['toll_list_number'],

                        'check_debit'=>2,

                        'toll_list'=>$id_toll_list,

                    );

                    $debit->updateDebit($data_debit,array('toll_list'=>$id_toll_list,'check_debit'=>2));
                    

                    echo "Cập nhật thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|toll_list|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    }

                

                

            }

            else{



                if ($toll_list_model->getTollByWhere(array('toll_list_number'=>$data['toll_list_number']))) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $toll_list_model->createToll($data);

                    $id_toll_list = $toll_list_model->getLastToll()->toll_list_id;

                    /*Log*/

                    /**/
                    $data_debit = array(

                        'debit_date'=>$data['toll_list_date'],

                        'money'=>$data['toll_list_price'],

                        'comment'=>'Phí cầu đường - '.$data['toll_list_number'],

                        'check_debit'=>2,

                        'toll_list'=>$id_toll_list,

                    );

                    $debit->createDebit($data_debit);


                    echo "Thêm thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$toll_list_model->getLastToll()->toll_list_id."|toll_list|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    }

                

                

            }


            /*$toll_list_price = 0;


            $arr = explode(',', $data['shipment']);



            foreach ($arr as $key) {

                $d = $shipment_model->getShipment($key);

                $toll_list_price += $d->shipment_ton*$d->shipment_charge;

            }


            $toll_list_model->updateShipment(array('toll_list_price'=>$toll_list_price),array('toll_list_id' => $id_toll_list));*/
            

                    

        }

    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->tolllist) || json_decode($_SESSION['user_permission_action'])->tolllist != "tolllist") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $toll_list_model = $this->model->get('tolllistModel');
            $debit = $this->model->get('debitModel');

            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {

                    $debit->queryDebit('DELETE FROM debit WHERE toll_list = '.$data);

                    $toll_list_model->deleteToll($data);

                    

                    

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|toll_list|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }



                /*Log*/

                    /**/



                return true;

            }

            else{

                $debit->queryDebit('DELETE FROM debit WHERE toll_list = '.$_POST['data']);
                /*Log*/

                    /**/

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|toll_list|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $toll_list_model->deleteToll($_POST['data']);

            }

            

        }

    }




}



?>