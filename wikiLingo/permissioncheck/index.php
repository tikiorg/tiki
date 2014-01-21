<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<title>Tiki Installation Permission Check</title>
<style type="text/css">
	.block		{text-align: justify;}
	.equal		{background-color: green;}
	.hint		{background-color: black;	color:	yellow;}
	.important	{background-color: black;	color:	red;}
	.modelworksno	{background-color: red;}
	.modelworksyes	{background-color: green;}
	.notequal	{background-color: red;}
<?php /*
	.readno	{background-color: red;}
	.readno	{background-color: orange;}
	.readno	{background-color: #88FFCC;}
 */ ?>
	.readno	{background-color: red;}
<?php /*
	.readyes	{background-color: green;}
	.readyes	{background-color: yellow;}
	.readyes	{background-color: #FF88CC;}
 */ ?>
	.readyes	{background-color: green;}
	.truetype	{font-family: courier;		background-color: #888888;}
	.unknown	{background-color: yellow;}
	.user		{background-color: blue;}
<?php /*
	.writeno	{background-color: red;}
	.writeno	{background-color: orange;}
	.writeno	{background-color: #88FFCC;} */
 ?>
	.writeno	{background-color: #FF88CC;}
<?php /*
	.writeyes	{background-color: green;}
	.writeyes	{background-color: yellow;}
	.writeyes	{background-color: #FF88CC;}
 */ ?>
	.writeyes	{background-color: #88FFCC;}
	a:hover		{background-color: orange;}
</style>
</head>
<body>
<h1>Tiki Installation Permission Check</h1>
<h3>Installation Problems?</h3>
<h4>check required filesystem permissions for your webserver</h4>
 <div class="block">
	This page should always be visible, independent from any installation problems
	with Tiki. If the Tiki installer does not run properly, this effect may be
	caused by some permission problems (some problems may be caused by webserver
	settings regarding htaccess or PHP settings regarding memory limit). There are
	many different use cases, thus there is no default permission setting which
	works in all cases and provides an appropriate security level.
 </div>
 <p>PHP check: <?php
		echo "PHP works";
	?>
 </p>
 <p>
	<?php
	include "permission_granted.inc.php";
	$ascii_linebreak = "\n";
	$html_and_ascii_linebreak = "<br />\n";
	if ($permission_granted=="yes\n") {
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
		echo '</p>' . $ascii_linebreak;
		echo ' <p><a href="./">permissioncheck</a></p>' . $ascii_linebreak;
		echo ' <p class="block">' . $ascii_linebreak;
		echo '	Enjoy <a href="https://tiki.org/" target="_blank">Tiki</a> and' . $ascii_linebreak;
		echo '	<a href="https://tiki.org/tiki-register.php" target="_blank">join the community</a>!' . $ascii_linebreak;
		echo ' </p>' . $ascii_linebreak;
		echo '</body></html>';
		die;
	}
	?>
 </p>
 <p>
	permission check: <?php
		//include "functions.inc.php";
		require 'functions.inc.php';
		//include "usecases,inc.php";
		require 'usecases.inc.php';
		$filename = 'index.php';
		$username = get_ownership_username($filename);
		$groupname = get_ownership_groupname($filename);
		$perms_oct = get_perms_octal($filename);
		$perms_asc = get_perms_ascii($filename);
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
	$html_almost_empty_table_row = '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td>';
	$html_empty_table_row = '<tr><td>&nbsp;</td><td></td><td></td><td></td><td></td><td></td></tr>'."\n  ";
	echo '<tr>'.'<td>should</td>'.'<td>user</td>'.'<td>group</td>'.'<td>ascii and <br />colored&nbsp;PHP<br />permissions';
	echo '<br />read:<span class="readyes">yes</span>/<span class="readno">no</span>';
	echo '<br />write:<span class="writeyes">yes</span>/<span class="writeno">no</span></td>';
	echo '<td>octal</td>'.'<td>filename</td>'.'</tr>';
	foreach ($uc_perms_subdir as $usecase => $perms_subdir) {
		$perms_file=$uc_perms_file[$usecase];
		echo $html_empty_table_row;
		// subdir
		$filename=$usecase;
		get_perm_data($filename, $username, $groupname, $perms_asc, $perms_oct);
		if ($perms_subdir==$perms_oct) {
			$css_class="equal";
		} else {
			$css_class="notequal";
		}
		color_classes_perm_asc($filename, $perms_asc, $css_class_writable);
		echo '<tr>'.'<td><em class="'.$css_class.'">'.$perms_subdir.'</em></td>'.'<td>'.$username.'</td><td>'.$groupname.'</td>';
		echo '<td class="' . $css_class_writable . '">'.$perms_asc.'</td><td>'.$perms_oct.'</td>';
		echo '<td><a href="'.$filename.'" target="_blank">permissioncheck/'.$filename."</a></td></tr>\n  ";
		// file
		$filename=$usecase."/".$default_file_name;
		get_perm_data($filename, $username, $groupname, $perms_asc, $perms_oct);
		if ($perms_file==$perms_oct) {
			$css_class="equal";
		} else {
			$css_class="notequal";
		}
//		if ( is_writable($filename) ) {
//			$css_class_writable = 'writeyes';
//		} else {
//			$css_class_writable = 'writeno';
//		}
//		$css_class_writable = 'noclass';
		color_classes_perm_asc($filename, $perms_asc, $css_class_writable);
		echo '<tr>'.'<td><em class="'.$css_class.'">'.$perms_file.'</em></td>'.'<td>'.$username.'</td><td>'.$groupname.'</td>';
		echo '<td class="' . $css_class_writable . '">'.$perms_asc.'</td><td>'.$perms_oct.'</td>';
		echo '<td><a href="'.$filename.'" target="_blank">permissioncheck/'.$filename."</a></td></tr>\n  ";
		// include this file as external one via HTTP request
		echo $html_almost_empty_table_row;
		echo '<td>';
	//	$check_if_model_works = false;
	//	include $filename;
	//	if ( $check_if_model_works ) {
	//		$check_if_model_works_text = '<span class="modelworksyes">Read: this model works for you</span>';
	//	} else {
	//		$check_if_model_works_text = '<span class="modelworksno">Read: this model does not work for you!</span>';
	//	}
		$url_name = get_page_url($filename);
		//print $url_name;
		$http_request = 'foo';
		$http_request = @file_get_contents($url_name);
		if ($http_request === false) {
			$http_output = '<span class="modelworksno">' . 'THIS DOES NOT WORK' . '</span>';
		} elseif ((strpos($http_request, 'arning') == true) or (strpos($http_request, 'rror') == true)) {
			$http_output = '<span class="modelworksno">' . 'THIS DOES NOT WORK' . '</span>';
		} else {
			$http_output = '<span class="modelworksyes">' . $http_request . '</span>';
		}
		//print file_get_contents($url_name) or print 'THIS DOES NOT WORK';
		echo $http_output;
		//echo $check_if_model_works_text;
		//echo $check_if_model_works_text . '</td>'."\n ";
		echo '</td>'."\n ";
	}
	// general data for special checks
	$perms_unknown = '???';
	$css_class_unknown = 'unknown';
	$css_class_user = 'user';
	echo $html_empty_table_row ;
	// special:
	// php safe mode: check for /tmp
	$tmpfile = '/tmp';
	$filename = $tmpfile;
	$perms_file = $perms_unknown;
	$css_class = $css_class_unknown;
	get_perm_data($filename, $username, $groupname, $perms_asc, $perms_oct);
//	if ( is_writable($filename) ) {
//		$css_class_writable = 'writeyes';
//	} else {
//		$css_class_writable = 'writeno';
//	}
	color_classes_perm_asc($filename, $perms_asc, $css_class_writable);
	echo '<tr>' . '<td><em class="'.$css_class.'">' . $perms_file . '</em></td><td>' . $username . '</td><td>' . $groupname . '</td>';
	echo '<td class="' . $css_class_writable . '">' . $perms_asc . '</td><td>' . $perms_oct . '</td><td>' . $filename . '</td></tr>' . "\n  ";
	//
//	$nosuchfile='/example_does_not_exist';
	$usersubmittedfile = isset($_POST['usersubmittedfile']) ? $_POST['usersubmittedfile'] : '';
	//$checkfile = $_POST['checkfile'];
	if ( $usersubmittedfile == "" ) {
		$dummy="foo";
	} else {
		$first_character = substr($usersubmittedfile, 0, 1);
		if ($first_character == '/') {
			//$path_prefix = '/';
			$path_prefix = '';
			$display_name = $usersubmittedfile;
		} else {
			$path_prefix = '../';
			$tmp_url = get_page_url_clean($usersubmittedfile);
			$display_name = '<a href="' . $tmp_url . '">' . $tmp_url . '</a>';
		}
		//$filename = '../' . $usersubmittedfile;
		$filename = $path_prefix . $usersubmittedfile;
		$perms_file = $perms_unknown;
		$css_class = $css_class_user;
		get_perm_data($filename, $username, $groupname, $perms_asc, $perms_oct);
//		if ( is_writable($filename) ) {
//			$css_class_writable = 'writeyes';
//		} else {
//			$css_class_writable = 'writeno';
//		}
		color_classes_perm_asc($filename, $perms_asc, $css_class_writable);
		echo '<tr>' . '<td><em class="'.$css_class.'">' . $perms_file . '</em></td><td>' . $username . '</td><td>' . $groupname . '</td>';
		//echo '<td class="' . $css_class_writable . '">' . $perms_asc . '</td><td>' . $perms_oct . '</td><td>' . $usersubmittedfile . '</td></tr>' . "\n  ";
		echo '<td class="' . $css_class_writable . '">' . $perms_asc . '</td><td>' . $perms_oct . '</td><td>' . $display_name . '</td></tr>' . "\n  ";
	}
?>
 </table></div>
 <div>&nbsp;</div>
 <form method="post"><input type="text" name="usersubmittedfile" size="42"> <input type="submit" class="btn btn-default btn-sm" name="checkfile" value="check path or file"></form>
 <p><a href="./">permissioncheck</a></p>
 <p><a href="./create_new_htaccess.php">create new_htaccess</a></p>
 <p class="block">
	Enjoy <a href="https://tiki.org/" target="_blank">Tiki</a> and
	<a href="https://tiki.org/tiki-register.php" target="_blank">join the community</a>!
 </p>
</body>
</html>
