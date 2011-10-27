<?php
$_SERVER['HTTP_USER_AGENT'] = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
 
require_once ('tiki-setup.php');
$tbp = new HtmlFeed();
print_r(json_encode(
	$tbp->feed()
));
die;
