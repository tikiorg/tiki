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
APIC::import("org.apicnet.io.File");

/**
 * OOoMeta
 * 
 * @package 
 * @author diogene
 * @copyright Copyright (c) 2004
 * @version $Id: OOoMeta.php,v 1.6 2007-02-04 20:09:43 mose Exp $
 * @access public
 **/
class OOoMeta extends absOOo {

	/**
	 * OOoMeta::OOoMeta(), construteur. méthode d'instanciation des paramètre nécessaire au bon fonctionnement de cette class
	 * 
	 * @param $dir
	 * @return none
	 **/
	function OOoMeta($dir){
		parent::absOOo();
		$this->DIRXML = $dir;
		$this->FILENAME = "meta.xml";
		
		$file = new File($dir."/".$this->FILENAME);
		if ($file->exists()) {
			$this->xml = new DOMIT_Document();
			$this->xml->loadXML($dir."/".$this->FILENAME, false);
		} else {
			$this->xml = new DOMIT_Document();
			$this->create();
		}
		$this->xml->setDocType("<!DOCTYPE office:document-meta PUBLIC \"-//OpenOffice.org//DTD OfficeDocument 1.0//EN\" \"office.dtd\">");
	}
	
	
	/**
	 * OOoMeta::create() est la méthode de création d'un fichier de méta vierge.
	 * 
	 * @return none
	 **/
	function create(){
		$docMetaNode =& $this->xml->createElement("office:document-meta");
		$docMetaNode->setAttribute("xmlns:office", "http://openoffice.org/2000/office");
		$docMetaNode->setAttribute("xmlns:xlink", "http://www.w3.org/1999/xlink");
		$docMetaNode->setAttribute("xmlns:dc", "http://purl.org/dc/elements/1.1/");
		$docMetaNode->setAttribute("xmlns:meta", "http://openoffice.org/2000/meta");
		$docMetaNode->setAttribute("office:version", "1.0");
		
		$officeMetaNode = &$this->xml->createElement("office:meta");
		$officeMetaNode->appendChild($this->ChildText("meta:generator", "C@RCOO 1.0"));
		$officeMetaNode->appendChild($this->ChildText("meta:creation-date", date ("Y-m-d\\TH:i:s")));
		$officeMetaNode->appendChild($this->ChildText("dc:date", date ("Y-m-d\\TH:i:s")));
		$officeMetaNode->appendChild($this->ChildText("dc:language", "fr-FR"));
		$officeMetaNode->appendChild($this->ChildText("meta:initial-creator", "C@RCOO"));
		
		$docMetaNode->appendChild($officeMetaNode);
		$this->xml->setDocumentElement($docMetaNode);
		$this->xml->setXMLDeclaration("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
	}
	
	/**
	 * OOoMeta::getBody() permet de générer la chaine de caractère correspondant au fichier de méta
	 * 
	 * @return String le fichier méta sous forme de chaîne de carctère
	 **/
	function getBody(){
		return $this->toString();
	}
	
	/**
	 * OOoMeta::generator(), la méthode pour connaitre l'outil ayant générer le document Ouvert. lorsque le document des crée avec OOoPHP le générator est "C@RCOO 1.0".
	 * 
	 * @return String
	 **/
	function generator(){
		$generator = $this->accessor("/office:document-meta/office:meta/meta:generator", 0);
		return $generator->getText();
	}

	/**
	 * OOoMeta::title()
	 * 
	 * @return String
	 **/
	function title(){
		$title = $this->accessor("/office:document-meta/office:meta/dc:title", 0);
		return $title->getText();
	}
	
	/**
	 * OOoMeta::setTitle()
	 * 
	 * @param String $text
	 * @return none
	 **/
	function setTitle($text){
		$officeMetaNode = &$this->xml->documentElement->firstChild;
		
		if (!$this->ssNodeExist($officeMetaNode, "dc:creator")) {
			$officeMetaNode->appendChild($this->ChildText("dc:title", $text));
		} else {
			$this->setNodeText("/office:document-meta/office:meta/dc:title", $text);
		}
	}
	
	
	/**
	 * OOoMeta::description()
	 * 
	 * @param String $text
	 * @return String
	 **/
	function description($text){
		$description = $this->accessor("/office:document-meta/office:meta/dc:description", 0);
		return $description->getText();
	}
	
	/**
	 * OOoMeta::setDescription()
	 * 
	 * @param String $text
	 * @return none
	 **/
	function setDescription($text){
		$officeMetaNode = &$this->xml->documentElement->firstChild;
		
		if (!$this->ssNodeExist($officeMetaNode, "dc:creator")) {
			$officeMetaNode->appendChild($this->ChildText("dc:description", $text));
		} else {
			$this->setNodeText("/office:document-meta/office:meta/dc:description", $text);
		}
	}

	/**
	 * OOoMeta::creation_date()
	 * 
	 * @return String
	 **/
	function creation_date(){
		$creation_date = $this->accessor("/office:document-meta/office:meta/meta:creation-date", 0);
		return $creation_date->getText();
	}

	/**
	 * OOoMeta::creator()
	 * 
	 * @return String
	 **/
	function creator(){
		$creator = $this->accessor("/office:document-meta/office:meta/dc:creator", 0);
		return $creator->getText();
	}
	
	/**
	 * OOoMeta::setCreator()
	 * 
	 * @param String $text
	 * @return none
	 **/
	function setCreator($text){
		$officeMetaNode = &$this->xml->documentElement->firstChild;
		
		if (!$this->ssNodeExist($officeMetaNode, "dc:creator")) {
			$officeMetaNode->appendChild($this->ChildText("dc:creator", $text));
		} else {
			$this->setNodeText("/office:document-meta/office:meta/dc:creator", $text);
		}
	}
	
	
	/**
	 * OOoMeta::subject()
	 * 
	 * @return String
	 **/
	function subject(){
		$creator = $this->accessor("/office:document-meta/office:meta/dc:subject", 0);
		return $creator->getText();
	}
	
	/**
	 * OOoMeta::setSubject()
	 * 
	 * @param String $text
	 * @return none
	 **/
	function setSubject($text){
		$officeMetaNode = &$this->xml->documentElement->firstChild;
		
		if (!$this->ssNodeExist($officeMetaNode, "dc:subject")) {
			$officeMetaNode->appendChild($this->ChildText("dc:subject", $text));
		} else {
			$this->setNodeText("/office:document-meta/office:meta/dc:subject", $text);
		}
	}

	/**
	 * OOoMeta::date()
	 * 
	 * @return String
	 **/
	function date(){
		$date = $this->accessor("/office:document-meta/office:meta/dc:date", 0);
		return $date->getText();
	}
	
	/**
	 * OOoMeta::setDate()
	 * 
	 * @return none
	 **/
	function setDate(){
		$this->setNodeText("/office:document-meta/office:meta/dc:date", date ("Y-m-d\\TH:i:s"));
	}

	/**
	 * OOoMeta::language()
	 * 
	 * @return String
	 **/
	function language(){
		$language = $this->accessor("/office:document-meta/office:meta/dc:language", 0);
		return $language->getText();
	}

	/**
	 * OOoMeta::keywords()
	 * 
	 * @return String
	 **/
	function keywords(){
		$areKeywords = TRUE;
		$strKeywords = "";
		$i = 0;
	
		while($areKeywords){
			$userDefined = $this->accessor("/office:document-meta/office:meta/meta:keywords/meta:keyword", $i);
			if ($userDefined != NULL) {
			    $strKeywords .= " ".$userDefined->getText();
				$i++;
			} else {
				$areKeywords = FALSE;
			}
		} // while
		return $strKeywords;
	}

	/**
	 * OOoMeta::addKeyword()
	 * 
	 * @param String $word
	 * @return none
	 **/
	function addKeyword($word){
		$areKeywords = FALSE;
		$i = 0;
		$userDefined = $this->accessor("/office:document-meta/office:meta/meta:keywords/meta:keyword", $i);
		
		while(!$areKeywords && $userDefined != NULL){
			
			if ($userDefined->getText() == $word) {
			    $areKeywords = TRUE;
			} else {
				$i++;
				$userDefined = $this->accessor("/office:document-meta/office:meta/meta:keywords/meta:keyword", $i);
			}
		}
		
		if (!$areKeywords) {
			$officeMetaNode = &$this->xml->documentElement->firstChild;
		
			if (!$this->ssNodeExist($officeMetaNode, "meta:keywords")) {
				$metaKeywordsNode = &$this->xml->createElement("meta:keywords");
				$metaKeywordsNode->appendChild($this->ChildText("meta:keyword", $word));
				
				$officeMetaNode->appendChild($metaKeywordsNode);
			}
		}
	}

	/**
	 * OOoMeta::removeKeyword()
	 * 
	 * @param String $word
	 * @return none
	 **/
	function removeKeyword($word){
			/*
			* <meta:keyworts>
				<meta:keywort>First keywort</meta:keywort>
				<meta:keywort>Secont keywort</meta:keywort>
				<meta:keywort>Thirt keywort</meta:keywort>
			  </meta:keyworts>
			*/
	}

	/**
	 * OOoMeta::removeKeywords()
	 * 
	 * @return none
	 **/
	function removeKeywords(){
		$this->removeNode("/office:document-meta/office:meta/meta:keywords");
	}
	
	/**
	 * OOoMeta::getUser_defined()
	 * 
	 * @param String $name
	 * @return String
	 **/
	function getUser_defined($name){
		$userDefined = $this->accessor("/office:document-meta/office:meta/meta:user-defined@[meta:name='".$name."']", 0);
		return $userDefined->getText();
	}
	
	
	
	/**
	 * OOoMeta::setUser_defined()
	 * 
	 * @param String $name
	 * @param String $value
	 * @return none
	 **/
	function setUser_defined($name, $value){
		$officeMetaNode = &$this->xml->documentElement->firstChild;
		
		if (!$this->ssNodeExist($officeMetaNode, "meta:user-defined@[meta:name='".$name."']") && $this->countNode("/office:document-meta/office:meta/meta:user-defined") < 3) {
			$userDefined = &$this->ChildText("meta:user-defined", $value);
			$userDefined->setAttribute("meta:name", $name);
			
			$officeMetaNode->appendChild($userDefined);
		} else {
			$this->setNodeText("/office:document-meta/office:meta/meta:user-defined@[meta:name='".$name."']", $value);
		}
	}

}
