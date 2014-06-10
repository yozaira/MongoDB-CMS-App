<?php
require_once('core/init.php');  

$id = (string) $_GET['id'];

if (filter_has_var(INPUT_POST, 'add_comment')) {

    try {
      // Declare array variables to store the missing field and errors inputs
      $missing = null;
      $input_errors = null;               
      $errors = null;               
      // The code includes the Validator class, creates an array called $required containing the names of all three form fields, and passes it as 
      // an argument to a new instance of the Validator class.
      $required = array('name' , 'email' , 'comment');    

      $validate = new Validator($required);
      
      $validate->removeTags('name');
      $validate->isEmail('email');
      $validate->checkTextLength('comment', 10, 500);
      $validate->useEntities('comment');

      // Call the getMissing() and getErrors() methods and capture the results in appropriately named variables
      $missing = $validate->getMissing();
      $input_errors = $validate->getErrors();
      $errors = array_merge ($missing, $input_errors );
      
      if (!$errors) {           
        // If no empty inputs, call createPost method to create the new article
        $comment = new PostComment ();
        $comment->createComment($id);                    
        // If no errors were found and data insertion is successful, redirct user
        header('Location: article.php?id='.$id);    
      } 
      else {
           $validate->displayInputErrors($errors);
      }
    } 
    catch (Exception $e) {
      echo $e;
    }  
}

?>
<form action=" " method="post"> 
  <p>
    <label for="name">Name: <br/>
    <?php if (isset($errors['name'])) {    echo '<span class="warning">' . $errors['name'] . '</span>';  }  ?>
    </label> <input name="name" type="text" class="textfield" id="name" />
  </p>        
  <p>
    <label for="email">Email: <br/>
    <?php if (isset($errors['email'])) { echo '<span class="warning">' . $errors['email'] . '</span>';  }?>
    </label> <input name="email" type="text" class="textfield" id="email" />
  </p>    
  <p>     
    <label for="comment">Comments:  <br/>
    <?php if (isset($errors['comment'])) {  echo '<span class="warning">' . $errors['comment'] . '</span>'; } ?>
    </label>  <br/><textarea name="comment" id="comment" cols="45" rows="5"></textarea>
  </p>                  
    <input type="hidden" name="article_id" value="<?php //echo $article->_id;  //the value of this hiiden field will be retrived on comment.php ?>"/> 
    <input type="submit" name="add_comment" value="Save"/>
</form>
         
        