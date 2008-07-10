<?php
    /**
    * Include the library {@link PluginsLib}
    */
    require_once "lib/wiki/pluginslib.php";
    /**
    * Title Search Plugin
    * Search the titles of all pages in this wiki
    * Params
    * <ul>
    * <li> search: required
    * <li> info (allows multiple columns, joined by '|') : hits,lastModif,user,ip,len,comment,
    * creator, version, flag, versions,links,backlinks
    * <li> exclude (allows multiple pagenames) : HomePage|RecentChanges
    * <li> noheader         : by default, false
    * </ul>    
    * @package Tikiwiki
    * @subpackage TikiPlugins
    * @author Claudio Bustos
    * @version $Revision: 1.25 $
    */
    function wikiplugin_titlesearch_help() {
        return tra("Search the titles of all pages in this wiki").":<br />~np~{TITLESEARCH(search=>Admin,info=>hits|user,exclude=>HomePage|SandBox,noheader=>0)}{TITLESEARCH}~/np~";
    }
    class WikiPluginTitleSearch extends PluginsLib {
        var $expanded_params = array("exclude", "info");
        function getDescription() {
            return wikiplugin_titlesearch_help();
        }
        function getDefaultArguments() {
            return array('exclude' => '' ,
                'noheader' => 0,
                'info' => false,
                'search' => false,
				'style' => 'table'
		);
        }
        function getName() {
            return "TitleSearch";
        }
        function getVersion() {
            return preg_replace("/[Revision: $]/", '',
                "\$Revision: 1.25 $");
        }
        function run ($data, $params) {
            global $wikilib; include_once('lib/wiki/wikilib.php');
						global $tikilib;
            $aInfoPreset = array_keys($this->aInfoPresetNames);
            $params = $this->getParams($params, true);
            extract ($params,EXTR_SKIP);
            if (!$search) {
                return $this->error("You have to define a search");
            }

            // no additional infos in list output
            if ($style == 'list') $info = false;

            //
            /////////////////////////////////
            // Create a valid list for $info
            /////////////////////////////////
            //
            if ($info) {
                $info_temp = array();
                foreach($info as $sInfo) {
                    if (in_array(trim($sInfo), $aInfoPreset)) {
                        $info_temp[] = trim($sInfo);
                    }
                    $info = $info_temp?$info_temp:
                    false;
                }
            } else {
                $info = false;
            }
            //
            /////////////////////////////////
            // Process pages
            /////////////////////////////////
            //
            $sOutput = "";
            $aPages = $tikilib->list_pages(0, -1, 'pageName_desc', $search);
            foreach($aPages["data"] as $idPage => $aPage) {
                if (in_array($aPage["pageName"], $exclude)) {
                    unset($aPages["data"][$idPage]);
                    $aPages["cant"]--;
                }
            }
            //
            /////////////////////////////////
            // Start of Output
            /////////////////////////////////
            //
            if (!$noheader) {
                // Create header
                $count = $aPages["cant"];
                if (!$count) {
                    $sOutput  .= tra("No pages found for title search")." '__".$search."__'";
                } elseif ($count == 1) {
                    $sOutput  .= tra("One page found for title search")." '__".$search."__'";
                } else {
                    $sOutput = "$count".tra(" pages found for title search")." '__".$search."__'";
                }
                $sOutput  .= "\n";
            }
            if ($style == 'list') {
                $sOutput.=PluginsLibUtil::createList($aPages["data"]);
            }
            else {
                $sOutput.=PluginsLibUtil::createTable($aPages["data"],$info);
            }
            return $sOutput;
        }
    }
    function wikiplugin_titlesearch($data, $params) {
        $plugin = new WikiPluginTitleSearch();
        return $plugin->run($data, $params);
    }
?>
