<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-map_upload.php,v 1.3 2003-11-12 00:09:05 franck Exp $

// Copyright (c) 2002-2003, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

if(@$feature_maps != 'y') {
  $smarty->assign('msg',tra("Feature disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

if (!$tiki_p_map_edit) {
  $smarty->assign('msg',tra("You do not have permissions to view the layers"));
  $smarty->display("styles/$style_base/error.tpl");
  die;      
}

$max_file_size=ini_get("upload_max_filesize");
$smarty->assign('max_file_size', $max_file_size);

if (isset($_REQUEST["dir"])) {
  $dir=realpath($map_path.$_REQUEST["dir"]);
  $directory_path=$dir;
  $dir="/".substr($dir,strlen($map_path));
  if (substr($dir,0,5)!=="/data") {
    $directory_path=$map_path."/data";
    $dir="/data";
  }
} else {
  $directory_path=$map_path."/data";
  $dir="/data";
}
$smarty->assign('dir', $dir);	

//Do we have a file to upload?
if (isset($_REQUEST["upload"])) {
  for ($i = 1; $i <= 6; $i++) {
    if(isset($_FILES["userfile$i"]) && is_uploaded_file($_FILES["userfile$i"]['tmp_name'])) {
      if(!move_uploaded_file($_FILES["userfile$i"]['tmp_name'], $directory_path."/".$_FILES["userfile$i"]['name'])) {
        $smarty->assign('msg',tra("Could not upload the file"));
        $smarty->display("styles/$style_base/error.tpl");
        die;
      }
    }
  }
}

//Do we have a file to delete?
if (isset($_REQUEST["action"]) && isset($_REQUEST["file"])) {
  if ($_REQUEST["action"]=="delete") {
    if (is_file($directory_path."/".$_REQUEST["file"])) {
      if (!$tiki_p_map_delete) {
        $smarty->assign('msg',tra("You do not have permissions to delete a file"));
        $smarty->display("styles/$style_base/error.tpl");
        die;      
      }
      unlink($directory_path."/".$_REQUEST["file"]);
    }
  }
}

//Do we have a directory to create or delete?
if (isset($_REQUEST["action"]) && isset($_REQUEST["directory"])) {
  if ($_REQUEST["action"]=="createdir") {
    if(!preg_match("/^\./", $_REQUEST["directory"])){
      if (!$tiki_p_map_create) {
        $smarty->assign('msg',tra("You do not have permissions to create a directory"));
        $smarty->display("styles/$style_base/error.tpl");
        die;      
      }
      if(!@mkdir($directory_path."/".$_REQUEST["directory"])) {
        $smarty->assign('msg',tra("The Directory is not empty"));
        $smarty->display("styles/$style_base/error.tpl");
        die;      
      }
    }
  }
  if ($_REQUEST["action"]=="deldir") {
    if(!preg_match("/^\./", $_REQUEST["directory"])){
      if (!$tiki_p_map_delete) {
        $smarty->assign('msg',tra("You do not have permissions to delete a directory"));
        $smarty->display("styles/$style_base/error.tpl");
        die;      
      }
      if(!@rmdir($directory_path."/".$_REQUEST["directory"])) {
        $smarty->assign('msg',tra("The Directory is not empty"));
        $smarty->display("styles/$style_base/error.tpl");
        die;      
      }
    }
  }  
}

// Get layers from the layers directory
$files = array();
$dirs = array();
$h = opendir($directory_path);

while (($file = readdir($h)) !== false) {
  // Ignore hidden files
  if(!preg_match("/^\./", $file)){
    // Put dirs in $dirs[] and files in $files[]
    if(is_dir($directory_path."/".$file)){
      $dirs[] = $file;
    }else{
      $files[] = $file;
    }
  }
}
closedir ($h);

if($dir!=="/data") {
  $dirs[]="..";
}

// if $dirs[] exists, sort it and print all elements in it.
if(is_array($dirs)){
  sort($dirs);
}

// if $files[] exists, sort it and print all elements in it.
if(is_array($files)){
  sort($files);
}  


$smarty->assign('files', $files);	
$smarty->assign('dirs', $dirs);


// Get templates from the templates/modules directori
$smarty->assign('mid', 'map/tiki-map_upload.tpl');
$smarty->display("styles/$style_base/tiki.tpl");

?>
