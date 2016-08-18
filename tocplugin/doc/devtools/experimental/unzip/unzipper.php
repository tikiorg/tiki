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
// global definitions and some functions

// linefeed
$LF="\n";

// Tiki 9
$tiki_zip_url["tiki-9.4"] = 'http://sourceforge.net/projects/tikiwiki/files/Tiki_9.x_Herbig_Haro/9.4/tiki-9.4.zip/download';
$tiki_zip_url["tiki-9.5"] = 'http://sourceforge.net/projects/tikiwiki/files/Tiki_9.x_Herbig_Haro/9.5/tiki-9.5.zip/download';
$tiki_zip_url["tiki-9.6"] = 'http://sourceforge.net/projects/tikiwiki/files/Tiki_9.x_Herbig_Haro/9.6/tiki-9.6.zip/download';
// Tiki 10
$tiki_zip_url["tiki-10.2"] = 'http://sourceforge.net/projects/tikiwiki/files/Tiki_10.x_Sun/10.2/tiki-10.2.zip/download';
$tiki_zip_url["tiki-10.3"] = 'http://sourceforge.net/projects/tikiwiki/files/Tiki_10.x_Sun/10.3/tiki-10.3.zip/download';
$tiki_zip_url["tiki-10.4"] = 'http://sourceforge.net/projects/tikiwiki/files/Tiki_10.x_Sun/10.4/tiki-10.4.zip/download';
// Tiki 11
$tiki_zip_url["tiki-11.0"] = 'http://sourceforge.net/projects/tikiwiki/files/Tiki_11.x_Vega/11.0/tiki-11.0.zip/download';
//$tiki_zip_url["tiki-."] = '';
//$tiki_zip_url["tiki-."] = '';
//$tiki_zip_url["tiki-."] = '';

function pagebottom()
{
	$LF="\n";
	$bottomtext = '<br /><hr>'.
		      ' <p class="block">'.$LF.
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
        This page should always be visible, independent from any installation
	problems with Tiki. It will help you to download and unzip the downloaded
	Tiki file directly on the webserver. When this is done you may continue
	with Tiki installer or (if necessary) with Tiki Permission Check. Make
	sure to have enough free space on your harddisk. This script will not
	check and the procedure will fail. If some of the values below are wrong,
	try reloading this page.
 </div>
 <p>PHP check: <?php
                echo "PHP works";
        ?>
 </p>

<br /><hr>
<h3>Check Read/Write/Delete Permissions</h3>

<?php
	if (isset($_POST['check'])) {
		$x = $_POST['check'];
	} else {
		$x = 'no check';
	}
	if ($x=='readwritedelete') {
?>
 <p>PHP file check: <?php
		$filename = 'test-php-write.txt';
		checkmyfile_exists($filename);
?></p>
 <p>PHP read check: <?php
		checkmyfile_readable($filename);
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
?> (should be deleted)</p>
 <p>PHP file check: <?php
		checkmyfile_exists($filename);
?> (should not exist now)</p>
 <p>PHP read check: <?php
		checkmyfile_readable($filename);
?> (should not be readable now)</p>
<?php
	}
?>

 <p><form method="post">
 <input type="radio" name="check" value="readwritedelete"> check file permissions<br />
 <br />
 <input type="reset" value="RESET">
 <button name="filecheck" value="checkperms" type="submit">CHECK</button></form>

<br /><hr>
<h3>Download Tiki Version</h3>

<?php
	$download = false;
	if (isset($_POST['choice'])) {
		$x = $_POST['choice'];
		$y = substr($x,0,-4);
		$download_name = $x;
		$download_url = $tiki_zip_url[$y];
		$download = true;
	} else {
		$x = 'no choice';
		$y = 'no choice';
	//	echo "no choice\n";
	}
	//echo "<p>Your Choice: $x</p>\n";

/*
	switch($x) {
		case 'tiki-9.4.zip':
			$download = true;
			$download_url = $tiki_zip_url["tiki-9.4"];
			$download_name = $x;
			break;
		case 'tiki-10.2.zip':
			$download = true;
			$download_url = $tiki_zip_url["tiki-10.2"];
			$download_name = $x;
			break;
		case 'no choice':
			$download = false;
			break;
		default:
			$download = false;
			break;
	}
*/
	if ($download) {
		if (function_exists(curl_exec)) {
			echo "$x to be downloaded from Sourceforge to server\n";
			$ch = curl_init($download_url);
			$fp = fopen($download_name, "w");
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
			curl_exec($ch);
		//	$info = curl_getinfo($ch);
		//	echo $info."\n";
			curl_close($ch);
			fclose($fp);
		} else {
			echo 'PHP curl_exec not installed';
		}
	} else {
		$dummy = 'foo';
	}
?>
 <p><form method="post">
<?php
	foreach ($tiki_zip_url as $version => $url) {
		echo ' <input type="radio" name="choice" value="'.$version.'.zip"> '.$version.'.zip<br />'.$LF ;
	}
// <input type="radio" name="choice" value="tiki-9.4.zip"> tiki-9.4.zip<br />
// <input type="radio" name="choice" value="tiki-10.2.zip"> tiki-10.2.zip<br />
?>
 <br />
 <input type="reset" value="RESET">
 <button name="choose" value="zipfile" type="submit">DOWNLOAD</button></form>
 <!--</p>-->
 <?php //<p><input name="foo" value="caramba" type="submit">INPUT</p> ?>

<br /><hr>
<h3>Unzip Tiki Version</h3>

<?php
	$unzip = false;
	if (isset($_POST['unzip'])) {
		$x = $_POST['unzip'];
		$unzip_name = $x;
		$unzip = true;
/*
		switch($x) {
			case 'tiki-9.4.zip':
				$unzip = true;
				//$download_url = $tiki_zip_url["tiki-9.4"];
				$unzip_name = $x;
				break;
			case 'tiki-10.2.zip':
				$unzip = true;
				//$download_url = $tiki_zip_url["tiki-10.2"];
				$unzip_name = $x;
				break;
			case 'no unzip':
				$unzip = false;
				break;
			default:
				$unzip = false;
				break;
		}
*/
		if ($unzip and (file_exists($unzip_name))) {
		//	system("unzip $unzip_name");
			$zip = new ZipArchive;
			$res = $zip->open("$unzip_name");
			if ($res === TRUE) {
				$zip->extractTo('./');
				$zip->close();
				echo 'unzip ok';
			} else {
				echo 'unzip failed';
			}
		} else {
			echo 'unzip not successful - does the file exist?';
		}
	} else {
		$x = 'no unzip';
	}
?>
 <p><form method="post">
<?php
	foreach ($tiki_zip_url as $version => $url) {
		echo ' <input type="radio" name="unzip" value="'.$version.'.zip"> '.$version.'.zip<br />'.$LF ;
	}
// <input type="radio" name="unzip" value="tiki-9.3.zip"> tiki-9.3.zip<br />
// <input type="radio" name="unzip" value="tiki-9.4.zip"> tiki-9.4.zip<br />
// <input type="radio" name="unzip" value="tiki-10.2.zip"> tiki-10.2.zip<br />
?>
 <br />
 <input type="reset" value="RESET">
 <button name="unzipper" value="zipfile" type="submit">UNZIP</button></form>

<?php
pagebottom();
