<?
/**
* CPAINT (Cross-Platform Asynchronous INterface Toolkit)
*
* http://sf.net/projects/cpaint
* 
* released under the terms of the GPL
* see http://www.fsf.org/licensing/licenses/gpl.txt for details
* 
* $Id: cpaint2.config.php,v 1.2 2006-02-13 02:11:51 amette Exp $
* $Log: not supported by cvs2svn $
* 
* Configuration file for backend scripts, including proxy
*
* @package    CPAINT
* @author     Paul Sullivan <wiley14@gmail.com>
* @author     Dominique Stender <dstender@st-webdevelopment.de>
* @copyright  Copyright (c) 2005-2006 Paul Sullivan, Dominique Stender - http://sf.net/projects/cpaint
* @version 	  2.0.3
*/

//---- proxy settings ----------------------------------------------------------
	$cpaint2_config["proxy.security.use_whitelist"] = true;		
				// Use the whitelist for allowed URLs?
					
//---- proxy security whitelist ------------------------------------------------
	/* 	whitelist data should be added to the variable $cpaint2_proxy_whitelist[]
			example: $cpaint2_proxy_whitelist[] = "example.com/test.php";
				- or -
			example: $cpaint2_proxy_whitelist[] = "example.com";
			** Omit http:// and https:// from the URL **
	*/
	$cpaint2_proxy_whitelist[] = $_SERVER['HTTP_HOST']; 	// this server	

?>