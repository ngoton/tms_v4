<?php

Class vehicleworkController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehiclework) || json_decode($_SESSION['user_permission_action'])->vehiclework != "vehiclework") {
            $this->view->data['disable_control'] = 1;
        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý hoạt động xe';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'vehicle_number ASC, start_work';

            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 50;

        }



        $vehicle_model = $this->model->get('vehicleModel');

        $vehicles = $vehicle_model->getAllVehicle(array('order_by'=>'vehicle_number','order'=>'ASC'));

        $this->view->data['vehicles'] = $vehicles;



        $join = array('table'=>'vehicle','where'=>'vehicle.vehicle_id = vehicle_work.vehicle');



        $vehicle_work_model = $this->model->get('vehicleworkModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        

        $tongsodong = count($vehicle_work_model->getAllVehicle(null,$join));

        $tongsotrang = ceil($tongsodong / $sonews);

        



        $this->view->data['page'] = $page;

        $this->view->data['order_by'] = $order_by;

        $this->view->data['order'] = $order;

        $this->view->data['keyword'] = $keyword;

        $this->view->data['limit'] = $limit;

        $this->view->data['pagination_stages'] = $pagination_stages;

        $this->view->data['tongsotrang'] = $tongsotrang;

        $this->view->data['sonews'] = $sonews;



        $data = array(

            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            );

        

        if ($keyword != '') {

            $search = '( vehicle_number LIKE "%'.$keyword.'%"  )';

            $data['where'] = $search;

        }

        

        

        

        $this->view->data['works'] = $vehicle_work_model->getAllVehicle($data,$join);



        $this->view->data['lastID'] = isset($vehicle_work_model->getLastVehicle()->vehicle_work_id)?$vehicle_work_model->getLastVehicle()->vehicle_work_id:0;

        

        $this->view->show('vehiclework/index');

    }




    public function add(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehiclework) || json_decode($_SESSION['user_permission_action'])->vehiclework != "vehiclework") {

            return $this->view->redirect('user/login');

        }

        if (isset($_POST['yes'])) {

            $vehicle_work_model = $this->model->get('vehicleworkModel');
            $vehicle_work_temp_model = $this->model->get('vehicleworktempModel');

            $data = array(


                        'vehicle' => trim($_POST['vehicle']),

                        'start_work' => strtotime(trim($_POST['start_work'])),

                        'end_work' => strtotime(trim($_POST['end_work'])),

                        );

            if ($_POST['yes'] != "") {
                
                    
                    $vehicle_work_model->updateVehicle($data,array('vehicle_work_id' => trim($_POST['yes'])));
                    echo "Cập nhật thành công";

                    $data2 = array('vehicle_work_id'=>$_POST['yes'],'vehicle_work_temp_date'=>strtotime(date('d-m-Y')),'vehicle_work_temp_action'=>2,'vehicle_work_temp_user'=>$_SESSION['userid_logined'],'name'=>'Hoạt động xe');
                    $data_temp = array_merge($data, $data2);
                    $vehicle_work_temp_model->createVehicle($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|vehicle_work|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                
                
            }
            else{
                
                $vehicle_work_model->createVehicle($data);

                    
                    echo "Thêm thành công";

                 $data2 = array('vehicle_work_id'=>$vehicle_work_model->getLastVehicle()->vehicle_work_id,'vehicle_work_temp_date'=>strtotime(date('d-m-Y')),'vehicle_work_temp_action'=>1,'vehicle_work_temp_user'=>$_SESSION['userid_logined'],'name'=>'Hoạt động xe');
                    $data_temp = array_merge($data, $data2);
                    $vehicle_work_temp_model->createVehicle($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$vehicle_work_model->getLastVehicle()->vehicle_work_id."|vehicle_work|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                
                
            }

                    

        }

    }



    

    



    public function delete(){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->vehiclework) || json_decode($_SESSION['user_permission_action'])->vehiclework != "vehiclework") {

            return $this->view->redirect('user/login');

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $vehicle = $this->model->get('vehicleworkModel');
            $vehicle_work_temp = $this->model->get('vehicleworktempModel');

            if (isset($_POST['xoa'])) {

                $data = explode(',', $_POST['xoa']);

                foreach ($data as $data) {

                    $vehicle_work_data = (array)$vehicle->getVehicle($data);

                    $vehicle->deleteVehicle($data);                    
                    
                    $data2 = array('vehicle_work_id'=>$data,'vehicle_work_temp_date'=>strtotime(date('d-m-Y')),'vehicle_work_temp_action'=>3,'vehicle_work_temp_user'=>$_SESSION['userid_logined'],'name'=>'Hoạt động xe');
                    $data_temp = array_merge($vehicle_work_data, $data2);
                    $vehicle_work_temp->createVehicle($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|vehicle_work|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);

                }

                return true;

            }

            else{

                $vehicle_work_data = (array)$vehicle->getVehicle($_POST['data']);
                $data2 = array('vehicle_work_id'=>$_POST['data'],'vehicle_work_temp_date'=>strtotime(date('d-m-Y')),'vehicle_work_temp_action'=>3,'vehicle_work_temp_user'=>$_SESSION['userid_logined'],'name'=>'Hoạt động xe');
                    $data_temp = array_merge($vehicle_work_data, $data2);
                    $vehicle_work_temp->createVehicle($data_temp);

                date_default_timezone_set("Asia/Ho_Chi_Minh"); 

                        $filename = "action_logs.txt";

                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|vehicle_work|"."\n"."\r\n";

                        

                        $fh = fopen($filename, "a") or die("Could not open log file.");

                        fwrite($fh, $text) or die("Could not write file!");

                        fclose($fh);



                return $vehicle->deleteVehicle($_POST['data']);

            }

            

        }

    }



    

}

?>