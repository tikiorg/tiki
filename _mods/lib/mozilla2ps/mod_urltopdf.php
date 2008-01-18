<?php
/* mod_urltopdf
 *   Function that uses mozilla2ps XUL application, xulrunner, ps2pdf and an X server (like Xvfb)
 *   to convert an URL to a PDF by doing an HTTP request on server side.
 *     - $url: the URL to convert to PDF. If not specified, use the current tiki URL with a 'print_all' display instead of 'pdf'
 *     - $filename: the filename presented to the web browser. Default: file.pdf
 *     - $options: mozilla2ps options. Default: -margins 0.25 -papername A4 -bgcolors true -bgimages true
 *     - $display: the display on which the X server is running. Default: :1
 *     - $try_fallback: weither to fallback to xvfb-run or not when the PDF can't be generated through an X server on $display
 */
function mod_urltopdf($url = null, $filename = 'file.pdf', $options = null, $display = ':1', $try_fallback = true) {
	global $base_url, $tikiroot, $tikipath, $prefs;
	if ( $url !== null && ( empty($url) || ! is_string($url) || $local_url[0] != '/' ) ) return false;

	// Default commands paths
	$xulrunner_path = '/usr/bin/xulrunner';
	$mozilla2ps_ini_path = $tikipath.'lib/mozilla2ps/application.ini';
	$ps2pdf_path = '/usr/bin/ps2pdf';
	$xvfb_wrapper_path = '/usr/bin/xvfb-run -a -n 1 -l';

	// Timeout of the pdf generation (in seconds)
	$timeout = 20;

	// Default directories
	$temp_dir = ( (( $prefs['tmpDir'][0] != '/' ) ? $tikipath : '').$prefs['tmpDir'] );

	// Generate a unique temporary filename
	$temp_file = tempnam($temp_dir, 'tiki_pdf_');
	unlink($temp_file);
	$temp_file .= '.ps';

	// Build mozilla2ps options
	if ( $options === null ) {
		// Default options
		$options_str = ' -margins 0.25 -papername A4 -bgcolors true -bgimages true';
	} elseif ( is_array($options) ) {
		$options_str = '';
		foreach ( $options as $k => $v ) {
			$options_str .= ' -'.$k.' '.$v;
		}
	}

	// Unlock current script's session to be able to use this session in the script called by xulrunner
	$current_session_id = session_id();
	session_write_close();

	// Build the full URL from the local one
	//  and use the current PHP session ID to be authenticated as the same user for the PDF content
	if ( $url === null ) {
		$url = $base_url
			.str_replace($tikiroot, '', $_SERVER['PHP_SELF'])
			.'?'.str_replace('display=pdf', 'display=print_all', $_SERVER['QUERY_STRING'])
			.'&PHPSESSID='.$current_session_id;
	}
///	if ( $prefs['force_http_for_local_requests'] == 'y' ) {
///		$url = ereg_replace('^https://', 'http://', $url);
///	}

	// Build the command to execute
	$exec_output = array();
	$command = $xulrunner_path.' --app '.$mozilla2ps_ini_path." '".$url."' ".$temp_file.' '.$options_str;

	// Set some environment variables that are needed by xulrunner & co.
	putenv('DISPLAY='.$display);
	putenv('HOME='.$temp_dir);

	// First try with an X server (e.g. Xvfb running)
	$exec_output = array();
	$ps_ok = -1;
	exec($command.' 2>&1', $exec_output, $ps_ok);

	// Fallback to xvfb-run if it failed
	if ( $ps_ok != 0 && $try_fallback ) {
		@unlink($temp_file);
		$exec_output = array();
		exec($xvfb_wrapper_path.' '.$command.' 2>&1', $exec_output, $ps_ok);
	}

	// Wait for the file
	$t = $timeout;
	do { sleep(1); $t--; } while ( ! file_exists($temp_file) && $t > 0 );
	sleep(1);

	// Send the generated PDF content to the browser
	//   or display an error if there was something wrong
	if ( $ps_ok == 0 && $t > 0 ) {
		header('Cache-Control: private');
		header('Content-Type: application/pdf');
		header('Content-Disposition: inline; filename="'.$filename.'"');
		flush();
		passthru($ps2pdf_path.' "'.$temp_file.'" -');
	} elseif ( $ps_ok == 0 ) {
		echo tra("Request Timed Out.");
	} else {
		echo "Error ($ps_ok):<br/><pre>".implode("\n", $exec_output).'</pre>';die;
	}

	// Delete the temporary file
	@unlink($temp_file);

	// Get the session back in this script
	session_id($current_session_id);
	session_start();
}
?>
