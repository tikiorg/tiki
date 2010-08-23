<?php 
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
require_once('lib/language/Language.php');
if ($prefs['lang_use_db'] != 'y') {
	$smarty->assign('msg', tra("This feature is disabled").": lang_use_db");
	$smarty->assign('error', 'y');
	$smarty->display("tiki-interactive_trans.tpl");
	die;
}

if ($tiki_p_edit_languages != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg', tra("Permission denied to use this feature"));
	$smarty->assign('error', 'y');
	$smarty->display("tiki-interactive_trans.tpl");
	die;
}


if (isset($_REQUEST['src']))	$_REQUEST['content']=$_REQUEST['src'];
$smarty->assign('analysed_word', $_REQUEST['content']);

function getTrans($trans,$lang){
	global $tikilib,$smarty,$entries;
	//First do the exact matching
	$query="select * from `tiki_language` where `lang`=? and (`source` = ? or `tran` = ?) order by lang";
	$result=$tikilib->query($query,array($lang,$trans,$trans));
	$compteur=0;
	$exact=false;
	while ($res = $result->fetchRow()) {
		$compteur++;	
		$exact=true;
		$entry=array();
		$entry["lang"] = $res["lang"];
		$entry["source"] = $res["source"];
		$entry["urlsource"] = urlencode($res["source"]);
		$entry["trans"] = $res["tran"]."";
		$entries[]=$entry;
	}	
	
	$query="select * from `tiki_language` where `lang`=? and (`source` like ? or `tran` like ?) order by lang";
	$result=$tikilib->query($query,array($lang,"%".$trans."%","%".$trans."%"));
	while ($res = $result->fetchRow())
		if (strlen($res["source"])!=strlen($trans)){
		$compteur++;
		$entry=array();
		$entry["lang"] = $res["lang"];
		$entry["source"] = $res["source"];
		$entry["urlsource"] = urlencode($res["source"]);
		$entry["trans"] = $res["tran"];
		$entries[]=$entry;
	}	
	
	if (!$exact){
		$compteur++;
		$entry=array();
		$entry["lang"] = $lang;
		$entry["source"] = $trans;
		$entry["urlsource"] = urlencode($trans);
		$entry["trans"] = "";
		$entries[]=$entry;
		
	}
}

$language = new Language;

//Update the translation
if (!isset($_REQUEST['dst']))$_REQUEST['dst']="";
if (isset($_REQUEST['src'])&&isset($_REQUEST['lang'])){
	$_REQUEST['src']=urldecode($_REQUEST['src']);
	$_REQUEST['dst']=urldecode($_REQUEST['dst']);
	$_REQUEST['src'] = htmlentities($_REQUEST['src'], ENT_NOQUOTES, "UTF-8");
	$_REQUEST['dst'] = htmlentities($_REQUEST['dst'], ENT_NOQUOTES, "UTF-8");
	$language->updateTrans( $_REQUEST['src'],$_REQUEST['dst']);
	$smarty->assign('update', 'y');
	$smarty->display("tiki-interactive_trans.tpl");
	die;
}

$languages = Language::getLanguages();

// Called by the JQuery ajax request. No response expected.
if( isset( $_REQUEST['source'], $_REQUEST['trans'] ) && count($_REQUEST['source']) == count($_REQUEST['trans']) ) {
	$lang = $prefs['language'];
	if( empty( $lang ) ) {
		$lang = $prefs['site_language'];
	}

	foreach( $_REQUEST['trans'] as $k => $translation ) {
		$source = $_REQUEST['source'][$k];

		$language->updateTrans( $source, $translation );
	}

	exit;
}

//Main windows 
$entries=array();
foreach ($languages as $key => $value)
	getTrans(urldecode($_REQUEST['content']),$value);

$smarty->assign('languages', $languages);
$smarty->assign('entries', $entries);
$smarty->display("tiki-interactive_trans.tpl");
