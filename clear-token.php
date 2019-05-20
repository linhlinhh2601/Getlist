<?php
require_once './google-api-php-client-2.2.2/vendor/autoload.php';  
session_start();    
$accesstoken=$_SESSION['access_token'];

//Unset token and user data from session    
unset($_SESSION['access_token']);

//Reset OAuth access token    
$client = new Google_Client();

//$client->revokeToken();    
$client->revokeToken($accesstoken);

//Destroy entire session    
session_destroy();    

ob_start();
$location = 'http://' . $_SERVER['HTTP_HOST'] . '/update.php';
header('Location:' . $location);
ob_end_flush();
die();