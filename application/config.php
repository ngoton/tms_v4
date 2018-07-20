<?php
define('DB_DATABASE','tms');// Database name
define('DB_USERNAME','root');// User database
define('DB_PASSWORD','');// Password database
define('DB_SERVER','localhost');// IP host
define('BASE_URL',isset($_SERVER["HTTPS"]) ? 'https://'.$_SERVER["SERVER_NAME"] : 'http://'.$_SERVER["SERVER_NAME"]);// IP host

define('CAPTCHA_KEY','6LeooloUAAAAABdP-yQ7hKHRyu9MZ8GIUSUO3DXF');// reCAPTCHA site key
define('CAPTCHA_SECRET','6LeooloUAAAAALnCBqYJQmtsR8lNfhds464IKuFi');// reCAPTCHA secret

define('DROPBOX_KEY','xty8leltwgu1u2w');// Dropbox key
define('DROPBOX_SECRET','yrd25ouxqaql8k0');// Dropbox secret
define('DROPBOX_TOKEN','CPrOUSMRAMAAAAAAAAAAoSpJcaiSO24R5gMJn6MoVy2A7Q1FLF2AP51ldF4I4Nwx');// Dropbox token

define('MAP_KEY','AIzaSyC3lStXX-NOBTp-Rh9wPzK723fnjt-akdM');// Google Map API key
?>
