<?php

class TikiAddons_Api extends TikiAddons_Utilities
{

	protected static $objects = array();

	private function loadObjects($folder) {
		if (!$this->isInstalled($folder)) {
			return array();
		}

		$ret = array();
		$table = $this->table('tiki_profile_symbols');

		$domain = 'file://addons/' . $folder . '/profiles';
		$installedProfiles = $this->getInstalledProfiles($folder);
		$profiles = array_unique(array_keys($installedProfiles));

		$all_info = array();
		foreach ($profiles as $profile) {
			$info = $table->fetchAll(
				array('object', 'type', 'value'),
				array('domain' => $domain, 'profile' => $profile)
			);
			$all_info = array_merge($all_info, $info);
		}
		foreach($all_info as $v) {
			$ret[$v['object']] = array('type' => $v['type'], 'id' => $v['value']);
		}

		self::$objects[$folder] = $ret;

		return $ret;
	}

	function getObjects($folder)
	{
		if (!empty(self::$objects[$folder])) {
			return self::$objects[$folder];
		} else {
			return $this->loadObjects($folder);
		}
	}

	function getObjectsFromToken($token)
	{
		$folder = $this->getFolderFromToken($token);
		return $this->getObjects($folder);
	}

	function getFolderFromToken($token)
	{
		$pos1 = strpos($token, '_');
		if ($pos1) {
			if ($pos2 = strpos($token, '_', $pos1 + 1)) {
				$folder = substr($token, 0, $pos2);
				return $folder;
			} elseif ($pos2 === false) {
				return $token;
			}
		}
		return '';
	}

	function getItemIdFromToken($token)
	{
		if (!$this->isInstalled($this->getFolderFromToken($token))) {
			return '';
		}

		preg_match('/\d+/', $token, $matches);
		if (!$matches[0]) {
			return '';
		}
		return $matches[0];
	}

	function getItemTitleFromToken($token, $type, $ref)
	{
		$objects = $this->getObjectsFromToken($token);
		if (empty($objects[$ref])) {
			return '';
		}

		$ret = '';
		if ($type == 'tracker') {
			$ret = TikiLib::lib('trk')->get_isMain_value($objects[$ref]['id'], $this->getItemIdFromToken($token));
		}

		return $ret;
	}

	function getItemIdFromRef($token, $ref)
	{
		$objects = $this->getObjectsFromToken($token);
		return $objects[$ref]['id'];
	}
}
