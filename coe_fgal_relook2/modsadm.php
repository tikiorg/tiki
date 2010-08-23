#!/usr/bin/php
<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (!defined('STDOUT') || !defined('STDIN') || !defined('STDERR'))
     die("<p>shell only</p>");
if( isset( $_SERVER['REQUEST_METHOD'] ) ) die;

// Initialization
require_once ('tiki-setup.php');
include('lib/mods/modslib.php');

$repos=array('installed' => array('url' => $prefs['mods_dir'].'/Installed/00_list.txt',
				  'content' => $installed),
	     'local' => array('url' => $prefs['mods_dir'].'/Packages/00_list.txt',
			      'content' => $local),
	     'remote' => array('url' => $prefs['mods_dir'].'/Packages/00_list.'.urlencode($mods_server).'.txt',
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
		'show' => array('usage' => 'mod1 [mod2...]'),
		'install' => array('usage' => '[options] mod1 [mod2...]',
				   'options' => array('-d' => array('help' => "Download only mods, don't install them"))),
		'remove' => array('usage' => 'mod1 [mod2...]'),
// 		'publish' => array('usage' => 'mod1 [mod2...]'),
// 		'unpublish' => array(),
// 		'republish' => array(),
		);

function tikimods_feedback_listener($num, $err) {
	switch($num) {
	case -1:
		echo $err."\n";
		break;
	case 0:
		echo "! ".$err."\n";
		break;
	case 1:
		echo "*** ".$err."\n";
		break;
	}
}
$modslib->add_feedback_listener('tikimods_feedback_listener');


function ask($str) {
	echo $str;
	$res=fgets(STDIN, 1024);
	return trim($res);
}

function command_help($goption, $coption, $cparams) {
	usage(0);
}

function command_install($goption, $coption, $cparams) {
	global $modslib;
	global $prefs;
	global $mods_server;

	$deps=$modslib->find_deps($prefs['mods_dir'], $mods_server, $cparams);

	if (count($deps['unavailable'])) {
		$err="Sorry, theses packages are required but not available:\n";
		foreach ($deps['unavailable'] as $mod) {
			$err.=" - ".$mod->modname."\n";
		}
		failure($err);
	}
	if (count($deps['conflicts'])) {
		$err="Sorry, theses packages are required but conflicts:\n";
		foreach ($deps['conflicts'] as $mod) {
			$err.=" - ".$mod->modname."\n";
		}
		failure($err);
	}
	if (count($deps['wanted'])) {
		echo "You asked to install these mods:\n";
		foreach ($deps['wanted'] as $mod) {
			echo "  ".$mod->modname."\n";
		}
	}
	if (count($deps['toinstall'])) {
		echo "The following packages will be installed:\n";
		foreach ($deps['toinstall'] as $mod) {
			echo "  ".$mod->modname." (".$mod->revision.")\n";
		}
	}
	if (count($deps['toupgrade'])) {
		echo "The following packages will be upgraded:\n";
		foreach ($deps['toupgrade'] as $meat) {
			echo "  ".$meat['to']->modname." (".$meat['to']->revision.") (from version ".$meat['from']->revision.")\n";
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

	$modslib->install_with_deps($prefs['mods_dir'], $mods_server, $deps);
}

function command_remove($goption, $coption, $cparams) {
	global $modslib;
	global $prefs;
	global $mods_server;

	$deps=$modslib->find_deps_remove($prefs['mods_dir'], $mods_server, $cparams);

	if (count($deps['wantedtoremove'])) {
		echo "You asked to remove these mods:\n";
		foreach ($deps['wantedtoremove'] as $mod) {
			echo "  ".$mod->modname." (".$mod->revision.")\n";
		}
	}
	if (count($deps['toremove'])) {
		echo "The following packages will be REMOVED:\n";
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

	$modslib->remove_with_deps($prefs['mods_dir'], $mods_server, $deps);	
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
		ksort($merged[$k]);
	}
	ksort($merged);
	foreach($merged as $type => $meat) {
		echo $type.":\n";
		foreach($meat as $name => $submeat) {
			$rev_installed = isset($submeat['installed']) ? $submeat['installed']->revision : '';
			$rev_remote    = isset($submeat['remote']) ? $submeat['remote']->revision : '';
			printf("  %-24.24s | %7.7s | %7.7s\n", $name, $rev_installed, $rev_remote);
		}
	}
}

function command_show($goption, $coption, $cparams) {
	global $repos;
	global $modslib;

	foreach($cparams as $cparam) {
		$found=NULL;
		$mod=new TikiMod($cparam);
		foreach($repos as $reponame => $repo) {
			$content=$modslib->read_list($repo['url'], $reponame);
			if (isset($content[$mod->type][$mod->name])) {
				$found=$content[$mod->type][$mod->name];
				break;
			}
		}
		
		if ($found === NULL) {
			echo "mod '".$mod->modname."' not found\n";
			continue;
		} else $mod=$found;

		if ($mod->repository !== NULL) echo "Repository:\n".$mod->repository."\n\n";
		echo "modname;\n".$mod->modname."\n\n";
		echo "Type:\n".$mod->type."\n\n";
		echo "Name:\n".$mod->name."\n\n";
		echo "Revision:\n".$mod->revision."\n\n";
		
		if (is_array($mod->author) && count($mod->author)) {
			echo "Author:\n";
			foreach($mod->author as $author) {
				echo $author."\n";
			}
			echo "\n";
		}
		
		if (is_array($mod->description) && count($mod->description)) {
			echo "Description:\n";
			foreach($mod->description as $description) {
				echo $description."\n";
			}
			echo "\n";
		}
		
		if ($mod->license !== NULL)
			echo "License:\n".$mod->license."\n\n";
		
		if (is_array($mod->version) && count($mod->version)) {
			echo "Version:\n";
			foreach($mod->version as $version) {
				echo $version."\n";
			}
			echo "\n";
		}
		
		if ($mod->md5 !== NULL)
			echo "md5:\n".$mod->md5."\n\n";
		
		if ($mod->lastmodif !== NULL)
			echo "lastmodif:\n".$mod->lastmodif."\n\n";
		
		if (is_array($mod->configuration) && count($mod->configuration)) {
			echo "Configuration:\n";
			foreach($mod->configuration as $configuration) {
				echo $configuration."\n";
			}
			echo "\n";
		}
		
		if (is_array($mod->configuration_help) && count($mod->configuration_help)) {
			echo "Configuration Help:\n";
			foreach($mod->configuration_help as $configuration_help) {
				echo $configuration_help."\n";
			}
			echo "\n";
		}
		
		if (is_array($mod->files) && count($mod->files)) {
			echo "Files:\n";
			foreach($mod->files as $files) {
				echo $files."\n";
			}
			echo "\n";
		}
		
		if (is_array($mod->contributor) && count($mod->contributor)) {
			echo "Contributor:\n";
			foreach($mod->contributor as $contributor) {
				echo $contributor."\n";
			}
			echo "\n";
		}
		
		if (is_array($mod->devurl) && count($mod->devurl)) {
			echo "devurl:\n";
			foreach($mod->devurl as $devurl) {
				echo $devurl."\n";
			}
			echo "\n";
		}
		
		if (is_array($mod->docurl) && count($mod->docurl)) {
			echo "docurl:\n";
			foreach($mod->docurl as $docurl) {
				echo $docurl."\n";
			}
			echo "\n";
		}
		
		if (is_array($mod->help) && count($mod->help)) {
			echo "Help:\n";
			foreach($mod->help as $help) {
				echo $help."\n";
			}
			echo "\n";
		}
		
		if (is_array($mod->url) && count($mod->url)) {
			echo "url:\n";
			foreach($mod->url as $url) {
				echo $url."\n";
			}
			echo "\n";
		}
		
		if (is_array($mod->sql_upgrade) && count($mod->sql_upgrade)) {
			echo "sql_Upgrade:\n";
			foreach($mod->sql_upgrade as $sql_upgrade) {
				echo $sql_upgrade."\n";
			}
			echo "\n";
		}
		
		if (is_array($mod->sql_install) && count($mod->sql_install)) {
			echo "sql_Install:\n";
			foreach($mod->sql_install as $sql_install) {
				echo $sql_install."\n";
			}
			echo "\n";
		}
		
		if (is_array($mod->sql_remove) && count($mod->sql_remove)) {
			echo "sql_Remove:\n";
			foreach($mod->sql_remove as $sql_remove) {
				echo $sql_remove."\n";
			}
			echo "\n";
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
