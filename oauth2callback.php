<?php
    set_include_path(get_include_path() . PATH_SEPARATOR . '/home/ubuntu/workspace/google-api-php-client/src');
    require_once '/home/ubuntu/workspace/google-api-php-client/src/Google/autoload.php';

    session_start();
    
    $client = new Google_Client();
    $client->setAuthConfigFile('client_secrets.json');
    $client->setRedirectUri('http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php');
    $client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
    
    if (! isset($_GET['code'])) {
      $auth_url = $client->createAuthUrl();
      header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
    } else {
      $client->authenticate($_GET['code']);
      $_SESSION['access_token'] = $client->getAccessToken();
      $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/';
      header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}