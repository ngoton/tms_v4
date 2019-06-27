<?php

Class warehouseController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý bồi dưỡng kho';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'place_name,warehouse_start_date';

            $order = $this->registry->router->order ? $this->registry->router->order : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $warehouse_model = $this->model->get('warehouseModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $join = array('table'=>'place','where'=>'warehouse_place=place_id','join'=>'LEFT JOIN');
        $data = array(
            'where'=>'1=1',
        );

        if (isset($_POST['filter'])) {
            if (isset($_POST['warehouse_place'])) {
                $data['where'] .= ' AND warehouse_place IN ('.implode(',',$_POST['warehouse_place']).')';
            }
            if (isset($_POST['warehouse_province'])) {
                $data['where'] .= ' AND warehouse_place IN (SELECT place_id FROM place WHERE place_province IN ('.implode(',',$_POST['warehouse_province']).'))';
            }
            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($warehouse_model->getAllWarehouse($data,$join));

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
            if (isset($_POST['warehouse_place'])) {
                $data['where'] .= ' AND warehouse_place IN ('.implode(',',$_POST['warehouse_place']).')';
            }
            if (isset($_POST['warehouse_province'])) {
                $data['where'] .= ' AND warehouse_place IN (SELECT place_id FROM place WHERE place_province IN ('.implode(',',$_POST['warehouse_province']).'))';
            }
        }
        

        if ($keyword != '') {

            $search = '( place_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['warehouses'] = $warehouse_model->getAllWarehouse($data,$join);



        return $this->view->show('warehouse/index');

    }


    public function addwarehouse(){
        $warehouse_model = $this->model->get('warehouseModel');

        if (isset($_POST['warehouse_place'])) {
            if($warehouse_model->getWarehouseByWhere(array('warehouse_place'=>$_POST['warehouse_place'],'warehouse_start_date'=>strtotime(str_replace('/', '-', $_POST['warehouse_start_date']))))){
                echo 'Thông tin đã tồn tại';
                return false;
            }

            $data = array(
                'warehouse_place'=>trim($_POST['warehouse_place']),
                'warehouse_start_date' => strtotime(str_replace('/', '-', $_POST['warehouse_start_date'])),
                'warehouse_end_date' => $_POST['warehouse_end_date']!=""?strtotime(str_replace('/', '-', $_POST['warehouse_end_date'])):null,
                'warehouse_cont' => str_replace(',', '', $_POST['warehouse_cont']),
                'warehouse_ton' => str_replace(',', '', $_POST['warehouse_ton']),
                'warehouse_add' => str_replace(',', '', $_POST['warehouse_add']),
                'warehouse_weight' => str_replace(',', '', $_POST['warehouse_weight']),
                'warehouse_clean' => str_replace(',', '', $_POST['warehouse_clean']),
                'warehouse_gate' => str_replace(',', '', $_POST['warehouse_gate']),
            );

            $ngaytruoc = strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['warehouse_start_date']).' -1 day')));

            if ($data['warehouse_end_date'] == null) {
                $warehouse_model->queryWarehouse('UPDATE warehouse SET warehouse_end_date = '.$ngaytruoc.' WHERE warehouse_place='.$data['warehouse_place'].' AND (warehouse_end_date IS NULL OR warehouse_end_date = 0)');
                $warehouse_model->createWarehouse($data);
            }
            else{
                $dm1 = $warehouse_model->queryWarehouse('SELECT * FROM warehouse WHERE warehouse_place='.$data['warehouse_place'].' AND warehouse_start_date <= '.$data['warehouse_start_date'].' AND warehouse_end_date <= '.$data['warehouse_end_date'].' AND warehouse_end_date >= '.$data['warehouse_start_date'].' ORDER BY warehouse_end_date ASC LIMIT 1');
                $dm2 = $warehouse_model->queryWarehouse('SELECT * FROM warehouse WHERE warehouse_place='.$data['warehouse_place'].' AND warehouse_end_date >= '.$data['warehouse_end_date'].' AND warehouse_start_date >= '.$data['warehouse_start_date'].' AND warehouse_start_date <= '.$data['warehouse_end_date'].' ORDER BY warehouse_end_date ASC LIMIT 1');
                $dm3 = $warehouse_model->queryWarehouse('SELECT * FROM warehouse WHERE warehouse_place='.$data['warehouse_place'].' AND warehouse_start_date <= '.$data['warehouse_start_date'].' AND warehouse_end_date >= '.$data['warehouse_end_date'].' ORDER BY warehouse_end_date ASC LIMIT 1');

                if ($dm3) {
                    foreach ($dm3 as $row) {
                        $d = array(
                            'warehouse_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['warehouse_start_date']).' -1 day'))),
                            );
                        $warehouse_model->updateWarehouse($d,array('warehouse_id'=>$row->warehouse_id));

                        $c = array(
                            'warehouse_place' => $row->warehouse_place,
                            'warehouse_cont' => $row->warehouse_cont,
                            'warehouse_ton' => $row->warehouse_ton,
                            'warehouse_add' => $row->warehouse_add,
                            'warehouse_weight' => $row->warehouse_weight,
                            'warehouse_clean' => $row->warehouse_clean,
                            'warehouse_gate' => $row->warehouse_gate,
                            'warehouse_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['warehouse_end_date']).' +1 day'))),
                            'warehouse_end_date' => $row->warehouse_end_date,
                            );
                        $warehouse_model->createWarehouse($c);

                    }
                    $warehouse_model->createWarehouse($data);

                }
                else if ($dm1 || $dm2) {
                    if($dm1){
                        foreach ($dm1 as $row) {
                            $d = array(
                                'warehouse_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['warehouse_start_date']).' -1 day'))),
                                );
                            $warehouse_model->updateWarehouse($d,array('warehouse_id'=>$row->warehouse_id));
                        }
                    }
                    if($dm2){
                        foreach ($dm2 as $row) {
                            $d = array(
                                'warehouse_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['warehouse_end_date']).' +1 day'))),
                                );
                            $warehouse_model->updateWarehouse($d,array('warehouse_id'=>$row->warehouse_id));
                        }
                    }
                    $warehouse_model->createWarehouse($data);
                }
                else{
                    $warehouse_model->createWarehouse($data);
                }
            }
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$warehouse_model->getLastWarehouse()->warehouse_id."|warehouse|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'warehouse',
                'user_log_table_name' => 'Bồi dưỡng kho',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->warehouse) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới bồi dưỡng kho';

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;

        return $this->view->show('warehouse/add');
    }

    public function editwarehouse(){
        $warehouse_model = $this->model->get('warehouseModel');

        if (isset($_POST['warehouse_id'])) {
            $id = $_POST['warehouse_id'];
            
            $data = array(
                'warehouse_place'=>trim($_POST['warehouse_place']),
                'warehouse_start_date' => strtotime(str_replace('/', '-', $_POST['warehouse_start_date'])),
                'warehouse_end_date' => $_POST['warehouse_end_date']!=""?strtotime(str_replace('/', '-', $_POST['warehouse_end_date'])):null,
                'warehouse_cont' => str_replace(',', '', $_POST['warehouse_cont']),
                'warehouse_ton' => str_replace(',', '', $_POST['warehouse_ton']),
                'warehouse_add' => str_replace(',', '', $_POST['warehouse_add']),
                'warehouse_weight' => str_replace(',', '', $_POST['warehouse_weight']),
                'warehouse_clean' => str_replace(',', '', $_POST['warehouse_clean']),
                'warehouse_gate' => str_replace(',', '', $_POST['warehouse_gate']),
            );

            $warehouse_model->updateWarehouse($data,array('warehouse_id'=>$id));
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|warehouse|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'warehouse',
                'user_log_table_name' => 'Bồi dưỡng kho',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->warehouse) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('warehouse');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật bồi dưỡng kho';

        $warehouse_model = $this->model->get('warehouseModel');

        $warehouse_data = $warehouse_model->getWarehouse($id);

        $this->view->data['warehouse_data'] = $warehouse_data;

        if (!$warehouse_data) {

            $this->view->redirect('warehouse');

        }

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;


        return $this->view->show('warehouse/edit');

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

            $this->view->redirect('warehouse');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin bồi dưỡng kho';

        $warehouse_model = $this->model->get('warehouseModel');

        $warehouse_data = $warehouse_model->getWarehouse($id);

        $this->view->data['warehouse_data'] = $warehouse_data;

        if (!$warehouse_data) {

            $this->view->redirect('warehouse');

        }

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace(array('order_by'=>'place_name','order'=>'ASC'));

        $this->view->data['places'] = $places;


        return $this->view->show('warehouse/view');

    }

    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $province_model = $this->model->get('provinceModel');
        $place_model = $this->model->get('placeModel');

        $provinces = $province_model->getAllProvince();
        $places = $place_model->getAllPlace(array('order_by'=>'place_code','order'=>'ASC'));

        $this->view->data['provinces'] = $provinces;
        $this->view->data['places'] = $places;

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('warehouse/filter');
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->warehouse) || json_decode($_SESSION['user_permission_action'])->warehouse != "warehouse") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $warehouse_model = $this->model->get('warehouseModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $warehouse_model->deleteWarehouse($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|warehouse|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'warehouse',
                    'user_log_table_name' => 'Bồi dưỡng kho',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $warehouse_model->deleteWarehouse($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|warehouse|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'warehouse',
                    'user_log_table_name' => 'Bồi dưỡng kho',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importwarehouse(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->warehouse) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('warehouse/import');

    }


}

?>