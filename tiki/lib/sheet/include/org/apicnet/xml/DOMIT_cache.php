<?php
//*******************************************************************
//DOMIT! is a non-validating, but lightweight and fast DOM parser for PHP
//*******************************************************************
//by John Heinstein
//jheinstein@engageinteractive.com
//johnkarl@nbnet.nb.ca
//*******************************************************************
//Version 0.7
//copyright 2004 Engage Interactive
//http://www.engageinteractive.com/domit/
//All rights reserved
//*******************************************************************
//Licensed under the GNU General Public License (GPL)
//
//This program is free software; you can redistribute it and/or modify
//it under the terms of the GNU General Public License as published by
//the Free Software Foundation; either version 2 of the License, or
//(at your option) any later version.
//
//This program is distributed in the hope that it will be useful,
//but WITHOUT ANY WARRANTY; without even the implied warranty of
//MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//GNU General Public License for more details.
//
//You should have received a copy of the GNU General Public License
//along with this program; if not, write to the Free Software
//Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
//*******************************************************************
//see GPL details at http://www.gnu.org/copyleft/gpl.html
//and also in file license.txt included with DOMIT! 
//*******************************************************************


class DOMIT_cache {
	function toCache($xmlFileName, &$doc, $writeAttributes = "w") {
		require_once("DOMIT_Utilities.php");

		$name = DOMIT_Utilities::removeExtension($xmlFileName) . "." . DOMIT_FILE_EXTENSION_CACHE;
		DOMIT_Utilities::putDataToFile($name, serialize($doc), $writeAttributes);

		return (file_exists($name) && is_writable($name));
	} //toCache
	
	function &fromCache($xmlFileName) {
		require_once("DOMIT_Utilities.php");
		
		$name = DOMIT_Utilities::removeExtension($xmlFileName) . "." . DOMIT_FILE_EXTENSION_CACHE;
		$fileContents =& DOMIT_Utilities::getDataFromFile($name, "r");
		$newxmldoc =& unserialize($fileContents);

		return $newxmldoc;
	} //fromCache	
	
	function cacheExists($xmlFileName) {
		require_once("DOMIT_Utilities.php");
		
		$name = DOMIT_Utilities::removeExtension($xmlFileName) . "." . DOMIT_FILE_EXTENSION_CACHE;
		return file_exists($name);
	} //xmlFileName
	
	function removeFromCache($xmlFileName) {
		require_once("DOMIT_Utilities.php");
		
		$name = DOMIT_Utilities::removeExtension($xmlFileName) . "." . DOMIT_FILE_EXTENSION_CACHE;
		return unlink($name);
	} //removeFromCache
} //DOMIT_cache
?>