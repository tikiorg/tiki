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
APIC::import("org.apicnet.xml.*");

define ("DOMIT_VERSION", "0.7");

//Nodes
define("DOMIT_ELEMENT_NODE", 1);
define("DOMIT_ATTRIBUTE_NODE", 2);
define("DOMIT_TEXT_NODE", 3);
define("DOMIT_CDATA_SECTION_NODE", 4);
define("DOMIT_ENTITY_REFERENCE_NODE", 5);
define("DOMIT_ENTITY_NODE", 6);
define("DOMIT_PROCESSING_INSTRUCTION_NODE", 7);
define("DOMIT_COMMENT_NODE", 8);
define("DOMIT_DOCUMENT_NODE", 9);
define("DOMIT_DOCUMENT_TYPE_NODE", 10);
define("DOMIT_DOCUMENT_FRAGMENT_NODE", 11);
define("DOMIT_NOTATION_NODE", 12);

//DOM Level 1 Exceptions
define("DOMIT_INDEX_SIZE_ERR", 1); 
define("DOMIT_DOMSTRING_SIZE_ERR", 2); 
define("DOMIT_HIERARCHY_REQUEST_ERR", 3); 
define("DOMIT_WRONG_DOCUMENT_ERR", 4); 
define("DOMIT_INVALID_CHARACTER_ERR", 5);
define("DOMIT_NO_DATA_ALLOWED_ERR", 6);
define("DOMIT_NO_MODIFICATION_ALLOWED_ERR", 7);
define("DOMIT_NOT_FOUND_ERR", 8);
define("DOMIT_NOT_SUPPORTED_ERR", 9);
define("DOMIT_INUSE_ATTRIBUTE_ERR", 10);

//DOM Level 2 Exceptions
define("DOMIT_INVALID_STATE_ERR", 11);
define("DOMIT_SYNTAX_ERR", 12);
define("DOMIT_INVALID_MODIFICATION_ERR", 13);
define("DOMIT_NAMESPACE_ERR", 14);
define("DOMIT_INVALID_ACCESS_ERR", 15);

//DOMIT! Exceptions
define("DOMIT_ABSTRACT_CLASS_INSTANTIATION_ERR", 100);
define("DOMIT_DOCUMENT_FRAGMENT_ERR", 101);

define ("DOMIT_FILE_EXTENSION_CACHE", "dch");

$GLOBALS['uidFactory'] = new UIDGenerator();


class DOMIT_Document extends DOMIT_ChildNodes_Interface {
	var $xmlDeclaration;
	var $doctype;
	var $documentElement;
	var $parser;
	var $implementation;
	
	function DOMIT_Document() {
		$this->_constructor();
		$this->xmlDeclaration = null;
		$this->doctype = null;
		$this->documentElement = null;
		$this->nodeType = DOMIT_DOCUMENT_NODE;
		$this->nodeName = "#document";
		$this->ownerDocument =& $this;
		$this->parser = "";
		$this->implementation =& new DOMIT_DOMImplementation();
	} //DOMIT_Document	
	
	function &setDocumentElement(&$node) {
		$this->documentElement =& $node;
		$this->firstChild =& $node;
		$this->lastChild =& $node;
		$this->childNodes[0] =& $node;
		$node->parentNode =& $this;
		
		//clear these references just in case 
		//they are left over from prior operations
		unset($node->nextSibling);
		$node->nextSibling = null;
		
		unset($node->previousSibling);
		$node->previousSibling = null;
		
		$node->ownerDocument =& $this;
		$node->setOwnerDocument($this);
		
		return $node;
	} //setDocumentElement
	
	function &appendChild(&$node) {
		//note: will overwrite documentElement if it already exists!
		
		//if a DocumentFragment is passed, it should only have 
		//a single child of type DOMIT_Element
		if ($node->nodeType == DOMIT_DOCUMENT_FRAGMENT_NODE) {
			if ($node->hasChildNodes() && (count($node->childNodes) == 1) && (is_a($node->firstChild, "DOMIT_Element"))) {
				$node =& $node->firstChild;
			}
			else {
			
				DOMIT_DOMException::raiseException(DOMIT_DOCUMENT_FRAGMENT_ERR, 
					("DOMIT_DocumentFragment can only be appended to DOMIT_Document" . 
					" if a single DOMIT_Element is present at its root level."));
					
			}
		}	
		
		$this->setDocumentElement($node);
		
		return $node;
	} //appendChild
	
	function &createDocumentFragment() {
		$node =& new DOMIT_DocumentFragment();
		$node->ownerDocument =& $this;
		
		return $node;
	} //createDocumentFragment
	
	function &createAttribute($name) {
		$node =& new DOMIT_Attr($name);
		
		return $node;
	} //createAttribute
	
	function &createElement($tagName) {
		$node =& new DOMIT_Element($tagName);
		$node->ownerDocument =& $this;
		
		return $node;
	} //createElement
	
	function &createTextNode($data) {
		$node =& new DOMIT_TextNode($data);
		$node->ownerDocument =& $this;
	
		return $node;
	} //createTextNode
	
	function &createCDATASection($data) {
		$node =& new DOMIT_CDATASection($data);
		$node->ownerDocument =& $this;
		
		return $node;
	} //createCDATASection
	
	function &createComment($text) {
		$node =& new DOMIT_Comment($text);
		$node->ownerDocument =& $this;
		
		return $node;
	} //createComment
	
	function &createProcessingInstruction($target, $data) {
		$node =& new DOMIT_ProcessingInstruction($target, $data);
		$node->ownerDocument =& $this;
		
		return $node;
	} //createProcessingInstruction
	
	function &getElementsByTagName($tagName) {
		$nodeList =& new DOMIT_NodeList();
		
		if ($this->documentElement != null) {
			$this->documentElement->getNamedElements($nodeList, $tagName);
		}
		
		return $nodeList;
	} //getElementsByTagName
	
	function &getElementsByPath($pattern, $nodeIndex = 0) {
	
		$gebp = new DOMIT_GetElementsByPath();
		$myResponse =& $gebp->parsePattern($this, $pattern, $nodeIndex);

		return $myResponse;
	} //getElementsByPath	
	
	function &getNodesByNodeType($type, &$contextNode) {
		$nodeList =& new DOMIT_NodeList();
		
		if (($type == DOMIT_DOCUMENT_NODE) || (is_a($contextNode, "DOMIT_Document"))){
			$nodeList->appendNode($this); 
		}
		else if (($contextNode->nodeType == DOMIT_ELEMENT_NODE) || (is_a($contextNode, "DOMIT_Element"))) {
			$contextNode->getTypedNodes($nodeList, $type);
		}
		else if ($contextNode->uid == $this->uid) {
			if ($this->documentElement != null) {
				if ($type == DOMIT_ELEMENT_NODE) {
					$nodeList->appendNode($this->documentElement); 
				}
					
				$this->documentElement->getTypedNodes($nodeList, $type);
			}
		}
		
		return $nodeList;
	} //getNodesByNodeType	

	function &getNodesByNodeValue($value, &$contextNode) {
		$nodeList =& new DOMIT_NodeList();
		
		 if ($contextNode->uid == $this->uid) {
			 if ($this->nodeValue == $value) {
				 $nodeList->appendNode($this);
			 }
		 }
		
		if ($this->documentElement != null) {
			$this->documentElement->getValuedNodes($nodeList, $value);
		}
		
		return $nodeList;
	} //getNodesByNodeValue
	
	function parseXML($xmlText, $useSAXY = true, $preserveCDATA = true, $fireLoadEvent = false) {
		
		if (DOMIT_Utilities::validateXML($xmlText)) {
			$domParser =& new DOMIT();
			
			if ($useSAXY || (!function_exists("xml_parser_create"))) {
				//use SAXY parser to populate xml tree
				$this->parser = "SAXY";
				$success = $domParser->parseSAXY($this, $xmlText, $preserveCDATA);
			}
			else {
				//use Expat parser to populate xml tree
				$this->parser = "EXPAT";
				$success = $domParser->parse($this, $xmlText, $preserveCDATA);
			}
			
			if ($fireLoadEvent && ($this->documentElement != null)) $this->load($this->documentElement);
				
			return $success;
		}
		else {
			return false;
		}
	} //parseXML
	
	function loadXML($filename, $useSAXY = true, $preserveCDATA = true, $fireLoadEvent = false) {
		$xmlText = $this->getTextFromFile($filename);
		return $this->parseXML($xmlText, $useSAXY, $preserveCDATA, $fireLoadEvent);
	} //loadXML
	
	function getTextFromFile($filename) {
		if (function_exists("file_get_contents")) {
			return file_get_contents($filename);
		}
		else {

			$fileContents =& DOMIT_Utilities::getDataFromFile($filename, "r");
			return $fileContents;
		}
		
		return "";
	} //getTextFromFile		

	function saveXML($filename) {
		$stringRep = $this->toString();
		return $this->saveTextToFile($filename, $stringRep);
	} //saveXML

	function saveTextToFile($filename, $text) {
		if (function_exists("file_put_contents")) {
			file_put_contents($filename, $text);
		}
		else {
			DOMIT_Utilities::putDataToFile($filename, $text, "w");
		}
		
		return (file_exists($filename) && is_writable($filename));
	} //saveTextToFile	
	
	
	function &createClone() {
		$className = get_class($this);
		$clone =& new $className($this->nodeName);
		 
		$clone->xmlDeclaration = $this->xmlDeclaration;
		$clone->doctype = $this->doctype;
		
		return $clone;
	} //createClone
	
	function parsedBy() {
		return $this->parser;
	} //parsedBy
	
	function getText() {
		if ($this->documentElement != null) {
			$root =& $this->documentElement; 
			return $root->getText();
		}
		else {
			return "";
		}
	} //getText
	
	function getDocType() {
		return $this->doctype;
	} //getDocType
	
	function setDocType($docType) {
		$this->doctype = $docType;
	}//setDocType
	
	function getXMLDeclaration() {
		return $this->xmlDeclaration;
	} //getXMLDeclaration
	
	function setXMLDeclaration($XMLDeclaration) {
		$this->xmlDeclaration = $XMLDeclaration;
	} //detXMLDeclaration
	
	function &getDOMImplementation() {
		return $this->implementation;
	} //getDOMImplementation
	
	function load(&$contextNode) {		
		if ($contextNode->hasChildNodes()) {
			$total = count($contextNode->childNodes);
			
			for ($i = 0; $i < $total; $i++) {
				$currNode =& $contextNode->childNodes[$i];
				$currNode->ownerDocument->load($currNode);
			}
		}	

		$contextNode->onLoad();
	} //load
	
	function getVersion() {
		return DOMIT_VERSION;
	} //getVersion
		
	function toString() {
		$result = "";
		$result .= $this->nvl($this->xmlDeclaration,"");
		$result .= $this->nvl($this->doctype, "");
		
		if ($this->documentElement != null) {
			$result .= $this->nvl($this->documentElement->toString(), "");
		}

		return $result;
	} //toString
} //DOMIT_Document

?>