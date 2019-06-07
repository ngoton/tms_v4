<?php
Class bankbalanceController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->bankbalance) || json_decode($_SESSION['user_permission_action'])->bankbalance != "bankbalance") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Số dư tài khoản tiền mặt ngân hàng';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;
            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'bank_code ASC, bank_name';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 18446744073709;
            $batdau = '01-'.date('m-Y');
            $ketthuc = date('t-m-Y');
            $vong = (int)date('m',strtotime($batdau));
            $trangthai = date('Y',strtotime($batdau));
        }
        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));
        $vong = (int)date('m',strtotime($batdau));
        $trangthai = date('Y',strtotime($batdau));


        $bank_model = $this->model->get('bankModel');

        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;
        
        $data = array(
            'where' => '1=1',
        );
        
        
        $tongsodong = count($bank_model->getAllBank($data));
        $tongsotrang = ceil($tongsodong / $sonews);
        

        $this->view->data['page'] = $page;
        $this->view->data['order_by'] = $order_by;
        $this->view->data['order'] = $order;
        $this->view->data['keyword'] = $keyword;
        $this->view->data['pagination_stages'] = $pagination_stages;
        $this->view->data['tongsotrang'] = $tongsotrang;
        $this->view->data['limit'] = $limit;
        $this->view->data['sonews'] = $sonews;
        $this->view->data['batdau'] = $batdau;
        $this->view->data['ketthuc'] = $ketthuc;
        $this->view->data['vong'] = $vong;
        $this->view->data['trangthai'] = $trangthai;

        $data = array(
            'order_by'=>$order_by,
            'order'=>$order,
            'limit'=>$x.','.$sonews,
            'where' => '1=1',
            );
        
      
        if ($keyword != '') {
            $search = '( bank_code LIKE "%'.$keyword.'%" 
                    OR bank_name LIKE "%'.$keyword.'%" 
                )';
            
                $data['where'] = $data['where'].' AND '.$search;
        }

        
        $banks = $bank_model->getAllBank($data);
        
        $this->view->data['banks'] = $banks;

        $bank_balance_model = $this->model->get('bankbalanceModel');

        $data_bank = array();

        $balances = $bank_balance_model->getAllBank(array('where'=>'bank_balance_date < '.strtotime($batdau)));

        foreach ($balances as $ba) {
            $data_bank[$ba->bank]['dauki'] = isset($data_bank[$ba->bank]['dauki'])?$data_bank[$ba->bank]['dauki']+$ba->bank_balance_money:$ba->bank_balance_money;
        }

        $balances = $bank_balance_model->getAllBank(array('where'=>'bank_balance_date >= '.strtotime($batdau).' AND bank_balance_date < '.strtotime($ngayketthuc)));

        
        foreach ($balances as $balance) {
            if ($balance->bank_balance_money > 0) {
                $data_bank[$balance->bank]['receipt'] = isset($data_bank[$balance->bank]['receipt'])?$data_bank[$balance->bank]['receipt']+$balance->bank_balance_money:$balance->bank_balance_money;
            }
            else {
                $data_bank[$balance->bank]['payment'] = isset($data_bank[$balance->bank]['payment'])?$data_bank[$balance->bank]['payment']+$balance->bank_balance_money:$balance->bank_balance_money;
            }
        }

        $this->view->data['data_bank'] = $data_bank;

        $this->view->data['lastID'] = isset($bank_model->getLastBank()->bank_id)?$bank_model->getLastBank()->bank_id:0;

        /* Lấy tổng doanh thu*/
        
        /*************/
        $this->view->show('bankbalance/index');
    }

    public function getBank(){
        if (isset($_POST['data'])) {
            $bank_balance_model = $this->model->get('bankbalanceModel');

            $bank = $_POST['data'];
            $batdau = $_POST['batdau'];
            $ketthuc = $_POST['ketthuc'];
            $type = $_POST['type'];
            $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

            $join = null;
            $data = array('where'=>'bank = '.$bank.' AND bank_balance_date >= '.strtotime($batdau).' AND bank_balance_date < '.strtotime($ngayketthuc));

            if ($type == 1) {
                $join = array('table'=>'receipt_voucher','where'=>'receipt_voucher = receipt_voucher_id AND receipt_voucher > 0');
                $balances = $bank_balance_model->getAllBank($data,$join);

                $join = array('table'=>'internal_transfer','where'=>'internal_transfer = internal_transfer_id AND bank_balance_money > 0');
                $balances2 = $bank_balance_model->getAllBank($data,$join);

                $tr = "";
                $tong = 0;

                $tr .= '<table class="table_data"><thead><tr><th>Ngày</th><th>ND</th><th>Số tiền</th></tr></thead><tbody>';
                foreach ($balances as $v) {
                    $tr.= '<tr>';
                    $tr.= '<td>'.$this->lib->hien_thi_ngay_thang($v->bank_balance_date).'</td>';
                    $tr.= '<td><a target="_blank" href="'.BASE_URL.'/receiptvoucher/index/'.$v->receipt_voucher_id.'">'.$v->receipt_voucher_comment.'</a></td>';
                    $tr.= '<td>'.$this->lib->formatMoney($v->bank_balance_money).'</td>';
                    $tr.= '</tr>';
                    $tong +=$v->bank_balance_money;
                }
                foreach ($balances2 as $v) {
                    $tr.= '<tr>';
                    $tr.= '<td>'.$this->lib->hien_thi_ngay_thang($v->bank_balance_date).'</td>';
                    $tr.= '<td><a target="_blank" href="'.BASE_URL.'/internaltransfer/index/'.$v->internal_transfer_id.'">'.$v->internal_transfer_comment.'</a></td>';
                    $tr.= '<td>'.$this->lib->formatMoney($v->bank_balance_money).'</td>';
                    $tr.= '</tr>';
                    $tong +=$v->bank_balance_money;
                }
                $tr.= '<tfoot><tr style="color:red"><td colspan="2">Tổng cộng</td><td>'.$this->lib->formatMoney($tong).'</td></tr><tr><td colspan="3" class="text-right">Bấm "ESC" để đóng!</td></tr></tfoot>';
                $tr.= "</tbody></table>";
            }
            else if ($type == 2) {
                $join = array('table'=>'payment_voucher','where'=>'payment_voucher = payment_voucher_id AND payment_voucher > 0');
                $balances = $bank_balance_model->getAllBank($data,$join);

                $join = array('table'=>'internal_transfer','where'=>'internal_transfer = internal_transfer_id AND bank_balance_money < 0');
                $balances2 = $bank_balance_model->getAllBank($data,$join);

                $tr = "";
                $tong = 0;

                $tr .= '<table class="table_data"><thead><tr><th>Ngày</th><th>ND</th><th>Số tiền</th></tr></thead><tbody>';
                foreach ($balances as $v) {
                    $tr.= '<tr>';
                    $tr.= '<td>'.$this->lib->hien_thi_ngay_thang($v->bank_balance_date).'</td>';
                    $tr.= '<td><a target="_blank" href="'.BASE_URL.'/paymentvoucher/index/'.$v->payment_voucher_id.'">'.$v->payment_voucher_comment.'</a></td>';
                    $tr.= '<td>'.$this->lib->formatMoney($v->bank_balance_money).'</td>';
                    $tr.= '</tr>';
                    $tong +=$v->bank_balance_money;
                }
                foreach ($balances2 as $v) {
                    $tr.= '<tr>';
                    $tr.= '<td>'.$this->lib->hien_thi_ngay_thang($v->bank_balance_date).'</td>';
                    $tr.= '<td><a target="_blank" href="'.BASE_URL.'/internaltransfer/index/'.$v->internal_transfer_id.'">'.$v->internal_transfer_comment.'</a></td>';
                    $tr.= '<td>'.$this->lib->formatMoney($v->bank_balance_money).'</td>';
                    $tr.= '</tr>';
                    $tong +=$v->bank_balance_money;
                }
                $tr.= '<tfoot><tr style="color:red"><td colspan="2">Tổng cộng</td><td>'.$this->lib->formatMoney($tong).'</td></tr><tr><td colspan="3" class="text-right">Bấm "ESC" để đóng!</td></tr></tfoot>';
                $tr.= "</tbody></table>";
            }


            echo $tr;
        }
    }

   

}
?>