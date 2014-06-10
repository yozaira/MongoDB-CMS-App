<?php
require('../core/classes/dbconnection.php');

// In this example, we are going to calculate the average rating each author received for his or her articles—published within the 
// last 24 hours—using the group() method.  We are going to execute this method within a PHP program. The program will output 
// a HTML table, displaying the total number of articles and average rating for each author. page 121

$mongo = DBConnection::instantiate();
$collection = $mongo->getCollection('articles');    // page 107

/*
 group() takes the following parameters:

--  key: Specifies the key or set of keys by which the documents will be grouped.
-- initial: The base aggregator counter, specifies initial values before aggregation.
-- reduce: A reduce that aggregates the documents. It takes two arguments, the current 
   document being iterated over, and the aggregation counter.  
  
   In addition to these, group() can also receive the following optional arguments:
   
	-- cond: A query object. Only the documents matching this query will be used in  grouping.
	-- finalize: A function that runs on each item in the result set (before returning the  item). 
	   It can either modify or replace the returning item.
*/


//  key specifies the key or set of keys by which the documents will be grouped.
// We supplied array('author' => 1) as the key parameter to group articles by author names.
$key = array ('title' => 1);

//  initial: The base aggregator counter, specifies initial values before aggregation.
// set both the aggregation counter and total rating to zero ratings
$initial = array ('count' => 0, 
					   'total_rating' => 0
					 );
						
// reduce: A reduce that aggregates the documents. It takes two arguments, the current document being iterated over, and the aggregation counter.  
//
// $reduce iterates through the document, increments the count field of the aggregation counter by 1, and adds the rating of the current 
// document to the total_rating field of the counter
$reduce = "function( obj, counter) {
					counter.count++; 
					counter.total_rating += obj.rating;
				}";

// finalize: A function that runs on each item in the result set (before returning the  item).  It can either modify or replace the returning item.					
//finalize function, finds the average rating.
//
// $finalize calculates the average rating by dividing the total rating with the counter and rounding off the quotient.
$finalize = "function(counter) { 
					counter.avg_rating = Math.round(counter.total_rating / counter.count);
				}";

// cond: A query object. Only the documents matching this query will be used in  grouping.					 
// query condition, selects the documents created over last 24  hours for running the group().
//
// The $condition argument selects the articles that have been published within the last 24 hours to run the group operation on.

#$condition = array('saved_at' => array('$gte' => new MongoDate(strtotime('1 day'))));

// group() can be viewed as a short-circuit approach for doing MapReduce.

// We passed all these parameters to the group() method on the MongoCollection object, which returns the result in an array:
$result = $collection->group( $key, $initial, new MongoCode( $reduce),
																						  array( 'finalize' => new MongoCode($finalize)
																								   # 'condition' => $condition
																								 )
											);
											
// The result of aggregation is contained in the retval field of $result. We iterated through this field to display the result in an HTML table.

//echo ' ARTICLE PER PAGE: <pre>'; print_r($result); 
// var_dump($result['retval']); 


?>
<html>
    <head>
        <title>Articles Rating</title> 
        <link rel="stylesheet" href="style.css"/>
    </head>
    <body>
        <div id="contentarea">
            <div id="innercontentarea">
                <h1>Articles' Ratings</h1>
                <table class="table-list" cellspacing="0" cellpadding="0">
                    <thead>
                        <tr>
                            <th width="50%">Title</th>
                            <th width="24%">Articles</th>
                            <th width="*">Average Rating</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($result['retval'] as $obj): ?>
                        <tr>
                            <td><?php echo $obj['title']; ?></td>
                            <td><?php echo $obj['count']; ?></td>
                            <td><?php echo $obj['avg_rating']; ?></td>
                        <tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </body>
	
						
</html>

        <div id="contentarea">
            <div id="innercontentarea">
	                        					
			</div>
        </div>			
							