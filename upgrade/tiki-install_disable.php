<?php
/*
 * Created on 24.5.2005
 */
 
	$removed = false;
	$fh = fopen('tiki-install.php', 'rb');
	$data = fread($fh, filesize('tiki-install.php'));
	fclose($fh);

if (!file_exists("install/tiki-install.php")) {
	echo "no such file";
} else {
	if (is_writable("install/tiki-install.php")) {
		/* first try to delete the file */
		if (@unlink("install/tiki-install.php")) {
			$removed = true;
		}
		/* if it fails, then try to rename it */
		else if (@rename("install/tiki-install.php","install/tiki-install_php.bak")) {
			$removed = true;
		}
		/* otherwise here's an attempt to delete the content of the file */
		else {
			$data = preg_replace('/\/\/stopinstall: /', '', $data);
			$fh = fopen('install/tiki-install.php', 'wb');
			if (fwrite($fh, $data) > 0) {
				$removed = true;
			}
			fclose($fh);
		}
	}

	if ($removed == true) {
		header ('location: tiki-index.php');
	} else {
		print "<html><body>
<p><font color='red'><b>Security Alert!</b><br />
Tiki installer failed to rename <b>tiki-install.php</b>.  Please remove or rename the file, <b>tiki-install.php</b>, manually.  Others can potentially wipe out your Tiki database if you do not remove or rename this file.</b></font><br />
<a href='index.php'>Proceed to your site</a> after you have removed or renamed <b>tiki-install.php</b></p>
</body></html>";
	}
	die;
}
?>
