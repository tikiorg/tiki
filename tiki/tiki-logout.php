<?php
// Initialization
require_once('tiki-setup.php');
session_unregister("user");
session_destroy();
unset($user);
header("location: $tikiIndex");
die;
?>