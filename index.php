<?php
ob_start();
session_start();
 /*** error reporting on ***/
 error_reporting(E_ALL);

 /*** define the site path ***/
 $site_path = realpath(dirname(__FILE__));
 define ('__SITE_PATH', $site_path);

 /*** include the config.php file ***/
 include 'config.php';
  
 /*** include the init.php file ***/
 include 'includes/init.php';



 if (!isset($_SESSION['user_logined'])) {
    if (isset($_COOKIE['remember']) && isset($_COOKIE['ui']) && isset($_COOKIE['up']) && $_COOKIE['remember'] == 1) {
        $model = baseModel::getInstance();
        $user = $model->get('user2Model');
        $row = $user->getUser(base64_decode(substr($_COOKIE['ui'], 2)));
        if($row->password == substr($_COOKIE['up'], 2) && $row->user_lock != 1){
            $_SESSION['user_logined'] = $row->username;
            $_SESSION['userid_logined'] = $row->user_id;
            $_SESSION['role_logined'] = $row->role;
            $_SESSION['user_permission'] = $row->permission;
            $_SESSION['user_permission_action'] = $row->permission_action;

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

            date_default_timezone_set("Asia/Ho_Chi_Minh"); 
            $filename = "user_logs.txt";
            $text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."login"."|".$ipaddress."\n"."\r\n";
            
            $fh = fopen($filename, "a") or die("Could not open log file.");
            fwrite($fh, $text) or die("Could not write file!");
            fclose($fh);
        }
        else{
            session_destroy();
            setcookie("remember", "",time() - 3600,"/");
            setcookie("uu", "",time() - 3600,"/");
            setcookie("ui", "",time() - 3600,"/");
            setcookie("ro", "",time() - 3600,"/");
            setcookie("up", "",time() - 3600,"/");
        }
        unset($user);
        unset($row);
    }
}

/*** load the router ***/
 $registry->router = new router($registry); 

 /*** set the path to the controllers directory ***/
 $registry->router->setPath (__SITE_PATH . '/controller'); 
 $registry->router->loader();

if (isset($_SESSION['userid_logined'])) {
    if ($registry->router->controller != "" && $registry->router->controller != "index" && $registry->router->controller != "admin" && $registry->router->controller != "support") {
        
        if ($_SESSION['user_permission'] == NULL || $_SESSION['user_permission'] == "" || !in_array($registry->router->controller, json_decode($_SESSION['user_permission']))) {
            return header('Location:'.BASE_URL.'/admin');
        }
    }
    
}



?>