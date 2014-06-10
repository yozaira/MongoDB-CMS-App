<?php
// Blog Dashboard, a page that lists most recently saved articles first, showing five articles at a time. The user is able to browse through all 
// articles using the navigation links at the bottom of the list. 

try {
    $mongodb = new Mongo();
    $articleCollection = $mongodb->myblogsite->articles;
} 
catch (MongoConnectionException $e) {
  die('Failed to connect to MongoDB '.$e->getMessage());
}

$currentPage = (isset($_GET['page'])) ? (int) $_GET['page']: 1;

$articlesPerPage = 5;

// skip() lets you skip a number of results in a cursor. It needs an integer as an argument, which is the number of results to skip:
$skip = ($currentPage - 1) * $articlesPerPage;

// In this example, we sent a second optional argument to find(), an array containing names of fields that we want to see in the retrieved documents:
$cursor = $articleCollection->find( array(), array('title', 'saved_at', 'update_count') );

// count() on a MongoCursor object returns the number of items in the cursor:
$totalArticles = $cursor->count();

$totalPages = (int) ceil($totalArticles / $articlesPerPage);

// The sort() method invoked on the MongoCursor object sorts the query results based on the value of a specified field. 
//Sorting order can be both ascending and descending.

$cursor->sort( array('saved_at' =>  -1) )->skip($skip)->limit($articlesPerPage); 
?>
<div class="container well "> 
  <h1>Dashboard</h1>
    <table class="table table-striped">
     <thead>
      <tr>
        <th>Title</th>
        <th>Created at</th>
        <th>Modified</th>
        <th>Action</th>
      </tr>
      </thead>
      <tbody>
      <?php while($cursor->hasNext()): $article = $cursor->getNext();?>
      <tr>      
          <td><?php echo substr($article['title'], 0, 25) .  '...' ;  ?></td>
          <td><?php if(isset($article['saved_at']) ) { echo date('F j, g:i a', $article['saved_at']->sec ); } ?></td>
          <td>
          <?php if (!empty($article['update_count'] ) ) { echo $article['update_count'] ; }?>
          <?php  //echo $article['update_count'] ; ?>
          </td>
          <td>
            <a href="../article.php?id=<?php if(isset( $article['_id'])) {echo $article['_id']; }?>">View</a>
            | <a href="edit.php?id=<?php echo $article['_id'];?>">Edit</a> 
            | <a href="#" onclick="confirmDelete('<?php echo $article['_id']; ?>')"> Delete </a> |
          </td>         
      </tr>
      <?php endwhile;?>
      </tbody>
    </table>

    <div id="navigation">
      
      <div class="prev">       
        <?php if($currentPage !== 1): ?>
        <a href="<?php echo $_SERVER['PHP_SELF'].'?page='.($currentPage - 1); ?>">Previous </a>
        <?php endif; ?>       
      </div>
    
      <div class="page-number">
       <?php echo $currentPage; ?>
      </div>
    
    <div class="next">
      <?php if($currentPage !== $totalPages): ?>
      <a href="<?php echo $_SERVER['PHP_SELF'].'?page='.($currentPage + 1); ?>">Next</a>
      <?php endif; ?>
    </div>

   </div>

  </div>
</div>
    
