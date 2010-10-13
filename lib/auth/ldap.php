<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/* 
 *  Class that adds LDAP Authentication to Tiki and aids Tiki to get User/Group Information
 *  from a LDAP directory
 */

// class uses Pears Net_LDAP2
require_once ("Net/LDAP2.php");


class TikiLdapLib
{

	// var to hold a esablished connection
	protected $ldaplink = NULL;

	// var for ldap configuration parameters
	protected $options = array(
		'host' => 'localhost',
		'port' => NULL,
		'version' => 3,
		'starttls' => false,
		'ssl'	=> false,
		'basedn' => '',
		'filter' => '(objectClass=*)',
		'scope' => 'sub',
		'bind_type' => 'default',
		'username' => '',
		'password' => '',
		'userdn' => '',
		'useroc' => 'inetOrgPerson',
		'userattr' => 'cn',
		'fullnameattr' => '',
		'emailattr' => 'mail',
		'countryattr' => '',
		'groupdn' => '',
		'groupattr' => 'gid',
		'groupoc' => 'groupOfNames',
		'groupnameattr' => '',
		'groupdescattr' => '',
		//neu
		'groupmemberattr' => '',
		'groupmemberisdn' => true,
		'usergroupattr' => '',
		'groupgroupattr' => '',
		// end neu
		'debug' => false
	);

	protected $logslib = NULL;

	/**
	 * @var array The user attributes
	 */
	protected $user_attributes = null;

	// Constructor
	public function __construct($options)
	{
		// debug setting
		global $logslib;
		if (isset($options['debug']) && ($options['debug']===true || $options['debug']=='y' )&& ($logslib instanceof LogsLib)) {
			$this->options['debug'] = true;
			$this->logslib = &$logslib;
		}
		// Configure the connection

		// host can be a list of hostnames.
		// It is easier to create URIs because if we use ssl, we have to create a URI
		if (isset($options['host']) && !empty($options['host'])) {
			$h = $options['host'];
		} else { // use default
			$h = $this->options['host'];
		}

		$t=preg_split('#[\s,]#',$h);
		if (isset($options['ssl']) && ($options['ssl']=='y' || $options['ssl']===true)) {
			$prefix = 'ldaps://';
			$port = 636;
		} else {
			$prefix = 'ldap://';
			$port = 389;
		}
		if (isset($options['port']) && !empty($options['port'])) {
			$port=intval($options['port']);
		}
		$this->options['port'] = NULL; // its save to set port in URI

		$this->options['host'] = array();
		foreach($t as $h) {
			if (preg_match('#^ldaps?://#',$h)) { // entry is already URI
				$this->options['host'][] = $h;
			} else {
				$this->options['host'][] = $prefix . $h . ':' . $port;
			}
		}

		if (isset($options['version']) && !empty($options['version'])) {
			$this->options['version'] = intval($options['version']);
		}

		if (isset($options['startls']) && !empty($options['startls'])) {
			$this->options['startls'] = ($options['startls']===true || $options['startls']=='y');
		}

		if (isset($options['groupmemberisdn']) && !empty($options['groupmemberisdn'])) {
			$this->options['groupmemberisdn'] = ($options['groupmemberisdn']===true || $options['groupmemberisdn']=='y');
		}

		// only string checking fo these ones
		foreach(array('basedn', 'username', 'password', 'userdn', 'useroc', 'userattr',
				'fullnameattr', 'emailattr', 'groupdn', 'groupattr', 'groupoc', 'groupnameattr',
				'groupdescattr', 'groupmemberattr', 'usergroupattr', 'groupgroupattr') as $n) {
			if (isset($options[$n]) && !empty($options[$n]) && preg_match('#\s#', $options[$n])==0) {
				$this->options[$n] = $options[$n];
			}
		}

		if (empty($this->options['groupgroupattr'])) $this->options['groupgroupattr']=$this->options['usergroupattr'];

		if (isset($options['password'])) $this->options['bindpw'] = $options['password'];

		if (isset($options['scope']) && !empty($options['scope'])) {
			switch($options['scope']) {
				case 'sub':
				case 'one':
				case 'base':
					$this->options['scope'] = $options['scope'];
					break;
				default:
					break;
			}
		}

		if (isset($options['bind_type']) && !empty($options['bind_type'])) {
			switch($options['bind_type']) {
				case 'ad':
				case 'ol':
				case 'full':
				case 'plain':
					$this->options['bind_type'] = $options['bind_type'];
					break;
				default:
					break;
			}
		}
	}
	// End public function TikiLdapLib($options)

	public function __destruct()
	{
		unset($this->ldaplink);
	}

	// Do a ldap bind
	public function bind( $reconnect = false )
	{
		global $prefs;

		// Force the reconnection
		if ($this->ldaplink instanceof Net_LDAP2) {
				if ($reconnect === true) {
						$this->ldaplink->disconnect();
				} else {
						return (true); // do not try to reconnect since this may lead to huge timeouts
				}
		}

		// Set the bindnpw with the options['password']
		$this->options['bindpw'] = $this->options['password'];

		$user = $this->options['username'];
		switch ($this->options['bind_type']) {
			case 'ad': // active directory
				preg_match_all('/\s*,?dc=\s*([^,]+)/i',$this->options['basedn'], $t);
				$this->options['binddn'] = $user.'@';
				if (isset($t[1]) && is_array($t[1])) {
					foreach($t[1] as $domainpart) {
						$this->options['binddn'] .= $domainpart.'.';
					}
					// cut trailing dot
					$this->options['binddn']=substr($this->options['binddn'], 0, -1);
				}
				// set referrals to 0 to avoid LDAP_OPERATIONS_ERROR
				$this->options['options']['LDAP_OPT_REFERRALS']=0;
				break;	
			case 'plain': // plain username
				$this->options['binddn'] = $user;
				break;
			case 'full':
				$this->options['binddn'] = $this->user_dn($user);
				break;
			case 'ol': // openldap
				$this->options['binddn'] = 'cn='.$user.','.$prefs['auth_ldap_basedn'];
				break;
			case 'default':
				// Anonymous binding
				$this->options['binddn'] = '';
				$this->options['bindpw'] = '';
				break;
			default:
				$this->add_log('ldap', 'Error: Invalid "bind_type" value "' . $this->options['bind_type'] . '".');
				die;
		}

		// attributes to fetch
/*
        $options['attributes'] = array();
        if ( $nameattr = $prefs['auth_ldap_nameattr'] ) $options['attributes'][] = $nameattr;
        if ( $countryattr = $prefs['auth_ldap_countryattr'] ) $options['attributes'][] = $countryattr;
        if ( $emailattr = $prefs['auth_ldap_emailattr'] ) $options['attributes'][] = $emailattr;
*/


		$this->add_log('ldap', 'Connect Host: '.implode($this->options['host']).'. Binddn: '.
					$this->options['binddn'].' at line '.__LINE__.' in '.__FILE__);
		//create options array to handle it to Net_LDAP2
		foreach(array('host', 'port', 'version', 'starttls', 'basedn', 'filter', 'scope', 'binddn', 'bindpw', 'options')
				as $o) {
			if (isset($this->options[$o])) {
				$options[$o] = $this->options[$o];
			}
		}

		$this->ldaplink= Net_LDAP2::connect($options);
		if (Net_LDAP2::isError($this->ldaplink)) {
			$this->add_log('ldap', 'Error: ' . $this->ldaplink->getMessage() . ' at line ' . __LINE__ . ' in ' . __FILE__);
			// return Net_LDAP2 Error codes. No need to redefine this.
			return($this->ldaplink->getCode());
		}

		return 'LDAP_SUCCESS';
	} // End bind()



	// return information about user attributes
	public function get_user_attributes()
	{

		if (!empty($this->user_attributes)) { //been there, done that
			return($this->user_attributes);
		}

		$userdn = $this->user_dn();
		// ensure we have a connection to the ldap server
		 if (!$this->bind()) {
			$this->add_log('ldap','Reuse of ldap connection failed: ' . $this->ldaplink->getMessage() . ' at line ' . __LINE__ . ' in ' . __FILE__);
			return false;
		}

		// todo: only fetch needed attributes

		$entry = $this->ldaplink->getEntry($userdn);
		if (Net_LDAP2::isError($entry)) { // wrong userdn. So we have to search
			// prepare Search Filter
			$filter = Net_LDAP2_Filter::create($this->options['userattr'], 'equals', $this->options['username']);
			$searchoptions=array('scope' => $this->options['scope']);
			$this->add_log('ldap', 'Searching for user information with filter: '.$filter->asString().' at line '.__LINE__.' in '.__FILE__);
			$searchresult = $this->ldaplink->search($this->userbase_dn(), $filter, $searchoptions);
			if (Net_LDAP2::isError($searchresult)) {
				$this->add_log('ldap', 'Search failed: ' . $searchresult->getMessage() . ' at line ' . __LINE__ . ' in ' . __FILE__);
				return false;
			}
			if ($searchresult->count() != 1) {
				$this->add_log('ldap', 'Error: Search returned ' . $searchresult->count() . ' entries' . ' at line ' . __LINE__ . ' in ' . __FILE__);
				return false;
			}
			// get first entry
			$entry = $searchresult->shiftEntry();

		}
		$this->user_attributes = $entry->getValues();
		$this->user_attributes['dn'] = $entry->dn();
		if (Net_LDAP2::isError($this->user_attributes)) {
			$this->add_log('ldap', 'Error fetching user attributes: ' . $this->user_attributes->getMessage() . ' at line ' . __LINE__ . ' in ' . __FILE__);
			return false;
		}

		return($this->user_attributes);

	} // End: public function get_user_attributes()




	// return dn of all groups a user belongs to
	public function get_groups()
	{
		if (empty($this->user_attributes))
			$this->get_user_attributes();
		// ensure we have a connection to the ldap server
		if (!$this->bind()) {
			$this->add_log('ldap', 'Reuse of ldap connection failed: ' . $this->ldaplink->getMessage() . ' at line ' . __LINE__ . ' in ' . __FILE__);
			return false;
		}


		$filter1 = Net_LDAP2_Filter::create('objectClass', 'equals', $this->options['groupoc']);

		if (!empty($this->options['groupmemberattr'])) {
			// get membership from group information
			if ($this->options['groupmemberisdn']) {
				$filter2 = Net_LDAP2_Filter::create($this->options['groupmemberattr'], 'equals', $this->user_dn());
			} else {
				$filter2 = Net_LDAP2_Filter::create($this->options['groupmemberattr'], 'equals', $this->options['username']);
			}
			$filter = Net_LDAP2_Filter::combine('and', array($filter1, $filter2));

		} else if (!empty($this->options['usergroupattr'])) {
			// get membership from user information

			$ugi = &$this->user_attributes[$this->options['usergroupattr']];
			if (!empty($ugi)) {

				if (!is_array($ugi)) {
					$ugi = array($ugi);
				}

				if (count($ugi) == 1) { // one gid
					$filter3 = Net_LDAP2_Filter::create($this->options['groupgroupattr'], 'equals', $ugi[0]);
				} else { // mor gids
					$filtertmp = array();
					foreach ($ugi as $g) {
						$filtertmp[] = Net_LDAP2_Filter::create($this->options['groupgroupattr'], 'equals', $g);
					}
					$filter3 = Net_LDAP2_Filter::combine('or', $filtertmp);
				}

				$filter = Net_LDAP2_Filter::combine('and', array($filter1, $filter3));
			} else { // User has no group
				$filter = NULL;
			}
		} else {
			// not possible to get groups - return empty array
			return(array());
		}

		if (Net_LDAP2::isError($filter)) {
			$this->add_log('ldap', 'LDAP Filter creation error: ' . $filter->getMessage() . ' at line ' . __LINE__ . ' in ' . __FILE__);
			return false;
		}

		$this->add_log('ldap', 'Searching for group entries with filter: ' . $filter->asString() . ' base ' . $this->groupbase_dn() . ' at line ' . __LINE__ . ' in ' . __FILE__);
		$searchoptions = array('scope' => $this->options['scope']);
		$searchresult = $this->ldaplink->search($this->groupbase_dn(), $filter, $searchoptions);

		if (Net_LDAP2::isError($searchresult)) {
			$this->add_log('ldap' , 'Search failed: ' . $searchresult->getMessage() . ' at line ' . __LINE__ . ' in ' . __FILE__);
			return false;
		}
		$this->add_log('ldap', 'Found ' . $searchresult->count() . ' entries. Extracting entries now.');

		$this->groups = array();
		while ($entry = $searchresult->shiftEntry()) {
			if (Net_LDAP2::isError($entry)) {
				$this->add_log('ldap', 'Error fetching group entries: ' . $entry->getMessage() . ' at line ' . __LINE__ . ' in ' . __FILE__);
				return false;
			}
			$this->groups[$entry->dn()] = $entry->getValues(); // no error checking necessary here
		}
		$this->add_log('ldap', count($this->groups) . ' groups found at line ' . __LINE__ . ' in ' . __FILE__);

		return($this->groups);

	} // End: private function get_group_dns()




	// helper functions
	private function userbase_dn()
	{
		if (empty($this->options['userdn']))
			return($this->options['basedn']);
		return($this->options['userdn'] . ',' . $this->options['basedn']);
	}

	private function user_dn()
	{
		if (isset($this->user_attributes['dn'])) {
			// we did already fetch user attributes and have the real dn now
			return($this->user_attributes['dn']);
		}
		if (empty($this->options['userattr'])) {
			$ua = 'cn=';
		} else {
			$ua = $this->options['userattr'] . '=';
		}
		return($ua.$this->options['username'] . ',' . $this->userbase_dn());
	}

	private function groupbase_dn()
	{
		if (empty($this->options['groupdn']))
			return($this->options['basedn']);
		return($this->options['groupdn'] . ',' . $this->options['basedn']);
	}

	private function add_log($facility, $message)
	{
		if ($this->options['debug'])
			$this->logslib->add_log($facility, $message);
	}

	/**
	 * Setter to set an otpion value
	 * @param string $name The name of the option
	 * @param mixed $value The value
	 * @return void
	 * @throw Exception
	 */
	public function setOption ($name, $value = null)
	{
		try {
			if (isset($this->options[$name])) {
				$this->options[$name] = $value;
			} else {
				throw new Exception(sprintf("Undefined option: %s \n", $name), E_USER_WARNING);
			}
		} catch (Exception $e) { }
	}

	/**
	 * Return the value of the attribue past in param
	 * @param string $name The name of the attribute
	 * @return mixed
	 * @throw Exception
	 */
	public function getUserAttribute ($name)
	{
		$value = '';
		try {
			$values = self::get_user_attributes();
			if (isset($values[$name])) {
				$value = $values[$name];
			} else {
				throw new Exception(sprintf("Undefined attribute %s \n", $name), E_USER_WARNING);
			}
		} catch (Exception $e) {}
		return $value;
	}
}
