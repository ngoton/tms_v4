<?php

Class exportstockController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý phiếu xuất kho';



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

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'export_stock_date';

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


        $export_stock_model = $this->model->get('exportstockModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        if ($batdau!="") {
            $data['where'] .= ' AND export_stock_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND export_stock_date < '.$ngayketthuc;
        }

        

        if (isset($_POST['filter'])) {
            
            if (isset($_POST['export_stock_house'])) {
                $data['where'] .= ' AND export_stock_house IN ('.implode(',',$_POST['export_stock_house']).')';
            }
            if (isset($_POST['export_stock_spare'])) {
                $data['where'] .= ' AND export_stock_id IN (SELECT export_stock FROM spare_stock WHERE spare_stock_code IN ('.implode(',',$_POST['export_stock_spare']).'))';
            }
            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($export_stock_model->getAllStock($data));

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
            $data['where'] .= ' AND export_stock_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND export_stock_date < '.$ngayketthuc;
        }

        if (isset($_POST['filter'])) {
            
            if (isset($_POST['export_stock_house'])) {
                $data['where'] .= ' AND export_stock_house IN ('.implode(',',$_POST['export_stock_house']).')';
            }
            if (isset($_POST['export_stock_spare'])) {
                $data['where'] .= ' AND export_stock_id IN (SELECT export_stock FROM spare_stock WHERE spare_stock_code IN ('.implode(',',$_POST['export_stock_spare']).'))';
            }
        }

        if ($keyword != '') {

            $search = '( export_stock_code  LIKE "%'.$keyword.'%" OR export_stock_comment  LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }

        $exportstocks = $export_stock_model->getAllStock($data);;

        $this->view->data['exportstocks'] = $exportstocks;


        return $this->view->show('exportstock/index');

    }


    public function addexportstock(){
        $export_stock_model = $this->model->get('exportstockModel');

        if (isset($_POST['export_stock_code'])) {
            if($export_stock_model->getStockByWhere(array('export_stock_code'=>trim($_POST['export_stock_code'])))){
                echo 'Số phiếu đã tồn tại';
                return false;
            }
            

            $data = array(
                'export_stock_date' => strtotime(str_replace('/', '-', $_POST['export_stock_date'])),
                'export_stock_code' => trim($_POST['export_stock_code']),
                'export_stock_comment' => trim($_POST['export_stock_comment']),
                'export_stock_house' => trim($_POST['export_stock_house']),
                'export_stock_create_user' => $_SESSION['userid_logined'],
            );
            $export_stock_model->createStock($data);

            $id_exportstock = $export_stock_model->getLastStock()->export_stock_id;

            $total = 0;
            $price = 0;
            $vat = 0;
            $arr = array();

            $spare_stock_model = $this->model->get('sparestockModel');

            $spare_stock_data = json_decode($_POST['spare_part']);

            if (isset($id_exportstock)) {
                foreach ($spare_stock_data as $v) {
                    if(is_array($v->spare_part_id)){
                        if ($v->spare_stock_number == count($v->spare_part_id)) {
                            $num = 1;
                        }
                        else{
                            $num = str_replace(',', '', $v->spare_stock_number);
                        }
                        foreach ($v->spare_part_id as $key) {
                            $id_spare_part = $key;

                            $data_spare_stock = array(
                                'export_stock' => $id_exportstock,
                                'spare_part' => $id_spare_part,
                                'spare_stock_code' => $v->spare_part_code_id,
                                'spare_stock_unit' => trim($v->spare_part_unit),
                                'spare_stock_number' => $num,
                                'spare_stock_price' => str_replace(',', '', $v->spare_stock_price),
                                'spare_stock_vat_percent' => trim($v->spare_stock_vat_percent),
                                'spare_stock_vat_price' => str_replace(',', '', $v->spare_stock_vat_price),
                                'spare_stock_date' => $data['export_stock_date'],
                                
                            );

                            if (!$spare_stock_model->getStockByWhere(array('export_stock'=>$id_exportstock,'spare_part'=>$id_spare_part))) {
                                $spare_stock_model->createStock($data_spare_stock);
                            }
                            else{
                                $id_stock = $spare_stock_model->getStockByWhere(array('export_stock'=>$id_exportstock,'spare_part'=>$id_spare_part))->spare_stock_id;
                                $spare_stock_model->updateStock($data_spare_stock,array('spare_stock_id'=>$id_stock));
                            }

                            $total += $data_spare_stock['spare_stock_number'];
                            $price += $data_spare_stock['spare_stock_price']*$data_spare_stock['spare_stock_number'];
                            $vat += $data_spare_stock['spare_stock_vat_price'];

                            $arr[] = $id_spare_part;
                        }
                    }
                }
                $old_stock = $spare_stock_model->getAllStock(array('where'=>'export_stock = '.$id_exportstock));
                if($old_stock){
                    foreach ($old_stock as $old) {
                        if(!in_array($old->spare_part, $arr)){
                            $spare_stock_model->queryStock('DELETE FROM spare_stock WHERE export_stock = '.$old->export_stock.' AND spare_part = '.$old->spare_part);
                        }
                    }
                }

            }

            $export_stock_model->updateStock(array('export_stock_total'=>$total,'export_stock_price'=>$price,'export_stock_vat'=>$vat),array('export_stock_id'=>$id_exportstock));

            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$id_exportstock."|export_stock|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'export_stock',
                'user_log_table_name' => 'Phiếu xuất kho',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->exportstock) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới phiếu xuất kho';

        $export_stock_model = $this->model->get('exportstockModel');
        $lastID = isset($export_stock_model->getLastStock()->export_stock_code)?$export_stock_model->getLastStock()->export_stock_code:'PNK00';
        $lastID++;
        $this->view->data['lastID'] = $lastID;


        $house = $this->model->get('houseModel');

        $this->view->data['houses'] = $house->getAllHouse(array('order_by'=>'house_name','order'=>'ASC'));


        return $this->view->show('exportstock/add');
    }

    public function editexportstock(){
        $export_stock_model = $this->model->get('exportstockModel');

        if (isset($_POST['export_stock_id'])) {
            $id = $_POST['export_stock_id'];
            if($export_stock_model->getAllStockByWhere($id.' AND export_stock_code = '.trim($_POST['export_stock_code']))){
                echo 'Số phiếu đã tồn tại';
                return false;
            }

            $data = array(
                'export_stock_date' => strtotime(str_replace('/', '-', $_POST['export_stock_date'])),
                'export_stock_code' => trim($_POST['export_stock_code']),
                'export_stock_comment' => trim($_POST['export_stock_comment']),
                'export_stock_house' => trim($_POST['export_stock_house']),
                'export_stock_update_user' => $_SESSION['userid_logined'],
            );
            $export_stock_model->updateStock($data,array('export_stock_id'=>$id));

            $id_exportstock = $id;

            $total = 0;
            $price = 0;
            $vat = 0;
            $arr = array();

            $spare_stock_model = $this->model->get('sparestockModel');

            $spare_stock_data = json_decode($_POST['spare_part']);

            if (isset($id_exportstock)) {
                foreach ($spare_stock_data as $v) {
                    if(is_array($v->spare_part_id)){
                        if ($v->spare_stock_number == count($v->spare_part_id)) {
                            $num = 1;
                        }
                        else{
                            $num = str_replace(',', '', $v->spare_stock_number);
                        }
                        foreach ($v->spare_part_id as $key) {
                            $id_spare_part = $key;

                            $data_spare_stock = array(
                                'export_stock' => $id_exportstock,
                                'spare_part' => $id_spare_part,
                                'spare_stock_code' => $v->spare_part_code_id,
                                'spare_stock_unit' => trim($v->spare_part_unit),
                                'spare_stock_number' => $num,
                                'spare_stock_price' => str_replace(',', '', $v->spare_stock_price),
                                'spare_stock_vat_percent' => trim($v->spare_stock_vat_percent),
                                'spare_stock_vat_price' => str_replace(',', '', $v->spare_stock_vat_price),
                                'spare_stock_date' => $data['export_stock_date'],
                                
                            );

                            if (!$spare_stock_model->getStockByWhere(array('export_stock'=>$id_exportstock,'spare_part'=>$id_spare_part))) {
                                $spare_stock_model->createStock($data_spare_stock);
                            }
                            else{
                                $id_stock = $spare_stock_model->getStockByWhere(array('export_stock'=>$id_exportstock,'spare_part'=>$id_spare_part))->spare_stock_id;
                                $spare_stock_model->updateStock($data_spare_stock,array('spare_stock_id'=>$id_stock));
                            }

                            $total += $data_spare_stock['spare_stock_number'];
                            $price += $data_spare_stock['spare_stock_price']*$data_spare_stock['spare_stock_number'];
                            $vat += $data_spare_stock['spare_stock_vat_price'];

                            $arr[] = $id_spare_part;
                        }
                    }
                }
                $old_stock = $spare_stock_model->getAllStock(array('where'=>'export_stock = '.$id_exportstock));
                if($old_stock){
                    foreach ($old_stock as $old) {
                        if(!in_array($old->spare_part, $arr)){
                            $spare_stock_model->queryStock('DELETE FROM spare_stock WHERE export_stock = '.$old->export_stock.' AND spare_part = '.$old->spare_part);
                        }
                    }
                }

            }

            $export_stock_model->updateStock(array('export_stock_total'=>$total,'export_stock_price'=>$price,'export_stock_vat'=>$vat),array('export_stock_id'=>$id_exportstock));


            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|export_stock|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'export_stock',
                'user_log_table_name' => 'Phiếu xuất kho',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->exportstock) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('exportstock');

        }

        $this->view->data['title'] = 'Cập nhật phiếu xuất kho';
        $this->view->data['lib'] = $this->lib;

        $export_stock_model = $this->model->get('exportstockModel');

        $export_stock_data = $export_stock_model->getStock($id);

        $this->view->data['export_stock_data'] = $export_stock_data;

        if (!$export_stock_data) {

            $this->view->redirect('exportstock');

        }

        $spare_part_code_model = $this->model->get('sparepartcodeModel');
        $spare_part_model = $this->model->get('sparepartModel');
        $spare_stock_model = $this->model->get('sparestockModel');

        $spare_part_codes = $spare_part_code_model->getAllStock(array('where'=>'spare_part_code_id IN (SELECT spare_stock_code FROM spare_stock WHERE export_stock='.$id.')'));
        $this->view->data['spare_part_codes'] = $spare_part_codes;

        $join = array('table'=>'spare_part','where'=>'spare_part = spare_part_id');
        $data_im = array(
            'where' => 'import_stock > 0 AND spare_stock_code IN (SELECT spare_stock_code FROM spare_stock WHERE export_stock = '.$id.')',
        );
        $stock_ims = $spare_stock_model->getAllStock($data_im,$join);

        $data_ex = array(
            'where' => 'export_stock > 0 AND spare_stock_code IN (SELECT spare_stock_code FROM spare_stock WHERE export_stock = '.$id.')',
        );
        $stock_exs = $spare_stock_model->getAllStock($data_ex,$join);
    
        $data_stock = array();
        foreach ($stock_ims as $stock) {
            $data_stock[$stock->spare_part] = isset($data_stock[$stock->spare_part])?$data_stock[$stock->spare_part]+$stock->spare_stock_number:$stock->spare_stock_number;
        }
        foreach ($stock_exs as $stock) {
            $data_stock[$stock->spare_part] = isset($data_stock[$stock->spare_part])?$data_stock[$stock->spare_part]-$stock->spare_stock_number:0-$stock->spare_stock_number;
        }

        $data = array(
            'where' => 'spare_part_code IN (SELECT spare_stock_code FROM spare_stock WHERE export_stock = '.$id.') AND spare_part_id NOT IN (SELECT spare_part FROM spare_stock WHERE export_stock = '.$id.')',
        );

        $spares = $spare_part_model->getAllStock($data);

        $stock = array();

        foreach ($spares as $spare) {
            if (isset($data_stock[$spare->spare_part_id]) && $data_stock[$spare->spare_part_id]>0) {
                $stock[$spare->spare_part_code] = isset($stock[$spare->spare_part_code])?$stock[$spare->spare_part_code].'<option title="'.$data_stock[$spare->spare_part_id].'" value="'.$spare->spare_part_id.'">'.($spare->spare_part_seri!=""?$spare->spare_part_seri:$spare->spare_part_name).' ['.$data_stock[$spare->spare_part_id].']</option>':'<option title="'.$data_stock[$spare->spare_part_id].'" value="'.$spare->spare_part_id.'">'.($spare->spare_part_seri!=""?$spare->spare_part_seri:$spare->spare_part_name).' ['.$data_stock[$spare->spare_part_id].']</option>';
            }
            
        }

        $this->view->data['stock'] = $stock;

        $join = array('table'=>'spare_stock','where'=>'spare_part=spare_part_id','join'=>'LEFT JOIN');
        $spares = $spare_part_model->getAllStock(array('where'=>'export_stock='.$id),$join);
        $spare_parts = array();
        $spare_part_data = array();
        foreach ($spares as $spare) {
            $spare_parts[$spare->spare_part_code][] = $spare;
            $spare_part_data[$spare->spare_part_code]['brand'] = $spare->spare_part_brand;
            $spare_part_data[$spare->spare_part_code]['date'] = $spare->spare_part_date_manufacture;
            $spare_part_data[$spare->spare_part_code]['unit'] = $spare->spare_part_unit;
            $spare_part_data[$spare->spare_part_code]['price'] = $spare->spare_stock_price;
            $spare_part_data[$spare->spare_part_code]['percent'] = $spare->spare_stock_vat_percent;
            $spare_part_data[$spare->spare_part_code]['vat'] = $spare->spare_stock_vat_price;
            $spare_part_data[$spare->spare_part_code]['seri'] = $spare->spare_part_seri;
            $spare_part_data[$spare->spare_part_code]['sl'] = isset($spare_part_data[$spare->spare_part_code]['sl'])?$spare_part_data[$spare->spare_part_code]['sl']+$spare->spare_stock_number:$spare->spare_stock_number;
        }
        $this->view->data['spare_parts'] = $spare_parts;
        $this->view->data['spare_part_data'] = $spare_part_data;

        $house = $this->model->get('houseModel');

        $this->view->data['houses'] = $house->getAllHouse(array('order_by'=>'house_name','order'=>'ASC'));


        return $this->view->show('exportstock/edit');

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

            $this->view->redirect('exportstock');

        }

        $this->view->data['title'] = 'Cập nhật phiếu xuất kho';
        $this->view->data['lib'] = $this->lib;

        $export_stock_model = $this->model->get('exportstockModel');

        $export_stock_data = $export_stock_model->getStock($id);

        $this->view->data['export_stock_data'] = $export_stock_data;

        if (!$export_stock_data) {

            $this->view->redirect('exportstock');

        }

        $spare_part_code_model = $this->model->get('sparepartcodeModel');
        $spare_part_model = $this->model->get('sparepartModel');
        $spare_stock_model = $this->model->get('sparestockModel');

        $spare_part_codes = $spare_part_code_model->getAllStock(array('where'=>'spare_part_code_id IN (SELECT spare_stock_code FROM spare_stock WHERE export_stock='.$id.')'));
        $this->view->data['spare_part_codes'] = $spare_part_codes;

        $join = array('table'=>'spare_part','where'=>'spare_part = spare_part_id');
        $data_im = array(
            'where' => 'import_stock > 0 AND spare_stock_code IN (SELECT spare_stock_code FROM spare_stock WHERE export_stock = '.$id.')',
        );
        $stock_ims = $spare_stock_model->getAllStock($data_im,$join);

        $data_ex = array(
            'where' => 'export_stock > 0 AND spare_stock_code IN (SELECT spare_stock_code FROM spare_stock WHERE export_stock = '.$id.')',
        );
        $stock_exs = $spare_stock_model->getAllStock($data_ex,$join);
    
        $data_stock = array();
        foreach ($stock_ims as $stock) {
            $data_stock[$stock->spare_part] = isset($data_stock[$stock->spare_part])?$data_stock[$stock->spare_part]+$stock->spare_stock_number:$stock->spare_stock_number;
        }
        foreach ($stock_exs as $stock) {
            $data_stock[$stock->spare_part] = isset($data_stock[$stock->spare_part])?$data_stock[$stock->spare_part]-$stock->spare_stock_number:0-$stock->spare_stock_number;
        }

        $data = array(
            'where' => 'spare_part_code IN (SELECT spare_stock_code FROM spare_stock WHERE export_stock = '.$id.') AND spare_part_id NOT IN (SELECT spare_part FROM spare_stock WHERE export_stock = '.$id.')',
        );

        $spares = $spare_part_model->getAllStock($data);

        $stock = array();

        foreach ($spares as $spare) {
            if (isset($data_stock[$spare->spare_part_id]) && $data_stock[$spare->spare_part_id]>0) {
                $stock[$spare->spare_part_code] = isset($stock[$spare->spare_part_code])?$stock[$spare->spare_part_code].'<option title="'.$data_stock[$spare->spare_part_id].'" value="'.$spare->spare_part_id.'">'.($spare->spare_part_seri!=""?$spare->spare_part_seri:$spare->spare_part_name).' ['.$data_stock[$spare->spare_part_id].']</option>':'<option title="'.$data_stock[$spare->spare_part_id].'" value="'.$spare->spare_part_id.'">'.($spare->spare_part_seri!=""?$spare->spare_part_seri:$spare->spare_part_name).' ['.$data_stock[$spare->spare_part_id].']</option>';
            }
            
        }

        $this->view->data['stock'] = $stock;

        $join = array('table'=>'spare_stock','where'=>'spare_part=spare_part_id','join'=>'LEFT JOIN');
        $spares = $spare_part_model->getAllStock(array('where'=>'export_stock='.$id),$join);
        $spare_parts = array();
        $spare_part_data = array();
        foreach ($spares as $spare) {
            $spare_parts[$spare->spare_part_code][] = $spare;
            $spare_part_data[$spare->spare_part_code]['brand'] = $spare->spare_part_brand;
            $spare_part_data[$spare->spare_part_code]['date'] = $spare->spare_part_date_manufacture;
            $spare_part_data[$spare->spare_part_code]['unit'] = $spare->spare_part_unit;
            $spare_part_data[$spare->spare_part_code]['price'] = $spare->spare_stock_price;
            $spare_part_data[$spare->spare_part_code]['percent'] = $spare->spare_stock_vat_percent;
            $spare_part_data[$spare->spare_part_code]['vat'] = $spare->spare_stock_vat_price;
            $spare_part_data[$spare->spare_part_code]['seri'] = $spare->spare_part_seri;
            $spare_part_data[$spare->spare_part_code]['sl'] = isset($spare_part_data[$spare->spare_part_code]['sl'])?$spare_part_data[$spare->spare_part_code]['sl']+$spare->spare_stock_number:$spare->spare_stock_number;
        }
        $this->view->data['spare_parts'] = $spare_parts;
        $this->view->data['spare_part_data'] = $spare_part_data;

        $house = $this->model->get('houseModel');

        $this->view->data['houses'] = $house->getAllHouse(array('order_by'=>'house_name','order'=>'ASC'));

        return $this->view->show('exportstock/view');

    }

    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $house = $this->model->get('houseModel');

        $this->view->data['houses'] = $house->getAllHouse(array('order_by'=>'house_name','order'=>'ASC'));

        $spare_part_code_model = $this->model->get('sparepartcodeModel');

        $this->view->data['spares'] = $spare_part_code_model->getAllStock(array('order_by'=>'name','order'=>'ASC'));

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('exportstock/filter');
    }

    public function getexportstock(){
        $export_stock_model = $this->model->get('exportstockModel');

        $exportstocks = $export_stock_model->getAllStock(array('order_by'=>'export_stock_code','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($exportstocks as $exportstock) {
            $result[$i]['id'] = $exportstock->export_stock_id;
            $result[$i]['text'] = $exportstock->export_stock_code;
            $i++;
        }
        echo json_encode($result);
    }
    public function getseri(){

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {

            $spare_model = $this->model->get('sparepartModel');
            $spare_stock_model = $this->model->get('sparestockModel');

            $join = array('table'=>'spare_part','where'=>'spare_part = spare_part_id');
            $data_im = array(
                'where' => 'import_stock > 0 AND spare_stock_code = '.trim($_GET['data']),
            );
            $stock_ims = $spare_stock_model->getAllStock($data_im,$join);

            $data_ex = array(
                'where' => 'export_stock > 0 AND spare_stock_code = '.trim($_GET['data']),
            );
            $stock_exs = $spare_stock_model->getAllStock($data_ex,$join);
        
            $data_stock = array();
            foreach ($stock_ims as $stock) {
                $data_stock[$stock->spare_part] = isset($data_stock[$stock->spare_part])?$data_stock[$stock->spare_part]+$stock->spare_stock_number:$stock->spare_stock_number;
            }
            foreach ($stock_exs as $stock) {
                $data_stock[$stock->spare_part] = isset($data_stock[$stock->spare_part])?$data_stock[$stock->spare_part]-$stock->spare_stock_number:0-$stock->spare_stock_number;
            }

            $data = array(
                'where' => 'spare_part_code = '.trim($_GET['data']),
            );

            $spares = $spare_model->getAllStock($data);

            $str = "";

            foreach ($spares as $spare) {
                if (isset($data_stock[$spare->spare_part_id]) && $data_stock[$spare->spare_part_id]>0) {
                    $str .= '<option title="'.$data_stock[$spare->spare_part_id].'" value="'.$spare->spare_part_id.'">'.($spare->spare_part_seri!=""?$spare->spare_part_seri:$spare->spare_part_name).' ['.$data_stock[$spare->spare_part_id].']</option>';
                }
                
            }

            echo $str;

        }

    }
    

    public function deletespare(){
        if (isset($_POST['data'])) {
            $spare_stock_model = $this->model->get('sparestockModel');
            $user_log_model = $this->model->get('userlogModel');

            $spare_stock_model->queryStock('DELETE FROM spare_stock WHERE spare_stock_id='.$_POST['data'].' AND spare_part='.$_POST['spare_part']);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|spare_stock|"."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);

            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'spare_stock',
                'user_log_table_name' => 'Chi tiết phiếu xuất kho',
                'user_log_action' => 'Xóa',
                'user_log_data' => json_encode($_POST['data']),
            );
            $user_log_model->createUser($data_log);
        }
    }
    public function deleteexportstockdetail(){
        if (isset($_POST['data'])) {
            $spare_stock_model = $this->model->get('sparestockModel');
            $user_log_model = $this->model->get('userlogModel');

            $spare_stock_model->queryStock('DELETE FROM spare_stock WHERE spare_stock_code='.$_POST['data'].' AND export_stock='.$_POST['exportstock']);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|spare_stock|"."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);

            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'spare_stock',
                'user_log_table_name' => 'Chi tiết phiếu xuất kho (code)',
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

        if ((!isset(json_decode($_SESSION['user_permission_action'])->exportstock) || json_decode($_SESSION['user_permission_action'])->exportstock != "exportstock") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $export_stock_model = $this->model->get('exportstockModel');
            $spare_stock_model = $this->model->get('sparestockModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $export_stock_model->deleteStock($data);

                    $spare_stock_model->queryStock('DELETE FROM spare_stock WHERE export_stock='.$data);

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|export_stock|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'export_stock',
                    'user_log_table_name' => 'Phiếu xuất kho',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $export_stock_model->deleteStock($_POST['data']);

                $spare_stock_model->queryStock('DELETE FROM spare_stock WHERE export_stock='.$_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|export_stock|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'export_stock',
                    'user_log_table_name' => 'Phiếu xuất kho',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importexportstock(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->exportstock) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('exportstock/import');

    }


}

?>