<?
include_once("tiki-setup_base.php");

if(($tiki_p_admin_drawings != 'y') && ($tiki_p_edit_drawings != 'y')) {
  die;  
}


if(isset($_REQUEST["close"])) {
  print("<script>window.opener.location.reload();</script>");
  print("<script>window.close();</script>");
}
?>
<?
$name=$_REQUEST["drawing"];
$path=$_REQUEST["path"];
?>
<applet code="CH.ifa.draw.twiki.TWikiDraw.class" archive="lib/jHotDraw/twikidraw.jar" width=100% height=40>
	<param name="drawpath" value="<?=$path?>/img/wiki/<?=$name?>.draw">
	<param name="gifpath"  value="<?=$path?>/img/wiki/<?=$name?>.gif">
        <param name="extracolors" value="Aquamarine=#70DB93,New Tan=EBC79E,Sea Green=238E68,Motorola Blue=#3ff">
	<param name="savepath" value="<?=$path?>/jhot.php">
	<param name="viewpath" value="tiki-editdrawing.php?close=1">
	<param name="helppath" value=".">
</applet>
