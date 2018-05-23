<?php
ob_start();
session_start();
date_default_timezone_set("Asia/Ho_Chi_Minh");

 /*** define the site path ***/
 $site_path = realpath(dirname(__FILE__));
 define ('__SITE_PATH', $site_path);

  
 /*** include the init.php file ***/
 include 'includes/init.php';



/*** load the router ***/
 $registry->router = new router($registry); 

 /*** set the path to the controllers directory ***/
 $registry->router->setPath (__SITE_PATH . '/controller'); 
 $registry->router->loader();

 

?>