<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<title>Tiki Installation Permission Check</title>
<style type="text/css">
	.block		{text-align: justify;}
	.truetype	{font-family: courier;}
</style>
</head>
<body>
<h1>Tiki Installation Permission Check</h1>
 <div class="block">
	This page should always be visible, independent from any installation problems
	with Tiki. If the Tiki installer does not run properly, this effect may be
	caused by some permission problems. There are many different usescases, thus
	there is no default permission setting which works in all cases and provides
	an appropriate security level.
 </div>
 <p>PHP check: <?php
		echo "PHP works";
	?>
 </p>
 <p>
	permission check: <?php
		//include "functions.inc";
		require "functions.inc";
		require "usecases.inc";
		$filename="index.php";
		$user=get_ownership_username($filename);
		$group=get_ownership_groupname($filename);
		$username=get_ownership_username($filename);
		$groupname=get_ownership_groupname($filename);
		$perms_oct=substr(sprintf('%o', fileperms($filename)), -3);
		$perms_asc=get_perms_ascii($filename);
		echo "\n\tthis file "."<strong>".$filename."</strong>"." owned by ";
		echo "\n\tuser "."<strong>".$username."</strong>"." and group "."<strong>".$groupname."</strong>"." has got access permissions ";
		echo "\n\t<strong>".$perms_asc."</strong>"." which is "."<strong>".$perms_oct."</strong>"." octal.";
		echo "<br />\n";
	?>
 </p>
 <p class="block">
	Please ensure correct permission settings of this permission test suite. You
	may modify permissions either by SSH access or by FTP access. The first column
	(italic) shows assumed permissions (what they should be to run this test), next
	is user (owner), group (owner), actual permissions ascii and octal) and the
	subdirectory or filename which was checked.
 </p>
 <div class="block"><table class="truetype"><?php
	echo "\n  ";
	//$file="permissioncheck/paranoia";
	//$filename="../".$file;

	foreach ($uc_perms_subdir as $usecase => $perms_subdir) {
		$perms_file=$uc_perms_file[$usecase];
		$filename=$usecase;
		get_perm_data($filename,$username,$groupname,$perms_asc,$perms_oct);
		echo "<tr>"."<td><em>".$perms_subdir."</em></td>"."<td>".$username."</td><td>".$groupname."</td><td>".$perms_asc."</td><td>".$perms_oct.'</td><td><a href="'.$filename.'" target="_blank">permissioncheck/'.$filename."</a></td></tr>\n  ";
		$filename=$usecase."/".$default_file_name;
		get_perm_data($filename,$username,$groupname,$perms_asc,$perms_oct);
		echo "<tr>"."<td><em>".$perms_file."</em></td>"."<td>".$username."</td><td>".$groupname."</td><td>".$perms_asc."</td><td>".$perms_oct.'</td><td><a href="'.$filename.'" target="_blank">permissioncheck/'.$filename."</a></td></tr>\n  ";
	}

//	echo"<tr><td><br />\n..........<br /></td></tr>\n";
//	$filename="paranoia";
//	get_perm_data($filename,$username,$groupname,$perms_asc,$perms_oct);
//	//echo $username." ".$groupname." ".$perms_asc." ".$perms_oct.' <a href="'.$filename.'" target="_blank">'.$filename."</a><br>\n";
//	echo "<tr>"."<td>".$username."</td><td>".$groupname."</td><td>".$perms_asc."</td><td>".$perms_oct.'</td><td><a href="'.$filename.'" target="_blank">'.$filename."</a></td></tr>\n  ";
//	$filename="paranoia-suphp";
//	get_perm_data($filename,$username,$groupname,$perms_asc,$perms_oct);
//	//echo $username." ".$groupname." ".$perms_asc." ".$perms_oct.' <a href="'.$filename.'" target="_blank">'.$filename."</a><br>\n";
//	echo "<tr>"."<td>".$username."</td><td>".$groupname."</td><td>".$perms_asc."</td><td>".$perms_oct.'</td><td><a href="'.$filename.'" target="_blank">'.$filename."</a></td></tr>\n  ";
//	$filename="mixed";
//	get_perm_data($filename,$username,$groupname,$perms_asc,$perms_oct);
//	//echo $username." ".$groupname." ".$perms_asc." ".$perms_oct.' <a href="'.$filename.'" target="_blank">'.$filename."</a><br>\n";
//	echo "<tr>"."<td>".$username."</td><td>".$groupname."</td><td>".$perms_asc."</td><td>".$perms_oct.'</td><td><a href="'.$filename.'" target="_blank">'.$filename."</a></td></tr>\n  ";
//	$filename="risky";
//	get_perm_data($filename,$username,$groupname,$perms_asc,$perms_oct);
//	//echo $username." ".$groupname." ".$perms_asc." ".$perms_oct.' <a href="'.$filename.'" target="_blank">'.$filename."</a><br>\n";
//	echo "<tr>"."<td>".$username."</td><td>".$groupname."</td><td>".$perms_asc."</td><td>".$perms_oct.'</td><td><a href="'.$filename.'" target="_blank">'.$filename."</a></td></tr>\n  ";
?>
<? /*	<div><a href="/permissioncheck/paranoia/" target="_blank">paranoia</a></div>
	<div><a href="/permissioncheck/paranoia-suphp/" target="_blank">paranoia-suphp</a></div>
	<div><a href="/permissioncheck/mixed/" target="_blank">mixed</a></div>
	<div><a href="/permissioncheck/risky/" target="_blank">risky</a></div>
*/ ?>	<?php //<div><a href="/permissioncheck/foo/" target="_blank">foo</a></div>?>
	<?php //<div><a href="/permissioncheck/bar/" target="_blank">bar</a></div>?>
	<?php echo "<!-- table end -->\n"; ?>
 </table></div>
 <p class="block">
	Enjoy <a href="https://tiki.org/" target="_blank">Tiki</a> and
	<a href="https://tiki.org/tiki-register.php" target="_blank">join the community</a>!
 </p>
</body>
</html>
