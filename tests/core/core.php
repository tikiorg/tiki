<?php
/**
 * \file
 * $Header: /cvsroot/tikiwiki/tiki/tests/core/core.php,v 1.3 2003-08-25 22:57:43 zaufi Exp $
 *
 * \brief Main file
 *
 */

// Include core subsystems
require_once('database.php');
require_once('extensions.php');
require_once('acl_tree.php');

// Some defines
define('TIKI_HARDCODED_CONFIG_FILE', dirname(__FILE__) . '/tiki-hardcoded-config.php');

/**
 * \brief The main class of Tiki core
 *
 * This class do not contain valued code... all work delegated to
 * corresponding subsystems. It is just facade class.
 *
 */
class TikiCore
{
    // Tiki core subsystems
    var $sys_db;                                        //!< Database subsystem
    var $sys_objmgr;                                    //!< Objects management subsystem
    var $sys_usermgr;                                   //!< User/group management subsystem
    var $sys_extmgr;                                    //!< Extensions management subsystem
    /// Core constructor
    function TikiCore()
    {
        $this->initialize();                            // Run init scripts
        // Init core subsystems
        $this->sys_db     = new TikiCoreDatabase($dbTiki);
        $this->sys_extmgr = new TikiCoreExtensions();
        $this->sys_objmgr = new TikiCoreObjectsManagement();
    }
    /// Run init.scripts
    function initialize()
    {
        // Get hardcoded globals before exec init scripts
        $tiki_conf = TIKI_HARDCODED_CONFIG_FILE;
        if (file_exists($tiki_conf))
            require_once($tiki_conf);
        else
            return TIKI_CORE_INIT_FAIL;
        //
        $scripts = array();
        // Read directory files into array
        $initdir = dirname(__FILE__) . '/init.scripts';
        $d = dir($initdir);
        while (($entry = $d->read()) !== false)
            // Add to scripts list only if file have mask 'DD-name.php'
            if (preg_match('/[0-9]{2}-.*\.php/', $entry))
                $scripts[] = $entry;
        $d->close();
        //
        sort($scripts);
        //
        foreach ($scripts as $script) require_once($initdir.'/'.$script);
    }

    /*
     * Database Subsystem API calls
     */
    function query($query, $values = null, $numrows = -1, $offset = -1, $reporterrors = true)
    {
        return $this->sys_db->query($query, $values, $numrows, $offset, $reporterrors);
    }
    function getOne($query, $values = null, $reporterrors = true)
    {
        return $this->sys_db->getOne($query, $values, $reporterrors);
    }
    /*
     * Extensions Management Subsystem API calls
     */
    function installed_extensions()
    {
        return $this->sys_extmgr->installed_extensions();
    }
    function enabled_extensions($user)
    {
        return $this->sys_extmgr->enabled_extensions($user);
    }
    function search_extensions()
    {
        return $this->sys_extmgr->search_extensions();
    }
    function install_extension($extension)
    {
        return $this->sys_extmgr->install_extension($extension);
    }
    function uninstall_extension($extension)
    {
        return $this->sys_extmgr->uninstall_extension($extension);
    }
    function is_installed($extension)
    {
        return $this->sys_extmgr->is_installed($extension);
    }
    function is_enabled($extension, $user)
    {
        return $this->sys_extmgr->is_enabled($extension, $user);
    }
    /*
     * Object Types API
     */
    function register_object_type($objtype, $name, $description)
    {
        return $this->sys_objmgr->register_object_type($objtype, $name, $description);
    }
    function get_object_types_list()
    {
        return $this->sys_objmgr->get_object_types_list()
    }
    function is_registered($objtype)
    {
        return $this->sys_objmgr->is_registered($objtype)
    }
    /*
     * Objects Management API
     */
    function add_object($objid, $objtype, $parentobjid, $parentobjtype)
    {
        return $this->sys_objmgr->add_object($objid, $objtype, $parentobjid, $parentobjtype)
    }
    function remove_object($objid, $objtype)
    {
        return $this->sys_objmgr->remove_object($objid, $objtype)
    }
    function get_parent_object($objid, $objtype)
    {
        return $this->sys_objmgr->get_parent_object($objid, $objtype)
    }
    function set_parent_object($objid, $objtype, $parentobjid, $parentobjtype)
    {
        return $this->sys_objmgr->set_parent_object($objid, $objtype, $parentobjid, $parentobjtype)
    }
    function get_child_objects_list($objid, $objtype)
    {
        return $this->sys_objmgr->get_child_objects_list($objid, $objtype)
    }
    /*
     * Permissions (Rights) Management API
     */
    // === ONE BIG QUESTION WHAT AND HOW TO IMPLEMENT ===
    function add_acl()   {}
    function del_acl()   {}
    function checl_acl() {}
}

// Create instance of Tiki core
global $core;
$core = new TikiCore();

?>
