<?php
    /**
    * Plugin Lib
    * A port of PhpWiki WikiPlugin class
    * Principal use is port PhpWiki plugins, but can be used to make new ones.
    * Use:
    * - Extends PluginsLib with your class
    * - add the lines
    * <code>
    * include "pluginslib.php";
    * $plugin = new BackLinks();
    * function wikiplugin_backlinks($data, $params) {
    *    global $plugin;
    *    return $plugin->run($data, $params);
    * }
    * function wikiplugin_backlinks_help() {
    *     global $plugin;
    *    return $plugin->description();
    * }    * </code>
    *
    * @author Claudio Bustos
    * @version 1.0
    */
    class PluginsLib extends TikiLib {
        var $_errors;
        var $_data;
        var $_params;
        var $expanded_params = array();
        var $separator = "|";
        var $aInfoPresetNames = array(
        "hits" => "Hits", "lastModif" => "Last mod", "user" => "Last author", "len" => "Size", "comment" => "Com", "creator" => "Creator", "version" => "Last ver", "flag" => "Status", "versions" => "Vers", "links" => "Links", "backlinks" => "Backlinks");
        function PluginsLib() {
            $this->db = $GLOBALS["dbTiki"];
        }
        function getParams($params, $request = false, $defaults = false) {
            if ($defaults === false) {
                $defaults = $this->getDefaultArguments();
            }
            $args = array();
            foreach ($defaults as $arg => $default_val) {
                if (isset($params[$arg])) {
                    $args[$arg] = $params[$arg];
                } elseif(isset($_REQUEST[$arg])) {
                    $args[$arg] = $_REQUEST[$arg];
                } else {
                    $args[$arg] = $default_val;
                }
                if (in_array($arg, $this->expanded_params) and $args[$arg]) {
                    $args[$arg] = explode($this->separator, $args[$arg]);
                }
            }
            return $args;
        }
        function getName() {
            return preg_replace('/^.*_/', '', get_class($this));
        }
        function getDescription() {
            return $this->getName();
        }
        function getVersion() {
            return tra("No version indicated");
            //return preg_replace("/[Revision: $]/", '',
            //                    "\$Revision: 1.1 $");
        }
        function getDefaultArguments() {
            return array('description' => $this->getDescription());
        }
        function run ($data, $params) {
            trigger_error("PluginsLib::run: pure virtual function",
                E_USER_ERROR);
        }
        function error ($message) {
            return "~np~<span class='warn'>Plugin ".$this->getName()." failed : $message</span>~/np~";
        }
        function getErrorDetail() {
            return $this->_errors;
        }
        function _error($message) {
            $this->_errors = $message;
            return false;
        }
    }
?>