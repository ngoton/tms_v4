<?php
Class supportController Extends baseController {
    public function index() {
    	$this->view->setLayout('admin');

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Hỗ trợ & Hướng dẫn sử dụng phần mềm quản lý vận tải';

        $this->view->show('support/index');
    }

}
?>