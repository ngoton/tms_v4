<?php
Class loanController Extends baseController {
    
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->loan) || json_decode($_SESSION['user_permission_action'])->loan != "loan") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Chi hộ';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $xe = isset($_POST['xe']) ? $_POST['xe'] : null;

            $kh = isset($_POST['nv']) ? $_POST['nv'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;
            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;

            $trangthai = isset($_POST['staff']) ? $_POST['staff'] : null;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'debit_date';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

            $xe = 0;

            $kh = 0;

            $vong = (int)date('m',strtotime($batdau));

            $trangthai = date('Y',strtotime($batdau));
        }

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));

        $trangthai = date('Y',strtotime($batdau));
        
        $id = $this->registry->router->param_id;


        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getAllCustomer(array('order_by'=>'customer_name','order'=>'ASC'));

        $this->view->data['customers'] = $customers;


        $debit_model = $this->model->get('debitModel');

        $join = array('table'=>'vehicle,shipment,customer,shipment_cost','where'=>'debit.customer=customer_id AND debit.shipment_cost=shipment_cost_id AND shipment_cost.shipment=shipment_id AND shipment.vehicle=vehicle_id');

        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $data = array(
            'where' => 'check_loan = 1 AND debit_date >= '.strtotime($batdau).' AND debit_date < '.strtotime($ngayketthuc),
        );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND debit_id = '.$id;
        }
        if($xe != 0){
            $data['where'] = $data['where'].' AND vehicle = '.$xe;
        }

        if($kh > 0){
            $data['where'] = $data['where'].' AND debit.customer = '.$kh;
        }

        $tongsodong = count($debit_model->getAllDebit($data,$join));
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

        $this->view->data['xe'] = $xe;
        $this->view->data['kh'] = $kh;

        $data = array(
            'order_by'=>$order_by,
            'order'=>$order,
            'limit'=>$x.','.$sonews,
            'where' => 'check_loan = 1 AND debit_date >= '.strtotime($batdau).' AND debit_date < '.strtotime($ngayketthuc),
            );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND debit_id = '.$id;
        }
        if($xe != 0){
            $data['where'] = $data['where'].' AND vehicle = '.$xe;
        }

        if($kh > 0){
            $data['where'] = $data['where'].' AND debit.customer = '.$kh;
        }
        
        if ($keyword != '') {
            $search = ' AND ( customer_name LIKE "%'.$keyword.'%"  
                        OR debit.comment LIKE "%'.$keyword.'%" 
                        )';
            $data['where'] .= $search;
        }
        
        $debits = $debit_model->getAllDebit($data,$join);
        $this->view->data['debits'] = $debits;

        $debit_pay_model = $this->model->get('debitpayModel');

        $debit_data = array();
        foreach ($debits as $debit) {
            $debit_pays = $debit_pay_model->getAllDebit(array('where'=>'debit = '.$debit->debit_id));
            foreach ($debit_pays as $de) {
                $debit_data[$debit->debit_id] = isset($debit_data[$debit->debit_id])?$debit_data[$debit->debit_id]+$de->debit_pay_money:$de->debit_pay_money;
            }
        }


        $this->view->data['debit_data'] = $debit_data;

        
        $this->view->show('loan/index');
    }
    

}
?>