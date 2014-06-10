<?php
require_once('autoLoader.php'); 

class BlogPost {
  
    const COLLECTION = 'articles';          
    protected $_mongo;
    protected $_collection;   
    // Location for overloaded data.  
    protected $_article = array();            

    public function __construct ()  {       
      $this->_mongo = DBConnection::instantiate();
      $this->_collection = $this->_mongo->getCollection(BlogPost::COLLECTION);  
      //echo '<pre>';print_r ($this->_mongo);   
     }

     // the __get() magic method is used to read the attributes  (name, title, content, and so on.) of a BlogPost object.     
     //  __get()  reads the data on  getSelectedPost() method.  
     // getSelectedPost() method uses the article id to pass the data of the selcted article to $this->_article property
     
    public function __get($attr) {
    
        if (empty($this->_article))
          return Null;
        
        switch($attr) {               
            case 'author_id':
              return $this->_article['author_id'];
            case 'author_name':
              return $this->_article['author_name'];
            case 'title':
              return $this->_article['title']; 
            case 'category':
              return $this->_article['category'];
            case 'tags':
              return $this->_article['tags'];
            
            case 'content':
              return $this->_article['content'];                    
                   // REMEMBER: comments are not embbeded in this class, but on a separated collection.
            default:
              return (isset($this->_article[$attr])) ? $this->_article[$attr] : NULL;  // with this, using __isset($attr) wont be necessary
        }
    }

    
    
    // This method uses article or post id to retrive the data of that selected article and then passes this data to $this->_article property
    public function getSelectedPost($postid = null ) {            
      $query = array('_id' => new MongoId($postid) );
      $this->_article = $this->_collection->findOne($query);
      // it returns a cursor.  The values will be read by _get() method.  
      return $this->_article;                
    } 
      

                  
    //  fetch the blog title by _id from the articles collection. 
    public function getArticleTitle($pid)   {
      $article = $this->_collection->findOne(array('_id' => new MongoId($pid) ) );
      return $article['title'];
    }
    
    
      
      
    public function getAllPosts() {
      // Find article collection and display articles in ascending order, order by the date created, and pass them to all_posts array.
      $all_posts = $this->_collection->find()->sort(array('saved_at'=>-1));   
      
      // REMEMBER: $this->all_posts will return a cursor. The find() method returns a MongoCursor object, 
      // an object that we can use to iterate  through the results of the query.  

      // When we don't pass any query arguments to find(), it gets an empty array by default  (an empty JSON object in MongoDB)
      // and matches all documents in the collection.             
      return $all_posts; 
       
      // Test
      #while ($this->_all_posts->hasNext()  )  { $postList = $this->_all_posts->getNext();  }
      #return $postList;              
    } 

    
    // this method will work only if  __set() is present ??   
    
    public function createPost()  {         
        try {
          // Pass form values to the object properties used in createPost() method to insert the new data in the collection.    
          $new_article['title'] =  $_POST['title'];
          // Reference author id using an instance of Author class.  This is the object related to the session,  loaded through _loadData().  
          // It is different to getAuthorData() object.
          $new_article['author_id'] = $_SESSION['author_id']; 
          $new_article['author_name'] = $author->name;    
          $new_article['category'] = $_POST['category'];  
          $new_article['tags']     = $_POST['tags'] ;                 
          $new_article['content'] = $_POST['content'] ;     
          $new_article['img_name'] = $img_path;     
          $new_article['img_caption']  = $_POST['caption']  ;               
          $new_article['saved_at'] = new MongoDate();                      
                                                                  
          $this->_collection->insert($new_article);             
        } 
        catch(MongoException $e) {
          die('Failed to insert data '.$e->getMessage());
        }
        catch(MongoConnectionException $e) {
          die("Failed to connect to database ".$e->getMessage());
        }
    }


    
    public function updatePost( $postid = null ) {  
   
        date_default_timezone_set('America/New_York');
        $now = date( "h:i:s", strtotime('now'));
        $today = date("F j, Y, g:i a"); 
        
        // Declare array variables to store the missing field and errors inputs
        $missing = null;
        $errors = null;

        // The code includes the Validator class, creates an array called $required containing the names of all three form fields, and passes it as 
        // an argument to a new instance of the Validator class.
        $required = array('title' , 'tags', 'category', 'content', 'caption');  
        
        $validate = new Validator( $required );
        
        // grab the values from the article variable and pass them into the corresponding values on the $post object
        $this->_article = $this->_collection->findOne(array('_id' => new MongoId($postid ) ) );   
        
         // Pass form values to the object properties used in createPost() method to insert the new data in the collection.   
        $this->_article['title'] = $validate->removeTags($_POST['title'] );  
        $this->_article['content'] = $validate->useEntities($_POST['content'] );      
        $this->_article['category'] = $validate->removeTags($_POST['category'] );  
        $this->_article['last_updated'] =  $today;
          
        // Call the getMissing() and getErrors() methods and capture the results in appropriately named variables
        $missing = $validate->getMissing();
        $errors = $validate->getErrors();
        
        if (!$missing && !$errors) {          
           // If no empty inputs, call save the new article 
           $this->_collection->save($this->_article);  
            // update the 'update_count' item
           $this->_collection->update( array('_id' => new MongoId($postid ) ),
                              array('$inc' => array('update_count' => 1) ),
                              array('upsert' => True) 
                              );  
        }else {
              // $errors = array_merge ($missing, $input_errors,  $invalid);
              // var_dump( $errors);
              $validate->displayInputErrors($missing) ;
        }                                                                  
        return $this->_article;   

     }
    

  
      public function deletePost($postid = null ) { 
         // The remove() method takes an array as its parameter, which it uses to query the document it is going to delete. 
         // If multiple documents match the query, all of them are deleted. If no query argument is passed, remove() will delete 
         // all documents in the collection.
         try {
           $this->_collection->remove(array('_id' => new MongoId($postid)));  
          
           // The thing you should know about remove() is that it does not alert you when it fails. To verify whether the file deletion 
           // was actually successful, you can call the lastError() method on MongoDB object, right after calling remove() and see 
           // if it returns any error message:
           $error = $this->_mongo->database->lastError();
           if (isset($error['err']) ) {
           throw new Exception ('Error deleting files '.$error['err'] );
           }                    
        }
        catch(MongoException $e) {
           die('Failed to remove data '.$e->getMessage() );
        }               
    }
    

} // end class

    

# TEST getSelectedPost() method:

// $id = '52dc7f7a8532579415000289';  
// $pid = '52e9bb0a853257ec15000031';
// $posts = new BlogPost();

// var_dump($post->getArticleTitle($pid) );

// echo '<pre>';print_r($posts);
// echo '<pre>';print_r($posts->collection);
// var_dump($posts->getAllPosts() );


/*
echo '<pre>';print_r($posts);  // shows just the content of the object or instance
echo  '<hr/>';

$posts->getSelectedPost($id);
echo $posts->title. '<br/>'; 
echo $posts->author_name. '<br/>'; 
var_dump($posts->comments). '<br/>'; 
// echo $posts->newProperty = 'added value';     // triggered by __set()

*/

// echo  '<hr/>'; 
// echo $posts->createPost();
// echo '<pre>';print_r($posts);  // shows  all the content of the object or instance, including the retrived values on the methods
// echo  '<hr/>';


/*
$posts->getAllPosts() ;
 foreach ( $posts as $post) { var_dump($post) ;  }
foreach ( $post as $post1) { var_dump($post1) ;  }


$posts->updatePost($id) ;
// $post->title;
// $post->title;
echo '<pre>';print_r($posts);
// var_dump(isset($post->title));
    
*/



  
      