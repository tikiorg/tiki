<?php
/**
 * \file
 * $Header: /cvsroot/tikiwiki/tiki/tests/core/extensions.php,v 1.1 2003-08-22 19:04:40 zaufi Exp $
 *
 * \brief Extensions Management
 *
 */

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
    function enabled_extensions($user)
    {
    }
    /**
     * \brief Rescan extensions dir
     * ... and update DB table.
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
     */
    function is_installed($extension)
    {
    }
    /**
     * \brief Return true if extension installed and enabled for current user
     *
     */
    function is_enabled($extension, $user)
    {
    }
}

?>