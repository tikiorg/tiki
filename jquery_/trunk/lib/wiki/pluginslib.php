<?php
    /**
    * Plugin Lib
    *
    * A port of PhpWiki WikiPlugin class
    * Principal use is port PhpWiki plugins, but can be used to make new ones.
    * Use:
    * - Extends PluginsLib with your class
    * - add the lines
    * <code>
    * include "pluginslib.php";
    * 
    * function wikiplugin_backlinks($data, $params) {
    *    $plugin = new BackLinks();
    *    return $plugin->run($data, $params);
    * }
    * function wikiplugin_backlinks_help() {
    *    $plugin = new BackLinks();
    *    return $plugin->getDescription();
    * }    * </code>
    * @package Tikiwiki
    * @subpackage Plugins
    * @author Claudio Bustos
    * @version $Revision: 1.12 $
    */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

    class PluginsLib extends TikiLib {
        var $_errors;
        var $_data;
        var $_params;
        /**
        * Array of params to be expanded as arrays. Explode the string with {@link $separator}
        * @var array
        */
        var $expanded_params = array();
        /**
        * Separator used to explote params listed on {@link $expanded_params}
        * @var string
        */
        var $separator = "|";
        /**
        * List of fields retrieved from {@link TikiLib::list_pages()}
        * Keys are the name of the fields and values the names for tra();
        * @var array
        */
        var $aInfoPresetNames = array(
        "hits" => "Hits", "lastModif" => "Last mod", "user" => "Last author", "len" => "Size", "comment" => "Com", "creator" => "Creator", "version" => "Last ver", "flag" => "Status", "versions" => "Vers", "links" => "Links", "backlinks" => "Backlinks");
        /**
        * Constructor
        */
        function PluginsLib() {
            $this->TikiLib($GLOBALS["dbTiki"]);
        }
        /**
        * Process the params, in this order:
        * - default values, asigned on {@link PluginsLib::getDefaultArguments()}
        * - request values, sended by GET or POST method, if $request is put to true
        * - explicit values, asigned on the Wiki
        * @param array sended to wikiplugin_name($data, $params)
        * @param bool if set to true, accept values from $_REQUEST
        * @param bool if set to true, assign default values from {@link PluginsLib::getDefaultArguments()}
        * @return array list of params
        */
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
                    // maybe this kind of transformation can be grouped on a external function
                    if ($default_val==="[pagename]") {
                        $default_val=$_REQUEST["page"];
                    }
                    $args[$arg] = $default_val;
                }
                if (in_array($arg, $this->expanded_params)) {
                    if ($args[$arg]) {
                    $args[$arg] = explode($this->separator, $args[$arg]);
                    foreach($args[$arg] as $id=>$value) {
                        $args[$arg][$id]=trim($value);
                    }
                    } else {
                    $args[$arg]=array();
                    }
                } 
            }
            return $args;
        }
        /**
        * Returns the name of the Plugin
        * By default, erase the first 'WikiPlugin'
        * Made for overload it.
        * @return string
        */
        function getName() {
            return preg_replace('/^WikiPlugin/', '', get_class($this));
        }
        /**
        * Returns a description of the Plugin
        * Made for overload it.
        * @return string
        */
        function getDescription() {
            return $this->getName();
        }
        /**
        * Returns the version of the version
        * Made for overload it.
        * @return string
        */
        function getVersion() {
            return tra("No version indicated");
            //return preg_replace("/[Revision: $]/", '',
            //                    "\$Revision: 1.12 $");
        }
        /**
        * Returns the default arguments for the plugin
        * Use keys as the arguments and values as ... the default values
        * @return array
        */
        function getDefaultArguments() {
            return array('description' => $this->getDescription());
        }
        /**
        * Run the plugin
        * For sake of God, overload it!
        * @param string
        * @param array
        */
        function run ($data, $params) {
            /**
            * UGLY ERROR!.
            */
            return $this->error("PluginsLib::run: pure virtual function. Don't be so lazy!");
        }
        function error ($message) {
            return "~np~<span class='warn'>".tra("Plugin ").$this->getName()." ".tra("failed")." : ".tra($message)."</span>~/np~";
        }
        function getErrorDetail() {
            return $this->_errors;
        }
        function _error($message) {
            $this->_errors = $message;
            return false;
        }
    }
    /**
    * Class with utilities for Plugins
    */
    class PluginsLibUtil {
        /**
        * Create a table with information from pages
        * @param array key ["data"] from one of the functions that retrieve informaci�n about pages
        * @param array list of keys to show.
        * @param array definition of the principal field. By default: 
        *              array("field"=>"pageName","name"=>"Page")
        * @return string
        */
        function createTable($aData,$aInfo=false,$aPrincipalField=false) {
            // contract
            if (!$aPrincipalField or !is_array($aPrincipalField)) {
                $aPrincipalField=array("field"=>"pageName","name"=>"Page");
            }
            if (!is_array($aInfo)) {
                $aInfo=false;
            }
            // ~contract
            $sOutput="";
            if ($aInfo) {
                $iNumCol=count($aInfo)+1;
                $sStyle=" style='width:".(floor(100/$iNumCol))."%' ";
                // Header for info
                $sOutput  .= "<table class='normal'><tr><td class='heading' $sStyle>".tra($aPrincipalField["name"])."</td>";
                foreach($aInfo as $iInfo => $sHeader) {
                    $sOutput  .= "<td class='heading' $sStyle >".tra($sHeader)."</td>";
                }
                $sOutput  .= "</tr>";
            }
            $iCounter=1;
            foreach($aData as $aPage) {
                $sClass=($iCounter%2)?"odd":"even";
                if (!$aInfo) {
                    $sOutput  .= "*((".$aPage[$aPrincipalField["field"]]."))\n";
                } else {
                    $sOutput  .= "<tr><td class='$sClass'>((".$aPage[$aPrincipalField["field"]]."))</td>";
                    foreach($aInfo as $sInfo) {
                        if (isset($aPage[$sInfo])) {
                            $sOutput  .= "<td class='$sClass'>".$aPage[$sInfo]."</td>";
                        }
                    }
                }
                
            $iCounter++;
            }
                if ($aInfo) {
                    $sOutput  .= "</table>";
                }
        return $sOutput;
        }

        function createList($aData) {
            $aPrincipalField=array("field"=>"pageName","name"=>"Pages");
	
            // Header for info
            $sOutput = "<table class='normal'><tr><td class='heading'>".tra($aPrincipalField["name"])."</td></tr><tr><td class='even'>";
            $iCounter=0;		
            // create a comma separated list of entries
            foreach($aData as $aPage) {
              if ($iCounter>0) $sOutput .= ", ";
              $sOutput  .= "((".$aPage[$aPrincipalField["field"]]."))";
              $iCounter++;
            }
              $sOutput .= "</td></tr></table>";
            return $sOutput;
        }
    }
?>
