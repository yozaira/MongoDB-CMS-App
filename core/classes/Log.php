<?php
require_once('autoLoader.php'); 

define('LOGNAME', 'access_log');


/**
* Handles the request logging logic. 
*
*/
class Logger {
    private $_dbconnection;
	private $_collection;
    private $_db;
    
    public function __construct()  {
        $this->_dbconnection = DBConnection::instantiate();
        $this->_collection = $this->_dbconnection->getCollection(LOGNAME);  // reference to the access_log collection.
		$this->_db = $this->_dbconnection->database;
    }

	
	
	/**
	*
	* Obtains the HTTP request information by accessing the $_SERVER super global array. 
	* Splits the query string and stores the parameters and their values in an array.
	* Merges the data with any additional data received as arguments and inserts it in a capped collection.
	* @ param string $data additional data
	*
	*/
    public function logRequest($data = array()) {
        $request = array();
        
        $request['page']        =   $_SERVER['SCRIPT_NAME'];
        $request['viewed_at']   = new MongoDate($_SERVER['REQUEST_TIME']);
        $request['ip_address']  = $_SERVER['REMOTE_ADDR'];
        $request['user_agent']  = $_SERVER['HTTP_USER_AGENT'];
		
        if (!empty($_SERVER['QUERY_STRING'])){
            $params = array();
            
            foreach(explode('&', $_SERVER['QUERY_STRING']) as $parameter) {
                
                list($key, $value) = explode('=', $parameter);
                $params[$key] = $value;
            }
            
            $request['query_params'] = $params; 
        }
        
        //adding addtional log data, if any
        if (!empty($data)) {
            $request = array_merge($request, $data);
        }
        
        $this->_collection->insert($request);
    }
    

	    public function log($data){
			$this->_collection->insert($data);
			return;
		}
	
	
	
			public function getIP($pid){
			    // $query = array('query_params' => array('id' => $pid )  );
				// $query = array('ip_address' => '127.0.0.1' );
			    // $query = array('article_id' => new MongoId($pid)  );   // The query does not work using new MongoId method  ????
				$query = array('article_id' => $pid );
				// $result = $this->_collection->findOne($query );
				$result = $this->_collection->find($query )->getNext();
				return $result['ip_address'];
			}
	
	
			public function updateVisitCounter($articleId){
				$articleVistiCounterDaily = $this->_dbconnection->getCollection('article_visit_counter_daily');   //  collection to store information 
				
				$criteria = array(
								'article_id' => new MongoId($articleId), 
								'request_date' => new MongoDate(strtotime('today'))
							);
				
				$newobj = array('$inc' => array('count' => 1));
				
				$articleVistiCounterDaily->update($criteria, $newobj, array('upsert' => True));
			}


			public function getPageViews()  {

						// access_log collection and its elements (query_parms, response_time_ms...) were created on Logger class.  See this class to understand the process
						$map = new MongoCode( "function() { 
																emit( this.query_params.id, { count: 1, resp_time: this.response_time_ms} ); 
																};"
															  );														  
						// The reduce function sums up the counter and response time values.
						$reduce = new MongoCode("function(key, values) { 
																	  var total_count = 0;
																	  var total_resp_time = 0;      														  
																	  values.forEach ( function(doc) {
																		   total_count = doc.count + doc.count;
																		   total_resp_time += doc.resp_time;              
																	   });              
																	   return {count: total_count, resp_time: total_resp_time}
																	}"

																  );							 
						// The finalize function determines average response time by dividing the sum of response times by the sum of counters     
						$finalize = "function(key, doc) {
										 doc.avg_resp_time = doc.resp_time / doc.count;
										 return doc;
										 }";
						   
						$reduce_ouput = $this->_db->command( array(
																							'mapreduce' => 'access_log', 
																							'map' => $map,
																							'reduce' => $reduce,
																							'query' => array('page' => '/2-MONGO-APPS/article.php', 'viewed_at' => array('$gt' => new MongoDate(strtotime('- 7 days') ) ) ),
																							'finalize' => new MongoCode($finalize),
																							'out'   => 'page_views_last_week'
																						   )
																			);
							// 'page_views_last_week' has two fileds: id (which is the id of the article), and the value field.  
							// Value filed at the same time, has 'count', resp_time', and  'avg_resp_time' ( the variables resulted of the reduce and finalized functions )
															
							// var_dump($reduce_ouput);												
							// echo $reduce_ouput['result'];	
							// $results = $dbConnection->getCollection('page_views_last_week')->find();

							 // $results = $this->_dbconnection->getCollection( $reduce_ouput['result'] )->find();
							// return $results;			
						    return $reduce_ouput ;			
		}
	
	
}

