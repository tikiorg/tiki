<?php
/** 
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 12/02/2004
* @copyright (C) 2005 the Tiki community
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
* 
* Docbook and OpenOffice lib
* 
*  */
include_once ("lib/tikidav/ziplib.php");
include_once ('lib/wiki/wikilib.php');
include_once ('lib/mime/mimelib.php');
include_once ("lib/init/initlib.php");
include_once ('lib/tikidav/docbooklib.php');

class DocBookLib extends TikiLib {
	function DocBookLib($db) {
		$this->TikiLib($db);
	}

	function transforma($data, $xslFile) {
		// Create a new processor handle
		$th = xslt_create() or die("Can't create XSLT handle!");

		// Open the XSL files
		$sh = fopen($xslFile, "r") or die("Can't open XSL file");

		// Read in the XSL contents
		$xslContent = fread($sh, filesize($xslFile));

		// Perform the XSL transformation
		$XSLtransformation = "";

		$XSLtransformation = xslt_process($th, 'arg:/_xml', 'arg:/_xsl', NULL, array ("/_xml" => $data, "/_xsl" => $xslContent));

		// Free up the resources
		@ xslt_free($th);

		return $XSLtransformation;
	}

	function docbook_to_wiki($docbookData) {
		$xslFile = "lib/tikidav/DocBook2TikiWiki.xslt";
		return $this->transforma($docbookData, $xslFile);
	}

	function openoffice_to_wiki($OOoData) {
		if (!file_exists("./temp/OOo"))
			mkdir("./temp/OOo");
		$zipName = getcwd()."/temp/OOo/".md5($OOoData).".zip";
		$zipfileTmp = fopen($zipName, "wr");
		fwrite($zipfileTmp, $OOoData);
		fclose($zipfileTmp);

		$zipfile = zip_open($zipName);
		$entradaZip = null;
		$contentXML = "";
		$stylesXML = "";
		$attachments = array ();
		do {
			$entradaZip = zip_read($zipfile);
			if ($entradaZip != FALSE) {
				if (zip_entry_name($entradaZip) == "content.xml") {
					$contentXML = $this->readZipEntry($zipfile, $entradaZip);
				} else
					if (zip_entry_name($entradaZip) == "styles.xml") {
						$stylesXML = $this->readZipEntry($zipfile, $entradaZip);
					} else
						if (strpos(zip_entry_name($entradaZip), "Pictures/") === 0) {
							$data = $this->readZipEntry($zipfile, $entradaZip);
							$attachments[zip_entry_name($entradaZip)]["data"] = $data;
							$attachments[zip_entry_name($entradaZip)]["size"] = zip_entry_filesize($entradaZip);
							$attachments[zip_entry_name($entradaZip)]["type"] = tiki_get_mime(zip_entry_name($entradaZip));
						}
			}
		} while ($entradaZip != FALSE);

		$xslFile = "lib/tikidav/OOo2TikiWiki.xslt";
		$contentXML = $this->pasteStyles($contentXML, $stylesXML);

		$resp = array ();
		$resp["wikidata"] = $this->transforma($contentXML, $xslFile);
		$resp["OOoContent"] = $contentXML;
		$resp["OOoStyles"] = $stylesXML;
		$resp["pictures"] = $attachments;
		
		zip_close($zipfile);
		unlink($zipName);
		return $resp;
	}

	function attachFiles($pageid, $attachments, $user) {
		foreach ($attachments as $key => $attach) {
			$fhash = "";
			//TODO: sacar de la configuracion el tipo de almacenamiento de adjuntos
			/*$w_use_dir=$tikilib->get_preference('w_use_db', 'y');
			if($w_use_db == 'n') {
				$fhash = md5($key);
				}*/
			$this->remove_wiki_attachment_byname($pageid, $key);
			global $wikilib;
			$wikilib->wiki_attach_file($pageid, $key, $attach["type"], $attach["size"], $attach["data"], "TikiDav attachment", $user, $fhash);
		}
	}

	function list_wiki_attachments($page, $find) {

		if ($find) {
			$mid = " where `page`=? and (`filename` like ?)"; // why braces?
			$bindvars = array ($page, "%".$find."%");
		} else {
			$mid = " where `page`=? ";
			$bindvars = array ($page);
		}

		$query = "select `user`,`attId`,`page`,`filename`,`filesize`,`filetype`,`downloads`,`created`,`comment`,`data` from `tiki_wiki_attachments` $mid ";
		$query_cant = "select count(*) from `tiki_wiki_attachments` $mid";
		$result = $this->query($query, $bindvars);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array ();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array ();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_wiki_attachmentByName($attName) {
		$query = "select * from `tiki_wiki_attachments` where `filename`=?";
		$result = $this->query($query, array ($attName));
		if (!$result->numRows())
			return false;
		$res = $result->fetchRow();

		return $res;
	}

	function remove_wiki_attachment_byname($pageName, $attachName) {
		global $w_use_dir;

		$path = $this->getOne("select `path` from `tiki_wiki_attachments` where `page`='$pageName' and `filename`='$attachName'");

		if ($path) {
			@ unlink($w_use_dir.$path);
		}

		$query = "delete from `tiki_wiki_attachments` where `page`='$pageName' and `filename`='$attachName'";
		$result = $this->query($query);
	}

	function pasteStyles($content, $styles) {
		$newContent = "";
		$iniStyles = strpos($styles, "<office:styles>") + strlen("<office:styles>");
		$finStyles = strpos($styles, "</office:styles>");
		$autoStyles = substr($styles, $iniStyles, $finStyles - $iniStyles);
		
		
		$finContentStyles = strpos($content, "</office:automatic-styles>");

		if (isset($finContentStyles) && $finContentStyles!=""){
			$newContent = substr($content, 0, $finContentStyles).$autoStyles.substr($content, $finContentStyles, strlen($content));
		}else{
			$finContentStyles = strpos($content, "<office:automatic-styles/>");
			$newContent = substr($content, 0, $finContentStyles)."<office:automatic-styles>".$autoStyles."</office:automatic-styles>".substr($content, $finContentStyles+strlen("<office:automatic-styles/>"), strlen($content));
		}
		return $newContent;
	}

	function readZipEntry($zipfile, $zipEntry) {
		zip_entry_open($zipfile, $zipEntry);
		//echo "longitud:".zip_entry_filesize($entradaZip);
		$data = "";
		do {
			$contentTMP = zip_entry_read($zipEntry);
			$data .= $contentTMP;
		} while ($contentTMP != FALSE);
		return $data;
	}

	function docbook_to_openoffice($docbookData) {
		$xslFile = "lib/tikidav/OOo2TikiWiki.xslt";
		return $this->transforma($OOoData, $xslFile);
	}

	function parse_openOffice_data($pageName, $data, $xmlHead = false) {
		$docbookData = $this->parse_docbook_data($data, $xmlHead);
		$xslFile = "lib/tikidav/docbooktosoffheadings.xslt";
		$OOoContent = $this->transforma($docbookData, $xslFile);
		$attachments = $this->list_wiki_attachments($pageName, "Pictures/%");

		//create sxw zip file
		$ziptmp = new ZipWriter("", "OOo.zip");
		$ziptmp->addRegularFile("content.xml", $OOoContent, false);

		//Add META-INF
		$ziptmp->addRegularFile("META-INF/manifest.xml", $this->createManifest(), false);

		foreach ($attachments["data"] as $key => $attach) {
			$ziptmp->addRegularFile($attach["filename"], $attach["data"], false);
		}
		return $ziptmp->finish();
	}

	function createManifest(){
		$manifest = '<?xml version="1.0" encoding="UTF-8"?>';
		$manifest .= '<!DOCTYPE manifest:manifest PUBLIC "-//OpenOffice.org//DTD Manifest 1.0//EN" "Manifest.dtd">';
		$manifest .= '<manifest:manifest xmlns:manifest="http://openoffice.org/2001/manifest">';
		$manifest .= ' <manifest:file-entry manifest:media-type="application/vnd.sun.xml.writer" manifest:full-path="/"/>';
		$manifest .= ' <manifest:file-entry manifest:media-type="application/vnd.sun.xml.ui.configuration" manifest:full-path="Configurations2/"/>';
		$manifest .= ' <manifest:file-entry manifest:media-type="" manifest:full-path="Pictures/"/>';
		$manifest .= ' <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="content.xml"/>';
		$manifest .= ' <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="styles.xml"/>';
		$manifest .= ' <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="meta.xml"/>';
		$manifest .= ' <manifest:file-entry manifest:media-type="" manifest:full-path="Thumbnails/thumbnail.png"/>';
		$manifest .= ' <manifest:file-entry manifest:media-type="" manifest:full-path="Thumbnails/"/>';
		$manifest .= ' <manifest:file-entry manifest:media-type="text/xml" manifest:full-path="settings.xml"/>';
		$manifest .= '</manifest:manifest>';
		return $manifest;
	}
	
	
	function parse_docbook_data($data, $xmlHead = false) {
		global $page_regex;

		global $slidemode;
		global $feature_hotwords;
		global $cachepages;
		global $ownurl_father;
		global $feature_drawings;
		global $tiki_p_admin_drawings;
		global $tiki_p_edit_drawings;
		global $tiki_p_edit_dynvar;
		global $feature_wiki_pictures;
		global $tiki_p_upload_picture;
		global $feature_wiki_plurals;
		global $feature_wiki_tables;
		global $page;
		global $page_ref_id;
		global $rsslib;
		global $dbTiki;
		global $structlib;
		global $user;
		global $tikidomain;
		global $feature_wikiwords;
		global $feature_wikiwords_usedash;

		$noparsedlinks = array ();

		// This section matches [...].
		// Added handling for [[foo] sections.  -rlpowell
		preg_match_all("/(?<!\[)\[([^\[][^\]]+)\]/", $data, $noparseurl);

		foreach (array_unique($noparseurl[1]) as $np) {
			$key = md5($this->genPass());

			$aux["key"] = $key;
			$aux["data"] = $np;
			$noparsedlinks[] = $aux;
			$data = str_replace("$np", $key, $data);
		}

		// Replace special characters
		//done after url catching because otherwise urls of dyn. sites will be modified
		//$this->parse_htmlchar($data);
		$data = htmlspecialchars($data);
		// Now replace a TOC
		//preg_match_all("/\{toc\s?(order=(desc|asc))?\s?(showdesc=(0|1))?\s?(shownum=(0|1))?\s?\}/i", $data, $tocs);

		// Now search for images uploaded by users
		if ($feature_wiki_pictures == 'y') {
			preg_match_all("/\{picture file=([^\}]+)\}/", $data, $pics);

			for ($i = 0; $i < count($pics[0]); $i ++) {
				// Check if the image exists
				$name = $pics[1][$i];

				$repl = '<inlinegraphic fileref="'.$name.'" />';

				// Replace by $repl
				$data = str_replace($pics[0][$i], $repl, $data);
			}
		}

		//REYES
		//Super Text
		$data = preg_replace("/\^\^(.*?)\^\^/", "<superscript>$1</superscript>", $data);
		//Sub Text
		$data = preg_replace("/\^_(.*?)_\^/", "<subscript>$1</subscript>", $data);

		// Replace boxes
		//$data = preg_replace("/\^([^\^]+)\^/", "<div class=\"simplebox\">$1</div>", $data);
		$data = preg_replace("/\^([^\^]+)\^/", "<note><para>$1</para></note>", $data);
		// Replace colors ~~color:text~~
		$data = preg_replace("/\~\~([^\:]+):([^\~]+)\~\~/", "<remark style=\"color:$1;\">$2</remark>", $data);
		// Underlined text
		$data = preg_replace("/===([^\=]+)===/", "<quote>$1</quote>", $data);
		// Center text
		$data = preg_replace("/::(.+?)::/", "<blockquote><para>$1</para></blockquote>", $data);

	   // reinsert hash-replaced links into page
	    foreach ($noparsedlinks as $np) {
		$data = str_replace($np["key"], $np["data"], $data);
	    }
    
		// Replace ))Words((
		//$data = preg_replace("/\(\(([^\)]+)\)\)/", "$1", $data);

		// Images
		preg_match_all("/(\{img [^\}]+})/", $data, $pages);

		foreach (array_unique($pages[1]) as $page_parse) {
			$parts = explode(" ", $page_parse);

			$imgdata = array ();
			$imgdata["src"] = '';
			$imgdata["height"] = '';
			$imgdata["width"] = '';
			$imgdata["link"] = '';
			$imgdata["align"] = '';
			$imgdata["desc"] = '';

			foreach ($parts as $part) {
				$part = str_replace('}', '', $part);

				$part = str_replace('{', '', $part);
				$part = str_replace('\'', '', $part);
				$part = str_replace('"', '', $part);

				if (strstr($part, '=')) {
					$subs = explode("=", $part, 2);

					$imgdata[$subs[0]] = $subs[1];
				}
			}

			$repl = '<inlinegraphic fileref="'.str_replace("./tiki-dowload_wiki_attachmentOOo.php?attName=", "#", $imgdata["src"]).'" ';
			//TODO: Change the pixel to cm transformation for OOo
			if ($imgdata["width"])
				$repl .= ' width="'. ($imgdata["width"] / 28.38).'cm"';

			if ($imgdata["height"])
				$repl .= ' height="'. ($imgdata["height"] / 28.38).'cm"';

			if ($imgdata["align"]) {
				$repl .= ' align="'.$imgdata["align"].'"';
			}

			$repl .= ' />';

			if ($imgdata["link"]) {
				$repl = '<ulink url="'.$imgdata["link"].'">'.$repl.'</ulink>';
			}

			if ($imgdata["desc"]) {
				$repl = '<figure><title>'.$imgdata["desc"].'</title>'.$repl.'</figure>';
			} else {
				$repl = '<figure><title>Figure</title>'.$repl.'</figure>';
			}

			$data = str_replace($page_parse, $repl, $data);
		}

		$links = $this->get_links($data);
		$notcachedlinks = $this->get_links_nocache($data);

		$cachedlinks = array_diff($links, $notcachedlinks);

		$this->cache_links($cachedlinks);


		// Note that there're links that are replaced
		foreach ($links as $link) {

			if (!isset ($_SERVER["SERVER_NAME"])) {
				$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
			}

			$link2 = str_replace("/", "\/", preg_quote($link));

			$pattern = "/(?<!\[)\[$link2\|([^\]\|]+)([^\]])*\]/";
			$data = preg_replace($pattern, "<ulink url='$link'>$1</ulink>", $data);
			$pattern = "/(?<!\[)\[$link2\]/";
			$data = preg_replace($pattern, "<ulink url='$link'>$link</ulink>", $data);
		}

		// Handle double square brackets.  -rlpowell
		$data = str_replace("[[", "[", $data);

		if ($feature_wiki_tables != 'new') {
			// New syntax for tables
			if (preg_match_all("/\|\|(.*)\|\|/", $data, $tables)) {
				$maxcols = 1;

				$cols = array ();

				for ($i = 0; $i < count($tables[0]); $i ++) {
					$rows = explode('||', $tables[0][$i]);

					$col[$i] = array ();

					for ($j = 0; $j < count($rows); $j ++) {
						$cols[$i][$j] = explode('|', $rows[$j]);

						if (count($cols[$i][$j]) > $maxcols)
							$maxcols = count($cols[$i][$j]);
					}
				}

				for ($i = 0; $i < count($tables[0]); $i ++) {
					$repl = '<informaltable><tgroup><tbody>';

					for ($j = 0; $j < count($cols[$i]); $j ++) {
						$ncols = count($cols[$i][$j]);

						if ($ncols == 1 && !$cols[$i][$j][0])
							continue;

						$repl .= '<row>';

						for ($k = 0; $k < $ncols; $k ++) {
							$repl .= '<entry ';

							if ($k == $ncols -1 && $ncols < $maxcols)
								$repl = $repl."namest=\"c$ncols\" nameend=\"c". ($ncols + ($maxcols - $k))."\"";

							$repl .= '><para>'.$cols[$i][$j][$k].'</para></entry>';
						}

						$repl .= '</row>';
					}

					$repl .= '</tbody></tgroup></informaltable>';
					$data = str_replace($tables[0][$i], $repl, $data);
				}
			}
		} else {
			// New syntax for tables
			// REWRITE THIS CODE
			if (preg_match_all("/\|\|(.*?)\|\|/s", $data, $tables)) {
				$maxcols = 1;

				$cols = array ();

				for ($i = 0; $i < count($tables[0]); $i ++) {
					$rows = split("\n|\<br\/\>", $tables[0][$i]);

					$col[$i] = array ();

					for ($j = 0; $j < count($rows); $j ++) {
						$rows[$j] = str_replace('||', '', $rows[$j]);

						$cols[$i][$j] = explode('|', $rows[$j]);

						if (count($cols[$i][$j]) > $maxcols)
							$maxcols = count($cols[$i][$j]);
					}
				}

				for ($i = 0; $i < count($tables[0]); $i ++) {
					$repl = '<informaltable><tgroup><tbody>';

					for ($j = 0; $j < count($cols[$i]); $j ++) {
						$ncols = count($cols[$i][$j]);

						if ($ncols == 1 && !$cols[$i][$j][0])
							continue;

						$repl .= '<row>';

						for ($k = 0; $k < $ncols; $k ++) {
							$repl .= '<entry ';

							if ($k == $ncols -1 && $ncols < $maxcols) {
								$repl = $repl."namest=\"c$ncols\" nameend=\"c". ($ncols + ($maxcols - $k))."\"";
							}
							$repl .= '>'.$cols[$i][$j][$k].'</entry>';
						}

						$repl .= '</row>';
					}

					$repl .= '</tbody></tgroup></informaltable>';
					$data = str_replace($tables[0][$i], $repl, $data);
				}
			}
		}

		// 26-Jun-2003, by zaufi
		//
		// {maketoc} --> create TOC from '!', '!!', '!!!' in current document
		//
		preg_match_all("/\{maketoc\}/", $data, $tocs);
		$anch = array ();

		// 08-Jul-2003, by zaufi
		// HotWords will be replace only in ordinal text
		// It looks __realy__ goofy in Headers or Titles

		// Get list of HotWords
		//$words = $this->get_hotwords();

		// Now tokenize the expression and process the tokens
		// Use tab and newline as tokenizing characters as well  ////
		$lines = explode("\n", $data);
		$data = '';
		$listbeg = array ();
		$divdepth = array ();
		$inTable = 0;

		// loop: process all lines
		foreach ($lines as $line) {

			// Check for titlebars...
			// NOTE: that title bar should be start from begining of line and
			//	   be alone on that line to be autoaligned... else it is old styled
			//	   styled title bar...

			if (substr(ltrim($line), 0, 2) == '-=' && substr(rtrim($line), -2, 2) == '=-') {
				// This is not list item -- must close lists currently opened
				/*    while (count($listbeg))
					$data .= array_shift($listbeg);
				*/
				$line = trim($line);
				$line = '<bridgehead>'.substr($line, 2, strlen($line) - 4).'</bridgehead>';
				$data .= $line;
				continue;
			}

			// check if we are inside a table, if so, ignore monospaced and do
			// not insert <br/>
			$inTable += substr_count(strtolower($line), "<informaltable");
			$inTable -= substr_count(strtolower($line), "</informaltable");

			// If the first character is ' ' and we are not in pre then we are in pre
			global $feature_wiki_monosp;

			if (substr($line, 0, 1) == ' ' && $feature_wiki_monosp == 'y' && $inTable == 0) {
				// This is not list item -- must close lists currently opened
				while (count($listbeg))
					$data .= array_shift($listbeg);

				// If the first character is space then
				// change spaces for &nbsp;
				$line = '<literallayout>'.$line.'</literallayout>';
			}

			// Replace monospaced text
			$line = preg_replace("/-\+(.*?)\+-/", "<literallayout>$1</literallayout>", $line);
			// Replace bold text
			$line = preg_replace("/__(.*?)__/", "<emphasis role=\"bold\">$1</emphasis>", $line);
			$line = preg_replace("/\'\'(.*?)\'\'/", "<emphasis>$1</emphasis>", $line);
			// Replace definition lists
			$line = preg_replace("/^;(.*):[^\/\/](.*)/", "<dl><dt>$1</dt><dd>$2</dd></dl>", $line);


			// This line is parseable then we have to see what we have
			if (substr($line, 0, 3) == '---') {
				// This is not list item -- must close lists currently opened
				while (count($listbeg))
					$data .= array_shift($listbeg);

				$line = "<para>$line</para>";
			} else {
				$litype = substr($line, 0, 1);

				if ($litype == '*' || $litype == '#') {
					$listlevel = $this->how_many_at_start($line, $litype);

					$liclose = '</listitem>';
					$addremove = 0;

					if ($listlevel < count($listbeg)) {
						while ($listlevel != count($listbeg))
							$data .= array_shift($listbeg);

						if (substr(current($listbeg), 0, 11) != '</listitem>')
							$liclose = '';
					}
					elseif ($listlevel > count($listbeg)) {
						$listyle = '';

						while ($listlevel != count($listbeg)) {
							array_unshift($listbeg, ($litype == '*' ? '</itemizedlist>' : '</orderedlist>'));
							$data .= ($litype == '*' ? "<itemizedlist>" : "<orderedlist>");
						}

						$liclose = '';
					}

					if ($litype == '*' && !strstr(current($listbeg), '</itemizedlist>') || $litype == '#' && !strstr(current($listbeg), '</orderedlist>')) {
						$data .= array_shift($listbeg);
						$data .= ($litype == '*' ? "<itemizedlist>" : "<orderedlist>");
						$liclose = '';
						array_unshift($listbeg, ($litype == '*' ? '</listitem></itemizedlist>' : '</listitem></orderedlist>'));
					}

					$line = $liclose.'<listitem><para>'.substr($line, $listlevel + $addremove).'</para>';

					if (substr(current($listbeg), 0, 11) != '</listitem>')
						array_unshift($listbeg, '</listitem>'.array_shift($listbeg));
				}
				elseif ($litype == '+') {
					// Must append paragraph for list item of given depth...
					$listlevel = $this->how_many_at_start($line, $litype);

					// Close lists down to requested level
					while ($listlevel < count($listbeg))
						$data .= array_shift($listbeg);

					if (count($listbeg)) {
						if (substr(current($listbeg), 0, 11) != '</listitem>') {
							array_unshift($listbeg, '</listitem>'.array_shift($listbeg));

							$liclose = '<listitem><para>';
						} else
							$liclose = '';
					} else
						$liclose = '';

					$line = $liclose."<para>$line</para>";

				} else {
					// This is not list item -- must close lists currently opened
					while (count($listbeg))
						$data .= array_shift($listbeg);

					// Get count of (possible) header signs at start
					$hdrlevel = $this->how_many_at_start($line, '!');

					// If 1st char on line is '!' and its count less than 6 (max in HTML)
					if ($litype == '!' && $hdrlevel > 0 && $hdrlevel <= 6) {
						// OK. Parse headers here...
						$anchor = '';
						$aclose = '';
						$addremove = 0;

						// Close lower level divs if opened
						for (; current($divdepth) >= $hdrlevel; array_shift($divdepth))
							$data .= '</sect'.count($divdepth).'>';
				
						$aclose = '<sect'.$hdrlevel.'>';
						array_unshift($divdepth, $hdrlevel);

						$line = $aclose."<title>".substr($line, $hdrlevel)."</title>";
					}
					elseif (!strcmp($line, "...page...")) {
						// Close lists and divs currently opened
						while (count($listbeg))
							$data .= array_shift($listbeg);

						while (count($divdepth)) {
							$data .= '</sect'.count($divdepth).'>';

							array_shift($divdepth);
						}

						// Leave line unchanged... tiki-index.php will split wiki here
						$line = "<beginpage/>";
					} else {
						// Usual paragraph.
						if ($inTable == 0) {
							$iniTable = strpos($line, "<informaltable>");
							$iniLine = "";

							if ($iniTable === false) {
								$line = '<para>'.$iniTable." ".$line.'</para>';
							} else {
								if ($iniTable && $iniTable > 0) {
									$iniLine = '<para>'.substr($line, 0, $iniTable).'</para>';
								}

								//if (!$iniTable) $iniTable=0;
								$line = $iniLine.substr($line, $iniTable);
							}
						}
					}
				}
			}

			$data .= $line;
		}

		// Close lists may remains opened
		while (count($listbeg))
			$data .= array_shift($listbeg);

		// Close header divs may remains opened
		for ($i = 0; $i < count($divdepth); $i ++)
			$data .= '</sect'. (count($divdepth) - $i).'>';

		if ($xmlHead) {
			$docbookHead = '<?xml version="1.0" encoding="UTF-8"?>';
			//$docbookHead =$docbookHead.'<!DOCTYPE article PUBLIC "-//OASIS//DTD DocBook XML V4.1.2//EN" "http://www.oasis-open.org/docbook/xml/4.1.2/docbookx.dtd">';
			$docbookHead = $docbookHead.'<article lang="en-US">';
			$docbookFoot = '</article>';
			$data = $docbookHead."\n".$data."\n".$docbookFoot;
		}
		return $data;
	}


}
?>