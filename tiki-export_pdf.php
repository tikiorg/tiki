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
  $smarty->assign('msg',tra("Page cannot be found"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}

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

// Get page data
$info = $tikilib->get_page_info($page);
$data=$tikilib->parse_data($info["data"]);
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
