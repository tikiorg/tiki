<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 *
 */
class BigBlueButtonLib
{
	private $version = false;

    /**
     * @return bool|string
     */
    private function getVersion()
	{
		if ( $this->version !== false ) {
			return $this->version;
		}

		if ( $version = $this->performRequest('', array()) ) {
			$values = $this->grabValues($version->documentElement);
			$version = $values['version'];

			if ( false !== $pos = strpos($version, '-') ) {
				$version = substr($version, 0, $pos);
			}

			$this->version = $version;
		} else {
			$this->version = '0.6';
		}

		return $this->version;
	}

    /**
     * @return array|mixed
     */
    public function getMeetings()
	{
		global $cachelib;

		if ( ! $meetings = $cachelib->getSerialized('bbb_meetinglist') ) {
			$meetings = array();

			if ( $dom = $this->performRequest('getMeetings', array('random' => 1)) ) {
				foreach ( $dom->getElementsByTagName('meeting') as $node ) {
					$meetings[] = $this->grabValues($node);
				}
			}

			$cachelib->cacheItem('bbb_meetinglist', serialize($meetings));
		}

		return $meetings;
	}

    /**
     * @param $room
     * @return array
     */
    public function getAttendees( $room )
	{
		if ( $meeting = $this->getMeeting($room) ) {
			if ( $dom = $this->performRequest('getMeetingInfo', array('meetingID' => $room, 'password' => $meeting['moderatorPW'])) ) {

				$attendees = array();

				foreach ( $dom->getElementsByTagName('attendee') as $node ) {
					$attendees[] = $this->grabValues($node);
				}

				return $attendees;
			}
		}
	}

    /**
     * @param $node
     * @return array
     */
    private function grabValues( $node )
	{
		$values = array();

		foreach ( $node->childNodes as $n ) {
			if ( $n instanceof DOMElement ) {
				$values[$n->tagName] = $n->textContent;
			}
		}

		return $values;
	}

    /**
     * @param $room
     * @return bool
     */
    public function roomExists( $room )
	{
		foreach ( $this->getMeetings() as $meeting ) {
			if ( $meeting['meetingID'] == $room ) {
				return true;
			}
		}

		return false;
	}

    /**
     * @param $room
     * @param array $params
     */
    public function createRoom( $room, array $params = array() )
	{
		global $tikilib, $cachelib, $prefs;

		$params = array_merge(
			array('logout' => $tikilib->tikiUrl(''),),
			$params
		);

		$request = array(
				'name' => $room,
				'meetingID' => $room,
				'logoutURL' => $params['logout'],
		);

		if ( isset($params['welcome']) ) {
			$request['welcome'] = $params['welcome'];
		}

		if ( isset($params['number']) ) {
			$request['dialNumber'] = $params['number'];
		}

		if ( isset($params['voicebridge']) ) {
			$request['voiceBridge'] = $params['voicebridge'];
		} else {
			$request['voiceBridge'] = '7' . rand(0, 9999);
		}

		if ( isset($params['logout']) ) {
			$request['logoutURL'] = $params['logout'];
		}

		if ( isset($params['recording']) && $params['recording'] > 0 && $this->isRecordingSupported() ) {
			$request['record'] = 'true';
			$request['duration'] = $prefs['bigbluebutton_recording_max_duration'];
		}

		$this->performRequest('create', $request);
		$cachelib->invalidate('bbb_meetinglist');
	}

    /**
     * @param $room
     */
    public function joinMeeting( $room )
	{
		$version = $this->getVersion();

		$name = $this->getAttendeeName();
		$password = $this->getAttendeePassword($room);

		if ( $name && $password ) {
			$this->joinRawMeeting($room, $name, $password);
		}
	}

    /**
     * @param $recordingID
     */
    public function removeRecording($recordingID)
	{
		if ($this->isRecordingSupported()) {
			$this->performRequest(
				'deleteRecordings',
				array('recordID' => $recordingID)
			);
		}
	}

    /**
     * @return bool|mixed|null|string
     */
    private function getAttendeeName()
	{
		global $user, $tikilib;

		if ( $realName = $tikilib->get_user_preference($user, 'realName') ) {
			return $realName;
		} elseif ( $user ) {
			return $user;
		} elseif (!empty($_SESSION['bbb_name'])) {
			return $_SESSION['bbb_name'];
		} else {
			return tra('anonymous');
		}
	}

    /**
     * @param $room
     * @return mixed
     */
    private function getAttendeePassword( $room )
	{
		if ( $meeting = $this->getMeeting($room) ) {
			$perms = Perms::get('bigbluebutton', $room);

			if ( $perms->bigbluebutton_moderate ) {
				return $meeting['moderatorPW'];
			} else {
				return $meeting['attendeePW'];
			}
		}
	}

    /**
     * @param $room
     * @return mixed
     */
    private function getMeeting( $room )
	{
		$meetings = $this->getMeetings();

		foreach ( $meetings as $meeting ) {
			if ( $meeting['meetingID'] == $room ) {
				return $meeting;
			}
		}
	}

    /**
     * @param $room
     * @param $name
     * @param $password
     */
    public function joinRawMeeting( $room, $name, $password )
	{
		$url = $this->buildUrl(
			'join',
			array(
				'meetingID' => $room,
				'fullName' => $name,
				'password' => $password,
			)
		);

		header('Location: ' . $url);
		exit;
	}

    /**
     * @param $action
     * @param array $parameters
     * @return DOMDocument
     */
    private function performRequest( $action, array $parameters )
	{
		global $tikilib;

		$url = $this->buildUrl($action, $parameters);

		if ( $result = $tikilib->httprequest($url) ) {
			$dom = new DOMDocument;
			if ( $dom->loadXML($result) ) {
				$nodes = $dom->getElementsByTagName('returncode');

				if ( $nodes->length > 0 && ($returnCode = $nodes->item(0)) && $returnCode->textContent == 'SUCCESS' ) {
					return $dom;
				}
			}
		}
	}

    /**
     * @param $action
     * @param array $parameters
     * @return string
     */
    private function buildUrl( $action, array $parameters )
	{
		global $prefs;

		if ( $action ) {
			if ( $checksum = $this->generateChecksum($action, $parameters) ) {
				$parameters['checksum'] = $checksum;
			}
		}

		$base = rtrim($prefs['bigbluebutton_server_location'], '/');

		if (parse_url($base, PHP_URL_PATH)) {
			$url = "$base/api/$action?" . http_build_query($parameters, '', '&');
		} else {
			$url = "$base/bigbluebutton/api/$action?" . http_build_query($parameters, '', '&');
		}
		return $url;
	}

    /**
     * @param $action
     * @param array $parameters
     * @return string
     */
    private function generateChecksum( $action, array $parameters )
	{
		global $prefs;

		if ( $prefs['bigbluebutton_server_salt'] ) {
			$query = http_build_query($parameters, '', '&');

			$version = $this->getVersion();

			if ( -1 === version_compare($version, '0.7') ) {
				return sha1($query . $prefs['bigbluebutton_server_salt']);
			} else {
				return sha1($action . $query . $prefs['bigbluebutton_server_salt']);
			}
		}
	}

    /**
     * @return bool
     */
    private function isRecordingSupported()
	{
		$version = $this->getVersion();
		return version_compare($version, '0.8') >= 0;
	}

    /**
     * @param $room
     * @return array
     */
    public function getRecordings( $room )
	{
		if (! $this->isRecordingSupported()) {
			return array();
		}

		$result = $this->performRequest(
			'getRecordings',
			array('meetingID' => $room,)
		);

		$data = array();
		$recordings = $result->getElementsByTagName('recording');

		foreach ($recordings as $recording) {
			$recording = simplexml_import_dom($recording);
			if ($recording->published == 'false') {
				$published = false;
			} else {
				$published = true;
			}
			$info = array(
					'recordID' => (string) $recording->recordID,
					'startTime' => floor(((string) $recording->startTime)/1000),
					'endTime' => ceil(((string) $recording->endTime)/1000),
					'playback' => array(),
					'published' => $published,
			);

			foreach ($recording->playback as $playback) {
				$info['playback'][ (string) $playback->format->type ] = (string) $playback->format->url;
			}

			$data[] = $info;
		}

		usort($data, array("BigBlueButtonLib", "cmpStartTime"));
		return $data;
	}

    /**
     * @param $a
     * @param $b
     * @return int
     */
    private static function cmpStartTime( $a, $b )
	{
		if ($a['startTime'] == $b['startTime']) {
			return 0;
		}
		return ($a['startTime'] > $b['startTime']) ? -1 : 1;
	}
}

global $bigbluebuttonlib;
$bigbluebuttonlib = new BigBlueButtonLib;

