<?php
Class bankController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->bank) || json_decode($_SESSION['user_permission_action'])->bank != "bank") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Danh mục tài khoản tiền mặt ngân hàng';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'bank_code ASC, bank_name';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 100;
        }
        
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

        

        
        $this->view->data['banks'] = $bank_model->getAllBank($data);
        $this->view->data['lastID'] = isset($bank_model->getLastBank()->bank_id)?$bank_model->getLastBank()->bank_id:0;

        /* Lấy tổng doanh thu*/
        
        /*************/
        $this->view->show('bank/index');
    }

   
   
    public function add(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (isset($_POST['yes'])) {
            
            $bank_model = $this->model->get('bankModel');
            $data = array(
                        
                        'bank_code' => trim($_POST['bank_code']),
                        'bank_name' => trim($_POST['bank_name']),
                        'account_number' => trim($_POST['account_number']),
                        'account_bank' => trim($_POST['account_bank']),
                        'account_bank_branch' => trim($_POST['account_bank_branch']),
                        );
            

            if ($_POST['yes'] != "") {
                $check = $bank_model->queryBank('SELECT * FROM bank WHERE (bank_code='.$data['bank_code'].' OR bank_name='.$data['bank_name'].' OR account_number='.$data['account_number'].') AND bank_id!='.$_POST['yes']);
                if($check){
                    echo "Tài khoản đã tồn tại";
                    return false;
                }
                else{
                    $bank_model->updateBank($data,array('bank_id' => trim($_POST['yes'])));
                    echo "Cập nhật thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|bank|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }
                
            }
            else{
                $check = $bank_model->queryBank('SELECT * FROM bank WHERE (bank_code='.$data['bank_code'].' OR bank_name='.$data['bank_name'].' OR account_number='.$data['account_number'].')');
                if($check){
                    echo "Tài khoản đã tồn tại";
                    return false;
                }
                else{
                    $bank_model->createBank($data);
                    echo "Thêm thành công";

             

                date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                    $filename = "action_logs.txt";
                    $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$bank_model->getLastBank()->bank_id."|bank|".implode("-",$data)."\n"."\r\n";
                    
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $bank_model = $this->model->get('bankModel');
           
            if (isset($_POST['xoa'])) {
                $data = explode(',', $_POST['xoa']);
                foreach ($data as $data) {
                       $bank_model->deleteBank($data);
                        echo "Xóa thành công";
                        date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|bank|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                    
                    
                }
                return true;
            }
            else{
                        $bank_model->deleteBank($_POST['data']);
                        echo "Xóa thành công";
                        date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|bank|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                    
            }
            
        }
    }

    public function import(){
        $this->view->disableLayout();
        header('Content-Type: text/html; charset=utf-8');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->bank) || json_decode($_SESSION['user_permission_action'])->bank != "bank") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES['import']['name'] != null) {

            require("lib/Classes/PHPExcel/IOFactory.php");
            require("lib/Classes/PHPExcel.php");

            $account = $this->model->get('accountModel');

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
                    
                    $a = trim($val[1]);
                    // $parent = substr($a, 0, strpos($a, '_'));
                    $parent = "";
                    $parent_id = null;

                    if (trim($val[3]) != "") {
                        $parent = trim($val[3]);
                    }

                    if ($parent != "") {
                        $parents = $account->getAccountByWhere(array('account_number'=>$parent));
                        if ($parents) {
                            $parent_id = $parents->account_id;
                        }
                    }

                    if (!$account->getAccountByWhere(array('account_number'=>$a))) {
                        $account_data = array(
                            'account_number' => $a,
                            'account_name' => trim($val[2]),
                            'account_parent' => $parent_id,
                            );

                        $account->createAccount($account_data);
                    }
                    else{
                        $id_account = $account->getAccountByWhere(array('account_number'=>$a))->account_id;

                        $account_data = array(
                            'account_number' => trim($val[1]),
                            'account_name' => trim($val[2]),
                            'account_parent' => $parent_id,
                            );

                            $account->updateAccount($account_data,array('account_id' => $id_account));
                    }
                    
                }
                


            }
            return $this->view->redirect('account');
        }
        $this->view->show('account/import');

    }

    public function importasset(){
        $this->view->disableLayout();
        header('Content-Type: text/html; charset=utf-8');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->bank) || json_decode($_SESSION['user_permission_action'])->bank != "bank") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_FILES['import']['name'] != null) {

            require("lib/Classes/PHPExcel/IOFactory.php");
            require("lib/Classes/PHPExcel.php");

            $account = $this->model->get('accountModel');
            $account_balance = $this->model->get('accountbalanceModel');

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

            $nameWorksheet = trim($objWorksheet->getTitle());


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
                    
                    $a = trim($val[1]);
                    if (!$account->getAccountByWhere(array('account_number'=>$a))) {
                        if (is_numeric($a)) {
                            $acc = array(
                                'account_number'=>$a,
                                'account_parent'=>0,
                            );
                            $account->createAccount($acc);
                            $id_account = $account->getLastAccount()->account_id;
                        }
                        else{
                            $ac = substr($a, 0, strpos($a, '_'));
                            $acc_parent = $account->getAccountByWhere(array('account_number'=>$ac));
                            $acc = array(
                                'account_number'=>$a,
                                'account_parent'=>$acc_parent->account_id,
                            );
                            $account->createAccount($acc);
                            $id_account = $account->getLastAccount()->account_id;
                        }

                    }
                    else{
                        $id_account = $account->getAccountByWhere(array('account_number'=>$a))->account_id;
                    }

                    if ($val[3] != null) {
                        $account_data = array(
                            'account_balance_date' => strtotime($nameWorksheet),
                            'account' => $id_account,
                            'money' => trim($val[3]),
                            'week' => (int)date('W', strtotime($nameWorksheet)),
                            'year' => (int)date('Y', strtotime($nameWorksheet)),
                            );

                        if($account_data['week'] == 53){
                            $account_data['week'] = 1;
                            $account_data['year'] = $account_data['year']+1;

                            $account_data['week'] = 1;
                            $account_data['year'] = $account_data['year']+1;
                        }
                        if (((int)date('W', strtotime($nameWorksheet)) == 1) && ((int)date('m', strtotime($nameWorksheet)) == 12) ) {
                            $account_data['year'] = (int)date('Y', strtotime($nameWorksheet))+1;
                            $account_data['year'] = (int)date('Y', strtotime($nameWorksheet))+1;
                        }

                        $account_balance->createAccount($account_data);
                    }
                    if ($val[4] != null) {
                        $account_data = array(
                            'account_balance_date' => strtotime($nameWorksheet),
                            'account' => $id_account,
                            'money' => 0-trim($val[4]),
                            'week' => (int)date('W', strtotime($nameWorksheet)),
                            'year' => (int)date('Y', strtotime($nameWorksheet)),
                            );

                        if($account_data['week'] == 53){
                            $account_data['week'] = 1;
                            $account_data['year'] = $account_data['year']+1;

                            $account_data['week'] = 1;
                            $account_data['year'] = $account_data['year']+1;
                        }
                        if (((int)date('W', strtotime($nameWorksheet)) == 1) && ((int)date('m', strtotime($nameWorksheet)) == 12) ) {
                            $account_data['year'] = (int)date('Y', strtotime($nameWorksheet))+1;
                            $account_data['year'] = (int)date('Y', strtotime($nameWorksheet))+1;
                        }

                        $account_balance->createAccount($account_data);
                    }
                    
                    
                }
                


            }
            return $this->view->redirect('account');
        }
        $this->view->show('account/importasset');

    }

    

}
?>