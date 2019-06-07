<?php
Class customerController Extends baseController {
    public function index() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->customer) || json_decode($_SESSION['user_permission_action'])->customer != "customer") {
            $this->view->data['disable_control'] = 1;
        }
        
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Quản lý Khách hàng - Đối tác - Người thụ hưởng';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;
            $order = isset($_POST['order']) ? $_POST['order'] : null;
            $page = isset($_POST['page']) ? $_POST['page'] : null;
            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;
            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;
        }
        else{
            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'customer_name';
            $order = $this->registry->router->order_by ? $this->registry->router->order_by : 'ASC';
            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;
            $keyword = "";
            $limit = 50;
        }
        $customer_model = $this->model->get('customerModel');


        $sonews = $limit;
        $x = ($page-1) * $sonews;
        $pagination_stages = 2;


        $data = array(
            'where' => '1=1',
        );

        if (isset($id) && $id > 0) {
            $data['where'] .= ' AND customer_id = '.$id;
        }
        
        $tongsodong = count($customer_model->getAllCustomer($data));
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
            $data['where'] .= ' AND customer_id = '.$id;
        }
        
        if ($keyword != '') {
            $search = ' AND ( customer_name LIKE "%'.$keyword.'%" 
                OR customer_phone LIKE "%'.$keyword.'%" 
                OR customer_mobile LIKE "%'.$keyword.'%" 
                OR customer_email LIKE "%'.$keyword.'%" 
                OR customer_mst LIKE "%'.$keyword.'%" )';
            $data['where'] .= $search;
        }
        $customers = $customer_model->getAllCustomer($data);
        $this->view->data['customers'] = $customers;

        $this->view->data['lastID'] = isset($customer_model->getLastCustomer()->customer_id)?$customer_model->getLastCustomer()->customer_id:0;
        
        $this->view->show('customer/index');
    }
    

    public function newcus(){
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->customer) || json_decode($_SESSION['user_permission_action'])->customer != "customer") {
            $this->view->data['disable_control'] = 1;
        }
        
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thêm Khách hàng - Đối tác - Người thụ hưởng';
        $this->view->show('customer/newcus');
    }
    public function editcus($id){
        $this->view->setLayout('admin');
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        if (!isset(json_decode($_SESSION['user_permission_action'])->customer) || json_decode($_SESSION['user_permission_action'])->customer != "customer") {
            $this->view->data['disable_control'] = 1;
        }
        
        if (!$id) {
            return $this->view->redirect('customer');
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật khách hàng - Đối tác - Người thụ hưởng';

        $customer_model = $this->model->get('customerModel');
        $customers = $customer_model->getCustomer($id);
        $this->view->data['customers'] = $customers;

        $contact_person_model = $this->model->get('contactpersonModel');
        $contact_persons = $contact_person_model->getAllCustomer(array('where'=>'customer = '.$id));
        $this->view->data['contact_persons'] = $contact_persons;

        $customer_sub_model = $this->model->get('customersubModel');

        $customer_sub = "";
        $sts = explode(',', $customers->customer_sub);
        foreach ($sts as $key) {
            $subs = $customer_sub_model->getCustomer($key);
            if($subs){
                if ($customer_sub == "")
                    $customer_sub .= $subs->customer_sub_name;
                else
                    $customer_sub .= ','.$subs->customer_sub_name;
            }
            
        }
        $this->view->data['customer_sub'] = $customer_sub;
        

        if (!$customers) {
            return $this->view->redirect('customer');
        }

        $this->view->show('customer/editcus');
    }
    public function deletecontact(){
        if (isset($_POST['data'])) {
            $contact_person = $this->model->get('contactpersonModel');

            $contact_person->queryCustomer('DELETE FROM contact_person WHERE contact_person_id='.$_POST['data'].' AND customer='.$_POST['customer']);
        }
    }
    public function getSub(){
        header('Content-type: application/json');
        $q = $_GET["search"];

        $sub_model = $this->model->get('customersubModel');
        $data = array(
            'where' => 'customer_sub_name LIKE "%'.$q.'%"',
        );
        $subs = $sub_model->getAllCustomer($data);
        $arr = array();
        foreach ($subs as $sub) {
            $arr[] = $sub->customer_sub_name;
        }
        
        echo json_encode($arr);
    }

    public function add(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        
        if (isset($_POST['yes'])) {
            $customer = $this->model->get('customerModel');
            $contact_person_model = $this->model->get('contactpersonModel');
            $customer_temp = $this->model->get('customertempModel');

            $contact_person = $_POST['contact_person'];

            $data = array(
                        'customer_code' => trim($_POST['customer_code']),
                        'customer_name' => trim($_POST['customer_name']),
                        'customer_company' => trim($_POST['customer_company']),
                        'customer_mst' => trim($_POST['customer_mst']),
                        'customer_address' => trim($_POST['customer_address']),
                        'customer_phone' => trim($_POST['customer_phone']),
                        'customer_mobile' => trim($_POST['customer_mobile']),
                        'customer_email' => trim($_POST['customer_email']),
                        'customer_bank_account' => trim($_POST['customer_bank_account']),
                        'customer_bank_name' => trim($_POST['customer_bank_name']),
                        'customer_bank_branch' => trim($_POST['customer_bank_branch']),
                        'type_customer' => trim($_POST['type_customer']),
                        );

            $customer_sub_model = $this->model->get('customersubModel');

            $contributor = "";
            if(trim($_POST['customer_sub']) != ""){
                $support = explode(',', trim($_POST['customer_sub']));

                if ($support) {
                    foreach ($support as $key) {
                        $name = $customer_sub_model->getCustomerByWhere(array('customer_sub_name'=>trim($key)));
                        if ($name) {
                            if ($contributor == "")
                                $contributor .= $name->customer_sub_id;
                            else
                                $contributor .= ','.$name->customer_sub_id;
                        }
                        else{
                            $customer_sub_model->createCustomer(array('customer_sub_name'=>trim($key)));
                            if ($contributor == "")
                                $contributor .= $customer_sub_model->getLastCustomer()->customer_sub_id;
                            else
                                $contributor .= ','.$customer_sub_model->getLastCustomer()->customer_sub_id;
                        }
                        
                    }
                }

            }
            $data['customer_sub'] = $contributor;


            if ($_POST['yes'] != "") {

                if ($customer->getAllCustomerByWhere($_POST['yes'].' AND customer_code = "'.$data['customer_code'].'"')) {
                    $mess = array(
                        'msg' => 'Mã khách hàng đã tồn tại',
                        'id' => $_POST['yes'],
                    );

                    echo json_encode($mess);
                }
                else if ($customer->getAllCustomerByWhere($_POST['yes'].' AND customer_name = "'.$data['customer_name'].'"')) {
                    $mess = array(
                        'msg' => 'Tên khách hàng đã tồn tại',
                        'id' => $_POST['yes'],
                    );

                    echo json_encode($mess);
                }
                else if ($customer->getAllCustomerByWhere($_POST['yes'].' AND customer_mst = "'.$data['customer_mst'].'"')) {
                    $mess = array(
                        'msg' => 'Khách hàng đã tồn tại',
                        'id' => $_POST['yes'],
                    );

                    echo json_encode($mess);
                }
                else{
                    $customer->updateCustomer($data,array('customer_id' => $_POST['yes']));

                    $id_customer = $_POST['yes'];

                    /*Log*/
                    /**/

                    $mess = array(
                        'msg' => 'Cập nhật thành công',
                        'id' => $_POST['yes'],
                    );

                    echo json_encode($mess);

                    $data2 = array('customer_id'=>$_POST['yes'],'customer_temp_date'=>strtotime(date('d-m-Y')),'customer_temp_action'=>2,'customer_temp_user'=>$_SESSION['userid_logined'],'name'=>'Khách hàng');
                    $data_temp = array_merge($data, $data2);
                    $customer_temp->createCustomer($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$_POST['yes']."|customer|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }
                    
                
                
            }
            else{

                if ($customer->getCustomerByWhere(array('customer_code'=>$data['customer_code']))) {
                    $mess = array(
                        'msg' => 'Mã khách hàng đã tồn tại',
                        'id' => "",
                    );

                    echo json_encode($mess);
                    
                }
                else if ($customer->getCustomerByWhere(array('customer_name'=>$data['customer_name']))) {
                    $mess = array(
                        'msg' => 'Tên khách hàng đã tồn tại',
                        'id' => "",
                    );

                    echo json_encode($mess);
                    
                }
                else if ($customer->getCustomerByWhere(array('customer_mst'=>$data['customer_mst']))) {
                    $mess = array(
                        'msg' => 'Khách hàng đã tồn tại',
                        'id' => "",
                    );

                    echo json_encode($mess);
                    
                }
                else{
                    $customer->createCustomer($data);

                    $id_customer = $customer->getLastCustomer()->customer_id;

                    /*Log*/
                    /**/

                    $mess = array(
                        'msg' => 'Thêm thành công',
                        'id' => $customer->getLastCustomer()->customer_id,
                    );

                    echo json_encode($mess);

                    $data2 = array('customer_id'=>$customer->getLastCustomer()->customer_id,'customer_temp_date'=>strtotime(date('d-m-Y')),'customer_temp_action'=>1,'customer_temp_user'=>$_SESSION['userid_logined'],'name'=>'Khách hàng');
                    $data_temp = array_merge($data, $data2);
                    $customer_temp->createCustomer($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$customer->getLastCustomer()->customer_id."|customer|".implode("-",$data)."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);
                }
                    
                
                
            }

            if (isset($id_customer)) {
                foreach ($contact_person as $v) {
                    $data_contact_person = array(
                        'contact_person_name' => trim($v['contact_person_name']),
                        'contact_person_phone' => trim($v['contact_person_phone']),
                        'contact_person_mobile' => trim($v['contact_person_mobile']),
                        'contact_person_email' => trim($v['contact_person_email']),
                        'contact_person_address' => trim($v['contact_person_address']),
                        'contact_person_position' => trim($v['contact_person_position']),
                        'contact_person_department' => trim($v['contact_person_department']),
                        'customer' => $id_customer,
                    );

                    

                    if ($contact_person_model->getCustomerByWhere(array('contact_person_name'=>$data_contact_person['contact_person_name'],'customer'=>$id_customer))) {
                        $id_contact_person = $contact_person_model->getCustomerByWhere(array('contact_person_name'=>$data_contact_person['contact_person_name'],'customer'=>$id_customer))->contact_person_id;
                        $contact_person_model->updateCustomer($data_contact_person,array('contact_person_id'=>$id_contact_person));
                    }
                    else if (!$contact_person_model->getCustomerByWhere(array('contact_person_name'=>$data_contact_person['contact_person_name'],'customer'=>$id_customer))) {
                        if ($data_contact_person['contact_person_name'] != "") {
                            $contact_person_model->createCustomer($data_contact_person);
                        }
                    }
                }
            }
            
                    
        }
    }
    public function delete(){
        if (!isset($_SESSION['userid_logined'])) {
            return $this->view->redirect('user/login');
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $customer = $this->model->get('customerModel');
            $customer_temp = $this->model->get('customertempModel');
            if (isset($_POST['xoa'])) {
                $data = explode(',', $_POST['xoa']);
                foreach ($data as $data) {
                    $customer_data = (array)$customer->getCustomer($data); 
                    $customer->deleteCustomer($data);

                    $data2 = array('customer_id'=>$data,'customer_temp_date'=>strtotime(date('d-m-Y')),'customer_temp_action'=>3,'customer_temp_user'=>$_SESSION['userid_logined'],'name'=>'Khách hàng');
                    $data_temp = array_merge($customer_data, $data2);
                    $customer_temp->createCustomer($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|customer|"."\n"."\r\n";
                        
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
                    $customer_data = (array)$customer->getCustomer($_POST['data']); 
                    $data2 = array('customer_id'=>$_POST['data'],'customer_temp_date'=>strtotime(date('d-m-Y')),'customer_temp_action'=>3,'customer_temp_user'=>$_SESSION['userid_logined'],'name'=>'Khách hàng');
                    $data_temp = array_merge($customer_data, $data2);
                    $customer_temp->createCustomer($data_temp);

                    date_default_timezone_set("Asia/Ho_Chi_Minh"); 
                        $filename = "action_logs.txt";
                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|customer|"."\n"."\r\n";
                        
                        $fh = fopen($filename, "a") or die("Could not open log file.");
                        fwrite($fh, $text) or die("Could not write file!");
                        fclose($fh);

                return $customer->deleteCustomer($_POST['data']);
            }
            
        }
    }


}
?>