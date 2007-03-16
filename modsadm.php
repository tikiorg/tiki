#!/usr/bin/php
<?php

if (!defined('STDOUT') || !defined('STDIN') || !defined('STDERR'))
     die("<p>shell only</p>");

// Initialization
require_once ('tiki-setup.php');
include('lib/mods/modslib.php');

//$STDERR = fopen('php://stderr', 'w');
//$STDIN = fopen('php://stdin', 'r');

$repos=array('installed' => array('url' => $mods_dir.'/Installed/00_list.txt',
				  'content' => $installed),
	     'local' => array('url' => $mods_dir.'/Packages/00_list.txt',
			      'content' => $local),
	     'remote' => array('url' => $mods_dir.'/Packages/00_list.'.urlencode($mods_server).'.txt',
			       'content' => $remote));

$goptions=array();

$commands=array('help' => array(),
// 		'update' => array(),
// 		'clean' => array(),
// 		'add-source' => array(),
// 		'remove-source' => array(),
// 		'list-sources' => array(),
		'list' => array('usage' => '[options] [regex]',
				'options' => array('-l' => array('help' => 'Do not query for remote mods'),
						   '-i' => array('help' => 'Show only installed mods'))),
		'install' => array('usage' => '[options] mod1 [mod2...]',
				   'options' => array('-d' => array('help' => "Download only mods, don't install them"))),
		'remove' => array('usage' => 'mod1 [mod2...]'),
		'publish' => array('usage' => 'mod1 [mod2...]'),
// 		'unpublish' => array(),
// 		'republish' => array(),
		);

function ask($str) {
	//global $STDIN;
	echo $str;
	$res=fgets(STDIN, 1024);
	return trim($res);
}

function command_help($goption, $coption, $cparams) {
	usage(0);
}

function command_install($goption, $coption, $cparams) {
	//global $STDIN;
	global $modslib;
	global $mods_dir;
	global $mods_server;

	$deps=$modslib->find_deps($mods_dir, $mods_server, $cparams);

	if (count($deps['unavailable'])) {
		$err="Sorry, theses packages are required but not available:\n";
		foreach ($deps['unavailable'] as $mod) {
			$err.=" - ".$mod->modname."\n";
		}
		failure($err);
	}
	if (count($deps['wanted'])) {
		echo "The following packages will be installed:\n";
		foreach ($deps['wanted'] as $mod) {
			echo "  ".$mod->modname." (".$mod->revision.")\n";
		}
	}
	if (count($deps['requires'])) {
		echo "The following extra packages will be installed:\n";
		foreach ($deps['requires'] as $mod) {
			echo "  ".$mod->modname." (".$mod->revision.")\n";
		}
	}
	if (count($deps['suggests'])) {
		echo "Suggested packages:\n";
		foreach ($deps['suggests'] as $mod) {
			echo "  ".$mod->modname."\n";
		}
	}

	$res=ask("Would you like to continue (y/N) ? ");
	if ($res != 'y') {
		echo "Good bye\n";
		exit(0);
	}

	$res=NULL;

	/* download packages if necessary */

	if ($res !== false) foreach($deps['requires'] as $mod) {
		if ($mod->repository == 'remote') {
			echo "downloading: ".$mod->modname.'-'.$mod->revision." ...";
			$res=$modslib->dl_remote($mods_server,$mod->modname.'-'.$mod->revision,$mods_dir);
			if ($res === false) {
				echo "failed\n";
				break;
			} else echo "done.\n";
		}
	}

	if ($res !== false) foreach($deps['wanted'] as $mod) {
		if ($mod->repository == 'remote') {
			echo "downloading: ".$mod->modname.'-'.$mod->revision." ...";
			$res=$modslib->dl_remote($mods_server,$mod->modname.'-'.$mod->revision,$mods_dir);
			if ($res === false) {
				echo "failed\n";
				break;
			} else echo "done.\n";
		}
	}

	// we reconstruct deps because now there are modules that are downloaded
	// (this is mainly to re-read_list the local repository)
	$modslib->rebuild_list($mods_dir."/Packages");
	$deps=$modslib->find_deps($mods_dir, $mods_server, $cparams);

	if (in_array('-d', $coption)) return;

	/* install packages */

	if ($res !== false) foreach($deps['requires'] as $mod) {
		echo "installing ".$mod->modname." (".$mod->revision.")...";
		$modslib->install($mods_dir, $mod->type, $mod->name);
		echo "done.\n";
	}

	if ($res !== false) foreach($deps['wanted'] as $mod) {
		echo "installing ".$mod->modname." (".$mod->revision.")...";
		$modslib->install($mods_dir, $mod->type, $mod->name);
		echo "done.\n";
	}
	
}

function command_remove($goption, $coption, $cparams) {
	//global $STDIN;
	global $modslib;
	global $mods_dir;
	global $mods_server;

	$deps=$modslib->find_deps_remove($mods_dir, $mods_server, $cparams);

	if (count($deps['wantedtoremove'])) {
		echo "The following packages will be REMOVED:\n";
		foreach ($deps['wantedtoremove'] as $mod) {
			echo "  ".$mod->modname." (".$mod->revision.")\n";
		}
	}
	if (count($deps['toremove'])) {
		echo "The following extra packages will be REMOVED:\n";
		foreach ($deps['toremove'] as $mod) {
			echo "  ".$mod->modname." (".$mod->revision.")\n";
		}
	}

	$res=ask("Would you like to continue (y/N) ? ");
	if ($res != 'y') {
		echo "Good bye\n";
		exit(0);
	}

	$res=NULL;

	/* remove packages */

	if ($res !== false) foreach($deps['toremove'] as $mod) {
		echo "removing ".$mod->modname." (".$mod->revision.")...";
		$modslib->remove($mods_dir, $mod->type, $mod->name);
		echo "done.\n";
	}
	
}

function command_list($goption, $coption, $cparams) {
	global $repos;
	global $modslib;
	$merged=array();

	if (count($cparams)) {
		$regex=$cparams[0];
	} else $regex='';

	foreach($repos as $reponame => $repo) {
		if ($reponame == 'remote' && in_array('-l', $coption)) continue;
		if ($reponame != 'installed' && in_array('-i', $coption)) continue;
		$content=$modslib->read_list($repo['url'], $reponame);
		foreach($content as $meat) {
			foreach($meat as $mod) {
				if (($regex !== '') && !preg_match('/'.$regex.'/', $mod->modname))
					continue;
				$merged[$mod->type][$mod->name][$reponame]=$mod;
			}
		}
	}


	foreach($merged as $k => $meat) {
		ksort(&$merged[$k]);
	}
	ksort(&$merged);

	foreach($merged as $type => $meat) {
		echo $type.":\n";
		foreach($meat as $name => $submeat) {
			$rev_installed = isset($submeat['installed']) ? $submeat['installed']->revision : '';
			$rev_remote    = isset($submeat['remote']) ? $submeat['remote']->revision : '';
			printf("  %-24.24s | %7.7s | %7.7s\n", $name, $rev_installed, $rev_remote);
		}
	}
}

function failure($errstr) {
	fprintf(STDERR, "%s\n", $errstr);
	exit(1);
}

function usage($err) {
	global $goptions;
	global $commands;

	echo "usage:\n";
	echo "php modsadm.php [options] commande\n\n";
	echo "commands:\n";
	foreach($commands as $command => $sglonk) {
		echo "  ".$command.(isset($sglonk['usage']) ? ' '.$sglonk['usage'] : '')."\n";
	}
	echo "\ncommands options:\n";
	foreach($commands as $command => $sglonk) {
		if (isset($sglonk['options'])) {
			echo "  ".$command.":\n";
			foreach($sglonk['options'] as $k => $option) {
				echo "    $k: ".$option['help']."\n";
			}
		}
	}

	exit($err);
}

function readargs($argv) {
	global $goptions;
	global $commands;

	$command=NULL;
	$goption=array();
	$coption=array();
	$cparams=array();
	foreach($argv as $argc => $arg) {
		if ($argc == 0) continue;

		if (substr($arg, 0, 1) == '-') {
			if ($command === NULL) {
				// global option
				if (!isset($goptions[$arg])) {
					usage(1);
				} else {
					$goption[]=$arg;
				}
			} else {
				// command option
				if (!isset($commands[$command]['options'])
				    || !isset($commands[$command]['options'][$arg])) {
					usage(1);
				} else {
					$coption[]=$arg;
				}
			}
		} else {
			if ($command === NULL) {
				// this is the command
				if (!isset($commands[$arg])) {
					usage(1);
				} else {
					$command=$arg;
				}
			} else {
				// command parameter
				$cparams[]=$arg;
			}
		}
	}

	if ($command === NULL) {
		usage(1);
	} else {
		$func='command_'.$command;
		$func($goption, $coption, $cparams);
	}
}

readargs($argv);

?>