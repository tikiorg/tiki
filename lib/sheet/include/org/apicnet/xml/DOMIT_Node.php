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

class DOMIT_Node {
	//Note: should never instantiate this class!
	var $nodeName = null;
	var $nodeValue = null;
	var $nodeType = null;
	var $parentNode = null;
	var $childNodes = null;
	var $firstChild = null;
	var $lastChild = null;
	var $previousSibling = null;
	var $nextSibling = null;
	var $attributes = null;
	var $ownerDocument = null;
	var $uid;
	
	function DOMIT_Node() {		
		DOMIT_DOMException::raiseException(DOMIT_ABSTRACT_CLASS_INSTANTIATION_ERR, 
			 "Cannot instantiate abstract class DOMIT_Node"); 
	} //DOMIT_Node
	
	function _constructor() {
		global  $uidFactory;
		$this->uid = $uidFactory->generateUID();
	} //_constructor	
	
	function &appendChild(&$child) {
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, 
			("Method appendChild cannot be called by class " . get_class($this)));
	} //appendChild

	function &insertBefore(&$newChild, &$refChild) {
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, 
			("Method insertBefore cannot be called by class " . get_class($this)));
	} //insertBefore
	
	function &replaceChild(&$newChild, &$oldChild) {
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, 
			("Method replaceChild cannot be called by class " . get_class($this)));
	} //replaceChild
	
	function &removeChild(&$oldChild) {
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, 
			("Method removeChild cannot be called by class " . get_class($this)));
	} //removeChild
	
	function getChildNodeIndex(&$arr, &$child) {
		$index = -1;
		$total = count($arr);
		
		for ($i = 0; $i < $total; $i++) {
			if ($child->uid == $arr[$i]->uid) {
				$index = $i;
				break;
			}
		}
		
		return $index;
	} //getChildNodeIndex
	
	function hasChildNodes() {
		return (count($this->childNodes) > 0);
	} //hasChildNodes

	function &cloneNode($deep) {
		//must provide a createClone method for all subclasses of DOMIT_Node
		$clone =& $this->createClone();
		
		if ($deep) {
			//return clone of this node's children
			if ($this->hasChildNodes()) {
				$total = count($this->childNodes);
				
				for ($i = 0; $i < $total; $i++) {
					$clone->appendChild($this->childNodes[$i]->cloneNode($deep));
				}
			}
		}
		
		return $clone;
	} //cloneNode

	function getNamedElements(&$nodeList, $tagName) {
		//Implemented in DOMIT_Element. 
		//Needs to be here though! This is called against all nodes in the document.
	} //getNamedElements	
	
	function setOwnerDocument(&$rootNode) {
		if ($rootNode->ownerDocument == null) {
			unset($this->ownerDocument);
			$this->ownerDocument = null;
		}
		else {
			$this->ownerDocument =& $rootNode->ownerDocument;
		}
		
		if ($this->hasChildNodes()) {
			$total = count($this->childNodes);
			
			for ($i = 0; $i < $total; $i++) {
				$this->childNodes[$i]->setOwnerDocument($rootNode);
			}
		}
	} //setOwnerDocument
	
	function &nvl(&$value,$default) {
		  if (is_null($value)) return $default;
		  return $value;
	} //nvl	
	
	function &selectNodes($pattern) {
		require_once("xml_domit_xpath.php");
		
		$xpParser =& new DOMIT_XPath();
		
		return $xpParser->parsePattern($this, $pattern);		
	} //selectNodes	
	
	function &getElementsByPath($pattern, $nodeIndex = 0) {
		 DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, 
			("Method getElementsByPath cannot be called by class " . get_class($this)));
	} //getElementsByPath	
	
	function getTypedNodes(&$nodeList, $type) {		 
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, 
			("Method getTypedNodes cannot be called by class " . get_class($this)));
	} //getTypedNodes
	
	function getValuedNodes(&$nodeList, $value) {		 
		DOMIT_DOMException::raiseException(DOMIT_HIERARCHY_REQUEST_ERR, 
			("Method getValuedNodes cannot be called by class " . get_class($this)));
	} //getValuedNodes
	
	function getText() {
		return $this->nodeValue;
	} //getText
	
	function onLoad() {
		//you can override this method if you subclass any of the 
		//DOMIT_Nodes. It's a way of performing  
		//initialization of your subclass as soon as the document
		//has been loaded (as opposed to as soon as the current node
		//has been instantiated).
	} //onLoad
	
	function toNormalizedString() {
		//require this file for generating a normalized (readable) xml string representation
		return DOMIT_Utilities::toNormalizedString($this);
	} //toNormalizedString
} //DOMIT_Node
?>