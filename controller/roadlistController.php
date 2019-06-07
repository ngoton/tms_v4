<?php



Class roadlistController Extends baseController {



    public function index() {



        $this->view->setLayout('admin');



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if (!isset(json_decode($_SESSION['user_permission_action'])->roadlist) || json_decode($_SESSION['user_permission_action'])->roadlist != "roadlist") {

            $this->view->data['disable_control'] = 1;

        }



        $this->view->data['lib'] = $this->lib;



        $this->view->data['title'] = 'Bảng kê tiền đi đường';







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



            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'road_list_date';



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



        $road_list_model = $this->model->get('roadlistModel');



        $sonews = $limit;



        $x = ($page-1) * $sonews;



        $pagination_stages = 2;







        $data = array(



            'where' => "1=1",



            );



        if($batdau != "" && $ketthuc != "" ){



            $data['where'] = $data['where'].' AND road_list_date >= '.strtotime($batdau).' AND road_list_date < '.strtotime($ngayketthuc);



        }




        if($kh > 0){



            $data['where'] = $data['where'].' AND steersman = '.$kh;



        }







        $tongsodong = count($road_list_model->getAllShipment($data,$join));



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



            $data['where'] = $data['where'].' AND road_list_date >= '.strtotime($batdau).' AND road_list_date < '.strtotime($ngayketthuc);



        }



        if($kh > 0){



            $data['where'] = $data['where'].' AND customer = '.$kh;



        }



        





        if ($keyword != '') {



            $ngay = (strtotime(str_replace("/", "-", $keyword))!="")?(' OR road_list_date LIKE "%'.strtotime(str_replace("/", "-", $keyword)).'%"'):"";



            $search = '(



                    road_list_number LIKE "%'.$keyword.'%"



                    OR steersman_name LIKE "%'.$keyword.'%"



                    '.$ngay.'



                        )';



            $data['where'] = $data['where']." AND ".$search;



        }







        $road_lists = $road_list_model->getAllShipment($data,$join);



        

        $this->view->data['road_lists'] = $road_lists;





        $this->view->data['lastID'] = isset($road_list_model->getLastShipment()->road_list_id)?$road_list_model->getLastShipment()->road_list_id:0;





        $this->view->show('roadlist/index');



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


    public function getshipment(){



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


            $shipment_model = $this->model->get('shipmentModel');
            $road_list_model = $this->model->get('roadlistModel');
            $place_model = $this->model->get('placeModel');

            $place_data = array();

            $places = $place_model->getAllPlace();

            foreach ($places as $place) {

                    $place_data['place_id'][$place->place_id] = $place->place_id;

                    $place_data['place_name'][$place->place_id] = $place->place_name;
            }


            $join = array('table'=>'vehicle,steersman','where'=>'shipment_road_add>0 AND vehicle=vehicle_id AND steersman=steersman_id');

            $data = array(

                'where'=>'steersman = '.$steersman.' AND bill_out >= '.strtotime($batdau.' '.$giobatdau).' AND bill_out <= '.strtotime($ketthuc.' '.$gioketthuc).$and,

                'order_by'=>'bill_delivery_date ASC',

                );

            $shipments = $shipment_model->getAllShipment($data,$join);



            $str = '<table class="table_data" id="tblExport2">';
            $str .= '<thead><tr><th class="fix"><input checked type="checkbox" onclick="checkall(\'checkbox\', this)" name="checkall"/></th><th class="fix">STT</th><th class="fix">Ngày</th><th class="fix">Số DO</th><th class="fix">Xe</th><th class="fix">Kho đi</th><th class="fix">Kho đến</th><th class="fix">Tài xế</th><th class="fix">Số tiền</th></tr></thead>';
            $str .= '<tbody>';

            $i = 1; $tongtien = 0; $tongnhan=0; $tonggiao=0;
            foreach ($shipments as $shipment) {

                $road_lists = $road_list_model->queryShipment('SELECT shipment FROM road_list WHERE shipment LIKE "'.$shipment->shipment_id.'" OR shipment LIKE "'.$shipment->shipment_id.',%" OR shipment LIKE "%,'.$shipment->shipment_id.',%" OR shipment LIKE "%,'.$shipment->shipment_id.'"');

                $road_list_adds = $road_list_model->queryShipment('SELECT shipment FROM road_list WHERE road_list_id = '.$id.' AND (shipment LIKE "'.$shipment->shipment_id.'" OR shipment LIKE "'.$shipment->shipment_id.',%" OR shipment LIKE "%,'.$shipment->shipment_id.',%" OR shipment LIKE "%,'.$shipment->shipment_id.'")');



                if (!$road_lists || $road_list_adds) {
                    $tien = $shipment->shipment_road_add;
                    $tongtien += $tien;
                    
                    $str .= '<tr class="tr" data="'.$shipment->shipment_id.'"><td><input checked name="check_i[]" type="checkbox" class="checkbox" value="'.$shipment->shipment_id.'" data="'.$tien.'" ></td><td class="fix">'.$i++.'</td><td class="fix">'.$this->lib->hien_thi_ngay_thang($shipment->shipment_date).'</td><td class="fix">'.$shipment->bill_number.'</td><td class="fix">'.$shipment->vehicle_number.'</td><td class="fix">'.$place_data['place_name'][$shipment->shipment_from].'</td><td class="fix">'.$place_data['place_name'][$shipment->shipment_to].'</td><td class="fix">'.$shipment->steersman_name.'</td><td class="fix">'.$this->lib->formatMoney($tien).'</td></tr>';
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->roadlist) || json_decode($_SESSION['user_permission_action'])->roadlist != "roadlist") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $road_list_model = $this->model->get('roadlistModel');
            $debit = $this->model->get('debitModel');


            $data = array(

                        'road_list_number' => trim($_POST['road_list_number']),

                        'road_list_date' => strtotime($_POST['road_list_date']),

                        'steersman' => trim($_POST['steersman']),

                        'road_list_price' => trim(str_replace(',','',$_POST['road_list_price'])),

                        'shipment' => trim($_POST['shipment']),

                        'start_time' => strtotime($_POST['start_time']),

                        'end_time' => strtotime($_POST['end_time']),

                        'start_work' => strtotime(trim($_POST['start_time']).' '.trim($_POST['start_work'])),

                        'end_work' => strtotime(trim($_POST['end_time']).' '.trim($_POST['end_work'])),

                        );



            if ($_POST['yes'] != "") {

                if ($road_list_model->checkShipment($_POST['yes'].' AND road_list_number = "'.trim($_POST['road_list_number']).'"')) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $road_list_model->updateShipment($data,array('road_list_id' => $_POST['yes']));

                    $id_road_list = $_POST['yes'];

                    /*Log*/

                    /**/
                    $data_debit = array(

                        'debit_date'=>$data['road_list_date'],

                        'money'=>$data['road_list_price'],

                        'comment'=>'Tiền đi đường - '.$data['road_list_number'],

                        'check_debit'=>2,

                        'road_list'=>$id_road_list,

                    );

                    $debit->updateDebit($data_debit,array('road_list'=>$id_road_list,'check_debit'=>2));
                    

                    echo "Cập nhật thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|road_list|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    }

                

                

            }

            else{



                if ($road_list_model->getShipmentByWhere(array('road_list_number'=>$data['road_list_number']))) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $road_list_model->createShipment($data);

                    $id_road_list = $road_list_model->getLastShipment()->road_list_id;

                    /*Log*/

                    /**/
                    $data_debit = array(

                        'debit_date'=>$data['road_list_date'],

                        'money'=>$data['road_list_price'],

                        'comment'=>'Tiền đi đường - '.$data['road_list_number'],

                        'check_debit'=>2,

                        'road_list'=>$id_road_list,

                    );

                    $debit->createDebit($data_debit);


                    echo "Thêm thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$road_list_model->getLastShipment()->road_list_id."|road_list|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    }

                

                

            }


            /*$road_list_price = 0;


            $arr = explode(',', $data['shipment']);



            foreach ($arr as $key) {

                $d = $shipment_model->getShipment($key);

                $road_list_price += $d->shipment_ton*$d->shipment_charge;

            }


            $road_list_model->updateShipment(array('road_list_price'=>$road_list_price),array('road_list_id' => $id_road_list));*/
            

                    

        }

    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->roadlist) || json_decode($_SESSION['user_permission_action'])->roadlist != "roadlist") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $road_list_model = $this->model->get('roadlistModel');
            $debit = $this->model->get('debitModel');

            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {

                    $debit->queryDebit('DELETE FROM debit WHERE road_list = '.$data);

                    $road_list_model->deleteShipment($data);

                    

                    

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|road_list|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }



                /*Log*/

                    /**/



                return true;

            }

            else{

                $debit->queryDebit('DELETE FROM debit WHERE road_list = '.$_POST['data']);
                /*Log*/

                    /**/

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|road_list|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $road_list_model->deleteShipment($_POST['data']);

            }

            

        }

    }




}



?>