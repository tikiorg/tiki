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
APIC::import("org.apicnet.xml.DOMIT_Node");

class DOMIT_CharacterData extends DOMIT_Node {
	function DOMIT_CharacterData() {		
		DOMIT_DOMException::raiseException(DOMIT_ABSTRACT_CLASS_INSTANTIATION_ERR, 
			 "Cannot instantiate abstract class DOMIT_CharacterData"); 
	} //DOMIT_CharacterData

	function getData() {
		return $this->nodeValue;
	} //getData
	
	function getLength() {
		return strlen($this->nodeValue);
	} //getLength
	
	function substringData($offset, $count) {
		$totalChars = $this->getLength();
		
		if (($offset < 0) || (($offset + $count) > $totalChars)) {
			
			DOMIT_DOMException::raiseException(DOMIT_INDEX_SIZE_ERR, 
				"Character Data index out of bounds.");
				
		}
		else {
			$data = $this->getData();
			return substr($data, $offset, $count);
		}
	} //substringData
	
	function appendData($arg) {
		$this->nodeValue .= $arg;
	} //appendData
	
	function insertData($offset, $arg) {
		$totalChars = $this->getLength();
		
		if (($offset < 0) || ($offset > $totalChars)) {

			DOMIT_DOMException::raiseException(DOMIT_INDEX_SIZE_ERR, 
				"Character Data index out of bounds.");
				
		}
		else {
			$data = $this->getData();
			$pre = substr($data, 0, $offset);
			$post = substr($data, $offset);
			
			$this->nodeValue = $pre . $arg . $post;
		}
	} //insertData
	
	function deleteData($offset, $count) {
		$totalChars = $this->getLength();
		
		if (($offset < 0) || (($offset + $count) > $totalChars)) {
		
			DOMIT_DOMException::raiseException(DOMIT_INDEX_SIZE_ERR, 
				"Character Data index out of bounds.");
				
		}
		else {
			$data = $this->getData();
			$pre = substr($data, 0, $offset);
			$post = substr($data, ($offset + $count));
			
			$this->nodeValue = $pre . $post;
		}
	} //substringData
	
	function replaceData($offset, $count, $arg) {
		$totalChars = $this->getLength();
		
		if (($offset < 0) || (($offset + $count) > $totalChars)) {
		
			DOMIT_DOMException::raiseException(DOMIT_INDEX_SIZE_ERR, 
				"Character Data index out of bounds.");
				
		}
		else {
			$data = $this->getData();
			$pre = substr($data, 0, $offset);
			$post = substr($data, ($offset + $count));
			
			$this->nodeValue = $pre . $arg . $post;
		}
	} //replaceData
} //DOMIT_CharacterData

?>