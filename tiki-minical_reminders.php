<?php
include_once('tiki-setup.php');
include_once('lib/minical/minicallib.php');
//$refresh=$_REQUEST['refresh']*1000;
$refresh=1000*60*1;
/*
[<a title="{tr}special chars{/tr}" class="link" href="#" onClick="javascript:window.open('templates/tiki-special_chars.php?area_name={$area_name}','','menubar=no,width=252,height=25');">{tr}chars{/tr}</a>]
*/
$evs = $minicallib->minical_get_events_to_remind($user,$minical_reminders);
foreach($evs as $ev) {
   $command = "<script>alert('event ".$ev['title']." will start at ".date("h:i",$ev['start'])."');</script>";
   print($command);
   $minicallib->minical_event_reminded($user,$ev['eventId']);
}
?>
<?php
print('<body onLoad="window.setInterval(\'location.reload()\','.$refresh.');">');
?>