<?php
Class provinceController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->province) || json_decode($_SESSION['user_permission_action'])->province != "province") {
            return $this->view->redirect('user/login');
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
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'province_name';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 20;
        }

        $id = $this->registry->router->param_id;

        $province_model = $this->model->get('provinceModel');
        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $data = array(
            'where' => '1=1',
        );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND province_id = '.$id;
        }
        
        $tongsodong = count($province_model->getAllProvince($data));
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
            $data['where'] .= ' AND province_id = '.$id;
        }
        
        if ($keyword != '') {
            $search = ' AND ( province_name LIKE "%'.$keyword.'%" OR province_code LIKE "%'.$keyword.'%" )';
            $data['where'] .= $search;
        }
        
        $this->view->data['provinces'] = $province_model->getAllProvince($data);

        $this->view->data['lastID'] = isset($province_model->getLastProvince()->province_id)?$province_model->getLastProvince()->province_id:0;
        
        $this->view->show('province/index');
    }

    public function add(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->province) || json_decode($_SESSION['user_permission_action'])->province != "province") {
            return $this->view->redirect('user/login');
        }
        if (isset($_POST['yes'])) {
            $province = $this->model->get('provinceModel');
            $data = array(
                        
                        'province_name' => trim($_POST['province_name']),
                        'province_code' => trim($_POST['province_code']),
                        );


            if ($_POST['yes'] != "") {
                //var_dump($data);
                if ($province->getAllProvinceByWhere($_POST['yes'].' AND province_code = "'.$data['province_code'].'"')) {
                    echo "Tên đã được sử dụng";
                    return false;
                }
                else if ($province->getAllProvinceByWhere($_POST['yes'].' AND province_name = "'.$data['province_name'].'"')) {
                    echo "Tên đã được sử dụng";
                    return false;
                }
                
                
                else{
                    $province->updateProvince($data,array('province_id' => $_POST['yes']));

                    /*Log*/
                    /**/
                    echo "Cập nhật thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|province|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }
                
            }
            else{
                //var_dump($data);
                if ($province->getProvinceByWhere(array('province_code'=>$data['province_code']))) {
                    echo "Tên đã được sử dụng";
                    return false;
                }
                else if ($province->getProvinceByWhere(array('province_name'=>$data['province_name']))) {
                    echo "Tên đã được sử dụng";
                    return false;
                }
                
                else{

                    $province->createProvince($data);

                    /*Log*/
                    /**/

                    echo "Thêm thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$province->getLastProvince()->province_id."|province|".implode("-",$data)."\n"."\r\n";
                        
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
        if (!isset(json_decode($_SESSION['user_permission_action'])->province) || json_decode($_SESSION['user_permission_action'])->province != "province") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $province = $this->model->get('provinceModel');
            $district = $this->model->get('districtModel');
            $place = $this->model->get('placeModel');
            if (isset($_POST['xoa'])) {
                $data = explode(',', $_POST['xoa']);
                foreach ($data as $data) {
                    $province->deleteProvince($data);
                    $district->queryDistrict('DELETE FROM district WHERE province='.$data);
                    $place->queryPlace('DELETE FROM place WHERE province='.$data);
                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|province|"."\n"."\r\n";
                        
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
                    $district->queryDistrict('DELETE FROM district WHERE province='.$_POST['data']);
                    $place->queryPlace('DELETE FROM place WHERE province='.$_POST['data']);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|province|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);

                return $province->deleteProvince($_POST['data']);
            }
            
        }
    }

    public function getProvince($id){
        return $this->getByID($this->table,$id);
    }

    public function import(){
        $this->view->disableLayout();
        header('Content-Type: text/html; charset=utf-8');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->province) || json_decode($_SESSION['user_permission_action'])->province != "province") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES['import']['name'] != null) {

            require("lib/Classes/PHPExcel/IOFactory.php");
            require("lib/Classes/PHPExcel.php");

            $province = $this->model->get('provinceModel');

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
                    

                    if (!$province->getProvinceByWhere(array('province_name'=>trim($val[1])))) {
                        $province_data = array(
                            'province_name' => trim($val[1]),
                            );

                        $province->createProvince($province_data);
                    }
                    else{
                        $id_province = $province->getProvinceByWhere(array('province_name'=>trim($val[1])))->province_id;

                        $province_data = array(
                            'province_name' => trim($val[1]),
                            );

                            $province->updateProvince($province_data,array('province_id' => $id_province));
                    }
                    
                }
                


            }
            return $this->view->redirect('province');
        }
        $this->view->show('province/import');

    }


}
?>