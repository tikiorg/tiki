<?php
//*******************************************************************
//SAXY version 0.5
//a non-validating, but lightweight and fast SAX parser for PHP
//*******************************************************************
//by John Heinstein
//jheinstein@engageinteractive.com
//johnkarl@nbnet.nb.ca
//*******************************************************************
//copyright 2003 Engage Interactive
//http://www.engageinteractive.com/saxy/
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
//and also in file license.txt included with SAXY 
//*******************************************************************

	define ("SAXY_VERSION", "0.5");

	define("SAXY_STATE_PROLOG_NONE", 0);
	define("SAXY_STATE_PROLOG_XMLDECLARATION", 1);
	define("SAXY_STATE_PROLOG_DTD", 2);
	define("SAXY_STATE_PROLOG_INLINEDTD", 3);
	define("SAXY_STATE_PARSING", 4);
	define("SAXY_SEARCH_CDATA", "![CDATA[");
	define("SAXY_SEARCH_NOTATION", "!NOTATION");
	define("SAXY_SEARCH_DOCTYPE", "!DOCTYPE");
	define("SAXY_STATE_ATTR_NONE", 0);
	define("SAXY_STATE_ATTR_KEY", 1);
	define("SAXY_STATE_ATTR_VALUE", 2);
	define("SAXY_CDATA_LEN", 8);
	
	class SAXY {
		var $state;
		var $charContainer;
		var $startElementHandler;
		var $endElementHandler;
		var $characterDataHandler;
		var $cDataSectionHandler = null;
		var $xmlDeclarationHandler;
		var $DTDHandler;
		var $commentHandler;
		var $processingInstructionHandler;
			
		function SAXY() {
			$this->charContainer = "";
			$this->state = SAXY_STATE_PROLOG_NONE;
		} //SAXY_Parser
		
		function xml_set_element_handler($startHandler, $endHandler) {
			$this->startElementHandler = $startHandler;
			$this->endElementHandler = $endHandler;
		} //xml_set_element_handler
		
		function xml_set_character_data_handler($handler) {
			$this->characterDataHandler =& $handler;
		} //xml_set_character_data_handler
		
		function xml_set_cdata_section_handler($handler) {
			$this->cDataSectionHandler =& $handler;
		} //xml_set_cdata_section_handler
		
		function xml_set_xml_declaration_handler($handler) {
			$this->xmlDeclarationHandler =& $handler;
		} //xml_set_xml_declaration_handler
		
		function xml_set_doctype_handler($handler) {
			$this->DTDHandler =& $handler;
		} //xml_set_doctype_handler
		
		function xml_set_comment_handler($handler) {
			$this->commentHandler =& $handler;
		} //xml_set_comment_handler
		
		function xml_set_processing_instruction_handler($handler) {
			$this->processingInstructionHandler =& $handler;
		} //xml_set_processing_instruction_handler
		
		function getVersion() {
			return SAXY_VERSION;
		} //getVersion		
				
		function preprocessXML($xmlText) {
			//strip prolog
			$xmlText = trim($xmlText);
			$startChar = -1;
			$total = strlen($xmlText);
			
			for ($i = 0; $i < $total; $i++) {
				$currentChar = $xmlText{$i};

				switch ($this->state) {
					case SAXY_STATE_PROLOG_NONE:	
						if ($currentChar == "<") {
							$nextChar = $xmlText{($i + 1)};
							
							if ($nextChar == "?")  {
								$this->state = SAXY_STATE_PROLOG_XMLDECLARATION;
								$this->charContainer .= $currentChar;
							}
							else if ($nextChar == "!") {
								$this->state = SAXY_STATE_PROLOG_DTD;
								$this->charContainer .= $currentChar;
								break;
							}
							else {
								$this->charContainer = "";
								$startChar  = $i;
								$this->state = SAXY_STATE_PARSING;
								return (substr($xmlText, $startChar));
							}
						}
						
						break;
						
					case SAXY_STATE_PROLOG_XMLDECLARATION:
						if ($currentChar == ">") {
							$this->state = SAXY_STATE_PROLOG_NONE;
							$this->fireXMLDeclarationEvent($this->charContainer . $currentChar);
							$this->charContainer = "";
						}
						else {
							$this->charContainer .= $currentChar;
						}
						
						break;
					
					case SAXY_STATE_PROLOG_DTD:
						if ($currentChar == "[") {
							$this->state = SAXY_STATE_PROLOG_INLINEDTD;
						}					
						else if ($currentChar == ">") {
							$this->state = SAXY_STATE_PROLOG_NONE;
							$this->fireDTDEvent($this->charContainer . $currentChar);
							$this->charContainer = "";
						}
						else {
							$this->charContainer .= $currentChar;
						}	
						
						break;
						
					case SAXY_STATE_PROLOG_INLINEDTD:
						$previousChar = $xmlText{($i - 1)};
						
						if (($currentChar == ">") && ($previousChar == "]")){
							$this->state = SAXY_STATE_PROLOG_NONE;
							$this->fireDTDEvent($this->charContainer . $currentChar);
							$this->charContainer = "";
						}
						else {
							$this->charContainer .= $currentChar;
						}	
						
						break;
					
				}
			}
		} //preprocessXML
		
		function parse ($xmlText) {
			$xmlText = $this->preprocessXML($xmlText);			
			$total = strlen($xmlText);

			for ($i = 0; $i < $total; $i++) {
				$currentChar = $xmlText{$i};

				switch ($this->state) {
					case SAXY_STATE_PARSING:
					
						switch ($currentChar) {
							case "<":
								if (substr($this->charContainer, 0, SAXY_CDATA_LEN) == SAXY_SEARCH_CDATA) {
									$this->charContainer .= $currentChar;
								}
								else {
									$this->parseBetweenTags($this->charContainer);
									$this->charContainer = "";
								}
						
								break;
								
							case ">":
								if ((substr($this->charContainer, 0, SAXY_CDATA_LEN) == SAXY_SEARCH_CDATA)   &&
									($this->getCharFromEnd($this->charContainer, 0) != "]") &&
									($this->getCharFromEnd($this->charContainer, 1) != "]")) {
									$this->charContainer .= $currentChar;
								}
								else {
									$this->parseTag($this->charContainer);
									$this->charContainer = "";
								}
								break;
								
							default:
								$this->charContainer .= $currentChar;
						}
						
						break;
				}
			}	

			return true;
		} //parse

		function getCharFromEnd($text, $index) {
			$len = strlen($text);
			$char = $text{($len - 1 - $index)};
			
			return $char;
		} //getCharFromEnd
		
		function parseTag($tagText) {
			$tagText = trim($tagText);
			$firstChar = $tagText{0};
			$myAttributes = "";
		
			switch ($firstChar) {
				case "/":
					$tagName = substr($tagText, 1);				
					$this->fireEndElementEvent($tagName);
					break;
				
				case "!":
					$upperCaseTagText = strtoupper($tagText);
				
					if (strpos($upperCaseTagText, SAXY_SEARCH_CDATA) !== false) { //CDATA Section
						$total = strlen($tagText);
						$openBraceCount = 0;
						$textNodeText = "";
						
						for ($i = 0; $i < $total; $i++) {
							$currentChar = $tagText{$i};
							
							if ($currentChar == "]") {
								break;
							}
							else if ($openBraceCount > 1) {
								$textNodeText .= $currentChar;
							}
							else if ($currentChar == "[") {
								$openBraceCount ++;
							}
						}
						
						if ($this->cDataSectionHandler == null) {
							$this->fireCharacterDataEvent($textNodeText);
						}
						else {
							$this->fireCDataSectionEvent($textNodeText);
						}
					}
					else if (strpos($upperCaseTagText, SAXY_SEARCH_NOTATION) !== false) { //NOTATION node, discard
						return;
					}
					else if (substr($tagText, 0, 2) == "!-") { //comment node
						$this->fireCommentEvent(substr($tagText, 3, (strlen($tagText) - 5)));
					}
					
					break;
					
				case "?": 
					//Processing Instruction node
					$endTarget = 0;
					$total = strlen($tagText);
					
					for ($x = 2; $x < $total; $x++) {
						if (trim($tagText{$x}) == "") {
							$endTarget = $x;
							break;
						}
					}
					
					$target = substr($tagText, 1, ($endTarget - 1));
					$data = substr($tagText, ($endTarget + 1), ($total - $endTarget - 2));
				
					$this->fireProcessingInstructionEvent($target, $data);
					break;
					
				default:				
					if ((strpos($tagText, "\"") !== false) || (strpos($tagText, "'") !== false)) {
						$total = strlen($tagText);
						$tagName = "";
	
						for ($i = 0; $i < $total; $i++) {
							$currentChar = $tagText{$i};
							
							if ($currentChar == " ") {
								$myAttributes = $this->parseAttributes(substr($tagText, $i));
								break;
							}
							else {
								$tagName.= $currentChar;
							}
						}
	
						if (strrpos($tagText, "/") == (strlen($tagText) - 1)) { //check $tagText, but send $tagName
							$this->fireStartElementEvent($tagName, $myAttributes);
							$this->fireEndElementEvent($tagName);
						}
						else {
							$this->fireStartElementEvent($tagName, $myAttributes);
						}
					}
					else {
						if (strpos($tagText, "/") !== false) {
							$tagText = trim(substr($tagText, 0, (strrchr($tagText, "/") - 1)));
							$this->fireStartElementEvent($tagText, $myAttributes);
							$this->fireEndElementEvent($tagText);
						}
						else {
							$this->fireStartElementEvent($tagText, $myAttributes);
						}
					}					
			}
		} //parseTag		
		
		function parseAttributes($attrText) {
			$attrText = trim($attrText);	
			$attrArray = array();
			
			$total = strlen($attrText);
			$keyDump = "";
			$valueDump = "";
			$currentState = SAXY_STATE_ATTR_NONE;
			$quoteType = "";
			
			for ($i = 0; $i < $total; $i++) {								
				$currentChar = $attrText{$i};
				
				if ($currentState == SAXY_STATE_ATTR_NONE) {
					if (trim($currentChar != "")) {
						$currentState = SAXY_STATE_ATTR_KEY;
					}
				}
				
				switch ($currentChar) {
					case "\t":
						if ($currentState == SAXY_STATE_ATTR_VALUE) {
							$valueDump .= $currentChar;
						}
						else {
							$currentChar = "";
						}
					case "\n":
						$currentChar = "";
						
					case "=";
						if ($currentState == SAXY_STATE_ATTR_VALUE) {
							$valueDump .= $currentChar;
						}
						else {
							$currentState = SAXY_STATE_ATTR_VALUE;
							$quoteType = "";
						}
						break;
						
					case "\"":
						if ($currentState == SAXY_STATE_ATTR_VALUE) {
							if ($quoteType == "") {
								$quoteType = "\"";
							}
							else {
								if ($quoteType == $currentChar) {
									$attrArray[trim($keyDump)] = trim($valueDump);
									$keyDump = $valueDump = $quoteType = "";
									$currentState = SAXY_STATE_ATTR_NONE;
								}
								else {
									$valueDump .= $currentChar;
								}
							}
						}
						break;
						
					case "'":
						if ($currentState == SAXY_STATE_ATTR_VALUE) {
							if ($quoteType == "") {
								$quoteType = "'";
							}
							else {
								if ($quoteType == $currentChar) {
									$attrArray[$keyDump] = $valueDump;
									$keyDump = $valueDump = $quoteType = "";
									$currentState = SAXY_STATE_ATTR_NONE;
								}
								else {
									$valueDump .= $currentChar;
								}
							}
						}
						break;
						
					default:
						if ($currentState == SAXY_STATE_ATTR_KEY) {
							$keyDump .= $currentChar;
						}
						else {
							$valueDump .= $currentChar;
						}
				}
			}

			return $attrArray;
		} //parseAttributes		
		
		function parseBetweenTags($betweenTagText) {
			if (trim($betweenTagText) != "") {
				$this->fireCharacterDataEvent($betweenTagText);
			}
		} //betweenTagText	

		function fireStartElementEvent($tagName, $attributes) {
			call_user_func($this->startElementHandler, $this, $tagName, $attributes);
		} //fireStartElementEvent		
		
		function fireEndElementEvent($tagName) {
			call_user_func($this->endElementHandler, $this, $tagName);
		} //fireEndElementEvent
		
		function fireCharacterDataEvent($data) {
			call_user_func($this->characterDataHandler, $this, $data);
		} //fireCharacterDataEvent	
		
		function fireCDataSectionEvent($data) {
			call_user_func($this->cDataSectionHandler, $this, $data);
		} //fireCDataSectionEvent	
		
		function fireXMLDeclarationEvent($data) {
			call_user_func($this->xmlDeclarationHandler, $this, $data);
		} //fireXMLDeclarationEvent

		function fireDTDEvent($data) {
			call_user_func($this->DTDHandler, $this, $data);
		} //fireDTDEvent
		
		function fireCommentEvent($data) {
			call_user_func($this->commentHandler, $this, $data);
		} //fireCommentEvent
		
		function fireProcessingInstructionEvent($target, $data) {
			call_user_func($this->processingInstructionHandler, $this, $target, $data);
		} //fireProcessingInstructionEvent
	
	} //SAXY_Parser
?>