<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
	exit;
}

class ModsLib {

	function ModsLib() { }

	function readconf($file) {
		if (is_file($file)) {
			$fp = fopen($file,"r");
			$next = true;
			$lab = '';
			$out = array();
			while (!feof($fp)) {
				$line = fgets($fp,1024);
				$line = trim($line);
				if ($line and substr($line,0,1) != '#') {
					if ($next) {
						$lab = trim(strtr(strtolower($line),':',' '));
						$next = false;
					} else {
						if ($lab == 'configuration') {
							$out["$lab"][] = split(',',trim($line));
						} elseif ($lab == 'files') {
							$out["$lab"][] = split(' ',trim($line));
						} else {
							$line = preg_replace('/\$[^:]*:([^\$]*)\$/',"$1",trim($line));
							$out["$lab"][] = trim($line);
						}
					}
				} else {
					$next = true;
				}
			}
			fclose($fp);
			return $out;
		} else {
			return false;
		}
	}

	function refresh_remote($dir,$host,$file,$to='') {
		if (!$to) $to = $file;
		if ($fp = fopen($host.$file,'r')) {
			$fl = fopen($dir.$to,'w');
			while (!feof($fp)) {
				$line = fgets($fp,1024);
				$line = trim($line);
				fputs($fl,$line."\n");
			}
			fclose($fl);
			fclose($fp);
		} else {
			return false;
		}
	}

	function publish($path,$public,$item='',$add=true) {
		$fp = fopen($path.'/30_list.public.txt',"w");
		foreach ($public as $type=>$meat) {
			foreach ($meat as $p) {
				if ($item == $type.'-'.$p['name']) {
					$item = '';
					if ($add) {
						fputs($fp,$p['literal']);
					}
				} else {
					fputs($fp,$p['literal']);
				}
			}
		}
		if ($item and $add) {
			$detail = $this->readconf($path.'/'.$item.'.info.txt');
			$pos = strpos($item,'-');
			$type = substr($item,0,$pos);
			$name = substr($item,$pos+1);
			$buf = "'". addslashes($type) ."','";
			$buf.= addslashes($name) ."','";
			$buf.= addslashes($detail['revision'][0]) ."','";
			$buf.= addslashes(implode(" ",$detail['description'])) ."','";
			$buf.= addslashes($detail['licence'][0]) ."'\n";
			fputs($fp,$buf);
			$this->package(dirname($path),$type,$name);
		}
		fclose($fp);
	}
	
	function unpublish($path,$public,$item) {
		$this->publish($path,$public,$item,false);
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
				$l = strtok($l,'.');
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
	
	function read_list($file) {
		$out = array();
		$fp = fopen($file,'r');
		while (!feof($fp)) {
			$line = fgets($fp,1024);
			if (trim($line) and substr(trim($line),0,1) != "#") {
				$str = split("','",substr($line,1,strlen($line)-3));
				$out["{$str[0]}"]["{$str[1]}"] = array(
					'name' => stripslashes($str[1]),
					'revision' => $str[2],
					'description' => stripslashes($str[3]),
					'licence' => stripslashes($str[4]),
					'literal' => stripslashes($line)
					);
			}
		}
		fclose($fp);
		return $out;
	}

	function package($path,$type,$package) {
		$oldir = getcwd();
		chdir($path);
		if (!is_dir("Dist")) mkdir("Dist");
		include($oldir.'/lib/tar.class.php');
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
		$tar->toTar('Dist/'.$type.'-'.$package.'-'.$info['revision'][0].'.tgz',1);
		chdir($oldir);
	}

	function install($path,$type,$package) {
		$file = $path.'Packages/'.$type.'-'.$package.'.info.txt';
		$info = $this->readconf($file);
		$conf['_SERVER'] = $_SERVER;
		if (isset($info['configuration']) and count($info['configuration'])) {
			if (is_file($path.'Installed/'.$type.'-'.$package.'.conf.txt')) {
				$conf = $this->readconf($path.'Installed/'.$type.'-'.$package.'.conf.txt');
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
		if (isset($info['sql-install']) and count($info['sql-install'])) {
			global $tikilib;
			foreach ($info['sql-install'] as $sql) {
				if (count($conf) and strpos($sql,'$')) {
					$sql = preg_replace('/\\$([_a-zA-Z0-9]*)/e','$conf[\'\\1\'][0]',$sql);
				}
				$tikilib->query($sql,array());
			}
		}
		if (isset($info['php-install']) and count($info['php-install'])) {
			foreach ($info['php-install'] as $php) {
				@include $path.'Packages/'.$php;
			}
		}
		if (isset($info['php-remove']) and count($info['php-remove'])) {
			foreach ($info['php-remove'] as $php) {
				copy($path.'Packages/'.$php,$path.'Installed/'.$php);
			}
		}
		if (isset($info['files']) and count($info['files'])) {
			foreach ($info['files'] as $f) {
				if (!is_dir(dirname($f[1]))) mkdir(dirname($f[1]));
				if (is_file($f[1])) rename($f[1],$f[1] . '.orig.' . $info['revision'][0]);
				if (substr(basename($f[0]),0,7) == "sample:") {
					$text = implode('',file($path.$f[0]));
					$text = preg_replace('/\[:::\[([^\]]*)\]:::\]/e','$conf[\'\\1\'][0]',$text);
					$f[0] = str_replace('sample:','',$f[0]);
					$fp = fopen($path.$f[0],"w");
					fputs($fp,$text);
					fclose($fp);
					if (!rename($path.$f[0],$f[1])) die("$f[0] to $f[1] impossible to copy");
				} else {
					if (!copy($path.$f[0],$f[1])) die("$f[0] to $f[1] impossible to copy");
				}
			}
		}
		copy($file,$path.'Installed/'.basename($file));
		$this->rebuild_list($path.'Installed/');
	}

	function remove($path,$type,$package) {
		$file = $path.'Installed/'.$type.'-'.$package.'.info.txt';
		if (is_file($file)) {
			$info = $this->readconf($file);
			if (isset($info['sql-remove']) and count($info['sql-remove'])) {
				global $tikilib;
				foreach ($info['sql-remove'] as $sql) {
					$tikilib->query($sql,array());
				}
			}
			if (isset($info['php-remove']) and count($info['php-remove'])) {
				foreach ($info['php-remove'] as $php) {
					@include $path.'Installed/'.$php;
					@unlink($path.'Installed/'.$php);
				}
			}
			if (isset($info['files']) and count($info['files'])) {
				foreach ($info['files'] as $f) {
					if (!@unlink($f[1])) die("$f[1] impossible to remove");
					if (is_file($f[1] . '.orig.' . $info['revision'][0])) {
						rename($f[1] . '.orig.' . $info['revision'][0],$f[1]);
					}
				}
			}
			unlink($file);
			$this->rebuild_list($path.'Installed/');
			if (is_file($path.'Installed/'.$type.'-'.$package.'.conf.txt')) {
				unlink($path.'Installed/'.$type.'-'.$package.'.conf.txt');
			}
		}
	}

	function upgrade($path,$type,$package) {
		$this->remove($path,$type,$package);
		$this->install($path,$type,$package);
	}

}

$modslib = new ModsLib();
?>
