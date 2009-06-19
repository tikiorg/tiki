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
APIC::import("org.apicnet.io.cdir");

class OOoManifest extends absOOo {

	function OOoManifest($dir){
		parent::absOOo();
		
		$this->DIRXML   = $dir;
		$this->FILENAME = "META-INF/manifest.xml";
		$this->xml      = new DOMIT_Document();
		$this->xml->setDocType("<!DOCTYPE manifest:manifest PUBLIC \"-//OpenOffice.org//DTD Manifest 1.0//EN\" \"Manifest.dtd\">");
	}
	
	function create($type){
		$docManifestNode =& $this->xml->createElement("manifest:manifest");
		$docManifestNode->setAttribute("xmlns:manifest", "http://openoffice.org/2001/manifest");
		
		
		$fileEntryNode = &$this->xml->createElement("manifest:file-entry");
		if (in_array($type, $this->XMLTYPE)) {
		    $fileEntryNode->setAttribute("manifest:media-type", "application/".$this->MIME[$type]);
		} else {
			$this -> ErrorTracker(4, 'Le type '.$type." est inconnu", 'create', __FILE__, __LINE__);
		}
		$fileEntryNode->setAttribute("manifest:full-path", "/");
		$docManifestNode->appendChild($fileEntryNode);
		
		$cdir = new CDir();
		$cdir->Read( $this->DIRXML."/", "", true, 5 , true, true);
		$allFiles = array();
		
		$sortFiles = $cdir->sort("'Path'", false, 4, "'File'", true, 0);
		reset( $sortFiles );
		
		while( list( $sKey, $aFile ) = each( $sortFiles ) ){
			$sFileName     = $cdir->FileName($aFile);
			$sFilePath     = $cdir->GetPath($aFile);
			$mediaType     = "";
			$sExtension    = "";
			$fileEntryNode = &$this->xml->createElement("manifest:file-entry");
			
			
			$i = strrpos( $sFileName, "." ) + 1;
			if ( substr( $sFileName, $i - 1, 1 ) == "." ) {
				$sExtension = substr( $sFileName, $i );
			} 
			
			switch($sExtension){
				case "": 
					$mediaType = "";
					break;
				case "png": 
				case "gif": 
				case "jpeg": 
					$mediaType = "image/".$sExtension;
					break;
				default:
					$mediaType = "text/xml";
			} // switch
			
			$dirname  = dirname($this->DIRXML."/".$sFilePath.$sFileName);
			$filename = basename($this->DIRXML."/".$sFilePath.$sFileName);
			if ($dirname != $this->DIRXML) {
				$filename = str_replace($this->DIRXML."/", "", $dirname)."/".$filename;
			} else {
				if ($cdir->GetIsDirectory( $aFile )) $filename = $filename."/";
			}
			
			
			$fileEntryNode->setAttribute("manifest:media-type", $mediaType);
			$fileEntryNode->setAttribute("manifest:full-path", $filename);
			$docManifestNode->appendChild($fileEntryNode);
		}
		
		$this->xml->setDocumentElement($docManifestNode);
		
		$this->xml->setXMLDeclaration("<?xml version=\"1.0\" encoding=\"UTF-8\"?>");
	}
	
	
	function Main(){
		$this->create("Writer");
		mkdir (CACHE_PATH."/OOotmp");
		mkdir (CACHE_PATH."/OOotmp/META-INF");
		$this->save(CACHE_PATH."/OOotmp");
	}
	

}	
