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
APIC::import("org.apicnet.xml.DOMIT_ChildNodes_Interface");

class DOMIT_Element extends DOMIT_ChildNodes_Interface {

	function DOMIT_Element($tagName) {
		$this->_constructor();		
		$this->nodeType = DOMIT_ELEMENT_NODE;
		$this->nodeName = $tagName;
		$this->attributes = new DOMIT_NamedNodeMap();
		$this->childNodes = array();
	} //DOMIT_Element
	
	function getTagName() {
		return $this->nodeName;
	} //getTagName
	
	function getNamedElements(&$nodeList, $tagName) {
		if (($this->nodeName == $tagName) || ($tagName == "*")) {
			$nodeList->appendNode($this); 
		}
		
		if ($this->hasChildNodes()) {
			$total = count($this->childNodes);
			
			for ($i = 0; $i < $total; $i++) {
				$this->childNodes[$i]->getNamedElements($nodeList, $tagName);
			}
		}
	} //getNamedElements
	
	function &createClone() {
		$className = get_class($this);
		$clone =& new $className($this->nodeName);

		$clone->attributes =& $this->attributes->createClone();
		
		return $clone;
	} //createClone
	
	function getText() {
		$text = "";
		$numChildren = count($this->childNodes);
				
		if ($numChildren != 0) {
			for ($i = 0; $i < $numChildren; $i++) {
				$child =& $this->childNodes[$i];
				$text .= $child->getText();
			}
		}
		
		return $text;
	} //getText
	
	function &getElementsByTagName($tagName) {
		$nodeList =& new DOMIT_NodeList();		
		$this->getNamedElements($nodeList, $tagName);
		
		return $nodeList;
	} //getElementsByTagName
	
	function &getElementsByPath($pattern, $nodeIndex = 0) {
		require_once("xml_domit_getelementsbypath.php");
	
		$gebp = new DOMIT_GetElementsByPath();
		$myResponse =& $gebp->parsePattern($this, $pattern, $nodeIndex);

		return $myResponse;
	} //getElementsByPath	
	
	function getTypedNodes(&$nodeList, $type) {
		$numChildren = count($this->childNodes);
				
		if ($numChildren != 0) {
			for ($i = 0; $i < $numChildren; $i++) {
				$child =& $this->childNodes[$i];
				
				if ($child->nodeType == $type) {
					$nodeList->appendNode($child);
				}
				
				if ($child->hasChildNodes()) {
					$child->getTypedNodes($nodeList, $type);
				}
			}
		}
	} //getTypedNodes
	
	function getValuedNodes(&$nodeList, $value) {
		$numChildren = count($this->childNodes);
				
		if ($numChildren != 0) {
			for ($i = 0; $i < $numChildren; $i++) {
				$child =& $this->childNodes[$i];
				
				if ($child->nodeValue == $value) {
					$nodeList->appendNode($child);
				}
				
				if ($child->hasChildNodes()) {
					$child->getValuedNodes($nodeList, $value);
				}
			}
		}
	} //getValuedNodes
	
	function getAttribute($name) {
		$returnNode =& $this->attributes->getNamedItem($name);
		
		if ($returnNode == null) {
			return "";
		}
		else {
			return $returnNode->getValue();
		}
	} //getAttribute	
	
	function setAttribute($name, $value) {
		$returnNode =& $this->attributes->getNamedItem($name);
		
		if ($returnNode == null) {
			$newAttr =& new DOMIT_Attr($name);
			$newAttr->setValue($value);
			$this->attributes->setNamedItem($newAttr);
		}
		else {
			$returnNode->setValue($value);
		}
	} //setAttribute	
	
	function removeAttribute($name) {
		$returnNode =& $this->attributes->removeNamedItem($name);
	} //removeAttribute
	
	function hasAttribute($name) {
		$returnNode =& $this->attributes->getNamedItem($name);

		return ($returnNode != null);
	} //hasAttribute
	
	function &getAttributeNode($name) {
		$returnNode =& $this->attributes->getNamedItem($name);
		return $returnNode;
	} //getAttributeNode	
	
	function &setAttributeNode(&$newAttr) {
		$returnNode =& $this->attributes->setNamedItem($newAttr);
		return $returnNode;
	} //setAttributeNode	
	
	function &removeAttributeNode(&$oldAttr) {
		$attrName = $oldAttr->getName();
		$returnNode =& $this->attributes->removeNamedItem($attrName);
		
		if ($returnNode == null) {
			DOMIT_DOMException::raiseException(DOMIT_NOT_FOUND_ERR, 
				"Target attribute not found.");
		}
		else {
			return $returnNode;
		}
	} //removeAttributeNode
	
	function normalize() {
		//collapses adjacent text nodes in entire element subtree
		if ($this->hasChildNodes()) {
			$currNode =& $this->childNodes[0];
			
			while ($currNode->nextSibling != null) {
				$nextNode =& $currNode->nextSibling;
				
				if (($currNode->nodeType == DOMIT_TEXT_NODE) && 
						($nextNode->nodeType == DOMIT_TEXT_NODE)) {
						
					$currNode->nodeValue .= $nextNode->nodeValue;
					$this->removeChild($nextNode);
				}
				else {
					$currNode->normalize();
				}
				
				if ($currNode->nextSibling != null) {
					$currNode =& $currNode->nextSibling;
				}
			}
		}
	} //normalize

	function toString() {
		$returnString = "<" . $this->nodeName;
		$returnString .= $this->attributes->toString();		
		
		//get children
		$myNodes =& $this->childNodes;
		$total = count($myNodes);
		
		if ($total != 0) {
			$returnString .= ">";
			
			for ($i = 0; $i < $total; $i++) {
				$child =& $myNodes[$i];
				$returnString .= $child->toString();
			}
			
			$returnString .= "</" . $this->nodeName . ">";
		}
		else {
			$returnString .= "/>";
		}		
		
		return $returnString;
	} //toString
} //DOMIT_Element
?>