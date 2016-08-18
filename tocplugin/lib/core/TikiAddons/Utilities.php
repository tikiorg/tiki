<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiAddons_Utilities extends TikiDb_Bridge
{
	function checkDependencies($folder) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$package = $folder;
			$folder = str_replace('/', '_', $folder);
		} else {
			$package = str_replace('_', '/', $folder);
		}
		$installed = array();
		$versions = array();
		$depends = array();
		foreach (Tikiaddons::getInstalled() as $conf) {
			if ($package == $conf->package) {
				$depends = $conf->depends;
			}
			$versions[$conf->package] = $conf->version;
			$installed[] = $conf->package;
		}
		foreach ($depends as $depend) {
			if (!in_array($depend->package, $installed)) {
				throw new Exception($package . tra(' cannot load because the following dependency is missing: ') . $depend->package);
			}
			if (!$this->checkVersionMatch($versions[$depend->package], $depend->version)) {
				throw new Exception($package . tra(' cannot load because a required version of a dependency is missing: ') . $depend->package . ' version ' . $depend->version);
			}
			$this->checkProfilesInstalled($depend->package, $depend->version);
		}
		return true;
	}

	function checkProfilesInstalled($folder, $version) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$package = $folder;
			$folder = str_replace('/', '_', $folder);
		} else {
			$package = str_replace('_', '/', $folder);
		}
		$profiles = $this->getInstalledProfiles($folder);
		foreach (glob(TIKI_PATH . '/addons/' . $folder . '/profiles/*.yml') as $file) {
			$profileName = str_replace('.yml', '', basename($file));
			if (!array_key_exists($profileName, $profiles)) {
				throw new Exception(tra('This profile for this addon has not yet been installed: ') . $package . ' - ' . $profileName);
			} else {
				$versionok = false;
				foreach ($profiles[$profileName] as $versionInstalled) {
					if ($this->checkVersionMatch($versionInstalled, $version)) {
						$versionok = true;
					}
				}
				if (!$versionok) {
					throw new Exception(tra('This profile for this version of the addon has not yet been installed: ') . $package . ' version ' . $version . ' - ' . $profileName);
				}
			}
		}
		return true;
	}

	function checkVersionMatch($version, $pattern) {
		$semanticVersion =$this->getSemanticVersion($version);
		$semanticPattern = $this->getSemanticVersion($pattern);
		foreach ($semanticPattern as $k => $v) {
			if (!isset($semanticVersion[$k])) {
				$semanticVersion[$k] = 0;
			}
			if (strpos($v, '-') !== false) {
				if ((int) $semanticVersion[$k] > (int) str_replace('-', '', $v)) {
					return false;
				}
			} elseif (strpos($v, '+') !== false) {
				if ((int) $semanticVersion[$k] < (int) str_replace('+', '', $v)) {
					return false;
				}
			} elseif ($v != '*') {
				if ((int) $semanticVersion[$k] !== (int) $v) {
					return false;
				}
			}
		}
		return true;
	}

	function getSemanticVersion($version)
	{
		return explode('.', $version);
	}

	function isInstalled($folder) {
		$installed = array_keys(Tikiaddons::getInstalled());
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$package = $folder;
		} else {
			$package = str_replace('_', '/', $folder);
		}
		if (in_array($package, $installed)) {
			return true;
		} else {
			return false;
		}
	}

	function getInstalledProfiles($folder) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$package = $folder;
		} else {
			$package = str_replace('_', '/', $folder);
		}
		$ret = array();
		$result = $this->table('tiki_addon_profiles')->fetchAll(array('profile', 'version'), array('addon' => $package));
		foreach ($result as $res) {
			$ret[$res['profile']][] = $res['version'];
		}
		return $ret;
	}

	function forgetProfileAllVersions($folder, $profile) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$package = $folder;
		} else {
			$package = str_replace('_', '/', $folder);
		}
		$this->table('tiki_addon_profiles')->deleteMultiple(array('addon' => $package, 'profile' => $profile));
	}

	function forgetProfile($folder, $version, $profile) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$package = $folder;
		} else {
			$package = str_replace('_', '/', $folder);
		}
		$this->table('tiki_addon_profiles')->delete(array('addon' => $package, 'version' => $version, 'profile' => $profile));
	}

	function updateProfile($folder, $version, $profile) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$package = $folder;
		} else {
			$package = str_replace('_', '/', $folder);
		}
		$this->table('tiki_addon_profiles')->insertOrUpdate(array('addon' => $package, 'version' => $version, 'profile' => $profile), array());
		return true;
	}

	function removeObject($objectId, $type) {
		if (empty($objectId) || empty($type)) {
			return;
		}
		// TODO add other types
		if ($type == 'wiki_page' || $type == 'wiki' || $type == 'wiki page' || $type == 'wikipage') {
			TikiLib::lib('tiki')->remove_all_versions($objectId);
		}
		if ($type == 'tracker' || $type == 'trk') {
			TikiLib::lib('trk')->remove_tracker($objectId);
		}
		if ($type == 'category' || $type == 'cat') {
			TikiLib::lib('categ')->remove_category($objectId);
		}
		if ($type == 'file_gallery' || $type == 'file gallery' || $type == 'filegal' || $type == 'fgal' || $type == 'filegallery') {
			TikiLib::lib('filegal')->remove_file_gallery($objectId);
		}
		if ($type == 'activity' || $type == 'activitystream' || $type == 'activity_stream' || $type == 'activityrule' || $type == 'activity_rule') {
			TikiLib::lib('activity')->deleteRule($objectId);
		}
		if ($type == 'forum' || $type == 'forums') {
			TikiLib::lib('comments')->remove_forum($objectId);
		}
		if ($type == 'trackerfield' || $type == 'trackerfields' || $type == 'tracker field') {
			$trklib = TikiLib::lib('trk');
			$res = $trklib->get_tracker_field($objectId);
			$trklib->remove_tracker_field($objectId, $res['trackerId']);
		}
		if ($type == 'module' || $type == 'modules') {
			$modlib = TikiLib::lib('mod');
			$modlib->unassign_module($objectId);
		}
	}

	function getObjectId($folder, $ref, $profile = '', $domain = '') {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$folder = str_replace('/', '_', $folder);
		}
		if (empty($domain)) {
			$domain = 'file://addons/' . $folder . '/profiles';
		}

		if (!$profile) {
			if ($this->table('tiki_profile_symbols')->fetchCount(array('domain' => $domain, 'object' => $ref)) > 1) {
				return $this->table('tiki_profile_symbols')->fetchColumn('value', array('domain' => $domain, 'object' => $ref));
			} else {
				return $this->table('tiki_profile_symbols')->fetchOne('value', array('domain' => $domain, 'object' => $ref));
			}
		} else {
			return $this->table('tiki_profile_symbols')->fetchOne('value', array('domain' => $domain, 'object' => $ref, 'profile' => $profile));
		}
	}

	function getLastVersionInstalled($folder) {
		if (strpos($folder, '/') !== false && strpos($folder, '_') === false) {
			$package = $folder;
		} else {
			$package = str_replace('_', '/', $folder);
		}
		$versions = array();
		$result = $this->table('tiki_addon_profiles')->fetchAll(array('version'), array('addon' => $package));
		foreach ($result as $res) {
			$versions[] = $res['version'];
		}
		natsort($versions);
		return array_pop($versions);
	}

	function getFolderFromObject($type, $id) {
		$type = Tiki_Profile_Installer::convertTypeInvert($type);
		$domain = $this->table('tiki_profile_symbols')->fetchOne('domain', array('value' => $id, 'type' => $type));	
		$folder = str_replace('file://addons/', '', $domain);
		$folder = str_replace('/profiles', '', $folder);
		return $folder;
	}

	function getAddonFilePath($filepath) {
		foreach (TikiAddons::getPaths() as $path) {
			if (file_exists($path."/".$filepath)) {
				return $path."/".$filepath;
			}
		}
		return false;
	}
}
