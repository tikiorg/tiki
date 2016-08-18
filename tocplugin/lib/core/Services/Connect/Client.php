<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

		$this->connectlib = TikiLib::lib('connect');
		$this->remote = new Services_RemoteController($prefs['connect_server'], 'connect_server');

	}

	function action_vote($input)
	{
		global $prefs;

		if (! Perms::get()->admin) {
			throw new Services_Exception(tr('Reserved for administrators during development'), 403);
		}

		if (empty($prefs['connect_guid'])) {
			throw new Services_Exception(tr('Tiki not connected. Please click "Send Info" to join in!'), 403);
		}

		$vote = $input->vote->text();
		$pref = $input->pref->text();

		$votes = $this->connectlib->getVotes(true);
		if (!isset( $votes->$pref )) {
			$votes->$pref = array();
		}
		$arr = $votes->$pref;

		if (substr($vote, 0, 2) === 'un') {
			$vote  = substr($vote, 2);
			unset($arr[ array_search($vote, $arr)]);
		} else if (!in_array($vote, $arr)) {
			$arr[] = $vote;
			$vote = 'un' . $vote;	// send back the opposite vote to update the icon
		}
		if ($votes->$pref != $arr) {
			$votes->$pref = $arr;
			$this->connectlib->saveVotesForGuid($prefs['connect_guid'], $votes);
		}

		return array( 'newVote' => $vote );
	}

	function action_list($input = null)
	{
		if (! Perms::get()->admin) {
			throw new Services_Exception(tr('Reserved for administrators during development'), 403);
		}
		$info = $this->connectlib->buildConnectData();

		//$info = $this->connectlib->diffDataWithLastSent($info);
		return $info;
	}

	function action_send($input)
	{
		global $prefs;

		if (! Perms::get()->admin) {
			throw new Services_Exception(tr('Reserved for administrators during development'), 403);
		}

		$tikilib = TikiLib::lib('tiki');
		$confirmedGuid = $this->connectlib->getConfirmedGuid();

		if ( empty($confirmedGuid) || empty($prefs['connect_guid']) || $prefs['connect_guid'] !== $confirmedGuid) {	// not connected?

			$pending = $this->connectlib->getPendingGuid();

			if (empty($pending)) {
				$data = $this->remote->new();

				if ($data && $data['status'] === 'pending' && !empty($data['guid'])) {
					$this->connectlib->recordConnection($data['status'], $data['guid']);
				} else {
					$data = array(
						'status' => 'error',
						'message' => empty($data['message']) ? tra('There was an error (Tiki Connect is still experimental). Please try again.') . ' (' . tra('registration') . ')' : $data['message'],
					);
				}

			} else {
				$data = $this->remote->confirm(
					array(
						'connect_data' => array(
							'guid' => $pending,
							'captcha' => $input->captcha->filter(),
						)
					)
				);

				$this->connectlib->removeGuid($pending);

				if ($data && !empty($data['guid']) && $data['status'] === 'confirmed' && $data['guid'] === $pending) {
					$tikilib->set_preference('connect_guid', $pending);
					$this->connectlib->recordConnection($data['status'], $pending);
				} else {
					$data = array(
						'status' => 'error',
						'message' => empty($data['message']) ? tra('There was an error (Tiki Connect is still experimental). Please try again.') . ' (' . tra('confirmation') . ')' : $data['message'],
					);
				}
			}

		} else {
			$odata = $this->connectlib->buildConnectData();	//$this->action_list();
			//$diffdata = $this->connectlib->diffDataWithLastSent($odata);	// maybe later

			$odata['guid'] = $prefs['connect_guid'];


			$data = $this->remote->receive(array( 'connect_data' => $odata ));

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

	function action_cancel($input)
	{
		$guid = $input->guid->text();
		if ($guid) {
			$this->connectlib->removeGuid($guid);
			$r = $this->remote->cancel(array('connect_data' => array('guid' => $guid)));
		}
		return array('guid' => $guid);
	}

}

