<?php
require_once('../core/init.php');     // SessionManager class is included on Author class

// The profile page checks whether the author is indeed logged in, if not it redirects him/her to the login page. 
// Otherwise, the _loadData methor in the the class constructor is executed, and it displays the author profile information.
if (!$author->isLoggedIn()){
    header('location: author_login.php');
    exit;
}
 
// This method queries articles collection and uses author ID as a parameter to display the list of all articles corresponding to that specific author.
// REMEMBER:  This is a cursor object, and to be able to get the restults, we have to use a LOOP to retrieve an then display the list of articles 
// per author.
$articles_per_author = $author->getArticlesPerAuthor( $_SESSION['author_id'] );  

?> 

<?php include_once '../includes/header.inc.php';  ?>
<?php  include_once ('author_nav_menu.php');   ?>

			<div class="container well ">	
				<h2>Welcome <?php echo $author->name; ?></h2>
                	<p> 
                    	<span class="field">Name: </span>
                        <span class="value"><?php echo $author->name; ?></span>
                        <div class="clear"> </div>
                    </p>
                	<p>
                    	<span class="field">Email: </span>
                        <span class="value"><?php echo $author->email; ?></span>
                        <div class="clear"> </div>
                    </p>
					<p>
                    	<span class="field">Signup Date: </span>
                        <span class="value"><?php  echo date('j F, Y',$author->date_created->sec); ?></span>
                        <div class="clear"> </div>
					</p>
					<p>
                    	<span class="field">Occupation: </span>
                        <span class="value"><?php echo $author->occupation; ?></span>
                        <div class="clear"> </div>
                    </p>
					<p>
                    	<span class="field">Biography: </span>
                        <span class="value"><?php echo $author->biography; ?></span>
                        <div class="clear"> </div>
                    </p>
					
					<h3>Published Articles: </h3>  <br/>
					<?php while($articles_per_author->hasNext() )  {  $article_per_author = $articles_per_author->getNext()   ?>           						
                        <h4><?php echo $article_per_author['title']; ?></h4>  <br/>
						<p><?php echo substr( $article_per_author ['content'], 0, 500 ).'...';  // display its title and first 200 characters of its content:? ?></p>				
					    <a href="../article.php?id=<?php echo $article_per_author['_id'];                  // this link takes us to the article.php file ?>">Read more</a>
                   <?php } ?>
              </div>
			  
		<?php include_once '../includes/footer.inc.php'; ?>	  
		
		
		<?php  
		// TEST AND DEBUG:
		// Make sure that the author id and session id are the same
		// echo $author->_id.'<br/>';
		// echo $_SESSION['author_id'];
		// echo '<pre>';
		// print_r($_SESSION);											
		?>		
