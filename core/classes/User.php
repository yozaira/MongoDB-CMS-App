<?php
require_once('dbconnection.php');
require_once('session.php');

class User  {

  const COLLECTION = 'users';         
  private $_mongo;
  private $_collection;
  private $_user;
  
  # Obtain a database connection and select the appropriate collection.           
  public function __construct()  {
    $this->_mongo = DBConnection::instantiate();
    $this->_collection = $this->_mongo->getCollection(User::COLLECTION);
    
    if ( $this->isLoggedIn() ) $this->_loadData();
  }
  
  
  /**
  *
  *Checks whether the user is already logged in by simply checking the existence of user_id in $_SESSION.
  */
  public function isLoggedIn()  {
    return isset($_SESSION['user_id']);
  }



  /**
  *
  *  Queries the database with the username and MD5 hash of the password.  
  * If a matching document is found, the ObjectId of the document is casted to string and stored in $_SESSION as user_id. 
  * Returns  TRUE to indicate that the user is successfully authenticated. 
  * @ param string $email user email
  * @ param string $password user password
  *
  */      
  public function authenticate($email, $password)  {
    $query = array('email' => $email, 'password' => md5($password));
    
    // now the collection is passed to _user property
    $this->_user = $this->_collection->findOne($query);  
    
    if (empty($this->_user)) return False;
    
    $_SESSION['user_id'] = (string) $this->_user['_id'];
    
    return True;
  }
  
  
  /**
  *Terminates the authenticated session by unsetting the user_id field.
  */
  public function logout()  {
    unset($_SESSION['user_id']);
  }

  
  # the __get() magic method is used to read the attributes (name, address, birth date, and so on.) of a User object.
  public function __get($attr) {
    if (empty($this->_user))
      return Null;
    
    switch($attr) {   
      case 'name':
      return $this->_user['name'];
      
      case 'username':
      return $this->_user['username'];
      
      case 'email':
      return $this->_user['email'];

      case 'password':
      return NULL;
      
      default:
      return (isset($this->_user[$attr])) ? $this->_user[$attr] : NULL;
    }
  }
            
  #If the user is logged in,  the _loadData() private method is called within the constructor to query the database with the 
  # ID and populate the values of user attributes.
  private function _loadData() {
    $id = new MongoId($_SESSION['user_id']);
    $this->_user = $this->_collection->findOne(array('_id' => $id));
  }         
            
}
      
      