<?php // $Header: /cvsroot/tikiwiki/tiki/parse_tiki.php,v 1.2 2004-03-17 17:06:45 mose Exp $

// heaviled modified get_strings.php
// dedicated as a tool for use in an eventual test suite
// mose@tikiwiki.org

require_once('tiki-setup.php');
if($tiki_p_admin != 'y')   die("You need to be admin to run this script");
$logfile = 'temp/tiki_parsed.txt';

function collect($dir) {
  global $files,$dirs;
  if (is_dir($dir) and is_dir("$dir/CVS")) {
		$list = file("$dir/CVS/Entries");
		foreach ($list as $l) {
			if (strstr($l,'/')) {
				$s = split('/',rtrim($l));
				$filepath = $dir.'/'.$s[1];
				if ($s[0] == 'D') {
					collect($filepath);
					$dirs["$filepath"] = array('files'=>'');
				} else {
					if (is_file($filepath)) {
						$stat = stat($filepath);
						$files["$filepath"]["mtime"] = $stat['mtime']; 
						$files["$filepath"]["ctime"] = $stat['ctime']; 
						$files["$filepath"]["atime"] = $stat['atime']; 
						$files["$filepath"]["size"] = $stat['size']; 
						$files["$filepath"]["rev"] = $s[2]; 
						$files["$filepath"]["date"] = $s[3]; 
						$files["$filepath"]["flags"] = $s[4]; 
						$files["$filepath"]["tag"] = $s[5]; 
						clearstatcache();
					}
				}
			}
		}
	}
}

function echoline($fd, $outstring, $br=true) {
	if ($br) $outstring = rtrim($outstring)."\n";
  print(htmlspecialchars($outstring));
  fwrite ($fd, $outstring);
}
?>
<html><body><form><input type="submit" name="action" value="process" /></form>
<a href="<? echo $logfile; ?>">last logfile</a>
<pre style="padding:10px;border: 1px solid #666666;">
<?
if (isset($_REQUEST['action'])) {
	$files = $dirs = array();
	collect('.');
  @unlink ($logfile);
  $fw = fopen($logfile,'w');
	foreach ($dirs as $dir=>$params) {
		$dirname = basename($dir);
		$path = dirname($dir);
		echoline($fw, "DIR: $dir\n");
	}
  foreach ($files as $file=>$params) {
    $fp = fopen ($file, "r");
    $data = fread ($fp, filesize ($file));
    fclose ($fp);
    $requests = array();
    $urls = array();
    if (preg_match("/\.ph(p|tml)$/", $file)) {	
      echoline($fw, "php file: $file");
      $data = preg_replace ("/(?s)\/\*.*?\*\//", "", $data);  // C comments
      $data = preg_replace ("/(?m)^\s*\/\/.*\$/", "", $data); // C++ comments
      $data = preg_replace ("/(?m)^\s*\#.*\$/",   "", $data); // shell comments
      $data = preg_replace('/(\r|\n)/', '', $data); // all one line
      preg_match_all('/\$_(REQUEST|POST|GET|COOKIE|SESSION)\[([^\]]*)\]/', $data, $requests); // requests uses
			for ($i=0;$i<count($requests[0]);$i++) {
				echoline($fw,"used var : ".$requests[1][$i]." = ".$requests[2][$i]); 
			}
      preg_match_all('/<a[^>]*href=(\'|")([^\'"]*)(\'|")/im', $data, $urls); // href links
			foreach ($urls[2] as $u) {
				echoline($fw,"url = ".$u); 
			}
      preg_match_all('/<form[^>]*action=(\'|")([^\'"]*)(\'|")/', $data, $forms); // form uses
			foreach ($forms[2] as $f) {
				echoline($fw,"form action = ".$f); 
			}
      preg_match_all('/<(input|textarea|select)[^>]*name=(\'|")([^\'"]*)(\'|")/', $data, $elements); // form elements uses
			for ($i=0;$i<count($elements[0]);$i++) {
				echoline($fw,"form = ".$elements[1][$i]." = ".$elements[3][$i]); 
			}
    } elseif (preg_match ("/\.tpl$/", $file)) {
      echoline($fw,"smarty file: $file\n");
      $data = preg_replace('/(?s)\{\*.*?\*\}/', '', $data); // Smarty comment 
      $data = preg_replace('/(\r|\n)/', '', $data); // all one line 
      preg_match_all('/<a[^>]*href=(\'|")([^\'"]*)(\'|")/im', $data, $urls); // href links
			foreach ($urls[2] as $u) {
				echoline($fw,"url = ".$u); 
			}
      preg_match_all('/<form[^>]*action=(\'|")([^\'"]*)(\'|")/', $data, $forms); // form uses
			foreach ($forms[2] as $f) {
				echoline($fw,"form action = ".$f); 
			}
      preg_match_all('/<(input|textarea|select)[^>]*name=(\'|")([^\'"]*)(\'|")/', $data, $elements); // form elements uses
			for ($i=0;$i<count($elements[0]);$i++) {
				echoline($fw,"form = ".$elements[1][$i]." = ".$elements[3][$i]); 
			}
    } elseif (preg_match ("/\.(gif|jpg|png)$/i", $file)) {
      echoline($fw, "image file: $file");
		} else {
      echoline($fw, "other file: $file");
		}
		echoline($fw, "    atime : ". date('D M d H:m:s Y',trim($params['atime'])));
		echoline($fw, "    mtime : ". date('D M d H:m:s Y',trim($params['mtime'])));
		echoline($fw, "    ctime : ". date('D M d H:m:s Y',trim($params['ctime'])));
		echoline($fw, "    date  : ". trim($params['date']));
		echoline($fw, "    size  : ". trim($params['size']));
		echoline($fw, "    rev   : ". trim($params['rev']));
		echoline($fw, "    tag   : ". substr(trim($params['tag']),1));
		flush();
  }
	fclose($fw);
}
?>
</pre>
</body></html>
