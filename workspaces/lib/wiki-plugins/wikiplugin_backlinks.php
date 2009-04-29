<?php
// $Id: /cvsroot/tikiwiki/tiki/lib/wiki-plugins/wikiplugin_backlinks.php,v 1.17 2007-06-16 16:01:59 sylvieg Exp $

    /**
    * Include the library {@link PluginsLib}
    */
    require_once "lib/wiki/pluginslib.php";
    /**
    * Backlinks plugin
    * List all pages which link to specific pages (same as tiki-backlinks.php)
    *
    * Params:
    * <ul>
    * <li>info (allows multiple columns, joined by '|') : hits,lastModif,user,ip,len,comment,
    * creator, version, flag, versions,links,backlinks
    * <li> exclude (allows multiple pagenames) : HomePage|RecentChanges
    * <li> include_self     : by default, false
    * <li> noheader         : by default, false
    * <li> page             :by default, the current page.
    * </ul>
    *
    * @package Tikiwiki
    * @subpackage TikiPlugins
    * @author Claudio Bustos
    * @version $Revision: 1.17 $
    */
    function wikiplugin_backlinks_help() {
        return tra("List all pages which link to specific pages").":<br />~np~{BACKLINKS(info=>hits|user,exclude=>HomePage|SandBox,include_self=>1,noheader=>0,page=>HomePage)}{BACKLINKS}~/np~";
    }
    class WikiPluginBackLinks extends PluginsLib {
        var $expanded_params = array("exclude", "info");
        function getDefaultArguments() {
            return array('exclude' => '',
                'include_self' => 0,
                'noheader' => 0,
                'page' => '[pagename]',
                'info' => false );
        }
        function getName() {
            return "BackLinks";
        }
        function getDescription() {
            return wikiplugin_backlinks_help();
        }
        function getVersion() {
            return preg_replace("/[Revision: $]/", '',
                "\$Revision: 1.17 $");
        }
        function run ($data, $params) {
            global $wikilib; include_once('lib/wiki/wikilib.php');
            $params = $this->getParams($params, true);
            $aInfoPreset = array_keys($this->aInfoPresetNames);
            extract ($params,EXTR_SKIP);
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
            $sOutput = "";
            // Verify if the page exists
            if (!$wikilib->page_exists($page)) {
                return $this->error(tra("Page cannot be found")." : <b>$page</b>");
            }
            //
            /////////////////////////////////
            // Process backlinks
            /////////////////////////////////
            //

            $aBackRequest = array();
            $aBackLinks = $wikilib->get_backlinks($page);
            foreach($aBackLinks as $backlink) {
                if (!in_array($backlink["fromPage"], $exclude)) {
                    $aBackRequest[] = $backlink["fromPage"];
                }
            }
            if ($include_self) {
                $aBackRequest[] = $page;
            }
            if (!$aBackRequest) {
                return tra("No pages links to")." (($page))";
            } else {
                $aPages = $this->list_pages(0, -1, 'pageName_desc', $aBackRequest);
            }
            //
            /////////////////////////////////
            // Start of Output
            /////////////////////////////////
            //
            if (!$noheader) {
                // Create header
                $count = $aPages["cant"];
                if ($count == 1) {
                    $sOutput  .= tra("One page links to")." (($page))";
                } else {
                    $sOutput = "$count ".tra("pages link to")." (($page))";
                }
                $sOutput  .= "\n";
            }
            $sOutput  .= PluginsLibUtil::createTable($aPages["data"], $info);
            return $sOutput;
        }
    }

	function wikiplugin_backlinks_info() {
		return array(
			'name' => tra('Backlinks'),
			'documentation' => 'PluginBacklinks',
			'description' => tra('List all pages linking to the specified page.'),
			'prefs' => array( 'feature_wiki', 'wikiplugin_backlinks' ),
			'params' => array(
				'page' => array(
					'required' => false,
					'name' => tra('Page'),
					'description' => tra('The page links will point to. Default value is the current page.'),
				),
				'info' => array(
					'required' => false,
					'name' => tra('Displayed information'),
					'description' => tra('Pipe separated list of fields to display. ex: hits|user'),
				),
				'exclude' => array(
					'required' => false,
					'name' => tra('Excluded pages'),
					'description' => tra('Pipe separated list of pages to be excluded from the listing. ex: HomePage|Sandbox'),
				),
				'include_self' => array(
					'required' => false,
					'name' => tra('Include Self'),
					'description' => tra('1|0'),
				),
				'noheader' => array(
					'required' => false,
					'name' => tra('No Header'),
					'description' => tra('1|0'),
				),
			),
		);
	}

    function wikiplugin_backlinks($data, $params) {
        $plugin = new wikipluginbacklinks();
        return $plugin->run($data, $params);
    }
?>
