<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo $page_title; ?></title>

<!-- Bootstrap and Custome CSS -->
<!-- in case there is a conflict or problem with the seesion and headers, create a file with the function that displayes the css and include it here  -->
<?php foreach ($directories as $directory) {  ?> 
<?php  foreach ($css_files  as $css ) {   ?> 
            <link rel="stylesheet" type="text/css" href="<?php echo  $directory.$css ?>" />  <!-- css path files are  displayed on init.php  -->
<?php }  } ?>
<!--[if lt IE 9]>
<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
  
<!-- Bootstrap Icones -->
<link href="http://netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.min.css" rel="stylesheet">
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>

<script src="assets/js/voting.js"></script> 
  
<script type="text/javascript" charset="utf-8">
  function confirmDelete(articleId) {
  var deleteArticle = confirm('Are you sure you want to delete this article?');
   if (deleteArticle){
    window.location.href = 'delete_post.php?id='+articleId;
  }
  return;
  }
</script>

<style> 
</style>  

</head> 
<body>   
<?php //include_once ('includes/navbar.inc.php');    ?>
