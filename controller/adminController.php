<?php
Class adminController Extends baseController {
    public function index() {
    	$this->view->setLayout('admin');
    	if (!isset($_SESSION['role_logined'])) {
            return $this->view->redirect('user/login');
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Dashboard';

        

        $this->view->show('admin/index');
    }

   public function checklockuser(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['data'] == 0) {
                echo 0;
            }
            else{
                $user_model = $this->model->get('userModel');
            
                $user = $user_model->getUserByWhere(array('user_id' => $_POST['data']));
                echo $user->user_lock;
            }
            
        }
    }
    

}
?>