<?php
/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2007 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * Configuration file for the PHP File Uploader.
 */

$tikiroot = dirname(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))));
$tikidomain = '';
if (is_file($tikiroot.'/db/virtuals.inc')) {
	if (isset($_SERVER['TIKI_VIRTUAL']) and is_file($tikiroot.'/db/'.$_SERVER['TIKI_VIRTUAL'].'/local.php')) {
		$tikidomain = $_SERVER['TIKI_VIRTUAL'];
	} elseif (isset($_SERVER['SERVER_NAME']) and is_file($tikiroot.'/db/'.$_SERVER['SERVER_NAME'].'/local.php')) {
		$tikidomain = $_SERVER['SERVER_NAME'];
	} elseif (isset($_SERVER['HTTP_HOST']) and is_file($tikiroot.'/db/'.$_SERVER['HTTP_HOST'].'/local.php')) {
		$tikidomain = $_SERVER['HTTP_HOST'];
	}
}
if ($tikidomain) $tikidomain.= '/';
if ($tikiroot != $_SERVER['DOCUMENT_ROOT']) {
	$tikipath = strrchr($tikiroot,$_SERVER['DOCUMENT_ROOT']).'/';
} else {
	$tikipath = '/';
}

global $Config;

// SECURITY: You must explicitelly enable this "uploader".
$Config['Enabled'] = true ;

// Set if the file type must be considere in the target path.
// Ex: /userfiles/image/ or /userfiles/file/
$Config['UseFileType'] = false ;

// Path to uploaded files relative to the document root.
$Config['UserFilesPath'] = $tikipath.'img/wiki_up/'.$tikidomain ;

// Fill the following value it you prefer to specify the absolute path for the
// user files directory. Usefull if you are using a virtual directory, symbolic
// link or alias. Examples: 'C:\\MySite\\userfiles\\' or '/root/mysite/userfiles/'.
// Attention: The above 'UserFilesPath' must point to the same directory.
$Config['UserFilesAbsolutePath'] = '' ;

// Due to security issues with Apache modules, it is reccomended to leave the
// following setting enabled.
$Config['ForceSingleExtension'] = true ;

$Config['AllowedExtensions']['File']	= array() ;
$Config['DeniedExtensions']['File']		= array('html','htm','php','php2','php3','php4','php5','phtml','pwml','inc','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','com','dll','vbs','js','reg','cgi','htaccess','asis','sh','shtml','shtm','phtm') ;

$Config['AllowedExtensions']['Image']	= array('jpg','gif','jpeg','png') ;
$Config['DeniedExtensions']['Image']	= array() ;

$Config['AllowedExtensions']['Flash']	= array('swf','fla') ;
$Config['DeniedExtensions']['Flash']	= array() ;

?>
