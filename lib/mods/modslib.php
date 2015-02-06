<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
	exit;
}

/*
 * This class describe the main informations of a module
 */
/**
 *
 */
class TikiMod
{
	public $modname;
	public $name;
	public $type;
	public $revision;

	/* use with $type and $name, or $type only as modname ("$type-$name") */
    /**
     * @param $type
     * @param bool $name
     */
    function TikiMod($type, $name=FALSE)
	{
		if ($name === FALSE) { // $type is a modname
			$this->modname=$type;
			list($this->type, $this->name) = explode('-', $type, 2);
		} else {
			$this->modname=$type.'-'.$name;
			$this->type=$type;
			$this->name=$name;
		}
	}

	/*
	 * return:
	 * -1 if $mod is newer
	 *  1 if $this is newer
	 *  0 if same revision
	 */
    /**
     * @param $mod
     * @return bool
     */
    function isnewerthan($mod)
	{
		if (ModsLib::revision_compare($this->revision, $mod->revision) > 0)
			return TRUE;
	}

}

/*
 * This class describe the informations of a module that are available
 * from a 00_list.txt style file
 */
/**
 *
 */
class TikiModAvailable extends TikiMod
{
	public $repository;      /* string */
	public $description;     /* array */
	public $licence;         /* string */
	public $version;         /* array */
	public $md5;             /* string */
	public $requires;        /* array */
	public $suggests;        /* array */
	public $conflicts;       /* array */

    /**
     * @param $type
     * @param bool $name
     */
    function TikiModAvailable($type, $name=FALSE)
	{
		$this->TikiMod($type, $name);
	}

	/* convert $this mod as a line viewable in files like 00_list.txt */
    /**
     * @return string
     */
    function toline()
	{
		$out='';

		$out.= "'". addslashes($this->type) ."',";
		$out.= "'". addslashes($this->name) ."',";
		$out.= "'". addslashes($this->revision) ."',";
		$out.= "'". addslashes($this->description) ."',";
		$out.= "'". addslashes($this->licence) ."',";
		$out.= "'". addslashes($this->version[0]) ."',"; // probably buggy isn't it?
		$out.= "'". addslashes($this->md5) ."',";

		$requires='';
		if (is_array($this->requires))
			foreach ($this->requires as $elem)
				$requires.=($requires == '' ? '' : '&').$elem->tostring();

		$suggests='';
		if (is_array($this->suggests))
			foreach ($this->suggests as $elem)
				$suggests.=($suggests == '' ? '' : '&').$elem->tostring();

		$conflicts='';
		if (is_array($this->conflicts))
			foreach ($this->conflicts as $elem)
				$conflicts.=($conflicts == '' ? '' : '&').$elem->tostring();

		$deps = empty($requires) ? '' : 'requires:'.$requires;
		$deps.= empty($suggests) ? '' : (empty($deps) ? '' : ';') . 'suggests:'.$suggests;
		$deps.= empty($conflicts) ? '' : (empty($deps) ? '' : ';') . 'conflicts:'.$conflicts;

		$out.= "'".addslashes($deps)."'";

		return $out;
	}

	/* used by readdeps_line and read_list for importing dependences */
    /**
     * @param $array
     * @param $str
     */
    function _decodedeps(&$array, $str)
	{
		$str=str_replace(' ', '', $str);
		$am=explode('&', $str);
		foreach ($am as $dep) {
			$modname=preg_replace('/[<>=].*/', '', $dep);
			$moddep=new TikiModDepend($modname);
			$moddep->tests=array();
			$tests=array();
			preg_match_all('/[<>=]+[0-9.]+/', $dep, $tests);
			foreach ($tests[0] as $test) {
				$moddep->tests[]=array('test' => preg_replace('/[0-9.]*$/', '', $test),
						       'revision' => preg_replace('/^[^0-9.]*/', '', $test));
			}
			$array[]=$moddep;
		}
	}

	/* import from a dependences element string in 00_list.txt style file */
    /**
     * @param $line
     */
    function readdeps_line($line)
	{
		$meat=explode(';', $line);
		foreach ($meat as $m) {
			unset($a);
			$a=FALSE;
			if (strpos($m, 'requires:') === 0) {
				$m=substr($m, 9);
				$a=&$this->requires;
			} elseif (strpos($m, 'suggests:') === 0) {
				$m=substr($m, 9);
				$a=&$this->suggests;
			} elseif (strpos($m, 'conflicts:') === 0) {
				$m=substr($m, 10);
				$a=&$this->conflicts;
			}

			if ($a !== FALSE) $this->_decodedeps($a, $m);
		}
	}
}

/*
 * This class contain full information of a module,
 * like there are available in it's .info.txt file
 */
/**
 *
 */
class TikiModInfo extends TikiModAvailable
{
	public $configuration;         /* array */
	public $configuration_help;    /* array */
	public $files;                 /* array */
	public $contributor;           /* array */
	public $lastmodif;             /* string */
	public $devurl;                /* array */
	public $docurl;                /* array */
	public $changelog;             /* array */
	public $author;                /* array */
	public $help;                  /* array */
	public $url;                   /* array */
	public $sql_upgrade;           /* array */
	public $sql_install;           /* array */
	public $sql_remove;            /* array */

    /**
     * @param $type
     * @param bool $name
     */
    function TikiModInfo($type, $name=FALSE)
	{
		$this->TikiModAvailable($type, $name);
	}

	/* Import all datas from the .info.txt file */
    /**
     * @param $file
     * @return bool|string
     */
    function readinfo($file)
	{
		if (!is_file($file)) {
			return sprintf(tra('File %s not found'), $file);
		}

		$fp = fopen($file, "r");
		$next = true;
		$lab = '';
		while (!feof($fp)) {
			$line = fgets($fp, 1024);
			$line = trim($line);
			if (empty($line)) {
				$next = true;
				continue;
			}
			if (substr($line, 0, 1) == '#') continue;

			if ($next) {
				$lab = trim(strtr(strtolower($line), ':', ' '));
				$next = false;
				$localkey = '*';
				continue;
			}

			switch($lab) {
			case 'sql-upgrade':
				if (substr($line, 0, 1) == ':') {
					$localkey=substr($line, 1);
				} else {
					$this->sql_upgrade[$localkey][] = trim($line);
				}
				break;
			case 'sql-install':
				$this->sql_install[] = trim($line);
				break;
			case 'sql-remove':
				$this->sql_remove[] = trim($line);
				break;
			case 'configuration':
				$this->configuration[] = explode(',', trim($line));
				break;
			case 'configuration help':
				$this->configuration_help[] = explode(',', trim($line));
				break;
			case 'files':
				$this->files[] = preg_split('/ +/', trim($line));
				break;
			case 'contributor':
				$this->contributor[] = trim(preg_replace('/\$[^:]*:([^\$]*)\$/', "$1", trim($line)));
				break;
			case 'revision':
				if (empty($this->revision))
					$this->revision = trim(preg_replace('/\$[^:]*:([^\$]*)\$/', "$1", trim($line)));
				break;
			case 'lastmodif':
				if (empty($this->lastmodif))
					$this->lastmodif = trim(preg_replace('/\$[^:]*:([^\$]*)\$/', "$1", trim($line)));
				break;
			case 'version':
				$this->version[]=trim($line);
				break;
			case 'licence':
				$this->licence = trim($line);
				break;
			case 'devurl':
				$this->devurl[] = trim($line);
				break;
			case 'docurl':
				$this->docurl[] = trim($line);
				break;
			case 'description':
				$this->description .= empty($this->description) ? trim($line) : ' '.trim($line);
				break;
			case 'changelog':
				$this->changelog[] = trim($line);
				break;
			case 'author':
				$this->author[] = trim($line);
				break;
			case 'requires':
				$this->_decodedeps($this->requires, $line);
				break;
			case 'suggests':
				$this->_decodedeps($this->suggests, $line);
				break;
			case 'conflicts':
				$this->_decodedeps($this->conflicts, $line);
				break;
			case 'help':
				$this->_help[] = trim($line);
				break;
			case 'url':
				$this->_url[] = trim($line);
				break;
			default:
				die("key: $lab to add");
			}
		}

		fclose($fp);
		return false;
	}

    /**
     * @param $mods_path
     */
    function writeinfo($mods_path)
	{
		die("not implemented");
	}

	/* read configuration file of this module */
    /**
     * @param $mods_path
     * @return array|bool
     */
    function readconf($mods_path)
	{
		if (!is_file($mods_path.'/Installed/'.$this->type.'-'.$this->name.'.conf.txt')) return false;
		$fp = fopen($mods_path.'/Installed/'.$this->type.'-'.$this->name.'.conf.txt', "r");
		$conf=array();
		$lab='';
		while (!feof($fp)) {
			$line = fgets($fp, 1024);
			$line = trim($line);
			if (empty($line)) {
				$next = true;
				continue;
			}
			if (substr($line, 0, 1) == '#') continue;

			if ($next) {
				$lab = trim(strtr(strtolower($line), ':', ' '));
				$next = false;
				continue;
			}

			if ($lab='') continue;
			$conf[$lab][]=$line;
		}
		return $conf;
	}

	/* write configuration file of this module */
    /**
     * @param $mods_path
     * @param $confs
     */
    function writeconf($mods_path, $confs)
	{
		$fp = fopen($mods_path.'/Installed/'.$this->type.'-'.$this->name.'.conf.txt', "w");
		foreach ($confs as $k=>$v) {
			fputs($fp, "$k:\n$v\n\n");
		}
		fclose($fp);
	}

	/* construct a package (.tar.gz) of this module from every it's files */
    /**
     * @param $mods_path
     * @param $info_file
     * @return bool|string
     */
    function package($mods_path, $info_file)
	{
		$oldir = getcwd();
		if (chdir($mods_path) === FALSE) {
			$err=sprintf(tra("Can't chdir to '%s'"), $mods_path);
			return $err;
		}
		include_once($oldir.'/lib/tar.class.php');

		$info_file = 'Packages/'.$this->type.'-'.$this->name.'.info.txt';
		$err=$this->readinfo($info_file);
		if ($err !== FALSE) {
			chdir($oldir);
			return $err;
		}

		if (is_file("Dist/".$this->type.'-'.$this->name.'-'.$this->revision.'.tgz')) {
			unlink("Dist/".$this->type.'-'.$this->name.'-'.$this->revision.'.tgz');
		}
		$tar = new tar;
		$tar->addFile($info_file);
		if (is_array($this->files)) {
			foreach ($this->files as $f) {
				$tar->addFile($f[0]);
			}
		}
		$filename = 'Dist/'.$this->type.'-'.$this->name.'-'.$this->revision.'.tgz';
		$tar->toTar($filename, 1);
		if (!function_exists('md5_file')) {
			$this->md5 = md5(implode('', file($filename)));
		} else {
			$this->md5 = md5_file($filename);
		}
		chmod($filename, 0644); // needed on some servers

		chdir($oldir);
		return $err;
	}

}

/*
 * This class represent one dependence for an another package
 */
/**
 *
 */
class TikiModDepend extends TikiMod
{
	public $tests;

    /**
     * @param $type
     * @param bool $name
     */
    function TikiModDepend($type, $name=FALSE)
	{
		$this->TikiMod($type, $name);
	}

    /**
     * @return string
     */
    function tostring()
	{
		$out=$this->modname;
		foreach ($this->tests as $test) {
			$out.=$test['test'].$test['revision'];
		}
		return $out;
	}

	/*
	 * Check if $mod is concerned by this depend
	 */
    /**
     * @param $mod
     * @return bool
     */
    function isitin($mod)
	{
		if ($mod->modname != $this->modname) return FALSE;
		if (!is_array($this->tests)) return TRUE;
		foreach ($this->tests as $test) {
			switch($test['test']) {
			case '=':
				if (ModsLib::revision_compare($mod->revision, $test['revision']) != 0) {
					return FALSE;
				}
				break;
			case '<':
				if (ModsLib::revision_compare($mod->revision, $test['revision']) != -1) {
					return FALSE;
				}
				break;
			case '>':
				if (ModsLib::revision_compare($mod->revision, $test['revision']) != 1) {
					return FALSE;
				}
				break;
			case '<=':
			case '=<':
				if (ModsLib::revision_compare($mod->revision, $test['revision']) > 0) {
					return FALSE;
				}
				break;
			case '>=':
			case '=>':
				if (ModsLib::revision_compare($mod->revision, $test['revision']) < 0) {
					return FALSE;
				}
				break;
			}
		}
		return TRUE;
	}
}

/*
 * This is the class that manage every modules
 */
/**
 *
 */
class ModsLib
{

	public $feedback_listeners;
	public $types;
	public $versions;

    /**
     *
     */
    function __construct()
	{
		$this->types = array();
		$this->feedback_listeners = array();
		$this->versions = array(
			'Unspecified' => -1,
			'1.x' => 1.0,
			'1.9.x' => 1.9,
			'2.x' => 2.0,
			'3.x' => 3.0,
			'4.x' => 4.0,
			'5.x' => 5.0,
			'6.x' => 6.0,
			'7.x' => 7.0,
			'8.x' => 8.0,
			'9.x' => 9.0,
			'10.x' => 10.0,
			'11.x' => 11.0,
			'12.x' => 12.0,
		);
	}

    /**
     * @param $feedback
     */
    function feedback_info($feedback)
	{
		foreach ($this->feedback_listeners as $listener) {
			$listener(-1, $feedback);
		}
	}

    /**
     * @param $feedback
     */
    function feedback_warning($feedback)
	{
		foreach ($this->feedback_listeners as $listener) {
			$listener(0, $feedback);
		}
	}

    /**
     * @param $feedback
     */
    function feedback_error($feedback)
	{
		foreach ($this->feedback_listeners as $listener) {
			$listener(1, $feedback);
		}
	}

    /**
     * @param $listener
     */
    function add_feedback_listener($listener)
	{
		$this->feedback_listeners[]=$listener;
	}

    /**
     * @param $path
     */
    function prepare_dir($path)
	{
		if ($path and $path != '/' and !is_dir(dirname($path))) $this->prepare_dir(dirname($path));
		if (!is_dir($path) and substr($path, 0, 1) != '.') mkdir($path, 02777);
	}

    /**
     * @param $remote
     * @param $file
     * @param $local
     * @return bool
     */
    function dl_remote($remote,$file,$local)
	{
		$meat = $this->get_remote($remote."/Dist/".$file.".tgz");
		if ($meat === FALSE) return FALSE;

		$localfile = $local."/Cache/".$file.".tgz";
		if (is_file($localfile)) unlink($localfile);
		$fp = fopen($localfile, "wb");
		fwrite($fp, $meat);
		fclose($fp);
		require_once("lib/tar.class.php");
		$tar = new tar;
		if ($tar->openTAR($localfile)) {
			foreach ($tar->files as $f) {
				$this->prepare_dir(dirname($local.'/'.$f['name']));
				$fp = fopen($local.'/'.$f['name'], "wb");
				fwrite($fp, $f['file'], $f['size']);
				fclose($fp);
			}
		} else {
			$this->feedback_error(sprintf(tra('File %s is not a valid archive'), $localfile));
			return false;
		}
	}

    /**
     * @param $url
     * @return bool
     */
    function get_remote($url)
	{
		global $tikilib;
		$this->feedback_info("downloading '$url'...");
		$buffer = $tikilib->httprequest($url, $reqmethod = "GET");
		if ( ! $buffer ) {
			$this->feedback_error(sprintf(tra('Impossible to open %s : %s'), $url, 'n/a'));
			return false;
		}

		return $buffer;
	}

    /**
     * @param $remote
     * @param $local
     * @return bool
     */
    function refresh_remote($remote,$local)
	{
		$buffer = $this->get_remote($remote);
		if ( ! $buffer || $buffer{0} != "'" ) {
			$this->feedback_error(sprintf(tra('The content retrieved at %s is not a list of mods'), $remote, 'n/a'));
			return false;
		}
		$fl = fopen($local, 'w');
		fputs($fl, $buffer);
		fclose($fl);
		return true;
	}

    /**
     * @param $modpath
     * @param $public
     * @param $items
     * @param bool $add
     */
    function _publish($modpath,$public, $items,$add=true)
	{
		$fp = fopen($modpath.'/Packages/00_list.public.txt', "w");
		foreach ($public as $meat) {
			foreach ($meat as $p) {
				if ($add || !in_array($p->modname, $items)) {
					fputs($fp, $p->toline()."\n");
				}
			}
		}

		if (count($items) and $add) {
			foreach ($items as $modname) {
				$mod=new TikiModInfo($modname);
				if (!isset($public[$mod->type])
				    || !isset($public[$mod->type][$mod->name])) {
					$this->feedback_info("packaging ".$mod->modname." ...");
					$err=$mod->package($modpath, 'Packages/'.$mod->type.'-'.$mod->name.'.info.txt');
					if ($err !== false) {
						$this->feedback_error($err);
						continue;
					}

					fputs($fp, $mod->toline()."\n");
				}
			}
		}
		fclose($fp);
	}

    /**
     * @param $modpath
     * @param $items
     */
    function publish($modpath, $items)
	{
		$public = $this->read_list($modpath."/Packages/00_list.public.txt", 'public');
		$this->_publish($modpath, $public, $items, true);
	}

    /**
     * @param $modpath
     * @param $items
     */
    function unpublish($modpath,$items)
	{
		$public = $this->read_list($modpath."/Packages/00_list.public.txt", 'public');
		$this->_publish($modpath, $public, $items, false);
	}

    /**
     * @param $dir
     * @return array
     */
    function scan_dist($dir)
	{
		$back = array();
		$h = opendir($dir);
		while ($file = readdir($h)) {
			$file = basename($file);
			if (substr($file, -4, 4) == '.tgz') {
				$lim = strrpos($file, '-');
				$modname = substr($file, 0, $lim);
				$revision = substr(substr($file, $lim+1), 0, -4);

				$b = new TikiMod($modname);
				$b->revision=$revision;
				if (isset($back[$modname])) {
					if ($this->revision_compare($back[$modname]->revision, $b->revision) < 0)
						$back[$modname] = $b;
				} else {
					$back[$modname] = $b;
				}
			}
		}
		return $back;
	}

    /**
     * @param $dir
     */
    function rebuild_list($dir)
	{
		$list = array();
		$h = opendir($dir);
		while ($file = readdir($h)) {
			if (substr($file, -9, 9) == '.info.txt') {
				$list[] = $file;
			}
		}
		closedir($h);
		$fp = fopen($dir.'/00_list.txt', 'w');
		if (count($list)) {
			foreach ($list as $l) {
				$l = substr($l, 0, -9);
				$pos = strpos($l, '-');
				$type = substr($l, 0, $pos);
				$name = substr($l, $pos+1);
				$detail = new TikiModInfo($type, $name);
				$err=$detail->readinfo($dir.'/'.$l.'.info.txt');
				if ($err !== FALSE) {
					$this->feedback_error($err);
					continue;
				}

				fputs($fp, $detail->toline()."\n");
			}
		} else {
				fputs($fp, "# nothing installed");
		}
		fclose($fp);
	}

    /**
     * @param $file
     * @param $reponame
     * @param string $type
     * @param string $find
     * @param bool $simplelist
     * @return array
     */
    function read_list($file,$reponame,$type='',$find='',$simplelist=false)
	{
		$out = array();
		$fp = @ fopen($file, 'r');
		if ($fp) {
			while (!feof($fp)) {
				$line = fgets($fp, 4096);
				if (trim($line) and substr(trim($line), 0, 1) == "'") {
					$str = explode("','", substr($line, 1, strlen($line)-3));
					if (count($str) < 4) continue; // line must have at least 4 elements
					if (empty($str[0]) || empty($str[1]) || empty($str[2])) continue; // theses field must be not empty

					foreach ($str as $k => $v) $str[$k]=stripslashes($v);
					$mod=new TikiModAvailable($str[0], $str[1]);
					$this->types[$mod->type] = true;
					if ((empty($type) or ($mod->type == $type)) and (empty($find) or (strpos($mod->name, $find) !== false))) {
						$mod->revision=$str[2];
						$mod->description=$str[3];
						$mod->licence=$str[4];
						$mod->repository=$reponame;

						// from a buggy past
						if (count($str) == 6 or count($str) == 7) {
							$col=5;
							while (isset($str[$col])) {
								$blah=stripslashes($str[$col]);
								if (preg_match('/^[0-9]+\.[0-9]+[0-9.]*$/', $blah))
									$mod->version[]=$blah;
								elseif (preg_match('/^[0-9abcdefABCDEF]{32}$/', $blah))
									$mod->md5=$blah;
								$col++;
							}
						}

						if (count($str) > 7) {
							// now, $str[5] MUST be version, and $str[6] MUST be md5
							$mod->version[]=$str[5];
							$mod->md5=$str[6];
							$mod->readdeps_line($str[7]);
						}

						if ($simplelist)
							$out[]=$mod->modname;
						else
							$out[$mod->type][$mod->name]=$mod;
					}
				}
			}
			fclose($fp);
		}
		return $out;
	}

	/*
	 * $a and $b are strings representation of the revisions numbers
	 * return:
	 *   1 if $a > $b
	 *  -1 if $a < $b
	 *   0 if $a == $b
	 */
    /**
     * @param $a
     * @param $b
     * @return int
     */
    function revision_compare($a, $b)
	{
		$ra=explode('.', $a);
		$rb=explode('.', $b);
		for ($i=0, $max_counts = max(count($ra), count($rb)); $i<$max_counts; $i++) {
			$suba=isset($ra[$i]) ? (int)$ra[$i] : 0;
			$subb=isset($rb[$i]) ? (int)$rb[$i] : 0;
			if ($suba > $subb) return 1;
			if ($suba < $subb) return -1;
		}
		return 0;
	}

	/*
	 * Search in $list if the package is available, optionally by checking the revision
	 * $list must be the result of read_list()
	 */
    /**
     * @param $moddep
     * @param $list
     * @param bool $check_revision
     * @return null
     */
    function get_depend_available($moddep, $list, $check_revision=FALSE)
	{
		if (isset($list[$moddep->type]) && isset($list[$moddep->type][$moddep->name])) {
			$mod=$list[$moddep->type][$moddep->name];
			if (!$check_revision || $moddep->isitin($mod)) return $mod;
		}
		return NULL;
	}

	/*
	 * Search from every repos the latest version of a package.
	 */
    /**
     * @param $repos
     * @param $moddep
     * @return null
     */
    function find_last_version($repos, $moddep)
	{
		$found=NULL;
		foreach ($repos as $repo_name => $repo) {
			$mod=$this->get_depend_available($moddep, $repo, TRUE);
			if ($mod !== NULL) {
				if ($found === NULL || $mod->isnewerthan($found)) {
					$found=$mod;
				}
			}
		}
		return $found;
	}

    /**
     * @param $modspath
     * @param $mods_server
     * @param $modnames
     * @return array
     */
    function find_deps($modspath, $mods_server, $modnames)
	{
		$deps=array("wanted" => array(),
			    "toinstall" => array(),
			    "toupgrade" => array(),
			    "suggests" => array(),
			    "conflicts" => array(),
			    "unavailable" => array());

		$querymod=new TikiModAvailable('fakemod', 'query');
		$querymod->requires=array();
		foreach ($modnames as $modname) {
			$moddep=new TikiModDepend($modname);
			$querymod->requires[]=$moddep;
			$deps['wanted'][$modname]=$moddep;
		}

		$repos=array('installed' => $this->read_list($modspath."/Installed/00_list.txt", 'installed'),
			     'local' => $this->read_list($modspath."/Packages/00_list.txt", 'local'),
			     'remote' => $this->read_list($modspath."/Packages/00_list.". urlencode($mods_server).".txt", 'remote'));

		$this->_find_deps($repos, $querymod, $deps);

		/* now remove duplicates from suggests */
		foreach ($deps['suggests'] as $suggest) {
			if (isset($deps['toinstall'][$suggest[0]->modname]))
				unset($deps['suggests'][$suggest[0]->modname]);
		}

		/* now search conflicts from the requireds */
		foreach ($deps['toinstall'] as $mod) {
			if (!is_array($mod->conflicts)) continue;
			foreach ($mod->conflicts as $moddep) {
				/* compare required conflicts with requireds */
				foreach ($deps['toinstall'] as $dep) {
					if ($moddep->isitin($dep)) {
						if (!isset($deps['conflicts'][$dep->modname])) {
							$dep->errors[]="1/conflict avec ".$mod->modname;
							$deps['conflicts'][$dep->modname]=$dep;
						}
						if (!isset($deps['conflicts'][$mod->modname])) {
							$mod->errors[]="2/conflict avec ".$dep->modname;
							$deps['conflicts'][$mod->modname]=$mod;
						}
					}
				}
				/* compare required conflicts with installed */
				foreach ($repos['installed'] as $meat) {
					foreach ($meat as $dep) {
						if ($moddep->isitin($dep)) {
							$mod->errors[]="conflict avec ".$dep->modname;
							$dep->errors[]="conflict avec ".$mod->modname;
							if (!isset($deps['conflicts'][$dep->modname])) {
								$dep->errors[]="3/conflict avec ".$mod->modname;
								$deps['conflicts'][$dep->modname]=$dep;
							}
							if (!isset($deps['conflicts'][$mod->modname])) {
								$mod->errors[]="4/conflict avec ".$dep->modname;
								$deps['conflicts'][$mod->modname]=$mod;
							}
						}
					}
				}
			}
		}

		/* now search conflicts from the installeds */
		foreach ($repos['installed'] as $meat) {
			foreach ($meat as $mod) {
				if (!is_array($mod->conflicts)) continue;
				foreach ($mod->conflicts as $moddep) {
					/* compare installed with requireds */
					foreach ($deps['toinstall'] as $dep) {
						if ($moddep->isitin($dep)) {
							if (!isset($deps['conflicts'][$dep->modname])) {
								$mod->errors[]="5/conflict avec ".$dep->modname;
								$deps['conflicts'][$dep->modname]=$dep;
							}
							if (!isset($deps['conflicts'][$mod->modname])) {
								$dep->errors[]="6/conflict avec ".$mod->modname;
								$deps['conflicts'][$mod->modname]=$mod;
							}
						}
					}
				}
			}
		}

		/* move upgraded package to upgrade */
 		foreach ($deps['toinstall'] as $k => $toinstall) {
			if (isset($repos['installed'][$toinstall->type])
			    && isset($repos['installed'][$toinstall->type][$toinstall->name])) {
				$meat=array('from' => $repos['installed'][$toinstall->type][$toinstall->name],
					    'to' => $toinstall);
				$deps['toupgrade'][$toinstall->modname]=$meat;
				unset($deps['toinstall'][$k]);
			}
 		}

		return $deps;
	}

    /**
     * @param $repos
     * @param $querymod
     * @param $deps
     */
    function _find_deps($repos, $querymod, &$deps)
	{
		if (is_array($querymod->requires)) {
			foreach ($querymod->requires as $moddep) {
				$mod=$this->find_last_version($repos, $moddep);
				if ($mod === NULL) {
					$moddep->errors[]="required by '".$querymod->name."' : Not available";
					$deps['unavailable'][]=$moddep;
				} else {
					if ($mod->repository == 'installed') continue;
					if (isset($deps['toinstall'][$mod->modname])) {
						if ($mod->isnewerthan($deps['toinstall'][$mod->modname])) {
							// if there is an older version in the deps['toinstall'],
							// this is because a previous requires was requiring
							// an older one. So we don't try to upgrade it, we
							// just try to see if it is compatible with this require.
							if (!$moddep->isitin($mod)) {
								// it is not compatible
								$moddep->errors[]="revision failure";
								$deps['unavailable'][]=$moddep;
							}
/*							 else {
								// it is compatible, let it.
							}
*/
						}
/*						 else {
							// not newer, let it
						}
*/
					} else {
						$deps['toinstall'][$mod->modname]=$mod;
						$this->_find_deps($repos, $mod, $deps);
					}
				}
			}
		}

		if (is_array($querymod->suggests)) {
			foreach ($querymod->suggests as $moddep) {
				$deps['suggests'][$moddep->modname][]=$moddep;
			}
		}
	}

    /**
     * @param $modspath
     * @param $mods_server
     * @param $modnames
     * @return array
     */
    function find_deps_remove($modspath, $mods_server, $modnames)
	{
		$deps=array("wantedtoremove" => array(),
			    "toremove" => array());

		$repos=array('installed' => $this->read_list($modspath."/Installed/00_list.txt", 'installed'),
			     /*'local' => $this->read_list($modspath."/Packages/00_list.txt", 'local'),
			     'remote' => $this->read_list($modspath."/Packages/00_list.". urlencode($mods_server).".txt", 'remote')*/);

		foreach ($modnames as $modname) {
			$mod=new TikiMod($modname);
			if (isset($repos['installed'][$mod->type][$mod->name])) {
				$mod=$repos['installed'][$mod->type][$mod->name];
				$deps['wantedtoremove'][$modname]=$mod;
				$this->_find_deps_remove($repos, $mod, $deps);
			} else {
				$deps['wantedtoremove'][$modname]=$mod;
			}
		}
		return $deps;
	}

    /**
     * @param $repos
     * @param $modtoremove
     * @param $deps
     */
    function _find_deps_remove($repos, $modtoremove, &$deps)
	{
		$deps['toremove'][$modtoremove->modname]=$modtoremove;
		foreach ($repos['installed'] as $meat) {
			foreach ($meat as $mod) {
				if (isset($deps['toremove'][$mod->modname])) continue;
				if (!is_array($mod->requires)) continue;
				foreach ($mod->requires as $moddep) {
					if ($moddep->isitin($modtoremove)) {
						$this->_find_deps_remove($repos, $mod, $deps);
					}
				}
			}
		}
	}

    /**
     * @param $modspath
     * @param $mods_server
     * @param $deps
     * @return bool
     */
    function install_with_deps($modspath, $mods_server, $deps)
	{

		/* download packages if necessary */

		foreach ($deps['toinstall'] as $mod) {
			if ($mod->repository == 'remote') {
				$res=$this->dl_remote($mods_server, $mod->modname.'-'.$mod->revision, $modspath);
				if ($res === false) return false;
			}
		}

		foreach ($deps['toupgrade'] as $meat) {
			$mod=$meat['to'];
			if ($mod->repository == 'remote') {
				$res=$this->dl_remote($mods_server, $mod->modname.'-'.$mod->revision, $modspath);
				if ($res === false) return false;
			}
		}

		// we reconstruct deps because now there are modules that are downloaded
		$this->rebuild_list($modspath."/Packages");

		/* install packages */

		foreach ($deps['toinstall'] as $mod) {
			$this->install($modspath, $mod);
		}

		foreach ($deps['toupgrade'] as $meat) {
			$this->remove($modspath, $meat['from'], true);
			$this->install($modspath, $meat['to'], $meat['from'], true);
		}

	}

    /**
     * @param $modspath
     * @param $mods_server
     * @param $deps
     */
    function remove_with_deps($modspath, $mods_server, $deps)
	{
		foreach ($deps['toremove'] as $mod) {
			$this->remove($modspath, $mod);
		}
	}

    /**
     * @param $path
     * @param $mod
     * @param null $from
     * @param bool $upgrade
     */
    function install($path,$mod,$from=NULL,$upgrade=false)
	{
		$this->feedback_info("installing ".$mod->modname." (".$mod->revision.") ...");
		$file = $path.'/Packages/'.$mod->modname.'.info.txt';
		$info = new TikiModInfo($mod->modname);
		$err=$info->readinfo($file);
		if ($err !== FALSE) {
			$this->feedback_error($err);
			return;
		}
		$conf=array('_SERVER' => $_SERVER);
		if (is_array($info->configuration) and count($info->configuration)) {
			$conf = $info->readconf($path);
			if ($conf === false) {
				$smarty = TikiLib::lib('smarty');
				$conf=array('_SERVER' => $_SERVER);
				if (is_array($info->configuration_help) and count($info->configuration_help)) {
					$smarty->assign('help', implode("<br />\n", $info->configuration_help));
				} else {
					$smarty->assign('help', '');
				}
				for ($i=0, $count_config = count($info->configuration); $i < $count_config; $i++) {
					$info->configuration[$i][2] = preg_replace('/\\$([_A-Z]*)/e', '$conf[\'_SERVER\'][\'\\1\']', $info->configuration[$i][2]);
				}
				$smarty->assign('type', $info->type);
				$smarty->assign('package', $info->name);
				$smarty->assign('info', $info);
				$smarty->assign('mid', 'tiki-mods_config.tpl');
				$smarty->display('tiki.tpl');
				die;
			}
		}
		if ($upgrade and is_array($info->sql_upgrade) and count($info->sql_upgrade)) {
			uksort($info->sql_upgrade, array($this, 'revision_compare'));
			global $tikilib;
			foreach ($info->sql_upgrade as $v=>$vv) {
				if ($this->revision_compare($from->revision, $v) < 0) {
					foreach ($vv as $sql) {
						if (count($conf) and strpos($sql, '$')) {
							$sql = preg_replace('/\\$([_a-zA-Z0-9]*)/e', '$conf[\'\\1\'][0]', $sql);
						}
						$this->feedback_error($from->revision." -> $v : $sql");
						$tikilib->query($sql, array());
					}
				}
			}
		} elseif (is_array($info->sql_install) and count($info->sql_install)) {
			global $tikilib;
			foreach ($info->sql_install as $sql) {
				if (count($conf) and strpos($sql, '$')) {
					$sql = preg_replace('/\\$([_a-zA-Z0-9]*)/e', '$conf[\'\\1\'][0]', $sql);
				}
				$tikilib->query($sql, array());
			}
		}
		if (is_array($info->files) and count($info->files)) {
			foreach ($info->files as $f) {
				$this->prepare_dir(dirname($f[1]));
				if (is_file($f[1])) {
					if (is_file($f[1] . '.orig.' . $info->revision)) {
						@unlink($f[1] . '.orig.' . $info->revision);
					}
					rename($f[1], $f[1] . '.orig.' . $info->revision);
				}
				if (substr(basename($f[0]), 0, 7) == "sample:") {
					$text = implode('', file($path.$f[0]));
					$text = preg_replace('/\[:::\[([^\]]*)\]:::\]/e', '$conf[\'\\1\'][0]', $text);
					$f[0] = str_replace('sample:', '', $f[0]);
					$fp = fopen($path.'/'.$f[0], "w");
					fputs($fp, $text);
					fclose($fp);
					if (!(rename($path.'/'.$f[0], $f[1]) && chmod($f[1], 0644))) die("$f[0] to $f[1] impossible to copy");
				} else {
					if (!(copy($path.'/'.$f[0], $f[1]) && chmod($f[1], 0644))) die("$f[0] to $f[1] impossible to copy");
				}
			}
		}
		copy($file, $path.'/Installed/'.basename($file));
		$this->rebuild_list($path.'/Installed/');
	}

    /**
     * @param $path
     * @param $mod
     * @param bool $upgrade
     * @return bool
     */
    function remove($path,$mod,$upgrade=false)
	{
		$this->feedback_info("removing ".$mod->modname." (".$mod->revision.") ...");
		$file = $path.'/Installed/'.$mod->modname.'.info.txt';
		if (is_file($file)) {
			$info = new TikiModInfo($mod->modname);
			$err = $info->readinfo($file);
			if ($err !== false) {
				$this->feedback_error($err);
				return false;
			}
			if (!$upgrade and is_array($info->sql_remove) and count($info->sql_remove)) {
				global $tikilib;
				foreach ($info->sql_remove as $sql) {
					$tikilib->query($sql, array());
				}
			}
			if (isset($info->files) and count($info->files)) {
				foreach ($info->files as $f) {
					if (!@unlink($f[1])) $this->feedback_warning(sprintf(tra("%s impossible to remove"), $f[1]));
					if (is_file($f[1] . '.orig.' . $info->revision)) {
						rename($f[1] . '.orig.' . $info->revision, $f[1]);
					}
				}
			}
			unlink($file);
			$this->rebuild_list($path.'/Installed/');
			if (is_file($path.'/Installed/'.$info->type.'-'.$info->name.'.conf.txt')) {
				unlink($path.'/Installed/'.$info->type.'-'.$info->name.'.conf.txt');
			}
			return $info->revision;
		} else {
			$this->feedback_warning("'$file' was not found for removing");
		}
	}
}

/**
 * @param $a
 * @param $b
 * @return int
 */
function newer($a,$b)
{
	$aa = explode('.', $a);
	$bb = explode('.', $b);
	for ($i=0, $max_counts = max(count($aa), count($bb)); $i<$max_counts; $i++) {
		if (!isset($bb[$i])) {
			$bb[$i] = '0';
		}
		if (!isset($aa[$i])) {
			$aa[$i] = '0';
		}
		if ($aa[$i] != $bb[$i]) {
			return $aa[$i] > $bb[$i]? 1: -1;
		}
	}
	return 0;
}
$modslib = new ModsLib;
