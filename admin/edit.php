<?php
		require_once('../core/init.php'); 
		$author = new Author ();
		$page_title = "Edit Post";

		// # Check whether the author or admin is indeed logged in, if not it redirects him/her to the login page. 	 
		 // if (!$author->isLoggedIn()){
			 // header('location: ../author/author_login.php');
			// exit;
		 // }
	 
		// edit.php that receives an article _id via the HTTP GET parameter and loads it into an HTML form
		$id = $_REQUEST['id'] ;  

		$action = (!empty($_POST['btn_submit']) && ($_POST['btn_submit'] === 'Save')) ? 'update_article' : 'show_form';	
		
		// Create an instance of the BlogPost class		
		$article = new BlogPost ();
		
		// initialize an array to hold our errors
		$required_inputs = array('title', 'content', 'tags', 'category');
			
		switch($action) {
							   case 'update_article':
							    // Call updatePost() method to update the selected article
	
								if ( !$article->checkRequiredFields($required_inputs) ) {
										 // Pass form values to the object properties used in createPost() method to insert the new data in the collection.
										$article->title         = $article->sanitize($_POST['title'] ) ;
									//	$article->authorId  = $author->_id;
									//	$article->author_name  = $author->name;
										$article->category  = $_POST['category'];	
										$article->tags        = $_POST['tags'] ;
										$article->content    = $_POST['content'];
												
										 // Call createPost method to create the new article										
										$article->updatePost3($id );	

										// If no errors were found and data insertion is successful, redirct user
								}
								else {
											  echo "<b>Please review the following fields:</b><br />";
											  foreach($required_inputs as $error) {
												echo "- " . ucfirst($error) . "<br/>";								   
											  }
								}

							case 'show_form':
							default:							
		}
		
		// extract the selected article collection content to display it in the form fields.
		$article->getSelectedPost($id) ; 	

		//var_dump($article->tags);
		//var_dump( explode( ',', $article->tags) );

?>

<?php 
include_once ('../includes/header.inc.php');
include_once ('includes/admin_nav.inc.php');    // fin not includen on header.php
?>
	<div class="container well ">	
			<h1>Blog Post Creator</h1>

					<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
					
						<h3>Title</h3>
						<p><input type="text" name="title"  id="title" value="<?php echo $article->title; ?>"/></p>
					  
					   <h3>Category</h3>
			           <p><input type="text" name="category" value="<?php echo $article->category ; ?>"/></p>
					   <!--
					   <h3>Tags</h3>    			
				       <p><input type="text" name="tags[]" value=" <?php  //echo  explode( ',' , $article->tags) ; ?> "/></p>
					    -->			      		
				  
						<h3>Content</h3>
						<textarea name="content" rows="15" cols="50"><?php echo $article->content; ?></textarea>		     					
						
						<input type="hidden" name="id" value="<?php echo $article->_id; ?>" />				
						<input type="hidden" name="update_count" value="<?php //echo $article['update_count'];?>" />		
						
						<p> <input type="submit" name="btn_submit" value="Save"/> </p>
					</form>		
	    	      <p>Article Id: <?php echo $id;?>.  <a href="../article.php?id=<?php echo $id;?>"> Read it. </a></p>

		
		</div>

			
	<?php	
	//echo '<pre>'; 
	//print_r($article );							
	//print_r($_POST);
	?>

    <?php include_once 'views/footer.php'; ?>
		