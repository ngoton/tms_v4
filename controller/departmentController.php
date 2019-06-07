<?php
Class departmentController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->department) || json_decode($_SESSION['user_permission_action'])->department != "department") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Quản lý thông tin phòng ban';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'department_name';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
        }

        $id = $this->registry->router->param_id;

        $department_model = $this->model->get('departmentModel');
        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $data = array(
            'where' => '1=1',
        );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND department_id = '.$id;
        }
        
        $tongsodong = count($department_model->getAllDepartment($data));
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
            $data['where'] .= ' AND department_id = '.$id;
        }
        
        if ($keyword != '') {
            $search = ' AND ( department_name LIKE "%'.$keyword.'%" OR department_code LIKE "%'.$keyword.'%" )';
            $data['where'] .= $search;
        }
        
        $this->view->data['departments'] = $department_model->getAllDepartment($data);

        $this->view->data['lastID'] = isset($department_model->getLastDepartment()->department_id)?$department_model->getLastDepartment()->department_id:0;
        
        $this->view->show('department/index');
    }

    public function add(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->department) || json_decode($_SESSION['user_permission_action'])->department != "department") {
            return $this->view->redirect('user/login');
        }
        if (isset($_POST['yes'])) {
            $department = $this->model->get('departmentModel');
            $department_temp = $this->model->get('departmenttempModel');
            $data = array(
                        
                        'department_name' => trim($_POST['department_name']),
                        'department_code' => trim($_POST['department_code']),
                        );


            if ($_POST['yes'] != "") {
                //var_dump($data);
                if ($department->getAllDepartmentByWhere($_POST['yes'].' AND department_code = "'.$data['department_code'].'"')) {
                    echo "Tên đã được sử dụng";
                    return false;
                }
                else if ($department->getAllDepartmentByWhere($_POST['yes'].' AND department_name = "'.$data['department_name'].'"')) {
                    echo "Tên đã được sử dụng";
                    return false;
                }
                
                
                else{
                    $department->updateDepartment($data,array('department_id' => $_POST['yes']));

                    $data2 = array('department_id'=>$_POST['yes'],'department_temp_date'=>strtotime(date('d-m-Y')),'department_temp_action'=>2,'department_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS phòng ban');
                    $data_temp = array_merge($data, $data2);
                    $department_temp->createDepartment($data_temp);

                    /*Log*/
                    /**/
                    echo "Cập nhật thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|department|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }
                
            }
            else{
                //var_dump($data);
                if ($department->getDepartmentByWhere(array('department_code'=>$data['department_code']))) {
                    echo "Tên đã được sử dụng";
                    return false;
                }
                else if ($department->getDepartmentByWhere(array('department_name'=>$data['department_name']))) {
                    echo "Tên đã được sử dụng";
                    return false;
                }
                
                else{

                    $department->createDepartment($data);

                    $data2 = array('department_id'=>$department->getLastDepartment()->department_id,'department_temp_date'=>strtotime(date('d-m-Y')),'department_temp_action'=>1,'department_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS phòng ban');
                    $data_temp = array_merge($data, $data2);
                    $department_temp->createDepartment($data_temp);

                    /*Log*/
                    /**/

                    echo "Thêm thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$department->getLastDepartment()->department_id."|department|".implode("-",$data)."\n"."\r\n";
                        
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
        if (!isset(json_decode($_SESSION['user_permission_action'])->department) || json_decode($_SESSION['user_permission_action'])->department != "department") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $department = $this->model->get('departmentModel');
            $department_temp = $this->model->get('departmenttempModel');
            if (isset($_POST['xoa'])) {
                $data = explode(',', $_POST['xoa']);
                foreach ($data as $data) {

                    $department_data = (array)$department->getDepartment($data);

                    $department->deleteDepartment($data);

                    $data2 = array('department_id'=>$data,'department_temp_date'=>strtotime(date('d-m-Y')),'department_temp_action'=>3,'department_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS phòng ban');
                    $data_temp = array_merge($department_data, $data2);
                    $department_temp->createDepartment($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|department|"."\n"."\r\n";
                        
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
                    $department_data = (array)$department->getDepartment($data);

                    $department->deleteDepartment($data);

                    $data2 = array('department_id'=>$data,'department_temp_date'=>strtotime(date('d-m-Y')),'department_temp_action'=>3,'department_temp_user'=>$_SESSION['userid_logined'],'name'=>'DS phòng ban');
                    $data_temp = array_merge($department_data, $data2);
                    $department_temp->createDepartment($data_temp);


                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|department|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);

                return $department->deleteDepartment($_POST['data']);
            }
            
        }
    }

    public function getdepartment($id){
        return $this->getByID($this->table,$id);
    }

    public function import(){
        $this->view->disableLayout();
        header('Content-Type: text/html; charset=utf-8');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->department) || json_decode($_SESSION['user_permission_action'])->department != "department") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES['import']['name'] != null) {

            require("lib/Classes/PHPExcel/IOFactory.php");
            require("lib/Classes/PHPExcel.php");

            $department = $this->model->get('departmentModel');

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

            

            for ($row = 2; $row <= $highestRow; ++ $row) {
                $val = array();
                for ($col = 0; $col < $highestColumnIndex; ++ $col) {
                    $cell = $objWorksheet->getCellByColumnAndRow($col, $row);
                    // Check if cell is merged
                    foreach ($objWorksheet->getMergeCells() as $cells) {
                        if ($cell->isInRange($cells)) {
                            $currMergedCellsArray = PHPExcel_Cell::splitRange($cells);
                            $cell = $objWorksheet->getCell($currMergedCellsArray[0][0]);
                            if ($col == 1) {
                                $y++;
                            }
                            
                            break;
                            
                        }
                    }

                    $val[] = $cell->getCalculatedValue();
                    //here's my prob..
                    //echo $val;
                }


                if ($val[1] != null) {
                    

                    if (!$department->getDepartmentByWhere(array('department_name'=>trim($val[1])))) {
                        $department_data = array(
                            'department_name' => trim($val[1]),
                            );

                        $department->createDepartment($department_data);
                    }
                    else{
                        $id_department = $department->getDepartmentByWhere(array('department_name'=>trim($val[1])))->department_id;

                        $department_data = array(
                            'department_name' => trim($val[1]),
                            );

                            $department->updateDepartment($department_data,array('department_id' => $id_department));
                    }
                    
                }
                


            }
            return $this->view->redirect('department');
        }
        $this->view->show('department/import');

    }


}
?>