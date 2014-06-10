<?php
require_once('SessionManager.php'); 
require_once('autoLoader.php'); 

			class Author {		
						const COLLECTION = 'authors';					
						private $_mongo;
						private $_collection;
						private $_db;
						
						private $_author;
						private $_article_count;
						private $_article_list;
						protected $_errors_found;
						protected $_errors;
					
						// In the constructor of this class, we obtain a database connection and select the appropriate collection. These objects 
						// are stored in private member variables of the class.
						
						public function __construct()  {
							$this->_mongo = DBConnection::instantiate();
							$this->_collection = $this->_mongo->getCollection(Author::COLLECTION);					
							$this->_db = $this->_mongo->database;		
							
							if ( $this->isLoggedIn() ) $this->_loadData();
						    $this->_errors_found = array();
						    $this->_errors = array();
						}
						
											
						// The isLoggedIn() method checks whether the author is already logged in by simply checking the existence of user_id in $_SESSION.
						public function isLoggedIn()  {
							return isset($_SESSION['author_id']);
						}
						
						
							// The method receives the username and password as its arguments. It queries the database with the username 
							// and MD5 hash of the password.  If a matching document is found, the ObjectId of the document is casted to string 
							// and stored in $_SESSION as user_id. The method returns   TRUE to indicate that the author is successfully authenticated. 
							// Otherwise the method returns FALSE.				
						public function authenticate($email, $password)  {
							$query = array('email' => $email, 'password' => md5($password));
							
							$this->_author = $this->_collection->findOne($query); // query usern in db					
							if (empty($this->_author)) return False;
							
							   $_SESSION['author_id'] = (string) $this->_author['_id'];     // pass author id to the the sesion			
							   return True;
						}
						
						
						// The logout() method terminates the authenticated session by unsetting the user_id field.
						public function logout()  {
							unset($_SESSION['author_id']);
						}

						
				     	 //	the __get() magic method is used to read the attributes (name, address, birth date, and so on.) of a author object.
						public function __get($attr) {
						
							if (empty($this->_author))
								return Null;
							
							switch($attr) {							
								case 'name':
									return $this->_author['name'];					
								case 'email':
									return $this->_author['email'];				
								case 'password':
									return NULL;								
								case 'image':
								return $this->_author['image'];									
								case 'occupation':
									return $this->_author['occupation'];									
								case 'biography':
									return $this->_author['biography'];								
								default:
									return (isset($this->_author[$attr])) ? $this->_author[$attr] : NULL;
							}
						}
										
										
						// If the author is logged in,  the _loadData() private method is called within the constructor to query the database with the 
						// ID and populate the values of author attributes.  The __get() magic method is used to read the attributes (name, address, 
						// birth date, and so on.) of a User object.						
						private function _loadData() {
							$id = new MongoId($_SESSION['author_id']);
							$this->_author = $this->_collection->findOne(array('_id' => $id));
						}			

									
						// This method will get the author data regardless of the author is logged in.  It is used to display public information about the author	
						// in the public profile page						
				        public function getAuthorData($authorId = null) {
							$this->_author = $this->_collection->findOne(array('_id' => new MongoId($authorId) ) );						
						}	
						
		

						public function findAll() {
							$author_collection = $this->_collection->find();	
							// $author_collection  = $this->_mongo->getCollection('authors')->find();	
							return $author_collection ;							
						}
						
						

                        public function emailExits($email) {						
						    $query = array('email' => $email) ;
							$email = $this->_collection->findOne($query);	
							return $email['email'];												
						}
						
						
						
						public function getArticlesPerAuthor($id = null ) {
							$query = array('author_id' => $id);
							#$this->_article_list = $this->_mongo->getCollection('articles')->findOne($query);
							#$this->_article_list = $this->_mongo->getCollection('articles')->findOne(array('author_id' => $id) );
							$this->_article_list = $this->_mongo->getCollection('articles')->find($query);
									
							return $this->_article_list ;		// This is a cursor object, and to be able to get the restults, we have to use a loop										 
							// To get the count of article, apply count() to the return result :  $this->_article_list->count()
						}

						
					
						public function createAccount()  {              
						
								  date_default_timezone_set('America/New_York');
								  $now = date( "h:i:s", strtotime('now'));
								  $today = date("F j, Y, g:i a"); 														  
						     try {																			
									// Pass form values to an array to insert the new data in the author collection.  	
									$new_author['name']    = $_POST['name'];	
									$new_author['image']   = $img_path ;	
									$new_author['img_dir'] = $img_dir;	
   									$new_author['email']   = $_POST['email'];	 
									$new_author['password']  = md5($_POST['password']);	
									$new_author['biography']      = $_POST['biography'];
									$new_author['occupation']     = $_POST['occupation'];								
									$new_author['date_created'] = $today;										 
									// $new_author['date_created'] = new MongoDate();										 								

									$this->_collection->save($new_author);						
							  } 
							catch(MongoException $e) {
									die('Failed to insert data '.$e->getMessage());
							}
							catch(MongoConnectionException $e) {
								die("Failed to connect to database ".$e->getMessage());
							}
						}
			
						
						
						// USING MAP REDUCE METHOD:
						// It is also possible to specify a query parameter in the runCommand() so that MapReduce will be applied only 
						// on the documents in the collection that match the query. For example, if we wanted the article count per author 
						// only for the article in the 'Programming' category, we could do the following:   p 114
						    //	> db.runCommand({
							// ... mapreduce: 'sample_articles',
							// ... query: {category: 'Programming'},
							// ... map: map,
							// ... reduce: reduce,
							// ... out: 'articles_per_author'
							// ... })					
						public function articlesPerAuthor($id = null) {    // Didnt get to use this.  Revise.
	 
								$map = new MongoCode(  "function() { 
																		          emit ( this._id,
																				  { author: this.author_name,  posted_on: this.saved_at, post_title: this.title,  post_content: this.content  } 
																				  )
																		        }"
																	   );		
								// Now, define the reduce function:
								$reduce = new MongoCode( "function(key, values) {
																					 for ( var i = 0; i < values.length; i++){
																						   doc_vals = values[i];
																					  } 
																					  return doc_vals;
																			  }"												  
																		   );														  
								// Apply the MapReduce operation on the articles collection using the next command:				
								$command =  array ( 
																 'mapreduce' => 'articles',
																 'query' => array('author_id' => $id),
																 // 'query' => array('author_name' => 'Yozaira Rojas'),
																 // 'query' => array('author_name' => 'Luke Skywalker'),
																  'map' => $map,
																  'reduce' => $reduce,
																  // 'finalize' => $finalize, 
																  // string Name for the output collection. Setting this option implies keeptemp : true.
																  'out' => 'articles_per_author'
																);																		
								$results = $this->_db->command($command);							
								#return $results;
								#$query = array('author_id' => $id);
									
								$this->_article_count = $this->_db->selectCollection($results['result'])->find();
								# $this->_article_count = $this->_db->selectCollection($results['result'])->find()->sort( array('value' => -1) );							
								return  $this->_article_count ;			
							}
						
	}


# TEST getAuthorData method:
// $id = '52d3d811853257c81000003a';	

// $author = new Author ();
// # This method queries the author collection and uses the author ID as a parameter to get all the fields of this collection
// var_dump($author->getAuthorData($id)  );  // null

// echo $author->email; 
// echo $author->name = 'lala';    # OJO -- >Value can be changed  --->  Try to change this for protected variables that dont use __get ()method to 
# read the collection (see PostComment class).

# FIND AUTHOR COLLECTION
// var_dump($author->findAll() );
// $author_collection = $author->findAll() ;
// foreach ( $author_collection as $authors ) { 
 // echo $authors[ 'email'];
// var_dump($authors) ; 
// }

# FIND AUTHOR EMAIL
// $email = "mor@mail.com";
// var_dump($author->emailExits($email) );


// $author_articles_count = $author->authorArticleCount($id);
// print_r( $author_articles_count); // Mongo Cursor Object
// foreach($author_articles_count  as $articles_per_author) {
// echo '<pre>';
// print_r($articles_per_author);
// echo 'Author: '.$articles_per_author['_id'].'<br/>';
// echo 'Articles Count: '.$articles_per_author['value'];
// }



/*
// TEST getArticlesPerAuthor method:
$id = '52d3da158532577c1e000066';	
# $id = '52d3d811853257c81000003a';
$author = new Author ();
$articles_per_author = $author->getArticlesPerAuthor($id) ;
 // To get the count of article, apply count() to the return result 
echo 'This Author has a total Article Count of: '.$articles_per_author->count();
# var_dump($articles_per_author);

echo '<hr/>';

foreach ($articles_per_author as $article) {   // This is a cursor object, and to be able to get the restults, we have to use a loop		
	echo  $article['title'].'<br/>';                          // works
	echo  $article['author_name'].'<br/>';          // works
	echo '<hr/>';
	# var_dump($article);
	#echo '<pre>';print_r($article);
}
*/
	
	
/*	
// TEST articlesPerAuthor method:
$id = '52d3da158532577c1e000066';	
//$id = '52d3d811853257c81000003a';	
$author = new Author ();
// $count_articles = $author->articlesPerAuthor();      //  if no argument passed, will display those articles with id=null
$count_articles = $author->articlesPerAuthor($id);
# To get the count of article, apply count() to the return result 
echo 'This Author has a total Article Count of: '.$count_articles->count();
# var_dump($count_articles);

echo '<hr/>';

				foreach ($count_articles as $count_article) {
				echo 'Author: '.$count_article['value']['author'].'<br/>';    // works
				echo 'Article Title: '.$count_article['value']['post_title'];    // works
				var_dump($count_article);     // works
				}   // this gives the numer of results according to the number or id values

				
echo '<hr/>'; 

				 // Test the output collection:					   
				while($count_articles->hasNext()) {
					 $article_per_author = $count_articles->getNext();
					 echo $article_per_author['value']['author'].'<br/>';        // works
					 echo $article_per_author['value']['post_title'].'<br/>';    // works
				 }

echo '<hr/>';
*/




/*
// TEST getAuthorData method:
$id = $id = new MongoId('52d3d811853257c81000003a');	
$author = new Author ();
$author->getAuthorData($id);
echo $author->name;


$obj = new Author ();
echo '<pre>';
print_r($obj);

echo $obj->name.'<br/>';

$methods = get_class_methods('Author');

foreach ($methods as $method) {
echo $method.'<br/>';

}

*/
