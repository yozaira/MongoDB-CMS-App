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
        <div id="contentarea">
            <div id="innercontentarea">
			   <!-- When the user clicks Log out in the top right corner of the profile page, the user is logged out and redirected to the login page again. -->
                <a style="float:right;" href="user_logout.php">Log out</a>
                
				<h1>Hello <?php echo $user->name; ?></h1>
                <ul class="profile-list">
                	<li> 
                    	<span class="field">Username: </span>
                        <span class="value"><?php echo $user->username; ?></span>
                        <div class="clear"> </div>
                    </li>
                	<li> 
                    	<span class="field">Name: </span>
                        <span class="value"><?php echo $user->name; ?></span>
                        <div class="clear"> </div>
                    </li>
                	<li>
                    	<span class="field">Email: </span>
                        <span class="value"><?php echo $user->email; ?></span>
                        <div class="clear"> </div>
                    </li>
					<li>
                    	<span class="field">Signup Date: </span>
                        <span class="value"><?php echo date('j F, Y',$user->date_created->sec); ?></span>
                        <div class="clear"> </div>
                    </li>
					
                </ul>
            </div>
        </div>
    </body>
</html>