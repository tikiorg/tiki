<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id $

// this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
require_once ('lib/core/lib/Zend/Oauth/Consumer.php');
require_once ('lib/core/lib/Zend/Service/Twitter.php');
require_once ('lib/logs/logslib.php');


// bundling social networks functionality
class SocialNetworksLib extends LogsLib
{

	public $options = array(
		'callbackUrl'    => '',
		'siteUrl'        => 'http://twitter.com/oauth',
		'consumerKey'    => '',
		'consumerSecret' => '',
	);

	function getURL() {
		$url='http';
		$port='';
		if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on') {
			$url.="s";
			if ($_SERVER['SERVER_PORT']!=443 and strpos($_SERVER['HTTP_HOST'],':')==0) {
				$port=":".$_SERVER['SERVER_PORT'];
			}
		} else {
			if ($_SERVER['SERVER_PORT']!=80 and strpos($_SERVER['HTTP_HOST'],':')==0) {
				$port=":".$_SERVER['SERVER_PORT'];
			}
		}
		$url.="://".$_SERVER['HTTP_HOST'].$port.$_SERVER['REQUEST_URI'];
		return $url;
	}
	
	function twitterRegistered() {
		global $prefs;
		return ($prefs['socialnetworks_twitter_consumer_key']!='' and $prefs['socialnetworks_twitter_consumer_secret']!='');
	}

	function getTwitterRequestToken() {
		global $prefs;

		if($prefs['socialnetworks_twitter_consumer_key']=='' or $prefs['socialnetworks_twitter_consumer_secret']=='') {
			return false;
		}


		$this->options['callbackUrl']=$this->getURL();
		$this->options['consumerKey']=$prefs['socialnetworks_twitter_consumer_key'];
		$this->options['consumerSecret']=$prefs['socialnetworks_twitter_consumer_secret'];
		
		$consumer = new Zend_Oauth_Consumer($this->options);
		$token = $consumer->getRequestToken();
		$_SESSION['TWITTER_REQUEST_TOKEN'] = serialize($token);
		$consumer->redirect();
	}

	function getTwitterAccessToken($user) {
		global $prefs;

		if($prefs['socialnetworks_twitter_consumer_key']=='' or $prefs['socialnetworks_twitter_consumer_secret']=='' or !isset($_SESSION['TWITTER_REQUEST_TOKEN'])) {
			return false;
		}

		$this->options['callbackUrl']=$this->getURL();
		$this->options['consumerKey']=$prefs['socialnetworks_twitter_consumer_key'];
		$this->options['consumerSecret']=$prefs['socialnetworks_twitter_consumer_secret'];

		$consumer = new Zend_Oauth_Consumer($this->options);
		$token = $consumer->getAccessToken($_GET, unserialize($_SESSION['TWITTER_REQUEST_TOKEN']));
		unset($_SESSION['TWITTER_REQUEST_TOKEN']);
		$this->set_user_preference($user, 'twitter_token', serialize($token));
		return true;
	}

	function facebookRegistered() {
		global $prefs;
		return ($prefs['socialnetworks_facebook_application_id']!='' and $prefs['socialnetworks_facebook_api_key']!='' and $prefs['socialnetworks_facebook_application_secr']!='');
	}
	
	function getFacebookRequestToken() {
		global $prefs;
		if($prefs['socialnetworks_facebook_application_id']=='' or $prefs['socialnetworks_facebook_api_key']=='' or $prefs['socialnetworks_facebook_application_secr']=='') {
			return false;
		}
		$url=$this->getURL();
		if (strpos($url,'?')!=0) {
			$url=preg_replace('/\?.*/','',$url);
		}
		$url=urlencode($url.'?request_facebook');
		$url='https://graph.facebook.com/oauth/authorize?client_id=' . $prefs['socialnetworks_facebook_application_id'] . 
			 '&scope=offline_access,publish_stream,create_event,rsvp_event,sms,manage_pages&redirect_uri='.$url;
		header("Location: $url");
		die();
	}

	function getFacebookAccessToken($user) {
		global $prefs;
		if($prefs['socialnetworks_facebook_application_id']=='' or $prefs['socialnetworks_facebook_api_key']=='' or $prefs['socialnetworks_facebook_application_secr']=='') {
			return false;
		}

		$url='/oauth/access_token?client_id=' . $prefs['socialnetworks_facebook_application_id'] .
                        '&redirect_uri=' . $this->getURL() .'&client_secret=' . $prefs['socialnetworks_facebook_application_secr']; // code is already in the url


        $request="GET $url HTTP/1.1\r\n".
                 "Host: graph.facebook.com\r\n".
                 "Accept: */*\r\n".
                 "Expect: 100-continue\r\n".
                 "Connection: close\r\n\r\n";

        $fp = fsockopen("ssl://graph.facebook.com", 443);
        if ($fp===false) {
            $this->add_log('getFacebookAccessToken',"can't connect");
            return false;
        } else {
            fputs($fp, $request);
            $ret='';
            while(!feof($fp)) {
                $ret .= fgets($fp, 128);
            }
            fclose($fp);
        }
        $ret=preg_split('/(\r\n\r\n|\r\r|\n\n)/',$ret,2);
		$ret=$ret[1];

		if(substr($ret,0,13)=='access_token=') {
			$this->set_user_preference($user, 'facebook_token', substr($ret,13));
			return true;
		} else {
			return false;
		}
	}
	
	function tweet($message, $user, $cutMessage=false) {
		global $prefs;
		$token=$this->get_user_preference($user, 'twitter_token', '');
		if ($token=='') {
			$this->add_log('tweet','user not registered with twitter');
			return -1; 
		}
		if($cutMessage) {
			$message=substr($message,0,140);
		} else {
			if (strlen($message)>140) {
				$this->add_log('tweet','message too long');
				return -2;
			}
		}
		$token = unserialize($token);
		$token= (object)$token;

		$this->options['callbackUrl']=$this->getURL();
		$this->options['consumerKey']=$prefs['socialnetworks_twitter_consumer_key'];
		$this->options['consumerSecret']=$prefs['socialnetworks_twitter_consumer_secret'];
		$client = $token->getHttpClient($this->options);
		$clientconfig['timeout']=60; // allow a longer timeout for twitter makes sense
		$client->setConfig($clientconfig);		
		$twitter = new Zend_Service_Twitter();
		$twitter->setLocalHttpClient($client);
		try {
			$response = $twitter->status->update($message);
		} catch (Zend_Http_Client_Exception $e) {
			$this->add_log('tweet','twitter error '.$e->getMessage());
			return -($e->getCode());
		}                                        
		$status=$response->getStatus();
		if ($status!=200) {
			$this->add_log('tweet','twitter response ' & $status);
			return -$status;
		} else {
			$id=(string)$response->id;
			$this->add_log('tweet','id: ' . $id);
			return $id;
		}
	}

	function destroyTweet($id, $user) {
		global $prefs;
		$token=$this->get_user_preference($user, 'twitter_token', '');
		if ($token=='') {
			return false;        
		}
		$token = unserialize($token);
		$token= (object)$token;
		$this->options['callbackUrl']=$this->getURL();
		$this->options['consumerKey']=$prefs['socialnetworks_twitter_consumer_key'];
		$this->options['consumerSecret']=$prefs['socialnetworks_twitter_consumer_secret'];
		$client = $token->getHttpClient($this->options);
		$twitter = new Zend_Service_Twitter();
		$twitter->setLocalHttpClient($client);
		try {
			$response = $twitter->status->destroy($id);
		} catch(Zend_Http_Client_Adapter_Exception $e)	{
			return false;
		}
		return true;
	}
	
	function facebookGraph($user, $action, $params, $addtoken=true) {
		global $prefs;
		if($prefs['socialnetworks_facebook_application_id']=='' or $prefs['socialnetworks_facebook_api_key']=='' or $prefs['socialnetworks_facebook_application_secr']=='') {
			$this->add_log('facebookGraph','application not set up');
			return false;
		}
		if ($addtoken) {
			$token=$this->get_user_preference($user, 'facebook_token', '');
			if ($token=='') {
				$this->add_log('facebookGraph','user not registered with facebook');
				return false; 
			}
		
			if (!isset($params['access_token'])) {
				$params['access_token'] =$token;
			}
		}
		
		$data=http_build_query($params,'','&');
		$request="POST $action HTTP/1.1\r\n".
				 "Host: graph.facebook.com\r\n".
				 "Accept: */*\r\n".
				 "Content-type: application/x-www-form-urlencoded\r\n".
				 "Content-length: ". strlen($data) ."\r\n".
				 "Expect: 100-continue\r\n".
				 "Connection: close\r\n\r\n".
				 $data;

  		$fp = fsockopen("ssl://graph.facebook.com", 443);
  		if ($fp===false) {
			$this->add_log('facebookGraph',"can't connect");
			return false; 
  		} else {
	  		fputs($fp, $request);
	  		$ret='';  		
			while(!feof($fp)) {
				$ret .= fgets($fp, 128);
	  		}
			fclose($fp);
  		}
		$ret=preg_split('/(\r\n\r\n|\r\r|\n\n)/',$ret,2);
		return $ret[1];
	}
	
	function facebookWallPublish($user, $message, $url='', $text='', $privacy='') {
		$params=array(
			'message' => $message,
		);
		$ret=$this->facebookGraph($user, 'me/feed/', $params);
		return $ret;
	}
}

global $socialnetworkslib;

$socialnetworkslib = new socialNetworksLib;