<?php
/**
 * @version $Id: tiki_registration_fields.php,v 1.3 2007-03-02 19:49:11 luciash Exp $
 * @package Tikiwiki
 * @subpackage db
 * @copyright (C) 2005 the Tiki community
 * @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
 */

if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class TikiRegistrationFields extends TikiLib {
    function TikiRegistrationFields() {
    }

    function getVisibleFields2($user=false) {
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

    function getHiddenFields() {
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
