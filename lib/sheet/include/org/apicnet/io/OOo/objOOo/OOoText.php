<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


/*This file is part of J4PHP - Ensembles de propriétés et méthodes permettant le developpment rapide d'application web modulaire
Copyright (c) 2002-2004 @PICNet

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU LESSER GENERAL PUBLIC LICENSE
as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU LESSER GENERAL PUBLIC LICENSE for more details.

You should have received a copy of the GNU LESSER GENERAL PUBLIC LICENSE
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/
APIC::import("org.apicnet.io.OOo.*");
APIC::import("org.apicnet.io.File");

class OOoText extends absOOo {

	var $_text   = NULL;
	var $_style;
	var $StyleName;
		

	/**
	 * OOoText::OOoText()
	 * 
	 * @return 
	 **/
	function __construct(){
		$this->xml = new DOMIT_Document();
	}
		/**
	 * OOoText::setStylePara()
	 * 
	 * @param $argPara = array(
	 			"paraName"		=> "NewPage"
				"img"			=> array(					// information sur l'image
						"scr"		=> "E:/_WebDev/www/cOOlWare2/cache/c_projekte.png",
						"type"		=> "no-repeat",		// (no-repeat|repeat|stretch)
						"position"	=> "bottom right"),	// (top, bottom, or center) (left, right, or center)
				"tabs"			=> array(),
				"marginL"		=> "",
				"marginR"		=> "",
				"marginB"		=> "",
				"marginT"		=> "",
				"align"			=> "",
				"indent"		=> "",
				"autoIndent"	=> "",
				"bgColor"		=> "",
				"padding"		=> "",
				"border"		=> "0.002cm solid #000000"
				)
	 * @return none
	 */
	function setStylePara($argPara){
		$this->_style = $argPara;
	}
	
	
	/**
	 * OOoText::addText()
	 * 
	 * @param $argText = array(
			"NameStyle"		=> "Standard",
			"Text"			=> "",
			"styleCarac"	=> array(
			 	"italic"	=> FALSE
				"bold"		=> FALSE
				"underline"	=> FALSE
				"bgColor"	=> FALSE
				"color"		=> FALSE
				"fontName"	=> FALSE)
		);
	 * @return none
	 **/
	function addText($argText){
		$this->_text[] = $argText;
	}
	
	
	/**
	 * OOoText::run()
	 * 
	 * @param $nodeContent
	 * @param $nodeStyle
	 * @return 
	 **/
	function run(&$nodeContent, &$nodeStyle, $dir){
		static $STYLNUM;
		if (!isset($STYLNUM)){
			$STYLNUM = array(
					'style_family_text' => 1,
					'style_family_para' => 1,
					'style_page_style'	=> 1
			);
		}
		
		$this->verifIntegrite($this->_style, "paraStyle");

	    $StyleName = "P".$STYLNUM['style_family_para'];
		$STYLNUM['style_family_para']++;
		
		$styleNode =& $this->xml->createElement("style:style");
		$styleNode->setAttribute("style:name", $StyleName);
		$styleNode->setAttribute("style:family", "paragraph");
		$styleNode->setAttribute("style:parent-style-name", "Standard");
		$styleNode->setAttribute("style:master-page-name", $this->_style["paraName"]);
		$styleNode->appendChild($this->setProperties($this->_style, $dir));
		$nodeStyle->appendChild($styleNode);
		
		
		for($i = 0; $i < count($this->_text); $i++) {
	  		
			if (!is_object($this->_text)) {
				if ($i == 0) {
					$pNode =& $this->xml->createElement("text:p");
					
					if (isset($this->_text[$i]["NameStyle"]) && !isset($this->_style)) $pNode->setAttribute("text:style-name", $this->_text[$i]["NameStyle"]);
					else $pNode->setAttribute("text:style-name", $StyleName);
				}

 				if (isset($this->_text[$i]["styleCarac"])) {
 					$StyleCaracName = "T".$STYLNUM['style_family_text'];
 					$STYLNUM['style_family_text']++;
						
 					$this->verifIntegrite($this->_text[$i], "styleText");
 					$styleNode =& $this->xml->createElement("style:style");
 					$styleNode->setAttribute("style:name", $StyleCaracName);
 					$styleNode->setAttribute("style:family", "text");
 					$styleNode->appendChild($this->setProperties($this->_text[$i]["styleCarac"], $dir));
 					$nodeStyle->appendChild($styleNode);
 					
 					$spanNode =& $this->xml->createElement("text:span");
 					$spanNode->setAttribute("text:style-name", $StyleCaracName);
 				    $textNode = &$this->xml->createTextNode($this->encode_text($this->_text[$i]["Text"]));
					$spanNode->appendChild($textNode);
					if ($this->_text[$i]["lineBreak"]) $spanNode->appendChild($this->xml->createElement("text:line-break"));
					if ($this->_text[$i]["tabStop"]) $spanNode->appendChild($this->xml->createElement("text:tab-stop"));
 					$pNode->appendChild($spanNode);
 				} else {
 					$textNode = &$this->xml->createTextNode($this->encode_text($this->_text[$i]["Text"]));
 					$pNode->appendChild($textNode);
					if ($this->_text[$i]["lineBreak"]) $pNode->appendChild($this->xml->createElement("text:line-break"));
					if ($this->_text[$i]["tabStop"]) $spanNode->appendChild($this->xml->createElement("text:tab-stop"));
 				}
			}
		}
		
		
 		$nodeContent->appendChild($pNode);
	}
}	
