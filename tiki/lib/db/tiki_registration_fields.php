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

class TikiRegistrationFields extends TikiDBTable {
    var $id=null;
    var $name=null;
    var $value=null;
    var $module=null;
    var $meta=null;
    
    function TikiRegistrationFields() {
        global $tikilib;
        $this->TikiDBTable('tiki_registration_fields', 'id', $tikilib);
    }

    function getVisibleFields() {
        $this->setQuery('SELECT `id`, `field` as `prefName`, `name` as `label`, `type`, `show`, `size` FROM '.$this->_tbl.' WHERE `show`=1;');
        return $this->loadObjectList();
    }

    function getVisibleFields2($user=false) {
        global $tikilib;

        $this->setQuery('SELECT `id`, `field` as `prefName`, `name` as `label`, `type`, `show`, `size` FROM '.$this->_tbl.' WHERE `show`="1";');
        $result = $this->query();

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
        $this->setQuery('SELECT `field` FROM '.$this->_tbl.' WHERE `show`="0";');
        $result = $this->query();

        $ret = array();

        while ($res = $result->fetchRow()) {
            $ret[] = $res['field'];
        }
        return $ret;
    }

}

?>
