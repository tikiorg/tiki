<?php
/**
 * \file
 * $Header: /cvsroot/tikiwiki/tiki/tests/core/extensions.php,v 1.2 2003-08-25 22:57:43 zaufi Exp $
 *
 * \brief Extensions Management
 *
 */

define('TIKI_EXT_BASE_DIR', dirname(__FILE__) . '/ext');

/**
 * \brief Extensions Management Core Subsystem
 *
 */
class TikiCoreExtensions
{
    /**
     * \brief List of installed extensions
     * (just get it from DB table)
     */
    function installed_extensions()
    {
    }
    /**
     * \brief List of enabled extensions
     * List available for user extensions
     */
    function enabled_extensions($objid, $objtype)
    {
    }
    /**
     * \brief Rescan extensions dir
     * ... and update DB table.
     *
     */
    function search_extensions()
    {
    }
    /**
     * \brief Install extension
     * ... and update DB table.
     */
    function install_extension($extension)
    {
    }
    /**
     * \brief Uninstall extension
     * ... and update DB table.
     */
    function uninstall_extension($extension)
    {
    }
    /**
     * \brief Return true if extension installed
     *
     * It is not enough to copy extension files to corresponding dir
     * it should be installed...
     *
     * \param $extension string extension name to check
     * \return true if installed, else false
     */
    function is_installed($extension)
    {
    }
    /**
     * \brief Return true if extension installed and enabled for current user
     *
     */
    function is_enabled($extension, $objid, $objtype)
    {
    }

    /*
     * API for extension installators
     */

    /**
     * \brief
     */
    function register_extension()
    {
    }
    /**
     * \brief
     */
    function unregister_extension()
    {
    }
    /**
     * \brief
     */
    function register_page()
    {
    }
    /**
     * \brief
     */
    function unregister_page()
    {
    }

}

?>