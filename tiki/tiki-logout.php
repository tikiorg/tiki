<?php
// Initialization
require_once('tiki-setup.php');
$userlib->user_logout($user);
session_unregister("user");
session_destroy();
unset($user);
header("location: $tikiIndex");
die;
?>