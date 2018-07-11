<?php

Class importstockController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý phiếu nhập kho';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;
            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;
            $nv = isset($_POST['nv']) ? $_POST['nv'] : null;
            $tha = isset($_POST['tha']) ? $_POST['tha'] : null;
            $na = isset($_POST['na']) ? $_POST['na'] : null;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'import_stock_date';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

            $batdau = '01/'.date('m/Y');
            $ketthuc = date('t/m/Y');
            $nv = 1;
            $tha = date('m');
            $na = date('Y');

        }

        $ngaybatdau = strtotime(str_replace('/', '-', $batdau));
        $ngayketthuc = strtotime(str_replace('/', '-', $ketthuc). ' + 1 days');
        $tha = (int)date('m',$ngaybatdau);
        $na = (int)date('Y',$ngaybatdau);
        $nv = ceil($tha/3);


        $import_stock_model = $this->model->get('importstockModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        if ($batdau!="") {
            $data['where'] .= ' AND import_stock_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND import_stock_date < '.$ngayketthuc;
        }

        

        if (isset($_POST['filter'])) {
            
            if (isset($_POST['import_stock_house'])) {
                $data['where'] .= ' AND import_stock_house IN ('.implode(',',$_POST['import_stock_house']).')';
            }
            if (isset($_POST['import_stock_customer'])) {
                $data['where'] .= ' AND import_stock_customer IN ('.implode(',',$_POST['import_stock_customer']).')';
            }
            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($import_stock_model->getAllStock($data));

        $tongsotrang = ceil($tongsodong / $sonews);

        



        $this->view->data['page'] = $page;

        $this->view->data['order_by'] = $order_by;

        $this->view->data['order'] = $order;

        $this->view->data['keyword'] = $keyword;

        $this->view->data['limit'] = $limit;

        $this->view->data['pagination_stages'] = $pagination_stages;

        $this->view->data['tongsotrang'] = $tongsotrang;

        $this->view->data['sonews'] = $sonews;

        $this->view->data['batdau'] = $batdau;
        $this->view->data['ketthuc'] = $ketthuc;
        $this->view->data['nv'] = $nv;
        $this->view->data['tha'] = $tha;
        $this->view->data['na'] = $na;



        $data = array(
            'where'=>'1=1',

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            );

        if ($batdau!="") {
            $data['where'] .= ' AND import_stock_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND import_stock_date < '.$ngayketthuc;
        }

        if (isset($_POST['filter'])) {
            
            if (isset($_POST['import_stock_house'])) {
                $data['where'] .= ' AND import_stock_house IN ('.implode(',',$_POST['import_stock_house']).')';
            }
            if (isset($_POST['import_stock_customer'])) {
                $data['where'] .= ' AND import_stock_customer IN ('.implode(',',$_POST['import_stock_customer']).')';
            }
        }

        if ($keyword != '') {

            $search = '( import_stock_code  LIKE "%'.$keyword.'%" OR import_stock_comment  LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }

        $importstocks = $import_stock_model->getAllStock($data);;

        $this->view->data['importstocks'] = $importstocks;


        return $this->view->show('importstock/index');

    }


    public function addimportstock(){
        $import_stock_model = $this->model->get('importstockModel');

        if (isset($_POST['import_stock_code'])) {
            if($import_stock_model->getStockByWhere(array('import_stock_code'=>trim($_POST['import_stock_code'])))){
                echo 'Số phiếu đã tồn tại';
                return false;
            }
            

            $data = array(
                'import_stock_date' => strtotime(str_replace('/', '-', $_POST['import_stock_date'])),
                'import_stock_code' => trim($_POST['import_stock_code']),
                'import_stock_customer' => trim($_POST['import_stock_customer']),
                'import_stock_comment' => trim($_POST['import_stock_comment']),
                'import_stock_invoice_number' => trim($_POST['import_stock_invoice_number']),
                'import_stock_invoice_date' => strtotime(str_replace('/', '-', $_POST['import_stock_invoice_date'])),
                'import_stock_deliver' => trim($_POST['import_stock_deliver']),
                'import_stock_deliver_address' => trim($_POST['import_stock_deliver_address']),
                'import_stock_house' => trim($_POST['import_stock_house']),
                'import_stock_create_user' => $_SESSION['userid_logined'],
            );
            $import_stock_model->createStock($data);

            $id_importstock = $import_stock_model->getLastStock()->import_stock_id;

            $total = 0;
            $price = 0;
            $vat = 0;

            $spare_code_model = $this->model->get('sparepartcodeModel');
            $spare_model = $this->model->get('sparepartModel');
            $spare_stock_model = $this->model->get('sparestockModel');

            $spare_stock_data = json_decode($_POST['spare_part']);

            if (isset($id_importstock)) {
                foreach ($spare_stock_data as $v) {
                    if (isset($v->spare_part_code_id) && $v->spare_part_code_id > 0) {
                        $id_code = $v->spare_part_code_id;
                    }
                    else{
                        $code_data = array(
                            'code'=>trim($v->spare_part_code),
                            'name'=>trim($v->spare_part_name),
                        );

                        if (!$spare_code_model->getStockByWhere(array('code'=>$code_data['code'],'name'=>$code_data['name']))) {
                            $spare_code_model->createStock($code_data);
                            $id_code = $spare_code_model->getLastStock()->spare_part_code_id;
                        }
                        else{
                            $id_code = $spare_code_model->getStockByWhere(array('code'=>$code_data['code'],'name'=>$code_data['name']))->spare_part_code_id;
                        }
                    }

                    if (isset($v->spare_part_id) && $v->spare_part_id > 0) {
                        $id_spare_part = $v->spare_part_id;
                    }
                    else{
                        $data_spare_part = array(
                            'spare_part_name' => trim($v->spare_part_name),
                            'spare_part_code' => $id_code,
                            'spare_part_seri' => trim($v->spare_part_seri),
                            'spare_part_brand' => trim($v->spare_part_brand),
                            'spare_part_unit' => trim($v->spare_part_unit),
                            'spare_part_date_manufacture' => strtotime(str_replace('/', '-', $v->spare_part_date_manufacture)),
                        );

                        if ($data_spare_part['spare_part_seri'] != "") {
                            if (!$spare_model->getStockByWhere(array('spare_part_seri'=>$data_spare_part['spare_part_seri'],'spare_part_code'=>$id_code))) {
                                $spare_model->createStock($data_spare_part);
                                $id_spare_part = $spare_model->getLastStock()->spare_part_id;
                            }
                            else{
                                $id_spare_part = $spare_model->getStockByWhere(array('spare_part_seri'=>$data_spare_part['spare_part_seri'],'spare_part_code'=>$id_code))->spare_part_id;
                            }
                        }
                        else{
                            if (!$spare_model->getStockByWhere(array('spare_part_name'=>$data_spare_part['spare_part_name'],'spare_part_code'=>$id_code))) {
                                $spare_model->createStock($data_spare_part);
                                $id_spare_part = $spare_model->getLastStock()->spare_part_id;
                            }
                            else{
                                $id_spare_part = $spare_model->getStockByWhere(array('spare_part_name'=>$data_spare_part['spare_part_name'],'spare_part_code'=>$id_code))->spare_part_id;
                            }
                        }
                    }

                    $data_spare_stock = array(
                        'import_stock' => $id_importstock,
                        'spare_part' => $id_spare_part,
                        'spare_stock_code' => $id_code,
                        'spare_stock_unit' => trim($v->spare_part_unit),
                        'spare_stock_number' => str_replace(',', '', $v->spare_stock_number),
                        'spare_stock_price' => str_replace(',', '', $v->spare_stock_price),
                        'spare_stock_vat_percent' => trim($v->spare_stock_vat_percent),
                        'spare_stock_vat_price' => str_replace(',', '', $v->spare_stock_vat_price),
                        'spare_stock_date' => $data['import_stock_date'],
                    );

                    if ($v->id_spare_stock>0) {
                        $spare_stock_model->updateStock($data_spare_stock,array('spare_stock_id'=>$v->id_spare_stock));

                        $total += $data_spare_stock['spare_stock_number'];
                        $price += $data_spare_stock['spare_stock_number']*$data_spare_stock['spare_stock_price'];
                        $vat += $data_spare_stock['spare_stock_vat_price'];
                    }
                    else{
                        if ($data_spare_stock['spare_stock_number']!="") {
                            $spare_stock_model->createStock($data_spare_stock);

                            $total += $data_spare_stock['spare_stock_number'];
                            $price += $data_spare_stock['spare_stock_number']*$data_spare_stock['spare_stock_price'];
                            $vat += $data_spare_stock['spare_stock_vat_price'];
                        }
                        
                    }
                }

            }

            $import_stock_model->updateStock(array('import_stock_total'=>$total,'import_stock_price'=>$price,'import_stock_vat'=>$vat),array('import_stock_id'=>$id_importstock));

            $import_stock_cost_model = $this->model->get('importstockcostModel');

            $import_stock_cost = json_decode($_POST['import_stock_cost']);
            
            if (isset($id_importstock)) {
                foreach ($import_stock_cost as $v) {
                    $data_import_stock_cost = array(
                        'import_stock' => $id_importstock,
                        'import_stock_cost_list' => trim($v->import_stock_cost_list),
                        'import_stock_cost_comment' => trim($v->import_stock_cost_comment),
                        'import_stock_cost_money' => str_replace(',', '', $v->import_stock_cost_money),
                        'import_stock_cost_money_vat' => trim($v->import_stock_cost_money_vat),
                        'import_stock_cost_customer' => trim($v->import_stock_cost_customer),
                        'import_stock_cost_invoice' => trim($v->import_stock_cost_invoice),
                        'import_stock_cost_invoice_date' => strtotime(str_replace('/', '-', $v->import_stock_cost_invoice_date)),
                    );

                    if ($v->id_import_stock_cost>0) {
                        $data_import_stock_cost['import_stock_cost_update_user'] = $_SESSION['userid_logined'];
                        $import_stock_cost_model->updateStock($data_import_stock_cost,array('import_stock_cost_id'=>$v->id_import_stock_cost));
                    }
                    else{
                        if ($data_import_stock_cost['import_stock_cost_money']!="") {
                            $data_import_stock_cost['import_stock_cost_create_user'] = $_SESSION['userid_logined'];
                            $import_stock_cost_model->createStock($data_import_stock_cost);
                        }
                        
                    }
                    
                }
            }

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$id_importstock."|import_stock|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'import_stock',
                'user_log_table_name' => 'Phiếu nhập kho',
                'user_log_action' => 'Thêm mới',
                'user_log_data' => json_encode($data),
            );
            $user_log_model->createUser($data_log);


            echo "Thêm thành công";
        }

    }

    public function add(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->importstock) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới phiếu nhập kho';

        $import_stock_model = $this->model->get('importstockModel');
        $lastID = isset($import_stock_model->getLastStock()->import_stock_code)?$import_stock_model->getLastStock()->import_stock_code:'PNK00';
        $lastID++;
        $this->view->data['lastID'] = $lastID;


        $house = $this->model->get('houseModel');

        $this->view->data['houses'] = $house->getAllHouse(array('order_by'=>'house_name','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('order_by'=>'customer_name','order'=>'ASC'));

        $costlist_model = $this->model->get('costlistModel');

        $cost_lists = $costlist_model->getAllCost(array('order_by'=>'cost_list_name','order'=>'ASC'));

        $this->view->data['cost_lists'] = $cost_lists;


        return $this->view->show('importstock/add');
    }

    public function editimportstock(){
        $import_stock_model = $this->model->get('importstockModel');

        if (isset($_POST['import_stock_id'])) {
            $id = $_POST['import_stock_id'];
            if($import_stock_model->getAllStockByWhere($id.' AND import_stock_code = '.trim($_POST['import_stock_code']))){
                echo 'Số phiếu đã tồn tại';
                return false;
            }

            $data = array(
                'import_stock_date' => strtotime(str_replace('/', '-', $_POST['import_stock_date'])),
                'import_stock_code' => trim($_POST['import_stock_code']),
                'import_stock_customer' => trim($_POST['import_stock_customer']),
                'import_stock_comment' => trim($_POST['import_stock_comment']),
                'import_stock_invoice_number' => trim($_POST['import_stock_invoice_number']),
                'import_stock_invoice_date' => strtotime(str_replace('/', '-', $_POST['import_stock_invoice_date'])),
                'import_stock_deliver' => trim($_POST['import_stock_deliver']),
                'import_stock_deliver_address' => trim($_POST['import_stock_deliver_address']),
                'import_stock_house' => trim($_POST['import_stock_house']),
                'import_stock_update_user' => $_SESSION['userid_logined'],
            );
            $import_stock_model->updateStock($data,array('import_stock_id'=>$id));

            $id_importstock = $id;

            $total = 0;
            $price = 0;
            $vat = 0;

            $spare_code_model = $this->model->get('sparepartcodeModel');
            $spare_model = $this->model->get('sparepartModel');
            $spare_stock_model = $this->model->get('sparestockModel');

            $spare_stock_data = json_decode($_POST['spare_part']);

            if (isset($id_importstock)) {
                foreach ($spare_stock_data as $v) {
                    if (isset($v->spare_part_code_id) && $v->spare_part_code_id > 0) {
                        $id_code = $v->spare_part_code_id;
                    }
                    else{
                        $code_data = array(
                            'code'=>trim($v->spare_part_code),
                            'name'=>trim($v->spare_part_name),
                        );

                        if (!$spare_code_model->getStockByWhere(array('code'=>$code_data['code'],'name'=>$code_data['name']))) {
                            $spare_code_model->createStock($code_data);
                            $id_code = $spare_code_model->getLastStock()->spare_part_code_id;
                        }
                        else{
                            $id_code = $spare_code_model->getStockByWhere(array('code'=>$code_data['code'],'name'=>$code_data['name']))->spare_part_code_id;
                        }
                    }

                    if (isset($v->spare_part_id) && $v->spare_part_id > 0) {
                        $id_spare_part = $v->spare_part_id;
                    }
                    else{
                        $data_spare_part = array(
                            'spare_part_name' => trim($v->spare_part_name),
                            'spare_part_code' => $id_code,
                            'spare_part_seri' => trim($v->spare_part_seri),
                            'spare_part_brand' => trim($v->spare_part_brand),
                            'spare_part_unit' => trim($v->spare_part_unit),
                            'spare_part_date_manufacture' => strtotime(str_replace('/', '-', $v->spare_part_date_manufacture)),
                        );

                        if ($data_spare_part['spare_part_seri'] != "") {
                            if (!$spare_model->getStockByWhere(array('spare_part_seri'=>$data_spare_part['spare_part_seri'],'spare_part_code'=>$id_code))) {
                                $spare_model->createStock($data_spare_part);
                                $id_spare_part = $spare_model->getLastStock()->spare_part_id;
                            }
                            else{
                                $id_spare_part = $spare_model->getStockByWhere(array('spare_part_seri'=>$data_spare_part['spare_part_seri'],'spare_part_code'=>$id_code))->spare_part_id;
                            }
                        }
                        else{
                            if (!$spare_model->getStockByWhere(array('spare_part_name'=>$data_spare_part['spare_part_name'],'spare_part_code'=>$id_code))) {
                                $spare_model->createStock($data_spare_part);
                                $id_spare_part = $spare_model->getLastStock()->spare_part_id;
                            }
                            else{
                                $id_spare_part = $spare_model->getStockByWhere(array('spare_part_name'=>$data_spare_part['spare_part_name'],'spare_part_code'=>$id_code))->spare_part_id;
                            }
                        }
                    }

                    $data_spare_stock = array(
                        'import_stock' => $id_importstock,
                        'spare_part' => $id_spare_part,
                        'spare_stock_code' => $id_code,
                        'spare_stock_unit' => trim($v->spare_part_unit),
                        'spare_stock_number' => str_replace(',', '', $v->spare_stock_number),
                        'spare_stock_price' => str_replace(',', '', $v->spare_stock_price),
                        'spare_stock_vat_percent' => trim($v->spare_stock_vat_percent),
                        'spare_stock_vat_price' => str_replace(',', '', $v->spare_stock_vat_price),
                        'spare_stock_date' => $data['import_stock_date'],
                    );

                    if ($v->id_spare_stock>0) {
                        $spare_stock_model->updateStock($data_spare_stock,array('spare_stock_id'=>$v->id_spare_stock));

                        $total += $data_spare_stock['spare_stock_number'];
                        $price += $data_spare_stock['spare_stock_number']*$data_spare_stock['spare_stock_price'];
                        $vat += $data_spare_stock['spare_stock_vat_price'];
                    }
                    else{
                        if ($data_spare_stock['spare_stock_number']!="") {
                            $spare_stock_model->createStock($data_spare_stock);

                            $total += $data_spare_stock['spare_stock_number'];
                            $price += $data_spare_stock['spare_stock_number']*$data_spare_stock['spare_stock_price'];
                            $vat += $data_spare_stock['spare_stock_vat_price'];
                        }
                        
                    }
                }

            }

            $import_stock_model->updateStock(array('import_stock_total'=>$total,'import_stock_price'=>$price,'import_stock_vat'=>$vat),array('import_stock_id'=>$id_importstock));

            $import_stock_cost_model = $this->model->get('importstockcostModel');

            $import_stock_cost = json_decode($_POST['import_stock_cost']);
            
            if (isset($id_importstock)) {
                foreach ($import_stock_cost as $v) {
                    $data_import_stock_cost = array(
                        'import_stock' => $id_importstock,
                        'import_stock_cost_list' => trim($v->import_stock_cost_list),
                        'import_stock_cost_comment' => trim($v->import_stock_cost_comment),
                        'import_stock_cost_money' => str_replace(',', '', $v->import_stock_cost_money),
                        'import_stock_cost_money_vat' => trim($v->import_stock_cost_money_vat),
                        'import_stock_cost_customer' => trim($v->import_stock_cost_customer),
                        'import_stock_cost_invoice' => trim($v->import_stock_cost_invoice),
                        'import_stock_cost_invoice_date' => strtotime(str_replace('/', '-', $v->import_stock_cost_invoice_date)),
                    );

                    if ($v->id_import_stock_cost>0) {
                        $data_import_stock_cost['import_stock_cost_update_user'] = $_SESSION['userid_logined'];
                        $import_stock_cost_model->updateStock($data_import_stock_cost,array('import_stock_cost_id'=>$v->id_import_stock_cost));
                    }
                    else{
                        if ($data_import_stock_cost['import_stock_cost_money']!="") {
                            $data_import_stock_cost['import_stock_cost_create_user'] = $_SESSION['userid_logined'];
                            $import_stock_cost_model->createStock($data_import_stock_cost);
                        }
                        
                    }
                    
                }
            }

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|import_stock|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'import_stock',
                'user_log_table_name' => 'Phiếu nhập kho',
                'user_log_action' => 'Cập nhật',
                'user_log_data' => json_encode($data),
            );
            $user_log_model->createUser($data_log);


            echo "Cập nhật thành công";
        }
    }

    public function edit($id){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->importstock) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('importstock');

        }

        $this->view->data['title'] = 'Cập nhật phiếu nhập kho';
        $this->view->data['lib'] = $this->lib;

        $import_stock_model = $this->model->get('importstockModel');

        $import_stock_data = $import_stock_model->getStock($id);

        $this->view->data['import_stock_data'] = $import_stock_data;

        if (!$import_stock_data) {

            $this->view->redirect('importstock');

        }

        $spare_part_code_model = $this->model->get('sparepartcodeModel');
        $spare_part_model = $this->model->get('sparepartModel');

        $spare_part_codes = $spare_part_code_model->getAllStock(array('where'=>'spare_part_code_id IN (SELECT spare_stock_code FROM spare_stock WHERE import_stock='.$id.')'));
        $this->view->data['spare_part_codes'] = $spare_part_codes;

        $spare_parts = array();
        foreach ($spare_part_codes as $spare_part_code) {
            $spare_parts[$spare_part_code->spare_part_code_id] = $spare_part_model->getAllStock(array('where'=>'spare_part_id IN (SELECT spare_part FROM spare_stock WHERE spare_stock_code='.$spare_part_code->spare_part_code_id.' AND import_stock='.$id.')'));
        }
        $this->view->data['spare_parts'] = $spare_parts;

        $import_stock_cost_model = $this->model->get('importstockcostModel');

        $join = array('table'=>'cost_list,customer','where'=>'import_stock_cost_customer=customer_id AND import_stock_cost_list=cost_list_id');
        $import_stock_costs = $import_stock_cost_model->getAllStock(array('where'=>'import_stock='.$id),$join);
        $this->view->data['import_stock_costs'] = $import_stock_costs;

        $house = $this->model->get('houseModel');

        $this->view->data['houses'] = $house->getAllHouse(array('order_by'=>'house_name','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('order_by'=>'customer_name','order'=>'ASC'));

        $costlist_model = $this->model->get('costlistModel');

        $cost_lists = $costlist_model->getAllCost(array('order_by'=>'cost_list_name','order'=>'ASC'));

        $this->view->data['cost_lists'] = $cost_lists;

        return $this->view->show('importstock/edit');

    }

    public function view($id){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('importstock');

        }

        $this->view->data['title'] = 'Cập nhật phiếu nhập kho';
        $this->view->data['lib'] = $this->lib;

        $import_stock_model = $this->model->get('importstockModel');

        $import_stock_data = $import_stock_model->getStock($id);

        $this->view->data['import_stock_data'] = $import_stock_data;

        if (!$import_stock_data) {

            $this->view->redirect('importstock');

        }

        $spare_stock_model = $this->model->get('sparestockModel');

        $spare_stocks = $spare_stock_model->getAllStock(array('where'=>'import_stock='.$id));
        $this->view->data['spare_stocks'] = $spare_stocks;

        $house = $this->model->get('houseModel');

        $this->view->data['houses'] = $house->getAllHouse(array('order_by'=>'house_name','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('where'=>'customer_type=2','order_by'=>'customer_name','order'=>'ASC'));

        $costlist_model = $this->model->get('costlistModel');

        $cost_lists = $costlist_model->getAllCost(array('order_by'=>'cost_list_name','order'=>'ASC'));

        $this->view->data['cost_lists'] = $cost_lists;

        return $this->view->show('importstock/view');

    }

    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $house = $this->model->get('houseModel');

        $this->view->data['houses'] = $house->getAllHouse(array('order_by'=>'house_name','order'=>'ASC'));

        $customer = $this->model->get('customerModel');

        $this->view->data['customers'] = $customer->getAllCustomer(array('where'=>'customer_type=2','order_by'=>'customer_name','order'=>'ASC'));

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('importstock/filter');
    }

    public function getimportstock(){
        $import_stock_model = $this->model->get('importstockModel');

        $importstocks = $import_stock_model->getAllStock(array('order_by'=>'import_stock_code','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($importstocks as $importstock) {
            $result[$i]['id'] = $importstock->import_stock_id;
            $result[$i]['text'] = $importstock->import_stock_code;
            $i++;
        }
        echo json_encode($result);
    }

    public function deleteimportstockdetail(){
        if (isset($_POST['data'])) {
            $spare_stock_model = $this->model->get('sparestockModel');
            $user_log_model = $this->model->get('userlogModel');

            $spare_stock_model->queryStock('DELETE FROM spare_stock WHERE spare_stock_id='.$_POST['data'].' AND import_stock='.$_POST['importstock']);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|spare_stock|"."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);

            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'spare_stock',
                'user_log_table_name' => 'Chi tiết phiếu nhập kho',
                'user_log_action' => 'Xóa',
                'user_log_data' => json_encode($_POST['data']),
            );
            $user_log_model->createUser($data_log);
        }
    }
    public function deleteimportstockcost(){
        if (isset($_POST['data'])) {
            $import_stock_cost_model = $this->model->get('importstockcostModel');
            $user_log_model = $this->model->get('userlogModel');

            $import_stock_cost_model->queryStock('DELETE FROM import_stock_cost WHERE import_stock_cost_id='.$_POST['data'].' AND import_stock='.$_POST['importstock']);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|import_stock_cost|"."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);

            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'import_stock_cost',
                'user_log_table_name' => 'Chi phí nhập kho',
                'user_log_action' => 'Xóa',
                'user_log_data' => json_encode($_POST['data']),
            );
            $user_log_model->createUser($data_log);
        }
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->importstock) || json_decode($_SESSION['user_permission_action'])->importstock != "importstock") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $import_stock_model = $this->model->get('importstockModel');
            $spare_stock_model = $this->model->get('sparestockModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $import_stock_model->deleteStock($data);

                    $spare_stock_model->queryStock('DELETE FROM spare_stock WHERE import_stock='.$data);

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|import_stock|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'import_stock',
                    'user_log_table_name' => 'Phiếu nhập kho',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $import_stock_model->deleteStock($_POST['data']);

                $spare_stock_model->queryStock('DELETE FROM spare_stock WHERE import_stock='.$_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|import_stock|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'import_stock',
                    'user_log_table_name' => 'Phiếu nhập kho',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importimportstock(){
        if (isset($_FILES['import']['name'])) {
            $total = count($_FILES['import']['name']);
            for( $i=0 ; $i < $total ; $i++ ) {
              $tmpFilePath = $_FILES['import']['name'][$i];
              echo $tmpFilePath;
            }
        }
    }
    public function import(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->importstock) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('importstock/import');

    }


}

?>