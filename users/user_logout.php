<?php
require_once('../classes/session.php');
require_once('../classes/user.php');

$user = new User();
$user->logout();

header('location: user_login.php');
exit;