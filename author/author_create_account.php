<?php  require_once('../core/init.php');   ?>   
<?php  $page_title = ' Create Accout'; ?>   
<?php  include_once '../includes/header.inc.php';  ?>
<?php  include_once ('author_nav_menu.php');   ?>

<div class="container well ">     
 
<?php if (filter_has_var(INPUT_POST,'submit')) {

        try {
            // Declare array variables to store the missing field and errors inputs, retrieved by getMissin() and getErrors() methods                 
            $missing = null;
            $invalid = null;
            $errors  = null;

            // Perform validation for required fields
            // Creates an array called $required containing the names of all form fields, and passes it as an argument to a new instance
            $required = array('name', 'email', 'password', 'occupation', 'biography');  
          
            // Instantiate imageHandler class and set a save dir
            $img_dir = '2-MONGO-APPS/author/images/'; 
            
            // Create an instance of each class       
            $img_obj = new ImageHandler( $img_dir,  array(500, 250) );            
            $validate = new Validator($required );    // The checkRequire() method on the constructor uses trim() func to remove white spaces.  
            $author = new Author();

            // Process the uploaded image and save it by calling processUploadImage() method          
            $img_path = $img_obj->processUploadedImage($_FILES['image'] );  
            if ( $img_obj->dispayImgErrors() ) { $invalid['image'] =  $img_obj->dispayImgErrors()  ;  }

            $validate->isEmail('email');
            $validate->useEntities('biography');
            
            if ( $author->emailExits($_POST['email'] )  ) { 
               $invalid['email'] = 'Email Error: This email alreday exist.  Please, choose another email.';
            } 
            else {
                 $new_author['email']   = $_POST['email'];   
            }
            if ( $_POST['password']  !== $_POST['pass_confirm'] )  {
               $invalid['password'] = 'Confirm password do not match.';
            } 
            else {
                  $new_author['password']  = md5($_POST['password']); 
            }
            // Call the getMissing() and getErrors() methods and capture the results in appropriately named variables 
            $missing = $validate->getMissing();
            $input_errors = $validate->getErrors();
            
            // var_dump($missing);
            // var_dump($invalid);
            
            if ( empty( $missing )  &&  empty($input_errors )   &&  empty($invalid) ) {                      
              // If no empty inputs, create an instance of Author class
              $author->createAccount();                
              // If no errors were found and data insertion is successful, redirct user   
              // header('Location: index.php'); 
            }
            else {
                 $errors = array_merge ($missing, $input_errors,  $invalid);
                 // var_dump( $errors);
                 $validate->displayInputErrors($errors) ;
            }       
        } 
        catch (Exception $e) {
          echo $e;
        }
  }
?>
<h2>Create Author Account</h2>
  <?php  // $validate->displayInputErrors($errors); ?>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" accept-charset="utf-8" enctype="multipart/form-data">   
    
      <label for="name">Name:</label>
      <input  type="text"  name="name"  value="<?php echo  (isset( $_POST['name'] ) ) ? $_POST['name']  : null;  ?>"   id="name" /> <br/>

      <label for="email">Email:</label>
      <input  type="email" name="email" value="<?php echo (isset( $_POST['email'] ) ) ? $_POST['email'] : null;  ?>" id="email" /> <br/>
                  
      <label for="pass">Password:</label>
      <input type="password" name="password"  id="pass" /> <br/>
      
      <label for="pass_confirm"> Confirm Password:</label>
      <input type="password" name="pass_confirm"  id="pass_confirm" /> <br/>
                  
      <label for="occup">Occupation:</label> <br/>
      <input  type="text" name="occupation" value="<?php if (isset( $_POST['occupation'] ) ) { echo $_POST['occupation'];  }  ?>" id="oc"  /> <br/>
      
      <label for="occupation">Biography:</label> <br/>
      <textarea name="biography" id="biog" class="form-control" rows="6"><?php echo (isset( $_POST['biography'] ) ) ?  $_POST['biography'] : null;  ?></textarea>  <br/>
      
      <label>Upload Image&nbsp; <input type="file" name="image" value="<?php if (isset( $img_path) ) { echo $img_path;  }  ?>"/></label>
      <label>Image Caption&nbsp;
      <input type="text" name="caption" value=" "/></label>
    
      <input type="submit" name="submit" value="Create Account" class="btn btn-primary"/> 
    </form>

</div><?php include_once '../includes/footer.inc.php'; ?> 



