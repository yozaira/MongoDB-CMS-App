<?php
require_once('../core/init.php');
// We edited the dashboard page HTML to display a Delete link in each row. We added some JavaScript code to the page so that when we click 
// the delete link, a pop-up box asks for confirmation. Clicking OK takes us to the page delete.php, which deletes the article and shows us a confirmation message.

$id = $_GET['id'];

// call deletePost() to execute the remove
$article->deletePost($id);
header('Location: index.php');
exit;

?>
<!--
<p>Article deleted. _id: <?php //echo $id;  ?>.  <a href="index.php">Go back to dashboard?</a>
-->