<?php
Class repairsalaryController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->repairsalary) || json_decode($_SESSION['user_permission_action'])->repairsalary != "repairsalary") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Chi phí sửa chữa bảo dưỡng';

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
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'staff_name';
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
        
        $staff_model = $this->model->get('staffModel');
        $staffs = $staff_model->getAllStaff(array('where'=>'staff_id IN (SELECT staff FROM repair)','order_by'=>'staff_name','order'=>'ASC'));
        $this->view->data['staffs'] = $staffs;


        $repair_model = $this->model->get('repairModel');
        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $join = array('table'=>'staff','where'=>'staff=staff_id');

        $data = array(
            'where' => 'repair_date >= '.strtotime($batdau).' AND repair_date < '.strtotime($ngayketthuc),
        );

        
        $tongsodong = count($repair_model->getAllRepair($data,$join));
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
            'where' => 'repair_date >= '.strtotime($batdau).' AND repair_date < '.strtotime($ngayketthuc),
            );

       
        
        if ($keyword != '') {
            $search = ' AND ( repair_code LIKE "%'.$keyword.'%" 
                        OR staff_name LIKE "%'.$keyword.'%" )';
            $data['where'] .= $search;
        }
        
        $repairs = $repair_model->getAllRepair($data,$join);

        $data_repair = array();
        foreach ($repairs as $repair) {
            $data_repair[$repair->staff] = isset($data_repair[$repair->staff])?$data_repair[$repair->staff]+$repair->repair_price:$repair->repair_price;
        }

        $this->view->data['data_repair'] = $data_repair;
        
        $this->view->show('repairsalary/index');
    }

    

}
?>