<?php

Class shippingController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý hãng tàu';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'shipping_name';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $shipping_model = $this->model->get('shippingModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $join = array('table'=>'country','where'=>'shipping_country=country_id');

        $tongsodong = count($shipping_model->getAllShipping(null,$join));

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

            $search = '( shipping_name LIKE "%'.$keyword.'%" OR country_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['shippings'] = $shipping_model->getAllShipping($data,$join);



        return $this->view->show('shipping/index');

    }


    public function addshipping(){
        $shipping_model = $this->model->get('shippingModel');

        if (isset($_POST['shipping_name'])) {
            if($shipping_model->getShippingByWhere(array('shipping_name'=>trim($_POST['shipping_name'])))){
                echo 'Tên hãng tàu đã tồn tại';
                return false;
            }

            $data = array(
                'shipping_country' => trim($_POST['shipping_country']),
                'shipping_name' => trim($_POST['shipping_name']),
            );
            $shipping_model->createshipping($data);

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$shipping_model->getLastShipping()->shipping_id."|shipping|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'shipping',
                'user_log_table_name' => 'Hãng tàu',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipping) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới hãng tàu';

        $country = $this->model->get('countryModel');

        $this->view->data['countrys'] = $country->getAllCountry();

        return $this->view->show('shipping/add');
    }

    public function editshipping(){
        $shipping_model = $this->model->get('shippingModel');

        if (isset($_POST['shipping_id'])) {
            $id = $_POST['shipping_id'];
            if($shipping_model->getAllShippingByWhere($id.' AND shipping_name = "'.trim($_POST['shipping_name']).'"')){
                echo 'Tên hãng tàu đã tồn tại';
                return false;
            }

            $data = array(
                'shipping_country' => trim($_POST['shipping_country']),
                'shipping_name' => trim($_POST['shipping_name']),
            );
            $shipping_model->updateShipping($data,array('shipping_id'=>$id));

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|shipping|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'shipping',
                'user_log_table_name' => 'Hãng tàu',
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipping) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('shipping');

        }

        $this->view->data['title'] = 'Cập nhật hãng tàu';

        $shipping_model = $this->model->get('shippingModel');

        $shipping_data = $shipping_model->getShipping($id);

        $this->view->data['shipping_data'] = $shipping_data;

        if (!$shipping_data) {

            $this->view->redirect('shipping');

        }

        $country = $this->model->get('countryModel');

        $this->view->data['countrys'] = $country->getAllCountry();

        return $this->view->show('shipping/edit');

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

            $this->view->redirect('shipping');

        }

        $this->view->data['title'] = 'Thông tin hãng tàu';

        $shipping_model = $this->model->get('shippingModel');

        $shipping_data = $shipping_model->getShipping($id);

        $this->view->data['shipping_data'] = $shipping_data;

        if (!$shipping_data) {

            $this->view->redirect('shipping');

        }

        $country = $this->model->get('countryModel');

        $this->view->data['countrys'] = $country->getAllCountry();

        return $this->view->show('shipping/view');

    }

    public function getshipping(){
        $shipping_model = $this->model->get('shippingModel');

        $shippings = $shipping_model->getAllShipping(array('order_by'=>'shipping_name','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($shippings as $shipping) {
            $result[$i]['id'] = $shipping->shipping_id;
            $result[$i]['text'] = $shipping->shipping_name;
            $i++;
        }
        echo json_encode($result);
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->shipping) || json_decode($_SESSION['user_permission_action'])->shipping != "shipping") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $shipping_model = $this->model->get('shippingModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $shipping_model->deleteShipping($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|shipping|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'shipping',
                    'user_log_table_name' => 'Hãng tàu',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $shipping_model->deleteShipping($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|shipping|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'shipping',
                    'user_log_table_name' => 'Hãng tàu',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importshipping(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->shipping) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('shipping/import');

    }


}

?>