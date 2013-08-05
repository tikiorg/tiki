<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
				'name' => tr('Show.tiki.org'),
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
						'name' => tr('Shell user name on remote server'),
						'description' => tr('The shell user name on the show server'),
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
			'snapshoturl' => '',
		);

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

		$userid = TikiLib::lib('tiki')->get_user_id($creator);
		if (!$userid) {
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

		stream_set_blocking( $infostream, TRUE );
		$infooutput = stream_get_contents( $infostream );
		$ret['debugoutput'] = $infostring . " " . $infooutput;
		
		$statuspos = strpos($infooutput, 'STATUS: ');
		$status = substr($infooutput, $statuspos + 8, 5);
		$status = trim($status);
		if (!$status || $status == 'FAIL') {
			$ret['status'] = 'FAIL';
		} else {
			$ret['status'] = $status;
		}	

		$ret['showurl'] = 'http://' . $ret['username'] . '-' . $ret['userid'] . '-' . $ret['id'] . '.' . $this->getOption('domain');
		$ret['snapshoturl'] = $ret['showurl'] . '/snapshots/';

		$cachelib->cacheItem($cacheKey, serialize($ret));

		// Note that one should never cache canDestroy = true
		if (TikiLib::lib('user')->user_has_permission($user, 'tiki_p_admin') || $user == $creator) {
			$ret['canDestroy'] = true;
		}

		return $ret;
	}

	function renderInput($context = array()) {		
		return $this->renderTemplate('trackerinput/showtikiorg.tpl', $context);
	}

	function renderOutput($context = array()) {
		return $this->renderTemplate('trackerinput/showtikiorg.tpl', $context);
	}


}

