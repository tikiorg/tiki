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


class DOMIT {
	var $xmlDoc = null;
	var $currentNode = null;
	var $lastChild = null;
	var $upcomingCDATA = false; //flag for Expat
	var $preserveCDATA;
	
	function parse (&$myXMLDoc, $xmlText, $preserveCDATA = true) {
		$this->xmlDoc =& $myXMLDoc;
		$this->preserveCDATA = $preserveCDATA;
		//create instance of expat parser (should be included in php distro)
		$parser = xml_parser_create();
		
		//set handlers for SAX events
		xml_set_element_handler($parser, array(&$this, "startElement"), array(&$this, "endElement")); 
		xml_set_character_data_handler($parser, array(&$this, "dataElement")); 
		xml_set_default_handler($parser, array(&$this, "defaultDataElement")); 
		xml_set_notation_decl_handler($parser, array(&$this, "notationElement")); 
		xml_set_processing_instruction_handler($parser, array(&$this, "processingInstructionElement")); 
		xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0); 	
		
		//parse out whitespace -  (XML_OPTION_SKIP_WHITE = 1 does not 
		//seem to work consistently across versions of PHP and Expat
		$xmlText = eregi_replace(">" . "[[:space:]]+" . "<" , "><", $xmlText);
		
		$success = xml_parse($parser, $xmlText);
		xml_parser_free($parser); 
		
		return $success;
	} //parse
	
	function parseSAXY(&$myXMLDoc, $xmlText, $preserveCDATA) {
		require_once("SAXY.php");
		
		$this->xmlDoc =& $myXMLDoc;
		
		//create instance of SAXY parser 
		$parser =& new SAXY();
		
		$parser->xml_set_element_handler(array(&$this, "startElement"), array(&$this, "endElement"));
		$parser->xml_set_character_data_handler(array(&$this, "dataElement"));
		$parser->xml_set_xml_declaration_handler(array(&$this, "xmlDeclarationElement"));
		$parser->xml_set_doctype_handler(array(&$this, "doctypeElement"));
		$parser->xml_set_comment_handler(array(&$this, "commentElement"));
		$parser->xml_set_processing_instruction_handler(array(&$this, "processingInstructionElement")); 
		
		if ($preserveCDATA) {
			$parser->xml_set_cdata_section_handler(array(&$this, "cdataElement"));
		}
		
		return $parser->parse($xmlText);
	} //parseSAXY

	function startElement(&$parser, $name, $attrs) {
		$currentNode =& $this->xmlDoc->createElement($name);

		if ($this->lastChild == null) {
			$this->xmlDoc->setDocumentElement($currentNode); 
		}
		else {
			$this->lastChild->appendChild($currentNode);
		}
		
		$numAttrs = count($attrs);
		
		if (($numAttrs > 0) && is_array($attrs)) {
			reset ($attrs);
			
			while (list($key, $value) = each ($attrs)) {
				$currentNode->setAttribute($key, $value);
			}
		}		
		
		$this->lastChild =& $currentNode;
	} //startElement	
	
	function endElement(&$parser, $name) {
		$this->lastChild =& $this->lastChild->parentNode;
	} //endElement	 
	
	function dataElement(&$parser, $data) {
		if ($this->upcomingCDATA) {
			$currentNode =& $this->xmlDoc->createCDataSection($data);
			$this->upcomingCDATA = false;
		}
		else {
			$currentNode =& $this->xmlDoc->createTextNode($data);
		}
		
		$this->lastChild->appendChild($currentNode);
	} //dataElement	
	
	function cdataElement(&$parser, $data) {
		$currentNode =& $this->xmlDoc->createCDATASection($data);

		$this->lastChild->appendChild($currentNode);
	} //dataElement	
	
	function defaultDataElement(&$parser, $data) {
		$pre = strtoupper(substr($data, 0, 3));
		
		switch ($pre) {
			case "<?X": //xml declaration
				$this->xmlDoc->xmlDeclaration = $data;
				break;
			case "<!E": //dtd element
				$this->xmlDoc->doctype .= "\n   " . $data;
				break;
			case "<![": //cdata section coming
				if ($this->preserveCDATA) {
					$this->upcomingCDATA = true;
				}
				break;	
			case "<!-": //comment
				$currentNode =& $this->commentElement($this, substr($data, 4, (strlen($data) - 7)));	
				break;				
			case "]]>": //cdata remnant - ignore
				break;
			default:
				$this->xmlDoc->doctype .= $data;
				break;
		}
	} //defaultDataElement
	
	function xmlDeclarationElement(&$parser, $data) {
		$this->xmlDoc->xmlDeclaration = $data;
	} //xmlDeclarationElement
	
	function doctypeElement(&$parser, $data) {
		$this->xmlDoc->doctype = $data;
	} //doctypeElement
	
	function notationElement(&$parser, $data) {
		//will implement later
	} //notationElement
	
	function commentElement(&$parser, $data) {
		$currentNode =& $this->xmlDoc->createComment($data);
		$this->lastChild->appendChild($currentNode);
	} //commentElement	
	
	function processingInstructionElement(&$parser, $target, $data) {	
		$currentNode =& $this->xmlDoc->createProcessingInstruction($target, $data);
		$this->lastChild->appendChild($currentNode);
	} //processingInstructionElement
} //DOMIT_Parser


?>