<?php
require '../core/classes/dbconnection.php';
$dbConnection = DBConnection::instantiate();
$db = $dbConnection->database;
$page_title = 'Page Views';

// This page runs a MapReduce operation on the access_log collection and display the extracted information in an HTML table
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
												   total_count += doc.count;
												   total_resp_time += doc.resp_time;              
											   });              
											   return {count: total_count, resp_time: total_resp_time }
										  }"
									  );    
// The finalize function determines average response time by dividing the sum of response times by the sum of counters     
$finalize = "function(key, doc) {
                 doc.avg_resp_time = doc.resp_time / doc.count;
                 return doc;
                 }";
// The result of the MapReduce operation is stored in a collection named page_views_last_week. Each document of this collection 
// has the blog ID in its _id field, and the counter and average response time in its values field	
$reduce_output = $db->command( array(
												'mapreduce' => 'access_log', 
												'map' => $map,
												'reduce' => $reduce,
												'query' => array('page' => '/2-MONGO-APPS/article.php', 'viewed_at' => array('$gt' => new MongoDate(strtotime('-1 week') ) ) ),
												'finalize' => new MongoCode($finalize),
												'out'   => 'page_views_last_week'
											   )
                                        );
$results = $dbConnection->getCollection($reduce_output ['result'])->find();

require_once('../core/init.php');
// get an instance of the db to use it to retrieve access_log collection content
$dbConnection = DBConnection::instantiate();

// instantiate Logger  to call getPageViews() method
$obj = new Logger();
              
// $results = $obj->getPageViews();  // Applying a class method to the MAP REDUCE process doestn work... The visit counts dont increase.   ????

$post = new BlogPost();
?>
<?php include_once ('../includes/header.inc.php'); ?>
<?php include_once ('includes/admin_nav.inc.php'); ?>

				<div class="container well ">	
				   <h1>Most viewed articles (Last 7 days)</h1>
                   <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width=" ">Article</th>
                                <th width=" ">Page views</th>
                                <th width="*">Avg response time</th>
                            </tr>
                        </thead>
                        <tbody>
						          <!-- 
 							      'page_views_last_week' has two fileds: id (which is the id of the article) and value.  
							       Value filed at the same time, has count, resp_time, and avg_resp_time
								   -->
                            <?php foreach($results->sort(array('value.count' => -1)) as $result): ?>
                            <tr>
							     <!-- 
								 REMEMBER: 
								 
								 'page_views_last_week'  collection is the result of the MAP REDUCE fuction applied on
								 access_log collection (out ).  The MAP fuction is using  'this.query_params.id', which is the post ID stored on getRequest() method the is called
								 at the end of article.php.
								 
								 'this.query_params.id' is referencing the ID of each viewed post. It is necessary a loop to display the content of  'page_views_last_week'  collection. 
								 
								 In the page_views.php script, we wrote a custom function to fetch the blog title by _id from the articles collection. 
								 articles are sorted  by their page views (sorting on the values.count field) in descending order, and render  an HTML table with their values.
								 
								// getArticleTitle() method fetch the blog title by _id from the articles collection. Then we sorted the articles by their page views (sorting on the
								// values.count field) in descending order, and render an HTML table with their values.  (See the article by id and the application of sort() on the 
								// html table where the values are displayed.
								 -->
                                <td><?php echo $post->getArticleTitle($result['_id']); ?></td>
                                <td ><?php echo $result['value']['count']; ?></td>
                                <td><?php echo sprintf('%f ms', $result['value']['avg_resp_time']); ?></td>
                            </tr>
                            <?php endforeach; 	?>						
                        </tbody>
                    </table>			
				<br/>
				     <h1>Article View Details</h1>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th width=" ">Article Title</th>
                                <th width=" ">Article ID</th>
                                <th width=" ">IP Address</th>
                                <th width=" ">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                             <?php 						
									$postInfo = $dbConnection->getCollection('access_log')->find() ;
									foreach ($postInfo  as $page_views) { 
										$article_id = (isset( $page_views['article_id']  ) ) ? $page_views['article_id'] : null;
							?>
                            <tr>
                                <td><?php echo (isset ($page_views['article_id'] ) ) ? $page_views['article_id'] .'<br/>'  : null; ?></td>
                                <td><?php echo $post->getArticleTitle($article_id); ?></td>
                                <td ><?php echo $page_views['ip_address']; ?></td>
                                <td ><?php echo $page_views['viewed_at']  ; ?></td>  
						<?php }  ?>
                        </tr>							
                    </tbody>
                  </table>
               </div>
             </body>	
       </html>	
<?php	
// $postInfo = $dbConnection->getCollection('access_log')->find() ;
// # $postInfo = $dbConnection->getCollection('access_log')->find(array('article_id' => $result['_id'] ) );
// # var_dump($postInfo );
// foreach ($postInfo  as $page_views) {  var_dump($page_views);  }	

# FOR THESTING AND DEBOGGING
// echo '<div id="contentarea"><div id="innercontentarea">	';

// echo 'Array.result View : '. $reduce_output ['result']."\n"; 
// echo ' <pre>'; print_r( $reduce_output );       
	
// echo '<br/><br/>';


// $pageViews = $dbConnection->getCollection('page_views_last_week')->find();  
// foreach ($pageViews as $pageView) {
	 // echo '<b>MAP REDUCE RESULT -  PAGE VIEWS:</b> <pre>'; print_r($pageView);      
// }
// echo '<hr/>';


// $acessLogs = $dbConnection->getCollection('access_log')->find();
// foreach ($acessLogs  as $acessLog) {
	 // echo '<b>ACCESS LOG PAGE: </b><pre>'; print_r($acessLog); echo '<pre>';      
// }
// echo '<hr/>';


// $rarticles  = $article = $dbConnection->getCollection('articles')->find();
// foreach ($rarticles  as $rarticle) {
     // echo '<b>ARTICLE COLLECTION:</b><pre>'; print_r($rarticle); echo '<pre>';   
// }
// echo '</div>';



// $visits = $dbConnection->getCollection('article_visit_counter_daily')->find();
// foreach ($visits  as $vcd) {
     // echo '<b>VISITE CONTER DAILY COLLECTION:</b><pre>'; print_r($vcd); echo '<pre>';   
// }
// echo '</div>';
?>

	
	
	
	
	
	
