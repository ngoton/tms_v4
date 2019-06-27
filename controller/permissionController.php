<?php

Class permissionController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        $this->view->data['lib'] = $this->lib;

        $this->view->data['title'] = 'Phân quyền hệ thống';


        $role = $this->model->get('roleModel');

        $this->view->data['roles'] = $role->getAllRole(array('where'=>'role_id > 1'));

        return $this->view->show('permission/index');

    }

    public function getuser(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $user_model = $this->model->get('userModel');

            $data = array(
                'where' => 'role = '.$_POST['data'],
            );

            $users = $user_model->getAllUser($data);

            $str = '<option value="">Chọn</option>';
            foreach ($users as $user) {
                $str .= '<option value="'.$user->user_id.'">'.$user->username.'</option>';
            }

            echo $str;
        }
    }

    public function getpermission(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['data'] > 0) {
                $user_model = $this->model->get('userModel');
                $users = $user_model->getUser($_POST['data']);

                echo $users->permission;
            }
        }
    }
     public function getpermission2(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['data'] > 0) {
                $user_model = $this->model->get('userModel');
                $users = $user_model->getUser($_POST['data']);

                echo $users->permission_action;
            }
        }
    }
    public function getrolepermission(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['data'] > 0) {
                $role_model = $this->model->get('roleModel');
                $roles = $role_model->getRole($_POST['data']);

                echo $roles->role_permission;
            }
        }
    }
    public function getrolepermission2(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['data'] > 0) {
                $role_model = $this->model->get('roleModel');
                $roles = $role_model->getRole($_POST['data']);

                echo $roles->role_permission_action;
            }
        }
    }

    public function setpermission(){
        if (!isset(json_decode($_SESSION['user_permission_action'])->permission) && $_SESSION['user_permission_action'] != '["all"]') {

            echo "Bạn không có quyền thực hiện thao tác này";
            return false;

        }
        $user_log_model = $this->model->get('userlogModel');

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $permission = isset($_POST['data'])?json_encode($_POST['data']):null;
            $permission_action = isset($_POST['act'])?json_encode($_POST['act']):null;

            if ($_POST['user'] > 0) {
                $user_model = $this->model->get('userModel');

                $data = array(
                    'permission' => $permission,
                    'permission_action' => $permission_action,
                );

                $user_model->updateUser($data,array('user_id'=>$_POST['user']));

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'user',
                    'user_log_table_name' => 'Người dùng',
                    'user_log_action' => 'Phân quyền',
                    'user_log_data' => json_encode($data),
                );
                $user_log_model->createUser($data_log);

                echo "Cập nhật thành công";
            }
            else {
                $role_model = $this->model->get('roleModel');

                $data = array(
                    'role_permission' => $permission,
                    'role_permission_action' => $permission_action,
                );

                $role_model->updateRole($data,array('role_id'=>$_POST['role']));

                $data_log = array(
                    'user_log' => $_SESSION['userid_logined'],
                    'user_log_date' => time(),
                    'user_log_table' => 'role',
                    'user_log_table_name' => 'Nhóm người dùng',
                    'user_log_action' => 'Phân quyền',
                    'user_log_data' => json_encode($data),
                );
                $user_log_model->createUser($data_log);

                echo "Cập nhật thành công";
            }
        }
    }



}

?>