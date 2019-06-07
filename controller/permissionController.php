<?php

Class permissionController Extends baseController {

    public function index() {

        $this->view->setLayout('admin');

        if (!isset($_SESSION['userid_logined'])) {

            return $this->view->redirect('user/login');

        }

        if (!isset(json_decode($_SESSION['user_permission_action'])->permission) || json_decode($_SESSION['user_permission_action'])->permission != "permission") {

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

                echo "Cập nhật thành công";
            }
            else {
                $role_model = $this->model->get('roleModel');

                $data = array(
                    'role_permission' => $permission,
                    'role_permission_action' => $permission_action,
                );

                $role_model->updateRole($data,array('role_id'=>$_POST['role']));

                echo "Cập nhật thành công";
            }
        }
    }



}

?>