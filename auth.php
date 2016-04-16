<?php
    set_include_path(get_include_path() . PATH_SEPARATOR . '/home/ubuntu/workspace/google-api-php-client/src');
    require_once '/home/ubuntu/workspace/google-api-php-client/src/Google/autoload.php';
    require_once '/home/ubuntu/workspace/google-api-php-client/src/Google/Client.php';
    

    define('ITERATIONS', 20000);
     //If you ever change this, none of the old passwords will work!

    function hashsalt($password, $stretch, $salt, $times){
    
    $temp = $stretch.$password.$salt;
    $temp = hash("sha256", $temp);
    //$temp = hash("sha256", strtoupper($temp));
    for ($x = 1; $x < $times; $x++) {
        $temp = hash("sha256", $temp.$password.$salt);
    }
    //echo $temp;
    return $temp;
    }

    //Will check to see if a username is already taken
    function is_taken($array, $dbhandle)
    {
        $query = "SELECT * FROM users WHERE username = :username";
        $statement = $dbhandle->prepare($query);
        
        $statement->execute(array("username" => $array["username"]));
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        if($array["username"] = $results[0]["username"])
        {

            //echo 0;
            return 0;
        }
        else 
        {

            //echo 1;
            return 1;
        }
    }
    //Take the password, make a salt, hash a few times, save salt, hash, and username in database
    function input_user($array, $dbhandle)
    {
        $stretch = mcrypt_create_iv(10, MCRYPT_DEV_URANDOM);
        $salt = mcrypt_create_iv(10, MCRYPT_DEV_URANDOM);
        $hash = hashsalt($array["password"], $stretch, $salt, ITERATIONS);
    
        $query = "insert into users (username, stretch, salt, hash) values(:username, :stretch, :salt, :hash)";
        $statement = $dbhandle->prepare($query);
        
        $statement->execute(array(
            "username" => $array["username"],
            "stretch" => $stretch,
            "salt" => $salt,
            "hash" => $hash
            ));
        
        header('HTTP/1.1 200 OK');
        echo "Account created!";
    }
    
    //use stretch and hash with username, see if password hash meets one in db
    function check_user($array, $dbhandle)
    {
        $query = "SELECT * FROM users WHERE username = :username";
        $statement = $dbhandle->prepare($query);
        
        $statement->execute(array("username" => $array["username"]));
        $results = $statement->fetchAll(PDO::FETCH_ASSOC);
        
        $hash = hashsalt($array["password"], $results[0]['stretch'], $results[0]['salt'], ITERATIONS);
        
        if($hash == $results[0]['hash'])
        {
            $_SESSION["logged_in"] = true;
            echo "You logged in!";
            //headers only work if you haven't sent anything back yet? 
            //Even if I take out the above echo nothing happens.
            //header("Location: https://second-login-pepperinsure.c9users.io/enter.html"); /* Redirect browser */
            //exit();
        } else {
            $_SESSION["logged_in"] = false;
            echo "Username or password invalid.";
            //For some reason using this one puts up a duplicate of index that can't do anything
            //header("Location: index.html"); /* Redirect browser */
            //exit();
        }
    }
    //grab the post
    $user_input = $_POST["user_input"];
    //put up database
    $dbhandle = new PDO("sqlite:accounts.sqlite") or die("Failed to open DB");
    if (!$dbhandle) die ($error);
    
    //figure out where to go
    //make a new user
    if($user_input["button_pressed"] == 1)
    {
        if(is_taken($user_input, $dbhandle) == 1)
        {
            input_user($user_input, $dbhandle);
        }
        else {
            echo "That account is already registered.";
        }
        
    }
    //try to login
    else if($user_input["button_pressed"] == 0)
    {
        check_user($user_input, $dbhandle);
    }
    else
    {
        echo "google auth coming soon";
        /*$client = new Google_Client();
        $client->setAuthConfigFile('client_secret_1086020971627-uag3a41v488e20elsdo82cmj17ifpcss.apps.googleusercontent.com.json');
        $client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
        $client->setRedirectUri('https://second-login-pepperinsure.c9users.io');
        
        session_start();

        $client = new Google_Client();
        $client->setAuthConfigFile('client_secret_1086020971627-uag3a41v488e20elsdo82cmj17ifpcss.apps.googleusercontent.com.json');
        $client->addScope(Google_Service_Drive::DRIVE_METADATA_READONLY);
        
        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
          $client->setAccessToken($_SESSION['access_token']);
          $drive_service = new Google_Service_Drive($client);
          $files_list = $drive_service->files->listFiles(array())->getItems();
          echo json_encode($files_list);
        } else {
          $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/oauth2callback.php';
          header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
        }
        
        */
    }
    
    
?>