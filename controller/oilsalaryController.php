<?php

Class oilsalaryController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Bảng tính dầu';



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

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'gas_date';

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

        $gas_model = $this->model->get('gasModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $data = array(
            'where'=>'1=1',
        );

        if ($batdau!="") {
            $data['where'] .= ' AND gas_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND gas_date < '.$ngayketthuc;
        }

        $join = array('table'=>'vehicle', 'where'=>'gas_vehicle=vehicle_id');

        if (isset($_POST['filter'])) {
            if (isset($_POST['gas_vehicle'])) {
                $data['where'] .= ' AND gas_vehicle IN ('.implode(',',$_POST['gas_vehicle']).')';
            }

            $this->view->data['filter'] = 1;
        }

        $tongsodong = count($gas_model->getAllGas($data,$join));

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
            $data['where'] .= ' AND gas_date >= '.$ngaybatdau;
        }
        if ($ketthuc!="") {
            $data['where'] .= ' AND gas_date < '.$ngayketthuc;
        }

        if (isset($_POST['filter'])) {
            if (isset($_POST['gas_vehicle'])) {
                $data['where'] .= ' AND gas_vehicle IN ('.implode(',',$_POST['gas_vehicle']).')';
            }
        }

        if ($keyword != '') {

            $search = '( vehicle_number LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }

        $vehicles = $gas_model->getAllGas($data,$join);

        $this->view->data['vehicles'] = $vehicles;

        $bonus_model = $this->model->get('bonusModel');

        $bonus_data = array();
        foreach ($vehicles as $vehicle) {
            $data = array(
                'where'=>'bonus_start_date <= '.$vehicle->gas_date.' AND (bonus_end_date IS NULL OR bonus_end_date=0 OR bonus_end_date >= '.$vehicle->gas_date.')',
            );
            $bonus = $bonus_model->getAllBonus($data);
            foreach ($bonus as $bo) {
                $bonus_data[$vehicle->gas_id]['plus'] = $bo->bonus_plus;
                $bonus_data[$vehicle->gas_id]['minus'] = $bo->bonus_minus;
            }

            $data = array(
                'where'=>'gas_vehicle = '.$vehicle->gas_vehicle.' AND gas_date <= '.$vehicle->gas_date.' AND gas_id != '.$vehicle->gas_id,
                'order_by'=>'gas_date DESC, gas_km DESC, gas_km_gps DESC',
                'limit'=>1
            );
            $gas = $gas_model->getAllGas($data);
            foreach ($gas as $ga) {
                $bonus_data[$vehicle->gas_id]['km'] = $ga->gas_km;
                $bonus_data[$vehicle->gas_id]['km_gps'] = $ga->gas_km_gps;
                $bonus_data[$vehicle->gas_id]['lit'] = $ga->gas_lit;
            }
        }
        $this->view->data['bonus_data'] = $bonus_data;


        return $this->view->show('oilsalary/index');

    }

    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];
        $this->view->data['nv'] = $_GET['nv'];
        $this->view->data['tha'] = $_GET['tha'];
        $this->view->data['na'] = $_GET['na'];
        $this->view->data['batdau'] = $_GET['batdau'];
        $this->view->data['ketthuc'] = $_GET['ketthuc'];

        return $this->view->show('oilsalary/filter');
    }

}

?>