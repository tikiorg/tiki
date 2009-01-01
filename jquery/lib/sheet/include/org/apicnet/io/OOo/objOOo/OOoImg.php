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

/**
 * 
 * @package 
 * @author Diogene 
 * @copyright Copyright (c) 2003
 * @version $Id: OOoImg.php,v 1.3 2005-05-18 11:01:39 mose Exp $
 * @access public 
 */
class OOoImg extends absOOo {

	var $_styleImg;
	
    /**
     * OOoTable::OOoTable()
     * 
     * @param $col
     * @param $argStyle  = array(
				"src"			=> "E:/_WebDev/www/cOOlWare2/cache/c_projekte.png",
				"z-index"		=> "",
				"height"		=> "",
				"width"			=> "",
				"anchorType"	=> "",		//(as-char|paragraph)
				"horizontal-pos	=> "center",
				"horizontal-rel	=> "paragraph",
				"mirror			=> "none",
				"clip			=> "rect(0cm 0cm 0cm 0cm)",
				"luminance		=> "0%",
				"contrast		=> "0%",
				"red			=> "0%",
				"green			=> "0%",
				"blue			=> "0%",
				"gamma			=> "1",
				"color-inversion=> "false",
				"transparency	=> "0%",
				"color-mode		=> "standard"
				)
     * @return none
     **/
    function OOoImg($argStyle){
        parent :: absOOo();
		
		if (is_array($argStyle)) {
			$this -> verifIntegrite($argStyle, "imgStyle");
			$this->_styleImg = $argStyle;
		}
		else  $this -> ErrorTracker(4, "L'argument de colSpan n'est pas un tableu ", 'colSpan', __FILE__, __LINE__);
		
		$this -> xml    = new DOMIT_Document();
    }
	
	
	function run(&$nodeContent, &$nodeStyle, $dir){
		static $STYLNUM;
		if (!isset($STYLNUM)){
			$STYLNUM = array(
					'style' => 1,
					'name'  => 1
			);
		}
		$StyleName = "fr".$STYLNUM['style'];
		$STYLNUM['style']++;
		
		$name = "Image".$STYLNUM['name'];
		$STYLNUM['name']++;

		$styleNode =& $this->xml->createElement("style:style");
		$styleNode->setAttribute("style:name", $StyleName);
		$styleNode->setAttribute("style:family", "graphics");
		$styleNode->setAttribute("style:parent-style-name", "Graphics");
		$propertiesNode =& $this->xml->createElement("style:properties");
		if (isset($this->_styleImg["horizontalPos"])) $propertiesNode->setAttribute("style:horizontal-pos", $this->_styleImg["horizontalPos"] );
		if (isset($this->_styleImg["horizontalRel"])) $propertiesNode->setAttribute("style:horizontal-rel", $this->_styleImg["horizontalRel"] );
		if (isset($this->_styleImg["mirror"])) $propertiesNode->setAttribute("style:mirror", $this->_styleImg["mirror"] );
		if (isset($this->_styleImg["clip"])) $propertiesNode->setAttribute("fo:clip", $this->_styleImg["clip"] );
		if (isset($this->_styleImg["luminance"])) $propertiesNode->setAttribute("draw:luminance", $this->_styleImg["luminance"]."%" );
		if (isset($this->_styleImg["contrast"])) $propertiesNode->setAttribute("draw:contrast", $this->_styleImg["contrast"]."%" );
		if (isset($this->_styleImg["red"])) $propertiesNode->setAttribute("draw:red", $this->_styleImg["red"]."%" );
		if (isset($this->_styleImg["green"])) $propertiesNode->setAttribute("draw:green", $this->_styleImg["green"]."%" );
		if (isset($this->_styleImg["blue"])) $propertiesNode->setAttribute("draw:blue", $this->_styleImg["blue"]."%" );
		if (isset($this->_styleImg["gamma"])) $propertiesNode->setAttribute("draw:gamma", $this->_styleImg["gamma"] );
		if (isset($this->_styleImg["colorInversion"])) $propertiesNode->setAttribute("draw:color-inversion", $this->_styleImg["colorInversion"] );
		if (isset($this->_styleImg["transparency"])) $propertiesNode->setAttribute("draw:transparency", $this->_styleImg["transparency"]."%" );
		if (isset($this->_styleImg["color-mode"])) $propertiesNode->setAttribute("draw:color-mode", $this->_styleImg["color-mode"] );
		$styleNode->appendChild($propertiesNode);
		$nodeStyle->appendChild($styleNode);
		
		$imageNode =& $this->xml->createElement("draw:image");
		$imageNode->setAttribute("draw:style-name", $StyleName);
		$imageNode->setAttribute("draw:name", $name);
		
		$ext = substr($this->_styleImg["src"], strlen($file)-3);
		$tmpfile = rand().".".$ext;
		copy($this->_styleImg["src"], $dir."/Pictures/".$tmpfile);
		if (isset($this->_styleImg["src"])) $imageNode->setAttribute("xlink:href", "#Pictures/".$tmpfile);
		if (isset($this->_styleImg["anchorType"])) $imageNode->setAttribute("text:anchor-type", $this->_styleImg["anchorType"]);
		
		$result = shell_exec(APIC_LIBRARY_PATH."/org/apicnet/io/OOo/objOOo/Taille.exe ".$dir."/Pictures/".$tmpfile);
		$temp   = split("centimeters", $result);
		$cm     = split("\*", substr($temp[count($temp)-1], 2));
	//	echo("cm : ".$cm[0]."<br>");
	//	echo("cm : ".$cm[1]);
		
		if (isset($this->_styleImg["width"])) $imageNode->setAttribute("svg:width", $this->_styleImg["width"]."cm");
		else $imageNode->setAttribute("svg:width", trim(str_replace(",", ".", $cm[0]))."cm");
		if (isset($this->_styleImg["height"])) $imageNode->setAttribute("svg:height", $this->_styleImg["height"]."cm");
		else $imageNode->setAttribute("svg:height", trim(str_replace(",", ".", $cm[1]))."cm");
		if (isset($this->_styleImg["z-index"])) $imageNode->setAttribute("draw:z-index", $this->_styleImg["z-index"]);
		
			
		
		
		$imageNode->setAttribute("xlink:type", "simple");
		$imageNode->setAttribute("xlink:actuate", "onLoad");
		$imageNode->setAttribute("xlink:show", "embed");
		
		$nodeContent->appendChild($imageNode);
	}
} 

?>
