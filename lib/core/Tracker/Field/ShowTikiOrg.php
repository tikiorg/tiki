<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_ShowTikiOrg extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		global $prefs;

		return array(
			'STO' => array(
				'name' => tr('show.tiki.org'),
				'description' => tr('Create, display or manage show.tiki.org instances.'),
				'prefs' => array('trackerfield_showtikiorg'),
				'tags' => array('experimental'),
				'help' => 'show.tiki.org',
				'default' => 'n',
				'params' => array(
					'domain' => array(
						'name' => tr('Domain name of show server'),
						'description' => tr('For example, show.tiki.org'),
						'filter' => 'text',
						'legacy_index' => 0,
					),
					'remoteShellUser' => array(
						'name' => tr('Shell username on remote server'),
						'description' => tr('The shell username on the show server'),
						'filter' => 'text',
						'legacy_index' => 1,
					),
					'publicKey' => array(
						'name' => tr('Public key file path'),
						'description' => tr('System path to public key on local server'),
						'filter' => 'text',
						'legacy_index' => 2,
					),
					'privateKey' => array(
						'name' => tr('Private key file path'),
						'description' => tr('System path to private key on local server'),
						'filter' => 'text',
						'legacy_index' => 3,
					),
					'debugMode' => array(
						'name' => tr('Show debugging information'),
						'description' => tr('Show debugging info during testing'),
						'filter' => 'int',
                                                'options' => array(
                                                        0 => tr('No'),
                                                        1 => tr('Yes'),
                                                ),
						'legacy_index' => 4,
					),
					'fixedUserId' => array(
						'name' => tr('Fixed user ID'),
						'description' => tr('Set fixed user ID instead of using the user ID of the creator of the tracker item'),
						'filter' => 'int',
						'legacy_index' => 5,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		global $user;

		$ret = array(
			'id' => 0,
			'userid' => 0,
			'status' => 'DISCO',
			'username' => '',
			'debugmode' => $this->getOption('debugMode'),
			'canDestroy' => false,
			'debugoutput' => '',
			'showurl' => '',
			'showlogurl' => '',
			'snapshoturl' => '',
			'value' => 'none', // this is required to show the field, otherwise it gets hidden if tracker is set to doNotShowEmptyField
		);

		if (!function_exists('ssh2_connect')) {
			$ret['status'] = 'NOSSH';
			return $ret;
		}

		$id = $this->getItemId();
		if (!$id) {
			return $ret;
		} else {
			$ret['id'] = $id;
		}

		// get cache to prevent too many hits to show.tiki.org
		$cachelib = TikiLib::lib('cache');

		$cacheKey = 'STO-' . $this->getOption('domain') . '-' . $this->getConfiguration('fieldId') . "-" . $id;
		if ($data = $cachelib->getSerialized($cacheKey)) {
			$creator = TikiLib::lib('tiki')->get_user_login($data['userid']);
			if (TikiLib::lib('user')->user_has_permission($user, 'tiki_p_admin') || $user == $creator) {
				$data['canDestroy'] = true;
			}
			return $data;
		}

		$item = TikiLib::lib('trk')->get_tracker_item($id);
		$creator = $item['createdBy'];
		if (!$creator) {
			$creator = TikiLib::lib('trk')->get_item_creator($item['trackerId'], $id);
		}

		if ($this->getOption('fixedUserId') > 0) {
			$userid = $this->getOption('fixedUserId');
		} else {
			$userid = TikiLib::lib('tiki')->get_user_id($creator);
		}

		if (!$userid || !$creator) {
			return $ret;
		} else {
			$ret['userid'] = $userid;
		}

		if (ctype_alnum($creator)) {
			$ret['username'] = $creator;
		} else {
			$ret['username'] = 'user';
		}

		$conn = ssh2_connect($this->getOption('domain'), 22);
		$conntry = ssh2_auth_pubkey_file(
			$conn,
			$this->getOption('remoteShellUser'),
			$this->getOption('publicKey'),
			$this->getOption('privateKey')
		);

		if (!$conntry) {
			$ret['status'] = 'DISCO';
			return $ret;
		}

		$infostring = "info -i $id -U $userid";
		$infostream = ssh2_exec($conn, $infostring);

		stream_set_blocking($infostream, TRUE);
		$infooutput = stream_get_contents($infostream);
		$ret['debugoutput'] = $infostring . " " . $infooutput;

		if (strpos($infooutput, 'MAINTENANCE: ') !== false) {
			$maintpos = strpos($infooutput, 'MAINTENANCE: ');
			$maintreason = substr($infooutput, $maintpos + 13);
			$maintreason = substr($maintreason, 0, strpos($maintreason, '"'));
			$ret['maintreason'] = $maintreason;
			$ret['status'] = 'MAINT';
			return $ret;
		}

		$versionpos = strpos($infooutput, 'VERSION: ');
		$version = substr($infooutput, $versionpos + 9);
		$version = substr($version, 0, strpos($version, PHP_EOL));
		$version = trim($version);
		$ret['version'] = $version;

		$statuspos = strpos($infooutput, 'STATUS: ');
		$status = substr($infooutput, $statuspos + 8, 5);
		$status = trim($status);
		if (!$status || $status == 'FAIL') {
			$ret['status'] = 'FAIL';
		} else {
			$ret['status'] = $status;
			$sitepos = strpos($infooutput, 'SITE: ');
			$site = substr($infooutput, $sitepos + 6);
			$site = substr($site, 0, strpos($site, ' '));
			$ret['showurl'] = $site;
			$ret['showlogurl'] = $site . '/info.txt';
			$ret['snapshoturl'] = $site . '/snapshots/';
			if ($site) {
				$ret['value'] = 'active ' . substr($site, 0, strpos($site, '.')); // the 'active' is useful for filtering on
			}
		}

		$cachelib->cacheItem($cacheKey, serialize($ret));

		// Note that one should never cache canDestroy = true
		if (TikiLib::lib('user')->user_has_permission($user, 'tiki_p_admin') || $user == $creator) {
			$ret['canDestroy'] = true;
		}

		return $ret;
	}

	function renderInput($context = array())
	{
		return $this->renderTemplate('trackerinput/showtikiorg.tpl', $context);
	}

	function renderOutput($context = array())
	{
		return $this->renderTemplate('trackerinput/showtikiorg.tpl', $context);
	}
}
