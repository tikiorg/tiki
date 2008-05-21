<?php
// $Header$

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

include_once ('lib/structures/structlib.php');

function copys($source,$dest)
{
    if (!is_dir($source))
    return 0;
    if (!is_dir($dest))
    {
        mkdir($dest);
    }
    $h=@dir($source);
    while (@($entry=$h->read()) !== false)
    {
        if (($entry!=".")&&($entry!=".."))
        {
            if (is_dir("$source/$entry")&&$dest!=="$source/$entry")
            {
                copys("$source/$entry","$dest/$entry");
            }
            else
            {
                @copy("$source/$entry","$dest/$entry");
            }
        }
    }
    $h->close();
    return 1;
}

function deldirfiles($dir){
  $current_dir = opendir($dir);
  while($entryname = readdir($current_dir)){
     if(is_dir("$dir/$entryname") and ($entryname != "." and $entryname!="..")){
        deldirfiles("${dir}/${entryname}");
     }elseif($entryname != "." and $entryname!=".."){
        unlink("${dir}/${entryname}");
     }
  }
  closedir($current_dir);
}

if ($prefs['feature_create_webhelp'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": feature_create_webhelp");

	$smarty->display("error.tpl");
	die;
}

if ($tiki_p_edit_structures != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
}

$struct_info = $structlib->s_get_structure_info($_REQUEST['struct']);
$smarty->assign_by_ref('struct_info',$struct_info);

if (!$tikilib->user_has_perm_on_object($user,$struct_info["pageName"],'wiki page','tiki_p_view')) {
	$smarty->assign('msg',tra('Permission denied you cannot view this page'));
	$smarty->display("error.tpl");
	die;
}
	
$smarty->assign('generated','y');
if(isset($_REQUEST['create'])) {
  $name=$_REQUEST['name'];
  $dir=$_REQUEST['dir'];
  $smarty->assign('dir',$_REQUEST['dir']);
  $struct=$_REQUEST['struct'];
  $top=$_REQUEST['top'];
  $top='foo1';
  $output='';
  $output.="TikiHelp WebHelp generation engine<br />";
  $output.="Generating WebHelp using <b>$name</b> as index. Directory: $name<br />";
  $base = "whelp/$dir";
  
  // added 2003-12-19 Checking the permission to write. epolidor
  if (!is_writeable("whelp")) {
    $smarty->assign('msg', tra("You need to change chmod 'whelp' manually to 777"));
    $smarty->display("error.tpl");
    die;
  }
  
  if(!is_dir("whelp/$dir")) { 
    $output.="Creating directory structure in $base<br />";
    mkdir("whelp/$dir");
    mkdir("$base/js");
    mkdir("$base/css");
    mkdir("$base/icons");
    mkdir("$base/menu");
    mkdir("$base/pages");
    mkdir("$base/pages/img");
    mkdir("$base/pages/img/wiki_up");
  }
  $output.="Eliminating previous files<br />";
  deldirfiles("$base/js");
  deldirfiles("$base/css");
  deldirfiles("$base/icons");
  deldirfiles("$base/menu");
  deldirfiles("$base/pages");
  deldirfiles("$base/pages/img/wiki_up");
  // Copy base files to the webhelp directory
  copys("lib/tikihelp","$base/");
  
  $structlib->structure_to_webhelp($struct,$dir,$top);
  $smarty->assign('generated','y');
}  

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$smarty->assign('mid', 'tiki-create_webhelp.tpl');
$smarty->display("tiki.tpl");

?>
