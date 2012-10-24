<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<title>Tiki Installation Permission Check</title>
<style type="text/css">
	.block		{text-align: justify;}
	.truetype	{font-family: courier;}
	.equal		{background-color: green;}
	.notequal	{background-color: red;}
	.unknown	{background-color: yellow;}
	.user		{background-color: blue;}
	.important	{background-color: black;	color:	red;}
	.hint		{background-color: black;	color:	yellow;}
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
	<?php
	include "permission_granted.php.inc";
	$html_and_ascii_linebreak = "<br />\n";
	if ($permission_granted=="yes\n") {
		//$dummy=true;
		echo '<a href="https://doc.tiki.org/Permission+Check">https://doc.tiki.org/Permission+Check</a>' . $html_and_ascii_linebreak ;
		echo $html_and_ascii_linebreak ;
		echo '<span class="important">disable permission check on production machines</span>' . $html_and_ascii_linebreak;
		echo $html_and_ascii_linebreak ;
		echo 'disable permission by running' . $html_and_ascii_linebreak;
		echo '<span class="truetype">sh prepare_permissioncheck.sh disable</span>' . $html_and_ascii_linebreak;
		echo "in Tiki's document root" . $html_and_ascii_linebreak;
		echo $html_and_ascii_linebreak ;
		echo 'or (not recommended) disable permission (setting: no) by copying file' . $html_and_ascii_linebreak;
		echo '<span class="truetype">permissioncheck/no.bin</span>' . $html_and_ascii_linebreak;
		echo 'to file' . $html_and_ascii_linebreak;
		echo '<span class="truetype">permissioncheck/permission_granted.bin</span>' . $html_and_ascii_linebreak;
		echo $html_and_ascii_linebreak ;
	} else {
		echo 'permission not granted' . $html_and_ascii_linebreak ;
		echo $html_and_ascii_linebreak ;
		echo 'enable permission by running' . $html_and_ascii_linebreak;
		echo '<span class="truetype">sh prepare_permissioncheck.sh enable</span>' . $html_and_ascii_linebreak;
		echo "in Tiki's document root" . $html_and_ascii_linebreak;
		echo $html_and_ascii_linebreak ;
		echo 'or (not recommended) enable permission (setting: yes) by copying file' . $html_and_ascii_linebreak;
		echo '<span class="truetype">permissioncheck/yes.bin</span>' . $html_and_ascii_linebreak;
		echo 'to file' . $html_and_ascii_linebreak;
		echo '<span class="truetype">permissioncheck/permission_granted.bin</span>' . $html_and_ascii_linebreak;
		echo $html_and_ascii_linebreak ;
		echo 'Do not edit those files - different line ending conventions (Mac,Unix,Windows) matter' . $html_and_ascii_linebreak;
		echo $html_and_ascii_linebreak ;
		echo '<a href="https://doc.tiki.org/Permission+Check">https://doc.tiki.org/Permission+Check</a>' . $html_and_ascii_linebreak ;
		echo $html_and_ascii_linebreak ;
		echo '<span class="hint">disable permission check on production machines</span>' . $html_and_ascii_linebreak;
		echo "</p></body></html>";
		die;
	}
	?>
 </p>
 <p>
	permission check: <?php
		//include "functions.php.inc";
		require "functions.php.inc";
		//include "usecases.php.inc";
		require "usecases.php.inc";
		$filename="index.php";
		$user=get_ownership_username($filename);
		$group=get_ownership_groupname($filename);
		$username=get_ownership_username($filename);
		$groupname=get_ownership_groupname($filename);
		//$perms_oct=substr(sprintf('%o', fileperms($filename)), -3);
		$perms_oct=get_perms_octal($filename);
		$perms_asc=get_perms_ascii($filename);
		echo "\n\tthis file " . '<strong>' . $filename . '</strong>' . ' owned by ';
		echo "\n\tuser " . '<strong>' . $username . '</strong>' . ' and group ' . '<strong>' . $groupname . '</strong>' . ' has got access permissions ';
		echo "\n\t<strong>" . $perms_asc . '</strong>' . ' which is ' . '<strong>' . $perms_oct. '</strong>' . ' octal.';
		echo $html_and_ascii_linebreak;
	?>
 </p>
 <p class="block">
	Please ensure correct permission settings of this permission test suite. You
	may modify permissions either by SSH access or by FTP access. The first column
	(italic) shows assumed permissions (what they should be to run this test), next
	is user (owner), group (owner), actual permissions ascii and octal) and the
	subdirectory or filename which was checked.<br />
	<br />
	Permissions where "is" equals "should" are green, deviations are red. If permission
	setting is correctly enabled but "is" shows 999 your webserver won't work with that
	model. There's no access to username, groupname or permission setting. All other
	versions of check.php may be checked. Choose the most restrictive model for security
	reasons. Webservers with SuPHP enabled are known to restrict write permissions, but
	make sure that you don't expose confidential information by sloppy read permissions.
 </p>
 <div class="block"><table class="truetype"><?php
	echo "\n  ";
	//$file="permissioncheck/paranoia";
	//$filename="../".$file;

	foreach ($uc_perms_subdir as $usecase => $perms_subdir) {
		$perms_file=$uc_perms_file[$usecase];
		$filename=$usecase;
		get_perm_data($filename,$username,$groupname,$perms_asc,$perms_oct);
		if ($perms_subdir==$perms_oct) {
			$css_class="equal";
		} else {
			$css_class="notequal";
		}
		echo "<tr>".'<td><em class="'.$css_class.'">'.$perms_subdir."</em></td>"."<td>".$username."</td><td>".$groupname."</td><td>".$perms_asc."</td><td>".$perms_oct.'</td><td><a href="'.$filename.'" target="_blank">permissioncheck/'.$filename."</a></td></tr>\n  ";
		$filename=$usecase."/".$default_file_name;
		get_perm_data($filename,$username,$groupname,$perms_asc,$perms_oct);
		if ($perms_file==$perms_oct) {
			$css_class="equal";
		} else {
			$css_class="notequal";
		}
		echo "<tr>".'<td><em class="'.$css_class.'">'.$perms_file."</em></td>"."<td>".$username."</td><td>".$groupname."</td><td>".$perms_asc."</td><td>".$perms_oct.'</td><td><a href="'.$filename.'" target="_blank">permissioncheck/'.$filename."</a></td></tr>\n  ";
	}
	// general data for special checks
	$perms_unknown='???';
	$css_class_unknown='unknown';
	$css_class_user='user';
	$html_empty_table_row='<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>'."\n  ";
	echo $html_empty_table_row ;
	// special:
	//
//	$homefile='/etc';
//	$filename=$homefile;
//	$perms_file=$perms_unknown;
//	$css_class=$css_class_unknown;
//	get_perm_data($filename,$username,$groupname,$perms_asc,$perms_oct);
//	echo '<tr>' . '<td><em class="'.$css_class.'">' . $perms_file . '</em></td><td>' . $username . '</td><td>' . $groupname . '</td><td>' . $perms_asc . '</td><td>' . $perms_oct . '</td><td>' . $filename . '</td></tr>' . "\n  ";
	//
//	$homefile='/home';
//	$filename=$homefile;
//	$perms_file=$perms_unknown;
//	$css_class=$css_class_unknown;
//	get_perm_data($filename,$username,$groupname,$perms_asc,$perms_oct);
//	echo '<tr>' . '<td><em class="'.$css_class.'">' . $perms_file . '</em></td><td>' . $username . '</td><td>' . $groupname . '</td><td>' . $perms_asc . '</td><td>' . $perms_oct . '</td><td>' . $filename . '</td></tr>' . "\n  ";
	// php safe mode: check for /tmp
	$tmpfile = '/tmp';
	$filename = $tmpfile;
	$perms_file = $perms_unknown;
	$css_class = $css_class_unknown;
	get_perm_data($filename,$username,$groupname,$perms_asc,$perms_oct);
	echo '<tr>' . '<td><em class="'.$css_class.'">' . $perms_file . '</em></td><td>' . $username . '</td><td>' . $groupname . '</td><td>' . $perms_asc . '</td><td>' . $perms_oct . '</td><td>' . $filename . '</td></tr>' . "\n  ";
	//
//	$nosuchfile='/example_does_not_exist';
	$usersubmittedfile = $_POST['usersubmittedfile'];
	//$sendfile = $_POST['sendfile'];
	if ( $usersubmittedfile == "" ) {
		$dummy="foo";
	} else {
		$first_character = substr($usersubmittedfile,0,1);
		if ($first_character == '/') {
			//$path_prefix = '/';
			$path_prefix = '';
		} else {
			$path_prefix = '../';
		}
		//$filename = '../' . $usersubmittedfile;
		$filename = $path_prefix . $usersubmittedfile;
		echo "$filename = $path_prefix . $usersubmittedfile";
		$perms_file = $perms_unknown;
		$css_class = $css_class_user;
		get_perm_data($filename,$username,$groupname,$perms_asc,$perms_oct);
		echo '<tr>' . '<td><em class="'.$css_class.'">' . $perms_file . '</em></td><td>' . $username . '</td><td>' . $groupname . '</td><td>' . $perms_asc . '</td><td>' . $perms_oct . '</td><td>' . $usersubmittedfile . '</td></tr>' . "\n  ";
	}
?>
 </table></div>
 <div>&nbsp;</div>
 <form method="post"><input type="text" name="usersubmittedfile" size="42"> <input type="submit" name="sendfile" value="send path or file"></form>
 <p class="block">
	Enjoy <a href="https://tiki.org/" target="_blank">Tiki</a> and
	<a href="https://tiki.org/tiki-register.php" target="_blank">join the community</a>!
 </p>
</body>
</html>
