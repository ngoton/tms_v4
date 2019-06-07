<?php
Class sparepartController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->sparepart) || json_decode($_SESSION['user_permission_action'])->sparepart != "sparepart") {
            $this->view->data['disable_control'] = 1;
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Quản lý thông tin vật tư';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'code';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
        }
        
        $id = $this->registry->router->param_id;
        $code = "";
        if (substr($id, -4) == "code") {
            $code = str_replace('code', '', $id);
        }
        

        $spare_code_model = $this->model->get('sparepartcodeModel');
        $spare_model = $this->model->get('sparepartModel');
        $spare_sub_model = $this->model->get('sparesubModel');

        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;

        $data = array(
            'where' => '1=1',
        );

        if ($code > 0) {
            $data['where'] .= ' AND spare_part_code_id = '.$code;
        }

        $tongsodong = count($spare_code_model->getAllStock($data));
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
        if ($code > 0) {
            $data['where'] .= ' AND spare_part_code_id = '.$code;
        }

        $spare_codes = $spare_code_model->getAllStock($data);

        $spares = array();
        $spare_part_types = array();

        foreach ($spare_codes as $spare) {
            $data = array(
                'where' => 'code_list = '.$spare->spare_part_code_id,
            );
            if (isset($id) && $id > 0 && $code == "") {
                $data['where'] .= ' AND spare_part_id = '.$id;
            }
            
            if ($keyword != '') {
                $search = ' AND ( spare_part_code LIKE "%'.$keyword.'%" 
                            OR spare_part_name LIKE "%'.$keyword.'%" 
                            OR spare_part_seri LIKE "%'.$keyword.'%" 
                            OR spare_part_brand LIKE "%'.$keyword.'%" )';
                $data['where'] .= $search;
            }
            
            $spares[$spare->spare_part_code_id] = $spare_model->getAllStock($data);

            foreach ($spares[$spare->spare_part_code_id] as $sp) {
                $spare_sub = "";
                $sts = explode(',', $sp->spare_part_type);
                foreach ($sts as $key) {
                    $subs = $spare_sub_model->getStock($key);
                    if ($subs) {
                        if ($spare_sub == "")
                            $spare_sub .= $subs->spare_sub_name;
                        else
                            $spare_sub .= ','.$subs->spare_sub_name;
                    }
                    
                }
                $spare_part_types[$sp->spare_part_id] = $spare_sub;
            }
        }

        $this->view->data['spare_codes'] = $spare_codes;
        $this->view->data['spares'] = $spares;
        $this->view->data['spare_part_types'] = $spare_part_types;


        $this->view->data['lastID'] = isset($spare_code_model->getLastStock()->spare_part_code_id)?$spare_code_model->getLastStock()->spare_part_code_id:0;
        
        $this->view->show('sparepart/index');
    }

    public function getCode(){

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }


        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $spare_code_model = $this->model->get('sparepartcodeModel');

            

            if ($_POST['keyword'] == "*") {



                $list = $spare_code_model->getAllStock();

            }

            else{

                $data = array(

                'where'=>'( name LIKE "%'.$_POST['keyword'].'%" OR code LIKE "%'.$_POST['keyword'].'%")',

                );

                $list = $spare_code_model->getAllStock($data);

            }

            

            foreach ($list as $rs) {

                // put in bold the written text

                $name = '['.$rs->code.']-'.$rs->name;

                if ($_POST['keyword'] != "*") {

                    $name = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', '['.$rs->code.']-'.$rs->name);

                }

                

                // add new option

                echo '<li onclick="set_item(\''.$rs->spare_part_code_id.'\',\''.$rs->name.'\',\''.$rs->code.'\')">'.$name.'</li>';

            }

        }

    }

    public function getSub(){
        header('Content-type: application/json');
        $q = $_GET["search"];

        $sub_model = $this->model->get('sparesubModel');
        $data = array(
            'where' => 'spare_sub_name LIKE "%'.$q.'%"',
        );
        $subs = $sub_model->getAllStock($data);
        $arr = array();
        foreach ($subs as $sub) {
            $arr[] = $sub->spare_sub_name;
        }
        
        echo json_encode($arr);
    }

    public function add(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->sparepart) || json_decode($_SESSION['user_permission_action'])->sparepart != "sparepart") {
            return $this->view->redirect('user/login');
        }
        if (isset($_POST['yes'])) {
            $spare = $this->model->get('sparepartModel');
            $spare_code = $this->model->get('sparepartcodeModel');
            $spare_temp = $this->model->get('spareparttempModel');
            $data = array(
                        
                        'spare_part_name' => trim($_POST['spare_part_name']),

                        'spare_part_code' => trim($_POST['spare_part_code']),

                        'spare_part_seri' => trim($_POST['spare_part_seri']),

                        'spare_part_brand' => trim($_POST['spare_part_brand']),

                        'spare_part_date_manufacture' => strtotime($_POST['spare_part_date_manufacture']),

                        'code_list' => trim($_POST['spare_part_code_id']),
                        );

            $spare_sub_model = $this->model->get('sparesubModel');

            $contributor = "";
            if(trim($_POST['spare_part_type']) != ""){
                $support = explode(',', trim($_POST['spare_part_type']));

                if ($support) {
                    foreach ($support as $key) {
                        $name = $spare_sub_model->getStockByWhere(array('spare_sub_name'=>trim($key)));
                        if ($name) {
                            if ($contributor == "")
                                $contributor .= $name->spare_sub_id;
                            else
                                $contributor .= ','.$name->spare_sub_id;
                        }
                        else{
                            $spare_sub_model->createStock(array('spare_sub_name'=>trim($key)));
                            if ($contributor == "")
                                $contributor .= $spare_sub_model->getLastStock()->spare_sub_id;
                            else
                                $contributor .= ','.$spare_sub_model->getLastStock()->spare_sub_id;
                        }
                        
                    }
                }

            }
            $data['spare_part_type'] = $contributor;

            if ($data['code_list'] != "") {
                $code = $spare_code->getStock($data['code_list']);
            }
            else{
                $data_code = array(
                    'name' => trim($_POST['spare_part_name']),
                    'code' => trim($_POST['spare_part_code']),
                );
                $spare_code->createStock($data_code);
                $code = $spare_code->getStock($spare_code->getLastStock()->spare_part_code_id);
            }

            $data['code_list'] = $code->spare_part_code_id;
            $data['spare_part_name'] = $code->name;
            $data['spare_part_code'] = $code->code;


            if ($_POST['yes'] != "") {

                if ($spare->getAllStockByWhere($_POST['yes'].' AND code_list = '.$data['code_list'].' AND spare_part_seri = '.trim($_POST['spare_part_seri']))) {
                    echo "Thông tin này đã tồn tại";
                    return false;
                }
                else{
                    $spare->updateStock($data,array('spare_part_id' => $_POST['yes']));

                    /*Log*/
                    /**/
                    echo "Cập nhật thành công";

                    $data2 = array('spare_part_id'=>$_POST['yes'],'spare_part_temp_date'=>strtotime(date('d-m-Y')),'spare_part_temp_action'=>2,'spare_part_temp_user'=>$_SESSION['userid_logined'],'name'=>'Vật tư');
                    $data_temp = array_merge($data, $data2);
                    $spare_temp->createStock($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|spare_part|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }
                    
                
                
            }
            else{

                if ($spare->getStockByWhere(array('code_list'=>$data['code_list'],'spare_part_seri'=>$data['spare_part_seri']))) {
                    echo "Thông tin này đã tồn tại";
                    return false;
                }
                else{
                    $spare->createStock($data);

                    /*Log*/
                    /**/

                    echo "Thêm thành công";

                    $data2 = array('spare_part_id'=>$spare->getLastStock()->spare_part_id,'spare_part_temp_date'=>strtotime(date('d-m-Y')),'spare_part_temp_action'=>1,'spare_part_temp_user'=>$_SESSION['userid_logined'],'name'=>'Vật tư');
                    $data_temp = array_merge($data, $data2);
                    $spare_temp->createStock($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$spare->getLastStock()->spare_part_id."|spare_part|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }
                    
                
                
            }
                    
        }
    }
    public function addcode(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->sparepart) || json_decode($_SESSION['user_permission_action'])->sparepart != "sparepart") {
            return $this->view->redirect('user/login');
        }
        if (isset($_POST['yes'])) {
            $spare_code = $this->model->get('sparepartcodeModel');
            $data = array(
                        
                        'name' => trim($_POST['name']),

                        'code' => trim($_POST['code']),

                        );


            if ($_POST['yes'] != "") {

                if ($spare_code->getAllStockByWhere($_POST['yes'].' AND code = "'.$data['code'].'" AND name = "'.$data['name'].'"')) {
                    echo "Thông tin này đã tồn tại";
                    return false;
                }
                else{
                    $spare_code->updateStock($data,array('spare_part_code_id' => $_POST['yes']));

                    /*Log*/
                    /**/
                    echo "Cập nhật thành công";


                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|spare_part_code|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }
                    
                
                
            }
            else{

                if ($spare_code->getStockByWhere(array('code'=>$data['code'],'name'=>$data['name']))) {
                    echo "Thông tin này đã tồn tại";
                    return false;
                }
                else{
                    $spare_code->createStock($data);

                    /*Log*/
                    /**/

                    echo "Thêm thành công";

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$spare_code->getLastStock()->spare_part_code_id."|spare_part_code|".implode("-",$data)."\n"."\r\n";
                        
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
        if (!isset(json_decode($_SESSION['user_permission_action'])->sparepart) || json_decode($_SESSION['user_permission_action'])->sparepart != "sparepart") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $spare = $this->model->get('sparepartModel');
            $spare_temp = $this->model->get('spareparttempModel');
            
            if (isset($_POST['xoa'])) {
                $data = explode(',', $_POST['xoa']);
                foreach ($data as $data) {
                    $spare_data = (array)$spare->getStock($data); 
                    $spare->deleteStock($data);

                    $data2 = array('spare_part_id'=>$data,'spare_part_temp_date'=>strtotime(date('d-m-Y')),'spare_part_temp_action'=>3,'spare_part_temp_user'=>$_SESSION['userid_logined'],'name'=>'Vật tư');
                    $data_temp = array_merge($spare_data, $data2);
                    $spare_temp->createStock($data_temp);
                    
                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|spare_part|"."\n"."\r\n";
                        
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
                    $spare_data = (array)$spare->getStock($_POST['data']);

                    $data2 = array('spare_part_id'=>$_POST['data'],'spare_part_temp_date'=>strtotime(date('d-m-Y')),'spare_part_temp_action'=>3,'spare_part_temp_user'=>$_SESSION['userid_logined'],'name'=>'Vật tư');
                    $data_temp = array_merge($spare_data, $data2);
                    $spare_temp->createStock($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|spare_part|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);

                return $spare->deleteStock($_POST['data']);
            }
            
        }
    }

    public function deletecode(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->sparepart) || json_decode($_SESSION['user_permission_action'])->sparepart != "sparepart") {
            return $this->view->redirect('user/login');
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $spare_code = $this->model->get('sparepartcodeModel');
            $spare = $this->model->get('sparepartModel');
            $spare_temp = $this->model->get('spareparttempModel');

            $spares = $spare->getAllStock(array('where'=>'code_list = '.$_POST['data']));
            foreach ($spares as $sp) {
                $spare_data = (array)$spare->getStock($sp->spare_part_id);

                $data2 = array('spare_part_id'=>$_POST['data'],'spare_part_temp_date'=>strtotime(date('d-m-Y')),'spare_part_temp_action'=>3,'spare_part_temp_user'=>$_SESSION['userid_logined'],'name'=>'Vật tư');
                $data_temp = array_merge($spare_data, $data2);
                $spare_temp->createStock($data_temp);

                $spare->deleteStock($sp->spare_part_id);
            }
            
            

            date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                $filename = "action_logs.txt";
                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|spare_part_code|"."\n"."\r\n";
                
                $fh = fopen($filename, "a") or die("Could not open log file.");
                fwrite($fh, $text) or die("Could not write file!");
                fclose($fh);

        return $spare_code->deleteStock($_POST['data']);
            
        }
    }

    public function getPlace($id){
        return $this->getByID($this->table,$id);
    }

    private function getUrl(){

    }


}
?>