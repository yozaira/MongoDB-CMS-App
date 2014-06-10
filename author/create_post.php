<?php require_once('../core/init.php');   ?>   
<?php $page_title = $author->name.'- Create Post'; ?>   
<?php include_once '../includes/header.inc.php';  ?>
<?php include_once ('author_nav_menu.php');   ?>

<div class="container well ">

<?php
// The profile page checks whether the author is indeed logged in, if not it redirects him/her to the login page.  Otherwise it displays the author 
// profile information.
 if (!$author->isLoggedIn()) {
  header('location: author_login.php');
  exit;
 } 

if (filter_has_var(INPUT_POST, 'send')) {

   try {
        // Declare array variables to store the missing field and errors inputs, retrieved by getMissin() and getErrors() methods
    $missing = null;
    $errors  = null;
    $invalid = null;

    // The code includes the Validator class, creates an array called $required containing the names of all three form fields, and passes it as 
    // an argument to a new instance of the Validator class.
    $required = array('title' , 'tags', 'category', 'content', 'caption');  
                             
    // Instantiate imageHandler class and set a save dir
    $img_dir = '2-MONGO-APPS/admin/images/';  
    
    // Create an instance of each class 
    $validate = new Validator( $required );   // the checkRequire() method on the constructor uses trim() func to remove white spaces.     
    $img_obj = new ImageHandler( $img_dir,  array(500, 250) );
    
    // Process the uploaded image and save it by calling processUploadImage() method          
    $img_path = $img_obj->processUploadedImage($_FILES['image'] );  
    if ( $img_obj->dispayImgErrors() ) { $invalid['image']  = $img_obj->dispayImgErrors()  ;  }                            

    $validate->removeTags('title', $preserveQuotes = false);
    $validate->removeTags('tags');
    $validate->removeTags('category');
    $validate->useEntities('content');

    // Call the getMissing() and getErrors() methods and capture the results in appropriately named variables
    $missing = $validate->getMissing();
    //var_dump($missing);
    $Input_erros = $validate->getErrors();
    //var_dump($Input_erros );
    
    if (empty($missing ) && empty($Input_erros) && empty($invalid) ) {          
      // If no empty inputs, call createPost method to create the new article
      $article->createPost();                  
      // If no errors were found and data insertion is successful, redirct user
      # header('Location: ../article.php?id='.$article->_id);   
      //header('Location: ../index.php');   
    } 
    else {
         $errors = array_merge ($missing, $Input_erros, $invalid);
         // var_dump( $errors);
         $validate->displayInputErrors( $errors);   
    }
  } 
  catch (Exception $e) {
     echo $e;
  }
  
}
 // var_dump($article);
 // var_dump($article->createPost() );  
 // var_dump($author);
?>  

  <h3>Add New Post</h3>       
  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">        
      <label>Title</label>
      <p><input type="text" name="title" id="title"  value="<?php if(isset($_POST['title']) ) { echo $_POST['title'];   }  ?>"/></p>  

      <label>Tags</label>         
      <p><input type="text" name="tags[]"  value="<?php //if(isset($_POST['tags']) ) { echo $_POST['tags']; }  ?>"/></p>              
      
      <label>Category</label>
      <p><input type="text" name="category" id="cat"  value="<?php if(isset($_POST['category']) ) { echo $_POST['category']; }  ?>"/></p> 
        
      <label>Content</label>
      <textarea name="content" class="form-control" rows="6" ><?php if(isset($_POST['content']) ) { echo $_POST['content'];  }  ?></textarea>
       
      <label>Upload Image&nbsp; <input type="file" name="image"/></label>
       
      <label>Image Caption&nbsp;
      <input type="text" name="caption" value="<?php if(isset($_POST['caption']) ) { echo $_POST['caption'];  }  ?>"/></label>
      <!--
      For multimple images upload
      <p><input type="file" name="image[]"/></p>
      <p><input type="file" name="image[]"/></p>        
      -->              
      <p><input type="submit" name="send"  value="Save Post" class="btn btn-primary"/></p> 
   </form>    
</div>

<?php include_once '../includes/footer.inc.php'; ?>
 
<?php
/*
var_dump($article->_id);
// test the session:
echo '<pre>';
print_r($_SESSION);           
*/
?>  

  

