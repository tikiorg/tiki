<?php // $Id$

// heaviled modified get_strings.php
// dedicated as a tool for use in an eventual test suite
// mose@tikiwiki.org

require_once('tiki-setup.php');
if($tiki_p_admin != 'y') {
		if ($prefs['feature_redirect_on_error'] == 'y') {
		header('location: '.$prefs['tikiIndex']);
		die;
	} else {
	  die("You need to be admin to run this script");
		}
}
$logfile = 'temp/tiki_parsed.txt';
$logfilehtml = 'temp/tiki_parsed.html';

function collect($dir) {
  global $dirs;
  if (is_dir($dir) and is_dir("$dir/CVS")) {
		$list = file("$dir/CVS/Entries");
		foreach ($list as $l) {
			// if (count($dirs) > 20) return true;
			if (strstr($l,'/')) {
				$s = split('/',rtrim($l));
				$filepath = $dir.'/'.$s[1];
				if ($s[0] == 'D') {
					collect($filepath);
					
					$dirs["$dir"][] = $s[1];
					$dirs["$dir"]['FILES'] = array();
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
						$dirs["$dir"]['FILES'] = $files;
					}
				}
			}
		}
	}
}

function echoline($fd, $fx, $outstring, $style='', $mod='', $br=true) {
	if ($br) {
		$br = "\n";
	} else {
		$br = '';
	}
  fwrite ($fd, $outstring.$br);
	if ($mod == 'd') {
		$outstring = date('D M d H:m:s Y',trim($outstring));
	}
	if ($style == 'eob') {
		$htmlstring = "</div>";
	} elseif ($style) {
		if ($style == 'dir') {
			$htmlstring = "<span class='$style' onclick=\"javascript:toggle('".$outstring."');\">". sprintf("    %-16s : ",$style). htmlspecialchars($outstring)."</span>";
			$htmlstring.= "<div class='box' id='".$outstring."'>";
			$br = '';
		} else { 
			$htmlstring = "<span class='$style'>". sprintf("    %-16s : ",$style). htmlspecialchars($outstring)."</span>";
		}
	} else {
		$htmlstring = htmlspecialchars($outstring);
	}
  fwrite ($fx, $htmlstring.$br);
}
$display = 'none';
if (isset($_REQUEST['all'])) $display = 'block';
?>
<html><head><style>
pre { padding : 10px; border: 1px solid #666666; background-color: #efefef; }
.dir { font-weight : bold; background-color: #ffffff; cursor : pointer; }
.box { padding : 10px; border : 1px solid #999999; background-color: #f6f6f6; display : <?php echo $display ?>; }
.file { font-weight : bold; }
.php { background-color: #AACCFF; }
.smarty { background-color: #FFccAA; }
.other { background-color: #cccccc; }
.image { background-color: #aaffcc; }
.sub { padding-left : 20px; font-size : 80%; }
.var { background-color: #FFFFAA; } 
.url { background-color: #FFAAAA; } 
.action { background-color: #AACCFF; } 
.form { background-color: #AABBFF; } 
.atime, .ctime, .mtime, .date { background-color: #dedede; } 
.size, .rev, .tag { background-color: #ededed; } 
</style><script type="text/javascript" src="lib/tiki-js.js"></script></head>
<body><form action="parse_tiki.php" method="post"><input type="submit" name="action" value="process" /></form>
<a href="<?php echo $logfile; ?>">raw report</a>
<pre>
<?php
if (isset($_POST['action'])) {
	$files = $dirs = array();
	collect('.');
  @unlink ($logfile);
  $fw = fopen($logfile,'w');
  $fx = fopen($logfilehtml,'w');
	foreach ($dirs as $dir=>$params) {
		$dirname = basename($dir);
		$path = dirname($dir);
		echoline($fw,$fx,$dir,'dir');
		echoline($fw,$fx,'');
		if (isset($dirs["$dir"]['FILES'])) {
			foreach ($dirs["$dir"]['FILES'] as $file=>$params) {
				$fp = fopen ($file, "r");
				$data = fread ($fp, filesize ($file));
				fclose ($fp);
				$requests = array();
				$urls = array();
				if (preg_match("/\.(tpl|ph(p|tml))$/", $file)) {
					if (preg_match("/\.ph(p|tml)$/", $file)) {	
						echoline($fw,$fx, $file,"file php");
						$data = preg_replace ("/(?s)\/\*.*?\*\//", "", $data);  // C comments
						$data = preg_replace ("/(?m)^\s*\/\/.*\$/", "", $data); // C++ comments
						$data = preg_replace ("/(?m)^\s*\#.*\$/",   "", $data); // shell comments
						$data = preg_replace('/(\r|\n)/', '', $data); // all one line
						preg_match_all('/\$_(REQUEST|POST|GET|COOKIE|SESSION)\[([^\]]*)\]/', $data, $requests); // requests uses
						$max = count($requests[0]);
						for ($i=0;$i<$max;$i++) {
							echoline($fw,$fx,$requests[1][$i]." = ".$requests[2][$i],'sub var'); 
						}
					} elseif (preg_match ("/\.tpl$/", $file)) {
						echoline($fw,$fx,$file,'file smarty');
						$data = preg_replace('/(?s)\{\*.*?\*\}/', '', $data); // Smarty comment 
						$data = preg_replace('/(\r|\n)/', '', $data); // all one line 
					}
					preg_match_all('/<(a[^>]*)>[^<]*<\/a>/im', $data, $urls); // href links
					foreach ($urls[1] as $u) {
						echoline($fw,$fx,$u,'sub url'); 
					}
					preg_match_all('/<(form[^>]*)>/', $data, $forms); // form uses
					foreach ($forms[1] as $f) {
						echoline($fw,$fx,$f,'sub action'); 
					}
					preg_match_all('/<((input|textarea|select)[^>]*)>/', $data, $elements); // form elements uses
					$max = count($elements[0]);
					for ($i=0;$i<$max;$i++) {
						echoline($fw,$fx,$elements[1][$i],'sub form'); 
					}
					echoline($fw,$fx,trim($params['atime']),'sub atime','d');
					echoline($fw,$fx,trim($params['mtime']),'sub mtime','d');
					echoline($fw,$fx,trim($params['ctime']),'sub ctime','d');
					echoline($fw,$fx,trim($params['date']),'sub date');
					echoline($fw,$fx,trim($params['size']),'sub size');
					echoline($fw,$fx,trim($params['rev']),'sub rev');
					echoline($fw,$fx,substr(trim($params['tag']),1),'sub tag');
				} elseif (preg_match ("/\.(gif|jpg|png)$/i", $file)) {
					echoline($fw,$fx,$file,'file image');
				} else {
					echoline($fw,$fx,$file,'file other');
				}
				echoline($fw,$fx,'');
				flush();
			}
		}
		echoline($fw,$fx,'end of box','eob');
	}
	fclose($fw);
	fclose($fx);
}
if (is_file($logfilehtml)) {
	readfile($logfilehtml);
}
?>
</pre>
</body></html>
