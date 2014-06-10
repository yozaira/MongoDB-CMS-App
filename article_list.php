<?php
require_once('core/init.php');
# REMEMBER: the result is a cursor. It needs a loop to see the content.  Using a class might ot work, since all articles contained in the cursor object, 
// has to be got and displayed with html.

// In the HTML portion of the code, we iterate the MongoCursor object to fetch each article one by one and display 
// its title and first 200 characters of its content.

$posts = new BlogPost ();
$article_cursor = $posts->getAllPosts();  
// Include the directory where images are stored.
$img_dir = './admin/images/';

$currentPage = (isset($_GET['page'])) ? (int) $_GET['page'] : 1;

$articlesPerPage = 4;

$skip = ($currentPage - 1) * $articlesPerPage;

$totalArticles = $article_cursor->count();

$totalPages = (int)  ceil($totalArticles / $articlesPerPage);

$article_cursor->skip($skip)->limit($articlesPerPage);  
?>
    <div class="container"> <!-- stars ther and ends on footer.php file -->
      <div class="row">
         <div class="col-lg-8">  
          <!-- 
          Call the getNext() method on it to get the first (in this case,  the only) document in the query result.  Finally, display the title and 
          content of the retrieved document using HTML markup. 
          
          The hasNext() checks whether or not there are any more objects in the cursor.  getNext() returns the next object pointed by the cursor, 
          and then advances the cursor. The documents do not get loaded into memory unless we invoke either getNext() or hasNext() on the cursor, 
          this is good for keeping the memory usage low.    
          -->
          <?php while ($article_cursor->hasNext()):  $article = $article_cursor->getNext(); ?>
            <!-- Blog Ttile -->
          <h1 class="blog-post-title"><a href="article.php?id=<?php echo  urlencode( $article['_id'] );  ?>"><?php  echo $article['title']; ?></a></h1>
          <!-- Author -->
          <p class="lead">by <a href="author_public_profile.php?id=<?php if(isset($article['author_id'] ) ) { echo  urlencode( $article['author_id'] ); } ?>">
          <?php if (!empty($article['author_name'])) {echo $article['author_name']; }?></a>  </p>
          <!-- Date -->      
          <p> <span class="glyphicon glyphicon-time"></span><?php if (isset( $article['saved_at']) ) { echo date(' F j, Y g:i a ',  $article['saved_at']->sec ); } ?></p>       
          <!-- Image -->
          <img src="<?php  echo (isset($article['img_name'] ) ) ? $img_dir.$article['img_name'] : null; ?>" class="img-responsive">
          <!-- Blog Content -->
          <p><?php echo substr($article['content'], 0, 200).'...';                    // display its title and first 200 characters of its content:?></p>
          <a class="btn btn-primary" href="article.php?id=<?php echo urlencode($article['_id'] );       // this link takes us to the article.php file ?>">
             Read more<span class="glyphicon glyphicon-chevron-right"></span>
          </a>
          <hr>
          <!--When we click on the Read more link, it takes us to the blog.php file . This file receives the _id of the article as an HTTP GET parameter.
          We invoke the findOne() method on the articles collection, sending the _id value as a parameter to the method. The findOne() method is used to 
          retrieve a single document, unlike find()  which we use to retrieve a cursor of a set of documents that we can iterate over: -->          
          <?php endwhile; ?>
        
          <!-- Pagination  -->    
          <ul class="pager">
          <li class="previous">
          <?php if ($currentPage !== 1): ?> <a href="<?php echo $_SERVER['PHP_SELF'].'?page='.($currentPage - 1); ?>">Previous </a>
          </li> <?php endif; ?>
          </ul>
          <ul class="pager">
          <li class="previous">
          <?php if ($currentPage !== $totalPages): ?> <a href="<?php echo $_SERVER['PHP_SELF'].'?page='.($currentPage + 1); ?>">Next</a>
          </li><?php endif; ?>
          </ul>
      
        </div><!-- end col-lg --> 

        <!-- Sidebar  -->  
        <?php include_once 'includes/sidebar-top.inc.php'; ?>
            
      </div><!-- end row -->   

  
  <?php
  // TEST AND DEBUGGING
  // foreach ($article_cursor as $article) {
  // echo '<pre>';
  // print_r($article);
  // }
  ?>




