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
		$this->error = false;

		if ( $prefs['print_pdf_from_url'] == 'webkit' ) {
			$path = $prefs['print_pdf_webkit_path'];
			if (!empty($path) && is_executable($path)) {
				$this->mode = 'webkit';
				$this->location = $path;
			} else {
				if (!empty($path)) {
					$this->error = tr('PDF webkit path "%0" not found.', $path);
				} else {
					$this->error = tr('The PDF webkit path has not been set.');
				}
			}
		} else if ($prefs['print_pdf_from_url'] == 'weasyprint') {
			$path = $prefs['print_pdf_weasyprint_path'];
			if (!empty($path) && is_executable($path)) {
				$this->mode = 'weasyprint';
				$this->location = $path;
			} else {
				if (!empty($path)) {
					$this->error = tr('PDF WeasyPrint path "%0" not found.', $path);
				} else {
					$this->error = tr('The PDF WeasyPrint path has not been set.');
				}
			}
		} elseif ( $prefs['print_pdf_from_url'] == 'webservice' ) {
			$path = $prefs['path'];
			if ( ! empty($path) ) {
				$this->mode = 'webservice';
				$this->location = $path;
			} else {
				if (!empty($path)) {
					$this->error = tr('PDF webservice URL "%0" not found.', $path);
				} else {
					$this->error = tr('The PDF webservice URL has not been set.');
				}
			}
		} elseif ( $prefs['print_pdf_from_url'] == 'mpdf' ) {
			$path = $prefs['print_pdf_mpdf_path'];
			if (substr($path, -1) !== '/') {
				$path .= '/';
			}
			if ( ! empty($path) && is_readable($path) && file_exists($path . 'mpdf.php')) {
				self::setupMPDFCacheLocation();

				//setting up dir for custom fonts and mpdf default fonts
				define('_MPDF_TTFONTPATH',TIKI_PATH.'/lib/pdf/fontdata/fontttf/');
		        define('_MPDF_SYSTEM_TTFONTS', $path. '/ttfonts/');


				if (!class_exists('mPDF')){
					include_once($path . 'mpdf.php');
				}
				if (! is_writable(_MPDF_TEMP_PATH) ||! is_writable(_MPDF_TTFONTDATAPATH)) {
					$this->error = tr('mPDF "%0" and "%1" directories must be writable', 'tmp',
						'ttfontdata');
				} else {
					$this->mode = 'mpdf';
					$this->location = $path;
				}
			} else {
				if (!empty($path)) {
					$this->error = tr('mPDF not found in path "%0"', $path);
				} else {
					$this->error = tr('The mPDF path has not been set.');
				}
			}
		}
		if ($this->error) {
			$this->error = tr('PDF generation failed.') . ' ' . $this->error . ' '
				. tr('This is set by the administrator (search for "print" in the settings control panels to locate the setting).');
		}
	}

    /**
     * @param $file
     * @param array $params
     * @return mixed
     */
    function getPdf( $file, array $params, $pdata='' )
	{
		return TikiLib::lib('tiki')->allocate_extra(
			'tracker_print_pdf_items',
			function () use ( $file, $params, $pdata ) {
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
				if (is_array($params['printpages']) || is_array($params['printstructures'])) {
					if (is_array($params['printpages'])) { 
						$params['printpages'] = implode('&', $params['printpages']);
					}
					else  { 
						$params['printpages'] = implode('&', $params['printstructures']);
					}
					//getting parsed data
					foreach($params['pages'] as $page) { 
						$pdata.= $page['parsed'];
					}
				}
				$url = $base_url . $file . '?' . http_build_query($params, '', '&');
				$session_params = session_get_cookie_params();
				return $this->{$this->mode}( $url,$pdata,$params);
			}
		);
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
		$filename = 'temp/public/out'.mt_rand().'.pdf';

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
		$filename = 'temp/public/out'.mt_rand().'.pdf';

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
	private function mpdf($url,$parsedData='',$params=array())
	{
		global $prefs;

      if($parsedData!='')
	      $html=$parsedData;

       //getting n replacing images
		$tempImgArr=array();
		$wikilib = TikiLib::lib('wiki');
		//checking and getting plugin_pdf parameters if set
		$pdfSettings=$this->getPDFSettings($html,$prefs,$params);
		//Add page title with content enabled in prefs and page indiviual settings
		if(($prefs['feature_page_title']=='y' && $wikilib->get_page_hide_title($params['page'])==0 && $pdfSettings['pagetitle']!='n') || $pdfSettings['pagetitle']=='y'){
			$html='<h1>'.$params['page'].'</h1>'.$html;
		}
		
		if($pdfSettings['toc']=='y'){  	//checking toc
		   //checking links
		   if($pdfSettings['toclinks']=='y'){
		    $links="links=\"1\"";
		   }
		   //checking toc heading
		   if($pdfSettings['tocheading']){
		    $tocpreHTML=htmlspecialchars("<h1>".$pdfSettings['tocheading']."</h1>", ENT_QUOTES);
		   }
		    $html="<html><tocpagebreak ".$links." toc-preHTML=\"".$tocpreHTML."\" toc-resetpagenum=\"1\" toc-suppress=\"on\" />".$html."</html>";
		}	
       $this->_getImages($html,$tempImgArr);
       
	   $this->_parseHTML($html);
	   self::setupMPDFCacheLocation();
		if (!class_exists('mPDF')){
	    	include_once($this->location . 'mpdf.php');
		}
		

	  	$mpdf=new mPDF('utf-8',$pdfSettings['pagesize'],'','',$pdfSettings['margin_left'],$pdfSettings['margin_right'] , $pdfSettings['margin_top'] , $pdfSettings['margin_bottom'] , $pdfSettings['margin_header'] , $pdfSettings['margin_footer'] ,$pdfSettings['orientation']);

		//custom fonts add, currently fontawesome support is added, more fonts can be added in future
		$custom_fontdata = array(
		 'fontawesome'=>array(
            'R' => "fontawesome.ttf",
            'I' => "fontawesome.ttf",
        ));

		//calling function to add custom fonts
		add_custom_font_to_mpdf($mpdf, $custom_fontdata);

		//for Cantonese support
	    $mpdf->autoScriptToLang = true;
		$mpdf->autoLangToFont = true;

		$mpdf->SetTitle($params['page']);
		
		//toc levels
		$mpdf->h2toc = $pdfSettings['toclevels'];
		//password protection
		if($pdfSettings['print_pdf_mpdf_password'])
		   $mpdf->SetProtection(array(), 'UserPassword', $pdfSettings['print_pdf_mpdf_password']);

		$mpdf->CSSselectMedia = 'print';				// assuming you used this in the document header

		//getting main base css file
		$basecss = file_get_contents('themes/base_files/css/tiki_base.css'); // external css

		//getting theme css
		$themeLib = TikiLib::lib('theme');
        $themecss=$themeLib->get_theme_path($prefs['theme'], '', $prefs['theme'] . '.css');
		$themecss = file_get_contents($themecss); // external css

		//checking if print friendly option is enabled, then attach print css otherwise theme styles will be retained by theme css
		if($pdfSettings['print_pdf_mpdf_printfriendly']=='y') {
			 $printcss = file_get_contents('themes/base_files/css/printpdf.css'); // external css
		}
		else
		{//preserving theme styles by removing media print styles to print what is shown on screen
		  $themecss=str_replace(array("media print","color : fff"),array("media p","color : #fff"),$themecss);
		  $basecss=str_replace(array("media print","color : fff"),array("media p","color : #fff"),$basecss);
		}
		
		$pdfPages=$this->getPDFPages($html,$pdfSettings);
		$ind=1;
		foreach($pdfPages as $pdfPage) {
			$bgColor='';$backgroundImage='';
			$pdfPage['orientation']=="L"?$orientation='landscape':$orientation='portrait';
			if($pdfPage['background_image']!='') {
				$backgroundImage='background-image:url(\''.$pdfPage['background_image'].'\');background-repeat:no-repeat;background-position:center center; ';
			}
			if($pdfPage['background']!='') {
				$bgColor='background-color:'.$pdfPage['background'].';  ';
			}
			$pageCSS.='@page page'.$ind.'{size:'.$orientation.';'.$backgroundImage.$bgColor.'} ';
			$ind++;
		}
		
		$cssStyles=str_replace(array(".tiki","opacity: 0;"),array("","fill: #fff;opacity:0.3;stroke:black"),'<style>'.$basecss.$themecss.$printcss.$pageCSS.$this->bootstrapReplace().'</style>'); //adding css styles with first page content
		//cover page checking
		if($pdfSettings['coverpage_text_settings']!='' || ($pdfSettings['coverpage_image_settings']!='' && $pdfSettings['coverpage_image_settings']!='off')) {
			$coverPage=explode("|",$pdfSettings['coverpage_text_settings']);
			$coverImage=$pdfSettings['coverpage_image_settings']!='off'?$pdfSettings['coverpage_image_settings']:'';
			$mpdf->SetHeader();		//resetting header footer for cover page	
			$mpdf->SetFooter();
			$mpdf->AddPage($pdfPage['orientation'],'','','','',0,0 , 0, 0, 0, 0); //adding new page with 0 margins
			$coverPage[2]=$coverPage[2]==''?'center':$coverPage[2];
			//getting border settings
			if(count($coverPage)>5) { 
				$borderWidth=$coverPage[5]==''?1:$coverPage[5];
				$coverPageTextStyles='border:'.$borderWidth.' solid '.$coverPage[6].';';
			}	
			$bgColor=$cover[3]==''?'background-color:'.$coverPage[3]:'';
			$mpdf->WriteHTML('<body style="'.$bgColor.';margin:0px;padding:0px"><div style="height:100%;background-image:url('.$coverImage.');padding:20px;background-repeat: no-repeat;background-position: center; "><div style="'.$coverPageTextStyles.'height:95%;">
<div style="text-align:'.$coverPage[2].';margin-top:30%;color:'.$coverPage[4].'"><div style=margin-bottom:10px;font-size:50px>'.$coverPage[0].'</div>'.$coverPage[1].'</div></div></body>');

		}	
		$pageNo=1;
		//end of coverpage generation			
		foreach($pdfPages as $pdfPage){
		 if(strip_tags(trim($pdfPage['pageContent']))!=''){	
		//checking header and footer
			if($pdfPage['header']=="off") {
				$header="";
			}
			else{
				$pdfPage['header']==''?$header=$pdfSettings['header']:$header=$pdfPage['header'];
			}
			if($pdfPage['footer']=="off") {
				$footer="";
			}
			elseif($pdfPage['footer']){
				$footer=$pdfPage['footer'];	
			}
			
			$mpdf->SetHeader(str_ireplace("{PAGETITLE}",$params['page'],$header));
			$mpdf->AddPage($pdfPage['orientation'],'','','','',$pdfPage['margin_left'],$pdfPage['margin_right'] , $pdfPage['margin_top'] , $pdfPage['margin_bottom'] , $pdfPage['margin_header'] , $pdfPage['margin_footer'],'','','','','','','','','page'.$pageNo,$pdfPage['pagesize']);
			$mpdf->SetFooter(str_ireplace("{PAGETITLE}",$params['page'],$footer)); //footer needs to be reset after page content is added
		
			//checking watermark on page
			$mpdf->SetWatermarkText($pdfPage['watermark']);
			$mpdf->showWatermarkText = true;
			$mpdf->SetWatermarkImage($pdfPage['watermark_image'], 0.15, '');
			$mpdf->showWatermarkImage = true;
			//hyperlink check
			if($pdfPage['hyperlinks']!="") {
				$pdfPage['pageContent']=$this->processHyperlinks($pdfPage['pageContent'],$pdfPage['hyperlinks'],$pageCounter++);
			}
			if($pdfPage['columns']>1) {
				$mpdf->SetColumns($pdfPage['columns'],'justify');
			}
			else {
				$mpdf->SetColumns(1,'justify');
			}
			$backgroundImage='';$bgColor="";
			if($pdfPage['background']!='') {
				$bgColor="background-color:".$pdfPage['background'];
			}	
			
			$mpdf->WriteHTML('<html><body style="'.$bgColor.';margin:0px;padding:0px;">'.$cssStyles.$pdfPage['pageContent'].'</body></html>');
			$pageNo++;
			$cssStyles=''; //set to blank after added with first page
		 }
		}
		$mpdf->setWatermarkText($pdfSettings['watermark']);
		$mpdf->SetWatermarkImage($pdfSettings['watermark_image'], 0.15, '');
		//resetting header,footer and watermark to blank 
		$mpdf->SetHeader($pdfSettings['header']);
		$mpdf->SetFooter($pdfSettings['footer']);
	    $this->clearTempImg($tempImgArr);
		return $mpdf->Output('', 'S');					// Return as a string
	}
	
	function getPDFSettings($html,$prefs,$params)
	{
		$pdfSettings=array();
		
		//checking if pdf plugin is set and passed
		$doc = new DOMDocument();
			@$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

			$pdf = $doc->getElementsByTagName('pdfsettings')->item(0);
			$prefs['print_pdf_mpdf_pagesize']=$prefs['print_pdf_mpdf_size'];
			if($pdf)
			{ 	if ($pdf->hasAttributes()) {
					foreach ($pdf->attributes as $attr) {
					//overridding global settings
						$prefs['print_pdf_mpdf_'.$attr->nodeName]=$attr->nodeValue;
					}
				}
			}
		//checking preferences
		$pdfSettings['orientation']=$prefs['print_pdf_mpdf_orientation']!=''?$prefs['print_pdf_mpdf_orientation']:'P';
		$pdfSettings['pagesize']=$prefs['print_pdf_mpdf_pagesize']!=''?$prefs['print_pdf_mpdf_pagesize']:'Letter';
		//custom size needs to be passed for Tabloid
		if($prefs['print_pdf_mpdf_size']=="Tabloid")
		  $pdfSettings['pagesize']=array(279,432);
		elseif($pdfSettings['orientation']=='L')
		  $pdfSettings['pagesize']=$pdfSettings['pagesize'].'-'.$pdfSettings['orientation'];

		$pdfSettings['margin_left']=$prefs['print_pdf_mpdf_margin_left']!=''?$prefs['print_pdf_mpdf_margin_left']:'10';
		$pdfSettings['margin_right']=$prefs['print_pdf_mpdf_margin_right']!=''?$prefs['print_pdf_mpdf_margin_right']:'10';
		$pdfSettings['margin_top']=$prefs['print_pdf_mpdf_margin_top']!=''?$prefs['print_pdf_mpdf_margin_top']:'10';
		$pdfSettings['margin_bottom']=$prefs['print_pdf_mpdf_margin_bottom']!=''?$prefs['print_pdf_mpdf_margin_bottom']:'10';
		$pdfSettings['margin_header']=$prefs['print_pdf_mpdf_margin_header']!=''?$prefs['print_pdf_mpdf_margin_header']:'5';
		$pdfSettings['margin_footer']=$prefs['print_pdf_mpdf_margin_footer']!=''?$prefs['print_pdf_mpdf_margin_footer']:'5';
        $pdfSettings['header']=str_ireplace("{PAGETITLE}",$params['page'],$prefs['print_pdf_mpdf_header']);
		$pdfSettings['footer']=str_ireplace("{PAGETITLE}",$params['page'],$prefs['print_pdf_mpdf_footer']);
		$pdfSettings['print_pdf_mpdf_password']=$prefs['print_pdf_mpdf_password'];
		$pdfSettings['toc']=$prefs['print_pdf_mpdf_toc']!=''?$prefs['print_pdf_mpdf_toc']:'n';
		$pdfSettings['toclinks']=$prefs['print_pdf_mpdf_toclinks']!=''?$prefs['print_pdf_mpdf_toclinks']:'n';
		$pdfSettings['tocheading']=$prefs['print_pdf_mpdf_tocheading'];
		$pdfSettings['pagetitle']=$prefs['print_pdf_mpdf_pagetitle'];
		$pdfSettings['watermark']=$prefs['print_pdf_mpdf_watermark'];
		$pdfSettings['watermark_image']=$prefs['print_pdf_mpdf_watermark_image'];
		$pdfSettings['coverpage_text_settings']=str_ireplace("{PAGETITLE}",$params['page'],$prefs['print_pdf_mpdf_coverpage_text_settings']);
		$pdfSettings['coverpage_image_settings']=str_ireplace("{PAGETITLE}",$params['page'],$prefs['print_pdf_mpdf_coverpage_image_settings']);
		$pdfSettings['hyperlinks']=$prefs['print_pdf_mpdf_hyperlinks'];
		$pdfSettings['columns']=$prefs['print_pdf_mpdf_columns'];				
		$pdfSettings['background']=$prefs['print_pdf_mpdf_background'];				
		$pdfSettings['background_image']=$prefs['print_pdf_mpdf_background_image'];				

		if($pdfSettings['toc']=='y'){
			//toc levels
			array('H1'=>0, 'H2'=>1, 'H3'=>2);
			$toclevels=$prefs['print_pdf_mpdf_toclevels']!=''?$prefs['print_pdf_mpdf_toclevels']:'H1|H2|H3';
			$toclevels=explode("|",$toclevels);
			$pdfSettings['toclevels']=array();
			for($toclevel=0;$toclevel<count($toclevels);$toclevel++){
				$pdfSettings['toclevels'][$toclevels[$toclevel]]=$toclevel;
			}
		}
		//PDF settings
		return $pdfSettings;
	}
	
	//mpdf read page for plugin PDFPage, introducted for advanced pdf creation
	function getPDFPages($html,$pdfSettings){
		//checking if pdf page tag exists
		$doc = new DOMDocument();
		$doc->loadHTML($html);
		$xpath = new DOMXpath($doc);
		//Getting pdf page custom pages from content
		$pdfPages = $doc->getElementsByTagName('pdfpage');
		$pageData=array();
		$mainContent=$html;
		foreach ($pdfPages as $page) {
			$pages=array();
			$pageTag="<pdfpage";
			if ($page->hasAttributes()) {
				foreach ($page->attributes as $attr) {
					$pages[$attr->nodeName]=$attr->nodeValue;
						$pageTag.= " ".$attr->nodeName."=\"".htmlentities($attr->nodeValue)."\"";
				}
			}
			$pageTag.=">";
		//mapping empty values with defaults
		foreach($pdfSettings as $setting=>$value) {
			if($pages[$setting]=="") {
				$pages[$setting]=$value;
			}
		}
		
		if($pages['pagesize']=="Tabloid") {
			$pages['pagesize']=array(279,432); }
		elseif($pages['orientation']=='L') {
			$pages['pagesize']=$pages['pagesize'].'-'.$pages['orientation']; }
			//dividing content in segments
			$ppages=explode($pageTag,$mainContent,2);
			$lpages=explode("</pdfpage>",$ppages[1],2);
			
			//for prepage settings pdfsettings will be used
			if($ppages[0]!="") {
				$prePage=$pdfSettings;
				$prePage['pageContent']=$ppages[0];
				$pageData[]=$prePage;
				
			}
			$pages['pageContent']= $doc->saveXML($page);
			$pageData[]=$pages;
			if(trim(strip_tags($lpages[1]))!="") {
				$mainContent=$lpages[1];
				
			}	
		}
		//no pages found
		if(count($pageData)==0)  {
			$defaultPage=$pdfSettings;
			$defaultPage['pageContent']=$html;
			$pageData[]=$defaultPage;
		}
		elseif(trim(strip_tags($lpages[1]))!='') { //adding and resetting options for last page if any
			$lastPage=$pdfSettings;
			$lastPage['pageContent']=$lpages[1];
			$pageData[]=$lastPage;
		}
		return $pageData;
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
	   $newFile='temp/pdfimg/pdfimg'.mt_rand(9999,999999).'.png';
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
	   $doc = new DOMDocument();
       $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
	   
	   $tables = $doc->getElementsByTagName('table');
       $tempValue=array();
	   $sortedContent=array();
       foreach ($tables as $table) {
		    $this->sortContent($table,$tempValue,$sortedContent,$table->tagName);
		}
		$xpath = new DOMXpath($doc);
		
		//defining array of plugins to be sorted
		$pluginArr=array(array("class","customsearch_results","div"),array("id","container_pivottable","div"),array("class","dynavar","a"));
		$tagsArr=array(array("input","tablesorter-filter","class"),array("select","tablesorter-filter","class"),array("select","pvtRenderer","class"),array("select","pvtAggregator","class"),array("td","pvtCols","class"),array("td","pvtUnused","class"),array("td","pvtRows","class"),array("div","plot-container","class"),array("a","heading-link","class"),array("a","tablename","class","1"));

		foreach($pluginArr as $pluginInfo)
		{
           $customdivs = $xpath->query('//*[contains(@'.$pluginInfo[0].', "'.$pluginInfo[1].'")]');
	       for ($i = 0; $i < $customdivs->length; $i++) {
			if($pluginInfo[1]=="dynavar") {
				$dynId=str_replace("display","edit",$customdivs->item($i)->parentNode->getAttribute('id'));
				$tagsArr[]=array("span",$dynId,"id");
			}
			else{
				$customdiv = $customdivs->item($i);
				$this->sortContent($customdiv,$tempValue,$sortedContent,$pluginInfo[2]);
			}
	       }
		}
        $html=@$doc->saveHTML();
		//replacing temp table with sorted content
			for($i=0;$i<count($sortedContent);$i++)
			{
			    $html=str_replace($tempValue[$i],$sortedContent[$i],$html);
		    }
			$html=cleanContent($html,$tagsArr);

			//making tablesorter and pivottable charts wrapper divs visible
		$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		$xpath = new DOMXpath($doc);
		$wrapperDefs=array(array("class","ts-wrapperdiv","visibility:visible"),array("id","png_container_pivottable","display:''"));
		foreach($wrapperDefs as $wrapperDef)
		{  $wrapperdivs = $xpath->query('//*[contains(@'.$wrapperDef[0].', "'.$wrapperDef[1].'")]');
		   for ($i = 0; $i < $wrapperdivs->length; $i++) {
			   $wrapperdiv = $wrapperdivs->item($i);
        	   $wrapperdiv->setAttribute("style",$wrapperDef[2]);
		   }
        }
		$html=@$doc->saveHTML();
		//font awesome support call
		$this->fontawesome($html);
		//& sign added in fa unicodes for proper printing in pdf
        $html=str_replace('#x',"&#x",$html); 

	 }
	 
	 function fontawesome(&$html)
	 {
	   $doc = new DOMDocument();
       $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
	   $xpath = new DOMXpath($doc);
      //font awesome code insertion
	   $fadivs = $xpath->query('//*[contains(@class, "fa")]');
	   //loading json file if there is any font-awesome tag in html
	    if($fadivs->length)
		{
		 $faCodes=file_get_contents('lib/pdf/fontdata/fa-codes.json');
		 $jfo = json_decode($faCodes,true);
         for ($i = 0; $i < $fadivs->length; $i++) {
               $fadiv = $fadivs->item($i);
               $faClass=explode(" ",str_replace(array("fa ","-"),"",$fadiv->getAttribute('class')));
			   foreach($faClass as $class)
			   {
				   if($jfo[$class][codeValue])
				   {
			           $faCode=$doc->createElement('span'," ".$jfo[$class][codeValue]);
					   $faCode->setAttribute("style","font-family: FontAwesome;float:left;padding-left:5px".$fadiv->getAttribute('style'));
					   //span with fontawesome code inserted before fa div
					   $faCode->setAttribute("class",$fadiv->getAttribute('class'));
					   $fadiv->parentNode->insertBefore($faCode,$fadiv);
					   $fadiv->parentNode->removeChild($fadiv);
				   }
			   }
			 
         }
		}

       $html=@$doc->saveHTML();
     }

	 function bootstrapReplace(){
	    return ".col-xs-12 {width: 100%;}.col-xs-11 {width: 81.66666667%;}.col-xs-10 {width: 72%;}.col-xs-9 {width: 64%;}.col-xs-8 {width: 62%;}.col-xs-7 {width: 49%;}.col-xs-6 {width: 45.7%;}.col-xs-5 {width: 35%;}.col-xs-4 {width: 28%;}.col-xs-3{width: 20%;}.col-xs-2 {width: 12.2%;}.col-xs-1 {width: 3.92%;}    .table-striped {border:1px solid #ccc;} .table-striped td { padding: 8px; line-height: 1.42857143;vertical-align: center;border-top: 1px solid #ccc;} .table-striped th { padding: 10px; line-height: 1.42857143;vertical-align: center;   } .table-striped .odd {padding:10px;} .table-striped .even {padding:10px;}.trackerfilter form{display:none;} table.pvtTable tr td {border:1px solid}.wp-sign{position:relative;display:block;background-color:#fff;color:#666;font-size:10px} .wp-sign a,.wp-sign a:visited{color:#999} .icon-link-external{margin-left:10px;font-size:10px}";
	}
	
	function sortContent(&$table,&$tempValue,&$sortedContent,$tag)
	{
	   $content='';
	   $tid= $table->getAttribute("id");
	   
	     
		   if(file_exists("temp/#".$tid."_".session_id().".txt"))
           {
			   $content=file_get_contents("temp/#".$tid."_".session_id().".txt");
			   //formating content
			   $tableTag="<".$tag;
			      if ($table->hasAttributes()) {
                       foreach ($table->attributes as $attr) {
                            $tableTag.=" ".$attr->nodeName."=\"".$attr->nodeValue."\"";
	                   }
                  }
			   $tableTag.=">";
               $content=$tableTag.$content.'</'.$tag.'>';
			   //end of cleaning content
			   $sortedContent[]=str_replace('<sc<x>ript type="text/javascript">
<!--//--><![CDATA[//><!--
$(document).ready(function(){
// jq_onready 0 
$(".convert-mailto").removeClass("convert-mailto").each(function () {
				var address = $(this).data("encode-name") + "@" + $(this).data("encode-domain");
				$(this).attr("href", "mailto:" + address).text(address);
			});
});
//--><!]]>
</script>',"",$content);
			   $tempValue[]=$tableTag;
			   $table->nodeValue="";
			   chmod("temp/#".$tid."_".session_id().".txt",0755);
			   //unlink tmp table file
			   unlink("temp/#".$tid."_".session_id().".txt");
			}
		}

		function processHyperlinks($content,$hyperlinkSetting,$pageCounter){
			$doc = new DOMDocument();
			$doc->loadHTML($content);
			$anchors = $doc->getElementsByTagName('a');
			$len = $anchors->length;
			$href='';
			$hrefDiv=$doc->createElement('div');

			for($i=0,$linkCnt=1; $i<$len; $i++) {
    			$anchor = $anchors->item(0);
    			$link = $doc->createElement('span', $anchor->nodeValue);
				$link->setAttribute('class', $anchor->getAttribute('class'));
				
				//checking if links to be added as footnote
				if($hyperlinkSetting!="off") {
					// Check if there is a url in the text
					$linkSup=$doc->createElement("sup");
					if(preg_match("/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/", $anchor->getAttribute('href'), $url)) {					
						
						$linkAn=$doc->createElement("anchor","[".$linkCnt."]");
						$linkAn->setAttribute("href","#".$pageCounter."lnk".$linkCnt);
						$linkSup->appendChild($linkAn);
						$link->appendChild($linkSup);	
						$hrefData=$doc->createElement("a",$anchor->getAttribute('href'));
						$hrefData->setAttribute("name",$pageCounter."lnk".$linkCnt);
						$hrefDiv->setAttribute("style","border-top:1px solid #ccc;line-height:1.2em");
						$hrefDiv->appendChild($doc->createElement("sup","&nbsp;[".$linkCnt."]&nbsp;"));
						$hrefDiv->appendChild($hrefData);
						$hrefDiv->appendChild($doc->createElement("br"));
						$linkCnt++;
					}
				}
				$anchor->parentNode->replaceChild($link, $anchor);
				
				
			}

			$hrefDiv->setAttribute('class',"footnotearea");
			$doc->getElementsByTagName('body')->item(0)->appendChild($hrefDiv);
			$content=$doc->saveHTML();
			return str_replace("anchor","a",$content);
			
		}// End of processHyperlinks
} //END OF PDF CLASS


function cleanContent($content,$tagArr){
	$doc = new DOMDocument();
	$doc->loadHTML($content);
	$xpath = new DOMXpath($doc);
	   
	foreach($tagArr as $tag)
	{
	  $list = $xpath->query('//'.$tag[0].'[contains(concat(\' \', normalize-space(@'.$tag[2].'), \' \'), "' .$tag[1]. '")]');
      for ($i = 0; $i < $list->length; $i++) {
          $p = $list->item($i);
		  if($tag[3]==1){ //the parameter checks if content of tag has to be preserved
				$attributes = $p->attributes;
				while ($attributes->length) {
				//preserving href
				
				if($attributes->item(0)->name=="href") {
					$hrefValue= $attributes->item(0)->value;
				}
					$p->removeAttribute($attributes->item(0)->name);
				}
				if($hrefValue) {					 
					$p->setAttribute("href",$hrefValue);
				}
			}
		  else
	      	$p->parentNode->removeChild($p);
       }
	}
    return $doc->saveHTML();
}

function add_custom_font_to_mpdf(&$mpdf, $fonts_list) {
    // Logic from line 1146 mpdf.pdf - $this->available_unifonts = array()...
    foreach ($fonts_list as $f => $fs) {
        // add to fontdata array
        $mpdf->fontdata[$f] = $fs;

        // add to available fonts array
        if (isset($fs['R']) && $fs['R']) { $mpdf->available_unifonts[] = $f; }
        if (isset($fs['B']) && $fs['B']) { $mpdf->available_unifonts[] = $f.'B'; }
        if (isset($fs['I']) && $fs['I']) { $mpdf->available_unifonts[] = $f.'I'; }
        if (isset($fs['BI']) && $fs['BI']) { $mpdf->available_unifonts[] = $f.'BI'; }
    }
    $mpdf->default_available_fonts = $mpdf->available_unifonts;
}

