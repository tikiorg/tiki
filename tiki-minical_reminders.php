<?php
include_once('tiki-setup_base.php');
include_once('lib/minical/minicallib.php');
if(!$minical_reminders) die;
//$refresh=$_REQUEST['refresh']*1000;
$refresh=1000*60*1;
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