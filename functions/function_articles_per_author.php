<?php
require('../classes/dbconnection.php');
$mongo = DBConnection::instantiate();
// get an instance of MongoDB object
$db = $mongo->database;


		//function count_articles_per_author() {
		function count_articles_per_author($id= NULL) {

				global $db;

				$map = new MongoCode(  "function() { 
															 emit ( this.author_name, 1 ); 
														   }"
													   );											
				// Now, define the reduce function:
				$reduce = new MongoCode( "function(key, values) {
																	 var count = 0;
																	 for (var i = 0; i < values.length; i++){
																		count += values[i];
																	  } 
																	  return count;
															  }"												  
														   );

				// Apply the MapReduce operation on the articles collection using the next command:
				$id = '52d3da158532577c1e000066';	
				
				$command =  array ( 
												 'mapreduce' => 'articles',
												  'query' => array('author_id' => $id),
												 // 'query' => array('author_name' => 'Luke Skywalker'),
												  'map' => $map,
												  'reduce' => $reduce,
												  'out' => 'articles_per_author'
												);					
					$results = $db->command($command);
					
					return $results;
			}
	

	 // test the function:
	 $count_results = count_articles_per_author();
	 echo ' ARTICLE PER PAGE: <pre>'; print_r(count_articles_per_author() ); 
	 
     // load all the tags in an array, sorted by frequenct:
	 echo 'RESULT COLLECTION NAME: '. 	$count_results['result']."\n";
	 echo '<br/>';

	 // Test the output collection:
	$collection = $mongo->getCollection('articles_per_author')->find();	// Momgo cursor object
	#$collection = $mongo->getCollection('count_author_articles');
	#$cursor = $collection->find();	
									   
	while($collection->hasNext()) {
		 $article_per_author = $collection->getNext(); 
		 print_r( $article_per_author );;
	 }
	 
