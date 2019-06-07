<?php
require_once "dropbox-sdk/lib/Dropbox/autoload.php";
use \Dropbox as dbx;

Class backupController Extends baseController {
    public function index() {
    	$this->view->setLayout('admin');
    	if (!isset($_SESSION['role_logined'])) {
            return $this->view->redirect('user/login');
        }
        $this->view->data['lib'] = $this->lib;
        $this->view->data['title'] = 'Backup & Restore';


        $this->view->show('backup/index');
    }
    public function restore() {
        $this->view->setLayout('admin');
        if (!isset($_SESSION['role_logined'])) {
            return $this->view->redirect('user/login');
        }

        $this->view->show('backup/restore');
    }

    function backup_db(){
          $host=DB_SERVER;
          $uname=DB_USERNAME;
          $pass=DB_PASSWORD;
          $database = DB_DATABASE;
        $connection=@mysql_connect($host,$uname,$pass)
        or die("Database Connection Failed");
        $selectdb=mysql_select_db($database) or die("Database could not be selected");
        $result=mysql_select_db($database)
        or die("database cannot be selected <br>");
        mysql_query("SET NAMES utf8", $connection);
        /* Luu tru tat ca ten Table vao mot mang */
        $allTables = array();
        $result = mysql_query('SHOW TABLES');
        while($row = mysql_fetch_row($result)){
             $allTables[] = $row[0];
        }
         $return = "";
        foreach($allTables as $table){
        $result = mysql_query('SELECT * FROM '.$table);
        $num_fields = mysql_num_fields($result);
         
        $return.= 'DROP TABLE IF EXISTS '.$table.';';
        $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
        $return.= "\n\n".$row2[1].";\n\n";
         
        for ($i = 0; $i < $num_fields; $i++) {
        while($row = mysql_fetch_row($result)){
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
         
        $date = date('m-d-Y-H-i-s', time());
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
        $connection=@mysql_connect($host,$uname,$pass)
        or die("Database Connection Failed");
        $selectdb=mysql_select_db($database) or die("Database could not be selected");
        $result=mysql_select_db($database)
        or die("database cannot be selected <br>");
        mysql_query("SET NAMES utf8", $connection);
        /* Luu tru tat ca ten Table vao mot mang */
        $allTables = array();
        $result = mysql_query('SHOW TABLES');
        while($row = mysql_fetch_row($result)){
             $allTables[] = $row[0];
        }
         $return = "";
        foreach($allTables as $table){
        $result = mysql_query('SELECT * FROM '.$table);
        $num_fields = mysql_num_fields($result);
         
        $return.= 'DROP TABLE IF EXISTS '.$table.';';
        $row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
        $return.= "\n\n".$row2[1].";\n\n";
         
        for ($i = 0; $i < $num_fields; $i++) {
        while($row = mysql_fetch_row($result)){
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
         
        $date = date('m-d-Y-H-i-s', time());
        $filename = $folder."db-backup-".BASE_URL."-".$date;
         
        $handle = fopen($filename.'.sql','w+');
        fwrite($handle,$return);
        fclose($handle);

        $backupFile = $filename.'.sql';
        $backupFilename = "db-backup-".BASE_URL."-".$date.'.sql';
        

        //our access token from the Dropbox App Panel
        $accessToken = 'CPrOUSMRAMAAAAAAAAAAD7W0UwU_FW5JLjIVO66xt1P3UOg5yf9lv0MvSaZMBLgW';

        //now run the DBox app info and set the client;

        $appInfo = dbx\AppInfo::loadFromJsonFile("config.json");
        $dbxClient = new dbx\Client($accessToken, "TMS_SQL_Backup");


        //now the main handling of the zipped file upload;
         
        //echo("Uploading $backupFilename to Dropbox\n");
        try {
                $f = fopen($backupFile, "rb");
                $result = $dbxClient->uploadFile('/TMS_SQL_Backup/'.$backupFilename, dbx\WriteMode::force(), $f);
                fclose($f);
        } catch (Exception $e) {
            $errors = "Failed to upload CRM DB Backup to Dropbox: " . $e->getMessage() . "\n";
        }


        //for testing, will confirm backup
        //echo("Finished CRM DB Backup.\n");

        unlink($filename.'.sql');
    }


}
?>