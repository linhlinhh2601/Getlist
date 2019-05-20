<?php
require_once './google-api-php-client-2.2.2/vendor/autoload.php';

session_start();

$client = new Google_Client();
$client->setScopes(Google_Service_Calendar::CALENDAR);
$client->setAuthConfig('client_id.json');
$client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');

if (! isset($_GET['code'])) {
  $auth_url = $client->createAuthUrl();
  header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
} else {
  $client->authenticate($_GET['code']);
  $_SESSION['access_token'] = $client->getAccessToken();
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/update.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}