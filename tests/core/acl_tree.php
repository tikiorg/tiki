<?php
/**
 * \file
 * $Header: /cvsroot/tikiwiki/tiki/tests/core/acl_tree.php,v 1.1 2003-08-24 00:42:17 zaufi Exp $
 *
 * \brief Tiki Objects Tree and associated ACL
 *
 */

// Core object types
define('USER_OBJECT_TYPE',      1);
define('GROUP_OBJECT_TYPE',     2);
define('CONTAINER_OBJECT_TYPE', 3);

/**
 * \brief Tiki Objects Tree and associated ACL
 *
 * Tiki Objects Tree and ACL management subsystem class
 *
 */
class TikiCoreObjectsManagement
{
    /// Cached object types
    var $object_types;

    ///
    function TikiCoreObjectsManagement()
    {
    }

    /*
     * Object Types API
     */

    /**
     * \brief Add custom object type to system
     *
     * \param $objtype int -- unique identifier of object type
     * \param $name string -- human readable name
     * \param $description string -- description
     *
     * \return boolean true/false
     */
    function register_object_type($objtype, $name, $description)
    {
    }
    /**
     * \brief Add custom object type to system
     *
     * \return array vector of object types
     */
    function get_object_types_list()
    {
        // if $this->object_types is empty then do actual request to DB
        // and fill $object_types (cached list of types)
        // else return this array...
    }
    /**
     * \brief Check if given object type regstered
     *
     * \param $objtype int -- object type to check
     * \return boolean true/false
     */
    function is_registered($objtype)
    {
    }

    /*
     * Objects Management API
     */

    /**
     * \brief Add object to ACL tree
     * \param $objid int -- ID of object
     * \param $objtype int -- type of object
     * \param $parentobjid int -- ID of parent object
     * \param $parentobjtype int -- type of parent object
     *
     * \return boolean true/false
     */
    function add_object($objid, $objtype, $parentobjid, $parentobjtype)
    {
    }
    /**
     * \brief Remove object from ACL tree
     * \param $objid int -- ID of object to be removed
     * \param $objtype int -- type of object to be removed
     *
     * \return boolean true/false
     */
    function remove_object($objid, $objtype)
    {
    }
    /**
     * \brief Get parent object for given object
     * \param $objid int -- ID of object to get parent for
     * \param $objtype int -- type of object to get parent for
     *
     * \return array('id', 'type') or false on error
     */
    function get_parent_object($objid, $objtype)
    {
    }
    /**
     * \brief Get parent object for given object
     * \param $objid int -- ID of object to set parent for
     * \param $objtype int -- type of object to set parent for
     * \param $parentobjid int -- ID of parent object to be set
     * \param $parentobjtype int -- type of parent object to be set
     *
     * \return array('id', 'type') or false on error
     */
    function set_parent_object($objid, $objtype, $parentobjid, $parentobjtype)
    {
    }
    /**
     * \brief Get list of child objects for given object
     * \param $objid int -- ID of object to get list for
     * \param $objtype int -- type of object to get list for
     *
     * \return array('id', 'type') or false on error
     */
    function get_child_objects_list($objid, $objtype)
    {
    }

    /*
     * Permissions (Rights) Management API
     */

    // === ONE BIG QUESTION WHAT AND HOW TO IMPLEMENT ===
    function add_acl()   {}
    function del_acl()   {}
    function checl_acl() {}
}

?>
