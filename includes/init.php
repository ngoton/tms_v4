<?php
 /*** include the config.php file ***/
 include __SITE_PATH . '/application/' . 'config.php';

 /*** include the controller class ***/
 include __SITE_PATH . '/application/' . 'controller_base.class.php';
 
  /*** include the view class ***/
 include __SITE_PATH . '/application/' . 'view_base.class.php';

   /*** include the database class ***/
 include __SITE_PATH . '/application/' . 'db.class.php';

   /*** include the model class ***/
 include __SITE_PATH . '/application/' . 'model_base.class.php';
 
 /*** include the registry class ***/
 include __SITE_PATH . '/application/' . 'registry.class.php';

 /*** include the router class ***/
 include __SITE_PATH . '/application/' . 'router.class.php';

 /*** include the library class ***/
 include __SITE_PATH . '/lib/' . 'lib.class.php';


 /*** a new registry object ***/
 $registry = new registry;
 
 Db::getInstance();

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

            
        	$text = date('d/m/Y H:i:s')."|".$_SESSION['user_logined']."|"."login"."|".$ipaddress."\n"."\r\n";
        	
        	$lib = Library::getInstance();
        	$lib->ghi_file("user_logs.txt",$text);
            
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
 
?>



