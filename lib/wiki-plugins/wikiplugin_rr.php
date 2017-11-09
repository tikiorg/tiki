<?php
// Parses R code (r-project.org) and shows the output in a wiki page.
// Corresponding author: Xavier de Pedro. <xavier.depedro (a) vhir.org> 
// Contributors: Rodrigo Sampaio, Lukáš Mašek, Louis-Philippe Huberdau, Sylvie Greverend, Jean-Marc Libs, Robert Plummer
// Usage:
// {R()}R code{R}. See documentation: https://doc.tiki.org/PluginR 
//	
// $Id: wikiplugin_rr.php 59285 2016-07-27 11:58:36Z xavidp $
/* 
From the R Extension for Mediawiki
(C) 2006- Sigbert Klinke (sigbert@wiwi.hu-berlin.de), Markus Cozowicz, Michael Cassin
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110, USA 
*/

function wikiplugin_rr_info() {
	return array(
		'name' => tra('RR (R syntax also)'),
		'documentation' => 'PluginR',
		'description' => tra('Same as PluginR, but allowing the execution of potentially dangerous commands once the admin has validated the plugin call.'),
		'prefs' => array( 'wikiplugin_rr' ),
		'validate' => 'all',
		'body' => tra('R Code'),
		'iconname' => 'code',
		'format' => 'html',
		'params' => array(
			'echo' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('echo'),
				'description' => tra('Show a code block with the R commands to be run before running them (similarly to the echo command)'),
				'filter' => 'int',
				'default' => '0',
				'since' => 'PluginR 0.78',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('No'), 'value' => '0'),
					array('text' => tra('Yes'), 'value' => '1'),
				),
				'advanced' => false,
			),
			'echodebug' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('echo debug'),
				'description' => tra('Show a code block with the R commands to be run even if the R program fails to execute'),
				'filter' => 'int',
				'default' => '0',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('No'), 'value' => '0'),
					array('text' => tra('Yes'), 'value' => '1'),
				),
				'advanced' => true,
			),
			'caption' => array(
				'required' => false,
				'name' => tra('Caption'),
				'description' => tra('Code snippet label.'),
				'default' => 'R Code',
				'since' => 'PluginR 0.78',
				'advanced' => true,
			),
			'wrap' => array(
				'required' => false,
				'name' => tra('Word Wrap'),
				'description' => tra('Enable word wrapping on the code to avoid breaking the layout.'),
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'default' => '1',
				'since' => 'PluginR 0.78',
				'advanced' => true,
			),
			'colors' => array(
				'required' => false,
				'name' => tra('Colors'),
				'description' => tra('Syntax highlighting with colors. Available: php, html, sql, javascript, css, java, c, doxygen, delphi, rsplus...'),
				'default' => 'r',
				'since' => 'PluginR 0.78',
				'advanced' => true,
			),
			'ln' => array(
				'required' => false,
				'name' => tra('Line Numbers'),
				'description' => tra('Show line numbers for each line of code.'),
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('Yes'), 'value' => '1'),
					array('text' => tra('No'), 'value' => '0'),
				),
				'default' => '1',
				'since' => 'PluginR 0.78',
				'advanced' => true,
			),
			'wikisyntax' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('wikisyntax'),
				'description' => tra('Choose whether the output should be parsed as wiki syntax (Optional). Options: 0 (no parsing, default), 1 (parsing)'),
				'filter' => 'int',
				'default' => '0',
				'since' => 'PluginR 0.1',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('0'), 'value' => '0'),
					array('text' => tra('1'), 'value' => '1'),
				),
				'advanced' => true,
			),
			'width' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('width'),
				'description' => tra('Width of the graph (Optional). Options: an integer number in pixels (default) or in units specified. If ommitted but height is set, width will be proportional to keep aspect ratio'),
				'filter' => 'int',
				'default' => '480',
				'since' => 'PluginR 0.1',
				'advanced' => true,
			),
			'height' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('height'),
				'description' => tra('Height of the graph (Optional). Options: an integer number in pixels (default) or in units specified. If ommitted but width is set, height will be proportional to keep aspect ratio'),
				'default' => '480',
				'since' => 'PluginR 0.1',
				'filter' => 'int',
				'advanced' => true,
			),
			'units' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('units'),
				'description' => tra('Choose units for the width and/or height parameters (Optional). Options: px (default) for pixels, in (inches), cm or mm'),
				'filter' => 'alpha',
				'default' => 'px',
				'since' => 'PluginR 0.1',
				'advanced' => true,
			),
			'pointsize' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('pointsize'),
				'description' => tra('The default pointsize of plotted text, interpreted as big points (1/72 inch) at res dpi (optional). Options: interger number such as 12 or bigger'),
				'filter' => 'int',
				'default' => '',
				'since' => 'PluginR 0.1',
				'advanced' => true,
			),
			'bg' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('bg'),
				'description' => tra('The initial background colour (optional). Options: white, yellow, grey, ... and transparent'),
				'filter' => 'striptags',
				'default' => 'transparent',
				'since' => 'PluginR 0.2',
				'advanced' => true,
			),
			'parse_body' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('parse_body'),
				'description' => tra('parses the body content to allow data to be generated from other plugins'),
				'filter' => 'alpha',
				'default' => 'n',
				'since' => 'PluginR 0.90 ?',
				'advanced' => true,
			),			
			'res' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('res'),
				'description' => tra('The nominal resolution in dpi which will be recorded in the bitmap file (if any). Also used for units other than the default, and to convert points to pixels (Optional). Options: a positive integer (default: 72 dpi). Values higher than 150 usually seem to be too much'),
				'filter' => 'int',
				'default' => '72',
				'since' => 'PluginR 0.1',
				'advanced' => true,
			),
			'svg' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('svg'),
				'description' => tra('Show link for the creation of the SVG version of the plot. Options: 0 (do not create it, default), 1 (create it). Requires R Cairo pakage, which can be checked with the following command in the R console: capabilities()'),
				'filter' => 'int',
				'default' => '0',
				'since' => 'PluginR 0.70',
				'advanced' => true,
			),
			'pdf' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('pdf'),
				'description' => tra('Show link for the creation of the PDF version of the plot. Options: 0 (do not create it, default), 1 (create it). Requires R Cairo pakage, which can be checked with the following command in the R console: capabilities()'),
				'filter' => 'int',
				'default' => '0',
				'since' => 'PluginR 0.70',
				'advanced' => true,
			),
			'onefile' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('onefile'),
				'description' => tra('Should all plots appear in one file? This is the default value (1); but if you answer no, they will attempt to appear in separate files in the server, even if you currently will not be able to fetch them easily through the internet browser. This param can be used with figure types svg and pdf; however, not many svg viewers support several plots in one svg file'),
				'filter' => 'int',
				'default' => '1',
				'since' => 'PluginR 0.71',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('No'), 'value' => '0'),
					array('text' => tra('Yes'), 'value' => '1'),
				),
				'advanced' => true,
			),
			'loadandsave' => array(
				'required' => false,
				'name' => tra('LoadAndSave'),
				'description' => tra('Load a previous R session (.RData, if any) for the same wiki page so that R object will be used while you work within the same page. For pretty trackers are used (wiki pages with itemId), the R session data (.RData) will be shared for the same itemId across wiki pages'),
				'filter' => 'int',
				'default' => '0',
				'since' => 'PluginR 0.61 (multiuser at 0.86)',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('No'), 'value' => '0'),
					array('text' => tra('Yes'), 'value' => '1'),
				),
				'advanced' => true,
			),
			'cachestrategy' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('Caching strategy'),
				'description' => tra('Define caching strategy, including LoadAndSave session. Default: One cache and one LoadAndSave session per plugin'),
				'filter' => 'word',
				'default' => 'one',
				'options' => array(
					array('text' => tra("One cache per plugin"), 'value' => 'one'),
					array('text' => tra("One cache per plugin per user"), 'value' => 'peruser'),
					array('text' => tra("No caching: Ignore and replace existing cache, if any"), 'value' => 'nocache'),
				),
				'advanced' => true,
			),
			'cacheduration' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('Limit cache duration'),
				'description' => tra('Force cache recalculation after time limit. Default: No limit'),
				'filter' => 'int',
				'default' => '0',
				'options' => array(
					array('text' => tra("No limit"), 'value' => '0'),
					array('text' => tra("1 hour"), 'value' => '3600'),
					array('text' => tra("1 day"), 'value' => '86400'),
					array('text' => tra("7 days"), 'value' => '604800'),
					array('text' => tra("30 days"), 'value' => '2592000'),
					array('text' => tra("365 days"), 'value' => '31536000'),
				),
				'advanced' => true,
			),
			'cacheby' => array(
				'required' => false,
				'name' => tra('CacheBy'),
				'description' => tra('Write cached files inside a folder containing the Page id (pageid; default option) or the Page name (pagename)'),
				'filter' => 'alpha',
				'default' => 'pageid',
				'since' => 'PluginR 0.88',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Page Name (pagename)'), 'value' => 'pagename'),
					array('text' => tra('Page Id (pageid)'), 'value' => 'pageid'),
				),
				'advanced' => true,
			),
			'cacheagedisplay' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('Cache age'),
				'description' => tra('Display cache last modification date below the graph (default option: do not display)'),
				'filter' => 'int',
				'default' => '0',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra("Don't display cache creation date"), 'value' => '0'),
					array('text' => tra("Display cache creation date"), 'value' => '1'),
				),
				'advanced' => true,
			),
			'attId' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('attId'),
				'description' => tra('AttId from a tracker Item attachment. ex: 1. (Optional)'),
				'filter' => 'int',
				'default' => '',
				'since' => 'PluginR 0.1',
				'advanced' => true,
			),
			'type' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('type'),
				'description' => tra('Choose the source file type in the appropriate mimetype syntax (Optional). Options: csv|xml. ex: csv. (default). For xml, see documentation for more details on the additional R packages required'),
				'filter' => 'alpha',
				'default' => 'csv (text/csv)',
				'since' => 'PluginR 0.1',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('csv'), 'value' => 'text/csv'),
					array('text' => tra('xml'), 'value' => 'text/xml'),
				),
				'advanced' => true,
			),
			'x11' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('x11'),
				'description' => tra('Choose whether the server can use X11 to produce graphs in R, or alternatively use dev2bitmap instead (Optional). Options: 1 (R has support for X11, default), 0 (no support for X11 thus using dev2bitmap). These capabilities can be checked in the server with the command in the R console: capabilities()'),
				'filter' => 'int',
				'default' => '1',
				'since' => 'PluginR 0.62',
				'advanced' => true,
			),
			'removen' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('removen'),
				'description' => tra('Remove the extra \n tags generated by some R packages out of the user control (such as with charts generated through GoogleVis R package). Options: 0 (do not remove \n tags, default), 1 (remove them all).'),
				'filter' => 'int',
				'default' => '0',
				'since' => 'PluginR 0.76',
				'advanced' => true,
			),
			'customoutput' => array(
				'required' => false,
				'safe' => true,
				'name' => tra('Custom output'),
				'description' => tra('Write your custom png creation R command. Use tikiRRfilename for value of output. RR does not produce an output file.'),
				'filter' => 'int',
				'default' => '0',
				'options' => array(
					array('text' => '', 'value' => ''),
					array('text' => tra('No'), 'value' => '0'),
					array('text' => tra('Yes'), 'value' => '1'),
				),
				'advanced' => true,
			),
			'security' => array(
				'required' => false,
				'safe' => false,
				'name' => tra('security'),
				'description' => tra('Set the security level for the R commands allowed by the plugin. ex: 1. (default in R), 0 for no security checking (default in RR).'),
				'filter' => 'int',
				'default' => '1',
				'since' => 'PluginR 0.4',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('No'), 'value' => '0'),
					array('text' => tra('Yes'), 'value' => '1'),
				),
				'advanced' => true,
			),
		),
	);
}


function wikiplugin_rr($data, $params) {
	global $smarty, $trklib, $tikilib, $prefs, $dbversion_tiki, $tikidomainslash, $user, $tiki_p_edit;

	include_once('db/tiki-db.php');	// to set up multitiki etc if there ($tikidomain)

	# Clean the <br /> , <p> and </p> tags added by the Tiki or smarty parsers.
	$data = str_replace(array("<br />", "<p>", "</p>"), "", $data);

	if ($dbversion_tiki>=7.0) {
		// quick fix for 7.1RC1 - might find a better one soon... (jb).  Thanks jonnyb!
		if (stripos($data, '&lt;') !== false ||
					stripos($data, '&gt;') !== false ||
					stripos($data, '&quot;') !== false
		) {	// add more bad entities here 
			$data =$tikilib->htmldecode($data);
		}
	}

	if ($params["security"]==0) {
		/* do nothing: i.e. don't check for security in the command sent to R*/
	}else{		/* default: check for security in the commands sent to R*/
		$rejected = checkCommands( $data );
		if( count($rejected) > 0 ) {
			return displayRrErrorBox("errors",tra("Blocked commands found: ") . implode(', ', $rejected),tra("Use Plugin RR instead and validate your plugin call, or contact a site admin to have the plugin call validated for you."));
		}
	}

	$output = 'text';
	$style = '';
	$ws = '';

	$trklib = TikiLib::lib('trk');

	if (isset($_REQUEST['itemId'])) {
		$atts = $trklib->list_item_attachments($_REQUEST['itemId'], 0, -1, 'created_desc', '');
		if (!empty($atts['data'][0]['attId'])) {
			$params['attId'] = $atts['data'][0]['attId'];
		}
		$info = $trklib->get_item_attachment($params["attId"]);
		$md5data = md5($info['data']);
	}

	if (isset($_REQUEST['itemId'])) {

		// This fetches the whole row from the mysql tables for that tracker item
		$item_info = $trklib->get_item_info($_REQUEST['itemId']);
		// This $item_info['lastModif'] displays just the lastModification of the item.
		// create the md5 hash for that value
		$md5lastModif = md5($item_info['lastModif']);

	}	

	if(isset($_REQUEST['itemId']) ) {
		// Moved the hashing after the attId recognition attempt, in order to include the filename (if any) in the hash process
		// so that if a new filename is passed through attId (and/or itemId), a new R script is generated and processed accordingly
		// to avoid the former caching issues when dynamically passing a different attId to the same cached R custom script
		$sha1 = md5($data . $md5lastModif . $params . $output . $style);
	} else {
		$sha1 = md5($data . $params . $output . $style);
	}

	if (isset($params["echo"])) {
		$r_echo = $params["echo"];
		if ($r_echo=="1" OR $r_echo=="y" OR $r_echo=="yes") { $r_echo = 1; }
		if ($r_echo=="0" OR $r_echo=="n" OR $r_echo=="no") { $r_echo = 0; }
	}else{
		$r_echo = 0;
		// We set echo by default as 0 to respect the environment for earlier users, 
		// even if setting it to 1 would make it easier for the new end user to review
		// which syntax was the one that produced that output seen on the page
	}

	if (isset($_REQUEST['rrefresh'])) {
		$rrefresh = $_REQUEST['rrefresh'];
		if ($rrefresh=="1") { $rrefresh = "y"; }
		if ($rrefresh=="0") { $rrefresh = "n"; }
	}else{
		$rrefresh = "n";
	}

	if (isset($params['cachestrategy'])) {
		$cachestrategy = $params['cachestrategy'];
	}else{
		$cachestrategy = "one";
	}

	if (isset($params['cacheduration'])) {
		$cacheduration = $params['cacheduration'];
	}else{
		$cacheduration = 0;
	}

	// Only insert user identification in filenames when "per user" caching strategy is chosen
	$userinfilename = userInFilename($user,$cachestrategy);

	if ($convertPath = getCommandPath('convert')) {
		define('convert', $convertPath);
	} else {
		return displayRrErrorBox("errors","…convert… " . tra("command unavailable"),tra("You need to ensure that you have __imagemagick__ installed successfully on the server.") . "\n" . tra("See requirements on [https://doc.tiki.org/PluginR]."));
	}

	if (isset($params["loadandsave"])) {
		$loadandsave = $params["loadandsave"];
		if ($loadandsave=="TRUE" OR $loadandsave=="1") { $loadandsave = 1; }
		if ($loadandsave=="FALSE"  OR $loadandsave=="0") { $loadandsave = 0; }
	}else{
		$loadandsave = 1;
	}

	if (isset($params["cacheby"])) {
		$cacheby = $params["cacheby"];
		if ($cacheby=="name") { $cacheby = "pagename"; }
		if ($cacheby=="id") { $cacheby = "pageid"; }
	}else{
		$cacheby = "pageid";
	}

	if (! $r_path = getCommandPath('R')) {
		return displayRrErrorBox("errors","…R… " . tra("command unavailable"),tra("The __R__ package needs to be installed on the server.") . "\n" . tra("See requirements on [https://doc.tiki.org/PluginR]."));
	}
	if ($loadandsave==1 && isset($_REQUEST['itemId'])  && $_REQUEST['itemId'] > 0) {
		// --save : data sets are saved at the end of the R session
		// --quiet : Do not print out the initial copyright and welcome messages from R
		$r_command = "'$r_path' --save --quiet";

		// added ' .$tikidomainslash. ' in path to consider the case of multitikis
		$r_dir = getcwd() . DIRECTORY_SEPARATOR . 'temp/cache/' .$tikidomainslash. 'R__itemid_' . sprintf ("%06u", $_REQUEST['itemId']);
		if (!file_exists ( $r_dir )) {
			mkdir($r_dir, 0700);
		}
		// added ' .$tikidomainslash. ' in path to consider the case of multitikis
		$graph_dir = '.' . DIRECTORY_SEPARATOR . 'temp/cache/' . $tikidomainslash . 'R__itemid_' . sprintf ("%06u", $_REQUEST['itemId']);
	}elseif ($loadandsave==1) {
		// --save : data sets are saved at the end of the R session
		// --quiet : Do not print out the initial copyright and welcome messages from R
		$r_command = "'$r_path' --save --quiet";

		if ($cacheby=='pagename') { // Cache by pagename as explicitly requested
			//Convert spaces into some character to avoid R complaining because it can't create such folder in the server
			$wikipage = str_replace(array(" ", "+", "'", "ç", "ñ"), "_", $_REQUEST['page']);
		} else { // Cache by page id  (default safest option) 
			// Convert strange characters into some simple character to avoid R complaining because it can't create such folder in the server
			// Also prefix with page id to ensure uniqueness
			$page_id = $tikilib->get_page_id_from_name($_REQUEST['page']);
			$wikipage = "page${page_id}_" . preg_replace('/[^a-zA-Z0-9]/', "_", $_REQUEST['page']);
		}

		// added ' .$tikidomainslash. ' in path to consider the case of multitikis
		$r_dir = getcwd() . DIRECTORY_SEPARATOR . 'temp/cache/' . $tikidomainslash . 'R_' . $wikipage;
		if (!file_exists ( $r_dir )) {
			mkdir($r_dir, 0777);
		}

		// added ' .$tikidomainslash. ' in path to consider the case of multitikis
		$graph_dir = '.' . DIRECTORY_SEPARATOR . 'temp/cache/' . $tikidomainslash . 'R_' . $wikipage;

	}else{
		// --vanilla : Combine --no-save, --no-environ, --no-site-file, --no-init-file and --no-restore. Under Windows, this also includes --no-Rconsole.
		// --slave : Make R run as quietly as possible. It implies --quiet and --no-save
		$r_command = "'$r_path' --vanilla --slave";
		// added ' .$tikidomainslash. ' in path to consider the case of multitikis
		$r_dir = getcwd() . DIRECTORY_SEPARATOR . 'temp/cache/' . $tikidomainslash;
		$graph_dir = '.' . DIRECTORY_SEPARATOR . 'temp/cache/' . $tikidomainslash;
	}

	$r_html = $r_dir . DIRECTORY_SEPARATOR . $userinfilename . $sha1 . ".html";

	if(isset($params["attId"]) ) {

		$info = $trklib->get_item_attachment($params["attId"]);

		if( $info['data'] ) {
			#$filepath = tempnam( '/tmp', 'r' );
			$filepath = "/tmp/" . $userinfilename . $sha1;
			file_put_contents( $filepath, $info['data'] );
		} else {
			$filepath = $prefs['t_use_dir'].$info['path'];
		}

		if ( empty($info['filetype']) || $info['filetype'] == 'application/x-octetstream' || $info['filetype'] == 'application/octet-stream' ) {
			include_once('lib/mime/mimelib.php');
			if ($dbversion_tiki<9.0) {
					$info['filetype'] = tiki_get_mime($filepath, 'application/octet-stream'); # Old code not working after Tiki9 r42542: http://code.tiki.org/Commit+42542.
			} else {
					$info['filetype'] = TikiLib::lib('mime')->from_path($filepath, 'application/octet-stream'); # New code after Tiki9 r42542: http://code.tiki.org/Commit+42542
			}
		}

		$type = $info["filetype"];
		$file = $info["filename"];
	} else {

	}

	if (isset($params["type"])) {
		$type = $params["type"];
	}


	if ( isset($params["attId"]) && ($type == "text/csv" || $type == "text/comma-separated-values")) {
		$path = $_SERVER["SCRIPT_NAME"];
		// record filetype, data_file (path and file name), and data (contents) to be displayed, if desired, from R
		$data = "file_type <- \"$type\"\ndata_file <- \"$filepath\"\ndata <- read.csv(\"$filepath\")\n$data";
	} elseif (isset($params["attId"]) && $type == "text/plain") {
		$path = $_SERVER["SCRIPT_NAME"];
		// record filetype, data_file (path and file name), and data (contents) to be displayed, if desired, from R
		// read.delim & read.delim2 expect tabs as field separators (read.delim2 uses comma "," as decimal point; whereas read.delim uses point ".")
		$data = "file_type <- \"$type\"\ndata_file <- \"$filepath\"\ndata <- read.delim2(\"$filepath\")\n$data";
	} elseif (isset($params["attId"]) && $type == "text/xml") {
		$path = $_SERVER["SCRIPT_NAME"];
		// record filetype, data_file (path and file name), and data (contents) to be displayed, if desired, from R
		$data = "library(XML)\nfile_type <- \"$type\"\ndata_file <- xml(\"$filepath\")\ndata <- xmlTreeParse(data_file,  getDTD = F )\n$data";
	} elseif (isset($params["attId"]) && $type != "text/csv" && $type != "text/comma-separated-values" && $type != "text/xml" && $type != "text/plain") {
		$data = "data <- \"This file type is not recognized: $type.<br />Read the <a href=http://doc.tiki.org/PluginR>documentation</a> about the allowed filetypes\"\nfile_type <- \"$type\"\ndata_file <- \"$filepath\"\n$data";	
	} else {
		// do nothing
	}
	if ($dbversion_tiki>=7.0) {
		# Clean the <br /> , <p> and </p> tags added by the Tiki or smarty parsers on smarty templates in tiki7
		$data = str_replace(array("<br />", "<p>", "</p>"), "", $data);
	}

	// Find age of previous cache
	$cache_last_modif = @filemtime($r_html);
	if ( $cache_last_modif == FALSE ) {
		$cache_last_modif_readable = 'No cache';
		$cache_age = 0;
	} else {
		$cache_last_modif_readable = $tikilib->get_long_datetime($cache_last_modif);
		$cache_age = time() - $cache_last_modif;
	}
	$now = time();

	// Check if new run is needed or cached results (from the same plugin r calls) can be shown
	if ( file_exists($r_html) ) {
		$use_cached_script = "y";
	} else {
		$use_cached_script = "n";
	}
	if ( $rrefresh=="y" ) $use_cached_script = "n";
	if ( $cachestrategy == "nocache") $use_cached_script = "n";
	// cacheduration
	if ( $cacheduration > 0 && $cache_age > $cacheduration ) $use_cached_script = "n";
	
	if ( $use_cached_script=="y" ) {
		// do not execute R program to generate html but reuse the html previously generated 
		$fn   = $r_html;
	} else {
		// execute R program without using the cached output files even if they exist
		$r_R = $r_dir . DIRECTORY_SEPARATOR . $userinfilename . $sha1 . '.R';
		$r_png = $r_dir . DIRECTORY_SEPARATOR . $userinfilename . $sha1 . '_1.png';

		// Delete cached files .R & .html for this hash $sha1 if they exist
		if ( file_exists($r_html) ) {
			unlink($r_html);
			unlink($r_R);
		}
		// Delete cached files .png for this hash $sha1 if it exists
		if ( file_exists($r_png) ) {
			unlink($r_png);
		}

		// if parse_body is set parse the body content to allow data to be generated from other plugins and strip tags - and if echodebug is set show the body content before parsing
		if (isset($params["echodebug"]) && $params["echodebug"] == '1' && $params["parse_body"] === 'y' ) {
			$ret .= "<div >DEBUG ECHO:body content before parsing<pre>" . htmlspecialchars($data) . "</pre></div>";
		}
		
		if ($params["parse_body"] === 'y') {
			// parse the body content but suppress any icon generation from plugins
			$data = TikiLib::lib('parser')->parse_data($data, array('noparseplugins' => false, 'suppress_icons' => true));
			// strip tags
			$data = strip_tags($data);
			// undo any unwanted parser changes to the body content
			$data = str_replace(array("&lt;", "&gt;"), array("<", ">"), $data);
			// Reclean the <br /> , <p> and </p> tags added by the Tiki or smarty parsers (just in case).
			$data = str_replace(array("<br />", "<p>", "</p>"), "", $data);
		}

		// execute R program
		$runR_errors = array('error'=>FALSE);
		$fn   = runR ($output, convert, $sha1, $data, $r_echo, $ws, $params, $user, $r_command, $r_dir, $graph_dir, $loadandsave, $use_cached_script, $cachestrategy, $runR_errors);

		if ($runR_errors['error'] == TRUE) {
			return displayRrErrorBox($runR_errors['type'],$runR_errors['title'],$runR_errors['body']);
		}

		// age of new cache
		$cache_last_modif_readable = $tikilib->get_long_datetime(time());
	}

	$ret .= file_get_contents ($fn);

	// Allow debug echo which shows the code and other parameters even when the script fails (the most useful case for looking at the code)
	if (isset($params["echodebug"]) && $params["echodebug"] == '1' ) {
		if ($params["parse_body"] === 'y') {
			$ret .= "<div >DEBUG ECHO: parse_body parameter set to y</div>";
		}
		$ret .= "<div >DEBUG ECHO: body content<pre>" . htmlspecialchars($data) . "</pre></div>";
	}

	if ( preg_match('/tiki-index.php/', curPageURL() ) == 1) {
		$concat_char = '&'; // Presumably, question mark present in the url, so new params go after &
	} else {
		$concat_char = '?'; // Presumably, no previous question mark in the url, so params go after ?
	}

	// Display age of cache
	if ( isset($params["cacheagedisplay"]) && $params["cacheagedisplay"] == "1" ) {
		$ret .= ' <div class="rcacheage">';
		$ret .= tr('Cache was last modified: %0', $cache_last_modif_readable);
		$ret .= '</div>';
	}

	// Show the cached message for loged user and button to click on refresh except when in tiki-print.php
	$printpage=FALSE;
	if (preg_match("/tiki-print.php/", $_SERVER['REQUEST_URI'])) {
		$printpage = TRUE;
	}
	// TODO: create a group permission for allowing refreshing. For the time being, we check for tiki_p_edit
	if ( !empty($user) && $tiki_p_edit == 'y' && !$printpage) {
		$url_params = parse_url($_SERVER['REQUEST_URI']);
		$ret .= '<form method="post"  action="' . curPageURL() . $concat_char . '">';
		if (isset($url_params['query'])) {
			$url_params_array = explode('&',$url_params['query']);
			$found_rrefresh = 0;
			foreach( $url_params_array as $parameter) {
				list($key,$value) = explode('=',$parameter,2);
				if ( $key == "rrefresh" ) {
					$value = 'y';
					$found_rrefresh = 1;
				}
				$ret .= ' <input type="hidden" name="' .htmlspecialchars($key). '" value="' .htmlspecialchars(urldecode($value)). '" >';
			}
			if ( $found_rrefresh == 0 ) $ret .= ' <input type="hidden" name="rrefresh" value="y" >';
		}
		if ($dbversion_tiki>=13.0) {
			$ret .= ' <a href="#" onclick="parentNode.submit();return false;" >' . '<span class="icon fa fa-refresh" Title="' . tr("Cached R output from %0. If you click, you will re-run all R scripts in this page",$cache_last_modif_readable) . '"></span></a> </form>';
		} else {
			// Maybe this should be an input tag with maybe still the image
			$ret .= ' <a href="#" onclick="parentNode.submit();return false;" >' . '<img src=img/icons/arrow_refresh.png alt=Refresh Title="' . tr("Cached R output from %0. If you click, you will re-run all R scripts in this page",$cache_last_modif_readable) . '"></a> </form>';
		}
	}

	// Surround plugin with actual div and class, for styling purpose
	$ret = '<div class="wikiplugin_r">' . $ret . '</div>';

	// Check for Tiki version, to apply parsing of content or not (behavior changed in Tiki7, it seems)
	// Right now, the behavior seems the almost the same one on 7+ and <7, but just in case, I leave this version check in place, 
	// since some changes are expected sooner or later..., so I leave this as an easy place holder (and proof-of-concept of working version check 
	if ($dbversion_tiki>=7.0) {
		if (isset($params["wikisyntax"]) && $params["wikisyntax"]==1) {
			return $tikilib->parse_data($ret, array('is_html'=>true));	// the is_html parsing options are needed, in tiki7+, it seems, but not in < 7.0
		}else{		// if wikisyntax != 1 : no parsing of any wiki syntax
			return $ret;
		}
	}else{	// case for Tiki versions earlier than 7.0, where content is parsed by default	
		if (isset($params["wikisyntax"]) && $params["wikisyntax"]==1) {
			return $tikilib->parse_data($ret, array());
			// return $ret;
		}else{		// if wikisyntax != 1 : no parsing of any wiki syntax
			return $ret;
		}
	} // end of check for Tiki version

}


function runR ($output, $convert, $sha1, $input, $r_echo, $ws, $params, $user, $r_cmd, $r_dir, $graph_dir, $loadandsave, $use_cached_script, $cachestrategy, &$runR_errors) {
	static $r_count = 0;

	$userinfilename = userInFilename($user,$cachestrategy);
	// Make one .Rdata per user or a global one depending on cachestrategy
	$rdata = '.' . $userinfilename . 'RData';

	// Generate a graphics
	$prg = ''; # This variable is not being used. ToDo: Remove or reuse for something.
	$err = "\n";
	$rws = $r_dir . DIRECTORY_SEPARATOR;
	$rst  = $r_dir . DIRECTORY_SEPARATOR . $userinfilename . $sha1 . '.html';
	// Since pluginR 0.7, graphic file type is not hardcoded here into png; 
	//  file extensions will be set later for png and svg and/or pdf
	$rgo  = $r_dir . DIRECTORY_SEPARATOR . $userinfilename . $sha1 ;
	$rgo_rel  = $graph_dir . DIRECTORY_SEPARATOR . $userinfilename . $sha1 ;

	if (isset($params["wikisyntax"])) {
		$wikisyntax = $params["wikisyntax"];
	}else{
		$wikisyntax = "0";
	}

	if (isset($params["width"])) {
		$width = $params["width"];
	}else{
		$width = "";
	}

	if (isset($params["height"])) {
		$height = $params["height"];
	}else{
		$height = "";
	}

	if (isset($params["units"])) {
		$units = $params["units"];
	}else{
		$units = "px";
	}

	if (isset($params["onefile"])) {
		$onefile = $params["onefile"];
		if ($onefile="1") { $onefile = TRUE; }
		if ($onefile="0") { $onefile = FALSE; }
	}else{
		$onefile = TRUE;
	}

	if (isset($params["pointsize"])) {
		$pointsize = $params["pointsize"];
	}else{
		$pointsize = "";
	}

	if (isset($params["bg"])) {
		$bg = $params["bg"];
	}else{
		$bg = "transparent";
	}

	if (isset($params["res"])) {
		$res = $params["res"];
	}else{		// if not specified, use 72 dpi, optimized for screen
		$res = 72;
	}

	if (isset($params["wrap"])) {
		$wrap = $params["wrap"];
		if ($wrap==1 OR $wrap=="y" OR $wrap=="yes") { $wrap = "1"; }
		if ($wrap==0 OR $wrap=="n" OR $wrap=="no") { $wrap = "0"; }
	}else{		// if not specified, use wrapping to avoid breaking layout
		$wrap = "1";
	}
	if ( isset($wrap) && $wrap == "1" && isset($wikisyntax) && $wikisyntax == "0") {
		// Force wrapping in <pre> tag through a CSS hack
		$pre_style = 'white-space:pre-wrap;'
			.' white-space:-moz-pre-wrap !important;'
			.' white-space:-pre-wrap;'
			.' white-space:-o-pre-wrap;'
			.' word-wrap:break-word;';
	} else{
		// If there is no wrapping, display a scrollbar (only if needed) to avoid truncating the text
		$pre_style = 'overflow:auto;';
		echo $wrap;
	}

		if (isset($params["caption"])) {
		$caption = $params["caption"];
	}else{
		$caption = "RR Code"; // Default value
	}

	if (isset($params["colors"])) {
		$colors = $params["colors"];
	}else{
		$colors = "r"; // Default value
	}

	if (isset($params["ln"])) {
		$ln = $params["ln"];
		if ($ln=="1" OR $ln=="y" OR $ln=="yes") { $ln = 1; }
		if ($ln=="0" OR $ln=="n" OR $ln=="no") { $ln = 0; }
	}else{
		$ln = 1; // Default value
	}

	if (isset($params["customoutput"])) {
		$customoutput = $params["customoutput"];
		if ($customoutput=="1" OR $customoutput=="y" OR $customoutput=="yes") { $customoutput = 1; }
		if ($customoutput=="0" OR $customoutput=="n" OR $customoutput=="no") { $customoutput = 0; }
	}else{
		$customoutput = 0; // Default value
	}

	if (!file_exists($rst) or onsave) {
		$content = '';
		$content .= 'rfiles<-"' . $r_dir . '"' . "\n";
		// TODO: check R capabilities on this server and save result on "r_cap" file on disk
		// if file r_cap doesn't exist, check capabilities and save on disk
		// if capabilities()[[2]] == FALSE //use dev2bitmap
		// else //use png()

		// Alternatively, request the user to use extra param x11=0 if no X11 on server.
		if ( (isset($params["X11"]) || isset($params["x11"])) && ($params["X11"]==0 || $params["x11"]==0) ) {
			$content = 'cat(" -->")'."\n". 'dev2bitmap("' . $rgo . '.png' . '" , width = ' . $width . ', height = ' . $height . ',  pointsize = ' . $pointsize . ', res = ' . $res . ')' . "\n";
			$content .= 'dev.off()' . "\n";
			// Add the user input code at the end
			$content .= $input . "\n";
		}else{	// png can be used because R was compiled with support for X11

			// Set R echo to false and Change the working directory to the current subfolder in the temp/cache folder
			$content = 'options(echo=FALSE)'."\n". 'cat(" -->")'."\n". 'setwd("'. $r_dir .'/")'."\n";

			// Load .Rdata if requested and only if it exists in that folder
			if ($loadandsave==1 && file_exists($r_dir . DIRECTORY_SEPARATOR . $rdata)) {
				$content .= 'load("' . $rdata . '")' . "\n";
			} // Else, case with no caching of r objects (loadandsave=0, therefore no .RData will be loaded at the beginning)

			// Check if the user wants to handle the creation of his custom png
			if ( isset($params["customoutput"]) && $params["customoutput"]=="1" ) {
				$image_number = 1;
				$content .= 'tikiRRfilename <- "' . $rgo . "_$image_number.png" . '"' . "\n";
				// Add the user input code at the end
				$content .= $input . "\n";

			// Check if the user requested an svg file or pdf file to be generated instead of the standard png in the wiki page
			} elseif (isset($_REQUEST['gtype']) && $_REQUEST['gtype']=="svg") {
				// Prepare the graphic device to create the svg file 
				$content .= onefile . "<-" . $onefile . "\n";
				$content .= 'svg(filename = if(onefile) "' . $rgo . '.svg' . '" else "' . $rgo . '%03d.svg' . '", onefile = ' . $onefile . ', width = ' . $width . ', height = ' . $height . ', pointsize = ' . $pointsize . ', bg = "' . $bg . '" , antialias = c("default", "none", "gray", "subpixel"))' . "\n";
				// Add the user input code at the end
				$content .= $input . "\n";
			} elseif (isset($_REQUEST['gtype']) && $_REQUEST['gtype']=="pdf") {  # case for pdf
				// Prepare the graphic device to create the pdf
				$content .= onefile . "<-" . $onefile . "\n";
				$content .= 'cairo_pdf(filename = if(onefile) "' . $rgo . '.pdf' . '" else "' . $rgo . '%03d.pdf' . '", onefile = ' . $onefile . ', width = ' . $width . ', height = ' . $height . ', pointsize = ' . $pointsize . ', bg = "' . $bg . '" , antialias = c("default", "none", "gray", "subpixel"))' . "\n";
				// Add the user input code at the end
				$content .= $input . "\n";
			} else { # else of choice between svg, pdf and png
				// Produce the standard png file
				$image_number = 1;
				$content .= 'png(filename = "' . $rgo . "_$image_number.png" . '", width = ' . $width . ', height = ' . $height . ', units = "' . $units . '", pointsize = ' . $pointsize . ', bg = "' . $bg . '" , res = ' . $res . ')' . "\n";
				// Parse the user input for more graphs
				$input_array = explode( "\n", $input);
				reset($input_array);
				while( list($key,$line) = each($input_array) ) {
					if ( preg_match('/^#\s*newgraph$/', trim($line)) ) {
						$image_number++;
						$input_array[$key] = 'png(filename = "' . $rgo . "_$image_number.png" . '", width = ' . $width . ', height = ' . $height . ', units = "' . $units . '", pointsize = ' . $pointsize . ', bg = "' . $bg . '" , res = ' . $res . ')' ;
					}
				}
				$input = implode("\n",$input_array);
				// Add the user input code at the end
				$content .= $input . "\n";
			} # enf of choice between svg and png

			// Save the image after the user input if requested with the param loadandsave
			if ($loadandsave==1) {
				$content .= 'save.image("' . $rdata . '")' . "\n";
			} // Else, case with no caching of r objects (loadandsave=0, therefore no .RData will be saved at the end)

		} // end of section where png can be used because R was compiled with support for X11
		$content .= 'q()';
		$fn = $r_dir . DIRECTORY_SEPARATOR . $userinfilename . $sha1 . '.R';
		if (! $fd = fopen ($fn, 'w')) {
			$runR_errors = array('error'=>TRUE, 'type'=>'error', 'title'=>'Can not open R file' , 'body'=>$err);
			return;
		}
		fwrite ($fd, $content);
		fclose ($fd);
		$cmd = $r_cmd . ' 2>&1 < ' . $fn . ' > ' . $rst;
		// Windows compatibility
		if (strncmp(PHP_OS, 'WIN', 3)==0) {
			$cmd = str_replace('/', '\\', $cmd);
		}
		$stdout = "";
		exec($cmd, $stdout);
		if (is_array($stdout)) {
			$err .= implode("\n", $stdout) . "\n";
		}
		if (is_string($stdout)) {
			$err .= $stdout . "\n";
		}
	}
	if (!file_exists($rst)) {
		$runR_errors = array('error'=>TRUE, 'type'=>'error', 'title'=>'Cached file does not exist' , 'body'=>$err);
		return;
	}
	$cont = file_get_contents ($rst);

	if (strpos ($cont, '<html>') === false) {
		if (! $fd = fopen ($rst, 'w')) {
			$runR_errors = array('error'=>TRUE, 'type'=>'error', 'title'=>'Can not open cached file' , 'body'=>$err);
			return;
		}

		if ($r_exitcode == 0) { // case when no error occurred

			// Start of Preprocessing HTML before sending it to the user's browser: cleanup, etc.
			// ----------------------------------
			//remove empty lines produced by some R packages such as googleVis that were inserting too much white space for granted before the graphs produced by the Google Visualization API 
			$cont = str_replace(array("// jsData", "// jsDrawChart", "// jsDisplayChart", "// jsChart", "// jsFooter"), '', $cont);
			$cont = str_replace(array("<!-- jsHeader -->", "<!-- jsChart -->", "<!-- divChart -->"), '', $cont);

			// Optionally, remove extra \n if requested explicitly, to keep the output cleaner with fewer non wanted \n, as in the case with graphs created through calls to googleVis R package
			if ( isset($params["removen"]) && $params["removen"]=="1") {
				// remove spaces at the start and end of new lines
				$cont = join("\n", array_map("trim", explode("\n", $cont)));
				// remove empty new lines
				$cont = preg_replace('/[ \t]+/', ' ', preg_replace('/\s*$^\s*/m', "\n", $cont));
			}
			// Write the start tag of an html comment to comment out the tag to remove echo from R console. The closing html comment tag is added inside $cont after the "option(echo=FALSE)"

			// End of HTML preprocessing

			// Echo requested?
			if ( $r_echo==1 ){
				// $ content still keeps the data of the R file to be executed, so it can be reused to get the raw content to clean before the echo is shown
				//Remove the first 5 lines which come from pluginr headers
				$echo_content = implode("\n", array_slice(explode("\n", $content), 5));
				//Remove the last 2 lines of R code which come from other pluginr params
				$echo_content = implode("\n", array_slice(explode("\n", $echo_content), 0, -2));

				// -------- Start of code borrowed from PluginCode
					global $prefs;
					static $code_count;

					$code_defaults = array(
						'wrap' => '1',
						'mediawiki' => '0'
					);

					$code_params = array_merge($code_defaults, $params);

					extract($code_params, EXTR_SKIP);
					$code = trim($echo_content);

					$code = str_replace('&lt;x&gt;', '', $code);
					$code = str_replace('<x>', '', $code);

					$id = 'codebox'.++$code_count;
					$boxid = " id=\"$id\" ";

					$out = $code;

					if (isset($colors) && $colors == '1') {	// remove old geshi setting as it upsets codemirror
						unset( $colors );
					}

					//respect wrap setting when Codemirror is off and set to wrap when Codemirror is on to avoid broken view while
					//javascript loads
					if ((isset($prefs['feature_syntax_highlighter']) && $prefs['feature_syntax_highlighter'] == 'y') || $wrap == 1) {
						$pre_style = 'white-space:pre-wrap;'
						.' white-space:-moz-pre-wrap !important;'
						.' white-space:-pre-wrap;'
						.' white-space:-o-pre-wrap;'
						.' word-wrap:break-word;';
					}

					$out = (isset($caption) ? '<div class="codecaption">'.$caption.'</div>' : "" )
						. '<pre class="codelisting" '
						. (isset($colors) ? ' data-syntax="' . $colors . '" ' : '')
						. (isset($ln) ? ' data-line-numbers="' . $ln . '" ' : '')
						. (isset($wrap) ? ' data-wrap="' . $wrap . '" ' : '')
						. ' dir="'.( (isset($rtl) && $rtl == 1) ? 'rtl' : 'ltr') . '" '
						. (isset($pre_style) ? ' style="'.$pre_style.'"' : '')
						. $boxid.'>'
						. (($options['ck_editor'] || $ishtml) ? $out : htmlentities($out, ENT_QUOTES, 'UTF-8'))
						. '</pre>';
				// -------- End of code borrowed from PluginCode

				fwrite ($fd, $prg . '<pre>' . $out . '</pre>');
			}// Else: no echo requested
			# Check if there is anything after "-->" in $cont. Otherwise, do not show the pre tags.
			# Split the content $cont by the splitter "-->", and the 2nd element (#1 since it starts with 0) is the text output, if any
			# if no text output, don't add the pre tags since in Tiki 15+ they display a grey box by default.
			$textoutput = preg_split("/-->/", $cont);
			if (!empty($textoutput[1])) {
				fwrite ($fd, $prg . '<pre id="routput' . $r_count . '" name="routput' . $r_count . '" style="'.$pre_style.'"><!-- ' . $cont . '</pre>');
			}
			for ( $i=1; $i<=$image_number; $i++) {
				if (file_exists($rgo . "_$i" . '.png')) {
					fwrite ($fd, $prg . '<img src="' . $rgo_rel . "_$i" . '.png' . '" class="fixedSize"' . ' alt="' . $rgo_rel . "_$i" . '.png' . '">');
				}
			}
			if ( !empty($user) && isset($params["svg"]) && $params["svg"]=="1" || ( isset($params["pdf"]) && $params["pdf"]=="1" ) ){
				fwrite ($fd, $prg . '</br>');
			}
			if ( !empty($user) && isset($params["svg"]) && $params["svg"]=="1") {
				if ( preg_match('/tiki-index.php/', curPageURL() ) == 1) {
					$PageURLRaw = preg_replace('/tiki-index.php/', 'tiki-index_raw.php', curPageURL() );
				} else {
					$PageURLRaw = preg_replace('#/([^/]+)$#', '/tiki-index_raw.php?page=\1', curPageURL() );
				}
				fwrite ($fd, $prg . ' <span class="button"><a href="' . $PageURLRaw . '&gtype=svg&clean=y' . '" alt="' . $rgo_rel . '.svg' . '" target="_blank">' . tr("Save Image as SVG") . '</a></span>');

			}
			if ( !empty($user) && isset($params["pdf"]) && $params["pdf"]=="1") {
				if ( preg_match('/tiki-index.php/', curPageURL() ) == 1) {
					$PageURLRaw = preg_replace('/tiki-index.php/', 'tiki-index_raw.php', curPageURL() );
				} else {
					$PageURLRaw = preg_replace('#/([^/]+)$#', '/tiki-index_raw.php?page=\1', curPageURL() );
				}
				fwrite ($fd, $prg . ' <span class="button"><a href="' . $PageURLRaw . '&gtype=pdf&clean=y' . '" alt="' . $rgo_rel . '.pdf' . '" target="_blank">' . tr("Save Image as PDF") . '</a></span>');
			}
			
		} else { // Case when some error occurred
			fwrite ($fd, $prg . '<pre><!-- ' . $cont . '<span style="color:red">' . $err . '</span>' . '</pre>');
		}
		fclose ($fd);
	}

	$r_count++;

	// Check if the user requested an svg file to be generated instead of the standard png in the wiki page
	if ( !empty($user) && isset($_REQUEST['gtype']) && $_REQUEST['gtype']=="svg") {
		// return an svg file to be downloaded
		if (isset($_REQUEST["filename"])) {
			$filename = $_REQUEST['filename'];
		} else {
			// Get wikipage name for the name of the svg or pdf files to be downloaded eventually
			//Convert spaces into some character to avoid R complaining because it can't create such folder in the server
			$wikipage = str_replace(array(" ", "+"), "_", $_REQUEST['page']);
			// Create the filename
			$filename = $wikipage . "_" . tr("plot") . $r_count . ".svg";
		}
		$filename = str_replace(array('?',"'",'"',':','/','\\'), '_', $filename);	// clean some bad chars
		header('Content-type: image/svg+xml');
		header('Content-Length: '.filesize($rgo . '.svg'));
		header("Content-Disposition: attachment; filename=\"$filename\"");
		readfile($rgo . '.svg');
	} elseif ( !empty($user) && isset($_REQUEST['gtype']) && $_REQUEST['gtype']=="pdf") { 	// Check if the user requested a pdf file to be generated instead of the standard png in the wiki page
		// return a pdf file to be downloaded
		if (isset($_REQUEST["filename"])) {
			$filename = $_REQUEST['filename'];
		} else {
			// Get wikipage name for the name of the svg or pdf files to be downloaded eventually
			//Convert spaces into some character to avoid R complaining becuase it can't create such folder in the server
			$wikipage = str_replace(array(" ", "+"), "_", $_REQUEST['page']);
			// Create the filename
			$filename = $wikipage . "_" . tr("plot") . $r_count . ".pdf";
		}
		$filename = str_replace(array('?',"'",'"',':','/','\\'), '_', $filename);	// clean some bad chars
		header('Content-type: application/pdf');
		header('Content-Length: '.filesize($rgo . '.pdf'));
		header("Content-Disposition: attachment; filename=\"$filename\"");
		readfile($rgo . '.pdf');
	} else {
		return $rst; // normal return of html file
	}

}

function curPageURL() {
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	$pageURL .= "://";
	if ($_SERVER["SERVER_PORT"] != "80") {
		 $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	} else {
		 $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	}
	return $pageURL;
}

function checkCommands ($input) {
	// Thanks to the R-php & R-MediaWiki people. We understand that the list of commands is not licensed since it's not PHP code as such)
	$banned = array('.C', '.Call', '.Call.graphics', '.External', '.External.graphics',
			'.Fortran', '.readRDS', '.saveRDS', '.Script', '.Tcl',
			'.Tcl.args', '.Tcl.callback', '.Tk.ID', '.Tk.newwin', '.Tk.subwin',
			'.Tkroot', '.Tkwin', 'basename', 'browseURL', 'bzfile',
			'capture.output', 'close', 'close.screen', 'closeAllConnection', 'data.entry',
			'data.restore', 'dataentry', 'de', 'dev.control', 'dev.copy2eps',
			'dev.cur', 'dev.list', 'dev.next', 'dev.prev', 'dev.print',
			'dev.set', 'dev2bitmap', 'dget', 'dir', 'dir.create',
			'dirname', 'do.call', 'download.file', 'dput', 'dump',
			'dyn.load', 'edit', 'edit.data.frame', 'emacs', 'erase.screen',
			'example', 'fifo', 'file', 'file.access', 'file.append',
			'file.choose', 'file.copy', 'file.create', 'file.exists', 'file.info',
			'file.path', 'file.remove', 'file.rename', 'file.show', 'file.symlink',
			'fix', 'getConnection', 'getwd', 'graphics.off', 'gzcon',
			'gzfile', 'INSTALL', 'install.packages', 'library.dynam',
			'list.files','loadhistory', 'locator', 'lookup.xport', 'make.packages.html',
			'make.socket', 'menu', 'open', 'parent.frame', 'path.expand',
			'pico', 'pictex', 'pipe',
			'postscript', 'print.socket', 'prompt', 'promptData', 'quartz',
			'R.home', 'R.version', 'read.00Index', 'read.dta', 'read.epiinfo',
			'read.fwf', 'read.mtp', 'read.socket', 'read.spss', 'read.ssd',
			'read.xport', 'readBin', 'readline', 'readLines', 'remove.packages',
			'Rprof', 'save', 'savehistory', 'scan', 'screen',
			'seek', 'setwd', 'showConnection', 'sink', 'sink.number',
			'socketConnection', 'source', 'split.screen', 'stderr', 'stdin',
			'stdout', 'sys.call', 'sys.calls', 'sys.frame', 'sys.frames',
			'sys.function', 'Sys.getenv', 'Sys.getlocale', 'Sys.info', 'sys.nframe',
			'sys.on.exit', 'sys.parent', 'sys.parents', 'Sys.putenv', 'Sys.sleep',
			'Sys.source', 'sys.source', 'sys.status', 'Sys.time', 'system',
			'system.file', 'tempfile', 'textConnection', 'tkpager', 'tkStartGUI',
			'unlink', 'unz', 'update.packages', 'url', 'url.show',
			'vi', 'write', 'write.dta', 'write.ftable', 'write.socket',
			'write.table', 'writeBin', 'writeLines', 'x11', 
			'xedit', 'xemacs', 'xfig', 'zip.file.extract',
			'readdataSK', 'biocLite',
			'runApp', 'runExample', 'runGist', 'runGitHub', 'runUrl', # from Shiny
			'png', 'jpeg', 'pdf',
			'get', 'rgl.init', # Suggested by Carlos J. Gil Bellosta , and Miguel Angel Rodriguez Muinos from list r-help-es
			'call', 'eval',	 # added by suggestion of M. Cassin 
			'paste',	 # added by suggestion of Philippe Grosjean from Numerical Ecology of Aquatic Systems, Mons University, Belgium 
			'ggsave' );	 # from ggplot: http://rgm2.lab.nig.ac.jp/RGM2/R_man-2.9.0/library/ggplot2/man/ggsave-ao.html
	$found = array();

	foreach( $banned as $suspected ) {
		if (false !== strpos($input, $suspected)) {
			// okay, we found something forbidden, now we need a regular expression to check if it is a function call like 'name  (', 'name =' or 'name.' !
			// Strictly speaking, this would be pattern '/\b' . preg_quote($suspected,'/') . '([\W]*\(|[\W]*\=|[\W]*\.)/'; but let's also forbid 'name .' until confirmation
			$pattern = '/\b' . preg_quote($suspected,'/') . '([\W]*\(|[\W]*\=|[\W]*\.)/';
			if (preg_match ($pattern, $input)) {
				$found[] = $suspected;
			}
		}
	}

	return $found;
}

function displayRrErrorBox($type='errors',$title,$body) {
	$error_text = '~/np~{REMARKSBOX(type="' . $type . '" title="Plugin R/RR: ' . $title . '")}' . $body . '{REMARKSBOX}~np~';
	return $error_text;
}

/* Either returns the full path to the command, of FALSE if not found.
 * Works on flavors of UNIX
 */
function getCommandPath($command, $options = '') {
	$classicPaths = array('/usr/bin/', '/usr/local/bin/', '/bin/');
	foreach ($classicPaths as $path) {
		if (file_exists($path . $command)) {
			return $path . $command . $options;
		}
	}
	$commandPath = `which $command`;
	if (empty($commandPath)) {
		return FALSE;
	} else {
		return $commandPath . $options;
	}
}

function userInFilename($user,$cachestrategy) {
	// Only insert user identification in filenames when "per user" caching strategy is chosen
	if ( $cachestrategy == "peruser" ) {
		//Convert spaces and @ into some character to avoid R complaining because it can't create such file on disk in the server
		// Also prefix with user id to ensure uniqueness
		$userid = UsersLib::get_user_id($user);
		$userinfilename = "user$userid_" . preg_replace('/[^a-zA-Z0-9]/', "_", $user) . '_';
	} else {
		$userinfilename = '';
	}
	return $userinfilename;
}
