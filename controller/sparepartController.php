<?php

Class sparepartController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý vật tư, thiết bị';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'code';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $spare_part_model = $this->model->get('sparepartModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        $join = array('table'=>'spare_part_code','where'=>'spare_part_code=spare_part_code_id','join'=>'LEFT JOIN');

        if (isset($_POST['filter'])) {
            if (isset($_POST['spare_part_code'])) {
                $data['where'] .= ' AND spare_part_code IN ('.implode(',',$_POST['spare_part_code']).')';
            }
            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($spare_part_model->getAllStock($data,$join));

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
            'where'=>'1=1',

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            );

        if (isset($_POST['filter'])) {
            if (isset($_POST['spare_part_code'])) {
                $data['where'] .= ' AND spare_part_code IN ('.implode(',',$_POST['spare_part_code']).')';
            }
        }

        if ($keyword != '') {

            $search = '( spare_part_name LIKE "%'.$keyword.'%" OR code  LIKE "%'.$keyword.'%" OR name LIKE "%'.$keyword.'%" OR spare_part_brand LIKE "%'.$keyword.'%" OR spare_part_seri LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }

        $spareparts = $spare_part_model->getAllStock($data,$join);;

        $this->view->data['spareparts'] = $spareparts;

        $arr = array();
        foreach ($spareparts as $sparepart) {
            $arr[$sparepart->code][$sparepart->name][] = $sparepart; 
        }
        $this->view->data['arr'] = $arr;

        return $this->view->show('sparepart/index');

    }


    public function addsparepart(){
        $spare_part_model = $this->model->get('sparepartModel');

        if (isset($_POST['spare_part_code'])) {
            if (trim($_POST['spare_part_seri']) != "") {
                if($spare_part_model->getStockByWhere(array('spare_part_code'=>trim($_POST['spare_part_code']),'spare_part_seri'=>trim($_POST['spare_part_seri'])))){
                    echo 'Số seri đã tồn tại';
                    return false;
                }
            }
            

            $data = array(
                'spare_part_code' => trim($_POST['spare_part_code']),
                'spare_part_name' => trim($_POST['spare_part_name']),
                'spare_part_seri' => trim($_POST['spare_part_seri']),
                'spare_part_brand' => trim($_POST['spare_part_brand']),
                'spare_part_date_manufacture' => strtotime(str_replace('/', '-', $_POST['spare_part_date_manufacture'])),
                'spare_part_unit' => trim($_POST['spare_part_unit']),
            );
            $spare_part_model->createStock($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$spare_part_model->getLastStock()->spare_part_id."|spare_part|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'spare_part',
                'user_log_table_name' => 'Vật tư',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparepart) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới vật tư';

        $spare_part_code = $this->model->get('sparepartcodeModel');

        $this->view->data['codes'] = $spare_part_code->getAllStock(array('order_by'=>'code','order'=>'ASC'));

        return $this->view->show('sparepart/add');
    }

    public function editsparepart(){
        $spare_part_model = $this->model->get('sparepartModel');

        if (isset($_POST['spare_part_id'])) {
            $id = $_POST['spare_part_id'];
            if (trim($_POST['spare_part_seri']) != "") {
                if($spare_part_model->getAllStockByWhere($id.' AND spare_part_code = '.$_POST['spare_part_code'].' AND spare_part_seri = "'.trim($_POST['spare_part_seri']))){
                    echo 'Số seri đã tồn tại';
                    return false;
                }
            }

            $data = array(
                'spare_part_code' => trim($_POST['spare_part_code']),
                'spare_part_name' => trim($_POST['spare_part_name']),
                'spare_part_seri' => trim($_POST['spare_part_seri']),
                'spare_part_brand' => trim($_POST['spare_part_brand']),
                'spare_part_date_manufacture' => strtotime(str_replace('/', '-', $_POST['spare_part_date_manufacture'])),
                'spare_part_unit' => trim($_POST['spare_part_unit']),
            );
            $spare_part_model->updateStock($data,array('spare_part_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|spare_part|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'spare_part',
                'user_log_table_name' => 'Vật tư',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparepart) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('sparepart');

        }

        $this->view->data['title'] = 'Cập nhật vật tư';
        $this->view->data['lib'] = $this->lib;

        $spare_part_model = $this->model->get('sparepartModel');

        $spare_part_data = $spare_part_model->getStock($id);

        $this->view->data['spare_part_data'] = $spare_part_data;

        if (!$spare_part_data) {

            $this->view->redirect('sparepart');

        }

        $spare_part_code = $this->model->get('sparepartcodeModel');

        $this->view->data['codes'] = $spare_part_code->getAllStock(array('order_by'=>'code','order'=>'ASC'));

        return $this->view->show('sparepart/edit');

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

            $this->view->redirect('sparepart');

        }

        $this->view->data['title'] = 'Cập nhật vật tư';
        $this->view->data['lib'] = $this->lib;

        $spare_part_model = $this->model->get('sparepartModel');

        $spare_part_data = $spare_part_model->getStock($id);

        $this->view->data['spare_part_data'] = $spare_part_data;

        if (!$spare_part_data) {

            $this->view->redirect('sparepart');

        }

        $spare_part_code = $this->model->get('sparepartcodeModel');

        $this->view->data['codes'] = $spare_part_code->getAllStock(array('order_by'=>'code','order'=>'ASC'));

        return $this->view->show('sparepart/view');

    }

    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $spare_part_code = $this->model->get('sparepartcodeModel');

        $codes = $spare_part_code->getAllStock(array('order_by'=>'code','order'=>'ASC'));

        $this->view->data['codes'] = $codes;

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('sparepart/filter');
    }

    public function getsparepart(){
        $spare_part_model = $this->model->get('sparepartModel');

        $spareparts = $spare_part_model->getAllStock(array('order_by'=>'spare_part_name','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($spareparts as $sparepart) {
            $result[$i]['id'] = $sparepart->spare_part_id;
            $result[$i]['text'] = $sparepart->spare_part_name;
            $i++;
        }
        echo json_encode($result);
    }
    public function getSpare(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            $spare_code_model = $this->model->get('sparepartcodeModel');
            $spare_model = $this->model->get('sparepartModel');
            $spare_stock_model = $this->model->get('sparestockModel');

            if ($_GET['keyword'] == "*") {
                $list = $spare_code_model->getAllStock();
            }
            else{
                $data = array(
                'where'=>'( name LIKE "%'.$_GET['keyword'].'%" )',
                );
                $list = $spare_code_model->getAllStock($data);

                if (!$list) {
                    $data = array(
                    'where'=>'( code LIKE "%'.$_GET['keyword'].'%" )',
                    );
                    $list = $spare_code_model->getAllStock($data);
                }
            }

            foreach ($list as $rs) {
                $spare_name = '['.$rs->code.']-'.$rs->name;

                if ($_GET['keyword'] != "*") {
                    $spare_name = str_replace($_GET['keyword'], '<b>'.$_GET['keyword'].'</b>', '['.$rs->code.']-'.$rs->name);
                }

                $stocks = $spare_stock_model->queryStock('SELECT * FROM spare_stock, spare_part WHERE spare_part = spare_part_id AND import_stock > 0 AND spare_part IN (SELECT spare_part_id FROM spare_part WHERE spare_part_code = '.$rs->spare_part_code_id.') ORDER BY spare_stock_id DESC LIMIT 1');

                if ($stocks) {
                    foreach ($stocks as $stock) {
                        echo '<li onclick="set_item_other(\''.$rs->spare_part_code_id.'\',\''.$rs->name.'\',\''.$rs->code.'\',\''.$this->lib->hien_thi_ngay_thang($stock->spare_part_date_manufacture).'\',\''.$stock->spare_part_brand.'\',\''.$_GET['offset'].'\',\''.$stock->spare_part_unit.'\',\''.$stock->spare_stock_price.'\')">'.$spare_name.'</li>';
                    }
                }
                else{
                    $spares = $spare_model->getAllStock(array('where'=>'spare_part_code = '.$rs->spare_part_code_id,'order_by'=>'spare_part_id DESC','limit'=>1));
                    foreach ($spares as $spare) {
                        echo '<li onclick="set_item_other(\''.$rs->spare_part_code_id.'\',\''.$rs->name.'\',\''.$rs->code.'\',\''.$this->lib->hien_thi_ngay_thang($spare->spare_part_date_manufacture).'\',\''.$spare->spare_part_brand.'\',\''.$_GET['offset'].'\',\''.$spare->spare_part_unit.'\',\'\')">'.$spare_name.'</li>';
                    }
                }
            }
        }
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->sparepart) || json_decode($_SESSION['user_permission_action'])->sparepart != "sparepart") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $spare_part_model = $this->model->get('sparepartModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $spare_part_model->deleteStock($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|spare_part|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'spare_part',
                    'user_log_table_name' => 'Vật tư',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $spare_part_model->deleteStock($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|spare_part|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'spare_part',
                    'user_log_table_name' => 'Vật tư',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importsparepart(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->sparepart) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('sparepart/import');

    }


}

?>