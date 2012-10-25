<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<title>Tiki Installation Permission Check | create new_htaccess</title>
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
<h3>create new_htaccess</h3>
 <div class="block">
	A template for your <span class="truetype">.htaccess</span> is created
	each time you view this page. It should already be there now:
 </div>
 <p>PHP check: <?php
		echo "PHP works\n";
		?>
 </p>
 <div class="block"><table class="truetype"><tr><?php
		require 'functions.inc.php';
		$filename = 'new_htaccess';
		prepare_htaccess_password_protection($filename);
		//$username = get_ownership_username($filename);
		//$groupname = get_ownership_groupname($filename);
		//$perms_oct = get_perms_octal($filename);
		//$perms_asc = get_perms_ascii($filename);
		get_perm_data($filename,$username,$groupname,$perms_asc,$perms_oct);

		echo '<td>' . $username . '</td><td>' . $groupname. '</td><td>' .$perms_asc . '</td>';
		echo '<td>' . $perms_oct . '</td><td>permissioncheck/' . $filename .'</td>';

	?>
</tr> 
 </table></div>
 <div>&nbsp;</div>
 <p><a href="./">permissioncheck</a></p>
 <p class="block">
	Enjoy <a href="https://tiki.org/" target="_blank">Tiki</a> and
	<a href="https://tiki.org/tiki-register.php" target="_blank">join the community</a>!
 </p>
</body>
</html>
