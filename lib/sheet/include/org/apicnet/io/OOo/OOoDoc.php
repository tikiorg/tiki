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

APIC::import("org.apicnet.io.OOo.*");


class OOoDoc extends OOoUtil {

	var $meta;
	var $content;
	var $setting;
	var $manifest;
	var $fileName;
	var $dirName;
	var $XMLTYPE = array('Writer', 'Calc', 'Impress',	'Draw');
	var $TYPE;
	
	
	function __construct(){
		$this->TYPE     = NULL;
		$this->docExist = FALSE;
		$this->manifest = NULL;
		$this->createDirectories();
	}
	
	
	function newWriter(){
		$this->TYPE     = "Writer";
		$this->docExist = TRUE;
		$this->meta     = new OOoMeta($this->tmpdir);
		$this->content  = new OOoWriter($this->tmpdir);
		
		$this->manifest = new OOoManifest($this->tmpdir);
		$this->mimeType = new OOoMime($this->tmpdir, $this->TYPE);
	}
	
	function newCalc(){
		$this->TYPE     = "Calc";
		$this->docExist = TRUE;
		$this->meta     = new OOoMeta($this->tmpdir);
	//	$this->style    = new OOoStyle($this->tmpdir);
		$this->content  = new OOoCalc($this->tmpdir);
		
		$this->manifest = new OOoManifest($this->tmpdir);
		$this->mimeType = new OOoMime($this->tmpdir, $this->TYPE);
	}
	
	function openWriter($file){
		$this->TYPE     = "Writer";
		$allRep         = explode("/", $file);
		$this->fileName = array_pop($allRep);
		$this->dirName  = join ("/", $allRep);

		$this->docExist = TRUE;
		
		$this->unZip($this->tmpdir, $file);
		$this->meta    = new OOoMeta($this->tmpdir);
		//$this->style    = new OOoStyle($file);
		//$this->content  = new OOoWriter($file);
	}
	
	function openCalc($file){
		$this->TYPE     = "Calc";
		
		$allRep         = explode("/", $file);
		$this->fileName = array_pop($allRep);
		$this->dirName  = join ("/", $allRep);

		$this->docExist = TRUE;
		
		$this->unZip($this->tmpdir, $file);
		$this->meta    = new OOoMeta($this->tmpdir);
		//$this->style   = new OOoStyle($file);
		//$this->content = new OOoCalc($file);
	}
	
	function setName($name){
		$this->fileName  = $name;
	}
	
	function save(){
		if ($this->docExist) {
			$this->meta->setDate();
			$this->meta->save();
			
			$this->content->save();
			
			if ($this->manifest != NULL){	
				$this->manifest->create($this->TYPE);
				$this->manifest->save();
			}
			$mimeType = new OOoMime($this->tmpdir, $this->TYPE);
			
			if ($this->fileName != ""){
				$this->Zip(CACHE_PATH."/".$this->fileName);
			} else {
				$this -> ErrorTracker(4, "Vous devez donner un nom a votre fichier", 'save', __FILE__, __LINE__);
			}
			
		} else {
			$this -> ErrorTracker(4, "Aucun document OpenOffice a été créé", 'save', __FILE__, __LINE__);
		}
	}
	
	function close(){
		$this->delDir($this->tmpdir);
	}
	
	
	function download(){
		$OOoFile = new File(CACHE_PATH."/".$this->fileName);
		if ($OOoFile->exists()) {
			$df_size = $OOoFile->length();
			
			header("Pragma: no-cache");
			header("Expires: 0");
			header("Cache-control: private");
			header("Content-Type: application/vnd.sun.xml.".$this->TYPE);
			header("Content-Length: ".$df_size);
			header("Content-Disposition: inline; filename=".$this->fileName);
			
			$fp = fopen(CACHE_PATH."/".$this->fileName, 'r');
			rewind($fp);
			fpassthru($fp); // ** CORRECT **
			fclose($fp);
			
			return $fp;
			
		}
		return false;
	}
	
	
	function mail(){
	}
}
