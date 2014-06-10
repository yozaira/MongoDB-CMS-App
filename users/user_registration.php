<?php 
  // When the user clicks the register button after writing, the following portion of the code gets executed, which connects to MongoDB 
  // and selects a database and a collection where the data will be stored.  
              
  $action = (!empty($_POST['btn_submit']) && ($_POST['btn_submit'] === 'Signup')) ? 'save_user' : 'show_form';

  switch($action) {
    case 'save_user': 
    
      try {
          require('../classes/dbconnection.php');
          $mongo = DBConnection::instantiate();
          $collection = $mongo->getCollection('users');  // remember that even if the collection does not exist, it will be created on the fly...
    
          $user = array(
                      'name'      => $_POST['name'], 
                      'username'  => $_POST['username'], 
                      'email'     => $_POST['email'], 
                      'password'   => md5($_POST['pass']),
                      'date_created' => new MongoDate()               
                       );                       
          $collection->insert($user);
          // $collection->save($user);
      } 
      catch(MongoConnectionException $e) {
        die("Failed to connect to database ". $e->getMessage());
      }
      catch(MongoException $e) {
        die('Failed to insert data '.$e->getMessage());
      }
      break;          
        case 'show_form':
        default:
  }

?>
  <html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <link rel="stylesheet" href="../css/style.css"/>
    <title>User Registration</title>
  </head>
  <body>
  <div id="contentarea">
      <div id="innercontentarea">
      <h1>User Registration</h1>
      
    <?php if ($action === 'show_form'): ?>
      <form action="<?php echo $_SERVER['PHP_SELF'];?>"method="post">
        
        <label for="name">Name:</label>
        <input id="name" name="name" type="text" required />
        
        <label for="username">Username:</label>
        <input id="username" name="username" type="text" required />
        
        <label for="email">Email:</label>
        <input id="email" name="email" type="email" required />
        
        <label for="pass">Password:</label>
        <input id="pass" name="pass" type="password" />
      
        <input type="submit" name="btn_submit" value="Signup" />

      </form>     
      <?php else: ?>  
                   <p>User saved successfully:  _id:<?php echo $user['_id'];?>.<a href="user_login.php">Go to login page</a></p>
      <?php endif;?>
  
  </div> 
  </div>
  </body>
  </html>



