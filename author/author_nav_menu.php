	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="../index.php">Mongo Site</a>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse navbar-ex1-collapse">
               <ul class="nav navbar-nav">
			     <!-- different navigation items are displayed depending on if the user is logged in or not. -->					  
				<?php  if ($author->isLoggedIn()) {  ?>
				 <li><a href="index.php">Home</a></li>
				 <li><a href="author_private_profile.php">My Profile</a></li> 
				 <li><a href="author_edit_profile.php">Edit Profile</a></li> 
				 <li><a href="create_post.php">Create Post</a></li>           
				 <li><a href="author_logout.php">Logout</a></li>	
				 <?php } else { ?>
						  
				 <li> <a  href="../index.php">Web Site</a></li>
				 <li><a href="author_create_account.php">Create Account</a></li>
			     <li><a href="author_login.php">Author Login</a></li>					  
				 <?php  } ?>							  						  
               </ul>
              </div>
           <!-- /.navbar-collapse -->
         </div>
      <!-- /.container -->
    </nav>
<!-- end navbar --> 
     <!-- end navbar --> 
	 