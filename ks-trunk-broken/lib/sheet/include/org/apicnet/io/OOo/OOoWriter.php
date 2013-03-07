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
APIC::import("org.apicnet.io.OOo.objOOo.OOoText");

/**
 * OOoWriter, classe de génération et de modification d'un fichier Writer
 * 
 * @package 
 * @author apicnet
 * @copyright Copyright (c) 2004
 * @version $Id: OOoWriter.php,v 1.3 2005-05-18 11:01:39 mose Exp $
 * @access public
 **/
class OOoWriter extends absOOo {
	
	var $style;
	var $STYLNUM = array(
		'style_family_text' => 1,
		'style_family_para' => 1,
		'style_page_style'	=> 1
	);
	
	function OOoWriter($dir){
		parent::absOOo();
		$this->DIRXML = $dir;
		$this->FILENAME = "content.xml";
		
		$file = new File($dir."/".$this->FILENAME);
		if ($file->exists()) {
			$this->xml = new DOMIT_Document();
			$this->xml->loadXML($dir."/".$this->FILENAME, false);
		} else {
			$this->xml = new DOMIT_Document();
			$this->create();
		}
		
		$this->style = new OOoStyle($this->DIRXML);
		$this->xml->setDocType("<!DOCTYPE office:document-content PUBLIC \"-//OpenOffice.org//DTD OfficeDocument 1.0//EN\" \"office.dtd\">");
	}
	
	
	function create(){
		$docWriterNode =& $this->xml->createElement("office:document-content");
		$docWriterNode->setAttribute("xmlns:office", "http://openoffice.org/2000/office");
		$docWriterNode->setAttribute("xmlns:style", "http://openoffice.org/2000/style");
		$docWriterNode->setAttribute("xmlns:text", "http://openoffice.org/2000/text");
		$docWriterNode->setAttribute("xmlns:table", "http://openoffice.org/2000/table");
		$docWriterNode->setAttribute("xmlns:draw", "http://openoffice.org/2000/drawing");
		$docWriterNode->setAttribute("xmlns:fo", "http://www.w3.org/1999/XSL/Format");
		$docWriterNode->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
		$docWriterNode->setAttribute("xmlns:number", "http://openoffice.org/2000/datastyle");
		$docWriterNode->setAttribute("xmlns:svg", "http://www.w3.org/2000/svg");
		$docWriterNode->setAttribute("xmlns:chart", "http://openoffice.org/2000/chart");
		$docWriterNode->setAttribute("xmlns:dr3d", "http://openoffice.org/2000/dr3d");
		$docWriterNode->setAttribute("xmlns:math", "http://www.w3.org/1998/Math/MathML");
		$docWriterNode->setAttribute("xmlns:form", "http://openoffice.org/2000/form");
		$docWriterNode->setAttribute("xmlns:script", "http://openoffice.org/2000/script");
		$docWriterNode->setAttribute("office:class", "text");
		$docWriterNode->setAttribute("office:version", "1.0");
		
		$docWriterNode->appendChild($this->ChildText("office:script", ""));
		
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
		$docWriterNode->appendChild($fontDeclsNode);
		
		$docWriterNode->appendChild($this->ChildText("office:automatic-styles", ""));
		
		$bodyNode =& $this->xml->createElement("office:body");
		$sequenceDeclsNode =& $this->xml->createElement("office:sequence-decls");
		$sequenceDeclNode =& $this->xml->createElement("office:sequence-decl");
		$sequenceDeclNode->setAttribute("text:display-outline-level", "0");
		$sequenceDeclNode->setAttribute("text:name", "Illustration");
		$sequenceDeclsNode->appendChild($sequenceDeclNode);
		$sequenceDeclNode =& $this->xml->createElement("office:sequence-decl");
		$sequenceDeclNode->setAttribute("text:display-outline-level", "0");
		$sequenceDeclNode->setAttribute("text:name", "Table");
		$sequenceDeclsNode->appendChild($sequenceDeclNode);
		$sequenceDeclNode =& $this->xml->createElement("office:sequence-decl");
		$sequenceDeclNode->setAttribute("text:display-outline-level", "0");
		$sequenceDeclNode->setAttribute("text:name", "Text");
		$sequenceDeclsNode->appendChild($sequenceDeclNode);
		$sequenceDeclNode =& $this->xml->createElement("office:sequence-decl");
		$sequenceDeclNode->setAttribute("text:display-outline-level", "0");
		$sequenceDeclNode->setAttribute("text:name", "Drawing");
		$sequenceDeclsNode->appendChild($sequenceDeclNode);
		
		$pNode =& $this->xml->createElement("text:p");
		$pNode->setAttribute("text:style-name", "Standard");
		
		$bodyNode->appendChild($sequenceDeclsNode);
		$bodyNode->appendChild($pNode);
		
		$docWriterNode->appendChild($bodyNode);
		
		$this->xml->setDocumentElement($docWriterNode);
		$this->xml->setXMLDeclaration("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
	}

	function main(){
		echo $this->toString();
	}
	
	function save(){
		parent::save();
		$this->style->save();
	}
	
	function areHeader(){
		$this->style->addHeader();
	}
	
	/**
	 * OOoWriter::addHeader()
	 * 
	 * @param $argHeager
	 * @param string $pageName
	 * @return 
	 **/
	function addHeader($argHeager, $pageName = "pm1"){
		$this->style->addStyleHeadFoot($argHeager, "Header", $pageName);
	}
	
	/**
	 * OOoWriter::addFooter()
	 * 
	 * @param $argFooter
	 * @param string $pageName
	 * @return 
	 **/
	function addFooter($argFooter, $pageName = "pm1"){
		$this->style->addStyleHeadFoot($argFooter, "Footer", $pageName);
	}
	
	/**
	 * OOoWriter::addStylePage()
	 * 
	 * @param $argPage
	 * @return 
	 **/
	function addStylePage($argPage){
		return $this->style->addStylePage($argPage);
	}
	
	/**
	 * OOoWriter::addText()
	 * 
	 * @param $objText
	 * @return 
	 **/
	function addText($objText){
		if ($objText->className() == strtolower("OOoText")) {
			$automaticStylesNode = & $this->getNode("/office:document-content/office:automatic-styles");
			$bodyNode = & $this->getNode("/office:document-content/office:body");
			$objText->run($bodyNode, $automaticStylesNode, $this->DIRXML);
		}
	}
	
	
	/**
	 * OOoWriter::addTable()
	 * 
	 * @param $objTable
	 * @return 
	 **/
	function addTable($objTable){
		if ($objTable->className() == strtolower("OOoTable")) {
			$automaticStylesNode = & $this->getNode("/office:document-content/office:automatic-styles");
			$bodyNode = & $this->getNode("/office:document-content/office:body");
			$objTable->run($bodyNode, $automaticStylesNode, $this->DIRXML);
		}
	}
	
	
	/**
	 * OOoWriter::addPage()
	 * 
	 * @param $styleNamePage
	 * @return 
	 **/
	function addPage($styleNamePage){
		$argPara = array(
			"paraName"	=> $styleNamePage
		);
		
		$para1 = new OOoText();
		$styleName = $para1->setStylePara($argPara);
		
		$argText = array(
			"NameStyle"		=> $styleName,
			"Text"			=> ""
		);
		$para1->addText($argText);
		$automaticStylesNode = & $this->getNode("/office:document-content/office:automatic-styles");
		$bodyNode = & $this->getNode("/office:document-content/office:body");
		$para1->run($bodyNode, $automaticStylesNode, $this->DIRXML);
		
	}
	
	
	/**
	 * OOoWriter::addLine(), méthode permettant d'ajouter des lignes au document
	 * 
	 * @param integer $num nombre de ligne à ajouter
	 * @return none
	 **/
	function addLine($num = 1){
		$bodyNode = & $this->getNode("/office:document-content/office:body");
		
		while ($num > 0){
			$pNode =& $this->xml->createElement("text:p");
			$pNode->setAttribute("text:style-name", "Standard");
			
			$bodyNode->appendChild($pNode);
			$num--;
		}
	}
}
