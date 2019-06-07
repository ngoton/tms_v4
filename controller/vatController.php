<?php
Class vatController Extends baseController {
    public function index() {

    }
    public function in() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->vat) || json_decode($_SESSION['user_permission_action'])->vat != "vat") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Hóa đơn đầu vào';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;
            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;

            $trangthai = isset($_POST['staff']) ? $_POST['staff'] : null;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'vat_date';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

            $vong = (int)date('m',strtotime($batdau));

            $trangthai = date('Y',strtotime($batdau));
        }

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));

        $trangthai = date('Y',strtotime($batdau));
        
        $id = $this->registry->router->param_id;

        $vat_model = $this->model->get('vatModel');


        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $data = array(
            'where' => 'in_out = 1 AND vat_date >= '.strtotime($batdau).' AND vat_date < '.strtotime($ngayketthuc),
        );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND vat_id = '.$id;
        }

        $tongsodong = count($vat_model->getAllVAT($data));
        $tongsotrang = ceil($tongsodong / $sonews);
        

        $this->view->data['page'] = $page;
        $this->view->data['order_by'] = $order_by;
        $this->view->data['order'] = $order;
        $this->view->data['keyword'] = $keyword;
        $this->view->data['pagination_stages'] = $pagination_stages;
        $this->view->data['tongsotrang'] = $tongsotrang;
        $this->view->data['sonews'] = $sonews;
        $this->view->data['limit'] = $limit;
        $this->view->data['batdau'] = $batdau;

        $this->view->data['ketthuc'] = $ketthuc;

        $this->view->data['vong'] = $vong;

        $this->view->data['trangthai'] = $trangthai;

        $data = array(
            'order_by'=>$order_by,
            'order'=>$order,
            'limit'=>$x.','.$sonews,
            'where' => 'in_out = 1 AND vat_date >= '.strtotime($batdau).' AND vat_date < '.strtotime($ngayketthuc),
            );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND vat_id = '.$id;
        }
        
        if ($keyword != '') {
            $search = ' AND ( vat_number LIKE "%'.$keyword.'%"  )';
            $data['where'] .= $search;
        }
        
        $vats = $vat_model->getAllVAT($data);
        $this->view->data['vats'] = $vats;

        $shipment_cost_model = $this->model->get('shipmentcostModel');
        $import_stock_model = $this->model->get('importstockModel');
        $import_stock_cost_model = $this->model->get('importstoccostkModel');
        $toll_cost_model = $this->model->get('tollcostModel');
        $road_cost_model = $this->model->get('roadcostModel');
        $checking_cost_model = $this->model->get('checkingcostModel');
        $insurance_cost_model = $this->model->get('insurancecostModel');

        $data_customer = array();
        foreach ($vats as $vat) {
            if ($vat->shipment_cost > 0) {
                $join = array('table'=>'customer','where'=>'receiver = customer_id');
                $data = array(
                    'where'=>'shipment_cost_id = '.$vat->shipment_cost,
                );
                $costs = $shipment_cost_model->getAllShipment($data,$join);
                foreach ($costs as $cost) {
                    $data_customer[$vat->vat_id]['link'] = BASE_URL.'/customer/editcus/'.$cost->receiver;
                    $data_customer[$vat->vat_id]['name'] = $cost->customer_name;
                    $data_customer[$vat->vat_id]['mst'] = $cost->customer_mst;
                }
            }
            else if ($vat->import_stock > 0) {
                $join = array('table'=>'customer','where'=>'invoice_customer = customer_id');
                $data = array(
                    'where'=>'import_stock_id = '.$vat->import_stock,
                );
                $costs = $import_stock_model->getAllStock($data,$join);
                foreach ($costs as $cost) {
                    $data_customer[$vat->vat_id]['link'] = BASE_URL.'/customer/editcus/'.$cost->invoice_customer;
                    $data_customer[$vat->vat_id]['name'] = $cost->customer_name;
                    $data_customer[$vat->vat_id]['mst'] = $cost->customer_mst;
                }
            }
            else if ($vat->import_stock_cost > 0) {
                $join = array('table'=>'customer','where'=>'receiver = customer_id');
                $data = array(
                    'where'=>'import_stock_cost_id = '.$vat->import_stock_cost,
                );
                $costs = $import_stock_cost_model->getAllStock($data,$join);
                foreach ($costs as $cost) {
                    $data_customer[$vat->vat_id]['link'] = BASE_URL.'/customer/editcus/'.$cost->receiver;
                    $data_customer[$vat->vat_id]['name'] = $cost->customer_name;
                    $data_customer[$vat->vat_id]['mst'] = $cost->customer_mst;
                }
            }
            else if ($vat->toll_cost > 0) {
                $join = array('table'=>'toll','where'=>'toll = toll_id');
                $data = array(
                    'where'=>'toll_cost_id = '.$vat->toll_cost,
                );
                $costs = $toll_cost_model->getAllToll($data,$join);
                foreach ($costs as $cost) {
                    $data_customer[$vat->vat_id]['link'] = BASE_URL.'/toll/index/'.$cost->toll;
                    $data_customer[$vat->vat_id]['name'] = $cost->toll_name;
                    $data_customer[$vat->vat_id]['mst'] = $cost->toll_mst;
                }
            }
            else if ($vat->road_cost > 0) {
                $join = array('table'=>'customer','where'=>'customer = customer_id');
                $data = array(
                    'where'=>'road_cost_id = '.$vat->road_cost,
                );
                $costs = $road_cost_model->getAllCost($data,$join);
                foreach ($costs as $cost) {
                    $data_customer[$vat->vat_id]['link'] = BASE_URL.'/customer/editcus/'.$cost->customer;
                    $data_customer[$vat->vat_id]['name'] = $cost->customer_name;
                    $data_customer[$vat->vat_id]['mst'] = $cost->customer_mst;
                }
            }
            else if ($vat->checking_cost > 0) {
                $join = array('table'=>'customer','where'=>'customer = customer_id');
                $data = array(
                    'where'=>'checking_cost_id = '.$vat->checking_cost,
                );
                $costs = $checking_cost_model->getAllCost($data,$join);
                foreach ($costs as $cost) {
                    $data_customer[$vat->vat_id]['link'] = BASE_URL.'/customer/editcus/'.$cost->customer;
                    $data_customer[$vat->vat_id]['name'] = $cost->customer_name;
                    $data_customer[$vat->vat_id]['mst'] = $cost->customer_mst;
                }
            }
            else if ($vat->insurance_cost > 0) {
                $join = array('table'=>'customer','where'=>'customer = customer_id');
                $data = array(
                    'where'=>'insurance_cost_id = '.$vat->insurance_cost,
                );
                $costs = $insurance_cost_model->getAllCost($data,$join);
                foreach ($costs as $cost) {
                    $data_customer[$vat->vat_id]['link'] = BASE_URL.'/customer/editcus/'.$cost->customer;
                    $data_customer[$vat->vat_id]['name'] = $cost->customer_name;
                    $data_customer[$vat->vat_id]['mst'] = $cost->customer_mst;
                }
            }
        }


        $this->view->data['data_customer'] = $data_customer;

        
        $this->view->show('vat/in');
    }
    public function out() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->vat) || json_decode($_SESSION['user_permission_action'])->vat != "vat") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Hóa đơn đầu ra';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;
            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;

            $trangthai = isset($_POST['staff']) ? $_POST['staff'] : null;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'vat_date';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

            $vong = (int)date('m',strtotime($batdau));

            $trangthai = date('Y',strtotime($batdau));
        }

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));

        $trangthai = date('Y',strtotime($batdau));
        
        $id = $this->registry->router->param_id;

        $vat_model = $this->model->get('vatModel');

        $join = array('table'=>'customer','where'=>'customer = customer_id');

        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $data = array(
            'where' => 'in_out = 2 AND vat_date >= '.strtotime($batdau).' AND vat_date < '.strtotime($ngayketthuc),
        );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND vat_id = '.$id;
        }

        $tongsodong = count($vat_model->getAllVAT($data,$join));
        $tongsotrang = ceil($tongsodong / $sonews);
        

        $this->view->data['page'] = $page;
        $this->view->data['order_by'] = $order_by;
        $this->view->data['order'] = $order;
        $this->view->data['keyword'] = $keyword;
        $this->view->data['pagination_stages'] = $pagination_stages;
        $this->view->data['tongsotrang'] = $tongsotrang;
        $this->view->data['sonews'] = $sonews;
        $this->view->data['limit'] = $limit;
        $this->view->data['batdau'] = $batdau;

        $this->view->data['ketthuc'] = $ketthuc;

        $this->view->data['vong'] = $vong;

        $this->view->data['trangthai'] = $trangthai;

        $data = array(
            'order_by'=>$order_by,
            'order'=>$order,
            'limit'=>$x.','.$sonews,
            'where' => 'in_out = 2 AND vat_date >= '.strtotime($batdau).' AND vat_date < '.strtotime($ngayketthuc),
            );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND vat_id = '.$id;
        }
        
        if ($keyword != '') {
            $search = ' AND ( vat_number LIKE "%'.$keyword.'%"  )';
            $data['where'] .= $search;
        }
        
        $vats = $vat_model->getAllVAT($data,$join);
        $this->view->data['vats'] = $vats;

        $this->view->show('vat/out');
    }
   


}
?>