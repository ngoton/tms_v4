<?php
Class officecostController Extends baseController {
    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->officecost) || json_decode($_SESSION['user_permission_action'])->officecost != "officecost") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Tổng hợp chi phí hành chính';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;

            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;

            $kh = isset($_POST['nv']) ? $_POST['nv'] : null;
            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;
            $trangthai = isset($_POST['trangthai']) ? $_POST['trangthai'] : null;
        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'payment_voucher_date';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

            $kh = 0;

            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y'); //cal_days_in_month(CAL_GREGORIAN, date('m'), date('Y')).'-'.date('m-Y');
            $vong = (int)date('m',strtotime($batdau));
            $trangthai = date('Y',strtotime($batdau));

        }
        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));
        $trangthai = date('Y',strtotime($batdau));

        $costlist_model = $this->model->get('costlistModel');

        $this->view->data['cost_lists'] = $costlist_model->getAllCost(array('where'=>'cost_list_type = 1'));



        $join = array('table'=>'cost_list, bank','where'=>'cost_list = cost_list_id AND bank_out = bank_id');



        $payment_model = $this->model->get('paymentvoucherModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;



        $data = array(

            'where' => 'payment_voucher_type = 2 AND payment_voucher_date >= '.strtotime($batdau).' AND payment_voucher_date < '.strtotime($ngayketthuc),

            );


        if($kh > 0){

            $data['where'] = $data['where'].' AND cost_list = '.$kh;

        }


        /*if ($_SESSION['role_logined'] == 3) {

            $data['where'] = $data['where'].' AND shipment_create_user = '.$_SESSION['userid_logined'];

            

        }*/



        $tongsodong = count($payment_model->getAllPayment($data,$join));

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


        $this->view->data['kh'] = $kh;
        $this->view->data['vong'] = $vong;
        $this->view->data['trangthai'] = $trangthai;




        $data = array(

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            'where' => 'payment_voucher_type = 2 AND payment_voucher_date >= '.strtotime($batdau).' AND payment_voucher_date < '.strtotime($ngayketthuc),

            );


        if($kh > 0){

            $data['where'] = $data['where'].' AND cost_list = '.$kh;

        }


        /*if ($_SESSION['role_logined'] == 3) {

            $data['where'] = $data['where'].' AND shipment_create_user = '.$_SESSION['userid_logined'];

            

        }*/



        if ($keyword != '') {

            $ngay = (strtotime(str_replace("/", "-", $keyword))!="")?(' OR payment_voucher_date LIKE "%'.strtotime(str_replace("/", "-", $keyword)).'%"'):"";

            $search = '(

                    payment_voucher_number LIKE "%'.$keyword.'%"

                    OR cost_list_name LIKE "%'.$keyword.'%"

                    '.$ngay.'

                        )';

            $data['where'] = $data['where']." AND ".$search;

        }


        $payments = $payment_model->getAllPayment($data,$join);


        $this->view->data['payments'] = $payments;


        $this->view->data['lastID'] = isset($payment_model->getLastPayment()->payment_voucher_id)?$payment_model->getLastPayment()->payment_voucher_id:0;


        $this->view->show('officecost/index');

    }
    


}
?>