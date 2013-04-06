<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="robots" content="noindex, nofollow" />
<title>Tiki Installation Unzipper</title>
<style type="text/css">
        .block          {text-align: justify;}
        a:hover         {background-color: orange;}
</style>
</head>
<?php
function pagebottom()
{
	$LF="\n";
	//echo ' <p class="block">'.$LF;
	//echo '	Enjoy <a href="https://tiki.org/" target="_blank">Tiki</a> and'.$LF;
	//echo '        <a href="https://tiki.org/tiki-register.php" target="_blank">join the community</a>!'.$LF;
	//echo ' </p>'.$LF;
	//echo '</body>'.$LF;
	//echo '</html>'.$LF;
	$bottomtext = ' <p class="block">'.$LF.
		      '	Enjoy <a href="https://tiki.org/" target="_blank">Tiki</a> and'.$LF.
		      '        <a href="https://tiki.org/tiki-register.php" target="_blank">join the community</a>!'.$LF.
		      ' </p>'.$LF.
		      '</body>'.$LF.
		      '</html>'.$LF;
	echo $bottomtext;
}

function checkmyfile_readable($filename)
{
	$read_permission = true;
	$fileout = fopen($filename, 'r') or $read_permission = false;
	if ( $read_permission ) {
		echo 'testfile is readable';
		fclose($fileout);
	} else {
		echo 'testfile is NOT readable';
	}
}

function checkmyfile_exists($filename)
{
	if ( file_exists($filename) ) {
		echo 'testfile does exist';
	} else {
		echo 'testfile does NOT exist';
	}
}
?>
<body>
<h1>Tiki Installation Unzipper</h1>
<h3>Installation Helper</h3>
<h4>unzip the Tiki package</h4>
<?php


?>
 <div class="block">
        This page should always be visible, independent from any installation problems
        with Tiki. It will help you to unzip the downloaded Tiki file directly on the
	webserver. When this is done you may continue with Tiki installer or (if
	necessary) with Tiki Permission Check. Make sure to have enough free space on
	your harddisk. This script will not check and the procedure will fail. If some
	of the values below are wrong, try reloading this page.
 </div>
 <p>PHP check: <?php
                echo "PHP works";
        ?>
 </p>
 <p>PHP file check: <?php
		$filename = 'test-php-write.txt';
		/*if ( file_exists($filename) ) {
			echo 'testfile does exist';
		} else {
			echo 'testfile does NOT exist';
		}*/
		checkmyfile_exists($filename);
?></p>
 <p>PHP read check: <?php
		/*$read_permission = true;
		$fileout = fopen($filename, 'r') or $read_permission = false;
		if ( $read_permission ) {
			echo 'testfile is readable';
			fclose($fileout);
		} else {
			echo 'testfile is NOT readable';
		}*/
		checkmyfile_readable($filename);
		//echo "\n";
?></p>
 <p>PHP write check: <?php
		$testcontent = 'foobar'."\n";
		$write_permission = true;
		$fileout = fopen($filename, 'w') or $write_permission = false;
		if ( $write_permission ) {
			fwrite($fileout, $testcontent);
			fclose($fileout);
			echo 'testfile is writable';
		} else {
			echo 'testfile is NOT writable';
		}
			//$dummy = 'foobar';
?> (should be writable)</p>
 <p>PHP file check: <?php
		checkmyfile_exists($filename);
?> (should exist now)</p>
 <p>PHP read check: <?php
		checkmyfile_readable($filename);
?> (should be readable now)</p>
 <p>PHP delete check: <?php
		unlink($filename) or die('cannot delete testfile - ERROR');
		echo 'testfile deleted';
?></p>
 <p>PHP file check: <?php
		checkmyfile_exists($filename);
?> (should not exist now)</p>
 <p>PHP read check: <?php
		checkmyfile_readable($filename);
?> (should not be readable now)</p>

<?php
	if (isset($_POST['choice'])) {
		$x = $_POST['choice'];
	} else {
		$x = 'no choice';
	//	echo "no choice\n";
	}
	echo "<p>Your Choice: $x</p>\n";
	if ( $x=='tiki-9.4.zip' ) {
		echo "$x to be downloaded from sourceforge to server\n";
		$ch = curl_init("http://sourceforge.net/projects/tikiwiki/files/Tiki_9.x_Herbig_Haro/9.4/tiki-9.4.zip/download");
		$fp = fopen("tikinew.zip", "w");
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		//curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_exec($ch);
	//	$info = curl_getinfo($ch);
	//	echo $info."\n";
		curl_close($ch);
		fclose($fp);
	}
?>
 <p><form method="post">
 <input type="radio" name="choice" value="a">a
 <input type="radio" name="choice" value="b">b
 <input type="radio" name="choice" value="tiki-9.4.zip">tiki-9.4.zip
 <input type="reset" value="RESET">
 <button name="choose" value="zipfile" type="submit">GO</button></form>
 <!--</p>-->
 <?php //<p><input name="foo" value="caramba" type="submit">INPUT</p> ?>

<?php
pagebottom();
?>
<?php /*<!--
</body>
</html>
-->*/?>
