<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
	exit;
}

class ModsLib {

	var $feedback;
	var $types;

	function ModsLib() { 
		$this->feedback = array();
		$this->types = array();
	}

	function readconf($file) {
		if (is_file($file)) {
			$fp = fopen($file,"r");
			$next = true;
			$lab = '';
			$out = array();
			while (!feof($fp)) {
				$line = fgets($fp,1024);
				$line = trim($line);
				if ($line) {
					if (substr($line,0,1) != '#') {
						if ($next) {
							$lab = trim(strtr(strtolower($line),':',' '));
							$next = false;
							$localkey = false;
						} else {
							if (substr($lab,-7,7) == 'upgrade') {
								if (substr($line,0,1) == ':') {
									$localkey = trim(substr($line,1));
								} elseif ($localkey) {
									$out["$lab"]["$localkey"][] = trim($line);
								} else {
									$out["$lab"][] = trim($line);
								}
							} elseif ($lab == 'configuration') {
								$out["$lab"][] = split(',',trim($line));
							} elseif ($lab == 'files') {
								$out["$lab"][] = split(' +',trim($line));
							} elseif ($lab == 'contributor' or $lab == 'revision' or $lab == 'lastmodif') {
								$out["$lab"][] = trim(preg_replace('/\$[^:]*:([^\$]*)\$/',"$1",trim($line)));
							} else {
								$out["$lab"][] = trim($line);
							}
						}
					}
				} else {
					$next = true;
				}
			}
			fclose($fp);
			return $out;
		} else {
			$this->feedback[] = array('num'=>1,'mes'=>sprintf(tra('File %s not found'),$file));
			return false;
		}
	}

	function prepare_dir($path) {
		if ($path and $path != '/' and !is_dir(dirname($path))) $this->prepare_dir(dirname($path));
		if (!is_dir($path) and substr($path,0,1) != '.') mkdir($path,02777);
	}

	function dl_remote($remote,$file,$local) {
		$meat = $this->get_remote($remote."/Dist/".$file.".tgz");
		if ($meat) {
			$localfile = $local."/Cache/".$file.".tgz";
			if (is_file($localfile)) unlink($localfile);
			$fp = fopen($localfile,"wb");
			fwrite($fp,$meat);
			fclose($fp);
			require("lib/tar.class.php");
			$tar = new tar;
			if ($tar->openTAR($localfile)) {
				foreach ($tar->files as $f) {
					$this->prepare_dir(dirname($local.'/'.$f['name']));
					$fp = fopen($local.'/'.$f['name'],"wb");
					fwrite($fp,$f['file'],$f['size']);
					fclose($fp);
				}
			} else {
				$this->feedback[] = array('num'=>1,'mes'=>sprintf(tra('File %s is not a valid archive'),$localfile));
				return false;
			}
		} else {
			$this->feedback[] = array('num'=>1,'mes'=>sprintf(tra('%s is an empty archive file'),$file));
			return false;
		}
	}

	function get_remote($url) {
		$buffer = $fp = '';
		$u = parse_url($url);
		if (!$u['path']) $u['path'] = '/';
		$fp = @fsockopen($u['host'],80,$errno,$errmsg,30);
		if ($fp) {
			fwrite($fp,"GET ".$u['path']." HTTP/1.0\nHost: ".$u['host']."\nConnection: close\n\n");
			while ($buf = fread($fp,1024)) {
				$buffer.= $buf;
			}
			fclose($fp);
			if (preg_match('/Content-Length: ([0-9]+)/', $buffer, $parts)) {
				$buffer = substr($buffer, -$parts[1]);
				return $buffer;
			} else {
				$this->feedback[] = array('num'=>1,'mes'=>sprintf(tra('Invalid remote file on url %s'),$url));
				return false;
			}
		} else {
			$this->feedback[] = array('num'=>1,'mes'=>sprintf(tra('Impossible to open %s : %s'),$url,$errmsg));
			return false;
		}
	}

	function refresh_remote($remote,$local) {
		$buffer = $this->get_remote($remote);
		if ($buffer) {
			$fl = fopen($local,'w');
			fputs($fl,$buffer);
			fclose($fl);
		} else {
			return false;
		}
	}

	function publish($path,$public,$items=array(),$add=true) {
		$fp = fopen($path.'/00_list.public.txt',"w");
		foreach ($public as $type=>$meat) {
			foreach ($meat as $p) {
				if (in_array($p['modname'],$items)) {
					$item = '';
					if ($add) {
						fputs($fp,$p['literal']);
					}
				} else {
					fputs($fp,$p['literal']);
				}
			}
		}
		if (count($items) and $add) {
			foreach ($items as $item) {
				$detail = $this->readconf($path.'/'.$item.'.info.txt');
				$pos = strpos($item,'-');
				$type = substr($item,0,$pos);
				$name = substr($item,$pos+1);
				if ($name) {
					$buf = "'". addslashes($type) ."','";
					$buf.= addslashes($name) ."','";
					$buf.= addslashes($detail['revision'][0]) ."','";
					$buf.= addslashes(implode(" ",$detail['description'])) ."','";
					$buf.= addslashes($detail['licence'][0]) ."','";
					if (isset($detail['version'])) $buf.= addslashes($detail['version'][0]) ."','";
					$md5 = $this->package(dirname($path),$type,$name);
					$buf.= addslashes($md5) ."'\n";
					fputs($fp,$buf);
				}
			}
		}
		fclose($fp);
	}
	
	function unpublish($path,$public,$items) {
		$this->publish($path,$public,$items,false);
	}

	function scan_dist($dir) {
		$back = array();
		$h = opendir($dir);
		while ($file = readdir($h)) {
			$file = basename($file);
			if (substr($file,-4,4) == '.tgz') {
				$lim = strrpos($file,'-');
				$name = substr($file,0,$lim);
				$version = substr(substr($file,$lim+1),0,-4);
				$b['revision'] = $version;
				$b['rev_major'] =  strtok($version,'.');
				$b['rev_minor'] =  strtok('.');
				$b['rev_subminor'] =  strtok('.');
				if (isset($back[$name])) { 
				 if ($back[$name]['rev_major'] < $b['rev_major'] or
						 ($back[$name]['rev_major'] == $b['rev_major'] and
						  $back[$name]['rev_minor'] < $b['rev_minor']) or 
						 ($back[$name]['rev_major'] == $b['rev_major'] and
						  $back[$name]['rev_minor'] == $b['rev_minor'] and
							$back[$name]['rev_subminor'] < $b['rev_subminor'])) {
						$back[$name] = $b;
					}
				} else {
					$back[$name] = $b;
				}
			}
		}
		return $back;
	}

	function rebuild_list($dir) {
		$list = array();
		$h = opendir($dir);
		while ($file = readdir($h)) {
			if (substr($file,-9,9) == '.info.txt') {
				$list[] = $file;
			}
		}
		closedir($h);
		$fp = fopen($dir.'/00_list.txt','w');
		if (count($list)) {
			$out = '';
			foreach ($list as $l) {
				$detail = $this->readconf($dir.'/'.$l);
				$l = substr($l,0,-9);
				$pos = strpos($l,'-');
				$type = substr($l,0,$pos);
				$name = substr($l,$pos+1);
				$out = "'". addslashes($type) ."',";
				$out.= "'". addslashes($name) ."',";
				$out.= "'". addslashes($detail['revision'][0]) ."',";
				$out.= "'". addslashes(implode(" ",$detail['description'])) ."',";
				$out.= "'". addslashes($detail['licence'][0]) ."'\n";
				fputs($fp,$out);
			}
		} else {
				fputs($fp,"# nothing installed");
		}
		fclose($fp);
	}

	function write_conf($dir,$type,$package,$arr) {
		$fp = fopen($dir.'/Installed/'.$type.'-'.$package.'.conf.txt',"w");
		foreach ($arr as $k=>$v) {
			fputs($fp,"$k:\n$v\n\n");
		}
		fclose($fp);
	}
	
	function read_list($file,$type=false,$find='',$field=false) {
		$out = array();
		$fp = @ fopen($file,'r');
		if ($fp) {
			while (!feof($fp)) {
				$line = fgets($fp,1024);
				if (trim($line) and substr(trim($line),0,1) == "'") {
					$str = split("','",substr($line,1,strlen($line)-3));
					if (isset($str[4]) and $str[0] and $str[1] and $str[2] and $str[3] and $str[4]) {
						$this->types["{$str[0]}"] = true;
						if ((!$type or $str[0] == $type) and (!$find or strpos($str[1],$find))) {
							$out["{$str[0]}"]["{$str[1]}"] = array(
								'modname' => $str[0] .'-'. stripslashes($str[1]),
								'name' => stripslashes($str[1]),
								'revision' => $str[2],
								'rev_major' => strtok($str[2],'.'),
								'rev_minor' => strtok('.'),
								'rev_subminor' => strtok('.'),
								'description' => stripslashes($str[3]),
								'licence' => stripslashes($str[4]),
								'literal' => stripslashes($line)
								);
							if (isset($str[5])) {
								$out["{$str[0]}"]["{$str[1]}"]['version'] = stripslashes($str[5]);
								if (isset($str[6])) {
									$out["{$str[0]}"]["{$str[1]}"]['md5'] = stripslashes($str[6]);
								}
							}
							if ($field and isset($out["{$str[0]}"]["{$str[1]}"][$field])) {
								$out[] = $out["{$str[0]}"]["{$str[1]}"][$field];
							}
						}
					}
				}
			}
			fclose($fp);
		}
		return $out;
	}

	function package($path,$type,$package) {
		$oldir = getcwd();
		chdir($path);
		include_once($oldir.'/lib/tar.class.php');
		$file = 'Packages/'.$type.'-'.$package.'.info.txt';
		$info = $this->readconf($file);
		if (is_file("Dist/".$type.'-'.$package.'-'.$info['revision'][0].'.tgz')) {
			unlink("Dist/".$type.'-'.$package.'-'.$info['revision'][0].'.tgz');
		}
		$tar = new tar;
		$tar->addFile($file);
		if (isset($info['files']) and count($info['files'])) {
			foreach ($info['files'] as $f) {
				$tar->addFile($f[0]);
			}
		}
		$filename = 'Dist/'.$type.'-'.$package.'-'.$info['revision'][0].'.tgz';
		$tar->toTar($filename,1);
		if (!function_exists('md5_file')) {
			$md5 = md5(implode('', file($filename)));
		} else {
			$md5 = md5_file($filename);
		}
		chdir($oldir);
		return $md5;
	}

	function install($path,$type,$package,$from=0,$upgrade=false) {
		$file = $path.'/Packages/'.$type.'-'.$package.'.info.txt';
		$info = $this->readconf($file);
		$conf['_SERVER'] = $_SERVER;
		if (isset($info['configuration']) and count($info['configuration'])) {
			if (is_file($path.'/Installed/'.$type.'-'.$package.'.conf.txt')) {
				$conf = $this->readconf($path.'/Installed/'.$type.'-'.$package.'.conf.txt');
			} else {
				global $smarty;
				if (isset($info['configuration help']) and count($info['configuration help'])) {
					$smarty->assign('help',implode("<br />\n",$info['configuration help']));
				} else {
					$smarty->assign('help','');
				}
				for ($i=0;$i<count($info['configuration']);$i++) {
					$info['configuration'][$i][2] = preg_replace('/\\$([_A-Z]*)/e','$conf[\'_SERVER\'][\'\\1\']',$info['configuration'][$i][2]);
				}
				$smarty->assign('type',$type);
				$smarty->assign('package',$package);
				$smarty->assign('info',$info);
				$smarty->assign('mid','tiki-mods_config.tpl');
				$smarty->display('tiki.tpl');
				die;
			}
		}
		if ($upgrade and isset($info['sql-upgrade']) and count($info['sql-upgrade'])) {
			uksort($info['sql-upgrade'],'newer');
			global $tikilib;
			foreach ($info['sql-upgrade'] as $v=>$vv) {
				if (newer($from,$v) < 0) {
					foreach ($vv as $sql) {
						if (count($conf) and strpos($sql,'$')) {
							$sql = preg_replace('/\\$([_a-zA-Z0-9]*)/e','$conf[\'\\1\'][0]',$sql);
						}
						$this->feedback[] = array('num'=>1,'mes'=>"$from -> $v : $sql");
						$tikilib->query($sql,array());
					}
				}
			}
		} elseif (isset($info['sql-install']) and count($info['sql-install'])) {
			global $tikilib;
			foreach ($info['sql-install'] as $sql) {
				if (count($conf) and strpos($sql,'$')) {
					$sql = preg_replace('/\\$([_a-zA-Z0-9]*)/e','$conf[\'\\1\'][0]',$sql);
				}
				$tikilib->query($sql,array());
			}
		}
		if (isset($info['files']) and count($info['files'])) {
			foreach ($info['files'] as $f) {
				$this->prepare_dir(dirname($f[1]));
				if (is_file($f[1])) {
					if (is_file($f[1] . '.orig.' . $info['revision'][0])) {
						@unlink($f[1] . '.orig.' . $info['revision'][0]);
					}
					rename($f[1],$f[1] . '.orig.' . $info['revision'][0]);
				}
				if (substr(basename($f[0]),0,7) == "sample:") {
					$text = implode('',file($path.$f[0]));
					$text = preg_replace('/\[:::\[([^\]]*)\]:::\]/e','$conf[\'\\1\'][0]',$text);
					$f[0] = str_replace('sample:','',$f[0]);
					$fp = fopen($path.'/'.$f[0],"w");
					fputs($fp,$text);
					fclose($fp);
					if (!(rename($path.'/'.$f[0],$f[1]) && chmod($f[1], 0644))) die("$f[0] to $f[1] impossible to copy");
				} else {
					if (!(copy($path.'/'.$f[0],$f[1]) && chmod($f[1], 0644))) die("$f[0] to $f[1] impossible to copy");
				}
			}
		}
		copy($file,$path.'/Installed/'.basename($file));
		$this->rebuild_list($path.'/Installed/');
	}

	function remove($path,$type,$package,$upgrade=false) {
		$file = $path.'/Installed/'.$type.'-'.$package.'.info.txt';
		if (is_file($file)) {
			$info = $this->readconf($file);
			if (!$upgrade and isset($info['sql-remove']) and count($info['sql-remove'])) {
				global $tikilib;
				foreach ($info['sql-remove'] as $sql) {
					$tikilib->query($sql,array());
				}
			}
			if (isset($info['files']) and count($info['files'])) {
				foreach ($info['files'] as $f) {
					if (!@unlink($f[1])) $this->feedback[] = array('num'=>1,'mes'=>("$f[1] impossible to remove"));
					if (is_file($f[1] . '.orig.' . $info['revision'][0])) {
						rename($f[1] . '.orig.' . $info['revision'][0],$f[1]);
					}
				}
			}
			unlink($file);
			$this->rebuild_list($path.'/Installed/');
			if (is_file($path.'/Installed/'.$type.'-'.$package.'.conf.txt')) {
				unlink($path.'/Installed/'.$type.'-'.$package.'.conf.txt');
			}
			return $info['revision'][0];
		}
	}

	function upgrade($path,$type,$package) {
		$from = $this->remove($path,$type,$package,$upgrade=true);
		$this->install($path,$type,$package,$from,$upgrade=true);
	}

}

function newer($a,$b) {
	$aa = split('\.',$a);
	$bb = split('\.',$b);
	for($i=0;$i<count($aa);$i++) {
		if (!isset($bb[$i])) { $bb[$i] = '0'; }
		if ($aa[$i] != $bb[$i]) { return strcmp($aa[$i],$bb[$i]); }
	} 
	return 0;
} 


$modslib = new ModsLib();
?>
