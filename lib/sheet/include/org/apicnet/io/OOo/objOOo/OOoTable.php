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
 * 
 * @package 
 * @author Diogene 
 * @copyright Copyright (c) 2003
 * @version $Id: OOoTable.php,v 1.3 2005-05-18 11:01:39 mose Exp $
 * @access public 
 */
class OOoTable extends absOOo {

	var $_column;
	var $_row;
	var $_lignCur;
	var $_colSpan;
	var $_rowSpan;
	var $_table = array();
	
    /**
     * OOoTable::OOoTable()
     * 
     * @param $col
     * @param $argStyle  = array(
				"img"			=> array(
						"scr"		=> "E:/_WebDev/www/cOOlWare2/cache/c_projekte.png",
						"type"		=> "no-repeat",
						"position"	=> "bottom right"),
				"marginL"		=> "",
				"marginR"		=> "",
				"marginB"		=> "",
				"marginT"		=> "",
				"align"			=> "",	//(margins|center)
				"width"			=> "",
				"bgColor"		=> ""
				)
     * @return 
     **/
    function OOoTable($column, $row, $argStyle){
        parent :: absOOo();
        $this -> _column  = $column;
		$this -> _row     = $row;
        $this -> _lignCur = 0;
		
		for($i= 0; $i < $row; $i++ ){
			for($j; $j < $column; $j++){
				$this->_table[$i][$j] = NULL;
			}
		}
		
		$this -> verifIntegrite($argStyle, "tableStyle");
		if (is_array($argStyle)) $this -> _style = $argStyle;
		else $this -> ErrorTracker(4, "L'argument de colSpan n'est pas un tableu ", 'OOoTable', __FILE__, __LINE__);
		if (isset($this -> _style["widthRow"]) && count($this -> _style["widthRow"]) != $column) $this -> ErrorTracker(4, "Error de largeurs de colonnes", 'OOoTable', __FILE__, __LINE__);
		$this -> xml = new DOMIT_Document();
    }
	
	
	/**
	 * OOoTable::colMaxNextSpan()
	 * 
	 * @param array $argArray
	 * @param integer $curentCol
	 * @return array
	 **/
	function colMaxNextSpan($argArray, $curentCol){
		$max    = $curentCol*1000000;	// un nombre très grand
		
		if (count($argArray) > 1) {
		    for(reset($argArray); $key = key($argArray); next($argArray)) {
				if ($argArray[$key]["colDeb"] > $curentCol) {
					if ($argArray[$key]["colDeb"] < $max) {
						$max    = $argArray[$key]["colDeb"];
						$result = $argArray[$key];
					} 
				}
			}
		} else {
			reset($argArray);
			$result = current($argArray);
		}
		return $result;
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
		if (is_array($argSpan)) {
			$this->verifIntegrite($argSpan, "infCSpan");
			
			if (!isset($this->_colSpan[$argSpan["lignSPan"]])){
				$this->_colSpan[$argSpan["lignSPan"]][$argSpan["colDeb"]] = $argSpan;
			} else {
				$nextSpan = $this->colMaxNextSpan($this->_colSpan[$argSpan["lignSPan"]], $argSpan["colDeb"]);
			
				//	echo $argSpan["colDeb"]." + ".$argSpan["nbCol"]." < ".$nextSpan["colDeb"]."<br>\n";
				//	echo $argSpan["colDeb"]." > ".$nextSpan["colDeb"]." + ".$nextSpan["nbCol"]."<br>\n";
			
				if ($argSpan["colDeb"] + $argSpan["nbCol"] <= $nextSpan["colDeb"] || $argSpan["colDeb"] >= $nextSpan["colDeb"] + $nextSpan["nbCol"]) {
					$this->_colSpan[$argSpan["lignSPan"]][$argSpan["colDeb"]] = $argSpan;
				} else {
					$this -> ErrorTracker(4, "Vous ne pouvez pas faire chevaucher plusieurs fusions de colonnes", 'colSpan', __FILE__, __LINE__);
				}
			}
		} else {
			$this -> ErrorTracker(4, "L'argument de colSpan n'est pas un tableu ", 'colSpan', __FILE__, __LINE__);
		}
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
		if (!is_array($argSpan)) {
			$this->verifIntegrite($argSpan, "infRSpan");
			
			if (!isset($this->_rowSpan[$argSpan["lignDeb"]])){
				$this->_rowSpan[$argSpan["lignDeb"]][$argSpan["colDeb"]] = $argSpan;
			} else {
				$nextSpan = $this->lignMaxNextSpan($this->_rowSpan[$argSpan["lignDeb"]], $argSpan["nbLign"]);
			
				//	echo $argSpan["colDeb"]." + ".$argSpan["nbCol"]." < ".$nextSpan["colDeb"]."<br>\n";
				//	echo $argSpan["colDeb"]." > ".$nextSpan["colDeb"]." + ".$nextSpan["nbCol"]."<br>\n";
			
				if ($argSpan["lignDeb"] + $argSpan["nbLign"] <= $nextSpan["lignDeb"] || $argSpan["lignDeb"] >= $nextSpan["lignDeb"] + $nextSpan["nbLign"]) {
					$this->_rowSpan[$argSpan["lignDeb"]][$argSpan["colDeb"]] = $argSpan;
				} else {
					$this -> ErrorTracker(4, "Vous ne pouvez pas faire chevaucher plusieurs fusions de colonnes", 'colSpan', __FILE__, __LINE__);
				}
			}
			
		} else {
			$this -> ErrorTracker(4, "L'argument de rowSpan n'est pas un tableu ", 'colSpan', __FILE__, __LINE__);
		}
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
	function addcellData($column, $row, $argData){
		if (is_array($argData)) {
			if ($column < $this->_column && $row < $this->_row) {
				$this->verifIntegrite($argData, "cellStyle");
			    $this->_ligne[$row][$column] = $argData;
			} 
		} else {
			$this -> ErrorTracker(4, "argData doit être un tableau", 'colSpan', __FILE__, __LINE__);
		}
    }
	
	
	function run(&$nodeContent, &$nodeStyle, $dir){
		static $STYLNUM;
		if (!isset($STYLNUM)){
			$STYLNUM = array(
					'tableau' 	=> 1,
					'table_cel' => 1,
					'table_row' => 1
			);
		}
		
		$StyleName = "Tableau".$STYLNUM['tableau'];
		$STYLNUM['tableau']++;
		
		$styleNode =& $this->xml->createElement("style:style");
		$styleNode->setAttribute("style:name", $StyleName);
		$styleNode->setAttribute("style:family", "table");
		
		$propertiesNode =& $this->xml->createElement("style:properties");
		if (isset($this->_style["width"])) $propertiesNode->setAttribute("style:width", $this->_style["width"]."cm" );
		if (isset($this->_style["align"])) $propertiesNode->setAttribute("table:align", $this->_style["align"]);

		if (isset($this->_style["marginL"])) $propertiesNode->setAttribute("fo:margin-left", $this->_style["marginL"]."cm" );
		if (isset($this->_style["marginR"])) $propertiesNode->setAttribute("fo:margin-right", $this->_style["marginR"]."cm" );
		if (isset($this->_style["marginT"])) $propertiesNode->setAttribute("fo:margin-top", $this->_style["marginT"]."cm" );
		if (isset($this->_style["marginB"])) $propertiesNode->setAttribute("fo:margin-bottom", $this->_style["marginB"]."cm" );

		
		if (isset($this->_style["img"]) && isset($this->_style["img"]["src"])) {
		    $backgroundImageNode =& $this->xml->createElement("style:background-image");
				
			$ext = substr($this->_style["img"]["src"], strlen($file)-3);
			$tmpfile = rand().".".$ext;
			copy($this->_style["img"]["src"], $dir."/Pictures/".$tmpfile);
			
			$backgroundImageNode->setAttribute("xlink:href", "#Pictures/".$tmpfile);
			$backgroundImageNode->setAttribute("xlink:type", "simple");
			$backgroundImageNode->setAttribute("xlink:actuate", "onLoad");
			if (isset($this->_style["img"]["type"])) $backgroundImageNode->setAttribute("style:repeat", $this->_style["img"]["type"]);
			if (isset($this->_style["img"]["position"])) $backgroundImageNode->setAttribute("style:position", $this->_style["img"]["position"]);
			
			$propertiesNode->setAttribute("fo:background-color", "transparent");
			$propertiesNode->appendChild($backgroundImageNode);
		} else {
			$propertiesNode->appendChild($this->ChildText("style:background-image", ""));
		}
		
		$styleNode->appendChild($propertiesNode);
		$nodeStyle->appendChild($styleNode);
		
		/*********************Création du tableau**********************/
		$tableNode =& $this->xml->createElement("table:table");
		$tableNode->setAttribute("table:name", $StyleName);
		$tableNode->setAttribute("table:style-name", $StyleName);
		
		$nodeContent->appendChild($tableNode);
		/**********************Fin de Création*************************/
		
		$rowSpanNb = 0;
		
		for ($i = 0; $i < $this->_row; $i++ ){
			
			$tableRowNode =& $this->xml->createElement("table:table-row");
			if ($i === 0) {
				$tableHeaderRowsNode =& $this->xml->createElement("table:table-header-rows");
				$tableHeaderRowsNode->appendChild($tableRowNode);
			}

			for($j = 0; $j < $this->_column; $j++){
				
				/**********************Création du style de la colonne*********************************/
	  			if (isset($this -> _style["widthRow"][$j]) && $i === 0) {
		  			$StyleNameR = $StyleName.".R".$STYLNUM['table_row'];
					$STYLNUM['table_row']++;
		  			$styleNode =& $this->xml->createElement("style:style");
					$styleNode->setAttribute("style:name", $StyleNameR);
					$styleNode->setAttribute("style:family", "table-column");
					$propertiesNode =& $this->xml->createElement("style:properties");
					if (isset($this -> _style["widthRow"])) $propertiesNode->setAttribute("style:column-width", $this -> _style["widthRow"][$j]."cm");
					$styleNode->appendChild($propertiesNode);
					$nodeStyle->appendChild($styleNode);
					
					$tableColumnNode =& $this->xml->createElement("table:table-column");
					$tableColumnNode->setAttribute("table:style-name", $StyleNameR);
					$tableNode->appendChild($tableColumnNode);
	  			} else {
					if ($i === 0 && $j === 0) {
					    $tableColumnNode =& $this->xml->createElement("table:table-column");
						$tableColumnNode->setAttribute("table:style-name",$StyleName."A");
						$tableColumnNode->setAttribute("table:number-columns-repeated", $this->_column);
						
						$tableNode->appendChild($tableColumnNode);
					}
				}
				/*********************Fin de la Création du style de la colonne**************************/
			   
				/*****************************Création du style de la cellule************************/
				$StyleNameC = $StyleName.".C".$STYLNUM['table_cel'];
				$STYLNUM['table_cel']++;
				$styleNode =& $this->xml->createElement("style:style");
				$styleNode->setAttribute("style:name", $StyleNameC);
				$styleNode->setAttribute("style:family", "table-cell");
				$propertiesNode =& $this->xml->createElement("style:properties");
				if (isset($this->_ligne[$i][$j]["padding"])) $propertiesNode->setAttribute("fo:padding", $this->_ligne[$i][$j]["padding"]."cm");
				if (isset($this->_ligne[$i][$j]["vAlign"])) $propertiesNode->setAttribute("fo:vertical-align", $this->_ligne[$i][$j]["vAlign"]);
				if (isset($this->_ligne[$i][$j]["bgColor"])) $propertiesNode->setAttribute("fo:background-color", $this->_ligne[$i][$j]["bgColor"]);

				if (isset($this->_style["borderL"])) $propertiesNode->setAttribute("fo:border-left", $this->_style["borderL"]);
				if (isset($this->_style["borderR"])) $propertiesNode->setAttribute("fo:border-right", $this->_style["borderR"]);
				if (isset($this->_style["borderT"])) $propertiesNode->setAttribute("fo:border-top", $this->_style["borderT"]);
				if (isset($this->_style["borderB"])) $propertiesNode->setAttribute("fo:border-bottom", $this->_style["borderB"]);
				if (isset($this->_ligne[$i][$j]["borderL"])) $propertiesNode->setAttribute("fo:border-left", $this->_ligne[$i][$j]["borderL"]);
				if (isset($this->_ligne[$i][$j]["borderR"])) $propertiesNode->setAttribute("fo:border-right", $this->_ligne[$i][$j]["borderR"]);
				if (isset($this->_ligne[$i][$j]["borderT"])) $propertiesNode->setAttribute("fo:border-top", $this->_ligne[$i][$j]["borderT"]);
				if (isset($this->_ligne[$i][$j]["borderB"])) $propertiesNode->setAttribute("fo:border-bottom", $this->_ligne[$i][$j]["borderB"]);
				
				if (isset($this->_ligne[$i][$j]["img"]) && isset($this->_ligne[$i][$j]["img"]["src"])) {
				    $backgroundImageNode =& $this->xml->createElement("style:background-image");
					
					$ext = substr($this->_ligne[$i][$j]["img"]["src"], strlen($file)-3);
					$tmpfile = rand().".".$ext;
					copy($this->_ligne[$i][$j]["img"]["src"], $dir."/Pictures/".$tmpfile);
					
					$backgroundImageNode->setAttribute("xlink:href", "#Pictures/".$tmpfile);
					$backgroundImageNode->setAttribute("xlink:type", "simple");
					$backgroundImageNode->setAttribute("xlink:actuate", "onLoad");
					if (isset($this->_ligne[$i][$j]["img"]["type"])) $backgroundImageNode->setAttribute("style:repeat", $this->_ligne[$i][$j]["img"]["type"]);
					if (isset($this->_ligne[$i][$j]["img"]["position"])) $backgroundImageNode->setAttribute("style:position", $this->_ligne[$i][$j]["img"]["position"]);
					
					$propertiesNode->setAttribute("fo:background-color", "transparent");
					$propertiesNode->appendChild($backgroundImageNode);
				} else {
					$propertiesNode->appendChild($this->ChildText("style:background-image", ""));
				}
				
				$styleNode->appendChild($propertiesNode);
				$nodeStyle->appendChild($styleNode);
				/**********************Fin de Création*************************/
				
				/********************Création de la cellule********************/
			/*	"lignDeb"		=> TRUE,
				"colDeb"		=> TRUE,
				"nbLign"		=> TRUE,
				"nbCol"			=> TRUE,
				)*/
				
				
				
				if (isset($this->_rowSpan["lignDeb"]) || $this->_rowSpan["lignDeb"] + $this->_rowSpan["nbLign"] < $i) {
				
					if ($this->_rowSpan["lignDeb"] == $i && $this->_rowSpan["colDeb"] == $j) {
						
					} else {
						$subTableNode = & $this->xml->createElement("table:sub-table");
						$tableColumnNode = & $this->xml->createElement("table:table-column");
					}
				
					
				//	$tableCellNode->setAttribute("table:number-columns-spanned", );
				//	$tableCellNode->setAttribute("table:number-columns-repeated", );
				}
				
				
				
				
				$tableCellNode =& $this->xml->createElement("table:table-cell");
				$tableCellNode->setAttribute("table:style-name", $StyleNameC);
				$tableCellNode->setAttribute("table:value-type", "string");
				
				$curCol = $j;
				if (isset($this->_colSpan[$i][$j])) {
				//	echo("_colSpan : ========================<br>\n");
				//	print_r($this->_colSpan[$i]);
				//	echo("et taille : ".count($this->_colSpan[$i])."<br>\n");
					
		 			$tableCellNode->setAttribute("table:number-columns-spanned", $this->_colSpan[$i][$j]["nbCol"]);
					$coveredTableCellNode =& $this->xml->createElement("table:covered-table-cell");
					
					$tableRowNode->appendChild($coveredTableCellNode);
					$j = $this->_colSpan[$i][$j]["nbCol"] + $j - 1;
				//	echo($j . "<br>");
				} 
				
				if (isset($this->_ligne[$i][$curCol]["data"]) && is_object($this->_ligne[$i][$curCol]["data"])){
					$this->_ligne[$i][$curCol]["data"]->run($tableCellNode, $nodeStyle, $dir);
				} else {
					$pNode =& $this->xml->createElement("text:p");
					$pNode->setAttribute("text:style-name","Standard");
					$tableCellNode->appendChild($pNode);
				}
				
				$tableRowNode->appendChild($tableCellNode);
				/**********************Fin de Création*************************/
			}
			
			if ($i === 0) $tableNode->appendChild($tableHeaderRowsNode);
			else $tableNode->appendChild($tableRowNode);
			
		}
	}
} 

?>
