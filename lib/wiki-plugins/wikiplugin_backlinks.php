<?php
    /**
    * Backlinks plugin
    * List all pages which link to specific pages (same as tiki-backlinks.php)
    *
    */
    include "pluginslib.php";
    /**
    * Create a list of backlinks to a page
    * @param string not used
    * @param array       - info (allows multiple columns, joined by '|')
    *                      ex.: info=hits,lastModif,user,ip,len,comment, creator, version,
    *                           flag, versions,links,backlinks
    *                    - exclude (allows multiple pagenames)
    *                      ex.: exclude=HomePage|RecentChanges
    *                    - include_self = by default, false
    *                    - noheader = by default, false
    *                    - page = by default, the current page.
    * @return string
    */

    class BackLinks extends PluginsLib {
        var $expanded_params = array("exclude", "info");
        function getDefaultArguments() {
            return array('exclude' => array(),
                'include_self' => 0,
                'noheader' => 0,
                'page' => $_REQUEST["page"],
                'info' => false );
        }
        function getName() {
            return tra("BackLinks");
        }
        function getDescription() {
            return tra("List all pages which link to specific pages").":<br />~np~{BACKLINKS(info=>hits|user,exclude=>HomePage|SandBox,include_self=>1,noheader=>0,page=>HomePage)}{BACKLINKS}~/np~";
        }
        function getVersion() {
            return preg_replace("/[Revision: $]/", '',
                "\$Revision: 1.2 $");
        }
        function run ($data, $params) {
            global $wikilib;
            $params = $this->getParams($params, true);
            $aInfoPreset = array_keys($this->aInfoPresetNames);
            extract ($params);
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
            }
            //
            /////////////////////////////////
            // Process backlinks
            /////////////////////////////////
            //
            $backlinks = $wikilib->get_backlinks($page);
            foreach($backlinks as $backlink) {
                if (!in_array($backlink["fromPage"], $exclude)) {
                    $aBackRequest[] = $backlink["fromPage"];
                }
            }
            if ($include_self) {
                $aBackRequest[] = $page;
            }
            $sOutput = "";
            $aInfo = $this->list_pages(0, -1, 'pageName_desc', $aBackRequest);
            //
            /////////////////////////////////
            // Start of Output
            /////////////////////////////////
            //
            if (!$noheader) {
                // Create header
                $count = $aInfo["cant"];
                if (!$count) {
                    $sOutput  .= tra("No pages link to")." (($page))";
                } elseif ($count == 1) {
                    $sOutput  .= tra("One page links to")." (($page))";
                } else {
                    $sOutput = "$count ".tra("pages links to")." (($page))";
                }
                $sOutput  .= "\n";
            }
            if ($info) {
                // Header for info
                $sOutput  .= "<table class='normal'><tr><td class='heading'>".tra("Page")."</td>";
                foreach($info as $iInfo => $sHeader) {
                    $sOutput  .= "<td class='heading'>".tra($sHeader)."</td>";
                }
                $sOutput  .= "</tr>";
            }
            foreach($aInfo["data"] as $aPage) {
                // Loop of Backlinks
                if (!$info) {
                    $sOutput  .= "*((".$aPage["pageName"]."))\n";
                } else {
                    $sOutput  .= "<tr><td>((".$aPage["pageName"]."))</td>";
                    foreach($info as $sInfo) {
                        if (isset($aPage[trim($sInfo)])) {
                            $sOutput  .= "<td>".$aPage[trim($sInfo)]."</td>";
                        }
                    }
                }
            }
            if ($info) {
                if ($info) {
                    $sOutput  .= "</table>";
                }
            }
            return $sOutput;
        }
    }
    function wikiplugin_backlinks($data, $params) {
        $plugin=new BackLinks();
        return $plugin->run($data, $params);
    }
    function wikiplugin_backlinks_help() {
        $plugin=new BackLinks();
        return $plugin->getDescription();
    }
?>