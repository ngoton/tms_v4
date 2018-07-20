<?php

Class shipdepositController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý phí cược cont';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'shipping_name,unit_name,shipdeposit_start_date';

            $order = $this->registry->router->order ? $this->registry->router->order : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $shipdeposit_model = $this->model->get('shipdepositModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        $join = array('table'=>'shipping', 'where'=>'shipdeposit_shipping=shipping_id LEFT JOIN unit ON shipdeposit_unit=unit_id','join'=>'LEFT JOIN');

        if (isset($_POST['filter'])) {
            if (isset($_POST['shipdeposit_shipping'])) {
                $data['where'] .= ' AND shipdeposit_shipping IN ('.implode(',',$_POST['shipdeposit_shipping']).')';
            }
            if (isset($_POST['shipdeposit_unit'])) {
                $data['where'] .= ' AND shipdeposit_unit IN ('.implode(',',$_POST['shipdeposit_unit']).')';
            }
            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($shipdeposit_model->getAllShipping($data,$join));

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
            if (isset($_POST['shipdeposit_shipping'])) {
                $data['where'] .= ' AND shipdeposit_shipping IN ('.implode(',',$_POST['shipdeposit_shipping']).')';
            }
            if (isset($_POST['shipdeposit_unit'])) {
                $data['where'] .= ' AND shipdeposit_unit IN ('.implode(',',$_POST['shipdeposit_unit']).')';
            }
            $this->view->data['filter'] = 1;
        }

        if ($keyword != '') {

            $search = '( shipping_name LIKE "%'.$keyword.'%" OR unit_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['shipdeposits'] = $shipdeposit_model->getAllShipping($data,$join);



        return $this->view->show('shipdeposit/index');

    }


    public function addshipdeposit(){
        $shipdeposit_model = $this->model->get('shipdepositModel');

        if (isset($_POST['shipdeposit_start_date'])) {
            if($shipdeposit_model->getShippingByWhere(array('shipdeposit_shipping'=>$_POST['shipdeposit_shipping'],'shipdeposit_unit'=>$_POST['shipdeposit_unit'],'shipdeposit_start_date'=>strtotime(str_replace('/', '-', $_POST['shipdeposit_start_date']))))){
                echo 'Thông tin đã tồn tại';
                return false;
            }

            $data = array(
                'shipdeposit_start_date' => strtotime(str_replace('/', '-', $_POST['shipdeposit_start_date'])),
                'shipdeposit_end_date' => $_POST['shipdeposit_end_date']!=""?strtotime(str_replace('/', '-', $_POST['shipdeposit_end_date'])):null,
                'shipdeposit_shipping' => trim($_POST['shipdeposit_shipping']),
                'shipdeposit_unit' => trim($_POST['shipdeposit_unit']),
                'shipdeposit_money' => str_replace(',', '', $_POST['shipdeposit_money']),
            );

            $ngaytruoc = strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['shipdeposit_start_date']).' -1 day')));

            if ($data['shipdeposit_end_date'] == null) {
                $shipdeposit_model->queryShipping('UPDATE shipdeposit SET shipdeposit_end_date = '.$ngaytruoc.' WHERE shipdeposit_shipping='.$data['shipdeposit_shipping'].' AND shipdeposit_unit='.$data['shipdeposit_unit'].' AND (shipdeposit_end_date IS NULL OR shipdeposit_end_date = 0)');
                $shipdeposit_model->createShipping($data);
            }
            else{
                $dm1 = $shipdeposit_model->queryShipping('SELECT * FROM shipdeposit WHERE shipdeposit_shipping='.$data['shipdeposit_shipping'].' AND shipdeposit_unit='.$data['shipdeposit_unit'].' AND shipdeposit_start_date <= '.$data['shipdeposit_start_date'].' AND shipdeposit_end_date <= '.$data['shipdeposit_end_date'].' AND shipdeposit_end_date >= '.$data['shipdeposit_start_date'].' ORDER BY shipdeposit_end_date ASC LIMIT 1');
                $dm2 = $shipdeposit_model->queryShipping('SELECT * FROM shipdeposit WHERE shipdeposit_shipping='.$data['shipdeposit_shipping'].' AND shipdeposit_unit='.$data['shipdeposit_unit'].' AND shipdeposit_end_date >= '.$data['shipdeposit_end_date'].' AND shipdeposit_start_date >= '.$data['shipdeposit_start_date'].' AND shipdeposit_start_date <= '.$data['shipdeposit_end_date'].' ORDER BY shipdeposit_end_date ASC LIMIT 1');
                $dm3 = $shipdeposit_model->queryShipping('SELECT * FROM shipdeposit WHERE shipdeposit_shipping='.$data['shipdeposit_shipping'].' AND shipdeposit_unit='.$data['shipdeposit_unit'].' AND shipdeposit_start_date <= '.$data['shipdeposit_start_date'].' AND shipdeposit_end_date >= '.$data['shipdeposit_end_date'].' ORDER BY shipdeposit_end_date ASC LIMIT 1');

                if ($dm3) {
                    foreach ($dm3 as $row) {
                        $d = array(
                            'shipdeposit_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['shipdeposit_start_date']).' -1 day'))),
                            );
                        $shipdeposit_model->updateShipping($d,array('shipdeposit_id'=>$row->shipdeposit_id));

                        $c = array(
                            'shipdeposit_shipping' => $row->shipdeposit_shipping,
                            'shipdeposit_unit' => $row->shipdeposit_unit,
                            'shipdeposit_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['shipdeposit_end_date']).' +1 day'))),
                            'shipdeposit_end_date' => $row->shipdeposit_end_date,
                            'shipdeposit_money' => $row->shipdeposit_money,
                            );
                        $shipdeposit_model->createShipping($c);

                    }
                    $shipdeposit_model->createShipping($data);

                }
                else if ($dm1 || $dm2) {
                    if($dm1){
                        foreach ($dm1 as $row) {
                            $d = array(
                                'shipdeposit_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['shipdeposit_start_date']).' -1 day'))),
                                );
                            $shipdeposit_model->updateShipping($d,array('shipdeposit_id'=>$row->shipdeposit_id));
                        }
                    }
                    if($dm2){
                        foreach ($dm2 as $row) {
                            $d = array(
                                'shipdeposit_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['shipdeposit_end_date']).' +1 day'))),
                                );
                            $shipdeposit_model->updateShipping($d,array('shipdeposit_id'=>$row->shipdeposit_id));
                        }
                    }
                    $shipdeposit_model->createShipping($data);
                }
                else{
                    $shipdeposit_model->createShipping($data);
                }
            }
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$shipdeposit_model->getLastShipping()->shipdeposit_id."|shipdeposit|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'shipdeposit',
                'user_log_table_name' => 'Phí cược cont',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipdeposit) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới phí cược cont';

        $shipping = $this->model->get('shippingModel');

        $this->view->data['shippings'] = $shipping->getAllShipping(array('order_by'=>'shipping_name','order'=>'ASC'));

        $unit = $this->model->get('unitModel');

        $this->view->data['units'] = $unit->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));

        return $this->view->show('shipdeposit/add');
    }

    public function editshipdeposit(){
        $shipdeposit_model = $this->model->get('shipdepositModel');

        if (isset($_POST['shipdeposit_id'])) {
            $id = $_POST['shipdeposit_id'];
            
            $data = array(
                'shipdeposit_start_date' => strtotime(str_replace('/', '-', $_POST['shipdeposit_start_date'])),
                'shipdeposit_end_date' => $_POST['shipdeposit_end_date']!=""?strtotime(str_replace('/', '-', $_POST['shipdeposit_end_date'])):null,
                'shipdeposit_shipping' => trim($_POST['shipdeposit_shipping']),
                'shipdeposit_unit' => trim($_POST['shipdeposit_unit']),
                'shipdeposit_money' => str_replace(',', '', $_POST['shipdeposit_money']),
            );

            $shipdeposit_model->updateShipping($data,array('shipdeposit_id'=>$id));
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|shipdeposit|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'shipdeposit',
                'user_log_table_name' => 'Phí cược cont',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipdeposit) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('shipdeposit');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật phí cược cont';

        $shipdeposit_model = $this->model->get('shipdepositModel');

        $shipdeposit_data = $shipdeposit_model->getShipping($id);

        $this->view->data['shipdeposit_data'] = $shipdeposit_data;

        if (!$shipdeposit_data) {

            $this->view->redirect('shipdeposit');

        }

        $shipping = $this->model->get('shippingModel');

        $this->view->data['shippings'] = $shipping->getAllShipping(array('order_by'=>'shipping_name','order'=>'ASC'));

        $unit = $this->model->get('unitModel');

        $this->view->data['units'] = $unit->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));

        return $this->view->show('shipdeposit/edit');

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

            $this->view->redirect('shipdeposit');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin phí cược cont';

        $shipdeposit_model = $this->model->get('shipdepositModel');

        $shipdeposit_data = $shipdeposit_model->getShipping($id);

        $this->view->data['shipdeposit_data'] = $shipdeposit_data;

        if (!$shipdeposit_data) {

            $this->view->redirect('shipdeposit');

        }

        $shipping = $this->model->get('shippingModel');

        $this->view->data['shippings'] = $shipping->getAllShipping(array('order_by'=>'shipping_name','order'=>'ASC'));

        $unit = $this->model->get('unitModel');

        $this->view->data['units'] = $unit->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));

        return $this->view->show('shipdeposit/view');

    }
    public function viewshipdeposit(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin phí cược cont';

        $id = $_GET['id'];

        $info = explode('~', $id);

        $shipdeposit_model = $this->model->get('shipdepositModel');

        $data = array(
            'where'=>'shipdeposit_shipping = '.$info[0].' AND shipdeposit_start_date <= '.strtotime(str_replace('/', '-', $info[1])).' AND (shipdeposit_end_date IS NULL OR shipdeposit_end_date=0 OR shipdeposit_end_date >= '.strtotime(str_replace('/', '-', $info[1])).')',
            'order_by'=>'shipdeposit_start_date',
            'order'=>'DESC',
            'limit'=>1
        );

        $shipdeposits = $shipdeposit_model->getAllShipping($data);
        foreach ($shipdeposits as $shipdeposit) {
            $shipdeposit_data = $shipdeposit;
        }

        $this->view->data['shipdeposit_data'] = $shipdeposit_data;

        if (!$shipdeposit_data) {

            $this->view->redirect('shipdeposit');

        }

        $shipping = $this->model->get('shippingModel');

        $this->view->data['shippings'] = $shipping->getAllshipping(array('order_by'=>'shipping_name','order'=>'ASC'));

        $unit = $this->model->get('unitModel');

        $this->view->data['units'] = $unit->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));

        return $this->view->show('shipdeposit/view');

    }


    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $shipping = $this->model->get('shippingModel');

        $this->view->data['shippings'] = $shipping->getAllShipping(array('order_by'=>'shipping_name','order'=>'ASC'));

        $unit = $this->model->get('unitModel');

        $this->view->data['units'] = $unit->getAllUnit(array('order_by'=>'unit_name','order'=>'ASC'));

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('shipdeposit/filter');
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->shipdeposit) || json_decode($_SESSION['user_permission_action'])->shipdeposit != "shipdeposit") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $shipdeposit_model = $this->model->get('shipdepositModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $shipdeposit_model->deleteShipping($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|shipdeposit|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'shipdeposit',
                    'user_log_table_name' => 'Phí cược cont',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $shipdeposit_model->deleteShipping($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|shipdeposit|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'shipdeposit',
                    'user_log_table_name' => 'Phí cược cont',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importshipdeposit(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipdeposit) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('shipdeposit/import');

    }


}

?>