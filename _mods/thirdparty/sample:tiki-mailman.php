<html>
<head><title>Mailman Mailing-lists</title>
<style>
a { text-decoration : none; color : #446688; }
</style>
</head>
<body style="background-color:white;">
<table border="0" width="100%" height="100%">
<tr><td valign="top" width="130" style="width:130px;border-right:1px dashed #999999;padding:10px;" rowspan="2">
<?
$mldomain = "[:::[mldomain]:::]";
$options = "-a -V $mldomain";

$urls['info'] = "http://$mldomain/cgi-bin/mailman/listinfo/%s";
$urls['parchives'] = "http://$mldomain/pipermail/%s/";
$urls['archives'] = "http://$mldomain/cgi-bin/mailman/private/%s/";
$urls['admin'] = "http://$mldomain/cgi-bin/mailman/admin/%s";
$urls['members'] = "http://$mldomain/cgi-bin/mailman/admin/%s/members";
$urls['queue'] = "http://$mldomain/cgi-bin/mailman/admindb/%s";

exec("sudo /usr/sbin/list_lists $options",$ls);
echo "<div style='font-weight:bold;font-size:1.2em;'><a href='http://nguild.org'>N'GUILD</a>LISTS</div>";
echo "<div style='font-size:.8em;'><a href='".$_SERVER['PHP_SELF']."'>". array_shift($ls). "</a></div><br />\n";
foreach ($ls as $l) {
	list($name,$desc) = split('-',$l);
	$name = trim($name);
	$desc = trim($desc);
	echo "<div><a href='".$_SERVER['PHP_SELF']."?list=$name";
	if (isset($_REQUEST['panel']) and isset($urls["{$_REQUEST['panel']}"])) {
		echo "&panel=".$_REQUEST['panel'];
	}
	echo "'>$name</a><div style='font-size:.7em;margin-bottom:5px;'>$desc</div></div>";
	$lists[] = $name;
}
?>
</td>
<?
if (isset($_REQUEST['list']) and in_array($_REQUEST['list'],$lists)) {
	$list = $_REQUEST['list'];
	echo "<td valign='top' height='34'>\n";
	echo "<h2>$list ";
	echo "<a href='".$_SERVER['PHP_SELF']."?list=$list&panel=info' style='font-size:.5em;padding:2px 10px;border:1px solid black;'>info</a> ";
	echo "<a href='".$_SERVER['PHP_SELF']."?list=$list&panel=parchives' style='font-size:.5em;padding:2px 10px;border:1px solid black;'>public archives</a> ";
	echo "<a href='".$_SERVER['PHP_SELF']."?list=$list&panel=archives' style='font-size:.5em;padding:2px 10px;border:1px solid black;'>archives</a> ";
	echo "<a href='".$_SERVER['PHP_SELF']."?list=$list&panel=members' style='font-size:.5em;padding:2px 10px;border:1px solid black;'>members</a> ";
	echo "<a href='".$_SERVER['PHP_SELF']."?list=$list&panel=queue' style='font-size:.5em;padding:2px 10px;border:1px solid black;'>queue</a> ";
	echo "<a href='".$_SERVER['PHP_SELF']."?list=$list&panel=admin' style='font-size:.5em;padding:2px 10px;border:1px solid black;'>admin</a> ";
	echo "</h2>";
	echo "</td></tr><tr><td>";
	if (isset($_REQUEST['panel']) and isset($urls["{$_REQUEST['panel']}"])) {
		$url = sprintf($urls["{$_REQUEST['panel']}"],$list);
		echo "<iframe src='$url' name='$list' height='100%' width='100%' frameborder='1' scrolling='auto' style='width:100%;'></iframe>";
	} else {
		echo "<i>choose a command</i>";
	}
	echo "</td>\n";
}
?>
</tr></table>
</body>
</html>
