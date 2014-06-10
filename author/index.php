<?php  
require_once('../core/init.php');

 if ($author->isLoggedIn()) {  
    $page_title = $author->name;
}
include_once ('../includes/header.inc.php');
include_once ('author_nav_menu.php');   

include_once 'author_private_profile.php';

include_once '../includes/footer.inc.php'; 

?>

 
 
 