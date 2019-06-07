<?php
Class repairlistController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->repairlist) || json_decode($_SESSION['user_permission_action'])->repairlist != "repairlist") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Tổng hợp chi phí sửa chữa bảo dưỡng';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
            $batdau = isset($_POST['batdau']) ? $_POST['batdau'] : null;
            $ketthuc = isset($_POST['ketthuc']) ? $_POST['ketthuc'] : null;
            $xe = isset($_POST['xe']) ? $_POST['xe'] : null;
            $mooc = isset($_POST['nv']) ? $_POST['nv'] : null;
            $vong = isset($_POST['vong']) ? $_POST['vong'] : null;
            $trangthai = isset($_POST['trangthai']) ? $_POST['trangthai'] : null;
            
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'repair_date';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
            $batdau = '01-'.date('m-Y');
            $ketthuc = date('t-m-Y');
            $xe = 0;
            $mooc = 0;
            $vong = (int)date('m',strtotime($batdau));
            $trangthai = date('Y',strtotime($batdau));
        }

        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));
        $trangthai = date('Y',strtotime($batdau));


        $vehicle_model = $this->model->get('vehicleModel');
        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $vehicle_data = array();
        foreach ($vehicles as $vehicle) {
            $vehicle_data['id'][$vehicle->vehicle_id] = $vehicle->vehicle_id;
            $vehicle_data['name'][$vehicle->vehicle_id] = $vehicle->vehicle_number;
        }
        $this->view->data['vehicle_data'] = $vehicle_data;

        $romooc_model = $this->model->get('romoocModel');
        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));

        $this->view->data['romoocs'] = $romoocs;

        $romooc_data = array();
        foreach ($romoocs as $romooc) {
            $romooc_data['id'][$romooc->romooc_id] = $romooc->romooc_id;
            $romooc_data['name'][$romooc->romooc_id] = $romooc->romooc_number;
        }
        $this->view->data['romooc_data'] = $romooc_data;

        $repair_list_model = $this->model->get('repairlistModel');

        $join = array('table'=>'repair, staff','where'=>'staff = staff_id AND repair = repair_id AND repair_date >= '.strtotime($batdau).' AND repair_date < '.strtotime($ngayketthuc));

        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $data = array(
            'where' => "1=1",
            );
        
        if($xe > 0){
            $data['where'] = $data['where'].' AND vehicle = '.$xe;
        }

        if($mooc > 0){
            $data['where'] = $data['where'].' AND romooc = '.$mooc;
        }

        $tongsodong = count($repair_list_model->getAllRepair($data,$join));
        $tongsotrang = ceil($tongsodong / $sonews);
        

        $this->view->data['page'] = $page;
        $this->view->data['order_by'] = $order_by;
        $this->view->data['order'] = $order;
        $this->view->data['keyword'] = $keyword;
        $this->view->data['pagination_stages'] = $pagination_stages;
        $this->view->data['tongsotrang'] = $tongsotrang;
        $this->view->data['sonews'] = $sonews;

        $this->view->data['batdau'] = $batdau;
        $this->view->data['ketthuc'] = $ketthuc;

        $this->view->data['xe'] = $xe;
        $this->view->data['mooc'] = $mooc;
        $this->view->data['vong'] = $vong;
        $this->view->data['trangthai'] = $trangthai;

        $this->view->data['limit'] = $limit;

        
        $data = array(
            'order_by'=>$order_by,
            'order'=>$order,
            'limit'=>$x.','.$sonews,
            'where' => "1=1",
            );
        
        if($xe > 0){
            $data['where'] = $data['where'].' AND vehicle = '.$xe;
        }
        if($mooc > 0){
            $data['where'] = $data['where'].' AND romooc = '.$mooc;
        }
        
        if ($keyword != '') {
            $ngay = (strtotime(str_replace("/", "-", $keyword))!="")?(' OR shipment_date LIKE "%'.strtotime(str_replace("/", "-", $keyword)).'%"'):"";
            $search = '(
                    vehicle IN (SELECT vehicle_id FROM vehicle WHERE vehicle_number LIKE "%'.$keyword.'%") 
                    OR romooc IN (SELECT romooc_id FROM romooc WHERE romooc_number LIKE "%'.$keyword.'%") 
                    OR staff_name LIKE "%'.$keyword.'%" 
                    OR repair_comment LIKE "%'.$keyword.'%" 
                    '.$ngay.'
                        )';
            $data['where'] = $data['where']." AND ".$search;
        }

        $repair_lists = $repair_list_model->getAllRepair($data,$join);

        $this->view->data['repair_lists'] = $repair_lists;


        $this->view->show('repairlist/index');
    }

    
}
?>