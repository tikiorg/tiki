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

APIC::import("org.apicnet.xml.DOMIT_Document");
APIC::import("org.apicnet.io.File");


/**
 * absOOo, classe d'abstraction de manipulation des fichiers XML, de sauvegarde des documents et de verification des données envoyé au parser OOo
 * 
 * @package 
 * @author diogene
 * @copyright Copyright (c) 2004
 * @version $Id: absOOo.php,v 1.3 2005-05-18 11:01:39 mose Exp $
 * @access public
 **/
class absOOo extends ErrorManager {
	
	var $DIRXML			 = "";						// chemin du répertoire ou se situe le xml instancié
	var $XMLTYPE 		 = array(					// Tableau des types de document pouvant être généré
			'Writer',
			'Calc',
			'Impress',
			'Draw');
	var $MIME			 = array(					// Instance tu type mime des fichiers OpenOffice crée
			'Writer' => "vnd.sun.xml.writer",
			'Calc' => "vnd.sun.xml.calc");
	var $FILENAME;									// Nom du ficheir xml instanciée
	var $xml;										// instance correspondant au xml chargé
	var $ARGDATA		= array(					// Tableau des données pouvant être envoyé au parseur OOo
		"PageStyle"	=>  array(						// lorsque la donnée a comme valeur TRUE cela signifie qu'elle est obligatoire si la valeur est FALSE elle est facultative et si une valeur est présente alors c'est la valeur par défaut dans le cas ou elle ne serai pas donnée
			"NameStyle"		=> TRUE,				// 
			"NameStyleSuiv"	=> FALSE,				// Nom du style de page suivant
			"pageWidth"		=> "20.999",			
			"pageHeight"	=> "29.699",			// ne serai pas donnée
			"printOrient"	=> FALSE,				// (portrait|paysage)
			"marginT"		=> "2",
			"marginB"		=> "2",
			"marginL"		=> "2",
			"marginR"		=> "2",
			"writingMode"	=> "lr-tb"),
		"Header"	=> array(
			"Text"			=> FALSE,
			"marginB"		=> FALSE,				// Ecart entre l'entête et le corps du document
			"marginL"		=> FALSE,				// Marge de gauche de l'entête
			"marginR"		=> FALSE,				// Marge de Droite de l'entête
			"minHeight"		=> FALSE,				// Hauteur de l'entête
			"align"			=> FALSE,				// Alignement du texte de l'entête (left|center|right|justify)
			"BgColor"		=> FALSE,				// Couleur de fond de l'entête dans le cas ou ce dernier n'a pas d'image #color en hexa
			"color"			=> FALSE),				// Couleur du text
		"Footer"	=> array(
			"Text"			=> FALSE,
			"marginB"		=> FALSE,				// Ecart entre l'entête et le corp du document
			"marginL"		=> FALSE,				// Marge de gauche de l'entête
			"marginR"		=> FALSE,				// Marge de Droite de l'entête
			"minHeight"		=> FALSE,				// Hauteur de l'entête
			"align"			=> FALSE,				// Alignement du texte de l'entête (left|center|right|justify)
			"BgColor"		=> FALSE,				// Couleur de fond de l'entête dans le cas ou ce dernier n'a pas d'image #color en hexa
			"color"			=> FALSE),				// Couleur du text
		"img"		=> array(
			"src"			=> TRUE,
			"type"			=> "no-repeat",			// (no-repeat|repeat|stretch)
			"position"		=> "center center"),	// (top|bottom|center) (left|right|center)
		"paraStyle"			=> array(
			"paraName"		=> FALSE,
			"marginL"		=> FALSE,				// double
			"marginR"		=> FALSE,				// double
			"marginB"		=> FALSE,				// double
			"marginT"		=> FALSE,				// double
			"align"			=> FALSE,				// (center|right|left|justify)
			"indent"		=> FALSE,				// (true|false)
			"autoIndent"	=> FALSE,				// (true|false)
			"bgColor"		=> FALSE,				// #color en hexa
			"padding"		=> FALSE,				// double
			"fontName"		=> FALSE,
			"fontSize"		=> FALSE,
			"border"		=> FALSE,				// "0.002cm solid #000000" c'est-a-dire taille du trait type du trait et couleur du trait
			"singleWord"	=> "false"),
		"textStyle"	=> array(
			"NameStyle"		=> TRUE,
			"Text"			=> FALSE,
			"lineBreak"		=> FALSE,
			"tabStop"		=> FALSE),
		"styleText"		=> array(
			"italic"		=> FALSE,				// true
			"bold"			=> FALSE,				// true
			"underline"		=> FALSE,				// true
			"bgColor"		=> FALSE,				// true
			"color"			=> FALSE,				// true
			"fontName"		=> FALSE,				// Le nom d'une police de caractère
			"fontSize"		=> FALSE),				// integer
		"tableStyle"	=> array(
			"marginL"		=> FALSE,				// double
			"marginR"		=> FALSE,				// double
			"marginB"		=> FALSE,				// double
			"marginT"		=> FALSE,				// double
			"align"			=> FALSE,				//(margins|center)
			"width"			=> FALSE,				// double
			"widthRow"		=> FALSE,				// array des valeurs des colonnes
			"bgColor"		=> FALSE),				// #color en hexa
		"widthRow"		=> array(),
		"cellStyle"		=> array(
			"data"			=> FALSE,				// 
			"marginL"		=> FALSE,				// 
			"marginR"		=> FALSE,				// 
			"marginB"		=> FALSE,				// 
			"marginT"		=> FALSE,				// 
			"vAlign"		=> FALSE,				//
			"bgColor"		=> FALSE,				// 
			"padding"		=> FALSE,				// 
			"valueType"		=> "string",			// (string|)
			"borderL"		=> FALSE,				// 
			"borderR"		=> FALSE,				// 
			"borderB"		=> FALSE,				// 
			"borderT"		=> FALSE,				//
			"dataType"		=> FALSE),				// 
		"imgStyle"		=> array(
			"src"			=> TRUE,
			"z-index"		=> "0",
			"height"		=> FALSE,
			"width"			=> FALSE,
			"anchorType"	=> "as-char",			//(as-char|paragraph)
			"horizontalPos"	=> "center",			//(middle|center)
			"horizontalRel"	=> "paragraph",			//(text|paragraph)
			"mirror"		=> "none",
			"clip"			=> "rect(0cm 0cm 0cm 0cm)",
			"luminance"		=> "0",
			"contrast"		=> "0",
			"red"			=> "0",
			"green"			=> "0",
			"blue"			=> "0",
			"gamma"			=> "1",
			"colorInversion"=> "false",				//(true|false)
			"transparency"	=> "0",
			"color-mode"	=> "standard"),
		"infCSpan"		=> array(
			"lignSPan"		=> TRUE,
	 		"colDeb"		=> TRUE,
			"nbCol"			=> TRUE),
		"infRSpan"		=> array(
	 		"lignDeb"		=> TRUE,
			"colDeb"		=> TRUE,
			"nbLign"		=> TRUE,
			"nbCol"			=> TRUE,
			)
		);
	
	/**
	 * absOOo::absOOo(), constructeur permettant uniquement d'instancié la classe pere de gestion des erreurs
	 * 
	 * @return none
	 * @access public
	 **/
	function absOOo(){
		parent::ErrorManager();
	}

	
	/**
	 * absOOo::decode_text(), methode pour décode un text utf8 en texte local avec les caractères accentués et les sigles
	 * 
	 * @param String $str chaine de caractère a coder
	 * @return String la chaine de caractère decodé
	 * @access public
	 **/
	function decode_text($str){
		//return iconv('UTF-8', 'ISO-8859-1', $str);
	}
	
	/**
	 * absOOo::encode_text(), methode pour encoder un texte locale en texte utf8 (norme internationnal de codage des caractères)
	 * 
	 * @param String $str chaine de caractère a encoder
	 * @return String la chaine de caractère encoder
	 * @access public
	 **/
	function encode_text($str){
		$str = iconv('ISO-8859-1', 'UTF-8', $str);
		$tbl["'"]="&apos;";
		$tbl["<"]="&lt;";
		$tbl[">"]="&gt;";
		$tbl["&nbsp;"]="&#160;";
		$tbl["\""]="&#34;";
		$tbl[" "]="&#32;";
		
		return str_replace(array_keys($tbl), array_values($tbl), $str);
	}
	
	
	/**
	 * absOOo::save(), sauvegarde le fichier xml courant
	 * 
	 * @return  none
	 * @access private
	 **/
	function save(){
		$XMLContent = $this->toString();

		$xmlFile = new File($this->DIRXML."/".$this->FILENAME, TRUE);
		if ($xmlFile->exists()) {
			$xmlFile->delFile();
			$xmlFile->createFile();
		}
		
		$xmlFile->writeData($XMLContent);
	}
	
	/**
	 * absOOo::verifIntegrite(), vérifie l'intégrité des données pour la génération du document OOo
	 * 
	 * @param array $arrayData le tableau de données a vérifier
	 * @param String $typeArray le type du tableau de données. les type de données sont : "PageStyle", "Header", "Footer", "img", "paraStyle", "textStyle", "styleCarac", "tableStyle", 
	 * @return none
	 * @access private
	 **/
	function verifIntegrite(&$arrayData, $typeArray){
		$ArrVerif = $this->ARGDATA[$typeArray];
		
		for(reset($ArrVerif); $key = key($ArrVerif); next($ArrVerif)) {
			if (is_array($arrayData[$key])) {
				$this->verifIntegrite($arrayData[$key], $key);
			} else {
				if ($ArrVerif[$key] === TRUE && !isset($arrayData[$key])) {
					$this -> ErrorTracker(4, "Le tableau en argument doit obligatoirement contenir l'information ".$key, 'verifIntegrite', __FILE__, __LINE__);
				} else {
					//echo($key." : ".$ArrVerif[$key]." et ".$arrayData[$key] ."<br>");
					if (!isset($arrayData[$key]) && $ArrVerif[$key] != FALSE) $arrayData[$key] = $ArrVerif[$key];
				}
			}
		}
	}
	
	
	function &setProperties($style, $dir){
		if (is_array($style)) {
			
			$propertiesNode =& $this->xml->createElement("style:properties");
			if (isset($style["bold"])){
				$propertiesNode->setAttribute("fo:font-weight", "bold");
				$propertiesNode->setAttribute("style:font-weight-asian", "bold");
				$propertiesNode->setAttribute("style:font-weight-complex", "bold");
			}
			if (isset($style["underline"]))$propertiesNode->setAttribute("style:text-underline", "single");
			if (isset($style["italic"])){
				$propertiesNode->setAttribute("fo:font-style", "italic");
				$propertiesNode->setAttribute("style:font-style-asian", "italic");
				$propertiesNode->setAttribute("style:font-style-complex", "italic");
			}
			if (isset($style["fontName"]))$propertiesNode->setAttribute("style:font-name", $style["fontName"]);
			if (isset($style["fontSize"])){
				$propertiesNode->setAttribute("fo:font-size", $style["fontSize"]."pt");
				$propertiesNode->setAttribute("style:font-size-asian", $style["fontSize"]."pt");
				$propertiesNode->setAttribute("style:font-size-complex", $style["fontSize"]."pt");
			}
			if (isset($style["bgColor"]))$propertiesNode->setAttribute("style:text-background-color", $style["bgColor"]);
			if (isset($style["color"]))$propertiesNode->setAttribute("fo:color", $style["color"]);
 			if (isset($style["minHeight"])) $propertiesNode->setAttribute("fo:min-height", $style["minHeight"]."cm");
			if (isset($style["marginL"])) $propertiesNode->setAttribute("fo:margin-left", $style["marginL"]."cm" );
			if (isset($style["marginR"])) $propertiesNode->setAttribute("fo:margin-right", $style["marginR"]."cm" );
			if (isset($style["marginT"])) $propertiesNode->setAttribute("fo:margin-top", $style["marginT"]."cm" );
			if (isset($style["marginB"])) $propertiesNode->setAttribute("fo:margin-bottom", $style["marginB"]."cm" );
			if (isset($style["align"])) $propertiesNode->setAttribute("fo:text-align",  $style["align"]);
			if (isset($style["singleWord"])) $propertiesNode->setAttribute("style:justify-single-word",  $style["singleWord"]);
			if (isset($style["indent"])) $propertiesNode->setAttribute("fo:text-indent", $style["indent"]."cm" );
			if (isset($style["autoIndent"])) $propertiesNode->setAttribute("style:auto-text-indent",  $style["autoIndent"]);
			$propertiesNode->setAttribute("fo:background-color", "transparent" );
			if (isset($style["padding"])) $propertiesNode->setAttribute("fo:padding", $style["padding"]."cm" );
			if (isset($style["border"])) $propertiesNode->setAttribute("fo:border", $style["border"]);
			if (isset($style["color"])) $propertiesNode->setAttribute("fo:color", $style["color"]);
			
			$propertiesNode->setAttribute("style:page-number", "0");
			$propertiesNode->setAttribute("style:dynamic-spacing", "false");
		
			if (isset($style["img"]) && isset($style["img"]["src"])) {
			    $backgroundImageNode =& $this->xml->createElement("style:background-image");
				
				$ext = substr($style["img"]["src"], strlen($file)-3);
				$tmpfile = rand().".".$ext;
				copy($style["img"]["src"], $dir."/Pictures/".$tmpfile);
				
				$backgroundImageNode->setAttribute("xlink:href", "#Pictures/".$tmpfile);
				$backgroundImageNode->setAttribute("xlink:type", "simple");
				$backgroundImageNode->setAttribute("xlink:actuate", "onLoad");
				if (isset($style["img"]["type"])) $backgroundImageNode->setAttribute("style:repeat", $style["img"]["type"]);
				if (isset($style["img"]["position"])) $backgroundImageNode->setAttribute("style:position", $style["img"]["position"]);
				
				$propertiesNode->setAttribute("fo:background-color", "transparent");
				$propertiesNode->appendChild($backgroundImageNode);
			} else {
				$propertiesNode->appendChild($this->ChildText("style:background-image", ""));
			}
			
			
			if (isset($style["tabs"])) {
				$propertiesNode->appendChild($this->setTabs($style["tabs"]));
			}
			
			return $propertiesNode;
		} else {
			$this -> ErrorTracker(4, "Le tableau en argument doit obligatoirement contenir l'information ".$key, 'setProperties', __FILE__, __LINE__);
		}
	}
	
	
	/**
	 * absOOo::setTabs()
	 * 
	 * @param $tabs = array(
	 * 			array(
				  		"position"	 => "18cm",
				 		"type"		 => "right", 
						"leaderChar" => "_"
				),
				array(...
	 * ) 
	 * @return 
	 **/
	function setTabs($tabs){
		if (is_array($tabs)) {
			
			$tabsNode =& $this->xml->createElement("style:tab-stops");
				
				for($i=0; $i < count($tabs); $i++){
					$tabNode =& $this->xml->createElement("style:tab-stop");
					$tabNode->setAttribute("style:position", $tabs[$i]["position"]);
					$tabNode->setAttribute("style:type", $tabs[$i]["type"]);
					if (isset($tabs[$i]["leaderChar"])) $tabNode->setAttribute("style:leader-char", $tabs[$i]["leaderChar"]);
					$tabsNode->appendChild($tabNode);
				}
			
			return $tabsNode;
		} else {
			$this -> ErrorTracker(4, "Les tabulation sont mal définies", 'setTabs', __FILE__, __LINE__);
		}
	}
	
	/**
	 * absOOo::accessor(), méthode de recherche d'un enfant. cette méthode ne renvoit pas un pointeur sur l'enfant mais une copie de l'enfant
	 * 
	 * @param String $path le chemin du noeud recherché
	 * @param integer $item la position de l'enfant recherché
	 * @return DOMIT_Nodes l'enfant rechercher
	 * @access private
	 **/
	function &accessor($path, $item = NULL){
		if (eregi("@", $path)) {
		
			$arrPath     = split("@", $path);
			$arrAtt		 = split("=", $arrPath[1]);
			
			$attName	 = substr($arrAtt[0], 1);
			$attValue	 = substr($arrAtt[1], 1, strlen($arrAtt[1]) - 3);
			
			$path	 	 = $arrPath[0];
			$strAtt		 = $arrPath[1];
		}
		
		$arrPath       = split("/", $path);
		$currentNode   = & $this->xml->documentElement;
		
		for($i=1; $i < count($arrPath); $i++){
			$nodeListTemp = new DOMIT_NodeList();
			$currentNode->getNamedElements($nodeListTemp, $arrPath[$i]);
			
			if ($nodeListTemp->getLength() == 0) {
				return NULL;
			}
		}
		
		if ($strAtt != "") {
				$find = FALSE;
				for ($i = 0; $i < $nodeListTemp->getLength(); $i++) {
					$node =& $nodeListTemp->item($i);
					if ($node->getAttribute($attName) == $attValue) {
						return $node;
						$find = TRUE;
					} 
				}
				
				if (!$find) return NULL;
		} else {
			if (isset($item) && $item < $nodeListTemp->getLength()) return $nodeListTemp->item($item);
			else  return NULL;
		}
	}
	
	/**
	 * absOOo::getNodeRec()
	 * 
	 * @param DOMIT_Nodes $node
	 * @param String $path
	 * @return DOMIT_Nodes
 	 * @access private
	 **/
	function &getNodeRec(&$node, $path){
		
		$arrPath     = split("/", $path);
		
	//	echo("<h4>".$arrPath[1]."</h4>");
		
		if (eregi("@", $arrPath[1])) {
			$arrNode     = split("@", $arrPath[1]);
			$arrAtt		 = split("=", $arrNode[1]);
			
			$attName	 = substr($arrAtt[0], 1);
			$attValue	 = substr($arrAtt[1], 1, strlen($arrAtt[1]) - 3);
			
			$strAtt		 = $arrNode[1];
			$curNodeName = $arrNode[0];
		} else {
			$curNodeName = $arrPath[1];
		}

		$tmpPath     = "/".implode("/", array_slice ($arrPath, 2));
	//	echo($path."======>tmpPath  : ".$tmpPath." et curNodeName : ".$curNodeName."<br>");
		$currentNode = &$node;
		$find        = FALSE;
		
		
		if ($curNodeName == $currentNode->nodeName && count($arrPath) == 2) {
		    return $currentNode;
		} else {
			
			if ($curNodeName == $currentNode->nodeName) {
				return $this->getNodeRec($currentNode->firstChild, $tmpPath);
			} else {
				$currentNode =& $currentNode->nextSibling;
				while (!$find && $currentNode != NULL) {
	//				echo("<b>".$curNodeName." == ".$currentNode->nodeName."</b><br>");
					if ($currentNode->nodeName == $curNodeName) {
	//					echo("strAtt : ".$strAtt."<br>");
						if ($strAtt != "") {
							$currentAtt = $currentNode->getAttribute($attName);
	//						echo("getAttribute : ".$currentNode->getAttribute($attName)."<br>");
							if ($currentAtt == $attValue) {
							    $find = TRUE;
							} else {
								$currentNode =& $currentNode->nextSibling;
							}
						} else {
							$find = TRUE;
						}
					} else {
						$currentNode =& $currentNode->nextSibling;
					}
				}
				
				if ($find) {
					if ($find && count($arrPath) == 2)  return $currentNode;
					else return $this->getNodeRec($currentNode->firstChild, $tmpPath);
				} else {
					$this -> ErrorTracker(4, 'Le noeud '.$arrPath[1]." est introuvable", 'getNodeRec', __FILE__, __LINE__);
				}
			}
		}
	}
	
	/**
	 * absOOo::getNode()
	 * 
	 * @param String $path
	 * @return DOMIT_Nodes 
	 * @access private
	 **/
	function &getNode($path){
	//	echo("<h2>".$path."</h2>");
		return $this->getNodeRec($this->xml->documentElement, $path);
	}
	
	/**
	 * absOOo::removeNode()
	 * 
	 * @param $path
	 * @return none
	 * @access private
	 **/
	function removeNode($path){
		$arrPath     = split("/", $path);
		$parentPath  = "/".implode("/", array_slice ($arrPath, 2));
		$childPath	 = $path;
		$parentNode	 = &$this->getNode($parentPath);
		$childNode	 = &$this->getNode($childPath);
		
		$parentNode->removeNode($childNode);
	}
	
	/**
	 * absOOo::ssNodeExist()
	 * 
	 * @param DOMIT_Nodes $node
	 * @param Boolean $nodeSearch
	 * @return boolean vrai si le sous-noeud exist dans le noeud, faux dans le cas contraire
	 * @access private
	 **/
	function ssNodeExist(&$node, $nodeSearch){
		$currentNode = &$node;
		$find        = FALSE;
		if (eregi("@", $nodeSearch)) {
			$arrNode     = split("@", $nodeSearch);
			$arrAtt		 = split("=", $arrNode[1]);
			
			$nodeName	 = $arrNode[0];
			$attName	 = substr($arrAtt[0], 1);
			$attValue	 = substr($arrAtt[1], 1, strlen($arrAtt[1]) - 3);
		} else {
			$nodeName = $nodeSearch;
		}
		
		while (!$find && $currentNode != NULL) {
			if ($currentNode->nodeName == $nodeName) {
				if (eregi("@", $nodeSearch)) {
					$currentAtt = $currentNode->getAttribute($attName);
					
					if ($currentAtt = $attValue) {
					    $find = TRUE;
						break;
					} 
				} else {
					$find = TRUE;
					break;
				}
			} else {
				$currentNode =& $currentNode->nextSibling;
			}
		}
		if (!$find) return FALSE;
		else return TRUE;
		
	}
	
	/**
	 * absOOo::countNode()
	 * 
	 * @param String $path
	 * @return Integer 
	 * @access private
	 **/
	function countNode($path){
		$arrPath       = split("/", $path);
		$currentNode   = & $this->xml->documentElement;
		
		for($i=1; $i < count($arrPath); $i++){
			
			$nodeListTemp = new DOMIT_NodeList();
			$currentNode->getNamedElements($nodeListTemp, $arrPath[$i]);
			
			if ($nodeListTemp->getLength() == 0) {
				return NULL;
			}
		}
		
		return $nodeListTemp->getLength();
	}
	
	/**
	 * absOOo::setNodeText()
	 * 
	 * @param String $path
	 * @param String $text
	 * @return none
	 * @access private
	 **/
	function setNodeText($path, $text){
		$node     = &$this->getNode($path);
	//	$textNode = &$this->xml->createTextNode($this->encode_text($text));
		$textNode = &$this->xml->createTextNode($text);

		if ($node->hasChildNodes()) {
			$node->replaceChild($textNode, $node->lastChild);
		} else {
			$node->appendChild($textNode);
		}
	}
	
	
	/**
	 * absOOo::ChildText()
	 * 
	 * @param String $tagName, nom de l'élément (balise) à créer
	 * @param String $text, valeur de la balise créée
	 * @return DOMIT_Node le nouveau noeud crée
	 * @access private
	 **/
	function ChildText($tagName, $text){
		$appChildNode = &$this->xml->createElement($tagName);
	//	$appChildNode->appendChild($this->xml->createTextNode($this->encode_text($text)));
		$appChildNode->appendChild($this->xml->createTextNode($text));
		return $appChildNode;
	}
	
	
	/**
	 * absOOo::toString(), convertir l'arbre xml en chaine de caractère.
	 * 
	 * @return String 
	 * @access private
	 **/
	function toString(){
		return $this->xml->toString(); 
	}
}
