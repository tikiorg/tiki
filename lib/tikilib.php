<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/debug/Tracer.php');

// this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

// This class is included by all the Tiki php scripts, so it's important
// to keep the class as small as possible to improve performance.
// What goes in this class:
// * generic functions that MANY scripts must use
// * shared functions (marked as /*shared*/) are functions that are
//   called from Tiki modules.

/**
 *
 */
class TikiLib extends TikiDb_Bridge
{
	public $buffer;
	public $flag;
	public $usergroups_cache = array();

	public $num_queries = 0;
	public $now;

	public $cache_page_info = array();
	public $sessionId = null;

	/**
	 * Collection of Tiki libraries.
	 * Populated by TikiLib::lib()
	 * @var array
	 */
	protected static $libraries = array();

	protected static $isExternalContext = false;

	/** Gets a library reference
	 *
	 * @param $name
	 * @return \ActivityLib|\AdminLib|\AreasLib|\ArtLib|\AttributeLib|\AutoSaveLib|\BannerLib|\BigBlueButtonLib|\BlogLib|\CacheLib|\CalendarLib|\Captcha|\CartLib|\CategLib|\Comments|\ContactLib|\ContributionLib|\CreditsLib|\CryptLib|\cssLib|\DCSLib|\EditLib|\ErrorReportLib|\FaqLib|\FederatedSearchLib|\FileGalBatchLib|\FileGalLib|\FlaggedRevisionLib|\FreetagLib|\GeoLib|\GoalEventLib|\GoalLib|\GoalRewardLib|\GroupAlertLib|\HeaderLib|\HistLib|\IconsetLib|\ImageGalsLib|\KalturaLib|\KalturaLib|\Language|\LanguageTranslations|\LdapLib|\LoginLib|\LogsLib|\LogsQueryLib|\MailinLib|\Memcachelib|\MenuLib|\Messu|\MimeLib|\ModLib|\MonitorLib|\MonitorMailLib|\MultilingualLib|\NotificationLib|\OAuthLib|\ObjectLib|\PageContentLib|\ParserLib|\PaymentLib|\PerspectiveLib|\PollLib|\PreferencesLib|\QuantifyLib|\QueueLib|\QuizLib|\RatingConfigLib|\RatingLib|\ReferencesLib|\RegistrationLib|\RelationLib|\RSSLib|\ScoreLib|\ScormLib|\SearchStatsLib|\SemanticLib|\ServiceLib|\SheetLib|\Smarty_Tiki|\SocialLib|\StatsLib|\StoredSearchLib|\StructLib|\TemplatesLib|\ThemeControlLib|\ThemeLib|\Tiki_Connect_Client|\Tiki_Connect_Server|\Tiki_Event_Manager|\Tiki_Profile_SymbolLoader|\Tiki\Object\Selector|\Tiki\Recommendation\BatchProcessor|\Tiki\Wiki\SlugManager|\TikiAccessLib|\TikiCalendarLib|\TikiDate|\TodoLib|\Tracker\Tabular\Manager|\TrackerLib|\UnifiedSearchLib|\UserMailinLib|\UserModulesLib|\UserPrefsLib|\UsersLib|\Validators|\VimeoLib|\WikiLib|\WikiLingoTikiEvents|\WizardLib|\WYSIWYGLib|\ZoteroLib
	 * @throws Exception
	 */
	public static function lib($name)
	{
		if (isset(self::$libraries[$name])) {
			return self::$libraries[$name];
		}

		$container = TikiInit::getContainer();

		//if no period in the lib name, default to tiki.lib prefix.
		if (strpos($name, ".") !== false) {
			$service = $name;
		} else {
			$service = "tiki.lib.$name";
		}

		if ($lib = $container->get($service, \Symfony\Component\DependencyInjection\ContainerInterface::NULL_ON_INVALID_REFERENCE)) {
			return $lib;
		}

		// One-time inits of the libraries provided
		switch ($name) {
			case 'tiki':
				global $tikilib;
				return self::$libraries[$name] = $tikilib;
		}

		unlink('temp/cache/container.php'); // Remove the container cache to help transition
		throw new Exception(tr("%0 library not found. This may be due to a typo or caused by a recent update.", $name));
	}

	/**
	 * @return Tiki_Event_Manager
	 */
	public static function events()
	{
		return self::lib('events');
	}

	/**
	 * @return Tiki_Profile_SymbolLoader
	 */
	public static function symbols()
	{
		return self::lib('symbols');
	}

	/**
	 * @return mixed
	 */
	public function get_site_hash()
	{
		global $prefs;

		if (! isset($prefs['internal_site_hash'])) {
			$hash = $this->generate_unique_sequence();

			$this->set_preference('internal_site_hash', $hash);
		}

		return $prefs['internal_site_hash'];
	}

	public function generate_unique_sequence($entropy = 100)
	{
		require_once 'lib/phpsec/phpsec/phpsec.rand.php';
		return base64_encode(phpsecRand::bytes($entropy));
	}

	// DB param left for interface compatibility, although not considered
	/**
	 * @param null $db
	 */
	function __construct( $db = null )
	{
		$this->now = time();
	}

	/**
	 * @param $data
	 * @param array $options
	 * @return mixed|string
	 */
	function parse_data($data, $options = array())
	{
		//to make migration easier
		$parserlib = TikiLib::lib('parser');
		return $parserlib->parse_data($data, $options);
	}

	/**
	 * @param $data
	 * @param $withReltype
	 * @return array
	 */
	function get_pages($data, $withReltype = false)
	{
		//to make migration easier
		$parserlib = TikiLib::lib('parser');
		return $parserlib->get_pages($data, $withReltype);
	}

	function allocate_extra($type, $callback)
	{
		global $prefs;

		$memory_name = 'allocate_memory_' . $type;
		$time_name = 'allocate_time_' . $type;

		if (! empty($prefs[$memory_name])) {
			$memory_limit = new Tiki_MemoryLimit($prefs[$memory_name]);
		}

		if (! empty($prefs[$time_name])) {
			$time_limit = new Tiki_TimeLimit($prefs[$time_name]);
		}

		return call_user_func($callback);
	}

	/**
	 * @param bool $url
	 * @param array $options
	 * @return mixed|Zend\Http\Client
	 */
	function get_http_client($url = false, $options = null)
	{
		global $prefs;

		$config = array(
			'timeout' => 10,
			'keepalive' => true,
		);

		if ($prefs['use_proxy'] == 'y') {
			$config['adapter'] = 'Zend\Http\Client\Adapter\Proxy';
			$config["proxy_host"] = $prefs['proxy_host'];
			$config["proxy_port"] = $prefs['proxy_port'];

			if ($prefs['proxy_user'] || $prefs['proxy_pass']) {
				$config["proxy_user"] = $prefs['proxy_user'];
				$config["proxy_pass"] = $prefs['proxy_pass'];
			}
		}

		if ($prefs['zend_http_sslverifypeer'] == 'y') {
			$config['sslverifypeer'] = true;
		} else {
			$config['sslverifypeer'] = false;
		}


		if(is_array($options)){
			foreach($options as $key => $value) {
				$config[$key] = $value;
			}
		}

		$client = new Zend\Http\Client(null, $config);
		$client->setArgSeparator('&');

		if ($url) {
			$client = $this->prepare_http_client($client, $url);

			$client->setUri($this->urlencode_accent($url));	// Zend\Http\Client seems to fail with accents in urls (jb june 2011)
		}

		return $client;
	}

	/**
	 * @param $client
	 * @param $url
	 * @return mixed
	 */
	private function prepare_http_client($client, $url)
	{
		$info = parse_url($url);

		// Obtain all methods matching the scheme and domain
		$table = $this->table('tiki_source_auth');
		$authentications = $table->fetchAll(
			array('path', 'method', 'arguments'),
			array('scheme' => $info['scheme'],'domain' => $info['host'])
		);

		// Obtain the method with the longest path matching
		$max = -1;
		$method = false;
		$arguments = false;
	 	foreach ($authentications as $auth) {
			if (0 === strpos($info['path'], $auth['path'])) {
				$len = strlen($auth['path']);
				if ($len > $max) {
					$max = $len;
					$method = $auth['method'];
					$arguments = $auth['arguments'];
				}
			}
		}

		if ($method) {
			$functionName = 'prepare_http_auth_' . $method;
			if (method_exists($this, $functionName)) {
				$arguments = json_decode($arguments, true);
				return $this->$functionName($client, $arguments);
			}
		} else {
			// Nothing special to do
			return $client;
		}
	}

	/**
	 * @param $client
	 * @param $arguments
	 * @return mixed
	 */
	private function prepare_http_auth_basic($client, $arguments)
	{
		$client->setAuth($arguments['username'], $arguments['password'], Zend\Http\Client::AUTH_BASIC);

		return $client;
	}

	/**
	 * @param $client
	 * @param $arguments
	 * @return mixed
	 */
	private function prepare_http_auth_get($client, $arguments)
	{
		$url = $arguments['url'];

		$client->setCookieJar();
		$client->setUri($this->urlencode_accent($url)); // Zend\Http\Client seems to fail with accents in urls
		$client->setMethod(Zend\Http\Request::METHOD_GET);
		$response = $client->send();
		$client->resetParameters();

		return $client;
	}

	/**
	 * @param $client
	 * @param $arguments
	 * @return mixed
	 */
	private function prepare_http_auth_post($client, $arguments)
	{
		$url = $arguments['post_url'];
		unset($arguments['post_url']);

		$client->setCookieJar();
		$client->setUri($this->urlencode_accent($url)); // Zend\Http\Client seems to fail with accents in urls
		$client->setMethod(Zend\Http\Request::METHOD_GET);
		$response = $client->send();
		$client->resetParameters();

		$client->setUri($this->urlencode_accent($url)); // Zend\Http\Client seems to fail with accents in urls
		$client->setParameterPost($arguments);
		$client->setMethod(Zend\Http\Request::METHOD_POST);
		$response = $client->send();
		$client->resetParameters();

		return $client;
	}

	/**
	 * @param $client
	 * @return mixed
	 */
	function http_perform_request($client)
	{
		global $prefs;
		$response = $client->send();

		$attempts = 0;
		while ($response->isRedirect() && $attempts < 10) { // prevent redirect loop
			$client->setUri($client->getUri());
			$response = $client->send();
			$attempts++;
		}

		if ($prefs['http_skip_frameset'] == 'y') {
			if ($outcome = $this->http_perform_request_skip_frameset($client, $response)) {
				return $outcome;
			}
		}

		return $response;
	}

	/**
	 * @param $client
	 * @param $response
	 * @return mixed
	 */
	private function http_perform_request_skip_frameset($client, $response)
	{
		// Only attempt if document is declared as HTML
		if (0 === strpos($response->getHeaders()->get('Content-Type'), 'text/html')) {
			$use_int_errors = libxml_use_internal_errors(true); // suppress errors and warnings due to bad HTML
			$dom = new DOMDocument;
			if ($response->getBody() && $dom->loadHTML($response->getBody())) {
				$frames = $dom->getElementsByTagName('frame');

				if (count($frames)) {
					// Frames were found
					foreach ($frames as $f) {
						// Request with the first frame where scrolling is not disabled (likely to be a menu or some other web 2.0 helper)
						if ($f->getAttribute('scrolling') != 'no') {
							$client->setUri($this->http_get_uri($client->getUri(), $this->urlencode_accent($f->getAttribute('src'))));
							libxml_clear_errors();
							libxml_use_internal_errors($use_int_errors);
							return $client->send();
						}
					}
				}
			}
			libxml_clear_errors();
			libxml_use_internal_errors($use_int_errors);
		}
	}

	/**
	 * @param Zend\Uri\Http $uri
	 * @param $relative
	 * @return Zend\Uri\Http
	 */
	function http_get_uri(Zend\Uri\Http $uri, $relative)
	{
		if (strpos($relative, 'http://') === 0 || strpos($relative, 'https://') === 0) {
			$uri = new Zend\Uri\Http($relative);

		} else {
			$uri = clone $uri;
			$uri->setQuery(array());
			$parts = explode('?', $relative, 2);
			$relative = $parts[0];

			if ($relative{0} === '/') {
				$uri->setPath($relative);
			} else {
				$path = dirname($uri->getPath());
				if ($path === '/') {
					$path = '';
				}

				$uri->setPath("$path/$relative");
			}

			if (isset($parts[1])) {
				$uri->setQuery($parts[1]);
			}
		}

		return $uri;
	}

	/**
	 * @param $url
	 * @param string $reqmethod
	 * @return bool
	 */
	function httprequest($url, $reqmethod = "GET")
	{
		// test url :
		// rewrite url if sloppy # added a case for https urls
		if ( (substr($url, 0, 7) <> "http://") and
				(substr($url, 0, 8) <> "https://")
			 ) {
			$url = "http://" . $url;
		}

		try {
			$client = $this->get_http_client($url);
			/* @var $response Zend\Http\Response */
			$response = $this->http_perform_request($client);

			if (!$response->isSuccess()) {
				return false;
			}

			return $response->getBody();
		} catch (Zend\Http\Exception\ExceptionInterface $e) {
			return false;
		}
	}

	/*shared*/
	/**
	 * @param $name
	 * @return bool
	 */
	function get_dsn_by_name($name)
	{
		if ($name == 'local') {
			return true;
		}
		return $this->table('tiki_dsn')->fetchOne('dsn', array('name' => $name));
	}

	/**
	 * @param $name
	 * @return array
	 */
	function get_dsn_info($name)
	{
		$info = array();

		$dsnsqlplugin = $this->get_dsn_by_name($name);

		$parsedsn = $dsnsqlplugin;
		$info['driver'] = strtok($parsedsn, ":");
		$parsedsn = substr($parsedsn, strlen($info['driver']) + 3);
		$info['user'] = strtok($parsedsn, ":");
		$parsedsn = substr($parsedsn, strlen($info['user']) + 1);
		$info['password'] = strtok($parsedsn, "@");
		$parsedsn = substr($parsedsn, strlen($info['password']) + 1);
		$info['host'] = strtok($parsedsn, "/");
		$parsedsn = substr($parsedsn, strlen($info['host']) + 1);
		$info['database'] = $parsedsn;

		return $info;
	}

	/**
	 * @param $name
	 * @return mixed
	 */
	function get_db_by_name( $name )
	{
		include_once ('tiki-setup.php');
		if ( $name == 'local' || empty($name) ) {
			return TikiDb::get();
		}

		try
		{
			static $connectionMap = array();

			if ( ! isset( $connectionMap[$name] ) ) {
				$connectionMap[$name] = false;

				$info = $this->get_dsn_info($name);
				$dbdriver = $info['driver'];
				$dbuserid = $info['user'];
				$dbpassword = $info['password'];
				$dbhost = $info['host'];
				$database = $info['database'];

				$api_tiki = null;
				require 'db/local.php';
				if (isset($api_tiki) &&  $api_tiki == 'adodb') {
					// Force autoloading
					if (! class_exists('ADOConnection')) {
						return null;
					}

					$dbsqlplugin = ADONewConnection($dbdriver);
					if ( $dbsqlplugin->NConnect($dbhost, $dbuserid, $dbpassword, $database) ) {
						$connectionMap[$name] = new TikiDb_AdoDb($dbsqlplugin);
					}
				} else {
					$dbsqlplugin = new PDO("$dbdriver:host=$dbhost;dbname=$database", $dbuserid, $dbpassword);
					$connectionMap[$name] = new TikiDb_Pdo($dbsqlplugin);
				}
			}
			return $connectionMap[$name];
		} catch (Exception $e) {
			TikiLib::lib('errorreport')->report($e->getMessage());
		}
	}

	/*shared*/
	// Returns IP address or IP address forwarded by the proxy if feature load balancer is set
	/**
	 * @return null|string
	 */
	function get_ip_address()
	{
		global $prefs;
		if (isset($prefs['feature_loadbalancer']) && $prefs['feature_loadbalancer'] == "y") {
			$ip = null;

			if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
				$fwips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
				$ip = $fwips[0];
			}
			if ((empty($ip) || strtolower($ip) == 'unknown') && isset($_SERVER['REMOTE_ADDR'])) {
				$ip = $_SERVER['REMOTE_ADDR'];
			}
			return $ip;
		} elseif (isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		} else {
			return '0.0.0.0';
		}
	}

	/*shared*/
	/**
	 * @param $user
	 * @param $section
	 * @return bool
	 */
	function check_rules($user, $section)
	{
		// Admin is never banned
		if ($user == 'admin')
			return false;

		$fullip = $this->get_ip_address();
		$ips = explode(".", $fullip);
		$query = "select tb.`message`,tb.`user`,tb.`ip1`,tb.`ip2`,tb.`ip3`,tb.`ip4`,tb.`mode` from `tiki_banning` tb, `tiki_banning_sections` tbs where tbs.`banId`=tb.`banId` and tbs.`section`=? and ( (tb.`use_dates` = ?) or (tb.`date_from` <= FROM_UNIXTIME(?) and tb.`date_to` >= FROM_UNIXTIME(?)))";
		$result = $this->fetchAll($query, array($section,'n',(int)$this->now,(int)$this->now));

		foreach ( $result as $res ) {
			if (!$res['message']) {
				$res['message'] = tra('You are banned from'). ': ' . $section;
			}

			if ($user && $res['mode'] == 'user') {
				// check user
				$pattern = '/' . $res['user'] . '/';

				if (preg_match($pattern, $user)) {
					return $res['message'];
				}
			} else {
				// check ip
				if (count($ips) == 4) {
					if (($ips[0] == $res['ip1'] || $res['ip1'] == '*') && ($ips[1] == $res['ip2'] || $res['ip2'] == '*')
							&& ($ips[2] == $res['ip3'] || $res['ip3'] == '*') && ($ips[3] == $res['ip4'] || $res['ip4'] == '*')) {
						return $res['message'];
					}
				}
			}
		}
		return false;
	}

	// $noteId 0 means create a new note
	/**
	 * @param $user
	 * @param $noteId
	 * @param $name
	 * @param $data
	 * @param null $parse_mode
	 * @return mixed
	 */
	function replace_note($user, $noteId, $name, $data, $parse_mode = null)
	{
		$size = strlen($data);

		$queryData = array(
			'user' => $user,
			'name' => $name,
			'data' => $data,
			'created' => $this->now,
			'lastModif' => $this->now,
			'size' => (int) $size,
			'parse_mode' => $parse_mode,
		);

		$userNotes = $this->table('tiki_user_notes');
		if ($noteId) {
			$userNotes->update($queryData, array('noteId' => (int) $noteId,));
		} else {
			$noteId = $userNotes->insert($queryData);
		}

		return $noteId;
	}

	/**
	 * @param $offset
	 * @param $maxRecords
	 * @param $sort_mode
	 * @param $find
	 * @return array
	 */
	function list_watches($offset, $maxRecords, $sort_mode, $find)
	{
		$mid = '';
		$mid2 = '';
		$bindvars1 = $bindvars2 = array();
		if ($find) {
			$mid = ' where `event` like ? or `email` like ? or `user` like ? or `object` like ? or `type` like ?';
			$mid2 = ' where `event` like ? or `group` like ? or `object` like ? or `type` like ?';
			$bindvars1 = array("%$find%", "%$find%", "%$find%", "%$find%", "%$find%");
			$bindvars2 = array("%$find%", "%$find%", "%$find%", "%$find%");
		}
		$query = "select 'user' as watchtype, `watchId`, `user`, `event`, `object`, `title`, `type`, `url`, `email` from `tiki_user_watches` $mid
			UNION ALL
				select 'group' as watchtype, `watchId`, `group`, `event`, `object`, `title`, `type`, `url`, '' as `email`
				from `tiki_group_watches` $mid2
			order by ".$this->convertSortMode($sort_mode);
		$query_cant = 'select count(*) from `tiki_user_watches` '.$mid;
		$query_cant2 = 'select count(*) from `tiki_group_watches` '. $mid2;
		$ret = $this->fetchAll($query, array_merge($bindvars1, $bindvars2), $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars1) + $this->getOne($query_cant2, $bindvars2);
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}


	/*shared*/
	/**
	 * @param $user
	 * @param $event
	 * @param $object
	 * @param null $type
	 * @param null $title
	 * @param null $url
	 * @param null $email
	 * @return bool
	 */
	function add_user_watch($user, $event, $object, $type = NULL, $title = NULL, $url = NULL, $email = NULL)
	{
		// Allow a warning when the watch won't be effective
		if (empty($email)) {
			$userlib = TikiLib::lib('user');

			$email = $userlib->get_user_email($user);
			if (empty($email)) {
				return false;
			}
		}

		if ($event != 'auth_token_called') {
			$this->remove_user_watch($user, $event, $object, $type, $email);
		}

		$userWatches = $this->table('tiki_user_watches');
		$userWatches->insert(
			array(
				'user' => $user,
				'event' => $event,
				'object' => $object,
				'email' => $email,
				'type' => $type,
				'title' => $title,
				'url' => $url,
			)
		);
		return true;
	}

	/**
	 * @param $group
	 * @param $event
	 * @param $object
	 * @param null $type
	 * @param null $title
	 * @param null $url
	 * @return bool
	 */
	function add_group_watch($group, $event, $object, $type = NULL, $title = NULL, $url = NULL)
	{

		if ($type == 'Category' && $object == 0) {
			return false;
		} else {
			$this->remove_group_watch($group, $event, $object, $type);
			$groupWatches = $this->table('tiki_group_watches');
			$groupWatches->insert(
				array(
					'group' => $group,
					'event' => $event,
					'object' => $object,
					'type' => $type,
					'title' => $title,
					'url' => $url,
				)
			);
			return true;
		}
	}

	/**
	 * get_user_notification: returns the owner (user) related to a watchId
	 *
	 * @param mixed $id watchId
	 * @access public
	 * @return the user login related to the watchId
	 */
	function get_user_notification($id)
	{

		return $this->table('tiki_user_watches')->fetchOne('user', array('watchId' => $id));

	}
	/*shared*/
	/**
	 * @param $id
	 * @return bool
	 */
	function remove_user_watch_by_id($id)
	{
		global $tiki_p_admin_notifications, $user;
		if ( $tiki_p_admin_notifications === 'y' or $user === $this->get_user_notification($id) ) {
			$this->table('tiki_user_watches')->delete(array('watchId' => (int) $id));

			return true;
		}

		return false;
	}

	/**
	 * @param $id
	 */
	function remove_group_watch_by_id($id)
	{
		$this->table('tiki_group_watches')->delete(array('watchId' => (int) $id,));
	}

	/*shared*/
	/**
	 * @param string $user
	 * @param string $event
	 * @param string $object
	 * @param string $type = 'wiki page'
	 * @param string $email = ''
	 */
	function remove_user_watch($user, $event, $object, $type = 'wiki page', $email = '')
	{
		$conditions = array(
			'user' => $user,
			'event' => $event,
			'object' => $object,
			'type' => $type,
		);

		if ($email) {
			$conditions['email'] = $email;
		}

		$this->table('tiki_user_watches')->deleteMultiple($conditions);
	}

	/*token notification*/
	/**
	 * @param $event
	 * @param $object
	 * @param string $type
	 */
	function remove_user_watch_object($event, $object, $type = 'wiki page')
	{
		$query = "delete from `tiki_user_watches` where `event`=? and `object`=? and `type` = ?";
		$this->query($query, array($event,$object,$type));
	}

	/**
	 * @param $group
	 * @param $event
	 * @param $object
	 * @param string $type
	 */
	function remove_group_watch($group, $event, $object, $type = 'wiki page')
	{
		$conditions = array(
			'group' => $group,
			'event' => $event,
			'object' => $object,
		);
		if (isset($type)) {
			$conditions['type'] = $type;
		}

		$this->table('tiki_group_watches')->deleteMultiple($conditions);
	}

	/*shared*/
	/**
	 * @param $user
	 * @param string $event
	 * @return mixed
	 */
	function get_user_watches($user, $event = '')
	{
		$userWatches = $this->table('tiki_user_watches');

		$conditions = array(
			'user' => $userWatches->exactly($user),
		);

		if ($event) {
			$conditions['event'] = $event;
		}

		return $userWatches->fetchAll($userWatches->all(), $conditions);
	}

	/*shared*/
	/**
	 * @return array
	 */
	function get_watches_events()
	{
		$query = "select distinct `event` from `tiki_user_watches`";
		$result = $this->fetchAll($query, array());
		$ret = array();
		foreach ( $result as $res ) {
			$ret[] = $res['event'];
		}
		return $ret;
	}

	/*shared*/
	/**
	 * @param $user
	 * @param $event
	 * @param $object
	 * @param null $type
	 * @return bool
	 */
	function user_watches($user, $event, $object, $type = NULL)
	{
		$userWatches = $this->table('tiki_user_watches');

		$conditions = array(
			'user' => $user,
			'object' => $object,
		);

		if ($type) {
			$conditions['type'] = $type;
		}

		if (is_array($event)) {
			$conditions['event'] = $userWatches->in($event);

			$ret = $userWatches->fetchColumn('event', $conditions);

			return empty($ret) ? false : $ret;
		} else {
			return $userWatches->fetchCount($conditions);
		}
	}

	/**
	 * @param $object
	 * @param $event
	 * @param null $type
	 * @return mixed
	 */
	function get_groups_watching( $object, $event, $type = NULL )
	{
		$groupWatches = $this->table('tiki_group_watches');
		$conditions = array(
			'object' => $object,
			'event' => $event,
		);

		if ($type) {
			$conditions['type'] = $type;
		}

		return $groupWatches->fetchColumn('group', $conditions);
	}

	/*shared*/
	/**
	 * @param $user
	 * @param $event
	 * @param $object
	 * @return mixed
	 */
	function get_user_event_watches($user, $event, $object)
	{
		return $this->table('tiki_user_watches')->fetchFullRow(
			array(
				'user' => $user,
				'event' => $event,
				'object' => $object,
			)
		);
	}

	/*shared*/
	/**
	 * @param $event
	 * @param $object
	 * @param null $info
	 * @return array
	 */
	function get_event_watches($event, $object, $info=null)
	{
		global $prefs;
		$ret = array();

		$mid = '';
		if ( $prefs['feature_user_watches_translations'] == 'y'  && $event == 'wiki_page_changed') {
			// If $prefs['feature_user_watches_translations'] is turned on, also look for
			// pages in a translation group.
			$mid = "`event`=?";
			$bindvars[] = $event;
			$multilinguallib = TikiLib::lib('multilingual');
			$page_info = $this->get_page_info($object);
			$pages = $multilinguallib->getTranslations('wiki page', $page_info['page_id'], $object, '');
			foreach ($pages as $page) {
				$mids[] = "`object`=?";
				$bindvars[] = $page['objName'];
			}
			$mid .= ' and ('.implode(' or ', $mids).')';
		} elseif ( $prefs['feature_user_watches_translations'] == 'y'
			&& $event == 'wiki_page_created' ) {
			$page_info = $this->get_page_info($object);
			$mid = "`event`='wiki_page_in_lang_created' and `object`=? and `type`='lang'";
			$bindvars[] = $page_info['lang'];
		} elseif ( $prefs['feature_user_watches_languages'] == 'y' && $event == 'category_changed' ) {
			$mid = "`object`=? and ((`event`='category_changed_in_lang' and `type`=? ) or (`event`='category_changed'))";
			$bindvars[] = $object;
			$bindvars[] = $info['lang'];
		} elseif ($event == 'forum_post_topic') {
			$mid = "(`event`=? or `event`=?) and `object`=?";
			$bindvars[] = $event;
			$bindvars[] = 'forum_post_topic_and_thread';
			$bindvars[] = $object;
		} elseif ($event == 'forum_post_thread') {
			$mid = "(`event`=? and `object`=?) or ( `event`=? and `object`=?)";
			$bindvars[] = $event;
			$bindvars[] = $object;
			$bindvars[] = 'forum_post_topic_and_thread';
			$forumId = $info['forumId'];
			$bindvars[] = $forumId;
		} else {
			$extraEvents = "";
			if (substr_count($event, 'article_')) {
				$extraEvents = " or `event`='article_*'";
			} elseif ($event == 'wiki_comment_changes') {
				$extraEvents = " or `event`='wiki_page_changed'";
			// Blog comment mail
			} elseif ($event == 'blog_comment_changes') {
				$extraEvents = " or `event`='blog_page_changed'";
			}
			$mid = "(`event`=?$extraEvents) and (`object`=? or `object`='*')";
			$bindvars[] = $event;
			$bindvars[] = $object;
		}

		// Obtain the list of watches on event/object for user watches
		// Union obtains all users member of groups being watched
		// Distinct union insures there are no duplicates
		$query = "select tuw.`watchId`, tuw.`user`, tuw.`event`, tuw.`object`, tuw.`title`, tuw.`type`, tuw.`url`, tuw.`email`,
				tup1.`value` as language, tup2.`value` as mailCharset
			from
				`tiki_user_watches` tuw
				left join `tiki_user_preferences` tup1 on (tup1.`user`=tuw.`user` and tup1.`prefName`='language')
				left join `tiki_user_preferences` tup2 on (tup2.`user`=tuw.`user` and tup2.`prefName`='mailCharset')
				where $mid
			UNION DISTINCT
			select tgw.`watchId`, uu.`login`, tgw.`event`, tgw.`object`, tgw.`title`, tgw.`type`, tgw.`url`, uu.`email`,
				tup1.`value` as language, tup2.`value` as mailCharset
			from
				`tiki_group_watches` tgw
				inner join `users_usergroups` ug on tgw.`group` = ug.`groupName`
				inner join `users_users` uu on ug.`userId` = uu.`userId` and uu.`email` is not null and uu.`email` <> ''
				left join `tiki_user_preferences` tup1 on (tup1.`user`=uu.`login` and tup1.`prefName`='language')
				left join `tiki_user_preferences` tup2 on (tup2.`user`=uu.`login` and tup2.`prefName`='mailCharset')
				where $mid
				";
		$result = $this->fetchAll($query, array_merge($bindvars, $bindvars));

		if ( count($result) > 0 ) {

			foreach ( $result as $res ) {
				if (empty($res['language'])) {
					$res['language'] = $this->get_preference('site_language');
				}
				switch($event) {
					case 'wiki_page_changed':
					case 'wiki_page_created':
						$res['perm']=($this->user_has_perm_on_object($res['user'], $object, 'wiki page', 'tiki_p_view') ||
								$this->user_has_perm_on_object($res['user'], $object, 'wiki page', 'tiki_p_admin_wiki'));
						break;
					case 'tracker_modified':
						$res['perm'] = $this->user_has_perm_on_object($res['user'], $object, 'tracker', 'tiki_p_view_trackers');
						break;
					case 'tracker_item_modified':
						$res['perm'] = $this->user_has_perm_on_object($res['user'], $info['trackerId'], 'tracker', 'tiki_p_view_trackers');
						break;
					case 'blog_post':
						$res['perm']=($this->user_has_perm_on_object($res['user'], $object, 'blog', 'tiki_p_read_blog') ||
								$this->user_has_perm_on_object($res['user'], $object, 'blog', 'tiki_p_admin_blog'));
						break;
					// Blog comment mail
					case 'blog_comment_changes':
						$res['perm']=($this->user_has_perm_on_object($res['user'], $object, 'blog', 'tiki_p_read_blog') ||
								$this->user_has_perm_on_object($res['user'], $object, 'comments', 'tiki_p_read_comments'));
						break;
					case 'forum_post_topic':
						$res['perm']=($this->user_has_perm_on_object($res['user'], $object, 'forum', 'tiki_p_forum_read') ||
								$this->user_has_perm_on_object($res['user'], $object, 'forum', 'tiki_p_admin_forum'));
						break;
					case 'forum_post_thread':
						$res['perm']=($this->user_has_perm_on_object($res['user'], $forumId, 'forum', 'tiki_p_forum_read') ||
								$this->user_has_perm_on_object($res['user'], $object, 'forum', 'tiki_p_admin_forum'));
						break;
					case 'file_gallery_changed':
						$res['perm']=($this->user_has_perm_on_object($res['user'], $object, 'file gallery', 'tiki_p_view_file_gallery') ||
								$this->user_has_perm_on_object($res['user'], $object, 'file gallery', 'tiki_p_download_files'));
						break;
					case 'article_submitted':
					case 'topic_article_created':
					case 'article_edited':
					case 'topic_article_edited':
					case 'article_deleted':
					case 'topic_article_deleted':
						$userlib = TikiLib::lib('user');
						$res['perm']= ($userlib->user_has_permission($res['user'], 'tiki_p_read_article') &&
								(empty($object) || $this->user_has_perm_on_object($res['user'], $object, 'topic', 'tiki_p_topic_read')));
						break;
					case 'calendar_changed':
						$res['perm']= $this->user_has_perm_on_object($res['user'], $object, 'calendar', 'tiki_p_view_calendar');
						break;
					case 'image_gallery_changed':
						$res['perm'] = $this->user_has_perm_on_object($res['user'], $object, 'image gallery', 'tiki_p_view_image_gallery');
						break;
					case 'category_changed':
						$categlib = TikiLib::lib('categ');
						$res['perm']= $categlib->has_view_permission($res['user'], $object);
						break;
					case 'fgal_quota_exceeded':
						global $tiki_p_admin_file_galleries;
						$res['perm'] = ($tiki_p_admin_file_galleries == 'y');
						break;
					case 'article_commented':
					case 'wiki_comment_changes':
						$res['perm'] = $this->user_has_perm_on_object($res['user'], $object, 'comments', 'tiki_p_read_comments');
						break;
					case 'user_registers':
						$userlib = TikiLib::lib('user');
						$res['perm'] = $userlib->user_has_permission($res['user'], 'tiki_p_admin');
						break;
					case 'auth_token_called':
						$res['perm'] = true;
						break;
					case 'user_joins_group':
						$res['perm']= $this->user_has_perm_on_object($res['user'], $object, 'group', 'tiki_p_group_view_members');
						break;
					default:
						// for security we deny all others.
						$res['perm']=FALSE;
						break;
				}

				if ($res['perm'] || empty($res['user']) && !empty($res['email'])) {
					// Allow admin created email (non-user) watches
					$ret[] = $res;
				}
			}
		}

		// Also include users that are watching a category to which this object belongs to.
		if ( $event != 'category_changed' ) {
			if ($prefs['feature_categories'] == 'y') {
				$categlib = TikiLib::lib('categ');
				$objectType="";
				switch($event) {
					case 'wiki_page_changed': $objectType="wiki page";
						break;
					case 'wiki_page_created': $objectType="wiki page";
						break;
					case 'blog_post': $objectType="blog";
						break;
					// Blog comment mail
					case 'blog_page_changed': $objectType="blog page";
						break;
					case 'map_changed': $objectType="map_changed";
						break;
					case 'forum_post_topic': $objectType="forum";
						break;
					case 'forum_post_thread': $objectType="forum";
						break;
					case 'file_gallery_changed': $objectType="file gallery";
						break;
					case 'article_submitted': $objectType="topic";
						break;
					case 'image_gallery_changed': $objectType="image gallery";
						break;
					case 'tracker_modified': $objectType="tracker";
						break;
					case 'tracker_item_modified': $objectType="tracker";
						break;
					case 'calendar_changed': $objectType="calendar";
						break;
				}
				if ( $objectType != "") {

					// If a forum post was changed, check the categories of the forum.
					if ( $event == "forum_post_thread" ) {
						$commentslib = TikiLib::lib('comments');
						$object = $commentslib->get_comment_forum_id($object);
					}

					// If a tracker item was changed, check the categories of the tracker.
					if ( $event == "tracker_item_modified" ) {
						$trklib = TikiLib::lib('trk');
						$object = $trklib->get_tracker_for_item($object);
					}

					$categs = $categlib->get_object_categories($objectType, $object);

					foreach ($categs as $category) {
						$watching_users = $this->get_event_watches('category_changed', $category, $info);

						// Add all users that are not already included
						foreach ($watching_users as $wu) {
							$included = false;
							foreach ($ret as $item) {
								if ($item['user'] == $wu['user']) {
									$included = true;
								}
							}
							if (!$included) {
								$ret[] = $wu;
							}
						}
					}
				}
			}
		}
		return $ret;
	}

	/*shared*/
	/**
	 * @return array
	 */
	function dir_stats()
	{
		$sites = $this->table('tiki_directory_sites');
		$categories = $this->table('tiki_directory_categories');
		$search = $this->table('tiki_directory_search');

		$aux = array();
		$aux["valid"] = $sites->fetchCount(array('isValid' => 'y'));
		$aux["invalid"] = $sites->fetchCount(array('isValid' => 'n'));
		$aux["categs"] = $categories->fetchCount(array());
		$aux["searches"] = $search->fetchOne($search->sum('hits'), array());
		$aux["visits"] = $search->fetchOne($sites->sum('hits'), array());
		return $aux;
	}

	/*shared*/
	/**
	 * @param $offset
	 * @param $maxRecords
	 * @param $sort_mode
	 * @param $find
	 * @return array
	 */
	function dir_list_all_valid_sites2($offset, $maxRecords, $sort_mode, $find)
	{
		$sites = $this->table('tiki_directory_sites');
		$conditions = array(
			'isValid' => 'y',
		);

		if ($find) {
			$conditions['search'] = $sites->expr('(`name` like ? or `description` like ?)', array("%$find%", "%$find%"));
		}

		return array(
			'data' => $sites->fetchAll($sites->all(), $conditions, $maxRecords, $offset, $sites->expr($this->convertSortMode($sort_mode))),
			'cant' => $sites->fetchCount($conditions),
		);
	}

	/*shared*/
	/**
	 * @param $categId
	 * @return mixed
	 */
	function get_directory($categId)
	{
		return $this->table('tiki_directory_categories')->fetchFullRow(array('categId' => $categId));
	}

	/*shared*/
	/**
	 * @param $user
	 * @return mixed
	 */
	function user_unread_messages($user)
	{
		$messages = $this->table('messu_messages');
		return $messages->fetchCount(
			array(
				'user' => $user,
				'isRead' => 'n',
			)
		);
	}

	/*shared*/
	/**
	 * @return array
	 */
	function get_online_users()
	{
		if ( ! isset($this->online_users_cache) ) {
			$this->update_session();
			$this->online_users_cache=array();
			$query = "select s.`user`, p.`value` as `realName`, `timestamp`, `tikihost` from `tiki_sessions` s left join `tiki_user_preferences` p on s.`user`<>? and s.`user` = p.`user` and p.`prefName` = 'realName' where s.`user` is not null;";
			$result = $this->fetchAll($query, array(''));
			foreach ($result as $res ) {
				$res['user_information'] = $this->get_user_preference($res['user'], 'user_information', 'public');
				$res['allowMsgs'] = $this->get_user_preference($res['user'], 'allowMsgs', 'y');
				$this->online_users_cache[$res['user']] = $res;
			}
		}
		return $this->online_users_cache;
	}

	/*shared*/
	/**
	 * @param $whichuser
	 * @return bool
	 */
	function is_user_online($whichuser)
	{
		if (!isset($this->online_users_cache)) {
			$this->get_online_users();
		}

		return(isset($this->online_users_cache[$whichuser]));
	}

	/*
	 * Score methods begin
	 */
	// All information about an event type
	// shared
	/**
	 * @param $event
	 * @return mixed
	 */
	function get_event($event)
	{
		return $this->table('tiki_score')->fetchFullRow(array('event' => $event));
	}

	// List users by best scoring
	// shared
	/**
	 * @param int $limit
	 * @param int $start
	 * @return mixed
	 */
	function rank_users($limit = 10, $start = 0)
	{
		global $prefs;
		$score_expiry_days = $prefs['feature_score_expday'];

		if (!$start) {
			$start = "0";
		}

		if (empty($score_expiry_days)) {
			// score does not expire
			$query = "select `recipientObjectId` as `login`,
				`pointsBalance` as `score`
				from `tiki_object_scores` tos
				where `recipientObjectType`='user'
				and tos.`id` = (select max(id) from `tiki_object_scores` where `recipientObjectId` = tos.`recipientObjectId` and `recipientObjectType`='user' group by `recipientObjectId`)
				group by `recipientObjectId` order by `score` desc";

			$result = $this->fetchAll($query, NULL, $limit, $start);
		} else {
			// score expires
			$query = "select `recipientObjectId` as `login`,
				`pointsBalance` - ifnull((select `pointsBalance` from `tiki_object_scores`
					where `recipientObjectId`=tos.`recipientObjectId`
					and `recipientObjectType`='user'
					and `date` < UNIX_TIMESTAMP(DATE_SUB(NOW(), INTERVAL ? DAY))
					order by id desc limit 1), 0) as `score`
				from `tiki_object_scores` tos
				where `recipientObjectType`='user'
				and tos.`id` = (select max(id) from `tiki_object_scores` where `recipientObjectId` = tos.`recipientObjectId` and `recipientObjectType`='user' group by `recipientObjectId`)
				group by `recipientObjectId` order by `score` desc";

			$result = $this->fetchAll($query, $score_expiry_days, $limit, $start);
		}

		foreach ( $result as & $res ) {
			$res['position'] = ++$start;
		}
		return $result;
	}

	// Returns html <img> tag to star corresponding to user's score
	// shared
	/**
	 * @param $score
	 * @return string
	 */
	function get_star($score)
	{
		global $prefs;
		$star = '';
		$star_colors = array(0 => 'grey',
				100 => 'blue',
				500 => 'green',
				1000 => 'yellow',
				2500 => 'orange',
				5000 => 'red',
				10000 => 'purple');
		foreach ($star_colors as $boundary => $color) {
			if ($score >= $boundary) {
				$star = 'star_'.$color.'.gif';
			}
		}
		if (!empty($star)) {
			$alt = sprintf(tra("%d points"), $score);
			if ($prefs['theme_iconset'] === 'legacy') {
				$star = "<img src='img/icons/$star' height='11' width='11' alt='$alt' />&nbsp;";
			} else {
				$smarty = TikiLib::lib('smarty');
				$smarty->loadPlugin('smarty_function_icon');
				$star = smarty_function_icon(['name' => 'star', 'istyle' => 'color:' . $color, 'iclass' => 'tips',
					'ititle' => ':' . $alt], $smarty) . "&nbsp;";
			}
		}
		return $star;
	}

	/*
	 * Score methods end
	 */
	//shared
	// \todo remove all hardcoded html in get_user_avatar()
	/**
	 * @param $user
	 * @param string $float
	 * @return string
	 */
	function get_user_avatar($user, $float = "")
	{
		global $prefs;

		if (empty($user))
			return '';

		if ( is_array($user) ) {
			$res = $user;
			$user = $user['login'];
		} else {
			$res = $this->table('users_users')->fetchRow(array('login', 'avatarType', 'avatarLibName', 'email'), array('login' => $user));
		}

		if (!$res) {
			return '';
		}

		if ($prefs['user_use_gravatar'] == 'y' && $res['email']) {
			$https_mode = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on';
			$hash = md5(strtolower(trim($res['email'])));

			if ($https_mode) {
				$url = "https://secure.gravatar.com/avatar/$hash?s=45";
			} else {
				$url = "http://www.gravatar.com/avatar/$hash?s=45";
			}
			$type = 'g';
		} else {
			$type = $res["avatarType"] ? $res["avatarType"] : 'u';
			$libname = $res["avatarLibName"];
			$ret = '';
		}

		$style = '';

		if (strcasecmp($float, "left") == 0) {
			$style = "style='float:left;margin-right:5px;'";
		} else if (strcasecmp($float, "right") == 0) {
			$style = "style='float:right;margin-left:5px;'";
		}

		switch ($type)	{
			case 'l':
				if ($libname) {
					$ret = "<img class='user-profile-picture img-rounded' width='45' height='45' src='" . $libname . "' " . $style . " alt='" . htmlspecialchars($user, ENT_NOQUOTES) . "'>";
				}
				break;
			case 'u':
				$userprefslib = TikiLib::lib('userprefs');
				$path = $userprefslib->get_public_avatar_path($user);

				if ($path) {
					$ret = "<img class='user-profile-picture img-rounded' src='" . htmlspecialchars($path, ENT_NOQUOTES) . "' " . $style . " alt='" . htmlspecialchars($user, ENT_NOQUOTES) . "'>";
				}
				break;
			case 'g':
				$ret = "<img class='user-profile-picture img-rounded' src='" . htmlspecialchars($url, ENT_NOQUOTES) . "' " . $style . " alt='" . htmlspecialchars($user, ENT_NOQUOTES) . "'>";
				break;
			case 'n':
			default:
				$ret = '';
				break;
		}
		return $ret;
	}

	/*shared*/
	/**
	 * @return array
	 */
	function get_forum_sections()
	{
		$query = "select distinct `section` from `tiki_forums` where `section`<>?";
		$result = $this->fetchAll($query, array(''));
		$ret = array();
		foreach ( $result as $res ) {
			$ret[] = $res["section"];
		}
		return $ret;
	}

	/* Referer stats */
	/*shared*/
	/**
	 * @param $referer
	 * @param $fullurl
	 */
	function register_referer($referer,$fullurl)
	{
		$refererStats = $this->table('tiki_referer_stats');

		$cant = $refererStats->fetchCount(array('referer' => $referer));

		if ($cant) {
			$refererStats->update(
				array(
					'hits' => $refererStats->increment(1),
					'last' => $this->now,
					'lasturl' => $fullurl,
				),
				array('referer' => $referer)
			);
		} else {
			$refererStats->insert(
				array(
					'last' => $this->now,
					'referer' => $referer,
					'hits' => 1,
					'lasturl' => $fullurl,
				)
			);
		}
	}

	// File attachments functions for the wiki ////
	/*shared*/
	/**
	 * @param $id
	 * @return bool
	 */
	function add_wiki_attachment_hit($id)
	{
		global $prefs, $user;
		if (StatsLib::is_stats_hit()) {
			$wikiAttachments = $this->table('tiki_wiki_attachments');
			$wikiAttachments->update(
				array('hits' => $wikiAttachments->increment(1)),
				array('attId' => (int) $id)
			);
		}
		return true;
	}

	/*shared*/
	/**
	 * @param $attId
	 * @return mixed
	 */
	function get_wiki_attachment($attId)
	{
		return $this->table('tiki_wiki_attachments')->fetchFullRow(array('attId' => (int) $attId));
	}

	/*shared*/
	/**
	 * @param $id
	 * @return mixed
	 */
	function get_gallery($id)
	{
		return $this->table('tiki_galleries')->fetchFullRow(array('galleryId' => (int) $id));
	}

	// Last visit module ////
	/*shared*/
	/**
	 * @param $user
	 * @return array|bool
	 */
	function get_news_from_last_visit($user)
	{
		if (!$user) return false;

		$last = $this->table('users_users')->fetchOne('lastLogin', array('login' => $user));

		$ret = array();
		if (!$last) {
			$last = time();
		}
		$ret["lastVisit"] = $last;
		$ret["images"] = $this->getOne("select count(*) from `tiki_images` where `created`>?", array((int)$last));
		$ret["pages"] = $this->getOne("select count(*) from `tiki_pages` where `lastModif`>?", array((int)$last));
		$ret["files"] = $this->getOne("select count(*) from `tiki_files` where `created`>?", array((int)$last));
		$ret["comments"] = $this->getOne("select count(*) from `tiki_comments` where `commentDate`>?", array((int)$last));
		$ret["users"] = $this->getOne("select count(*) from `users_users` where `registrationDate`>? and `provpass`=?", array((int)$last, ''));
		$ret["trackers"] = $this->getOne("select count(*) from `tiki_tracker_items` where `lastModif`>?", array((int)$last));
		$ret["calendar"] = $this->getOne("select count(*) from `tiki_calendar_items` where `lastmodif`>?", array((int)$last));
		return $ret;
	}

	/**
	 * @return mixed|string
	 */
	function pick_cookie()
	{
		$cant = $this->getOne("select count(*) from `tiki_cookies`", array());
		if (!$cant) return '';

		$bid = rand(0, $cant - 1);
		//$cookie = $this->getOne("select `cookie`  from `tiki_cookies` limit $bid,1"); getOne seems not to work with limit
		$result = $this->query("select `cookie`  from `tiki_cookies`", array(), 1, $bid);
		if ($res = $result->fetchRow()) {
			$cookie = str_replace("\n", "", $res['cookie']);
			return preg_replace('/^(.+?)(\s*--.+)?$/', '<em>"$1"</em>$2', $cookie);
		} else {
			return "";
		}
	}

	function get_usage_chart_data()
	{
		TikiLib::lib('quiz')->compute_quiz_stats();

		$data['xdata'][] = tra('wiki');
		$data['ydata'][] = $this->getOne('select sum(`hits`) from `tiki_pages`', array());
		$data['xdata'][] = tra('img-g');
		$data['ydata'][] = $this->getOne('select sum(`hits`) from `tiki_galleries`', array());

		$data['xdata'][] = tra('file-g');
		$data['ydata'][] = $this->getOne('select sum(`hits`) from `tiki_file_galleries`', array());

		$data['xdata'][] = tra('FAQs');
		$data['ydata'][] = $this->getOne('select sum(`hits`) from `tiki_faqs`', array());

		$data['xdata'][] = tra('quizzes');
		$data['ydata'][] = $this->getOne('select sum(`timesTaken`) from `tiki_quiz_stats_sum`', array());

		$data['xdata'][] = tra('arts');
		$data['ydata'][] = $this->getOne('select sum(`nbreads`) from `tiki_articles`', array());

		$data['xdata'][] = tra('blogs');
		$data['ydata'][] = $this->getOne('select sum(`hits`) from `tiki_blogs`', array());

		$data['xdata'][] = tra('forums');
		$data['ydata'][] = $this->getOne('select sum(`hits`) from `tiki_forums`', array());

		return $data;
	}

	// User assigned modules ////
	/*shared*/
	/**
	 * @param $id
	 * @return mixed
	 */
	function get_user_login($id)
	{
		return $this->table('users_users')->fetchOne('login', array('userId' => (int) $id));
	}

	/**
	 * @param $u
	 * @return int
	 */
	function get_user_id($u)
	{
		// Anonymous is not in db
		if ( $u == '' ) return -1;

		// If we ask for the current user id and if we already know it in session
		$current = ( isset($_SESSION['u_info']) && $u == $_SESSION['u_info']['login'] );
		if ( isset($_SESSION['u_info']['id']) && $current ) return $_SESSION['u_info']['id'];

		// In other cases, we look in db
		$id = $this->table('users_users')->fetchOne('userId', array('login' => $u));
		$id = ($id === false) ? -1 : $id;
		if ( $current ) $_SESSION['u_info']['id'] = $id;
		return $id;
	}

	/*shared*/
	/**
	 * @param $group
	 * @return array
	 */
	function get_groups_all($group)
	{
		$result = $this->table('tiki_group_inclusion')->fetchColumn('groupName', array('includeGroup' => $group));
		$ret = $result;
		foreach ( $result as $res ) {
			$ret = array_merge($ret, $this->get_groups_all($res));
		}
		return array_unique($ret);
	}

	/*shared*/
	/**
	 * @param $group
	 * @return array
	 */
	function get_included_groups($group)
	{
		$result = $this->table('tiki_group_inclusion')->fetchColumn('includeGroup', array('groupName' => $group));
		$ret = $result;
		foreach ( $result as $res ) {
			$ret = array_merge($ret, $this->get_included_groups($res));
		}
		return array_unique($ret);
	}

	/*shared*/
	/**
	 * @param $user
	 * @return array
	 */
	function get_user_groups($user)
	{
		global $prefs;
		$userlib = TikiLib::lib('user');
		if (empty($user) || $user === 'Anonymous') {
			$ret = array();
			$ret[] = "Anonymous";
			return $ret;
		}
		if ($prefs['feature_intertiki'] == 'y' and empty($prefs['feature_intertiki_mymaster']) and strstr($user, '@')) {
			$realm = substr($user, strpos($user, '@')+1);
			if (isset($prefs['interlist'][$realm])) {
				$user = substr($user, 0, strpos($user, '@'));
				$groups = $prefs['interlist'][$realm]['groups'].',Anonymous';
				return explode(',', $groups);
			}
		}
		if (!isset($this->usergroups_cache[$user])) {
			$userid = $this->get_user_id($user);
			$result = $this->table('users_usergroups')->fetchColumn('groupName', array('userId' => $userid));
			$ret = $result;
			foreach ( $result as $res ) {
				$ret = array_merge($ret, $userlib->get_included_groups($res));
			}
			$ret[] = "Registered";

			if (isset($_SESSION["groups_are_emulated"]) && $_SESSION["groups_are_emulated"]=="y") {
				if (in_array('Admins', $ret)) {
					// Members of group 'Admins' can emulate being in any list of groups
					$ret = unserialize($_SESSION['groups_emulated']);
				} else {
					// For security purposes, user can only emulate a subset of user's list of groups
					// This prevents privilege escalation
					$ret = array_intersect($ret, unserialize($_SESSION['groups_emulated']));
				}
			}
			$ret = array_values(array_unique($ret));
			$this->usergroups_cache[$user] = $ret;
			return $ret;
		} else {
			return $this->usergroups_cache[$user];
		}
	}

	/**
	 * @param $user
	 */
	function invalidate_usergroups_cache($user)
	{
		unset($this->usergroups_cache[$user]);
	}

	/**
	 * @param $user
	 * @return string
	 */
	function get_user_cache_id($user)
	{
		$groups = $this->get_user_groups($user);
		sort($groups, SORT_STRING);
		$cacheId = implode(":", $groups);
		if ($user == 'admin') {
			// in this case user get permissions from no group
			$cacheId = 'ADMIN:'.$cacheId;
		}
		return $cacheId;
	}

	/*shared*/
	/**
	 * @return string
	 */
	function genPass()
	{
		global $prefs;
		$length = max($prefs['min_pass_length'], 8);
		$list = array('aeiou', 'AEIOU', 'bcdfghjklmnpqrstvwxyz', 'BCDFGHJKLMNPQRSTVWXYZ', '0123456789');
		$list[] = $prefs['pass_chr_special'] == 'y'? '_*&+!*-=$@':'_';
		shuffle($list);
		$r = '';
		for ($i = 0; $i < $length; $i++) {
			$ch = $list[$i % count($list)];
			$r .= $ch{rand(0, strlen($ch) - 1)};
		}
		return $r;
	}

	// generate a random string (for unsubscription code etc.)
	/**
	 * @param string $base
	 * @return string
	 */
	function genRandomString($base="")
	{
		if ($base == "") $base = $this->genPass();
		$base .= microtime();
		return md5($base);
	}

	// This function calculates the pageRanks for the tiki_pages
	// it can be used to compute the most relevant pages
	// according to the number of links they have
	// this can be a very interesting ranking for the Wiki
	// More about this on version 1.3 when we add the pageRank
	// column to tiki_pages
	/**
	 * @param int $loops
	 * @return array
	 */
	function pageRank($loops = 16)
	{
		$pagesTable = $this->table('tiki_pages');

		$ret = $pagesTable->fetchColumn('pageName', array());

		// Now calculate the loop
		$pages = array();

		foreach ($ret as $page) {
			$val = 1 / count($ret);

			$pages[$page] = $val;

			$pagesTable->update(array('pageRank' => (int) $val), array('pageName' => $page));
		}

		for ($i = 0; $i < $loops; $i++) {
			foreach ($pages as $pagename => $rank) {
				// Get all the pages linking to this one
				// Fixed query.  -rlpowell
				$query = "select `fromPage`  from `tiki_links` where `toPage` = ? and `fromPage` not like 'objectlink:%'";
				// page rank does not count links from non-page objects TODO: full feature allowing this with options
				$result = $this->fetchAll($query, array($pagename));
				$sum = 0;

				foreach ( $result as $res ) {
					$linking = $res["fromPage"];

					if (isset($pages[$linking])) {
						// Fixed query.  -rlpowell
						$q2 = "select count(*) from `tiki_links` where `fromPage`= ? and `fromPage` not like 'objectlink:%'";
						// page rank does not count links from non-page objects TODO: full feature allowing this with options
						$cant = $this->getOne($q2, array($linking));
						if ($cant == 0) $cant = 1;
						$sum += $pages[$linking] / $cant;
					}
				}

				$val = (1 - 0.85) + 0.85 * $sum;
				$pages[$pagename] = $val;

				$pagesTable->update(array('pageRank' => (int) $val), array('pageName' => $pagename));
			}
		}
		arsort($pages);
		return $pages;
	}

	/**
	 * @param $maxRecords
	 * @return array
	 */
	function list_recent_forum_topics($maxRecords)
	{
		$bindvars = array('forum', 0);

		$query = 'select `threadId`, `forumId` from `tiki_comments`,`tiki_forums`'
			  . " where `object`=`forumId` and `objectType`=? and `parentId`=? order by " . $this->convertSortMode('commentDate_desc');
		$result = $this->fetchAll($query, $bindvars, $maxRecords * 3, 0); // Load a little more, for permission filters
		$res = $ret = $retids = array();
		$n = 0;

		foreach ( $result as $res ) {
			$objperm = $this->get_perm_object($res['forumId'], 'forums', '', false);
			if ($objperm['tiki_p_forum_read'] == 'y') {
				$retids[] = $res['threadId'];

				$n++;

				if ($n >= $maxRecords) {
					break;
				}
			}
		}

		if ( $n > 0 ) {
		  $query = 'select * from `tiki_comments`'
			  . ' where `threadId` in (' . implode(',', $retids) . ') order by ' . $this->convertSortMode('commentDate_desc');
		  $ret = $this->fetchAll($query);
		}

		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $n;
		return $retval;
	}

	/*shared*/
	/**
	 * @param $forumId
	 * @param $offset
	 * @param $maxRecords
	 * @param $sort_mode
	 * @param $find
	 * @return array
	 */
	function list_forum_topics($forumId, $offset, $maxRecords, $sort_mode, $find)
	{
		$bindvars = array($forumId,$forumId,'forum',0);
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " and (`title` like ? or `data` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_comments`,`tiki_forums` where ";
		$query.= " `forumId`=? and `object`=? and `objectType`=? and `parentId`=? $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_comments`,`tiki_forums` where ";
		$query_cant.= " `forumId`=? and `object`=? and `objectType`=? and `parentId`=? $mid";
		$ret = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	/*shared*/
	/**
	 * @param $type
	 * @param $id
	 * @return bool
	 */
	function remove_object($type, $id)
	{
		global $prefs;
		$categlib = TikiLib::lib('categ');
		$objectlib = TikiLib::lib('object');
		$categlib->uncategorize_object($type, $id);

		// Now remove comments
		$threads = $this->table('tiki_comments')->fetchColumn('threadId', array('object' => $id, 'objectType' => $type));
		if ( !empty($threads) ) {
			$commentslib = TikiLib::lib('comments');

			foreach ( $threads as $threadId ) {
				$commentslib->remove_comment($threadId);
			}
		}

		// Remove individual permissions for this object if they exist
		$object = $type . $id;
		$this->table('users_objectpermissions')->deleteMultiple(array('objectId' => md5($object), 'objectType' => $type));
		// remove links from this object to pages
		$linkhandle = "objectlink:$type:$id";
		$this->table('tiki_links')->deleteMultiple(array('fromPage' => $linkhandle));
		// remove fgal backlinks
		if ( $prefs['feature_file_galleries'] == 'y') {
			$filegallib = TikiLib::lib('filegal');
			$filegallib->deleteBacklinks(array('type'=>$type, 'object'=>$id));
		}
		// remove object
		$objectlib->delete_object($type, $id);

		$objectAttributes = $this->table('tiki_object_attributes');
		$objectAttributes->deleteMultiple(array('type' => $type,'itemId' => $id));

		$objectRelations = $this->table('tiki_object_relations');
		$objectRelations->deleteMultiple(array('source_type' => $type,	'source_itemId' => $id));
		$objectRelations->deleteMultiple(array('target_type' => $type,	'target_itemId' => $id));

		return true;
	}

	/*shared*/
	/**
	 * @param $offset
	 * @param $maxRecords
	 * @param $sort_mode
	 * @param string $find
	 * @param string $type
	 * @param string $structureName
	 * @return array
	 */
	function list_received_pages($offset, $maxRecords, $sort_mode, $find='', $type='', $structureName='')
	{
		$bindvars = array();
		if ($type == 's')
			$mid = ' `trp`.`structureName` is not null ';
			if (!$sort_mode)
				$sort_mode = '`structureName_asc';
		elseif ($type == 'p')
			$mid = ' `trp`.`structureName` is null ';
			if (!$sort_mode)
				$sort_mode = '`pageName_asc';
		else
			$mid = '';

		if ($find) {
			$findesc = '%'.$find.'%';
			if ($mid)
				$mid .= ' and ';
			$mid .= '(`trp`.`pageName` like ? or `trp`.`structureName` like ? or `trp`.`data` like ?)';
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		}
		if ($structureName) {
			if ($mid)
				$mid .= ' and ';
			$mid .= ' `trp`.`structureName`=? ';
			$bindvars[] = $structureName;
		}
		if ($mid)
			$mid = "where $mid";

		$query = "select trp.*, tp.`pageName` as pageExists from `tiki_received_pages` trp left join `tiki_pages` tp on (tp.`pageName`=trp.`pageName`) $mid order by `structureName` asc, `pos` asc," . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_received_pages` trp $mid";
		$ret = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	// User voting system ////
	// Used to vote everything (polls,comments,files,submissions,etc) ////
	// Checks if a user has voted
	/*shared*/
	/**
	 * @param $user
	 * @param $id
	 * @return bool
	 */
	function user_has_voted($user, $id)
	{
		global $prefs;

		$ret = false;

		if (isset($_SESSION['votes'])) {
			$votes = $_SESSION['votes'];
			if (is_array($votes) && in_array($id, $votes)) { // has already voted in the session (logged or not)
				return true;
			}
		}

		if (!$user) {
			if ($prefs['ip_can_be_checked'] != 'y' && !isset($_COOKIE[ session_name() ])) {// cookie has not been activated too bad for him
				$ret = true;
			} elseif (isset($_COOKIE[md5("tiki_wiki_poll_$id")])) {
				$ret = true;
			}
			// we have no idea if cookie was deleted  or if really he has not voted
		} else {
			$query = "select count(*) from `tiki_user_votings` where `user`=? and `id`=?";
			if ($this->getOne($query, array($user,(string) $id)) > 0) {
				$ret = true;
			}
		}
		if ($prefs['ip_can_be_checked'] == 'y') {
			$query = 'select count(*) from `tiki_user_votings` where `ip`=? and `id`=?';
			if ($this->getOne($query, array($this->get_ip_address(), $id)) > 0) {
				return true; // IP has already voted logged or not
			}
		}
		return $ret;
	}

	// Registers a user vote
	/*shared*/
	/**
	 * @param $user
	 * @param $id
	 * @param bool $optionId
	 * @param array $valid_options
	 * @param bool $allow_revote
	 * @return bool
	 */
	function register_user_vote($user, $id, $optionId=false, array $valid_options = array(), $allow_revote = false )
	{
		global $prefs;

		// If an option is specified and the valid options are specified, skip the vote entirely if not valid
		if ( false !== $optionId && count($valid_options) > 0 && ! in_array($optionId, $valid_options) ) {
			return false;
		}

		if ( $user && ! $allow_revote && $this->user_has_voted($user, $id) ) {
			return false;
		}

		$userVotings = $this->table('tiki_user_votings');

		$ip = $this->get_ip_address();
		$_SESSION['votes'][] = $id;
		setcookie(md5("tiki_wiki_poll_$id"), $ip, time()+60*60*24*300);
		if (!$user) {
			if ($prefs['ip_can_be_checked'] == 'y') {
				$userVotings->delete(array('ip' => $ip, 'id' => $id, 'user' => ''));
				if ( $optionId !== false && $optionId != 'NULL' ) {
					$userVotings->insert(
						array(
							'user' => '',
							'ip' => $ip,
							'id' => (string) $id,
							'optionId' => (int) $optionId,
							'time' => $this->now,
						)
					);
				}
			} elseif (isset($_COOKIE[md5("tiki_wiki_poll_$id")])) {
				return false;
			} elseif ($optionId !== false && $optionId != 'NULL' ) {
				$userVotings->insert(
					array(
						'user' => '',
						'ip' => $ip,
						'id' => (string) $id,
						'optionId' => (int) $optionId,
						'time' => $this->now,
					)
				);
			}
		} else {
			if ($prefs['ip_can_be_checked'] == 'y') {
				$userVotings->delete(array('user' => $user,'id' => $id));
				$userVotings->delete(array('ip' => $ip,'id' => $id));
			} else {
				$userVotings->delete(array('user' => $user,'id' => $id));
			}
			if ( $optionId !== false  && $optionId !== 'NULL' ) {
				$userVotings->insert(
					array(
						'user' => $user,
						'ip' => $ip,
						'id' => (string) $id,
						'optionId' => (int) $optionId,
						'time' => $this->now,
					)
				);
			}
		}

		return true;
	}

	/**
	 * @param $id
	 * @param $user
	 * @return null
	 */
	function get_user_vote($id,$user)
	{
		global $prefs;
		$vote = null;
		if ($user) {
			$vote = $this->getOne("select `optionId` from `tiki_user_votings` where `user` = ? and `id` = ? order by `time` desc", array( $user, $id));
		}
		if ($vote == null && $prefs['ip_can_be_checked'] == 'y') {
			$vote = $this->getOne("select `optionId` from `tiki_user_votings` where `ip` = ? and `id` = ? order by `time` desc", array( $user, $id));
		}
		return $vote;
	}
	// end of user voting methods

	// Semaphore functions ////
	/**
	 * @param $semName
	 * @param string $objectType
	 * @return bool|mixed|null|string
	 */
	function get_semaphore_user($semName, $objectType='wiki page')
	{
		global $user;
		// the old semaphores have been deleted by semaphore_is_set - this function must be called before
		$query = "select `user` from `tiki_semaphores` where `semName`=? and `objectType`=?";
		$result = $this->fetchAll($query, array($semName, $objectType));
		$user_is_in = false;
		foreach ( $result as $res ) {
			if ($res['user'] != $user || (!$user && $res['user'] == 'anonymous')) {
				return $res['user']; // return the other users if exist
			} else {
				$user_is_in = true;
			}
		}
		if ($user_is_in)
			return $user;
		else
			return '';
	}

	/**
	 * @param $semName
	 * @param $limit
	 * @param string $objectType
	 * @return mixed
	 */
	function semaphore_is_set($semName, $limit, $objectType='wiki page')
	{
		$lim = $this->now - $limit;

		$semaphores = $this->table('tiki_semaphores');
		$semaphores->deleteMultiple(array('timestamp' => $semaphores->lesserThan((int) $lim)));

		$query = "select `semName`  from `tiki_semaphores` where `semName`=? and `objectType`=?";
		$result = $this->query($query, array($semName, $objectType));
		return $result->numRows();
	}

	/**
	 * @param $semName
	 * @param string $objectType
	 * @return int
	 */
	function semaphore_set($semName, $objectType='wiki page')
	{
		global $user;

		if ($user == '') {
			$user = 'anonymous';
		}

		$semaphores = $this->table('tiki_semaphores');
		$semaphores->delete(array('semName' => $semName,'objectType' => $objectType));
		$semaphores->insert(
			array(
				'semName' => $semName,
				'timestamp' => $this->now,
				'user' => $user,
				'objectType' => $objectType,
			)
		);
		return $this->now;
	}

	/**
	 * @param $semName
	 * @param $lock
	 * @param string $objectType
	 */
	function semaphore_unset($semName, $lock, $objectType='wiki page')
	{
		$semaphores = $this->table('tiki_semaphores');
		$semaphores->delete(array('semName' => $semName,'timestamp' => (int) $lock,'objectType' => $objectType));
	}

	/**
	 * @param int $offset
	 * @param $maxRecords
	 * @param string $sort_mode
	 * @param string $find
	 * @param bool $include_prefs
	 * @return array
	 */
	function list_users($offset = 0, $maxRecords = -1, $sort_mode = 'pref:realName', $find = '', $include_prefs = false)
	{
		global $user, $prefs;
		$userprefslib = TikiLib::lib('userprefs');

		$bindvars = array();
		if ( $find ) {
			$findesc = '%'.$find.'%';
			$mid = 'where (`login` like ? or p1.`value` like ?)';
			$mid_cant = $mid;
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
			$bindvars2 = array($findesc, $findesc);
			$find_join = " left join `tiki_user_preferences` p1 on (u.`login` = p1.`user` and p1.`prefName` = 'realName')";
			$find_join_cant = $find_join;
		} else {
			$mid = '';
			$bindvars2 = array();
			$find_join = '';
			$find_join_cant = '';
			$mid_cant = '';
		}

		// This allows to use a sort_mode by prefs
		// In this case, sort_mode must have this syntax :
		//   pref:PREFERENCE_NAME[_asc|_desc]
		// e.g. to sort on country :
		//   pref:country  OR  pref:country_asc  OR  pref:country_desc

		if ( $ppos = strpos($sort_mode, ':') ) {

			$sort_value = substr($sort_mode, $ppos + 1);
			$sort_way = 'asc';

			if ( preg_match('/^(.+)_(asc|desc)$/i', $sort_value, $regs) ) {
				$sort_value = $regs[1];
				$sort_way = $regs[2];
				unset($regs);
			}

			if ( $find_join != '' && $sort_value == 'realName' ) {
				// Avoid two joins if we can do only one
				$find_join = '';
				$mid = 'where (`login` like ? or p.`value` like ?)';
			}
			$sort_mode = "p.`value` $sort_way";
			$pref_where = ( ( $mid == '' ) ? 'where' : $mid.' and' )." p.`prefName` = '$sort_value'";
			$pref_join = 'left join `tiki_user_preferences` p on (u.`login` = p.`user`)';
			$pref_field = ', p.`value` as sf';

		} else {

			$sort_mode = $this->convertSortMode($sort_mode);
			$pref_where = $mid;
			$pref_join = '';
			$pref_field = '';
		}

		if ( $sort_mode != '' ) $sort_mode = 'order by '.$sort_mode;

		$query = "select u.* $pref_field  from `users_users` u $pref_join $find_join $pref_where $sort_mode";

		$query_cant = "select count(distinct u.`login`) from `users_users` u $find_join_cant $mid_cant";
		$result = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars2);

		$ret = array();
		foreach ( $result as $res ) {
			if ( $include_prefs ) {
				$res['preferences'] = $userprefslib->get_userprefs($res['login']);
			}
			$ret[] = $res;
		}

		return array('data' => $ret, 'cant' => $cant);
	}

	// CMS functions -ARTICLES- & -SUBMISSIONS- ////
	/*shared*/
	/**
	 * @param int $max
	 * @return mixed
	 */
	function get_featured_links($max = 10)
	{
		$query = "select * from `tiki_featured_links` where `position` > ? order by ".$this->convertSortMode("position_asc");
		return  $this->fetchAll($query, array(0), (int)$max, 0);
	}

	/**
	 * @param $sessionId
	 */
	function setSessionId($sessionId)
	{
		$this->sessionId = $sessionId;
	}

	/**
	 * @return null
	 */
	function getSessionId()
	{
		return $this->sessionId;
	}

	/**
	 * @return bool
	 */
	function update_session()
	{
		static $uptodate = false;
		if ( $uptodate === true || $this->sessionId === null ) return true;

		global $user, $prefs;
		$logslib = TikiLib::lib('logs');

		if ($user === false) $user = '';
		// If pref login_multiple_forbidden is set, length of tiki_sessions must match real session length to be up to date so we can detect concurrent logins of same user
		if ( $prefs['login_multiple_forbidden'] == 'y' ) {
			$delay = ini_get('session.gc_maxlifetime');
		} else {	// Low value so as to guess who actually is in front of the computer
			$delay = 5*60; // 5 minutes
		}
		$oldy = $this->now - $delay;
		if ($user != '') { // was the user timeout?
			$query = "select count(*) from `tiki_sessions` where `sessionId`=?";
			$cant = $this->getOne($query, array($this->sessionId));
			if ($cant == 0) {
				if ( $prefs['login_multiple_forbidden'] != 'y' || $user == 'admin' ) {
					// Recover after timeout
					$logslib->add_log("login", "back", $user, '', '', $this->now);
				} else {
					// Prevent multiple sessions for same user
					// Must check any user session, not only timed out ones
					$query = "SELECT count(*) FROM `tiki_sessions` WHERE user = ?";
					$cant = $this->getOne($query, array($user));
					if ($cant == 0) {
						// Recover after timeout (no other session)
						$logslib->add_log("login", "back", $user, '', '', $this->now);
					} else {
						// User has an active session on another browser
						$userlib = TikiLib::lib('user');
						$userlib->user_logout($user, false, '');
					}
				}
			}
		}
		$query = "select * from `tiki_sessions` where `timestamp`<?";
		$result = $this->fetchAll($query, array($oldy));
		foreach ( $result as $res ) {
			if ($res['user'] && $res['user'] != $user)
				$logslib->add_log('login', 'timeout', $res['user'], ' ', ' ', $res['timestamp']+ $delay);
		}

		$sessions = $this->table('tiki_sessions');

		$sessions->delete(array('sessionId' => $this->sessionId));
		$sessions->deleteMultiple(array('timestamp' => $sessions->lesserThan($oldy)));

		if ($user) {
			$sessions->delete(array('user' => $user));
		}

		$sessions->insert(
			array(
				'sessionId' => $this->sessionId,
				'timestamp' => $this->now,
				'user' => $user,
				'tikihost' => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost',
			)
		);
		if ($prefs['session_storage'] == 'db') {
			// clean up adodb sessions as well in case adodb session garbage collection not working
			$sessions = $this->table('sessions');

			$sessions->deleteMultiple(array('expiry' => $sessions->lesserThan($oldy)));
		}

		$uptodate = true;
		return true;
	}

	// Returns the number of registered users which logged in or were active in the last 5 minutes.
	/**
	 * @return mixed
	 */
	function count_sessions()
	{
		$this->update_session();
		return $this->table('tiki_sessions')->fetchCount(array());
	}

	// Returns a string-indexed array with all the hosts/servers active in the last 5 minutes. Keys are hostnames. Values represent the number of registered users which logged in or were active in the last 5 minutes on the host.
	/**
	 * @return array
	 */
	function count_cluster_sessions()
	{
		$this->update_session();
		$query = "select `tikihost`, count(`tikihost`) as cant from `tiki_sessions` group by `tikihost`";
		return $this->fetchMap($query, array());
	}

	/**
	 * @param $links
	 * @return bool
	 */
	function cache_links($links)
	{
		global $prefs;
		if ($prefs['cachepages'] != 'y') return false;
		foreach ($links as $link) {
			if (!$this->is_cached($link)) {
				$this->cache_url($link);
			}
		}
	}

	/**
	 * @param $data
	 * @return array
	 */
	function get_links($data)
	{
		$links = array();

		/// Prevent the substitution of link [] inside a <tag> ex: <input name="tracker[9]" ... >
		$data = preg_replace("/<[^>]*>/", "", $data);

		/// Match things like [...], but ignore things like [[foo].
		// -Robin
		if (preg_match_all("/(?<!\[)\[([^\[\|\]]+)(?:\|?[^\[\|\]]+){0,2}\]/", $data, $r1)) {
			$res = $r1[1];
			$links = array_unique($res);
		}

		return $links;
	}

	/**
	 * @param $data
	 * @return array
	 */
	function get_links_nocache($data)
	{
		$links = array();

		if (preg_match_all("/\[([^\]]+)/", $data, $r1)) {
			$res = array();

			foreach ($r1[1] as $alink) {
				$parts = explode('|', $alink);

				if (isset($parts[1]) && $parts[1] == 'nocache') {
					$res[] = $parts[0];
				} elseif (isset($parts[2]) && $parts[2] == 'nocache') {
					$res[] = $parts[0];
				} else {
					if (isset($parts[3]) && $parts[3] == 'nocache') {
						$res[] = $parts[0];
					}
				}
				/// avoid caching URLs with common binary file extensions
				$extension = substr($parts[0], -4);
				$binary = array(
						'.arj',
						'.asf',
						'.avi',
						'.bz2',
						'.com',
						'.dat',
						'.doc',
						'.exe',
						'.hqx',
						'.mid',
						'.mov',
						'.mp3',
						'.mpg',
						'.ogg',
						'.pdf',
						'.ram',
						'.rar',
						'.rpm',
						'.rtf',
						'.sea',
						'.sit',
						'.tar',
						'.tgz',
						'.wav',
						'.wmv',
						'.xls',
						'.zip',
						'ar.Z', // .tar.Z
						'r.gz'  // .tar.gz
							);
				if (in_array($extension, $binary)) {
					$res[] = $parts[0];
				}

			}

			$links = array_unique($res);
		}

		return $links;
	}

	/**
	 * @param $url
	 * @return bool
	 */
	function is_cacheable($url)
	{
		// simple implementation: future versions should analyse
		// if this is a link to the local machine
		if (strstr($url, 'tiki-')) {
			return false;
		}

		if (strstr($url, 'messu-')) {
			return false;
		}

		return true;
	}

	/**
	 * @param $url
	 * @return mixed
	 */
	function is_cached($url)
	{
		return $this->table('tiki_link_cache')->fetchCount(array('url' => $url));
	}

	/**
	 * @param $offset
	 * @param $maxRecords
	 * @param $sort_mode
	 * @param $find
	 * @return array
	 */
	function list_cache($offset, $maxRecords, $sort_mode, $find)
	{

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`url` like ?) ";
			$bindvars=array($findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		$query = "select `cacheId` ,`url`,`refresh` from `tiki_link_cache` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_link_cache` $mid";
		$ret = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	/**
	 * @param $cacheId
	 * @return bool
	 */
	function refresh_cache($cacheId)
	{
		$linkCache = $this->table('tiki_link_cache');

		$url = $linkCache->fetchOne('url', array('cacheId' => $cacheId));

		$data = $this->httprequest($url);

		$linkCache->update(array('data' => $data,	'refresh' => $this->now), array('cacheId' => $cacheId));
		return true;
	}

	/**
	 * @param $cacheId
	 * @return bool
	 */
	function remove_cache($cacheId)
	{
		$linkCache = $this->table('tiki_link_cache');
		$linkCache->delete(array('cacheId' => $cacheId));

		return true;
	}

	/**
	 * @param $cacheId
	 * @return mixed
	 */
	function get_cache($cacheId)
	{
		return $this->table('tiki_link_cache')->fetchFullRow(array('cacheId' => $cacheId));
	}

	/**
	 * @param $url
	 * @return bool
	 */
	function get_cache_id($url)
	{
		$id =  $this->table('tiki_link_cache')->fetchOne('cacheId', array('url' => $url));
		return $id ? $id : false;
	}
	/* cachetime = 0 => no cache, otherwise duration cache is valid */
	/**
	 * @param $url
	 * @param $isFresh
	 * @param int $cachetime
	 * @return mixed
	 */
	function get_cached_url($url, &$isFresh, $cachetime=0)
	{
		$linkCache = $this->table('tiki_link_cache');

		$res = $linkCache->fetchFullRow(array('url' => $url));
		$now =  $this->now;

		if (empty($res) || ($now - $res['refresh']) > $cachetime) { // no cache or need to refresh
			$res['data'] = $this->httprequest($url);
			$isFresh = true;
			//echo '<br />Not cached:'.$url.'/'.strlen($res['data']);
			$res['refresh'] = $now;
			if ($cachetime > 0) {
				if (empty($res['cacheId'])) {
					$linkCache->insert(array('url' => $url, 'data' => $res['data'], 'refresh' => $res['refresh']));

					$res = $linkCache->fetchFullRow(array('url' => $url));
				} else {
					$linkCache->update(array('data' => $res['data'], 'refresh' => $res['refresh']), array('cacheId' => $res['cacheId']));
				}
			}
		} else {
			//echo '<br />Cached:'.$url;
			$isFresh = false;
		}
		return $res;
	}

	// This funcion return the $limit most accessed pages
	// it returns pageName and hits for each page
	/**
	 * @param $limit
	 * @return array
	 */
	function get_top_pages($limit)
	{
		$query = "select `pageName` , `hits`
			from `tiki_pages`
			order by `hits` desc";

		$result = $this->fetchAll($query, array(), $limit);
		$ret = array();

		foreach ( $result as $res ) {
			$aux["pageName"] = $res["pageName"];

			$aux["hits"] = $res["hits"];
			$ret[] = $aux;
		}

		return $ret;
	}

	// Returns the name of all pages
	/**
	 * @return mixed
	 */
	function get_all_pages()
	{
		return $this->table('tiki_pages')->fetchAll(array('pageName'), array());
	}

	/**
	 * \brief Cache given url
	 * If \c $data present (passed) it is just associated \c $url and \c $data.
	 * Else it will request data for given URL and store it in DB.
	 * Actualy (currently) data may be proviced by TIkiIntegrator only.
	 */
	function cache_url($url, $data = '')
	{
		// Avoid caching internal references... (only if $data not present)
		// (cdx) And avoid other protocols than http...
		// 03-Nov-2003, by zaufi
		// preg_match("_^(mailto:|ftp:|gopher:|file:|smb:|news:|telnet:|javascript:|nntp:|nfs:)_",$url)
		// was removed (replaced to explicit http[s]:// detection) bcouse
		// I now (and actualy use in my production Tiki) another bunch of protocols
		// available in my konqueror... (like ldap://, ldaps://, nfs://, fish://...)
		// ... seems like it is better to enum that allowed explicitly than all
		// noncacheable protocols.
		if (((strstr($url, 'tiki-') || strstr($url, 'messu-')) && $data == '')
				|| (substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://'))
			return false;
		// Request data for URL if nothing given in parameters
		// (reuse $data var)
		if ($data == '') $data = $this->httprequest($url);

		// If stuff inside [] is *really* malformatted, $data
		// will be empty.  -rlpowell
		if ($data) {
			$linkCache = $this->table('tiki_link_cache');
			$linkCache->insert(array('url' => $url, 'data' => $data, 'refresh' => $this->now));
			return true;
		}
		else return false;
	}

	// Removes all the versions of a page and the page itself
	/*shared*/
	/**
	 * @param $page
	 * @param string $comment
	 * @return bool
	 */
	function remove_all_versions($page, $comment = '')
	{
		global $user, $prefs;
		if ($prefs['feature_actionlog'] == 'y') {
			$info= $this->get_page_info($page);
			$params = 'del='.strlen($info['data']);
		} else {
			$params = '';
		}
		//  Deal with mail notifications.
		include_once('lib/notifications/notificationemaillib.php');
		$foo = parse_url($_SERVER["REQUEST_URI"]);
		$machine = self::httpPrefix(true). dirname($foo["path"]);
		$page_info = $this->get_page_info($page);
		sendWikiEmailNotification('wiki_page_deleted', $page, $user, $comment, 1, $page_info['data'], $machine);

		//Remove the bibliography references for this page
		$this->removePageReference($page);

		$wikilib = TikiLib::lib('wiki');
		$multilinguallib = TikiLib::lib('multilingual');
		$multilinguallib->detachTranslation('wiki page', $multilinguallib->get_page_id_from_name($page));
		$this->invalidate_cache($page);
		//Delete structure references before we delete the page
		$query  = "select `page_ref_id` ";
		$query .= "from `tiki_structures` ts, `tiki_pages` tp ";
		$query .= "where ts.`page_id`=tp.`page_id` and `pageName`=?";
		$result = $this->fetchAll($query, array($page));
		foreach ( $result as $res ) {
			$this->remove_from_structure($res["page_ref_id"]);
		}

		$this->table('tiki_pages')->delete(array('pageName' => $page));
		if ($prefs['feature_contribution'] == 'y') {
			$contributionlib = TikiLib::lib('contribution');
			$contributionlib->remove_page($page);
		}
		$this->table('tiki_history')->deleteMultiple(array('pageName' => $page));
		$this->table('tiki_links')->deleteMultiple(array('fromPage' => $page));
		$logslib = TikiLib::lib('logs');
		$logslib->add_action('Removed', $page, 'wiki page', $params);
		//get_strings tra("Removed");
		$this->table('users_groups')->updateMultiple(array('groupHome' => null), array('groupHome' => $page));

		$this->table('tiki_theme_control_objects')->deleteMultiple(array('name' => $page,'type' => 'wiki page'));
		$this->table('tiki_copyrights')->deleteMultiple(array('page' => $page));

		$this->remove_object('wiki page', $page);

		$this->table('tiki_user_watches')->deleteMultiple(array('event' => 'wiki_page_changed', 'object' => $page));
		$this->table('tiki_group_watches')->deleteMultiple(array('event' => 'wiki_page_changed', 'object' => $page));

		$atts = $wikilib->list_wiki_attachments($page, 0, -1, 'created_desc', '');
		foreach ($atts["data"] as $at) {
			$wikilib->remove_wiki_attachment($at["attId"]);
		}

		$wikilib->remove_footnote('', $page);
		$this->refresh_index('wiki page', $page);

		return true;
	}

	/*shared*/
	/**
	 * @param $page_ref_id
	 * @return bool
	 */
	function remove_from_structure($page_ref_id)
	{
		// Now recursively remove
		$query  = "select `page_ref_id` ";
		$query .= "from `tiki_structures` as ts, `tiki_pages` as tp ";
		$query .= "where ts.`page_id`=tp.`page_id` and `parent_id`=?";
		$result = $this->fetchAll($query, array($page_ref_id));

		foreach ( $result as $res ) {
			$this->remove_from_structure($res["page_ref_id"]);
		}

		$structlib = TikiLib::lib('struct');
		$page_info = $structlib->s_get_page_info($page_ref_id);

		$structures = $this->table('tiki_structures');

		$structures->updateMultiple(
			array('pos' => $structures->decrement(1)),
			array('pos' => $structures->greaterThan((int) $page_info['pos']),	'parent_id' => (int) $page_info['parent_id'],)
		);

		$structures->delete(array('page_ref_id' => $page_ref_id));
		return true;
	}

	/*shared*/
	/**
	 * @param int $offset
	 * @param $maxRecords
	 * @param string $sort_mode
	 * @param string $user
	 * @param null $find
	 * @return array
	 */
	function list_galleries($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $user = '', $find = null)
	{
		// If $user is admin then get ALL galleries, if not only user galleries are shown
		global $tiki_p_admin_galleries, $tiki_p_admin;

		$old_sort_mode = '';

		if (in_array($sort_mode, array('images desc', 'images asc'))) {
			$old_offset = $offset;

			$old_maxRecords = $maxRecords;
			$old_sort_mode = $sort_mode;
			$sort_mode = 'user desc';
			$offset = 0;
			$maxRecords = -1;
		}

		// If the user is not admin then select `it` 's own galleries or public galleries
		if ( $tiki_p_admin_galleries === 'y' or $tiki_p_admin === 'y') {
			$whuser = "";
			$bindvars = array();
		} else {
			$whuser = "where `user`=? or public=?";
			$bindvars = array($user,'y');
		}

		if ( ! empty($find) ) {
			$findesc = '%' . $find . '%';

			if (empty($whuser)) {
				$whuser = "where `name` like ? or `description` like ?";
				$bindvars=array($findesc,$findesc);
			} else {
				$whuser .= " and `name` like ? or `description` like ?";
				$bindvars[]=$findesc;
				$bindvars[]=$findesc;
			}
		}

		// If sort mode is versions then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		// If sort mode is links then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		// If sort mode is backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		$query = "select * from `tiki_galleries` $whuser order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_galleries` $whuser";
		$result = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		$images = $this->table('tiki_images');
		foreach ( $result as $res ) {

			global $user;
			$add=$this->user_has_perm_on_object($user, $res['galleryId'], 'image gallery', 'tiki_p_view_image_gallery');
			if ($add) {
				$aux = array();

				$aux["name"] = $res["name"];
				$gid = $res["galleryId"];
				$aux["visible"] = $res["visible"];
				$aux["id"] = $gid;
				$aux["galleryId"] = $res["galleryId"];
				$aux["description"] = $res["description"];
				$aux["created"] = $res["created"];
				$aux["lastModif"] = $res["lastModif"];
				$aux["user"] = $res["user"];
				$aux["hits"] = $res["hits"];
				$aux["public"] = $res["public"];
				$aux["theme"] = $res["theme"];
				$aux["geographic"] = $res["geographic"];
				$aux["images"] = $images->fetchCount(array('galleryId' => $gid));
				$ret[] = $aux;
			}
		}

		if ($old_sort_mode == 'images asc') {
			usort($ret, 'compare_images');
		}

		if ($old_sort_mode == 'images desc') {
			usort($ret, 'r_compare_images');
		}

		if (in_array($old_sort_mode, array('images desc', 'images asc'))) {
			$ret = array_slice($ret, $old_offset, $old_maxRecords);
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	// Deprecated in favor of list_pages
	/**
	 * @param $maxRecords
	 * @param string $categories
	 * @return array
	 */
	function last_pages($maxRecords = -1, $categories='')
	{
		if (is_array($categories))
			$filter=array("categId" => $categories);
		else
			$filter=array();

		return $this->list_pages(0, $maxRecords, "lastModif_desc", '', '', true, true, false, false, $filter);
	}

	// Broken. Equivalent to last_pages($maxRecords)
	/**
	 * @param $maxRecords
	 * @return array
	 */
	function last_major_pages($maxRecords = -1)
	{
		return $this->list_pages(0, $maxRecords, "lastModif_desc");
	}
	// use this function to speed up when pagename is only needed (the 3 getOne can killed tikiwith more that 3000 pages)
	/**
	 * @param int $offset
	 * @param $maxRecords
	 * @param string $sort_mode
	 * @param string $find
	 * @return array
	 */
	function list_pageNames($offset = 0, $maxRecords = -1, $sort_mode = 'pageName_asc', $find = '')
	{
		return $this->list_pages($offset, $maxRecords, $sort_mode, $find, '', true, true);
	}

	/**
	 * @param int $offset
	 * @param $maxRecords
	 * @param string $sort_mode
	 * @param string $find
	 * @param string $initial
	 * @param bool $exact_match
	 * @param bool $onlyName
	 * @param bool $forListPages
	 * @param bool $only_orphan_pages
	 * @param string $filter
	 * @param bool $onlyCant
	 * @param string $ref
	 * @return array
	 */
	function list_pages($offset = 0, $maxRecords = -1, $sort_mode = 'pageName_desc', $find = '', $initial = '', $exact_match = true, $onlyName=false, $forListPages=false, $only_orphan_pages = false, $filter='', $onlyCant=false, $ref='')
	{
		global $prefs, $tiki_p_wiki_view_ratings;

		$loadCategories = (isset($prefs['wiki_list_categories']) && $prefs['wiki_list_categories'] == 'y') || (isset($prefs['wiki_list_categories_path']) && $prefs['wiki_list_categories_path'] == 'y');
		$loadCategories = $loadCategories && $forListPages;

		$join_tables = '';
		$join_bindvars = array();
		$old_sort_mode = '';
		if ($sort_mode == 'size_desc') $sort_mode = 'page_size_desc';
		if ($sort_mode == 'size_asc') $sort_mode = 'page_size_asc';
		$select = '';

		// If sort mode is versions, links or backlinks then offset is 0, maxRecords is -1 (again) and sort_mode is nil
		$need_everything = false;
		if ( in_array($sort_mode, array('versions_desc', 'versions_asc', 'links_asc', 'links_desc', 'backlinks_asc', 'backlinks_desc'))) {
			$old_sort_mode = $sort_mode;
			$sort_mode = 'user_desc';
			$need_everything = true;
		}

		if (is_array($find)) { // you can use an array of pages
			$mid = " where LOWER(`pageName`) IN (".implode(',', array_fill(0, count($find), 'LOWER(?)')).")";
			$bindvars = $find;
		} elseif (is_string($find) && !empty($find)) { // or a string
			if (!$exact_match && $find) {
				$find = preg_replace("/([^\s]+)/", "%\\1%", $find);
				$f = preg_split("/[\s]+/", $find, -1, PREG_SPLIT_NO_EMPTY);
				if (empty($f)) {//look for space...
					$mid = " where LOWER(`pageName`) like LOWER('%$find%')";
				} else {
					$findop = $forListPages ? ' AND' : ' OR';
					$mid = " where LOWER(`pageName`) like ".implode($findop . ' LOWER(`pageName`) like ', array_fill(0, count($f), 'LOWER(?)'));
					$bindvars = $f;
				}
			} else {
				$mid = " where LOWER(`pageName`) like LOWER(?) ";
				$bindvars = array($find);
			}
		} else {
			$bindvars = array();
			$mid = '';
		}

		$categlib = TikiLib::lib('categ');
		$category_jails = $categlib->get_jail();

		if ( ! isset( $filter['andCategId'] ) && ! isset( $filter['categId'] ) && empty( $filter['noCateg'] ) && ! empty( $category_jails ) ) {
			$filter['categId'] = $category_jails;
		}

		// If language is set to '', assume that no language filtering should be done.
		if (isset($filter['lang']) && $filter['lang'] == '') {
			unset($filter['lang']);
		}

		$distinct = '';
		if (!empty($filter)) {
			$tmp_mid = array();
			foreach ($filter as $type=>$val) {
				if ($type == 'andCategId') {
					$categories = $categlib->get_jailed((array) $val);
					$join_tables .= " inner join `tiki_objects` as tob on (tob.`itemId`= tp.`pageName` and tob.`type`= ?) ";
					$join_bindvars[] = 'wiki page';
					foreach ($categories as $i=>$categId) {
						$join_tables .= " inner join `tiki_category_objects` as tc$i on (tc$i.`catObjectId`=tob.`objectId` and tc$i.`categId` =?) ";
						$join_bindvars[] = $categId;
					}
				} elseif ($type == 'categId') {
					$categories = $categlib->get_jailed((array) $val);
					$categories[] = -1;

					$cat_count = count($categories);
					$join_tables .= " inner join `tiki_objects` as tob on (tob.`itemId`= tp.`pageName` and tob.`type`= ?) inner join `tiki_category_objects` as tc on (tc.`catObjectId`=tob.`objectId` and tc.`categId` IN(" . implode(', ', array_fill(0, $cat_count, '?')) . ")) ";

					if ( $cat_count > 1 ) {
						$distinct = ' DISTINCT ';
					}

					$join_bindvars = array_merge(array('wiki page'), $categories);
				} elseif ($type == 'noCateg') {
					$join_tables .= ' left join `tiki_objects` as tob on (tob.`itemId`= tp.`pageName` and tob.`type`= ?) left join `tiki_categorized_objects` as tcdo on (tcdo.`catObjectId`=tob.`objectId`) left join `tiki_category_objects` as tco on (tcdo.`catObjectId`=tco.`catObjectId`)';
					$join_bindvars[] = 'wiki page';
					$tmp_mid[] = '(tco.`categId` is null)';
				} elseif ($type == 'notCategId') {
					foreach ($val as $v) {
						$tmp_mid[] = '(tp.`pageName` NOT IN(SELECT itemId FROM tiki_objects INNER JOIN tiki_category_objects ON catObjectId = objectId WHERE type = "wiki page" AND categId = ?))';
						$bindvars[] = $v;
					}
				} elseif ($type == 'lang') {
					$tmp_mid[] = 'tp.`lang`=?';
					$bindvars[] = $val;
				} elseif ($type == 'structHead') {
					$join_tables .= " inner join `tiki_structures` as ts on (ts.`page_id` = tp.`page_id` and ts.`parent_id` = 0) ";
					$select .= ',ts.`page_alias`';
				} elseif ($type == 'langOrphan') {
					$join_tables .= " left join `tiki_translated_objects` tro on (tro.`type` = 'wiki page' AND tro.`objId` = tp.`page_id`) ";
					$tmp_mid[] = "( (tro.`traId` IS NULL AND tp.`lang` != ?) OR tro.`traId` NOT IN(SELECT `traId` FROM `tiki_translated_objects` WHERE `lang` = ?))";
					$bindvars[] = $val;
					$bindvars[] = $val;
				} elseif ($type == 'structure_orphans') {
					$join_tables .= " left join `tiki_structures` as tss on (tss.`page_id` = tp.`page_id`) ";
					$tmp_mid[] = "(tss.`page_ref_id` is null)";
				} elseif ($type == 'translationOrphan') {
					$multilinguallib = TikiLib::lib('multilingual');
					$multilinguallib->sqlTranslationOrphan('wiki page', 'tp', 'page_id', $val, $join_tables, $midto, $bindvars);
					$tmp_mid[] = $midto;
				}
			}
			if (!empty($tmp_mid)) {
				$mid .= empty($mid) ? ' where (' : ' and (';
				$mid .= implode(' and ', $tmp_mid) . ')';
			}
		}
		if (!empty($initial)) {
			$mid .= empty($mid) ? ' where (' : ' and (';
			$tmp_mid = '';
			if (is_array($initial)) {
				foreach ($initial as $i) {
					if ( ! empty($tmp_mid) ) $tmp_mid .= ' or ';
					$tmp_mid .= ' `pageName` like ? ';
					$bindvars[] = $i.'%';
				}
			} else {
				$tmp_mid = " `pageName` like ? ";
				$bindvars[] = $initial.'%';
			}
			$mid .= $tmp_mid.')';
		}

		if ( $only_orphan_pages ) {
			$join_tables .= ' left join `tiki_links` as tl on tp.`pageName` = tl.`toPage` left join `tiki_structures` as tsoo on tp.`page_id` = tsoo.`page_id`';
			$mid .= ( $mid == '' ) ? ' where ' : ' and ';
			$mid .= 'tl.`toPage` IS NULL and tsoo.`page_id` IS NULL';
		}

		if ( $prefs['rating_advanced'] == 'y' ) {
			$ratinglib = TikiLib::lib('rating');
			$join_tables .= $ratinglib->convert_rating_sort($sort_mode, 'wiki page', '`page_id`');
		}

			if ($tiki_p_wiki_view_ratings === 'y' && $prefs['feature_polls'] =='y' && $prefs['feature_wiki_ratings'] == 'y' ) {
				$select .= ', (select sum(`tiki_poll_options`.`title`*`tiki_poll_options`.`votes`) as rating from `tiki_objects` as tobt, `tiki_poll_objects` as tpo, `tiki_poll_options` where tobt.`itemId`= tp.`pageName` and tobt.`type`=\'wiki page\' and tobt.`objectId`=tpo.`catObjectId` and `tiki_poll_options`.`pollId`=tpo.`pollId` group by `tiki_poll_options`.`pollId`) as rating';
			}

		if (!empty($join_bindvars)) {
			$bindvars = empty($bindvars)? $join_bindvars : array_merge($join_bindvars, $bindvars);
		}

		$query = "select $distinct"
			.( $onlyCant ? "tp.`pageName`" : "tp.* ".$select )
			." from `tiki_pages` as tp $join_tables $mid order by ".$this->convertSortMode($sort_mode);
		$countquery = "select count($distinct tp.`pageName`) from `tiki_pages` as tp $join_tables $mid";
		$pageCount = $this->getOne($countquery, $bindvars);


		// HOTFIX (svn Rev. 22969 or near there)
		// Chunk loading. Because we cannot know what pages are visible, we load chunks of pages
		// and use Perms::filter to see what remains. Stop, if we have enough.
		$cant = 0;
		$n = -1;
		$ret = array();
		$raw = array();

		$offset_tmp = 0;
		$haveEnough=FALSE;
		$filterPerms = empty($ref)? 'view': array('view', 'wiki_view_ref');
		while (!$haveEnough) {
			$rawTemp = $this->fetchAll($query, $bindvars, $maxRecords, $offset_tmp);
			$offset_tmp+=$maxRecords; // next offset

			if (count($rawTemp) == 0) $haveEnough = TRUE; // end of table

			$rawTemp = Perms::filter(array( 'type' => 'wiki page' ), 'object', $rawTemp, array('object' => 'pageName', 'creator' => 'creator'), $filterPerms);

			$raw = array_merge($raw, $rawTemp);
			if ( (count($raw) >= $offset + $maxRecords) || $maxRecords == -1 ) $haveEnough = TRUE; // now we have enough records
		} // prbably this brace has to include the next foreach??? I am unsure.
		// but if yes, the next lines have to be reviewed.


		$history = $this->table('tiki_history');
		$links = $this->table('tiki_links');

		foreach ( $raw as $res ) {
			if ( $initial ) {
				$valid = false;
				$verified = self::take_away_accent($res['pageName']);
				foreach ( (array) $initial as $candidate ) {
					if ( stripos($verified, $candidate) === 0 ) {
						$valid = true;
						break;
					}
				}

				if ( ! $valid ) {
					continue;
				}
			}
			//WYSIWYCA
			$res['perms'] = $this->get_perm_object($res['pageName'], 'wiki page', $res, false);

			$n++;
			if ( ! $need_everything && $offset != -1 && $n < $offset ) continue;

			if ( ! $onlyCant && ( $need_everything || $maxRecords == -1 || $cant < $maxRecords ) ) {
				if ( $onlyName ) $res = array('pageName' => $res['pageName']);
				else {
					$page = $res['pageName'];
					$res['len'] = $res['page_size'];
					unset($res['page_size']);
					$res['flag'] = $res['flag'] == 'L' ? 'locked' : 'unlocked';
					if ($forListPages && $prefs['wiki_list_versions'] == 'y')
						$res['versions'] = $history->fetchCount(array('pageName' => $page));
					if ($forListPages && $prefs['wiki_list_links'] == 'y')
						$res['links'] = $links->fetchCount(array('fromPage' => $page));
					if ($forListPages && $prefs['wiki_list_backlinks'] == 'y')
						$res['backlinks'] = $links->fetchCount(array('toPage' => $page, 'fromPage' => $links->unlike('objectlink:%')));
					// backlinks do not include links from non-page objects TODO: full feature allowing this with options
				}

				if ($loadCategories) {
					$cats = $categlib->get_object_categories('wiki page', $res['pageName']);
					$res['categpath'] = array();
					$res['categname'] = array();
					foreach ($cats as $cat) {
						$res['categpath'][] = $cp = $categlib->get_category_path_string($cat);
						if ($s = strrchr($cp, ':')) $res['categname'][] = substr($s, 1);
						else $res['categname'][] = $cp;
					}
				}
				$ret[] = $res;
			}
			$cant++;
		}
		if ( ! $need_everything ) $cant += $offset;

		// If sortmode is versions, links or backlinks sort using the ad-hoc function and reduce using old_offset and old_maxRecords
		if ( $need_everything ) {
			switch ( $old_sort_mode ) {
				case 'versions_asc': usort($ret, 'compare_versions');
					break;
				case 'versions_desc': usort($ret, 'r_compare_versions');
					break;
				case 'links_desc': usort($ret, 'compare_links');
					break;
				case 'links_asc': usort($ret, 'r_compare_links');
					break;
				case 'backlinks_desc': usort($ret, 'compare_backlinks');
					break;
				case 'backlinks_asc': usort($ret, 'r_compare_backlinks');
					break;
			}
		}

		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $pageCount; // this is not exact. Workaround.
		return $retval;
	}


	// Function that checks for:
	// - tiki_p_admin
	// - the permission itself
	// - individual permission
	// - category permission
	// if O.K. this function shall replace similar constructs in list_pages and other functions above.
	// $categperm is the category permission that should grant $perm. if none, pass 0
	// If additional perm arguments are specified, the user must have all the perms to pass the test
	/**
	 * @param $usertocheck
	 * @param $object
	 * @param $objtype
	 * @param $perm1
	 * @param null $perm2
	 * @param null $perm3
	 * @return bool
	 */
	function user_has_perm_on_object($usertocheck,$object,$objtype,$perm1,$perm2=null,$perm3=null)
	{
		$accessor = $this->get_user_permission_accessor($usertocheck, $objtype, $object);

		$chk1 = $perm1 != null ? $accessor->$perm1 : true;
		$chk2 = $perm2 != null ? $accessor->$perm2 : true;
		$chk3 = $perm3 != null ? $accessor->$perm3 : true;

		return $chk1 && $chk2 && $chk3;
	}

	function get_user_permission_accessor($usertocheck, $type = null, $object = null)
	{
		global $user;
		if ($type && $object) {
			$context = array( 'type' => $type, 'object' => $object );
			$accessor = Perms::getCombined($context);
		} else {
			$accessor = Perms::get();
		}

		// Do not override perms for current users otherwise security tokens won't work
		if ($usertocheck != $user) {
			$groups = $this->get_user_groups($usertocheck);
			$accessor->setGroups($groups);
		}

		return $accessor;
	}

	/* get all the perm of an object either in a table or global+smarty set
	 * OPTIMISATION: better to test tiki_p_admin outside for global=false
	 * TODO: all the objectTypes
	 * TODO: replace switch with object
	 * global = true set the global perm and smarty var, otherwise return an array of perms
	 */
	/**
	 * @param $objectId
	 * @param $objectType
	 * @param string $info
	 * @param bool $global
	 * @return array|bool
	 */
	function get_perm_object($objectId, $objectType, $info='', $global=true)
	{
		global $user;
		$smarty = TikiLib::lib('smarty');
		$userlib = TikiLib::lib('user');

		$perms = Perms::get(array( 'type' => $objectType, 'object' => $objectId ));
		if (empty($perms->getGroups())) {
			$perms->setGroups($this->get_user_groups($user));
		}
		$permNames = $userlib->get_permission_names_for($this->get_permGroup_from_objectType($objectType));

		$ret = array();
		foreach ( $permNames as $perm ) {
			$ret[$perm] = $perms->$perm ? 'y' : 'n';

			if ( $global ) {
				$smarty->assign($perm, $ret[$perm]);
				$GLOBALS[ $perm ] = $ret[$perm];
			}
		}

		// Skip those 'local' permissions for admin users and when global is not requested.
		if ($global && ! Perms::get()->admin) {
			$ret2 = $this->get_local_perms($user, $objectId, $objectType, $info, true);
			if ($ret2) {
				$ret = $ret2;
			}
		}

		return $ret;
	}

	/**
	 * @param $objectType
	 * @return string
	 */
	function get_permGroup_from_objectType($objectType)
	{

		switch ($objectType) {
			case 'tracker':
				return 'trackers';
			case 'image gallery':
			case 'image':
				return 'image galleries';
			case 'file gallery':
			case 'file':
				return 'file galleries';
			case 'article':
			case 'submission':
				return 'cms';
			case 'forum':
				return 'forums';
			case 'blog':
			case 'blog post':
				return 'blogs';
			case 'wiki page':
			case 'history':
				return 'wiki';
			case 'faq':
				return 'faqs';
			case 'survey':
				return 'surveys';
			case 'newsletter':
				return 'newsletters';
				/* TODO */
			default:
				return $objectType;
		}
	}

	/**
	 * @param $objectType
	 * @return string
	 */
	function get_adminPerm_from_objectType($objectType)
	{

		switch ($objectType) {
			case 'tracker':
				return 'tiki_p_admin_trackers';
			case 'image gallery':
			case 'image':
				return 'tiki_p_admin_galleries';
			case 'file gallery':
			case 'file':
				return 'tiki_p_admin_file_galleries';
			case 'article':
			case 'submission':
				return 'tiki_p_admin_cms';
			case 'forum':
				return 'tiki_p_admin_forum';
			case 'blog':
			case 'blog post':
				return 'tiki_p_blog_admin';
			case 'wiki page':
			case 'history':
				return 'tiki_p_admin_wiki';
			case 'faq':
				return 'tiki_p_admin_faqs';
			case 'survey':
				return 'tiki_p_admin_surveys';
			case 'newsletter':
				return 'tiki_p_admin_newsletters';
				/* TODO */
			default:
				return "tiki_p_admin_$objectType";
		}
	}

	/* deal all the special perm */
	/**
	 * @param $user
	 * @param $objectId
	 * @param $objectType
	 * @param $info
	 * @param $global
	 * @return array|bool
	 */
	function get_local_perms($user, $objectId, $objectType, $info, $global)
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');
		$userlib = TikiLib::lib('user');
		$ret = array();
		switch ($objectType) {
			case 'wiki page': case 'wiki':
				if ( $prefs['wiki_creator_admin'] == 'y' && !empty($user) && !empty($info) && $info['creator'] == $user ) { //can admin his page
					$perms = $userlib->get_permission_names_for($this->get_permGroup_from_objectType($objectType));
					foreach ($perms as $perm) {
						$ret[$perm] = 'y';
						if ($global) {
							$GLOBALS[$perm] = 'y';
							$smarty->assign($perm, 'y');
						}
					}
					return $ret;
				}
				// Enabling userpage is not enough, the prefix must be present, otherwise, permissions will be messed-up on new page creation
				if ($prefs['feature_wiki_userpage'] == 'y' && !empty($prefs['feature_wiki_userpage_prefix']) && !empty($user) && strcasecmp($prefs['feature_wiki_userpage_prefix'], substr($objectId, 0, strlen($prefs['feature_wiki_userpage_prefix']))) == 0) {
					if (strcasecmp($objectId, $prefs['feature_wiki_userpage_prefix'].$user) == 0) { //can edit his page
						if (!$global) {
							$perms = $userlib->get_permission_names_for($this->get_permGroup_from_objectType($objectType));
							foreach ($perms as $perm) {
								if ($perm == 'tiki_p_view' || $perm == 'tiki_p_edit') {
									$ret[$perm] = 'y';
								} else {
									$ret[$perm] = $GLOBALS[$perm];
								}
							}
						} else {
							global $tiki_p_edit, $tiki_p_view;
							$tiki_p_view = 'y';
							$smarty->assign('tiki_p_view', 'y');
							$tiki_p_edit = 'y';
							$smarty->assign('tiki_p_edit', 'y');
						}
					} else {
						if (!$global) {
							$ret['tiki_p_edit'] = 'n';
						} else {
							global $tiki_p_edit;
							$tiki_p_edit = 'n';
							$smarty->assign('tiki_p_edit', 'n');
						}
					}
					if (!$global) {
						$ret['tiki_p_rename'] = 'n';
						$ret['tiki_p_rollback'] = 'n';
						$ret['tiki_p_lock'] = 'n';
						$ret['tiki_p_assign_perm_wiki_page'] = 'n';
					} else {
						global $tiki_p_rename, $tiki_p_rollback, $tiki_p_lock, $tiki_p_assign_perm_wiki_page;
						$tiki_p_rename = $tiki_p_rollback = $tiki_p_lock = $tiki_p_assign_perm_wiki_page = 'n';
						$smarty->assign('tiki_p_rename', 'n');
						$smarty->assign('tiki_p_rollback', 'n');
						$smarty->assign('tiki_p_lock', 'n');
						$smarty->assign('tiki_p_assign_perm_wiki_page', 'n');
					}
				}
				break;
			case 'file gallery':
			case 'file':
				global $tiki_p_userfiles;

				if ($objectType === 'file') {
					$gal_info = TikiLib::lib('filegal')->get_file_gallery_info($info['galleryId']);
					if ($gal_info['user'] === $user) {
						$info['type'] = 'user';			// show my files as mine
					} else {
						$info['type'] = '';
					}
				}
				if ($prefs['feature_use_fgal_for_user_files'] === 'y' &&
						$info['type'] === 'user' && $info['user'] === $user && $tiki_p_userfiles === 'y') {

					foreach (array('tiki_p_download_files',
									'tiki_p_upload_files',
									'tiki_p_view_file_gallery',
									'tiki_p_remove_files',
									'tiki_p_create_file_galleries',
									'tiki_p_edit_gallery_file',
								) as $perm) {

						$GLOBALS[$perm] = 'y';
						$smarty->assign($perm, 'y');
						$ret[$perm] = 'y';
					}
					return $ret;
				}
				break;
			default:
				break;
		}
		return false;
	}

	// Returns a string-indexed array of modified preferences (those with a value other than the default). Keys are preference names. Values are preference values.
	// NOTE: prefslib contains a similar method now called getModifiedPrefsForExport
	/**
	 * @return array
	 */
	function getModifiedPreferences()
	{
		$defaults = get_default_prefs();
		$modified = array();

		$results = $this->table('tiki_preferences')->fetchAll(array('name', 'value'), array());

		foreach ( $results as $result ) {
			$name = $result['name'];
			$value = $result['value'];

			$strDef = "";
			if (isset($defaults[$name]) && is_array($defaults[$name])) {
				$strDef = implode(" ", $defaults[$name]);
			} else {
				$strDef = isset($defaults[$name]) ? $defaults[$name] : "";
			}
			if ( empty($strDef) || ($strDef != (string) $value) )
				$modified[$name] = $value;
		}
		return $modified;
	}

	/**
	 * @param $names
	 * @param bool $exact_match
	 * @param bool $no_return
	 * @return array|bool
	 */
	function get_preferences( $names, $exact_match = false, $no_return = false )
	{
		global $prefs;

		$preferences = array();
		if ( $exact_match ) {
			if ( is_array($names) ) {
				$this->_get_values('tiki_preferences', 'name', $names, $prefs);
				if ( ! $no_return ) foreach ( $names as $name ) $preferences[$name] = $prefs[$name];
			} else {
				$this->get_preference($names);
				if ( ! $no_return ) $preferences = array( $names => $prefs[$names] );
			}
		} else {
			if ( is_array($names) ) {
				//Only handle $filtername as array with exact_matches
				return false;
			} else {
				$tikiPreferences = $this->table('tiki_preferences');
				$preferences = $tikiPreferences->fetchMap('name', 'value', array('name' => $tikiPreferences->like($names)));
			}
		}
		return $preferences;
	}

	/**
	 * @param $name
	 * @param string $default
	 * @param bool $expectArray
	 * @return mixed|string
	 */
	function get_preference($name, $default = '', $expectArray = false )
	{
		global $prefs;

        $value = isset($prefs[$name]) ? $prefs[$name] : $default;

		if ( empty($value) ) {
			if ( $expectArray ) {
				return array();
			} else {
				return $value;
			}
		}

		if ( $expectArray && is_string($value) ) {
			return unserialize($value);
		} else {
			return $value;
		}
	}

	/**
	 * @param $name
	 */
	function delete_preference($name)
	{
		global $user_overrider_prefs, $user_preferences, $user, $prefs;
		$prefslib = TikiLib::lib('prefs');

		$this->table('tiki_preferences')->delete(array('name' => $name));
		$cachelib = TikiLib::lib('cache');
		$cachelib->invalidate('global_preferences');

		$definition = $prefslib->getPreference($name);
		$value = $definition['default'];
		if (isset($prefs)) {
			if ( in_array($name, $user_overrider_prefs) ) {
				$prefs['site_'.$name] = $value;
			} elseif ( isset($user_preferences[$user][$name] ) ) {
				$prefs[$name] = $user_preferences[$user][$name];
			} else {
				$prefs[$name] = $value;
			}
		}
	}

	/**
	 * @param $name
	 * @param $value
	 * @return bool
	 */
	function set_preference($name, $value)
	{
		global $user_overrider_prefs, $user_preferences, $user, $prefs;

		$prefslib = TikiLib::lib('prefs');

		$definition = $prefslib->getPreference($name);

		if ($definition && ! $definition['available']) {
			return false;
		}

		$menulib = TikiLib::lib('menu');
		$menulib->empty_menu_cache();

		$cachelib = TikiLib::lib('cache');
		$cachelib->invalidate('global_preferences');

		$preferences = $this->table('tiki_preferences');
		$preferences->insertOrUpdate(array('value' => is_array($value) ? serialize($value) : $value), array('name' => $name));

		if ( isset($prefs) ) {
			if ( in_array($name, $user_overrider_prefs) ) {
				$prefs['site_'.$name] = $value;
			} elseif ( isset($user_preferences[$user][$name] ) ) {
				$prefs[$name] = $user_preferences[$user][$name];
			} else {
				$prefs[$name] = $value;
			}
		}
		return true;
	}

	/**
	 * @param $table
	 * @param $field_name
	 * @param null $var_names
	 * @param $global_ref
	 * @param string $query_cond
	 * @param null $bindvars
	 * @return bool
	 */
	function _get_values($table, $field_name, $var_names = null, &$global_ref, $query_cond = '', $bindvars = null)
	{
		if ( empty($table) || empty($field_name) ) return false;

		$needed = array();
		$defaults = null;

		if ( is_array($var_names) ) {

			// Detect if var names are specified as keys (then values are considered as var defaults)
			//   by looking at the type of the first key
			$defaults = ! is_integer(key($var_names));

			// Check if we need to get the value from DB by looking in the global $user_preferences array
			// (this is able to handle more than one var at a time)
			//   ... and store the default values as well, just in case we needs them later
			if ( $defaults ) {
				foreach ( $var_names as $var => $default ) {
					if ( ! isset($global_ref[$var]) ) $needed[$var] = $default;
				}
			} else {
				foreach ( $var_names as $var ) {
					if ( ! isset($global_ref[$var]) ) $needed[$var] = null;
				}
			}

		} elseif ( $var_names !== null ) {
			return false;
		}

		$cond_query = '';
		if (empty($query_cond) && empty($needed)) {
			$query_cond = 'TRUE';
		}
		$result = null;
		if ( is_null($bindvars) ) $bindvars = array();
		if ( count($needed) > 0 ) {
			foreach ( $needed as $var => $def ) {
				if ( $cond_query != '' ) {
					$cond_query .= ' or ';
				} elseif ( $query_cond != '' ) {
					$cond_query = ' and ';
				}
				$cond_query .= "`$field_name`=?";
				$bindvars[] = $var;
			}
		}
		$query = "select `$field_name`, `value` from `$table` where $query_cond $cond_query";
		$result = $this->fetchAll($query, $bindvars);

		foreach ( $result as $res ) {
			// store the db value in the global array
			$global_ref[$res[$field_name]] = $res['value'];
			// remove vars that have a value in db from the $needed array to avoid affecting them a default value
			unset($needed[$res[$field_name]]);
		}

		// set defaults values if needed and if there is no value in database and if it's default was not null
		if ( $defaults ) {
			foreach ( $needed as $var => $def ) {
				if ( ! is_null($def) ) $global_ref[$var] = $def;
			}
		}
		return true;
	}

	function clear_cache_user_preferences()
	{
		global $user_preferences;
		unset($user_preferences);
	}

	/**
	 * @param $my_user
	 * @param null $names
	 * @return bool
	 */
	function get_user_preferences($my_user, $names = null)
	{
		global $user_preferences;

		// $my_user must be specified
		if ( ! is_string($my_user) || $my_user == '' ) return false;

		global $user_preferences;
		if (!is_array($user_preferences) || !array_key_exists($my_user, $user_preferences)) {
			$user_preferences[$my_user] = array();
		}
		$global_ref =& $user_preferences[$my_user];
		$return = $this->_get_values('tiki_user_preferences', 'prefName', $names, $global_ref, '`user`=?', array($my_user));

		// Handle special display_timezone values
		if ( isset($user_preferences[$my_user]['display_timezone']) && $user_preferences[$my_user]['display_timezone'] != 'Site' && $user_preferences[$my_user]['display_timezone'] != 'Local'
				&& ! TikiDate::TimezoneIsValidId($user_preferences[$my_user]['display_timezone'])
			 ) {
			unset($user_preferences[$my_user]['display_timezone']);
		}
		return $return;
	}

	// Returns a boolean indicating whether the specified user (anonymous or not, the current user by default) has the specified preference set
	/**
	 * @param $preference
	 * @param bool $username
	 * @return bool
	 */
	function userHasPreference($preference, $username = FALSE)
	{
		global $user, $user_preferences;
		if ($username === FALSE) {
			$username = $user;
		}
		if ($username) {
			if (!isset($user_preferences[$username])) {
				$this->get_user_preferences($username);
			}
			return isset($user_preferences[$username][$preference]);
		} else { // If $username is empty, we must be Anonymous looking up one of our own preferences
			return isset($_SESSION['preferences'][$preference]);
		}
	}

	/**
	 * @param $my_user
	 * @param $name
	 * @param null $default
	 * @return null
	 */
	function get_user_preference($my_user, $name, $default = null)
	{
		global $user_preferences, $user;
		if ($my_user) {
			if ($user != $my_user && !isset($user_preferences[$my_user])) {
				$this->get_user_preferences($my_user);
			}
			if ( isset($user_preferences) && isset($user_preferences[$my_user]) && isset($user_preferences[$my_user][$name]) ) {
				return $user_preferences[$my_user][$name];
			}
		} else { // If $my_user is empty, we must be Anonymous getting one of our own preferences
			if (isset($_SESSION['preferences'][$name])) {
				return $_SESSION['preferences'][$name];
			}
		}
		return $default;
	}

	/**
	 * @param $my_user
	 * @param $name
	 * @param $value
	 * @return bool
	 */
	function set_user_preference($my_user, $name, $value)
	{
		global $user_preferences, $prefs, $user, $user_overrider_prefs;

		if ($my_user) {
			$cachelib = TikiLib::lib('cache');
			$cachelib->invalidate('user_details_'.$my_user);

			if ($name == "realName") {
				// attempt to invalidate userlink cache (does not cover all options - only the default)
				$cachelib->invalidate('userlink.'.$user.'.'.$my_user.'0');
				$cachelib->invalidate('userlink.'.$my_user.'0');
			}

			$userPreferences = $this->table('tiki_user_preferences');
			$userPreferences->delete(array('user' => $my_user, 'prefName' => $name));
			$userPreferences->insert(array('user' => $my_user,	'prefName' => $name,	'value' => $value));

			$user_preferences[$my_user][$name] = $value;

			if ($my_user == $user ) {
				$prefs[$name] = $value;
				if ( $name == 'theme' && $prefs['change_theme'] == 'y' ) {
					$prefs['users_prefs_theme'] = $value;
					if ( $value == '' ) {
						$userPreferences->delete(array('user' => $my_user, 'prefName' => $name));
					}
				} elseif ( $name == 'theme_option' && $prefs['change_theme'] == 'y' ) {
					$prefs['users_prefs_theme-option'] = $value;
					if ( $value == '' ) {
						$userPreferences->delete(array('user' => $my_user, 'prefName' => $name));
					}
				} elseif ( $value == '' ) {
					if ( in_array($name, $user_overrider_prefs) ) {
						$prefs[$name] = $prefs['site_'.$name];
						$userPreferences->delete(array('user' => $my_user, 'prefName' => $name));
					}
				}
			}

		} else { // If $my_user is empty, we must be Anonymous updating one of our own preferences
			if ( $name == 'theme' && $prefs['change_theme'] == 'y' ) {
				$prefs['theme'] = $value;
				$_SESSION['preferences']['theme'] = $value;
				if ( $value == '' ) {
					unset($_SESSION['preferences']['theme']);
					unset($_SESSION['preferences']['theme_option']);
				}
			} elseif ( $name == 'theme_option' && $prefs['change_theme'] == 'y' && !empty($_SESSION['preferences']['theme']) ) {
				$prefs['theme_option'] = $value;
				$_SESSION['preferences']['theme_option'] = $value;
			} elseif ( $value == '' ) {
				if ( in_array($name, $user_overrider_prefs) ) {
					$prefs[$name] = $prefs['site_'.$name];
					unset($_SESSION['preferences'][$name]);
				}
			} else {
				$prefs[$name] = $value;
				$_SESSION['preferences'][$name] = $value;
			}
		}

		return true;
	}

	// similar to set_user_preference, but set all at once.
	/**
	 * @param $my_user
	 * @param $preferences
	 * @return bool
	 */
	function set_user_preferences($my_user, &$preferences)
	{
		global $user_preferences, $prefs, $user;

		$cachelib = TikiLib::lib('cache');
		$cachelib->invalidate('user_details_'.$my_user);

		$userPreferences = $this->table('tiki_user_preferences');
		$userPreferences->deleteMultiple(array('user' => $my_user));

		foreach ($preferences as $prefName => $value) {
			$userPreferences->insert(array('user' => $my_user, 'prefName' => $prefName, 'value' => $value));
		}
		$user_preferences[$my_user] =& $preferences;

		if ( $my_user == $user ) {
			$prefs =array_merge($prefs, $preferences);
			$_SESSION['s_prefs']=array_merge($_SESSION['s_prefs'], $preferences);
		}
		return true;
	}

	// This implements all the functions needed to use Tiki
	/*shared*/
	// Returns whether a page named $pageName exists. Unless $casesensitive is set to true, the check is case-insensitive.
	/**
	 * @param $pageName
	 * @param bool $casesensitive
	 * @return int
	 */
	function page_exists($pageName, $casesensitive = false)
	{
		$page_info = $this->get_page_info($pageName, false);
		return ( $page_info !== false && ( ! $casesensitive || $page_info['pageName'] == $pageName ) ) ? 1 : 0;
	}

	/**
	 * @param $pageName
	 * @return mixed
	 */
	function page_exists_desc( &$pageName )
	{

		$page_info = $this->get_page_info($pageName, false);

		return empty($page_info['description']) ? $pageName : $page_info['description'];
	}

	/**
	 * @param $pageName
	 * @return bool|int
	 */
	function page_exists_modtime($pageName)
	{
		$page_info = $this->get_page_info($pageName, false);
		if ( $page_info === false ) return false;
		return empty($page_info['lastModif']) ? 0 : $page_info['lastModif'];
	}

	/**
	 * @param $pageName
	 * @return bool
	 */
	function add_hit($pageName)
	{
		global $prefs;
		if (StatsLib::is_stats_hit()) {
			$pages = $this->table('tiki_pages');
			$pages->update(array('hits' => $pages->increment(1)), array('pageName' => $pageName));
		}
		return true;
	}

	/** Create a wiki page
		@param array $hash- lock_it,contributions, contributors
	 **/
	function create_page($name, $hits, $data, $lastModif, $comment, $user = 'admin', $ip = '0.0.0.0', $description = '', $lang='', $is_html = false, $hash=null, $wysiwyg=NULL, $wiki_authors_style='', $minor=0, $created='')
	{
		global $prefs, $tracer;

        $tracer->trace('tikilib.create_page', "** invoked");

		if ( ! $is_html ) {
			$data = str_replace('<x>', '', $data);
		}
		$name = trim($name); // to avoid pb with trailing space http://dev.mysql.com/doc/refman/5.1/en/char.html

		if (!$user) $user = 'anonymous';
		if (empty($wysiwyg)) {
			$wysiwyg = $prefs['wysiwyg_default'];
			if ($wysiwyg === 'y') {
				$is_html = $prefs['wysiwyg_htmltowiki'] !== 'y';
			}
		}
		// Collect pages before modifying data
		$pointedPages = $this->get_pages($data, true);
		$this->check_alias($data, $name);
		if (!isset($_SERVER["SERVER_NAME"])) {
			$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
		}

		if ($this->page_exists($name))
        {
            $tracer->trace('tikilib.create_page', "** Page \$name already exists. Exiting...");
            return false;
        }

        $tracer->trace('tikilib.create_page', "** TikiLib::lib...");
        $parserlib = TikiLib::lib('parser');
        $tracer->trace('tikilib.create_page', "** invoking process_save_plugins, \$parserlib=".get_class($parserlib));
		$data = $parserlib->process_save_plugins(
			$data,
			array(
				'type' => 'wiki page',
				'itemId' => $name,
				'user' => $user,
			)
		);

		$html=$is_html?1:0;
		if ($html && $prefs['feature_purifier'] != 'n') {
			$parserlib->isHtmlPurifying = true;
			$parserlib->isEditMode = true;
			$noparsed = array();
			$parserlib->plugins_remove($data, $noparsed);

			require_once('lib/htmlpurifier_tiki/HTMLPurifier.tiki.php');
			$data = HTMLPurifier($data);

			$parserlib->plugins_replace($data, $noparsed, true);
			$parserlib->isHtmlPurifying = false;
			$parserlib->isEditMode = false;
		}

		$insertData = array(
			'pageName' => $name,
			'pageSlug' => TikiLib::lib('slugmanager')->generate($prefs['wiki_url_scheme'] ?: 'urlencode', $name, $prefs['url_only_ascii'] === 'y'),
			'hits' => (int) $hits,
			'data' => $data,
			'description' => $description,
			'lastModif' => (int) $lastModif,
			'comment' => $comment,
			'version' => 1,
			'version_minor' => $minor,
			'user' => $user,
			'ip' => $ip,
			'creator' => $user,
			'page_size' => strlen($data),
			'is_html' => $html,
			'created' => empty($created) ? $this->now : $created,
			'wysiwyg' => $wysiwyg,
			'wiki_authors_style' => $wiki_authors_style,
		);
		if ($lang) {
			$insertData['lang'] = $lang;
		}
		if (!empty($hash['lock_it']) && ($hash['lock_it'] == 'y' || $hash['lock_it'] == 'on')) {
			$insertData['flag'] = 'L';
			$insertData['lockedby'] = $user;
		} elseif (empty($hash['lock_it']) || $hash['lock_it'] == 'n') {
			$insertData['flag'] = '';
			$insertData['lockedby'] = '';
		}
		if ($prefs['wiki_comments_allow_per_page'] != 'n') {
			if (!empty($hash['comments_enabled']) && $hash['comments_enabled'] == 'y') {
				$insertData['comments_enabled'] = 'y';
			} else if (empty($hash['comments_enabled']) || $hash['comments_enabled'] == 'n') {
				$insertData['comments_enabled'] = 'n';
			}
		}
		if (empty($hash['contributions'])) {
			$hash['contributions'] = '';
		}
		if (empty($hash['contributors'])) {
			$hash2 = '';
		} else {
			foreach ($hash['contributors'] as $c) {
				$hash3['contributor'] = $c;
				$hash2[] = $hash3;
			}
		}
		$pages = $this->table('tiki_pages');
		$page_id = $pages->insert($insertData);

		//update status, page storage was updated in tiki 9 to be non html encoded
		$wikilib = TikiLib::lib('wiki');
		$converter = new convertToTiki9();
		$converter->saveObjectStatus($page_id, 'tiki_pages');

		$this->replicate_page_to_history($name);

		$this->clear_links($name);

		// Pages are collected before adding slashes
		foreach ($pointedPages as $pointedPage => $types) {
			$this->replace_link($name, $pointedPage, $types);
		}

		// Update the log
		if (strtolower($name) != 'sandbox') {
			$logslib = TikiLib::lib('logs');
			$logslib->add_action("Created", $name, 'wiki page', 'add='.strlen($data), $user, '', '', '', $hash['contributions'], $hash2);
			//get_strings tra("Created");

			//  Deal with mail notifications.
			include_once('lib/notifications/notificationemaillib.php');

			$foo = parse_url($_SERVER["REQUEST_URI"]);
			$machine = self::httpPrefix(true). dirname($foo["path"]);
			sendWikiEmailNotification('wiki_page_created', $name, $user, $comment, 1, $data, $machine, '', false, $hash['contributions']);
			if ($prefs['feature_contribution'] == 'y') {
				$contributionlib = TikiLib::lib('contribution');
				$contributionlib->assign_contributions($hash['contributions'], $name, 'wiki page', $description, $name, "tiki-index.php?page=".urlencode($name));
			}
		}

		//if there are links to this page, clear cache to avoid linking to edition
		$toInvalidate = $this->table('tiki_links')->fetchColumn('fromPage', array('toPage' => $name));
		foreach ( $toInvalidate as $res ) {
			$this->invalidate_cache($res);
		}

		TikiLib::events()->trigger(
			'tiki.wiki.create',
			array(
				'type' => 'wiki page',
				'object' => $name,
				'namespace' => $wikilib->get_namespace($name),
				'user' => $GLOBALS['user'],
				'page_id' => $page_id,
				'version' => 1,
				'data' => $data,
				'old_data' => '',
			)
		);

		// Update HTML wanted links when wysiwyg is in use - this is not an elegant fix
		// but will do for now until the "use wiki syntax in WYSIWYG" feature is ready
		if ($prefs['feature_wysiwyg'] == 'y' && $prefs['wysiwyg_htmltowiki'] != 'y') {
			$wikilib = TikiLib::lib('wiki');
			$temppage = md5($this->now . $name);
			$wikilib->wiki_rename_page($name, $temppage, false, $user);
			$wikilib->wiki_rename_page($temppage, $name, false, $user);
		}

        $tracer->trace('tikilib.create_page', "** Returning");

		return true;
	}

	/**
	 * @param $pageName
	 * @return bool|mixed
	 */
	private function replicate_page_to_history($pageName)
	{
		if (strtolower($pageName) == 'sandbox') {
			return false;
		}

		$query = "INSERT IGNORE INTO `tiki_history`(`pageName`, `version`, `version_minor`, `lastModif`, `user`, `ip`, `comment`, `data`, `description`,`is_html`)
			SELECT `pageName`, `version`, `version_minor`, `lastModif`, `user`, `ip`, `comment`, `data`, `description`,`is_html`
			FROM tiki_pages
			WHERE pageName = ?
			LIMIT 1";

		$this->query($query, array($pageName));

		$id = $this->lastInsertId();

		//update status, we don't want the page to be decoded later
		$wikilib = TikiLib::lib('wiki');
		$converter = new convertToTiki9();
		$converter->saveObjectStatus($id, 'tiki_history');

		return $id;
	}

	/**
	 * @param $user
	 * @param $max
	 * @param string $who
	 * @return mixed
	 */
	function get_user_pages($user, $max, $who='user')
	{
		return $this->table('tiki_pages')->fetchAll(array('pageName'), array($who => $user), $max);
	}

	/**
	 * @param $user
	 * @param $max
	 * @return array
	 */
	function get_user_galleries($user, $max)
	{
		$query = "select `name` ,`galleryId`  from `tiki_galleries` where `user`=? order by `name` asc";

		$result = $this->fetchAll($query, array($user), $max);
		$ret = array();

		foreach ( $result as $res ) {
			//FIXME Perm::filter ?
			if ($this->user_has_perm_on_object($user, $res['galleryId'], 'image gallery', 'tiki_p_view_image_gallery')) {
				$ret[] = $res;
			}
		}
		return $ret;
	}

	/**
	 * @param $pageName
	 * @return bool
	 */
	function get_page_print_info($pageName)
	{
		$query = "SELECT `pageName`, `data` as `parsed`, `is_html` FROM `tiki_pages` WHERE `pageName`=?";
		$result = $this->query($query, array($pageName));
		if ( ! $result->numRows() ) {
			return false;
		} else {
			$page_info = $result->fetchRow();
			$page_info['parsed'] = $this->parse_data($page_info['parsed'], array('is_html' => $page_info['is_html'], 'print'=>'y', 'page'=>$pageName));
		}
		return $page_info;
	}

	/**
	 * @param $pageName
	 * @param bool $retrieve_datas
	 * @param bool $skipCache
	 * @return bool
	 */
	function get_page_info($pageName, $retrieve_datas = true, $skipCache = false)
	{
        global $prefs;
		$pageNameEncode = urlencode($pageName);
		if ( !$skipCache && isset($this->cache_page_info[$pageNameEncode])
			&& ( ! $retrieve_datas || isset($this->cache_page_info[$pageNameEncode]['data']) )
		) {
			return $this->cache_page_info[$pageNameEncode];
		}

		if ( $retrieve_datas ) {
			$query = "SELECT * FROM `tiki_pages` WHERE `pageName`=?";
		} else {
			$query = "SELECT `page_id`, `pageName`, `hits`, `description`, `lastModif`, `comment`, `version`, `version_minor`, `user`, `ip`, `flag`, `points`, `votes`, `wiki_cache`, `cache_timestamp`, `pageRank`, `creator`, `page_size`, `lang`, `lockedby`, `is_html`, `created`, `wysiwyg`, `wiki_authors_style`, `comments_enabled` FROM `tiki_pages` WHERE `pageName`=?";
		}
		$result = $this->query($query, array($pageName));

		if ( ! $result->numRows() ) {
			return false;
		} else {
			$row = $result->fetchRow();
			$row['baseName'] = TikiLib::lib('wiki')->get_without_namespace($row['pageName']);
			$row['prettyName'] = TikiLib::lib('wiki')->get_readable($row['pageName']);
			$row['namespace'] = TikiLib::lib('wiki')->get_namespace($row['pageName']);
			$row['namespace_parts'] = TikiLib::lib('wiki')->get_namespace_parts($row['pageName']);

			// Be sure to have the correct character case (because DB is caseinsensitive)
			$pageNameEncode = urlencode($row['pageName']);

			// Limit memory usage of the page cache.  No
			// intelligence is attempted here whatsoever.  This was
			// done because a few thousand ((page)) links would blow
			// up memory, even with the limit at 128MiB.
			// Information on 128 pages really should be plenty.
			while ( count($this->cache_page_info) >= 128 ) {
				// Need to delete something; pick at random
				$keys=array_keys($this->cache_page_info);
				$num=rand(0, count($keys));
				if (isset($keys[$num])) {
					unset($this->cache_page_info[$keys[$num]]);
				}
			}
            $row['outputType'] = '';
            if ($prefs['feature_wikilingo'] == 'y') {
                $row['outputType'] = $this->getOne ("SELECT `outputType` FROM `tiki_output` WHERE `entityId` = ? AND `objectType` = ? AND `version` = ?", array($row['pageName'], 'wikiPage', $row['version']));
            }

			$this->cache_page_info[$pageNameEncode] = $row;
			return $this->cache_page_info[$pageNameEncode];
		}
	}

	/**
	 * @param $page_id
	 * @return mixed
	 */
	function get_page_info_from_id($page_id)
	{
		return $this->table('tiki_pages')->fetchFullRow(array('page_id' => $page_id));
	}


	/**
	 * @param $page_id
	 * @return mixed
	 */
	function get_page_name_from_id($page_id)
	{
		return $this->table('tiki_pages')->fetchOne('pageName', array('page_id' => $page_id));
	}

	/**
	 * @param $page
	 * @return mixed
	 */
	function get_page_id_from_name($page)
	{
		return $this->table('tiki_pages')->fetchOne('page_id', array('pageName' => $page));
	}

	/**
	 * @param $str
	 * @param $car
	 * @return int
	 */
	function how_many_at_start($str, $car)
	{
		$cant = 0;
		$i = 0;
		while (($i < strlen($str)) && (isset($str{$i})) && ($str{$i}== $car)) {
			$i++;
			$cant++;
		}
		return $cant;
	}

	/**
	 * @param $name
	 * @param $domain
	 * @param string $sep
	 * @return string
	 */
	function protect_email($name, $domain, $sep = '@')
	{
		TikiLib::lib('header')->add_jq_onready(
			'$(".convert-mailto").removeClass("convert-mailto").each(function () {
				var address = $(this).data("encode-name") + "@" + $(this).data("encode-domain");
				$(this).attr("href", "mailto:" + address).text(address);
			});'
		);
		return "<a class=\"convert-mailto\" href=\"mailto:nospam@example.com\" data-encode-name=\"$name\" data-encode-domain=\"$domain\">$name ".tra("at", "", true)." $domain</a>";
		}

	//Updates a dynamic variable found in some object
	/*Shared*/
	/**
	 * @param $name
	 * @param $value
	 * @param null $lang
	 * @return bool
	 */
	function update_dynamic_variable($name,$value, $lang = null)
	{
		$dynamicVariables = $this->table('tiki_dynamic_variables');
		$dynamicVariables->delete(array('name' => $name, 'lang' => $lang));
		$dynamicVariables->insert(array('name' => $name, 'data' => $value, 'lang' => $lang));
		return true;
	}

	/**
	 * @param $page
	 */
	function clear_links($page)
	{
		$this->table('tiki_links')->deleteMultiple(array('fromPage' => $page));

		$objectRelations = $this->table('tiki_object_relations');
		$objectRelations->deleteMultiple(
			array(
				'source_type' => 'wiki page',
				'source_itemId' => $page,
				'target_type' => 'wiki page',
				'relation' => $objectRelations->like('tiki.link.%'),
			)
		);
	}

	/**
	 * @param $pageFrom
	 * @param $pageTo
	 * @param array $types
	 */
	function replace_link($pageFrom, $pageTo, $types = array())
	{
		global $prefs;
		if ($prefs['namespace_enabled'] == 'y' && $prefs['namespace_force_links'] == 'y'
			&& TikiLib::lib('wiki')->get_namespace($pageFrom)
			&& !TikiLib::lib('wiki')->get_namespace($pageTo) ) {
				$namespace = TikiLib::lib('wiki')->get_namespace($pageFrom);
				$pageTo = $namespace . $prefs['namespace_separator'] . $pageTo;
		}

		$links = $this->table('tiki_links');
		$links->insert(array('fromPage' => $pageFrom, 'toPage' => $pageTo), true);

		$relationlib = TikiLib::lib('relation');
		foreach ( $types as $type ) {
			$relationlib->add_relation("tiki.link.$type", 'wiki page', $pageFrom, 'wiki page', $pageTo);
		}
	}

	/**
	 * @param $page
	 */
	function invalidate_cache($page)
	{
		unset( $this->cache_page_info[urlencode($page)] );
		$this->table('tiki_pages')->update(array('cache_timestamp' => 0), array('pageName' => $page));

		$pageCache = Tiki_PageCache::create()
			->checkMeta('wiki-page-output-meta-timestamp', array('page' => $page ))
			->invalidate();
	}

	/** Update a wiki page
		@param array $hash- lock_it,contributions, contributors
		@param int $saveLastModif - modification time - pass null for now, unless importing a Wiki page
	 **/
	function update_page($pageName, $edit_data, $edit_comment, $edit_user, $edit_ip, $edit_description = null, $edit_minor = 0, $lang='', $is_html=null, $hash=null, $saveLastModif=null, $wysiwyg='', $wiki_authors_style='')
	{
		global $prefs;
		$histlib = TikiLib::lib('hist');

		if (!$edit_user) $edit_user = 'anonymous';

		$this->invalidate_cache($pageName);
		// Collect pages before modifying edit_data (see update of links below)
		$pages = $this->get_pages($edit_data, true);
		$this->check_alias($edit_data, $pageName);
		if (!$this->page_exists($pageName))
			return false;

		// Get this page information
		$info = $this->get_page_info($pageName);

		if ($edit_description === null) {
			$edit_description = $info['description'];
		}

		// Use largest version +1 in history table rather than tiki_page because versions used to be bugged
		// tiki_history is also bugged as not all changes get stored in the history, like minor changes
		// and changes that do not modify the body of the page. Both numbers are wrong, but the largest of
		// them both is right.
		$old_version = max($info["version"], $histlib->get_page_latest_version($pageName));

		$user = $info["user"] ? $info["user"] : 'anonymous';
		$data = $info["data"];
		$willDoHistory = ($prefs['feature_wiki_history_full'] == 'y' || $data != $edit_data || $info['description'] != $edit_description || $info["comment"] != $edit_comment );
		$version = $old_version + ($willDoHistory?1:0);

		if ($is_html === null) {
			$html = $info['is_html'];
		} else {
			$html = $is_html ? 1 : 0;
		}
		if ($wysiwyg == '') {
			$wysiwyg = $info['wysiwyg'];
		}

		if ( $wysiwyg == 'y' && $html != 1 && $prefs['wysiwyg_htmltowiki'] != 'y') {	// correct for html only wysiwyg
			$html = 1;
		}

		$parserlib = TikiLib::lib('parser');
		$edit_data = $parserlib->process_save_plugins(
			$edit_data,
			array(
				'type' => 'wiki page',
				'itemId' => $pageName,
				'user' => $user,
			)
		);

		if ($html == 1 && $prefs['feature_purifier'] != 'n') {
			$parserlib->isHtmlPurifying = true;
			$parserlib->isEditMode = true;
			$noparsed = array();
			$parserlib->plugins_remove($edit_data, $noparsed);

			require_once('lib/htmlpurifier_tiki/HTMLPurifier.tiki.php');
			$edit_data = HTMLPurifier($edit_data);

			$parserlib->plugins_replace($edit_data, $noparsed, true);
			$parserlib->isHtmlPurifying = false;
			$parserlib->isEditMode = false;
		}

		if ( is_null($saveLastModif) ) {
			$saveLastModif = $this->now;
		}

		$queryData = array(
			'description' => $edit_description,
			'data' => $edit_data,
			'comment' => $edit_comment,
			'lastModif' => (int) $saveLastModif,
			'version' => $version,
			'version_minor' => $edit_minor,
			'user' => $edit_user,
			'ip' => $edit_ip,
			'page_size' => strlen($edit_data),
			'is_html' => $html,
			'wysiwyg' => $wysiwyg,
			'wiki_authors_style' => $wiki_authors_style,
			'lang' => $lang,
		);

		if ($hash !== null) {
			if (!empty($hash['lock_it']) && ($hash['lock_it'] == 'y' || $hash['lock_it'] == 'on')) {
				$queryData['flag'] = 'L';
				$queryData['lockedby'] = $user;
			} else if (empty($hash['lock_it']) || $hash['lock_it'] == 'n') {
				$queryData['flag'] = '';
				$queryData['lockedby'] = '';
			}
		}
		if ($prefs['wiki_comments_allow_per_page'] != 'n') {
			if (!empty($hash['comments_enabled']) && $hash['comments_enabled'] == 'y') {
				$queryData['comments_enabled'] = 'y';
			} else if (empty($hash['comments_enabled']) || $hash['comments_enabled'] == 'n') {
				$queryData['comments_enabled'] = 'n';
			}
		}
		if (empty($hash['contributions'])) {
			$hash['contributions'] = '';
		}
		if (empty($hash['contributors'])) {
			$hash2 = '';
		} else {
			foreach ($hash['contributors'] as $c) {
				$hash3['contributor'] = $c;
				$hash2[] = $hash3;
			}
		}

		$this->table('tiki_pages')->update($queryData, array('pageName' => $pageName));

		// Synchronize object comment
		if ($prefs['feature_wiki_description'] == 'y') {
			$query = 'update `tiki_objects` set `description`=? where `itemId`=? and `type`=?';
			$this->query($query, array( $edit_description, $pageName, 'wiki page'));
		}

		//update status, page storage was updated in tiki 9 to be non html encoded
		$wikilib = TikiLib::lib('wiki');
		$converter = new convertToTiki9();
		$converter->saveObjectStatus($this->getOne("SELECT page_id FROM tiki_pages WHERE pageName = ?", array($pageName)), 'tiki_pages');

		// Parse edit_data updating the list of links from this page
		$this->clear_links($pageName);

		// Pages collected above
		foreach ($pages as $page => $types) {
			$this->replace_link($pageName, $page, $types);
		}

		if (strtolower($pageName) != 'sandbox' && !$edit_minor) {
			$maxversions = $prefs['maxVersions'];

			if ($maxversions && ($nb = $histlib->get_nb_history($pageName)) > $maxversions) {
				// Select only versions older than keep_versions days
				$keep = $prefs['keep_versions'];

				$oktodel = $saveLastModif - ($keep * 24 * 3600) + 1;

				$history = $this->table('tiki_history');
				$result = $history->fetchColumn(
					'version',
					array('pageName' => $pageName,'lastModif' => $history->lesserThan($oktodel)),
					$nb - $maxversions, 0, array('lastModif' => 'ASC')
				);
				foreach ( $result as $toRemove ) {
					$histlib->remove_version($pageName, $toRemove);
				}
			}
		}

		// This if no longer checks for minor-ness of the change; sendWikiEmailNotification does that.
		if ( $willDoHistory ) {
			$this->replicate_page_to_history($pageName);
			if (strtolower($pageName) != 'sandbox') {
				if ($prefs['feature_contribution'] == 'y') {// transfer page contributions to the history
					$contributionlib = TikiLib::lib('contribution');
					$history = $this->table('tiki_history');
					$historyId = $history->fetchOne($history->max('historyId'), array('pageName' => $pageName, 'version' => (int) $old_version));
					$contributionlib->change_assigned_contributions($pageName, 'wiki page', $historyId, 'history', '', $pageName.'/'.$old_version, "tiki-pagehistory.php?page=$pageName&preview=$old_version");
				}
			}
			include_once('lib/diff/difflib.php');
			if (strtolower($pageName) != 'sandbox') {
				$logslib = TikiLib::lib('logs');
				$bytes = diff2($data, $edit_data, 'bytes');
				$logslib->add_action('Updated', $pageName, 'wiki page', $bytes, $edit_user, $edit_ip, '', $this->now, $hash['contributions'], $hash2);
				if ($prefs['feature_contribution'] == 'y') {
					$contributionlib = TikiLib::lib('contribution');
					$contributionlib->assign_contributions($hash['contributions'], $pageName, 'wiki page', $edit_description, $pageName, "tiki-index.php?page=".urlencode($pageName));
				}
			}

			if ($prefs['feature_multilingual'] == 'y' && $lang ) {
				// Need to update the translated objects table when an object's language changes.
				$this->table('tiki_translated_objects')->update(array('lang' => $lang), array('type' => 'wiki page', 'objId' => $info['page_id']));
			}

			if ($prefs['wiki_watch_minor'] != 'n' || !$edit_minor) {
				//  Deal with mail notifications.
				include_once('lib/notifications/notificationemaillib.php');
				$histlib = TikiLib::lib('hist');
				$old = $histlib->get_version($pageName, $old_version);
				$foo = parse_url($_SERVER["REQUEST_URI"]);
				$machine = self::httpPrefix(true). dirname($foo["path"]);
				$diff = diff2($old["data"], $edit_data, "unidiff");
				sendWikiEmailNotification('wiki_page_changed', $pageName, $edit_user, $edit_comment, $old_version, $edit_data, $machine, $diff, $edit_minor, $hash['contributions'], 0, 0, $lang);
			}
		}

		$tx = $this->begin();

		TikiLib::events()->trigger(
			'tiki.wiki.update',
			array(
				'type' => 'wiki page',
				'object' => $pageName,
				'namespace' => $wikilib->get_namespace($pageName), 
				'reply_action' => 'comment',
				'user' => $GLOBALS['user'],
				'page_id' => $info['page_id'],
				'version' => $version,
				'data' => $edit_data,
				'old_data' => $info['data'],
			)
		);

		$tx->commit();
	}

	/**
	 * @param $context
	 * @param $data
	 */
	function object_post_save( $context, $data )
	{
		global $prefs;

		if (is_array($data)) {
			if ( isset( $data['content'] ) && $prefs['feature_file_galleries'] == 'y') {
				$filegallib = TikiLib::lib('filegal');
				$filegallib->syncFileBacklinks($data['content'], $context);
			}

			if ( isset( $data['content'] ) ) {
				$this->plugin_post_save_actions($context, $data);
			}
		} else {
			if ( isset( $context['content'] ) && $prefs['feature_file_galleries'] == 'y') {
				$filegallib = TikiLib::lib('filegal');
				$filegallib->syncFileBacklinks($context['content'], $context);
			}

			$this->plugin_post_save_actions($context);
		}
	}

	/**
	 * Foreach plugin used in a object content call its save handler,
	 * if one exist, and send email notifications when it has pending
	 * status, if preference is enabled.
	 *
	 * A plugin save handler is a function defined on the plugin file
	 * with the following format: wikiplugin_$pluginName_save()
	 *
	 * This function is called from object_post_save. Do not call directly.
	 *
	 * @param array $context object type and id
	 * @param array $data
	 * @return void
	 */
	private function plugin_post_save_actions( $context, $data = null )
	{
		global $prefs;
		$parserlib = TikiLib::lib('parser');

		if (is_null($data)) {
			$content = array();
			if (isset($context['values'])) {
				$content = $context['values'];
			}
			if (isset($context['data'])) {
				$content[] = $context['data'];
			}
			$data['content'] = implode(' ', $content);
		}

		$argumentParser = new WikiParser_PluginArgumentParser;

		$matches = WikiParser_PluginMatcher::match($data['content']);

		foreach ( $matches as $match ) {
			$plugin_name = $match->getName();
			$body = $match->getBody();
			$arguments = $argumentParser->parse($match->getArguments());

			$dummy_output = '';
			if ( $parserlib->plugin_enabled($plugin_name, $dummy_output)) {
				$status = $parserlib->plugin_can_execute($plugin_name, $body, $arguments, true);

				// when plugin status is pending, $status equals plugin fingerprint
				if ($prefs['wikipluginprefs_pending_notification'] == 'y' && $status !== true && $status != 'rejected') {
					$this->plugin_pending_notification($plugin_name, $body, $arguments, $context);
				}

				$alias = new WikiPlugin_Negotiator_Wiki_Alias();
				$alias->findImplementation($plugin_name, $body, $arguments);

				$func_name = 'wikiplugin_' . $plugin_name . '_save';

				if ( function_exists($func_name) ) {
					$func_name( $context, $body, $arguments );
				}
			}
		}
	}

	/**
	 * Send notification by email that a plugin is waiting to be
	 * approved to everyone with permission to approve it.
	 *
	 * @param string $plugin_name
	 * @param string $body plugin body
	 * @param array $arguments plugin arguments
	 * @param array $context object type and id
	 * @return void
	 */
	private function plugin_pending_notification($plugin_name, $body, $arguments, $context)
	{
		require_once('lib/webmail/tikimaillib.php');
		global $prefs, $base_url;
		$mail = new TikiMail(null, $prefs['sender_email']);
		$mail->setSubject(tr("Plugin %0 pending approval", $plugin_name));

		$smarty = TikiLib::lib('smarty');
		$smarty->assign('plugin_name', $plugin_name);
		$smarty->assign('type', $context['type']);
		$smarty->assign('objectId', $context['object']);
		$smarty->assign('arguments', $arguments);
		$smarty->assign('body', $body);

		$mail->setHtml(nl2br($smarty->fetch('mail/plugin_pending_notification.tpl')));

		$recipients = $this->plugin_get_email_users_with_perm();

		$mail->setBcc($recipients);

		if (!empty($prefs['sender_email'])) {
			$mail->send(array($prefs['sender_email']));
		} elseif ($admin_email = TikiLib::lib('user')->get_user_email('admin')) {
			$recipients = array_diff($recipients, array($admin_email));
			$mail->setBcc($recipients);
			$mail->send(array($admin_email));
		}
	}

	/**
	 * Return a list of e-mails from the users with permission
	 * to approve a plugin.
	 *
	 * @return array
	 */
	private function plugin_get_email_users_with_perm()
	{
		$userlib = TikiLib::lib('user');

		$allGroups = $userlib->get_groups();
		$accessor = Perms::get();

		// list of groups with permission to approve plugin on this object
		$groups = array();

		foreach ($allGroups['data'] as $group) {
			$accessor->setGroups(array($group['groupName']));
			if ($accessor->plugin_approve) {
				$groups[] = $group['groupName'];
			}
		}

		$recipients = array();

		foreach ($groups as $group) {
			$recipients = array_merge($recipients, $userlib->get_group_users($group, 0, -1, 'email'));
		}

		$recipients = array_filter($recipients);
		$recipients = array_unique($recipients);

		return $recipients;
	}

	/**
	 * @param bool $_user
	 * @return null|string
	 */
	function get_display_timezone($_user = false)
	{
		global $prefs, $user;

		if ( $_user === false || $_user == $user ) {
			// If the requested timezone is the current user timezone
			$tz = $prefs['display_timezone'];
		} elseif ( $_user ) {
			// ... else, get the user timezone preferences from DB
			$tz = $this->get_user_preference($_user, 'display_timezone');
		}
		if ( ! TikiDate::TimezoneIsValidId($tz) ) {
			$tz = $prefs['server_timezone'];
		}
		if ( ! TikiDate::TimezoneIsValidId($tz) ) {
			$tz = 'UTC';
		}
		return $tz;
	}

	function set_display_timezone($user)
	{
		global $prefs;
		if ($prefs['users_prefs_display_timezone'] == 'Site'
			|| (isset($user_preferences[$user]['display_timezone'])
				&& $user_preferences[$user]['display_timezone'] == 'Site')
		) {
			// Stay in the time zone of the server
			$prefs['display_timezone'] = $prefs['server_timezone'];
		} elseif ( !isset($prefs['display_timezone']) and ! isset($user_preferences[$user]['display_timezone'])
			|| $user_preferences[$user]['display_timezone'] == ''
			|| $user_preferences[$user]['display_timezone'] == 'Local'
		) {
			// If the display timezone is not known ...
			if ( isset($_COOKIE['local_tz'])) {
				//   ... we try to use the timezone detected by javascript and stored in cookies
				if (TikiDate::TimezoneIsValidId($_COOKIE['local_tz'])) {
					$prefs['timezone_offset'] = isset($_COOKIE['local_tzoffset']) ? $_COOKIE['local_tzoffset'] : '';
					if (isset($_COOKIE['local_tzoffset'])) {
						$tzname = timezone_name_from_abbr($_COOKIE['local_tz'], $_COOKIE['local_tzoffset'] * 60 * 60);
						$prefs['timezone_offset'] = $_COOKIE['local_tzoffset'];
					} else {
						$tzname = timezone_name_from_abbr($_COOKIE['local_tz']);
						$prefs['timezone_offset'] = '';
					}
					if (TikiDate::TimezoneIsValidId($tzname)) {
						$prefs['display_timezone'] = $tzname;
					} else {
						$prefs['display_timezone'] = $_COOKIE['local_tz'];
					}
				} elseif ( $_COOKIE['local_tz'] == 'HAEC' ) {
					// HAEC, returned by Safari on Mac, is not recognized as a DST timezone (with daylightsavings)
					//  ... So use one equivalent timezone name
					$prefs['display_timezone'] = 'Europe/Paris';
				} else {
					$prefs['display_timezone'] = $prefs['server_timezone'];
				}
			} else {
				// ... and we fallback to the server timezone if the cookie value is not available
				$prefs['display_timezone'] = $prefs['server_timezone'];
			}
		}
	}

	function get_long_date_format()
	{
		global $prefs;
		return $prefs['long_date_format'];
	}

	function get_short_date_format()
	{
		global $prefs;
		return $prefs['short_date_format'];
	}

	function get_long_time_format()
	{
		global $prefs;
		return $prefs['long_time_format'];
	}

	function get_short_time_format()
	{
		global $prefs;
		return $prefs['short_time_format'];
	}

	/**
	 * @return string
	 */
	function get_long_datetime_format()
	{
		static $long_datetime_format = false;

		if (!$long_datetime_format) {
			$t = trim($this->get_long_time_format());
			if (!empty($t)) {
				$t = ' '.$t;
			}
			$long_datetime_format = $this->get_long_date_format().$t;
		}

		return $long_datetime_format;
	}

	/**
	 * @return string
	 */
	function get_short_datetime_format()
	{
		static $short_datetime_format = false;

		if (!$short_datetime_format) {
			$t = trim($this->get_short_time_format());
			if (!empty($t)) {
				$t = ' '.$t;
			}
			$short_datetime_format = $this->get_short_date_format().$t;
		}

		return $short_datetime_format;
	}

	/**
	 * @param $format
	 * @param bool $timestamp
	 * @param bool $_user
	 * @param int $input_format
	 * @return string
	 */
	static function date_format2($format, $timestamp = false, $_user = false, $input_format = 5/*DATE_FORMAT_UNIXTIME*/)
	{
		return TikiLib::date_format($format, $timestamp, $_user, $input_format, false);
	}

	/**
	 * @param $format
	 * @param bool $timestamp
	 * @param bool $_user
	 * @param int $input_format
	 * @param bool $is_strftime_format
	 * @return string
	 */
	static function date_format($format, $timestamp = false, $_user = false, $input_format = 5/*DATE_FORMAT_UNIXTIME*/, $is_strftime_format = true)
	{
		$tikilib = TikiLib::lib('tiki');
		static $currentUserDateByFormat = array();

		if ( ! $timestamp ) {
			$timestamp = $tikilib->now;
		}

		if ( $_user === false && $is_strftime_format && $timestamp == $tikilib->now && isset( $currentUserDateByFormat[ $format . $timestamp ] ) ) {
			return $currentUserDateByFormat[ $format . $timestamp ];
		}

		$tikidate = TikiLib::lib('tikidate');
		$tikidate->setTZbyID('UTC');
		try {
			$tikidate->setDate($timestamp);
		} catch (Exception $e) {
			return $e->getMessage();
		}

		$tz = $tikilib->get_display_timezone($_user);

		// If user timezone is not also in UTC, convert the date
		if ( $tz != 'UTC' ) {
			$tikidate->setTZbyID($tz);
		}

		$return = $tikidate->format($format, $is_strftime_format);
		if ( $is_strftime_format ) {
			$currentUserDateByFormat[ $format . $timestamp ] = $return;
		}
		return $return;
	}

	/**
	 * @param $hour
	 * @param $minute
	 * @param $second
	 * @param $month
	 * @param $day
	 * @param $year
	 * @return int
	 */
	function make_time($hour,$minute,$second,$month,$day,$year)
	{
		$tikilib = TikiLib::lib('tiki');
		$tikidate = TikiLib::lib('tikidate');
		$display_tz = $tikilib->get_display_timezone();
		if ( $display_tz == '' ) $display_tz = 'UTC';
		$tikidate->setTZbyID($display_tz);
		$tikidate->setLocalTime($day, $month, $year, $hour, $minute, $second, 0);
		return $tikidate->getTime();
	}

	/**
	 * @param $timestamp
	 * @param bool $user
	 * @return string
	 */
	function get_long_date($timestamp, $user = false)
	{
		return $this->date_format($this->get_long_date_format(), $timestamp, $user);
	}

	/**
	 * @param $timestamp
	 * @param bool $user
	 * @return string
	 */
	function get_short_date($timestamp, $user = false)
	{
		return $this->date_format($this->get_short_date_format(), (int) $timestamp, $user);
	}

	/**
	 * @param $timestamp
	 * @param bool $user
	 * @return string
	 */
	function get_long_time($timestamp, $user = false)
	{
		return $this->date_format($this->get_long_time_format(), $timestamp, $user);
	}

	/**
	 * @param $timestamp
	 * @param bool $user
	 * @return string
	 */
	function get_short_time($timestamp, $user = false)
	{
		return $this->date_format($this->get_short_time_format(), $timestamp, $user);
	}

	/**
	 * @param $timestamp
	 * @param bool $user
	 * @return string
	 */
	function get_long_datetime($timestamp, $user = false)
	{
		return $this->date_format($this->get_long_datetime_format(), $timestamp, $user);
	}

	/**
	 * @param $timestamp
	 * @param bool $user
	 * @return string
	 */
	function get_short_datetime($timestamp, $user = false)
	{
		return $this->date_format($this->get_short_datetime_format(), $timestamp, $user);
	}

	/**
		Per http://www.w3.org/TR/NOTE-datetime
	 */
	function get_iso8601_datetime($timestamp, $user = false)
	{
		return $this->date_format('%Y-%m-%dT%H:%M:%S%O', $timestamp, $user);
	}

	/**
	 * @param $timestamp
	 * @param bool $user
	 * @return string
	 */
	function get_compact_iso8601_datetime($timestamp, $user = false)
	{
		// no dashes and no tz info - latter should be fixed
		return $this->date_format('%Y%m%dT%H%M%S', $timestamp, $user);
	}

	/**
	 * @return  array of css files in the style dir
	 */
	function list_styles()
	{
		global $tikidomain;
		$csslib = TikiLib::lib('css');

		$sty = array();
		$style_base_path = $this->get_style_path();	// knows about $tikidomain

		if ($style_base_path) {
			$sty = $csslib->list_css($style_base_path);
		}

		if ($tikidomain) {
			$sty = array_unique(array_merge($sty, $csslib->list_css('styles')));
		}
		foreach ($sty as &$s) {
			if (in_array($s, array('mobile', '960_gs'))) {
				$s = '';
			} elseif (substr($s, -4) == '-rtl' || substr($s, -6) == '-print') {
				$s = '';
			} else {
				$s .= '.css';	// add the .css back onto the end of the style names
			}
		}
		$sty = array_filter($sty);
		sort($sty);
		return $sty;

		/* What is this $tikidomain section?
		 * Some files that call this method used to list styles without considering
		 * $tikidomain, now they do. They're listed below:
		 *
		 *  tiki-theme_control.php
		 *  tiki-theme_control_objects.php
		 *  tiki-theme_control_sections.php
		 *  tiki-my_tiki.php
		 *  modules/mod-switch_theme.php
		 *
		 *  lfagundes
		 *
		 *  Tiki 3.0 - now handled by get_style_path()
		 *  jonnybradley
		 */

	}

	/**
	 * @param $a_style - main style (e.g. "thenews.css")
	 * @return array of css files in the style options dir
	 */
	function list_style_options($a_style='')
	{
		global $prefs;
		$csslib = TikiLib::lib('css');

		if (empty($a_style)) {
			$a_style = $prefs['style'];
		}

		$sty = array();
		$option_base_path = $this->get_style_path($a_style).'options/';

		if (is_dir($option_base_path)) {
			$sty = $csslib->list_css($option_base_path);
		}

		if (count($sty)) {
			foreach ($sty as &$s) {	// add .css back as above
				$s .= '.css';
			}
			sort($sty);
			return $sty;
		} else {
			return false;
		}
	}

	/**
	 * @param $stl - main style (e.g. "thenews.css")
	 * @return string - style passed in up to - | or . char (e.g. "thenews")
	 */
	function get_style_base($stl)
	{
		$parts = preg_split('/[\-\.]/', $stl);
		if (count($parts) > 0) {
			return $parts[0];
		} else {
			return '';
		}
	}

	/**
	 * @param $stl - main style (e.g. "thenews.css" - can be empty to return main styles dir)
	 * @param $opt - optional option file name (e.g. "purple.css")
	 * @param $filename - optional filename to look for (e.g. "purple.png")
	 * @return path to dir or file if found or empty if not - e.g. "styles/mydomain.tld/thenews/options/purple/"
	 */
	function get_style_path($stl = '', $opt = '', $filename = '')
	{
		global $tikidomain;

		$path = '';
		$dbase = '';
		if ($tikidomain && is_dir("styles/$tikidomain")) {
			$dbase = $tikidomain.'/';
		}

		$sbase = '';
		if (!empty($stl)) {
			$sbase = $this->get_style_base($stl).'/';
		}

		$obase = '';
		if (!empty($opt)) {
			$obase = 'options/';
			if ($opt != $filename) {	// exception for getting option.css as it doesn't live in it's own dir
				$obase .= substr($opt, 0, strlen($opt) - 4).'/';
			}
		}

		if (empty($filename)) {
			if (is_dir('styles/'.$dbase.$sbase.$obase)) {
				$path = 'styles/'.$dbase.$sbase.$obase;
			} else if (is_dir('styles/'.$dbase.$sbase)) {
				$path = 'styles/'.$dbase.$sbase;	// try "parent" style dir if no option one
			} else if (is_dir('styles/'.$sbase.$obase)) {
				$path = 'styles/'.$sbase.$obase;	// try root style dir if no domain one
			} else {
				$path = 'styles/'.$sbase;			// fall back to "parent" style dir if no option one
			}
		} else {
			if (is_file('styles/'.$dbase.$sbase.$obase.$filename)) {
				$path = 'styles/'.$dbase.$sbase.$obase.$filename;
			} else if (is_file('styles/'.$dbase.$sbase.$filename)) {	// try "parent" style dir if no option one
				$path = 'styles/'.$dbase.$sbase.$filename;
			} else if (is_file('styles/'.$sbase.$obase.$filename)) {	// try non-tikidomain dirs if not found
				$path = 'styles/'.$sbase.$obase.$filename;
			} else if (is_file('styles/'.$sbase.$filename)) {
				$path = 'styles/'.$sbase.$filename;				// fall back to "parent" style dir if no option
			} else if (is_file('styles/'.$dbase.$filename)) {
				$path = 'styles/'.$dbase.$filename;				// tikidomain root style dir?
			} else if (is_file('styles/'.$dbase.$filename)) {
				$path = 'styles/'.$filename;					// root style dir?
			}
		}

		return $path;
	}

	/**
	 * @param bool $user
	 * @return null
	 */
	function get_language($user = false)
	{
		global $prefs;
		static $language = false;

		if (!$language) {
			if ($user) {
				$language = $this->get_user_preference($user, 'language', 'default');
				if (!$language || $language == 'default') {
					$language = $prefs['language'];
				}
			} else {
				$language = $prefs['language'];
			}
		}
		return $language;
	}

	/**
	 * @param $text
	 * @return string
	 */
	function read_raw($text)
	{
		$file = explode("\n", $text);
		$back = '';
		foreach ($file as $line) {
			$r = $s = '';
			if (substr($line, 0, 1) != "#") {
				if ( preg_match("/^\[([A-Z0-9]+)\]/", $line, $r) ) {
					$var = strtolower($r[1]);
				}
				if (isset($var) and (preg_match("/^([-_\/ a-zA-Z0-9]+)[ \t]+[:=][ \t]+(.*)/", $line, $s))) {
					$back[$var][trim($s[1])] = trim($s[2]);
				}
			}
		}
		return $back;
	}


	/**
	 * Get URL Scheme (http / https)
	 * Considers the use of a reverse proxy / ssl offloader. I.e If request is https -> ssl offloader -> http tiki, then it will correctly return https
	 * @return string http | https
	 */
	static function httpScheme()
	{
		global $url_scheme;
		return $url_scheme;
	}

	/**
	 * @param bool $isUserSpecific
	 * @return string
	 */
	static function httpPrefix( $isUserSpecific = false )
	{
		global $url_scheme, $url_host, $url_port, $prefs;

		if ( $isUserSpecific && $prefs['https_login'] != 'disabled' && $prefs['https_external_links_for_users'] == 'y' ) {
			$scheme = 'https';
		} else {
			$scheme = $url_scheme;
		}

		return $scheme.'://'.$url_host.(($url_port!='')?":$url_port":'');
	}

	/**
	 * Includes the full tiki path in the links for external link generation.
	 * @param string $relative
	 * @param array $args
	 * @return string
	 */
	static function tikiUrl( $relative = "", $args = array() )
	{
		global $tikiroot;

		if (preg_match('/^http(s?):/', $relative)) {
			$base = $relative;
		} else {
			$base = self::httpPrefix() . $tikiroot . $relative;
		}

		if ( count($args) ) {
			$base .= '?';
			$base .= http_build_query($args, '', '&');
		}

		return $base;
	}

	/**
	 * Include the full tiki path if requested in an external context.
	 * Otherwise, leave as-is.
	 *
	 * @param string $relative
	 * @param array $args
	 * @return string
	 */
	static function tikiUrlOpt($relative)
	{
		if (self::$isExternalContext) {
			return self::tikiUrl($relative);
		} else {
			return $relative;
		}
	}

	static function setExternalContext($isExternal)
	{
		self::$isExternalContext = (bool) $isExternal;
	}

	static function contextualizeKey($key, $param1 = null, $param2 = null)
	{
		global $prefs;

		$args = func_get_args();
		array_shift($args);

		foreach ($args as $arg) {
			if ($arg == 'language') {
				$language = isset($prefs['language']) ? $prefs['language'] : 'en';
				$key .= "_{$language}";
			} elseif ($arg == 'external') {
				$key .= (int) self::$isExternalContext;
			}
		}

		return $key;
	}

	/**
	 * Removes the protocol, host and path from a URL if they match
	 *
	 * @param string $url		URL to be converted
	 * @return string			relative URL if possible
	 */
	static function makeAbsoluteLinkRelative($url) {
		global $base_url;

		if (strpos($url, $base_url) !== false) {
			$out = substr($url, strlen($base_url));
		} else {
			$out = $url;
		}
		return $out;
	}

	/**
	 * @param $lat1
	 * @param $lon1
	 * @param $lat2
	 * @param $lon2
	 * @return int
	 */
	function distance($lat1,$lon1,$lat2,$lon2)
	{
		// This function uses a pure spherical model
		// it could be improved to use the WGS84 Datum
		// Franck Martin
		$lat1rad=deg2rad($lat1);
		$lon1rad=deg2rad($lon1);
		$lat2rad=deg2rad($lat2);
		$lon2rad=deg2rad($lon2);
		$distance=6367*acos(sin($lat1rad)*sin($lat2rad)+cos($lat1rad)*cos($lat2rad)*cos($lon1rad-$lon2rad));
		return($distance);
	}

	/**
	 * returns a list of usergroups where the user is a member and the group has the right perm
	 * sir-b
	 **/
	function get_groups_to_user_with_permissions($user,$perm)
	{
		$userid = $this->get_user_id($user);
		$query = "SELECT DISTINCT `users_usergroups`.`groupName` AS `groupName`";
		$query.= "FROM  `users_grouppermissions`, `users_usergroups` ";
		$query.= "WHERE `users_usergroups`.`userId` = ? AND ";
		$query.= "`users_grouppermissions`.`groupName` = `users_usergroups`.`groupName` AND ";
		$query.= "`users_grouppermissions`.`permName` = ? ";
		$query.= "ORDER BY `groupName`";
		return $this->fetchAll($query, array((int)$userid, $perm));
	}

	/**
	 * @param $tab
	 * @param $valField1
	 * @param $field1
	 * @param $field2
	 * @return mixed
	 */
	function other_value_in_tab_line($tab, $valField1, $field1, $field2)
	{
		foreach ($tab as $line) {
			if ($line[$field1] == $valField1)
				return $line[$field2];
		}
	}

	/**
	 * @param $file_name
	 * @return string
	 */
	function get_attach_hash_file_name($file_name)
	{
		global $prefs;
		do {
			$fhash = md5($file_name.date('U').rand());
		} while (file_exists($prefs['w_use_dir'].$fhash));
		return $fhash;
	}

	/**
	 * @param $file_name
	 * @param $file_tmp_name
	 * @param $store_type
	 * @return array
	 */
	function attach_file($file_name, $file_tmp_name, $store_type)
	{
		global $prefs;
		$tmp_dest = $prefs['tmpDir'] . "/" . $file_name.".tmp";
		if (!move_uploaded_file($file_tmp_name, $tmp_dest))
			return array("ok"=>false, "error"=>tra('Errors detected'));
		$fp = fopen($tmp_dest, "rb");
		$data = '';
		$fhash = '';
		$chunk = '';
		if ($store_type == 'dir') {
			$fhash = $this->get_attach_hash_file_name($file_name);
			$fw = fopen($prefs['w_use_dir'].$fhash, "wb");
			if (!$fw)
				return array("ok"=>false, "error"=>tra('Cannot write to this file:').$prefs['w_use_dir'].$fhash);
		}
		while (!feof($fp)) {
			$chunk = fread($fp, 8192*16);

			if ($store_type == 'dir') {
				fwrite($fw, $chunk);
			}
			$data .= $chunk;
		}
		fclose($fp);
		unlink($tmp_dest);
		if ($store_type == 'dir') {
			fclose($fw);
			$data = "";
		}
		return array("ok"=>true, "data"=>$data, "fhash"=>$fhash);
	}

	/* to get the length of a data without the quoted part (very
		 approximative)  */
	/**
	 * @param $data
	 * @return int
	 */
	function strlen_quoted($data)
	{
		global $prefs;
		if ($prefs['feature_use_quoteplugin'] != 'y') {
			$data = preg_replace('/^>.*\\n?/m', '', $data);
		} else {
			$data = preg_replace('/{QUOTE\([^\)]*\)}.*{QUOTE}/Ui', '', $data);
		}
		return strlen($data);
	}

	/**
	 * @param $id
	 * @param int $offset
	 * @param $maxRecords
	 * @param string $sort_mode
	 * @param string $find
	 * @param string $table
	 * @param string $column
	 * @param string $from
	 * @param string $to
	 * @return array
	 */
	function list_votes($id, $offset=0, $maxRecords=-1, $sort_mode='user_asc', $find='', $table='', $column='', $from='', $to='')
	{
		$mid = 'where  `id`=?';
		$bindvars[] = $id;
		$select = '';
		$join = '';
		if (!empty($find)) {
			$mid .= ' and (`user` like ? or `title` like ? or `ip` like ?)';
			$bindvars[] = '%'.$find.'%';
			$bindvars[] = '%'.$find.'%';
			$bindvars[] = '%'.$find.'%';
		}
		if (!empty($from) && !empty($to)) {
			$mid .= ' and ((time >= ? and time <= ?) or time = ?)';
			$bindvars[] = $from;
			$bindvars[] = $to;
			$bindvars[] = 0;
		}
		if (!empty($table) && !empty($column)) {
			$select = ", `$table`.`$column` as title";
			$join = "left join `$table` on (`tiki_user_votings`.`optionId` = `$table`.`optionId`)";
		}
		$query = "select * $select from `tiki_user_votings` $join $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_user_votings` $join $mid";
		$ret = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	/**
	  *  Returns explicit message on upload problem
	  *
	  *	@params: $iError: php status of the file uploading (documented in http://uk2.php.net/manual/en/features.file-upload.errors.php )
	  *
	  */
	function uploaded_file_error($iError)
	{
		switch($iError) {
			case UPLOAD_ERR_OK:
				return tra('The file was successfully uploaded.');
			case UPLOAD_ERR_INI_SIZE :
				return tra('The uploaded file exceeds the upload_max_filesize directive in php.ini.');
			case UPLOAD_ERR_FORM_SIZE:
				return tra('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.');
			case UPLOAD_ERR_PARTIAL:
				return tra('The file was only partially uploaded.');
			case UPLOAD_ERR_NO_FILE:
				return tra('No file was uploaded. Was a file selected ?');
			case UPLOAD_ERR_NO_TMP_DIR:
				return tra('A temporary folder is missing.');
			case UPLOAD_ERR_CANT_WRITE:
				return tra('Failed to write file to disk.');
			case UPLOAD_ERR_EXTENSION:
				return tra('File upload stopped by extension.');

			default:
				return tra('Unknown error.');
		}
	}

	// from PHP manual (ini-get function example)
	/**
	 * @param string $val		php.ini key returning memory string i.e. 32M
	 * @return int				size in bytes
	 */
	function return_bytes( $val )
	{
		$val = trim($val);
		$last = strtolower($val{strlen($val)-1});
		switch ( $last ) {
			// The 'G' modifier is available since PHP 5.1.0
			case 'g': $val *= 1024;
			case 'm': $val *= 1024;
			case 'k': $val *= 1024;
		}
		return $val;
	}

	/**
	 * @return int	bytes of memory available for PHP
	 */
	function get_memory_avail()
	{
		return $this->get_memory_limit() - memory_get_usage(true);
	}

	/**
	 * @return int
	 */
	function get_memory_limit()
	{
		return $this->return_bytes(ini_get('memory_limit'));
	}

	/**
	 * @param bool $with_names
	 * @param bool $translate
	 * @param bool $sort_names
	 * @return array|mixed
	 */
	function get_flags($with_names = false, $translate = false, $sort_names = false, $langsort = false)
	{
		global $prefs;

		$cachelib = TikiLib::lib('cache');
		$args = func_get_args();
		$cacheKey = serialize($args) . $prefs['language'];

		if ($data = $cachelib->getSerialized($cacheKey, 'flags')) {
			return $data;
		}

		$flags = array();
		$h = opendir("img/flags/");
		while ($file = readdir($h)) {
			if (strstr($file, ".gif")) {
				$parts = explode('.', $file);
				$flags[] = $parts[0];
			}
		}
		closedir($h);
		if($langsort) {
			foreach($flags as $flagname => $flagtra){
				unset($flags[$flagname]);
				$flags[$flagtra] = tra($flagtra);
			}
		}
		natcasesort($flags);

		if ( $with_names ) {
			$ret = array();
			$names = array();
			foreach ( $flags as $f ) {
				$ret[$f] = strtr($f, '_', ' ');
				if ( $translate ) {
					$ret[$f] = tra($ret[$f]);
				}
				if ($sort_names) {
					$names[$f] = strtolower($this->take_away_accent($ret[$f]));
				}
			}
			if ( $sort_names ) {
				array_multisort($names, $ret);
			}

			$flags = $ret;
		}

		$cachelib->cacheItem($cacheKey, serialize($flags), 'flags');

		return $flags;
	}


	/**
	 * @param {string} $data
	 * @return string
	 */
	function strip_tags($data) {
		$result = preg_replace('/[<]style[^>]*?[>](.|\n|\r)*?[<][\/]style[>]/', '', $data);
		$result = strip_tags($result);
		return $result;
	}
	/**
	 * @param $data
	 * @param string $outputType
	 * @param boolean $is_html
	 * @param string $highlight
	 * @param int $length
	 * @param string $start
	 * @param string $end
	 * @return string
	 */
	function get_snippet($data, $outputType = '', $is_html = false, $highlight='', $length=240, $start='', $end='')
	{
		global $prefs;
		if ($prefs['search_parsed_snippet'] == 'y') {

			$data = preg_replace('/{(:?make)?toc[^}]*}/', '', $data);
			//standard wiki parse
			if ($outputType == '') {
				$_REQUEST['redirectpage'] = 'y'; //do not interpret redirect
				$data = $this->parse_data($data, array('is_html' => $is_html, 'stripplugins' => true, 'parsetoc' => true));
			}

			//wikiLingo parse
			else if (strtolower($outputType) == 'wikilingo' && $prefs['feature_wikilingo'] == 'y') {
				$_REQUEST['redirectpage'] = 'y'; //do not interpret redirect
				if ($this->wikiLingo == null) {
					$this->wikiLingo = new WikiLingo\Parser();
					require_once('lib/wikiLingo_tiki/WikiLingoEvents.php');
					(new WikiLingoEvents($this->wikiLingo, false));
				}
				$data = $this->wikiLingo->parse($data);
			}
		}


		$data = strip_tags($data);
		if ($length > 0) {
			if (function_exists('mb_substr'))
				return mb_substr($data, 0, $length);
			else
				return substr($data, 0, $length);
		}
		if (!empty($start) && ($i = strpos($data, $start))) {
			$data = substr($data, $i+strlen($start));
		}
		if (!empty($end) && ($i = strpos($data, $end))) {
			$data = substr($data, 0, $i);
		}
		return $data;
	}

	/**
	 * @param $string
	 * @param int $quote_style
	 * @param int $translation_table
	 * @return string
	 */
	static function htmldecode($string, $quote_style = ENT_COMPAT, $translation_table = HTML_ENTITIES)
	{
		if ( $translation_table == HTML_ENTITIES ) {
			$string = html_entity_decode($string, $quote_style, 'utf-8');
		} elseif ( $translation_table === HTML_SPECIALCHARS ) {
			$string = htmlspecialchars_decode($string, $quote_style);
		}

		return $string;
	}

	/**
	 * @param $str
	 * @return mixed
	 */
	static function take_away_accent($str)
	{
		$accents = explode(' ', 'À Á Â Ã Ä Å Ç È É Ê Ë Ì Í Î Ï Ð Ñ Ò Ó Ô Õ Ö ø Ø Ù Ú Û Ü Ý ß à á â ã ä å ç è é ê ë ì í î ï ñ ò ó ô õ ö ù ú û ü ý Æ æ');
		$convs =   explode(' ', 'A A A A A A C E E E E I I I I D N O O O O O o O U U U U Y s a a a a a a c e e e e i i i i n o o o o o u u u u y AE ae');
		return str_replace($accents, $convs, $str);
	}

	/**
	 * @param $str
	 * @return mixed
	 */
	static function substituteSeparators($str)
	{
		$subst = explode(' ', '+ \' : ;');
		$convs =   explode(' ', '_ _ _ _');
		$ret = str_replace($subst, $convs, $str);
		$ret = str_replace(' ', '_', $ret);
		return $ret;
	}

	/**
	 * @param $str
	 * @return mixed
	 */
	function urlencode_accent($str)
	{
		$convs = array();
		preg_match_all('/[\x80-\xFF| ]/', $str, $matches);
		$accents = $matches[0];
		foreach ($accents as $a) {
			$convs[] = rawurlencode($a);
		}
		return str_replace($accents, $convs, $str);
	}

	/**
	 * Remove all "non-word" characters and accents from a string
	 * Can be used for DOM elements and preferences etc
	 *
	 * @static
	 * @param string $str
	 * @return string cleaned
	 */

	static function remove_non_word_characters_and_accents($str)
	{
		return preg_replace('/\W+/', '_', TikiLib::take_away_accent($str));
	}

	/* return the positions in data where the hdr-nth header is find
	 */
	/**
	 * @param $data
	 * @param $hdr
	 * @return array
	 */
	function get_wiki_section($data, $hdr)
	{
		$start = 0;
		$end = strlen($data);
		$lines = explode("\n", $data);
		$header = 0;
		$pp_level = 0;
		$np_level = 0;
		for ($i = 0, $count_lines = count($lines); $i < $count_lines; ++$i) {
			$pp_level += preg_match('/~pp~/', $lines[$i]);
			$pp_level -= preg_match('/~\/pp~/', $lines[$i]);
			$np_level += preg_match('/~np~/', $lines[$i]);
			$np_level -= preg_match('/~\/np~/', $lines[$i]);
			// We test if we are inside nonparsed or pre section to ignore !*
			if ($pp_level%2 == 0 and $np_level%2 == 0) {
				if (substr($lines[$i], 0, 1) == '!') {
					++$header;
					if ($header == $hdr) { // we are on it - now find the next header at same or lower level
						$level = $this->how_many_at_start($lines[$i], '!');
						$end = strlen($lines[$i]) + 1;
						for (++$i; $i < $count_lines; ++$i) {
							if (substr($lines[$i], 0, 1) == '!' && $level >= $this->how_many_at_start($lines[$i], '!')) {
								return (array($start, $end));
							}
							$end += strlen($lines[$i]) + 1;
						}
						break;
					}
				}
			}
			$start += strlen($lines[$i]) + 1;
		}
		return (array($start, $end));
	}

	/**
	 * \brief Function to embed a flash object (using JS method by default when JS in user's browser is detected)
	 *
	 * So far it's being called from wikiplugin_flash.php and tiki-edit_banner.php
	 *
	 * @param javascript = y or n to force to generate a version with javascript or not, ='' user prefs
	 */
	function embed_flash($params, $javascript='', $flashvars = false)
	{
		global $prefs;
		$headerlib = TikiLib::lib('header');
		if (! isset($params['movie']) ) {
			return false;
		}
		$defaults = array(
						  'width' => 425,
						  'height' => 350,
						  'quality' => 'high',
						  'version' => '9.0.0',
						  'wmode' => 'transparent',
						  );
		$params = array_merge($defaults, $params);
		if (preg_match('/^(\/|https?:)/', $params['movie'])) {
			$params['allowscriptaccess'] = 'always';
		}

		if ( ((empty($javascript) && $prefs['javascript_enabled'] == 'y') || $javascript == 'y')) {
			$myId = (!empty($params['id'])) ? ($params['id']) : 'wp-flash-' . uniqid();
			$movie = '"'.$params['movie'].'"';
			$div = json_encode($myId);
			$width = (int) $params['width'];
			$height = (int) $params['height'];
			$version = json_encode($params['version']);
			if (!empty($params['altimg'])) {
				$alt = '<img src="' . $params['altimg'] . '" width="' . $width . '" height="' . $height . '" alt=\"\" />';
			} else {
				$alt = ''; // Must be blank otherwise for a split second before Flash loads you can see any text that is set
			}
			unset( $params['movie'], $params['width'], $params['height'], $params['version'], $params['altimg'] );
			$params = json_encode($params);

			if (!$flashvars) {
				$flashvars = '{}';
			} else {
				$flashvars = json_encode($flashvars);
				$flashvars = str_replace('\\/', '/', $flashvars);
			}
			$js = <<<JS
swfobject.embedSWF( $movie, $div, $width, $height, $version, 'lib/swfobject/expressInstall.swf', $flashvars, $params, {} );
JS;
			$headerlib->add_js($js);
			return "<div id=\"$myId\">" . $alt . "</div>";
		} else { // link on the movie will not work with IE6

			$asetup = "<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,29,0\" width=\"{$params['width']}\" height=\"{$params['height']}\">";
			$asetup .= "<param name=\"movie\" value=\"{$params['movie']}\" />";
			$asetup .= "<param name=\"quality\" value=\"{$params['quality']}\" />";
			$asetup .= "<param name=\"wmode\" value=\"transparent\" />";
			if (!empty($params['allowscriptaccess'])) {
				$asetup .= "<param name=\"allowscriptaccess\" value=\"always\" />";
			}
			if (!empty($params['allowFullScreen'])) {
				$asetup .= '<param name="allowFullScreen" value="' . $params['allowFullScreen'] . '"></param>';
			}
			if (!empty($params['altimg'])) {
				$asetup .= '<img src="' . $params['altimg'] . '" width="' . $params['width'] . '" height="' . $params['height'] . '" alt=\"\" />';
			}
			$asetup .= "<embed src=\"{$params['movie']}\" quality=\"{$params['quality']}\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\"" .
				" type=\"application/x-shockwave-flash\" width=\"{$params['width']}\" height=\"{$params['height']}\" wmode=\"transparent\"></embed></object>";
			return $asetup;
		}
	}

	// Tiki version of parse_str, that:
	//  - uses a workaround for a bug in PHP 5.2.0
	//  - Handle the value of magic_quotes_gpc to stripslashes when needed (as already done for GET/POST/... in tiki-setup_base.php)
	/**
	 * @param $str
	 * @param $arr
	 */
	static function parse_str($str, &$arr)
	{
		parse_str($str, $arr);

		/* From PHP Manual comments (quoting Vladimir Kornea):
		 *   parse_str() contained a bug (#39763) in PHP 5.2.0 that caused it to apply magic quotes twice.
		 * This bug was marked as fixed in the release notes of PHP 5.2.1, but there were apparently some
		 * issues with getting the fix through CVS on time, as our install of PHP 5.2.1 was still affected by it.
		 */
		if ( version_compare(PHP_VERSION, '5.2.0', '>=') && version_compare(PHP_VERSION, '5.2.1', '<') ) {
			$arr = array_map('stripslashes', $arr);
		}

		// parse_str's behavior also depends on magic_quotes_gpc...
		global $magic_quotes_gpc;
		if ( $magic_quotes_gpc ) remove_gpc($arr);
	}

	/**
	 * @param bool $descendants The default is to get all descendents of the jailed categories, but for unified search
	 *                          we only need the "root" jailed categories as the search does a deep_categories search on them
	 * @return array
	 */
	function get_jail($descendants = true)
	{
		global $prefs;
		// if jail is zero, we should allow non-categorized objects to be seen as well, i.e. consider as no jail
		if ( $prefs['feature_categories'] == 'y' && ! empty( $prefs['category_jail'] ) && $prefs['category_jail'] != array(0 => 0) ) {
			$expanded = array();
			if ($descendants) {
				$categlib = TikiLib::lib('categ');
				foreach ($prefs['category_jail'] as $categId) {
					$expanded = array_merge($expanded, $categlib->get_category_descendants($categId));
				}
			} else {
				$expanded = $prefs['category_jail'];
			}
			return $expanded;
		} else {
			return array();
		}
	}

	/**
	 * @param $type
	 * @param $old
	 * @param $new
	 */
	protected function rename_object( $type, $old, $new, $user = '' )
	{
		global $prefs;

		// comments
		$this->table('tiki_comments')->updateMultiple(array('object' => $new), array('object' => $old, 'objectType' => $type));

		// Move email notifications
		$oldId = str_replace($type, ' ', '') . $old;
		$newId = str_replace($type, ' ', '') . $new;
		$this->table('tiki_user_watches')->updateMultiple(array('object' => $newId), array('object' => $oldId));
		$this->table('tiki_group_watches')->updateMultiple(array('object' => $newId), array('object' => $oldId));

		// theme_control_objects(objId,name)
		$oldId = md5($type . $old);
		$newId = md5($type . $new);
		$this->table('tiki_theme_control_objects')->updateMultiple(array('objId' => $newId, 'name' => $new), array('objId' => $oldId));

		// polls
		if ($prefs['feature_polls'] == 'y') {
			$query = "update `tiki_polls` tp inner join `tiki_poll_objects` tpo on tp.`pollId` = tpo.`pollId` inner join `tiki_objects` tob on tpo.`catObjectId` = tob.`objectId` set tp.`title`=? where tp.`title`=? and tob.`type` = ?";
			$this->query($query, array( $new, $old, $type ));
		}

		// Move custom permissions
		$oldId = md5($type . TikiLib::strtolower($old));
		$newId = md5($type . TikiLib::strtolower($new));
		$this->table('users_objectpermissions')->updateMultiple(array('objectId' => $newId), array('objectId' => $oldId, 'objectType' => $type));

		// Logs
		if ($prefs['feature_actionlog'] == 'y') {
			$logslib = TikiLib::lib('logs');
			$logslib->add_action('Renamed', $new, 'wiki page', 'old='.$old.'&new='.$new, $user, '', '', '', '', array(array('rename'=>$old)));
			$logslib->rename($type, $old, $new);
		}

		// Attributes
		$this->table('tiki_object_attributes')->updateMultiple(array('itemId' => $new), array('itemId' => $old, 'type' => $type));
		$this->table('tiki_object_relations')->updateMultiple(array('source_itemId' => $new), array('source_itemId' => $old, 'source_type' => $type));
		$this->table('tiki_object_relations')->updateMultiple(array('target_itemId' => $new), array('target_itemId' => $old, 'target_type' => $type));

		$menulib = TikiLib::lib('menu');
		$menulib->rename_wiki_page($old, $new);
	}

	/**
	 * @param $delimiters
	 * @param $string
	 * @return array
	 */
	function multi_explode($delimiters, $string)
	{
		global $prefs;

		if (is_array($delimiters) == false) $delimiters = array($delimiters);

		$delimiter = array_shift($delimiters);
		$temp = explode($delimiter, $string);
				
		$array = array();
		$keep = false;

		$ignore_chars = array_unique(str_split($prefs['namespace_separator']));

		foreach ($temp as $v) {
			$filtered = str_replace($ignore_chars, '', $v);
			if ($filtered == '' && $v != '') {
				if (! $keep) {
					$array[count($array) - 1] .= $delimiter;
				}

				$array[count($array) - 1] .= $v . $delimiter;
				$keep = true;
			} elseif ($keep) {
				$array[count($array) - 1] .= $v;
				$keep = false;
			} else {
				$array[] = $v;
			}
		}

		if ($delimiters != NULL) {
			foreach ($array as $key => $val) {
				 $array[$key] = $this->multi_explode($delimiters, $val);
			}
		}

		return $array;
	}

	/**
	 * @param $delimiters
	 * @param $string
	 * @return string
	 */
	function multi_implode($delimiters, $array)
	{
		$delimiters = (array) $delimiters;
		$delimiter = array_shift($delimiters);

		if (count($delimiters)) {
			$self = $this;
			$array = array_map(
				function ($value) use ($delimiters, $self) {
					return $self->multi_implode($delimiters, $value);
				},
				$array
			);
		}

		return implode($delimiter, $array);
	}

	/**
	 * @param $vals
	 * @param $filter
	 * @return string
	 */
	function array_apply_filter($vals, $filter)
	{
		if (is_array($vals) == true) {
			foreach ($vals as $key => $val) {
				$vals[$key] = $this->array_apply_filter($val, $filter);
			}
			return $vals;
		} else {
			return trim($filter->filter($vals));
		}
	}

	/**
	 * @param $type
	 * @param $object
	 * @param bool $process
	 * @return bool
	 */
	function refresh_index($type, $object, $process = true)
	{
		require_once 'lib/search/refresh-functions.php';
		return refresh_index($type, $object, $process);
	}

	/**
	 * @param $string
	 * @return string
	 */
	public static function strtolower($string)
	{
		if (function_exists('mb_strtolower')) {
			return mb_strtolower($string, 'UTF-8');
		} else {
			return strtolower($string);
		}
	}

	/**
	 * @param $string
	 * @return string
	 */
	public static function strtoupper($string)
	{
		if (function_exists('mb_strtoupper')) {
			return mb_strtoupper($string, 'UTF-8');
		} else {
			return strtoupper($string);
		}
	}

	/**
	 * @param $string
	 * @return string UTF-8
	 */
	public static function urldecode($string)
	{
	   return TikiInit::to_utf8(urldecode($string));
	}

	/**
	 * @param $string
	 * @return string UTF-8
	 */
	public static function rawurldecode($string)
	{
	   return TikiInit::to_utf8(rawurldecode($string));
	}

	/**
	 * Unparse an array of url parts, e.g. the result of parse_url()
	 * Thanks to http://php.net/manual/en/function.parse-url.php#106731
	 *
	 * @param $parsed_url
	 * @return string
	 */
	public static function unparse_url($parsed_url) {
	  $scheme   = isset($parsed_url['scheme'])   ? $parsed_url['scheme'] . '://' : '//';
	  $host     = isset($parsed_url['host'])     ? $parsed_url['host']           : '';
	  $port     = isset($parsed_url['port'])     ? ':' . $parsed_url['port']     : '';
	  $user     = isset($parsed_url['user'])     ? $parsed_url['user']           : '';
	  $pass     = isset($parsed_url['pass'])     ? ':' . $parsed_url['pass']     : '';
	  $pass     = ($user || $pass)               ? "$pass@"                      : '';
	  $path     = isset($parsed_url['path'])     ? $parsed_url['path']           : '';
	  $query    = isset($parsed_url['query'])    ? '?' . $parsed_url['query']    : '';
	  $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';

	  return "$scheme$user$pass$host$port$path$query$fragment";
	}



	/**
	*	Return the request URI.
	*	Assumes http or https is used. Non-standard ports are taken into account
	*	@return Full URL to the current page
  	* \static
	*/
	// Note: this is unused as of r37658, but quite generic.
	static function curPageURL()
	{
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on")) {
			$pageURL .= 's';
		}
		$pageURL .= '://';
		if ($_SERVER['SERVER_PORT'] != '80') {
			$pageURL .= $_SERVER['SERVER_NAME'].":".$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		} else {
			$pageURL .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		}
		return $pageURL;
	}

	/**
	 * @param array $data
	 * @return array
	 */
	public static function array_flat(array $data)
	{
		$out = array();
		foreach ($data as $entry) {
			if (is_array($entry)) {
				$out = array_merge($out, self::array_flat($entry));
			} else {
				$out[] = $entry;
			}
		}
		return $out;
	}

	/**
	 * This checks the modifier array and scans the template directory for templates
	 * that match the modifiers.
	 * Example: if we are looking at modifier "blog" for the articles.tpl, this function
	 * looks for the existence of articles--blog.tpl to use before using the standard articles.tpl
	 *
	 * @param $basetpl
	 * @param $modifier_arr
	 * @return string
	 * @throws Exception
	 */
	public static function custom_template($basetpl,$modifier_arr){
		//if it's an item passed and not an array, put the item in an array
		if (!is_array($modifier_arr)) {
			$modifier_arr = [$modifier_arr];
		}
		//strip the .tpl
		$temp = explode('.', $basetpl);
		$ext  = array_pop($temp);
		$base = implode('.', $temp);

		$smarty = TikiLib::lib('smarty');
		foreach ($modifier_arr as $modifier){
			if ($smarty->templateExists("$base--$modifier.tpl")) {
				return "$base--$modifier.tpl";
			}
		}
		return "$base.tpl";
	}

	/**
	 * @param $theme
	 * @return array
	 */
	public function getSlideshowTheme($theme)
	{
		global $prefs;

		$result = array();
		//this makes it so that any input so long as the characters are the same can be used
		$theme = ($theme == 'default' ? $prefs['feature_jquery_ui_theme'] : $theme);

		$theme = strtolower($theme);
		$theme = str_replace(' ', '', $theme);
		$theme = str_replace('-', '', $theme);

		$result['themeName'] = $theme;
		switch ($theme) {
			case "none":
				$result['backgroundColor'] = 'inherit';
				$result['headerFontColor'] = 'inherit';
				$result['headerBackgroundColor'] = 'inherit';
				$result['slideFontColor'] = 'inherit';
				$result['listItemHighlightColor'] = 'inherit';
				break;
			case "uilightness":
				$result['backgroundColor'] = '#F6A828';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#1C94C4';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#333';
				$result['listItemHighlightColor'] = '#363636';
				break;
			case "uidarkness":
				$result['backgroundColor'] = '#333';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = 'white';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = 'white';
				$result['listItemHighlightColor'] = '#1C94C4';
				break;
			case "smoothness":
				$result['backgroundColor'] = '#E6E6E6';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#212121';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#222';
				$result['listItemHighlightColor'] = '';
				break;
			case "start":
				$result['backgroundColor'] = '#2191c0';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#acdd4a';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = 'white';
				$result['listItemHighlightColor'] = '#77D5F7';
				break;
			case "redmond":
				$result['backgroundColor'] = '#C5DBEC';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#2E6E9E';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#222';
				$result['listItemHighlightColor'] = '#E17009';
				break;
			case "sunny":
				$result['backgroundColor'] = '#FEEEBD';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#0074C7';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#383838';
				$result['listItemHighlightColor'] = '#4C3000';
				break;
			case "overcast":
				$result['backgroundColor'] = '#C9C9C9';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#212121';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#333';
				$result['listItemHighlightColor'] = '#599FCF';
				break;
			case "lefrog":
				$result['backgroundColor'] = '#285C00';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = 'white';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = 'white';
				$result['listItemHighlightColor'] = '#F9DD34';
				break;
			case "flick":
				$result['backgroundColor'] = '#DDD';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#0073EA';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#444';
				$result['listItemHighlightColor'] = '#FF0084';
				break;
			case "peppergrinder":
				$result['backgroundColor'] = '#ECEADF';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#654B24';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#1F1F1F';
				$result['listItemHighlightColor'] = '#B83400';
				break;
			case "eggplant":
				$result['backgroundColor'] = '#3D3644';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = 'white';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = 'white';
				$result['listItemHighlightColor'] = '#FFDB1F';
				break;
			case "darkhive":
				$result['backgroundColor'] = '#444';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#0972A5';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = 'white';
				$result['listItemHighlightColor'] = '#2E7DB2';
				break;
			case "cupertino":
				$result['backgroundColor'] = '#D7EBF9';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#2694E8';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#362B36';
				$result['listItemHighlightColor'] = '#2694E8';
				break;
			case "southstreet":
				$result['backgroundColor'] = '#F5F3E5';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#459E00';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#312E25';
				$result['listItemHighlightColor'] = '#459E00';
				break;
			case "blitzer":
				$result['backgroundColor'] = '#EEE';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#C00';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#333';
				$result['listItemHighlightColor'] = '#004276';
				break;
			case "humanity":
				$result['backgroundColor'] = '#EDE4D4';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#B85700';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#1E1B1D';
				$result['listItemHighlightColor'] = '#592003';
				break;
			case "hotsneaks":
				$result['backgroundColor'] = '#35414F';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#E1E463';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#93C3CD';
				$result['listItemHighlightColor'] = '#DB4865';
				break;
			case "excitebike":
				$result['backgroundColor'] = '#EEE';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#E69700';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#1484E6';
				$result['listItemHighlightColor'] = '#2293F7';
				break;
			case "vader":
				$result['backgroundColor'] = '#121212';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#ADADAD';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#EEE';
				$result['listItemHighlightColor'] = '#ADADAD';
				break;
			case "dotluv":
				$result['backgroundColor'] = '#111';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#0b3e6f';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#D9D9D9';
				$result['listItemHighlightColor'] = '#0b58a2';
				break;
			case "mintchoc":
				$result['backgroundColor'] = '#453326';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#BAEC7E';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#ffffff';
				$result['listItemHighlightColor'] = '#619226';
				break;
			case "blacktie":
				$result['backgroundColor'] = '#333333';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#a3a3a3';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#eeeeee';
				$result['listItemHighlightColor'] = '#ffeb80';
				break;
			case "trontastic":
				$result['backgroundColor'] = '#222222';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#9fda58';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#ffffff';
				$result['listItemHighlightColor'] = '#f1fbe5';
				break;
			case "swankypurse":
				$result['backgroundColor'] = '#261803';
				$result['backgroundImage'] = 'vendor/jquery/jquery-s5/images/bg.png';
				$result['headerFontColor'] = '#eacd86';
				$result['headerBackgroundColor'] = '';
				$result['slideFontColor'] = '#efec9f';
				$result['listItemHighlightColor'] = '#d5ac5d';
				break;
		}

		return $result;
	}

	/**
	 * @param $page
	 * @return mixed
	 */
	public function removePageReference($page)
	{
		$page_id = $this->get_page_id_from_name($page);
		$query = "DELETE FROM `tiki_page_references` WHERE `page_id`=?";
		$result = $this->query($query, array($page_id));
		return $result;
	}

	/**
	 * @param array $new_toolbars
	 * @param string $section
	 * @param string $action
	 */
	public function saveEditorToolbars($new_toolbars = array(), $section='global', $action='add')
	{
		global $prefs;
		$prefName = 'toolbar_' . $section;
		$toolbars = explode(',', $prefs[$prefName]);
		if ($action == 'add') {
			foreach ($new_toolbars as $key => $value) {
				if (!in_array($value, $toolbars)) {
					$toolbars[] = $value;
				}
			}
		} else {//remove the toolbars
			$toolbars = array_diff($toolbars, $new_toolbars);
		}
		$toolbars = implode(',', $toolbars);
		$this->set_preference($prefName, $toolbars);
	}

	/**
	 * @param $haystack
	 * @param $needle
	 * @return bool
	 */
	static function startsWith($haystack, $needle)
	{
		$length = strlen($needle);
		return (substr($haystack, 0, $length) === $needle);
	}

	/**
	 * @param $haystack
	 * @param $needle
	 * @return bool
	 */
	static function endsWith($haystack, $needle)
	{
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}

		$start  = $length * -1; //negative
		return (substr($haystack, $start) === $needle);
	}

	function check_alias($edit, $page) {
		$smarty = TikiLib::lib('smarty');
		foreach ($this->get_pages($edit, true) as $pointedPage => $types) {
			if (isset($types[0]) && $types[0] == 'alias') {
				$alias = $this->table('tiki_object_relations')->fetchColumn('source_itemId', array('target_itemId' => $pointedPage));
				if(($key = array_search($page, $alias)) !== false) { unset($alias[$key]); }
				if(isset($alias) && count($alias) > 0) {
					$aliasmsg = "Can't duplicate alias link, <b>" . $pointedPage . "</b> link already present in <b>" . implode(',', $alias) . "</b> page";
					$smarty->assign('msg', $aliasmsg);
					$smarty->display("error.tpl");
					return false;
				}
			}
		}
	}
}
// end of class ------------------------------------------------------

// function to check if a file or directory is in the path
// returns FALSE if incorrect
// returns the canonicalized absolute pathname otherwise
/**
 * @param $file
 * @param $dir
 * @return bool|string
 */
function inpath($file,$dir)
{
	$realfile=realpath($file);
	$realdir=realpath($dir);
	if (!$realfile) return (FALSE);
	if (!$realdir) return (FALSE);
	if (substr($realfile, 0, strlen($realdir))!= $realdir) {
		return(FALSE);
	} else {
		return($realfile);
	}
}

/**
 * @param $ar1
 * @param $ar2
 * @return mixed
 */
function compare_links($ar1, $ar2)
{
	return $ar1["links"] - $ar2["links"];
}

/**
 * @param $ar1
 * @param $ar2
 * @return mixed
 */
function compare_backlinks($ar1, $ar2)
{
	return $ar1["backlinks"] - $ar2["backlinks"];
}

/**
 * @param $ar1
 * @param $ar2
 * @return mixed
 */
function r_compare_links($ar1, $ar2)
{
	return $ar2["links"] - $ar1["links"];
}

/**
 * @param $ar1
 * @param $ar2
 * @return mixed
 */
function r_compare_backlinks($ar1, $ar2)
{
	return $ar2["backlinks"] - $ar1["backlinks"];
}

/**
 * @param $ar1
 * @param $ar2
 * @return mixed
 */
function compare_images($ar1, $ar2)
{
	return $ar1["images"] - $ar2["images"];
}

/**
 * @param $ar1
 * @param $ar2
 * @return mixed
 */
function r_compare_images($ar1, $ar2)
{
	return $ar2["images"] - $ar1["images"];
}

/**
 * @param $ar1
 * @param $ar2
 * @return mixed
 */
function compare_versions($ar1, $ar2)
{
	return $ar1["versions"] - $ar2["versions"];
}

/**
 * @param $ar1
 * @param $ar2
 * @return mixed
 */
function r_compare_versions($ar1, $ar2)
{
	return $ar2["versions"] - $ar1["versions"];
}

/**
 * @param $ar1
 * @param $ar2
 * @return mixed
 */
function compare_changed($ar1, $ar2)
{
	return $ar1["lastChanged"] - $ar2["lastChanged"];
}

/**
 * @param $ar1
 * @param $ar2
 * @return mixed
 */
function r_compare_changed($ar1, $ar2)
{
	return $ar2["lastChanged"] - $ar1["lastChanged"];
}

/**
 * @param $ar1
 * @param $ar2
 * @return int
 */
function compare_names($ar1, $ar2)
{
	return strcasecmp(tra($ar1["name"]), tra($ar2["name"]));
}

function chkgd2()
{
	return function_exists('imagecreatetruecolor');
}


/**
 * @return string
 */
function detect_browser_language()
{
	global $prefs;
	// Get supported languages
	if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
		$supported = preg_split('/\s*,\s*/', preg_replace('/;q=[0-9.]+/', '', $_SERVER['HTTP_ACCEPT_LANGUAGE']));
	else
		return '';

	// Get available languages
	$available = array();
	$available_aprox = array();

	if (is_dir("lang")) {
		$dh = opendir("lang");
		while ($lang = readdir($dh)) {
			if (!strpos($lang, '.') and is_dir("lang/$lang") and file_exists("lang/$lang/language.php") and ($prefs['restrict_language'] === 'n' || empty($prefs['available_languages']) || in_array($lang, $prefs['available_languages']))) {
				$available[strtolower($lang)] = $lang;
				$available_aprox[substr(strtolower($lang), 0, 2)] = $lang;
			}
		}
	}

	// Check better language
	// Priority has been changed in 2.0 to that defined in RFC 4647
	$aproximate_lang = '';
	foreach ($supported as $supported_lang) {
		$lang = strtolower($supported_lang);
		if (in_array($lang, array_keys($available))) {
			// exact match is always good
			return $available[$lang];
		} elseif (in_array($lang, array_keys($available_aprox))) {
			// otherwise if supported language matches any available dialect, ok also
			return $available_aprox[$lang];
		} elseif ($aproximate_lang == '') {
			// otherwise if supported dialect matches language, store as possible fallback
			$lang = substr($lang, 0, 2);
			if (in_array($lang, array_keys($available_aprox))) {
				$aproximate_lang = $available_aprox[$lang];
			}
		}
	}

	return $aproximate_lang;
}

/**
 * @param $email
 * @return mixed
 */
function validate_email($email)
{
	$validate = new Zend\Validator\EmailAddress(['allow' => Zend\Validator\Hostname::ALLOW_ALL]);
	return $validate->isValid($email);
}

/**
 * @param $val
 * @param $default
 * @return string
 */
function makeBool($val, $default)
{
	// Warning: This function is meant to return a string 'true' or 'false' to be used in JS, not a real boolean value
	if (isset($val) && !empty($val)) {
		$val = ($val == 'y' ? true : false);
	} else {
		$val = $default;
	}
	return ($val ? 'true' : 'false');
}
/* Editor configuration
	 Local Variables:
	 tab-width: 4
	 c-basic-offset: 4
End:
 * vim: fdm=marker tabstop=4 shiftwidth=4 noet:
 */
