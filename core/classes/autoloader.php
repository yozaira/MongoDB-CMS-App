<?php
# define('ROOT', __DIR__ . '/');  
/*

function __autoload($classname) {
  // include_once(ROOT . strtolower($classname) . ".class.php");
  // include_once(ROOT . "/classes/" . strtolower($classname) . "class.php"); // if _DIR_  is defined in the root in the autoload func, there is no need to include the folder name where the autoload.php is i
  // include_once(ROOT . strtolower($classname) . ".class.php");
  include_once(ROOT .$classname. ".class.php");
}
spl_autoload_register('__autoload');

  */

/*
// Define autoloader
function __autoload($classname) {
    if (file_exists( ROOT .$classname. ".class.php")) 
    include_once(ROOT .$classname. ".class.php");
  // else throw new Exception('Class "' . $classname . '" could not be autoloaded');
  
  // $file =  './classes/'.$classname . '.class.php';
  // var_dump($file );
   var_dump( ROOT .$classname. ".class.php");
}

# print_r( __DIR__ );
# print_r(ROOT );
var_dump( ROOT );
var_dump( __DIR__ );
  
*/
  
  
  
/*
function autoLoader($className) {
// function __autoload($className) {
  $filename = "classes/" . $className . ".class.php";
   if (is_readable($filename)) {
    require $filename;
     var_dump($filename);
   }
}
// spl_autoload_register('__autoload');
 spl_autoload_register(' autoLoader');
*/



function autoLoader($className) {

    $directories = array ('/', '', 'classes/', './classes/',  '../classes/',  '../../') ;
    // add your file naming formats.  sprintf — Return a formatted string.  http://www.php.net/manual/en/function.sprintf.php
    $namingFormats = array ( '%s.php', '%s.class.php', 'class.%s.php', '%s.inc.php' );
    
    // OJO  is not retrieving class.php extension, just php
    
    #var_dump( $directories);
    #var_dump($namingFormats);
      
    foreach ($directories as $directory) {
      foreach ($namingFormats  as $namingFormat ) {
        $path = $directory.sprintf($namingFormat, $className);
        #$path = DIRECTORY. sprintf($namingFormat, $className);
        if (file_exists($path) ) {
          // TEST
          # var_dump($path);             
          #$path_parts = pathinfo($path);
          #var_dump($path_parts);
          // echo $path_parts['dirname'], "\n";
          // echo $path_parts['basename'], "\n";
          // echo $path_parts['extension'], "\n";
          // echo $path_parts['filename'], "\n"; // since PHP 5.2.0
 
          include_once($path);
          return;
        }
      }
    }    
}

spl_autoload_register("autoLoader");

// $posts = new BlogPost ();
// print_r($posts);

    
?>






