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

class DOMIT_NodeList {
	var $arNodeList = array();
	
	function &item($index) {
		if ($index < $this->getLength()) {
			return $this->arNodeList[$index];
		}
		else {
			return null;
		}
	} //item
	
	function getLength() {
		return count($this->arNodeList);
	} //getLength
	
	function &appendNode(&$node) {
		$this->arNodeList[] =& $node;
		return $node;
	} //appendNode
	
	function &removeNode(&$node) {
		$total = $this->getLength();
		$returnNode = null;
		$found = false;
		
		for ($i = 0; $i < $total; $i++) {
			if (!$found) {
				if ($node->uid == $this->arNodeList[$i]->uid) {
					$found = true;
					$returnNode=& $node;
				}
			}
			
			if ($found) {
				if ($i == ($total - 1)) {
					unset($this->arNodeList[$i]);
				}
				else {
					$this->arNodeList[$i] =& $this->arNodeList[($i + 1)];
				}
			}			
		}
		
		return $returnNode;
	} //$removeNode
	
	function &createClone() {
		$className = get_class($this);
		$clone =& new $className();
		
		foreach ($this->arNodeList as $key => $value) {
			$currNode =& $this->arNodeList[$key];
			$clone[$key] =& $currNode->createClone();
		}
		
		return $clone;
	} //createClone
	
	function toString() {
		$retString = "";
		
		foreach ($this->arNodeList as $key => $value) {
			$currNode =& $this->arNodeList[$key];
			$retString .= $currNode->toString();
		}
		
		return $retString;
	} //toString
} //DOMIT_NodeList

?>