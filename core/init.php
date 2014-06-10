<?php 
	require_once 'classes/SessionManager.php';  // If dbconnection.php is included here, it seems that the rest of the classes inherite the connection
	require_once 'classes/dbconnection.php';
	require_once 'classes/log.php';
	require_once 'classes/Author.class.php';
	require_once 'classes/ImageHandler.class.php';
	require_once 'classes/BlogPost.php';
	require_once 'classes/PostComment.php';
	require_once 'classes/Validator.php';
	require_once 'classes/Form.class.php';


	// Instatiate classes
	$author = new Author ();
	$article = new BlogPost ();
	$comment = new PostComment ();

	
	/*
	$classes_directories = array ('/',  './',  '../',  '../../' );
	foreach ($classes_directories as $directory) {

		switch($directory) {
			case '/':  require_once('autoLoader.php'); 
			break;
				
			case './':  require_once('./autoLoader.php'); 
			break;
					
			case '../':  require_once('../autoloader.php'); 
			break;
		}

	}
		
	*/
	

	ob_start(); // Added to avoid a common error of 'header already sent'
	
	$root_dir = str_replace('\\', '/', __DIR__ );
	#var_dump($dir );
	//echo $root_dir .'<br/>';

	define('ROOT', $root_dir. '/');
	#var_dump(ROOT);	
	
	$dir_path = str_replace('\\', '/', __FILE__);
	#var_dump($dir_path);	
	//echo $dir_path.'<br/>';

    // Set up the page title and CSS files
	$directories = array ('./',  '../' );
	
	$css_files =    array( 
								//'assets/css/bootstrap-responsive.min.css', 
								'assets/css/bootstrap.min.css', 
								'assets/css/blog-home.css', 
								);

	/*
	foreach ($directories as $directory) {
		foreach ($css_files  as $css ) {
		   $css_path = $directory.$css; // this is an array of files
			#var_dump($css_path);				
		}
	}
	
	*/
	
	// TEST			 		 
	#$path_parts = pathinfo( $css_path ;
	#var_dump($path_parts);
	// echo $path_parts['dirname'], "\n";
	// echo $path_parts['basename'], "\n";
	// echo $path_parts['extension'], "\n";
	// echo $path_parts['filename'], "\n"; // since PHP 5.2.0



