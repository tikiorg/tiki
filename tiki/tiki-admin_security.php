<?php
// $Header: /cvsroot/tikiwiki/tiki/tiki-admin_security.php,v 1.2 2005-01-22 22:54:52 mose Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
require_once ('tiki-setup.php');

// do we need it?
require_once('lib/admin/adminlib.php');

if ($tiki_p_admin != 'y') {
   $smarty->assign('msg', tra("You do not have permission to use this feature"));
   $smarty->display("error.tpl");
   die;
}


// get all dangerous php settings and check them 
$phpsettings=array();


// register globals
$s=ini_get('register_globals');
if ($s) {
   $phpsettings['register_globals']=array('risk' => tra('unsafe'),
       'setting' => $s,
       'message' => tra('register_globals should be off by default. See the php manual for details.'));
} else {
   $phpsettings['register_globals']=array('risk' => tra('safe'),
       'setting' => $s);
}

// trans_sid
$s=ini_get('session.use_trans_sid');
if ($s) {
   $phpsettings['session.use_trans_sid']=array('risk' => tra('unsafe'),
      'setting' => $s,
      'message' => tra('session.use_trans_sid should be off by default. See the php manual for details.'));
} else {
   $phpsettings['session.use_trans_sid']=array('risk' => tra('safe'),
     'setting' => $s);
}

// check file upload dir and compare it to tiki root dir
$s=ini_get('upload_tmp_dir');
$sn=substr($_SERVER['SCRIPT_NAME'],0,-23);
if (strpos($sn,$s) !== FALSE) {
   $phpsettings['upload_tmp_dir']=array('risk' => tra('unsafe'),
      'setting' => $s,
      'message' => tra('upload_tmp_dir is probably within your TikiWiki directory. There is a risk that someone can upload any file to this directory and access them via web browser'));
} else {
   $phpsettings['upload_tmp_dir']=array(
      'risk' => tra('unknown'),
      'setting' => $s,
      'message' => tra('cannot check if the upload_tmp_dir is accessible via web browser. To be sure you should check your webserver config.'));
}

$s=ini_get('xbithack');
if($s==1) {
   $phpsettings['xbithack']=array('risk' => tra('unsafe'),
     'setting' => $s,
     'message' => tra('setting the xbithack option is unsafe. Depending on the file handling of your webserver and your tiki settings, it may be possible that a attacker can upload scripts to file gallery and execute them'));
} else {
   $phpsettings['xbithack']=array('risk' => tra('safe'), 'setting' => $s);
}

ksort($phpsettings);
$smarty->assign_by_ref('phpsettings',$phpsettings);


// tikiwiki preferences check
// do we need to get the preferences or are they already loaded? 

$tikisettings=array();

if($feature_file_galleries='y' && !empty($fgal_use_dir) && 
    substr($fgal_use_dir,0,1)!='/') { // todo: check if absolute path is in tiki root
  $tikisettings['fgal_use_dir']=array('risk' => tra('unsafe'),'setting' => $fgal_use_dir,
     'message' => tra('The Path to store files in the filegallery should be outside the tiki root directory'));
}

if($feature_galleries='y' && !empty($gal_use_dir) && 
    substr($gal_use_dir,0,1)!='/') {
  $tikisettings['gal_use_dir']=array('risk' => tra('unsafe'),'setting' => $gal_use_dir,
     'message' => tra('The Path to store files in the imagegallery should be outside the tiki root directory'));
}

ksort($tikisettings);
$smarty->assign_by_ref('tikisettings',$tikisettings);



// dir walk & check functions
function md5_check_dir($dir,&$result) { // save all suspicious files in $result
  global $tikimd5; // todo: put md5 sums in db to avoid the problem that the md5 sum of lib/admin/secdb.php is wron always
  $d=dir($dir);
  while (false !== ($e = $d->read())) {
    $entry=$dir.'/'.$e;
    if(is_dir($entry)) {
      if($e != '..' && $e != '.') { // do not descend
        md5_check_dir($entry,$result);
      }
    } else {
      if(!isset($tikimd5[$entry])) {
        $result[$entry]=tra('This is not a TikiWiki File. Check if this file was uploaded and if it is dangerous.');
      } else if(!is_readable($entry)) {
	$result[$entry]=tra('File is not readable. Unable to check.');
      } else if(md5_file($entry) != $tikimd5[$entry]) {
        $result[$entry]=tra('This file is modified.');
      }
    }
  }
  $d->close();
}


// if check installation is pressed, walk through all files and compute md5 sums
if (isset($_REQUEST['check_files'])) {
  require_once('lib/admin/secdb.php');
  $result=array();
  md5_check_dir(".",$result);
  // echo "<pre>"; print_r($tikimd5);echo "</pre><br />";
  // echo "<pre>"; print_r($result);echo "</pre><br />";
  $smarty->assign('filecheck',true);
  $smarty->assign_by_ref('tikifiles',$result);
}


$smarty->assign('mid', 'tiki-admin_security.tpl');
$smarty->display("tiki.tpl");

?>
