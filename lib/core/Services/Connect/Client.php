<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Connect_Client
{

	private $connectlib;
	private $remote;

	function setUp()
	{
		global $prefs;

		if ($prefs['connect_feature'] !== 'y') {
			throw new Services_Exception(tr('Feature disabled'), 403);
		}

		include_once 'lib/core/TikiConnect.php';
		$this->connectlib = new TikiConnect();
		$this->remote = new Services_RemoteController($prefs['connect_server'], 'connect_server');

	}

	function action_list($input = null)
	{
		if (! Perms::get()->admin) {
			throw new Services_Exception(tr('Reserved to administrators during development'), 403);
		}
		$info = $this->connectlib->buildConnectData();

		//$info = $this->connectlib->diffDataWithLastSent($info);
		return $info;
	}

	function action_send($input) {
		global $prefs;
		
		if (! Perms::get()->admin) {
			throw new Services_Exception(tr('Reserved to administrators during development'), 403);
		}

		$tikilib = TikiLib::lib('tiki');
		$confirmedGuid = $this->connectlib->getConfirmedGuid();

		if ( empty($confirmedGuid) || empty($prefs['connect_guid']) || $prefs['connect_guid'] !== $confirmedGuid) {	// not connected?

			$pending = $this->connectlib->getPendingGuid();

			if (empty($pending)) {
				$data = $this->remote->new();

				if ($data['status'] === 'pending' && !empty($data['guid'])) {
					$this->connectlib->recordConnection($data['status'], $data['guid']);
				}

			} else {
				$data = $this->remote->confirm( array(
					'connect_data' => array(
					'guid' => $pending,
					'captcha' => $input->captcha->filter(),
				)));

				$this->connectlib->removeGuid($pending);
				
				if ($data && !empty($data['guid']) && $data['status'] === 'confirmed' && $data['guid'] === $pending) {
					$tikilib->set_preference('connect_guid', $pending);
					$this->connectlib->recordConnection($data['status'], $pending);
				} else {
					$data = array(
						'status' => 'error',
						'message' => empty($data['message']) ? tra('Something went wrong. Tiki Connect is still experimental. Please try again.') : $data['message'],
					);
				}
			}

		} else {
			$odata = $this->connectlib->buildConnectData();	//$this->action_list();
			//$diffdata = $this->connectlib->diffDataWithLastSent($odata);	// maybe later

			$odata['guid'] = $prefs['connect_guid'];


			$data = $this->remote->receive( array( 'connect_data' => $odata ));

			if ($data && $data['status'] === 'received') {
				$status = 'sent';
				$this->connectlib->recordConnection($status, $prefs['connect_guid'], $odata);
			} else {
				$this->connectlib->removeGuid($confirmedGuid);
				$tikilib->set_preference('connect_guid', '');
			}
		}
		return $data;
	}

	function action_cancel($input) {
		$guid = $input->guid->filter();
		if ($guid) {
			$this->connectlib->removeGuid($guid);
			$r = $this->remote->cancel(array(
				'connect_data' => array('guid' => $guid)
			));
		}
		return array('guid' => $guid);
	}

}

