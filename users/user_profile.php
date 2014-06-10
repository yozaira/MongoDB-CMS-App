<?php
require('../classes/session.php');
require('../classes/user.php');
$user = new User();

// The profile page checks whether the user is indeed logged in, if not it redirects him/her to the login page. Otherwise it displays the user profile information.
if (!$user->isLoggedIn()){
    header('location: user_login.php');
    exit;
}
    
?>
<!DOCTYPE html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link rel="stylesheet" href="style.css" /> 
  <title>Welcome <?php echo $user->name; ?></title>
</head>

<body>
<div>

<!-- When the user clicks Log out in the top right corner of the profile page, the user is logged out and redirected to the login page again. -->
<a style="float:right;" href="user_logout.php">Log out</a>

<h1>Hello <?php echo $user->name; ?></h1>
<p> 
  <span class="field">Username: </span>
  <span class="value"><?php echo $user->username; ?></span>
</p>
<p> 
  <span class="field">Name: </span>
  <span class="value"><?php echo $user->name; ?></span>
</p>
<p>
  <span class="field">Email: </span>
  <span class="value"><?php echo $user->email; ?></span>
</p>
<p>
  <span class="field">Signup Date: </span>
  <span class="value"><?php echo date('j F, Y',$user->date_created->sec); ?></span>
</p>

</div>
</body>
</html>