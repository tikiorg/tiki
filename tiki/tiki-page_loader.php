<?php
include_once('tiki-setup.php');
include_once('lib/htmlpages/htmlpageslib.php');  

$refresh=1000*$_REQUEST["refresh"];
?>
<html>
<head>
<script language='Javascript' type='text/javascript'>
<?php
$zones = $htmlpageslib->list_html_page_content($_REQUEST["pageName"],0,-1,'zone_asc','');
$cmds=Array();
for($i=0;$i<count($zones["data"]);$i++) {
  $cmd='top.document.getElementById("'.$zones["data"][$i]["zone"].'").innerHTML="'.$zones["data"][$i]["content"].'";';
  print($cmd);
}
?>
</script>
</head>
<?php
print('<body onLoad="window.setInterval(\'location.reload()\','.$refresh.');">');
//print_r($cmds);
?>
</body>
</html>
