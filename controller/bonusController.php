<?php

Class bonusController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý thưởng phạt dầu';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'bonus_start_date';

            $order = $this->registry->router->order ? $this->registry->router->order : 'DESC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $bonus_model = $this->model->get('bonusModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        

        $tongsodong = count($bonus_model->getAllBonus());

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

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            );

        

        if ($keyword != '') {

            $search = '( bonus_plus LIKE "%'.$keyword.'%" OR bonus_minus LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['bonuss'] = $bonus_model->getAllBonus($data);



        return $this->view->show('bonus/index');

    }


    public function addbonus(){
        $bonus_model = $this->model->get('bonusModel');

        if (isset($_POST['bonus_start_date'])) {
            if($bonus_model->getBonusByWhere(array('bonus_start_date'=>strtotime(str_replace('/', '-', $_POST['bonus_start_date']))))){
                echo 'Thông tin đã tồn tại';
                return false;
            }

            $data = array(
                'bonus_start_date' => strtotime(str_replace('/', '-', $_POST['bonus_start_date'])),
                'bonus_end_date' => $_POST['bonus_end_date']!=""?strtotime(str_replace('/', '-', $_POST['bonus_end_date'])):null,
                'bonus_plus' => str_replace(',', '', $_POST['bonus_plus']),
                'bonus_minus' => str_replace(',', '', $_POST['bonus_minus']),
            );

            $ngaytruoc = strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['bonus_start_date']).' -1 day')));

            if ($data['bonus_end_date'] == null) {
                $bonus_model->queryBonus('UPDATE bonus SET bonus_end_date = '.$ngaytruoc.' WHERE (bonus_end_date IS NULL OR bonus_end_date = 0)');
                $bonus_model->createBonus($data);
            }
            else{
                $dm1 = $bonus_model->queryBonus('SELECT * FROM bonus WHERE bonus_start_date <= '.$data['bonus_start_date'].' AND bonus_end_date <= '.$data['bonus_end_date'].' AND bonus_end_date >= '.$data['bonus_start_date'].' ORDER BY bonus_end_date ASC LIMIT 1');
                $dm2 = $bonus_model->queryBonus('SELECT * FROM bonus WHERE bonus_end_date >= '.$data['bonus_end_date'].' AND bonus_start_date >= '.$data['bonus_start_date'].' AND bonus_start_date <= '.$data['bonus_end_date'].' ORDER BY bonus_end_date ASC LIMIT 1');
                $dm3 = $bonus_model->queryBonus('SELECT * FROM bonus WHERE bonus_start_date <= '.$data['bonus_start_date'].' AND bonus_end_date >= '.$data['bonus_end_date'].' ORDER BY bonus_end_date ASC LIMIT 1');

                if ($dm3) {
                    foreach ($dm3 as $row) {
                        $d = array(
                            'bonus_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['bonus_start_date']).' -1 day'))),
                            );
                        $bonus_model->updateBonus($d,array('bonus_id'=>$row->bonus_id));

                        $c = array(
                            'bonus_plus' => $row->bonus_plus,
                            'bonus_minus' => $row->bonus_minus,
                            'bonus_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['bonus_end_date']).' +1 day'))),
                            'bonus_end_date' => $row->bonus_end_date,
                            );
                        $bonus_model->createBonus($c);

                    }
                    $bonus_model->createBonus($data);

                }
                else if ($dm1 || $dm2) {
                    if($dm1){
                        foreach ($dm1 as $row) {
                            $d = array(
                                'bonus_end_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['bonus_start_date']).' -1 day'))),
                                );
                            $bonus_model->updateBonus($d,array('bonus_id'=>$row->bonus_id));
                        }
                    }
                    if($dm2){
                        foreach ($dm2 as $row) {
                            $d = array(
                                'bonus_start_date' => strtotime(date('d-m-Y',strtotime(str_replace('/', '-', $_POST['bonus_end_date']).' +1 day'))),
                                );
                            $bonus_model->updateBonus($d,array('bonus_id'=>$row->bonus_id));
                        }
                    }
                    $bonus_model->createBonus($data);
                }
                else{
                    $bonus_model->createBonus($data);
                }
            }
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$bonus_model->getLastBonus()->bonus_id."|bonus|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'bonus',
                'user_log_table_name' => 'Thưởng phạt dầu',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->bonus) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới thưởng phạt dầu';

        return $this->view->show('bonus/add');
    }

    public function editbonus(){
        $bonus_model = $this->model->get('bonusModel');

        if (isset($_POST['bonus_id'])) {
            $id = $_POST['bonus_id'];
            
            $data = array(
                'bonus_start_date' => strtotime(str_replace('/', '-', $_POST['bonus_start_date'])),
                'bonus_end_date' => $_POST['bonus_end_date']!=""?strtotime(str_replace('/', '-', $_POST['bonus_end_date'])):null,
                'bonus_plus' => str_replace(',', '', $_POST['bonus_plus']),
                'bonus_minus' => str_replace(',', '', $_POST['bonus_minus']),
            );

            $bonus_model->updateBonus($data,array('bonus_id'=>$id));
            

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|bonus|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'bonus',
                'user_log_table_name' => 'Thưởng phạt dầu',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->bonus) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('bonus');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật thưởng phạt dầu';

        $bonus_model = $this->model->get('bonusModel');

        $bonus_data = $bonus_model->getBonus($id);

        $this->view->data['bonus_data'] = $bonus_data;

        if (!$bonus_data) {

            $this->view->redirect('bonus');

        }


        return $this->view->show('bonus/edit');

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

            $this->view->redirect('bonus');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin thưởng phạt dầu';

        $bonus_model = $this->model->get('bonusModel');

        $bonus_data = $bonus_model->getBonus($id);

        $this->view->data['bonus_data'] = $bonus_data;

        if (!$bonus_data) {

            $this->view->redirect('bonus');

        }


        return $this->view->show('bonus/view');

    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->bonus) || json_decode($_SESSION['user_permission_action'])->bonus != "bonus") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $bonus_model = $this->model->get('bonusModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $bonus_model->deleteBonus($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|bonus|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'bonus',
                    'user_log_table_name' => 'Thưởng phạt dầu',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $bonus_model->deleteBonus($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|bonus|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'bonus',
                    'user_log_table_name' => 'Thưởng phạt dầu',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importbonus(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->bonus) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('bonus/import');

    }


}

?>