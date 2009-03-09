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
APIC::import("org.apicnet.io.OOo.absOOo");

/**
 * OOoStyle
 * 
 * @package 
 * @author apicnet
 * @copyright Copyright (c) 2004
 * @version $Id: OOoStyle.php,v 1.3 2005-05-18 11:01:39 mose Exp $
 * @access public
 **/
class OOoStyle extends absOOo {

	var $type;
	
	var $STYLNUM = array(
		'style_family_text' => 1,
		'style_family_para' => 1,
		'style_page_style'	=> 1
	);

	function OOoStyle($dir){
		parent::absOOo();
		$this->DIRXML = $dir;
		$this->FILENAME = "styles.xml";
		
		$file = new File($dir."/".$this->FILENAME);
		if ($file->exists()) {
			$this->xml = new DOMIT_Document();
			$this->xml->loadXML($dir."/".$this->FILENAME, false);
		} else {
			$this->xml = new DOMIT_Document();
			$this->create();
		}
		$this->xml->setDocType("<!DOCTYPE office:document-styles PUBLIC \"-//OpenOffice.org//DTD OfficeDocument 1.0//EN\" \"office.dtd\">");
	}
	
	
	function create(){
	
		$docStyleNode =& $this->xml->createElement("office:document-styles");
		$docStyleNode->setAttribute("xmlns:office", "http://openoffice.org/2000/office");
		$docStyleNode->setAttribute("xmlns:style", "http://openoffice.org/2000/style" );
		$docStyleNode->setAttribute("xmlns:text", "http://openoffice.org/2000/text" );
		$docStyleNode->setAttribute("xmlns:table", "http://openoffice.org/2000/table" );
		$docStyleNode->setAttribute("xmlns:draw", "http://openoffice.org/2000/drawing" );
		$docStyleNode->setAttribute("xmlns:fo", "http://www.w3.org/1999/XSL/Format" );
		$docStyleNode->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink" );
		$docStyleNode->setAttribute("xmlns:number", "http://openoffice.org/2000/datastyle" );
		$docStyleNode->setAttribute("xmlns:svg", "http://www.w3.org/2000/svg" );
		$docStyleNode->setAttribute("xmlns:chart", "http://openoffice.org/2000/chart" );
		$docStyleNode->setAttribute("xmlns:dr3d", "http://openoffice.org/2000/dr3d" );
		$docStyleNode->setAttribute("xmlns:math", "http://www.w3.org/1998/Math/MathML" );
		$docStyleNode->setAttribute("xmlns:form", "http://openoffice.org/2000/form" );
		$docStyleNode->setAttribute("xmlns:script", "http://openoffice.org/2000/script" );
		$docStyleNode->setAttribute("office:version", "1.0");
		
		
		$fontDeclsNode =& $this->xml->createElement("office:font-decls");
		$fontDeclNode =& $this->xml->createElement("style:font-decl");
		$fontDeclNode->setAttribute("style:name", "Tahoma1");
		$fontDeclNode->setAttribute("fo:font-family", "Tahoma");
		$fontDeclsNode->appendChild($fontDeclNode);
		$fontDeclNode =& $this->xml->createElement("style:font-decl");
		$fontDeclNode->setAttribute("style:name", "Andale Sans UI");
		$fontDeclNode->setAttribute("fo:font-family", "&amp;apos;Andale Sans UI&amp;apos;");
		$fontDeclNode->setAttribute("style:font-pitch", "variable");
		$fontDeclsNode->appendChild($fontDeclNode);
		$fontDeclNode =& $this->xml->createElement("style:font-decl");
		$fontDeclNode->setAttribute("style:name", "Tahoma");
		$fontDeclNode->setAttribute("fo:font-family", "Tahoma");
		$fontDeclNode->setAttribute("style:font-pitch", "variable");
		$fontDeclsNode->appendChild($fontDeclNode);
		$fontDeclNode =& $this->xml->createElement("style:font-decl");
		$fontDeclNode->setAttribute("style:name", "Thorndale");
		$fontDeclNode->setAttribute("fo:font-family", "Thorndale");
		$fontDeclNode->setAttribute("style:font-family-generic", "roman");
		$fontDeclNode->setAttribute("style:font-pitch", "variable");
		$fontDeclsNode->appendChild($fontDeclNode);
		$fontDeclNode =& $this->xml->createElement("style:font-decl");
		$fontDeclNode->setAttribute("style:name", "Arial");
		$fontDeclNode->setAttribute("fo:font-family", "Arial");
		$fontDeclNode->setAttribute("style:font-family-generic", "swiss");
		$fontDeclNode->setAttribute("style:font-pitch", "variable");
		$fontDeclsNode->appendChild($fontDeclNode);
		$docStyleNode->appendChild($fontDeclsNode);

		$docStyleNode->appendChild($this->ChildText("office:automatic-styles", ""));
		$docStyleNode->appendChild($this->ChildText("office:master-styles", ""));
		
		$stylesNode =& $this->xml->createElement("office:styles");
		$styleNode = & $this->xml->createElement("style:style");
		$styleNode->setAttribute("style:name", $type);
		$styleNode->setAttribute("style:family", "paragraph");
		$styleNode->setAttribute("style:parent-style-name", "Standard");
		$styleNode->setAttribute("style:class", "extra");
		
		$propertiesNode =& $this->xml->createElement("style:properties");
		$propertiesNode->setAttribute("text:number-lines", "false");
		$propertiesNode->setAttribute("text:line-number", "0");
		
		$tabStopsNode =& $this->xml->createElement("style:tab-stops");
		$tabStopNode =& $this->xml->createElement("style:tab-stop");
		$tabStopNode->setAttribute("style:position", "8.498cm");
		$tabStopNode->setAttribute("style:type", "center");
		$tabStopsNode->appendChild($tabStopNode);
		$tabStopNode =& $this->xml->createElement("style:tab-stop");
		$tabStopNode->setAttribute("style:position", "16.999cm");
		$tabStopNode->setAttribute("style:type", "right");
		$tabStopsNode->appendChild($tabStopNode);
		
		$propertiesNode->appendChild($tabStopsNode);
		$styleNode->appendChild($propertiesNode);
		$stylesNode->appendChild($styleNode);
		
		$styleNode = & $this->xml->createElement("style:style");
		$styleNode->setAttribute("style:name", "Standard");
		$styleNode->setAttribute("style:family", "paragraph");
		$styleNode->setAttribute("style:class", "text");
		
		$propertiesNode =& $this->xml->createElement("style:properties");
		$propertiesNode->setAttribute("fo:text-align", "justify");
		$propertiesNode->setAttribute("style:justify-single-word", "false");
		
		$stylesNode->appendChild($styleNode);
		$docStyleNode->appendChild($stylesNode);
		
		$this->xml->setDocumentElement($docStyleNode);
		$this->xml->setXMLDeclaration("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
		
		
		$argPage = array(
			"NameStyle"		=> "Standard",
			"pageWidth"		=> "20.999",
			"pageHeight"	=> "29.699",
			"printOrient"	=> "portrait",
			"marginT"		=> "2",
			"marginB"		=> "2",
			"marginL"		=> "2",
			"marginR"		=> "2",	
			"writingMode"	=> "lr-tb"
		);
		$this->addStylePage($argPage);
		$this->addGraphicStyle();
	}

	function main(){
		echo $this->toString();
	}
	
	/**
	 * OOoStyle::addStyle()
	 * 
	 * @param Array $styleArg
	 * @return none
	 **/
	function addStyle($styleArg){
		/*
		* style:name
		* style:family
		* style:parent-style-name
		* style:class
		* style:next-style-name
		* style:list-style-name
		* style:master-page-name
		*/
		/*
		<style:style style:name="Text body" style:family="paragraph" style:parent-style-name="Standard" style:class="text">
         <style:properties fo:margin-top="0cm" fo:margin-bottom="0.212cm" style:font-name="Arial" fo:font-weight="bold" />
      </style:style>*/
	}
	
	/**
	 * OOoStyle::addStylePage() est une méthode d'ajout d'un entête ou d'un pied de page.
	 * 
	 * @param Array $argPage de type
	 * 	$argPage = array(
			"NameStyle"		=> "Standard",
			"NameStyleSuiv" => "Standard",
			"pageWidth"		=> "20.999",			//text
			"pageHeight"	=> "29.699",			// (top, bottom, or center) (left, right, or center)
			"printOrient"	=> "portrait",			// Ecart entre l'entête et le corp du document
			"marginT"		=> "2",
			"marginB"		=> "2",
			"marginL"		=> "2",					// Mager de gauche de l'entête
			"marginR"		=> "2",					// Mager de Droite de l'entête
			"writingMode"	=> "lr-tb"				// Hauteur de l'entête
		);
	 * @return none
	 **/
	function addStylePage($argPage){
		$this->verifIntegrite($argPage, "PageStyle");
		$automaticStylesNode = & $this->getNode("/office:document-styles/office:automatic-styles");

		$pageMasterNode =& $this->xml->createElement("style:page-master");
		$pageName = "pm".$this->STYLNUM['style_page_style'];
		$pageMasterNode->setAttribute("style:name", $pageName );
		$this->STYLNUM['style_page_style']++;
		
		$propertieNode =& $this->xml->createElement("style:properties");
		$propertieNode->setAttribute("fo:page-width", $argPage["pageWidth"]."cm");
		$propertieNode->setAttribute("fo:page-height", $argPage["pageHeight"]."cm" );
		$propertieNode->setAttribute("style:num-format", "1" );
		$propertieNode->setAttribute("style:print-orientation", $argPage["printOrient"] );
		$propertieNode->setAttribute("fo:margin-top", $argPage["marginT"]."cm" );
		$propertieNode->setAttribute("fo:margin-bottom", $argPage["marginB"]."cm" );
		$propertieNode->setAttribute("fo:margin-left", $argPage["marginL"]."cm" );
		$propertieNode->setAttribute("fo:margin-right", $argPage["marginR"]."cm" );
		$propertieNode->setAttribute("style:writing-mode", $argPage["writingMode"] );
		$propertieNode->setAttribute("style:footnote-max-height", "0cm");
		
		$footnoteSepNode =& $this->xml->createElement("style:footnote-sep");
		$footnoteSepNode->setAttribute("style:width", "0.018cm");
		$footnoteSepNode->setAttribute("style:distance-before-sep", "0.101cm");
		$footnoteSepNode->setAttribute("style:distance-after-sep", "0.101cm");
		$footnoteSepNode->setAttribute("style:adjustment", "left");
		$footnoteSepNode->setAttribute("style:rel-width", "25%");
		$footnoteSepNode->setAttribute("style:color", "#000000");
		
		$propertieNode->appendChild($footnoteSepNode);
		$pageMasterNode->appendChild($propertieNode);
		$pageMasterNode->appendChild($this->ChildText("style:header-style", ""));
		$pageMasterNode->appendChild($this->ChildText("style:footer-style", ""));
		
		$automaticStylesNode->appendChild($pageMasterNode);
		
		$masterStylesNode = & $this->getNode("/office:document-styles/office:master-styles");
		$masterPageNode =& $this->xml->createElement("style:master-page");
		$masterPageNode->setAttribute("style:name", $argPage["NameStyle"]);
		$masterPageNode->setAttribute("style:page-master-name", $pageName);
		if (isset($argPage["NameStyleSuiv"])) $masterPageNode->setAttribute("style:next-style-name", $argPage["NameStyleSuiv"]);
		
		$masterStylesNode->appendChild($masterPageNode);
		
		
		return $pageName;
	}
	
	
	
	/**
	 * OOoStyle::addStyleHeadFoot() est une méthode d'ajout d'un entête ou d'un pied de page.
	 * 
	 * @param Array $styleArg de type
	 * 	$argHeager = array(
			"Text"		=> "@PICNet",				//text
			"img"		=> array(					// information sur l'image
					"scr"		=> "E:/_WebDev/www/cOOlWare2/cache/c_projekte.png",
					"type"		=> "no-repeat",		// (no-repeat|repeat|stretch)
					"position"	=> "bottom right"),	// (top, bottom, or center) (left, right, or center)
			"marginB"	=> "0.499",					// Ecart entre l'entête et le corp du document
			"marginL"	=> "0.499",					// Mager de gauche de l'entête
			"marginR"	=> "0.499",					// Mager de Droite de l'entête
			"minHeight"	=> "0.998",					// Hauteur de l'entête
			"align"		=> "center",				// Alignement du texte de l'entête (left|center|right)
			"BgColor"	=> "CEFFB5",				// Couleur de fond de l'entête dans le cas ou ce dernier n'a pas d'image
		);
	 * @param String $type est le type a créer, soit un entête soit un pied de page
	 * @param $pageMasterName
	 * @return none
	 **/
	function addStyleHeadFoot($styleArg, $type, $pageMasterName){
		
		if ($type != "Header" && $type != "Footer") {
		    $this -> ErrorTracker(4, "Le type demander doit être Header ou Footer", 'addStyleHeadFoot', __FILE__, __LINE__);
		}
		$this->verifIntegrite($styleArg, $type);
		$headerStyleNode = & $this->getNode("/office:document-styles/office:automatic-styles/style:page-master@[style:name='".$pageMasterName."']/style:".strtolower($type)."-style");
		$headerStyleNode->appendChild($this->setProperties($styleArg, $this->DIRXML));
		
		$automaticStylesNode = & $this->getNode("/office:document-styles/office:automatic-styles");
		$StyleName = "S".$this->STYLNUM['style_family_text'];
		if (!$this->ssNodeExist($automaticStylesNode, "style:style@[style:name='".$StyleName."']")){
			$styleNode = & $this->xml->createElement("style:style");
			$styleNode->setAttribute("style:name", $StyleName);
			$STYLNUM['style_family_text']++;
			$styleNode->setAttribute("style:family", "paragraph");
			$styleNode->setAttribute("style:parent-style-name", $type);
		}
		
		$propertiesNode =& $this->xml->createElement("style:properties");
		$propertiesNode->setAttribute("fo:text-align", $styleArg["align"]);
		$propertiesNode->setAttribute("style:justify-single-word", "false");
		
		$styleNode->appendChild($propertiesNode);
		$automaticStylesNode->appendChild($styleNode);
		
		$masterPageNode = & $this->getNode("/office:document-styles/office:master-styles/style:master-page@[style:page-master-name='".$pageMasterName."']");
		
		$headerNode =& $this->xml->createElement("style:".strtolower($type));
		
		/********************Création de la cellule********************/
		if (isset($styleArg["Text"]) && is_object($styleArg["Text"])){
			
			if ($styleArg["Text"]->className() == "oooimg") {
				$pNode =& $this->xml->createElement("text:p");
				$pNode->setAttribute("text:style-name", $StyleName);
				$headerNode->appendChild($pNode);
				$styleArg["Text"]->run($pNode, $automaticStylesNode, $this->DIRXML);
			} else {
				$styleArg["Text"]->run($headerNode, $automaticStylesNode, $this->DIRXML);
			}
			
		} else {
			$pNode =& $this->xml->createElement("text:p");
			$pNode->setAttribute("text:style-name", $StyleName);
			$pNode->appendChild($this->xml->createTextNode($styleArg["Text"]));
			$headerNode->appendChild($pNode);
		}
		/**********************Fin de Création*************************/
		$masterPageNode->appendChild($headerNode);	
	}
	
	
	function addGraphicStyle(){
		$stylesNode = & $this->getNode("/office:document-styles/office:styles");
		$styleNode = & $this->xml->createElement("style:default-style");
		$styleNode->setAttribute("style:family", "graphics");
		
		$propertiesNode =& $this->xml->createElement("style:properties");
		$propertiesNode->setAttribute("draw:start-line-spacing-horizontal", "0.283cm");
		$propertiesNode->setAttribute("draw:start-line-spacing-vertical", "0.283cm");
		$propertiesNode->setAttribute("style:use-window-font-color", "true");
		$propertiesNode->setAttribute("style:font-name", "Thorndale");
		$propertiesNode->setAttribute("fo:font-size", "12pt");
		$propertiesNode->setAttribute("fo:language", "fr");
		$propertiesNode->setAttribute("fo:country", "FR");
		$propertiesNode->setAttribute("style:font-name-asian", "Andale Sans UI");
		$propertiesNode->setAttribute("style:font-size-asian", "12pt");
		$propertiesNode->setAttribute("style:language-asian", "none");
		$propertiesNode->setAttribute("style:font-name-complex", "none");
		$propertiesNode->setAttribute("style:country-complex", "none");
		$propertiesNode->setAttribute("style:text-autospace", "ideograph-alpha");
		$propertiesNode->setAttribute("style:line-break", "strict");
		$propertiesNode->setAttribute("style:writing-mode", "lr-tb");
		$propertiesNode->setAttribute("country-asian", "none");
		
		$tabStopsNode =& $this->xml->createElement("style:tab-stops");
		
		$propertiesNode->appendChild($tabStopsNode);
		$styleNode->appendChild($propertiesNode);
		$stylesNode->appendChild($styleNode);
	}
	
	
	
	
}
?>
