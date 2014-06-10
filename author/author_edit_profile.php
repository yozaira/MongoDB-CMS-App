<?php	
require_once('../core/init.php');     // SessionManager class is included on Author class

// The profile page checks whether the author is indeed logged in, if not it redirects him/her to the login page. 
// Otherwise, the _loadData methor in the the class constructor is executed, and it displays the author profile information.
if (!$author->isLoggedIn()){
    header('location: author_login.php');
    exit;
}
					
	$action = (!empty($_POST['btn_submit']) && ($_POST['btn_submit'] === 'Update')) ? 'save_change' : 'show_form';

	switch($action) {
				case 'save_change':	
				
						// REMEMBER TO UPDATE THIS FORM TO MAKE SURE THAT THE SAME USER DOES NOT REGISTER TWICE
				
						try {
								require('../classes/dbconnection.php');
								$mongo = DBConnection::instantiate();
								$collection = $mongo->getCollection('authors');  

								  // We construct an array with the author-submitted data, and pass this array as an argument to the insert() method of the 
								  // MongoCollection object
															
								$author = array (
														'name'       => $_POST['name'], 
														'email' => $_POST['email'], 
														'password' => md5($_POST['pass']),
														'date_created'  => new MongoDate(),
														'biography'       => $_POST['biography'], 
														//'author_image'     => $_POST['author_img'], 
														'occupation'     => $_POST['occup']
													   );												
								  // The insert() method stores the data in the collection. 
								  // The $article array automatically receives a field named _id, which is the  autogenerated 
								  // unique ObjectId of the inserted BSON document.
								  
							      $collection->insert($author);
								  // $collection->save($author);
								  header ('Location: author_private_profile.php');
								  exit;
						} 
						catch(MongoConnectionException $e) {
							     die("Failed to connect to database ".
							     $e->getMessage());
						}
						catch(MongoException $e) {
							    die('Failed to insert data '.$e->getMessage());
						}
						break;
						
							case 'show_form':
							default:
	}
    
	?>
<?php include_once '../includes/header.inc.php';  ?>
<?php  include_once ('author_nav_menu.php');   ?>

	<div class="container well ">
			<h1>Create Author Account</h1>

			<form action="<?php echo $_SERVER['PHP_SELF'];?>"method="post">
		    
				<label for="name">Name:</label>
				<input  type="text"  name="name" value="<?php echo $author->name; ?> " id="name" required /> <br/><br/>			

				<label for="email">Email:</label>
				<input  type="email" name="email" value="<?php echo $author->email; ?> " id="email" required /> <br/><br/>
								
				<p><a href="#">Reset Password</a></p> <!-- This file is not created yet -->
										
				<label for="occup">Occupation:</label>
				<input  type="text" name="occup" value=" <?php echo $author->occupation; ?>" id="occup"  required /> <br/><br/>
				
				
				<label for="occup">Biography:</label> <br/>
				<textarea name="biography" id="biog" rows="15" cols="40" ><?php echo $author->biography; ?> </textarea>  <br/>
			
				<input type="submit" name="btn_submit" value="Update" /> 

			</form>			

	</div> 
	<?php include_once '../includes/footer.inc.php'; ?>	  


