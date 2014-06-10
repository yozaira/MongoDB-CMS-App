<?php
require_once('core/init.php');

// This file receives the _id of the article as an HTTP GET parameter. 
$id = (string) $_GET['id'];

//  Added timers at the start and end of this script so we can measure the time taken to render the page. At the end of the script, 
// we instantiated Logger and called logRequest() on it, passing the response time taken as an optional argument:
$start = microtime();

// Define page tielt
$page_title = $article->title; 

// Include the directory where images are stored.
$img_dir = './admin/images/';

// Call the method that retrive the requested article
$article->getSelectedPost($id);

// Call the method that retrieve the comments contained on the requested article
// PostComment class was instanciated on core/init.php file
// The result is a MongoCursor object. A loop is required to retrive the conntent
$comments = $comment->getPostComments( $id);

// To count the total comments that each post has, there are two ways to do it:
// 1- Applying Mongo count() method to the getPostComment() method resulting variable:
   # echo $comments->count();
// 2- Creating another method on PostComment class. 
    #echo $comment->getCommentsCount($id) ;

  
# Test object and methods
// echo '<pre>';print_r($article);
// echo '<pre>';print_r($article->getSelectedPost($id) ); 
// echo '<pre>';print_r($comments);  // is working
?>
<?php include_once 'includes/header.inc.php'; ?>
<?php include_once 'includes/navbar.inc.php'; ?>

<div class="container"> <!-- stars ther and ends on footer.php file -->
  <div class="row">
    <div class="col-lg-8"> 
     <section>          
      <!-- Blog Ttile -->
      <h1 class="blog-post-title"><?php echo $article->title; ?></h2> 
      <?php  // If author or admin is logged in, show the Edit Post link. 
           if ($author->isLoggedIn()){   ?>
            <p><a href="author/edit_post.php?id=<?php echo urlencode( $article->_id);?>">Edit Post</a></p>
      <?php }  ?>

      <!-- Author -->
      <p class="lead">by <a href="author_public_profile.php?id=<?php echo urlencode( $article->author_id); ?>"><?php echo $article->author_name; ?></a> </p>
      <!-- Date -->
      <p> <span class="glyphicon glyphicon-time"></span><?php echo date(' F j, Y g:i a ',  $article->saved_at->sec ); ?></p>
      <!-- Image -->
      <img src="<?php  echo (isset( $article->img_name ) ) ?  $img_dir.$article->img_name : null; ?>" class="img-responsive">
      <img src="http://placehold.it/900x300" class="img-responsive">        
      <!-- Blog Content -->         
      <p><?php echo $article->content; ?></p>
          
      <!-- the comments -->      
        <h3>Comments</h3> 
      <!-- Total number of comments -->
        <span class="comment-count">
        <?php if (empty( $comments) ) { echo "There are currently no comments"; }
              else { echo  "This article has " .$comments->count() . " comments" ; } ?>
      </span>
      <hr>           
      <?php if (!empty($comments) ) :
            foreach( $comments as $comment):  ?>                  
             <small> <?php //if (isset($comment['posted_at'] ) ): echo date('g:i a, F j', $comment['posted_at']->sec) ;  endif; ?> </small><br/>  
             <p><?php  if (isset($comment['posted_at'] ) ): echo $comment['posted_at'] .' ...'; endif; ?></p>           
             <p><?php  if (isset($comment['name'] ) ): echo $comment['name'] .' ...'; endif; ?></p>           
             <p><?php  if (isset($comment['comment'] ) ): echo  $comment['comment']; endif; ?></p>
             <hr>           
      <?php endforeach;  ?>               
      <?php endif; ?>  

    </section>  
    
    <!-- the comment Form -->     
    <section>          
      <div class="well">
        <h4>Leave a Comment:</h4>
        <?php include_once('add_comment.php');  ?>    
      </div>        
    </section>   

  </div><!-- end col-lg -->
  
    <?php
    //Add timmers to this script so that it loads log.php at runtime.
    $end = microtime();
    $data = array('response_time_ms' => ($end - $start) * 1000);

    $logger = new Logger();
    $logger->logRequest($data);
    // This method is on log.php class and was added on page 142 of the book. Method that keeps track of how many times a blog post has been viewed daily. 
    $logger->updateVisitCounter($id );
    ?>

    <!-- Sidebar  --> 
    <?php include_once 'includes/sidebar-top.inc.php'; ?>
            
</div><!-- end row -->  
<?php  include_once 'includes/footer.inc.php';  ?>

    
<?php
// TEST 
//var_dump($_POST);
// echo '<pre>';print_r($comment);   // is working 
# echo '<pre>';
# print_r($article->comments);
# print_r($comment);
// if (!empty($comment) ) {
// print_r($comment); 
// }  
# will throw an error 'Notice: Undefined property: BlogPost::$comments' if is not defined in BlogPost class together with the other properties.
?>
  
