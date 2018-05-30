<?php

Class userController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }
        if (!in_array($this->registry->router->controller, json_decode($_SESSION['user_permission'])) && $_SESSION['user_permission'] != '["all"]') {

            return $this->view->redirect('admin');

        }


        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Quản lý user';



        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $order_by = isset($_POST['order_by']) ? $_POST['order_by'] : null;

            $order = isset($_POST['order']) ? $_POST['order'] : null;

            $page = isset($_POST['page']) ? $_POST['page'] : null;

            $keyword = isset($_POST['keyword']) ? $_POST['keyword'] : null;

            $limit = isset($_POST['limit']) ? $_POST['limit'] : 18446744073709;

        }

        else{

            $order_by = $this->registry->router->order_by ? $this->registry->router->order_by : 'user_id';

            $order = $this->registry->router->order ? $this->registry->router->order : 'ASC';

            $page = $this->registry->router->page ? (int) $this->registry->router->page : 1;

            $keyword = "";

            $limit = 100;

        }



        



        $user_model = $this->model->get('userModel');

        $sonews = $limit;

        $x = ($page-1) * $sonews;

        $pagination_stages = 2;

        

        $join = array('table'=>'role','where'=>'user.role = role.role_id');



        

        $tongsodong = count($user_model->getAllUser(null,$join));

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

            $search = '( username LIKE "%'.$keyword.'%" OR role_name LIKE "%'.$keyword.'%" )';

            $data['where'] = $search;

        }



        $this->view->data['users'] = $user_model->getAllUser($data,$join);



        return $this->view->show('user/index');

    }



    public function login() {
        $this->view->disableLayout();
        $this->view->data['title'] = 'Đăng nhập';
        /*Kiểm tra CSDL*/
        if (isset($_POST['submit'])) {
            if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
                $captcha=$_POST['g-recaptcha-response'];
                $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".CAPTCHA_SECRET."&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
                $obj = json_decode($response);
                if ($obj->success == true) {
                    if ($_POST['username'] != '' && $_POST['password'] != '' ) {
                        $user = $this->model->get('userModel');
                        
                        $row = $user->getUserByUsername(addslashes($_POST['username']));
                        
                        if ($row) {
                            if ($row->password == md5($_POST['password']) && $row->user_lock != 1) {
                                $_SESSION['user_logined'] = $row->username;
                                $_SESSION['userid_logined'] = $row->user_id;
                                $_SESSION['role_logined'] = $row->role;
                                $_SESSION['user_permission'] = $row->permission;
                                $_SESSION['user_permission_action'] = $row->permission_action;

                                $user->updateUser(array('lasted_online'=>time()),array('user_id'=>$row->user_id));

                                echo "Đăng nhập thành công";

                                if (isset($_POST['ghinho']) && $_POST['ghinho'] == 1) { 
                                    setcookie("remember", 1,time()+30*60*24*100,"/");
                                    setcookie("uu", 'yf'.base64_encode($row->username),time()+30*60*24*100,"/");
                                    setcookie("ui", 'kq'.base64_encode($row->user_id),time()+30*60*24*100,"/");
                                    setcookie("ro", 'xg'.base64_encode($row->role),time()+30*60*24*100,"/");
                                    setcookie("up", 'oi'.md5($_POST['password']),time()+30*60*24*100,"/");
                                 }

                                $ipaddress = '';
                                if (getenv('HTTP_CLIENT_IP'))
                                    $ipaddress = getenv('HTTP_CLIENT_IP');
                                else if(getenv('HTTP_X_FORWARDED_FOR'))
                                    $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
                                else if(getenv('HTTP_X_FORWARDED'))
                                    $ipaddress = getenv('HTTP_X_FORWARDED');
                                else if(getenv('HTTP_FORWARDED_FOR'))
                                    $ipaddress = getenv('HTTP_FORWARDED_FOR');
                                else if(getenv('HTTP_FORWARDED'))
                                   $ipaddress = getenv('HTTP_FORWARDED');
                                else if(getenv('REMOTE_ADDR'))
                                    $ipaddress = getenv('REMOTE_ADDR');
                                else
                                    $ipaddress = 'UNKNOWN';

                                
                                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."login"."|".$ipaddress."\n"."\r\n";
                                $this->lib->ghi_file("user_logs.txt",$text);
                                

                                $this->view->redirect('admin');
                            }
                            else{
                                $this->view->data['error'] = "Sai mật khẩu";
                            }
                        }
                        else{
                            $this->view->data['error'] =  "Không tồn tại username";
                        }
                    }
                    else{
                        $this->view->data['error'] =  "Vui lòng nhập vào username / password";
                    }
                }
                else{
                    $this->view->data['error'] =  "Có lỗi xảy ra vui lòng thử lại";
                }
            }
            else{
                $this->view->data['error'] =  "Vui lòng xác nhận captcha";
            }

            $this->view->data['user'] =  $_POST['username'];
        }
        return $this->view->show('user/login');
    }
    public function captcha(){
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=".CAPTCHA_SECRET."&response=".$_POST['g-recaptcha-response']."&remoteip=".$_SERVER['REMOTE_ADDR']);
    }



    public function logout(){

        $user = $this->model->get('userModel');
        $user->updateUser(array('lasted_online'=>time()),array('user_id'=>$_SESSION['userid_logined']));

        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."logout"."\n"."\r\n";

        $this->lib->ghi_file("user_logs.txt",$text);

        session_destroy();

        setcookie("remember", "",time() - 3600,"/");

        setcookie("uu", "",time() - 3600,"/");

        setcookie("ui", "",time() - 3600,"/");

        setcookie("ro", "",time() - 3600,"/");

        setcookie("up", "",time() - 3600,"/");

        return $this->view->redirect('');

    }

    public function adduser(){
        $role = $this->model->get('roleModel');

        /*Thêm vào CSDL*/

        if (isset($_POST['username'])) {

            if ($_POST['username'] != '' && $_POST['password'] != '' && $_POST['role'] != '') {

                $user = $this->model->get('userModel');



                $r = $user->getUserByUsername($_POST['username']);

                

                if (!$r) {

                    $r = $user->getUserByWhere(array('user_email'=>trim($_POST['user_email'])));

                    if (!$r) {
                        $role_permission = $role->getRole(trim($_POST['role']));

                        $time = time();

                        $data = array(

                            'username' => trim($_POST['username']),

                            'password' => trim(md5($_POST['password'])),

                            'user_email' => trim($_POST['user_email']),

                            'create_time' => $time,

                            'role' => trim($_POST['role']),

                            'permission' => $role_permission->role_permission,

                            'permission_action' => $role_permission->role_permission_action,

                            );

                        $user->createUser($data);



                            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."add"."|".$user->getLastUser()->user_id."|user|".$data['username']."\n"."\r\n";

                            $this->lib->ghi_file("action_logs.txt",$text);


                        $user_log_model = $this->model->get('userlogModel');
                        $data_log = array(
                            'user_log' => $_SESSION['userid_logined'],
                            'user_log_date' => time(),
                            'user_log_table' => 'user',
                            'user_log_table_name' => 'Tài khoản',
                            'user_log_action' => 'Thêm mới',
                            'user_log_data' => json_encode($data),
                        );
                        $user_log_model->createUser($data_log);


                        echo "Đăng kí thành công";
                    }
                    else{
                        echo "Email này đã được sử dụng";
                    }

                }

                else{

                     echo "Tên đăng nhập đã tồn tại";

                }

            }

        }
    }

    public function add(){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->user) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Đăng ký tài khoản';

        /*Lấy danh sách quyền*/

        $role = $this->model->get('roleModel');

        $this->view->data['role'] = $role->getAllRole();

        

        return $this->view->show('user/add');

    }

    public function edituser(){
        $user = $this->model->get('userModel');
        $role = $this->model->get('roleModel');
        if (isset($_POST['user_id'])) {
            $id = $_POST['user_id'];

            $qr = $user->query('SELECT user_id FROM user WHERE user_id != '.$id.' AND user_email = "'.trim($_POST['user_email']).'"');
            if (!$qr) {
                if ($_POST['role'] != '') {

                    $role_permission = $role->getRole(trim($_POST['role']));

                    if ($_POST['password'] != '') {


                        $data = array(

                            'password' => trim(md5($_POST['password'])),

                            'role' => trim($_POST['role']),

                            'user_email' => trim($_POST['user_email']),

                            'user_lock' => trim($_POST['userlock']),

                            'permission' => $role_permission->role_permission,

                            'permission_action' => $role_permission->role_permission_action,

                            );

                    }

                    else{


                        $data = array(

                            'role' => trim($_POST['role']),

                            'user_email' => trim($_POST['user_email']),

                            'user_lock' => trim($_POST['userlock']),

                            'permission' => $role_permission->role_permission,

                            'permission_action' => $role_permission->role_permission_action,

                            );

                    }

                        $user->updateUser($data,array('user_id'=>$id));


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."edit"."|".$id."|user|".implode("-",$data)."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);

                        $user_log_model = $this->model->get('userlogModel');
                        $data_log = array(
                            'user_log' => $_SESSION['userid_logined'],
                            'user_log_date' => time(),
                            'user_log_table' => 'user',
                            'user_log_table_name' => 'Tài khoản',
                            'user_log_action' => 'Cập nhật thông tin',
                            'user_log_data' => json_encode($data),
                        );
                        $user_log_model->createUser($data_log);

                        echo "Cập nhật thành công";

                }
            }
            else{
                echo "Email này đã được sử dụng";
            }

            

        }
    }

    public function edit($id){

        $this->view->disableLayout();

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->user) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        if (!$id) {

            $this->view->redirect('user');

        }

        $this->view->data['title'] = 'Cập nhật tài khoản';

        $user = $this->model->get('userModel');

        $user_data = $user->getUser($id);

        

        if (!$user_data) {

            $this->view->redirect('user');

        }

        else {

            
            

            /*Lấy danh sách quyền*/

            $role = $this->model->get('roleModel');

            $role_data = $role->getRole($user_data->role);

            $this->view->data['user_role'] = $role_data;

            $this->view->data['user_id'] = $user_data->user_id;
            $this->view->data['user_email'] = $user_data->user_email;

            $this->view->data['role'] = $role->getAllRoleByWhere($role_data->role_id);

            /*Thêm vào CSDL*/

            

        }

        

        return $this->view->show('user/edit');

    }



    public function delete(){

        if (!isset($_SESSION['userid_logined'])) {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ((!isset(json_decode($_SESSION['user_permission_action'])->user) || json_decode($_SESSION['user_permission_action'])->user != "user") && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $user = $this->model->get('userModel');
            $user_log_model = $this->model->get('userlogModel');

            if (isset($_POST['xoa'])) {

                $datas = explode(',', $_POST['xoa']);

                foreach ($datas as $data) {

                    $user->deleteUser($data);


                        $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$data."|user|"."\n"."\r\n";

                        $this->lib->ghi_file("action_logs.txt",$text);



                }


                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'user',
                    'user_log_table_name' => 'Tài khoản',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($datas),
                );
                $user_log_model->createUser($data_log);


                echo "Xóa thành công";
                return true;

            }

            else{

                $user->deleteUser($_POST['data']);

                $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."delete"."|".$_POST['data']."|user|"."\n"."\r\n";

                $this->lib->ghi_file("action_logs.txt",$text);

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'user',
                    'user_log_table_name' => 'Tài khoản',
                    'user_log_action' => 'Xóa',
                    'user_log_data' => json_encode($_POST['data']),
                );
                $user_log_model->createUser($data_log);

                echo "Xóa thành công";
                return true;

            }

            

        }

    }

    public function fogot(){

        $this->view->setLayout('admin');

        return $this->view->show('user/fogot');

    }



    private function getUrl(){



    }

    public function changepass(){
        $user = $this->model->get('userModel');

        if (isset($_POST['password'])) {

             $id = $_POST['user_id'];

            if ($_POST['oldpassword'] != '' && $_POST['password'] != '') {

                $check = $user->getUserByWhere(array('password'=>md5($_POST['oldpassword'])));

                if ($check) {

                    $data = array(

                    'password' => trim(md5($_POST['password'])),

                    );

                    $user->updateUser($data,array('user_id'=>$id));

                    echo "Đổi mật khẩu thành công";

                    $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."changepassword"."|".$id."|user|"."\n"."\r\n";

                    $this->lib->ghi_file("action_logs.txt",$text);

                    $user_log_model = $this->model->get('userlogModel');
                    $data_log = array(
                        'user_log' => $_SESSION['userid_logined'],
                        'user_log_date' => time(),
                        'user_log_table' => 'user',
                        'user_log_table_name' => 'Tài khoản',
                        'user_log_action' => 'Đổi mật khẩu',
                        'user_log_data' => json_encode($data),
                    );
                    $user_log_model->createUser($data_log);

                }

                else{

                    echo "Mật khẩu cũ không đúng";

                }

                

            }

        
        

        }
    }

    public function info($id){

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!$id) {

            $this->view->redirect('');

        }

        if ($_SESSION['userid_logined'] != $id && !isset(json_decode($_SESSION['user_permission_action'])->user) && $_SESSION['user_permission_action'] != '["all"]') {

            return $this->view->redirect('user/login');

        }

        
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Thông tin tài khoản';

        $user = $this->model->get('userModel');

        $user_data = $user->getUser($id);

        $this->view->data['user_id'] = $id;
        $this->view->data['user_data'] = $user_data;

        $user_log_model = $this->model->get('userlogModel');
        $d_log = array(
            'where'=>'user_log='.$id,
            'order_by'=>'user_log_date',
            'order'=>'DESC',
            'limit'=>10,
        );
        $d_join = array('table'=>'user','where'=>'user_log=user_id');
        $user_logs = $user_log_model->getAllUser($d_log,$d_join);
        $this->view->data['user_logs'] = $user_logs;

        $staff_model = $this->model->get('staffModel');
        $staffs = $staff_model->getStaffByWhere(array('staff_account'=>$id));
        $this->view->data['staffs'] = $staffs;

        if ($staffs) {
            $position_model = $this->model->get('positionModel');
            $department_model = $this->model->get('departmentModel');
            $positions = $position_model->getPosition($staffs->staff_position);
            $departments = $department_model->getDepartment($staffs->staff_department);

            $this->view->data['positions'] = $positions;
            $this->view->data['departments'] = $departments;
        }
        

        if (!$user_data) {

            $this->view->redirect('user');

        }
        

        return $this->view->show('user/info');

    }


    public function importuser(){
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

        if (!isset(json_decode($_SESSION['user_permission_action'])->user) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }

        $this->view->data['title'] = 'Nhập dữ liệu';

        /*Lấy danh sách quyền*/

        $role = $this->model->get('roleModel');

        $this->view->data['role'] = $role->getAllRole();

        

        return $this->view->show('user/import');

    }


}

?>