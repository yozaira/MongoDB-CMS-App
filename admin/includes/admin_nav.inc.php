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
						  <?php  //if ($author->isLoggedIn()) {  ?>
						  <li><a class="brand" href="../index.php">Website</a></li>
						  <li><a href="index.php">Admin</a></li>
						  <li><a href="images_list.php">Img Info</a></li>
						  <li><a href="page_views.php">Posts Views</a></li>		  
						  <li><a href="avg_rating.php">Posts Ratings</a></li>		  
						  <li><a href="#">Create Account</a></li>  
						  <li><a href="#">Admin Login</a></li> 						  
						  <li><a href="#">Logout</a></li>	
						<?php // } ?>		
						<!-- 
						Admin login?
						<li><a href="./author/author_created_account.php">Create Author Account</a></li>
						<li><a href="./author/author_public_profile.php">Author Profile</a></li> 
						--> 						  
                </ul>
              </div>
           <!-- /.navbar-collapse -->
         </div>
      <!-- /.container -->
    </nav>
<!-- end navbar --> 