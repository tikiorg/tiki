<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<title>Tiki Installation Permission Check</title>
<style type="text/css">
div.block {text-align: justify;}
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
