<?php
/* $Header: /cvsroot/tikiwiki/tiki/db/local_multi.php,v 1.6 2004-05-07 20:51:12 mose Exp $

	-----------------------------------------------------------
  -> Multi-tiki trick for virtualhosting

	$tikidomain variable is the setting from apache
	directive ServerName

	-----------------------------------------------------------
	-> Multi-trick for subdirs
	
	You may want to substitute something else instead of
  SERVER_NAME and HTTP_HOST environment variables if you use
	subdirs. For example you can have
	* http://mysite.com/tiki1
	* http://mysite.com/tiki2
	* http://mysite.com/tiki3

	the use 

	$tikipath = split('/',$_SERVER['REQUEST_URI']);
	// or
	$tikipath = split('/',$_SERVER['PHP_SELF']);

	// and
	$tikidomain = $tikipath[1];

	then $tikidomain variable will be the name of the top dir

*/

if (isset($tikidomain_multi) and $tikidomain_multi) {
	if (isset($_SERVER['TIKI_VIRTUAL'])) {
		// declare in apache : SetEnv TIKI_VIRTUAL myvirtualhostname
		$tikidomain = $_SERVER['TIKI_VIRTUAL'];
	} elseif (isset($_SERVER['SERVER_NAME'])) {
		$tikidomain = $_SERVER['SERVER_NAME'];
	} elseif (isset($_SERVER['HTTP_HOST'])) {
		$tikidomain = $_SERVER['HTTP_HOST'];
	} else {
		die("No possible action.");
	}

	$file_multi = "db/$tikidomain/local.php";
	if (is_file($file_multi)) {
		$file_local_php = $file_multi;
	}
}
?>
