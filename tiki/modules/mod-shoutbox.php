<?php
require_once('lib/tikilib.php'); # httpScheme()

if($feature_shoutbox=='y' && $tiki_p_view_shoutbox == 'y') {
$setup_parsed_uri = parse_url($_SERVER["REQUEST_URI"]);
if(isset($setup_parsed_uri["query"]) ) {
  parse_str($setup_parsed_uri["query"],$sht_query);
} else {
  $sht_query=Array();
}
$shout_father=httpScheme().'://'.$_SERVER["SERVER_NAME"].$setup_parsed_uri["path"];
if(isset($sht_query) && count($sht_query)>0) {
  $sht_first=1;
  foreach($sht_query as $sht_name => $sht_val) {
    if($sht_first) {
      $sht_first=false;
      $shout_father.='?'.$sht_name.'='.$sht_val;
    } else {
      $shout_father.='&amp;'.$sht_name.'='.$sht_val;
    }
  }
  $shout_father.='&amp;';
} else {
  $shout_father.='?';
}
$smarty->assign('shout_ownurl',$shout_father);

if($tiki_p_admin_shoutbox == 'y') {
  if(isset($_REQUEST["shout_remove"])) {
    $tikilib->remove_shoutbox($_REQUEST["shout_remove"]);
  }
}

if($tiki_p_post_shoutbox == 'y') {
  if(isset($_REQUEST["shout_send"])) {
    $tikilib->replace_shoutbox(0, $user,$_REQUEST["shout_msg"]);
  }
}  

$shout_msgs = $tikilib->list_shoutbox(0,$module_rows,'timestamp_desc','');
$smarty->assign('shout_msgs',$shout_msgs["data"]);
}
?>