<?php # $Header: /cvsroot/tikiwiki/tiki/tiki-editdrawing.php,v 1.5 2003-05-14 15:41:16 lrargerich Exp $

include_once("tiki-setup_base.php");

if(($tiki_p_admin_drawings != 'y') && ($tiki_p_edit_drawings != 'y')) {
  die;  
}

if(isset($_REQUEST["close"])) {
  print("<script>window.opener.location.reload();</script>");
  print("<script>window.close();</script>");
  die;
}
$tikilib->invalidate_cache($_REQUEST['page']);
$name=$_REQUEST["drawing"];
$path=$_REQUEST["path"];
?>
<applet archive="lib/jgraphpad/jgraphpad.jar"
code="com.jgraph.JGraphpad.class" width=100% height=40>
	<param name="drawpath" value="<?php echo $path?>/img/wiki/<?php echo $name?>.pad_xml">
	<param name="gifpath"  value="<?php echo $path?>/img/wiki/<?php echo $name?>.gif">
	<param name="savepath" value="<?php echo $path?>/jhot.php">
	<param name="viewpath" value="tiki-editdrawing.php?close=1">
</applet>	
<!--
<applet code="CH.ifa.draw.twiki.TWikiDraw.class" archive="lib/jHotDraw/twikidraw.jar" width=100% height=40>
	<param name="drawpath" value="<?php echo $path?>/img/wiki/<?php echo $name?>.draw">
	<param name="gifpath"  value="<?php echo $path?>/img/wiki/<?php echo $name?>.gif">
        <param name="extracolors" value="Aquamarine=#70DB93,New Tan=EBC79E,Sea Green=238E68,Motorola Blue=#3ff">
	<param name="savepath" value="<?php echo $path?>/jhot.php">
	<param name="viewpath" value="tiki-editdrawing.php?close=1">
	<param name="helppath" value=".">
</applet>
-->