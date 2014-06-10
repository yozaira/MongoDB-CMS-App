<?php
require_once('../core/init.php');      // SessionManager class is included on Author class
$author->logout();

header('location: index.php');
exit;