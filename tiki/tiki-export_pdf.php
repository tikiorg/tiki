<?php
include_once("tiki-setup_base.php");
include_once('lib/structures/structlib.php');
include_once('lib/wiki/wikilib.php');
include_once("lib/ziplib.php");
include_once('lib/wiki/exportlib.php');
include_once('lib/pdflib/pdflib.php');

//if($feature_wiki != 'y') {
//  die;
//}

if(isset($_REQUEST["page"])) {
  $page=$_REQUEST["page"];
} else {die;}

if(!$tikilib->page_exists($page)) {
  die;
}

//Permissions
if($tiki_p_view != 'y') {
  die;
}

// Get page data
$info = $tikilib->get_page_info($page);
$data=$tikilib->parse_data($info["data"]);
$pdflib->insert_html($data);
$pdflib->ezStream();
//  header("Content-type: application/pdf");
//  header( "Content-Disposition: inline; filename=$page.pdf" );
?>
