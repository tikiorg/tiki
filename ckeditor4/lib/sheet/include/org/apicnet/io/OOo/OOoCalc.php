<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


/*
This file is part of J4PHP - Ensembles de propriétés et méthodes permettant le developpment rapide d'application web modulaire
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
//APIC::import("org.apicnet.io.OOo.objOOo.OOoCell");

/**
 * OOoWriter, classe de génération et de modification d'un fichier Writer
 * 
 * @package 
 * @author apicnet
 * @copyright Copyright (c) 2004
 * @version $Id: OOoCalc.php,v 1.3 2005-05-18 11:01:38 mose Exp $
 * @access public
 **/
class OOoCalc extends absOOo {
	
	var $style;
	var $STYLNUM = array(
		'style_family_text' => 1,
		'style_family_para' => 1,
		'style_page_style'	=> 1
	);
	
	function OOoCalc($dir){
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
		$docCalcNode =& $this->xml->createElement("office:document-content");
		$docCalcNode->setAttribute("xmlns:office", "http://openoffice.org/2000/office");
		$docCalcNode->setAttribute("xmlns:style", "http://openoffice.org/2000/style");
		$docCalcNode->setAttribute("xmlns:text", "http://openoffice.org/2000/text");
		$docCalcNode->setAttribute("xmlns:table", "http://openoffice.org/2000/table");
		$docCalcNode->setAttribute("xmlns:draw", "http://openoffice.org/2000/drawing");
		$docCalcNode->setAttribute("xmlns:fo", "http://www.w3.org/1999/XSL/Format");
		$docCalcNode->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
		$docCalcNode->setAttribute("xmlns:number", "http://openoffice.org/2000/datastyle");
		$docCalcNode->setAttribute("xmlns:svg", "http://www.w3.org/2000/svg");
		$docCalcNode->setAttribute("xmlns:chart", "http://openoffice.org/2000/chart");
		$docCalcNode->setAttribute("xmlns:dr3d", "http://openoffice.org/2000/dr3d");
		$docCalcNode->setAttribute("xmlns:math", "http://www.w3.org/1998/Math/MathML");
		$docCalcNode->setAttribute("xmlns:form", "http://openoffice.org/2000/form");
		$docCalcNode->setAttribute("xmlns:script", "http://openoffice.org/2000/script");
		$docCalcNode->setAttribute("office:class", "spreadsheet");
		$docCalcNode->setAttribute("office:version", "1.0");
		
		$docCalcNode->appendChild($this->ChildText("office:script", ""));
		
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
		$docCalcNode->appendChild($fontDeclsNode);
		
		
		$docCalcNode->appendChild($this->ChildText("office:automatic-styles", ""));
		$docCalcNode->appendChild($this->ChildText("office:body", ""));
		
		$this->xml->setDocumentElement($docCalcNode);
		$this->xml->setXMLDeclaration("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
	}

	function main(){
		echo $this->toString();
	}
	
	function save(){
		parent::save();
		$this->style->save();
	}
	
	/**
	 * OOoWriter::addHeader()
	 * 
	 * @param $argHeager
	 * @param string $pageName
	 * @return 
	 **/
	function addHeader($argHeager, $pageName = "pm1"){
	//	$this->style->addStyleHeadFoot($argHeager, "Header", $pageName);
	}
	
	/**
	 * OOoWriter::addFooter()
	 * 
	 * @param $argFooter
	 * @param string $pageName
	 * @return 
	 **/
	function addFooter($argFooter, $pageName = "pm1"){
	//	$this->style->addStyleHeadFoot($argFooter, "Footer", $pageName);
	}
	
	
	
	/**
	 * OOoTable::colSpan(), méthode de fusion de colonnes. Le numéro des colonnes commence à 0.
	 * 
	 * @param $argSpan = array(
	 			"lignSPan"	=> 1,
	 			"colDeb"	=> 1,
				"nbCol"		=> 2
				)
	 * @return none
	 **/
	function colSpan($argSpan){
		
	}
	
	
	/**
	 * OOoTable::rowSpan(), méthode de fusion de lignes. Le numéro des lignes commence à 0.
	 * 
	 * @param $argSpan = array(
	 			"lignDeb"	=> 1,
				"colDeb"	=> 1,
				"nbLign"	=> 2,
				"nbCol"		=> 2,
				)
	 * @return none
	 **/
	function rowSpan($argSpan){
		
	}

    /**
     * table::addcellData(), méthode d'ajout de données dans une cellule.
     * 
     * @param  $argData  = array(
	 			"data"			=> DATA
				"img"			=> array(
						"scr"		=> "E:/_WebDev/www/cOOlWare2/cache/c_projekte.png",
						"type"		=> "no-repeat",
						"position"	=> "bottom right"),
				"marginL"		=> "",
				"marginR"		=> "",
				"marginB"		=> "",
				"marginT"		=> "",
				"vAlign"		=> "",
				"bgColor"		=> "",
				"padding"		=> "",
				"valueType"		=> "",
				"borderL"		=> "0.002cm solid #000000",
				"borderR"		=> "0.002cm solid #000000",
				"borderB"		=> "0.002cm solid #000000",
				"borderT"		=> "0.002cm solid #000000",
				"width"			=> "10"
				)
     * @return none
     */
	function addcellData($lign, $Col, $argData){
		echo "/************************traitement des données***********************************/<br>";
		echo("Col : ".$Col."<br>");
		echo("lign : ".$lign."<br>");
	
		$tableNode = & $this->getNode("/office:document-content/office:body/table:table");
		$addCell   = FALSE;
		$addLign   = FALSE;
		$searchRow = 1;
		
		$tableRowNode = & $this->xml->createElement("table:table-row");
		$tableRowNode->setAttribute("style:name", "ro1");
		
		$tableCellNode = & $this->xml->createElement("table:table-cell");
		$tableCellNode->setAttribute("table:value-type", "float");
		$tableCellNode->setAttribute("table:value", $argData["DATA"]);
		$pNode = & $this->xml->createElement("text:p"); 
		$textNode = &$this->xml->createTextNode($argData["DATA"]);
		$pNode->appendChild($textNode);
		$tableCellNode->appendChild($pNode);
		
		if ($tableNode->hasChildNodes()) {
			echo("/**************************il existe des données dans la feuille calc****************/<br>");
			echo("tableNode->childCount : ".count($tableNode->childNodes)."<br>");
			
			$currentNode = &$tableNode->firstChild;
			$nbLign      = count($tableNode->childNodes);
			$nbCell      = count($currentNode->childNodes);
			
			if ($lign > $nbLign) $addLign = TRUE;
			if ($Col > $nbCell) $addCell = TRUE;
			
			echo("nbLign : ".$nbLign." & addLign : ".$addLign."<br>");
			echo("nbCell : ".$nbCell." & addCell : ".$addCell."<br>");
			echo("currentNode : ".($currentNode != NULL)?"NOT NULL<br>":"NULL<br>");
			
			if (!$addLign) {
				while ($currentNode != NULL) {
					if ($searchRow == $lign){
						if ($addCell){
							for ($i = $nbCell; $i < $Col-1; $i++) $currentNode->appendChild($this->ChildText("table:table-cell", ""));
							$currentNode->appendChild($tableCellNode);
						} else {
							$currentCellNode =& $currentNode->firstChild;
							$searchCell = 1;
							while ($currentCellNode != NULL) {
								if ($searchCell == $Col) {
									$parentNodeRepalce = &$currentCellNode->parentNode;
								    $parentNodeRepalce->replaceChild($tableCellNode, $currentCellNode);
									
								}
								$currentCellNode =& $currentCellNode->nextSibling;
								$searchCell++;
							}
						}
					}
					
					if ($addCell) for ($i = $nbCell; $i < $Col; $i++) $currentNode->appendChild($this->ChildText("table:table-cell", ""));
					
					$prevNode = &$currentNode;
					$currentNode =& $currentNode->nextSibling;
					$searchRow++;
				}
			} else {
				echo("/******************Ajout des lignes******************/<br>");
				while ($currentNode != NULL) {
					if ($addCell){
						echo("/******************Ajout de cellule******************/<br>");
						for ($i = $nbCell; $i < $Col; $i++) $currentNode->appendChild($this->ChildText("table:table-cell", ""));
					}
					$currentNode =& $currentNode->nextSibling;
				}
				
				for ($i = $nbLign; $i < $lign; $i++){
					$currentRowNode =& $tableRowNode->cloneNode(true);
					echo("/******************Ajout de ligne******************/<br>");
					for ($j = 1; (($i==$lign-1)?($j < $Col):($j < $Col+1)); $j++){
						$currentRowNode->appendChild($this->ChildText("table:table-cell", ""));
					}
					$tableNode->appendChild($currentRowNode);
				}
				$currentRowNode->appendChild($tableCellNode);
			}
			
		} else {
			echo("/**************************Aucune données dans la feuille calc****************/<br>");
			for ($i = 0; $i < $lign; $i++){
				$currentRowNode =& $tableRowNode->cloneNode(true);
				echo("/******************Ajout de ligne******************/<br>");
				for ($j = 1; (($i==$lign-1)?($j < $Col):($j < $Col+1)); $j++){
					$currentRowNode->appendChild($this->ChildText("table:table-cell", ""));
				}
				$tableNode->appendChild($currentRowNode);
			}
			$currentRowNode->appendChild($tableCellNode);
		}
    }
	
	
	function maxChild(&$node){
		$currentNode = &$node;
		$max = 0;
		
		while ($currentNode != NULL) {
			if ($max < $currentNode->childCount) $max = $currentNode->childCount;
			$currentNode =& $currentNode->nextSibling;
		}
		
		return $max;
	}
	
	 /**
     * table::addcellData(), méthode d'ajout de données dans une cellule.
     * 
     * @param  $argData  = array(
	 			"name"			=> Nom
				"img"			=> array(
						"scr"		=> "E:/_WebDev/www/cOOlWare2/cache/c_projekte.png",
						"type"		=> "no-repeat",
						"position"	=> "bottom right"),
				"marginL"		=> "",
				"marginR"		=> "",
				"marginB"		=> "",
				"marginT"		=> "",
				"vAlign"		=> "",
				"bgColor"		=> "",
				"padding"		=> "",
				"valueType"		=> "",
				"borderL"		=> "0.002cm solid #000000",
				"borderR"		=> "0.002cm solid #000000",
				"borderB"		=> "0.002cm solid #000000",
				"borderT"		=> "0.002cm solid #000000",
				"width"			=> "10"
				)
     * @return none
     */
	function addFeuille($argData = ""){
		//<table:table table:name="Feuille1" table:style-name="ta1">
		//<table:table-column table:style-name="co1" table:number-columns-repeated="3" table:default-cell-style-name="Default" />
		
		$automaticStylesNode =& $this->getNode("/office:document-content/office:automatic-styles");
		$styleNode =& $this->xml->createElement("style:style");
		$styleNode->setAttribute("style:name", "ta1");
		$styleNode->setAttribute("style:family", "table");
		$styleNode->setAttribute("style:master-page-name", "Default");
		$propertiesNode =& $this->xml->createElement("style:properties");
		$propertiesNode->setAttribute("table:display", "true");
		$styleNode->appendChild($propertiesNode);
		$automaticStylesNode->appendChild($styleNode);
		
		
		$bodyNode = & $this->getNode("/office:document-content/office:body");
		$tableNode = & $this->xml->createElement("table:table");
		$tableNode->setAttribute("table:name", "1erFeuille");
		$tableNode->setAttribute("table:style-name", "ta1");
		
		$bodyNode->appendChild($tableNode);
		
		
		/*
<office:automatic-styles>
	<style:style style:name="co1" style:family="table-column">
	   <style:properties fo:break-before="auto" style:column-width="2.267cm" />
	</style:style>
	
	<style:style style:name="ro1" style:family="table-row">
	   <style:properties style:row-height="0.453cm" fo:break-before="auto" style:use-optimal-row-height="true" />
	</style:style>
	
	<style:style style:name="ta1" style:family="table" style:master-page-name="Default">
	   <style:properties table:display="true" />
	</style:style>
</office:automatic-styles>
   * */
	}
}
