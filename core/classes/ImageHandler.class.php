<?php
// Image Processing and GD  http://www.php.net/manual/en/book.image.php

		require_once 'dbconnection.php';
		require_once('autoLoader.php');   // why is not loading DBConnection class?


		class ImageHandler  {
			const COLLECTION = 'imageMetadata';					
			protected $_mongo;
			protected $_collection;
			protected $_metadata;
			protected $_error_msg;	
			protected $img_error;	
			
			public $save_dir;
			
			// Define maximum dimensions for uploaded images. 
			public $max_dims;

			public function __construct($save_dir, $max_dims=array(350, 240) ) {		
				$this->_mongo = DBConnection::instantiate();
				$this->_collection = $this->_mongo->getCollection(ImageHandler::COLLECTION);	// if it doesnt exits, the collection will be created
				
				$this->save_dir = $save_dir;
				$this->max_dims = $max_dims;
				$this->_error_msg = array();
				$this->img_error = null;
			}
				
	
				/**
				* Resizes/resamples an image uploaded via a web form
				*
				* @param array $file the array contained in $_FILES
				* @param bool $rename whether or not the image should be renamed 
				* @return string the path to the resized uploaded file
				*/
			public function processUploadedImage($file, $rename=TRUE)   {   
			
				// Separate the uploaded file array. array_values — Return all the values of an array.      http://us2.php.net/array_values
				list($filename, $filetype, $tmp, $err, $size) = array_values($file);

				// Before trying to process the file,  make sure that your $err value is equivalent to UPLOAD_ERR_OK
				
				if ($err == UPLOAD_ERR_OK) {			  
				 
					// Private method that generates a resized image
					$this->doImageResize($tmp);
				
					// Rename the file if the flag is set to TRUE
					if ($rename === TRUE) {
						// Retrieve the extension of the image
						$img_ext = $this->getImageExtension($filetype);
						$filename = $this->renameFile($img_ext);
					}
							
					// Check that the directory exists
					$this->checkSaveDir();

					// Create the full path to the image for saving
					$filepath = $this->save_dir . $filename;

					// Store the absolute path to move the image
					$absolute = $_SERVER['DOCUMENT_ROOT'] . $filepath;

					// Save the image
					$success =  move_uploaded_file($tmp, $absolute);	
					if ($success ) {
						// Create document array with the metadata						 
						try {									
							$this->_metadata['img_name'] = $filename;
							$this->_metadata['img_type'] = $filetype;
							$this->_metadata['img_caption'] = $_POST['caption'];
							$this->_metadata['file_dir'] = $this->save_dir;
							$this->_metadata['relative_path'] = $filepath;
							$this->_metadata['absolute_path'] = $absolute;
							$this->_metadata['img_size'] = $size;
							$this->_metadata['img_dimensions'] = $this->max_dims;
							$this->_metadata['renamed'] = ($rename= TRUE) ? 'Yes': 'No' ;
							$this->_metadata['uploaded_date'] = new MongoDate();
									
							$this->_collection->save($this->_metadata);							
						} 
						catch(MongoException $e) {
							die('Failed to insert data '.$e->getMessage());
						}
					}
					else { $this->img_error = $this->getImgErrors($err);}
				} 
				else {   
						// If image could not be saved, retrieve image erros using getImgErros() method, and then pass the result to img_error array .
						$this->img_error = $this->getImgErrors($err);
						return false;
						// If an error occurred, throw an exception
						//throw new Exception('An error occurred with the upload!  ERROR: '.$this->getImgErrors($err) );           // for debugging
				}
				return $filename;
			}
			
	

			// This returns the the image erros that were passed to img_error array using getImgErros() method
		    public function dispayImgErrors() {	
				if (isset ($this->img_error ) ) {				
					return $this->img_error;	  
				}				
		    }
	

	
			private function getImgErrors($error)  {		
				 
				       switch ($error) {
								case 1;
									$this->_error_msg = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
									break;
								case 2:
									$this->_error_msg = "Image Error: The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
									break;
								case 3:
									$this->_error_msg = "Image Error: The uploaded file was only partially uploaded";
									break;
								case 4:
									$this->_error_msg = "Image Error: No file was uploaded";
									break;
								case 5:
									$this->_error_msg = "Image Error: Missing a temporary folder";
									break;
								case 7:
									$this->_error_msg = "Image Error: Failed to write file to disk";
									break;
								case 8:
									$this->_error_msg = "Image Error: File upload stopped by extension";
									break;
								default:
									$this->_error_msg = "Image Error: Unknown upload error";											
					    }
				        return $this->_error_msg;
               }
		

				/**
				* Generates a unique filename for a file
				*
				* Uses the current timestamp and a randomly generated number
				* to create a unique filename to be used for an uploaded file.
				* This helps prevent a new file upload from overwriting an
				* existing file with the same filename.
				*
				* @param string $ext the file extension for the upload
				* @return string the new filename
				*/	
				private function renameFile($ext)  {
					/*
					 * Returns the current timestamp and a random number
					 * to avoid duplicate filenames
					 */
					return time() . '_' . mt_rand(1000,9999) . $ext;
				}

	
				/**
				* Determines the filetype and extension of an image
				*
				* @param string $filetype the MIME type of the image
				* @return string the extension to be used with the file
				*/
				private function getImageExtension($filetype) {
				
				switch($filetype) {
					case 'image/gif':
						return '.gif';
						break;
					case 'image/jpeg':
					case 'image/pjpeg':
						return '.jpg';
						break;
					case 'image/png':
						return '.png';
						break;
					default:
						//throw new Exception('This file is not in JPG, GIF, or PNG format!');
						$this->_error_msg = 'This file is not in JPG, GIF, or PNG format!';
				}
			}


			/**
			* Ensures that the save directory exists
			*
			* Checks for the existence of the supplied save directory,
			* and creates the directory if it doesn't exist. Creation is
			* recursive.
			*
			* @param void
			* @return void
			*/
			private function checkSaveDir()
			{
				// Determines the path to check
				$path = $_SERVER['DOCUMENT_ROOT'] . $this->save_dir;

				// Checks if the directory exists
				if(!is_dir($path))
				{
					// Creates the directory
					if (!mkdir($path, 0777, TRUE))
					{
						// On failure, throws an error
						throw new Exception("Can't create the directory!");
					}
				}
			}
			

			
				/**
				* Determines new dimensions for an image
				*
				* It is used by doImageResize() private method
				*
				* @param string $img the path to the upload
				* @return array the new and original image dimensions
				*/			
			private function getNewDims($img)   {
			
				if (!empty($img) ) {
						// Use the list() function to define the first two array elements as the $src_w (source width) and $src_h (source height) variables. 
						// getimagesize() function returns the width and height of the image supplied as an argument (as well as other information.
						list ($src_w, $src_h) = getimagesize($img);		
			
						// Use property variable with the maximum dimensions. Use the list() function to separate their values into $max_w (maximum width) and $max_h (maximum height)	
						list($max_w, $max_h) = $this->max_dims;

						 // Check that the image is bigger than the maximum dimensions
						if($src_w > $max_w || $src_h > $src_h)
						{
							 // Determine the scale to which the image should be scaled
							$s = ($src_w > $src_h) ? $max_w/$src_w : $max_h/$src_h;
						}
						else  {	
								/*
								 * If the image is smaller than the max dimensions, keep
								 * its dimensions by multiplying by 1
								 */
							$s = 1;
						}
						// Get the new dimensions
						$new_w = round($src_w * $s);
						$new_h = round($src_h * $s);
						
						// Return the new dimensions
						return array($new_w, $new_h, $src_w, $src_h);
				}

			}


			/**
			* Determines how to process images
			*
			* It is used by doImageResize() private method to determine what func to use
			*
			* Uses the MIME type of the provided image to determine
			* what image handling functions should be used. This
			* increases the perfomance of the script versus using
			* imagecreatefromstring().
			*
			* @param string $img the path to the upload
			* @return array the image type-specific functions
			*/
			private function getImageFunctions($img)  {
			
				if (!empty($img) ) {
					$info = getimagesize($img);

					switch($info['mime'])
					{
						case 'image/jpeg':
						case 'image/pjpeg':
							return array('imagecreatefromjpeg', 'imagejpeg');
							break;
						case 'image/gif':
							return array('imagecreatefromgif', 'imagegif');
							break;
						case 'image/png':
							return array('imagecreatefrompng', 'imagepng');
							break;
						default:
							return FALSE;
							break;
					}
				}
			}
			
			
			
				/**
				* Generates a resampled and resized image
				*
				* Creates and saves a new image based on the new dimensions
				* and image type-specific functions determined by other
				* class methods.
				*
				* @param array $img the path to the upload
				* @return void
				*/
			private function doImageResize($img)   {
			
				if (!empty($img) ) {
					// Determine the new dimensions
					$d = $this->getNewDims($img);

					// Determine what functions to use
					$funcs = $this->getImageFunctions($img);

					// Create the image resources for resampling.  
					$src_img = $funcs[0]($img);

					$new_img = imagecreatetruecolor($d[0], $d[1]);

					// Copy the original image into the new image resource using  imagecopyresampled() function
					if ( imagecopyresampled(  $new_img, $src_img, 0, 0, 0, 0, $d[0], $d[1], $d[2], $d[3]  ))  {
					
						// Check whether the imagecopyresampled() call is successful, then destroy the source image ($src_img) to free system 
						imagedestroy($src_img);
						
						if ($new_img && $funcs[1]($new_img, $img))
						{
							imagedestroy($new_img);
						}
						else  {
								 throw new Exception('Failed to save the new image!');
						}
					}
					else {
							throw new Exception('Could not resample the image!');
					}
					
				}
			}
			
	  
			  // This method retrieve the metadata stored in imageMetadata collection of all images 
			  public function getImageMetadata()  {
					$images_info = $this->_collection->find();      
					return $images_info;				
			  }
			
			
			
			
			/**
			* Retrieve the metadata stored in imageMetadata collection of all images 
			* Uses images id to retrieve the metadata stored in imageMetadata collection retated to that specific image.
			 * returns value (a cursor) that will allow to display the images stored in the  images upload directory
			 * @ param string $img_id the id of the selected image
			 */
			public function getSelectedImge($img_id = null ) {						
					$query = array('_id' => new MongoId($img_id) );
					$img_info = $this->_collection->findOne($query);
					// it returns a cursor. 
					return $img_info;		
			}
						
						
			/**
			* Delete image
			* remove() method takes an array as its parameter, which it uses to query the document it is going to delete. 
			 * @ param string $id the id of the image to be deleted
			 */							
			public function deleteImage($id = null ) {	
			
					 # If multiple documents match the query, all of them are deleted. If no query argument is passed, remove() will delete 
					 # all documents in the collection.
					 $this->_collection->remove(array('_id' => new MongoId( $id )));	
										  
					# The thing you should know about remove() is that it does not alert you when it fails. To verify whether the file deletion 
					 # was actually successful, you can call the lastError() method on MongoDB object, right after calling remove() and see 
					 # if it returns any error message:
					$error = $this->_mongo->database->lastError();
					if (isset($error['err']) ) {
						throw new Exception ('Error deleting image '.$error['err'] );
					}									  
								
			}
			

			public function deleteFromDir($id = null, $img_dir = null) {	#  This method is in progress. Needs more testing

					// Get images metadata
					$img_cursor = $this->getImageMetadata();

					# Test
					// echo '<pre>';print_r($img_cursor );
					foreach ($img_cursor  as $img ) { 
					$img_name = $img['img_name'] ; 
					$img_id = $img['_id'] ; 
					//$img_dir = $img['file_dir'] ; 
					//echo  $img_name;  
					}

					if (!is_dir($img_dir) ) { throw new Exception ('Directory '.$img_dir. ' was not found'); }
					if ($img_id = $id) { 
						unlink($img_dir.$img_name);
					} 
					else { 
							throw new Exception ('There is no image with ' .$id. 'in this collection');
					}
					  
					// if (is_file( $img_name ) ) {
						// unlink($this->save_dir.$img_name);
					// }

					# $this->_collection->remove(array('_id' => new MongoId( $id )));									  
					// $error = $this->_mongo->database->lastError();
					// if (isset($error['err']) ) {
						// throw new Exception ('Error deleting image '.$error['err'] );
					// }									  				
			}			
						
						


	} // end class

	
# TESTING:		
// $img_id = '52eda11e8532578c0a000045';
// $images = new ImageHandler();

// # Call the method that retrives the requested image
// $img_obj = $images->getImageBytes($img_id );
// echo '<pre>';print_r($img_obj);
// echo '<pre>';print_r ($img_obj->file);
// echo $img_obj->file['filetype'];

// $img_chunks = $images->getImageChunks($img_id );
// # echo '<pre>';print_r($img_chunks );    // mongo object
// # output the data in chunks
// foreach ($img_chunks as $chunk ) {  
// echo '<pre>';print_r($chunk);         // chunk data
 // # echo $chunk['data']->bin;           // binary data
// }
		
?>


