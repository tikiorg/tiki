<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<title>Tiki Installation Permission Check</title>
<style type="text/css">
.block {text-align: justify;}
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
 <p>permission check: <?php
		$filename="index.php";
		$user=(posix_getpwuid(fileowner($filename)));
		$group=posix_getgrgid(filegroup($filename));
		$perms_oct=substr(sprintf('%o', fileperms($filename)), -3);

$perms = fileperms($filename);

if (($perms & 0xC000) == 0xC000) {
    // Socket
    $info = 's';
} elseif (($perms & 0xA000) == 0xA000) {
    // Symbolic Link
    $info = 'l';
} elseif (($perms & 0x8000) == 0x8000) {
    // Regular
    $info = '-';
} elseif (($perms & 0x6000) == 0x6000) {
    // Block special
    $info = 'b';
} elseif (($perms & 0x4000) == 0x4000) {
    // Directory
    $info = 'd';
} elseif (($perms & 0x2000) == 0x2000) {
    // Character special
    $info = 'c';
} elseif (($perms & 0x1000) == 0x1000) {
    // FIFO pipe
    $info = 'p';
} else {
    // Unknown
    $info = 'u';
}

// Owner
$info .= (($perms & 0x0100) ? 'r' : '-');
$info .= (($perms & 0x0080) ? 'w' : '-');
$info .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x' ) :
            (($perms & 0x0800) ? 'S' : '-'));

// Group
$info .= (($perms & 0x0020) ? 'r' : '-');
$info .= (($perms & 0x0010) ? 'w' : '-');
$info .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x' ) :
            (($perms & 0x0400) ? 'S' : '-'));

// World
$info .= (($perms & 0x0004) ? 'r' : '-');
$info .= (($perms & 0x0002) ? 'w' : '-');
$info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x' ) :
            (($perms & 0x0200) ? 'T' : '-'));

//echo $info;

		//$perms_asc=substr(sprintf(fileperms($filename)), -3);
		$perms_asc=$info;
		echo "this file "."<strong>".$filename."</strong>"." owned by ";
		echo "user "."<strong>".$user["name"]."</strong>"." and group "."<strong>".$group["name"]."</strong>"." has got access permissions ";
		echo "<strong>".$perms_asc."</strong>"." which is "."<strong>".$perms_oct."</strong>"." octal.";
		echo "<br />\n";
		//echo substr(sprintf('%o', fileperms($filename)), -3);
		//echo "PHP works";
	?>
 </p>
 <p class="block">
	Please ensure correct permission settings of this permission test suite. You
	may modify permissions either by SSH access or by FTP access. TODO: List of
	files and perms in permission/
 </p>
 <div class="block">
	<div><a href="/permissioncheck/paranoia/" target="_blank">paranoia</a></div>
	<div><a href="/permissioncheck/paranoia-suphp/" target="_blank">paranoia-suphp</a></div>
	<div><a href="/permissioncheck/mixed/" target="_blank">mixed</a></div>
	<div><a href="/permissioncheck/risky/" target="_blank">risky</a></div>
	<?php //<div><a href="/permissioncheck/foo/" target="_blank">foo</a></div>?>
	<?php //<div><a href="/permissioncheck/bar/" target="_blank">bar</a></div>?>
 </div>
 <p class="block">
	Enjoy <a href="https://tiki.org/" target="_blank">Tiki</a> and
	<a href="https://tiki.org/tiki-register.php" target="_blank">join the community</a>!
 </p>
</body>
</html>
