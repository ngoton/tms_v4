<?php
Class steersmanController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->steersman) || json_decode($_SESSION['user_permission_action'])->steersman != "steersman") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Quản lý tài xế';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'steersman_name';
            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
        }

        $id = $this->registry->router->param_id;

        $steersman_model = $this->model->get('steersmanModel');
        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;
        
        $data = array(
            'where' => '1=1',
        );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND steersman_id = '.$id;
        }

        $tongsodong = count($steersman_model->getAllSteersman($data));
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
            'where'=>'1=1',
            );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND steersman_id = '.$id;
        }
        
        if ($keyword != '') {
            $search = '( steersman_name LIKE "%'.$keyword.'%" OR steersman_phone LIKE "%'.$keyword.'%" )';
            $data['where'] = $search;
        }
        
        
        
        $this->view->data['steersmans'] = $steersman_model->getAllSteersman($data);

        $this->view->data['lastID'] = isset($steersman_model->getLastSteersman()->steersman_id)?$steersman_model->getLastSteersman()->steersman_id:0;
        
        $this->view->show('steersman/index');
    }

    

    public function add(){
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->steersman) || json_decode($_SESSION['user_permission_action'])->steersman != "steersman") {
            return $this->view->redirect('user/login');
        }
        if (isset($_POST['yes'])) {
            $steersman = $this->model->get('steersmanModel');
            $steersman_temp = $this->model->get('steersmantempModel');
            $data = array(
                        'steersman_name' => trim($_POST['steersman_name']),
                        'steersman_phone' => trim($_POST['steersman_phone']),
                        'steersman_cmnd' => trim($_POST['steersman_cmnd']),
                        'steersman_code' => trim($_POST['steersman_code']),
                        'steersman_bank' => trim($_POST['steersman_bank']),
                        'steersman_gplx' => trim($_POST['steersman_gplx']),
                        'steersman_birth' => strtotime(trim($_POST['steersman_birth'])),
                        'steersman_start_time' => strtotime(trim($_POST['steersman_start_time'])),
                        'steersman_end_time' => strtotime(trim($_POST['steersman_end_time'])),
                        );
            if ($_POST['yes'] != "") {
                //$data['steersman_update_user'] = $_SESSION['userid_logined'];
                //$data['steersman_update_time'] = time();
                //var_dump($data);
                if ($steersman->checkSteersman($_POST['yes'],trim($_POST['steersman_code']))) {
                    echo "Thông tin này đã tồn tại";
                    return false;
                }
                else{
                    $steersman->updateSteersman($data,array('steersman_id' => $_POST['yes']));
                    echo "Cập nhật thành công";

                    $data2 = array('steersman_id'=>$_POST['yes'],'steersman_temp_date'=>strtotime(date('d-m-Y')),'steersman_temp_action'=>2,'steersman_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS tài xế');
                    $data_temp = array_merge($data, $data2);
                    $steersman_temp->createSteersman($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|steersman|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                    }
            }
            else{
                //$data['steersman_create_user'] = $_SESSION['userid_logined'];
                //$data['staff'] = $_POST['staff'];
                //var_dump($data);
                if ($steersman->getSteersmanByWhere(array('steersman_code'=>$data['steersman_code']))) {
                    echo "Thông tin này đã tồn tại";
                    return false;
                }
                else{
                    $steersman->createSteersman($data);
                    echo "Thêm thành công";

                    $data2 = array('steersman_id'=>$steersman->getLastSteersman()->steersman_id,'steersman_temp_date'=>strtotime(date('d-m-Y')),'steersman_temp_action'=>1,'steersman_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS tài xế');
                    $data_temp = array_merge($data, $data2);
                    $steersman_temp->createSteersman($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$steersman->getLastSteersman()->steersman_id."|steersman|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }
                
            }
                    
        }
    }

    
    

    public function delete(){
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->steersman) || json_decode($_SESSION['user_permission_action'])->steersman != "steersman") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $steersman = $this->model->get('steersmanModel');
            $steersman_temp = $this->model->get('steersmantempModel');
            if (isset($_POST['xoa'])) {
                $data = explode(',', $_POST['xoa']);
                foreach ($data as $data) {
                    $steersman_data = (array)$steersman->getSteersman($data);

                    $steersman->deleteSteersman($data);

                    $data2 = array('steersman_id'=>$data,'steersman_temp_date'=>strtotime(date('d-m-Y')),'steersman_temp_action'=>3,'steersman_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS tài xế');
                    $data_temp = array_merge($steersman_data, $data2);
                    $steersman_temp->createSteersman($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|steersman|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }
                return true;
            }
            else{

                $steersman_data = (array)$steersman->getSteersman($_POST['data']);
                $data2 = array('steersman_id'=>$_POST['data'],'steersman_temp_date'=>strtotime(date('d-m-Y')),'steersman_temp_action'=>3,'steersman_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS tài xế');
                    $data_temp = array_merge($steersman_data, $data2);
                    $steersman_temp->createSteersman($data_temp);

                date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|steersman|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);

                return $steersman->deleteSteersman($_POST['data']);
            }
            
        }
    }

    
    public function import(){
        $this->view->disableLayout();
        header('Content-Type: text/html; charset=utf-8');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->steersman) || json_decode($_SESSION['user_permission_action'])->steersman != "steersman") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES['import']['name'] != null) {

            require("lib/Classes/PHPExcel/IOFactory.php");
            require("lib/Classes/PHPExcel.php");

            $steersman = $this->model->get('steersmanModel');

            $objPHPExcel = new PHPExcel();
            // Set properties
            if (pathinfo($_FILES['import']['name'], PATHINFO_EXTENSION) == "xls") {
                $objReader = PHPExcel_IOFactory::createReader('Excel5');
            }
            else if (pathinfo($_FILES['import']['name'], PATHINFO_EXTENSION) == "xlsx") {
                $objReader = PHPExcel_IOFactory::createReader('Excel2007');
            }
            
            $objReader->setReadDataOnly(false);

            $objPHPExcel = $objReader->load($_FILES['import']['tmp_name']);
            $objWorksheet = $objPHPExcel->getActiveSheet();

            

            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'

            $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn); // e.g. 5

            //var_dump($objWorksheet->getMergeCells());die();
            
             

                for ($row = 2; $row <= $highestRow; ++ $row) {
                    $val = array();
                    for ($col = 0; $col < $highestColumnIndex; ++ $col) {
                        $cell = $objWorksheet->getCellByColumnAndRow($col, $row);
                        // Check if cell is merged
                        foreach ($objWorksheet->getMergeCells() as $cells) {
                            if ($cell->isInRange($cells)) {
                                $currMergedCellsArray = PHPExcel_Cell::splitRange($cells);
                                $cell = $objWorksheet->getCell($currMergedCellsArray[0][0]);
                                break;
                                
                            }
                        }
                        //$val[] = $cell->getValue();
                        $val[] = is_numeric($cell->getCalculatedValue()) ? round($cell->getCalculatedValue()) : $cell->getCalculatedValue();
                        //here's my prob..
                        //echo $val;
                    }
                    if ($val[1] != null && $val[2] != null) {

                            if(!$steersman->getSteersmanByWhere(array('steersman_number'=>trim($val[1])))) {
                                $steersman_data = array(
                                'steersman_number' => trim($val[1]),
                                'steersman_name' => trim($val[2]),
                                'steersman_phone' => trim($val[3]),
                                );
                                $steersman->createSteersman($steersman_data);
                            }
                            else if($steersman->getSteersmanByWhere(array('steersman_number'=>trim($val[1])))){
                                $id_steersman = $steersman->getSteersmanByWhere(array('steersman_number'=>trim($val[1])))->steersman_id;
                                $steersman_data = array(
                                'steersman_name' => trim($val[2]),
                                'steersman_phone' => trim($val[3]),
                                );
                                $steersman->updateSteersman($steersman_data,array('steersman_id' => $id_steersman));
                            }


                        
                    }
                    
                    //var_dump($this->getNameDistrict($this->lib->stripUnicode($val[1])));
                    // insert


                }
                //return $this->view->redirect('transport');
            
            return $this->view->redirect('steersman');
        }
        $this->view->show('steersman/import');

    }
    

    public function view() {
        
        $this->view->show('handling/view');
    }

}
?>