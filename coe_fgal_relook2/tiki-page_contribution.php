<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
$section = 'wiki page';
$section_class = "tiki_wiki_page manage";	// This will be body class instead of $section
require_once ('tiki-setup.php');
include_once ('lib/wiki/histlib.php');
require_once("lib/diff/difflib.php");

$access->check_feature('feature_wiki');
$access->check_feature('feature_page_contribution');
if (!isset($_REQUEST["page"])) {
	$smarty->assign('msg', tra("No page indicated"));
	$smarty->display("error.tpl");
	die;
} else {
	$page = $_REQUEST["page"];
	$smarty->assign_by_ref('page', $page);
}

$tikilib->get_perm_object( $page, 'wiki page' );
$access->check_permission('tiki_p_page_contribution_view');

if (isset($_REQUEST['process'])) {
	$process=$_REQUEST['process'];
} else {
	$process=1;
}
$smarty->assign('process', $process);

if (isset($_REQUEST['lastversion'])) {
	$lastversion=$_REQUEST['lastversion'];
} else {
	$lastversion=0;
}
$smarty->assign('lastversion', $lastversion);

if (!isset($_REQUEST['show'])) { //defaults
	$showstatistics=1;
	$showpage=1;
	$showpopups=0;
	$escape=0;
} else {
	if(isset($_REQUEST['showstatistics'])) {
		$showstatistics=$_REQUEST['showstatistics'];
	} else {
		$showstatistics=0;
	}	
	if(isset($_REQUEST['showpage'])) {
		$showpage=$_REQUEST['showpage'];
	} else {
		$showpage=0;
	}	
	
	if(isset($_REQUEST['showpopups'])) {
		$showpopups=$_REQUEST['showpopups'];
	} else {
		$showpopups=0;
	}
	$escape=0;
	if (isset($prefs['feature_source']) and $prefs['feature_source']=='y' and 
		isset($tiki_p_wiki_view_source) and $tiki_p_wiki_view_source=='y') {
			if(isset($_REQUEST['escape'])) {
				$escape=$_REQUEST['escape'];
			}
	}
}
$smarty->assign('showpage', $showpage);
$smarty->assign('showstatistics', $showstatistics);
$smarty->assign('showpopups', $showpopups);
$smarty->assign('escape', $escape);
$getOptions=array('showpopups' => ($showpopups==1),
				  'escape' => ($escape==1),
				 );
$document = new Document($page, $lastversion, $process);
$smarty->assign('info',$document->getInfo());
$history=$document->getHistory();
$smarty->assign('history',$history);	

if ($showstatistics==1) {
	$authors=$document->getStatistics();
	$smarty->assign('authors',$authors);
	$smarty->assign('total',$document->getTotal());
}
if ($showpage==1) {
	$data=$document->get('wiki', $getOptions);
	$data=$tikilib->parse_data($data);
	if ($escape==1) { // make breaks visible again
		$data=preg_replace('/[\n]/', "<br />\n", $data);
	}
	$smarty->assign('parsed',$data);
//	$smarty->assign('colors',array('black', 'blue',  'red',   'green', 'maroon', 'yellow', 'aqua', 'fuchsia', 'teal',  'purple', 'white', 'olive', 'gray',  'navy',  'silver', 'lime'));
//	$smarty->assign('backgrounds',array('white', 'white', 'white', 'white', 'white',  'gray',   'gray', 'gray',    'white', 'white',  'blue',  'white', 'white', 'white', 'navy',   'gray'));
}
$smarty->assign('mid','tiki-page_contribution.tpl');
$smarty->display("tiki.tpl");