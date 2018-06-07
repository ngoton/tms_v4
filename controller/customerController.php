<?php

Class customerController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý khách hàng - đối tác';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'customer_code';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }




        $customer_model = $this->model->get('customerModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        $join = array('table'=>'province','where'=>'customer_province=province_id');

        $data = array(
            'where'=>'1=1',
        );

        if (isset($_POST['filter'])) {
            if (isset($_POST['customer_type'])) {
                $data['where'] .= ' AND customer_type IN ('.implode(',',$_POST['customer_type']).')';
            }
            if (isset($_POST['customer_province'])) {
                $data['where'] .= ' AND customer_province IN ('.implode(',',$_POST['customer_province']).')';
            }
            if (isset($_POST['customer_sub'])) {
                $str = implode(',', $_POST['customer_sub']);
                $data['where'] .= ' AND (customer_sub LIKE "'.$str.'" OR customer_sub LIKE "'.$str.',%" OR customer_sub LIKE "%,'.$str.',%" OR customer_sub LIKE "%,'.$str.'")';
            }
        }

        $tongsodong = count($customer_model->getAllCustomer($data,$join));

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
            'where'=>'1=1',
            
            'order_by'=>$order_by,

            'order'=>$order,

            'limit'=>$x.','.$sonews,

            );

        if (isset($_POST['filter'])) {
            if (isset($_POST['customer_type'])) {
                $data['where'] .= ' AND customer_type IN ('.implode(',',$_POST['customer_type']).')';
            }
            if (isset($_POST['customer_province'])) {
                $data['where'] .= ' AND customer_province IN ('.implode(',',$_POST['customer_province']).')';
            }
            if (isset($_POST['customer_sub'])) {
                $str = implode(',', $_POST['customer_sub']);
                $data['where'] .= ' AND (customer_sub LIKE "'.$str.'" OR customer_sub LIKE "'.$str.',%" OR customer_sub LIKE "%,'.$str.',%" OR customer_sub LIKE "%,'.$str.'")';
            }
            $this->view->data['filter'] = 1;
        }

        if ($keyword != '') {

            $search = '( customer_code LIKE "%'.$keyword.'%" 
                OR customer_name LIKE "%'.$keyword.'%" 
                OR customer_company LIKE "%'.$keyword.'%" 
                OR customer_mst LIKE "%'.$keyword.'%" 
                OR customer_email LIKE "%'.$keyword.'%" 
                OR REPLACE(customer_phone, " ", "") LIKE "%'.str_replace(' ', '', $keyword).'%" 
                OR REPLACE(customer_mobile, " ", "") LIKE "%'.str_replace(' ', '', $keyword).'%"
            )';

            $data['where'] = $search;

        }



        $this->view->data['customers'] = $customer_model->getAllCustomer($data,$join);



        return $this->view->show('customer/index');

    }


    public function addcustomer(){
        $customer_model = $this->model->get('customerModel');

        if (isset($_POST['customer_code'])) {
            if($customer_model->getCustomerByWhere(array('customer_code'=>trim($_POST['customer_code'])))){
                echo 'Mã khách hàng - đối tác đã tồn tại';
                return false;
            }
            if($customer_model->getCustomerByWhere(array('customer_name'=>trim($_POST['customer_name'])))){
                echo 'Tên khách hàng - đối tác đã tồn tại';
                return false;
            }
            if($customer_model->getCustomerByWhere(array('customer_company'=>trim($_POST['customer_company'])))){
                echo 'Tên công ty khách hàng - đối tác đã tồn tại';
                return false;
            }
            if($customer_model->getCustomerByWhere(array('customer_mst'=>trim($_POST['customer_mst'])))){
                echo 'Mã số thuế khách hàng - đối tác đã tồn tại';
                return false;
            }

            $data = array(
                'customer_code' => trim($_POST['customer_code']),
                'customer_name' => trim($_POST['customer_name']),
                'customer_company' => trim($_POST['customer_company']),
                'customer_mst' => trim($_POST['customer_mst']),
                'customer_address' => trim($_POST['customer_address']),
                'customer_province' => trim($_POST['customer_province']),
                'customer_phone' => trim(str_replace('_', '', $_POST['customer_phone'])),
                'customer_mobile' => trim(str_replace('_', '', $_POST['customer_mobile'])),
                'customer_email' => trim($_POST['customer_email']),
                'customer_bank_account' => trim($_POST['customer_bank_account']),
                'customer_bank_name' => trim($_POST['customer_bank_name']),
                'customer_bank_branch' => trim($_POST['customer_bank_branch']),
                'customer_type' => trim($_POST['customer_type']),
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

            $customer_model->createCustomer($data);

            $id_customer = $customer_model->getLastCustomer()->customer_id;

            $contact_person_model = $this->model->get('contactpersonModel');

            $contact_person = json_decode($_POST['contact_person']);
            if (isset($id_customer)) {
                foreach ($contact_person as $v) {
                    $data_contact_person = array(
                        'contact_person_name' => trim($v->contact_person_name),
                        'contact_person_phone' => trim(str_replace('_', '', $v->contact_person_phone)),
                        'contact_person_mobile' => trim(str_replace('_', '', $v->contact_person_mobile)),
                        'contact_person_email' => trim($v->contact_person_email),
                        'contact_person_birthday' => strtotime(str_replace('/', '-', $v->contact_person_birthday)),
                        'contact_person_address' => trim($v->contact_person_address),
                        'contact_person_position' => trim($v->contact_person_position),
                        'contact_person_department' => trim($v->contact_person_department),
                        'contact_person_customer' => $id_customer,
                    );

                    if ($v->id_contact_person>0) {
                        $contact_person_model->updateCustomer($data_contact_person,array('contact_person_id'=>$v->id_contact_person));
                    }
                    else{
                        if ($data_contact_person['contact_person_name']!="") {
                            $contact_person_model->createCustomer($data_contact_person);
                        }
                        
                    }

                }
            }

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$id_customer."|customer|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'customer',
                'user_log_table_name' => 'Khách hàng - đối tác',
                'user_log_action' => 'Thêm mới',
                'user_log_data' => json_encode($data),
            );
            $user_log_model->createUser($data_log);


            echo "Thêm thành công";
        }

    }

    public function add(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->customer) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Thêm mới khách hàng - đối tác';

        $customer_model = $this->model->get('customerModel');
        $lastID = isset($customer_model->getLastCustomer()->customer_code)?$customer_model->getLastCustomer()->customer_code:'KH00';
        $lastID++;
        $this->view->data['lastID'] = $lastID;

        $province_model = $this->model->get('provinceModel');

        $provinces = $province_model->getAllProvince();

        $this->view->data['provinces'] = $provinces;

        return $this->view->show('customer/add');
    }

    public function editcustomer(){
        $customer_model = $this->model->get('customerModel');

        if (isset($_POST['customer_id'])) {
            $id = $_POST['customer_id'];
            if($customer_model->getAllCustomerByWhere($id.' AND customer_code = "'.trim($_POST['customer_code']).'"')){
                echo 'Mã khách hàng - đối tác đã tồn tại';
                return false;
            }
            if($customer_model->getAllCustomerByWhere($id.' AND customer_name = "'.trim($_POST['customer_name']).'"')){
                echo 'Tên khách hàng - đối tác đã tồn tại';
                return false;
            }
            if($customer_model->getAllCustomerByWhere($id.' AND customer_company = "'.trim($_POST['customer_company']).'"')){
                echo 'Tên công ty khách hàng - đối tác đã tồn tại';
                return false;
            }
            if($customer_model->getAllCustomerByWhere($id.' AND customer_mst = "'.trim($_POST['customer_mst']).'"')){
                echo 'Mã số thuế khách hàng - đối tác đã tồn tại';
                return false;
            }

            $data = array(
                'customer_code' => trim($_POST['customer_code']),
                'customer_name' => trim($_POST['customer_name']),
                'customer_company' => trim($_POST['customer_company']),
                'customer_mst' => trim($_POST['customer_mst']),
                'customer_address' => trim($_POST['customer_address']),
                'customer_province' => trim($_POST['customer_province']),
                'customer_phone' => trim(str_replace('_', '', $_POST['customer_phone'])),
                'customer_mobile' => trim(str_replace('_', '', $_POST['customer_mobile'])),
                'customer_email' => trim($_POST['customer_email']),
                'customer_bank_account' => trim($_POST['customer_bank_account']),
                'customer_bank_name' => trim($_POST['customer_bank_name']),
                'customer_bank_branch' => trim($_POST['customer_bank_branch']),
                'customer_type' => trim($_POST['customer_type']),
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
            
            $customer_model->updateCustomer($data,array('customer_id'=>$id));

            $id_customer = $id;

            $contact_person_model = $this->model->get('contactpersonModel');

            $contact_person = json_decode($_POST['contact_person']);
            if (isset($id_customer)) {
                foreach ($contact_person as $v) {
                    $data_contact_person = array(
                        'contact_person_name' => trim($v->contact_person_name),
                        'contact_person_phone' => trim(str_replace('_', '', $v->contact_person_phone)),
                        'contact_person_mobile' => trim(str_replace('_', '', $v->contact_person_mobile)),
                        'contact_person_email' => trim($v->contact_person_email),
                        'contact_person_birthday' => strtotime(str_replace('/', '-', $v->contact_person_birthday)),
                        'contact_person_address' => trim($v->contact_person_address),
                        'contact_person_position' => trim($v->contact_person_position),
                        'contact_person_department' => trim($v->contact_person_department),
                        'contact_person_customer' => $id_customer,
                    );

                    if ($v->id_contact_person>0) {
                        $contact_person_model->updateCustomer($data_contact_person,array('contact_person_id'=>$v->id_contact_person));
                    }
                    else{
                        if ($data_contact_person['contact_person_name']!="") {
                            $contact_person_model->createCustomer($data_contact_person);
                        }
                        
                    }

                }
            }

            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|customer|".implode("-",$data)."\n"."\r\n";
            $this->lib->ghi_file("action_logs.txt",$text);


            $user_log_model = $this->model->get('userlogModel');
            $data_log = array(
                'user_log' => $_SESSION['userid_logined'],
                'user_log_date' => time(),
                'user_log_table' => 'customer',
                'user_log_table_name' => 'Khách hàng - đối tác',
                'user_log_action' => 'Cập nhật',
                'user_log_data' => json_encode($data),
            );
            $user_log_model->createUser($data_log);


            echo "Cập nhật thành công";
        }
    }

    public function edit($id){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->customer) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('customer');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật khách hàng - đối tác';

        $customer_model = $this->model->get('customerModel');

        $customer_data = $customer_model->getCustomer($id);

        $this->view->data['customer_data'] = $customer_data;

        if (!$customer_data) {

            $this->view->redirect('customer');

        }

        $province_model = $this->model->get('provinceModel');

        $provinces = $province_model->getAllProvince();

        $this->view->data['provinces'] = $provinces;

        $contact_person_model = $this->model->get('contactpersonModel');
        $contact_persons = $contact_person_model->getAllCustomer(array('where'=>'contact_person_customer = '.$id));
        $this->view->data['contact_persons'] = $contact_persons;

        $customer_sub_model = $this->model->get('customersubModel');

        $customer_sub = "";
        $sts = explode(',', $customer_data->customer_sub);
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

        return $this->view->show('customer/edit');

    }
    public function view($id){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('customer');

        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin khách hàng - đối tác';

        $customer_model = $this->model->get('customerModel');

        $customer_data = $customer_model->getCustomer($id);

        $this->view->data['customer_data'] = $customer_data;

        if (!$customer_data) {

            $this->view->redirect('customer');

        }

        $province_model = $this->model->get('provinceModel');

        $provinces = $province_model->getAllProvince();

        $this->view->data['provinces'] = $provinces;

        $contact_person_model = $this->model->get('contactpersonModel');
        $contact_persons = $contact_person_model->getAllCustomer(array('where'=>'contact_person_customer = '.$id));
        $this->view->data['contact_persons'] = $contact_persons;

        $customer_sub_model = $this->model->get('customersubModel');

        $customer_sub = "";
        $sts = explode(',', $customer_data->customer_sub);
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

        return $this->view->show('customer/view');

    }
    public function filter(){
        $this->view->disableLayout();

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Lọc dữ liệu';

        $province_model = $this->model->get('provinceModel');

        $provinces = $province_model->getAllProvince();

        $this->view->data['provinces'] = $provinces;

        $customer_sub_model = $this->model->get('customersubModel');

        $customer_subs = $customer_sub_model->getAllCustomer(array('order_by'=>'customer_sub_name','order'=>'ASC'));

        $this->view->data['customer_subs'] = $customer_subs;

        $this->view->data['page'] = $_GET['page'];
        $this->view->data['order_by'] = $_GET['order_by'];
        $this->view->data['order'] = $_GET['order'];
        $this->view->data['limit'] = $_GET['limit'];
        $this->view->data['keyword'] = $_GET['keyword'];

        return $this->view->show('customer/filter');
    }

    public function getcustomer(){
        $customer_model = $this->model->get('customerModel');

        $customers = $customer_model->getAllCustomer(array('order_by'=>'customer_code','order'=>'ASC'));
        $result = array();
        $i = 0;
        foreach ($customers as $customer) {
            $result[$i]['id'] = $customer->customer_id;
            $result[$i]['text'] = $customer->customer_name;
            $i++;
        }
        echo json_encode($result);
    }

    public function getlastcustomercode(){
        $customer_model = $this->model->get('customerModel');

        $type = $_GET['customer_type'];
        if ($type==1) {
            $lastID = "KH00";
        }
        else if ($type==2) {
            $lastID = "NCC00";
        }
        else if ($type==3) {
            $lastID = "CN00";
        }

        $customers = $customer_model->getAllCustomer(array('where'=>'customer_type='.$type,'order_by'=>'customer_code','order'=>'DESC','limit'=>1));
        foreach ($customers as $customer) {
            $lastID = $customer->customer_code;
        }
        $lastID++;
        
        echo $lastID;
    }

    public function getcustomersub(){
        $customer_sub_model = $this->model->get('customersubModel');

        $type = $_GET['q'];

        $customers = $customer_sub_model->getAllCustomer(array('where'=>'customer_sub_name LIKE "%'.$type.'%"'));
        $result = array();
        foreach ($customers as $customer) {
            $result[] = $customer->customer_sub_name;
        }
        
        echo json_encode($result);
    }

    public function deletecontact(){
        if (isset($_POST['data'])) {
            $contact_person = $this->model->get('contactpersonModel');

            $contact_person->queryCustomer('DELETE FROM contact_person WHERE contact_person_id='.$_POST['data'].' AND contact_person_customer='.$_POST['customer']);
        }
    }

    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->customer) || json_decode($_SESSION['user_permission_action'])->customer != "customer") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $customer_model = $this->model->get('customerModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $customer_model->deleteCustomer($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|customer|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'customer',
                    'user_log_table_name' => 'Khách hàng - đối tác',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $customer_model->deleteCustomer($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|customer|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'customer',
                    'user_log_table_name' => 'Khách hàng - đối tác',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function importcustomer(){
        if (isset($_FILES['import']['name'])) {
            $total = count($_FILES['import']['name']);
            for( $i=0 ; $i < $total ; $i++ ) {
              $tmpFilePath = $_FILES['import']['name'][$i];
              echo $tmpFilePath;
            }
        }
    }
    public function import(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->customer) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

       
        return $this->view->show('customer/import');

    }


}

?>