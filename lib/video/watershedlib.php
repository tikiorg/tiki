<?php 

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

}
	
	
// SOAP Types {{{

class Watershed_SoapServer_Acknowledge
{
	public $processed;
	function __construct() {
        $this->processed = true;
    }
}

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
	function __construct() {
        $this->acknowledged = new Watershed_SoapServer_Acknowledge;
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
	function __construct() {
        $videoAttributes = new Watershed_SoapServer_videoAttributes;
    }
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
    function __construct() {
        $timeLimit = new Watershed_SoapServer_TimeControlModule;
    }
}

class Watershed_SoapServer_TimeControlModule
{
	public $userTimeLeft;
	public $shouldCheckAgain;
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
			'loginBroadcasterByChannelTokenResponse' => 'Watershed_SoapServer_loginBroadcasterResponse',
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
		// Password broadcaster auth not implemented
		$this->initiateEnv();
		$ret = new Watershed_SoapServer_loginBroadcasterResponse;
		$ret->sessionId = 'tikisession';
		$ret->authMessage = "Sorry, failed Tiki broadcaster auth";
		return $ret;
	}
	
	function loginBroadcasterByChannelToken( Watershed_SoapServer_loginBroadcasterByChannelToken $token ) {
		// Flash Media Encoder shared secret auth not implemented
		$ret = new Watershed_SoapServer_loginBroadcasterResponse;
		$ret->sessionId = 'tikisession';
		$ret->authMessage = "Sorry, failed Tiki broadcaster auth";
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
		// TODO
		return new Watershed_SoapServer_AcknowledgeResponse;
	}
	
	function checkStreamUserTimeLimit( Watershed_SoapServer_checkStreamUserTimeLimit $session ) {
		// Not implemented because not documented 
		$ret = new Watershed_SoapServer_checkStreamUserTimeLimitResponse;
		$ret->timeLimit->userTimeLeft = 9000000;
		$ret->timeLimit->shouldCheckAgain = false;
		return $ret;
	}
	
	function registerRemainderStreamUserTime ( Watershed_SoapServer_registerRemainderStreamUserTime $session ) {
		// Not implemented because not documented
		return new Watershed_SoapServer_AcknowledgeResponse;
	}
	
} // }}}

global $watershedlib;
$watershedlib = new watershedLib;
