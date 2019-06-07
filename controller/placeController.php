<?php
Class placeController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->place) || json_decode($_SESSION['user_permission_action'])->place != "place") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Quản lý thông tin kho hàng';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'place_name';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
        }
        $province_model = $this->model->get('provinceModel');
        $provinces = $province_model->getAllProvince(array('order_by'=>'province_name','order'=>'ASC'));
        $this->view->data['provinces'] = $provinces;

        $id = $this->registry->router->param_id;

        $place_model = $this->model->get('placeModel');
        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $join = array('table'=>'province','where'=>'province=province_id');

        $data = array(
            'where' => '1=1',
        );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND place_id = '.$id;
        }
        
        $tongsodong = count($place_model->getAllPlace($data,$join));
        $tongsotrang = ceil($tongsodong / $sonews);
        

        $this->view->data['page'] = $page;
        $this->view->data['order_by'] = $order_by;
        $this->view->data['order'] = $order;
        $this->view->data['keyword'] = $keyword;
        $this->view->data['pagination_stages'] = $pagination_stages;
        $this->view->data['tongsotrang'] = $tongsotrang;
        $this->view->data['sonews'] = $sonews;
        $this->view->data['limit'] = $limit;

        $data = array(
            'order_by'=>$order_by,
            'order'=>$order,
            'limit'=>$x.','.$sonews,
            'where' => '1=1',
            );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND place_id = '.$id;
        }
        
        if ($keyword != '') {
            $search = ' AND ( place_name LIKE "%'.$keyword.'%" 
                        OR province_name LIKE "%'.$keyword.'%" )';
            $data['where'] .= $search;
        }
        
        $this->view->data['places'] = $place_model->getAllPlace($data,$join);

        $this->view->data['lastID'] = isset($place_model->getLastPlace()->place_id)?$place_model->getLastPlace()->place_id:0;
        
        $this->view->show('place/index');
    }

    public function add(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->place) || json_decode($_SESSION['user_permission_action'])->place != "place") {
            return $this->view->redirect('user/login');
        }
        if (isset($_POST['yes'])) {
            $place = $this->model->get('placeModel');
            $place_temp = $this->model->get('placetempModel');
            $data = array(
                        
                        'province' => trim($_POST['province']),
                        'place_name' => trim($_POST['place_name']),
                        );


            if ($_POST['yes'] != "") {

                if ($place->checkPlace($_POST['yes'],trim($_POST['place_name']))) {
                    echo "Tên đã được sử dụng";
                    return false;
                }
                else{
                    $place->updatePlace($data,array('place_id' => $_POST['yes']));

                    $data2 = array('place_id'=>$_POST['yes'],'place_temp_date'=>strtotime(date('d-m-Y')),'place_temp_action'=>2,'place_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS kho hàng');
                    $data_temp = array_merge($data, $data2);
                    $place_temp->createPlace($data_temp);

                    /*Log*/
                    /**/
                    echo "Cập nhật thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|place|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }
                    
                
                
            }
            else{

                if ($place->getPlaceByWhere(array('place_name'=>$data['place_name']))) {
                    echo "Tên đã được sử dụng";
                    return false;
                }
                else{
                    $place->createPlace($data);

                    $data2 = array('place_id'=>$place->getLastPlace()->place_id,'place_temp_date'=>strtotime(date('d-m-Y')),'place_temp_action'=>1,'place_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS kho hàng');
                    $data_temp = array_merge($data, $data2);
                    $place_temp->createPlace($data_temp);

                    /*Log*/
                    /**/

                    echo "Thêm thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$place->getLastPlace()->place_id."|place|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }
                    
                
                
            }
                    
        }
    }
    public function delete(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->place) || json_decode($_SESSION['user_permission_action'])->place != "place") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $place = $this->model->get('placeModel');
            $place_temp = $this->model->get('placetempModel');
            if (isset($_POST['xoa'])) {
                $data = explode(',', $_POST['xoa']);
                foreach ($data as $data) {
                    $place_data = (array)$place->getPlace($data);

                    $place->deletePlace($data);
                    
                    $data2 = array('place_id'=>$data,'place_temp_date'=>strtotime(date('d-m-Y')),'place_temp_action'=>3,'place_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS kho hàng');
                    $data_temp = array_merge($place_data, $data2);
                    $place_temp->createPlace($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|place|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }

                /*Log*/
                    /**/

                return true;
            }
            else{
                /*Log*/
                    /**/
                    $place_data = (array)$place->getPlace($_POST['data']);
                    $data2 = array('place_id'=>$_POST['data'],'place_temp_date'=>strtotime(date('d-m-Y')),'place_temp_action'=>3,'place_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS kho hàng');
                    $data_temp = array_merge($place_data, $data2);
                    $place_temp->createPlace($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|place|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);

                return $place->deletePlace($_POST['data']);
            }
            
        }
    }

    public function getPlace($id){
        return $this->getByID($this->table,$id);
    }

    private function getUrl(){

    }


}
?>