<?php
    /** 
    * Title Search Plugin
    * Search the titles of all pages in this wiki
    */
    function wikiplugin_titlesearch_help() {
        return tra("Search the titles of all pages in this wiki").":<br />~np~{TITLESEARCH(search=>Admin,info=>hits+user,exclude=>HomePage+SandBox,noheader=>0,)}{TITLESEARCH}~/np~";
    }
    /**
    * Search the titles of all pages in this wiki
    * @param string not used
    * @param array       // info (allows multiple columns, joined by '+') |
    *                       info=hits,lastModif,user,ip,len,comment, creator, version,
    *                       flag, versions,links,backlinks
    *                    // exclude (allows multiple pagenames)|exclude=HomePage+RecentChanges
    *                    // noheader = by default, false
    *                    // search = required
    * @return string
    */

    function wikiplugin_titlesearch($data, $params) {
        global $wikilib;
        $aInfoPresetNames = array(
        "hits" => "Hits", "lastModif" => "Last mod", "user" => "Last author", "len" => "Size", "comment" => "Com", "creator" => "Creator", "version" => "Last ver", "flag" => "Status", "versions" => "Vers", "links" => "Links", "backlinks" => "Backlinks");
        $aInfoPreset = array_keys($aInfoPresetNames);
        extract ($params);
        /////////////////////////////////
        // Default values
        /////////////////////////////////
        //
        if (!isset($search)) {
            return ("You have to define a search");
        }
        $noheader = isset($noheader)?(bool)$noheader : 0; // Include a header, with the search and number of pages
        $exclude = isset($exclude)?explode("+", $exclude) : array(); // List of pages to exclude, separated by '+'
        //
        /////////////////////////////////
        // Create a valid list for $info
        /////////////////////////////////
        //
        if (isset($info)) {
            $info = explode("+", $info);
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
        $aInfo = $wikilib->list_pages(0, -1, 'pageName_desc', $search);
        foreach($aInfo["data"] as $idPage=>$aPage) {
            if(in_array($aPage["pageName"],$exclude)) {
                unset($aInfo["data"][$idPage]);
                $aInfo["cant"]--;
            }
        }
        //
        /////////////////////////////////
        // Start of Output
        /////////////////////////////////
        //
        if (!$noheader) {
            // Create header
            $count = $aInfo["cant"];
            if (!$count) {
                $sOutput .= tra("No pages found for title search")." '__".$search."__'";
            } elseif ($count == 1) {
                $sOutput .= tra("One page found for title search")." '__".$search."__'";
            } else {
                $sOutput = "$count".tra(" pages found for title search")." '__".$search."__'";
            }
            $sOutput .= "\n";
        }
        if ($info) {
            // Header for info
            $sOutput .= "<table class='normal'><tr><td class='heading'>".tra("Page")."</td>";
            foreach($info as $iInfo => $sHeader) {
                $sOutput .= "<td class='heading'>".tra($sHeader)."</td>";
            }
            $sOutput .= "</tr>";
        }
        foreach($aInfo["data"] as $aPage) {
            // Loop of Backlinks
            if (!$info) {
                $sOutput .= "*((".$aPage["pageName"]."))\n";
            } else {
                $sOutput .= "<tr><td>((".$aPage["pageName"]."))</td>";
                foreach($info as $sInfo) {
                    if (isset($aPage[trim($sInfo)])) {
                        $sOutput .= "<td>".$aPage[trim($sInfo)]."</td>";
                    }
                }
            }
        }
        if ($info) {
            if ($info) {
                $sOutput .= "</table>";
            }
        }
        return $sOutput;
    }
?>