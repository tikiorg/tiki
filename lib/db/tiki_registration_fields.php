<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @package Tiki
 * @subpackage db
 */

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 *
 */
class TikiRegistrationFields extends TikiLib
{
	function __construct()
	{
	}

    /**
     * @param bool $user
     * @return array
     */
    function getVisibleFields2($user=false)
	{
        global $tikilib;

		$query = 'SELECT `id`, `field` as `prefName`, `name` as `label`, `type`, `show`, `size` FROM `tiki_registration_fields` WHERE `show`=?';
        $result = $tikilib->query($query, array(1));

        $ret = array();

        while ($res = $result->fetchRow()) {
            if ($user) {
                $res['value'] = $tikilib->get_user_preference($user, $res['prefName'], '');
            }
            $ret[] = $res;
        }
        return $ret;
	}

    /**
     * @return array
     */
    function getHiddenFields()
	{
        global $tikilib;
		$query = 'SELECT `field` FROM `tiki_registration_fields` WHERE `show`=?';
		$result = $tikilib->query($query, array(0));

        $ret = array();

        while ($res = $result->fetchRow()) {
            $ret[] = $res['field'];
        }
        return $ret;
	}
}
