<?php
//*******************************************************************
//DOMIT_GetElementsByPath is a simply utility for 
//path-based access to nodes in a DOMIT! document .
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

define("GET_ELEMENTS_BY_PATH_SEPARATOR", "/");
define("GET_ELEMENTS_BY_PATH_SEARCH_ABSOLUTE", 0);
define("GET_ELEMENTS_BY_PATH_SEARCH_RELATIVE", 1);
define("GET_ELEMENTS_BY_PATH_SEARCH_VARIABLE", 2);

class DOMIT_GetElementsByPath {
	var $callingNode;
	var $searchType;
	var $contextNode;
	var $arPathSegments = array();
	var $nodeList;
	var $targetIndex;
	var $abortSearch = false;
	
	function DOMIT_GetElementsByPath() {
		$this->nodeList =& new DOMIT_NodeList();
	} //DOMIT_GetElementsByPath
	
	function &parsePattern(&$node, $pattern, $nodeIndex = 0) {
		$this->callingNode =& $node;		 
		$pattern = trim($pattern);	
		
		$this->determineSearchType($pattern);
		$this->setContextNode();
		$this->splitPattern($pattern);
	
		$this->targetIndex = $nodeIndex;
		$totalSegments = count($this->arPathSegments);		
		
		if ($totalSegments > 0) {
			if ($this->searchType == GET_ELEMENTS_BY_PATH_SEARCH_VARIABLE) {
				$arContextNodes =& $this->contextNode->ownerDocument->getElementsByTagName($this->arPathSegments[0]);
				$totalContextNodes = $arContextNodes->getLength();
				
				for ($i = 0; $i < $totalContextNodes; $i++) {
					$this->selectNamedChild($arContextNodes->item($i), 1);
				}
			}
			else {
				if ($this->searchType == GET_ELEMENTS_BY_PATH_SEARCH_ABSOLUTE) {
					if ($this->contextNode->nodeName == $this->arPathSegments[0]) {
						if (count($this->arPathSegments) == 1) {
							$this->nodeList->appendNode($this->contextNode);
						}
						else {
							$this->selectNamedChild($this->contextNode, 1);	
						}
					}
				}
				else if ($this->searchType == GET_ELEMENTS_BY_PATH_SEARCH_RELATIVE) {
					$this->selectNamedChild($this->contextNode, 0);	
				}
			}
		}		
	
		if ($nodeIndex > 0) {
			if ($nodeIndex <= $this->nodeList->getLength()) {
				return $this->nodeList->item(($nodeIndex - 1));
			}
			else {
				return null;
			}
		}
		
		return $this->nodeList;
	} //parsePattern
	
	
	function determineSearchType($pattern) {
		$firstChar = $pattern{0};
		
		if ($firstChar != GET_ELEMENTS_BY_PATH_SEPARATOR) {
			//relative path
			$this->searchType = GET_ELEMENTS_BY_PATH_SEARCH_RELATIVE;
		}
		else {
			$secondChar = $pattern{1};
				
			if ($secondChar != GET_ELEMENTS_BY_PATH_SEPARATOR) {
				//absolute path
				$this->searchType = GET_ELEMENTS_BY_PATH_SEARCH_ABSOLUTE;
			}
			else {
				//variable path
				$this->searchType = GET_ELEMENTS_BY_PATH_SEARCH_VARIABLE;				
			}
		}
	} //determineSearchType
	
	
	function setContextNode() {
		switch($this->searchType) {
			case GET_ELEMENTS_BY_PATH_SEARCH_ABSOLUTE:
				$this->contextNode =& $this->callingNode->ownerDocument->documentElement;
				break;
				
			case GET_ELEMENTS_BY_PATH_SEARCH_RELATIVE:
				if ($this->callingNode->uid != $this->callingNode->ownerDocument->uid) {
					$this->contextNode =& $this->callingNode;
				}
				else {
					$this->contextNode =& $this->callingNode->ownerDocument->documentElement;
				}
				break;

			case GET_ELEMENTS_BY_PATH_SEARCH_VARIABLE:
				$this->contextNode =& $this->callingNode->ownerDocument->documentElement;
				break;
		}
	} //setContextNode
	
	
	function splitPattern($pattern) {
		switch($this->searchType) {
			case GET_ELEMENTS_BY_PATH_SEARCH_ABSOLUTE:
				$this->arPathSegments = explode(GET_ELEMENTS_BY_PATH_SEPARATOR, substr($pattern, 1));
				break;
				
			case GET_ELEMENTS_BY_PATH_SEARCH_RELATIVE:
				$this->arPathSegments = explode(GET_ELEMENTS_BY_PATH_SEPARATOR, substr($pattern, 0));
				break;

			case GET_ELEMENTS_BY_PATH_SEARCH_VARIABLE:
				$this->arPathSegments = explode(GET_ELEMENTS_BY_PATH_SEPARATOR, substr($pattern, 2));
				break;
		}
	} //splitPattern
	
	
	
	function selectNamedChild(&$node, $pIndex) {	
		if (!$this->abortSearch) {
			if ($pIndex < count($this->arPathSegments)) { //not at last path segment
				$name = $this->arPathSegments[$pIndex];
				$numChildren = count($node->childNodes);
			
				for ($i = 0; $i < $numChildren; $i++) {
					$currentChild =& $node->childNodes[$i];
		
					if ($currentChild->nodeName == $name) {
						$this->selectNamedChild($currentChild, ($pIndex + 1));
					}
				}
			}
			else {
				$this->nodeList->appendNode($node);
				
				if ($this->targetIndex == $this->nodeList->getLength()) {
					$this->abortSearch = true;
				}
			}
		}
	} //selectNamedChild
} //DOMIT_GetElementsByPath
?>