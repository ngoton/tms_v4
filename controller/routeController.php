<?php
Class routeController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->route) || json_decode($_SESSION['user_permission_action'])->route != "route") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Quản lý thông tin địa điểm';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'route_name';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
        }
        $province_model = $this->model->get('provinceModel');
        $provinces = $province_model->getAllProvince(array('order_by'=>'province_name','order'=>'ASC'));
        $this->view->data['provinces'] = $provinces;

        $id = $this->registry->router->param_id;

        $route_model = $this->model->get('routeModel');
        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $join = array('table'=>'province','where'=>'province=province_id');

        $data = array(
            'where' => '1=1',
        );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND route_id = '.$id;
        }
        
        $tongsodong = count($route_model->getAllPlace($data,$join));
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
            $data['where'] .= ' AND route_id = '.$id;
        }
        
        if ($keyword != '') {
            $search = ' AND ( route_name LIKE "%'.$keyword.'%" 
                        OR province_name LIKE "%'.$keyword.'%" )';
            $data['where'] .= $search;
        }
        
        $this->view->data['routes'] = $route_model->getAllPlace($data,$join);

        $this->view->data['lastID'] = isset($route_model->getLastPlace()->route_id)?$route_model->getLastPlace()->route_id:0;
        
        $this->view->show('route/index');
    }

    public function add(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->route) || json_decode($_SESSION['user_permission_action'])->route != "route") {
            return $this->view->redirect('user/login');
        }
        if (isset($_POST['yes'])) {
            $route = $this->model->get('routeModel');
            $route_temp = $this->model->get('routetempModel');
            $data = array(
                        
                        'province' => trim($_POST['province']),
                        'route_name' => trim($_POST['route_name']),
                        );


            if ($_POST['yes'] != "") {

                if ($route->checkPlace($_POST['yes'],trim($_POST['route_name']))) {
                    echo "Tên đã được sử dụng";
                    return false;
                }
                else{
                    $route->updatePlace($data,array('route_id' => $_POST['yes']));

                    $data2 = array('route_id'=>$_POST['yes'],'route_temp_date'=>strtotime(date('d-m-Y')),'route_temp_action'=>2,'route_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS địa điểm');
                    $data_temp = array_merge($data, $data2);
                    $route_temp->createPlace($data_temp);

                    /*Log*/
                    /**/
                    echo "Cập nhật thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|route|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }
                    
                
                
            }
            else{

                if ($route->getPlaceByWhere(array('route_name'=>$data['route_name']))) {
                    echo "Tên đã được sử dụng";
                    return false;
                }
                else{
                    $route->createPlace($data);

                    $data2 = array('route_id'=>$route->getLastPlace()->route_id,'route_temp_date'=>strtotime(date('d-m-Y')),'route_temp_action'=>1,'route_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS địa điểm');
                    $data_temp = array_merge($data, $data2);
                    $route_temp->createPlace($data_temp);

                    /*Log*/
                    /**/

                    echo "Thêm thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$route->getLastPlace()->route_id."|route|".implode("-",$data)."\n"."\r\n";
                        
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
        if (!isset(json_decode($_SESSION['user_permission_action'])->route) || json_decode($_SESSION['user_permission_action'])->route != "route") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $route = $this->model->get('routeModel');
            $route_temp = $this->model->get('routetempModel');
            if (isset($_POST['xoa'])) {
                $data = explode(',', $_POST['xoa']);
                foreach ($data as $data) {
                    $route_data = (array)$route->getPlace($data);

                    $route->deletePlace($data);
                    
                    $data2 = array('route_id'=>$data,'route_temp_date'=>strtotime(date('d-m-Y')),'route_temp_action'=>3,'route_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS địa điểm');
                    $data_temp = array_merge($route_data, $data2);
                    $route_temp->createPlace($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|route|"."\n"."\r\n";
                        
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
                    $route_data = (array)$route->getPlace($_POST['data']);
                    $data2 = array('route_id'=>$_POST['data'],'route_temp_date'=>strtotime(date('d-m-Y')),'route_temp_action'=>3,'route_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS địa điểm');
                    $data_temp = array_merge($route_data, $data2);
                    $route_temp->createPlace($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|route|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);

                return $route->deletePlace($_POST['data']);
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