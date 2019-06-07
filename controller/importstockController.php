<?php

Class importstockController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->importstock) || json_decode($_SESSION['user_permission_action'])->importstock != "importstock") {

            $this->view->data['disable_control'] = 1;

        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Nhập kho';



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

            $tab_active = isset($_POST['tha']) ? $_POST['tha'] : null;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'import_stock_date';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

            $batdau = '01-'.date('m-Y');



            $ketthuc = date('t-m-Y');



            $vong = (int)date('m',strtotime($batdau));



            $trangthai = date('Y',strtotime($batdau));

            $tab_active = 1;

        }



        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));



        $vong = (int)date('m',strtotime($batdau));



        $trangthai = date('Y',strtotime($batdau));



        $costlist_model = $this->model->get('costlistModel');

        $this->view->data['cost_lists'] = $costlist_model->getAllCost();



        $house_model = $this->model->get('houseModel');

        $houses = $house_model->getAllHouse();

        $this->view->data['houses'] = $houses;

        



        $customer_model = $this->model->get('customerModel');



        $customers = $customer_model->getAllCustomer(array('order_by'=>'customer_name','order'=>'ASC'));



        $customer_data = array();



        foreach ($customers as $customer) {



            $customer_data['customer_id'][$customer->customer_id] = $customer->customer_id;



            $customer_data['customer_name'][$customer->customer_id] = $customer->customer_name;



        }

        $this->view->data['customer_data'] = $customer_data;





        $import_model = $this->model->get('importstockModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;



        $join = array('table'=>'user, house','where'=>'import_stock_user=user_id AND house = house_id');



        $data = array(

            'where' => 'import_stock_date >= '.strtotime($batdau).' AND import_stock_date < '.strtotime($ngayketthuc),

        );



        

        $tongsodong = count($import_model->getAllStock($data,$join));

        $tongsotrang = ceil($tongsodong / $sonews);

        $imports = array();
        foreach ($houses as $house) {
            

            $data = array(

                'order_by'=>$order_by,

                'order'=>$order,

                'limit'=>$x.','.$sonews,

                'where' => 'house = '.$house->house_id.' AND import_stock_date >= '.strtotime($batdau).' AND import_stock_date < '.strtotime($ngayketthuc),

                );



           

            

            if ($keyword != '') {

                $search = ' AND ( import_stock_code LIKE "%'.$keyword.'%" 

                            OR username LIKE "%'.$keyword.'%" )';

                $data['where'] .= $search;

            }

            

             $imports[$house->house_id] = $import_model->getAllStock($data,$join);

             if ($tab_active == 0) {
                 $tab_active = $house->house_id;
             }
        }

        
        $this->view->data['imports'] = $imports;
        
        $this->view->data['tab_active'] = $tab_active;


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



        



        $this->view->data['lastID'] = isset($import_model->getLastStock()->import_stock_id)?$import_model->getLastStock()->import_stock_id:0;

        

        $this->view->show('importstock/index');

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



                echo '<li onclick="set_item_customer(\''.$rs->customer_id.'\',\''.$rs->customer_name.'\')">'.$customer_name.'</li>';



            }



        }



    }



    public function add(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->importstock) || json_decode($_SESSION['user_permission_action'])->importstock != "importstock") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $import_model = $this->model->get('importstockModel');

            $stock_model = $this->model->get('sparestockModel');

            $spare_model = $this->model->get('sparepartModel');

            $spare_temp = $this->model->get('spareparttempModel');



            $spare_code_model = $this->model->get('sparepartcodeModel');



            $debit = $this->model->get('debitModel');

            $vat = $this->model->get('vatModel');



            $import_stock_cost_model = $this->model->get('importstockcostModel');



            /**************/



            $import_stock_cost_list = $_POST['import_stock_cost'];



            /**************/



            $data = array(

                        

                        'import_stock_code' => trim($_POST['import_stock_code']),

                        'import_stock_date' => strtotime($_POST['import_stock_date']),

                        'import_stock_user' => $_SESSION['userid_logined'],

                        'invoice_number' => trim($_POST['invoice_number']),

                        'invoice_date' => strtotime($_POST['invoice_date']),

                        'invoice_customer' => trim($_POST['invoice_customer']),

                        'import_stock_comment' => trim($_POST['import_stock_comment']),

                        'deliver' => trim($_POST['deliver']),

                        'deliver_address' => trim($_POST['deliver_address']),

                        'house' => trim($_POST['house']),

                        );





            if ($_POST['yes'] != "") {

                if ($import_model->checkStock($_POST['yes'],trim($_POST['import_stock_code']))) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $import = $import_model->getStock($_POST['yes']);



                    $import_model->updateStock($data,array('import_stock_id' => $_POST['yes']));

                    $id_import = $_POST['yes'];

                    /*Log*/

                    /**/

                    if (($import->invoice_customer == "" || $import->invoice_customer == 0 ) && $data['invoice_customer'] > 0) {

                        $data_debit = array(

                                'debit_date'=>$data['import_stock_date'],

                                'customer'=>$data['invoice_customer'],

                                'money'=>0,

                                'money_vat'=>1,

                                'comment'=>'Mua hàng HD số '.$data['invoice_number'],

                                'check_debit'=>2,

                                'import_stock'=>$id_import,

                            );

                            $debit->createDebit($data_debit);



                        $data_vat = array(

                                'in_out'=>1,

                                'vat_number'=>$data['invoice_number'],

                                'vat_date'=>$data['invoice_date'],

                                'import_stock'=>$id_import,

                            );

                            $vat->createVAT($data_vat);

                    }

                    else if($import->invoice_customer > 0 && $data['invoice_customer'] > 0){

                        $data_debit = array(

                            'debit_date'=>$data['import_stock_date'],

                            'customer'=>$data['invoice_customer'],

                            'money'=>0,

                            'money_vat'=>1,

                            'comment'=>'Mua hàng HD số '.$data['invoice_number'],

                            'check_debit'=>2,

                            'import_stock'=>$id_import,

                        );

                        $debit->updateDebit($data_debit,array('import_stock'=>$id_import));



                        $data_vat = array(

                                'in_out'=>1,

                                'vat_number'=>$data['invoice_number'],

                                'vat_date'=>$data['invoice_date'],

                                'import_stock'=>$id_import,

                            );

                            $vat->updateVAT($data_vat,array('import_stock'=>$id_import));

                    }

                    else if($import->invoice_customer > 0 && ($data['invoice_customer'] == 0 || $data['invoice_customer'] == "")){

                        $debit->queryDebit('DELETE FROM debit WHERE import_stock = '.$id_import);

                        $vat->queryVAT('DELETE FROM vat WHERE import_stock = '.$id_import);

                    }



                    foreach ($import_stock_cost_list as $v) {



                        $data_cost = array(



                            'import_stock' => $id_import,



                            'cost' => trim(str_replace(',','',$v['cost'])),



                            'cost_list' => $v['cost_list'],



                            'check_vat' => $v['check_vat'],



                            'comment' => trim($v['comment']),



                            'receiver' => isset($v['receiver'])?$v['receiver']:null,



                            'cost_document' => trim($v['cost_document']),



                            'cost_document_date' => trim(strtotime($v['cost_document_date'])),



                        );





                        if ($v['import_stock_cost_id'] == "") {



                            if ($data_cost['receiver'] > 0) {

                                $import_stock_cost_model->createStock($data_cost);

                                $id_stock_cost = $import_stock_cost_model->getLastStock()->import_stock_cost_id;



                                if ($data_cost['check_vat'] == 1) {

                                    $data_debit = array(

                                        'debit_date'=>$data['import_stock_date'],

                                        'customer'=>$data_cost['receiver'],

                                        'money'=>round($data_cost['cost']/1.1),

                                        'money_vat_price'=>$data_cost['cost']-round($data_cost['cost']/1.1),

                                        'money_vat'=>$data_cost['check_vat'],

                                        'comment'=>$data_cost['comment'],

                                        'check_debit'=>2,

                                        'import_stock_cost'=>$id_stock_cost,

                                    );

                                    $debit->createDebit($data_debit);

                                    $data_vat = array(

                                        'in_out'=>1,

                                        'vat_number'=>$data_cost['cost_document'],

                                        'vat_date'=>$data_cost['cost_document_date'],

                                        'import_stock_cost'=>$id_stock_cost,

                                    );

                                    $vat->createVAT($data_vat);

                                }

                                else{

                                    $data_debit = array(

                                        'debit_date'=>$data['import_stock_date'],

                                        'customer'=>$data_cost['receiver'],

                                        'money'=>$data_cost['cost'],

                                        'money_vat_price'=>0,

                                        'money_vat'=>$data_cost['check_vat'],

                                        'comment'=>$data_cost['comment'],

                                        'check_debit'=>2,

                                        'import_stock_cost'=>$id_stock_cost,

                                    );

                                    $debit->createDebit($data_debit);

                                }

                                $vat_sum = round($data_cost['cost']/1.1);
                                $vat_price = $data_cost['cost']-round($data_cost['cost']/1.1);

                                $vat->updateVAT(array('vat_sum'=>$vat_sum,'vat_price'=>$vat_price),array('import_stock_cost' => $id_stock_cost));

                            }

                            
                            


                        }



                        else if ($v['import_stock_cost_id'] > 0) {

                            $check = $import_stock_cost_model->getStock($v['import_stock_cost_id']);

                            $import_stock_cost_model->updateStock($data_cost,array('import_stock_cost_id'=>$v['import_stock_cost_id']));



                            if ($data_cost['check_vat'] == 1) {

                                $data_debit = array(

                                    'debit_date'=>$data['import_stock_date'],

                                    'customer'=>$data_cost['receiver'],

                                    'money'=>round($data_cost['cost']/1.1),

                                    'money_vat_price'=>$data_cost['cost']-round($data_cost['cost']/1.1),

                                    'money_vat'=>$data_cost['check_vat'],

                                    'comment'=>$data_cost['comment'],

                                    'check_debit'=>2,

                                    'import_stock_cost'=>$v['import_stock_cost_id'],

                                );

                                $debit->updateDebit($data_debit,array('import_stock_cost'=>$v['import_stock_cost_id']));

                            }

                            else{

                                $data_debit = array(

                                    'debit_date'=>$data['import_stock_date'],

                                    'customer'=>$data_cost['receiver'],

                                    'money'=>$data_cost['cost'],

                                    'money_vat_price'=>0,

                                    'money_vat'=>$data_cost['check_vat'],

                                    'comment'=>$data_cost['comment'],

                                    'check_debit'=>2,

                                    'import_stock_cost'=>$v['import_stock_cost_id'],

                                );

                                $debit->updateDebit($data_debit,array('import_stock_cost'=>$v['import_stock_cost_id']));

                            }

                            



                            if ($check->check_vat == 1 && $data_cost['check_vat'] == 1) {

                                 $data_vat = array(

                                    'in_out'=>1,

                                    'vat_number'=>$data_cost['cost_document'],

                                    'vat_date'=>$data_cost['cost_document_date'],

                                    'import_stock_cost'=>$v['import_stock_cost_id'],

                                );

                                $vat->updateVAT($data_vat,array('import_stock_cost'=>$v['import_stock_cost_id']));

                            }

                            else if ($check->check_vat == 1 && $data_cost['check_vat'] != 1) {

                                $vat->queryVAT('DELETE FROM vat WHERE import_stock_cost = '.$v['import_stock_cost_id']);

                            }

                            else if ($check->check_vat != 1 && $data_cost['check_vat'] == 1) {

                                 $data_vat = array(

                                    'in_out'=>1,

                                    'vat_number'=>$data_cost['cost_document'],

                                    'vat_date'=>$data_cost['cost_document_date'],

                                    'import_stock_cost'=>$v['import_stock_cost_id'],

                                );

                                $vat->createVAT($data_vat);

                            }

                            $vat_sum = round($data_cost['cost']/1.1);
                            $vat_price = $data_cost['cost']-round($data_cost['cost']/1.1);

                            $vat->updateVAT(array('vat_sum'=>$vat_sum,'vat_price'=>$vat_price),array('import_stock_cost' => $v['import_stock_cost_id']));

                        }



                    }



                    echo "Cập nhật thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|import_stock|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    }

                

                

            }

            else{



                if ($import_model->getStockByWhere(array('import_stock_code'=>$data['import_stock_code']))) {

                    echo "Thông tin này đã tồn tại";

                    return false;

                }

                else{

                    $import_model->createStock($data);

                    $id_import = $import_model->getLastStock()->import_stock_id;

                    /*Log*/

                    /**/



                    if ($data['invoice_customer'] > 0) {

                        $data_debit = array(

                                'debit_date'=>$data['import_stock_date'],

                                'customer'=>$data['invoice_customer'],

                                'money'=>0,

                                'money_vat'=>1,

                                'comment'=>'Mua hàng HD số '.$data['invoice_number'],

                                'check_debit'=>2,

                                'import_stock'=>$id_import,

                            );

                            $debit->createDebit($data_debit);



                            $data_vat = array(

                                'in_out'=>1,

                                'vat_number'=>$data['invoice_number'],

                                'vat_date'=>$data['invoice_date'],

                                'import_stock'=>$id_import,

                            );

                            $vat->createVAT($data_vat);

                    }

                    $vat_sum = 0;
                    $vat_price = 0;

                    foreach ($import_stock_cost_list as $v) {



                        $data_cost = array(



                            'import_stock' => $id_import,



                            'cost' => trim(str_replace(',','',$v['cost'])),



                            'cost_list' => $v['cost_list'],



                            'check_vat' => $v['check_vat'],



                            'comment' => trim($v['comment']),



                            'receiver' => isset($v['receiver'])?$v['receiver']:null,



                            'cost_document' => trim($v['cost_document']),



                            'cost_document_date' => trim(strtotime($v['cost_document_date'])),



                        );





                        if ($v['import_stock_cost_id'] == "") {



                            if ($data_cost['receiver'] > 0) {



                                $import_stock_cost_model->createStock($data_cost);

                                $id_stock_cost = $import_stock_cost_model->getLastStock()->import_stock_cost_id;



                                if ($data_cost['check_vat'] == 1) {

                                    $data_debit = array(

                                        'debit_date'=>$data['import_stock_date'],

                                        'customer'=>$data_cost['receiver'],

                                        'money'=>round($data_cost['cost']/1.1),

                                        'money_vat_price'=>$data_cost['cost']-round($data_cost['cost']/1.1),

                                        'money_vat'=>$data_cost['check_vat'],

                                        'comment'=>$data_cost['comment'],

                                        'check_debit'=>2,

                                        'import_stock_cost'=>$id_stock_cost,

                                    );

                                    $debit->createDebit($data_debit);



                                    $data_vat = array(

                                        'in_out'=>1,

                                        'vat_number'=>$data_cost['cost_document'],

                                        'vat_date'=>$data_cost['cost_document_date'],

                                        'import_stock_cost'=>$id_stock_cost,

                                    );

                                    $vat->createVAT($data_vat);

                                }

                                else{

                                    $data_debit = array(

                                        'debit_date'=>$data['import_stock_date'],

                                        'customer'=>$data_cost['receiver'],

                                        'money'=>$data_cost['cost'],

                                        'money_vat_price'=>0,

                                        'money_vat'=>$data_cost['check_vat'],

                                        'comment'=>$data_cost['comment'],

                                        'check_debit'=>2,

                                        'import_stock_cost'=>$id_stock_cost,

                                    );

                                    $debit->createDebit($data_debit);

                                }

                                



                                $vat_sum = round($data_cost['cost']/1.1);
                                $vat_price = $data_cost['cost']-round($data_cost['cost']/1.1);

                                $vat->updateVAT(array('vat_sum'=>$vat_sum,'vat_price'=>$vat_price),array('import_stock_cost' => $id_stock_cost));



                            }

                            



                        }



                        else if ($v['import_stock_cost_id'] > 0) {

                            $check = $import_stock_cost_model->getStock($v['import_stock_cost_id']);



                            $import_stock_cost_model->updateStock($data_cost,array('import_stock_cost_id'=>$v['import_stock_cost_id']));



                            if ($data_cost['check_vat'] == 1) {

                                $data_debit = array(

                                    'debit_date'=>$data['import_stock_date'],

                                    'customer'=>$data_cost['receiver'],

                                    'money'=>round($data_cost['cost']/1.1),

                                    'money_vat_price'=>$data_cost['cost']-round($data_cost['cost']/1.1),

                                    'money_vat'=>$data_cost['check_vat'],

                                    'comment'=>$data_cost['comment'],

                                    'check_debit'=>2,

                                    'import_stock_cost'=>$v['import_stock_cost_id'],

                                );

                                $debit->updateDebit($data_debit,array('import_stock_cost'=>$v['import_stock_cost_id']));

                            }

                            else{

                                $data_debit = array(

                                    'debit_date'=>$data['import_stock_date'],

                                    'customer'=>$data_cost['receiver'],

                                    'money'=>$data_cost['cost'],

                                    'money_vat_price'=>0,

                                    'money_vat'=>$data_cost['check_vat'],

                                    'comment'=>$data_cost['comment'],

                                    'check_debit'=>2,

                                    'import_stock_cost'=>$v['import_stock_cost_id'],

                                );

                                $debit->updateDebit($data_debit,array('import_stock_cost'=>$v['import_stock_cost_id']));

                            }



                            if ($check->check_vat == 1 && $data_cost['check_vat'] == 1) {

                                 $data_vat = array(

                                    'in_out'=>1,

                                    'vat_number'=>$data_cost['cost_document'],

                                    'vat_date'=>$data_cost['cost_document_date'],

                                    'import_stock_cost'=>$v['import_stock_cost_id'],

                                );

                                $vat->updateVAT($data_vat,array('import_stock_cost'=>$v['import_stock_cost_id']));

                            }

                            else if ($check->check_vat == 1 && $data_cost['check_vat'] != 1) {

                                $vat->queryVAT('DELETE FROM vat WHERE import_stock_cost = '.$v['import_stock_cost_id']);

                            }

                            else if ($check->check_vat != 1 && $data_cost['check_vat'] == 1) {

                                 $data_vat = array(

                                    'in_out'=>1,

                                    'vat_number'=>$data_cost['cost_document'],

                                    'vat_date'=>$data_cost['cost_document_date'],

                                    'import_stock_cost'=>$v['import_stock_cost_id'],

                                );

                                $vat->createVAT($data_vat);

                            }


                            $vat_sum = round($data_cost['cost']/1.1);
                            $vat_price = $data_cost['cost']-round($data_cost['cost']/1.1);

                            $vat->updateVAT(array('vat_sum'=>$vat_sum,'vat_price'=>$vat_price),array('import_stock_cost' => $v['import_stock_cost_id']));
                           



                        }



                    }



                    echo "Thêm thành công";



                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$import_model->getLastStock()->import_stock_id."|import_stock|".implode("-",$data)."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                    }

                

                

            }



            $total_number = 0;

            $total_price = 0;

            $total_vat_price = 0;



            $spare_part = $_POST['spare_part'];



            foreach ($spare_part as $v) {



                if (isset($v['spare_part_code_id']) && $v['spare_part_code_id'] > 0) {



                    $id_code = $v['spare_part_code_id'];



                }

                else{

                    $code_data = array(

                        'code'=>trim($v['spare_part_code']),

                        'name'=>trim($v['spare_part_name']),

                    );



                    if (!$spare_code_model->getStockByWhere(array('code'=>$code_data['code'],'name'=>$code_data['name']))) {

                        $spare_code_model->createStock($code_data);

                        $id_code = $spare_code_model->getLastStock()->spare_part_code_id;

                    }

                    else{

                        $id_code = $spare_code_model->getStockByWhere(array('code'=>$code_data['code'],'name'=>$code_data['name']))->spare_part_code_id;

                    }

                    

                }



                    if (isset($v['spare_part_id']) && $v['spare_part_id'] > 0) {



                        $id_spare_part = $v['spare_part_id'];



                    }



                    else{



                        $data_spare_part = array(



                            'spare_part_name' => trim($v['spare_part_name']),



                            'spare_part_code' => trim($v['spare_part_code']),



                            'spare_part_seri' => trim($v['spare_part_seri']),



                            'spare_part_brand' => trim($v['spare_part_brand']),



                            'spare_part_date_manufacture' => strtotime($v['spare_part_date_manufacture']),



                            'code_list' => $id_code,



                        );

                        if ($data_spare_part['spare_part_seri'] != "") {
                            if (!$spare_model->getStockByWhere(array('spare_part_seri'=>$data_spare_part['spare_part_seri'],'code_list'=>$id_code))) {

                                $spare_model->createStock($data_spare_part);

                                $id_spare_part = $spare_model->getLastStock()->spare_part_id;

                            }

                            else{

                                $id_spare_part = $spare_model->getStockByWhere(array('spare_part_seri'=>$data_spare_part['spare_part_seri'],'code_list'=>$id_code))->spare_part_id;

                            }
                        }
                        else{
                            if (!$spare_model->getStockByWhere(array('spare_part_code'=>$data_spare_part['spare_part_code'],'code_list'=>$id_code))) {

                                $spare_model->createStock($data_spare_part);

                                $id_spare_part = $spare_model->getLastStock()->spare_part_id;

                            }

                            else{

                                $id_spare_part = $spare_model->getStockByWhere(array('spare_part_code'=>$data_spare_part['spare_part_code'],'code_list'=>$id_code))->spare_part_id;

                            }
                        }

                        



                        



                        $data2 = array('spare_part_id'=>$id_spare_part,'spare_part_temp_date'=>strtotime(date('d-m-Y')),'spare_part_temp_action'=>1,'spare_part_temp_user'=>$_SESSION['userid_logined'],'name'=>'Vật tư');

                        $data_temp = array_merge($data_spare_part, $data2);

                        $spare_temp->createStock($data_temp);



                    }



                    $data_stock = array(



                        'import_stock' => $id_import,



                        'spare_part' => $id_spare_part,



                        'spare_stock_unit' => $v['spare_stock_unit'],



                        'spare_stock_number' => $v['spare_stock_number'],



                        'spare_stock_price' => trim(str_replace(',','',$v['spare_stock_price'])),



                        'spare_stock_vat_percent' => $v['spare_stock_vat_percent'],



                        'spare_stock_vat_price' => trim(str_replace(',','',$v['spare_stock_vat_price'])),

                        



                    );



                    if (!$stock_model->getStockByWhere(array('import_stock'=>$id_import,'spare_part'=>$id_spare_part))) {

                        $stock_model->createStock($data_stock);

                    }

                    else{

                        $id_stock = $stock_model->getStockByWhere(array('import_stock'=>$id_import,'spare_part'=>$id_spare_part))->spare_stock_id;

                        $stock_model->updateStock($data_stock,array('spare_stock_id'=>$id_stock));

                    }



                    $total_number += $data_stock['spare_stock_number'];

                    $total_price += $data_stock['spare_stock_price']*$data_stock['spare_stock_number'];

                    $total_vat_price += $data_stock['spare_stock_vat_price'];

                }



                $import_model->updateStock(array('import_stock_total'=>$total_number,'import_stock_price'=>$total_price,'import_stock_vat'=>$total_vat_price),array('import_stock_id'=>$id_import));

                
                $data_vat = array(

                    'vat_sum'=>$total_price,

                    'vat_price'=>$total_vat_price,

                );

                $vat->updateVAT($data_vat,array('import_stock'=>$id_import));

                $data_debit = array(

                    'money'=>$total_price,

                    'money_vat_price'=>$total_vat_price,

                );

                $debit->updateDebit($data_debit,array('import_stock'=>$id_import));

                    

        }

    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->importstock) || json_decode($_SESSION['user_permission_action'])->importstock != "importstock") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $import_model = $this->model->get('importstockModel');

            $stock_model = $this->model->get('sparestockModel');

            $debit = $this->model->get('debitModel');

            $vat = $this->model->get('vatModel');

            $import_stock_cost_model = $this->model->get('importstockcostModel');

            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {



                    $costs = $import_stock_cost_model->getAllStock(array('where'=>'import_stock = '.$data));

                    foreach ($costs as $cost) {

                        $debit->queryDebit('DELETE FROM debit WHERE import_stock_cost = '.$cost->import_stock_cost_id);

                        $vat->queryVAT('DELETE FROM vat WHERE import_stock_cost = '.$cost->import_stock_cost_id);

                        $import_stock_cost_model->deleteStock($cost->import_stock_cost_id);

                    }



                    $stock_model->query('DELETE FROM spare_stock WHERE import_stock = '.$data);

                    $debit->queryDebit('DELETE FROM debit WHERE import_stock = '.$data);

                    $vat->queryVAT('DELETE FROM vat WHERE import_stock = '.$data);



                    $import_model->deleteStock($data);

                    

                    

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|import_stock|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }



                /*Log*/

                    /**/



                return true;

            }

            else{



                $costs = $import_stock_cost_model->getAllStock(array('where'=>'import_stock = '.$_POST['data']));

                foreach ($costs as $cost) {

                    $debit->queryDebit('DELETE FROM debit WHERE import_stock_cost = '.$cost->import_stock_cost_id);

                    $vat->queryVAT('DELETE FROM vat WHERE import_stock_cost = '.$cost->import_stock_cost_id);

                    $import_stock_cost_model->deleteStock($cost->import_stock_cost_id);

                }



                $stock_model->query('DELETE FROM spare_stock WHERE import_stock = '.$_POST['data']);

                $debit->queryDebit('DELETE FROM debit WHERE import_stock = '.$_POST['data']);

                $vat->queryVAT('DELETE FROM vat WHERE import_stock = '.$_POST['data']);

                /*Log*/

                    /**/

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|import_stock|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $import_model->deleteStock($_POST['data']);

            }

            

        }

    }



    public function getSpare(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {



            $spare_code_model = $this->model->get('sparepartcodeModel');

            $spare_model = $this->model->get('sparepartModel');

            $spare_stock_model = $this->model->get('sparestockModel');



            if ($_POST['keyword'] == "*") {



                



                $list = $spare_code_model->getAllStock();



            }



            else{



                $data = array(



                'where'=>'( name LIKE "%'.$_POST['keyword'].'%" )',



                );



                $list = $spare_code_model->getAllStock($data);



                if (!$list) {

                    $data = array(



                    'where'=>'( code LIKE "%'.$_POST['keyword'].'%" )',



                    );



                    $list = $spare_code_model->getAllStock($data);

                }



            }



            



            foreach ($list as $rs) {



                // put in bold the written text

                



                $spare_name = '['.$rs->code.']-'.$rs->name;



                if ($_POST['keyword'] != "*") {



                    $spare_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', '['.$rs->code.']-'.$rs->name);



                }





                $stocks = $spare_stock_model->queryStock('SELECT * FROM spare_stock, spare_part WHERE spare_part = spare_part_id AND import_stock > 0 AND spare_part IN (SELECT spare_part_id FROM spare_part WHERE code_list = '.$rs->spare_part_code_id.') ORDER BY spare_stock_id DESC LIMIT 1');

                if ($stocks) {

                    foreach ($stocks as $stock) {

                        echo '<li onclick="set_item_other(\''.$rs->spare_part_code_id.'\',\''.$rs->name.'\',\''.$rs->code.'\',\''.($stock->spare_part_date_manufacture>0?date('d-m-Y',$stock->spare_part_date_manufacture):null).'\',\''.$stock->spare_part_brand.'\',\''.$_POST['offset'].'\',\''.$stock->spare_stock_unit.'\',\''.$stock->spare_stock_price.'\')">'.$spare_name.'</li>';

                    }

                    

                }

                else{

                    $spares = $spare_model->getAllStock(array('where'=>'code_list = '.$rs->spare_part_code_id,'order_by'=>'spare_part_id DESC','limit'=>1));

                    foreach ($spares as $spare) {

                        echo '<li onclick="set_item_other(\''.$rs->spare_part_code_id.'\',\''.$rs->name.'\',\''.$rs->code.'\',\''.($spare->spare_part_date_manufacture>0?date('d-m-Y',$spare->spare_part_date_manufacture):null).'\',\''.$spare->spare_part_brand.'\',\''.$_POST['offset'].'\',\'\',\'\')">'.$spare_name.'</li>';

                    }

                    

                }



                



            }



        }



    }

    public function deletespare(){



        if (isset($_POST['import_stock'])) {



            $spare_model = $this->model->get('sparestockModel');







            $spare_model->queryStock('DELETE FROM spare_stock WHERE import_stock = '.$_POST['import_stock'].' AND spare_part = '.$_POST['spare_part']);



            echo 'Đã xóa thành công';



        }



    }

    public function deletesparecode(){



        if (isset($_POST['import_stock'])) {



            $spare_model = $this->model->get('sparestockModel');







            $spare_model->queryStock('DELETE FROM spare_stock WHERE import_stock = '.$_POST['import_stock'].' AND spare_part IN (SELECT spare_part_id FROM spare_part WHERE code_list = '.$_POST['spare_part_code'].')');



            echo 'Đã xóa thành công';



        }



    }

    public function spare(){



        if(isset($_POST['import_stock'])){



            



            $spare_model = $this->model->get('sparestockModel');



            $spare_code_model = $this->model->get('sparepartcodeModel');



            $join = array('table'=>'spare_part, spare_stock','where'=>'code_list = spare_part_code_id AND spare_part_id = spare_part GROUP BY spare_part_code_id');

            $data = array(



                'where' => 'import_stock = '.$_POST['import_stock'],



            );



            $codes = $spare_code_model->getAllStock($data,$join);



            $count = array();

            $number = array();

            $vat_price = array();

            $imports = array();

            $check_seri = array();

            foreach ($codes as $code) {

                $join = array('table'=>'spare_part','where'=>'spare_stock.spare_part = spare_part.spare_part_id');



                $data = array(



                    'where' => 'code_list = '.$code->spare_part_code_id.' AND import_stock = '.$_POST['import_stock'],



                );



                $imports[$code->spare_part_code_id] = $spare_model->getAllStock($data,$join);

                $check_seri[$code->spare_part_code_id] = 0;

                foreach ($imports[$code->spare_part_code_id] as $spare) {

                    $number[$code->spare_part_code_id] = isset($number[$code->spare_part_code_id])?$number[$code->spare_part_code_id]+$spare->spare_stock_number:$spare->spare_stock_number;

                    $vat_price[$code->spare_part_code_id] = isset($vat_price[$code->spare_part_code_id])?$vat_price[$code->spare_part_code_id]+$spare->spare_stock_vat_price:$spare->spare_stock_vat_price;



                    

                    if ($spare->spare_part_seri != "") {

                        $check_seri[$code->spare_part_code_id] = 1;

                    }

                }



                $count[$code->spare_part_code_id] = count($imports[$code->spare_part_code_id]);

            }







            $str = "";



            if (!$codes) {



                $str .= '<tr class="'.$_POST['import_stock'].'">';



                $str .= '<td><input type="checkbox"  name="chk"></td>';



                $str .= '<td><table style="width: 100%">';



                $str .= '<tr class="'.$_POST['import_stock'] .'">';



                $str .= '<td>Tên sản phẩm</td>';



                $str .= '<td><input type="text" autocomplete="off" class="spare_part" name="spare_part[]" placeholder="Nhập tên hoặc * để chọn" >';



                $str .= '<ul class="name_list_id"></ul></td>';



                $str .= '<td>Mã sản phẩm</td>';



                $str .= '<td><input autocomplete="off" type="text" class="spare_part_code" name="spare_part_code[]" tabindex="4" placeholder="Nhập tên hoặc * để chọn" >';



                $str .= '<ul class="name_list_id_2"></ul></td></tr>';



                $str .= '<tr><td>Nhà sản xuất</td>';



                $str .= '<td><input type="text" class="spare_part_brand" name="spare_part_brand[]"></td>';



                $str .= '<td>Ngày sản xuất</td>';



                $str .= '<td><input type="text" class="spare_part_date_manufacture ngay" name="spare_part_date_manufacture[]"></td></tr>';



                $str .= '<tr><td>Đơn vị tính</td>';



                $str .= '<td><input type="text" class="spare_stock_unit" name="spare_stock_unit[]"></td>';



                $str .= '<td>Đơn giá</td>';



                $str .= '<td><input type="text" class="spare_stock_price numbers" name="spare_stock_price[]"></td></tr>';



                $str .= '<tr><td>Số lượng</td>';



                $str .= '<td><input style="width:50px" type="text" class="spare_stock_number number" name="spare_stock_number[]" tabindex="10" value="1" min="1" >';

                $str .= '<input type="checkbox" name="check_seri[]" class="check_seri" > Có số seri</td>';



                $str .= '<td>VAT</td>';



                $str .= '<td><input style="width:50px" type="text" class="spare_stock_vat_percent number" name="spare_stock_vat_percent[]" tabindex="11" placeholder="%" >';

                

                $str .= '<input style="width:120px" type="text" class="spare_stock_vat_price numbers" name="spare_stock_vat_price[]" tabindex="12" placeholder="Tổng tiền thuế" ></td></tr>';



                $str .= '<tr class="tr_seri" style="display:none">';

                    $str .= '<td></td>';

                    $str .= '<td colspan="4">';

                        

                        $str .= '<table class="dataTb" id="dataTb0" border="1" style="width: 100%; border: 1px solid rgb(221, 217, 217); margin-bottom: 10px" >';

                          $str .= '<tbody>';

                            $str .= '<tr>';

                              $str .= '<td><input type="checkbox" name="chk3" class="chk3"></td>';

                              $str .= '<td>';

                                $str .= '<table style="width:100%">';

                                  $str .= '<tr>';

                                    $str .= '<td>Số seri</td>';

                                    $str .= '<td>';

                                      $str .= '<input code="" data="0" type="text" class="spare_part_seri2" name="spare_part_seri2[]" tabindex="6" ><span class="dem" data="1">(1)</span>';

                                    $str .= '</td>';

                                    

                                  $str .= '</tr>';

                                $str .= '</table>';

                              $str .= '</td>';

                            $str .= '</tr>';

                          $str .= '</tbody>';

                        $str .= '</table>';

                        $str .= '<input type="button" value="Thêm" class="addRow3" onclick="addRow3(dataTb0)">';



                        $str .= '<input type="button" value="Xóa" class="deleteRow3" onclick="deleteRow3(dataTb0)">';

                    $str .= '</td>';

                $str .= '</tr>';



                $str .= '</table></td></tr>';



            }



            else{

                $i = 0;

                foreach ($codes as $code) {



                    $checked = $check_seri[$code->spare_part_code_id]>0?'checked="checked"':null;

                    $show = $check_seri[$code->spare_part_code_id]<1?'style="display:none"':null;



                    $str .= '<tr class="'.$_POST['import_stock'].'">';



                    $str .= '<td><input type="checkbox"  name="chk" alt="'.$code->spare_part_code_id.'"  data="'.$_POST['import_stock'].'" ></td>';



                    $str .= '<td><table style="width: 100%">';



                    $str .= '<tr class="'.$_POST['import_stock'] .'">';



                    $str .= '<td>Tên sản phẩm</td>';



                    $str .= '<td><input disabled type="text" autocomplete="off" class="spare_part" name="spare_part[]" placeholder="Nhập tên hoặc * để chọn" value="'.$code->name.'" data="'.$code->spare_part_code_id.'" >';



                    $str .= '<ul class="name_list_id"></ul></td>';



                    $str .= '<td>Mã sản phẩm</td>';



                    $str .= '<td><input disabled autocomplete="off" type="text" class="spare_part_code" name="spare_part_code[]" tabindex="4" placeholder="Nhập tên hoặc * để chọn" value="'.$code->code.'" >';



                    $str .= '<ul class="name_list_id_2"></ul></td></tr>';



                    $str .= '<tr><td>Nhà sản xuất</td>';



                    $str .= '<td><input type="text" class="spare_part_brand" name="spare_part_brand[]" value="'.$code->spare_part_brand.'" ></td>';



                    $str .= '<td>Ngày sản xuất</td>';



                    $str .= '<td><input type="text" class="spare_part_date_manufacture ngay" name="spare_part_date_manufacture[]" value="'.($code->spare_part_date_manufacture>0?date('d-m-Y',$code->spare_part_date_manufacture):null).'"></td></tr>';



                    $str .= '<tr><td>Đơn vị tính</td>';



                    $str .= '<td><input type="text" class="spare_stock_unit" name="spare_stock_unit[]" value="'.$code->spare_stock_unit.'"></td>';



                    $str .= '<td>Đơn giá</td>';



                    $str .= '<td><input type="text" class="spare_stock_price numbers" name="spare_stock_price[]" value="'.$this->lib->formatMoney($code->spare_stock_price).'"></td></tr>';



                    $str .= '<tr><td>Số lượng</td>';



                    $str .= '<td><input style="width:50px" type="text" class="spare_stock_number number" name="spare_stock_number[]" tabindex="10" value="'.$number[$code->spare_part_code_id].'" min="1" >';

                    $str .= '<input type="checkbox" name="check_seri[]" class="check_seri" '.$checked.' > Có số seri</td>';



                    $str .= '<td>VAT</td>';



                    $str .= '<td><input style="width:50px" type="text" class="spare_stock_vat_percent number" name="spare_stock_vat_percent[]" tabindex="11" placeholder="%" value="'.$code->spare_stock_vat_percent.'" >';

                    

                    $str .= '<input style="width:120px" type="text" class="spare_stock_vat_price numbers" name="spare_stock_vat_price[]" tabindex="12" placeholder="Tổng tiền thuế" value="'.$this->lib->formatMoney($vat_price[$code->spare_part_code_id]).'" ></td></tr>';



                    $str .= '<tr class="tr_seri" '.$show.'>';

                        $str .= '<td></td>';

                        $str .= '<td colspan="4">';

                            

                            $str .= '<table class="dataTb" id="dataTb'.$i.'" border="1" style="width: 100%; border: 1px solid rgb(221, 217, 217); margin-bottom: 10px" >';

                              $str .= '<tbody>';
                            $ns = 0;  
                              foreach ($imports[$code->spare_part_code_id] as $v) {
                                $ns++;
                                $str .= '<tr>';

                                  $str .= '<td><input type="checkbox" name="chk3" class="chk3" alt="'.$v->spare_part_id.'" data="'.$_POST['import_stock'].'" ></td>';

                                  $str .= '<td>';

                                    $str .= '<table style="width:100%">';

                                      $str .= '<tr>';

                                        $str .= '<td>Số seri</td>';

                                        $str .= '<td>';

                                          $str .= '<input code="'.$v->spare_part_id.'" data="'.$i.'" type="text" class="spare_part_seri2" name="spare_part_seri2[]" tabindex="6" value="'.($check_seri[$code->spare_part_code_id]>0?$v->spare_part_seri:null).'" ><span class="dem" data="'.$ns.'">('.$ns.')</span>';

                                        $str .= '</td>';

                                        

                                      $str .= '</tr>';

                                    $str .= '</table>';

                                  $str .= '</td>';

                                $str .= '</tr>';

                              }

                              $str .= '</tbody>';

                            $str .= '</table>';

                            $str .= '<input type="button" value="Thêm" class="addRow3" onclick="addRow3(\'dataTb'.$i.'\')">';



                            $str .= '<input type="button" value="Xóa" class="deleteRow3" onclick="deleteRow3(\'dataTb'.$i.'\')">';

                        $str .= '</td>';

                    $str .= '</tr>';



                    $str .= '</table></td></tr>';



                    $i++;



                }



            }







            echo $str;



        }



    }



    public function deletecost(){



        if (isset($_POST['import_stock_cost_id'])) {



            $import_stock_cost_model = $this->model->get('importstockcostModel');

            $debit = $this->model->get('debitModel');

            $vat = $this->model->get('vatModel');



            $debit->queryDebit('DELETE FROM debit WHERE import_stock_cost = '.$_POST['import_stock_cost_id']);
            $vat->queryVAT('DELETE FROM vat WHERE import_stock_cost = '.$_POST['import_stock_cost_id']);

            $import_stock_cost_model->queryStock('DELETE FROM import_stock_cost WHERE import_stock_cost_id = '.$_POST['import_stock_cost_id']);



            echo 'Đã xóa thành công';



        }



    }

    public function getcost(){



        if(isset($_POST['import_stock'])){



            



            $import_stock_cost_model = $this->model->get('importstockcostModel');

            $cost_list_model = $this->model->get('costlistModel');



            $cost_lists = $cost_list_model->getAllCost();





            $join = array('table'=>'customer, cost_list','where'=>'receiver = customer_id AND cost_list = cost_list_id');



            $data = array(



                'where' => 'import_stock = '.$_POST['import_stock'],



            );



            $costs = $import_stock_cost_model->getAllStock($data,$join);







            $str = "";



            if (!$costs) {



                $cost_data = "";

                foreach ($cost_lists as $cost) {

                    $cost_data .= '<option value="'.$cost->cost_list_id.'">'.$cost->cost_list_name.'</option>';

                }



                $str .= '<tr class="'.$_POST['import_stock'].'">';



                $str .= '<td><input type="checkbox"  name="chk2" data=""></td>';



                $str .= '<td><table style="width: 100%">';



                $str .= '<tr class="'.$_POST['import_stock'] .'">';



                $str .= '<td>Chi phí <a target="_blank" title="Thêm chi phí" href="'.BASE_URL.'/costlist"><i class="fa fa-plus"></i></a></td>';



                $str .= '<td><select style="width:150px" name="cost_list[]" class="cost_list" >';



                    $str .= $cost_data;



                $str .= '</select></td>';



                $str .= '<td>Số tiền</td>';



                $str .= '<td><input style="width:120px" type="text" class="cost numbers" name="cost[]"><input type="checkbox" class="check_vat" name="check_vat[]" value="1"> VAT</td></tr>';



                $str .= '<tr><td>Nội dung</td>';



                $str .= '<td><textarea class="comment" name="comment[]"></textarea></td>';



                $str .= '<td>Người nhận <a target="_blank" title="Thêm người nhận" href="'.BASE_URL.'/customer/newcus"><i class="fa fa-plus"></i></a></td>';



                $str .= '<td><input type="text" autocomplete="off" class="receiver" name="receiver[]" placeholder="Nhập tên hoặc * để chọn" >';

                $str .= '<ul class="name_list_id_3"></ul></td></tr>';



                $str .= '<tr><td>Số Hóa đơn chứng từ</td>';



                $str .= '<td><input type="text" class="cost_document" name="cost_document[]" style="width:100px" > Ngày <input type="text" class="cost_document_date ngay" name="cost_document_date[]" style="width:60px"></td>';



                $str .= '</tr></table></td></tr>';



            }



            else{



                foreach ($costs as $v) {



                    $cost_data = "";

                    foreach ($cost_lists as $cost) {

                        $cost_data .= '<option '.($v->cost_list==$cost->cost_list_id?'selected="selected"':null).' value="'.$cost->cost_list_id.'">'.$cost->cost_list_name.'</option>';

                    }



                    $checked = $v->check_vat==1?'checked="checked"':null;



                    $str .= '<tr class="'.$_POST['import_stock'].'">';



                    $str .= '<td><input type="checkbox" name="chk2" data="'.$v->import_stock_cost_id.'"  ></td>';



                    $str .= '<td><table style="width: 100%">';



                    $str .= '<tr class="'.$_POST['import_stock'] .'">';



                    $str .= '<td>Chi phí <a target="_blank" title="Thêm chi phí" href="'.BASE_URL.'/costlist"><i class="fa fa-plus"></i></a></td>';



                    $str .= '<td><select style="width:150px" name="cost_list[]" class="cost_list" >';



                        $str .= $cost_data;



                    $str .= '</select></td>';



                    $str .= '<td>Số tiền</td>';



                    $str .= '<td><input style="width:120px" type="text" class="cost numbers" name="cost[]" value="'.$this->lib->formatMoney($v->cost).'" ><input '.$checked.' type="checkbox" class="check_vat" name="check_vat[]" value="1"> VAT</td></tr>';



                    $str .= '<tr><td>Nội dung</td>';



                    $str .= '<td><textarea class="comment" name="comment[]">'.$v->comment.'</textarea></td>';



                    $str .= '<td>Người nhận <a target="_blank" title="Thêm người nhận" href="'.BASE_URL.'/customer/newcus"><i class="fa fa-plus"></i></a></td>';



                    $str .= '<td><input type="text" autocomplete="off" class="receiver" name="receiver[]" placeholder="Nhập tên hoặc * để chọn" value="'.$v->customer_name.'" data="'.$v->customer_id.'" >';

                    $str .= '<ul class="name_list_id_3"></ul></td></tr>';



                    $str .= '<tr><td>Số Hóa đơn chứng từ</td>';



                    $str .= '<td><input type="text" class="cost_document" name="cost_document[]" style="width:100px" value="'.$v->cost_document.'" > Ngày <input type="text" class="cost_document_date ngay" name="cost_document_date[]" style="width:60px" value="'.($v->cost_document_date>0?date('d-m-Y',$v->cost_document_date):null).'"></td>';



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

        $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

        $info_model = $this->model->get('infoModel');
        $infos = $info_model->getLastInfo();

        $customer_model = $this->model->get('customerModel');
        $customers = $customer_model->getAllCustomer(array('order_by'=>'customer_name','order'=>'ASC'));
        $customer_data = array();
        foreach ($customers as $customer) {
            $customer_data['customer_id'][$customer->customer_id] = $customer->customer_id;
            $customer_data['customer_name'][$customer->customer_id] = $customer->customer_name;
        }

        $house_model = $this->model->get('houseModel');

        $houses = $house_model->getAllHouse();

        $import_model = $this->model->get('importstockModel');
        $spare_stock_model = $this->model->get('sparestockModel');

        $join = array('table'=>'user, house','where'=>'import_stock_user=user_id AND house = house_id');



        $data = array(

            'where' => 'import_stock_date >= '.$batdau.' AND import_stock_date < '.$ngayketthuc,

            );




        $data['order_by'] = 'import_stock_date';

        $data['order'] = 'ASC';




            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();

        $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

        
            foreach ($houses as $house) {
                
                $data = array(

                'where' => 'house = '.$house->house_id.' AND import_stock_date >= '.$batdau.' AND import_stock_date < '.$ngayketthuc,

                );

                $imports = $import_model->getAllStock($data,$join);

                $objPHPExcel->createSheet();

                $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', mb_strtoupper($infos->info_company, "UTF-8"))

                ->setCellValue('A2', 'PHÒNG VẬT TƯ KỸ THUẬT')

                ->setCellValue('E1', 'CỘNG HÒA XÃ CHỦ NGHĨA VIỆT NAM')

                ->setCellValue('E2', 'Độc lập - Tự do - Hạnh phúc')

                ->setCellValue('A4', 'BẢNG TỔNG HỢP PHIẾU NHẬP KHO')

                ->setCellValue('A6', 'STT')

               ->setCellValue('B6', 'NGÀY')

               ->setCellValue('C6', 'PHIẾU NHẬP KHO')

               ->setCellValue('D6', 'NỘI DUNG')

               ->setCellValue('E6', 'NGƯỜI GIAO HÀNG')

               ->setCellValue('F6', 'SỐ HÓA ĐƠN')

               ->setCellValue('G6', 'ĐƠN VỊ BÁN');

              


            if ($imports) {



                $hang = 7;

                $i=1;



                $k=0;
                foreach ($imports as $row) {


                        $spares = $spare_stock_model->getAllStock(array('where'=>'import_stock = '.$row->import_stock_id),array('table'=>'spare_part, spare_part_code','where'=>'spare_part = spare_part_id AND code_list = spare_part_code_id'));




                        //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                         $objPHPExcel->setActiveSheetIndex($index_worksheet)

                            ->setCellValue('A' . $hang, $i++)

                            ->setCellValueExplicit('B' . $hang, $this->lib->hien_thi_ngay_thang($row->import_stock_date))

                            ->setCellValue('C' . $hang, $row->import_stock_code)

                            ->setCellValue('D' . $hang, $row->import_stock_comment)

                            ->setCellValue('E' . $hang, $row->deliver)

                            ->setCellValue('F' . $hang, $row->invoice_number)

                            ->setCellValue('G' . $hang, (isset($customer_data['customer_name'][$row->invoice_customer])?$customer_data['customer_name'][$row->invoice_customer]:null));

                        $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':G'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':G'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':G'.$hang)->getFont()->setBold(true);

                         $hang++;

                         $j=1;

                        foreach ($spares as $spare) {
                            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                            ->setCellValue('A' . $hang, $j++)

                            ->setCellValueExplicit('B' . $hang, $spare->code)

                            ->setCellValue('C' . $hang, $spare->name)

                            ->setCellValue('D' . $hang, $spare->spare_part_seri)

                            ->setCellValue('E' . $hang, $spare->spare_stock_number)

                            ->setCellValue('F' . $hang, $spare->spare_stock_price+($spare->spare_stock_price*($spare->spare_stock_vat_percent/100)))

                            ->setCellValue('G' . $hang, "=E".$hang."*F".$hang);

                            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':G'.$hang)->getFont()->setItalic(true);

                            $hang++;
                        }




                }

            }



            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('B'.$hang, 'TỔNG')


               ->setCellValue('E'.$hang, '=SUM(E7:E'.($hang-1).')')

               ->setCellValue('G'.$hang, '=SUM(G7:G'.($hang-1).')');

            


            $objPHPExcel->getActiveSheet()->getStyle('A6:G'.$hang)->applyFromArray(

                array(

                    

                    'borders' => array(

                        'allborders' => array(

                          'style' => PHPExcel_Style_Border::BORDER_THIN

                        )

                    )

                )

            );


            $highestColumn = $objPHPExcel->getActiveSheet()->getHighestDataColumn();

            



            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.($hang+3), 'NGƯỜI LẬP BIỂU')

                ->setCellValue('E'.($hang+3), mb_strtoupper($infos->info_company, "UTF-8"));



            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+3).':C'.($hang+3));

            $objPHPExcel->getActiveSheet()->mergeCells('E'.($hang+3).':G'.($hang+3));


            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':G'.($hang+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':G'.($hang+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang).':G'.($hang+3))->applyFromArray(

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

            $objPHPExcel->getActiveSheet()->mergeCells('E1:G1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:C2');

            $objPHPExcel->getActiveSheet()->mergeCells('E2:G2');

            $objPHPExcel->getActiveSheet()->mergeCells('A4:G4');



            $objPHPExcel->getActiveSheet()->getStyle('A1:G4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:G4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(16);



            $objPHPExcel->getActiveSheet()->getStyle('A1:G4')->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('A2:C2')->applyFromArray(

                array(

                    

                    'font' => array(

                        'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('E7:G'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A6:G6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:G6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:G6')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(26);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(25);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);

            //$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);



            

            $objPHPExcel->getActiveSheet()->setTitle($house->house_name);



            $objPHPExcel->getActiveSheet()->freezePane('A7');

            $objPHPExcel->setActiveSheetIndex($index_worksheet);

            $index_worksheet++;
            }
        
        
            







            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Sale Report")

                            ->setSubject("Sale Report")

                            ->setDescription("Sale Report.")

                            ->setKeywords("Sale Report")

                            ->setCategory("Sale Report");



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BẢNG TỔNG HỢP PHIẾU NHẬP KHO.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }


}

?>