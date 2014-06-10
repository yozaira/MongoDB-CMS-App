<?php
require('../core/classes/dbconnection.php');

$mongo = DBConnection::instantiate();
$collection = $mongo->getCollection('articles');    // page 107

//  key specifies the key or set of keys by which the documents will be grouped.
// We supplied array('author' => 1) as the key parameter to group articles by author names.
$key = array ('title' => 1);

// initial: The base aggregator counter, specifies initial values before aggregation.
// set both the aggregation counter and total rating to zero ratings
$initial = array ('count' => 0, 
             'total_rating' => 0
           );

$reduce = "function( obj, counter) {
          counter.count++; 
          counter.total_rating += obj.rating;
        }";

$finalize = "function(counter) { 
          counter.avg_rating = Math.round(counter.total_rating / counter.count);
        }";
$result = $collection->group( $key, $initial, new MongoCode( $reduce),
                              array( 'finalize' => new MongoCode($finalize)
                              # 'condition' => $condition
                              )
                            );

?>
<html>
  <head>
  <title>Articles Rating</title> 
  <link rel="stylesheet" href="style.css"/>
  </head>
<body>
  <div>
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
</body>
</html>
