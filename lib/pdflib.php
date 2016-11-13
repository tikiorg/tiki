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
				Feedback::error(tr('PDF webkit path not found: "%0"', $path), 'session');
			}
		} else if ($prefs['print_pdf_from_url'] == 'weasyprint') {
			$path = $prefs['print_pdf_weasyprint_path'];
			if (!empty($path) && is_executable($path)) {
				$this->mode = 'weasyprint';
				$this->location = $path;
			} else {
				Feedback::error(tr('PDF WeasyPrint path not found: "%0"', $path), 'session');
			}
		} elseif ( $prefs['print_pdf_from_url'] == 'webservice' ) {
			$path = $prefs['path'];
			if ( ! empty($path) ) {
				$this->mode = 'webservice';
				$this->location = $path;
			} else {
				Feedback::error(tr('PDF webservice URL empty'), 'session');
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
    function getPdf( $file, array $params, $pdata='' )
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
        $session_params = session_get_cookie_params();
		//need to hide edit icons appearing in pdf
	//	$pdata=preg_replace('<img src="img/icons/page_edit.png" alt="Edit" width="16" height="16" title="" class="icon tips" data-original-title="" data-pin-nopin="true" aria-describedby="popover768086" style="display: inline-block;">', '', $pdata); 
		//$pdata=str_replace('class="editplugin tips"','class="editplugin tips" style=display:none',str_replace('src="img/icons/page_edit.png"','src=""',$pdata));
	return $this->{$this->mode}( $url,$pdata);	}

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
	private function mpdf($url,$parsedData='')
	{
		if (!extension_loaded('curl')) {
			TikiLib::lib('reporterror')->report(tra('mPDF: CURL PHP extension not available'));
			return '';
		}
      if($parsedData!='')
	      $html=$parsedData;
	   else
	   {	  
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
				Feedback::error($err ? $err : tr('mPDF: An error occurred retrieving page %0', $url), 'session');
				return '';
			}
		}
	   }
       //getting n replacing images
	   $tempImgArr=array();
	   $this->_parseHTML($html);
	
	   $this->_getImages($html,$tempImgArr);
		self::setupMPDFCacheLocation();
		if (!class_exists('mPDF')){
			include($this->location . 'mpdf.php');
		}
		$mpdf = new mPDF('utf-8');
		$mpdf->useSubstitutions = true;					// optional - just as an example
		$mpdf->SetHeader($url . '||Page {PAGENO}');		// optional - just as an example
		$mpdf->CSSselectMedia = 'print';				// assuming you used this in the document header

		$mpdf->autoScriptToLang = true;
		$mpdf->autoLangToFont = true;

		$mpdf->setBasePath($url);
		
		$stylesheet = file_get_contents('themes/base_files/css/tiki_base.css'); // external css
        $mpdf->WriteHTML($stylesheet,1);
		
		
		//getting main theme css
		global $prefs;
	    $themeLib = TikiLib::lib('theme');
        $themecss=$themeLib->get_theme_path($prefs['theme'], '', $prefs['theme'] . '.css');
		$stylesheet = file_get_contents($themecss); // external css
        $mpdf->WriteHTML($stylesheet.'@page,body.print* {background:#fff;color:#000;} p,.print{color:#000;} .editplugin{display:none;visibility:hidden}',1);
		 
		$stylesheet = file_get_contents('vendor/fortawesome/font-awesome/css/font-awesome.min.css'); // external css
        $mpdf->WriteHTML($stylesheet,1);
		$mpdf->WriteHTML('<html><body class="print">'.$html."</body></html>");
	    //echo 'after'.$html;
	    $this->clearTempImg($tempImgArr);
        return $mpdf->Output('', 'S');					// Return as a string
	}
	
	function _getImages(&$html,&$tempImgArr)
	{
			$doc = new DOMDocument();
			@$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

			$tags = $doc->getElementsByTagName('img');

			foreach ($tags as $tag) {
       			$imgSrc=$tag->getAttribute('src');
				//replacing image with new temp image, all these images will be unlinked after pdf creation
				$newFile=$this->file_get_contents_by_fget($imgSrc);
				//replacing old protected image path with temp image
				if($newFile!='')
				   $tag->setAttribute('src',$newFile);
				$tempImgArr[]=$newFile;
				}	
				$html=@$doc->saveHTML();
		}
	
	function file_get_contents_by_fget($url){
		global $base_url;
		
		//check if image is internal with full path
		$internalImg=0;
		  if(substr($url,0,strlen($base_url))==$base_url)  
		    $internalImg=1;
		 
		//checking for external images
		$checkURL = parse_url($url);
		
		
        //not replacing in case of external image
       if(($checkURL['scheme'] == 'https' || $checkURL['scheme'] == 'http') && !$internalImg){
          return '';
		  }
	
	    if(!$internalImg)
		  $url=$base_url.$url;	  
		
		if(! file_exists ('temp/pdfimg'))
		{
			mkdir('temp/pdfimg');
			chmod('temp/pdfimg',0755);
			
			}
			
	$opts = array('http' => array('header'=> 'Cookie: ' . $_SERVER['HTTP_COOKIE']."\r\n"));
	$context = stream_context_create($opts);
	session_write_close();
	$data=file_get_contents($url, false, $context);
	$newFile='temp/pdfimg/pdfimg'.rand(9999,999999).'.png';
	file_put_contents($newFile, $data);
	chmod($newFile,0755);
	
	
    return $newFile;

	}
	
  function clearTempImg($tempImgArr){ 
	   foreach ($tempImgArr as $tempImg) {
       unlink($tempImg);
      }
	  }
	  
  function _parseHTML(&$html)
	{
		
		$html=str_replace('style="visibility:hidden" class="ts-wrapperdiv">','style="visibility:visible" class="ts-wrapperdiv">',$html);
        $doc = new DOMDocument();
			$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

			$tables = $doc->getElementsByTagName('table');
		  
				foreach ($tables as $table) {
					$tid= $table->getAttribute("id");
					if(file_exists("temp/#".$tid."_".session_id().".txt"))
                        { 
						    $content=file_get_contents("temp/#".$tid."_".session_id().".txt");
							//cleaning content
							$content=cleanContent($content,"input");
							$content=cleanContent($content,"select");
							
							$content=str_replace("on>click=","",$content);
							//$content=cleanContent($content,"a");

							//end of cleaning content
							$table->nodeValue=$content;
							$html=html_entity_decode($doc->saveHTML()); 
						    chmod("temp/#".$tid."_".session_id().".txt",0755);	
							//unlink tmp table file
							unlink("temp/#".$tid."_".session_id().".txt");
						}
                }
			
	}
}

function DOMinnerHTML(DOMNode $element) 
{ 
    $innerHTML = ""; 
    $children  = $element->childNodes;

    foreach ($children as $child) 
    { 
	    
        $innerHTML .= $element->ownerDocument->saveHTML($child);
    }

    return $innerHTML; 
} 

function cleanContent($content,$tag){
	$doc = new DOMDocument();
		
	$doc->loadHTML($content);
$list = $doc->getElementsByTagName($tag);

while ($list->length > 0) {
    $p = $list->item(0);
    $p->parentNode->removeChild($p);
}
  return $doc->saveHTML();
	
	}

