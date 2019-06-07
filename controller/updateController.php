<?php
Class updateController Extends baseController {
    public function index() {
    	$this->view->setLayout('admin');
    	if (!isset($_SESSION['role_logined'])) {
            return $this->view->redirect('user/login');
        }
        if ($_SESSION['role_logined'] != 1) {
            return $this->view->redirect('user/login');
        }

        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Cập nhật phần mềm';

        if (isset($_FILES["zip_file"])) {
            if($_FILES["zip_file"]["name"]) {
                $filename = $_FILES["zip_file"]["name"];
                $source = $_FILES["zip_file"]["tmp_name"];
                $type = $_FILES["zip_file"]["type"];

                $name = explode(".", $filename);
                $accepted_types = array('application/zip', 'application/x-zip-compressed', 'multipart/x-zip', 'application/x-compressed');
                foreach($accepted_types as $mime_type) {
                    if($mime_type == $type) {
                        $okay = true;
                        break;
                    } 
                }

                $continue = strtolower($name[1]) == 'zip' ? true : false;
                if(!$continue) {
                    $message = "Vui lòng tải file có đuôi .zip.";
                }

              /* PHP current path */
              $path = __SITE_PATH.'/';  // absolute path to the directory where zipper.php is in
              //$filenoext = basename ($filename, '.zip');  // absolute path to the directory where zipper.php is in (lowercase)
              //$filenoext = basename ($filenoext, '.ZIP');  // absolute path to the directory where zipper.php is in (when uppercase)

              $targetdir = $path; // target directory
              $targetzip = $path . $filename; // target zip file

              /* create directory if not exists', otherwise overwrite */
              /* target directory is same as filename without extension */

              /*if (is_dir($targetdir))  $this->rmdir_recursive ( $targetdir);


              mkdir($targetdir, 0777);*/


              /* here it is really happening */

                if(move_uploaded_file($source, $targetzip)) {
                    $zip = new ZipArchive();
                    $x = $zip->open($targetzip);  // open the zip file to extract
                    if ($x === true) {
                        $zip->extractTo($targetdir); // place in the directory with same name  
                        $zip->close();

                        unlink($targetzip);
                    }
                    $message = "Cập nhật thành công.";
                } else {    
                    $message = "Có lỗi trong quá trình tải lên. Vui lòng thử lại.";
                }

                $this->view->data['error'] = $message;
            }
        }

        $this->view->show('update/index');
    }
    function update(){
        
    }
    function rmdir_recursive($dir) {
        foreach(scandir($dir) as $file) {
           if ('.' === $file || '..' === $file) continue;
           if (is_dir("$dir/$file")) $this->rmdir_recursive("$dir/$file");
           else unlink("$dir/$file");
       }

       return rmdir($dir);
    }

}
?>