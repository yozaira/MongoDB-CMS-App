<?php

$action = (!empty($_POST['login']) && ($_POST['login'] === 'Log in')) ? 'login' : 'show_form';

switch($action) {
  case 'login':      
    require_once('../classes/session.php');
    require('../classes/user.php');
    
    $user = new User();   
    $email= $_POST['email'];
    $password = $_POST['pass'];
    
    if ($user->authenticate($email, $password)) {            
       header('location: user_profile.php');
       exit;
    }
    else {                
         $errorMessage = "Username/password did not match.";
         break;
    }

  case 'show_form':
  default:
          $errorMessage = NULL;
}

?>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="stylesheet" href="style.css" /> 
  <title>User Login</title>
</head>

<body>
<div>

  <h1>Log in here</h1>

  <form id="login" action="user_login.php" method="post" accept-charset="utf-8">

    <?php if(isset($errorMessage)): ?>
    <?php echo $errorMessage; ?></br>
    <?php endif ?>
    <p>
      <label>Email </label> 
      <input class="textbox" tabindex="1" type="email" name="email" autocomplete="on"/>
    </p>
    <p>
      <label>Password </label> 
      <input class="textbox" tabindex="2" type="password" name="pass"/>
    </p>

    <p><input  type="submit" name="login"  value="Log in"  id="login-submit" tabindex="3" / </p>

  </form>                        

  </div>                
</body>
</html>