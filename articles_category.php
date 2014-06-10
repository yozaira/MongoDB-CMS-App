<?php
require_once('classes/autoloader.php'); 
$mongo = DBConnection::instantiate();
//get an instance of MongoDB object
$db = $mongo->database;

$result = $db->command(array('distinct' => 'articles', 'key' => 'category'));

?>
 <html>
        <head>
            <title>Categories</title> 
            <link rel="stylesheet" href="style.css"/>

        </head>
        <body>
            <div id="contentarea">
                <div id="innercontentarea">
                    <h1>Distinct Categories</h1>
                    <ul>
                    <?php foreach($result['values'] as $value): ?>
                    <li><?php echo $value; ?></li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            </div>

        </body>
 </html>
	
			<br/>
		   <div id="contentarea">
                <div id="innercontentarea">		
		     </div>   
	      </div>
		  
		         <?php
				// echo '<pre>';
				// print_r($result);
			    ?>
		  
		  