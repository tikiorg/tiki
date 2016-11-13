<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 *
 */
class PdfGenerator
{
	private $mode;
	private $location;

    /**
     *
     */
    function __construct()
	{
		global $prefs;
		$this->mode = 'none';

		if ( $prefs['print_pdf_from_url'] == 'webkit' ) {
			$path = $prefs['print_pdf_webkit_path'];
			if (!empty($path) && is_executable($path)) {
				$this->mode = 'webkit';
				$this->location = $path;
			} else {
				TikiLib::lib('errorreport')->report(tr('PDF webkit path not found: "%0"', $path));
			}
		} else if ($prefs['print_pdf_from_url'] == 'weasyprint') {
			$path = $prefs['print_pdf_weasyprint_path'];
			if (!empty($path) && is_executable($path)) {
				$this->mode = 'weasyprint';
				$this->location = $path;
			} else {
				TikiLib::lib('errorreport')->report(tr('PDF WeasyPrint path not found: "%0"', $path));
			}
		} elseif ( $prefs['print_pdf_from_url'] == 'webservice' ) {
			$path = $prefs['path'];
			if ( ! empty($path) ) {
				$this->mode = 'webservice';
				$this->location = $path;
			} else {
				TikiLib::lib('errorreport')->report(tr('PDF webservice URL empty'));
			}
		} elseif ( $prefs['print_pdf_from_url'] == 'mpdf' ) {
			$path = $prefs['print_pdf_mpdf_path'];
			if (substr($path, -1) !== '/') {
				$path .= '/';
			}
			if ( ! empty($path) && is_readable($path) && file_exists($path . 'mpdf.php')) {
				self::setupMPDFCacheLocation();
				if (!class_exists('mPDF')){
					include_once($path . 'mpdf.php');
				}
				if (! is_writable(_MPDF_TEMP_PATH) ||! is_writable(_MPDF_TTFONTDATAPATH)) {
					Feedback::error(tr('mPDF "%0" and "%1" directories must be writable', 'tmp',
					'ttfontdata'), 'session');
				} else {
					$this->mode = 'mpdf';
					$this->location = $path;
				}
			} else {
				Feedback::error(tr('mPDF not found in path: "%0"', $path), 'session');
			}
		}
	}

    /**
     * @param $file
     * @param array $params
     * @return mixed
     */
    function getPdf( $file, array $params )
	{
		global $prefs, $base_url, $tikiroot;

		if ( $prefs['auth_token_access'] == 'y' ) {
			$perms = Perms::get();

			require_once 'lib/auth/tokens.php';
			$tokenlib = AuthTokens::build($prefs);
			$params['TOKEN'] = $tokenlib->createToken(
				$tikiroot . $file, $params, $perms->getGroups(),
				array('timeout' => 120)
			);
		}

		$url = $base_url . $file . '?' . http_build_query($params, '', '&');

		return $this->{$this->mode}( $url );
	}

    /**
     * @param $url
     * @return null
     */
    private function none( $url )
	{
		return null;
	}

    /**
     * @param $url
     * @return mixed
     */
    private function webkit( $url )
	{
		// Make sure shell_exec is available
		if (!function_exists('shell_exec')) {
			die(tra('Required function shell_exec is not enabled.'));
		}

		// escapeshellarg will replace all % characters with spaces on Windows
		// So, decode the URL before sending it to the commandline
		$urlDecoded = urldecode($url);
		$arg = escapeshellarg($urlDecoded);

		// Write a temporary file, instead of using stdout
		// There seemed to be encoding issues when using stdout (on Windows 7 64 bit).

		// Use temp/public. It is cleaned up during a cache clean, in case some files are left
		$filename = 'temp/public/out'.rand().'.pdf';

		// Run shell_exec command to generate out file
		// NOTE: this requires write permissions
		$quotedFilename = '"'.$filename.'"';
		$quotedCommand = '"'.$this->location.'"';
		
		`$quotedCommand -q $arg $quotedFilename`;

		// Read the out file
		$pdf = file_get_contents($filename);

		// Delete the outfile
		unlink($filename);

		return $pdf;
	}

	/**
     * @param $url
     * @return mixed
     */
    private function weasyprint( $url )
	{
		// Make sure shell_exec is available
		if (!function_exists('shell_exec')) {
			die(tra('Required function shell_exec is not enabled.'));
		}

		// escapeshellarg will replace all % characters with spaces on Windows
		// So, decode the URL before sending it to the commandline
		$urlDecoded = urldecode($url);
		$arg = escapeshellarg($urlDecoded);

		// Write a temporary file, instead of using stdout
		// There seemed to be encoding issues when using stdout (on Windows 7 64 bit).

		// Use temp/public. It is cleaned up during a cache clean, in case some files are left
		$filename = 'temp/public/out'.rand().'.pdf';

		// Run shell_exec command to generate out file
		// NOTE: this requires write permissions
		$quotedFilename = '"'.$filename.'"';
		$quotedCommand = '"'.$this->location.'"';

		// redirect STDERR to null with 2>/dev/null becasue it outputs plenty of irrelevant warnings (hopefully nothing critical)
		`$quotedCommand $arg $quotedFilename 2>/dev/null`;

		// Read the out file
		$pdf = file_get_contents($filename);

		// Delete the outfile
		unlink($filename);

		return $pdf;
	}

    /**
     * @param $url
     * @return bool
     */
    private function webservice( $url )
	{
		global $tikilib;

		$target = $this->location . '?' . $url;
		return $tikilib->httprequest($target);
	}

	/**
	 * Setup mPDF Cache locations to a folder (mpdf) inside the filesystem cache
	 */
	static public function setupMPDFCacheLocation()
	{
		// set cache paths
		$cache = new CacheLibFileSystem();
		$mPDFBaseCachePath = $cache->folder . '/mpdf/';
		if (!is_dir($mPDFBaseCachePath)) {
			mkdir($mPDFBaseCachePath);
			chmod($mPDFBaseCachePath, 0777);
		}

		$constantsAndDirectories = array(
			'_MPDF_TEMP_PATH'      => 'tmp/',
			'_MPDF_TTFONTDATAPATH' => 'ttfontdata/',
		);

		foreach ($constantsAndDirectories as $constant => $directory) {
			if (!is_dir($mPDFBaseCachePath . $directory)) {
				mkdir($mPDFBaseCachePath . $directory);
				chmod($mPDFBaseCachePath . $directory, 0777);
			}
			if (!defined($constant)) {
				define($constant, $mPDFBaseCachePath . $directory);
			}
		}
	}

	/**
	 * @param $url string - address of the item to print as PDF
	 * @return string     - contents of the PDF
	 */
	private function mpdf($url)
	{
		if (!extension_loaded('curl')) {
			TikiLib::lib('reporterror')->report(tra('mPDF: CURL PHP extension not available'));
			return '';
		}

		// To prevent anyone else using your script to create their PDF files - TODO?
		//if (!preg_match("/^$base_url/", $url)) { die("Access denied"); }

/*		FIXME later, cookie auth not working yet
		$cookie = [];
		foreach ($_COOKIE as $key => $value) {
		    $cookie[] = "{$key}={$value}";
		};
		$cookie = implode(';', $cookie);

		$ckfile = tempnam("/tmp", 'curl_cookies_');
		file_put_contents($ckfile, $cookie);

		$curl_log = fopen('temp/curl_debug.txt', 'w+'); // open file for READ and write
*/

		$options = array(
			CURLOPT_RETURNTRANSFER => true,     // return web page
			CURLOPT_HEADER => false,     		// return headers in addition to content
			CURLINFO_HEADER_OUT => true,
			CURLOPT_ENCODING => "",       		// handle all encodings
			CURLOPT_HTTPHEADER => ['Expect:'],	// remove Expect header to avoid 100 Continue situations?

			CURLOPT_FOLLOWLOCATION => true,     // follow redirects
			CURLOPT_AUTOREFERER => true,		// set referer on redirect
			CURLOPT_MAXREDIRS => 10,			// stop after 10 redirects
			CURLOPT_CONNECTTIMEOUT => 10,		// timeout on connect
			CURLOPT_TIMEOUT => 30,				// timeout on response

			CURLOPT_SSL_VERIFYPEER => false,	// Disabled SSL Cert checks
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

/*			CURLOPT_COOKIE => $cookie,
			//CURLOPT_COOKIESESSION => true,	// no difference
			//CURLOPT_COOKIEFILE => $ckfile,
			CURLOPT_COOKIEJAR => $ckfile,

			CURLOPT_VERBOSE => true,
			CURLOPT_STDERR => $curl_log,
*/
		);

		// For $_POST i.e. forms with fields
		if (count($_POST) > 0) {
			$ch = curl_init($url);

			curl_setopt_array( $ch, $options );

			$formvars = [];
			foreach ($_POST AS $name => $post) {
				$formvars = [ $name => $post . " \n" ];
			}
			curl_setopt($ch, CURLOPT_POSTFIELDS, $formvars);
			$html = curl_exec($ch);
			curl_close($ch);
		} else {
			$ch = curl_init($url);

			curl_setopt_array($ch, $options );

			$html = curl_exec($ch);
			curl_close($ch);

			if (!$html) {
				$err = curl_error($ch);
				TikiLib::lib('errorreport')->report($err ? $err : tr('mPDF: An error occurred retrieving page %0', $url));
				return '';
			}
		}

		self::setupMPDFCacheLocation();
		if (!class_exists('mPDF')){
			include_once($this->location . 'mpdf.php');
		}
		$mpdf = new mPDF('');
		$mpdf->useSubstitutions = true;					// optional - just as an example
		$mpdf->SetHeader($url . '||Page {PAGENO}');		// optional - just as an example
		$mpdf->CSSselectMedia = 'print';				// assuming you used this in the document header

		$mpdf->autoScriptToLang = true;
		$mpdf->autoLangToFont = true;

		$mpdf->setBasePath($url);
		$mpdf->WriteHTML($html);

		return $mpdf->Output('', 'S');					// Return as a string
	}
}

