<?php
require_once('../core/init.php');
$page_title = 'Images List';
$img_dir = 'images/';	
$img_obj = new ImageHandler($img_dir );
// Call the method that list all images
$img_cursor = $img_obj->getImageMetadata();

# FOR TESTING:
// echo '<pre>';print_r($img_cursor );
// foreach ($img_cursor  as $img ) {	echo '<pre>';print_r($img ); }
// $id = '52efb57c853257ec15000045';
 // if ($img['_id'] = $id) { echo "There is a match: ".$img['img_name'] ; } else { echo 'No match';   }
 // if ($img['_id'] = $id) { $img_name = $img['img_name'] ;  echo $img_name; } else { echo 'No match';   }
 // foreach ($img_cursor  as $img ) { $img_name = $img['img_name'] ; echo  $img_name;  }
?>

<?php include_once ('../includes/header.inc.php'); ?>
<?php include_once ('includes/admin_nav.inc.php'); ?>

        <div class="container well ">	  
            <div>		
                <h1>Uploaded Images</h1>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Filename</th>
                            <th>Directory</th>
							 <th>Size</th>
							<th>Dimensions</th>
							<th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
						<?php while ($img_cursor->hasNext()):  $image = $img_cursor->getNext(); ?>						
							   <?php  							   					
								# TEST OBJECT:
								// echo '<pre>';print_r($image);		  								
								// echo '<pre>';print_r($image['_id'] );		  								
								?>						
                         <tr>
                            <td>
							   <a href="display_image.php?id=<?php echo $image['_id'];  ?>"> 	
							     <?php echo $image['img_name']; ?></td>
							   </a>	
                            <td><?php echo $image['file_dir']; ?></td>
							
                            <td ><?php echo ceil( $image['img_size'] / 1024).' KB'; ?></td>
							
                            <td ><?php  echo $image['img_dimensions'][0].  ' x ' .$image['img_dimensions'][1]; ?></td>
							
                            <!-- <td ><?php //echo $image['img_caption'] ; ?></td>-->
						    <td>
							  | <a href="delete_image.php?id=<?php echo $image['_id'] ; ?> " onclick="return confirm('Are you sure you want to delete this Image?'); " >Delete Metadata </a> |
							  | <a href="delete_image_dir.php?id=<?php echo $image['_id'] ; ?> " onclick="return confirm('Are you sure you want to delete this Image?'); " >Delete from Directory </a> |
						   </td>			
                        </tr>
                        <?php endwhile;?>
                    </tbody> 
              </table>
            </div>
        </div>
    </body>
</html>




