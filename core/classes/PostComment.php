<?php   require_once('autoLoader.php');   

		
		class PostComment extends BlogPost {

				const COLLECTION = 'article_comments';					
				//protected $_mongo;
				protected $_comment_collection;
				protected $_new_comment;

				public function __construct($user = null) {
					parent::__construct();			
					$this->_comment_collection = $this->_mongo->getCollection(PostComment::COLLECTION);
					//var_dump($this->_comment_collection );
					//var_dump($this->_collection );
				}

				
				
				
				// Since comments is an element embbeded in the article collection, the article id has to be passed in createComment()					 
				public function createComment($pid =null ) { 
						
						date_default_timezone_set('America/New_York');
						$now = date( "h:i:s", strtotime('now'));
						$today = date("F j, Y, g:i a"); 
						// Create instance of User class, so the author name can be added automatically to each post.
						// $user = new User();										 
						     try  {	
									if (!empty($pid) ) {
										# Find the post or article with the comments we want to retrieve.
										// $post = $this->_collection->findOne(array('_id' => new MongoId($pid) ) );
										$post = $this->getSelectedPost($pid) ;
										$post_id = (isset($post['_id']) )? $post['_id']: array();	
										// return $post['_id'];
									}	
								
									 // Creat a document or array for the new comment
									 $new_comment['post_id'] =  $post_id; 
									 $new_comment['name'] =  $_POST['name'] ;
									 $new_comment['email'] =  $_POST['email'] ;
									 // REMEMBER:  Limit amount of words 
									 $new_comment['comment'] =  $_POST['comment'] ;                                
									 $new_comment['user_id'] =  (isset($_SEESION['user_id']) )? $_SEESION['user_id'] : 'guest';										 
								     $new_comment['user_ip'] = null;   
								     $new_comment['active'] = null;   
								     $new_comment['location'] = null;   								 
									 $new_comment['votes_up'] = 0;          									        								 
									 $new_comment['votes_down'] = 0;          									        								 
									 $new_comment['posted_at'] = new MongoDate();
									// $this->_new_comment['posted_at'] = $today;
  		
									//$this->_comment_collection->insert($this->_new_comment);						
									$this->_comment_collection->save($new_comment);						
						     } 
							 catch(MongoException $e) {
								die('Failed to insert comment. Error: '.$e->getMessage());
							 }
							 catch(MongoConnectionException $e) {
								die("Failed to connect to database ".$e->getMessage());
							}						
				}

					
					
				 // Use the post id to query the comment collection to find the comments made on that post
				public function getPostComments($pid = null) {  		
						// if the query is made using findOne(), the method will return just the element being queried 						
						$comments = $this->_comment_collection->find(array('post_id' => new MongoId($pid) ) );    
						return $comments;	
				}

						
						
				public function getCommentsCount($pid = null) {			
						$comments_count = $this->getPostComments($pid) ;
						return $comments_count->count()  ;	
				}

		
				public function deleteComment($cid = null ) {							
						try {									
							  $this->_collection->remove(array('_id' => new MongoId($cid)));		
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

															
} // en class

						 

	# ================  TEST access to BlogPost class:   ========================
	// post id
	// $pid1 = '52dc7f7a8532579415000289';	
	// $pid2 = '52e9bb0a853257ec15000031';	
	//$pid3 = '52efb5da853257ec15000048';	
	
	
	// $posts = new BlogPost();
	// echo '<pre>';print_r($posts);  // shows just the content of the object or instance
	# It accessed BlogPost method.  It shows the selected article, including all its comments.  PROBLEM: Dont allown inserting new comments.
	// echo '<pre>';print_r($posts->getSelectedPost($pid2 ) );  

    # echo  '<hr/>';
	# $posts->getSelectedPost($pid);
	// echo $posts->title. '<br/>'; 
	// echo $posts->author_name. '<br/>'; 
    # var_dump($posts->comments). '<br/>';   // comments coming from BlogPost class

	//====================================================================
	
	
	# TEST createPost() method:
    // echo  '<hr/>';
	// $comment_obj = new PostComment();   
	
    //  echo '<pre>';print_r($comment_obj ); 
     //echo '<pre>';print_r($comment_obj->getSelectedPost($pid3 ) ); 
    // echo '<pre>';print_r($comment_obj->createComment($pid3) ); 
    // $comment_obj->createComment($pid3) ;
   
    # var_dump($comment_obj->createComment($pid ));   // check that the data appears in the new array or document (deactivate new_commment array first)
	# $comment_obj->createComment($pid2);  								// empty
	
	//var_dump($comment_obj->createComment($pid3) ); 			// comments: null
	   
	# TEST getAllComments() method, which contains the existing comments
	// var_dump( $comment_obj->getAllComments($pid2) );
	
	// var_dump( $comment_obj->getPostComments($pid3) );
	// var_dump( $comment_obj->getByUser($pid3) );
	
	
	//getByUser($cid = null);

	 // var_dump( $comment_obj->getAllComments() );   
	 // $c = $comment_obj->getAllComments(); 
	 // echo $c['name'];
	
	// $com = $comment_obj->getAllComments();
	// foreach($com as $comments ) { 
	// echo $comments['post_id'].'<br/>';   
	// echo $comments['name'].'<br/>';   
	// echo $comments['email'].'<br/>';   
	// echo '<pre>';print_r($comments);  
	// }   
	// echo '<hr/>';
	// echo $comment['name'].'<br/>';
	// echo $comment['email'].'<br/>';
	// echo $comment['comment'].'<br/>';			


	
	// 	// TEST  comments coming from BlogPost class
	// $comments = new PostComment();   
	// echo '<pre>';print_r($comments);          				  // without the method declared, this shows just the CommentPost object
	// echo '<hr/>';
	// var_dump($comments->getAllComments($pid) );
	// with the method declared, this shows just the CommentPost object including the comments
	# var_dump($comments->getAllComments($pid) );   // thes values are null , 	WHY??  The problem was that I needed to pass the result of the method to a property (_comments)
	// echo '<pre>';print_r($comments );
	// echo '<hr/>';
	
	
	/*	
	$comments_array = $comment_obj->getAllComments($pid);   // now it is working!!!!
	
	foreach($comments_array  as $comment) { 
	// print_r($comment['comments']);  // Notice: Undefined index: comments in
	// var_dump($comment);    
	echo '<pre>';print_r($comment);   
	echo '<hr/>';
	echo $comment['name'].'<br/>';
	echo $comment['email'].'<br/>';
	echo $comment['comment'].'<br/>';			
	}

    */
	 
	 
	 
	 
	 
	 
	 
	 
	 
	