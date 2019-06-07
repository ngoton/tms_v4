<?php
Class exportstockController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->exportstock) || json_decode($_SESSION['user_permission_action'])->exportstock != "exportstock") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Xuất kho';

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
            $tab_active = isset($_POST['tha']) ? $_POST['tha'] : null;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'export_stock_date';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'DESC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

            $vong = (int)date('m',strtotime($batdau));

            $trangthai = date('Y',strtotime($batdau));
            $tab_active = 1;
        }
        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));

        $trangthai = date('Y',strtotime($batdau));

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $romooc_model = $this->model->get('romoocModel');

        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));

        $this->view->data['romoocs'] = $romoocs;

        $costlist_model = $this->model->get('costlistModel');
        $this->view->data['cost_lists'] = $costlist_model->getAllCost();

        $house_model = $this->model->get('houseModel');
        $houses = $house_model->getAllHouse();
        $this->view->data['houses'] = $houses;

        $export_model = $this->model->get('exportstockModel');
        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $join = array('table'=>'user, steersman, house','where'=>'export_stock_user=user_id AND steersman = steersman_id AND house = house_id');

        $data = array(
            'where' => 'export_stock_date >= '.strtotime($batdau).' AND export_stock_date < '.strtotime($ngayketthuc),
        );

        
        $tongsodong = count($export_model->getAllStock($data,$join));
        $tongsotrang = ceil($tongsodong / $sonews);

        $exports = array();
        foreach ($houses as $house) {
            $data = array(
                'order_by'=>$order_by,
                'order'=>$order,
                'limit'=>$x.','.$sonews,
                'where' => 'house = '.$house->house_id.' AND export_stock_date >= '.strtotime($batdau).' AND export_stock_date < '.strtotime($ngayketthuc),
                );

           
            
            if ($keyword != '') {
                $search = ' AND ( export_stock_code LIKE "%'.$keyword.'%" 
                            OR username LIKE "%'.$keyword.'%" )';
                $data['where'] .= $search;
            }
            
            
            $exports[$house->house_id] = $export_model->getAllStock($data,$join);

             if ($tab_active == 0) {
                 $tab_active = $house->house_id;
             }
        }
        $this->view->data['exports'] = $exports;
        $this->view->data['tab_active'] = $tab_active;

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

        

        $this->view->data['lastID'] = isset($export_model->getLastStock()->export_stock_id)?$export_model->getLastStock()->export_stock_id:0;
        
        $this->view->show('exportstock/index');
    }

    public function sale() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->exportstock) || json_decode($_SESSION['user_permission_action'])->exportstock != "exportstock") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Xuất kho - Kinh doanh';

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
            $tab_active = isset($_POST['tha']) ? $_POST['tha'] : null;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'export_stock_date';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'DESC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

            $vong = (int)date('m',strtotime($batdau));

            $trangthai = date('Y',strtotime($batdau));
            $tab_active = 1;
        }
        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));

        $trangthai = date('Y',strtotime($batdau));

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $romooc_model = $this->model->get('romoocModel');

        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));

        $this->view->data['romoocs'] = $romoocs;

        $costlist_model = $this->model->get('costlistModel');
        $this->view->data['cost_lists'] = $costlist_model->getAllCost();

        $house_model = $this->model->get('houseModel');
        $houses = $house_model->getAllHouse();
        $this->view->data['houses'] = $houses;

        $export_model = $this->model->get('exportstockModel');
        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $join = array('table'=>'user, steersman, house','where'=>'export_stock_user=user_id AND steersman = steersman_id AND house = house_id');

        $data = array(
            'where' => 'export_type = 2 AND export_stock_date >= '.strtotime($batdau).' AND export_stock_date < '.strtotime($ngayketthuc),
        );

        
        $tongsodong = count($export_model->getAllStock($data,$join));
        $tongsotrang = ceil($tongsodong / $sonews);

        $exports = array();
        foreach ($houses as $house) {
            $data = array(
                'order_by'=>$order_by,
                'order'=>$order,
                'limit'=>$x.','.$sonews,
                'where' => 'house = '.$house->house_id.' AND export_type = 2 AND export_stock_date >= '.strtotime($batdau).' AND export_stock_date < '.strtotime($ngayketthuc),
                );

           
            
            if ($keyword != '') {
                $search = ' AND ( export_stock_code LIKE "%'.$keyword.'%" 
                            OR username LIKE "%'.$keyword.'%" )';
                $data['where'] .= $search;
            }
            
            
            $exports[$house->house_id] = $export_model->getAllStock($data,$join);

             if ($tab_active == 0) {
                 $tab_active = $house->house_id;
             }
        }
        $this->view->data['exports'] = $exports;
        $this->view->data['tab_active'] = $tab_active;

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

        

        $this->view->data['lastID'] = isset($export_model->getLastStock()->export_stock_id)?$export_model->getLastStock()->export_stock_id:0;
        
        $this->view->show('exportstock/sale');
    }
    public function internal() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->exportstock) || json_decode($_SESSION['user_permission_action'])->exportstock != "exportstock") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Xuất kho - Nội bộ';

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
            $tab_active = isset($_POST['tha']) ? $_POST['tha'] : null;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'export_stock_date';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'DESC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
            $batdau = '01-'.date('m-Y');

            $ketthuc = date('t-m-Y');

            $vong = (int)date('m',strtotime($batdau));

            $trangthai = date('Y',strtotime($batdau));
            $tab_active = 1;
        }
        $ngayketthuc = date('d-m-Y', strtotime($ketthuc. ' + 1 days'));

        $vong = (int)date('m',strtotime($batdau));

        $trangthai = date('Y',strtotime($batdau));

        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;

        $romooc_model = $this->model->get('romoocModel');

        $romoocs = $romooc_model->getAllVehicle(array('order_by'=>'romooc_number','order'=>'ASC'));

        $this->view->data['romoocs'] = $romoocs;

        $costlist_model = $this->model->get('costlistModel');
        $this->view->data['cost_lists'] = $costlist_model->getAllCost();

        $house_model = $this->model->get('houseModel');
        $houses = $house_model->getAllHouse();
        $this->view->data['houses'] = $houses;

        $export_model = $this->model->get('exportstockModel');
        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $join = array('table'=>'user, steersman, house','where'=>'export_stock_user=user_id AND steersman = steersman_id AND house = house_id');

        $data = array(
            'where' => 'export_type = 1 AND export_stock_date >= '.strtotime($batdau).' AND export_stock_date < '.strtotime($ngayketthuc),
        );

        
        $tongsodong = count($export_model->getAllStock($data,$join));
        $tongsotrang = ceil($tongsodong / $sonews);

        $exports = array();
        foreach ($houses as $house) {
            $data = array(
                'order_by'=>$order_by,
                'order'=>$order,
                'limit'=>$x.','.$sonews,
                'where' => 'house = '.$house->house_id.' AND export_type = 1 AND export_stock_date >= '.strtotime($batdau).' AND export_stock_date < '.strtotime($ngayketthuc),
                );

           
            
            if ($keyword != '') {
                $search = ' AND ( export_stock_code LIKE "%'.$keyword.'%" 
                            OR username LIKE "%'.$keyword.'%" )';
                $data['where'] .= $search;
            }
            
            
            $exports[$house->house_id] = $export_model->getAllStock($data,$join);

             if ($tab_active == 0) {
                 $tab_active = $house->house_id;
             }
        }
        $this->view->data['exports'] = $exports;
        $this->view->data['tab_active'] = $tab_active;

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

        

        $this->view->data['lastID'] = isset($export_model->getLastStock()->export_stock_id)?$export_model->getLastStock()->export_stock_id:0;
        
        $this->view->show('exportstock/internal');
    }

    public function add(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->exportstock) || json_decode($_SESSION['user_permission_action'])->exportstock != "exportstock") {
            return $this->view->redirect('user/login');
        }
        if (isset($_POST['yes'])) {
            $export_model = $this->model->get('exportstockModel');
            $stock_model = $this->model->get('sparestockModel');
            $spare_model = $this->model->get('sparepartModel');

            $data = array(
                        
                        'export_stock_code' => trim($_POST['export_stock_code']),
                        'export_stock_date' => strtotime($_POST['export_stock_date']),
                        'export_stock_user' => $_SESSION['userid_logined'],
                        'export_stock_comment' => trim($_POST['export_stock_comment']),
                        'steersman' => trim($_POST['steersman']),
                        'house' => trim($_POST['house']),
                        'vehicle' => trim($_POST['vehicle']),
                        'romooc' => trim($_POST['romooc']),
                        'export_type' => 1,
                        );


            if ($_POST['yes'] != "") {
                if ($export_model->checkStock($_POST['yes'],trim($_POST['export_stock_code']))) {
                    echo "Thông tin này đã tồn tại";
                    return false;
                }
                else{
                    $export_model->updateStock($data,array('export_stock_id' => $_POST['yes']));
                    $id_export = $_POST['yes'];
                    /*Log*/
                    /**/
                    echo "Cập nhật thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|export_stock|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                    }
                
                
            }
            else{

                if ($export_model->getStockByWhere(array('export_stock_code'=>$data['export_stock_code']))) {
                    echo "Thông tin này đã tồn tại";
                    return false;
                }
                else{
                    $export_model->createStock($data);
                    $id_export = $export_model->getLastStock()->export_stock_id;
                    /*Log*/
                    /**/

                    echo "Thêm thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$export_model->getLastStock()->export_stock_id."|export_stock|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                    }
                
                
            }

            $arr = array();

            $total_number = 0;
            $total_price = 0;
            $total_vat = 0;

            $spare_part = $_POST['spare_part'];

            foreach ($spare_part as $v) {

                    if(is_array($v['spare_part_id'])){
                        if ($v['spare_stock_number'] == count($v['spare_part_id'])) {
                            $num = 1;
                        }
                        else{
                            $num = $v['spare_stock_number'];
                        }
                        foreach ($v['spare_part_id'] as $key) {
                            $id_spare_part = $key;

                            $data_stock = array(

                                'export_stock' => $id_export,

                                'spare_part' => $id_spare_part,

                                'spare_stock_unit' => $v['spare_stock_unit'],

                                'spare_stock_number' => $num,

                                'spare_stock_price' => trim(str_replace(',','',$v['spare_stock_price'])),

                                'spare_stock_vat_percent' => $v['spare_stock_vat_percent'],

                                'spare_stock_vat_price' => trim(str_replace(',','',$v['spare_stock_vat_price'])),
                                

                            );

                            if (!$stock_model->getStockByWhere(array('export_stock'=>$id_export,'spare_part'=>$id_spare_part))) {
                                $stock_model->createStock($data_stock);
                            }
                            else{
                                $id_stock = $stock_model->getStockByWhere(array('export_stock'=>$id_export,'spare_part'=>$id_spare_part))->spare_stock_id;
                                $stock_model->updateStock($data_stock,array('spare_stock_id'=>$id_stock));
                            }

                            $total_number += $data_stock['spare_stock_number'];
                            $total_price += $data_stock['spare_stock_price']*$data_stock['spare_stock_number'];
                            $total_vat += $data_stock['spare_stock_vat_price'];

                            $arr[] = $id_spare_part;
                        }
                    }

                    
                }

                $old_stock = $stock_model->getAllStock(array('where'=>'export_stock = '.$id_export));
                if($old_stock){
                    foreach ($old_stock as $old) {
                        if(!in_array($old->spare_part, $arr)){
                            $stock_model->queryStock('DELETE FROM spare_stock WHERE export_stock = '.$old->export_stock.' AND spare_part = '.$old->spare_part);
                        }
                    }
                }


                $export_model->updateStock(array('export_stock_total'=>$total_number,'export_stock_price'=>$total_price,'export_stock_vat'=>$total_vat),array('export_stock_id'=>$id_export));
                    
        }
    }
    public function add2(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->exportstock) || json_decode($_SESSION['user_permission_action'])->exportstock != "exportstock") {
            return $this->view->redirect('user/login');
        }
        if (isset($_POST['yes'])) {
            $export_model = $this->model->get('exportstockModel');
            $stock_model = $this->model->get('sparestockModel');
            $spare_model = $this->model->get('sparepartModel');
            $shipment_model = $this->model->get('shipmentModel');

            $data = array(
                        
                        'export_stock_code' => trim($_POST['export_stock_code']),
                        'export_stock_date' => strtotime($_POST['export_stock_date']),
                        'export_stock_user' => $_SESSION['userid_logined'],
                        'export_stock_comment' => trim($_POST['export_stock_comment']),
                        'steersman' => trim($_POST['steersman']),
                        'house' => trim($_POST['house']),
                        'vehicle' => trim($_POST['vehicle']),
                        'romooc' => trim($_POST['romooc']),
                        'export_type' => 2,
                        'shipment_bill' => trim($_POST['shipment_bill']),
                        );

            $total_number_old = 0;

            if ($_POST['yes'] != "") {
                if ($export_model->checkStock($_POST['yes'],trim($_POST['export_stock_code']))) {
                    echo "Thông tin này đã tồn tại";
                    return false;
                }
                else{

                    $ex = $export_model->getStock($_POST['yes']);
                    if ($ex->shipment_bill>0) {
                        $total_number_old = $ex->export_stock_total;
                    }

                    $export_model->updateStock($data,array('export_stock_id' => $_POST['yes']));
                    $id_export = $_POST['yes'];
                    /*Log*/
                    /**/
                    echo "Cập nhật thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|export_stock|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                    }
                
                
            }
            else{

                if ($export_model->getStockByWhere(array('export_stock_code'=>$data['export_stock_code']))) {
                    echo "Thông tin này đã tồn tại";
                    return false;
                }
                else{
                    $export_model->createStock($data);
                    $id_export = $export_model->getLastStock()->export_stock_id;
                    /*Log*/
                    /**/

                    echo "Thêm thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$export_model->getLastStock()->export_stock_id."|export_stock|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                    }
                
                
            }

            


            $arr = array();

            $total_number = 0;
            $total_price = 0;
            $total_vat = 0;

            $spare_part = $_POST['spare_part'];

            foreach ($spare_part as $v) {

                    if(is_array($v['spare_part_id'])){
                        if ($v['spare_stock_number'] == count($v['spare_part_id'])) {
                            $num = 1;
                        }
                        else{
                            $num = $v['spare_stock_number'];
                        }
                        foreach ($v['spare_part_id'] as $key) {
                            $id_spare_part = $key;

                            $data_stock = array(

                                'export_stock' => $id_export,

                                'spare_part' => $id_spare_part,

                                'spare_stock_unit' => $v['spare_stock_unit'],

                                'spare_stock_number' => $num,

                                'spare_stock_price' => trim(str_replace(',','',$v['spare_stock_price'])),

                                'spare_stock_vat_percent' => $v['spare_stock_vat_percent'],

                                'spare_stock_vat_price' => trim(str_replace(',','',$v['spare_stock_vat_price'])),
                                

                            );

                            if (!$stock_model->getStockByWhere(array('export_stock'=>$id_export,'spare_part'=>$id_spare_part))) {
                                $stock_model->createStock($data_stock);
                            }
                            else{
                                $id_stock = $stock_model->getStockByWhere(array('export_stock'=>$id_export,'spare_part'=>$id_spare_part))->spare_stock_id;
                                $stock_model->updateStock($data_stock,array('spare_stock_id'=>$id_stock));
                            }

                            $total_number += $data_stock['spare_stock_number'];
                            $total_price += $data_stock['spare_stock_price']*$data_stock['spare_stock_number'];
                            $total_vat += $data_stock['spare_stock_vat_price'];

                            $arr[] = $id_spare_part;
                        }
                    }

                    
                }

                $old_stock = $stock_model->getAllStock(array('where'=>'export_stock = '.$id_export));
                if($old_stock){
                    foreach ($old_stock as $old) {
                        if(!in_array($old->spare_part, $arr)){
                            $stock_model->queryStock('DELETE FROM spare_stock WHERE export_stock = '.$old->export_stock.' AND spare_part = '.$old->spare_part);
                        }
                    }
                }


                $export_model->updateStock(array('export_stock_total'=>$total_number,'export_stock_price'=>$total_price,'export_stock_vat'=>$total_vat),array('export_stock_id'=>$id_export));

                if($data['shipment_bill']>0){
                    if ($_POST['yes'] != "") {
                        if ($ex->shipment_bill>0 && $ex->shipment_bill == $data['shipment_bill']) {
                            $shipments = $shipment_model->getShipment($data['shipment_bill']);
                            $oil = $shipments->shipment_oil-$total_number_old+$total_number;
                            if ($shipments->export_stock != $data['shipment_bill']) {
                                $ex_num = $shipments->export_stock.','.$id_export;
                            }
                            else{
                                $ex_num = $id_export;
                            }
                            $shipment_model->updateShipment(array('shipment_oil'=>$oil,'export_stock'=>$ex_num),array('shipment_id'=>$data['shipment_bill']));
                        }
                        else if($ex->shipment_bill>0 && ($data['shipment_bill']==0 || $data['shipment_bill']=="")){
                            $shipment_olds = $shipment_model->getShipment($ex->shipment_bill);
                            $oil = $shipment_olds->shipment_oil-$total_number_old;
                            if ($shipment_olds->export_stock == $id_export) {
                                $ex_num = "";
                            }
                            else{
                                $ex_num = str_replace(','.$id_export, "", $shipment_olds->export_stock);
                            }
                            $shipment_model->updateShipment(array('shipment_oil'=>$oil,'export_stock'=>$ex_num),array('shipment_id'=>$ex->shipment_bill));
                        }
                        else if($ex->shipment_bill>0 && $data['shipment_bill']>0 && $data['shipment_bill']!=$ex->shipment_bill){
                            $shipment_olds = $shipment_model->getShipment($ex->shipment_bill);
                            $oil = $shipment_olds->shipment_oil-$total_number_old;
                            if ($shipment_olds->export_stock == $id_export) {
                                $ex_num = "";
                            }
                            else{
                                $ex_num = str_replace(','.$id_export, "", $shipment_olds->export_stock);
                            }
                            $shipment_model->updateShipment(array('shipment_oil'=>$oil,'export_stock'=>$ex_num),array('shipment_id'=>$ex->shipment_bill));

                            $shipments = $shipment_model->getShipment($data['shipment_bill']);
                            $oil = $shipments->shipment_oil+$total_number;
                            if ($shipments->export_stock != "") {
                                $ex_num = $shipments->export_stock.','.$id_export;
                            }
                            else{
                                $ex_num = $id_export;
                            }
                            $shipment_model->updateShipment(array('shipment_oil'=>$oil,'export_stock'=>$ex_num),array('shipment_id'=>$data['shipment_bill']));
                        }
                    }
                    else{
                        $shipments = $shipment_model->getShipment($data['shipment_bill']);
                        $oil = $shipments->shipment_oil+$total_number;
                        if ($shipments->export_stock != "") {
                            $ex_num = $shipments->export_stock.','.$id_export;
                        }
                        else{
                            $ex_num = $id_export;
                        }
                        $shipment_model->updateShipment(array('shipment_oil'=>$oil,'export_stock'=>$ex_num),array('shipment_id'=>$data['shipment_bill']));
                    }
                    
                }
                    
        }
    }
    public function delete(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->exportstock) || json_decode($_SESSION['user_permission_action'])->exportstock != "exportstock") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $export_model = $this->model->get('exportstockModel');
            $stock_model = $this->model->get('sparestockModel');
            $shipment_model = $this->model->get('shipmentModel');
            if (isset($_POST['xoa'])) {
                $data = explode(',', $_POST['xoa']);
                foreach ($data as $data) {
                    $ex = $export_model->getStock($data);
                    if($ex->shipment_bill>0){
                        $shipments = $shipment_model->getShipment($ex->shipment_bill);
                        $oil = $shipments->shipment_oil-$ex->export_stock_total;
                        if ($shipments->export_stock == $data) {
                            $ex_num = null;
                        }
                        else{
                            $ex_num = $shipments->export_stock;
                        }
                        $shipment_model->updateShipment(array('shipment_oil'=>$oil,'export_stock'=>$ex_num),array('shipment_id'=>$ex->shipment_bill));
                    }
                    
                    $export_model->deleteStock($data);
                    $stock_model->query('DELETE FROM spare_stock WHERE export_stock = '.$data);
                    
                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|export_stock|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }

                /*Log*/
                    /**/

                return true;
            }
            else{
                $ex = $export_model->getStock($_POST['data']);
                if($ex->shipment_bill>0){
                    $shipments = $shipment_model->getShipment($ex->shipment_bill);
                    $oil = $shipments->shipment_oil-$ex->export_stock_total;
                    if ($shipments->export_stock == $_POST['data']) {
                        $ex_num = null;
                    }
                    else{
                        $ex_num = $shipments->export_stock;
                    }
                    $shipment_model->updateShipment(array('shipment_oil'=>$oil,'export_stock'=>$ex_num),array('shipment_id'=>$ex->shipment_bill));
                }

                $stock_model->query('DELETE FROM spare_stock WHERE export_stock = '.$_POST['data']);
                /*Log*/
                    /**/
                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|export_stock|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);

                return $export_model->deleteStock($_POST['data']);
            }
            
        }
    }

    public function getvehicle(){



        if (!isset($_SESSION['userid_logined'])) {



            return $this->view->redirect('user/login');



        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $vehicle = $_POST['vehicle'];
            $ngay = $_POST['ngay'];


            $driver_model = $this->model->get('driverModel');
            $shipment_model = $this->model->get('shipmentModel');

            
            $data_driver = array(

                'where'=>'vehicle='.$vehicle.' AND (start_work <= '.strtotime($ngay).' AND (end_work >= '.strtotime($ngay).' OR (end_work IS NULL OR end_work=0) ) )',

                'order_by'=>'start_work DESC',

                'limit'=>1,

            );

            $join = array('table'=>'steersman','where'=>'steersman=steersman_id');

            $drivers = $driver_model->getAllDriver($data_driver,$join);

            $steersman_id = "";
            $steersman_name = "";

            foreach ($drivers as $driver) {
                $steersman_id = $driver->steersman_id;
                $steersman_name = $driver->steersman_name;
            }

            $data_shipment = array(

                'where'=>'vehicle='.$vehicle.' AND shipment_date <= '.strtotime($ngay),

                'order_by'=>'shipment_date DESC, bill_delivery_date DESC, shipment_id DESC',

                'limit'=>1,

            );
            $shipments = $shipment_model->getAllShipment($data_shipment);
            $bill = "";

            foreach ($shipments as $shipment) {
                $bill = '<option value="'.$shipment->shipment_id.'">'.$shipment->bill_number.'</option>';
            }
            
            $result = array(
                'id' => $steersman_id,
                'name' => $steersman_name,
                'bill' => $bill,
            );

            echo json_encode($result);


        }



    }
    public function getbill(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $bill = $_POST['shipment_bill'];
            $shipment_model = $this->model->get('shipmentModel');

            
            $shipments = $shipment_model->getShipment($bill);
            $bill = '<option value="'.$shipments->shipment_id.'">'.$shipments->bill_number.'</option>';
            
            $result = array(
                'bill' => $bill,
            );

            echo json_encode($result);


        }



    }
    public function getlastspare(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $house = $_POST['house'];
            $import_stock_model = $this->model->get('importstockModel');
            $spare_stock_model = $this->model->get('sparestockModel');
            $spare_code_model = $this->model->get('sparepartcodeModel');
            $spare_model = $this->model->get('sparepartModel');
            
            $item = null;
            $value = null;
            $code = null;
            $vitri = 0;
            $unit = null;
            $price = null;

            $import_stocks = $import_stock_model->getAllStock(array('where'=>'house='.$house,'order_by'=>'import_stock_id DESC','limit'=>1));
            foreach ($import_stocks as $key) {
                $stocks = $spare_stock_model->getAllStock(array('where'=>'import_stock='.$key->import_stock_id,'order_by'=>'spare_stock_id DESC','limit'=>1));
                foreach ($stocks as $key2) {
                    $spare_parts = $spare_model->getStock($key2->spare_part);
                    $spare_part_codes = $spare_code_model->getStock($spare_parts->code_list);

                    $item = $spare_part_codes->spare_part_code_id;
                    $value = $spare_part_codes->name;
                    $code = $spare_part_codes->code;
                    $vitri = 0;
                    $unit = $key2->spare_stock_unit;
                    $price = $key2->spare_stock_price;
                }
            }

            
            $result = array(
                'item' => $item,
                'value' => $value,
                'code' => $code,
                'vitri' => $vitri,
                'unit' => $unit,
                'price' => $price,
            );

            echo json_encode($result);


        }



    }

    public function getsteersman(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $steersman_model = $this->model->get('steersmanModel');
            $driver_model = $this->model->get('driverModel');

            $ngay = $_POST['ngay'];
            

            if ($_POST['keyword'] == "*") {



                $list = $steersman_model->getAllSteersman();

            }

            else{

                $data = array(

                'where'=>'( steersman_name LIKE "%'.$_POST['keyword'].'%" )',

                );

                $list = $steersman_model->getAllSteersman($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text

                $steersman_name = $rs->steersman_name;

                if ($_POST['keyword'] != "*") {

                    $steersman_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $rs->steersman_name);

                }

                $data_driver = array(

                    'where'=>'steersman='.$rs->steersman_id.' AND (start_work <= '.strtotime($ngay).' AND (end_work >= '.strtotime($ngay).' OR (end_work IS NULL OR end_work=0) ) )',

                    'order_by'=>'start_work DESC',

                    'limit'=>1,

                );

                $join = array('table'=>'vehicle','where'=>'vehicle=vehicle_id');

                $drivers = $driver_model->getAllDriver($data_driver,$join);

                $vehicle_id = "";

                foreach ($drivers as $driver) {
                    $vehicle_id = $driver->vehicle_id;
                }

                // add new option

                echo '<li onclick="set_item_steersman(\''.$rs->steersman_id.'\',\''.$rs->steersman_name.'\',\''.$vehicle_id.'\')">'.$steersman_name.'</li>';

            }

        }

    }

    public function getSpare(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $spare_code_model = $this->model->get('sparepartcodeModel');
            $spare_model = $this->model->get('sparepartModel');
            $spare_stock_model = $this->model->get('sparestockModel');

            if ($_POST['keyword'] == "*") {

                

                $list = $spare_code_model->getAllStock();

            }

            else{

                $data = array(

                'where'=>'( name LIKE "%'.$_POST['keyword'].'%" )',

                );

                $list = $spare_code_model->getAllStock($data);

                if (!$list) {
                    $data = array(

                    'where'=>'( code LIKE "%'.$_POST['keyword'].'%" )',

                    );

                    $list = $spare_code_model->getAllStock($data);
                }

            }

            

            foreach ($list as $rs) {

                // put in bold the written text
                

                $spare_name = '['.$rs->code.']-'.$rs->name;

                if ($_POST['keyword'] != "*") {

                    $spare_name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', '['.$rs->code.']-'.$rs->name);

                }

                $stocks = $spare_stock_model->queryStock('SELECT * FROM spare_stock, spare_part WHERE spare_part = spare_part_id AND import_stock > 0 AND spare_part IN (SELECT spare_part_id FROM spare_part WHERE code_list = '.$rs->spare_part_code_id.') ORDER BY spare_stock_id DESC LIMIT 1');
                if ($stocks) {
                    foreach ($stocks as $stock) {
                        echo '<li onclick="set_item_other(\''.$rs->spare_part_code_id.'\',\''.$rs->name.'\',\''.$rs->code.'\',\''.$_POST['offset'].'\',\''.$stock->spare_stock_unit.'\',\''.$stock->spare_stock_price.'\')">'.$spare_name.'</li>';
                    }
                    
                }
                else{
                    $spares = $spare_model->getAllStock(array('where'=>'code_list = '.$rs->spare_part_code_id,'order_by'=>'spare_part_id DESC','limit'=>1));
                    foreach ($spares as $spare) {
                        echo '<li onclick="set_item_other(\''.$rs->spare_part_code_id.'\',\''.$rs->name.'\',\''.$rs->code.'\',\''.$_POST['offset'].'\',\'\',\'\')">'.$spare_name.'</li>';
                    }
                    
                }

                

            }

        }

    }
    public function getseri(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $spare_model = $this->model->get('sparepartModel');
            $spare_stock_model = $this->model->get('sparestockModel');

            $join = array('table'=>'spare_part','where'=>'spare_part = spare_part_id');
            $data_im = array(
                'where' => 'import_stock > 0 AND code_list = '.trim($_POST['data']),
            );
            $stock_ims = $spare_stock_model->getAllStock($data_im,$join);

            $data_ex = array(
                'where' => 'export_stock > 0 AND code_list = '.trim($_POST['data']),
            );
            $stock_exs = $spare_stock_model->getAllStock($data_ex,$join);
        
            $data_stock = array();
            foreach ($stock_ims as $stock) {
                $data_stock[$stock->spare_part] = isset($data_stock[$stock->spare_part])?$data_stock[$stock->spare_part]+$stock->spare_stock_number:$stock->spare_stock_number;
            }
            foreach ($stock_exs as $stock) {
                $data_stock[$stock->spare_part] = isset($data_stock[$stock->spare_part])?$data_stock[$stock->spare_part]-$stock->spare_stock_number:0-$stock->spare_stock_number;
            }

            $data = array(
                'where' => 'code_list = '.trim($_POST['data']),
            );

            $spares = $spare_model->getAllStock($data);

            $str = "";

            foreach ($spares as $spare) {
                if (isset($data_stock[$spare->spare_part_id]) && $data_stock[$spare->spare_part_id]>0) {
                    $str .= '<option title="'.$data_stock[$spare->spare_part_id].'" value="'.$spare->spare_part_id.'">'.($spare->spare_part_seri!=""?$spare->spare_part_seri:$spare->spare_part_name).' ['.$data_stock[$spare->spare_part_id].']</option>';
                }
                
            }

            echo $str;

        }

    }
    public function deletespare(){

        if (isset($_POST['export_stock'])) {

            $spare_model = $this->model->get('sparestockModel');



            $spare_model->queryStock('DELETE FROM spare_stock WHERE export_stock = '.$_POST['export_stock'].' AND spare_part = '.$_POST['spare_part']);

            echo 'Đã xóa thành công';

        }

    }
    public function deletesparecode(){

        if (isset($_POST['export_stock'])) {

            $spare_model = $this->model->get('sparestockModel');



            $spare_model->queryStock('DELETE FROM spare_stock WHERE export_stock = '.$_POST['export_stock'].' AND spare_part IN (SELECT spare_part_id FROM spare_part WHERE code_list = '.$_POST['spare_part_code'].')');

            echo 'Đã xóa thành công';

        }

    }
    public function spare(){

        if(isset($_POST['export_stock'])){

            $spare_part_model = $this->model->get('sparepartModel');

            $spare_model = $this->model->get('sparestockModel');

            $spare_code_model = $this->model->get('sparepartcodeModel');

            $join = array('table'=>'spare_part, spare_stock','where'=>'code_list = spare_part_code_id AND spare_part_id = spare_part GROUP BY spare_part_code_id');
            $data = array(

                'where' => 'export_stock = '.$_POST['export_stock'],

            );

            $codes = $spare_code_model->getAllStock($data,$join);

            $count = array();
            $number = array();
            $vat_price = array();
            $exports = array();
            $spares = array();
            $data_stock = array();
            foreach ($codes as $code) {
                $join = array('table'=>'spare_part','where'=>'spare_stock.spare_part = spare_part.spare_part_id');

                $data = array(

                    'where' => 'code_list = '.$code->spare_part_code_id.' AND export_stock = '.$_POST['export_stock'],

                );

                $exports[$code->spare_part_code_id] = $spare_model->getAllStock($data,$join);

                foreach ($exports[$code->spare_part_code_id] as $spare) {
                    $number[$code->spare_part_code_id] = isset($number[$code->spare_part_code_id])?$number[$code->spare_part_code_id]+$spare->spare_stock_number:$spare->spare_stock_number;
                    $vat_price[$code->spare_part_code_id] = isset($vat_price[$code->spare_part_code_id])?$vat_price[$code->spare_part_code_id]+$spare->spare_stock_vat_price:$spare->spare_stock_vat_price;
                }

                $count[$code->spare_part_code_id] = count($exports[$code->spare_part_code_id]);

                $join = array('table'=>'spare_part','where'=>'spare_part = spare_part_id');
                $data_im = array(
                    'where' => 'import_stock > 0 AND code_list = '.$code->spare_part_code_id,
                );
                $stock_ims = $spare_model->getAllStock($data_im,$join);

                $data_ex = array(
                    'where' => 'export_stock > 0 AND code_list = '.$code->spare_part_code_id,
                );
                $stock_exs = $spare_model->getAllStock($data_ex,$join);
            
                
                foreach ($stock_ims as $stock) {
                    $data_stock[$stock->spare_part] = isset($data_stock[$stock->spare_part])?$data_stock[$stock->spare_part]+$stock->spare_stock_number:$stock->spare_stock_number;
                }
                foreach ($stock_exs as $stock) {
                    $data_stock[$stock->spare_part] = isset($data_stock[$stock->spare_part])?$data_stock[$stock->spare_part]-$stock->spare_stock_number:0-$stock->spare_stock_number;
                }

                $data = array(
                    'where' => 'spare_part_id NOT IN (SELECT spare_part FROM spare_stock WHERE export_stock = '.$_POST['export_stock'].') AND code_list = '.$code->spare_part_code_id,
                );

                $spares[$code->spare_part_code_id] = $spare_part_model->getAllStock($data);
            }

            


            $str = "";

            if (!$codes) {

                $str .= '<tr class="'.$_POST['export_stock'].'">';

                $str .= '<td><input type="checkbox"  name="chk"></td>';

                $str .= '<td><table style="width: 100%">';

                $str .= '<tr class="'.$_POST['export_stock'] .'">';

                $str .= '<td>Tên sản phẩm</td>';

                $str .= '<td><input type="text" autocomplete="off" class="spare_part" name="spare_part[]" placeholder="Nhập tên hoặc * để chọn" >';

                $str .= '<ul class="name_list_id"></ul></td>';

                $str .= '<td>Mã sản phẩm</td>';

                $str .= '<td><input autocomplete="off" type="text" class="spare_part_code" name="spare_part_code[]" tabindex="4" placeholder="Nhập tên hoặc * để chọn" >';

                $str .= '<ul class="name_list_id_2"></ul></td></tr>';

                $str .= '<tr class="show_seri"><td>Chọn 1 sản phẩm</td>';

                $str .= '<td><select class="choose_seri" name="choose_seri[]" multiple="multiple" tabindex="11" data="0"></select></td>';

                $str .= '<td></td>';

                $str .= '<td></td></tr>';

                $str .= '<tr><td>Đơn vị tính</td>';

                $str .= '<td><input type="text" class="spare_stock_unit" name="spare_stock_unit[]"></td>';

                $str .= '<td>Đơn giá</td>';

                $str .= '<td><input type="text" class="spare_stock_price numbers" name="spare_stock_price[]"></td></tr>';

                $str .= '<tr><td>Số lượng</td>';

                $str .= '<td><input style="width:80px" type="number" class="spare_stock_number number" name="spare_stock_number[]" tabindex="10" value="0" max="0" ></td>';

                $str .= '<td>VAT</td>';

                $str .= '<td><input style="width:50px" type="text" class="spare_stock_vat_percent number" name="spare_stock_vat_percent[]" tabindex="11" placeholder="%" >';
                
                $str .= '<input style="width:120px" type="text" class="spare_stock_vat_price numbers" name="spare_stock_vat_price[]" tabindex="12" placeholder="Tổng tiền thuế" ></td></tr>';

                $str .= '</table></td></tr>';

            }

            else{
                $i = 0;
                foreach ($codes as $code) {

                    $str .= '<tr class="'.$_POST['export_stock'].'">';

                    $str .= '<td><input type="checkbox"  name="chk" alt="'.$code->spare_part_code_id.'"  data="'.$_POST['export_stock'].'"></td>';

                    $str .= '<td><table style="width: 100%">';

                    $str .= '<tr class="'.$_POST['export_stock'] .'">';

                    $str .= '<td>Tên sản phẩm</td>';

                    $str .= '<td><input disabled type="text" autocomplete="off" class="spare_part" name="spare_part[]" placeholder="Nhập tên hoặc * để chọn" value="'.$code->name.'" data="'.$code->spare_part_code_id.'" >';

                    $str .= '<ul class="name_list_id"></ul></td>';

                    $str .= '<td>Mã sản phẩm</td>';

                    $str .= '<td><input disabled autocomplete="off" type="text" class="spare_part_code" name="spare_part_code[]" tabindex="4" placeholder="Nhập tên hoặc * để chọn" value="'.$code->code.'" >';

                    $str .= '<ul class="name_list_id_2"></ul></td></tr>';

                    $str .= '<tr class="show_seri"><td>Chọn 1 sản phẩm</td>';

                    $str .= '<td><select class="choose_seri" name="choose_seri[]" multiple="multiple" tabindex="11" data="'.$i.'">';

                        foreach ($exports[$code->spare_part_code_id] as $v) {
                            $str .= '<option selected title="'.$v->spare_stock_number.'" value="'.$v->spare_part_id.'">'.($v->spare_part_seri!=""?$v->spare_part_seri:$v->spare_part_name).' ['.$v->spare_stock_number.']</option>';
                        }
                        foreach ($spares[$code->spare_part_code_id] as $v) {
                            if (isset($data_stock[$v->spare_part_id]) && $data_stock[$v->spare_part_id]>0) {
                                $str .= '<option title="'.$data_stock[$v->spare_part_id].'" value="'.$v->spare_part_id.'">'.($v->spare_part_seri!=""?$v->spare_part_seri:$v->spare_part_name).' ['.$data_stock[$v->spare_part_id].']</option>';
                            }
                        }
                    $str .= '</select></td>';

                    $str .= '<td></td>';

                    $str .= '<td></td></tr>';

                    $str .= '<tr><td>Đơn vị tính</td>';

                    $str .= '<td><input type="text" class="spare_stock_unit" name="spare_stock_unit[]" value="'.$code->spare_stock_unit.'"></td>';

                    $str .= '<td>Đơn giá</td>';

                    $str .= '<td><input type="text" class="spare_stock_price numbers" name="spare_stock_price[]" value="'.$this->lib->formatMoney($code->spare_stock_price).'"></td></tr>';

                    $str .= '<tr><td>Số lượng</td>';

                    $str .= '<td><input style="width:80px" type="number" class="spare_stock_number number" name="spare_stock_number[]" tabindex="10" value="'.$number[$code->spare_part_code_id].'" ></td>';

                    $str .= '<td>VAT</td>';

                    $str .= '<td><input style="width:50px" type="text" class="spare_stock_vat_percent number" name="spare_stock_vat_percent[]" tabindex="11" placeholder="%" value="'.$code->spare_stock_vat_percent.'" >';
                    
                    $str .= '<input style="width:120px" type="text" class="spare_stock_vat_price numbers" name="spare_stock_vat_price[]" tabindex="12" placeholder="Tổng tiền thuế" value="'.$this->lib->formatMoney($vat_price[$code->spare_part_code_id]).'" ></td></tr>';

                    $str .= '</table></td></tr>';

                    

                    $i++;

                }

            }



            echo $str;

        }

    }
    
    function export(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }



        $batdau = $this->registry->router->param_id;

        $ketthuc = $this->registry->router->page;

        $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

        $info_model = $this->model->get('infoModel');
        $infos = $info_model->getLastInfo();

        $vehicle_model = $this->model->get('vehicleModel');
        $vehicles = $vehicle_model->getAllVehicle();

        $vehicle_data = array();
        foreach ($vehicles as $vehicle) {
            $vehicle_data['id'][$vehicle->vehicle_id] = $vehicle->vehicle_id;
            $vehicle_data['name'][$vehicle->vehicle_id] = $vehicle->vehicle_number;
        }

        $romooc_model = $this->model->get('romoocModel');
        $romoocs = $romooc_model->getAllVehicle();

        $romooc_data = array();
        foreach ($romoocs as $romooc) {
            $romooc_data['id'][$romooc->romooc_id] = $romooc->romooc_id;
            $romooc_data['name'][$romooc->romooc_id] = $romooc->romooc_number;
        }

        $house_model = $this->model->get('houseModel');

        $houses = $house_model->getAllHouse();

        $export_model = $this->model->get('exportstockModel');
        $spare_stock_model = $this->model->get('sparestockModel');

        $join = array('table'=>'user, steersman, house','where'=>'export_stock_user=user_id AND steersman = steersman_id AND house = house_id');



        $data = array(

            'where' => 'export_stock_date >= '.$batdau.' AND export_stock_date < '.$ngayketthuc,

            );




        $data['order_by'] = 'export_stock_date';

        $data['order'] = 'ASC';




            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();

        $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

        
            foreach ($houses as $house) {
                
                $data = array(

                'where' => 'house = '.$house->house_id.' AND export_stock_date >= '.$batdau.' AND export_stock_date < '.$ngayketthuc,

                );

                $exports = $export_model->getAllStock($data,$join);

                $objPHPExcel->createSheet();

                $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', mb_strtoupper($infos->info_company, "UTF-8"))

                ->setCellValue('A2', 'PHÒNG VẬT TƯ KỸ THUẬT')

                ->setCellValue('F1', 'CỘNG HÒA XÃ CHỦ NGHĨA VIỆT NAM')

                ->setCellValue('F2', 'Độc lập - Tự do - Hạnh phúc')

                ->setCellValue('A4', 'BẢNG TỔNG HỢP PHIẾU XUẤT KHO')

                ->setCellValue('A6', 'STT')

               ->setCellValue('B6', 'NGÀY')

               ->setCellValue('C6', 'PHIẾU XUẤT KHO')

               ->setCellValue('D6', 'NỘI DUNG')

               ->setCellValue('E6', 'SỐ LƯỢNG')

               ->setCellValue('F6', 'NGƯỜI NHẬN')

               ->setCellValue('G6', 'XE')

               ->setCellValue('H6', 'MOOC');

              


            if ($exports) {



                $hang = 7;

                $i=1;



                $k=0;
                foreach ($exports as $row) {


                        $spares = $spare_stock_model->getAllStock(array('where'=>'export_stock = '.$row->export_stock_id),array('table'=>'spare_part, spare_part_code','where'=>'spare_part = spare_part_id AND code_list = spare_part_code_id'));




                        //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                         $objPHPExcel->setActiveSheetIndex($index_worksheet)

                            ->setCellValue('A' . $hang, $i++)

                            ->setCellValueExplicit('B' . $hang, $this->lib->hien_thi_ngay_thang($row->export_stock_date))

                            ->setCellValue('C' . $hang, $row->export_stock_code)

                            ->setCellValue('D' . $hang, $row->export_stock_comment)

                            ->setCellValue('E' . $hang, $row->export_stock_total)

                            ->setCellValue('F' . $hang, $row->steersman_name)

                            ->setCellValue('G' . $hang, (isset($vehicle_data['name'][$row->vehicle])?$vehicle_data['name'][$row->vehicle]:null))

                            ->setCellValue('H' . $hang, (isset($romooc_data['name'][$row->romooc])?$romooc_data['name'][$row->romooc]:null));

                        
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':H'.$hang)->getFont()->setBold(true);

                         $hang++;

                         $j=1;

                        foreach ($spares as $spare) {
                            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                            ->setCellValue('A' . $hang, $j++)

                            ->setCellValueExplicit('B' . $hang, $spare->code)

                            ->setCellValue('C' . $hang, $spare->name)

                            ->setCellValue('D' . $hang, $spare->spare_part_seri)

                            ->setCellValue('E' . $hang, $spare->spare_stock_number)

                            ->setCellValue('G' . $hang, (isset($vehicle_data['name'][$row->vehicle])?$vehicle_data['name'][$row->vehicle]:null))

                            ->setCellValue('H' . $hang, (isset($romooc_data['name'][$row->romooc])?$romooc_data['name'][$row->romooc]:null));


                            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':H'.$hang)->getFont()->setItalic(true);

                            $hang++;
                        }




                }

            }



            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('B'.$hang, 'TỔNG')


               ->setCellValue('E'.$hang, '=SUM(E7:E'.($hang-1).')/2');

            

            $objPHPExcel->getActiveSheet()->getStyle('A7:D'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A7:D'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('F7:H'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('F7:H'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:H'.$hang)->applyFromArray(

                array(

                    

                    'borders' => array(

                        'allborders' => array(

                          'style' => PHPExcel_Style_Border::BORDER_THIN

                        )

                    )

                )

            );


            $highestColumn = $objPHPExcel->getActiveSheet()->getHighestDataColumn();

            



            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.($hang+3), 'NGƯỜI LẬP BIỂU')

                ->setCellValue('F'.($hang+3), mb_strtoupper($infos->info_company, "UTF-8"));



            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+3).':C'.($hang+3));

            $objPHPExcel->getActiveSheet()->mergeCells('F'.($hang+3).':H'.($hang+3));


            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':H'.($hang+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':H'.($hang+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang).':H'.($hang+3))->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );





            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;



            $objPHPExcel->getActiveSheet()->mergeCells('A1:C1');

            $objPHPExcel->getActiveSheet()->mergeCells('F1:H1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:C2');

            $objPHPExcel->getActiveSheet()->mergeCells('F2:H2');

            $objPHPExcel->getActiveSheet()->mergeCells('A4:H4');



            $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(16);



            $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('A2:C2')->applyFromArray(

                array(

                    

                    'font' => array(

                        'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('E7:E'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(26);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(25);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);

            //$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);



            

            $objPHPExcel->getActiveSheet()->setTitle($house->house_name);



            $objPHPExcel->getActiveSheet()->freezePane('A7');

            $objPHPExcel->setActiveSheetIndex($index_worksheet);

            $index_worksheet++;
            }
        
        
            







            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Sale Report")

                            ->setSubject("Sale Report")

                            ->setDescription("Sale Report.")

                            ->setKeywords("Sale Report")

                            ->setCategory("Sale Report");



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BẢNG TỔNG HỢP PHIẾU XUẤT KHO.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }
    function export1(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }



        $batdau = $this->registry->router->param_id;

        $ketthuc = $this->registry->router->page;

        $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

        $info_model = $this->model->get('infoModel');
        $infos = $info_model->getLastInfo();

        $vehicle_model = $this->model->get('vehicleModel');
        $vehicles = $vehicle_model->getAllVehicle();

        $vehicle_data = array();
        foreach ($vehicles as $vehicle) {
            $vehicle_data['id'][$vehicle->vehicle_id] = $vehicle->vehicle_id;
            $vehicle_data['name'][$vehicle->vehicle_id] = $vehicle->vehicle_number;
        }

        $romooc_model = $this->model->get('romoocModel');
        $romoocs = $romooc_model->getAllVehicle();

        $romooc_data = array();
        foreach ($romoocs as $romooc) {
            $romooc_data['id'][$romooc->romooc_id] = $romooc->romooc_id;
            $romooc_data['name'][$romooc->romooc_id] = $romooc->romooc_number;
        }

        $house_model = $this->model->get('houseModel');

        $houses = $house_model->getAllHouse();

        $export_model = $this->model->get('exportstockModel');
        $spare_stock_model = $this->model->get('sparestockModel');

        $join = array('table'=>'user, steersman, house','where'=>'export_stock_user=user_id AND steersman = steersman_id AND house = house_id');



        $data = array(

            'where' => 'export_type = 1 AND  export_stock_date >= '.$batdau.' AND export_stock_date < '.$ngayketthuc,

            );




        $data['order_by'] = 'export_stock_date';

        $data['order'] = 'ASC';




            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();

        $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

        
            foreach ($houses as $house) {
                
                $data = array(

                'where' => 'export_type = 1 AND house = '.$house->house_id.' AND export_stock_date >= '.$batdau.' AND export_stock_date < '.$ngayketthuc,

                );

                $exports = $export_model->getAllStock($data,$join);

                $objPHPExcel->createSheet();

                $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', mb_strtoupper($infos->info_company, "UTF-8"))

                ->setCellValue('A2', 'PHÒNG VẬT TƯ KỸ THUẬT')

                ->setCellValue('F1', 'CỘNG HÒA XÃ CHỦ NGHĨA VIỆT NAM')

                ->setCellValue('F2', 'Độc lập - Tự do - Hạnh phúc')

                ->setCellValue('A4', 'BẢNG TỔNG HỢP PHIẾU XUẤT KHO')

                ->setCellValue('A6', 'STT')

               ->setCellValue('B6', 'NGÀY')

               ->setCellValue('C6', 'PHIẾU XUẤT KHO')

               ->setCellValue('D6', 'NỘI DUNG')

               ->setCellValue('E6', 'SỐ LƯỢNG')

               ->setCellValue('F6', 'NGƯỜI NHẬN')

               ->setCellValue('G6', 'XE')

               ->setCellValue('H6', 'MOOC');

              


            if ($exports) {



                $hang = 7;

                $i=1;



                $k=0;
                foreach ($exports as $row) {


                        $spares = $spare_stock_model->getAllStock(array('where'=>'export_stock = '.$row->export_stock_id),array('table'=>'spare_part, spare_part_code','where'=>'spare_part = spare_part_id AND code_list = spare_part_code_id'));




                        //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                         $objPHPExcel->setActiveSheetIndex($index_worksheet)

                            ->setCellValue('A' . $hang, $i++)

                            ->setCellValueExplicit('B' . $hang, $this->lib->hien_thi_ngay_thang($row->export_stock_date))

                            ->setCellValue('C' . $hang, $row->export_stock_code)

                            ->setCellValue('D' . $hang, $row->export_stock_comment)

                            ->setCellValue('E' . $hang, $row->export_stock_total)

                            ->setCellValue('F' . $hang, $row->steersman_name)

                            ->setCellValue('G' . $hang, (isset($vehicle_data['name'][$row->vehicle])?$vehicle_data['name'][$row->vehicle]:null))

                            ->setCellValue('H' . $hang, (isset($romooc_data['name'][$row->romooc])?$romooc_data['name'][$row->romooc]:null));

                        
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':H'.$hang)->getFont()->setBold(true);

                         $hang++;

                         $j=1;

                        foreach ($spares as $spare) {
                            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                            ->setCellValue('A' . $hang, $j++)

                            ->setCellValueExplicit('B' . $hang, $spare->code)

                            ->setCellValue('C' . $hang, $spare->name)

                            ->setCellValue('D' . $hang, $spare->spare_part_seri)

                            ->setCellValue('E' . $hang, $spare->spare_stock_number)

                            ->setCellValue('G' . $hang, (isset($vehicle_data['name'][$row->vehicle])?$vehicle_data['name'][$row->vehicle]:null))

                            ->setCellValue('H' . $hang, (isset($romooc_data['name'][$row->romooc])?$romooc_data['name'][$row->romooc]:null));


                            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':H'.$hang)->getFont()->setItalic(true);

                            $hang++;
                        }




                }

            }



            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('B'.$hang, 'TỔNG')


               ->setCellValue('E'.$hang, '=SUM(E7:E'.($hang-1).')/2');

            

            $objPHPExcel->getActiveSheet()->getStyle('A7:D'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A7:D'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('F7:H'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('F7:H'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:H'.$hang)->applyFromArray(

                array(

                    

                    'borders' => array(

                        'allborders' => array(

                          'style' => PHPExcel_Style_Border::BORDER_THIN

                        )

                    )

                )

            );


            $highestColumn = $objPHPExcel->getActiveSheet()->getHighestDataColumn();

            



            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.($hang+3), 'NGƯỜI LẬP BIỂU')

                ->setCellValue('F'.($hang+3), mb_strtoupper($infos->info_company, "UTF-8"));



            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+3).':C'.($hang+3));

            $objPHPExcel->getActiveSheet()->mergeCells('F'.($hang+3).':H'.($hang+3));


            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':H'.($hang+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':H'.($hang+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang).':H'.($hang+3))->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );





            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;



            $objPHPExcel->getActiveSheet()->mergeCells('A1:C1');

            $objPHPExcel->getActiveSheet()->mergeCells('F1:H1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:C2');

            $objPHPExcel->getActiveSheet()->mergeCells('F2:H2');

            $objPHPExcel->getActiveSheet()->mergeCells('A4:H4');



            $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(16);



            $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('A2:C2')->applyFromArray(

                array(

                    

                    'font' => array(

                        'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('E7:E'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(26);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(25);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);

            //$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);



            

            $objPHPExcel->getActiveSheet()->setTitle($house->house_name);



            $objPHPExcel->getActiveSheet()->freezePane('A7');

            $objPHPExcel->setActiveSheetIndex($index_worksheet);

            $index_worksheet++;
            }
        
        
            







            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Sale Report")

                            ->setSubject("Sale Report")

                            ->setDescription("Sale Report.")

                            ->setKeywords("Sale Report")

                            ->setCategory("Sale Report");



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BẢNG TỔNG HỢP PHIẾU XUẤT KHO.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }
    function export2(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }



        $batdau = $this->registry->router->param_id;

        $ketthuc = $this->registry->router->page;

        $ngayketthuc = strtotime(date('d-m-Y', strtotime(date('d-m-Y',$ketthuc). ' + 1 days')));

        $info_model = $this->model->get('infoModel');
        $infos = $info_model->getLastInfo();

        $vehicle_model = $this->model->get('vehicleModel');
        $vehicles = $vehicle_model->getAllVehicle();

        $vehicle_data = array();
        foreach ($vehicles as $vehicle) {
            $vehicle_data['id'][$vehicle->vehicle_id] = $vehicle->vehicle_id;
            $vehicle_data['name'][$vehicle->vehicle_id] = $vehicle->vehicle_number;
        }

        $romooc_model = $this->model->get('romoocModel');
        $romoocs = $romooc_model->getAllVehicle();

        $romooc_data = array();
        foreach ($romoocs as $romooc) {
            $romooc_data['id'][$romooc->romooc_id] = $romooc->romooc_id;
            $romooc_data['name'][$romooc->romooc_id] = $romooc->romooc_number;
        }

        $house_model = $this->model->get('houseModel');

        $houses = $house_model->getAllHouse();

        $export_model = $this->model->get('exportstockModel');
        $spare_stock_model = $this->model->get('sparestockModel');

        $join = array('table'=>'user, steersman, house','where'=>'export_stock_user=user_id AND steersman = steersman_id AND house = house_id');



        $data = array(

            'where' => 'export_type = 2 AND export_stock_date >= '.$batdau.' AND export_stock_date < '.$ngayketthuc,

            );




        $data['order_by'] = 'export_stock_date';

        $data['order'] = 'ASC';




            require("lib/Classes/PHPExcel/IOFactory.php");

            require("lib/Classes/PHPExcel.php");



            $objPHPExcel = new PHPExcel();

        $index_worksheet = 0; //(worksheet mặc định là 0, nếu tạo nhiều worksheet $index_worksheet += 1)

        
            foreach ($houses as $house) {
                
                $data = array(

                'where' => 'export_type = 2 AND house = '.$house->house_id.' AND export_stock_date >= '.$batdau.' AND export_stock_date < '.$ngayketthuc,

                );

                $exports = $export_model->getAllStock($data,$join);

                $objPHPExcel->createSheet();

                $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A1', mb_strtoupper($infos->info_company, "UTF-8"))

                ->setCellValue('A2', 'PHÒNG VẬT TƯ KỸ THUẬT')

                ->setCellValue('F1', 'CỘNG HÒA XÃ CHỦ NGHĨA VIỆT NAM')

                ->setCellValue('F2', 'Độc lập - Tự do - Hạnh phúc')

                ->setCellValue('A4', 'BẢNG TỔNG HỢP PHIẾU XUẤT KHO')

                ->setCellValue('A6', 'STT')

               ->setCellValue('B6', 'NGÀY')

               ->setCellValue('C6', 'PHIẾU XUẤT KHO')

               ->setCellValue('D6', 'NỘI DUNG')

               ->setCellValue('E6', 'SỐ LƯỢNG')

               ->setCellValue('F6', 'NGƯỜI NHẬN')

               ->setCellValue('G6', 'XE')

               ->setCellValue('H6', 'MOOC');

              


            if ($exports) {



                $hang = 7;

                $i=1;



                $k=0;
                foreach ($exports as $row) {


                        $spares = $spare_stock_model->getAllStock(array('where'=>'export_stock = '.$row->export_stock_id),array('table'=>'spare_part, spare_part_code','where'=>'spare_part = spare_part_id AND code_list = spare_part_code_id'));




                        //$objPHPExcel->setActiveSheetIndex(0)->getStyle('B'.$hang)->getNumberFormat()->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

                         $objPHPExcel->setActiveSheetIndex($index_worksheet)

                            ->setCellValue('A' . $hang, $i++)

                            ->setCellValueExplicit('B' . $hang, $this->lib->hien_thi_ngay_thang($row->export_stock_date))

                            ->setCellValue('C' . $hang, $row->export_stock_code)

                            ->setCellValue('D' . $hang, $row->export_stock_comment)

                            ->setCellValue('E' . $hang, $row->export_stock_total)

                            ->setCellValue('F' . $hang, $row->steersman_name)

                            ->setCellValue('G' . $hang, (isset($vehicle_data['name'][$row->vehicle])?$vehicle_data['name'][$row->vehicle]:null))

                            ->setCellValue('H' . $hang, (isset($romooc_data['name'][$row->romooc])?$romooc_data['name'][$row->romooc]:null));

                        
                        $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':H'.$hang)->getFont()->setBold(true);

                         $hang++;

                         $j=1;

                        foreach ($spares as $spare) {
                            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                            ->setCellValue('A' . $hang, $j++)

                            ->setCellValueExplicit('B' . $hang, $spare->code)

                            ->setCellValue('C' . $hang, $spare->name)

                            ->setCellValue('D' . $hang, $spare->spare_part_seri)

                            ->setCellValue('E' . $hang, $spare->spare_stock_number)

                            ->setCellValue('G' . $hang, (isset($vehicle_data['name'][$row->vehicle])?$vehicle_data['name'][$row->vehicle]:null))

                            ->setCellValue('H' . $hang, (isset($romooc_data['name'][$row->romooc])?$romooc_data['name'][$row->romooc]:null));


                            $objPHPExcel->getActiveSheet()->getStyle('A'.$hang.':H'.$hang)->getFont()->setItalic(true);

                            $hang++;
                        }




                }

            }



            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('B'.$hang, 'TỔNG')


               ->setCellValue('E'.$hang, '=SUM(E7:E'.($hang-1).')/2');

            

            $objPHPExcel->getActiveSheet()->getStyle('A7:D'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('A7:D'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('F7:H'.$hang)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle('F7:H'.$hang)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:H'.$hang)->applyFromArray(

                array(

                    

                    'borders' => array(

                        'allborders' => array(

                          'style' => PHPExcel_Style_Border::BORDER_THIN

                        )

                    )

                )

            );


            $highestColumn = $objPHPExcel->getActiveSheet()->getHighestDataColumn();

            



            $objPHPExcel->setActiveSheetIndex($index_worksheet)

                ->setCellValue('A'.($hang+3), 'NGƯỜI LẬP BIỂU')

                ->setCellValue('F'.($hang+3), mb_strtoupper($infos->info_company, "UTF-8"));



            $objPHPExcel->getActiveSheet()->mergeCells('A'.($hang+3).':C'.($hang+3));

            $objPHPExcel->getActiveSheet()->mergeCells('F'.($hang+3).':H'.($hang+3));


            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':H'.($hang+3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang+3).':H'.($hang+3))->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle('A'.($hang).':H'.($hang+3))->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );





            $highestRow = $objPHPExcel->getActiveSheet()->getHighestRow();



            $highestRow ++;



            $objPHPExcel->getActiveSheet()->mergeCells('A1:C1');

            $objPHPExcel->getActiveSheet()->mergeCells('F1:H1');

            $objPHPExcel->getActiveSheet()->mergeCells('A2:C2');

            $objPHPExcel->getActiveSheet()->mergeCells('F2:H2');

            $objPHPExcel->getActiveSheet()->mergeCells('A4:H4');



            $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);



            $objPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setSize(16);



            $objPHPExcel->getActiveSheet()->getStyle('A1:H4')->applyFromArray(

                array(

                    

                    'font' => array(

                        'bold'  => true,

                        'color' => array('rgb' => '000000')

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('A2:C2')->applyFromArray(

                array(

                    

                    'font' => array(

                        'underline' => PHPExcel_Style_Font::UNDERLINE_SINGLE,

                    )

                )

            );



            $objPHPExcel->getActiveSheet()->getStyle('E7:E'.$highestRow)->getNumberFormat()->setFormatCode("#,##0_);[Black](#,##0)");

            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

            $objPHPExcel->getActiveSheet()->getStyle('A6:H6')->getFont()->setBold(true);

            $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(26);

            $objPHPExcel->getActiveSheet()->getDefaultColumnDimension()->setWidth(25);

            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(10);

            //$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(25);



            

            $objPHPExcel->getActiveSheet()->setTitle($house->house_name);



            $objPHPExcel->getActiveSheet()->freezePane('A7');

            $objPHPExcel->setActiveSheetIndex($index_worksheet);

            $index_worksheet++;
            }
        
        
            







            // Set properties

            $objPHPExcel->getProperties()->setCreator("TCMT")

                            ->setLastModifiedBy($_SESSION['user_logined'])

                            ->setTitle("Sale Report")

                            ->setSubject("Sale Report")

                            ->setDescription("Sale Report.")

                            ->setKeywords("Sale Report")

                            ->setCategory("Sale Report");



            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');



            header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");

            header("Content-Disposition: attachment; filename= BẢNG TỔNG HỢP PHIẾU XUẤT KHO.xlsx");

            header("Cache-Control: max-age=0");

            ob_clean();

            $objWriter->save("php://output");

        

    }


}
?>