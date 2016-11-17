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
		global $prefs;
		if (!extension_loaded('curl')) {
			TikiLib::lib('reporterror')->report(tra('mPDF: CURL PHP extension not available'));
			return '';
		}
		
      if($parsedData!='')
	      $html=$parsedData;
	  
       //getting n replacing images
	   $tempImgArr=array();
	   $this->_parseHTML($html);
	
	   $this->_getImages($html,$tempImgArr);
		self::setupMPDFCacheLocation();
		if (!class_exists('mPDF')){
			include_once($this->location . 'mpdf.php');
		}
		//checking preferences 
		$orientation=$prefs['print_pdf_mpdf_orientation']!=''?$prefs['print_pdf_mpdf_orientation']:'P';
		$pageSize=$prefs['print_pdf_mpdf_size']!=''?$prefs['print_pdf_mpdf_size']:'A4';
		$marginLeft=$prefs['print_pdf_mpdf_margin_left']!=''?$prefs['print_pdf_mpdf_margin_left']:'10';
		$marginRight=$prefs['print_pdf_mpdf_margin_right']!=''?$prefs['print_pdf_mpdf_margin_right']:'10';
		$marginTop=$prefs['print_pdf_mpdf_margin_top']!=''?$prefs['print_pdf_mpdf_margin_top']:'10';
		$marginBottom=$prefs['print_pdf_mpdf_margin_bottom']!=''?$prefs['print_pdf_mpdf_margin_bottom']:'10';
		$marginHeader=$prefs['print_pdf_mpdf_margin_header']!=''?$prefs['print_pdf_mpdf_margin_header']:'5';
		$marginFooter=$prefs['print_pdf_mpdf_margin_footer']!=''?$prefs['print_pdf_mpdf_margin_footer']:'5';
		
		$mpdf=new mPDF('utf-8',$pageSize,'','',$marginLeft,$marginRight , $marginTop , $marginBottom , $marginHeader , $marginFooter ,$orientation);
	    
		//setting header and footer
		if($prefs['print_pdf_mpdf_header'])
	      $mpdf->SetHeader($prefs['print_pdf_mpdf_header']);
        if($prefs['print_pdf_mpdf_footer'])
		$mpdf->SetFooter($prefs['print_pdf_mpdf_footer']);
		
		//password protection
		if($prefs['print_pdf_mpdf_password'])
		   $mpdf->SetProtection(array(), 'UserPassword', $prefs['print_pdf_mpdf_password']);
		   
		
		
		$mpdf->CSSselectMedia = 'print';				// assuming you used this in the document header

		//getting main theme css
		$themeLib = TikiLib::lib('theme');
        $themecss=$themeLib->get_theme_path($prefs['theme'], '', $prefs['theme'] . '.css');
		$stylesheet1 = file_get_contents('themes/base_files/css/tiki_base.css'); // external css
        $stylesheet2 = file_get_contents($themecss); // external css

        $mpdf->WriteHTML('<style>'.$stylesheet1.$stylesheet2.$this->bootstrapReplace().'</style>'.$html."");
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
	
	function file_get_contents_by_fget($url)
    {
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
			   //end of cleaning content
			   $table->nodeValue=$content;
			   $html=html_entity_decode($doc->saveHTML()); 
			   chmod("temp/#".$tid."_".session_id().".txt",0755);	
			   //unlink tmp table file
			   unlink("temp/#".$tid."_".session_id().".txt");
			}
        }
			
	 }
	 
	 function bootstrapReplace(){
	    return ".col-xs-12 {width: 90%;}.col-xs-11 {width: 81.66666667%;}.col-xs-10 {width: 72%;}.col-xs-9 {width: 64%;}.col-xs-8 {width: 57%;}.col-xs-7 {width: 49%;}.col-xs-6 {width: 42%;}.col-xs-5 {width: 35%;}.col-xs-4 {width: 28%;}.col-xs-3{width: 20%;}.col-xs-2 {width: 12.2%;}.col-xs-1 {width: 3.92%;}'@page,body.print* {background:#fff;color:#000;} p,.print{color:#000;} .editplugin{display:none;visibility:hidden}'";	 
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

