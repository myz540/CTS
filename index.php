<?php

include '/libraries/controller.php';

// flag for valid login credentials
$valid_credentials = false;

// if button is pressed
if(isset($_POST['submit'])){
    
    // check POST data
    if(isset($_POST['login']) && is_array($_POST['login']) && count($_POST['login'] == 2)){
    
        // sanitize username and password
        foreach($_POST['login'] as $key => $value){
            $login[$key] = filter_var($value, FILTER_SANITIZE_STRING);
        }   
    
        // connect to db to validate login credentials
        // currently use default user and pass but will eventually use $login creds 
        // to connect to db
        $pdo = connect();
        
        if($pdo){
            // SELECT PHS ID and password to validate login credentials
            $sql = 'SELECT * FROM `users` WHERE `phs_id` = :user';

            $statement = $pdo->prepare($sql);
            $statement->execute(array('user' => $login['user']));

            // $result holds the query for given user
            $result = $statement->fetch(PDO::FETCH_ASSOC);
            if($result && is_array($result) && count($result) > 0){
                
                // set flag for valid credentials to true
                if($login['user'] == $result['phs_id'] && $login['pass'] == $result['password']){
                    $valid_credentials = true;
                }
                else{
                    echo '<br /><br /> username or password is incorrect';
                }
            }
            else{
                echo '<br /><br /> user name not found';
            } 
        }
        else{
            echo '<br /><br />Failed to connect to DB';
        }
    }
    else{
        echo '<br /><br />POST data invalid';
    }
}

?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title> Welcome to CTS </title>
    </head>
    <body>
        <div class="frontpage-login">
            <h1 style="align-content: center"> Welcome to Cell Therapy Solutions <br /> Please login to continue </h1>
        </div>
        <form method="post" action="#">
            User: <input type="text" name="login[user]" maxlength="8" autocomplete="off" />
            Password: <input type="password" name="login[pass]" maxlength="8" autocomplete="off" />
            <input type="submit" name="submit" value="login" />
        </form>
    </body>
</html>

<?php

if($valid_credentials){

    // begin a session
    session_start();
    
    echo 'initializing session data for user: ' . $result['name'] . '<br />';
    
    // initialize session variables for 'user' and 'valid'
    $_SESSION['user'] = $result;
    $_SESSION['valid'] = true;
    $_SESSION['logger'] = new Logger();

    // generate link to proceed
    echo '<br /><a href="frontpage.php"> Proceed to CTS Suite </a>';
}

//phpinfo(INFO_VARIABLES);


