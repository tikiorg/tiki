<?php
//copy this file to lib/smarty_tiki
//create a new module and put the following
//{wikistructure id=1 detail=1}
//id for structure id, or page_ref_id
//detail if you only wanna display subbranches of the open node within the structure
// assign your module


//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}


function smarty_function_wikistructure($params, &$smarty)
{

    global $tikilib, $user, $dbTiki, $structlib;

extract($params);

require_once ('lib/structures/structlib.php');
if (!isset($structlib)) {
  $structlib = new StructLib($dbTiki);
}
if (!isset($_REQUEST["page"])) $_REQUEST["page"]='';
if ($_REQUEST["page"] == '') {
if (isset($_REQUEST["page_ref_id"])) {
    // If a structure page has been requested
    $page_ref_id = $_REQUEST["page_ref_id"];
}
}
else {
//Get the structures this page is a member of
if (!isset($structure)) $structure='';
$structs = $structlib->get_page_structures($_REQUEST["page"],$structure);
//If page is only member of one structure, display if requested
$single_struct = count($structs) == 1;
if ($single_struct) {
$page_ref_id=$structs[0]['req_page_ref_id'];
$_REQUEST["page_ref_id"]=$page_ref_id;
}
}
if (!isset($channels)) $channels='';
if (isset($page_ref_id) && isset($detail)) {
  $channels.= $structlib->get_toc($page_ref_id,'asc',false,false,'','plain',$_REQUEST["page"]);
}
else {
  $channels.= $structlib->get_toc($id,'asc',false,false,'','plain',$_REQUEST["page"]);
}

return $channels;

}




?>
