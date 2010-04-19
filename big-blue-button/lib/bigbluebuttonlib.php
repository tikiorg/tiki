<?php

class BigBlueButtonLib
{
	public function getMeetings() {
		global $cachelib;

		if( ! $meetings = $cachelib->getSerialized( 'bbb_meetinglist' ) ) {
			$dom = $this->performRequest( 'getMeetings', array( 'random' => 1 ) );

			$meetings = array();

			foreach( $dom->getElementsByTagName( 'meeting' ) as $node ) {
				$meeting = array();

				foreach( $node->childNodes as $n ) {
					if( $n instanceof DOMElement ) {
						$meeting[$n->tagName] = $n->textContent;
					}
				}

				$meetings[] = $meeting;
			}

			$cachelib->cacheItem( 'bbb_meetinglist', serialize( $meetings ) );
		}

		return $meetings;
	}

	public function joinMeeting( $room ) {
		$name = $this->getAttendeeName();
		$password = $this->getAttendeePassword( $room );

		if( $name && $password ) {
			$this->joinRawMeeting( $room, $name, $password );
		}
	}

	private function getAttendeeName() {
		global $u_info;

		if( isset( $u_info['prefs']['realName'] ) ) {
			return $u_info['prefs']['realName'];
		} elseif( $u_info['login'] ) {
			return $u_info['login'];
		} else {
			return tra('anonymous');
		}
	}

	private function getAttendeePassword( $room ) {
		$meetings = $this->getMeetings();
		$meetings = Perms::filter( array( 'type' => 'bigbluebutton' ), 'object', $meetings, array( 'object' => 'meetingID' ), 'bigbluebutton_join' );

		foreach( $meetings as $meeting ) {
			if( $meeting['meetingID'] == $room ) {
				$perms = Perms::get( 'bigbluebutton', $room );

				if( $perms->bigbluebutton_moderate ) {
					return $meeting['moderatorPW'];
				} else {
					return $meeting['attendeePW'];
				}
			}
		}
	}

	public function joinRawMeeting( $room, $name, $password ) {
		$url = $this->buildUrl( 'join', array(
			'meetingID' => $room,
			'fullName' => $name,
			'password' => $password,
		) );

		header( 'Location: ' . $url );
		exit;
	}

	private function performRequest( $action, array $parameters ) {
		global $tikilib;

		$url = $this->buildUrl( $action, $parameters );

		if( $result = $tikilib->httprequest( $url ) ) {
			$dom = new DOMDocument;
			if( $dom->loadXML( $result ) ) {
				$nodes = $dom->getElementsByTagName( 'returncode' );

				if( $nodes->length > 0 && ($returnCode = $nodes->item(0)) && $returnCode->textContent == 'SUCCESS' ) {
					return $dom;
				}
			}
		}
	}

	private function buildUrl( $action, array $parameters ) {
		global $prefs;
		if( $checksum = $this->generateChecksum( $parameters ) ) {
			$parameters['checksum'] = $checksum;
		}

		$base = rtrim( $prefs['bigbluebutton_server_location'], '/' );

		return "$base/bigbluebutton/api/$action?" . http_build_query( $parameters, '', '&' );
	}

	private function generateChecksum( array $parameters ) {
		global $prefs;

		if( $prefs['bigbluebutton_server_salt'] ) {
			$query = http_build_query( $parameters, '', '&' );
			return sha1( $query . $prefs['bigbluebutton_server_salt'] );
		}
	}
}

global $bigbluebuttonlib;
$bigbluebuttonlib = new BigBlueButtonLib;

