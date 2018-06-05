<?php

Class roadController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý định mức tuyến đường';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'road_start_date';

            $order = $this->registry->router->order ? $this->registry->router->order : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $road_model = $this->model->get('roadModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        if (isset($_POST['filter'])) {
            if (isset($_POST['road_place'])) {
                $data['where'] .= ' AND road_place IN ('.implode(',',$_POST['road_place']).')';
            }
            if (isset($_POST['road_province'])) {
                $data['where'] .= ' AND road_place IN (SELECT place_id FROM place WHERE place_province IN ('.implode(',',$_POST['road_province']).'))';
            }
        }

        $tongsodong = count($road_model->getAllRoad($data));

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
            if (isset($_POST['road_place'])) {
                $data['where'] .= ' AND road_place IN ('.implode(',',$_POST['road_place']).')';
            }
            if (isset($_POST['road_province'])) {
                $data['where'] .= ' AND road_place IN (SELECT place_id FROM place WHERE place_province IN ('.implode(',',$_POST['road_province']).'))';
            }
        }
        

        if ($keyword != '') {

            $search = '( place_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['roads'] = $road_model->getAllRoad($data,$join);



        return $this->view->show('road/index');

    }


    public function addroad(){
        $road_model = $this->model->get('roadModel');

        if (isset($_POST['road_place'])) {
            if($road_model->getRoadByWhere(array('road_place'=>$_POST['road_place'],'road_start_date'=>strtotime(str_replace('/', '-', $_POST['road_start_date']))))){
                echo 'Thông tin đã tồn tại';
                return false;
            }

            $data = array(
                'road_place'=>trim($_POST['road_place']),
                'road_start_date' => strtotime(str_replace('/', '-', $_POST['road_start_date'])),
                'road_end_date' => $_POST['road_end_date']!=""?strtotime(str_replace('/', '-', $_POST['road_end_date'])):null,
                'road_cont' => str_replace(',', '', $_POST['road_cont']),
                'road_ton' => str_replace(',', '', $_POST['road_ton']),
                'road_add' => str_replace(',', '', $_POST['road_add']),
                'road_weight' => str_replace(',', '', $_POST['road_weight']),
                'road_clean' => str_replace(',', '', $_POST['road_clean']),
                'road_gate' => str_replace(',', '', $_POST['road_gate']),
            );

            $ngaytruoc = strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['road_start_date']).' -1 day')));

            if ($data['road_end_date'] == null) {
                $road_model->queryRoad('UPDATE road SET road_end_date = '.$ngaytruoc.' WHERE road_place='.$data['road_place'].' AND (road_end_date IS NULL OR road_end_date = 0)');
                $road_model->createRoad($data);
            }
            else{
                $dm1 = $road_model->queryRoad('SELECT * FROM road WHERE road_place='.$data['road_place'].' AND road_start_date <= '.$data['road_start_date'].' AND road_end_date <= '.$data['road_end_date'].' AND road_end_date >= '.$data['road_start_date'].' ORDER BY road_end_date ASC LIMIT 1');
                $dm2 = $road_model->queryRoad('SELECT * FROM road WHERE road_place='.$data['road_place'].' AND road_end_date >= '.$data['road_end_date'].' AND road_start_date >= '.$data['road_start_date'].' AND road_start_date <= '.$data['road_end_date'].' ORDER BY road_end_date ASC LIMIT 1');
                $dm3 = $road_model->queryRoad('SELECT * FROM road WHERE road_place='.$data['road_place'].' AND road_start_date <= '.$data['road_start_date'].' AND road_end_date >= '.$data['road_end_date'].' ORDER BY road_end_date ASC LIMIT 1');

                if ($dm3) {
                    foreach ($dm3 as $row) {
                        $d = array(
                            'road_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['road_start_date']).' -1 day'))),
                            );
                        $road_model->updateRoad($d,array('road_id'=>$row->road_id));

                        $c = array(
                            'road_place' => $row->road_place,
                            'road_cont' => $row->road_cont,
                            'road_ton' => $row->road_ton,
                            'road_add' => $row->road_add,
                            'road_weight' => $row->road_weight,
                            'road_clean' => $row->road_clean,
                            'road_gate' => $row->road_gate,
                            'road_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['road_end_date']).' +1 day'))),
                            'road_end_date' => $row->road_end_date,
                            );
                        $road_model->createRoad($c);

                    }
                    $road_model->createRoad($data);

                }
                else if ($dm1 || $dm2) {
                    if($dm1){
                        foreach ($dm1 as $row) {
                            $d = array(
                                'road_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['road_start_date']).' -1 day'))),
                                );
                            $road_model->updateRoad($d,array('road_id'=>$row->road_id));
                        }
                    }
                    if($dm2){
                        foreach ($dm2 as $row) {
                            $d = array(
                                'road_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['road_end_date']).' +1 day'))),
                                );
                            $road_model->updateRoad($d,array('road_id'=>$row->road_id));
                        }
                    }
                    $road_model->createRoad($data);
                }
                else{
                    $road_model->createRoad($data);
                }
            }
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$road_model->getLastRoad()->road_id."|road|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'road',
                'user_log_table_name' => 'Định mức tuyến đường',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->road) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới định mức tuyến đường';

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace();

        $this->view->data['places'] = $places;

        return $this->view->show('road/add');
    }

    public function editroad(){
        $road_model = $this->model->get('roadModel');

        if (isset($_POST['road_id'])) {
            $id = $_POST['road_id'];
            
            $data = array(
                'road_place'=>trim($_POST['road_place']),
                'road_start_date' => strtotime(str_replace('/', '-', $_POST['road_start_date'])),
                'road_end_date' => $_POST['road_end_date']!=""?strtotime(str_replace('/', '-', $_POST['road_end_date'])):null,
                'road_cont' => str_replace(',', '', $_POST['road_cont']),
                'road_ton' => str_replace(',', '', $_POST['road_ton']),
                'road_add' => str_replace(',', '', $_POST['road_add']),
                'road_weight' => str_replace(',', '', $_POST['road_weight']),
                'road_clean' => str_replace(',', '', $_POST['road_clean']),
                'road_gate' => str_replace(',', '', $_POST['road_gate']),
            );

            $road_model->updateRoad($data,array('road_id'=>$id));
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|road|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'road',
                'user_log_table_name' => 'Định mức tuyến đường',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->road) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('road');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật định mức tuyến đường';

        $road_model = $this->model->get('roadModel');

        $road_data = $road_model->getRoad($id);

        $this->view->data['road_data'] = $road_data;

        if (!$road_data) {

            $this->view->redirect('road');

        }

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace();

        $this->view->data['places'] = $places;


        return $this->view->show('road/edit');

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

            $this->view->redirect('road');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin định mức tuyến đường';

        $road_model = $this->model->get('roadModel');

        $road_data = $road_model->getRoad($id);

        $this->view->data['road_data'] = $road_data;

        if (!$road_data) {

            $this->view->redirect('road');

        }

        $place_model = $this->model->get('placeModel');

        $places = $place_model->getAllPlace();

        $this->view->data['places'] = $places;


        return $this->view->show('road/view');

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

        return $this->view->show('road/filter');
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->road) || json_decode($_SESSION['user_permission_action'])->road != "road") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $road_model = $this->model->get('roadModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $road_model->deleteRoad($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|road|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'road',
                    'user_log_table_name' => 'Định mức tuyến đường',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $road_model->deleteRoad($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|road|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'road',
                    'user_log_table_name' => 'Định mức tuyến đường',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importroad(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->road) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('road/import');

    }


}

?>