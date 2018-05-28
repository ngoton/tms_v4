<?php
// require "vendor/autoload.php";
// use Kunnu\Dropbox\Dropbox;
// use Kunnu\Dropbox\DropboxApp;
// use Kunnu\Dropbox\DropboxFile;
// use Kunnu\Dropbox\Exceptions\DropboxClientException;

Class adminbackupController Extends baseController {
    public function index() {
    	$this->view->setLayout('admin');
    	if (!isset($_SESSION['role_logined'])) {
            return $this->view->redirect('user/login');
        }
        
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Backup & Restore';


        $this->view->show('adminbackup/index');
    }
    public function restore() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['role_logined'])) {
            return $this->view->redirect('user/login');
        }
        

        $this->view->show('adminbackup/restore');
    }

    function backup_db(){
          $host=DB_SERVER;
          $uname=DB_USERNAME;
          $pass=DB_PASSWORD;
          $database = DB_DATABASE;
        $connection=@mysqli_connect($host,$uname,$pass,$database)
        or die("Database Connection Failed");
        $selectdb=mysqli_select_db($connection,$database) or die("Database could not be selected");
        $result=mysqli_select_db($connection,$database)
        or die("database cannot be selected <br>");
        mysqli_query($connection,"SET NAMES utf8");
        /* Luu tru tat ca ten Table vao mot mang */
        $allTables = array();
        $result = mysqli_query($connection,'SHOW TABLES');
        while($row = mysqli_fetch_row($result)){
             $allTables[] = $row[0];
        }
         $return = "";
        foreach($allTables as $table){
        $result = mysqli_query($connection,'SELECT * FROM '.$table);
        $num_fields = mysqli_num_fields($result);
         
        $return.= 'DROP TABLE IF EXISTS '.$table.';';
        $row2 = mysqli_fetch_row(mysqli_query($connection,'SHOW CREATE TABLE '.$table));
        $return.= "\n\n".$row2[1].";\n\n";
         
        for ($i = 0; $i < $num_fields; $i++) {
        while($row = mysqli_fetch_row($result)){
           $return.= 'INSERT INTO '.$table.' VALUES(';
             for($j=0; $j<$num_fields; $j++){
               $row[$j] = addslashes($row[$j]);
               $row[$j] = str_replace("\n","\\n",$row[$j]);
               if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; }
               else { $return.= '""'; }
               if ($j<($num_fields-1)) { $return.= ','; }
             }
           $return.= ");\n";
        }
        }
        $return.="\n\n";
        }
         
        // Tao Backup Folder
        $folder = 'DB_Backup/';
        if (!is_dir($folder))
        mkdir($folder, 0777, true);
        chmod($folder, 0777);
         
        $date = date('d-m-Y-H-i-s', time());
        $filename = $folder."db-backup-".$date;
         
        $handle = fopen($filename.'.sql','w+');
        fwrite($handle,$return);
        fclose($handle);


        


        //mở file để đọc với chế độ nhị phân (binary)
        $fp = fopen($filename.'.sql', "rb");
         
        //gởi header đến cho browser
        header('Content-type: application/octet-stream');
        header('Content-disposition: attachment; filename="'.$filename.'.sql"');
        header('Content-length: ' . filesize($filename.'.sql'));
         
        //đọc file và trả dữ liệu về cho browser
        fpassthru($fp);
        fclose($fp);

        unlink($filename.'.sql');
         

    }

    function autobackup(){
        # Include the Dropbox SDK libraries
        $host=DB_SERVER;
          $uname=DB_USERNAME;
          $pass=DB_PASSWORD;
          $database = DB_DATABASE;
        $connection=@mysqli_connect($host,$uname,$pass,$database)
        or die("Database Connection Failed");
        $selectdb=mysqli_select_db($connection,$database) or die("Database could not be selected");
        $result=mysqli_select_db($connection,$database)
        or die("database cannot be selected <br>");
        mysqli_query($connection,"SET NAMES utf8");
        /* Luu tru tat ca ten Table vao mot mang */
        $allTables = array();
        $result = mysqli_query($connection,'SHOW TABLES');
        while($row = mysqli_fetch_row($result)){
             $allTables[] = $row[0];
        }
         $return = "";
        foreach($allTables as $table){
        $result = mysqli_query($connection,'SELECT * FROM '.$table);
        $num_fields = mysqli_num_fields($result);
         
        $return.= 'DROP TABLE IF EXISTS '.$table.';';
        $row2 = mysqli_fetch_row(mysqli_query($connection,'SHOW CREATE TABLE '.$table));
        $return.= "\n\n".$row2[1].";\n\n";
         
        for ($i = 0; $i < $num_fields; $i++) {
        while($row = mysqli_fetch_row($result)){
           $return.= 'INSERT INTO '.$table.' VALUES(';
             for($j=0; $j<$num_fields; $j++){
               $row[$j] = addslashes($row[$j]);
               $row[$j] = str_replace("\n","\\n",$row[$j]);
               if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; }
               else { $return.= '""'; }
               if ($j<($num_fields-1)) { $return.= ','; }
             }
           $return.= ");\n";
        }
        }
        $return.="\n\n";
        }
         
        // Tao Backup Folder
        $folder = 'DB_Backup/';
        if (!is_dir($folder))
        mkdir($folder, 0777, true);
        chmod($folder, 0777);
         
        $date = date('d-m-Y-H-i-s', time());
        $filename = $folder."db-".$_SERVER["SERVER_NAME"]."-".$date;
         
        $handle = fopen($filename.'.sql','w+');
        fwrite($handle,$return);
        fclose($handle);

        $backupFile = $filename.'.sql';
        $backupFilename = "db-".$_SERVER["SERVER_NAME"]."-".$date.'.sql';
        
        //Configure Dropbox Application
        $app = new DropboxApp("xty8leltwgu1u2w", "yrd25ouxqaql8k0", "CPrOUSMRAMAAAAAAAAAAoSpJcaiSO24R5gMJn6MoVy2A7Q1FLF2AP51ldF4I4Nwx");

        //Configure Dropbox service
        $dropbox = new Dropbox($app);
        
        try {
            // Create Dropbox File from Path
            $dropboxFile = new DropboxFile($backupFile);

            // Upload the file to Dropbox
            $uploadedFile = $dropbox->upload($dropboxFile, "/TMS_SQL_Backup/" . $backupFilename, ['autorename' => true]);

            // File Uploaded
            //echo $uploadedFile->getPathDisplay();
        } catch (DropboxClientException $e) {
            echo $e->getMessage();
        }

        //unlink($filename.'.sql');
    }

    public function autodelete(){
        $tempDir = "DB_Backup/"; 
        array_map('unlink', glob($tempDir."*"));
    }


}
?>