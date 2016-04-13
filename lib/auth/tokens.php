<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class AuthTokens
{
	const SCHEME = 'MD5( CONCAT(tokenId, creation, timeout, entry, parameters, groups) )';
	private $db;
	private $table;
	private $dt;
	private $maxTimeout = 3600;
	private $maxHits = 1;
	public $ok = false;

	public static function build( $prefs )
	{
		return new AuthTokens(
			TikiDb::get(),
			array(
				'maxTimeout' => $prefs['auth_token_access_maxtimeout'],
				'maxHits' => $prefs['auth_token_access_maxhits'],
			)
		);
	}

	function __construct( $db, $options = array(), DateTime $dt = null )
	{
		$this->db = $db;
		$this->table = $this->db->table('tiki_auth_tokens');

		if (is_null($dt)) {
			$this->dt = new DateTime;
		} else {
			$this->dt = $dt;
		}

		if ( isset($options['maxTimeout']) ) {
			$this->maxTimeout = (int) $options['maxTimeout'];
		}

		if ( isset($options['maxHits']) ) {
			$this->maxHits = (int) $options['maxHits'];
		}
	}

	function getToken( $token )
	{
		$data = $this->db->query(
			'SELECT * FROM tiki_auth_tokens WHERE token = ? AND token = ' . self::SCHEME,
			array( $token )
		)->fetchRow();

		return $data;
	}

	function getTokens()
	{
		return $this->table->fetchAll(array(), array(), -1, -1, array('creation' => 'asc'));
	}

	function getGroups( $token, $entry, $parameters )
	{
		// Process deletion of temporary users that are created via tokens
		$usersToDelete = $this->db->fetchAll(
			'SELECT tokenId, userPrefix FROM tiki_auth_tokens
			WHERE (timeout != -1 AND UNIX_TIMESTAMP(creation) + timeout < UNIX_TIMESTAMP()) OR `hits` = 0'
		);

		foreach ($usersToDelete as $del) {
			TikiLib::lib('user')->remove_temporary_user($del['userPrefix'] . $del['tokenId']);
		}

		$this->db->query(
			'DELETE FROM tiki_auth_tokens
			 WHERE (timeout != -1 AND UNIX_TIMESTAMP(creation) + timeout < UNIX_TIMESTAMP()) OR `hits` = 0'
		);

		$data = $this->db->query(
			'SELECT tokenId, entry, parameters, groups, email, createUser, userPrefix FROM tiki_auth_tokens WHERE token = ? AND token = ' . self::SCHEME,
			array( $token )
		)->fetchRow();

		global $prefs, $full, $smarty, $tikiroot;		// $full defined in route.php
		$sefurl = '';

		if ($prefs['feature_sefurl'] === 'y') {
			$sefurl = substr($full, strlen($tikiroot)) . '?' . http_build_query($_GET);
			$sefurlTypeMap = $this->getSefurlTypeMap();

			$smarty->loadPlugin('smarty_modifier_sefurl');
			$sefurl = $tikiroot . smarty_modifier_sefurl($sefurl, $sefurlTypeMap[$_GET[0]]);
		}

		// entry doesn't match "or" sefurl feature is in use but that also doesn't match
		if ( $data['entry'] != $entry && $sefurl && $data['entry']  !== $sefurl ) {
			return null;
		}

		$registered = (array) json_decode($data['parameters'], true);

		if ( ! $this->allPresent($registered, $parameters) || ! $this->allPresent($parameters, $registered) ) {
			return null;
		}

		$this->db->query(
			'UPDATE `tiki_auth_tokens` SET `hits` = `hits` - 1 WHERE `tokenId` = ? AND hits != -1',
			array( $data['tokenId'] )
		);

		// Process autologin of temporary users
		if ($data['createUser'] == 'y') {
			$userlib = TikiLib::lib('user');
			$tempuser = $data['userPrefix'] . $userlib->autogenerate_login($data['tokenId'], 6);
			$groups = json_decode($data['groups'], true);
			$parameters = json_decode($data['parameters'], true);
			if (!$userlib->user_exists($tempuser)) {
				$randompass = $userlib->genPass();
				$userlib->add_user($tempuser, $randompass, $data['email'], '', false, NULL, NULL, NULL, $groups);
			}
			$userlib->autologin_user($tempuser);
			$url = basename($data['entry']);
			if ($parameters) {
				$query = '?' . http_build_query($parameters, '', '&');
				$url .= $query;
			}
			include_once('tiki-sefurl.php');
			$url = filter_out_sefurl($url);
			TikiLib::lib('access')->redirect($url);
			die;
		}

		$this->ok = true;
		return (array) json_decode($data['groups'], true);
	}

	private function allPresent( $a, $b )
	{
		foreach ( $a as $key => $value ) {
			if ( ! isset($b[$key]) || $value != $b[$key] ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Provide mapping between item key and object type
	 * TODO centralise this info in objectlib.php (?) and decide on one word or two for each
	 *
	 * @return array
	 */
	private function getSefurlTypeMap()
	{
		return [
			'page'      => 'wiki page',
			'articleId' => 'article',
			'blogId'    => 'blog',
			'postId'    => 'blog post',
			'parentId'  => 'category',
			'fileId'    => 'file',
			'galleryId' => 'file gallery',
			'forumId'   => 'forum',
			'nlId'      => 'newsletter',
			'trackerId' => 'tracker',
			'itemId'    => 'trackeritem',
			'sheetId'   => 'sheet',
			'userId'    => 'user',
			'calIds'    => 'calendar',
		];
	}

	function createToken( $entry, array $parameters, array $groups, array $arguments = array() )
	{
		if ( !empty($arguments['timeout']) ) {
			$timeout = $arguments['timeout'];
		} else {
			$timeout = $this->maxTimeout;
		}

		if ( !empty($arguments['hits']) ) {
			$hits = $arguments['hits'];
		} else {
			$hits = $this->maxHits;
		}

		if (isset($arguments['email'])) {
			$email = $arguments['email'];
		} else {
			$email = '';
		}

		if (!empty($arguments['createUser']) && $arguments['createUser'] !== 'n') {
			$createUser = 'y';
		} else {
			$createUser = 'n';
		}

		if (isset($arguments['userPrefix'])) {
			$userPrefix = $arguments['userPrefix'];
		} else {
			$userPrefix = '';
		}

		$this->db->query(
			'INSERT INTO tiki_auth_tokens ( timeout, maxhits, hits, entry, parameters, groups, email, createUser, userPrefix ) VALUES( ?, ?, ?, ?, ?, ?, ?, ?, ? )',
			array(
				(int) $timeout,
				(int) $hits,
				(int) $hits,
				$entry,
				json_encode($parameters),
				json_encode($groups),
				$email,
				$createUser,
				$userPrefix,

			)
		);

		$max = $this->db->getOne('SELECT MAX(tokenId) FROM tiki_auth_tokens');

		$this->db->query('UPDATE tiki_auth_tokens SET token = ' . self::SCHEME . ' WHERE tokenId = ?', array( $max ));

		return $this->db->getOne('SELECT token FROM tiki_auth_tokens WHERE tokenId = ?', array( $max ));
	}

	/**
	 * This is a function that includes a security token into a provided URL
	 * @param string $url The URL where the token is valid.
	 * @param array $groups The groups from which the person using the token will have permissions for.
	 * @param string $email The email that the token was sent to, for recording purpose. If there are multiple
	 * emails, it currently saves as comma separated list but exploding will need to trim spaces.
	 * @param int $timeout Timeout to set in seconds. If not included, will use default as set in prefs.
	 * @param int $hits Number of hits allowed before token expires. If not included, will use default as set in prefs.
	 * @param boolean $createUser Login token user as temporary user if set to true
	 * @param string $userPrefix Username of the created users will be a 6 digit number based on the token ID prefixed with this (default is 'guest')
	 * @return string A URL that has the security token included.
	 */
	function includeToken( $url, array $groups = array(), $email = '', $timeout = 0, $hits = 0, $createUser = false, $userPrefix = 'guest')
	{
		$data = parse_url($url);
		$longurl = '';

		if ( isset($data['query']) ) {
			parse_str($data['query'], $args);
			unset($args['TOKEN']);
		} else {
			global $prefs, $sefurl_regex_out;
			include_once 'tiki-sefurl.php';
			if ($prefs['feature_sefurl'] === 'y' && !empty($sefurl_regex_out)) {
				global $base_url;

				$short = substr($url, strlen($base_url));
				$is_numeric = preg_match('/\d+/', $short);

				foreach (array_reverse($sefurl_regex_out) as $regex) {	// wiki is the first one and will match anything
					if ($is_numeric) {
						$replace = '(\d+)';	// match digits
					} else {
						$replace ='(.+)';	// or anything (for wiki pages)
					}
					$pattern = str_replace('$1', $replace, $regex['right']);

					if (preg_match('/' . $pattern . '/', $short, $matches)) {
						$longurl = preg_replace('/' . preg_quote($replace) . '/', $matches[1], $regex['left']);
						$longurl = $base_url . stripcslashes($longurl);	// add back the beginning and get rid of the \ infront of the ?
						break;
					}
				}

				if ($longurl) {
					$longdata = parse_url($longurl);
					parse_str($longdata['query'], $args);
				} else {
					$args = array();
				}

			} else {
				$args = array();
			}
		}

		$settings = array('email'=>$email);
		if (!empty($timeout)) {
			$settings['timeout'] = $timeout;
		}
		if (!empty($hits)) {
			$settings['hits'] = $hits;
		}
		$settings['createUser'] = $createUser;
		$settings['userPrefix'] = $userPrefix;

		$token = $this->createToken($data['path'], $args, $groups, $settings);
		if ($longurl) {	// sefurl was used so the args should be reset now the token has been created
			$args = array();
		}
		$args['TOKEN'] = $token;

		$query = '?' . http_build_query($args, '', '&');

		if ( ! isset($data['fragment']) ) {
			$anchor = '';
		} else {
			$anchor = "#{$data['fragment']}";
		}

		return "{$data['scheme']}://{$data['host']}{$data['path']}$query$anchor";
	}

	function deleteToken($tokenId)
	{
		$userPrefix = $this->table->fetchOne(
			'userPrefix',
			array('tokenId' => $tokenId, 'createUser' => 'y')
		);
		if ($userPrefix) {
			TikiLib::lib('user')->remove_temporary_user($userPrefix . $tokenId);
		}
		$this->table->delete(array('tokenId' => $tokenId));
	}
}
