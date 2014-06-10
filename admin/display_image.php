<?php
require_once('../core/init.php');

$id = $_GET['id'];

$img_dir = 'images/';	     // OJO
$img_obj = new ImageHandler($img_dir, array(100, 100) );

# Call the method to get all image metadata stored in imagesMetadata collection
$img = $img_obj->getSelectedImge($id);
echo "<img src=". $img_dir.$img['img_name'] .">";

# Test
// echo '<pre>';print_r($img_obj->getSelectedImge($id));
var_dump($img['absolute_path']);
var_dump($img['relative_path']);
var_dump($img['file_dir']);


// $mongo = DBConnection::instantiate();
// $gridFS = $mongo->database->getGridFS();
// $object = $gridFS->findOne(array('_id' => new MongoId($id)));

#  TESTING OBJECTS:
// The query returns a document that shows the filename, type, and caption of the uploaded image. These are the fields that we explicitly 
// set while storing the file. It also has the size of the file (length), time of upload (uploadDate), size of each chunk (chunkSize), and the 
// MD5 hash of the file. These fields are set by MongoDB itself.
# echo '<pre>';print_r($gridFS);      // same object name and array elements as one used in getImageBytes() methos.
// echo '<pre>';print_r($object);
// echo '<pre>';print_r($object->file);
// echo $object->file['filetype'];
// echo '<hr/>';
 
// # Deactivate when testing:
// header('Content-type: ' .$object->file['filetype']);
// echo $object->getBytes();

/* =================================================================================      

// Now, let's take a look at chunks:
// chunks will have one or more documents (depending on file size) associated with a file. The files_id field refers to the _id of the document 
// in files. n shows the position of the chunk in the set of chunks (if n is zero then it is the first chunk). And data obviously stores the file content.
$chunks = $mongo->database->fs->chunks->find( array('files_id' => $object->file['_id'] ) )->sort(array('n' => 1));
# echo '<pre>';print_r($chunks);            // mongo object

 # Deactivate when testing:
header('Content-type: '.$object->file['filetype']);

# output the data in chunks  
foreach ( $chunks as $chunk ) {  
	# echo '<pre>';print_r($chunk);        // chunk data
	echo $chunk['data']->bin;          	   // binary data
 }

//=================================================================================      */

 // require_once('../core/init.php');
// require_once('../core/classes/ImageHandler.class.php');
// $images = new ImageHandler();
# Call the method that retrives the requested image
// echo $images->getImageBytes($id);
// echo $images->getImageChunks($id);
// echo '<pre>';print_r($images->getImageChunks($id) );

// Test object
// echo '<pre>';print_r($img_obj);
// echo '<pre>';print_r($img_obj->file);
// echo $img_obj['filetype'];
// echo $img_obj['caption'];

# Deactivate when testing:
# header('Content-type: ' .$img_obj->file['filetype'] );
#echo $img_obj->getBytes();


?>