<?php
//*******************************************************************
//DOMIT_NodeList and DOMIT_NamedNodeMap are structures
//for storing and accessing collections of DOMIT_Nodes.
//*******************************************************************
//by John Heinstein
//jheinstein@engageinteractive.com
//johnkarl@nbnet.nb.ca
//*******************************************************************
//Version 0.3
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


class DOMIT_NamedNodeMap {
	//to be used for attributes list
	var $arNodeMap = array();
	
	function &getNamedItem($name) {
		if (isset($this->arNodeMap[$name])) {
			return $this->arNodeMap[$name];
		}
		else {
			return null;
		}
	} //getNamedItem
	
	function &setNamedItem(&$arg) {
		$returnNode = null;
		
		if (isset($this->arNodeMap[$arg->nodeName])) {
			$returnNode =& $this->arNodeMap[$arg->nodeName];
		}
		
		$this->arNodeMap[$arg->nodeName] =& $arg;		
		return $returnNode;
	} //setNamedItem
	
	function &removeNamedItem($name) {
		$returnNode = null;
		
		if (isset($this->arNodeMap[$name])) {
			$returnNode =& $this->arNodeMap[$name];
			unset($this->arNodeMap[$name]);
		}
		
		return $returnNode;
	} //removeNamedItem
	
	function &item($index) {
		$returnNode = null;
		$i = 0;
		
		foreach ($this->arNodeMap as $key => $value) {
			if ($i == $index) {
				$returnNode = & $this->arNodeMap[$key];
				break;
			}
			
			$i++;
		}
		
		return $returnNode;
	} //item
	
	function getLength() {
		return count($this->arNodeMap);
	} //getLength
	
	function &createClone() {
		$className = get_class($this);
		$clone =& new $className();
		
		foreach ($this->arNodeMap as $key => $value) {
			$currNode =& $this->arNodeMap[$key];
			$clone[$key] =& $currNode->createClone();
		}
		
		return $clone;
	} //createClone
	
	function toString() {
		$retString = "";
		
		foreach ($this->arNodeMap as $key => $value) {
			$currNode =& $this->arNodeMap[$key];
			$retString .= $currNode->toString();
		}
		
		return $retString;
	} //toString
} //DOMIT_NamedNodeMap

?>