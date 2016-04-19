<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}


/**
 * Add Javascript and CSS to output
 * Javascript and CSS can be added:
 * - as files (filename including relative path to tikiroot)
 * - as scripts (string)
 * - as a url to load from a cdn (Note: use $tikilib->httpScheme() to build the url. It considers reverse proxies and returns correctly 'http' or 'https') 
 * Note: there are 2 prefs to add additional cdns. one for http and one for https. 
 *  
 * To maintain the order of loading Javascript and to allow minifying, the following "ranks" are supported:
 * '10dynamic': loaded first to allow minification of the other ranks. Usally module and plugin descriptions.
 * '20cdn' : loaded after 'dynamic', no minification possible // main libs like jquery from jquery/google cdn (no user cdns)
 * '30dependancy': loaded after 'cdn', minification possible  // main libs like jquery, codemirror
 * '40external': loaded after 'dependancy', minification possible // custom libs that require main libs
 * '50standard': loaded after 'external', minification possible // standard js that might require main / custom libs
 * '60late': loaded after 'standard', minification possible // page specific js 
 *   Note: this rank is activated in tiki-setup.php to seperate page specific JS from common JS
 *   So any JS loaded after tiki-setup.php that has no rank 'external' is put into 'late'.
 *   If minification is activated for late, any new combination of late files will be created automaically if needed.
 *   When using user specific CDNs AND minification for late is enabled, any possible minified file must be avaliable via that CDN! 
 * 
 * The order of files within each section will be maintained. What adds first will be processed first. 
 *  
 * Note: cdns (google/jquery, not user cdns), files and scripts (strings) willl be handled seperatetly.
 * 
 * To add JS the follwoing methods are available. Note: if $skip_minify == true, this file will not be processed for further minification.
 * This could be used to avoid screwing up the JS file in the rare case minification on that particular file does not work.
 * It will however be concated to one single JS file. 
 * Useful methods to ad JS files:
 * add_jsfile_cdn($url) - add a JS File from a CDN
 * add_jsfile_dependancy($filename, $skip_minify) - add a JS File to the section dependancy
 * add_jsfile_external($filename, $skip_minify) - add a JS File to the section external
 * add_jsfile($filename, $skip_minify) - add a JS File to the section standard 
 * add_jsfile_late($filename, $skip_minify) - add a JS File to the section late

 * 
 * These functions allow to add JS as scripts/strings. No minification on them:
 * add_js($script, $rank) - add JS as string
 * add_jq_onready($script, $rank) - add JS as string and add to the onready event
 * add_js_config($script, $rank) - add JS that usally represents a config object
 * 
 * @TODO CSS handling
 * 
 */
class HeaderLib
{
	public $title;
	
	/**
	 * Array of js files arrays or js urls arrays to load
	 * key = rank, value = array of filenames with relative path or urls
	 * Some ranks have special meanings: (note ranks are array keys in the same array)
	 * @var array()
	 */
	public $jsfiles;
	

	/**
	 * Array of js files that are already minified or should not be minified
	 * Filled when adding jsfiles and setting the $skip_minify param to true
	 * key = filename with relative path
	 * @var array
	 */
	public $skip_minify;
	
	
	/**
	 * Array of JS scripts arrays as strings to load
	 * key = rank (load order), value = array of scripts.
	 * js[$rank][] = $script;
	 * @var array
	 */
	public $js;
	
	
	/**
	 * Array of JS Scripts arrays as string that act as config
	 * Usally created dynamically 
	 * js_config[$rank][] = $script;
	 * @var array
	 */
	public $js_config;
	
	
	/**
	 * Array of JS Scripts arrays as string that should be called onReady().
	 * Key = rank (load order), value = array of scripts.
	 * jq_onready[$rank][] = $script;
	 * @var array
	 */
	public $jq_onready;
	
	public $cssfiles;
	public $css;
	public $rssfeeds;
	public $metatags;
	
	public $wysiwyg_parsing;
	
	
	/* If set to true, any js added through add_jsfile() that has not rank 'external' will be put to rank 'late' 
	 * Only set once in tiki-setup.php to separate wiki page specific js from common js.
	 * @var boolean
	 */
	public $forceJsRankLate;


	public $jquery_version = '2.2.2';
	public $jqueryui_version = '1.11.4';
	public $jquerymigrate_version = '1.4.0';


	function __construct()
	{
		$smarty = TikiLib::lib('smarty');
		$smarty->assign('headerlib', $this);

		$this->title = '';
		$this->jsfiles = array();
		$this->skip_minify = array();
		$this->js = array();
		$this->js_config = array();
		$this->jq_onready = array();
		$this->cssfiles = array();
		$this->css = array();
		$this->rssfeeds = array();
		$this->metatags = array();

		$this->wysiwyg_parsing = false;
		$this->forceJsRankLate = false;
	}

	
	/**
	 * user cdn and feature multi_cdn see r46854
	 * @param string $file
	 * @param string $rank
	 * @return string $file
	 */
	function convert_cdn( $file, $rank = null )
	{
		global $prefs, $tikiroot;

		// using this method, also reverse proxy / ssl offloading will continue to work
		$httpScheme = Tikilib::httpScheme();
		$https_mode = ($httpScheme == 'https') ? true : false; 

		$cdn_ssl_uri = array_filter(preg_split('/\s+/', $prefs['tiki_cdn_ssl']));
		$cdn_uri = array_filter(preg_split('/\s+/', $prefs['tiki_cdn']));

		if ($https_mode && !empty($cdn_ssl_uri)) {
			$cdn_pref = &$cdn_ssl_uri;
		} elseif (!empty($cdn_uri)) {
			$cdn_pref = &$cdn_uri;
		}

		// feature multi_cdn see r46854 - quote from commit:
		// filename hash is used to select/assign one CDN URI from the list. 
		// It ensure a same file will always point/use the same CDN and ensure proper caching.
		if ( !empty($cdn_pref) && 'http' != substr($file, 0, 4) && $rank !== 'dynamic' ) {
			$index = hexdec(hash("crc32b", $file)) % count($cdn_pref);
			$file = $cdn_pref[$index] . $tikiroot . $file;
		}

		return $file;
	}

	
	function set_title($string)
	{
		$this->title = urlencode($string);
	}

	/**
	 * Add a js url from this tiki instance to top priority load order.
	 * These are usally dynamic created js scripts for configuration, module settings etc.
	 * Urls added here will not be further processed (like minified or put into a single file)
	 * @param string $url - relative url to this tiki instance
	 * @return object $HeaderLib
	 */
	function add_jsfile_dynamic($url)
	{
		$this->add_jsfile_by_rank($url, '10dynamic');
		return $this;
	}
		
	
	/**
	 * Add a js url to top priority load order. That url must be loaded from an external source.
	 * These are usally libraries like jquery that are loaded from a cdn = content delivery network.
 	 * Urls added here will not be further processed (like minified or put into a single file) 
	 * @param string $url - absolute url including http/https
	 * @return object $HeaderLib
	 */
	function add_jsfile_cdn($url)
	{
		$this->add_jsfile_by_rank($url, '20cdn');
		return $this;
	}
	
	
	/**
	 * Add a js file to top priority load order, right after cdns and dynamics. That file must not be loaded from an external source.
	 * Theses are usally libraries like jquery or codemirror, so files where other js file depend on.
	 * Depending on prefs, it could be minified and put into a single js file.
	 * @param string $filename with path relative to tiki dir
	 * @param booloean $skip_minify default = false - true if the file must not be minified
	 * @return object $HeaderLib
	 */
	function add_jsfile_dependancy($file, $skip_minify = false)
	{
		$this->add_jsfile_by_rank($file, '30dependancy', $skip_minify);
		return $this;
	}
	
	
	/**
	 * Add a js file to load after dependancy . That file must not be loaded from an external source.
	 * Theses are usally custom libraries like raphael, gaffle etc.
	 * Depending on prefs, it could be minified and put into a single js file.
	 * @param string $filename with path relative to tiki dir
	 * @param booloean $skip_minify default = false - true if the file must not be minified
	 * @return object $HeaderLib
	 */
	function add_jsfile_external($file, $skip_minify = false)
	{
		$this->add_jsfile_by_rank($file, '40external', $skip_minify);
		return $this;
	}
	

	/**
	 * Adds a js file to load after external. That file must not be loaded from an external source.
	 * Depending on prefs, it could be minified and also put into a single js file
	 * @param string $file -  path relative to tiki dir
	 * @param boolean $skip_minify optional, default = false - true if the file must not be minified
	 * @return object $HeaderLib
	 */
	function add_jsfile($file, $skip_minify=false) {
		$this->add_jsfile_by_rank($file, '50standard', $skip_minify);
		return $this;
	}

	
	/**
	 * Add a js file to load after standard . That file must not be loaded from an external source.
	 * Use this method to add page specific js files. They will be minified separatly. 
	 * @see $this->forceJsRankLate() 
	 * Depending on prefs, it could be minified and put into a single js file.
	 * @param string $filename with path relative to tiki dir
	 * @param booloean $skip_minify default = false - true if the file must not be minified
	 * @return object $HeaderLib
	 */
	function add_jsfile_late($file, $skip_minify = false)
	{
		$this->add_jsfile_by_rank($file, '60late', $skip_minify);
		return $this;
	}	
	
	
	/**
	 * Add a jf file by rank. Do not use this function directly! 
	 * Only reason that it is public, is for access from lib/core/tiki/PageCache.php
	 * @param string $file
	 * @param string $rank
	 * @param boolean $skip_minify
	 * @return object $HeaderLib
	 */
	function add_jsfile_by_rank($file, $rank, $skip_minify = false) {
		// if js is added after tiki-setup.php is run, add those js files to 'late'
		// need to check wether this is really needed
		if ($this->forceJsRankLate == true && $rank !== '40external') {
			$rank = '60late';
		}
		
		if (!$this->wysiwyg_parsing && (empty($this->jsfiles[$rank]) or !in_array($file, $this->jsfiles[$rank]))) {
			$this->jsfiles[$rank][] = $file;
			if ($skip_minify) {
				$this->skip_minify[$file] = $skip_minify;
			}
		}
		return $this;
	}


	/**
	 * Add js that works as config. Usally created dynamically.
	 * @param string $script
	 * @param integer $rank - loadorder optional, default 0
	 * @return object $HeaderLib
	 */
	function add_js_config($script, $rank=0)
	{
		if (!$this->wysiwyg_parsing && (empty($this->js_config[$rank]) or !in_array($script, $this->js_config[$rank]))) {
			$this->js_config[$rank][] = $script;
		}
		return $this;
	}


	/**
	 * JS scripts to add as string 
	 * @param string $script
	 * @param integer $rank loadorder optional, default = 0
	 * @return object $HeaderLib
	 */
	function add_js($script, $rank = 0)
	{
		if (!$this->wysiwyg_parsing && (empty($this->js[$rank]) or !in_array($script, $this->js[$rank]))) {
			$this->js[$rank][] = $script;
		}
		return $this;
	}

	/**
	 * Adds lines or blocks of JQuery JavaScript to $(document).ready handler
	 * @param string $script - Script to execute
	 * @param number $rank - load order (default=0)
	 * @return $object HeaderLib
	 */
	function add_jq_onready($script,$rank=0)
	{
		if (!$this->wysiwyg_parsing && (empty($this->jq_onready[$rank]) or !in_array($script, $this->jq_onready[$rank]))) {
			$this->jq_onready[$rank][] = $script;
		}
		return $this;
	}

	function add_cssfile($file, $rank=0)
	{
		if (empty($this->cssfiles[$rank]) or !in_array($file, $this->cssfiles[$rank])) {
			$this->cssfiles[$rank][] = $file;
		}
		return $this;
	}

	function replace_cssfile($old, $new, $rank)
	{
		foreach ($this->cssfiles[$rank] as $i=>$css) {
			if ($css == $old) {
				$this->cssfiles[$rank][$i] = $new;
				break;
			}
		}
		return $this;
	}

	function drop_cssfile($file)
	{
		$out = array();
		foreach ($this->cssfiles as $rank=>$data) {
			foreach ($data as $f) {
				if ($f != $file) {
					$out[$rank][] = $f;
				}
			}
		}
		$this->cssfiles = $out;
		return $this;
	}

	function add_css($rules,$rank=0)
	{
		if (empty($this->css[$rank]) or !in_array($rules, $this->css[$rank])) {
			$this->css[$rank][] = $rules;
		}
		return $this;
	}

	function add_rssfeed($href,$title,$rank=0)
	{
		if (empty($this->rssfeeds[$rank]) or !in_array($href, array_keys($this->rssfeeds[$rank]))) {
			$this->rssfeeds[$rank][$href] = $title;
		}
		return $this;
	}

	function set_metatags($tag,$value,$rank=0)
	{
		$tag = addslashes($tag);
		$this->metatags[$tag] = $value;
		return $this;
	}

	function output_headers()
	{
		global $style_ie6_css, $style_ie7_css, $style_ie8_css, $style_ie9_css;
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_modifier_escape');

		ksort($this->cssfiles);
		ksort($this->css);
		ksort($this->rssfeeds);

		$back = "\n";
		if ($this->title) {
			$back = '<title>'.smarty_modifier_escape($this->title)."</title>\n\n";
		}

		if (count($this->metatags)) {
			foreach ($this->metatags as $n=>$m) {
				$back.= "<meta name=\"" . smarty_modifier_escape($n) . "\" content=\"" . smarty_modifier_escape($m) . "\" />\n";
			}
			$back.= "\n";
		}

		$back .= $this->output_css_files();

		if (count($this->css)) {
			$back.= "<style type=\"text/css\"><!--\n";
			foreach ($this->css as $x=>$css) {
				$back.= "/* css $x */\n";
				foreach ($css as $c) {
					$back.= "$c\n";
				}
			}
			$back.= "-->\n</style>\n\n";
		}

		// Handle theme's special CSS file for IE8 or IE9 hacks
		$back .= "<!--[if IE 8]>\n"
				.'<link rel="stylesheet" href="themes/base_files/feature_css/ie8.css" type="text/css" />'."\n";
		if ( $style_ie8_css != '' ) {
			$back .= '<link rel="stylesheet" href="'.smarty_modifier_escape($this->convert_cdn($style_ie8_css)).'" type="text/css" />'."\n";
		}
		$back .= "<![endif]-->\n";
		$back .= "<!--[if IE 9]>\n"
				.'<link rel="stylesheet" href="themes/base_files/feature_css/ie9.css" type="text/css" />'."\n";
		if ( $style_ie9_css != '' ) {
			$back .= '<link rel="stylesheet" href="'.smarty_modifier_escape($this->convert_cdn($style_ie9_css)).'" type="text/css" />'."\n";
		}
		$back .= "<![endif]-->\n";

		if (count($this->rssfeeds)) {
			foreach ($this->rssfeeds as $x=>$rssf) {
				$back.= "<!-- rss $x -->\n";
				foreach ($rssf as $rsstitle=>$rssurl) {
					$back.= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"".smarty_modifier_escape($this->convert_cdn($rsstitle))."\" href=\"".smarty_modifier_escape($rssurl)."\" />\n";
				}
			}
			$back.= "\n";
		}
		return $back;
	}


	/**
	 * Force JS Files being added after tiki-setup.php is done to the rank/loadorder 'late' if rank is not 'external'. 
	 * Used to seperate page specific JS Files from the rest.
	 * @return object $HeaderLib
	 */
	public function forceJsRankLate()
	{
		$this->forceJsRankLate = true;
		return $this;
	}


	/**
	 * Gets included JavaScript files (for AJAX)
	 * Used in also lib/wiki/wikilib.php to rebuild the cache if activated
	 * @return array $jsFiles effectivly used jsfiles in scripttags considering minification / cdns if activated.
	 */
	function getJsFilesWithScriptTags()
	{
		/*
		 // MISCONCEPTION: user cdns are supposed to work as entire tiki cdns - not user based additional url sources
		
		 // check for user defined cdns: prefs: tiki_cdn_ssl, tiki_cdn
		 // the current prefs ask for complete urls including the scheme-name (http / https)
		
		 $httpScheme = Tikilib::httpScheme();
		 $cdnType  = ($httpScheme == 'http') ? 'tiki_cdn' : 'tiki_cdn_ssl';
		 if (isset($prefs[$cdnType])) {
		
		 $customCdns = array_filter(preg_split('/\s+/', $prefs[$cdnType]));
		 $rank = 'customCdn';
		 foreach ($customCdns as $entry) {
		 trim($entry);
		 if (!empty($entry)) {
		 $output[$rank] .= "<script type=\"text/javascript\" src=\"".smarty_modifier_escape($entry)."\"></script>\n";
		 }
		 }
		 }
		 */
		
		
		global $prefs;
		if ($prefs['javascript_enabled'] == 'n') {
			return;
		}
		
		if (count($this->jsfiles) == 0) {
			return;
		}
		
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_modifier_escape');
		
		ksort($this->jsfiles);
		$jsfiles = $this->jsfiles;
		
		
		// array that holds a sorted list for all JS files including script tags in the correct order
		$output = array();

		// output dynamic and cdn first - they cannot be minified anyway
		$ranks = array('10dynamic', '20cdn');
		foreach ($ranks as $rank) {
			if (isset($jsfiles[$rank])) {
				foreach ($jsfiles[$rank] as $entry) {
					$output[] = "<script type=\"text/javascript\" src=\"" . smarty_modifier_escape($entry) . "\"></script>\n";
				}
			}
		}
		
		// all other ranks could be minified - minification only happens if activated and if the file was not blocked by $skip_minify
		
		//  check wether we need to minify. minify also includes to put the minified files into one single file
		$minifyActive = isset($prefs['tiki_minify_javascript']) && $prefs['tiki_minify_javascript'] == 'y' ? true : false;
		
		if (!$minifyActive) {
			$ranks = array('30dependancy', '40external', '50standard', '60late');
			foreach ($ranks as $rank) {
				if (isset($jsfiles[$rank])) {
					foreach ($jsfiles[$rank] as $entry) {
						$entry = $this->convert_cdn($entry, $rank);
						$output[] = "<script type=\"text/javascript\" src=\"" . smarty_modifier_escape($entry) . "\"></script>\n";
					}
				}
			}
		} else {
			// minify (each set of ranks will be compressed into one file).
			
			// late stuff can vary by page. if we would include it in main, then we get multiple big js files.
			// better to accept 2 js request: a big one wich rarely changes and small ones that include (page specific) late stuff.
			// at the end we could get rid of this pref though
			
			$ranks = array('30dependancy', '40external', '50standard');
 			$entry =  $this->minifyJSFiles($jsfiles, $ranks);
			$output[] .= "<script type=\"text/javascript\" src=\"".smarty_modifier_escape($entry)."\"></script>\n";

			$minifyLateActive = isset($prefs['tiki_minify_late_js_files']) && $prefs['tiki_minify_late_js_files'] == 'y' ? true : false;
			if ($minifyLateActive) {
				$rank = '60late';
				// handling of user defined cdn servers is done inside minifyJSFiles()
				$entry =  $this->minifyJSFiles($jsfiles, array($rank));
				$output[] .= "<script type=\"text/javascript\" src=\"".smarty_modifier_escape($entry)."\"></script>\n";
			} else {
				foreach ($jsfiles[$rank] as $entry) {
					$output[] = "<script type=\"text/javascript\" src=\"".smarty_modifier_escape($entry)."\"></script>\n";
				}
			}
		}
		
		return $output;
	}
	
	
	/**
	 * Minify multiple JS files over multiple ranks into one single JS file. 
	 * The file is identified by a hash over the given $jsfiles array and automatically created if needed.
	 * @param array $jsfiles array of jsfiles ordered by ranks
	 * @param array $ranks simple array of ranks that needs to be processed. 
	 * @return string $filename - name and relative path of the final js file.
	 */
	private function minifyJSFiles($allJsfiles, $ranks) {
		global $tikidomainslash;
		
		// build hash to identify minified file based on the _requested_ ranks, NOT on the entire jsfiles array
		// $jsfiles contains only those keys defined in $ranks
		$jsfiles = array_intersect_key($allJsfiles, array_flip($ranks));
		$hash = md5(serialize($jsfiles));
		$tempDir = 'temp/public/' . $tikidomainslash;
		$file = $tempDir . "min_main_". $hash. ".js";
		$cdnFile = $this->convert_cdn($file);
		
		// check if we are on a user defined CDN. 
		if ($file != $cdnFile) {
			return $cdnFile;
		}
		
 		if (file_exists($file)) {
			return $file;
 		}

 		// file does not exist - create it
 		require_once 'lib/minify/JSMin.php';
 		$minifiedAll = '';
 		// show all relevant messages about the JS files on top - will be prepended to the output
 		$topMsg = "/**** start overview of included js files *****/\n";
 		foreach ($ranks as $rank) {
		// add list of minfied js files to output
 			$topMsg .= "\n/* list of files for rank:$rank */\n";
			$topMsg .= '/* ' . print_r($jsfiles[$rank], true) . ' */' . "\n";
			foreach ($jsfiles[$rank] as $f) {
				// important - some scripts like vendor/jquery/plugins/async/jquery.async.js do not terminate their last bits with a ';'
				// this is bad practise and that causes issues when putting them all in one file! 
				$minified = ';';
				$msg = '';
				// if the name contains not  'min' and that file is not blacklisted for minification assume it is minified
				// preferable is to set $skip_minify proper
				if (!preg_match('/min\.f$/', $f) && $this->skip_minify[$f] !== true) {
					set_time_limit(600);
					try {
						// to optimize processing time for changed js requirements, cache the minified version of each file
						$hash = md5($f);
						// filename without extension - makes it easier to identify the compressed files if needed.
						$prefix = basename($f, '.js');
						$minifyFile = $tempDir. "min_s_". $prefix. "_". $hash. ".js";
						if (file_exists($minifyFile)) {
							$temp = file_get_contents($minifyFile);
						} else {
							$content = file_get_contents($f);
							$temp = JSMin::minify($content);
							file_put_contents($minifyFile, $temp);
							chmod($file, 0644);
						}
						$msg .= "\n/* rank:$rank - minify:ok. $f */\n";
						$topMsg .= $msg;
						$minified .= $msg;
						$minified .= $temp;
					} catch (JSMinException $e) {
						$content = file_get_contents($f);
						$error = $e->getMessage();
						$msg .= "\n/* rank:$rank - minify:error ($error) - adding raw file. $f */\n";
						$topMsg .= $msg;
						$minified .= $msg;
						$minified .= $content;
					}
				} else {
					$content = file_get_contents($f);
					$msg .= "\n/* rank:$rank - minify:disabled - adding raw file. $f */\n";
					$topMsg .= $msg;
					$minified .= $msg;
					$minified .= $content;
				}
				
				$minifiedAll .= $minified;
			}
			
 		}
 		
 		$topMsg .= "\n/**** end overview of included js files *****/\n";
 		file_put_contents($file, $topMsg. $minifiedAll);
 		chmod($file, 0644); 		
		return $file;
	}

	
	
	/**
	 * Output script tags for all javascript files being used.
	 * If minification is activated, file based JS (so not from a CDN) will bi minified und put into one single file
	 * @return string $jsScriptTags
	 */
	function output_js_files()
	{
	
		// we get one sorted array with script tags
		$js_files = $this->getJsFilesWithScriptTags();
		$output = '';
	
		foreach ($js_files as $entry) {
			$output .= "\n$entry";
		}
	
		return $output;
	}
	
	

	function output_js_config($wrap = true)
	{
		global $prefs;

		if ($prefs['javascript_enabled'] == 'n') {
			return;
		}

		$back = null;
		if (count($this->js_config)) {
			ksort($this->js_config);
			$back = "\n<!-- js_config before loading JSfile -->\n";
			$b = "";
			foreach ($this->js_config as $x=>$js) {
				$b.= "// js $x \n";
				foreach ($js as $j) {
					$b.= "$j\n";
				}
			}
			if ( $wrap === true ) {
   	     $back .= $this->wrap_js($b);
			} else {
				$back .= $b;
			}
		}

		return $back;

	}

	function clear_js($clear_js_files = false)
	{
		$this->js = array();
		$this->jq_onready = array();
		if ($clear_js_files) {
			$this->jsfiles = array();
		}
		return $this;
	}

	function output_js($wrap = true)
	{	// called in tiki.tpl - JS output at end of file now (pre 5.0)
		global $prefs;

		if ($prefs['javascript_enabled'] == 'n') {
			return;
		}

		ksort($this->js);
		ksort($this->jq_onready);

		$back = "\n";

		if (count($this->js)) {
			$b = '';
			foreach ($this->js as $x=>$js) {
				$b.= "// js $x \n";
				foreach ($js as $j) {
					$b.= "$j\n";
				}
			}
			if ( $wrap === true ) {
				$back .= $this->wrap_js($b);
			} else {
				$back .= $b;
			}
		}

		if (count($this->jq_onready)) {
			$b = '$(document).ready(function(){'."\n";
			foreach ($this->jq_onready as $x=>$js) {
				$b.= "// jq_onready $x \n";
				foreach ($js as $j) {
					$b.= "$j\n";
				}
			}
			$b .= "});\n";
			if ( $wrap === true ) {
				$back .= $this->wrap_js($b);
			} else {
				$back .= $b;
			}
		}

		return $back;
	}

	/**
	 * Gets JavaScript and jQuery scripts as an array (for AJAX)
	 * @return array[strings]
	 */
	function getJs()
	{
		global $prefs;

		ksort($this->js);
		ksort($this->jq_onready);
		$out = array();

		if (count($this->js)) {
			foreach ($this->js as $x=>$js) {
				foreach ($js as $j) {
					$out[] = "$j\n";
				}
			}
		}
		if (count($this->jq_onready)) {
			$b = '$(document).ready(function(){'."\n";
			foreach ($this->jq_onready as $x=>$js) {
				$b.= "// jq_onready $x \n";
				foreach ($js as $j) {
					$b.= "$j\n";
				}
			}
			$b .= "}) /* end on ready */;\n";
			$out[] = $b;
		}
		return $out;
	}


	function wrap_js($inJs)
	{
		return "<script type=\"text/javascript\">\n<!--//--><![CDATA[//><!--\n".$inJs."//--><!]]>\n</script>\n";
	}

	/**
	 * Get JavaScript tags from html source - used for AJAX responses and cached pages
	 *
	 * @param string $html - source to search for JavaScript
	 * @param bool $switch_fn_definition - if set converts 'function fName ()' to 'fName = function()' for AJAX
	 * @param bool $isFiles - if set true, get external scripts. If set to false, get inline scripts. If true, the external script tags's src attributes are returned as an array.
	 *
	 * @return array of JavaScript strings
	 */
	function getJsFromHTML( $html, $switch_fn_definition = false, $isFiles = false )
	{
		$jsarr = array();
		$js_script = array();

		preg_match_all('/(?:<script.*type=[\'"]?text\/javascript[\'"]?.*>\s*?)(.*)(?:\s*<\/script>)/Umis', $html, $jsarr);
		if ($isFiles == false) {
			if (count($jsarr) > 1 && is_array($jsarr[1]) && count($jsarr[1]) > 0) {
				$js = preg_replace('/\s*?<\!--\/\/--><\!\[CDATA\[\/\/><\!--\s*?/Umis', '', $jsarr[1]);	// strip out CDATA XML wrapper if there
				$js = preg_replace('/\s*?\/\/--><\!\]\]>\s*?/Umis', '', $js);

				if ($switch_fn_definition) {
					$js = preg_replace('/function (.*)\(/Umis', "$1 = function(", $js);
				}

				$js_script = array_merge($js_script, $js);
			}
		} else {
			foreach ($jsarr[0] as $key=>$tag) {
				if (empty($jsarr[1][$key])) { //if there was no content in the script, it is a src file
					//we load the js as a xml element, then look to see if it has a "src" tag, if it does, we push it to array for end back
					$js = simplexml_load_string($tag);
					if (!empty($js['src']))
						array_push($js_script, (string)$js['src']);
				}
			}
		}
		// this is very probably possible as a single regexp, maybe a preg_replace_callback
		// but it was stopping the CDATA group being returned (and life's too short ;)
		// the one below should work afaics but just doesn't! :(
		// preg_match_all('/<script.*type=[\'"]?text\/javascript[\'"]?.*>(\s*<\!--\/\/--><\!\[CDATA\[\/\/><\!--)?\s*?(.*)(\s*\/\/--><\!\]\]>\s*)?<\/script>/imsU', $html, $js);

		return array_filter($js_script);
	}

	function removeJsFromHTML( $html )
	{
		$html = preg_replace('/(?:<script.*type=[\'"]?text\/javascript[\'"]?.*>\s*?)(.*)(?:\s*<\/script>)/Umis', "", $html);
		return $html;
	}

	public function get_all_css_content()
	{
		$files = $this->collect_css_files();
		$minified = '';
		foreach ( array_merge($files['screen'], $files['default']) as $file) {
			$minified .= $this->minify_css($file);
		}
		$minified = $this->handle_css_imports($minified);

		return $minified;
	}

	private function output_css_files()
	{
		$files = $this->collect_css_files();

		$back = $this->output_css_files_list($files['default'], '');
		$back .= $this->output_css_files_list($files['screen'], 'screen');
		$back .= $this->output_css_files_list($files['print'], 'print');
		return $back;
	}

	private function output_css_files_list( $files, $media = '' )
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_modifier_escape');

		$back = '';

		if ( $prefs['tiki_minify_css'] == 'y' && !empty($files)) {
			if ( $prefs['tiki_minify_css_single_file'] == 'y' ) {
				$files = $this->get_minified_css_single($files);
			} else {
				$files = $this->get_minified_css($files);
			}
		}

		foreach ( $files as $file ) {
			$file = $this->convert_cdn($file);
			$back .= "<link rel=\"stylesheet\" href=\"" . smarty_modifier_escape($file) . "\" type=\"text/css\"";
			if (!empty($media)) {
				$back .= " media=\"" . smarty_modifier_escape($media) . "\"";
			}
			$back .= " />\n";
		}

		return $back;
	}

	private function get_minified_css( $files )
	{
		global $tikidomainslash;
		$out = array();
			$target = 'temp/public/'.$tikidomainslash;

		foreach ( $files as $file ) {
			$hash = md5($file);
			$min = $target . "minified_$hash.css";

			if ( ! file_exists($min) ) {
				file_put_contents($min, $this->minify_css($file));
				chmod($min, 0644);
			}

			$out[] = $min;
		}

		return $out;
	}

	private function get_minified_css_single( $files )
	{
		global $tikidomainslash;
		$hash = md5(serialize($files));
		$target = 'temp/public/'.$tikidomainslash;
		$file = $target . "minified_$hash.css";

		if ( ! file_exists($file)) {
			$minified = '';

			foreach ( $files as $f ) {
				$minified .= $this->minify_css($f);
			}

			$minified = $this->handle_css_imports($minified);

			file_put_contents($file, $minified);
			chmod($file, 0644);
		}

		return array( $file );
	}

	private function handle_css_imports( $minified )
	{
		global $tikiroot;

		preg_match_all('/@import\s+url\("([^;]*)"\);/', $minified, $parts);
		$top = [];

		$pre = '';
		foreach ( $parts[1] as $k => $f ) {
			if (substr($f, 0, 2) == '//' || substr($f, 0, 7) == 'http://' || substr($f, 0, 8) == 'https://') {
				$top[] = $parts[0][$k];
				unset($parts[0][$k]); // Exclude import removal, external file
			} else {
				$pre .= $this->minify_css($f);
			}
		}

		$imports = array_unique($parts[0]);
		$minified = $pre . $minified;
		$minified = str_replace($imports, '', $minified);

		return implode("\n", $top) . "\n" . $minified;
	}

	public function minify_css( $file )
	{
		global $tikipath, $tikiroot;
		if (strpos($file, $tikiroot) === 0) {
			$file = substr($file, strlen($tikiroot));
		}

		$currentdir = str_replace($tikipath, $tikiroot, str_replace('\\', '/', dirname(realpath($file))));
		if ( $file[0] == '/' ) {
			$file = $tikipath . $file;
		}

		$content = file_get_contents($file);

		return Minify_CSS::minify($content, array('prependRelativePath' => $currentdir.'/', 'bubbleCssImports' => true));
	}


	private function collect_css_files()
	{
		global $tikipath;

		$files = array(
			'default' => array(),
			'screen' => array(),
			'print' => array(),
		);

		$pushFile = function ($section, $file) use (& $files) {
			global $prefs;
			$files[$section][] = $file;

			if ($prefs['feature_bidi'] == 'y') {
				$rtl = str_replace('.css', '', $file) . '-rtl.css';

				if (file_exists($rtl)) {
					$files[$section][] = $rtl;
				}
			}
		};

		foreach ($this->cssfiles as $x=>$cssf) {
			foreach ($cssf as $cf) {
				$cfprint = str_replace('.css', '', $cf) . '-print.css';
				if (!file_exists($tikipath . $cfprint)) {
					$pushFile('default', $cf);
				} else {
					$pushFile('screen', $cf);
					$pushFile('print', $cfprint);
				}
			}
		}
		return $files;
	}

	function get_css_files()
	{
		$files = $this->collect_css_files();

		return array_merge($files['default'], $files['screen']);
	}

	/**
	 * Compile a new css file in temp/public using the provided theme and the custom LESS string
	 *
	 * @param string $custom_less        The LESS syntax string
	 * @param string $themename          Theme to base the compile on
	 * @param string $themeoptionname    Theme option name (for future use)
	 * @param bool $use_cache            (for future use and testing)
	 * @return array                     Array of CSS file paths out (can be theme and option if there's an error)
	 */
	function compile_custom_less($custom_less, $themename, $themeoptionname = '', $use_cache = true) {

		global $tikidomainslash, $tikiroot;

		$hash = md5($custom_less . $themename . $themeoptionname);
		$target = "temp/public/$tikidomainslash";
		$css_file = $target . "custom_less_$hash.css";
		$css_files = array($css_file);

		if ( ! file_exists($css_file) || ! $use_cache) {

			$themeLib = TikiLib::lib('theme');

			$theme_less_file = $themeLib->get_theme_path($themename, '', $themename . '.less');
			$themeoption_less_file = $themeLib->get_theme_path($themename, $themeoptionname, $themeoptionname . '.less');

			if ($theme_less_file === $themeoption_less_file) {
				$themeoption_less_file = '';	// some theme options are CSS only
			}

			$options = array(
				'compress' => true,
				'cache_dir' => realpath($target),
			);

			$parser = new Less_Parser($options);

			try {

				$nesting = count(array_filter(explode(DIRECTORY_SEPARATOR, $tikiroot)));
				$depth = count(array_filter(explode(DIRECTORY_SEPARATOR, $target)));
				$offset = $nesting ? str_repeat('../', $depth) : '';

				// less.php does all the work of course
				$parser->parseFile($theme_less_file, $offset . $tikiroot);	// appears to need the relative path from temp/public where the CSS will be cached
				if ($themeoption_less_file) {
					$parser->parseFile($themeoption_less_file, $offset . $tikiroot);
				}
				$parser->parse($custom_less);
				$css = $parser->getCss();

				file_put_contents($css_file, $css);
				chmod($css_file, 0644);

				$css_files = array($css_file);

			} catch (Exception $e) {
				if (is_writeable($css_file)) {
					unlink($css_file);
				}

				TikiLib::lib('errorreport')->report(tra('Custom Less compilation failed with error:') . $e->getMessage());
				$css_files = array(
					$themeLib->get_theme_path($themename, '', $themename . '.css'),
					$themeLib->get_theme_path($themename, $themeoptionname, ($themeoptionname ?: $themename) . '.css'),
				);

			}

		}

		return $css_files;
	}

	function add_map()
	{
		global $prefs;

		$tikilib = TikiLib::lib('tiki');
		$enabled = $tikilib->get_preference('geo_tilesets', array('openstreetmap'), true);

		$google = array_intersect(array('google_street', 'google_physical', 'google_satellite', 'google_hybrid'), $enabled);
		if (count($google) > 0 || $prefs['geo_google_streetview'] == 'y') {
			$args = array(
				'v' => '3.3',
				'sensor' => 'false',
			);

			if (! empty($prefs['gmap_key'])) {
				$args['key'] = $prefs['gmap_key'];
			}

			$url = $tikilib->httpScheme() . '://maps.googleapis.com/maps/api/js?' . http_build_query($args, '', '&');

			if (TikiLib::lib('access')->is_xml_http_request()) {
				$this->add_js('function loadScript() {
var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = "' . $url . '";
	document.body.appendChild(script);
}

window.onload = loadScript;');
			} else {
				$this->add_jsfile_external($url, true);
			}
		}

		/* Needs additional testing
		$visual = array_intersect(array('visualearth_road', 'visualearth_aerial', 'visualearth_hybrid'), $enabled);
		if (count($visual) > 0) {
			$this->add_jsfile_cdn('http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1');
		}
		*/

		if ($prefs['geo_openlayers_version'] === 'ol3') {
//			$this->add_jsfile_external('vendor/openlayers/ol3/ol.js', true);
			$this->add_jsfile_external('vendor/openlayers/ol3/ol-debug.js', true);
			$this->add_js(
			    ''
	        );
		} else {
			$this->add_jsfile_external('lib/openlayers/OpenLayers.js', true);
		}

		$this->add_js(
		    '$(".map-container:not(.done)")
		        .addClass("done")
		        .visible(function() {
		            $(this).createMap();
		    });'
        );

		return $this;
	}


	function __toString()
	{
		return '';
	}
}

