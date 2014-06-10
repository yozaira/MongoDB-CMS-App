<?php
require_once('../core/init.php');      // SessionManager class is included on Author class
$page_title = 'Log in';

 // If the author is indeed logged in, it is redirected to main page. 
if ($author->isLoggedIn()){
header('location:  index.php'); // in author/index.php the user will be redirected to login page if is not logged in
exit;
 }

$action = (!empty($_POST['login']) && ($_POST['login'] === 'Log in')) ? 'login' : 'show_form';
		switch($action) {
					case 'login':      
									$email = $_POST['email'];
									$password = $_POST['pass'];
									
									if ($author->authenticate($email, $password)) {            
										header('location: index.php');
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
<?php include_once '../includes/header.inc.php';  ?>
<?php  include_once ('author_nav_menu.php');   ?>
 
        	<div class="container well ">		
                  <h2>Log in</h2>				
                      <form role="form action=" " method="post" accept-charset="utf-8" >
                                <?php if(isset($errorMessage)): ?>
                                <p><?php echo $errorMessage; ?></p>
                                <?php endif ?>
                                <p>
                                    <label>Email </label> 
                                    <input type="email" name="email" value="<?php if (isset($_POST['email']) ) { echo $_POST['email']; } ?>" autocomplete="on"/>
                                </p>
                                <p>
                                    <label>Password </label> 
                                    <input  type="password" name="pass"/>
                                </p>
                                <p>
                                    <input  type="submit" name="login"  value="Log in"  id="login-submit" class="btn btn-primary"/>
                                </p>
                                <p class="clear"></p>
                        </form>   					
                    </div>			
            </div>
        </div>
		
<?php include_once '../includes/footer.inc.php'; ?>