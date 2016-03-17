<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * PerspectiveLib
 *
 */
class PerspectiveLib
{
	private $perspectives;
	private $perspectivePreferences;

    /**
     *
     */
    function __construct()
	{
		$this->perspectives = TikiDb::get()->table('tiki_perspectives');
		$this->perspectivePreferences = TikiDb::get()->table('tiki_perspective_preferences');
	}

    /**
     * @param $prefs
     * @return int
     */
    function get_current_perspective( $prefs )
	{
		if ( isset( $_REQUEST['perspectiveId'] ) ) {
			return (int) $_REQUEST['perspectiveId'];
		} elseif ( isset( $_SESSION['current_perspective'] ) ) {
			return (int) $_SESSION['current_perspective'];
		}

		$tikilib = TikiLib::lib("tiki");
		if (method_exists($tikilib, "get_ip_address")) {
			$ip = $tikilib->get_ip_address();
		}

		foreach ( $this->get_subnet_map($prefs) as $subnet => $perspective ) {
			if ( $this->is_in_subnet($ip, $subnet) ) {
				return $perspective;
			}
		}

		$currentDomain = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
		foreach ( $this->get_domain_map($prefs) as $domain => $perspective ) {
			if ( $domain == $currentDomain ) {
				$_SESSION['current_perspective'] = trim($perspective);
				return $perspective;
			}
		}
	}

    /**
     * @param $prefs
     * @param $active_pref
     * @param $config_pref
     * @return array
     */
    private function get_map( $prefs, $active_pref, $config_pref )
	{
		if ( ! $prefs ) {
			global $prefs;
		}

		$out = array();

		if (( !empty($prefs[$active_pref]) && $prefs[$active_pref] != 'n' ) && isset($prefs[$config_pref])) {
			foreach ( explode("\n", $prefs[$config_pref]) as $config ) {
				if (substr_count($config, ',') == 1) {
					// Ignore lines which don't have exactly one comma, such as empty lines.
					// TODO: make sure there are no such lines in the first place
					list($domain, $perspective) = explode(',', $config);
					$out[$domain] = trim($perspective);
				}
			}
		}

		return $out;
	}

    /**
     * @param null $prefs
     * @return array
     */
    function get_subnet_map( $prefs = null )
	{
		return $this->get_map($prefs, 'site_terminal_active', 'site_terminal_config');
	}

    /**
     * @param null $prefs
     * @return array
     */
    function get_domain_map( $prefs = null )
	{
		return $this->get_map($prefs, 'multidomain_active', 'multidomain_config');
	}

    /**
     * @param $ip
     * @param $subnet
     * @return bool
     */
    private function is_in_subnet( $ip, $subnet )
	{
		list($subnet, $size) = explode('/', $subnet);

		// Warning - bit shifting ahead.

		// Create the real mask from the /X suffix
		$mask = 0xFFFFFFFF ^ ((1 << (int) (32 - $size)) - 1);

		// Make sure the subnet-relevant part matches for the IP and the subnet being compared
		return (ip2long($subnet) & $mask) === (ip2long($ip) & $mask);
	}

	/**
	 * Returns a string-indexed array containing the preferences for the given perspective as "pref_name" => "pref_value".
	 *
	 */
	function get_preferences( $perspectiveId )
	{
		$result = TikiDb::get()->query("SELECT pref, value FROM tiki_perspective_preferences WHERE perspectiveId = ?", array( $perspectiveId ));

		$out = array();

		while ( $row = $result->fetchRow() ) {
			$out[ $row['pref'] ] = unserialize($row['value']);
		}

		return $out;
	}

    /**
     * @param $perspectiveId
     * @return mixed
     */
    function get_perspective( $perspectiveId )
	{
		$result = TikiDb::get()->query("SELECT perspectiveId, name FROM tiki_perspectives WHERE perspectiveId = ?", array( $perspectiveId ));

		if ( $info = $result->fetchRow() ) {
			$perms = Perms::get(array( 'type' => 'perspective', 'object' => $perspectiveId ));
			if ( $perms->perspective_view ) {
				$info['preferences'] = $this->get_preferences($perspectiveId);
				$this->write_permissions($info, $perms);

				return $info;
			}
		}
	}


	/**
	 * Changes the current perspective and redirects if multidomain_switchdomain enabled
	 *
	 * @param int $perspective	perspective id
	 * @param bool $by_area		switched by the "areas" feature according to content, so keeps the same REQUEST_URI
	 */
    function set_perspective($perspective, $by_area = false)
	{
		global $prefs, $url_scheme, $tikiroot;

		if ( $this->get_perspective($perspective) || empty($perspective)) {
			if ($prefs['multidomain_switchdomain'] == 'y') {
				foreach ($this->get_domain_map() as $domain => $persp) {
					if ($persp == $perspective && isset($_SERVER['HTTP_HOST']) && $domain != $_SERVER['HTTP_HOST']) {
						$path = $tikiroot;
						if ($by_area && !empty($_SERVER['REQUEST_URI'])) {
							$path = $_SERVER['REQUEST_URI'];
						}
						$targetUrl = $url_scheme . '://' . $domain . $path;

						if ($prefs['feature_areas'] === 'y') {
							header('HTTP/1.0 301 Found');
						}
						header('Location: ' . $targetUrl);
						exit;
					}
				}
			}
		}
		if (empty($perspective)) {
			unset($_SESSION['current_perspective']);
		} else {
			$_SESSION['current_perspective'] = $perspective;
		}

	}


    /**
     * @param $info
     * @param $perms
     */
    private function write_permissions( & $info, $perms )
	{
		$info['can_edit'] = $perms->perspective_edit;
		$info['can_remove'] = $perms->perspective_admin;
		$info['can_perms'] = $perms->perspective_admin;
	}

	/**
	 * Adds or renames a perspective. If $perspectiveId exists, rename it to $name.
	 * Otherwise, create a new perspective with id $perspectiveId named $name.
	 * Returns true if and only if the operation succeeds.
	 *
	 */
	function replace_perspective( $perspectiveId, $name )
	{
		if ( $perspectiveId ) {
			$this->perspectives->update(
				array('name' => $name,),
				array('perspectiveId' => $perspectiveId,)
			);

			return $perspectiveId;
		} else {
			return $this->perspectives->insert(array('name' => $name,));
		}
	}

	/**
	 * Removes a perspective
	 *
	 */
	function remove_perspective ( $perspectiveId )
	{
		if ( $perspectiveId ) {
			$this->perspectives->delete(array('perspectiveId' => $perspectiveId));
			$this->perspectivePreferences->deleteMultiple(array('perspectiveId' => $perspectiveId));
		}
	}

	/**
	 * Replaces all preferences from $perspectiveId with those in the provided string-indexed
	 *   array (in format "pref_name" => "pref_value").
	 *
	 */
	function replace_preferences( $perspectiveId, $preferences )
	{
		$this->perspectivePreferences->deleteMultiple(array('perspectiveId' => $perspectiveId));

		$prefslib = TikiLib::lib('prefs');
		foreach ( $preferences as $pref => $value ) {
			$value = $prefslib->formatPreference($pref, array($pref => $value));
			$this->set_preference($perspectiveId, $pref, $value);
		}
	}

	/**
	 * Replaces a specific preference
	 *
	 */
	function replace_preference ( $preference, $value, $newValue )
	{
		$this->perspectivePreferences->update(
			array('value' => serialize($newValue),),
			array(
				'pref' => $preference,
				'value' => serialize($value),
			)
		);
	}

	/**
	 * Sets $preference's value for $perspectiveId to $value
	 *
	 */
	function set_preference( $perspectiveId, $preference, $value )
	{
		$this->perspectivePreferences->delete(
			array(
				'perspectiveId' => $perspectiveId,
				'pref' => $preference,
			)
		);

		$this->perspectivePreferences->insert(
			array(
				'perspectiveId' => $perspectiveId,
				'pref' => $preference,
				'value' => serialize($value),
			)
		);
	}

	/**
	 * Returns true if and only if a perspective with the given $perspectiveId exists
	 *
	 */
	function perspective_exists( $perspectiveId )
	{
		$db = TikiDb::get();

		$id = $db->getOne(
			'SELECT perspectiveId FROM tiki_perspectives WHERE perspectiveId = ?',
			array( $perspectiveId )
		);

		return ! empty( $id );
	}

    /**
     * @param int $offset
     * @param $maxRecords
     * @return array
     */
    function list_perspectives( $offset = 0, $maxRecords = -1 )
	{
		$db = TikiDb::get();

		$list = $db->fetchAll("SELECT perspectiveId, name FROM tiki_perspectives", array(), $maxRecords, $offset);

		$list = Perms::simpleFilter('perspective', 'perspectiveId', 'perspective_view', $list);

		foreach ( $list as & $info ) {
			$perms = Perms::get(array( 'type' => 'perspective', 'object' => $info['perspectiveId'] ));
			$this->write_permissions($info, $perms);
		}

		return $list;
	}

	/**
	 * Returns one of the perspectives with the given name
	 *
	 */
	function get_perspective_with_given_name ( $name )
	{
		$db = TikiDb::get();

		return $db->getOne("SELECT perspectiveId FROM tiki_perspectives WHERE name = ?", array ( $name ));
	}
}

