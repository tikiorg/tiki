<?php
$check_if_model_works = false;
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) == false) {
	//echo 'This model works';
	$check_if_model_works = true;
	return $check_if_model_works;
} else {
	echo 'Tiki Read Check: this model works';
	return;
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<title>Congratulation | Tiki Installation Permission Check</title>
<style type="text/css">
	.block		{text-align: justify;}
	.truetype	{font-family: courier;}
</style>
</head>
<body>
<h1>Congratulation | Tiki Installation Permission Check</h1>
 <div class="block">
	This page is obviously visible, so the choosen permission model works.
 </div>
 <p class="block">
	Enjoy <a href="https://tiki.org/" target="_blank">Tiki</a> and
	<a href="https://tiki.org/tiki-register.php" target="_blank">join the community</a>!
 </p>
</body>
</html>
