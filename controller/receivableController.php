<?php
Class receivableController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->receivable) || json_decode($_SESSION['user_permission_action'])->receivable != "receivable") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Công nợ phải thu';

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
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'debit_date';
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

        $this->view->data['page'] = $page;
        $this->view->data['order_by'] = $order_by;
        $this->view->data['order'] = $order;
        $this->view->data['keyword'] = $keyword;

        $this->view->data['batdau'] = $batdau;

        $this->view->data['ketthuc'] = $ketthuc;

        $this->view->data['vong'] = $vong;

        $this->view->data['trangthai'] = $trangthai;

        $this->view->data['page'] = $page;
        $this->view->data['order_by'] = $order_by;
        $this->view->data['order'] = $order;


        $debit_model = $this->model->get('debitModel');
        $debit_pay_model = $this->model->get('debitpayModel');

        $data = array(
            'where'=>'check_debit = 1 AND debit_date < '.strtotime($batdau),
        );
        $debit_olds = $debit_model->getAllDebit($data);

        $data_old = array();
        foreach ($debit_olds as $debit) {
            $pay_money = 0;
            $pays = $debit_pay_model->getAllDebit(array('where'=>'debit = '.$debit->debit_id));
            foreach ($pays as $pay) {
                $pay_money += $pay->debit_pay_money;
            }

            if ($debit->customer > 0) {
                $data_old['customer'][$debit->customer] = isset($data_old['customer'][$debit->customer])?$data_old['customer'][$debit->customer]+($debit->money+$debit->money_vat_price-$pay_money):($debit->money+$debit->money_vat_price-$pay_money);
            }
            else if ($debit->staff > 0) {
                $data_old['staff'][$debit->staff] = isset($data_old['staff'][$debit->staff])?$data_old['staff'][$debit->staff]+($debit->money+$debit->money_vat_price-$pay_money):($debit->money+$debit->money_vat_price-$pay_money);
            }
            else if ($debit->steersman > 0) {
                $data_old['steersman'][$debit->steersman] = isset($data_old['steersman'][$debit->steersman])?$data_old['steersman'][$debit->steersman]+($debit->money+$debit->money_vat_price-$pay_money):($debit->money+$debit->money_vat_price-$pay_money);
            }
        }

        $this->view->data['data_old'] = $data_old;

        $data = array(
            'where'=>'check_debit = 1 AND debit_date >= '.strtotime($batdau).' AND debit_date < '.strtotime($ngayketthuc),
        );
        $debit_news = $debit_model->getAllDebit($data);

        $data_new = array();
        foreach ($debit_news as $debit) {
            $pay_money = 0;
            $pays = $debit_pay_model->getAllDebit(array('where'=>'debit = '.$debit->debit_id));
            foreach ($pays as $pay) {
                $pay_money += $pay->debit_pay_money;
            }

            if ($debit->customer > 0) {
                $data_new['customer'][$debit->customer]['no'] = isset($data_new['customer'][$debit->customer]['no'])?$data_new['customer'][$debit->customer]['no']+($debit->money+$debit->money_vat_price):($debit->money+$debit->money_vat_price);
                $data_new['customer'][$debit->customer]['co'] = isset($data_new['customer'][$debit->customer]['co'])?$data_new['customer'][$debit->customer]['co']+($pay_money):($pay_money);
            }
            else if ($debit->staff > 0) {
                $data_new['staff'][$debit->staff]['no'] = isset($data_new['staff'][$debit->staff]['no'])?$data_new['staff'][$debit->staff]['no']+($debit->money+$debit->money_vat_price):($debit->money+$debit->money_vat_price);
                $data_new['staff'][$debit->staff]['co'] = isset($data_new['staff'][$debit->staff]['co'])?$data_new['staff'][$debit->staff]['co']+($pay_money):($pay_money);
            }
            else if ($debit->steersman > 0) {
                $data_new['steersman'][$debit->steersman]['no'] = isset($data_new['steersman'][$debit->steersman]['no'])?$data_new['steersman'][$debit->steersman]['no']+($debit->money+$debit->money_vat_price):($debit->money+$debit->money_vat_price);
                $data_new['steersman'][$debit->steersman]['co'] = isset($data_new['steersman'][$debit->steersman]['co'])?$data_new['steersman'][$debit->steersman]['co']+($pay_money):($pay_money);
            }
        }

        $this->view->data['data_new'] = $data_new;


        $steersman_model = $this->model->get('steersmanModel');

        $steersmans = $steersman_model->getAllSteersman(array('order_by'=>'steersman_name ASC'));

        $this->view->data['steersmans'] = $steersmans;


        $staff_model = $this->model->get('staffModel');

        $staffs = $staff_model->getAllStaff(array('order_by'=>'staff_name ASC'));
        $this->view->data['staffs'] = $staffs;


        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getAllCustomer(array('order_by'=>'customer_name ASC'));

        $this->view->data['customers'] = $customers;
        
        $this->view->show('receivable/index');
    }

   public function customer() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->receivable) || json_decode($_SESSION['user_permission_action'])->receivable != "receivable") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Phải thu khách hàng';

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



        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getAllCustomer();

        $this->view->data['customers'] = $customers;


        $debit_model = $this->model->get('debitModel');

        $join = array('table'=>'customer','where'=>'debit.customer=customer_id');

        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $data = array(
            'where' => 'check_debit = 1 AND check_loan = 2 AND customer > 0 AND debit_date >= '.strtotime($batdau).' AND debit_date < '.strtotime($ngayketthuc),
        );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND debit_id = '.$id;
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

        $this->view->data['kh'] = $kh;

        $data = array(
            'order_by'=>$order_by,
            'order'=>$order,
            'limit'=>$x.','.$sonews,
            'where' => 'check_debit = 1 AND check_loan = 2 AND customer > 0 AND debit_date >= '.strtotime($batdau).' AND debit_date < '.strtotime($ngayketthuc),
            );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND debit_id = '.$id;
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

        $vat_model = $this->model->get('vatModel');
        $debit_pay_model = $this->model->get('debitpayModel');

        $vat_data = array();
        $debit_data = array();
        foreach ($debits as $debit) {
            $debit_pays = $debit_pay_model->getAllDebit(array('where'=>'debit = '.$debit->debit_id));
            foreach ($debit_pays as $de) {
                $debit_data[$debit->debit_id] = isset($debit_data[$debit->debit_id])?$debit_data[$debit->debit_id]+$de->debit_pay_money:$de->debit_pay_money;
            }

            $debit_vats = $vat_model->getVAT($debit->vat);
            if ($debit_vats) {
                $vat_data[$debit->debit_id]['id'] = $debit_vats->vat_id;
                $vat_data[$debit->debit_id]['number'] = $debit_vats->vat_number;
            }
            
        }


        $this->view->data['debit_data'] = $debit_data;
        $this->view->data['vat_data'] = $vat_data;

        
        $this->view->show('receivable/customer');
    }
    public function staff() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->receivable) || json_decode($_SESSION['user_permission_action'])->receivable != "receivable") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Phải thu nhân viên';

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



        $staff_model = $this->model->get('staffModel');

        $staffs = $staff_model->getAllStaff();

        $this->view->data['staffs'] = $staffs;


        $debit_model = $this->model->get('debitModel');

        $join = array('table'=>'staff','where'=>'debit.staff=staff_id');

        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $data = array(
            'where' => 'check_debit = 1 AND check_loan = 2 AND staff > 0 AND debit_date >= '.strtotime($batdau).' AND debit_date < '.strtotime($ngayketthuc),
        );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND debit_id = '.$id;
        }

        if($kh > 0){
            $data['where'] = $data['where'].' AND debit.staff = '.$kh;
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

        $this->view->data['kh'] = $kh;

        $data = array(
            'order_by'=>$order_by,
            'order'=>$order,
            'limit'=>$x.','.$sonews,
            'where' => 'check_debit = 1 AND check_loan = 2 AND staff > 0 AND debit_date >= '.strtotime($batdau).' AND debit_date < '.strtotime($ngayketthuc),
            );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND debit_id = '.$id;
        }
        if($kh > 0){
            $data['where'] = $data['where'].' AND debit.staff = '.$kh;
        }
        
        if ($keyword != '') {
            $search = ' AND ( staff_name LIKE "%'.$keyword.'%"  
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

        
        $this->view->show('receivable/staff');
    }
    public function steersman() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->receivable) || json_decode($_SESSION['user_permission_action'])->receivable != "receivable") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Phải thu tài xế';

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



        $steersman_model = $this->model->get('steersmanModel');

        $steersmans = $steersman_model->getAllSteersman();

        $this->view->data['steersmans'] = $steersmans;


        $debit_model = $this->model->get('debitModel');

        $join = array('table'=>'steersman','where'=>'debit.steersman=steersman_id');

        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $data = array(
            'where' => 'check_debit = 1 AND check_loan = 2 AND steersman > 0 AND debit_date >= '.strtotime($batdau).' AND debit_date < '.strtotime($ngayketthuc),
        );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND debit_id = '.$id;
        }

        if($kh > 0){
            $data['where'] = $data['where'].' AND debit.steersman = '.$kh;
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

        $this->view->data['kh'] = $kh;

        $data = array(
            'order_by'=>$order_by,
            'order'=>$order,
            'limit'=>$x.','.$sonews,
            'where' => 'check_debit = 1 AND check_loan = 2 AND steersman > 0 AND debit_date >= '.strtotime($batdau).' AND debit_date < '.strtotime($ngayketthuc),
            );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND debit_id = '.$id;
        }
        if($kh > 0){
            $data['where'] = $data['where'].' AND debit.steersman = '.$kh;
        }
        
        if ($keyword != '') {
            $search = ' AND ( steersman_name LIKE "%'.$keyword.'%"  
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

        
        $this->view->show('receivable/steersman');
    }


}
?>