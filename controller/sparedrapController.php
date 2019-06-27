<?php

Class sparedrapController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý phụ tùng thay ra';



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

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'end_time ASC, code';

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

        $vehicle_model = $this->model->get('vehicleModel');
        $romooc_model = $this->model->get('romoocModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));
        $vehicle_data = array();
        foreach ($vehicles as $ve) {
            $vehicle_data[$ve->vehicle_id] = $ve->vehicle_number;
        }
        $this->view->data['vehicle_data'] = $vehicle_data;
        
        $romoocs = $romooc_model->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));
        $romooc_data = array();
        foreach ($romoocs as $ro) {
            $romooc_data[$ro->romooc_id] = $ro->romooc_number;
        }
        $this->view->data['romooc_data'] = $romooc_data;

        $spare_drap_model = $this->model->get('sparedrapModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $join = array('table'=>'spare_vehicle','where'=>'spare_vehicle = spare_vehicle_id LEFT JOIN spare_part ON spare_drap.spare_part = spare_part_id LEFT JOIN spare_part_code ON spare_part_code=spare_part_code_id','join'=>'LEFT JOIN');
        $data = array(
            'where'=>'1=1',
        );

        if ($batdau!="") {
            $data['where'] .= ' AND end_time >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND end_time < '.$ngayketthuc;
        }

        if (isset($_POST['filter'])) {
            
            if (isset($_POST['vehicle'])) {
                $data['where'] .= ' AND vehicle IN ('.implode(',',$_POST['vehicle']).')';
            }
            if (isset($_POST['romooc'])) {
                $data['where'] .= ' AND romooc IN ('.implode(',',$_POST['romooc']).')';
            }
            if (isset($_POST['spare_part'])) {
                $data['where'] .= ' AND spare_part_code_id IN ('.implode(',',$_POST['spare_part']).')';
            }
            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($spare_drap_model->getAllStock($data, $join));

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

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            'where'=>'1=1',

            );

        if ($batdau!="") {
            $data['where'] .= ' AND end_time >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND end_time < '.$ngayketthuc;
        }

        if (isset($_POST['filter'])) {
            
            if (isset($_POST['vehicle'])) {
                $data['where'] .= ' AND vehicle IN ('.implode(',',$_POST['vehicle']).')';
            }
            if (isset($_POST['romooc'])) {
                $data['where'] .= ' AND romooc IN ('.implode(',',$_POST['romooc']).')';
            }
            if (isset($_POST['spare_part'])) {
                $data['where'] .= ' AND spare_part_code_id IN ('.implode(',',$_POST['spare_part']).')';
            }
            
        }

        if ($keyword != '') {

            $search = ' ( code LIKE "%'.$keyword.'%" 
                        OR spare_part_name LIKE "%'.$keyword.'%" 
                        OR spare_part_seri LIKE "%'.$keyword.'%" 
                        OR spare_part_brand LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $spares = $spare_drap_model->getAllStock($data,$join);
        $this->view->data['spares'] = $spares;



        return $this->view->show('sparedrap/index');

    }
    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $vehicle = $this->model->get('vehicleModel');

        $this->view->data['vehicles'] = $vehicle->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $romooc = $this->model->get('romoocModel');

        $this->view->data['romoocs'] = $romooc->getAllRomooc(array('order_by'=>'romooc_number','order'=>'ASC'));

        $spare_part_code_model = $this->model->get('sparepartcodeModel');

        $this->view->data['spares'] = $spare_part_code_model->getAllStock(array('order_by'=>'name','order'=>'ASC'));

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('sparedrap/filter');
    }



}

?>