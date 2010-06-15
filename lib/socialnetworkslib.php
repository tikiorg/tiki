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

// bundling social networks functionality
class SocialNetworksLib extends TikiLib
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
			if ($_SERVER['SERVER_PORT']!=443) $port=":".$_SERVER['SERVER_PORT'];
		} else {
			if ($_SERVER['SERVER_PORT']!=80) $port=":".$_SERVER['SERVER_PORT'];
		}
		$url.="://".$_SERVER['HTTP_HOST'].$port.$_SERVER['REQUEST_URI'];
		return $url;
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
	
	function tweet($message, $user, $cutMessage=false) {
		global $prefs;
		$token=$this->get_user_preference($user, 'twitter_token', '');
		if ($token=='') {
			return -1;        
		}
		if($cutMessage) {
			$message=substr($message,0,140);
		} else {
			if (strlen($message)>140) {
				return -2;
			}
		}
		$token = unserialize($token);
		$token= (object)$token;

		$this->options['callbackUrl']=$this->getURL();
		$this->options['consumerKey']=$prefs['socialnetworks_twitter_consumer_key'];
		$this->options['consumerSecret']=$prefs['socialnetworks_twitter_consumer_secret'];
		$client = $token->getHttpClient($this->options);
		$clientconfig['timeout']=30; // allow a longer timeout for twitter makes sense
		$client->setConfig($clientconfig);		
		$twitter = new Zend_Service_Twitter();
		$twitter->setLocalHttpClient($client);
		try {
			$response = $twitter->status->update($message);
		} catch (Zend_Http_Client_Exception $e) {
			return -($e->getCode());
		}                                        
		$status=$response->getStatus();
		if ($status!=200) {
			return -$status;
		} else {
			$id=(string)$response->id;
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
}

global $socialnetworkslib;

$socialnetworkslib = new socialNetworksLib;