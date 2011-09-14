<?php 
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$
/**
 * This script may only be included, so it is better to die if called directly.
 */
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class watershedLib
{
	function storeSessionId( $u, $sessionId ) {
		global $tikilib;
		$tikilib->set_user_preference( $u, 'watershed_sessionId', $sessionId );
		$tikilib->set_user_preference( $u, 'watershed_sessionId_time', $tikilib->now );
		return true;
	}
	
	function getSessionId( $u ) {
		global $tikilib, $userlib;
		$userinfo = $userlib->get_user_info($u);
		if ($tikilib->get_user_preference( $u, 'watershed_sessionId_time', 0) > $userinfo["lastLogin"]) {
			$sessionId = $tikilib->get_user_preference( $u, 'watershed_sessionId', '');
			return $sessionId;	
		} else {
			return '';
		}
	}
	
	function getAllViewableChannels( $channelName = '', $brandId = '') {
		global $prefs;
		global $trklib; include_once ('lib/trackers/trackerlib.php');
		$channels = array();
		
		if ($channelName && $brandId) {
			$filterfield = array($prefs["watershed_channel_fieldId"], $prefs["watershed_brand_fieldId"]);
			$exactvalue = array($channelName, $brandId);
		} else if ($channelName) {
			$filterfield = $prefs["watershed_channel_fieldId"];
			$exactvalue = $channelName;
		} else if ($brandId) {
			$filterfield = $prefs["watershed_brand_fieldId"];
			$exactvalue = $brandId;
		} else {
			$filterfield = '';
			$exactvalue = '';
		}
		$listfields = array( $prefs["watershed_channel_fieldId"] => array('type' => 't', 'name' => 'channelCode'),
			$prefs["watershed_brand_fieldId"] => array('type' => 't', 'name' => 'brandId'));
		$items = $trklib->list_items($prefs["watershed_channel_trackerId"], 0, -1, '', $listfields, $filterfield, '', '', '', $exactvalue);
		foreach ($items["data"] as $i) {
			$brandId = '';
			$channelCode = '';
			foreach ($i["field_values"] as $fl) {
				if ($fl["fieldId"] == $prefs["watershed_brand_fieldId"]) {
					$brandId = $fl["value"]; 
				} elseif ($fl['fieldId'] == $prefs["watershed_channel_fieldId"]) {
					$channelCode = $fl["value"];
				}
			}
			if ($brandId && $channelCode) {
					$channels[] = array('itemId' => $i["itemId"], 'status' => $i["status"], 'brandId' => $brandId, 'channelCode' => $channelCode);		
			}
		}
		return $channels;
	}
	
	function filterChannels( $channels, $mode = 'viewer' ) {
		global $prefs, $tikilib;
		global $trklib; include_once ('lib/trackers/trackerlib.php');
		$tracker_info = $trklib->get_tracker($prefs["watershed_channel_trackerId"]);
		if ($t = $trklib->get_tracker_options($prefs["watershed_channel_trackerId"])) {
				$tracker_info = array_merge($tracker_info, $t);
		}
		$perms = $tikilib->get_perm_object($prefs["watershed_channel_trackerId"], 'tracker', $tracker_info, false);
		$ret = array();
		if ($mode == 'broadcaster') {
			foreach ( $channels as $c ) {		
				if ($perms['tiki_p_modify_tracker_items'] == 'y' && $c["status"] != 'p' && $c["status"] != 'c'
				|| $perms['tiki_p_modify_tracker_items_pending'] == 'y' && $c["status"] == 'p'
				|| $perms['tiki_p_modify_tracker_items_closed'] == 'y' && $c["status"] == 'c') {
					$ret[] = $c;
				}
			}
		} else if ( $mode == 'viewer') {
			foreach ( $channels as $c ) {		
				if ($perms['tiki_p_view_trackers'] == 'y' && $c["status"] != 'p' && $c["status"] != 'c'
				|| $perms['tiki_p_view_trackers_pending'] == 'y' && $c["status"] == 'p'
				|| $perms['tiki_p_view_trackers_closed'] == 'y' && $c["status"] == 'c') {
					$ret[] = $c;
				}
			}
		}
		return $ret;
	}

	function checkArchiveViewable( $videoId, $channels ) {
		global $prefs, $tikilib;
		global $trklib; include_once ('lib/trackers/trackerlib.php');
		$archive = $trklib->get_item( $prefs["watershed_archive_trackerId"], $prefs['watershed_archive_fieldId'], $videoId);
		$archiveChannelCode = $archive[$prefs["watershed_archive_channel_fieldId"]];
		$validchannel = false;
		foreach ($channels as $c) {
			if ($c["channelCode"] == $archiveChannelCode) {
				$validchannel = true;
			}
		}
		if (!$validchannel) {
			return false;
		}
		$tracker_info = $trklib->get_tracker($prefs["watershed_archive_trackerId"]);
		if ($t = $trklib->get_tracker_options($prefs["watershed_archive_trackerId"])) {
			$tracker_info = array_merge($tracker_info, $t);
		}
		$perms = $tikilib->get_perm_object($prefs["watershed_archive_trackerId"], 'tracker', $tracker_info, false);
		if ($perms['tiki_p_view_trackers'] == 'y' && $archive["status"] != 'p' && $archive["status"] != 'c'
			|| $perms['tiki_p_view_trackers_pending'] == 'y' && $archive["status"] == 'p'
			|| $perms['tiki_p_view_trackers_closed'] == 'y' && $archive["status"] == 'c') {
			return true;
		} else {
			return false;
		}
	}
	
	function storeArchive( $recording ) {
		global $prefs;
		global $trklib; include_once ('lib/trackers/trackerlib.php');
		$fields = array();
		if (empty($prefs['watershed_archive_trackerId'])) {
			return false;
		}
		if ($recording->videoAttributes->isPrivate) {
			// Handling of private videos is not a feature yet, so simply do not add into Tiki
			return false;
		}
		if (!empty($prefs['watershed_archive_fieldId'])) {
			$fields[] = array('type' => 't', 'fieldId' => $prefs['watershed_archive_fieldId'], 'value' => $recording->videoId );	
		} else {
			return false;
		}
		// Check if already sent before
		$items = $trklib->list_items($prefs["watershed_archive_trackerId"], 0, -1, '', '', $prefs['watershed_archive_fieldId'], '', '', '', $recording->videoId);
		if ($items["cant"]) {
			return false;
		}
		if (!empty($prefs['watershed_archive_brand_fieldId'])) {
			$fields[] = array('type' => 't', 'fieldId' => $prefs['watershed_archive_brand_fieldId'], 'value' => $recording->brandId );
		} else {
			return false;
		}
		if (!empty($prefs['watershed_archive_channel_fieldId'])) {
			$fields[] = array('type' => 't', 'fieldId' => $prefs['watershed_archive_channel_fieldId'], 'value' => $recording->channelCode );
		} else {
			return false;
		}
		if (!empty($prefs['watershed_archive_rtmpurl_fieldId'])) {
			$fields[] = array('type' => 'L', 'fieldId' => $prefs['watershed_archive_rtmpurl_fieldId'], 'value' => $recording->videoAttributes->rtmpUrl );
		} else {
			return false;
		}
		if (!empty($prefs['watershed_archive_flvurl_fieldId'])) {
			$fields[] = array('type' => 'L', 'fieldId' => $prefs['watershed_archive_flvurl_fieldId'], 'value' => $recording->videoAttributes->flvUrl );
		} else {
			return false;
		}
		// optional fields
		if (!empty($prefs['watershed_archive_date_fieldId'])) {
			$fields[] = array('type' => 't', 'fieldId' => $prefs['watershed_archive_date_fieldId'], 'value' => $recording->createdAt );
		} 
		if (!empty($prefs['watershed_archive_duration_fieldId'])) {
			$fields[] = array('type' => 't', 'fieldId' => $prefs['watershed_archive_duration_fieldId'], 'value' => $recording->videoAttributes->duration );
		}
		if (!empty($prefs['watershed_archive_desc_fieldId'])) {
			$fields[] = array('type' => 'a', 'fieldId' => $prefs['watershed_archive_desc_fieldId'], 'value' => $recording->videoAttributes->description );
		}
		if (!empty($prefs['watershed_archive_title_fieldId'])) {
			$fields[] = array('type' => 't', 'fieldId' => $prefs['watershed_archive_title_fieldId'], 'value' => $recording->videoAttributes->title );
		}
		if (!empty($prefs['watershed_archive_filesize_fieldId'])) {
			$fields[] = array('type' => 't', 'fieldId' => $prefs['watershed_archive_filesize_fieldId'], 'value' => $recording->videoAttributes->fileSize );
		}
		if (!empty($prefs['watershed_archive_tags_fieldId'])) {
			$fields[] = array('type' => 'F', 'fieldId' => $prefs['watershed_archive_tags_fieldId'], 'value' => implode(" ",unserialize($recording->videoAttributes->tags)) );
		}
		$ins_fields["data"] = $fields;
		$rid = $trklib->replace_item($prefs['watershed_archive_trackerId'], '', $ins_fields);
		$ins_categs = array(); // No categorization for now
		$mainfield = ''; // categorize link as just itemId for now
		$trklib->categorized_item($prefs['watershed_archive_trackerId'], $rid, $mainfield, $ins_categs);
		return true;
	}
}
	
	
// SOAP Types {{{

class Watershed_SoapServer_validateBroadcasterSession
{
	public $brandId;
	public $channelCode;
	public $sessionId;
}

class Watershed_SoapServer_validateBroadcasterSessionResponse
{
	public $authStatus;
	public $authMessage;
}

class Watershed_SoapServer_notifySystemMessage
{
	public $brandId;
	public $message;
	public $priority;
}

class Watershed_SoapServer_AcknowledgeResponse
{
	public $acknowledged;
	function __construct( $processed = true ) {
        $this->acknowledged = array('processed' => $processed);
    }
}

class Watershed_SoapServer_loginBroadcaster
{
	public $brandId;
	public $userName;
	public $password;
}

class Watershed_SoapServer_loginBroadcasterResponse
{
	public $sessionId;
	public $authMessage;
	public $channels;
}

class Watershed_SoapServer_loginBroadcasterByChannelTokenResponse
{
	public $sessionId;
	public $authMessage;
}

class Watershed_SoapServer_loginBroadcasterByChannelToken
{
	public $brandId;
	public $channelCode;
	public $channelToken;
}

class Watershed_SoapServer_notifyChannelStatusChanged
{
	public $brandId;
	public $channelCode;
	public $status;
	public $changedAt;
}

class Watershed_SoapServer_validateViewerSession
{
	public $brandId;
	public $channelCode;
	public $sessionId;
}

class Watershed_SoapServer_validateViewerSessionResponse
{
	public $authStatus;
	public $authMessage;
}

class Watershed_SoapServer_notifyRecordingStarted
{
	public $brandId;
	public $channelCode;
	public $startedAt;
}

class Watershed_SoapServer_notifyRecordingStartedResponse
{
	public $acknowledged;
}

class Watershed_SoapServer_notifyRecordingCompleted
{
	public $brandId;
	public $channelCode;
	public $videoId;
	public $createdAt;
	public $videoAttributes;
}

class Watershed_SoapServer_videoAttributes
{
	public $duration;
	public $fileSize;
	public $rtmpUrl;
	public $flvUrl;
	public $title;
	public $description;
	public $tags;
	public $isPrivate;
}

class Watershed_SoapServer_checkStreamUserTimeLimit
{
	public $brandId;
	public $channelCode;
	public $sessionId;
}

class Watershed_SoapServer_checkStreamUserTimeLimitResponse
{
	public $timeLimit;
    function __construct($userTimeLeft = 0, $shouldCheckAgain = false) {
        $this->$timeLimit = array('userTimeLeft' => $userTimeLeft, 'shouldCheckAgain' => $shouldCheckAgain);
    }
}

class Watershed_SoapServer_registerRemainderStreamUserTime
{
	public $brandId;
	public $channelCode;
	public $sessionId;
	public $remainderUserTime;
}

class Watershed_SoapServer_registerRemainderStreamUserTime2Response
{
	public $out;	
}
// }}}

class Watershed_SoapServer // {{{
{
	
	private $user;
	
	public static function getClassMap()
	{
		return array(
			'validateBroadcasterSessionRequest' => 'Watershed_SoapServer_validateBroadcasterSession',
			'validateBroadcasterSessionResponse' => 'Watershed_SoapServer_validateBroadcasterSessionResponse',
			'notifySystemMessageRequest' => 'Watershed_SoapServer_notifySystemMessage',
			'notifySystemMessageResponse' => 'Watershed_SoapServer_AcknowledgeResponse',
			'loginBroadcasterRequest"' => 'Watershed_SoapServer_loginBroadcaster',
			'loginBroadcasterResponse' => 'Watershed_SoapServer_loginBroadcasterResponse',
			'loginBroadcasterByChannelTokenRequest' => 'Watershed_SoapServer_loginBroadcasterByChannelToken',
			'loginBroadcasterByChannelTokenResponse' => 'Watershed_SoapServer_loginBroadcasterByChannelTokenResponse',
			'notifyChannelStatusChangedRequest' => 'Watershed_SoapServer_notifyChannelStatusChanged',
			'notifyChannelStatusChangedResponse' => 'Watershed_SoapServer_AcknowledgeResponse',
			'validateViewerSessionRequest' => 'Watershed_SoapServer_validateViewerSession',
			'validateViewerSessionResponse' => 'Watershed_SoapServer_validateViewerSessionResponse',
			'notifyRecordingStartedRequest' => 'Watershed_SoapServer_notifyRecordingStarted',
			'notifyRecordingStartedResponse' => 'Watershed_SoapServer_notifyRecordingStartedResponse',
			'notifyRecordingCompletedRequest' => 'Watershed_SoapServer_notifyRecordingCompleted',
			'notifyRecordingCompletedResponse' => 'Watershed_SoapServer_AcknowledgeResponse',
			'checkStreamUserTimeLimitRequest' => 'Watershed_SoapServer_checkStreamUserTimeLimit',
			'checkStreamUserTimeLimitResponse' => 'Watershed_SoapServer_checkStreamUserTimeLimitResponse',
			'registerRemainderStreamUserTime2Request' => 'Watershed_SoapServer_registerRemainderStreamUserTime',
			'registerRemainderStreamUserTime2Response' => 'Watershed_SoapServer_registerRemainderStreamUserTime2Response',
			'registerRemainderStreamUserTimeRequest' => 'Watershed_SoapServer_registerRemainderStreamUserTime',
			'registerRemainderStreamUserTimeResponse' => 'Watershed_SoapServer_AcknowledgeResponse',
		);
	}
	
	private function loginBySession( $sessionId ) {
		require ('db/local.php');
		$watersheddb = mysql_connect($host_tiki, $user_tiki, $pass_tiki);
		mysql_select_db($dbs_tiki, $watersheddb);
		$query = "SELECT `user` FROM `tiki_user_preferences` WHERE prefName = 'watershed_sessionId' AND value = '$sessionId'";
		$result = mysql_query($query, $watersheddb);			
		if (is_resource($result)) {
			while ($res = mysql_fetch_assoc($result)) {
				$u = $res["user"];
			}
		}
		if (isset($u)) {
			$this->user = $this->loginUser($u);	
		} else {
			$this->user = '';
		}
		return $this->user;	
	}

	private function loginUser( $u ) {
		require ('db/local.php');
		$watersheddb = mysql_connect($host_tiki, $user_tiki, $pass_tiki);
		mysql_select_db($dbs_tiki, $watersheddb);
		$query = "SELECT `value` FROM `tiki_preferences` WHERE name = 'cookie_name'";
		$result = mysql_query($query, $watersheddb);			
		if (is_resource($result)) {
			while ($res = mysql_fetch_assoc($result)) {
				$cookie_name = $res["value"];
			}
		}
		if (empty($cookie_name)) {
			$cookie_name = 'tikiwiki';
		}
		$cookie_site = preg_replace("/[^a-zA-Z0-9]/", "", $cookie_name);
		$user_cookie_site = 'tiki-user-' . $cookie_site;
		session_start();
		$_SESSION[$user_cookie_site] = $u;
		global $user;
		$user = $u;
		return $u;
	}
	
	function initiateEnv() {
		global $prefs, $watershedlib, $tikilib, $smarty;
		require_once ('lib/setup/third_party.php');
		require_once ('tiki-setup_base.php');
		require_once ('lib/setup/sections.php');
		require_once ('lib/setup/user_prefs.php');
		require_once ('lib/setup/language.php');
		if ($prefs['feature_categories'] == 'y') {
			require_once ('lib/setup/categories.php');	
		}
		if ($prefs["feature_watershed"] == 'y') {
			return true;
		} else {
			return false;
		}
	}
	
	function validateBroadcasterSession( Watershed_SoapServer_validateBroadcasterSession $session ) {
		$this->loginBySession( $session->sessionId );
		if (!$this->initiateEnv()) {
			$ret->authStatus = false;
			$ret->authMessage = tra("Cannot connect with web service");
			return $ret;
		}
		
		global $prefs, $watershedlib;
		
		$ret = new Watershed_SoapServer_validateBroadcasterSessionResponse;
		
		$channels = $watershedlib->getAllViewableChannels();
		if ($channels) {
			$channels = $watershedlib->filterChannels( $channels, 'broadcaster' );
		}
		if ($channels) {
			$ret->authStatus = false;
			$ret->authMessage = tra("No permission to broadcast to channel");
			foreach ($channels as $c) {
				if ( $session->brandId == $c["brandId"] && $session->channelCode == $c["channelCode"]) {
					$ret->authStatus = true;
					$ret->authMessage = tra("Broadcaster successfully authenticated");
					break;
				}
			}				
		} else {
			$ret->authStatus = false;
			$ret->authMessage = tra("No permission to broadcast to any channel");
		}
		if ($prefs["watershed_log_errors"] == 'y') {
			global $logslib;
			$logslib->add_log('watershed', $ret->authMessage );
		}
		return $ret;
	}
	
	function notifySystemMessage( Watershed_SoapServer_notifySystemMessage $message ) {
		$this->initiateEnv();
		global $prefs;
		if ($prefs["watershed_log_errors"] == 'y') {
			global $logslib;
			$error = $message->brandId . ": " . $message->message . " (" . $message->priority . ")";
			$logslib->add_log('watershed', $error );
		}
		return new Watershed_SoapServer_AcknowledgeResponse;
	}

	function loginBroadcaster( Watershed_SoapServer_loginBroadcaster $broadcaster ) {
		// Service is supposedly used only for trying to login from mobile, so not tested
		$this->initiateEnv();
		$ret = new Watershed_SoapServer_loginBroadcasterResponse;
		global $watershedlib, $prefs, $tikilib;
		// Note that these prefs do not exist by default, but if they do should give access to all anonymous viewable channels
		if (isset($prefs["watershed_mobile_user"]) && isset($prefs["watershed_mobile_pw"]) && $broadcaster->userName == $prefs["watershed_mobile_user"] && $broadcaster->password == $prefs["watershed_mobile_pw"]) {
			$channels = $watershedlib->getAllViewableChannels( '', $broadcaster->brandId);
			if ($channels) {
				$ret->sessionId = md5('watershedmobileuser' . $tikilib->now . rand(100000,999999));
				$ret->authMessage = tra("Successfully logged in mobile broadcaster");
				$outc = array();
				foreach ($channels as $c) {
					$outc[] = array('title' => $c["channelCode"], 'channelCode' => $c["channelCode"]); 
				}
				$ret->channels = $outc;  
			} else {
				$ret->sessionId = null;
				$ret->authMessage = tra("No viewable channels available for mobile");
			}
		} else {
			$ret->sessionId = null;
			$ret->authMessage = tra("Failed to log in mobile broadcaster");
		} 
		if ($prefs["watershed_log_errors"] == 'y') {
			global $logslib;
			$logslib->add_log('watershed', $ret->authMessage );
		}
		return $ret;
	}
	
	function loginBroadcasterByChannelToken( Watershed_SoapServer_loginBroadcasterByChannelToken $token ) {
		// This is used for Flash Media Encoder shared secret authentication only
		$this->initiateEnv();
		global $prefs, $watershedlib, $tikilib;
		$ret = new Watershed_SoapServer_loginBroadcasterByChannelTokenResponse;
		if (isset($prefs["watershed_fme_key"]) && $token->channelToken == $prefs["watershed_fme_key"]) {
			$ret->sessionId = md5('watershedfmeuser' . $tikilib->now . rand(100000,999999));
			$ret->authMessage = tra("Successfully logged in FME");
		} else {
			$ret->sessionId = null;
			$ret->authMessage = tra("Failed to log in FME");
		}
		if ($prefs["watershed_log_errors"] == 'y') {
			global $logslib;
			$logslib->add_log('watershed', $ret->authMessage );
		}
		return $ret;	
	}
	
	function notifyChannelStatusChanged( Watershed_SoapServer_notifyChannelStatusChanged $status ) {
		$this->initiateEnv();
		global $prefs;
		if ($prefs["watershed_log_errors"] == 'y') {
			global $logslib;
			$error = $status->brandId . ": " . $status->channelCode . ": " . $status->status;
			$logslib->add_log('watershed', $error );
		}
		return new Watershed_SoapServer_AcknowledgeResponse;
	}
	
	function validateViewerSession( Watershed_SoapServer_validateViewerSession $session ) {
		$this->loginBySession( $session->sessionId );
		if (!$this->initiateEnv()) {
			$ret->authStatus = false;
			$ret->authMessage = tra("Cannot connect with web service");
			return $ret;
		}

		global $prefs, $watershedlib;
		
		$ret = new Watershed_SoapServer_validateViewerSessionResponse;
		
		$channels = $watershedlib->getAllViewableChannels();
		if ($channels) {
			$channels = $watershedlib->filterChannels( $channels, 'viewer' );
		}
		
		if ($channels) {
			$ret->authStatus = false;
			$ret->authMessage = tra("No permission to view channel");
			foreach ($channels as $c) {
				if ( $session->brandId == $c["brandId"] && $session->channelCode == $c["channelCode"]) {
					$ret->authStatus = true;
					$ret->authMessage = tra("Viewer successfully authenticated");
					break;
				}
			}				
		} else {
			$ret->authStatus = false;
			$ret->authMessage = tra("No permission to view any channel");
		}
		if ($prefs["watershed_log_errors"] == 'y') {
			global $logslib;
			$logslib->add_log('watershed', $ret->authMessage );
		}
		return $ret;		
	}
	
	function notifyRecordingCompleted( Watershed_SoapServer_notifyRecordingCompleted $recording) {
		$this->initiateEnv();
		global $prefs, $watershedlib;
		
		if ($watershedlib->storeArchive($recording)) {
			$error = tra('Successfully stored archive in tracker');
		} else {
			$error = tra('Failed to stored archive in tracker');
		}
		
		if ($prefs["watershed_log_errors"] == 'y') {
			global $logslib;
			$logslib->add_log('watershed', $error );
		}
		return new Watershed_SoapServer_AcknowledgeResponse;
	}
	
	function checkStreamUserTimeLimit( Watershed_SoapServer_checkStreamUserTimeLimit $session ) {
		// Not implemented because not documented but is here returning positive userTimeLeft in case called
		return new Watershed_SoapServer_checkStreamUserTimeLimitResponse(9999999);
	}
	
	function registerRemainderStreamUserTime ( Watershed_SoapServer_registerRemainderStreamUserTime $session ) {
		// Not implemented because not documented but is here in case called
		return new Watershed_SoapServer_AcknowledgeResponse;
	}
	
} // }}}

global $watershedlib;
$watershedlib = new watershedLib;
