<?php require_once ('tiki-setup.php');?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"  />
<script type="text/javascript" src="lib/tiki-js.js"></script>
<title>Interactive Translator</title>
<link rel="StyleSheet"  href="styles/tikineat.css" type="text/css" />
</head>
<body class="tiki_wiki">
<?php


echo "<center><h1>Interactive translator</h1>";
if (isset($_REQUEST['src']))	$_REQUEST['content']=$_REQUEST['src'];
echo "<b><i>'".$_REQUEST['content']."'</i></b></center>";

function update_trans($add_tran_source,$add_tran_tran,$edit_language){
	global $tikilib;
	$i=0;
	$query="select * from tiki_language where lang='$edit_language' and (source = '".$add_tran_source."' )";
	$result=mysql_query($query);
	$exist=($row = mysql_fetch_assoc($result));
	if (!$exist){
		$query = "insert into `tiki_language` values(binary '$add_tran_source','$edit_language',binary '$add_tran_tran' )";
		$result=mysql_query($query);
		if (mysql_errno())
        	{
           		echo "MySQL error ".mysql_errno().": ".mysql_error()."\nWhen 	executing:<b>$query</b><br>";
	   		exit;
 		}
	}else {
		$query = "update `tiki_language` set `tran`='$add_tran_tran' where `source`=binary '$add_tran_source' and `lang`='$edit_language'";
		$result=mysql_query($query);
		if (mysql_errno())
		{
		echo "MySQL error ".mysql_errno().": ".mysql_error()."\nWhen executing:<b>$query</b><br>";
		exit;
		}
	}	
}

function getTrans($trans,$lang){

	//First do the exact matching
	$query="select * from tiki_language where lang='$lang' and (source like '".$trans."' or tran like '".$trans."') order by lang";
	$res=mysql_query($query);
	if (mysql_errno())
	{
		echo "MySQL error ".mysql_errno().": ".mysql_error()."\nWhen executing:<b>$query</b><br>";
		//exit;
		}
	$compteur=0;
	while ($row = mysql_fetch_assoc($res)){
	$compteur++;
	$class=($compteur % 2)?"odd":"even";
	echo "<form><tr class='$class'><td width='40%'>";
	echo "<input type=hidden name='lang' value='$lang'>";
	echo "<input type=hidden name='src' value='".urlencode($row['source'])."'>";
	echo $row['source']."</td><td>";
	echo "<input type=text name='dst' value='".$row['tran']."' style='width:190px;' >&nbsp;&nbsp;";
	echo "<input type=submit name='submit' value='Submit'>";
	echo "</td></tr></form>";
	}

	//Let's find the other one
	$query="select * from tiki_language where lang='$lang' and (source like '%".$trans."%' or tran like '%".$trans."%') order by lang";
	$res=mysql_query($query);
	if (mysql_errno())
	{
		echo "MySQL error ".mysql_errno().": ".mysql_error()."\nWhen executing:<b>$query</b><br>";
		//exit;
		}
	while ($row = mysql_fetch_assoc($res))
	if (!(strlen($row['source'])==strlen($trans))){
	$compteur++;
	$class=($compteur % 2)?"odd":"even";
	echo "<form><tr class='$class'><td width='40%'>";
	echo "<input type=hidden name='lang' value='$lang'>";
	echo "<input type=hidden name='src' value='".urlencode($row['source'])."'>";
	echo $row['source']."</td><td>";
	echo "<input type=text name='dst' value='".$row['tran']."' style='width:190px;' >&nbsp;&nbsp;";
	echo "<input type=submit name='submit' value='Submit'>";
	echo "</td></tr></form>";
	}


	
	if ($compteur==0){
	$class=($compteur % 2)?"odd":"even";
	echo "<form><tr class='$class'><td width='40%'>";
	echo "<input type=hidden name='lang' value='$lang'>";
	echo "<input type=hidden name='src' value='".urlencode($trans)."'>";
	echo "$trans:"."</td><td>";
	echo "<input type=text name='dst' value='' style='width:190px;'>&nbsp;&nbsp;";
	echo "<input type=submit name='submit' value='Submit'>";
	echo "</td></tr></form>";
	}
}

function getLanguages(){
	$query = "select `lang` from `tiki_languages`";
	$res=mysql_query($query);
	$languages = array();
	while ($row = mysql_fetch_assoc($res))
		$languages[] = $row["lang"];
	return $languages;
}

//Update the translation
if (isset($_REQUEST['src'])&&isset($_REQUEST['lang'])&&isset($_REQUEST['dst'])){
	$_REQUEST['src']=urldecode($_REQUEST['src']);
	$_REQUEST['dst']=urldecode($_REQUEST['dst']);
	$_REQUEST['src'] = htmlentities($_REQUEST['src'], ENT_NOQUOTES, "UTF-8");
	$_REQUEST['dst'] = htmlentities($_REQUEST['dst'], ENT_NOQUOTES, "UTF-8");
	update_trans( $_REQUEST['src'],$_REQUEST['dst'],$_REQUEST['lang']);
	echo  "<center>&nbsp;&nbsp;&nbsp; has been updated <br> <center><input type='submit' value ='Close this window' onclick='window.opener.location.reload();self.close();'>&nbsp;&nbsp;<input type='submit' value ='Go Back' onclick='javascript:history.go(-1);'>";
	echo "<br>*Closing the window will reload your main browser";
	die;
}

//Main windows 
echo "<center><input type='submit' value ='Close this window' onclick='self.close();'></center>";
echo "<table class='normal'>";
$languages[]= getLanguages();
foreach ($languages as $key1 => $value1)
	foreach ($value1 as $key => $value){
	echo "<tr><td colspan=2 class='heading'><b>Language:<i> $value</i></b></td></tr>";
	echo getTrans(urldecode($_REQUEST['content']),$value);
	echo "<tr><td colspan='2'>&nbsp;</td></tr>";
}
echo "</table>";

?>