<?php
    /**
    * Include the library {@link PluginsLib}
    */
    require_once "lib/wiki/pluginslib.php";
    /**
    * Plugin Manager
    * Provides a list of plugins on this wiki.
    *
    * Params:
    * <ul>
    * <li>info (allows multiple columns, joined by '|') : version,description,arguments
    *           . By default, selected all.
    * </ul>
    *
    * @package TikiWiki
    * @subpackage TikiPlugins
    * @author Claudio Bustos
    * @version $Revision: 1.1 $
    */
    class WikiPluginPluginManager extends PluginsLib {
        var $expanded_params = array("info");
        function getDefaultArguments() {
            return array('info' => "version|description|arguments");
        }
        function getName() {
            return tra("PluginManager");
        }
        function getVersion() {
            return preg_replace("/[Revision: $]/", '',
                "\$Revision: 1.1 $");
        }
        function getDescription() {
            return tra("Provides a list of plugins on this wiki.").":<br />~np~{PLUGINMANAGER(info=>version|description|arguments)}{PLUGINMANAGER}~/np~";
        }
        function run($data, $params) {
            global $wikilib;
            if (!is_dir(PLUGINS_DIR)) {
                return $this->error("No plugins directory defined");
            }
            $params = $this->getParams($params);
            extract($params);
            $aPlugins = $wikilib->list_plugins();
            $aData=array();
            foreach($aPlugins as $sPluginFile) {
                preg_match("/wikiplugin_(.*)\.php/i", $sPluginFile, $match);
                $sPlugin= $match[1];
                include_once(PLUGINS_DIR.'/'.$sPluginFile);
                // First, locate the new format ;)
                if (class_exists("WikiPlugin".$sPlugin)) {
                    $sClassName="WikiPlugin".$sPlugin;
                    $oClass=new $sClassName();
                    if (method_exists($oClass,'getName')) {
                        $sPlugin=$oClass->getName();
                    }
                    $aData[$sPlugin]["description"]=$this->processDescription($oClass->getDescription());
                    if (method_exists($oClass,'getVersion')) {
                        $aData[$sPlugin]["version"]=$oClass->getVersion();
                    } else {
                        $aData[$sPlugin]["version"]=" -- ";
                    }
                    $aParams=$oClass->getDefaultArguments();
                        $aData[$sPlugin]["arguments"]="";
                        foreach ($aParams as $arg => $default) {
                        if (stristr($default, ' ')) {
                        $default = "'$default'";}
                        if ($default==="[pagename]") {
                            $default="[[pagename]";
                        }
                        $aData[$sPlugin]["arguments"].=$arg." => ".$default."<br \>";
                        }
                    unset($oClass);
                } else {
                        $sFuncName="wikiplugin_".$sPlugin."_help";
                        if (function_exists($sFuncName)) {
                            $sDescription=$this->processDescription($sFuncName());
                        } else {
                            $sDescription= " --- ";
                        }
                        $aData[$sPlugin]["description"] =$sDescription;
                        $aData[$sPlugin]["version"] = tra("No version indicated");
                        $aData[$sPlugin]["arguments"] = tra("No arguments indicated");
                    }
                    $aData[$sPlugin]["plugin"] = "[http://tikiwiki.org/tiki-index.php?page=Plugin".ucfirst($sPlugin)."|Plugin". ucfirst($sPlugin)."]";
                } // Plugins Loop
            return PluginsLibUtil::createTable($aData,$info,array("field"=>"plugin","name"=>"Plugin"));
        }
        function processDescription($sDescription) {
            $sDescription=str_replace(",",", ",$sDescription);
            $sDescription=str_replace("|","| ",$sDescription);
            $sDescription=strip_tags(wordwrap($sDescription,35));
            return $sDescription;
        }
    }
    function wikiplugin_pluginmanager($data, $params) {
        $plugin = new WikiPluginPluginManager();
        return $plugin->run($data, $params);
    }
    function wikiplugin_pluginmanager_help() {
        $plugin = new WikiPluginPluginManager();
        return $plugin->getDescription();
    }
?>