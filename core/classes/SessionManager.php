<?php
require_once('dbconnection.php');

class SessionManager{
      
    const COLLECTION = 'sessions';         // name of collection where sessions will be stored  
    const SESSION_TIMEOUT = 600;           // Expire session after 10 mins in inactivity
    const SESSION_LIFESPAN = 3600;         // 1 hour
    const SESSION_NAME = 'mongosessid';    // name of the session cookie      
    const SESSION_COOKIE_PATH = '/';
    const SESSION_COOKIE_DOMAIN = ''; 
    
    private $_mongo;
    private $_collection;
    // represents the current session
    private $_currentSession;


    public function __construct()  {
    
          // Calling the initialize() static method on DBConnection class returns an instance of this class.              
        $this->_mongo = DBConnection::instantiate();
        
        // we can then select a collection by invoking the  getCollection() method on this instance, like this:
        $this->_collection = $this->_mongo->getCollection(SessionManager::COLLECTION);
        
         // sets the user-level session storage functions which are used for storing and retrieving data associated with a session. 
         // This is most useful when a storage method other than those supplied by PHP sessions is preferred. i.e. Storing the session 
         // data in a local database. 
        session_set_save_handler(
                              array(&$this, 'open'),
                              array(&$this, 'close'),
                              array(&$this, 'read'),
                              array(&$this, 'write'),
                              array(&$this, 'destroy'),
                              array(&$this, 'gc')            
                            );      
        //Set session garbage collection period.        
        ini_set('session.gc_maxlifetime', SessionManager::SESSION_LIFESPAN);

        // set session cookie configurations.
        // This func sets cookie parameters defined in the php.ini file. The effect of this function only lasts for the duration of the script. 
        // Thus, you need to call session_set_cookie_params() for every request and before session_start() is called. 
        // http://www.php.net/manual/en/function.session-set-cookie-params.php
        
        session_set_cookie_params(  SessionManager::SESSION_LIFESPAN, 
                                   SessionManager::SESSION_COOKIE_PATH, 
                                   SessionManager::SESSION_COOKIE_DOMAIN
                              );
        
        // Replace 'PHPSESSID' with 'mongosessid' as the session name.  
        session_name(SessionManager::SESSION_NAME);
        
        // The cache limiter defines which cache control HTTP headers are sent to the client. These headers determine the rules by which the 
        // page content may be cached by the client and intermediate proxies. Setting the cache limiter to nocache disallows any client/proxy caching. 
        // A value of public permits caching by proxies and the client, whereas private disallows caching by proxies and permits the client to cache the 
        // contents.    http://www.php.net/manual/en/function.session-cache-limiter.php
        
        session_cache_limiter('nocache');
        session_start();
      }
    

    /**
    *
    * This method is called whenever a session is initiated with session_ start(). 
    *@ param string $path the path to where the session will be stored 
     * @ param string $name the name of the session cookie. 
     * It returns TRUE to indicate successful initiation of a session.
     *
    */    
    public function open($path, $name)  {
        return true;
    }
    
    
    
    /**
    *
    * This method is called at the successful end of a PHP script using session handling. 
    * returns TRUE
    */
    public function close()  {
      return true;
    }
    
    
    /**
    *
    * This function is executed whenever we are trying to add or change something in $_SESSION. 
    *  It  looks up the collection for a document with the session ID,  overwrites the data if it finds one, 
    * and resets its timedout_at  timestamp to 10 minutes into the future (the default session timeout is set to 10 minutes).
    * @ param  $sessionId id of the session
    * @ param string $data serialized representation of the data to be stored in $_SESSION 
    *
    */
    public function write($sessionId, $data) {    
        $expired_at = time() + self::SESSION_TIMEOUT;
        
        $new_obj = array('data' => $data, 
                         'timedout_at' => time() + self::SESSION_TIMEOUT, 
                         'expired_at' => (empty($this->_currentSession)) ? 
                          time()+ SessionManager::SESSION_LIFESPAN: $this->_currentSession['expired_at'] 
                        );
        
        $query = array('session_id' => $sessionId);
        $this->_collection->update( $query, array('$set' => $new_obj), array('upsert' => True) );
        return True;
    }
    
    
    
    /**
    *
    * This method is called whenever we are trying to retrieve a variable from the $_SESSION super global array. 
    * @ param sessionId returns a string value of the $_SESSION variable.   
    */
    public function read($sessionId) {          
    
          // It queries the collection for a document with the  session ID whose expiration timestamp is set to future, and which is going to time out in the future. 
          // If it finds such a document, it returns the value of the data field for this document. page 84.
          $query = array(
                      'session_id' => $sessionId,
                      'timedout_at' => array('$gte' => time()),
                      'expired_at' => array('$gte' => time())
                      );              
        $result = $this->_collection->findOne($query);
        $this->_currentSession = $result;
        if ( !isset($result['data']) ) {
           return '';
        }
        return $result['data'];   
    }
    
    

    
    
    // destroy(): This is called whenever we are trying to terminate a session by calling the built-in session_destroy() method. 
    // It takes the session ID as its only parameter and returns TRUE upon success.
    public function destroy($sessionId) {
      $this->_collection->remove(array('session_id' =>$sessionId));
      return True;
    }
    
    
    // gc(): This function is executed by the PHP session garbage collector. It takes the maximum lifetime of session cookies as its argument, 
    // and removes any session older than the specified lifetime. It also returns TRUE on success. The session.gc_ probability setting in php.ini 
    // specifies the probability of the session garbage collector running.
    // http://www.php.net/manual/en/session.configuration.php#ini.session.gc-probability
    
    public function gc() {
      $query = array( 'expired_at' => array('$lt' => time()));
      $this->_collection->remove($query);
      return True;
    }
    
          
    
    public function __destruct() {      
       // session_write_close — Write session data and end session.  No value is returned. 
       // http://www.php.net/session_write_close
       session_write_close();
    }

} // end of class 
  

# Initiate a session by instantiating a SessionManager object. If we need to start a session in a PHP page    
$session = new SessionManager();