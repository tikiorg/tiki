<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once "lib/wiki/pluginslib.php";

function wikiplugin_titlesearch_info() {
	return array(
		'name' => tra('Title Search'),
		'documentation' => 'PluginTitleSearch',
		'description' => tra('Search pages by title'),
		'prefs' => array( 'feature_wiki', 'wikiplugin_titlesearch' ),
		'icon' => 'pics/icons/page_find.png',
		'params' => array(
			'search' => array(
				'required' => true,
				'name' => tra('Search Criteria'),
				'description' => tra('Portion of a page name.'),
				'default' => '',
			),
			'info' => array(
				'required' => false,
				'name' => tra('Information'),
				'description' => tra('Also show page hits or user'),
				'default' => '',
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Hits'), 'value' => 'hits'), 
					array('text' => tra('User'), 'value' => 'user')
				)
			),
			'exclude' => array(
				'required' => false,
				'name' => tra('Exclude'),
				'description' => tra('Pipe-separated list of page names to exclude from results.'),
				'default' => '',
			),
			'noheader' => array(
				'required' => false,
				'name' => tra('No Header'),
				'description' => tra('Set to 1 (Yes) to have no header for the search results.'),
				'default' => 0,
				'options' => array(
					array('text' => '', 'value' => ''), 
					array('text' => tra('Yes'), 'value' => 1), 
					array('text' => tra('No'), 'value' => 0)
				)
			),
		),
	);
}
class WikiPluginTitleSearch extends PluginsLib
{
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
        if (isset($style) && $style == 'list') $info = false;

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
        if (isset($noheader) && !$noheader) {
            // Create header
            $count = $aPages["cant"];
            if (!$count) {
                $sOutput  .= tra("No pages found for title search")." '__".$search."__'";
            } elseif ($count == 1) {
                $sOutput  .= tra("One page found for title search")." '__".$search."__'";
            } else {
                $sOutput = "$count ".tra("pages found for title search")." '__".$search."__'";
            }
            $sOutput  .= "\n";
        }
        if (isset($style) && $style == 'list') {
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
