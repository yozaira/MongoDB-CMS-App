<?php
// Note: This file can be modified to include image metadata and image file updated.

		require_once('../core/init.php');    
		$author = new Author ();

		 // Check whether the author is indeed logged in, if not it redirects him/her to the login page. 
		 if (!$author->isLoggedIn()){
			 header('location: ../author/author_login.php');
			exit;
		 }
		 
	    $page_title = $author->name.'- Create Post';
	 
		// edit.php that receives an article _id via the HTTP GET parameter and loads it into an HTML form
		$id = (string) $_REQUEST['id'] ;  

		$action = (!empty($_POST['btn_submit']) && ($_POST['btn_submit'] === 'Update Post')) ? 'update_article' : 'show_form';	
		
		// Create an instance of the BlogPost class		
		$article = new BlogPost ();
		
		// initialize an array to hold our errors
		// $required_inputs = array('title', 'content', 'category');
			
		switch($action) {
							   case 'update_article':   
							     // NOTE:  This form was validated in the class method, while create_post.php validated in the form, which one is best??

								 // Call createPost method to create the new article										
								$article->updatePost($id );	

								// REMEMBER: If no errors were found and data insertion is successful, redirct user											
							case 'show_form':
							  default:							
		}			
		$article->getSelectedPost($id) ; 	 // extract the selected article collection content to display it in the form fields.
  
// Test tags
// var_dump($article->tags);
// var_dump( explode( ',', $article->tags) );
?>

<?php include_once ('../includes/header.inc.php'); ?>
<?php include_once ('author_nav_menu.php');      ?>

		<div class="container well ">	
			<h1>Blog Post Creator</h1>
				<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post">
					
						<h3>Title</h3>
						<p><input type="text" name="title"  id="title" value="<?php echo $article->title; ?>"/></p>
					  
					   <h3>Category</h3>
			           <p><input type="text" name="category" value="<?php echo $article->category ; ?>"/></p>
				  
					   <h3>Tags</h3>    			
				       <p><input type="text" name="tags[]" value=" <?php  //echo  explode( ',' , $article->tags) ; ?> "/></p>
				      				  
						<h3>Content</h3>
						<textarea name="content" class="form-control" rows="6" ><?php echo $article->content; ?></textarea>		     					
			   
					    <label>Upload Image&nbsp; <input type="file" name="image"/></label>
					   
					    <label>Image Caption&nbsp;<input type="text" name="caption" value="<?php echo $article->img_caption; ?>"/></label>
						<!--
						For multimple images upload
						<p><input type="file" name="image[]"/></p>
						<p><input type="file" name="image[]"/></p>				
						-->	
						<input type="hidden" name="id" value="<?php echo $article->_id; ?>" />				
						<input type="hidden" name="update_count" value="<?php //echo $article['update_count'];?>" />		
						
						<p> <input type="submit" name="btn_submit" value="Update Post" class="btn btn-primary"/> </p>
					</form>	
						<p>Article saved. _id: <?php echo $id;?>.  <a href="../article.php?id=<?php echo $id;?>"> Read it. </a></p>
		 					
				 </div> 
			   <?php include_once '../includes/footer.inc.php';  ?>
			
			<?php	
			//echo '<pre>'; 
			//print_r($article );							
			//print_r($_POST);
			?>
						

		