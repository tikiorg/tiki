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

//Permissions
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this page"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

//feature
$feature_wiki_pdf=$tikilib->get_preference('feature_wiki_pdf','n');
if($feature_wiki_pdf != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

//defaults

if(!isset($_REQUEST["font"])){
  $_REQUEST["font"]="Helvetica";
}

if(!isset($_REQUEST["textheight"])){
  $_REQUEST["textheight"]=10;
}

if(!isset($_REQUEST["h1height"])){
  $_REQUEST["h1height"]=16;
}

if(!isset($_REQUEST["h2height"])){
  $_REQUEST["h2height"]=14;
}

if(!isset($_REQUEST["h3height"])){
  $_REQUEST["h3height"]=12;
}

if(!isset($_REQUEST["tbheight"])){
  $_REQUEST["tbheight"]=14;
}

if(!isset($_REQUEST["imagescale"])){
  $_REQUEST["imagescale"]=0.4;
}

if(!isset($_REQUEST["convertpages"])) {
  $convertpages = Array();
  if(isset($_REQUEST["page"]) && $tikilib->page_exists($_REQUEST["page"])) {
    $convertpages[]=$_REQUEST["page"];
  }
} else {
  $convertpages = unserialize(urldecode($_REQUEST['convertpages']));
}

$pdfopts=Array('font' => $_REQUEST["font"],
	'textheight' => $_REQUEST["textheight"],
	'h1height' => $_REQUEST["h1height"],
	'h2height' => $_REQUEST["h2height"],
	'h3height' => $_REQUEST["h3height"],
	'tbheight' => $_REQUEST["tbheight"],
	'imagescale' => $_REQUEST["imagescale"]);

$pdflib =& new TikiPdfLib($pdfopts);

// Get pages data
$data='';
foreach(array_values($convertpages) as $page) {
  $info = $tikilib->get_page_info($page);
  $data.="\n<C:page:$page>\n<br/>\n";
  $data.=$tikilib->parse_data($info["data"]);
}

//todo: add linkdestinations for titlebars
$pdflib->insert_linkdestinations($convertpages);
// now add data
$pdflib->insert_html($data);
$pdfdebug=false;
if($pdfdebug) {
  $pdfcode = $pdflib->output(1);
  $pdfcode = str_replace("\n","\n<br>",htmlspecialchars($pdfcode));
  echo '<html>';
  echo trim($pdfcode);
  echo '</body>';
} else {  
$hopts=Array('Content-Disposition'=> $page);
$pdflib->ezStream($hopts);
}
?>
