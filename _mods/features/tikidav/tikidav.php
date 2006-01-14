<?php
/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 12/02/2004
* @copyright (C) 2005 the Tiki community
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/
	include "lib/tikidav/tiki-webdav_server.php"; 
  	
  	$server  = new TikiDAV_Server();
    	$server->ServeRequest(); 
?>